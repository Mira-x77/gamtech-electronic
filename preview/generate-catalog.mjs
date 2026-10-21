import fs from 'fs';
import path from 'path';
import { spawnSync } from 'child_process';
import { fileURLToPath } from 'url';
import { applyOverrides, loadOverrides, writeCatalogData } from './lib/catalog-store.mjs';

const root = path.join(path.dirname(fileURLToPath(import.meta.url)), '..');
const imgDir = path.join(root, 'wp-content/uploads/product-images');
const chargerDir = path.join(imgDir, 'chargers');
const phpFile = path.join(root, 'wp-content/themes/woodmart-child/inc/gamtech-import-products.php');
const IMAGE_RE = /\.(jpe?g|jfif|png|webp|gif|avif|bmp|tiff?|heic|heif|jp2|j2k|ico)$/i;

const skipNorm = process.env.VERCEL || process.env.SKIP_IMAGE_NORMALIZE;
if (!skipNorm) {
  const py = process.platform === 'win32' ? 'python' : 'python3';
  const norm = spawnSync(py, [path.join(root, 'preview/normalize-images.py')], {
    cwd: root,
    stdio: 'inherit',
  });
  if (norm.status) {
    console.warn('Image normalize exited with', norm.status);
  }
}

const php = fs.readFileSync(phpFile, 'utf8');
const re =
  /array\(\s*'((?:\\'|[^'])*)'\s*,\s*'((?:\\'|[^'])*)'\s*,\s*'((?:\\'|[^'])*)'\s*,\s*(null|'((?:\\'|[^'])*)')\s*(?:,\s*(null|'((?:\\'|[^'])*)'|\d+(?:\.\d+)?))?\s*\)/g;

function normName(s) {
  return String(s || '')
    .toLowerCase()
    .replace(/^gamtech\s+/i, '')
    .replace(/[^a-z0-9]+/g, ' ')
    .trim();
}

function applyPrice(product, hit) {
  if (hit == null || hit === '' || hit === 'null') return;
  if (typeof hit === 'number') {
    product.price = hit;
    return;
  }
  if (typeof hit === 'string' && hit.trim() !== '') {
    const n = Number(hit);
    if (!Number.isNaN(n)) product.price = n;
    return;
  }
  product.price = hit.price ?? hit.regular ?? product.price;
  if (hit.was != null && hit.was !== '') product.was = Number(hit.was);
  else if (hit.sale != null && hit.sale !== '' && hit.regular != null) product.was = Number(hit.regular);
}

function imageUrl(file) {
  return '/product-images/' + file.split(/[/\\]/).map(encodeURIComponent).join('/');
}

const products = [];
let id = 1;
let m;
while ((m = re.exec(php))) {
  const name = m[1];
  const file = m[2];
  const cat = m[3];
  const sub = m[4] === 'null' ? null : m[5];
  const rawPrice = m[6] == null || m[6] === 'null' ? null : m[7] != null ? m[7] : m[6];
  const abs = path.join(imgDir, file);
  if (!fs.existsSync(abs)) continue;
  const product = {
    id: id++,
    name,
    cat,
    sub,
    img: imageUrl(file),
    price: null,
    was: null,
  };
  applyPrice(product, rawPrice);
  products.push(product);
}

if (fs.existsSync(chargerDir)) {
  const groups = new Map();
  for (const file of fs.readdirSync(chargerDir)) {
    if (!IMAGE_RE.test(file)) continue;
    const base = file.replace(IMAGE_RE, '').replace(/-x\d+$/i, '');
    if (!groups.has(base)) groups.set(base, file);
  }
  for (const [base, file] of groups) {
    const pretty = base.replace(/-/g, ' ');
    products.push({
      id: id++,
      name: pretty,
      cat: 'Laptop Accessories',
      sub: 'Chargers',
      img: imageUrl('chargers/' + file),
      price: null,
      was: null,
    });
  }
}

const pricesPath = path.join(root, 'preview/prices.json');
if (fs.existsSync(pricesPath)) {
  const prices = JSON.parse(fs.readFileSync(pricesPath, 'utf8'));
  const entries = Object.entries(prices).map(([key, val]) => [normName(key), val]);
  for (const p of products) {
    const exact = prices[p.name] || prices[String(p.id)];
    if (exact != null) {
      applyPrice(p, exact);
      continue;
    }
    const needle = normName(p.name);
    const hits = entries.filter(([key]) => key === needle);
    if (hits.length === 1) applyPrice(p, hits[0][1]);
  }
}

const listePath = path.join(root, 'preview/prix-acte.json');
if (fs.existsSync(listePath)) {
  const extra = JSON.parse(fs.readFileSync(listePath, 'utf8'));
  const byNorm = new Map(products.map((p) => [normName(p.name), p]));
  let added = 0;
  let priced = 0;
  for (const it of extra) {
    const key = normName(it.name);
    if (!key) continue;
    const existing = byNorm.get(key);
    if (existing) {
      if (it.price != null) {
        applyPrice(existing, it.price);
        priced += 1;
      }
      if (it.also) existing.also = [existing.also, it.also].filter(Boolean).join(' ');
      continue;
    }
    const confirmHint = {
      Batteries: 'laptop battery',
      Chargers: 'laptop charger',
      'Replacement Keyboards': 'laptop keyboard',
      Screens: 'laptop screen',
    };
    const confirmQ = [it.name, confirmHint[it.cat] || ''].filter(Boolean).join(' ');
    const product = {
      id: id++,
      name: it.name,
      cat: it.cat,
      sub: it.sub || it.cat,
      img: null,
      price: it.price ?? null,
      was: null,
      listOnly: !!it.listOnly,
      also: it.also || '',
      confirmUrl: 'https://www.google.com/search?udm=2&q=' + encodeURIComponent(confirmQ),
    };
    products.push(product);
    byNorm.set(key, product);
    added += 1;
  }
  console.log('Merged price list:', added, 'new items,', priced, 'prices on existing photos');
}

applyOverrides(products, loadOverrides(root));
writeCatalogData(root, products);
console.log('Wrote', products.length, 'real products to preview/catalog-data.js');
