<?php

namespace TestApp\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;
use TestApp\Controller\Component\AuthUserComponent;

class AppController extends Controller {

	public function initialize(): void {
		parent::initialize();
		if (Configure::check('Test.authUserData')) {
			$this->loadComponent('AuthUser', [
				'className' => AuthUserComponent::class,
				'user' => Configure::read('Test.authUserData'),
			]);
		}
	}

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
