# Getting started

Start with filesystem storage and a single submission route. Complete [Installation](./installation) first.

## Choose private storage

Add these settings after loading the example configuration in `config/bootstrap.php`:

```php
Configure::write('Feedback.returnlink', false);
Configure::write('Feedback.enablecopybyemail', false);
```

The return link is disabled because this setup does not expose report browsing. Sending a copy by email uses legacy code and is not part of the beginner setup.

## Add the submission route

In your application's existing route scope, add this before its fallback routes:

```php
$routes->plugin('Feedback', ['path' => '/feedback'], function (\Cake\Routing\RouteBuilder $routes): void {
    $routes->connect('/save', ['controller' => 'Feedback', 'action' => 'save'])
        ->setMethods(['POST', 'PUT']);
});
```

The controller inherits your `AppController`. Allow this action through your application's authentication and authorization rules if anonymous visitors may submit feedback. Keep CSRF middleware enabled. The widget sends the request's CSRF token in the `X-CSRF-Token` header.

## Render the tab

In `templates/layout/default.php`, place the element near the end of the body, outside other forms. Load jQuery before the fetched script block:

```php
<?= $this->element('Feedback.sidebar') ?>
<?= $this->fetch('script') ?>
```

Use your layout's existing `fetch('script')` once. The element adds the bundled screenshot library and widget script to that block, and renders its stylesheet directly unless `Feedback.skipCss` is enabled.

## Submit a first report

Open a page with the element, enter a subject and message, accept the screenshot notice, and submit. The request should return a success message. Check `tmp/feedback/` for a `.feedback` file containing the report and screenshot data.

A screenshot renders the page's DOM, rather than capturing the whole browser window. Cross-origin images and unsupported CSS may be missing. Test representative pages before relying on screenshots for support.

If submission fails, inspect the POST to `/feedback/save` in your browser's network panel. Check access rules, the CSRF header, writable storage, and whether jQuery loaded before `Feedback.functions.js`. A success response describes the first store's result, so verify each destination separately when using multiple stores.
