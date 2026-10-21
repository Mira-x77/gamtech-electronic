const CATS = [
  ['Mouse', '<path d="M12 2a6 6 0 0 1 6 6v8a6 6 0 0 1-12 0V8a6 6 0 0 1 6-6z"/><line x1="12" y1="2" x2="12" y2="8"/>'],
  ['Keyboards', '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M6 11h.01M10 11h.01M14 11h.01M18 11h.01M8 15h8"/>'],
  ['Headphones & Audio', '<path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/>'],
  ['Storage', '<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>'],
  ['RAM & Memory', '<rect x="4" y="8" width="16" height="8" rx="1"/><path d="M8 8V6M12 8V6M16 8V6M8 16v2M12 16v2M16 16v2"/>'],
  ['Networking', '<rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>'],
  ['Cables & Converters', '<path d="M18 4l3 3-3 3"/><path d="M3 7h18M6 20l-3-3 3-3"/><path d="M21 17H3"/>'],
  ['Laptop Accessories', '<rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>'],
  ['Computer Accessories', '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>'],
  ['Tools & Repair', '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>'],
  ['Adapters & Hubs', '<circle cx="12" cy="12" r="3"/><path d="M12 2v4M12 18v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M2 12h4M18 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/>'],
  ['Replacement Keyboards', '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M6 11h.01M10 11h.01M14 11h.01M18 11h.01M8 15h8"/>'],
  ['Batteries', '<rect x="6" y="7" width="12" height="14" rx="2"/><path d="M10 7V5h4v2"/><path d="M10 12h4"/>'],
  ['Chargers', '<path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>'],
  ['Screens', '<rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>'],
];

function products() {
  const all = Array.isArray(window.GAMTECH_PRODUCTS) ? window.GAMTECH_PRODUCTS : [];
  return all.filter((p) => !p.hidden);
}

function esc(s) {
  return String(s || '').replace(/[&<>"']/g, (c) => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
  }[c]));
}

function formatCFA(n) {
  return Math.round(Number(n)).toLocaleString('fr-FR') + ' CFA';
}

function priceHTML(p) {
  if (p.price == null || p.price === '') {
    return '<span class="gs-pnow">Ask for price</span>';
  }
  const now = Number(p.price);
  const was = p.was != null && p.was !== '' ? Number(p.was) : 0;
  const sale = was > now;
  const pct = sale ? Math.round((was - now) / was * 100) : 0;
  return `<span class="gs-pnow">${formatCFA(now)}</span>` +
    (sale ? `<span class="gs-pwas">${formatCFA(was)}</span><span class="gs-psave">-${pct}%</span>` : '');
}

function confirmUrl(p) {
  if (p.confirmUrl) return p.confirmUrl;
  const q = [p.name, p.cat].filter(Boolean).join(' ');
  return 'https://www.google.com/search?udm=2&q=' + encodeURIComponent(q);
}

function listRowHTML(p) {
  const addPrice = p.price == null || p.price === '' ? 0 : Number(p.price);
  return `<div class="gs-list-row gs-card">
    <div class="gs-list-main">
      <span class="gs-card-brand">${esc(p.sub || p.cat || 'GamTech')}</span>
      <span class="gs-card-name">${esc(p.name)}</span>
      <div class="gs-card-price">${priceHTML(p)}</div>
    </div>
    <div class="gs-list-actions">
      <a class="gs-confirm-img" href="${esc(confirmUrl(p))}" target="_blank" rel="noopener noreferrer">Confirm photo</a>
      <a href="#" class="gs-add-btn" data-id="${esc(p.id)}" data-price="${addPrice}" data-name="${esc(p.name)}" data-img="">Add to Cart</a>
    </div>
  </div>`;
}

function cardHTML(p) {
  if (p.listOnly || !p.img) return listRowHTML(p);
  const addPrice = p.price == null || p.price === '' ? 0 : Number(p.price);
  return `<div class="gs-card">
    <a href="#shop" class="gs-card-img"><img src="${esc(p.img)}" alt="${esc(p.name)}" decoding="async"></a>
    <div class="gs-card-body">
      <span class="gs-card-brand">${esc(p.sub || p.cat || 'GamTech')}</span>
      <a href="#shop" class="gs-card-name">${esc(p.name)}</a>
      <div class="gs-card-price">${priceHTML(p)}</div>
    </div>
    <a href="#" class="gs-add-btn" data-id="${esc(p.id)}" data-price="${addPrice}" data-name="${esc(p.name)}" data-img="${esc(p.img || '')}">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
      Add to Cart
    </a>
  </div>`;
}

const WA_PHONE = '22890597003';

function tokens(s) {
  return String(s || '')
    .toLowerCase()
    .replace(/usb[\s-]*c/g, ' usbc ')
    .replace(/type[\s-]*c/g, ' usbc ')
    .replace(/[^a-z0-9]+/g, ' ')
    .trim()
    .split(/\s+/)
    .filter((t) => t && t.length > 1);
}

function haystack(p) {
  return tokens([p.name, p.cat, p.sub, p.also].filter(Boolean).join(' ')).join(' ');
}

function tokenHit(hay, tok) {
  if (!tok) return false;
  if (hay.includes(tok)) return true;
  return hay.split(' ').some((w) => w.startsWith(tok) || (tok.length >= 3 && tok.startsWith(w) && w.length >= 3));
}

function hasAllTokens(hay, toks) {
  return toks.length > 0 && toks.every((t) => tokenHit(hay, t));
}

function queryLabel(name, model) {
  return [name, model].map((s) => String(s || '').trim()).filter(Boolean).join(' ').trim();
}

function waHref(label) {
  const msg = 'Bonjour GamTech, je cherche: ' + label;
  return 'https://wa.me/' + WA_PHONE + '?text=' + encodeURIComponent(msg);
}

function waAskHTML(label, note) {
  return `<div class="gs-find-row">
    <div class="gs-find-row-text">
      <strong>${esc(label)}</strong>
      <span>${esc(note)}</span>
    </div>
    <a class="gs-find-wa" href="${esc(waHref(label))}" target="_blank" rel="noopener noreferrer">Ask on WhatsApp</a>
  </div>`;
}

function findMatches(nameQ, modelQ) {
  const nameToks = tokens(nameQ);
  const modelToks = tokens(modelQ);
  if (!nameToks.length && !modelToks.length) return [];
  const q = queryLabel(nameQ, modelQ).toLowerCase();
  return products()
    .map((p) => {
      const hay = haystack(p);
      const nameLow = String(p.name || '').toLowerCase();
      const nameHit = !nameToks.length || hasAllTokens(hay, nameToks);
      const modelHit = !modelToks.length || hasAllTokens(hay, modelToks);
      if (!nameHit || !modelHit) return null;
      let score = 0;
      if (nameToks.length && nameHit) score += 10;
      if (modelToks.length && modelHit) score += 20;
      if (nameToks.length && modelToks.length) score += 5;
      if (q && nameLow === q) score += 80;
      else if (q && nameLow.startsWith(q)) score += 40;
      else if (q && nameLow.includes(q)) score += 12;
      const last = (modelToks[modelToks.length - 1] || nameToks[nameToks.length - 1] || '');
      if (last && nameLow.split(/\s+/).some((w) => w.startsWith(last))) score += 15;
      return { p, score };
    })
    .filter(Boolean)
    .sort((a, b) => b.score - a.score || String(a.p.name).localeCompare(String(b.p.name)))
    .map((x) => x.p);
}

function scrollToFind() {
  const mn = document.querySelector('.gs-mn');
  const target = document.getElementById('find-model');
  if (!mn || !target) return;
  mn.scrollTo({ top: Math.max(0, target.offsetTop - 12), behavior: 'smooth' });
}

const LIVE_CAP = 40;
const SUGGEST_CAP = 12;

function renderHits(hits, label, live) {
  const hint = document.getElementById('gs-find-hint');
  const status = document.getElementById('gs-find-status');
  const grid = document.getElementById('gs-find-grid');
  const ask = document.getElementById('gs-find-ask');
  if (!grid || !ask) return;

  if (!label) {
    if (hint) hint.hidden = false;
    if (status) { status.hidden = true; status.textContent = ''; }
    grid.hidden = true;
    grid.innerHTML = '';
    ask.hidden = true;
    ask.innerHTML = '';
    return;
  }

  if (hint) hint.hidden = true;
  const shown = live ? hits.slice(0, LIVE_CAP) : hits;
  const withImg = shown.filter((p) => p.img && !p.listOnly);
  const listed = shown.filter((p) => !p.img || p.listOnly);

  if (status) {
    status.hidden = false;
    if (!hits.length) {
      status.textContent = live ? 'No similar names yet' : 'No catalog match for this model';
    } else if (live && hits.length > shown.length) {
      status.textContent = 'Showing ' + shown.length + ' of ' + hits.length + ' similar names';
    } else {
      status.textContent = hits.length + ' similar name' + (hits.length === 1 ? '' : 's');
    }
  }

  if (withImg.length) {
    grid.hidden = false;
    grid.innerHTML = withImg.map(cardHTML).join('');
  } else {
    grid.hidden = true;
    grid.innerHTML = '';
  }

  const rows = listed.map(listRowHTML);
  if (!hits.length) {
    rows.push(waAskHTML(label, 'Not in the catalog — ask the shop for this model'));
  } else if (!live) {
    rows.push(waAskHTML(label, 'Not the right one? Ask for this model'));
  }
  ask.hidden = !rows.length;
  ask.innerHTML = rows.join('');
}

function runFind(nameQ, modelQ, opts) {
  const live = !!(opts && opts.live);
  const formName = document.getElementById('gs-find-name');
  const formModel = document.getElementById('gs-find-model');
  if (formName && nameQ != null) formName.value = nameQ;
  if (formModel && modelQ != null) formModel.value = modelQ;
  const name = (formName && formName.value) || '';
  const model = (formModel && formModel.value) || '';
  const label = queryLabel(name, model);
  const hits = label ? findMatches(name, model) : [];
  renderHits(hits, label, live);
  if (!live && label) scrollToFind();
}

function suggestHTML(hits) {
  if (!hits.length) return '';
  return hits.map((p) => {
    const meta = p.price == null || p.price === '' ? esc(p.cat || '') : formatCFA(p.price);
    return `<button type="button" class="gs-suggest-item" data-name="${esc(p.name)}" data-cat="${esc(p.cat || '')}">
      <span class="gs-suggest-name">${esc(p.name)}</span>
      <span class="gs-suggest-meta">${meta}</span>
    </button>`;
  }).join('');
}

function bindSuggestBox(input, box) {
  if (!input || !box) return;
  const paint = () => {
    const q = input.value || '';
    const hits = q.trim() ? findMatches(q, '').slice(0, SUGGEST_CAP) : [];
    if (!hits.length) {
      box.hidden = true;
      box.innerHTML = '';
      return;
    }
    box.hidden = false;
    box.innerHTML = suggestHTML(hits);
  };
  input.addEventListener('input', paint);
  input.addEventListener('focus', paint);
  box.addEventListener('mousedown', (e) => {
    const item = e.target.closest('.gs-suggest-item');
    if (!item) return;
    e.preventDefault();
    const name = item.getAttribute('data-name') || '';
    input.value = name;
    box.hidden = true;
    runFind(name, '');
  });
}

function bindFind() {
  const form = document.getElementById('gs-find-form');
  const formName = document.getElementById('gs-find-name');
  const formModel = document.getElementById('gs-find-model');
  let liveTimer = 0;
  const live = () => {
    clearTimeout(liveTimer);
    liveTimer = setTimeout(() => runFind(undefined, undefined, { live: true }), 60);
  };
  if (form) {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      runFind();
    });
  }
  formName && formName.addEventListener('input', live);
  formModel && formModel.addEventListener('input', live);

  document.querySelectorAll('.gs-hd-search-form, .gs-mob-search-form').forEach((hdr) => {
    hdr.addEventListener('submit', (e) => {
      e.preventDefault();
      const q = (hdr.querySelector('input[type="search"]') || {}).value || '';
      document.getElementById('gs-hd-suggest')?.setAttribute('hidden', '');
      document.getElementById('gs-mob-suggest')?.setAttribute('hidden', '');
      runFind(q, '');
    });
  });

  bindSuggestBox(
    document.querySelector('.gs-hd-search-input'),
    document.getElementById('gs-hd-suggest')
  );
  bindSuggestBox(
    document.querySelector('.gs-mob-search-form input[type="search"]'),
    document.getElementById('gs-mob-suggest')
  );

  document.addEventListener('click', (e) => {
    if (e.target.closest('.gs-hd-search-wrap, .gs-mob-search-form, .gs-suggest')) return;
    const hd = document.getElementById('gs-hd-suggest');
    const mob = document.getElementById('gs-mob-suggest');
    if (hd) { hd.hidden = true; }
    if (mob) { mob.hidden = true; }
  });
}

function catSlug(name) {
  return String(name || '')
    .toLowerCase()
    .replace(/&/g, 'and')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '');
}

function catId(name) {
  return 'cat-' + catSlug(name);
}

function stripOwnerNav() {
  document.querySelectorAll('a.gs-nav-a, a.gs-hd-action').forEach((a) => {
    const href = (a.getAttribute('href') || '').toLowerCase();
    const text = (a.textContent || '').replace(/\s+/g, ' ').trim().toLowerCase();
    if (href.includes('admin') || href === '#account' || text.includes('my account') || text.includes('stock desk')) {
      a.remove();
    }
  });
}

function shopAllBox() {
  let extra = document.getElementById('shop-all') || document.getElementById('gs-extra-cats');
  if (!extra) {
    extra = document.createElement('div');
    extra.id = 'shop-all';
    document.querySelector('.gs-mn')?.appendChild(extra);
  }
  extra.id = 'shop-all';
  return extra;
}

function catItem([name, ico]) {
  const short = name.length > 12 ? name.slice(0, 11) + '…' : name;
  return `<a href="#${catId(name)}" class="gs-cat">
    <div class="gs-cat-ico"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">${ico}</svg></div>
    <span class="gs-cat-lbl">${esc(short)}</span>
  </a>`;
}

function byCat(name) {
  return products().filter((p) => p.cat === name);
}

function shuffle(list) {
  const a = list.slice();
  for (let i = a.length - 1; i > 0; i -= 1) {
    const j = Math.floor(Math.random() * (i + 1));
    const tmp = a[i];
    a[i] = a[j];
    a[j] = tmp;
  }
  return a;
}

function isNameOnlyKind(p) {
  if (!p || p.listOnly) return true;
  const blob = [p.cat, p.sub, p.name].filter(Boolean).join(' ').toLowerCase();
  return /batter(?:y|ies)|chargeur|chargers?\b|replacement keyboards?|\b(?:e|é)crans?\b|\bscreens?\b/.test(blob);
}

function mixByCategory(items) {
  const buckets = new Map();
  items.forEach((p) => {
    const key = p.cat || 'Other';
    if (!buckets.has(key)) buckets.set(key, []);
    buckets.get(key).push(p);
  });
  const cats = shuffle([...buckets.keys()]);
  cats.forEach((c) => {
    buckets.set(c, shuffle(buckets.get(c)));
  });
  const mixed = [];
  let more = true;
  while (more) {
    more = false;
    cats.forEach((c) => {
      const bucket = buckets.get(c);
      if (bucket.length) {
        mixed.push(bucket.shift());
        more = true;
      }
    });
  }
  return mixed;
}

function boot() {
  stripOwnerNav();
  const all = products();
  const sb = document.getElementById('gs-cats-list');
  if (sb) {
    sb.innerHTML = CATS.map(([name, ico]) =>
      `<a href="#${catId(name)}" class="gs-sb-cat"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">${ico}</svg>${esc(name)}</a>`
    ).join('');
  }
  const row = document.getElementById('gs-cats-row');
  if (row) row.innerHTML = CATS.map((c) => catItem(c)).join('');

  const shortcuts = document.getElementById('gs-shortcuts');
  if (shortcuts) {
    shortcuts.innerHTML = CATS.map(([name, ico]) => {
      const short = name.length > 10 ? name.slice(0, 9) + '…' : name;
      return `<a href="#${catId(name)}" class="gs-sc">
        <span class="gs-sc-ico"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">${ico}</svg></span>
        <span class="gs-sc-lbl">${esc(short)}</span>
      </a>`;
    }).join('');
  }

  const photo = mixByCategory(all.filter((p) => p.img && !isNameOnlyKind(p)));
  const deals = document.getElementById('gs-deals');
  if (deals) deals.innerHTML = photo.slice(0, 8).map(cardHTML).join('');

  const rec = document.getElementById('gs-rec');
  if (rec) rec.innerHTML = photo.slice(8, 16).map(cardHTML).join('');

  const extra = shopAllBox();
  extra.innerHTML = CATS.map(([name]) => {
    const items = byCat(name);
    if (!items.length) return '';
    const photos = items.filter((p) => p.img && !p.listOnly);
    const listed = items.filter((p) => !p.img || p.listOnly);
    return `<section class="gs-cat-block" id="${catId(name)}">
      <div class="gs-sh"><div><h2>${esc(name)}</h2><p>${items.length} products</p></div>
      <a href="#shop-all" class="gs-viewall">View All</a></div>
      ${photos.length ? `<div class="gs-grid">${photos.map(cardHTML).join('')}</div>` : ''}
      ${listed.length ? `<div class="gs-list">${listed.map(listRowHTML).join('')}</div>` : ''}
    </section>`;
  }).join('');

  bindFind();
  goHash(location.hash);
}

function scrollMainTo(id) {
  const mn = document.querySelector('.gs-mn');
  if (!mn) return false;
  if (!id) {
    mn.scrollTo({ top: 0, behavior: 'smooth' });
    return true;
  }
  const target = document.getElementById(id);
  if (!target) return false;
  const top = target.getBoundingClientRect().top - mn.getBoundingClientRect().top + mn.scrollTop - 12;
  mn.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
  document.getElementById('gs-sb')?.classList.remove('open');
  document.getElementById('gs-ov')?.classList.remove('on');
  return true;
}

function resolveHash(hash) {
  const raw = decodeURIComponent(String(hash || '').replace(/^#/, '')).trim();
  if (!raw || raw === 'shop' || raw === 'shop-all') return 'shop-all';
  if (raw === 'deals') return 'deals';
  if (raw === 'arrivals' || raw === 'new') return 'arrivals';
  if (raw === 'sellers' || raw === 'best') return 'deals';
  if (raw === 'contact' || raw === 'about' || raw === 'privacy') return 'contact';
  if (raw.startsWith('cat-')) {
    if (document.getElementById(raw)) return raw;
    const slug = catId(raw.slice(4));
    if (document.getElementById(slug)) return slug;
  }
  if (document.getElementById(raw)) return raw;
  return '';
}

function goHash(hash) {
  const id = resolveHash(hash);
  if (!id) return false;
  document.querySelectorAll('.gs-sc.active').forEach((el) => el.classList.remove('active'));
  const sc = document.querySelector('.gs-sc[href="#' + id + '"]');
  if (sc) sc.classList.add('active');
  return scrollMainTo(id);
}

document.addEventListener('click', (e) => {
  const a = e.target.closest('a[href]');
  if (!a) return;
  const href = a.getAttribute('href') || '';
  if (/admin/i.test(href) || href === '#account') {
    e.preventDefault();
    a.remove();
    return;
  }
  if (href.charAt(0) !== '#') return;
  e.preventDefault();
  goHash(href);
});

window.addEventListener('hashchange', () => goHash(location.hash));

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', boot);
} else {
  boot();
}
