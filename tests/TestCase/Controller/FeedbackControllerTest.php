<?php

namespace Feedback\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Feedback\Store\FilesystemStore;

/**
 * @uses \Feedback\Controller\FeedbackController
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

		$savepath = Configure::read('Feedback.configuration.Filesystem.location');
		$files = glob($savepath . '*.*') ?: [];
		foreach ($files as $file) {
			unlink($file);
		}
	}

	/**
	 * @return void
	 */
	public function testIndex() {
		$this->get(['plugin' => 'Feedback', 'controller' => 'Feedback', 'action' => 'index']);

		$this->assertResponseCode(200);
		$this->assertNoRedirect();
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

		$this->get(['plugin' => 'Feedback', 'controller' => 'Feedback', 'action' => 'viewimage', $file]);

		$this->assertResponseCode(200);
		$this->assertNoRedirect();

		unlink($savepath . $file);
	}

	/**
	 * @return void
	 */
	public function testSaveInvalid() {
		$this->disableErrorHandlerMiddleware();

		$this->expectException(MethodNotAllowedException::class);

		$this->get(['plugin' => 'Feedback', 'controller' => 'Feedback', 'action' => 'save']);
	}

	/**
	 * @return void
	 */
	public function testSave() {
		$data = [];
		$this->post(['plugin' => 'Feedback', 'controller' => 'Feedback', 'action' => 'save'], $data);

		$this->assertResponseCode(200);
		$this->assertNoRedirect();

		$expected = __d('feedback', 'Thank you. Your feedback was saved.');
		$this->assertResponseEquals($expected);
	}

	/**
	 * @return void
	 */
	public function testSaveScreenshot() {
		$data = [
			'screenshot' => 123,
		];
		$this->post(['plugin' => 'Feedback', 'controller' => 'Feedback', 'action' => 'save'], $data);

		$this->assertResponseCode(200);
		$this->assertNoRedirect();

		$savepath = Configure::read('Feedback.configuration.Filesystem.location');

		$feedbacks = [];
		//Loop through files
		foreach (glob($savepath . '*-' . session_id() . '.feedback') as $feedbackfile) {
			$feedbackObject = unserialize(file_get_contents($feedbackfile));
			$feedbacks[$feedbackObject['time']] = $feedbackObject;
		}

		//Sort by time
		krsort($feedbacks);

		$last = array_shift($feedbacks);
		$this->assertSame('123', $last['screenshot']);
	}

	public function testApplicationAccessCheck(): void {
		$this->disableErrorHandlerMiddleware();
		Configure::write('Test.denyFeedback', true);
		$this->expectException(ForbiddenException::class);
		try {
			$this->get(['plugin' => 'Feedback', 'controller' => 'Feedback', 'action' => 'index']);
		} finally {
			Configure::delete('Test.denyFeedback');
		}
	}

	public function testAjaxGetCannotSave(): void {
		$this->disableErrorHandlerMiddleware();
		$this->expectException(MethodNotAllowedException::class);
		$this->configRequest(['headers' => ['X-Requested-With' => 'XMLHttpRequest']]);
		$this->get(['plugin' => 'Feedback', 'controller' => 'Feedback', 'action' => 'save']);

	}

	public function testForcedSessionIdentity(): void {
		Configure::write('Feedback.forceauthusername', true);
		Configure::write('Feedback.forceemail', true);
		$this->session(['Auth' => ['User' => ['username' => 'Session user', 'email' => 'session@example.com']]]);
		$this->post(['plugin' => 'Feedback', 'controller' => 'Feedback', 'action' => 'save'], [
			'name' => 'Submitted user',
			'email' => 'submitted@example.com',
		]);

		$this->assertResponseOk();
		$files = glob(TMP . '*-' . session_id() . '.feedback');
		$this->assertCount(1, $files);
		$data = unserialize(file_get_contents($files[0]), ['allowed_classes' => false]);
		$this->assertSame('Session user', $data['name']);
		$this->assertSame('session@example.com', $data['email']);
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
