<?php
declare(strict_types = 1);

namespace Feedback\Test\TestCase\Model\Table;

use Cake\Core\Configure;
use Cake\Mailer\TransportFactory;
use Cake\TestSuite\TestCase;
use Feedback\Model\Table\FeedbackstoreTable;
use TypeError;

/**
 * @uses \Feedback\Model\Table\FeedbackstoreTable
 */
class FeedbackstoreTableTest extends TestCase {

	/**
	 * @var \Feedback\Model\Table\FeedbackstoreTable
	 */
	protected $Feedbackstore;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		TransportFactory::drop('default');
		TransportFactory::setConfig('default', [
			'className' => 'Debug',
		]);

		$this->Feedbackstore = $this->getTableLocator()->get('Feedback.Feedbackstore', [
			'className' => FeedbackstoreTable::class,
		]);

		Configure::delete('Feedback');
	}

	/**
	 * @return void
	 */
	protected function tearDown(): void {
		TransportFactory::drop('default');
		Configure::delete('Feedback');

		// mail() writes a screenshot attachment to ROOT/tmp before sending; clean up any leftovers.
		foreach (glob(ROOT . DS . 'tmp' . DS . '*.png') ?: [] as $tmpFile) {
			unlink($tmpFile);
		}

		parent::tearDown();
	}

	/**
	 * The mail provider settings must be read from the documented `Feedback.configuration.mail.*`
	 * path (the same path StoreCollection and the controllers use), not from `Feedback.methods.mail.*`.
	 *
	 * When `to`/`from` are read from the correct path, `Mailer::setTo()`/`setFrom()` receive valid
	 * values and do not throw. If the values are read from the wrong path they resolve to null and
	 * `setFrom(null)` throws a TypeError (array|string expected). So "no TypeError" proves the path.
	 *
	 * @return void
	 */
	public function testMailReadsConfigurationPath(): void {
		Configure::write('Feedback.configuration.mail', [
			'to' => 'target@example.com',
			'from' => ['noreply@example.com' => 'FeedbackIt mailer'],
		]);

		$feedbackObject = $this->feedbackObject();

		$threw = false;
		try {
			$this->Feedbackstore->mail($feedbackObject);
		} catch (TypeError $e) {
			$threw = true;
		}

		$this->assertFalse($threw, 'mail provider config was not read from Feedback.configuration.mail.*');
	}

	/**
	 * Sanity check: without any config the recipient/sender are null and `setFrom(null)` throws,
	 * which is exactly the failure users hit when their config sat under the documented
	 * `configuration` path while the code only read the `methods` path.
	 *
	 * @return void
	 */
	public function testMailMissingConfigThrows(): void {
		$feedbackObject = $this->feedbackObject();

		$this->expectException(TypeError::class);
		$this->Feedbackstore->mail($feedbackObject);
	}

	/**
	 * @return array<string, mixed>
	 */
	protected function feedbackObject(): array {
		return [
			'screenshot' => base64_encode('image-bytes'),
			'subject' => 'A subject',
			'feedback' => 'Some feedback body',
			'browser' => 'Firefox',
			'browser_version' => '1.0',
			'url' => 'http://example.com',
			'os' => 'Linux',
			'name' => 'Reporter',
			'email' => '',
		];
	}

}
