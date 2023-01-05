<?php

namespace Feedback;

use Cake\Core\BasePlugin;
use Cake\Routing\RouteBuilder;

class Plugin extends BasePlugin {

	/**
	 * @var bool
	 */
	protected bool $middlewareEnabled = false;

	/**
	 * @var bool
	 */
	protected bool $consoleEnabled = false;

	/**
	 * @var bool
	 */
	protected bool $bootstrapEnabled = false;

	/**
	 * @param \Cake\Routing\RouteBuilder $routes The route builder to update.
	 * @return void
	 */
	public function routes(RouteBuilder $routes): void {
		$routes->plugin(
			'Feedback',
			function (RouteBuilder $routes): void {
				$routes->connect('/', ['controller' => 'Feedback', 'action' => 'index']);

				$routes->fallbacks();
			},
		);

		$routes->prefix('Admin', function (RouteBuilder $routes): void {
			$routes->plugin('Feedback', function (RouteBuilder $routes): void {
				$routes->connect('/', ['controller' => 'Feedback', 'action' => 'index']);

				$routes->fallbacks();
			});
		});

	}

}
