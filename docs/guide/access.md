# Access and privacy

All plugin controllers extend your application's `AppController` and call its `beforeFilter()`. That callback returns `void` on CakePHP 6; set a redirect response on the event with `$event->setResult($this->redirect('/login'))`. The plugin preserves that response and stops later listeners. Configure authentication and authorization in your application; the plugin does not supply an admin role or separate authentication mode.

## Submission and browsing are separate

`FeedbackController::save()` accepts POST and PUT. The widget uses POST. An AJAX header does not allow a GET submission.

Allow only the submission action for public feedback. Leave report browsing disabled unless your application checks ownership or otherwise restricts access. `index()` lists filesystem reports for the current session. `viewimage()` validates the filename and storage path, but does not check that the report belongs to the current session. A private filename is not an access rule.

The plugin unlocks the submission action for FormProtection because the widget submits a plain HTML form with generated screenshot fields. CSRF middleware remains a separate requirement.

## Admin routes

When your application protects all admin actions, you can enable the plugin routes:

```php
$this->addPlugin('Feedback');
```

Replace the earlier plugin registration; do not register the plugin twice. Full routes include `/feedback`, `/feedback/feedback/viewimage/...`, `/admin/feedback`, and the `Admin/FeedbackItems` actions. Restrict the admin prefix and report actions before enabling these routes.

## Storage and screenshots

Keep filesystem reports outside webroot. Reports include the page URL, browser information, a session identifier, optional name and email, and a screenshot of page content. Set retention and access policies appropriate to that data. Avoid rendering sensitive information on pages where screenshot feedback is available.

`Feedback.enableacceptterms` controls the browser's checkbox. It is a UI prompt, not server-side consent validation. Applications requiring recorded consent must implement that validation themselves.

Submitted names and emails are editable by default. Force options replace them with configured account data, but access control still belongs to the application.
