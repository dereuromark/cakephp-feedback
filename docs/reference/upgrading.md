# Upgrading

This development branch uses CakePHP 6 and PHP 8.5. Follow [Installation](../guide/installation.md) for development dependencies. Entity field permissions use `patchable` rather than `_accessible`.

## Current controller behavior

Plugin controllers now call the application's `beforeFilter()`. Review application callbacks that assume every controller belongs to the app or that allow or deny actions by controller name. Submission accepts POST or PUT; an AJAX GET no longer saves a report.

The widget forwards the request's CSRF token and uses the server's success message. Forced name/email settings retain the resolved session account values instead of replacing them with empty values.

## Existing configuration

Keep storage outside webroot and disable return links unless your application exposes the corresponding protected report action. Database-only installations should disable return links because the built-in link points to the filesystem index.

Legacy integration configuration does not install an active store. Migrate custom integrations to `StoreInterface`; do not copy old Bitbucket API or password-based GitHub examples into new applications.

The bundled screenshot library uses the html2canvas 0.4.1 callback API. Replacing it with a newer library requires updating the widget code too. Bootstrap's jQuery modal API is optional; without it, the widget uses alerts.

Direct callers of the deprecated `FeedbackstoreTable` methods can retain settings under `Feedback.methods.<store>`. When `Feedback.configuration.<store>` exists, only that block is read, including explicit null values. Otherwise, the released `Feedback.methods.<store>` block is used. Credentials from the two paths are never merged. This compatibility path does not register a store or modernize the legacy integrations.
