const { defineConfig } = require('@playwright/test');

module.exports = defineConfig({
    testDir: './tests/browser',
    fullyParallel: false,
    workers: 1,
    use: {
        baseURL: 'http://127.0.0.1:18763',
        trace: 'retain-on-failure',
    },
    webServer: {
        command: 'php -S 127.0.0.1:18763 tests/browser/server.php',
        url: 'http://127.0.0.1:18763/phpinfo',
        reuseExistingServer: !process.env.CI,
    },
});
