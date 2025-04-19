# Feedback Plugin Documentation

## Installation and Setup

1. Include the Feedback CakePHP plugin with composer in your application:
    ```
    composer require dereuromark/cakephp-feedback
    ```
2. Load the plugin:
    ```
    bin/cake plugin load Feedback
    ```

    Make sure to disable routing if you do not have authentication set up:
    ```
    bin/cake plugin load Feedback --no-routes
    ```
    You do not want visitors to be able to browse to the feedback backend.

    This backend is also optional, you can always replace it with your own.

3. Copy the default feedback config file into your applications config folder:

    Copy `vendor/dereuromark/cakephpfeedback/config/config.dist.php` to `config/app_feedback.php`
    and adjust it to your needs. Then include it as `Configure::load('app_feedback')`.

    You can also just copy-and-paste the config array into your existing app.php file.

4. Use the sidebar element in a view or layout to place the feedback tab on that (or those) pages.
    It doesn't matter where you place the following line since it uses absolute DOM element positioning.
    ```php
    <?php echo $this->element('Feedback.sidebar');?>`
    ```
    It is recommended to add it as one of the last elements in your layout ctp, though, shortly before the closing body tag.
    It must however be before your layout's `<?php echo $this->fetch('script') ?>` line that inserts the fetched JS code.

## Usage and Configuration

### Stores
By default, it will use the Filesystem store. This only requires a writable directory below webroot (usually in `ROOT . DS . 'files' . DS`).

If you want to add or replace stores, you can adjust it in your config:
```php
'Feedback' => [
    'stores' => [
        \Feedback\Store\FilesystemStore::class => null, // This disables the default
        \App\Store\MyCustomStore::class => \App\Store\MyCustomStore::class,
        ...
    ],
```

The configuration per store is in the `configuration` key:
If you want to add or replace stores, you can adjust it in your config:
```php
'Feedback' => [
    'configuration' => [
        'Filesystem' => [
            'location' => ROOT . DS . 'files' . DS . 'feedback' . DS,
        ],
        ...
    ],
```

Note that only the first store will be used for feedback. This should be the primary one.
If the others fail the user will still get the successful feedback from the first store method.

#### Database Store
You can also use the built in database storage as `feedback_items` table.
Execute the migrations (or copy and modify on app level for custom fields):
```
bin/cake migrations migrate -p Feedback
```

Then make sure to hook in this store:
```php
'Feedback' => [
    'stores' => [
        \Feedback\Store\FilesystemStore::class => null, // This disables the default
        \Feedback\Store\DatabaseStore::class => \Feedback\Store\DatabaseStore::class,
    'configuration' => [
        'Database' => [
            ... // optional additional config
        ],
        ...
    ],
```
It will then store the data in this database table, and in the backend you can
paginate and display those items.

### Other options

See `config.php` file.

### Authentication
If you are using AuthComponent, you need to make sure at least `save()` method is publicly accessible.
If you want your visitor to see the posted feedback using `'returnlink'` key, you might also want to allow the index and viewimage actions.
You can do that e.g. in your AppController.

Tip: Use [TinyAuth](https://github.com/dereuromark/cakephp-tinyauth) and just set it in the `auth_allow.ini` file:
```
Feedback.Feedback = save, index, viewimage
```

Also make sure that the admin backend is only available to respective roles.
With TinyAuth it would look like this in `auth_acl.ini` file:
```
[Feedback.Admin/Feedback]
* = admin

[Feedback.Admin/FeedbackItems]
* = admin
```

WARNING: Do not expose the controller actions without any proper auth in place.
You do not want to make the uploaded content accessible publicly.

### Writing your own store

Just implement the StoreInterface, add your save functionality and include it in the above config.
So if you wanted to write into a "feedback" table using a FeedbackTable class, you could do:

```php
namespace App\Store;

use Feedback\Store\StoreInterface;

class DatabaseStore implements StoreInterface {

    public const NAME = 'Database'; // For configuration options

    /**
     * @param array $object
     * @param array $options
     *
     * @return array
     */
    public function save(array $object, array $options = []): array {
        $Feedback = TableRegistry::getLocator()->get('Feedback');
        $feedback = $Feedback->newEntity([
            ...
        ]);
        $result = (bool)$Feedback->save($feedback);

        ...
    }

}
```
If you need additional configuration options, use:
```php
'Feedback' => [
    'autoLink' => false,
    ...

    'configuration' => [
        'Database' => [
            ...
        ],
        ...
    ],
```
