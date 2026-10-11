<?php

namespace Feedback\Store;

use Cake\Core\Configure;
use Cake\I18n\I18n;

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
			$array[$priority] = match ($label) {
				'low' => __d('feedback', 'low'),
				'medium' => __d('feedback', 'medium'),
				'high' => __d('feedback', 'high'),
				default => I18n::getTranslator('feedback')->translate($label),
			};
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
