<?php

namespace Feedback\Store;

use Cake\Core\Configure;
use InvalidArgumentException;

class StoreCollection {

	/**
	 * @var array<string, string>
	 */
	protected array $defaultStores = [
		FilesystemStore::class => FilesystemStore::class,
	];

	/**
	 * @var array<\Feedback\Store\StoreInterface>
	 */
	protected $stores;

	/**
	 * @param array $stores
	 */
	public function __construct(array $stores = []) {
		$defaultStores = (array)Configure::read('Feedback.stores') + $this->defaultStores;
		$stores += $defaultStores;

		foreach ($stores as $store) {
			if (!$store) {
				continue;
			}

			$this->add($store);
		}
	}

	/**
	 * Adds a task to the collection.
	 *
	 * @param \Feedback\Store\StoreInterface|string $store The store to run.
	 * @return $this
	 */
	public function add($store) {
		if (is_string($store)) {
			$store = new $store();
		}

		$class = get_class($store);
		if (!$store instanceof StoreInterface) {
			throw new InvalidArgumentException(
				"Cannot use '$class' as task, it is not implementing " . StoreInterface::class . '.',
			);
		}

		$this->stores[$class] = $store;

		return $this;
	}

	/**
	 * @return array<\Feedback\Store\StoreInterface>
	 */
	public function stores(): array {
		return $this->stores;
	}

	/**
	 * @param array $object
	 * @return array
	 */
	public function save(array $object): array {
		$results = [];

		$config = (array)Configure::read('Feedback');

		foreach ($this->stores as $store) {
			$options = (array)Configure::read('Feedback.configuration.' . $store::NAME) + $config;
			$results[] = $store->save($object, $options);
		}

		return $results;
	}

}
