<?php
declare(strict_types = 1);

namespace Feedback\Test\TestCase\Model\Table;

use Cake\TestSuite\TestCase;
use Feedback\Model\Table\FeedbackItemsTable;

/**
 * Feedback\Model\Table\FeedbackItemsTable Test Case
 */
class FeedbackItemsTableTest extends TestCase {

	/**
	 * Test subject
	 *
	 * @var \Feedback\Model\Table\FeedbackItemsTable
	 */
	protected $FeedbackItems;

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	protected $fixtures = [
		'plugin.Feedback.FeedbackItems',
	];

	/**
	 * setUp method
	 *
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();
		$config = $this->getTableLocator()->exists('FeedbackItems') ? [] : ['className' => FeedbackItemsTable::class];
		$this->FeedbackItems = $this->getTableLocator()->get('FeedbackItems', $config);
	}

	/**
	 * tearDown method
	 *
	 * @return void
	 */
	public function tearDown(): void {
		unset($this->FeedbackItems);

		parent::tearDown();
	}

	/**
	 * Test validationDefault method
	 *
	 * @return void
	 */
	public function testSave(): void {
		$data = [
			'url' => 'url',
			'sid' => 'sid',
		];
		$feedbackItem = $this->FeedbackItems->newEntity($data);
		$this->FeedbackItems->saveOrFail($feedbackItem);

		$this->assertNotEmpty($feedbackItem->created);
	}

}
