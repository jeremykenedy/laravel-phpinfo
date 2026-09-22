# Upgrading Laravel PHP Info

`composer update jeremykenedy/laravel-phpinfo` keeps the `/phpinfo` route, its `laravelPhpInfo::phpinfo` name, the `laravelPhpInfo` view/translation namespace, and the original publish tag. There are no Composer scripts that run installation or change your framework.

Bootstrap 4 and the application's `layouts.app` view remain the defaults. A published config with `bootstapVersion` set to `3` still selects Bootstrap 3. Existing configuration keys retain their original spelling. Published views continue to override the bundled templates.

## Adopt the new interface

If you have not published the views, the bundled page receives the new styling, search, and appearance controls automatically. If you have published them, keep your current view or explicitly refresh it:

```bash
php artisan phpinfo:update --refresh-views
```

Refreshing first copies the published directory to `resources/views/vendor/laravelPhpInfo.backup-<timestamp>-<id>`. A failed backup stops the operation. The command then copies the current templates and leaves unrelated files alone. Reapply any template customizations from the backup. The commands never replace the main configuration or translations.

Display selections are saved in `config/laravelPhpInfoUi.php`; application settings remain in `config/laravelPhpInfo.php`. Rebuild cached configuration after running a setup command if your deployment normally caches it. Static assets are served by the package, so no public asset files need updating.

The controller continues to inherit from the host's base controller when it provides Laravel's middleware API. On newer applications with a plain base controller, it uses Laravel's routing controller instead. Authentication, web sessions, and the configured role middleware remain active as before.

## Translation publishing on Laravel 9 and newer

The package now publishes translations to the path returned by the application. On a typical Laravel 9+ application this is `lang/vendor/laravelPhpInfo`, while older applications use `resources/lang/vendor/laravelPhpInfo`. Custom language paths are respected.

For an installation affected by [issue #8](https://github.com/jeremykenedy/laravel-phpinfo/issues/8):

1. Check the language directory your application uses and back up customized translations.
2. Move only this package's translation directory from the old path to the application's actual language directory, if they differ.
3. Run `php artisan vendor:publish --tag=laravelPhpInfo-lang` to add any missing files.

Existing translation files are not overwritten by default. Merge new language lines from `src/resources/lang/en/laravel-phpinfo.php` if needed. Do not delete another package's translations. A Laravel application that intentionally retains `resources/lang` can continue using that directory.

## Optional frameworks

Bootstrap 5 and Tailwind are explicit choices. Use `phpinfo:update --css=bootstrap5` or `phpinfo:switch --css=tailwind`; these commands do not replace your application's CSS dependencies. Run `npm run build` after updating bundled framework styles.

`--ui-kit` is optional and configures an already installed Laravel UI Kit package. The normal install and update path has no dependency on UI Kit, Livewire, or any JavaScript framework.
