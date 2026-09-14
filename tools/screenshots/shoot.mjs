// Authenticated Filament screenshots for package covers and visual checks.
//
// Usage: SHOT_EMAIL=... SHOT_PASSWORD=... node shoot.mjs <config.json>
//
// config.json:
// {
//   "baseUrl": "https://tomato.fadymondy.com",
//   "outDir": "E:/Sites/tomatophp/tools/screenshots/out",
//   "width": 1440, "height": 900, "scale": 2,
//   "shots": [
//     { "name": "users-list", "path": "/admin/users", "modes": ["light", "dark"],
//       "click": "optional selector to click first", "waitFor": "optional selector",
//       "clip": "optional selector to capture only that element", "delay": 600 }
//   ]
// }
import fs from 'node:fs';
import puppeteer from 'puppeteer-core';

const config = JSON.parse(fs.readFileSync(process.argv[2], 'utf8'));
const { SHOT_EMAIL: email, SHOT_PASSWORD: password } = process.env;

if (!email || !password) {
    console.error('Set SHOT_EMAIL and SHOT_PASSWORD.');
    process.exit(1);
}

fs.mkdirSync(config.outDir, { recursive: true });

const browser = await puppeteer.launch({
    executablePath: 'C:/Program Files/Google/Chrome/Application/chrome.exe',
    headless: true,
    args: ['--hide-scrollbars', '--no-first-run'],
});

try {
    const page = await browser.newPage();
    await page.setViewport({ width: config.width ?? 1440, height: config.height ?? 900, deviceScaleFactor: config.scale ?? 2 });

    await page.goto(`${config.baseUrl}/admin/login`, { waitUntil: 'networkidle2' });
    await page.type('input[type=email]', email);
    await page.type('input[type=password]', password);
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle2' }),
        page.click('button[type=submit]'),
    ]);

    if (page.url().includes('/login')) {
        throw new Error('Login failed.');
    }

    for (const shot of config.shots) {
        for (const mode of shot.modes ?? ['light', 'dark']) {
            // Filament reads the theme from localStorage before painting.
            await page.evaluate((theme) => localStorage.setItem('theme', theme), mode);
            await page.emulateMediaFeatures([{ name: 'prefers-color-scheme', value: mode }]);
            await page.goto(`${config.baseUrl}${shot.path}`, { waitUntil: 'networkidle2' });

            if (shot.click) {
                await page.waitForSelector(shot.click, { visible: true });
                await page.click(shot.click);
            }

            if (shot.waitFor) {
                await page.waitForSelector(shot.waitFor, { visible: true });
            }

            await new Promise((resolve) => setTimeout(resolve, shot.delay ?? 600));

            const file = `${config.outDir}/${shot.name}-${mode}.png`;

            if (shot.clip) {
                const element = await page.$(shot.clip);
                await element.screenshot({ path: file });
            } else {
                await page.screenshot({ path: file, fullPage: Boolean(shot.fullPage) });
            }

            console.log(`saved ${file}`);
        }
    }
} finally {
    await browser.close();
}
