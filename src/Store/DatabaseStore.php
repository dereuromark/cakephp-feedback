<?php

namespace Feedback\Store;

use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * Store to Database, either internal Feedback table "Feedback.FeedbackItems" or your custom one.
 */
class DatabaseStore implements StoreInterface {

	public const NAME = 'Database';

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

		if ($this->saveToDatabase($object, $options)) {
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
	 * @param array $options
	 * @return bool
	 */
	protected function saveToDatabase(array $object, array $options) {
		$feedbackItemsTable = TableRegistry::getTableLocator()->get($options['table'] ?? 'Feedback.FeedbackItems');

		$itemArray = [];
		$itemArray['created'] = new FrozenTime($object['time']);
		unset($object['time']);

		$columns = $feedbackItemsTable->getSchema()->columns();
		foreach ($columns as $column) {
			if (isset($object[$column])) {
				$itemArray[$column] = $object[$column];
				unset($object[$column]);
			}
		}
		$itemArray['data'] = $object;

		$item = $feedbackItemsTable->newEntity($itemArray);

		return (bool)$feedbackItemsTable->save($item);
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
