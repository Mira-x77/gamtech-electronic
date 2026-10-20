import { loadJson, paths, saveJson } from './catalog-store.mjs';

export const DEFAULT_HERO = {
  slides: [
    {
      img: '/images/storefront.jpg',
      tag: 'Boutique GamTech · Lomé',
      title: 'Toutes solutions',
      highlight: 'informatiques ✦',
      text: 'Magasin ouvert à Lomé. Souris, claviers, stockage, réseaux et plus. WhatsApp (00228) 90 59 70 03 / 93 65 55 59',
      cta: 'Shop Now',
    },
    {
      img: '/product-images/Logitech-G435.webp',
      tag: 'Audio Collection',
      title: 'Premium',
      highlight: 'Audio ✦',
      text: 'Headphones, speakers and audio accessories for every need.',
      cta: 'Explore Audio',
    },
    {
      img: '/product-images/Logitech-MX-Keys-S.jpg',
      tag: 'Best Products',
      title: 'Top Rated',
      highlight: 'Gear ✦',
      text: 'Best-selling electronics chosen by our customers.',
      cta: 'View Best Sellers',
    },
  ],
};

function todayKey() {
  return new Date().toISOString().slice(0, 10);
}

export function loadHero(root) {
  const data = loadJson(paths(root).hero, null);
  if (data && Array.isArray(data.slides) && data.slides.length) return data;
  saveHero(root, DEFAULT_HERO);
  return DEFAULT_HERO;
}

export function saveHero(root, hero) {
  const clean = {
    slides: (hero.slides || []).slice(0, 6).map((s) => ({
      img: String(s.img || ''),
      tag: String(s.tag || ''),
      title: String(s.title || ''),
      highlight: String(s.highlight || ''),
      text: String(s.text || ''),
      cta: String(s.cta || 'Shop Now'),
    })),
  };
  saveJson(paths(root).hero, clean);
  try {
    saveJson(paths(root).heroPublic, clean);
  } catch {
    /* public folder may be missing until prepare-static */
  }
  return clean;
}

export function recordVisit(root, deviceId) {
  const id = String(deviceId || '').replace(/[^a-zA-Z0-9_-]/g, '').slice(0, 40);
  if (id.length < 8) return visitSummary(loadJson(paths(root).visits, { total: 0, devices: [], byDay: {} }));
  const file = paths(root).visits;
  const data = loadJson(file, { total: 0, devices: [], byDay: {} });
  const day = todayKey();
  data.total = (Number(data.total) || 0) + 1;
  if (!Array.isArray(data.devices)) data.devices = [];
  if (!data.devices.includes(id)) data.devices.push(id);
  if (data.devices.length > 8000) data.devices = data.devices.slice(-4000);
  if (!data.byDay || typeof data.byDay !== 'object') data.byDay = {};
  if (!data.byDay[day]) data.byDay[day] = { visits: 0, devices: [] };
  data.byDay[day].visits += 1;
  if (!data.byDay[day].devices.includes(id)) data.byDay[day].devices.push(id);
  const keys = Object.keys(data.byDay).sort();
  if (keys.length > 90) {
    keys.slice(0, keys.length - 90).forEach((k) => {
      delete data.byDay[k];
    });
  }
  saveJson(file, data);
  return visitSummary(data);
}

export function visitSummary(data) {
  const src = data || { total: 0, devices: [], byDay: {} };
  const days = [];
  for (let i = 13; i >= 0; i -= 1) {
    const d = new Date();
    d.setDate(d.getDate() - i);
    const key = d.toISOString().slice(0, 10);
    const row = (src.byDay && src.byDay[key]) || { visits: 0, devices: [] };
    days.push({
      day: key,
      visits: Number(row.visits) || 0,
      devices: Array.isArray(row.devices) ? row.devices.length : Number(row.devices) || 0,
    });
  }
  return {
    total: Number(src.total) || 0,
    unique: Array.isArray(src.devices) ? src.devices.length : 0,
    days,
  };
}

export function loadVisitSummary(root) {
  return visitSummary(loadJson(paths(root).visits, { total: 0, devices: [], byDay: {} }));
}

export function saveOrder(root, order) {
  const file = paths(root).orders;
  const list = loadJson(file, []);
  const row = {
    id: String(order.id || ''),
    name: String(order.name || '').slice(0, 80),
    items: Array.isArray(order.items) ? order.items.slice(0, 40) : [],
    total: Number(order.total) || 0,
    at: new Date().toISOString(),
  };
  list.unshift(row);
  saveJson(file, list.slice(0, 80));
  return row;
}

export function loadOrders(root) {
  return loadJson(paths(root).orders, []);
}
