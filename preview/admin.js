const CATS = [
  'Mouse', 'Keyboards', 'Headphones & Audio', 'Storage', 'RAM & Memory', 'Networking',
  'Cables & Converters', 'Laptop Accessories', 'Computer Accessories', 'Tools & Repair',
  'Adapters & Hubs', 'Replacement Keyboards', 'Batteries', 'Chargers', 'Screens',
];

const state = { pin: sessionStorage.getItem('gs-admin-pin') || '', products: [] };

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

function filtered() {
  const q = ($('q').value || '').toLowerCase().trim();
  const cat = $('cat').value;
  const missing = $('missing').checked;
  return state.products.filter((p) => {
    if (cat && p.cat !== cat) return false;
    if (missing && p.img) return false;
    if (q && !`${p.name} ${p.cat} ${p.sub || ''}`.toLowerCase().includes(q)) return false;
    return true;
  });
}

function photoCell(p) {
  if (p.img) return `<img class="ad-thumb" src="${p.img}" alt="">`;
  return `<div class="ad-ph">no<br>photo</div>`;
}

function rowHTML(p) {
  const catOpts = CATS.map((c) => `<option${c === p.cat ? ' selected' : ''}>${c}</option>`).join('');
  return `<tr data-id="${p.id}">
    <td>${photoCell(p)}</td>
    <td><input type="text" data-k="name" value="${String(p.name || '').replace(/"/g, '&quot;')}"></td>
    <td><select data-k="cat">${catOpts}</select></td>
    <td><input type="number" data-k="price" value="${p.price ?? ''}" min="0" step="100"></td>
    <td><input type="checkbox" data-k="listOnly" ${p.listOnly ? 'checked' : ''}></td>
    <td class="ad-actions">
      <button type="button" data-act="save">Save</button>
      <button type="button" data-act="find">Find photo</button>
      <label class="ad-file">Upload<input type="file" accept="image/*" data-act="upload"></label>
    </td>
  </tr>`;
}

function render() {
  const rows = filtered();
  $('count').textContent = rows.length + ' / ' + state.products.length + ' SKUs';
  $('rows').innerHTML = rows.slice(0, 80).map(rowHTML).join('') +
    (rows.length > 80 ? `<tr><td colspan="6">Showing 80 of ${rows.length}. Narrow the search to edit the rest.</td></tr>` : '');
}

function readRow(tr) {
  const id = tr.getAttribute('data-id');
  const p = state.products.find((x) => String(x.id) === String(id));
  const name = tr.querySelector('[data-k=name]').value.trim();
  const cat = tr.querySelector('[data-k=cat]').value;
  const priceRaw = tr.querySelector('[data-k=price]').value;
  const listOnly = tr.querySelector('[data-k=listOnly]').checked;
  return { ...p, name, cat, listOnly, price: priceRaw === '' ? null : Number(priceRaw) };
}

async function openDesk() {
  const data = await api('/api/admin/catalog');
  state.products = data.products || [];
  $('gate').hidden = true;
  $('app').hidden = false;
  const sel = $('cat');
  sel.innerHTML = '<option value="">All categories</option>' + CATS.map((c) => `<option>${c}</option>`).join('');
  render();
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

['q', 'cat', 'missing'].forEach((id) => $(id).addEventListener('input', render));
$('cat').addEventListener('change', render);
$('missing').addEventListener('change', render);

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
      status('Saved ' + out.product.name, true);
      render();
    }
    if (btn.dataset.act === 'find') {
      status('Searching photo for ' + payload.name + '…');
      const out = await api('/api/admin/fetch-image', { id: payload.id, force: true });
      const i = state.products.findIndex((x) => String(x.id) === String(out.product.id));
      if (i >= 0) state.products[i] = out.product;
      status('Photo set: ' + (out.title || out.product.name), true);
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

if (state.pin) {
  openDesk().catch(() => {
    sessionStorage.removeItem('gs-admin-pin');
    state.pin = '';
  });
}
