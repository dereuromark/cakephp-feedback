<?php

namespace Feedback\Store;

use Cake\Http\Exception\NotFoundException;

/**
 * Store to filesystem. This is a simple default for single server setups on a real server.
 */
class Filesystem {

	/**
	 * Open a specific file.
	 *
	 * @param string $path
	 *
	 * @return array
	 */
	public static function get(string $path): array {
		if (!file_exists($path)) {
			throw new NotFoundException('Could not find that file');
		}

		$content = file_get_contents($path);
		if ($content === false) {
			throw new NotFoundException('Cannot read file: ' . $path);
		}

		$feedback = unserialize($content);

		return $feedback;
	}

	/**
	 * Get all files.
	 *
	 * @param string $path
	 * @param string|null $sid
	 *
	 * @return array
	 */
	public static function read(string $path, ?string $sid = null): array {
		if (!is_dir($path)) {
			mkdir($path, 0770, true);
			if (!is_dir($path)) {
				throw new NotFoundException('Feedback location not found and cannot be accessed: ' . $path);
			}
		}

		$pattern = $path . '*.feedback';
		if ($sid) {
			$pattern = $path . '*-' . $sid . '.feedback';
		}

		$feedbackFiles = glob($pattern) ?: [];
		$result = [];
		foreach ($feedbackFiles as $feedbackFile) {
			$content = file_get_contents($feedbackFile);
			if ($content === false) {
				continue;
			}
			$feedbackObject = unserialize($content);
			$result[$feedbackObject['time']] = $feedbackObject;
		}

		//Sort by time
		krsort($result);

		return $result;
	}

}
