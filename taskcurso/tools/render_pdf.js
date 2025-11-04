const fs = require('fs');
const path = require('path');
const puppeteer = require('puppeteer');

// Usage: node render_pdf.js <url> <outputPath>
(async () => {
  try {
    const args = process.argv.slice(2);
    if (args.length < 2) {
      console.error('Usage: node render_pdf.js <url> <outputPath>');
      process.exit(2);
    }
    const url = args[0];
    const out = args[1];

    const browser = await puppeteer.launch({ args: ['--no-sandbox', '--disable-setuid-sandbox'] });
    const page = await browser.newPage();
    await page.setViewport({ width: 1200, height: 900 });

    // Wait until network is idle so charts/scripts have time to render
    await page.goto(url, { waitUntil: 'networkidle2', timeout: 30000 });

    // Give a short extra delay for client-side charts to paint if needed
    await page.waitForTimeout(500);

    await page.pdf({ path: out, format: 'A4', printBackground: true });
    await browser.close();
    console.log('OK');
  } catch (err) {
    console.error('ERR', err && err.message ? err.message : err);
    process.exit(1);
  }
})();
