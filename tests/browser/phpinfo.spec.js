const { test, expect } = require('@playwright/test');
const AxeBuilder = require('@axe-core/playwright').default;

for (const css of ['bootstrap3', 'bootstrap4', 'bootstrap5', 'tailwind']) {
    test(`${css} renders and filters settings`, async ({ page }) => {
        const errors = [];
        page.on('pageerror', error => errors.push(error.message));
        await page.goto(`/phpinfo?css=${css}`);
        await expect(page.getByRole('heading', { name: 'PHP Information', exact: true })).toBeVisible();
        await expect(page.getByLabel('Filter settings')).toBeVisible();
        await page.getByLabel('Filter settings').fill('memory_limit');
        await expect(page.getByRole('cell', { name: 'memory_limit', exact: true })).toBeVisible();
        await expect(page.getByRole('cell', { name: 'display_errors', exact: true })).toBeHidden();
        await page.getByLabel('Filter settings').fill('curl');
        await expect(page.getByRole('cell', { name: 'cURL support', exact: true })).toBeVisible();
        await page.getByLabel('Filter settings').fill('no-setting-matches-this');
        await expect(page.getByRole('status')).toHaveText('No settings match your search.');
        await page.getByLabel('Filter settings').fill('');
        await expect(page.getByRole('cell', { name: 'display_errors', exact: true })).toBeVisible();
        expect(errors).toEqual([]);
    });
}

test('dark mode follows the system and remembers explicit choices', async ({ page }) => {
    await page.emulateMedia({ colorScheme: 'dark' });
    await page.goto('/phpinfo');
    await expect(page.locator('.phpinfo-card')).toHaveCSS('background-color', 'rgb(21, 27, 39)');
    await page.getByLabel('Appearance').selectOption('light');
    await expect(page.locator('.phpinfo-card')).toHaveCSS('background-color', 'rgb(255, 255, 255)');
    await page.reload();
    await expect(page.getByLabel('Appearance')).toHaveValue('light');
    await page.getByLabel('Appearance').selectOption('dark');
    await expect(page.locator('.phpinfo-card')).toHaveCSS('background-color', 'rgb(21, 27, 39)');
    await expect(page.locator('html')).not.toHaveAttribute('data-theme');
});

test('the host Bootstrap theme takes precedence over the system', async ({ page }) => {
    await page.emulateMedia({ colorScheme: 'dark' });
    await page.goto('/phpinfo');
    await page.locator('html').evaluate(element => element.setAttribute('data-bs-theme', 'light'));
    await expect(page.locator('.phpinfo-card')).toHaveCSS('background-color', 'rgb(255, 255, 255)');
    await page.locator('html').evaluate(element => element.setAttribute('data-bs-theme', 'dark'));
    await expect(page.locator('.phpinfo-card')).toHaveCSS('background-color', 'rgb(21, 27, 39)');
});

test('blocked storage does not break search or theme controls', async ({ page }) => {
    await page.addInitScript(() => {
        Object.defineProperty(window, 'localStorage', { get() { throw new Error('Storage is blocked'); } });
    });
    await page.goto('/phpinfo');
    await page.getByLabel('Appearance').selectOption('dark');
    await page.getByLabel('Filter settings').fill('memory_limit');
    await expect(page.getByRole('cell', { name: 'memory_limit', exact: true })).toBeVisible();
    await expect(page.locator('.phpinfo-card')).toHaveCSS('background-color', 'rgb(21, 27, 39)');
});

for (const theme of ['light', 'dark']) {
    test(`${theme} mode is usable on mobile and passes accessibility checks`, async ({ page }) => {
        await page.setViewportSize({ width: 375, height: 812 });
        await page.goto('/phpinfo?css=tailwind');
        await page.getByLabel('Appearance').selectOption(theme);
        expect(await page.evaluate(() => document.documentElement.scrollWidth <= window.innerWidth)).toBe(true);
        const results = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21aa']).analyze();
        expect(results.violations).toEqual([]);
    });
}

test('information remains readable without JavaScript', async ({ browser }) => {
    const context = await browser.newContext({ javaScriptEnabled: false });
    const page = await context.newPage();
    await page.goto('http://127.0.0.1:18763/phpinfo');
    await expect(page.getByRole('cell', { name: 'memory_limit', exact: true })).toBeVisible();
    await expect(page.getByLabel('Filter settings')).toBeHidden();
    await context.close();
});

test('real web-server phpinfo output is rendered inside the page', async ({ page }) => {
    await page.goto('/phpinfo?real=1');
    await expect(page.getByRole('heading', { name: 'PHP Information', exact: true })).toBeVisible();
    await expect(page.locator('.php-info')).toContainText('PHP Version');
    await expect(page.locator('.php-info table').first()).toBeVisible();
    await expect(page.locator('body')).toHaveCount(1);
});
