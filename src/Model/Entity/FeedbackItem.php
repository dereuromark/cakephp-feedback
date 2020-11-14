<?php
declare(strict_types = 1);

namespace Feedback\Model\Entity;

use Cake\Core\Configure;
use Cake\ORM\Entity;

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
 * @property array $data
 * @property int $status
 * @property \Cake\I18n\FrozenTime $created
 * @property string|null $url_short
 */
class FeedbackItem extends Entity {

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

}
