<?php

namespace Feedback\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;
use Feedback\Store\StoreCollection;

class FeedbackController extends AppController {

	/**
	 * @var string
	 */
	public $modelClass = 'Feedback.Feedbackstore';

	/**
	 * @param \Cake\Event\Event $event
	 *
	 * @return \Cake\Http\Response|null
	 */
	public function beforeFilter(Event $event) {
		// Check security component loaded and disable it for this plugin:
		if (isset($this->Security)) {
			$this->Security->csrfCheck = false;
			$this->Security->validatePost = false;
		}

		if (Configure::read('Feedback')) {
			return null;
		}

		// Throw error, config file required
		throw new NotFoundException('No Feedback config found.');
	}

	/**
	 * Ajax function to save the feedback form.
	 *
	 * @return \Cake\Http\Response|null
	 */
	public function save() {
	    $this->request->allowMethod('post');

		$data = $this->request->data();

		//Is ajax action
		$this->viewBuilder()->layout('ajax');

		//Save screenshot:
		$data['screenshot'] = str_replace('data:image/png;base64,', '', $this->request->data('screenshot'));

		//Add current time to data
		$data['time'] = time();

		//Check name
		if (empty($data['name'])) {
			$data['name'] = __d('feedback', 'Anonymous');
		}

		$data['sid'] = $this->request->session()->id();

		//Determine method of saving
		$collection = new StoreCollection();
		$result = $collection->save($data);

		if (empty($result)) {
			throw new NotFoundException('No stores defined.');
		}

		// Only first result is important
		$result = array_shift($result);

		//Prepare result
		if (!$result['result']) {
			$this->response->statusCode(500);

			if (empty($result['msg'])) {
				$result['msg'] = __d('feedback', 'Error saving feedback.');
			}
		} else {
			if (empty($result['msg'])) {
				$result['msg'] = __d('feedback', 'Your feedback was saved successfully.');
			}
		}

		$this->set('msg', $result['msg']);

		//Send a copy to the reciever:
		if (!empty($data['copyme'])) {
			//FIXME: Move to a store class
			$this->Feedbackstore->mail($data, true);
		}
	}

	/**
	 * Example index function for current save in tmp dir solution.
	 * Must only display images of own session
	 *
	 * @return \Cake\Http\Response|null
	 */
	public function index() {
		$savepath = Configure::read('Feedback.configuration.Filesystem.location');

		//Check dir
		if (!is_dir($savepath)) {
			throw new NotFoundException('savepath not exists');
		}

		//Creat feedback array in a cake-like way
		$feedbacks = [];

		//Loop through files
		foreach (glob($savepath . '*-' . $this->request->session()->id() . '.feedback') as $feedbackfile) {
			$feedbackObject = unserialize(file_get_contents($feedbackfile));
			$feedbacks[$feedbackObject['time']] = $feedbackObject;
		}

		//Sort by time
		krsort($feedbacks);

		$this->set('feedbacks', $feedbacks);
	}

	/**
	 * Temp function to view captured image from index page
	 *
	 * @param string $file
	 * @return \Cake\Http\Response|null
	 */
	public function viewimage($file) {
		$savepath = Configure::read('Feedback.configuration.Filesystem.location');

		if (!file_exists($savepath . $file)) {
			 throw new NotFoundException('Could not find that file');
		}

		$feedbackobject = unserialize(file_get_contents($savepath . $file));

		if (!isset($feedbackobject['screenshot'])) {
			throw new NotFoundException('No screenshot found');
		}

		$this->set('screenshot', $feedbackobject['screenshot']);

		$this->viewBuilder()->layout('ajax');
	}

}
