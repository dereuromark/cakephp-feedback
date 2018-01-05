<?php

namespace Feedback\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;

class FeedbackController extends AppController {

	/**
	 * @var string
	 */
	public $modelClass = 'Feedback.Feedbackstore';

	/**
	 * @param \Cake\Event\Event $event
	 *
	 * @return bool|\Cake\Http\Response|null
	 */
	public function beforeFilter(Event $event) {
		if (Configure::read('Feedback')) {
			return null;
		}

		//Throw error, config file required
		throw new NotFoundException('No Feedback config found.');
	}

	/*
	Example index function for current save in tmp dir solution
	 */
	public function index() {
		$savepath = Configure::read('Feedback.configuration.Filesystem.location');

		//Check dir
		if (!is_dir($savepath)) {
		    mkdir($savepath, 0770, true);
		    if (!is_dir($savepath)) {
				throw new NotFoundException('Feedback location not found: ' . $savepath);
			}
		}

		//Creat feedback array in a cake-like way
		$feedbacks = [];

		//Loop through files
		foreach (glob($savepath . '*.feedback') as $feedbackfile) {

			$feedbackObject = unserialize(file_get_contents($feedbackfile));
			$feedbacks[$feedbackObject['time']] = $feedbackObject;

		}

		//Sort by time
		krsort($feedbacks);

		$this->set('feedbacks', $feedbacks);
	}

	/*
	Temp function to view captured image from index page
	 */
	public function viewimage($feedbackfile) {
		$savepath = Configure::read('Feedback.configuration.Filesystem.location');

		if (!file_exists($savepath . $feedbackfile)) {
			 throw new NotFoundException('Could not find that file');
		}

		$feedbackobject = unserialize(file_get_contents($savepath . $feedbackfile));

		if (!isset($feedbackobject['screenshot'])) {
			throw new NotFoundException('No screenshot found');
		}

		$this->set('screenshot', $feedbackobject['screenshot']);

		$this->viewBuilder()->layout('ajax');
	}

}
