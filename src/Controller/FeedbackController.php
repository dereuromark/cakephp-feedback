<?php

namespace Feedback\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;
use Cake\Network\Exception\NotImplementedException;

class FeedbackController extends AppController {

	public $modelClass = 'Feedback.Feedbackstore';

	/**
	 * @param \Cake\Event\Event $event
	 *
	 * @return bool|\Cake\Http\Response|null
	 */
	public function beforeFilter(Event $event) {
		//Check security component loaded and disable it for this plugin:
		if (isset($this->Security)) {
			$this->Security->csrfCheck = false;
			$this->Security->validatePost = false;
		}

		//Config file location (if you use it)
		$configfile = Plugin::path('Feedback') . 'config' . DS . 'config.php';

		//Check if a config file exists:
		if(file_exists($configfile) AND is_readable($configfile)){
			//Load config file into CakePHP config
			Configure::load('Feedback.config');
			return true;
		}

		//Throw error, config file required
		throw new NotFoundException( __d('feedback', 'No config file found. Please create one: ') . ' (' . $configfile . ')' );
	}

	/*
	Ajax function to save the feedback form. Lots of TODO's on this side.
	 */
	public function save() {
	    //$this->request->allowMethod('post');

		//Is ajax action
		$this->viewBuilder()->layout('ajax');

		//Save screenshot:
		$this->request->data['screenshot'] = str_replace('data:image/png;base64,', '', $this->request->data['screenshot']);

		//Add current time to data
		$this->request->data['time'] = time();

		//Check name
		if(empty($this->request->data['name'])){
			$this->request->data['name'] = 'Anonymous';
		}

		//Create feedbackObject
		$feedbackObject = $this->request->data;

		//Determine method of saving
		$methods = (array)Configure::read('Feedback.method');

		if (empty($methods)) {
			throw new NotFoundException(__d('feedback', 'No save method found in config file'));
		}

		//Multiple methods possible
		foreach ($methods as $index => $method) {

			//Check method exists in Model
			if(!(method_exists($this->Feedbackstore, $method)) AND $index != 0) { //Only throw error on first method
				throw new NotImplementedException( __d('feedback', 'Method not found in Feedbackstore model:') . ' ' . $method );
			}

			//If not first method, go to next. No user feedback for methods 2 -> n
			if($index != 0) {
				$this->Feedbackstore->$method($feedbackObject);
				continue;
			}

			//Parse result of first method only
			$result = $this->Feedbackstore->$method($feedbackObject);

			//Prepare result
			if(!$result['result']) {
				$this->response->statusCode(500);

				if(empty($result['msg'])){
					$result['msg'] = __d('feedback', 'Error saving feedback.');
				}
			}else{
				if(empty($result['msg'])){
					$result['msg'] = __d('feedback', 'Your feedback was saved succesfully.');
				}
			}

			$this->set('msg', $result['msg']);

		} //End method loop

		//Send a copy to the reciever:
		if(!empty($feedbackObject['copyme'])) {
			$this->Feedbackstore->mail($feedbackObject, true);
		}
	}

	/*
	Example index function for current save in tmp dir solution
	 */
	public function index(){
		$methods = Configure::read('Feedback.method');

		if(!in_array('filesystem', $methods)){
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
