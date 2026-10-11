<?php

namespace TestApp\Controller\Component;

use Cake\Controller\Component;

class AuthUserComponent extends Component {

	public function user(string $field): mixed {
		return $this->getConfig('user')[$field] ?? null;
	}

}
