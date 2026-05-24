<?php
declare(strict_types = 1);

namespace Feedback\Test\TestCase\Model\Table;

use Cake\Core\Configure;
use Cake\Mailer\Mailer;
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

		// mail() instantiates a bare `new Mailer()`, which resolves the `default` mailer profile.
		// Point that profile at the `default` (Debug) transport so deliver() has a transport to use.
		Mailer::drop('default');
		Mailer::setConfig('default', [
			'transport' => 'default',
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
		Mailer::drop('default');
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
		} catch (TypeError) {
			$threw = true;
		}

		$this->assertFalse($threw, 'mail provider config was not read from Feedback.configuration.mail.*');
	}

	/**
	 * Regression guard for the send/deliver bug.
	 *
	 * The raw HTML feedback body must be delivered via `Mailer::deliver()`. In CakePHP 5
	 * `Mailer::send(?string $action)` treats a string argument as an action (mailer method)
	 * name, so passing the HTML body throws MissingActionException. That exception is swallowed
	 * by the surrounding try/catch (it only logs), so `mail()` would silently return result=false.
	 *
	 * BEFORE the fix: result is false (MissingActionException caught and logged).
	 * AFTER the fix: result is true and the mail is delivered.
	 *
	 * @return void
	 */
	public function testMailDeliversRawContent(): void {
		Configure::write('Feedback.configuration.mail', [
			'to' => 'target@example.com',
			'from' => ['noreply@example.com' => 'FeedbackIt mailer'],
		]);

		$result = $this->Feedbackstore->mail($this->feedbackObject());

		$this->assertTrue($result['result']);
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
	 * The temporary screenshot attachment written to ROOT/tmp must be removed after mail()
	 * runs, including on the success path (which previously returned before unlink()).
	 *
	 * @return void
	 */
	public function testMailRemovesTempAttachmentFile(): void {
		Configure::write('Feedback.configuration.mail', [
			'to' => 'target@example.com',
			'from' => ['noreply@example.com' => 'FeedbackIt mailer'],
		]);

		$pattern = ROOT . DS . 'tmp' . DS . '*.png';
		$before = count(glob($pattern) ?: []);

		$result = $this->Feedbackstore->mail($this->feedbackObject());

		$this->assertTrue($result['result']);
		$this->assertCount($before, glob($pattern) ?: [], 'mail() leaked the temporary screenshot attachment file');
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
