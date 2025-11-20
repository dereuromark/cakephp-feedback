<?php

namespace Feedback\Store;

use Cake\Http\Exception\NotFoundException;

/**
 * Store to filesystem. This is a simple default for single server setups on a real server.
 */
class Filesystem {

	/**
	 * Validate filename to prevent path traversal attacks
	 *
	 * @param string $filename
	 * @return bool
	 */
	public static function isValidFilename(string $filename): bool {
		return str_ends_with($filename, '.feedback') &&
			!str_contains($filename, '/') &&
			!str_contains($filename, '\\') &&
			!str_contains($filename, '..') &&
			!str_contains($filename, "\0");
	}

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

		// Unserialize with allowed_classes => false for security (allows arrays/stdClass but no custom objects)
		// This maintains BC with existing serialized array files while preventing RCE via object injection
		$feedback = unserialize($content, ['allowed_classes' => false]);

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
			// Unserialize with allowed_classes => false for security
			$feedbackObject = unserialize($content, ['allowed_classes' => false]);
			$result[$feedbackObject['time']] = $feedbackObject;
		}

		//Sort by time
		krsort($result);

		return $result;
	}

}
