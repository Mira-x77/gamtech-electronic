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
import { findProductImage, slugName, needsStorePhoto } from './lib/find-product-image.mjs';
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
            return send(res, 200, { products: loadProducts(root), pinSet: true });
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

          if (req.method === 'POST' && url === '/api/admin/fetch-image') {
            const body = parseJsonBody(await readBody(req));
            const products = loadProducts(root);
            const p = products.find((x) => String(x.id) === String(body.id));
            if (!p) return send(res, 404, { error: 'Product not found' });
            if (!needsStorePhoto({ ...p, listOnly: false, cat: p.cat }) && !body.force) {
              return send(res, 400, { error: 'This category stays name-only. Tick force to search anyway.' });
            }
            const hit = await findProductImage(p);
            if (!hit) return send(res, 404, { error: 'No photo found for this name' });
            const dir = path.join(paths(root).imgDir, 'fetched');
            fs.mkdirSync(dir, { recursive: true });
            const file = `${p.id}-${slugName(p.name)}${hit.ext}`;
            fs.writeFileSync(path.join(dir, file), hit.buf);
            p.img = '/product-images/fetched/' + file;
            p.listOnly = false;
            upsertOverride(root, p);
            writeCatalogData(root, products);
            return send(res, 200, { product: p, title: hit.title });
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
