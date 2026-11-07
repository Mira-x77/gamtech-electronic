import fs from 'fs';
import path from 'path';
import {
  loadAdminConfig,
  loadProducts,
  saveJson,
  paths,
  upsertOverride,
  writeCatalogData,
} from './lib/catalog-store.mjs';
import {
  findProductImageCandidates,
  slugName,
} from './lib/find-product-image.mjs';
import {
  loadHero,
  saveHero,
  recordVisit,
  loadVisitSummary,
  saveOrder,
  loadOrders,
} from './lib/site-store.mjs';

function send(res, code, body) {
  const json = JSON.stringify(body);
  res.statusCode = code;
  res.setHeader('Content-Type', 'application/json; charset=utf-8');
  res.setHeader('Cache-Control', 'no-store');
  res.end(json);
}

function readBody(req) {
  return new Promise((resolve, reject) => {
    const chunks = [];
    req.on('data', (c) => chunks.push(c));
    req.on('end', () => resolve(Buffer.concat(chunks)));
    req.on('error', reject);
  });
}

function parseJsonBody(buf) {
  return JSON.parse(buf.toString('utf8') || '{}');
}

function checkPin(req, cfg) {
  return String(req.headers['x-admin-pin'] || '') === String(cfg.pin);
}

function decodeImage(body) {
  const raw = String(body.data || '');
  const comma = raw.indexOf(',');
  const b64 = comma >= 0 ? raw.slice(comma + 1) : raw;
  return Buffer.from(b64, 'base64');
}

function imageExt(buf, type = '') {
  if (type.includes('png') || (buf[0] === 0x89 && buf[1] === 0x50)) return '.png';
  if (type.includes('webp') || buf.slice(8, 12).toString() === 'WEBP') return '.webp';
  if (type.includes('gif') || buf.slice(0, 3).toString() === 'GIF') return '.gif';
  return '.jpg';
}

const BROWSER_UA =
  'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

async function downloadImage(url) {
  if (!/^https?:\/\//i.test(String(url || ''))) return null;
  let referer = '';
  try {
    referer = new URL(url).origin + '/';
  } catch {
    referer = '';
  }
  // Some shops reject a bare request but serve the image when it looks like a
  // normal browser navigation, so retry with a Referer/Accept pair.
  const variants = [
    { 'User-Agent': BROWSER_UA, Accept: 'image/avif,image/webp,image/*,*/*;q=0.8', Referer: referer },
    { 'User-Agent': 'Mozilla/5.0', Accept: 'image/avif,image/webp,image/*,*/*;q=0.8', Referer: referer },
    { 'User-Agent': BROWSER_UA },
    { 'User-Agent': 'Mozilla/5.0' },
  ];
  for (const headers of variants) {
    const ctrl = new AbortController();
    const timer = setTimeout(() => ctrl.abort(), 20000);
    try {
      const res = await fetch(url, { signal: ctrl.signal, redirect: 'follow', headers });
      if (!res.ok) continue;
      const type = String(res.headers.get('content-type') || '');
      if (type.includes('text/html')) continue;
      const buf = Buffer.from(await res.arrayBuffer());
      if (buf.length < 800 || buf.length > 8_000_000) continue;
      return { buf, ext: imageExt(buf, type) };
    } catch {
      // try the next header variant
    } finally {
      clearTimeout(timer);
    }
  }
  return null;
}

function queueFile(root) {
  return path.join(paths(root).dataDir, 'image-candidates.json');
}

function loadQueue(root) {
  try {
    return JSON.parse(fs.readFileSync(queueFile(root), 'utf8'));
  } catch {
    return { generatedAt: '', items: [] };
  }
}

function saveQueue(root, queue) {
  fs.mkdirSync(path.dirname(queueFile(root)), { recursive: true });
  fs.writeFileSync(queueFile(root), JSON.stringify(queue, null, 2) + '\n');
}

export function adminApi(root) {
  return {
    name: 'gamtech-admin-api',
    configureServer(server) {
      server.middlewares.use((req, _res, next) => {
        const url = req.url ? req.url.split('?')[0] : '';
        if (url === '/admin01' || url === '/admin01/') req.url = '/admin01.html';
        next();
      });
      server.middlewares.use(async (req, res, next) => {
        const url = req.url ? req.url.split('?')[0] : '';
        const isPublicApi =
          url === '/api/visit' || url === '/api/hero' || url === '/api/order';
        if (!url.startsWith('/api/admin/') && !isPublicApi) return next();
        try {
          if (req.method === 'POST' && url === '/api/visit') {
            const body = parseJsonBody(await readBody(req));
            const stats = recordVisit(root, body.deviceId);
            return send(res, 200, { ok: true, stats });
          }
          if (req.method === 'GET' && url === '/api/hero') {
            return send(res, 200, loadHero(root));
          }
          if (req.method === 'POST' && url === '/api/order') {
            const body = parseJsonBody(await readBody(req));
            if (!body.id || !body.name) return send(res, 400, { error: 'Name and cart id required' });
            return send(res, 200, { order: saveOrder(root, body) });
          }

          const cfg = loadAdminConfig(root);
          if (req.method === 'POST' && url === '/api/admin/login') {
            const body = parseJsonBody(await readBody(req));
            if (String(body.pin || '') !== String(cfg.pin)) return send(res, 401, { error: 'Wrong PIN' });
            return send(res, 200, { ok: true });
          }
          if (!checkPin(req, cfg)) return send(res, 401, { error: 'Sign in required' });

          if (req.method === 'GET' && url === '/api/admin/catalog') {
            const products = loadProducts(root).filter((p) => !p.hidden && !p.deleted);
            return send(res, 200, { products, pinSet: true });
          }

          if (req.method === 'GET' && url === '/api/admin/stats') {
            return send(res, 200, { stats: loadVisitSummary(root), orders: loadOrders(root).slice(0, 20) });
          }

          if (req.method === 'GET' && url === '/api/admin/hero') {
            return send(res, 200, loadHero(root));
          }

          if (req.method === 'POST' && url === '/api/admin/hero') {
            const body = parseJsonBody(await readBody(req));
            return send(res, 200, saveHero(root, body));
          }

          if (req.method === 'POST' && url === '/api/admin/hero-image') {
            const body = parseJsonBody(await readBody(req));
            const buf = decodeImage(body);
            if (!buf.length) return send(res, 400, { error: 'Empty image' });
            const idx = Math.max(0, Math.min(5, Number(body.index) || 0));
            const ext = (body.name || '').toLowerCase().endsWith('.png') ? '.png' : '.jpg';
            const dir = path.join(paths(root).imgDir, 'hero');
            fs.mkdirSync(dir, { recursive: true });
            const file = `slide-${idx}-${Date.now()}${ext}`;
            fs.writeFileSync(path.join(dir, file), buf);
            const hero = loadHero(root);
            if (!hero.slides[idx]) hero.slides[idx] = { img: '', tag: '', title: '', highlight: '', text: '', cta: 'Shop Now' };
            hero.slides[idx].img = '/product-images/hero/' + file;
            saveHero(root, hero);
            return send(res, 200, { hero, img: hero.slides[idx].img });
          }

          if (req.method === 'POST' && url === '/api/admin/pin') {
            const body = parseJsonBody(await readBody(req));
            const pin = String(body.pin || '').trim();
            if (pin.length < 4) return send(res, 400, { error: 'PIN must be at least 4 characters' });
            saveJson(paths(root).adminConfig, { pin });
            return send(res, 200, { ok: true });
          }

          if (req.method === 'POST' && url === '/api/admin/product') {
            const body = parseJsonBody(await readBody(req));
            const products = loadProducts(root);
            let p = products.find((x) => String(x.id) === String(body.id));
            if (!p) {
              const id = products.reduce((m, x) => Math.max(m, Number(x.id) || 0), 0) + 1;
              p = { id, name: body.name || 'New item', cat: body.cat || 'Computer Accessories', sub: body.sub || '', img: null, price: null, was: null, listOnly: false };
              products.push(p);
            }
            for (const key of ['name', 'cat', 'sub', 'img', 'price', 'was', 'listOnly', 'confirmUrl', 'hidden', 'also']) {
              if (body[key] !== undefined) p[key] = body[key];
            }
            if (body.price === '' || body.askPrice) p.price = null;
            if (body.img === '' || body.clearPhoto) {
              p.img = null;
              p.listOnly = true;
            }
            upsertOverride(root, p);
            writeCatalogData(root, products);
            return send(res, 200, { product: p });
          }

          if (req.method === 'POST' && url === '/api/admin/delete') {
            const body = parseJsonBody(await readBody(req));
            const products = loadProducts(root);
            const p = products.find((x) => String(x.id) === String(body.id));
            if (!p) return send(res, 404, { error: 'Product not found' });
            p.deleted = true;
            p.hidden = true;
            upsertOverride(root, p);
            const kept = products.filter((x) => String(x.id) !== String(p.id));
            writeCatalogData(root, kept);
            return send(res, 200, { ok: true, id: p.id });
          }

          if (req.method === 'POST' && url === '/api/admin/upload') {
            const body = parseJsonBody(await readBody(req));
            const products = loadProducts(root);
            const p = products.find((x) => String(x.id) === String(body.id));
            if (!p) return send(res, 404, { error: 'Product not found' });
            const buf = decodeImage(body);
            if (!buf.length) return send(res, 400, { error: 'Empty image' });
            const ext = (body.name || '').toLowerCase().endsWith('.png') ? '.png' : '.jpg';
            const dir = path.join(paths(root).imgDir, 'admin');
            fs.mkdirSync(dir, { recursive: true });
            const file = `${p.id}-${slugName(p.name)}${ext}`;
            fs.writeFileSync(path.join(dir, file), buf);
            p.img = '/product-images/admin/' + file;
            p.listOnly = false;
            upsertOverride(root, p);
            writeCatalogData(root, products);
            return send(res, 200, { product: p });
          }

          if (req.method === 'GET' && url === '/api/admin/photo-candidates') {
            const queue = loadQueue(root);
            return send(res, 200, { generatedAt: queue.generatedAt || '', items: queue.items || [] });
          }

          // Candidate search never publishes anything. The desk shows the
          // verified thumbnails and the user approves one explicitly.
          if (req.method === 'POST' && url === '/api/admin/fetch-image') {
            const body = parseJsonBody(await readBody(req));
            const products = loadProducts(root);
            const p = products.find((x) => String(x.id) === String(body.id));
            if (!p) return send(res, 404, { error: 'Product not found' });
            const candidates = await findProductImageCandidates(p, { limit: 6 });
            return send(res, 200, { candidates, requiresApproval: true });
          }

          if (req.method === 'POST' && url === '/api/admin/use-photo') {
            const body = parseJsonBody(await readBody(req));
            const products = loadProducts(root);
            const p = products.find((x) => String(x.id) === String(body.id));
            if (!p) return send(res, 404, { error: 'Product not found' });
            const file = await downloadImage(body.image);
            if (!file) return send(res, 400, { error: 'Could not download that image' });
            const dir = path.join(paths(root).imgDir, 'verified');
            fs.mkdirSync(dir, { recursive: true });
            const name = `${p.id}-${slugName(p.name)}${file.ext}`;
            fs.writeFileSync(path.join(dir, name), file.buf);
            p.img = '/product-images/verified/' + name;
            p.listOnly = false;
            upsertOverride(root, p);
            writeCatalogData(root, products);
            const queue = loadQueue(root);
            queue.items = (queue.items || []).filter((x) => String(x.id) !== String(p.id));
            saveQueue(root, queue);
            return send(res, 200, { product: p });
          }

          if (req.method === 'POST' && url === '/api/admin/skip-photo') {
            const body = parseJsonBody(await readBody(req));
            const queue = loadQueue(root);
            const before = (queue.items || []).length;
            queue.items = (queue.items || []).filter((x) => String(x.id) !== String(body.id));
            saveQueue(root, queue);
            return send(res, 200, { ok: true, removed: before - queue.items.length });
          }

          return send(res, 404, { error: 'Unknown admin route' });
        } catch (err) {
          return send(res, 500, { error: String(err.message || err) });
        }
      });
    },
    configurePreview(server) {
      server.middlewares.use((req, _res, next) => {
        const url = req.url ? req.url.split('?')[0] : '';
        if (url === '/admin01' || url === '/admin01/') req.url = '/admin01.html';
        next();
      });
    },
  };
}
