<?php

namespace Feedback\Store;

interface StoreInterface {

	/**
	 * @param array $object
	 * @param array $options
	 *
	 * @return array
	 */
	public function save($object, array $options = []);

}
