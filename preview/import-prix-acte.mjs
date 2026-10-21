import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const here = path.dirname(fileURLToPath(import.meta.url));
const xl = process.argv[2] || path.join(here, 'tmp-xlsx/xl');
const outFile = path.join(here, 'prix-acte.json');

const SKIP_SHEETS = /commande potentiel/i;
const HEADER_RE =
  /^(models?|modeles?|quantites?|quantity|prix|prix tech|pice|amount|gam tech|\*\*|original batteries|batteries remplacementes|batteries a commander|new en cours)$/i;
const SECTION_BRANDS = /^(hp|dell|lenovo|asus|acer|toshiba|tosh|samsung|sandisk|scandisk|kingston|lexar|transcend|seagate|western digital|apple|macbooks|sony|fujitsu|medion|comfast|lb link|tp.?link|ubiquiti|ubikiti|mikrotiks?|tenda|d-link|cudy|mercury|zonch|alfa|onten|logitech|logi)$/i;

const SHEET_CAT = [
  [/claviers/i, 'Replacement Keyboards', true],
  [/bat replacement/i, 'Batteries', true],
  [/bat original/i, 'Batteries', true],
  [/chargeur/i, 'Chargers', true],
  [/gadgets|tapis|souris/i, 'Computer Accessories', false],
  [/hdmi|hub|convert/i, 'Adapters & Hubs', false],
  [/^sacs/i, 'Laptop Accessories', false],
  [/cle usb|hdd|ssd|ram/i, 'Storage', false],
  [/mikrotik|oscar/i, 'Networking', false],
  [/ecrans|moniteur/i, 'Screens', true],
  [/tplink|ubikiti|comfast|lb link/i, 'Networking', false],
  [/coffet|d.?link|cudy|mercury/i, 'Networking', false],
  [/cable|pince|testa|alfa/i, 'Cables & Converters', false],
  [/produits finis|mapasen|camera/i, 'Computer Accessories', false],
  [/switch|zonch|pixlink|tenda/i, 'Networking', false],
];

function catForSheet(name) {
  for (const [re, cat, listOnly] of SHEET_CAT) {
    if (re.test(name)) return { cat, listOnly };
  }
  return { cat: 'Computer Accessories', listOnly: false };
}

function parseSharedStrings(xml) {
  const strings = [];
  const re = /<si>([\s\S]*?)<\/si>/g;
  let m;
  while ((m = re.exec(xml))) {
    const texts = [];
    const tRe = /<t[^>]*>([\s\S]*?)<\/t>/g;
    let tm;
    while ((tm = tRe.exec(m[1]))) texts.push(decodeXml(tm[1]));
    strings.push(texts.join('').replace(/\s+/g, ' ').trim());
  }
  return strings;
}

function decodeXml(s) {
  return String(s || '')
    .replace(/&amp;/g, '&')
    .replace(/&lt;/g, '<')
    .replace(/&gt;/g, '>')
    .replace(/&quot;/g, '"')
    .replace(/&#39;/g, "'");
}

function colIndex(ref) {
  const letters = ref.replace(/[0-9]/g, '');
  let n = 0;
  for (const ch of letters) n = n * 26 + (ch.charCodeAt(0) - 64);
  return n;
}

function parseSheet(xml, strings) {
  const rows = new Map();
  const cellRe = /<c r="([A-Z]+)(\d+)"([^>]*)>(?:[\s\S]*?<v>([^<]*)<\/v>)?/g;
  let m;
  while ((m = cellRe.exec(xml))) {
    const col = colIndex(m[1]);
    const row = Number(m[2]);
    const attrs = m[3] || '';
    const raw = m[4];
    if (raw == null || raw === '') continue;
    let val;
    if (/\bt="s"/.test(attrs)) val = strings[Number(raw)] || '';
    else if (/\bt="inlineStr"/.test(attrs)) val = raw;
    else {
      const n = Number(raw);
      val = Number.isFinite(n) ? n : raw;
    }
    if (!rows.has(row)) rows.set(row, []);
    rows.get(row).push({ col, val });
  }
  for (const cells of rows.values()) cells.sort((a, b) => a.col - b.col);
  return rows;
}

function isHeader(s) {
  const t = String(s || '').trim();
  if (!t || t.length < 2) return true;
  if (HEADER_RE.test(t)) return true;
  if (/^-+$/.test(t)) return true;
  return false;
}

function isProductName(s) {
  const t = String(s || '').trim();
  if (isHeader(t)) return false;
  if (/^\d+(\.\d+)?$/.test(t)) return false;
  if (t.length < 2) return false;
  return true;
}

function pickPrice(nums) {
  const money = nums.filter((n) => n >= 400 && n <= 400000);
  if (!money.length) return null;
  return Math.max(...money);
}

const BRAND_COL = /^(hp|dell|lenovo|asus|acer|toshiba|tosh|samsung|sandisk|scandisk|kingston|lexar|transcend|seagate|western digital|apple|macbooks?|sony|fujitsu|fujitshu|medion|comfast|lb link|tp.?link|ubiquiti|ubikiti|mikrotiks?|tenda|d-link|cudy|mercury|zonch|alfa|onten|logitech|logi|surface)$/i;
const SKIP_NOTE = /^(lum|ori|fr|us|mj|with |uk lum)/i;

function nearestBrand(brands, col) {
  let best = '';
  let bestCol = -1;
  for (const [c, b] of brands) {
    if (c <= col && c >= bestCol) {
      bestCol = c;
      best = b;
    }
  }
  return best;
}

function chargerHint(spec) {
  if (/type\s*c|usb[\s-]*c/i.test(spec)) return 'USB-C';
  if (/\busb\b/i.test(spec)) return 'USB tip';
  if (/\b(bb|bp|bj|pb|bn|br)\b/i.test(spec)) return 'DC barrel';
  if (/lame|pointier|rond|pin/i.test(spec)) return '';
  return '';
}

function expandAlso(name, cat) {
  const bits = new Set();
  const raw = String(name || '');
  bits.add(raw);
  bits.add(raw.replace(/\s+/g, ''));
  const g = raw.match(/(\d{3,4})\s*G\s*(\d{1,2})/i);
  if (g) {
    bits.add(g[1] + 'g' + g[2]);
    bits.add(g[1] + ' g' + g[2]);
    bits.add(g[1]);
    if (/^84[05]$/.test(g[1]) || g[1] === '830') bits.add('elitebook ' + g[1]);
    if (/^64[05]$/.test(g[1]) || g[1] === '645') bits.add('elitebook ' + g[1]);
    if (/^4[345]0$/.test(g[1])) bits.add('probook ' + g[1]);
  }
  if (/zbook/i.test(raw)) bits.add('hp zbook workstation');
  if (/latitude/i.test(raw)) bits.add('dell latitude');
  if (/\b(t16|t460|t470|t480|yoga|x1)\b/i.test(raw)) bits.add('lenovo thinkpad');
  const w = raw.match(/(\d{2,3})\s*W/i);
  if (w) bits.add(w[1] + ' watt ' + w[1] + 'w');
  const v = raw.match(/(\d+(?:\.\d+)?)\s*V/i);
  if (v) bits.add(v[1] + 'v ' + v[1] + ' volt');
  if (/type\s*c|usb-c|usb c/i.test(raw)) bits.add('usb-c usbc type-c type c');
  if (/dc barrel|barrel/i.test(raw)) bits.add('barrel tip jack chargeur');
  if (cat === 'Batteries') bits.add('batterie battery bat');
  if (cat === 'Chargers') bits.add('chargeur charger adaptateur');
  if (cat === 'Replacement Keyboards') bits.add('clavier keyboard clavier pc');
  return [...bits].join(' ');
}

function extractBrandColumns(rows, kind) {
  const items = [];
  const brands = new Map();
  const sorted = [...rows.entries()].sort((a, b) => a[0] - b[0]);
  for (const [, cells] of sorted) {
    cells.sort((a, b) => a.col - b.col);
    for (let i = 0; i < cells.length; i += 1) {
      const cell = cells[i];
      if (typeof cell.val !== 'string') continue;
      const text = cell.val.replace(/\s+/g, ' ').trim();
      if (!isProductName(text)) continue;
      if (BRAND_COL.test(text) && !/\d/.test(text)) {
        brands.set(cell.col, text.replace(/macbooks/i, 'MacBook').replace(/fujitshu/i, 'Fujitsu'));
        continue;
      }
      if (SKIP_NOTE.test(text) && kind === 'keyboards') continue;
      const brand = nearestBrand(brands, cell.col);
      const nums = [];
      for (const nxt of cells) {
        if (nxt.col <= cell.col || nxt.col > cell.col + 3) continue;
        if (typeof nxt.val === 'number') nums.push(nxt.val);
      }
      const price = pickPrice(nums);
      if (price == null && nums.length && Math.max(...nums) < 400 && kind !== 'chargers') continue;
      let name = brand && !text.toLowerCase().includes(brand.toLowerCase()) ? `${brand} ${text}` : text;
      if (kind === 'chargers') {
        const hint = chargerHint(text);
        if (hint && !name.toLowerCase().includes(hint.toLowerCase())) name += ' ' + hint;
      }
      name = name.replace(/\s+/g, ' ').trim();
      items.push({
        name,
        price: price == null ? null : price,
        also: expandAlso(name, kind === 'chargers' ? 'Chargers' : 'Replacement Keyboards'),
      });
    }
  }
  return items;
}

function extractBatteryItems(rows) {
  const items = [];
  const brandAt = new Map();
  const sorted = [...rows.entries()].sort((a, b) => a[0] - b[0]);
  for (const [, cells] of sorted) {
    cells.sort((a, b) => a.col - b.col);
    for (let i = 0; i < cells.length; i += 1) {
      const cell = cells[i];
      if (typeof cell.val !== 'string') continue;
      const text = cell.val.replace(/\s+/g, ' ').trim();
      if (!isProductName(text)) continue;
      if (BRAND_COL.test(text) && !/\d/.test(text)) {
        brandAt.set(cell.col, text.replace(/macbooks/i, 'MacBook').replace(/fujitshu/i, 'Fujitsu'));
        continue;
      }
      const brand = nearestBrand(brandAt, cell.col);
      let spec = '';
      const nums = [];
      for (const nxt of cells) {
        if (nxt.col <= cell.col || nxt.col > cell.col + 4) continue;
        if (typeof nxt.val === 'string' && /\d/.test(nxt.val) && /\b(v|wh|mah|w)\b/i.test(nxt.val)) {
          spec = nxt.val.trim();
        } else if (typeof nxt.val === 'number') nums.push(nxt.val);
      }
      const price = pickPrice(nums);
      if (price == null && !spec && nums.length && Math.max(...nums) < 400) continue;
      let name = brand && !text.toLowerCase().includes(brand.toLowerCase()) ? `${brand} ${text}` : text;
      if (spec && !name.toLowerCase().includes(spec.toLowerCase())) name += ' ' + spec;
      name = name.replace(/\s+/g, ' ').trim();
      items.push({
        name,
        price: price == null ? null : price,
        also: expandAlso(name, 'Batteries'),
      });
    }
  }
  return items;
}

function extractItems(rows, electrical) {
  const items = [];
  const prefixByCol = new Map();
  for (const [, cells] of [...rows.entries()].sort((a, b) => a[0] - b[0])) {
    let i = 0;
    while (i < cells.length) {
      const cell = cells[i];
      if (typeof cell.val !== 'string' || !isProductName(cell.val)) {
        i += 1;
        continue;
      }
      let name = cell.val.trim();
      const nums = [];
      let j = i + 1;
      if (electrical) {
        if (
          j < cells.length &&
          cells[j].col <= cell.col + 2 &&
          typeof cells[j].val === 'string' &&
          /\d/.test(cells[j].val) &&
          /\b(v|wh|mah|w)\b/i.test(cells[j].val)
        ) {
          name += ' ' + cells[j].val.trim();
          j += 1;
        } else if (
          j < cells.length &&
          cells[j].col <= cell.col + 2 &&
          typeof cells[j].val === 'number' &&
          cells[j].val >= 5 &&
          cells[j].val <= 24
        ) {
          name += ' ' + cells[j].val + 'V';
          j += 1;
          if (
            j < cells.length &&
            typeof cells[j].val === 'number' &&
            cells[j].val > 0 &&
            cells[j].val <= 15
          ) {
            name += ' ' + cells[j].val + 'A';
            j += 1;
          }
        }
      }
      while (j < cells.length && cells[j].col <= cell.col + 5 && typeof cells[j].val === 'number') {
        nums.push(cells[j].val);
        j += 1;
      }
      const price = pickPrice(nums);
      if (SECTION_BRANDS.test(name) && price == null) {
        prefixByCol.set(cell.col, name.replace(/\s+/g, ' ').trim());
        i = j;
        continue;
      }
      if (price == null && nums.length && Math.max(...nums) < 400) {
        i = j;
        continue;
      }
      const prefix = prefixByCol.get(cell.col) || '';
      const full = prefix && !name.toLowerCase().includes(prefix.toLowerCase())
        ? `${prefix} ${name}`
        : name;
      items.push({ name: full.replace(/\s+/g, ' ').trim(), price });
      i = j;
    }
  }
  return items;
}

function parseWorkbook(wbXml) {
  const sheets = [];
  const re = /<sheet name="([^"]+)"[^>]*r:id="rId(\d+)"/g;
  let m;
  while ((m = re.exec(wbXml))) {
    sheets.push({ name: decodeXml(m[1]).trim(), rId: Number(m[2]), file: `sheet${m[2]}.xml` });
  }
  return sheets;
}

const strings = parseSharedStrings(fs.readFileSync(path.join(xl, 'sharedStrings.xml'), 'utf8'));
const sheets = parseWorkbook(fs.readFileSync(path.join(xl, 'workbook.xml'), 'utf8'));
const catalog = [];
const seen = new Set();

function norm(s) {
  return String(s || '')
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, ' ')
    .trim();
}

for (const sheet of sheets) {
  if (SKIP_SHEETS.test(sheet.name)) continue;
  const file = path.join(xl, 'worksheets', sheet.file);
  if (!fs.existsSync(file)) {
    console.warn('Missing sheet file', sheet.file, sheet.name);
    continue;
  }
  const { cat, listOnly } = catForSheet(sheet.name);
  const parsed = parseSheet(fs.readFileSync(file, 'utf8'), strings);
  let items;
  if (/claviers/i.test(sheet.name)) items = extractBrandColumns(parsed, 'keyboards');
  else if (/chargeur/i.test(sheet.name)) items = extractBrandColumns(parsed, 'chargers');
  else if (/bat /i.test(sheet.name)) items = extractBatteryItems(parsed);
  else items = extractItems(parsed, /bat|chargeur/i.test(sheet.name));
  let added = 0;
  for (const it of items) {
    const key = norm(it.name);
    if (!key || key.length < 2) continue;
    if (seen.has(key)) continue;
    seen.add(key);
    catalog.push({
      name: it.name,
      price: it.price,
      cat,
      sub: sheet.name.replace(/\s+/g, ' ').trim(),
      listOnly,
      also: it.also || '',
    });
    added += 1;
  }
  console.log(sheet.name, '->', added, 'items,', cat, listOnly ? 'list' : 'cards');
}

fs.writeFileSync(outFile, JSON.stringify(catalog, null, 2));
console.log('Wrote', catalog.length, 'items to', outFile);
