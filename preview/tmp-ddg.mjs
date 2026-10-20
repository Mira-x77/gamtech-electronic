const q = 'Mikrotik RB941';
const html = await (
  await fetch('https://duckduckgo.com/?q=' + encodeURIComponent(q) + '&iax=images&ia=images', {
    headers: {
      'User-Agent':
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    },
  })
).text();
const m = html.match(/vqd="([^"]+)"/) || html.match(/vqd='([^']+)'/);
const u =
  'https://duckduckgo.com/i.js?l=us-en&o=json&q=' +
  encodeURIComponent(q) +
  '&vqd=' +
  encodeURIComponent(m[1]) +
  '&f=,,,';
const data = await (
  await fetch(u, {
    headers: {
      'User-Agent':
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
      Referer: 'https://duckduckgo.com/',
    },
  })
).json();
const first = (data.results || [])[0];
console.log(Object.keys(data));
console.log(first);
