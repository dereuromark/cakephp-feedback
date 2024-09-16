<?php

namespace Feedback\Test\TestCase\Controller\Admin;

use Cake\Core\Configure;
use Cake\Http\Session;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Feedback\Store\FilesystemStore;

/**
 * @uses \Feedback\Controller\Admin\FeedbackController
 */
class FeedbackControllerTest extends TestCase {

	use IntegrationTestTrait;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->loadPlugins(['Feedback']);

		Configure::write('Feedback', [
			'configuration' => [
				FilesystemStore::NAME => [
					'location' => TMP,
				],
			],
		]);
	}

	/**
	 * @return void
	 */
	public function testIndex() {
		$this->disableErrorHandlerMiddleware();

		$this->get(['prefix' => 'Admin', 'plugin' => 'Feedback', 'controller' => 'Feedback', 'action' => 'index']);
		$this->assertResponseCode(200);
	}

	/**
	 * @return void
	 */
	public function testListing() {
		$this->disableErrorHandlerMiddleware();

		$savepath = Configure::read('Feedback.configuration.Filesystem.location');
		$files = glob($savepath . '*.feedback') ?: [];
		foreach ($files as $file) {
			unlink($file);
		}

		$file = time() . '-' . (new Session())->id() . '.feedback';
		$data = [
			'screenshot' => '123',
			'time' => time(),
			'filename' => 'filename',
			'subject' => 'subject',
			'feedback' => 'feedback',
		];
		file_put_contents($savepath . $file, serialize($data));
		$this->assertFileExists($savepath . $file);

		$this->get(['prefix' => 'Admin', 'plugin' => 'Feedback', 'controller' => 'Feedback', 'action' => 'listing']);
		$this->assertResponseCode(200);

		unlink($savepath . $file);
	}

	/**
	 * @return void
	 */
	public function testViewImage() {
		$file = time() . '-' . session_id() . '.feedback';
		$savepath = Configure::read('Feedback.configuration.Filesystem.location');
		$data = [
			'screenshot' => '123',
		];
		file_put_contents($savepath . $file, serialize($data));
		$this->assertFileExists($savepath . $file);

		$this->get(['prefix' => 'Admin', 'plugin' => 'Feedback', 'controller' => 'Feedback', 'action' => 'viewimage', $file]);

		$this->assertResponseCode(200);
		$this->assertNoRedirect();

		unlink($savepath . $file);
	}

	/**
	 * @return void
	 */
	public function testRemove() {
		$this->disableErrorHandlerMiddleware();

		$file = time() . '-' . (new Session())->id() . '.feedback';
		$savepath = Configure::read('Feedback.configuration.Filesystem.location');
		$data = [
			'screenshot' => '123',
		];
		file_put_contents($savepath . $file, serialize($data));
		$this->assertFileExists($savepath . $file);

		$this->post(['prefix' => 'Admin', 'plugin' => 'Feedback', 'controller' => 'Feedback', 'action' => 'remove', $file]);

		$this->assertResponseCode(302);
		$this->assertRedirect(['prefix' => 'Admin', 'plugin' => 'Feedback', 'controller' => 'Feedback', 'action' => 'index']);

		$this->assertFileDoesNotExist($savepath . $file);
	}

	/**
	 * @return void
	 */
	public function tearDown(): void {
		parent::tearDown();

		$savepath = Configure::read('Feedback.configuration.Filesystem.location');
		foreach (glob($savepath . '*-' . session_id() . '.feedback') as $feedbackfile) {
			unlink($feedbackfile);
		}
	}

}
