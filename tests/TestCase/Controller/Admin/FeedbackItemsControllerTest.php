<?php
declare(strict_types = 1);

namespace Feedback\Test\TestCase\Controller\Admin;

use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * @uses \Feedback\Controller\Admin\FeedbackItemsController
 */
class FeedbackItemsControllerTest extends TestCase {

	use IntegrationTestTrait;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		Configure::write('Feedback', [
			'configuration' => [
			],
		]);
	}

	/**
	 * @var string[]
	 */
	protected $fixtures = [
		'plugin.Feedback.FeedbackItems',
	];

	/**
	 * @return void
	 */
	public function testIndex(): void {
		$this->disableErrorHandlerMiddleware();

		$this->get(['prefix' => 'Admin', 'plugin' => 'Feedback', 'controller' => 'FeedbackItems', 'action' => 'index']);
		$this->assertResponseCode(200);
	}

	/**
	 * @return void
	 */
	public function testView(): void {
		$this->markTestIncomplete('Not implemented yet.');
	}

	/**
	 * @return void
	 */
	public function testAdd(): void {
		$this->markTestIncomplete('Not implemented yet.');
	}

	/**
	 * @return void
	 */
	public function testEdit(): void {
		$this->markTestIncomplete('Not implemented yet.');
	}

	/**
	 * @return void
	 */
	public function testDelete(): void {
		$this->markTestIncomplete('Not implemented yet.');
	}

}
