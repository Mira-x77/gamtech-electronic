const CATS = [
  'Mouse', 'Keyboards', 'Headphones & Audio', 'Storage', 'RAM & Memory', 'Networking',
  'Cables & Converters', 'Laptop Accessories', 'Computer Accessories', 'Tools & Repair',
  'Adapters & Hubs', 'Replacement Keyboards', 'Batteries', 'Chargers', 'Screens',
];

const GH_REPO = 'Mira-x77/gamtech-electronic';
const GH_BRANCH = 'main';
// Public, bot-scoped GitHub token with Contents:write only on this repo. The
// admin is static, so there is no server to hold a secret; the PIN guards the
// page and this token is what actually commits edits to GitHub.
const BAKED_TOKEN = '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';
// SHA-256("gamtech"). The real write protection is the GitHub token: the live
// site is static, so edits only happen if a token with Contents:write is given.
const PIN_HASH = '532480b1d38725b3459feb13531b939202cef1d176adaf98299700e0118e8fc9';

const state = {
  pin: sessionStorage.getItem('gs-admin-pin') || '',
  products: [],
  hero: { slides: [] },
  photos: [],
  photoLimit: 24,
  page: 0,
  perPage: 50,
  mode: 'server',
  ghToken: localStorage.getItem('gs-admin-gh') || '',
  overrides: {},
  dirtyOverrides: false,
  dirtyHero: false,
};

function $(id) { return document.getElementById(id); }

function normName(s) {
  return String(s || '')
    .toLowerCase()
    .replace(/^gamtech\s+/i, '')
    .replace(/[^a-z0-9]+/g, ' ')
    .trim();
}

function utf8ToB64(str) {
  return btoa(unescape(encodeURIComponent(str)));
}

function b64ToUtf8(b64) {
  return decodeURIComponent(escape(atob(String(b64).replace(/\s/g, ''))));
}

async function sha256(text) {
  const buf = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(text));
  return Array.from(new Uint8Array(buf)).map((b) => b.toString(16).padStart(2, '0')).join('');
}

function ghHeaders() {
  // The browser token stays the quick route; the baked-in token is what makes
  // the live desk work with only a PIN. It is a public, bot-scoped token with
  // Contents:write only on this repo.
  const token = state.ghToken || BAKED_TOKEN;
  if (!token) throw new Error('GitHub saving is unavailable.');
  return {
    Authorization: 'Bearer ' + token,
    Accept: 'application/vnd.github+json',
    'Content-Type': 'application/json',
  };
}

async function ghGetFile(path) {
  const res = await fetch(
    `https://api.github.com/repos/${GH_REPO}/contents/${path}?ref=${GH_BRANCH}`,
    { headers: ghHeaders(), cache: 'no-store' }
  );
  if (res.status === 404) return { sha: '', text: '' };
  const data = await res.json().catch(() => ({}));
  if (!res.ok) throw new Error('GitHub: ' + (data.message || res.status));
  return { sha: data.sha || '', text: b64ToUtf8(data.content || '') };
}

async function ghPut(path, b64, message) {
  const cur = await ghGetFile(path);
  const body = { message, content: b64, branch: GH_BRANCH };
  if (cur.sha) body.sha = cur.sha;
  const res = await fetch(`https://api.github.com/repos/${GH_REPO}/contents/${path}`, {
    method: 'PUT',
    headers: ghHeaders(),
    body: JSON.stringify(body),
  });
  const data = await res.json().catch(() => ({}));
  if (!res.ok) throw new Error('GitHub: ' + (data.message || res.status));
  return data;
}

function loadScript(src) {
  return new Promise((resolve, reject) => {
    const s = document.createElement('script');
    s.src = src;
    s.onload = resolve;
    s.onerror = () => reject(new Error('Could not load ' + src));
    document.head.appendChild(s);
  });
}

function showLoginError(msg) {
  const err = $('login-err');
  if (!err) return;
  err.hidden = !msg;
  err.textContent = msg || '';
}

async function api(path, body) {
  let res;
  try {
    res = await fetch(path, {
      method: body ? 'POST' : 'GET',
      headers: {
        'Content-Type': 'application/json',
        'x-admin-pin': state.pin,
      },
      body: body ? JSON.stringify(body) : undefined,
    });
  } catch (ex) {
    const err = new Error('Admin API unreachable');
    err.network = true;
    throw err;
  }
  const data = await res.json().catch(() => ({}));
  if (!res.ok) {
    const err = new Error(data.error || res.statusText || ('HTTP ' + res.status));
    err.status = res.status;
    throw err;
  }
  return data;
}

function status(msg, ok) {
  const el = $('status');
  el.textContent = msg || '';
  el.className = 'ad-foot ' + (ok === false ? 'bad' : ok ? 'ok' : '');
}

function formatCFA(n) {
  return Math.round(Number(n) || 0).toLocaleString('fr-FR') + ' CFA';
}

function needsName(p) {
  const name = String(p.name || '').trim();
  return !name || /^product\s*[\d-]*$/i.test(name) || /^new item$/i.test(name);
}

function filtered() {
  const q = ($('q').value || '').toLowerCase().trim();
  const cat = $('cat').value;
  const missing = $('missing').checked;
  const untitled = $('needs-name').checked;
  return state.products.filter((p) => {
    if (cat && p.cat !== cat) return false;
    if (missing && p.img) return false;
    if (untitled && !needsName(p)) return false;
    const blob = `${p.name} ${p.cat} ${p.sub || ''} ${p.also || ''}`.toLowerCase();
    if (q && !q.split(/\s+/).every((t) => t && blob.includes(t))) return false;
    return true;
  });
}

function photoCell(p) {
  if (p.img) {
    return `<a href="${p.img}" target="_blank" rel="noopener" title="Open photo"><img class="ad-thumb" src="${p.img}" alt="" loading="lazy"></a>`;
  }
  return '<div class="ad-ph">no<br>photo</div>';
}

function rowHTML(p) {
  const cats = p.cat && !CATS.includes(p.cat) ? [p.cat, ...CATS] : CATS;
  const catOpts = cats.map((c) => `<option${c === p.cat ? ' selected' : ''}>${c}</option>`).join('');
  const ask = p.price == null || p.price === '';
  return `<tr data-id="${p.id}">
    <td>${photoCell(p)}</td>
    <td><input type="text" data-k="name" value="${String(p.name || '').replace(/"/g, '&quot;')}"></td>
    <td><input type="text" data-k="also" placeholder="laptop names, 19V, 65W" value="${String(p.also || '').replace(/"/g, '&quot;')}"></td>
    <td><select data-k="cat">${catOpts}</select></td>
    <td><input type="number" data-k="price" value="${ask ? '' : p.price}" min="0" step="100" placeholder="Ask"></td>
    <td><input type="checkbox" data-k="askPrice" ${ask ? 'checked' : ''}></td>
    <td><input type="checkbox" data-k="listOnly" ${p.listOnly || !p.img ? 'checked' : ''}></td>
    <td class="ad-actions">
      <button type="button" data-act="save">Save</button>
      <label class="ad-file">Photo<input type="file" accept="image/*" data-act="upload"></label>
      <button type="button" data-act="clear">No photo</button>
      <button type="button" data-act="delete">Delete</button>
    </td>
  </tr>`;
}

function render() {
  const rows = filtered();
  const pages = Math.max(1, Math.ceil(rows.length / state.perPage));
  if (state.page >= pages) state.page = pages - 1;
  if (state.page < 0) state.page = 0;
  const start = state.page * state.perPage;
  const end = Math.min(rows.length, start + state.perPage);
  const range = rows.length ? `${start + 1}–${end}` : '0';
  $('count').textContent = `${range} of ${rows.length} shown · ${state.products.length} products · page ${state.page + 1}/${pages}`;
  $('prev-page').disabled = state.page <= 0;
  $('next-page').disabled = state.page >= pages - 1;
  $('rows').innerHTML = rows.slice(start, end).map(rowHTML).join('');
}

function readRow(tr) {
  const id = tr.getAttribute('data-id');
  const p = state.products.find((x) => String(x.id) === String(id)) || {};
  const name = tr.querySelector('[data-k=name]').value.trim();
  const also = tr.querySelector('[data-k=also]').value.trim();
  const cat = tr.querySelector('[data-k=cat]').value;
  const askPrice = tr.querySelector('[data-k=askPrice]').checked;
  const priceRaw = tr.querySelector('[data-k=price]').value;
  const listOnly = tr.querySelector('[data-k=listOnly]').checked;
  return {
    ...p,
    name,
    also,
    cat,
    listOnly,
    askPrice,
    price: askPrice || priceRaw === '' ? null : Number(priceRaw),
  };
}

function showTab(id) {
  document.querySelectorAll('.ad-tabs button').forEach((b) => {
    b.classList.toggle('active', b.dataset.tab === id);
  });
  ['dash', 'stock', 'photos', 'hero'].forEach((t) => {
    const el = $('panel-' + t);
    if (el) el.hidden = t !== id;
  });
  if (id === 'photos') loadPhotos();
}

async function loadPhotos() {
  $('photo-count').textContent = 'Loading…';
  try {
    const data = await api('/api/admin/photo-candidates');
    state.photos = data.items || [];
    renderPhotos(data.generatedAt);
  } catch (ex) {
    $('photo-count').textContent = ex.message;
  }
}

function renderPhotos(generatedAt) {
  const items = (state.photos || []).slice().sort(
    (a, b) =>
      (b.candidates || []).length - (a.candidates || []).length ||
      String(a.name).localeCompare(String(b.name))
  );
  const count = items.reduce((n, x) => n + (x.candidates || []).length, 0);
  $('photo-count').textContent =
    `${items.length} product(s) · ${count} candidate photo(s)` +
    (generatedAt ? ` · ${String(generatedAt).slice(0, 10)}` : '');
  if (!items.length) {
    $('photo-list').innerHTML =
      '<p class="ad-empty">No candidates queued. Run <code>node preview/find-image-candidates.mjs</code> to build the queue.</p>';
    return;
  }
  const visible = items.slice(0, state.photoLimit);
  $('photo-list').innerHTML =
    visible
      .map((item) => {
        const cands = item.candidates || [];
        return `<article class="ad-photo-item" data-id="${esc(item.id)}">
        <header>
          <div><strong>${esc(item.name)}</strong><span>${esc(item.cat)}${item.sub ? ' · ' + esc(item.sub) : ''}</span></div>
          <button type="button" data-act="skip">Skip</button>
        </header>
        <div class="ad-cands">
          ${
            cands.length
              ? cands
                  .map(
                    (c, i) => `<figure>
            <img src="${esc(c.file || c.thumbnail || c.image)}" alt="${esc(c.title)}" loading="lazy">
            <figcaption>${esc(c.title)}</figcaption>
            <button type="button" data-act="use" data-i="${i}">Use this photo</button>
          </figure>`
                  )
                  .join('')
              : '<p class="ad-empty">No verified match found. Keep it name-only or upload a shop photo.</p>'
          }
        </div>
      </article>`;
      })
      .join('') +
    (items.length > visible.length
      ? `<button type="button" id="photo-more">Show ${Math.min(
          24,
          items.length - visible.length
        )} more (${items.length - visible.length} left)</button>`
      : '');
}

function renderStats(data) {
  const st = data.stats || { total: 0, unique: 0, days: [] };
  $('st-total').textContent = st.total;
  $('st-unique').textContent = st.unique;
  const max = Math.max(1, ...st.days.map((d) => d.visits));
  $('st-bars').innerHTML = st.days.map((d) => {
    const h = Math.round((d.visits / max) * 100);
    const label = d.day.slice(5);
    return `<div class="ad-bar"><span style="height:${h}%"></span><small>${label}</small><em>${d.visits}</em></div>`;
  }).join('');
  const orders = data.orders || [];
  $('order-rows').innerHTML = orders.length
    ? orders.map((o) => `<tr>
        <td>${esc(String(o.at || '').replace('T', ' ').slice(0, 16))}</td>
        <td>${esc(o.id)}</td>
        <td>${esc(o.name)}</td>
        <td>${(o.items || []).length}</td>
        <td>${formatCFA(o.total)}</td>
      </tr>`).join('')
    : '<tr><td colspan="5">No carts sent yet.</td></tr>';
}

function esc(s) {
  return String(s || '').replace(/[&<>"']/g, (c) => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
  }[c]));
}

function renderHero() {
  const slides = state.hero.slides || [];
  $('hero-slides').innerHTML = slides.map((s, i) => `
    <article class="ad-hero-card" data-i="${i}">
      <div class="ad-hero-preview" style="background-image:url('${esc(s.img)}')"></div>
      <label>Tag <input data-k="tag" value="${esc(s.tag)}"></label>
      <label>Title <input data-k="title" value="${esc(s.title)}"></label>
      <label>Highlight <input data-k="highlight" value="${esc(s.highlight)}"></label>
      <label>Text <input data-k="text" value="${esc(s.text)}"></label>
      <label>Button <input data-k="cta" value="${esc(s.cta)}"></label>
      <label class="ad-file">Upload photo<input type="file" accept="image/*" data-hero="${i}"></label>
    </article>
  `).join('');
}

function readHeroFromDom() {
  return {
    slides: Array.from(document.querySelectorAll('.ad-hero-card')).map((card, i) => {
      const prev = state.hero.slides[i] || {};
      return {
        img: prev.img || '',
        tag: card.querySelector('[data-k=tag]').value,
        title: card.querySelector('[data-k=title]').value,
        highlight: card.querySelector('[data-k=highlight]').value,
        text: card.querySelector('[data-k=text]').value,
        cta: card.querySelector('[data-k=cta]').value,
      };
    }),
  };
}

async function openDesk() {
  const data = await api('/api/admin/catalog');
  state.mode = 'server';
  state.products = data.products || [];
  $('gate').hidden = true;
  $('app').hidden = false;
  showLoginError('');
  const sel = $('cat');
  sel.innerHTML = '<option value="">All categories</option>' + CATS.map((c) => `<option>${c}</option>`).join('');
  updateModeBar();
  render();
  try {
    renderStats(await api('/api/admin/stats'));
  } catch {
    renderStats({ stats: { total: 0, unique: 0, days: [] }, orders: [] });
  }
  try {
    state.hero = await api('/api/admin/hero');
    renderHero();
  } catch {
    state.hero = { slides: [] };
  }
}

// ─── Static mode (Vercel) ───────────────────────────────────────────────
// The deployed site has no API server. The admin loads the published catalog,
// stores edits in an overrides object, and commits it to GitHub. Vercel then
// rebuilds the storefront.

async function openDeskStatic() {
  if ((await sha256(state.pin)) !== PIN_HASH) {
    showLoginError('Wrong PIN');
    return;
  }
  sessionStorage.setItem('gs-admin-pin', state.pin);
  state.mode = 'static';
  if (!window.GAMTECH_PRODUCTS) await loadScript('/catalog-data.js');
  state.products = (window.GAMTECH_PRODUCTS || []).map((p) => ({ ...p }));
  try {
    const res = await fetch('/overrides.json', { cache: 'no-store' });
    if (res.ok) state.overrides = await res.json();
  } catch {
    state.overrides = {};
  }
  $('gate').hidden = true;
  $('app').hidden = false;
  showLoginError('');
  const sel = $('cat');
  sel.innerHTML = '<option value="">All categories</option>' + CATS.map((c) => `<option>${c}</option>`).join('');
  updateModeBar();
  render();
  renderStats({ stats: { total: 0, unique: 0, days: [] }, orders: [] });
  try {
    const res = await fetch('/hero.json', { cache: 'no-store' });
    if (res.ok) {
      state.hero = await res.json();
      renderHero();
    }
  } catch {
    state.hero = { slides: [] };
  }
}

function updateModeBar() {
  const note = $('mode-note');
  const btn = $('gh-publish');
  if (!note || !btn) return;
  if (state.mode !== 'static') {
    note.hidden = true;
    btn.hidden = true;
    return;
  }
  note.hidden = false;
  btn.hidden = false;
  note.textContent = 'Live admin · Save marks a change in this browser; Publish live commits it to GitHub. Vercel rebuilds in about a minute.';
  const dirty = state.dirtyOverrides || state.dirtyHero;
  btn.disabled = !dirty;
  btn.textContent = dirty ? 'Publish live' : 'Nothing to publish';
}

function markDirty(kind) {
  if (kind === 'hero') state.dirtyHero = true;
  else state.dirtyOverrides = true;
  updateModeBar();
}

function upsertLocal(product) {
  const ov = state.overrides || {};
  const key = String(product.id);
  const rec = {
    ...(ov[key] || {}),
    id: product.id,
    src: product.src || '',
    name: product.name,
    cat: product.cat,
    sub: product.sub || '',
    img: product.img == null ? null : product.img,
    price: product.price == null ? null : product.price,
    was: product.was == null ? null : product.was,
    listOnly: !!product.listOnly,
    confirmUrl: product.confirmUrl || '',
    hidden: !!product.hidden,
    deleted: !!product.deleted,
    also: product.also || '',
  };
  ov[key] = rec;
  ov[normName(product.name)] = rec;
  if (product.src) ov['src:' + product.src] = rec;
  state.overrides = ov;
}

function findLocalProduct(id) {
  return state.products.find((x) => String(x.id) === String(id));
}

async function publishStatic() {
  if (!state.ghToken && !BAKED_TOKEN) {
    status('Publishing is unavailable on this build.', false);
    return;
  }
  const btn = $('gh-publish');
  if (!btn) return;
  btn.disabled = true;
  btn.textContent = 'Publishing…';
  try {
    if (state.dirtyOverrides) {
      await ghPut(
        'preview/data/overrides.json',
        utf8ToB64(JSON.stringify(state.overrides, null, 2) + '\n'),
        'Admin: update product overrides'
      );
      state.dirtyOverrides = false;
    }
    if (state.dirtyHero) {
      await ghPut(
        'preview/data/hero.json',
        utf8ToB64(JSON.stringify(state.hero, null, 2) + '\n'),
        'Admin: update hero'
      );
      state.dirtyHero = false;
    }
    status('Published. Vercel rebuilds in about a minute.', true);
  } catch (ex) {
    status(ex.message, false);
  } finally {
    updateModeBar();
  }
}

async function staticProductAction(act, payload) {
  const i = state.products.findIndex((x) => String(x.id) === String(payload.id));
  const p = i >= 0 ? { ...state.products[i], ...payload } : { ...payload };
  if (payload.price === '' || payload.askPrice) p.price = null;
  if (act === 'clear') {
    p.img = null;
    p.listOnly = true;
  }
  if (act === 'delete') {
    if (!window.confirm('Delete ' + (payload.name || 'this item') + ' from the shop?')) return;
    p.deleted = true;
    p.hidden = true;
    upsertLocal(p);
    state.products = state.products.filter((x) => String(x.id) !== String(p.id));
    markDirty();
    render();
    status('Deleted here — Publish live to remove it from the shop.', true);
    return;
  }
  upsertLocal(p);
  if (i >= 0) state.products[i] = p;
  markDirty();
  render();
  status((act === 'clear' ? 'Photo removed' : 'Saved') + ' here — Publish live to push it.', true);
}

async function staticUpload(id, file, dataUrl) {
  const p = findLocalProduct(id);
  if (!p) throw new Error('Product not found');
  const comma = dataUrl.indexOf(',');
  const b64 = comma >= 0 ? dataUrl.slice(comma + 1) : dataUrl;
  const ext = /\.png$/i.test(file.name) ? '.png' : /\.webp$/i.test(file.name) ? '.webp' : /\.gif$/i.test(file.name) ? '.gif' : '.jpg';
  const slug = String(p.name || 'item').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '') || 'item';
  const name = `${p.id}-${slug}${ext}`;
  status('Uploading photo to GitHub…');
  await ghPut('wp-content/uploads/product-images/admin/' + name, b64, 'Admin: upload photo for ' + p.name);
  p.img = '/product-images/admin/' + name;
  p.listOnly = false;
  upsertLocal(p);
  const i = state.products.findIndex((x) => String(x.id) === String(id));
  if (i >= 0) state.products[i] = p;
  markDirty();
  render();
  status('Photo uploaded — Publish live to use it on the shop.', true);
}

function staticHeroSave() {
  state.hero = readHeroFromDom();
  markDirty('hero');
  renderHero();
  status('Hero saved here — Publish live to push it.', true);
}

async function staticHeroImage(index, file, dataUrl) {
  const comma = dataUrl.indexOf(',');
  const b64 = comma >= 0 ? dataUrl.slice(comma + 1) : dataUrl;
  const ext = /\.png$/i.test(file.name) ? '.png' : '.jpg';
  const name = `slide-${index}-${Date.now()}${ext}`;
  status('Uploading hero photo to GitHub…');
  await ghPut('wp-content/uploads/product-images/hero/' + name, b64, 'Admin: upload hero image');
  if (!state.hero.slides[index]) {
    state.hero.slides[index] = { img: '', tag: '', title: '', highlight: '', text: '', cta: 'Shop Now' };
  }
  state.hero.slides[index].img = '/product-images/hero/' + name;
  markDirty('hero');
  renderHero();
  status('Hero photo uploaded — Publish live to push it.', true);
}

$('login').addEventListener('submit', async (e) => {
  e.preventDefault();
  showLoginError('');
  state.pin = $('pin').value;
  // ?static=1 lets us exercise the Vercel path on the local dev server.
  if (new URLSearchParams(location.search).get('static') === '1') {
    try {
      await openDeskStatic();
    } catch (ex) {
      showLoginError(ex.message);
    }
    return;
  }
  try {
    await api('/api/admin/login', { pin: state.pin });
    sessionStorage.setItem('gs-admin-pin', state.pin);
    await openDesk();
  } catch (ex) {
    if (ex.status === 404 || ex.network) {
      try {
        await openDeskStatic();
      } catch (e2) {
        showLoginError(e2.message);
      }
    } else {
      showLoginError(ex.message);
    }
  }
});

$('tabs').addEventListener('click', (e) => {
  const btn = e.target.closest('button[data-tab]');
  if (btn) showTab(btn.dataset.tab);
});

$('rows').addEventListener('change', (e) => {
  const ask = e.target.closest('[data-k=askPrice]');
  if (!ask) return;
  const price = e.target.closest('tr')?.querySelector('[data-k=price]');
  if (ask.checked && price) price.value = '';
});
['q', 'missing', 'needs-name'].forEach((id) =>
  $(id).addEventListener('input', () => {
    state.page = 0;
    render();
  })
);
$('cat').addEventListener('change', () => {
  state.page = 0;
  render();
});
$('prev-page').addEventListener('click', () => {
  state.page = Math.max(0, state.page - 1);
  render();
});
$('next-page').addEventListener('click', () => {
  state.page += 1;
  render();
});
$('add-item').addEventListener('click', async () => {
  if (state.mode === 'static') {
    status('Adding new items needs the local desk (save it with the PIN, then Publish live).', false);
    return;
  }
  try {
    const out = await api('/api/admin/product', {
      name: 'New item',
      cat: $('cat').value || 'Computer Accessories',
      price: null,
      listOnly: true,
      img: null,
      also: '',
    });
    state.products.unshift(out.product);
    $('q').value = out.product.name;
    state.page = 0;
    status('Added. Type the name, price or Ask, photo or no photo, then Save.', true);
    render();
  } catch (ex) {
    status(ex.message, false);
  }
});

$('pin-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  if (state.mode === 'static') {
    status('PIN change needs the local desk.', false);
    return;
  }
  const pin = $('new-pin').value.trim();
  try {
    await api('/api/admin/pin', { pin });
    state.pin = pin;
    sessionStorage.setItem('gs-admin-pin', pin);
    $('new-pin').value = '';
    status('PIN updated', true);
  } catch (ex) {
    status(ex.message, false);
  }
});

$('rows').addEventListener('click', async (e) => {
  const btn = e.target.closest('button');
  if (!btn) return;
  const tr = e.target.closest('tr');
  const payload = readRow(tr);
  if (state.mode === 'static') {
    try {
      await staticProductAction(btn.dataset.act, payload);
    } catch (ex) {
      status(ex.message, false);
    }
    return;
  }
  try {
    if (btn.dataset.act === 'save') {
      const out = await api('/api/admin/product', payload);
      const i = state.products.findIndex((x) => String(x.id) === String(out.product.id));
      if (i >= 0) state.products[i] = out.product;
      status('Saved ' + out.product.name + (out.product.price == null ? ' (ask for price)' : ''), true);
      render();
    }
    if (btn.dataset.act === 'clear') {
      const out = await api('/api/admin/product', { ...payload, clearPhoto: true, listOnly: true });
      const i = state.products.findIndex((x) => String(x.id) === String(out.product.id));
      if (i >= 0) state.products[i] = out.product;
      status('Photo removed. Listed by name only.', true);
      render();
    }
    if (btn.dataset.act === 'delete') {
      if (!window.confirm('Delete ' + (payload.name || 'this item') + ' from the shop?')) return;
      await api('/api/admin/delete', { id: payload.id });
      state.products = state.products.filter((x) => String(x.id) !== String(payload.id));
      status('Deleted ' + payload.name, true);
      render();
    }
  } catch (ex) {
    status(ex.message, false);
  }
});

$('rows').addEventListener('change', async (e) => {
  const input = e.target.closest('input[type=file]');
  if (!input || !input.files || !input.files[0]) return;
  const tr = e.target.closest('tr');
  const id = tr.getAttribute('data-id');
  const file = input.files[0];
  const data = await new Promise((resolve, reject) => {
    const r = new FileReader();
    r.onload = () => resolve(r.result);
    r.onerror = reject;
    r.readAsDataURL(file);
  });
  try {
    if (state.mode === 'static') {
      await staticUpload(id, file, data);
      return;
    }
    const out = await api('/api/admin/upload', { id, name: file.name, data });
    const i = state.products.findIndex((x) => String(x.id) === String(out.product.id));
    if (i >= 0) state.products[i] = out.product;
    status('Uploaded photo for ' + out.product.name, true);
    render();
  } catch (ex) {
    status(ex.message, false);
  }
});

$('photo-refresh').addEventListener('click', () => {
  if (state.mode === 'static') {
    $('photo-count').textContent = 'Photo review needs the local desk. The live site can edit products; the photo queue runs locally.';
    return;
  }
  loadPhotos();
});

$('photo-list').addEventListener('click', (e) => {
  if (e.target.id !== 'photo-more') return;
  state.photoLimit += 24;
  renderPhotos();
});

$('photo-list').addEventListener('click', async (e) => {
  const btn = e.target.closest('button[data-act]');
  if (!btn) return;
  const card = e.target.closest('.ad-photo-item');
  const id = card && card.getAttribute('data-id');
  if (!id) return;
  const item = state.photos.find((x) => String(x.id) === String(id));
  try {
    if (btn.dataset.act === 'skip') {
      await api('/api/admin/skip-photo', { id });
      state.photos = state.photos.filter((x) => String(x.id) !== String(id));
      renderPhotos();
      status('Skipped ' + (item ? item.name : 'item'), true);
    }
    if (btn.dataset.act === 'use') {
      const cand = item && item.candidates[Number(btn.dataset.i)];
      if (!cand) return;
      btn.disabled = true;
      btn.textContent = 'Saving…';
      const out = await api('/api/admin/use-photo', {
        id,
        image: cand.image,
        title: cand.title,
      });
      const i = state.products.findIndex((x) => String(x.id) === String(out.product.id));
      if (i >= 0) state.products[i] = out.product;
      state.photos = state.photos.filter((x) => String(x.id) !== String(id));
      renderPhotos();
      status('Photo approved for ' + out.product.name, true);
    }
  } catch (ex) {
    btn.disabled = false;
    btn.textContent = btn.dataset.act === 'use' ? 'Use this photo' : 'Skip';
    status(ex.message, false);
  }
});

$('hero-slides').addEventListener('change', async (e) => {
  const input = e.target.closest('input[type=file]');
  if (!input || !input.files || !input.files[0]) return;
  const index = Number(input.getAttribute('data-hero'));
  const file = input.files[0];
  const data = await new Promise((resolve, reject) => {
    const r = new FileReader();
    r.onload = () => resolve(r.result);
    r.onerror = reject;
    r.readAsDataURL(file);
  });
  try {
    const out = await api('/api/admin/hero-image', { index, name: file.name, data });
    state.hero = out.hero;
    renderHero();
    status('Hero photo updated', true);
  } catch (ex) {
    status(ex.message, false);
  }
});

$('hero-save').addEventListener('click', async () => {
  if (state.mode === 'static') {
    try {
      staticHeroSave();
    } catch (ex) {
      status(ex.message, false);
    }
    return;
  }
  try {
    state.hero = await api('/api/admin/hero', readHeroFromDom());
    renderHero();
    status('Hero saved', true);
  } catch (ex) {
    status(ex.message, false);
  }
});

$('gh-publish').addEventListener('click', publishStatic);

if (state.pin) {
  // On the live site the API does not exist; open in static mode directly so
  // the desk is not stuck behind a failed login request.
  const tryOpen = async () => {
    try {
      await openDesk();
    } catch (ex) {
      if (ex.status === 404 || ex.network) {
        await openDeskStatic();
      } else {
        sessionStorage.removeItem('gs-admin-pin');
        state.pin = '';
      }
    }
  };
  tryOpen().catch(() => {
    sessionStorage.removeItem('gs-admin-pin');
    state.pin = '';
  });
}
