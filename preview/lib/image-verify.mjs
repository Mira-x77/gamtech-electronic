// Strict candidate verification for product photos.
//
// The store must never show a photo of the wrong item. Search engines happily
// return a whole laptop when asked for "HP 840G5 keyboard", so a candidate is
// only accepted when the result itself proves it matches:
//   - the brand of the product,
//   - every model token of the product name,
//   - the product type (keyboard / battery / charger / ...),
//   - no conflicting model variant (840G3 while looking for 840G5).
//
// Passing this check is still not permission to publish: a human approves the
// result in the stock desk before it becomes the product photo.

export function norm(s) {
  return String(s || '')
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9]+/g, ' ')
    .trim();
}

export function compact(s) {
  return norm(s).replace(/\s+/g, '');
}

function hasPhrase(text, phrase) {
  const p = norm(phrase);
  if (!p) return false;
  if (p.includes(' ')) return (' ' + text + ' ').includes(' ' + p + ' ');
  const words = text.split(' ');
  return words.some((w) => w === p || w === p + 's' || w === p + 'es');
}

// Alias -> canonical brand. Longest alias wins when scanning a name.
const BRANDS = [
  ['hp', ['hp', 'hewlett packard']],
  ['dell', ['dell']],
  ['lenovo', ['lenovo', 'thinkpad', 'ideapad']],
  ['asus', ['asus', 'zenbook', 'vivobook']],
  ['acer', ['acer', 'aspire']],
  ['toshiba', ['toshiba', 'dynabook']],
  // SanDisk is checked before Samsung on purpose: some price-list rows read
  // "SAMSUNG SANDISK ... E31", where the actual product is a SanDisk SSD.
  ['sandisk', ['sandisk', 'scandisk']],
  ['samsung', ['samsung']],
  ['apple', ['apple', 'macbook']],
  ['logitech', ['logitech', 'logi']],
  ['tp-link', ['tp link', 'tplink']],
  ['mikrotik', ['mikrotik', 'mikrotiks', 'mtik', 'mikrotik']],
  ['ubiquiti', ['ubiquiti', 'ubnt', 'unifi']],
  ['tenda', ['tenda']],
  ['d-link', ['d link', 'dlink']],
  ['cudy', ['cudy']],
  ['mercury', ['mercury']],
  ['zonch', ['zonch']],
  ['pixlink', ['pixlink']],
  ['onten', ['onten']],
  ['comfast', ['comfast']],
  ['lb-link', ['lb link', 'lblink']],
  ['alfa', ['alfa']],
  ['kingston', ['kingston']],
  ['seagate', ['seagate']],
  ['western-digital', ['western digital', 'wd']],
  ['crucial', ['crucial']],
  ['lexar', ['lexar']],
  ['transcend', ['transcend']],
  ['baseus', ['baseus']],
  ['ugreen', ['ugreen']],
  ['orico', ['orico']],
  ['anker', ['anker']],
  ['havit', ['havit']],
  ['a4tech', ['a4tech']],
  ['genius', ['genius']],
  ['trust', ['trust']],
  ['jbl', ['jbl']],
  ['sony', ['sony']],
  ['canon', ['canon']],
  ['epson', ['epson']],
  ['brother', ['brother']],
  ['apc', ['apc']],
  ['eaton', ['eaton']],
];

export function brandOf(name) {
  const text = ' ' + norm(name) + ' ';
  let best = null;
  for (const [key, aliases] of BRANDS) {
    for (const alias of aliases) {
      const a = norm(alias);
      if ((' ' + text + ' ').includes(' ' + a + ' ')) {
        if (!best || a.length > best.alias.length) {
          best = { key, alias: a, words: aliases.map(norm) };
        }
      }
    }
  }
  return best;
}

// Units, capacities and marketing numbers that are not model numbers.
const UNIT_RE =
  /^\d+(?:[.,]\d+)?(?:mm|cm|mtr|m|in|ft|w|v|a|ah|mah|wh|tb|gb|mb|kb|g|kg|ml|l|pcs|pieces|pack|ports?|pin|pins|hz|khz|mhz|ghz|fps|mp|nm|nits|ms|k|core|cores|gen|th)$/;
const IGNORE_MODELS = new Set([
  '4k', '8k', '2k', '1080p', '720p', '1440p', '2160p', 'usb2', 'usb3', 'usb4', '3d', '5g', '4g', 'lte',
]);

export function modelTokens(name) {
  const tokens = norm(name)
    .split(' ')
    .filter((t) => {
      if (!t || t.length < 2) return false;
      if (!/\d/.test(t)) return false;
      if (UNIT_RE.test(t)) return false;
      if (IGNORE_MODELS.has(t)) return false;
      if (/^\d+$/.test(t)) return t.length >= 3;
      return true;
    });
  // Marketing suffixes such as "M5" in "MERCURY MWB505G M5 GIGA" are not a
  // separate model when the name already carries a real code (MWB505G). Keep
  // short tokens like the "G5" in "840 G5", because no longer code is present.
  const strong = tokens.some((t) => t.length >= 4 && /[a-z]/.test(t));
  return strong ? tokens.filter((t) => !/^[a-z]\d$/.test(t)) : tokens;
}

const TYPE_GROUPS = [
  { keys: ['keyboard', 'keyboards', 'clavier', 'claviers'], words: ['keyboard', 'clavier', 'keys'] },
  { keys: ['battery', 'batteries', 'batterie', 'batteries'], words: ['battery', 'batterie', 'bateria', 'akku'] },
  {
    keys: ['charger', 'chargers', 'chargeur', 'chargeurs', 'alimentation'],
    words: ['charger', 'chargeur', 'adapter', 'adaptateur', 'power supply', 'alimentation'],
  },
  { keys: ['screen', 'screens', 'ecran', 'ecrans', 'display'], words: ['screen', 'lcd', 'display', 'ecran', 'panel', 'dalle'] },
  { keys: ['mouse', 'souris'], words: ['mouse', 'souris'] },
  {
    keys: ['headset', 'headsets', 'casque', 'casques', 'ecouteur', 'ecouteurs', 'headphone', 'headphones'],
    words: ['headset', 'casque', 'headphone', 'earphone', 'ecouteur', 'audio'],
  },
  { keys: ['webcam', 'webcams', 'camera', 'cameras'], words: ['webcam', 'camera', 'cam'] },
  { keys: ['cable', 'cables', 'cordon'], words: ['cable', 'cordon', 'lead'] },
  { keys: ['hub', 'hubs'], words: ['hub', 'dock', 'splitter'] },
  {
    keys: ['baffle', 'baffles', 'baf', 'speaker', 'speakers', 'enceinte', 'enceintes', 'barre de son'],
    words: ['speaker', 'enceinte', 'soundbar', 'baffle', 'haut parleur'],
  },
  {
    keys: ['boitier', 'boitiers', 'enclosure'],
    words: ['enclosure', 'case', 'caddy', 'boitier', 'adapter'],
  },
  { keys: ['ssd', 'nvme'], words: ['ssd', 'nvme'] },
  { keys: ['cle usb', 'pendrive', 'flash drive'], words: ['flash drive', 'usb drive', 'cle usb', 'pendrive', 'flash'] },
  {
    keys: ['adapter', 'adapters', 'adaptateur', 'adaptateurs', 'convertisseur', 'convertisseurs', 'converter'],
    words: ['adapter', 'adaptateur', 'converter', 'convertisseur'],
  },
  { keys: ['ssd'], words: ['ssd'] },
  { keys: ['hdd', 'disque', 'disques'], words: ['hdd', 'hard drive', 'disque'] },
  { keys: ['ram', 'memoire', 'memoires', 'barette', 'barettes'], words: ['ram', 'memory', 'memoire'] },
  { keys: ['router', 'routers', 'routeur', 'routeurs'], words: ['router', 'routeur'] },
  { keys: ['switch', 'switchs', 'switches'], words: ['switch'] },
  {
    keys: ['pointeur', 'pointeurs', 'pointer', 'presenter', 'presentateur'],
    words: ['pointer', 'presenter', 'laser', 'pointeur', 'telecommande'],
  },
  { keys: ['sticker', 'stickers'], words: ['sticker', 'autocollant'] },
  { keys: ['tapis', 'pad'], words: ['pad', 'mat', 'tapis', 'mousepad'] },
  { keys: ['sac', 'sacs', 'bag'], words: ['bag', 'sac', 'backpack', 'sacoche', 'housse', 'case'] },
  { keys: ['support', 'stand', 'pose', 'bracket'], words: ['stand', 'support', 'holder', 'riser', 'bracket'] },
  { keys: ['toner', 'cartouche'], words: ['toner', 'cartridge', 'cartouche'] },
  { keys: ['imprimante'], words: ['printer', 'imprimante'] },
  { keys: ['fan', 'ventilateur'], words: ['fan', 'ventilateur', 'cooler'] },
  { keys: ['micro', 'microphone'], words: ['microphone', 'micro'] },
  { keys: ['speaker', 'enceinte'], words: ['speaker', 'enceinte'] },
  { keys: ['projecteur', 'projector'], words: ['projector', 'projecteur'] },
  { keys: ['onduleur'], words: ['ups', 'onduleur', 'inverter'] },
  { keys: ['carte', 'card'], words: ['card', 'carte', 'sd', 'microsd'] },
];

// ---------------------------------------------------------------------------
// Identity of a catalogue item, used to collapse the same product entered twice
// (for example the curated "Logitech G502 X" photo entry and the price-list row
// "SOURIS LOGITECH G502 X"). Two names match only when they share the brand, the
// exact same model tokens and the same remaining words. Descriptive words such
// as "wired"/"bluetooth" and the product type are ignored, so "HP M10" matches
// "SOURIS HP M10 A FIL" but "SCANDISK M2 NVME" never matches its 2242/2230
// variants, and "10-100" never matches "10-100-1000".
// ---------------------------------------------------------------------------
const IDENTITY_STOP = new Set([
  'de', 'du', 'des', 'la', 'le', 'les', 'a', 'au', 'aux', 'pour', 'avec', 'et', 'en',
  'the', 'with', 'for', 'and', 'of', 'to', 'vers', 'in', 'on', 'new', 'nouveau',
  'original', 'oem', 'bluetooth', 'bt', 'fil', 'wired', 'wireless', 'sans',
  'rechargeable',
]);
const IDENTITY_LINES = new Set(['signature']);

// Categories that unambiguously describe the product family, so a clean photo
// name such as "HP H231R" still carries a type even without the word "casque".
const IDENTITY_CATEGORY_TYPES = {
  Mouse: 'mouse',
  'Headphones & Audio': 'headset',
  'Replacement Keyboards': 'keyboard',
  Batteries: 'battery',
  Chargers: 'charger',
  Screens: 'screen',
};

export function identitySignature(product) {
  const name = typeof product === 'string' ? product : (product && product.name) || '';
  const cat = typeof product === 'object' && product ? product.cat : '';
  const text = norm(name);
  const brand = brandOf(name);
  const models = [...new Set(modelTokens(name))].sort();
  if (!models.length) return null;

  const matched = TYPE_GROUPS.filter((g) => g.keys.some((k) => hasPhrase(text, k)));
  const typeWords = new Set(matched.flatMap((g) => g.words).flatMap((w) => norm(w).split(' ')));
  const categoryType = IDENTITY_CATEGORY_TYPES[cat];
  if (categoryType) {
    const group = TYPE_GROUPS.find((g) => g.keys.includes(categoryType));
    if (group) for (const w of group.words) for (const t of norm(w).split(' ')) typeWords.add(t);
  }

  const brandWords = new Set(brand ? brand.words.flatMap((w) => norm(w).split(' ')) : []);
  const rest = [
    ...new Set(
      text
        .split(' ')
        .filter(
          (w) =>
            w &&
            !brandWords.has(w) &&
            !models.includes(w) &&
            !IDENTITY_STOP.has(w) &&
            !IDENTITY_LINES.has(w) &&
            !typeWords.has(w)
        )
    ),
  ].sort();

  const types = [
    ...new Set([...matched.map((g) => g.keys[0]), ...(categoryType ? [categoryType] : [])]),
  ].sort();
  return { brand: brand ? brand.key : '', models, rest, types };
}

// Two identity signatures describe the same item only when brand, model tokens
// and remaining words all match, and their product types do not conflict.
export function sameIdentity(a, b) {
  if (!a || !b) return false;
  if (a.brand !== b.brand) return false;
  if (a.models.length !== b.models.length) return false;
  if (!a.models.every((t, i) => t === b.models[i])) return false;
  if (a.rest.length !== b.rest.length) return false;
  if (!a.rest.every((t, i) => t === b.rest[i])) return false;
  if (a.types.length && b.types.length && !a.types.some((t) => b.types.includes(t))) return false;
  return true;
}

const CATEGORY_FALLBACK = {
  'Replacement Keyboards': ['keyboard', 'clavier', 'keys'],
  Batteries: ['battery', 'batterie', 'bateria', 'akku'],
  Chargers: ['charger', 'chargeur', 'adapter', 'adaptateur', 'power supply', 'alimentation'],
  Screens: ['screen', 'lcd', 'display', 'ecran', 'panel', 'dalle'],
};

// Results that are documentation, diagrams or clip art rather than a product.
const BAD_TITLE =
  /\b(guide|manual|diagram|schematic|pinout|spec sheet|specification|how to|repair guide|function guide|layout|clipart|vector|illustration|drawing|icon)\b/;

export function typeWordsFor(product) {
  const text = norm(product && product.name);
  // "Carte WiFi" is a network adapter, not a memory card: the generic "carte"
  // group would otherwise demand "sd"/"card" in an English product title.
  if (/\bcarte (wifi|reseau)\b/.test(text) || /\b(wifi|network) card\b/.test(text)) {
    return ['adapter', 'adaptateur', 'wifi', 'usb', 'card', 'carte'];
  }
  const matched = TYPE_GROUPS.filter((g) => g.keys.some((k) => hasPhrase(text, k)));
  if (matched.length) return [...new Set(matched.flatMap((g) => g.words))];
  return CATEGORY_FALLBACK[product && product.cat] || [];
}

// Find "840g3" style variants of a "840g5" model in a candidate title.
function conflictingVariants(token, haystack) {
  const m = String(token).match(/^(\d{2,5})([a-z]{1,3})(\d{0,3})$/);
  if (!m) return [];
  const [, core, letters, digits] = m;
  const re = new RegExp('\\b' + core + '\\s*([a-z]{1,3})?\\s*(\\d{1,4})?\\b', 'g');
  const found = [];
  let hit;
  while ((hit = re.exec(haystack))) {
    const candidate = core + (hit[1] || '') + (hit[2] || '');
    if (candidate !== token && !candidate.startsWith(token)) found.push(candidate);
  }
  return [...new Set(found)].filter((v) => /\d$/.test(v) && v !== core);
}

// The page a photo came from can prove the brand when the title only carries
// the model (for example lb-link.com pages titled "BR500 5.8G ..."). Only the
// host is used, so a reseller page cannot fake it through its path.
function brandInHost(brand, value) {
  if (!brand) return false;
  let host = '';
  try {
    host = compact(new URL(String(value || '')).hostname);
  } catch {
    host = compact(value);
  }
  if (!host) return false;
  return brand.words.map(compact).some((w) => w.length >= 3 && host.includes(w));
}

export function verifyCandidate(product, candidate) {
  const reasons = [];
  const title = norm(candidate && candidate.title);
  const titleCompact = compact(candidate && candidate.title);

  if (!title) reasons.push('no-title');
  if (BAD_TITLE.test(title)) reasons.push('bad-title');

  const brand = brandOf(product && product.name);
  if (brand && !brand.words.some((w) => hasPhrase(title, w))) {
    if (!brandInHost(brand, candidate && (candidate.pageUrl || candidate.url))) reasons.push('brand');
  }

  const models = modelTokens(product && product.name);
  if (!models.length) reasons.push('no-model');
  for (const token of models) {
    const code = compact(token);
    // OCR/typo tolerance: the price list writes "LO09UiGS" for "L009UiGS".
    const codeNoLetterO = code.replace(/o/g, '0');
    if (!titleCompact.includes(code) && !titleCompact.includes(codeNoLetterO)) {
      reasons.push('model:' + token);
    }
  }

  const types = typeWordsFor(product);
  if (types.length && !types.some((w) => hasPhrase(title, w))) reasons.push('type');

  if (candidate && candidate.width && candidate.height && Math.min(candidate.width, candidate.height) < 250) {
    reasons.push('small');
  }

  for (const token of models) {
    const conflict = conflictingVariants(token, title);
    if (conflict.length) {
      reasons.push('conflict:' + conflict[0]);
      break;
    }
  }

  let score = 100 - reasons.length * 20;
  if (candidate && candidate.width >= 600 && candidate.height >= 600) score += 3;
  if (/aliexpress|pinterest|facebook|tiktok/i.test((candidate && candidate.url) || '')) score -= 15;

  return { ok: reasons.length === 0, score: Math.max(0, Math.min(100, score)), reasons };
}

// A product is worth automatic candidate search only when it names both a known
// brand and a model number. Generic names stay name-only.
export function isExactModelItem(product) {
  if (!product || !product.name) return false;
  return !!(brandOf(product.name) && modelTokens(product.name).length);
}
