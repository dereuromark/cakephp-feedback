# Stores

## Filesystem

The default store writes serialized arrays to `tmp/feedback/` when using the example configuration. No database migration is required. Files are named from the submission timestamp and session ID; repeated submissions from the same session within one second can overwrite the same file. Use database storage if you need separate records for that case.

Do not expose these files through your web server. Use the protected report actions or your own reader.

## Database

Install and load CakePHP Migrations in your application, then create the plugin table:

```sh
bin/cake migrations migrate -p Feedback
```

Select the database store in configuration:

```php
use Feedback\Store\DatabaseStore;
use Feedback\Store\FilesystemStore;

Configure::write('Feedback.stores', [
    FilesystemStore::class => null,
    DatabaseStore::class => DatabaseStore::class,
]);
Configure::write('Feedback.configuration.Database.table', 'Feedback.FeedbackItems');
Configure::write('Feedback.returnlink', false);
```

Reports are saved to `feedback_items`. URL and session ID are required by the built-in table validation. Additional fields, including the screenshot, are kept in its JSON `data` column. The admin database interface lists, views, edits, and removes records; enable it only with [access checks](../guide/access).

The database store's optional return link points to the filesystem report index. Keep it disabled when using only the database store.

## Multiple stores

Stores run in configured order. The filesystem default is appended unless explicitly disabled. All stores are called, but the submission response uses only the first result. A later store's unsuccessful result does not change that response; an exception can still abort the request. Use application logging or delivery monitoring when additional destinations matter.

## Custom store

Implement `Feedback\Store\StoreInterface`. A store receives the report array and its configuration, and returns `result` and `msg`:

```php
namespace App\Store;

use Feedback\Store\StoreInterface;

class SupportStore implements StoreInterface {
    public const NAME = 'Support';

    public function save(array $object, array $options = []): array {
        // Replace this example failure with your persistence or delivery code.
        return ['result' => false, 'msg' => 'Support delivery is not configured.'];
    }
}
```

Register `App\Store\SupportStore::class` in `Feedback.stores`, and place its options in `Feedback.configuration.Support`. The controller adds `time` and `sid` before invoking stores. Protect credentials in application configuration and handle delivery errors in your implementation.
