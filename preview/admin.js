const CATS = [
  'Mouse', 'Keyboards', 'Headphones & Audio', 'Storage', 'RAM & Memory', 'Networking',
  'Cables & Converters', 'Laptop Accessories', 'Computer Accessories', 'Tools & Repair',
  'Adapters & Hubs', 'Replacement Keyboards', 'Batteries', 'Chargers', 'Screens',
];

const state = { pin: sessionStorage.getItem('gs-admin-pin') || '', products: [], hero: { slides: [] }, showAll: false };

function $(id) { return document.getElementById(id); }

async function api(path, body) {
  const res = await fetch(path, {
    method: body ? 'POST' : 'GET',
    headers: {
      'Content-Type': 'application/json',
      'x-admin-pin': state.pin,
    },
    body: body ? JSON.stringify(body) : undefined,
  });
  const data = await res.json().catch(() => ({}));
  if (!res.ok) throw new Error(data.error || res.statusText);
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

function filtered() {
  const q = ($('q').value || '').toLowerCase().trim();
  const cat = $('cat').value;
  const missing = $('missing').checked;
  return state.products.filter((p) => {
    if (cat && p.cat !== cat) return false;
    if (missing && p.img) return false;
    const blob = `${p.name} ${p.cat} ${p.sub || ''} ${p.also || ''}`.toLowerCase();
    if (q && !q.split(/\s+/).every((t) => t && blob.includes(t))) return false;
    return true;
  });
}

function photoCell(p) {
  if (p.img) return `<img class="ad-thumb" src="${p.img}" alt="">`;
  return `<div class="ad-ph">no<br>photo</div>`;
}

function rowHTML(p) {
  const catOpts = CATS.map((c) => `<option${c === p.cat ? ' selected' : ''}>${c}</option>`).join('');
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
  const cap = state.showAll ? rows.length : 80;
  $('count').textContent = rows.length + ' / ' + state.products.length + ' SKUs';
  $('rows').innerHTML = rows.slice(0, cap).map(rowHTML).join('') +
    (!state.showAll && rows.length > 80 ? `<tr><td colspan="8">Showing 80 of ${rows.length}. Press Show all or search.</td></tr>` : '');
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
  ['dash', 'stock', 'hero'].forEach((t) => {
    const el = $('panel-' + t);
    if (el) el.hidden = t !== id;
  });
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
  state.products = data.products || [];
  $('gate').hidden = true;
  $('app').hidden = false;
  const sel = $('cat');
  sel.innerHTML = '<option value="">All categories</option>' + CATS.map((c) => `<option>${c}</option>`).join('');
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

$('login').addEventListener('submit', async (e) => {
  e.preventDefault();
  const err = $('login-err');
  err.hidden = true;
  state.pin = $('pin').value;
  try {
    await api('/api/admin/login', { pin: state.pin });
    sessionStorage.setItem('gs-admin-pin', state.pin);
    await openDesk();
  } catch (ex) {
    err.hidden = false;
    err.textContent = ex.message;
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
['q', 'cat', 'missing'].forEach((id) => $(id).addEventListener('input', render));
$('cat').addEventListener('change', render);
$('missing').addEventListener('change', render);
$('show-all').addEventListener('click', () => {
  state.showAll = true;
  render();
});
$('add-item').addEventListener('click', async () => {
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
    state.showAll = true;
    status('Added. Type the name, price or Ask, photo or no photo, then Save.', true);
    render();
  } catch (ex) {
    status(ex.message, false);
  }
});

$('pin-form').addEventListener('submit', async (e) => {
  e.preventDefault();
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
    const out = await api('/api/admin/upload', { id, name: file.name, data });
    const i = state.products.findIndex((x) => String(x.id) === String(out.product.id));
    if (i >= 0) state.products[i] = out.product;
    status('Uploaded photo for ' + out.product.name, true);
    render();
  } catch (ex) {
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
  try {
    state.hero = await api('/api/admin/hero', readHeroFromDom());
    renderHero();
    status('Hero saved', true);
  } catch (ex) {
    status(ex.message, false);
  }
});

if (state.pin) {
  openDesk().catch(() => {
    sessionStorage.removeItem('gs-admin-pin');
    state.pin = '';
  });
}
