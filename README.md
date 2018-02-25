# Laravel PHP Info

[![Latest Stable Version](https://poser.pugx.org/jeremykenedy/laravel-phpinfo/v/stable)](https://packagist.org/packages/jeremykenedy/laravel-phpinfo)
[![Total Downloads](https://poser.pugx.org/jeremykenedy/laravel-phpinfo/downloads)](https://packagist.org/packages/jeremykenedy/laravel-phpinfo)
[![Travis-CI Build](https://travis-ci.org/jeremykenedy/laravel-phpinfo.svg?branch=master)](https://travis-ci.org/jeremykenedy/laravel-phpinfo)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Table of contents:
- [About](#about)
- [Features](#features)
- [Requirements](#requirements)
- [Installation Instructions](#installation-instructions)
- [Configuration](#configuration)
- [Route](#route)
- [Screenshot](#screenshot)
- [File Tree](#file-tree)
- [License](#license)

### About
Laravel PHP Info is a package that provides a [PHPInfo()](http://php.net/manual/en/function.phpinfo.php) page using blade templating.

The config file can be used to enable/disable auth protection and specify the roles middleware.

| Laravel PHP Info |
| :------------ |
| Easily show [PHPInfo()](http://php.net/manual/en/function.phpinfo.php) page with blade templates |
| Can publish customizable views and assets |
| Enable/Disable Auth middleware |
| Specify additional middlewares |
| [configuration](#configuration) options |
| Uses Language [localization](https://laravel.com/docs/5.5/localization) files |

### Requirements
* [Laravel 5.3, 5.4, 5.5, or 5.6+](https://laravel.com/docs/installation)

### Installation Instructions
1. From your projects root folder in terminal run:

```bash
    composer require jeremykenedy/laravel-phpinfo
```

2. Register the package

* Laravel 5.5 and up
Uses package auto discovery feature, no need to edit the `config/app.php` file.

* Laravel 5.4 and below
Register the package with laravel in `config/app.php` under `providers` with the following:

```php
    'providers' => [
        jeremykenedy\laravelPhpInfo\LaravelPhpInfoServiceProvider::class,
    ];
```

3. Publish the packages views, config file, assets, and language files by running the following from your projects root folder:

```bash
    php artisan vendor:publish --tag=laravelPhpInfo
```

### Configuration
Laravel PHP Info can be configured in directly in [`/config/laravelPhpInfo.php`](https://github.com/jeremykenedy/laravel-phpinfo/blob/master/src/config/laravelPhpInfo.php) file.

##### Config File
Here is the [`/config/laravelPhpInfo.php`](https://github.com/jeremykenedy/laravel-phpinfo/blob/master/src/config/laravelPhpInfo.php) configuration options:

```php
/*
|--------------------------------------------------------------------------
| Laravel PHP Info settings
|--------------------------------------------------------------------------
*/

// The parent blade file
'laravelPhpInfoBladeExtended'   => 'layouts.app',

// Enable `auth` middleware
'authEnabled'                   => true,

// Enable Optional Roles Middleware
'rolesEnabled'                  => false,

// Optional Roles Middleware
'rolesMiddlware'                => 'role:admin',

'bootstapVersion'               => '4',

// Additional Card classes for styling -
// See: https://getbootstrap.com/docs/4.0/components/card/#background-and-color
// Example classes: 'text-white bg-primary mb-3'
'bootstrapCardClasses'          => '',
```

### Route
* [`/phpinfo`](https://github.com/jeremykenedy/laravel-phpinfo/blob/master/src/resources/views/phpinfo/php-info.blade.php)

### Screenshot
![PHP Info page](https://s3-us-west-2.amazonaws.com/github-project-images/laravel-phpinfo/phpinfo1.jpg)

### File Tree

```
LaravelPhpInfo
    ├── .gitignore
    ├── .travis.yml
    ├── LICENSE
    ├── README.md
    ├── composer.json
    └── src
        ├── app
        │   └── Http
        │       └── Controllers
        │           └── LaravelPhpInfoController.php
        ├── config
        │   └── laravelPhpInfo.php
        ├── laravelPhpInfoServiceProvider.php
        ├── resources
        │   ├── lang
        │   │   └── en
        │   │       └── laravel-phpinfo.php
        │   └── views
        │       └── phpinfo
        │           └── php-info.blade.php
        └── routes
            └── web.php

```

* Tree command can be installed using brew: `brew install tree`
* File tree generated using command `tree -a -I '.git|node_modules|vendor|storage|tests`

### License
Laravel PHP Info is licensed under the MIT license. Enjoy!
