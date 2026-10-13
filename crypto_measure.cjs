const puppeteer = require('puppeteer');

(async () => {
  const browser = await puppeteer.launch({
    headless: 'new',
    args: ['--no-sandbox', '--disable-gpu', '--disable-dev-shm-usage'],
  });
  const page = await browser.newPage();
  await page.setViewport({ width: 1440, height: 900 });
  await page.goto('http://localhost:8080/', { waitUntil: 'networkidle2', timeout: 60000 });

  // swiper lazy init bekle
  await page.evaluate(() => new Promise((r) => setTimeout(r, 2500)));

  const data = await page.evaluate(() => {
    const swiper = document.querySelector('[data-widget-type="crypto"] .swiper');
    if (!swiper) return { error: 'crypto swiper yok' };
    const slides = Array.from(swiper.querySelectorAll('.swiper-slide'));
    const swiperWidth = swiper.getBoundingClientRect().width;
    return {
      swiperWidth,
      swiperClass: swiper.className,
      slideCount: slides.length,
      slides: slides.map((s, i) => {
        const r = s.getBoundingClientRect();
        const card = s.querySelector('[data-type="stats-bar-type1"]');
        const anchor = s.querySelector('a');
        const inner = s.querySelector('.flex.flex-col');
        const ir = inner ? inner.getBoundingClientRect() : null;
        return {
          i,
          left: Math.round(r.left),
          width: Math.round(r.width),
          cardWidth: card ? Math.round(card.getBoundingClientRect().width) : null,
          cardLeft: card ? Math.round(card.getBoundingClientRect().left) : null,
          innerWidth: ir ? Math.round(ir.width) : null,
          linked: !!anchor,
          href: anchor ? anchor.getAttribute('href') : null,
          name: s.textContent.trim().slice(0, 30),
        };
      }),
    };
  });

  if (data.error) { console.log(JSON.stringify(data, null, 2)); await browser.close(); return; }

  console.log('swiperWidth=' + data.swiperWidth + ' slideCount=' + data.slideCount);
  console.log('swiperClass=[' + data.swiperClass + ']');
  data.slides.forEach((s) => {
    const prev = data.slides[s.i - 1];
    const gap = prev ? s.left - (prev.left + prev.width) : 0;
    console.log(
      `slide#${s.i} ${String(s.name).padEnd(14)} left=${String(s.left).padStart(5)} w=${String(s.width).padStart(5)} ` +
      `cardW=${String(s.cardWidth).padStart(4)} innerW=${String(s.innerWidth).padStart(4)} gapFromPrev=${gap} ${s.linked ? 'LINK' : ''}`
    );
  });

  // ekran görüntüsü: crypto bölümü
  const cryptoSection = await page.$('[data-section="crypto"]');
  if (cryptoSection) {
    await cryptoSection.screenshot({ path: '/tmp/crypto_onpage.png' });
    console.log('screenshot: /tmp/crypto_onpage.png');
  }
  await browser.close();
})().catch((e) => { console.error('ERR', e); process.exit(1); });
