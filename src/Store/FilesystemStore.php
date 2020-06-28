<?php

namespace Feedback\Store;

use Cake\Http\Exception\NotFoundException;
use Cake\Routing\Router;

class FilesystemStore implements StoreInterface {

	public const NAME = 'Filesystem';

	/**
	 * @param array $object
	 * @param array $options
	 *
	 * @return array
	 */
	public function save(array $object, array $options = []): array {
		$returnobject = [];
		$returnobject['result'] = false;
		$returnobject['msg'] = '';

		if (empty($object)) {
			return $returnobject;
		}

		//Create filename based on timestamp and random number (to prevent collisions)
		$object['filename'] = $this->generateFilename($object);

		if ($this->saveFile($object, $options['location'])) {
			$msg = __d('feedback', 'Thank you. Your feedback was saved.');

			if (!empty($options['returnlink'])) {
				$msg .= ' ';
				$msg .= __d('feedback', 'View your feedback on: ');

				$url = Router::url(['plugin' => 'Feedback', 'controller' => 'Feedback', 'action' => 'index'], true);

				$msg .= '<a target="_blank" href="' . $url . '">' . $url . '</a>';
			}

			$returnobject['result'] = true;
			$returnobject['msg'] = $msg;
		}

		return $returnobject;
	}

	/**
	 * Auxiliary function that saves the file
	 *
	 * @param array $object
	 * @param string $location
	 * @return bool
	 */
	protected function saveFile(array $object, $location) {
		//Serialize and save the object to a store in the Cake's tmp dir.
		if (!file_exists($location)) {
			if (!mkdir($location, 0770, true)) {
				//Throw error, directory is requird
				throw new NotFoundException('Could not create directory to save feedbacks in. Please provide write rights to webserver user on directory: ' . $location);
			}
		}

		if (file_put_contents($location . $object['filename'], serialize($object))) {
			//Add filename to data
			return true;
		}

		return false;
	}

	/**
	 * Auxiliary function that creates filename
	 *
	 * @param array $object
	 *
	 * @return string
	 */
	protected function generateFilename(array $object) {
		return $object['time'] . '-' . $object['sid'] . '.feedback';
	}

}
