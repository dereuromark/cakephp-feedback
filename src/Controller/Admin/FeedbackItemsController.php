<?php
declare(strict_types = 1);

namespace Feedback\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Feedback\Store\DatabaseStore;
use Feedback\Store\Filesystem;
use RuntimeException;

/**
 * @property \Feedback\Model\Table\FeedbackItemsTable $FeedbackItems
 * @method \Feedback\Model\Entity\FeedbackItem[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class FeedbackItemsController extends AppController {

	/**
	 * @return void
	 */
	public function initialize(): void {
		parent::initialize();

		$this->paginate['order'] = [
			'created' => 'DESC',
		];

		if (!isset($this->Flash)) {
			$this->loadComponent('Flash');
		}
	}

	/**
	 * @param \Cake\Event\EventInterface $event
	 *
	 * @return bool|\Cake\Http\Response|null
	 */
	public function beforeFilter(EventInterface $event) {
		if (Configure::read('Feedback')) {
			return null;
		}

		//Throw error, config file required
		throw new NotFoundException('No Feedback config found.');
	}

	/**
	 * @return \Cake\Http\Response|null|void Renders view
	 */
	public function index() {
		$feedbackItems = $this->paginate($this->FeedbackItems);

		$this->set(compact('feedbackItems'));
	}

	/**
	 * View method
	 *
	 * @param string|null $id Feedback Item id.
	 * @return \Cake\Http\Response|null|void Renders view
	 */
	public function view($id = null) {
		$feedbackItem = $this->FeedbackItems->get($id, [
			'contain' => [],
		]);

		$this->set(compact('feedbackItem'));
	}

	/**
	 * Temp function to view captured image from index page
	 *
	 * @param string|int|null $id
	 *
	 * @return \Cake\Http\Response|null|void
	 */
	public function viewimage($id) {
		$feedbackItem = $this->FeedbackItems->get($id);

		if (!isset($feedbackItem->data['screenshot'])) {
			throw new NotFoundException('No screenshot found');
		}

		$this->set('screenshot', $feedbackItem->data['screenshot']);

		$this->viewBuilder()->setLayout('ajax');
	}

	/**
	 * @param string|null $id Feedback Item id.
	 * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
	 */
	public function edit($id = null) {
		$feedbackItem = $this->FeedbackItems->get($id, [
			'contain' => [],
		]);
		if ($this->request->is(['patch', 'post', 'put'])) {
			$feedbackItem = $this->FeedbackItems->patchEntity($feedbackItem, $this->request->getData());
			if ($this->FeedbackItems->save($feedbackItem)) {
				$this->Flash->success(__('The feedback item has been saved.'));

				return $this->redirect(['action' => 'index']);
			}
			$this->Flash->error(__('The feedback item could not be saved. Please, try again.'));
		}
		$this->set(compact('feedbackItem'));
	}

	/**
	 * @param string|null $id Feedback Item id.
	 * @return \Cake\Http\Response|null|void Redirects to index.
	 */
	public function delete($id = null) {
		$this->request->allowMethod(['post', 'delete']);
		$feedbackItem = $this->FeedbackItems->get($id);
		if ($this->FeedbackItems->delete($feedbackItem)) {
			$this->Flash->success(__('The feedback item has been deleted.'));
		} else {
			$this->Flash->error(__('The feedback item could not be deleted. Please, try again.'));
		}

		return $this->redirect(['action' => 'index']);
	}

	/**
	 * @return \Cake\Http\Response|null|void
	 */
	public function importFiles() {
		$this->request->allowMethod(['post']);

		$savepath = Configure::read('Feedback.configuration.Filesystem.location');
		if (!$savepath || !is_dir($savepath)) {
			$this->Flash->error('No path configured or no such directory found.');

			return $this->redirect($this->referer(['action' => 'index']));
		}

		$store = new DatabaseStore();

		$files = glob($savepath . '*.feedback') ?: [];
		foreach ($files as $file) {
			$data = Filesystem::get($file);

			$result = $store->save($data);
			if (!$result['result']) {
				throw new RuntimeException('Import failed for ' . $file);
			}

			unlink($file);
		}

		$this->Flash->success(count($files) . ' files imported.');

		return $this->redirect($this->referer(['action' => 'index']));
	}

}
