<?php

namespace Feedback\Test\TestCase\Store;

use Cake\TestSuite\TestCase;
use Feedback\Store\DatabaseStore;

class DatabaseStoreTest extends TestCase {

	/**
	 * @var array<string>
	 */
	protected array $fixtures = [
		'plugin.Feedback.FeedbackItems',
	];

	/**
	 * @var \Feedback\Store\DatabaseStore
	 */
	protected $store;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->store = new DatabaseStore();
	}

	/**
	 * @return void
	 */
	public function testSaveInvalid() {
		$data = [];
		$result = $this->store->save($data);

		$expected = [
			'result' => false,
			'msg' => '',
		];
		$this->assertSame($expected, $result);
	}

	/**
	 * @return void
	 */
	public function testSave() {
		$data = [
			'screenshot' => 'abc',
			'time' => time(),
			'sid' => '123',
			'url' => 'http://example.org',
		];
		$result = $this->store->save($data);

		$expected = [
			'result' => true,
			'msg' => __d('feedback', 'Thank you. Your feedback was saved.'),
		];
		$this->assertSame($expected, $result);

		$feedbackItem = $this->getTableLocator()->get('Feedback.FeedbackItems')->find()->orderByDesc('id')->firstOrFail();

		$expected = [
			'screenshot' => $data['screenshot'],
		];
		$this->assertSame($expected, $feedbackItem->data);
	}

}
