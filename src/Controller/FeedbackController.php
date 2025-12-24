<?php

namespace Feedback\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\NotFoundException;
use Feedback\Store\Filesystem;
use Feedback\Store\StoreCollection;

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
		// Check FormProtection component loaded and disable it for this plugin:
		if (isset($this->FormProtection)) {
			$this->FormProtection->setConfig('validatePost', false);
			$this->FormProtection->setConfig('unlockedActions', ['save']);
		}

		if (Configure::read('Feedback')) {
			return;
		}

		// Throw error, config file required
		throw new NotFoundException('No Feedback config found.');
	}

	/**
	 * Ajax function to save the feedback form.
	 *
	 * @return \Cake\Http\Response|null|void
	 */
	public function save() {
		$this->request->allowMethod(['put', 'post', 'ajax']);

		$map = (array)Configure::read('Feedback.authMap') + [
			'username' => 'username',
			'email' => 'email',
		];
		$userField = $map['username'];
		$emailField = $map['email'];

		$sessionKey = Configure::read('Feedback.sessionKey') ?? 'Auth.User';

		if ($this->components()->has('AuthUser')) {
			$name = $this->AuthUser->user($userField) ?: $this->AuthUser->user('account') ?: '';
			$email = $this->AuthUser->user($emailField) ?: $this->AuthUser->user('email') ?: '';
		} else {
			$name = $this->request->getSession()->read($userField) ?: $this->request->getSession()->read($sessionKey . '.username') ?: '';
			$email = $this->request->getSession()->read($emailField) ?: $this->request->getSession()->read($sessionKey . '.email') ?: '';
		}

		$data = (array)$this->request->getData() + [
			'name' => $name,
			'email' => $email,
		];

		//Is ajax action
		$this->viewBuilder()->setLayout('ajax');

		//Save screenshot:
		if ($this->request->getData('screenshot')) {
			$screenshot = (string)$this->request->getData('screenshot');

			// Only validate if it looks like a data URI (starts with 'data:')
			if (str_starts_with($screenshot, 'data:')) {
				// Validate base64 format
				if (!preg_match('/^data:image\/png;base64,[A-Za-z0-9+\/=]+$/', $screenshot)) {
					throw new BadRequestException('Invalid screenshot format');
				}

				$base64Data = str_replace('data:image/png;base64,', '', $screenshot);

				// Validate size (max 3MB encoded data)
				if (strlen($base64Data) > 3_000_000) {
					throw new BadRequestException('Screenshot too large');
				}

				$data['screenshot'] = $base64Data;
			} else {
				// Allow simple values for testing/backwards compatibility
				$data['screenshot'] = $screenshot;
			}
		}

		//Add current time to data
		$data['time'] = time();

		if (!$this->request->getSession()->started()) {
			$this->request->getSession()->start();
		}
		$data['sid'] = $this->request->getSession()->id();

		$map = (array)Configure::read('Feedback.authMap') + [
			'username' => 'username',
			'email' => 'email',
		];
		if ($this->components()->has('AuthUser')) {
			$username = $this->AuthUser->user($userField) ?: $this->AuthUser->user('account') ?: '';
			if (empty($data['name']) || Configure::read('Feedback.forceauthusername')) {
				$data['name'] = $username;
			}
			$email = $this->AuthUser->user($emailField) ?: $this->AuthUser->user('email') ?: '';
			if (empty($data['email']) || Configure::read('Feedback.forceemail')) {
				$data['email'] = $email;
			}
		} else {
			$username = $this->request->getSession()->read($map['username']);
			if (empty($data['name']) || Configure::read('Feedback.forceauthusername')) {
				$data['name'] = $username;
			}
			$email = $this->request->getSession()->read($map['email']);
			if (empty($data['email']) || Configure::read('Feedback.forceemail')) {
				$data['email'] = $email;
			}
		}

		if (empty($data['name'])) {
			$data['name'] = __d('feedback', 'Anonymous');
		}

		$copyMe = $data['copyme'] ?? false;
		unset($data['copyme']);

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
			$this->response = $this->response->withStatus(500);

			if (empty($result['msg'])) {
				$result['msg'] = __d('feedback', 'Error saving feedback.');
			}
		} else {
			if (empty($result['msg'])) {
				$result['msg'] = __d('feedback', 'Your feedback was saved successfully.');
			}
		}

		$this->set('msg', $result['msg']);

		//Send a copy to the receiver:
		if ($copyMe) {
			//FIXME: Move to a store class
			$this->Feedbackstore->mail($data, true);
		}
	}

	/**
	 * Example index function for current save in tmp dir solution.
	 * Must only display images of own session
	 *
	 * @return \Cake\Http\Response|null|void
	 */
	public function index() {
		$savepath = Configure::read('Feedback.configuration.Filesystem.location');

		if (!$this->request->getSession()->started()) {
			$this->request->getSession()->start();
		}
		$feedbacks = Filesystem::read($savepath, $this->request->getSession()->id());

		$this->set('feedbacks', $feedbacks);
	}

	/**
	 * Temp function to view captured image from index page
	 *
	 * @param string $file
	 * @return \Cake\Http\Response|null|void
	 */
	public function viewimage($file) {
		if (!Filesystem::isValidFilename($file)) {
			throw new NotFoundException('Invalid file format');
		}

		$savepath = Configure::read('Feedback.configuration.Filesystem.location');
		$realPath = realpath($savepath . $file);
		$basePath = realpath($savepath);

		// Ensure the file is within the allowed directory
		if (!$realPath || !$basePath || strpos($realPath, $basePath) !== 0) {
			throw new NotFoundException('Invalid file path');
		}

		$feedback = Filesystem::get($realPath);

		if (!isset($feedback['screenshot'])) {
			throw new NotFoundException('No screenshot found');
		}

		$this->set('screenshot', $feedback['screenshot']);

		$this->viewBuilder()->setLayout('ajax');
	}

}
