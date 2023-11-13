<?php
declare(strict_types = 1);

namespace Feedback\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FeedbackItemsFixture
 */
class FeedbackItemsFixture extends TestFixture {

	/**
	 * Fields
	 *
	 * @var array
	 */
	// phpcs:disable
	public array $fields = [
		'id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
		'sid' => ['type' => 'string', 'length' => 120, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
		'url' => ['type' => 'string', 'length' => 190, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
		'name' => ['type' => 'string', 'length' => 120, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
		'email' => ['type' => 'string', 'length' => 120, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
		'subject' => ['type' => 'string', 'length' => 150, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
		'feedback' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
		'priority' => ['type' => 'string', 'length' => 20, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
		'data' => ['type' => 'text', 'length' => 16777215, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
		'status' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
		'created' => ['type' => 'datetime', 'length' => null, 'precision' => null, 'null' => false, 'default' => null, 'comment' => ''],
		'_constraints' => [
			'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
		],
		'_options' => [
			'engine' => 'InnoDB',
			'collation' => 'utf8mb4_unicode_ci'
		],
	];
	// phpcs:enable
	/**
	 * Init method
	 *
	 * @return void
	 */
	public function init(): void {
		$this->records = [
			[
				'id' => 1,
				'sid' => 'Lorem ipsum dolor sit amet',
				'url' => 'Lorem ipsum dolor sit amet',
				'name' => 'Lorem ipsum dolor sit amet',
				'email' => 'Lorem ipsum dolor sit amet',
				'subject' => 'Lorem ipsum dolor sit amet',
				'feedback' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
				'priority' => null,
				'data' => null,
				'status' => 1,
				'created' => '2020-11-14 14:54:36',
			],
		];
		parent::init();
	}

}
