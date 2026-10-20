const UA =
  'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

function timedFetch(url, opts = {}, ms = 12000) {
  const ctrl = new AbortController();
  const t = setTimeout(() => ctrl.abort(), ms);
  return fetch(url, { ...opts, signal: ctrl.signal }).finally(() => clearTimeout(t));
}

const NO_PHOTO_CATS = new Set([
  'Batteries',
  'Chargers',
  'Replacement Keyboards',
  'Screens',
]);

const SKIP_NAMES =
  /^(les |casque et|tapis souris$|hdmi$|convertisseurs? tout|cle usb$|cl[eé] usb$|ssd externes?$|hdd externe|cameras oscar|cables reseaux$|connecteurs$|alfa marque$|nvr$|sacs ordinateurs$|stickers?$)/i;

export function needsStorePhoto(p) {
  if (!p || p.listOnly || p.hidden) return false;
  if (NO_PHOTO_CATS.has(p.cat)) return false;
  const name = String(p.name || '').trim();
  if (name.length < 4) return false;
  if (SKIP_NAMES.test(name)) return false;
  return true;
}

export function searchQuery(p) {
  const hint = {
    Storage: 'usb flash ssd hdd',
    Networking: 'network router',
    'Cables & Converters': 'ethernet cable',
    'Adapters & Hubs': 'hdmi adapter hub',
    'Computer Accessories': 'computer accessory',
    'Laptop Accessories': 'laptop bag',
    Mouse: 'computer mouse',
    Keyboards: 'computer keyboard',
  }[p.cat] || '';
  return [p.name, hint].filter(Boolean).join(' ');
}

function tokens(s) {
  return String(s || '')
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, ' ')
    .trim()
    .split(/\s+/)
    .filter((t) => t.length >= 2);
}

async function ddgVqd(q) {
  const html = await (
    await timedFetch('https://duckduckgo.com/?q=' + encodeURIComponent(q) + '&iax=images&ia=images', {
      headers: { 'User-Agent': UA },
    })
  ).text();
  const m = html.match(/vqd="([^"]+)"/) || html.match(/vqd='([^']+)'/);
  return m ? m[1] : null;
}

async function ddgImages(q) {
  const vqd = await ddgVqd(q);
  if (!vqd) return [];
  const url =
    'https://duckduckgo.com/i.js?l=us-en&o=json&q=' +
    encodeURIComponent(q) +
    '&vqd=' +
    encodeURIComponent(vqd) +
    '&f=,,,';
  const res = await timedFetch(url, {
    headers: { 'User-Agent': UA, Referer: 'https://duckduckgo.com/' },
  });
  if (!res.ok) return [];
  const data = await res.json();
  return Array.isArray(data.results) ? data.results : [];
}

function rank(results, name) {
  const toks = tokens(name);
  return results
    .map((r) => {
      const title = String(r.title || '').toLowerCase();
      let score = 0;
      for (const t of toks) if (title.includes(t)) score += t.length > 3 ? 3 : 1;
      if ((r.width || 0) >= 400 && (r.height || 0) >= 400) score += 2;
      if (/aliexpress|pinterest|facebook|tiktok/i.test(r.url || '')) score -= 2;
      return { r, score };
    })
    .sort((a, b) => b.score - a.score)
    .map((x) => x.r);
}

async function download(url) {
  const res = await timedFetch(url, {
    headers: { 'User-Agent': UA, Referer: 'https://duckduckgo.com/' },
    redirect: 'follow',
  });
  if (!res.ok) return null;
  const type = String(res.headers.get('content-type') || '');
  if (type.includes('text/html')) return null;
  const buf = Buffer.from(await res.arrayBuffer());
  if (buf.length < 800 || buf.length > 5_000_000) return null;
  let ext = '.jpg';
  if (type.includes('png') || buf[0] === 0x89) ext = '.png';
  else if (type.includes('webp')) ext = '.webp';
  else if (type.includes('gif')) ext = '.gif';
  return { buf, ext };
}

async function fromList(ranked, name) {
  for (const hit of ranked.slice(0, 6)) {
    const urls = [hit.image, hit.thumbnail].filter(Boolean);
    for (const url of urls) {
      try {
        const file = await download(url);
        if (file) return { ...file, source: url, title: hit.title || name };
      } catch {
        /* next */
      }
    }
  }
  return null;
}

async function wikimedia(q) {
  const url =
    'https://commons.wikimedia.org/w/api.php?action=query&format=json&origin=*&generator=search&gsrlimit=5&gsrnamespace=6&gsrsearch=' +
    encodeURIComponent(q) +
    '&prop=imageinfo&iiprop=url&iiurlwidth=1000';
  const data = await (await timedFetch(url, { headers: { 'User-Agent': UA } })).json();
  const pages = data.query && data.query.pages ? Object.values(data.query.pages) : [];
  return pages
    .map((pg) => {
      const info = (pg.imageinfo && pg.imageinfo[0]) || {};
      return { title: pg.title || q, image: info.thumburl || info.url, thumbnail: info.thumburl };
    })
    .filter((r) => r.image);
}

async function openverse(q) {
  const url = 'https://api.openverse.org/v1/images/?page_size=8&q=' + encodeURIComponent(q);
  const res = await timedFetch(url, { headers: { 'User-Agent': UA, Accept: 'application/json' } });
  if (!res.ok) return [];
  const data = await res.json();
  return (data.results || []).map((r) => ({
    title: r.title || q,
    image: r.url,
    thumbnail: r.thumbnail,
    width: r.width,
    height: r.height,
  }));
}

export async function findProductImage(p) {
  const queries = [searchQuery(p), String(p.name || '').trim()].filter(
    (q, i, arr) => q && arr.indexOf(q) === i
  );
  for (const q of queries) {
    try {
      const hit = await fromList(rank(await ddgImages(q), p.name), p.name);
      if (hit) return hit;
    } catch {
      /* next source */
    }
    try {
      const hit = await fromList(rank(await wikimedia(q), p.name), p.name);
      if (hit) return hit;
    } catch {
      /* next */
    }
    try {
      const hit = await fromList(rank(await openverse(q), p.name), p.name);
      if (hit) return hit;
    } catch {
      /* next */
    }
    await new Promise((r) => setTimeout(r, 400));
  }
  return null;
}

export function slugName(name) {
  return String(name || 'item')
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-|-$/g, '')
    .slice(0, 80) || 'item';
}
