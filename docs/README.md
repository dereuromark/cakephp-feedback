# Feedback Plugin Documentation

## Installation and Setup

1. Include the Feedback CakePHP plugin with composer in your application:
	```
	composer require dereuromark/feedback": "dev-master"
	```
2. Load the plugin in config/bootstrap.php:
	```php
	Plugin::load('Feedback', ['bootstrap' => true]);
	```

3. Copy the default feedback config file into your applications config folder:

	Copy `../vendor/dereuromark/cakephpfeedback/config/config.php` to `../config/app_feedback.php`
	and adjust it to your needs. Then include it as `Configure::load('app_feedback')`.
	
	You can also just copy-and-paste the config array into your existing app.php file.
	
	If you `Plugin::load('Feedback', ['bootstrap' => true, ...]);`, it will load the plugin's default config.

4. Use the sidebar element in a view or layout to place the feedback tab on that (or those) pages. 
	It doesn't matter where you place the following line since it uses absolute DOM element positioning.
	```php
	<?php echo $this->element('Feedback.sidebar');?>`
	```
	It is recommended to add it as one of the last elements in your layout ctp, though, shortly before the closing body tag.

### Usage and Configuration

#### Stores
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


### Writing your own store

Just implement the StoreInterface, add your save functionality and include it in the above config.
So if you wanted to write into a "feedback" table using a FeedbackTable class, you could do:

```php
namespace App/Store;

use Feedback\Store\StoreInterface;

class DatabaseStore implements StoreInterface {
	
	const NAME = 'Database'; // For configuration options
	
	/**
	 * @param array $object
	 * @param array $options
	 *
	 * @return array
	 */
	public function save($object, array $options = []) {
		$Feedback = TableRegistry::get('Feedback');
		$feedback = $Feedback->newEntity([
			...
		]);
		$result = $Feedback->save($feedback);
		
		...
	}
	
}
```
