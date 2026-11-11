<?php
declare(strict_types = 1);

namespace Feedback\Test\TestCase\Controller\Admin;

use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Event\EventManager;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\MiddlewareQueue;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

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

		$this->loadPlugins(['Feedback']);

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
	 * @return array<string, array{0: string, 1: array<string, string>, 2: string}>
	 */
	public static function nonceProvider(): array {
		$cases = [];
		foreach (['index', 'view', 'edit'] as $action) {
			$cases[$action . ' cspScriptNonce'] = [$action, ['cspScriptNonce' => 'core-nonce'], 'core-nonce'];
			$cases[$action . ' cspNonce fallback'] = [$action, ['cspNonce' => 'custom-nonce'], 'custom-nonce'];
			$cases[$action . ' both set'] = [$action, ['cspScriptNonce' => 'core-nonce', 'cspNonce' => 'custom-nonce'], 'core-nonce'];
		}

		return $cases;
	}

	/**
	 * CakePHP's CspMiddleware stores the nonce as `cspScriptNonce`, which wins over a custom `cspNonce`.
	 *
	 * @param string $action
	 * @param array<string, string> $attributes
	 * @param string $expected
	 *
	 * @return void
	 */
	#[DataProvider('nonceProvider')]
	public function testScriptNonce(string $action, array $attributes, string $expected): void {
		$this->disableErrorHandlerMiddleware();
		foreach ($attributes as $name => $value) {
			$this->withRequestAttribute($name, $value);
		}

		$url = ['prefix' => 'Admin', 'plugin' => 'Feedback', 'controller' => 'FeedbackItems', 'action' => $action];
		if ($action !== 'index') {
			$url[] = 1;
		}
		$this->get($url);

		$this->assertResponseCode(200);
		$this->assertResponseContains('<script nonce="' . $expected . '">');
		$this->assertResponseNotRegExp('/<script(?![^>]*nonce="' . $expected . '")/');
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

	public function testApplicationAccessCheck(): void {
		$this->disableErrorHandlerMiddleware();
		Configure::write('Test.denyFeedback', true);
		$this->expectException(ForbiddenException::class);
		try {
			$this->get(['prefix' => 'Admin', 'plugin' => 'Feedback', 'controller' => 'FeedbackItems', 'action' => 'index']);
		} finally {
			Configure::delete('Test.denyFeedback');
		}
	}

	public function testApplicationRedirectIsPreserved(): void {
		Configure::write('Test.redirectFeedback', true);
		try {
			$this->get(['prefix' => 'Admin', 'plugin' => 'Feedback', 'controller' => 'FeedbackItems', 'action' => 'index']);
			$this->assertRedirect('/login');
			$this->assertResponseCode(302);
		} finally {
			Configure::delete('Test.redirectFeedback');
		}
	}

	/**
	 * Adds a middleware that sets a request attribute, like a CSP middleware would.
	 *
	 * @param string $name
	 * @param string $value
	 *
	 * @return void
	 */
	protected function withRequestAttribute(string $name, string $value): void {
		EventManager::instance()->on('Server.buildMiddleware', function (EventInterface $event, MiddlewareQueue $queue) use ($name, $value): void {
			$queue->prepend(function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($name, $value): ResponseInterface {
				return $handler->handle($request->withAttribute($name, $value));
			});
		});
	}

}
