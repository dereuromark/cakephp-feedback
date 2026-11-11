<?php

namespace Feedback\Test\TestCase\Controller\Admin;

use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Event\EventManager;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\MiddlewareQueue;
use Cake\Http\Session;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Feedback\Store\FilesystemStore;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

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
	 * @return array<string, array{0: array<string, string>, 1: string}>
	 */
	public static function nonceProvider(): array {
		return [
			'cspScriptNonce' => [['cspScriptNonce' => 'core-nonce'], 'core-nonce'],
			'cspNonce fallback' => [['cspNonce' => 'custom-nonce'], 'custom-nonce'],
			'both set' => [['cspScriptNonce' => 'core-nonce', 'cspNonce' => 'custom-nonce'], 'core-nonce'],
		];
	}

	/**
	 * @param array<string, string> $attributes
	 * @param string $expected
	 *
	 * @return void
	 */
	#[DataProvider('nonceProvider')]
	public function testListingScriptNonce(array $attributes, string $expected): void {
		$this->disableErrorHandlerMiddleware();
		foreach ($attributes as $name => $value) {
			$this->withRequestAttribute($name, $value);
		}

		$this->get(['prefix' => 'Admin', 'plugin' => 'Feedback', 'controller' => 'Feedback', 'action' => 'listing']);

		$this->assertResponseCode(200);
		$this->assertResponseContains('<script nonce="' . $expected . '">');
		$this->assertResponseNotRegExp('/<script(?![^>]*nonce="' . $expected . '")/');
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

	public function testApplicationAccessCheck(): void {
		$this->disableErrorHandlerMiddleware();
		Configure::write('Test.denyFeedback', true);
		$this->expectException(ForbiddenException::class);
		try {
			$this->get(['prefix' => 'Admin', 'plugin' => 'Feedback', 'controller' => 'Feedback', 'action' => 'index']);
		} finally {
			Configure::delete('Test.denyFeedback');
		}
	}

	public function testApplicationRedirectIsPreserved(): void {
		Configure::write('Test.redirectFeedback', true);
		try {
			$this->get(['prefix' => 'Admin', 'plugin' => 'Feedback', 'controller' => 'Feedback', 'action' => 'index']);
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
