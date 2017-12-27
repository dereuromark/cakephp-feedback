<?php

namespace Feedback\Store;

interface StoreInterface {

	public function save($object, array $options = []);

}
