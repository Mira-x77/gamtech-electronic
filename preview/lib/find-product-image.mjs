import { brandOf, isExactModelItem, modelTokens, typeWordsFor, verifyCandidate } from './image-verify.mjs';

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
  const type = typeWordsFor(p)[0] || '';
  const hint = type ||
    {
      Storage: 'usb flash ssd hdd',
      Networking: 'network router',
      'Cables & Converters': 'ethernet cable',
      'Adapters & Hubs': 'hdmi adapter hub',
      'Computer Accessories': 'computer accessory',
      'Laptop Accessories': 'laptop bag',
      Mouse: 'computer mouse',
      Keyboards: 'computer keyboard',
    }[p.cat] ||
    '';
  return [p.name, hint].filter(Boolean).join(' ');
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

async function bingImages(q) {
  const res = await timedFetch(
    'https://www.bing.com/images/search?q=' +
      encodeURIComponent(q) +
      '&form=HDRSC2&first=1',
    { headers: { 'User-Agent': UA, 'Accept-Language': 'en-US,en;q=0.9' } }
  );
  if (!res.ok) return [];
  const html = await res.text();
  const out = [];
  for (const match of html.matchAll(/m="(\{[^"]+?\})"/g)) {
    try {
      const data = JSON.parse(match[1].replace(/&quot;/g, '"').replace(/&amp;/g, '&'));
      if (!data.murl) continue;
      out.push({
        title: data.t || data.title || q,
        image: data.murl,
        thumbnail: data.turl || data.murl,
        url: data.purl || '',
      });
    } catch {
      /* skip malformed entry */
    }
  }
  return out;
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

async function wikimedia(q) {
  const url =
    'https://commons.wikimedia.org/w/api.php?action=query&format=json&origin=*&generator=search&gsrlimit=6&gsrnamespace=6&gsrsearch=' +
    encodeURIComponent(q) +
    '&prop=imageinfo&iiprop=url&iiurlwidth=1000';
  const data = await (await timedFetch(url, { headers: { 'User-Agent': UA } })).json();
  const pages = data.query && data.query.pages ? Object.values(data.query.pages) : [];
  return pages
    .map((pg) => {
      const info = (pg.imageinfo && pg.imageinfo[0]) || {};
      return { title: pg.title || q, image: info.thumburl || info.url, thumbnail: info.thumburl, url: info.descriptionurl };
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
    url: r.foreign_landing_url || r.url,
    width: r.width,
    height: r.height,
  }));
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
  if (buf.length < 800 || buf.length > 8_000_000) return null;
  let ext = '.jpg';
  if (type.includes('png') || (buf[0] === 0x89 && buf[1] === 0x50)) ext = '.png';
  else if (type.includes('webp') || (buf.slice(8, 12).toString() === 'WEBP')) ext = '.webp';
  else if (type.includes('gif') || buf.slice(0, 3).toString() === 'GIF') ext = '.gif';
  return { buf, ext };
}

// Verified candidates, best first. Nothing is downloaded or saved here: the
// review desk shows these thumbnails and a human picks one.
export async function findProductImageCandidates(p, { limit = 5 } = {}) {
  const queries = [searchQuery(p), String(p.name || '').trim()];

  // The price list sometimes writes a letter O where the model uses a zero
  // ("LO09UiGS" instead of "L009UiGS"). Search engines will not always correct
  // that on their own, so try a cleaned query as well.
  const brand = brandOf(p && p.name);
  const rawModels = modelTokens(p && p.name);
  const cleanedModels = rawModels.map((t) => t.replace(/o/g, '0'));
  if (brand && cleanedModels.some((t, i) => t !== rawModels[i])) {
    const cleaned = [brand.alias, ...cleanedModels, typeWordsFor(p)[0] || '']
      .filter(Boolean)
      .join(' ');
    if (!queries.includes(cleaned)) queries.push(cleaned);
  }

  // Full price-list names are noisy ("HP K558 LUMINEUX CLAVIER"); a short
  // brand + model + type query often matches the same listing better.
  if (brand && rawModels.length) {
    const short = [brand.alias, ...rawModels, typeWordsFor(p)[0] || '']
      .filter(Boolean)
      .join(' ');
    if (!queries.includes(short)) queries.push(short);
  }
  const pool = [];
  for (const q of queries) {
    try {
      pool.push(...(await bingImages(q)));
    } catch {
      /* next source */
    }
    // Keep going through the query variants: a noisy price-list name can fill
    // the first page with near-misses while the short query hits the exact model.
    if (pool.length >= 80) break;
    await new Promise((r) => setTimeout(r, 300));
  }
  if (pool.length < 4) {
    for (const q of queries) {
      try {
        pool.push(...(await ddgImages(q)));
      } catch {
        /* next source */
      }
      if (pool.length >= 20) break;
      await new Promise((r) => setTimeout(r, 1200));
    }
  }
  if (!pool.length) {
    for (const q of queries) {
      try {
        pool.push(...(await wikimedia(q)));
      } catch {
        /* next */
      }
      try {
        pool.push(...(await openverse(q)));
      } catch {
        /* next */
      }
    }
  }

  const seen = new Set();
  const verified = [];
  for (const hit of pool) {
    const image = hit.image || hit.thumbnail;
    if (!image || seen.has(image)) continue;
    const check = verifyCandidate(p, hit);
    if (!check.ok) continue;
    seen.add(image);
    verified.push({
      title: hit.title || p.name,
      image,
      thumbnail: hit.thumbnail || hit.image,
      pageUrl: hit.url || hit.pageUrl || '',
      width: Number(hit.width) || 0,
      height: Number(hit.height) || 0,
      score: check.score,
    });
  }
  return verified
    .sort((a, b) => b.score - a.score || (b.width || 0) - (a.width || 0))
    .slice(0, limit);
}

// Backwards-compatible single result. Now only returns a candidate that passed
// strict verification; callers should still treat it as a proposal.
export async function findProductImage(p) {
  const [hit] = await findProductImageCandidates(p, { limit: 1 });
  if (!hit) return null;
  const file = await download(hit.image);
  if (!file) return null;
  return { ...file, source: hit.image, title: hit.title, score: hit.score };
}

export { isExactModelItem };

export function slugName(name) {
  return String(name || 'item')
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-|-$/g, '')
    .slice(0, 80) || 'item';
}
