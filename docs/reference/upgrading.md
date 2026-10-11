# Upgrading

Composer defines the supported runtime and CakePHP versions. This code targets CakePHP 5; these changes do not claim CakePHP 6 compatibility.

## Current controller behavior

Plugin controllers now call the application's `beforeFilter()`. Review application callbacks that assume every controller belongs to the app or that allow or deny actions by controller name. Submission accepts POST or PUT; an AJAX GET no longer saves a report.

The widget forwards the request's CSRF token and uses the server's success message. Forced name/email settings retain the resolved session account values instead of replacing them with empty values.

## Existing configuration

Keep storage outside webroot and disable return links unless your application exposes the corresponding protected report action. Database-only installations should disable return links because the built-in link points to the filesystem index.

Legacy integration configuration does not install an active store. Migrate custom integrations to `StoreInterface`; do not copy old Bitbucket API or password-based GitHub examples into new applications.

The bundled screenshot library uses the html2canvas 0.4.1 callback API. Replacing it with a newer library requires updating the widget code too. Bootstrap's jQuery modal API is optional; without it, the widget uses alerts.

Direct callers of the deprecated `FeedbackstoreTable` methods can retain settings under `Feedback.methods.<store>`. Settings under `Feedback.configuration.<store>` take precedence, with the old path used as a fallback. This compatibility path does not register a store or modernize the legacy integrations.
