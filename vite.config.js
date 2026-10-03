import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { defineConfig } from 'vite';

const rootDir = path.dirname(fileURLToPath(import.meta.url));

const EXT_MIME = {
  '.css': 'text/css',
  '.js': 'application/javascript',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.jpeg': 'image/jpeg',
  '.jfif': 'image/jpeg',
  '.webp': 'image/webp',
  '.gif': 'image/gif',
  '.svg': 'image/svg+xml',
  '.avif': 'image/avif',
  '.bmp': 'image/bmp',
};

function sniffMime(buf, ext) {
  if (buf.length >= 3 && buf[0] === 0xff && buf[1] === 0xd8 && buf[2] === 0xff) return 'image/jpeg';
  if (buf.length >= 8 && buf[0] === 0x89 && buf[1] === 0x50 && buf[2] === 0x4e && buf[3] === 0x47) return 'image/png';
  if (buf.length >= 6 && buf[0] === 0x47 && buf[1] === 0x49 && buf[2] === 0x46) return 'image/gif';
  if (buf.length >= 12 && buf[0] === 0x52 && buf[1] === 0x49 && buf[2] === 0x46 && buf[3] === 0x46 && buf[8] === 0x57 && buf[9] === 0x45 && buf[10] === 0x42 && buf[11] === 0x50) {
    return 'image/webp';
  }
  return EXT_MIME[ext] || 'application/octet-stream';
}

function serveFolder(urlPrefix, dir) {
  return {
    name: 'serve-' + urlPrefix.replace(/\W+/g, '-'),
    configureServer(server) {
      server.middlewares.use((req, res, next) => {
        if (!req.url || !req.url.startsWith(urlPrefix)) return next();
        const rel = decodeURIComponent(req.url.split('?')[0].slice(urlPrefix.length));
        const file = path.normalize(path.join(dir, rel));
        if (!file.startsWith(path.normalize(dir))) return next();
        if (!fs.existsSync(file) || !fs.statSync(file).isFile()) return next();
        const stat = fs.statSync(file);
        const fd = fs.openSync(file, 'r');
        const head = Buffer.alloc(Math.min(16, stat.size));
        fs.readSync(fd, head, 0, head.length, 0);
        fs.closeSync(fd);
        res.setHeader('Content-Type', sniffMime(head, path.extname(file).toLowerCase()));
        res.setHeader('Content-Length', String(stat.size));
        res.setHeader('Cache-Control', 'no-cache');
        fs.createReadStream(file).pipe(res);
      });
    },
  };
}

export default defineConfig({
  root: path.join(rootDir, 'preview'),
  publicDir: path.join(rootDir, 'preview/public'),
  plugins: [
    serveFolder('/product-images/', path.join(rootDir, 'wp-content/uploads/product-images')),
  ],
  server: {
    port: 3000,
    host: true,
    strictPort: false,
    fs: {
      allow: [rootDir],
    },
  },
  build: {
    outDir: path.join(rootDir, 'preview/dist'),
    emptyOutDir: true,
    assetsDir: 'assets',
  },
});
