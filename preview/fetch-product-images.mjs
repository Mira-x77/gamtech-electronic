import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { findProductImage, needsStorePhoto, slugName } from './lib/find-product-image.mjs';
import {
  applyOverrides,
  loadProducts,
  loadOverrides,
  saveOverrides,
  upsertOverride,
  writeCatalogData,
  paths,
} from './lib/catalog-store.mjs';

const root = path.join(path.dirname(fileURLToPath(import.meta.url)), '..');
const limit = Number(process.env.FETCH_LIMIT || 0);
const products = applyOverrides(loadProducts(root), loadOverrides(root));
const targets = products.filter((p) => needsStorePhoto(p) && !p.img);
const work = limit > 0 ? targets.slice(0, limit) : targets;
const imgDir = path.join(paths(root).imgDir, 'fetched');
fs.mkdirSync(imgDir, { recursive: true });

console.log('Need photos:', targets.length, 'fetching:', work.length);
let ok = 0;
let fail = 0;
const concurrency = Number(process.env.FETCH_CONCURRENCY || 1);
let cursor = 0;

async function one(p, n) {
  process.stdout.write(`[${n}/${work.length}] ${p.name} ... `);
  try {
    const hit = await findProductImage(p);
    if (!hit) {
      fail += 1;
      console.log('none');
      return;
    }
    const file = `${p.id}-${slugName(p.name)}${hit.ext}`;
    fs.writeFileSync(path.join(imgDir, file), hit.buf);
    p.img = '/product-images/fetched/' + file;
    upsertOverride(root, p);
    ok += 1;
    if (ok % 10 === 0) writeCatalogData(root, products);
    console.log('ok', hit.ext, Math.round(hit.buf.length / 1024) + 'kb');
  } catch (err) {
    fail += 1;
    console.log('err', err.message || err);
  }
}

async function worker() {
  while (cursor < work.length) {
    const n = cursor + 1;
    const p = work[cursor];
    cursor += 1;
    await one(p, n);
    await new Promise((r) => setTimeout(r, 500));
  }
}

await Promise.all(Array.from({ length: concurrency }, () => worker()));

saveOverrides(root, loadOverrides(root));
writeCatalogData(root, products);
console.log('Done. photos', ok, 'misses', fail);
