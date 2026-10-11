<?php

namespace TestApp\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;

class AppController extends Controller {

	public function beforeFilter(EventInterface $event): void {
		parent::beforeFilter($event);
		if (Configure::read('Test.denyFeedback')) {
			throw new ForbiddenException('Feedback access denied by application.');
		}
	}

}
