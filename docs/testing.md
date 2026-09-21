# Testing and compatibility

The runtime dependency constraint stays unchanged. Development dependencies are separate and do not raise the PHP requirement for consuming applications.

## PHP tests

```bash
composer install
composer test
composer lint:test
composer validate --strict
```

The same test suite runs in the following GitHub Actions matrix:

| Laravel | PHP | Testbench |
| --- | --- | --- |
| 5.3 | 5.6 | 3.3 |
| 5.4 | 5.6 | 3.4 |
| 5.5 | 7.0 | 3.5 |
| 5.6 | 7.1 | 3.6 |
| 5.7 | 7.1 | 3.7 |
| 5.8 | 7.2 | 3.8 |
| 6 | 7.2 | 4 |
| 7 | 7.3 | 5 |
| 8 | 7.4 | 6 |
| 9 | 8.0 | 7 |
| 10 | 8.1 | 8 |
| 11 | 8.2 | 9 |
| 12 | 8.2, 8.5 | 10 |
| 13 | 8.3, 8.4, 8.5 | 11 |

A separate job checks runtime source syntax with PHP 5.4 to preserve the existing Composer constraint. This is a syntax check, not a claim that modern Laravel runs on PHP 5.4.

Legacy jobs select matching test dependencies and allow historical dependency versions in the isolated CI job. This lets the suite exercise applications running unsupported Laravel versions despite known advisories in those old frameworks. That exception is not saved in the package's Composer configuration. Current framework jobs retain Composer's security checks and run `composer audit`.

## Browser tests

```bash
npm ci
npx playwright install chromium
npm test
```

Playwright starts a local PHP server on `127.0.0.1:18763`. Most tests use a fixed PHP Info fixture so screenshots and failures do not contain local environment data. A separate smoke test checks real web-server `phpinfo()` output.

Browser coverage includes Bootstrap 3, 4, and 5, Tailwind, extension and directive search, theme persistence, host theme integration, blocked browser storage, JavaScript disabled, mobile overflow, and WCAG accessibility checks in light and dark mode. Each framework is tested with its real stylesheet loaded. The test command compiles Tailwind's utilities from the package source before starting the browser.

Node 20 or newer is needed for browser-test tooling. Bootstrap 3.4.1 is a development-only dependency used to test its CSS. npm reports historical advisories for that version's JavaScript plugins; those plugins are never loaded by the tests or the package. Applications installing this package through Composer do not acquire these npm test dependencies.

The browser fixture disables authentication only inside the local test application. It is not an application entry point and must not be deployed as one.
