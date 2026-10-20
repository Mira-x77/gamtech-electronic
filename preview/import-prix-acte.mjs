import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const xl = process.argv[2] || path.join(process.env.TEMP || '/tmp', 'prix-acte-xlsx/unpacked/xl');
const outFile = path.join(path.dirname(fileURLToPath(import.meta.url)), 'prix-acte.json');

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
  const items = extractItems(
    parseSheet(fs.readFileSync(file, 'utf8'), strings),
    /bat|chargeur/i.test(sheet.name)
  );
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
    });
    added += 1;
  }
  console.log(sheet.name, '->', added, 'items,', cat, listOnly ? 'list' : 'cards');
}

fs.writeFileSync(outFile, JSON.stringify(catalog, null, 2));
console.log('Wrote', catalog.length, 'items to', outFile);
