<?php
declare(strict_types = 1);

namespace Feedback\Test\TestCase\Model\Entity;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Feedback\Model\Entity\FeedbackItem;

class FeedbackItemTest extends TestCase {

	/**
	 * Test validationDefault method
	 *
	 * @return void
	 */
	public function testUrlShort(): void {
		$feedbackItem = new FeedbackItem();
		$feedbackItem->url = 'http://localhost/foo/bar';

		$urlShort = $feedbackItem->url_short;
		$this->assertSame('/foo/bar', $urlShort);
	}

}
