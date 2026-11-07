// Build the photo review queue.
//
//   node preview/find-image-candidates.mjs
//   node preview/find-image-candidates.mjs --limit=20
//   node preview/find-image-candidates.mjs --cat=Chargers
//   node preview/find-image-candidates.mjs --id=144 --force
//
// Only products with a known brand + model number are queued. Results are
// strictly verified but never published: the stock desk shows the thumbnails
// and you approve one by one.

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { applyOverrides, loadOverrides, paths } from './lib/catalog-store.mjs';
import { findProductImageCandidates, isExactModelItem } from './lib/find-product-image.mjs';

const root = path.join(path.dirname(fileURLToPath(import.meta.url)), '..');
const args = process.argv.slice(2);
const arg = (name, fallback = '') => {
  const hit = args.find((a) => a === '--' + name || a.startsWith('--' + name + '='));
  if (!hit) return fallback;
  const eq = hit.indexOf('=');
  return eq >= 0 ? hit.slice(eq + 1) : 'true';
};
const limit = Number(arg('limit', '0')) || 0;
const cat = arg('cat', '');
const onlyId = arg('id', '');
const force = arg('force', '') === 'true';
const concurrency = Math.max(1, Number(arg('concurrency', '2')) || 2);

const queueFile = path.join(root, 'preview/data/image-candidates.json');
const thumbDir = path.join(root, 'preview/data/image-review');

const catalog = applyOverrides(
  JSON.parse(
    (() => {
      const t = fs.readFileSync(paths(root).catalogJs, 'utf8');
      return t.slice(t.indexOf('['), t.lastIndexOf(']') + 1);
    })()
  ),
  loadOverrides(root)
);

let queue = { generatedAt: '', items: [] };
if (fs.existsSync(queueFile) && !force) {
  try {
    queue = JSON.parse(fs.readFileSync(queueFile, 'utf8'));
  } catch {
    queue = { generatedAt: '', items: [] };
  }
}
const queued = new Set(queue.items.map((x) => String(x.id)));

let targets = catalog.filter((p) => !p.img && isExactModelItem(p));
if (cat) targets = targets.filter((p) => String(p.cat).toLowerCase() === cat.toLowerCase());
if (onlyId) targets = targets.filter((p) => String(p.id) === String(onlyId));
targets = targets.filter((p) => force || !queued.has(String(p.id)));
if (limit > 0) targets = targets.slice(0, limit);

console.log(`Exact-model items in catalog: ${catalog.filter((p) => !p.img && isExactModelItem(p)).length}`);
console.log(`Queued in file: ${queue.items.length} · processing now: ${targets.length}`);

function timedFetch(url, opts = {}, ms = 15000) {
  const ctrl = new AbortController();
  const t = setTimeout(() => ctrl.abort(), ms);
  return fetch(url, { ...opts, signal: ctrl.signal }).finally(() => clearTimeout(t));
}

async function saveThumb(url, dir, index) {
  try {
    const res = await timedFetch(url, { redirect: 'follow' });
    if (!res.ok) return '';
    const type = String(res.headers.get('content-type') || '');
    if (type.includes('text/html')) return '';
    const buf = Buffer.from(await res.arrayBuffer());
    if (buf.length < 500 || buf.length > 6_000_000) return '';
    let ext = '.jpg';
    if (type.includes('png') || (buf[0] === 0x89 && buf[1] === 0x50)) ext = '.png';
    else if (type.includes('webp') || buf.slice(8, 12).toString() === 'WEBP') ext = '.webp';
    else if (type.includes('gif') || buf.slice(0, 3).toString() === 'GIF') ext = '.gif';
    const name = `${index}${ext}`;
    fs.mkdirSync(dir, { recursive: true });
    fs.writeFileSync(path.join(dir, name), buf);
    return name;
  } catch {
    return '';
  }
}

let done = 0;
let added = 0;
async function one(product) {
  const candidates = await findProductImageCandidates(product, { limit: 5 });
  const dir = path.join(thumbDir, String(product.id));
  const saved = [];
  for (let i = 0; i < candidates.length; i++) {
    const thumb = candidates[i].thumbnail || candidates[i].image;
    const file = await saveThumb(thumb, dir, i);
    saved.push({
      ...candidates[i],
      file: file ? `/data/image-review/${product.id}/${file}` : '',
    });
  }
  done += 1;
  const item = {
    id: product.id,
    name: product.name,
    cat: product.cat,
    sub: product.sub || '',
    candidates: saved,
  };
  queue.items = queue.items.filter((x) => String(x.id) !== String(product.id));
  queue.items.push(item);
  queue.items.sort((a, b) => a.name.localeCompare(b.name));
  queue.generatedAt = new Date().toISOString();
  fs.mkdirSync(path.dirname(queueFile), { recursive: true });
  fs.writeFileSync(queueFile, JSON.stringify(queue, null, 2) + '\n');
  added += 1;
  console.log(
    `[${done}/${targets.length}] ${product.name} → ${saved.length} verified candidate(s)` +
      (saved.length ? '' : ' (kept blank)')
  );
}

let cursor = 0;
async function worker() {
  while (cursor < targets.length) {
    const product = targets[cursor++];
    try {
      await one(product);
    } catch (err) {
      console.warn(`Failed ${product.name}: ${err.message}`);
    }
  }
}
await Promise.all(Array.from({ length: concurrency }, worker));

console.log(`\nDone. Added ${added} product(s). Queue now holds ${queue.items.length} product(s).`);
console.log(`Review at: http://localhost:3000/admin01.html → Photos`);
