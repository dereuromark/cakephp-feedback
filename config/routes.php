<?php
/**
 * @var \Cake\Routing\RouteBuilder $routes
 */

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

$routes->prefix('Admin', function (RouteBuilder $routes) {
	$routes->plugin('Feedback', function (RouteBuilder $routes) {
		$routes->connect('/', ['controller' => 'Feedback', 'action' => 'index']);

		$routes->fallbacks();
	});
});

$routes->plugin('Feedback', function (RouteBuilder $routes) {
	$routes->connect('/', ['controller' => 'Feedback', 'action' => 'index']);

	$routes->fallbacks();
});
