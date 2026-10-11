# Installation

This development branch requires CakePHP 6 and PHP 8.5. Migrations currently
uses the upstream CakePHP 6 compatibility branch.

Install the package in your application:

```sh
composer config minimum-stability dev
composer config prefer-stable true
composer require cakephp/cakephp:^6.0@dev dereuromark/cakephp-feedback:dev-cake6
```

[Composer constraints](https://github.com/dereuromark/cakephp-feedback/blob/master/composer.json) define the supported PHP and CakePHP versions.

## Load the plugin

Load it with routes disabled while you configure access:

```php
// src/Application.php, inside bootstrap() after parent::bootstrap()
$this->addPlugin('Feedback', ['routes' => false]);
```

The beginner setup adds only the submission route. The plugin's full routes also expose report browsing and the admin interface; enable them after implementing [access rules](./access).

## Load configuration

Copy `vendor/dereuromark/cakephp-feedback/config/app.example.php` to `config/app_feedback.php`. In `config/bootstrap.php`, load it after the application's configuration:

```php
use Cake\Core\Configure;

Configure::load('app_feedback');
```

The example selects private filesystem storage through the default store. Its default directory is `ROOT . DS . 'tmp' . DS . 'feedback' . DS`. The web server must be able to write there. Keep that directory outside webroot.

Use the [configuration reference](../reference/configuration) to choose the settings you need.

## Browser assets

The widget needs jQuery loaded before the plugin scripts. Use your application's installed jQuery; a slim build without AJAX support does not work. The plugin bundles html2canvas 0.4.1 for screenshots. Bootstrap is optional: without its jQuery modal API, submission results use an alert. Icon classes can be customized or omitted.

Continue with [Getting started](./getting-started).
