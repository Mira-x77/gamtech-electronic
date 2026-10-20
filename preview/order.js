function formatCFA(n) {
  return Math.round(Number(n) || 0).toLocaleString('fr-FR') + ' CFA';
}

function decodeOrder(raw) {
  const pad = raw.replace(/-/g, '+').replace(/_/g, '/');
  const b64 = pad + '==='.slice((pad.length + 3) % 4);
  const json = decodeURIComponent(escape(atob(b64)));
  const c = JSON.parse(json);
  return {
    id: c.i || c.id || '',
    name: c.n || c.name || '',
    total: c.t != null ? c.t : c.total,
    items: (c.l || c.items || []).map((row) => {
      if (Array.isArray(row)) return { name: row[0], qty: row[1], price: row[2] };
      return { name: row.name, qty: row.qty, price: row.price };
    }),
  };
}

function esc(s) {
  return String(s || '').replace(/[&<>"']/g, (ch) => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
  }[ch]));
}

const params = new URLSearchParams(location.search);
const packed = params.get('c') || '';
const miss = document.getElementById('or-miss');
try {
  if (!packed) throw new Error('empty');
  const order = decodeOrder(packed);
  document.getElementById('or-name').textContent = order.name || 'Customer';
  document.getElementById('or-id').textContent = order.id ? 'Cart ' + order.id : '';
  document.getElementById('or-rows').innerHTML = (order.items || []).map((item) => {
    const line = (Number(item.price) || 0) * (Number(item.qty) || 1);
    return `<tr>
      <td>${esc(item.name)}</td>
      <td>${esc(item.qty)}</td>
      <td>${item.price ? formatCFA(item.price) : 'Ask'}</td>
      <td>${item.price ? formatCFA(line) : '—'}</td>
    </tr>`;
  }).join('');
  document.getElementById('or-total').textContent = 'Total ' + formatCFA(order.total);
} catch {
  miss.hidden = false;
}
