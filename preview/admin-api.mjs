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

function checkPin(req, cfg) {
  return String(req.headers['x-admin-pin'] || '') === String(cfg.pin);
}

export function adminApi(root) {
  return {
    name: 'gamtech-admin-api',
    configureServer(server) {
      server.middlewares.use(async (req, res, next) => {
        const url = req.url ? req.url.split('?')[0] : '';
        if (!url.startsWith('/api/admin/')) return next();
        try {
          const cfg = loadAdminConfig(root);
          if (req.method === 'POST' && url === '/api/admin/login') {
            const body = JSON.parse((await readBody(req)).toString('utf8') || '{}');
            if (String(body.pin || '') !== String(cfg.pin)) return send(res, 401, { error: 'Wrong PIN' });
            return send(res, 200, { ok: true });
          }
          if (!checkPin(req, cfg)) return send(res, 401, { error: 'Sign in required' });

          if (req.method === 'GET' && url === '/api/admin/catalog') {
            return send(res, 200, { products: loadProducts(root), pinSet: true });
          }

          if (req.method === 'POST' && url === '/api/admin/pin') {
            const body = JSON.parse((await readBody(req)).toString('utf8') || '{}');
            const pin = String(body.pin || '').trim();
            if (pin.length < 4) return send(res, 400, { error: 'PIN must be at least 4 characters' });
            saveJson(paths(root).adminConfig, { pin });
            return send(res, 200, { ok: true });
          }

          if (req.method === 'POST' && url === '/api/admin/product') {
            const body = JSON.parse((await readBody(req)).toString('utf8') || '{}');
            const products = loadProducts(root);
            let p = products.find((x) => String(x.id) === String(body.id));
            if (!p) {
              const id = products.reduce((m, x) => Math.max(m, Number(x.id) || 0), 0) + 1;
              p = { id, name: body.name || 'New item', cat: body.cat || 'Computer Accessories', sub: body.sub || '', img: null, price: null, was: null, listOnly: false };
              products.push(p);
            }
            for (const key of ['name', 'cat', 'sub', 'img', 'price', 'was', 'listOnly', 'confirmUrl', 'hidden']) {
              if (body[key] !== undefined) p[key] = body[key];
            }
            if (body.price === '') p.price = null;
            upsertOverride(root, p);
            writeCatalogData(root, products);
            return send(res, 200, { product: p });
          }

          if (req.method === 'POST' && url === '/api/admin/upload') {
            const body = JSON.parse((await readBody(req)).toString('utf8') || '{}');
            const products = loadProducts(root);
            const p = products.find((x) => String(x.id) === String(body.id));
            if (!p) return send(res, 404, { error: 'Product not found' });
            const raw = String(body.data || '');
            const comma = raw.indexOf(',');
            const b64 = comma >= 0 ? raw.slice(comma + 1) : raw;
            const buf = Buffer.from(b64, 'base64');
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
            const body = JSON.parse((await readBody(req)).toString('utf8') || '{}');
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
  };
}
