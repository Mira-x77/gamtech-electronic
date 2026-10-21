import fs from 'fs';
import path from 'path';

export function normName(s) {
  return String(s || '')
    .toLowerCase()
    .replace(/^gamtech\s+/i, '')
    .replace(/[^a-z0-9]+/g, ' ')
    .trim();
}

export function paths(root) {
  return {
    dataDir: path.join(root, 'preview/data'),
    overrides: path.join(root, 'preview/data/overrides.json'),
    adminConfig: path.join(root, 'preview/data/admin-config.json'),
    catalogJs: path.join(root, 'preview/catalog-data.js'),
    catalogJsPublic: path.join(root, 'preview/public/catalog-data.js'),
    imgDir: path.join(root, 'wp-content/uploads/product-images'),
    visits: path.join(root, 'preview/data/visits.json'),
    hero: path.join(root, 'preview/data/hero.json'),
    heroPublic: path.join(root, 'preview/public/hero.json'),
    orders: path.join(root, 'preview/data/orders.json'),
  };
}

export function loadJson(file, fallback) {
  if (!fs.existsSync(file)) return fallback;
  try {
    return JSON.parse(fs.readFileSync(file, 'utf8'));
  } catch {
    return fallback;
  }
}

export function saveJson(file, data) {
  fs.mkdirSync(path.dirname(file), { recursive: true });
  fs.writeFileSync(file, JSON.stringify(data, null, 2) + '\n');
}

export function loadProducts(root) {
  const file = paths(root).catalogJs;
  if (!fs.existsSync(file)) return [];
  const t = fs.readFileSync(file, 'utf8');
  const start = t.indexOf('[');
  const end = t.lastIndexOf(']');
  if (start < 0 || end < start) return [];
  return JSON.parse(t.slice(start, end + 1));
}

export function writeCatalogData(root, products) {
  const p = paths(root);
  const body = `window.GAMTECH_PRODUCTS = ${JSON.stringify(products, null, 2)};\n`;
  fs.mkdirSync(path.dirname(p.catalogJs), { recursive: true });
  fs.writeFileSync(p.catalogJs, body);
  fs.mkdirSync(path.dirname(p.catalogJsPublic), { recursive: true });
  fs.writeFileSync(p.catalogJsPublic, body);
}

export function loadOverrides(root) {
  return loadJson(paths(root).overrides, {});
}

export function saveOverrides(root, ov) {
  saveJson(paths(root).overrides, ov);
}

export function applyOverrides(products, ov) {
  if (!ov || typeof ov !== 'object') return products;
  for (const p of products) {
    const hit = ov[String(p.id)] || ov[normName(p.name)];
    if (!hit || typeof hit !== 'object') continue;
    if (hit.deleted) {
      p.deleted = true;
      continue;
    }
    for (const key of ['name', 'cat', 'sub', 'img', 'price', 'was', 'listOnly', 'confirmUrl', 'hidden', 'also']) {
      if (hit[key] !== undefined) p[key] = hit[key];
    }
  }
  return products.filter((p) => !p.deleted && !p.hidden);
}

export function upsertOverride(root, product) {
  const ov = loadOverrides(root);
  const key = String(product.id);
  ov[key] = {
    ...(ov[key] || {}),
    id: product.id,
    name: product.name,
    cat: product.cat,
    sub: product.sub,
    img: product.img,
    price: product.price,
    was: product.was ?? null,
    listOnly: !!product.listOnly,
    confirmUrl: product.confirmUrl || '',
    hidden: !!product.hidden,
    deleted: !!product.deleted,
    also: product.also || '',
  };
  ov[normName(product.name)] = ov[key];
  saveOverrides(root, ov);
  return ov;
}

export function loadAdminConfig(root) {
  const file = paths(root).adminConfig;
  const cfg = loadJson(file, null);
  if (cfg && cfg.pin) return cfg;
  const created = { pin: 'gamtech' };
  saveJson(file, created);
  return created;
}
