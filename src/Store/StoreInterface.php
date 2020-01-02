<?php

namespace Feedback\Store;

interface StoreInterface {

	/**
	 * @param array $object
	 * @param array $options
	 *
	 * @return array
	 */
	public function save(array $object, array $options = []): array;

}
