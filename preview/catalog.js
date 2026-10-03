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
];

function products() {
  return Array.isArray(window.GAMTECH_PRODUCTS) ? window.GAMTECH_PRODUCTS : [];
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

function cardHTML(p) {
  const addPrice = p.price == null || p.price === '' ? 0 : Number(p.price);
  return `<div class="gs-card">
    <a href="#shop" class="gs-card-img"><img src="${esc(p.img)}" alt="${esc(p.name)}" decoding="async"></a>
    <div class="gs-card-body">
      <span class="gs-card-brand">${esc(p.sub || p.cat || 'GamTech')}</span>
      <a href="#shop" class="gs-card-name">${esc(p.name)}</a>
      <div class="gs-card-price">${priceHTML(p)}</div>
    </div>
    <a href="#" class="gs-add-btn" data-id="${esc(p.id)}" data-price="${addPrice}" data-name="${esc(p.name)}" data-img="${esc(p.img)}">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
      Add to Cart
    </a>
  </div>`;
}

function catItem([name, ico]) {
  const short = name.length > 12 ? name.slice(0, 11) + '…' : name;
  return `<a href="#cat-${esc(name)}" class="gs-cat">
    <div class="gs-cat-ico"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">${ico}</svg></div>
    <span class="gs-cat-lbl">${esc(short)}</span>
  </a>`;
}

function byCat(name) {
  return products().filter((p) => p.cat === name);
}

function boot() {
  const all = products();
  const sb = document.getElementById('gs-cats-list');
  if (sb) {
    sb.innerHTML = CATS.map(([name, ico]) =>
      `<a href="#cat-${esc(name)}" class="gs-sb-cat"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">${ico}</svg>${esc(name)}</a>`
    ).join('');
  }
  const row = document.getElementById('gs-cats-row');
  if (row) row.innerHTML = CATS.map((c) => catItem(c)).join('');

  const deals = document.getElementById('gs-deals');
  if (deals) deals.innerHTML = all.slice(0, 8).map(cardHTML).join('');

  const rec = document.getElementById('gs-rec');
  if (rec) rec.innerHTML = all.slice(8, 16).map(cardHTML).join('');

  const extra = document.getElementById('gs-extra-cats');
  if (extra) {
    extra.innerHTML = CATS.map(([name]) => {
      const items = byCat(name);
      if (!items.length) return '';
      return `<div class="gs-sh" id="cat-${esc(name)}"><div><h2>${esc(name)}</h2><p>${items.length} products</p></div>
        <a href="#shop" class="gs-viewall">View All</a></div>
        <div class="gs-grid">${items.map(cardHTML).join('')}</div>`;
    }).join('');
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', boot);
} else {
  boot();
}
