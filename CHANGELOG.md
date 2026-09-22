# Changelog

## Unreleased

- Add Bootstrap 5 and Tailwind display options while retaining Bootstrap 4 as the default and support for existing Bootstrap 3 configuration.
- Add responsive styling, local search, light/dark/system appearance, and a standalone page layout.
- Add install, update, and switch commands that preserve application configuration and published views. Explicit view refreshes create a backup first.
- Add optional setup integration with Laravel UI Kit without a runtime dependency.
- Correct the controller namespace and support host applications whose base controller has no middleware method.
- Publish translations to the application's language path, fixing issue #8 on Laravel 9 and newer.
- Add PHP regression tests, a Laravel 5.3 through 13 CI matrix, code style checks, and browser/accessibility tests.
- Update documentation, light/dark README banners, badges, and the license year.
