<?php

namespace Feedback;

use Cake\Core\BasePlugin;
use Cake\Routing\RouteBuilder;

class Plugin extends BasePlugin {

	/**
	 * @var bool
	 */
	protected $middlewareEnabled = false;

	/**
	 * @var bool
	 */
	protected $consoleEnabled = false;

	/**
	 * @var bool
	 */
	protected $bootstrapEnabled = false;

	/**
	 * @param \Cake\Routing\RouteBuilder $routes The route builder to update.
	 * @return void
	 */
	public function routes(RouteBuilder $routes): void {
		$routes->plugin(
			'Feedback',
			function (RouteBuilder $routes) {
				$routes->connect('/', ['controller' => 'Feedback', 'action' => 'index']);

				$routes->fallbacks();
			},
		);

		$routes->prefix('Admin', function (RouteBuilder $routes) {
			$routes->plugin('Feedback', function (RouteBuilder $routes) {
				$routes->connect('/', ['controller' => 'Feedback', 'action' => 'index']);

				$routes->fallbacks();
			});
		});

	}

}
