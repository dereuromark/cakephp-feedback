<?php

namespace IdeHelper\Test\TestCase\Generator;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Feedback\Store\StoreCollection;

class StoreCollectionTest extends TestCase {

	/**
	 * @var \Feedback\Store\StoreCollection
	 */
	protected $storeCollection;

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$this->storeCollection = new StoreCollection();
	}

	/**
	 * @return void
	 */
	public function testCollectInvalid() {
		$data = [
		];
		$result = $this->storeCollection->save($data);

		$expected = [
			[
				'result' => false,
				'msg' => '',
			],
		];
		$this->assertSame($expected, $result);
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$config = [
			'configuration' => [
				'Filesystem' => [
					'location' => TMP,
				],
			],
		];
		Configure::write('Feedback', $config);

		$data = [
			'screenshot' => 'abc',
			'time' => time(),
			'sid' => '123',
		];
		$result = $this->storeCollection->save($data);

		$expected = [
			[
				'result' => true,
				'msg' => __d('feedback', 'Thank you. Your feedback was saved.'),
			],
		];
		$this->assertSame($expected, $result);
		$file = TMP . $data['time'] . '-' . $data['sid'] . '.feedback';
		$this->assertFileExists($file);
		unlink($file);
	}

}
