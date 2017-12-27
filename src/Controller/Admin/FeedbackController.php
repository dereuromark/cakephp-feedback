<?php

namespace Feedback\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;

/**
 * @property \Feedback\Model\Table\FeedbackstoreTable $Feedbackstore
 */
class FeedbackController extends AppController {

	public $modelClass = 'Feedback.Feedbackstore';

	/**
	 * @param \Cake\Event\Event $event
	 *
	 * @return bool|\Cake\Http\Response|null
	 */
	public function beforeFilter(Event $event) {
		//Config file location (if you use it)
		$configfile = Plugin::path('Feedback') . 'config' . DS . 'config.php';

		//Check if a config file exists:
		if (file_exists($configfile) && is_readable($configfile)) {
			//Load config file into CakePHP config
			Configure::load('Feedback.config');
			return true;
		}

		//Throw error, config file required
		throw new NotFoundException( __d('feedback', 'No config file found. Please create one: ') . ' (' . $configfile . ')' );
	}

	/*
	Example index function for current save in tmp dir solution
	 */
	public function index() {
		$methods = Configure::read('Feedback.method');

		if (!in_array('filesystem', $methods)) {
			$this->Flash->error(__d('feedback', 'This function is only available with filesystem save method'));
			return $this->redirect($this->referer());
		}

		//Find all files in feedbackit dir
		$savepath = Configure::read('Feedback.methods.filesystem.location');

		//Check dir
		if (!is_dir($savepath)) {
		    mkdir($savepath, 0770, true);
		    if (!is_dir($savepath)) {
				throw new NotFoundException(__d('feedback', 'Feedback location not found: ') . $savepath);
			}
		}

		//Creat feedback array in a cake-like way
		$feedbacks = [];

		//Loop through files
		foreach (glob($savepath . '*.feedback') as $feedbackfile){

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
	public function viewimage($feedbackfile){
		$savepath = Configure::read('Feedback.methods.filesystem.location');

		if(!file_exists($savepath . $feedbackfile)){
			 throw new NotFoundException( __d('feedback', 'Could not find that file') );
		}

		$feedbackobject = unserialize(file_get_contents($savepath . $feedbackfile));

		if(!isset($feedbackobject['screenshot'])){
			throw new NotFoundException( __d('feedback', 'No screenshot found') );
		}

		$this->set('screenshot', $feedbackobject['screenshot']);

		$this->viewBuilder()->layout('ajax');
	}

}
