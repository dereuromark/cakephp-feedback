<?php
declare(strict_types = 1);

namespace Feedback\Test\TestCase\View\Element;

use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use PHPUnit\Framework\Attributes\DataProvider;

class SidebarElementTest extends TestCase {

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

		Configure::delete('Feedback');
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
	public function testScriptNonce(array $attributes, string $expected): void {
		$request = new ServerRequest();
		foreach ($attributes as $name => $value) {
			$request = $request->withAttribute($name, $value);
		}
		$view = new View($request);

		$result = $view->element('Feedback.sidebar');

		$this->assertStringContainsString('<script nonce="' . $expected . '">', $result);
		$this->assertDoesNotMatchRegularExpression('/<script(?![^>]*nonce="' . $expected . '")/', $result);
	}

}
