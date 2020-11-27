<?php
declare(strict_types = 1);

namespace Feedback\Model\Entity;

use Cake\Core\Configure;
use Cake\ORM\Entity;
use Feedback\Store\Priorities;

/**
 * FeedbackItem Entity
 *
 * @property int $id
 * @property string $sid
 * @property string $url
 * @property string|null $name
 * @property string|null $email
 * @property string|null $subject
 * @property string|null $feedback
 * @property string|int|null $priority
 * @property array $data
 * @property int $status
 * @property \Cake\I18n\FrozenTime $created
 * @property string|null $url_short
 */
class FeedbackItem extends Entity {

	/**
	 * @var string[]
	 */
	protected static $statuses = [
		self::STATUS_NEW => 'new',
		self::STATUS_PROGRESS => 'in progress',
		self::STATUS_ARCHIVED => 'archived',
	];

	public const STATUS_NEW = 0;
	public const STATUS_PROGRESS = 1;
	public const STATUS_ARCHIVED = 2;

	/**
	 * Fields that can be mass assigned using newEntity() or patchEntity().
	 *
	 * Note that when '*' is set to true, this allows all unspecified fields to
	 * be mass assigned. For security purposes, it is advised to set '*' to false
	 * (or remove it), and explicitly make individual fields accessible as needed.
	 *
	 * @var array
	 */
	protected $_accessible = [
		'*' => true,
		'id' => false,
	];

	/**
	 * @return string|null
	 */
	protected function _getUrlShort(): ?string {
		$url = $this->url;
		if (!$url) {
			return $url;
		}

		$base = (string)Configure::read('App.fullBaseUrl');

		return str_replace($base, '', $url);
	}

	/**
	 * @param string|null $value
	 *
	 * @return string|string[]|null
	 */
	public static function priorities($value = null) {
		$priorities = Priorities::getList();
		if ($value === null) {
			return $priorities;
		}

		if (!isset($priorities[$value])) {
			return null;
		}

		return $priorities[$value];
	}

	/**
	 * @param string|null $value
	 *
	 * @return string|string[]|null
	 */
	public static function statuses($value = null) {
		$statuses = static::$statuses;
		foreach ($statuses as $key => $name) {
			$statuses[$key] = __d('feedback', $name);
		}

		if ($value === null) {
			return $statuses;
		}

		if (!isset($statuses[$value])) {
			return null;
		}

		return $statuses[$value];
	}

}
