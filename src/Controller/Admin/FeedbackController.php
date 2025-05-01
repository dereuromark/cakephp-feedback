<?php

namespace Feedback\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Feedback\Store\Filesystem;

/**
 * @property \Feedback\Model\Table\FeedbackstoreTable $Feedbackstore
 */
class FeedbackController extends AppController {

	/**
	 * @var string|null
	 */
	public ?string $defaultTable = 'Feedback.Feedbackstore';

	/**
	 * @return void
	 */
	public function initialize(): void {
		parent::initialize();

		if (!isset($this->Flash)) {
			$this->loadComponent('Flash');
		}
	}

	/**
	 * @param \Cake\Event\EventInterface $event
	 *
	 * @return void
	 */
	public function beforeFilter(EventInterface $event): void {
		if (Configure::read('Feedback')) {
			return;
		}

		//Throw error, config file required
		throw new NotFoundException('No Feedback config found.');
	}

	/**
	 * @return \Cake\Http\Response|null|void
	 */
	public function index() {
		$storeConfig = (array)Configure::read('Feedback.stores');

		$stores = [];
		foreach ($storeConfig as $store) {
			if (!$store) {
				continue;
			}

			$storeName = substr($store, strrpos($store, '\\') + 1);
			$stores[$store] = $storeName;
		}

		if (isset($stores['Feedback\Store\DatabaseStore'])) {
			$feedbackItemsTable = $this->getTableLocator()->get(Configure::read('Feedback.configuration.Database.table') ?? 'Feedback.FeedbackItems');
			/** @var class-string<\Feedback\Model\Entity\FeedbackItem> $entityClass */
			$entityClass = $feedbackItemsTable->getEntityClass();
			$feedbackItems = $feedbackItemsTable->find()
				->select(['id', 'status', 'created', 'name', 'subject', 'priority'])
				->where(['status' => $entityClass::STATUS_NEW])->all()->toArray();
			$this->set(compact('feedbackItems'));
		}

		$this->set(compact('stores'));
	}

	/**
	 * Example index function for current save in tmp dir solution
	 *
	 * @return \Cake\Http\Response|null|void
	 */
	public function listing() {
		$savepath = Configure::read('Feedback.configuration.Filesystem.location');

		$feedbacks = Filesystem::read($savepath);

		$this->set('feedbacks', $feedbacks);
	}

	/**
	 * Temp function to view captured image from index page
	 *
	 * @param string $file
	 *
	 * @return \Cake\Http\Response|null|void
	 */
	public function viewimage($file) {
		$savepath = Configure::read('Feedback.configuration.Filesystem.location');
		$feedback = Filesystem::get($savepath . $file);

		if (!isset($feedback['screenshot'])) {
			throw new NotFoundException('No screenshot found');
		}

		$this->set('screenshot', $feedback['screenshot']);

		$this->viewBuilder()->setLayout('ajax');
	}

	/**
	 * @param string|null $file
	 *
	 * @return \Cake\Http\Response|null
	 */
	public function remove($file = null) {
		$this->request->allowMethod('post');

		$savepath = Configure::read('Feedback.configuration.Filesystem.location');

		if (!$file || !file_exists($savepath . $file)) {
			throw new NotFoundException('Could not find that file: ' . $savepath . $file);
		}

		unlink($savepath . $file);

		$this->Flash->success('Removed');

		return $this->redirect($this->referer(['action' => 'index']));
	}

}
