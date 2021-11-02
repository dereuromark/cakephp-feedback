<?php

namespace Feedback\Store;

use Cake\Core\Configure;

class Priorities {

	/**
	 * @var array<string>
	 */
	protected static $defaults = [
		'low' => 'low',
		'medium' => 'medium',
		'high' => 'high',
	];

	/**
	 * @return array|null
	 */
	public static function getList(): ?array {
		$priorities = Configure::read('Feedback.priorities');
		if ($priorities === true) {
			$priorities = static::defaults();
		}

		if (!$priorities) {
			return null;
		}

		$array = [
			'' => __d('feedback', ' - no priority - '),
 		];
		foreach ($priorities as $priority => $label) {
			$array[$priority] = __d('feedback', $label);
		}

		return $array;
	}

	/**
	 * @return array
	 */
	protected static function defaults(): array {
		return static::$defaults;
	}

}
