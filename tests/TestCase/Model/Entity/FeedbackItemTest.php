<?php
declare(strict_types = 1);

namespace Feedback\Test\TestCase\Model\Entity;

use Cake\Core\Configure;
use Cake\I18n\I18n;
use Cake\I18n\Package;
use Cake\TestSuite\TestCase;
use Feedback\Model\Entity\FeedbackItem;
use Feedback\Store\Priorities;

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

	/**
	 * @return void
	 */
	public function testDefaultAndCustomLabelsUseTheFeedbackTranslator(): void {
		$locale = I18n::getLocale();
		try {
			I18n::setLocale('fr_FR');
			I18n::setTranslator('feedback', static function (): Package {
				$package = new Package();
				$package->setMessages([
					'new' => 'nouveau',
					'high' => 'haute',
					'custom status' => 'statut personnalisé',
					'custom priority' => 'priorité personnalisée',
				]);

				return $package;
			});
			$this->assertSame('nouveau', FeedbackItem::statuses(FeedbackItem::STATUS_NEW));
			$custom = new class extends FeedbackItem {
				/**
				 * @var array<string>
				 */
				protected static $statuses = ['custom' => 'custom status'];
			};
			$this->assertSame('statut personnalisé', $custom::statuses('custom'));
			Configure::write('Feedback.priorities', ['high' => 'high', 'custom' => 'custom priority']);
			$this->assertSame([
				'' => ' - no priority - ',
				'high' => 'haute',
				'custom' => 'priorité personnalisée',
			], Priorities::getList());
		} finally {
			Configure::delete('Feedback.priorities');
			I18n::clear();
			I18n::setLocale($locale);
		}
	}

}
