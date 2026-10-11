// CSS/JS surumleme: public/css/app.css -> public/css/app.v<hash>.css
// Cloudflare 4 saat cache tutuyor (cf-cache-status: HIT); ?v=filemtime
// query'si cache'i delmiyor. Dosya adini surumleerek cache bust yapiyoruz.
import fs from 'node:fs';
import path from 'node:path';
import crypto from 'node:crypto';
import { fileURLToPath } from 'node:url';

const here = path.dirname(fileURLToPath(import.meta.url));
const base = path.join(here, '..', 'public', 'css');
const src = path.join(base, 'app.css');

if (!fs.existsSync(src)) {
    console.error('app.css bulunamadi:', src);
    process.exit(1);
}

const buf = fs.readFileSync(src);
const hash = crypto.createHash('sha1').update(buf).digest('hex').slice(0, 8);
const outName = `app.v${hash}.css`;

fs.writeFileSync(path.join(base, outName), buf);

for (const f of fs.readdirSync(base)) {
    if (/^app\.v[0-9a-f]{8}\.css$/.test(f) && f !== outName) {
        try { fs.unlinkSync(path.join(base, f)); } catch { /* yoksay */ }
    }
}

fs.writeFileSync(
    path.join(base, '.version'),
    JSON.stringify({ css: outName, hash, at: new Date().toISOString() })
);

console.log('CSS surumlendi ->', outName);