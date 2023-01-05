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
				'Filesystem' => [
					'location' => TMP . 'feedback_test' . DS,
				],
			],
		]);
	}

	/**
	 * @return void
	 */
	public function tearDown(): void {
		parent::tearDown();

		Configure::delete('Feedback.configuration');
	}

	/**
	 * @var array<string>
	 */
	protected array $fixtures = [
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
		$this->disableErrorHandlerMiddleware();

		$this->get(['prefix' => 'Admin', 'plugin' => 'Feedback', 'controller' => 'FeedbackItems', 'action' => 'view', 1]);
		$this->assertResponseCode(200);
	}

	/**
	 * @return void
	 */
	public function testViewImage(): void {
		$this->disableErrorHandlerMiddleware();

		$feedbackItem = $this->getTableLocator()->get('Feedback.FeedbackItems')->get(1);
		$feedbackItem->data = [
			'screenshot' => 'abc',
		];
		$this->getTableLocator()->get('Feedback.FeedbackItems')->saveOrFail($feedbackItem);

		$this->get(['prefix' => 'Admin', 'plugin' => 'Feedback', 'controller' => 'FeedbackItems', 'action' => 'viewimage', 1]);
		$this->assertResponseCode(200);
	}

	/**
	 * @return void
	 */
	public function testEdit(): void {
		$this->disableErrorHandlerMiddleware();

		$this->get(['prefix' => 'Admin', 'plugin' => 'Feedback', 'controller' => 'FeedbackItems', 'action' => 'edit', 1]);
		$this->assertResponseCode(200);
	}

	/**
	 * @return void
	 */
	public function testDelete(): void {
		$this->disableErrorHandlerMiddleware();

		$this->post(['prefix' => 'Admin', 'plugin' => 'Feedback', 'controller' => 'FeedbackItems', 'action' => 'delete', 1]);
		$this->assertResponseCode(302);
	}

	/**
	 * @return void
	 */
	public function testImportFiles(): void {
		$this->disableErrorHandlerMiddleware();

		$array = [
			'screenshot' => 'abc',
			'time' => time(),
			'sid' => '123',
			'url' => 'http://example.org',
			'name' => 'my-name',
			'email' => 'my-email@test.de',
		];
		$content = serialize($array);
		if (!is_dir(TMP . 'feedback_test' . DS)) {
			mkdir(TMP . 'feedback_test' . DS, 0770, true);
		}
		file_put_contents(TMP . 'feedback_test' . DS . '1605397242-sid.feedback', $content);

		$this->post(['prefix' => 'Admin', 'plugin' => 'Feedback', 'controller' => 'FeedbackItems', 'action' => 'importFiles']);
		$this->assertResponseCode(302);

		$this->assertFileDoesNotExist(TMP . 'feedback_test' . DS . '1605397242-sid.feedback');
	}

}
