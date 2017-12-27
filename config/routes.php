<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::prefix('admin', function (RouteBuilder $routes) {
	$routes->plugin('Feedback', function (RouteBuilder $routes) {
		$routes->connect('/', ['controller' => 'Feedback', 'action' => 'index']);

		$routes->fallbacks();
	});
});

Router::plugin('Feedback', function (RouteBuilder $routes) {
	$routes->connect('/', ['controller' => 'Feedback', 'action' => 'index']);

	$routes->fallbacks();
});
