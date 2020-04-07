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
By default it will use the Filesystem store. This only requires a writable directory below webroot (usually in `ROOT . DS . 'files' . DS`).

If you want to add or replace stores, you can adjust it in your config:
```php
'Feedback' => [
    'stores' => [
        \Feedback\Store\FilesystemStore::store => null, // This disables the default
        \App\Store\MyCustomStore::store => \App\Store\MyCustomStore::store,
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

### Other options

See `config.php` file.

### Authentication
If you are using AuthComponent, you need to make sure at least `save()` method is publicly accessible.
If you want your visitor to see the posted feedback using `'returnlink'` key, you might also want to allow the index and viewimage actions.
You can do that e.g. in your AppController.

Tip: Use [TinyAuth](https://github.com/dereuromark/cakephp-tinyauth) and just set it in the auth_allow.ini file:
```
Feedback.Feedback = save, index, viewimage
```

### Writing your own store

Just implement the StoreInterface, add your save functionality and include it in the above config.
So if you wanted to write into a "feedback" table using a FeedbackTable class, you could do:

```php
namespace App\Store;

use Feedback\Store\StoreInterface;

class DatabaseStore implements StoreInterface {

    const NAME = 'Database'; // For configuration options

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
        $result = $Feedback->saveOrFail($feedback);

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
