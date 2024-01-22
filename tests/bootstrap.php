<?php

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Database\Type\JsonType;
use Cake\Database\TypeFactory;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Table;
use Cake\TestSuite\Fixture\SchemaLoader;
use Cake\Utility\Security;
use Cake\View\View;
use Feedback\FeedbackPlugin;
use TestApp\Application;
use TestApp\Controller\AppController;

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

define('ROOT', dirname(__DIR__));
define('APP_DIR', 'src');

// Point app constants to the test app.
define('TEST_ROOT', ROOT . DS . 'tests' . DS . 'test_app' . DS);
define('APP', TEST_ROOT . APP_DIR . DS);
define('TEST_FILES', ROOT . DS . 'tests' . DS . 'test_files' . DS);

define('TMP', ROOT . DS . 'tmp' . DS);
if (!is_dir(TMP)) {
	mkdir(TMP, 0770, true);
}
define('TESTS', ROOT . DS . 'tests' . DS);
define('CONFIG', TESTS . 'config' . DS);

define('LOGS', TMP . 'logs' . DS);
define('CACHE', TMP . 'cache' . DS);

define('CAKE_CORE_INCLUDE_PATH', ROOT . '/vendor/cakephp/cakephp');
define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
define('CAKE', CORE_PATH . APP_DIR . DS);

require dirname(__DIR__) . '/vendor/autoload.php';
require CORE_PATH . 'config/bootstrap.php';
require CAKE_CORE_INCLUDE_PATH . '/src/functions.php';

Configure::write('App', [
	'encoding' => 'utf-8',
	'namespace' => 'App',
	'paths' => [
		'templates' => [TESTS . 'test_app' . DS . 'templates' . DS],
	],
	'fullBaseUrl' => 'http://localhost',
]);

Configure::write('debug', true);

$cache = [
	'default' => [
		'engine' => 'File',
	],
	'_cake_core_' => [
		'className' => 'File',
		'prefix' => 'crud_myapp_cake_core_',
		'path' => CACHE . 'persistent/',
		'serialize' => true,
		'duration' => '+10 seconds',
	],
	'_cake_model_' => [
		'className' => 'File',
		'prefix' => 'crud_my_app_cake_model_',
		'path' => CACHE . 'models/',
		'serialize' => 'File',
		'duration' => '+10 seconds',
	],
];

Cache::setConfig($cache);

Security::setSalt('123');

TypeFactory::map('json', JsonType::class);

class_alias(Application::class, 'App\Application');
class_alias(AppController::class, 'App\Controller\AppController');
class_alias(Table::class, 'App\Model\Table\Table');
class_alias(View::class, 'App\View\AppView');

Plugin::getCollection()->add(new FeedbackPlugin());

// Ensure default test connection is defined
if (!getenv('DB_URL')) {
	putenv('DB_URL=sqlite:///:memory:');
}

ConnectionManager::setConfig('test', [
	'url' => getenv('DB_URL') ?: null,
	'timezone' => 'UTC',
	'quoteIdentifiers' => true,
	'cacheMetadata' => true,
]);

if (env('FIXTURE_SCHEMA_METADATA')) {
	$loader = new SchemaLoader();
	$loader->loadInternalFile(env('FIXTURE_SCHEMA_METADATA'));
}
