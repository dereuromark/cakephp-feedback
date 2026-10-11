<?php

namespace TestApp\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;

class AppController extends Controller {

	/**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     * @return mixed
	 */
	public function beforeFilter(EventInterface $event) {
		parent::beforeFilter($event);
		if (Configure::read('Test.redirectFeedback')) {
			return $this->redirect('/login');
		}
		if (Configure::read('Test.denyFeedback')) {
			throw new ForbiddenException('Feedback access denied by application.');
		}

		return null;
	}

}
