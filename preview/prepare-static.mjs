import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import sharp from 'sharp';

const root = path.join(path.dirname(fileURLToPath(import.meta.url)), '..');
const pub = path.join(root, 'preview/public');
const imgSrc = path.join(root, 'wp-content/uploads/product-images');
const assets = path.join(root, 'wp-content/themes/woodmart-child/assets');
const catalogFile = path.join(root, 'preview/catalog-data.js');

function copyFile(from, to) {
  fs.mkdirSync(path.dirname(to), { recursive: true });
  fs.copyFileSync(from, to);
}

async function writeWebImage(from, to) {
  fs.mkdirSync(path.dirname(to), { recursive: true });
  const ext = path.extname(from).toLowerCase();
  const img = sharp(from, { failOn: 'none' }).rotate().resize(1000, 1000, {
    fit: 'inside',
    withoutEnlargement: true,
  });
  if (ext === '.png') {
    await img.png({ compressionLevel: 9, palette: true }).toFile(to);
  } else if (ext === '.webp') {
    await img.webp({ quality: 76 }).toFile(to);
  } else if (ext === '.gif') {
    copyFile(from, to);
  } else {
    await img.jpeg({ quality: 76, mozjpeg: true }).toFile(to);
  }
}

async function main() {
  fs.rmSync(pub, { recursive: true, force: true });
  fs.mkdirSync(pub, { recursive: true });

  copyFile(path.join(assets, 'gamtech-unified.css'), path.join(pub, 'gamtech-unified.css'));
  copyFile(path.join(assets, 'gamtech-unified.js'), path.join(pub, 'gamtech-unified.js'));
  copyFile(catalogFile, path.join(pub, 'catalog-data.js'));
  copyFile(path.join(root, 'preview/catalog.js'), path.join(pub, 'catalog.js'));

  const catalog = fs.readFileSync(catalogFile, 'utf8');
  const imgs = new Set();
  for (const m of catalog.matchAll(/"img":\s*"\/product-images\/([^"]+)"/g)) {
    imgs.add(decodeURIComponent(m[1]));
  }

  const heroFile = path.join(root, 'preview/data/hero.json');
  if (fs.existsSync(heroFile)) {
    copyFile(heroFile, path.join(pub, 'hero.json'));
    const hero = JSON.parse(fs.readFileSync(heroFile, 'utf8'));
    for (const slide of hero.slides || []) {
      const m = String(slide.img || '').match(/^\/product-images\/(.+)$/);
      if (m) imgs.add(decodeURIComponent(m[1]));
    }
  }

  const storefront = path.join(assets, 'images/storefront.jpg');
  if (fs.existsSync(storefront)) {
    await writeWebImage(storefront, path.join(pub, 'images/storefront.jpg'));
  }

  let copied = 0;
  for (const rel of imgs) {
    const from = path.join(imgSrc, rel);
    if (!fs.existsSync(from)) {
      console.warn('Missing product image', rel);
      continue;
    }
    await writeWebImage(from, path.join(pub, 'product-images', rel));
    copied++;
  }

  console.log('Prepared static public folder:', copied, 'product images');
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
