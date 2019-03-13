<?php
namespace Feedback\Model\Table;

use Cake\Core\Configure;
use Cake\Http\Client;
use Cake\Http\Exception\NotImplementedException;
use Cake\Mailer\Email;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\Table;
use Cake\Routing\Router;
use SoapClient;

/**
 * @deprecated Make a Store class
 */
class FeedbackstoreTable extends Table {

	/**
	 * @var bool
	 */
	public $useTable = false;

	/**
	 * Mantis store function
	 *
	 * @deprecated Make a Store class
	 *
	 * @param array|null $feedbackObject
	 *
	 * @return array
	 *
	 * @throws \Cake\Http\Exception\NotImplementedException
	 */
	public function mantis($feedbackObject = null) {
		$returnobject = [];
		$returnobject['result'] = false;
		$returnobject['msg'] = '';

		if (empty($feedbackObject)) {
			return $returnobject;
		}

		//Mandatory
		$api_url	= Configure::read('Feedback.methods.mantis.api_url');
		$username	= Configure::read('Feedback.methods.mantis.username');
		$password	= Configure::read('Feedback.methods.mantis.password');
		$project_id	= Configure::read('Feedback.methods.mantis.project_id');
		$category	= Configure::read('Feedback.methods.mantis.category');
		$decodeimage = Configure::read('Feedback.methods.mantis.decodeimage');

		//Optional HTTP credentials for bypassing Basic Auth or Kerberos
		$soap_options = [];
		
		$http_username = Configure::read('Feedback.methods.mantis.http_username');
		$http_password = Configure::read('Feedback.methods.mantis.http_password');
		
		if ($http_username && $http_password) {
			$soap_options = [
				'login' => $http_username,
				'password' => $http_password,
				];
		}

		throw new NotImplementedException('TODO');

		/*
		//Uncomment to debug:
		$soap_options['cache_wsdl'] = WSDL_CACHE_NONE;

		//Create a SoapClient
		$c = new SoapClient($api_url, $soap_options);

		//Mantis specific: append browser, browser version and URL to feedback:
		$feedbackObject['feedback'] .= "\n\n";
		$feedbackObject['feedback'] .= sprintf("Browser: %s %s\n", $feedbackObject['browser'], $feedbackObject['browser_version']);
		$feedbackObject['feedback'] .= sprintf("Url: %s\n", $feedbackObject['url']);
		$feedbackObject['feedback'] .= sprintf("OS: %s\n", $feedbackObject['os']);
		$feedbackObject['feedback'] .= sprintf('By: %s', $feedbackObject['name']);

	    //Create new issue
		$issue = [
			'summary' => $feedbackObject['subject'],
			'description' => $feedbackObject['feedback'],
			'project' => ['id' => $project_id],
			'category' => $category
			];

	    //Try to save the issue
		if ($issueid = $c->mc_issue_add($username, $password, $issue)) {

	    	//Decode image or not?
			if ($decodeimage) {
				$feedbackObject['screenshot'] = base64_decode($feedbackObject['screenshot']);
			}

	    	//Add screenshot to issue (Do not send as base64 despite what de WSDL says)
			if ($c->mc_issue_attachment_add( $username, $password, $issueid, date('d-m-Y_H-i-s') . '.png', 'image/png', $feedbackObject['screenshot'] )) {

				$msg = __d('feedback', 'Thank you. Your feedback was saved.');

			if (Configure::read('Feedback.returnlink')) {
					$msg .= '<br/>';
					$msg .= __d('feedback', 'View your feedback on: ');

					list($url, $tmp) = explode('api', $api_url );
					$url .= 'view.php?id=' . $issueid;

					$msg .= '<a target="_blank" href="' . $url . '">' . $url . '</a>';
				}

				$returnobject['result'] = true;
				$returnobject['msg'] = $msg;

			}
		}
		*/

		return $returnobject;
	}

	/**
	 * Mail function
	 * - Function has possibility to mail submitting user instead of target adress
	 *
	 * @deprecated Make a Store class
	 *
	 * @param array $feedbackObject
	 * @param bool $copyreporter
	 * @return array
	 */
	public function mail(array $feedbackObject, $copyreporter = false) {
		$returnobject = [];
		$returnobject['result'] = false;
		$returnobject['msg'] = '';

		if (empty($feedbackObject)) {
			return $returnobject;
		}

		//Read settings from config if not in copy mode
		$to = Configure::read('Feedback.methods.mail.to');
		$from = Configure::read('Feedback.methods.mail.from');

		// Change recipient if sending a copy
		if ($copyreporter) {
			$to = $feedbackObject['email'];
		}

		//Change the sender if any given
		if (!empty($feedbackObject['email']) && !empty($feedbackObject['name'])) {
			$from = [$feedbackObject['email'] => $feedbackObject['name']];
		}

		//Tmp store the screenshot:
		$tmpfile = APP . 'tmp' . DS . time() . '_' . rand(1000, 9999) . '.png';
		if (!file_put_contents($tmpfile, base64_decode($feedbackObject['screenshot']))) {
			//Need to save tmp file
			throw new NotFoundException('Could not save tmp file for attachment in mail');
		}

		$email = new Email();
		$email->from($from);
		$email->to($to);
		$email->subject($feedbackObject['subject']);
		$email->emailFormat('html');
		$email->attachments([
		    'screenshot.png' => [
		        'file' => $tmpfile,
		        'mimetype' => 'image/png',
		        'contentId' => 'id-screenshot'
		    ]
		]);

		//Mail specific: append browser, browser version, URL, etc to feedback :
		if ($copyreporter) {
			$feedbackObject['feedback'] = '<p>' . __d('feedback', 'A copy of your submitted feedback:') . '</p>' . $feedbackObject['feedback'];
		}
		$feedbackObject['feedback'] .= '<p>';
		$feedbackObject['feedback'] .= sprintf('Browser: %s %s<br/>', $feedbackObject['browser'], $feedbackObject['browser_version']);
		$feedbackObject['feedback'] .= sprintf('Url: %s<br/>', $feedbackObject['url']);
		$feedbackObject['feedback'] .= sprintf('OS: %s<br/>', $feedbackObject['os']);
		$feedbackObject['feedback'] .= sprintf('By: %s<br/>', $feedbackObject['name']);
		$feedbackObject['feedback'] .= 'Screenshot: <br/>';
		$feedbackObject['feedback'] .= '</p>';
		$feedbackObject['feedback'] .= '<img src="cid:id-screenshot">'; //Add inline screenshot

		if ($email->send($feedbackObject['feedback'])) {
			$returnobject['result'] = true;
			$returnobject['msg'] = __d('feedback', 'Thank you. Your feedback was saved.');

			return $returnobject;
		}

		unlink($tmpfile);

		return $returnobject;
	}

	/**
	 * Github API v3
	 *
	 * @deprecated Make a Store class
	 *
	 * @param array $feedbackObject
	 * @return array
	 */
	public function github(array $feedbackObject) {
		$returnobject = [];
		$returnobject['result'] = false;
		$returnobject['msg'] = '';

		if (empty($feedbackObject)) {
			return $returnobject;
		}

		//Read settings
		$api_url			= Configure::read('Feedback.methods.github.api_url');
		$username			= Configure::read('Feedback.methods.github.username');
		$password			= Configure::read('Feedback.methods.github.password');
		$localimagestore = Configure::read('Feedback.methods.github.localimagestore');

		//Github specific: append browser, browser version and URL to feedback:
		$feedbackObject['feedback'] .= "\n\n";
		$feedbackObject['feedback'] .= sprintf("**Browser**: %s %s\n\n", $feedbackObject['browser'], $feedbackObject['browser_version']);
		$feedbackObject['feedback'] .= sprintf("**Url**: %s\n\n", $feedbackObject['url']);
		$feedbackObject['feedback'] .= sprintf("**OS**: %s\n\n", $feedbackObject['os']);
		$feedbackObject['feedback'] .= sprintf("**By**: %s\n\n", $feedbackObject['name']);

		// WARNING: This may not work for sites with different domains (or dev environments)
		//          If the given URL is not public, Github won't display the screenshot
		if ($localimagestore) {
			//Create filename based on timestamp and random number (to prevent collisions)
		if ($imagename = $this->saveScreenshot($feedbackObject)) {
				$viewimageUrl = Router::url("/img/screenshots/$imagename", true);

				$feedbackObject['feedback'] .= sprintf("**Screenshot**:\n![screenshot](%s)", $viewimageUrl);
			}
		}
		// Github still doesn't support this kind of image format in Markup Language
		// $content = '[screenshot]: data:image/png;base64,'. $feedbackObject['screenshot'] . " \n\n";

		//Prepare data
		$data = ['title' => $feedbackObject['subject'], 'body' => $feedbackObject['feedback']];
		$data_string = json_encode($data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

		$result = curl_exec($ch);
		$curlstatuscode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if (!$result) {
			//Return curl error
			$returnobject['msg'] = curl_error($ch);

		} elseif ($curlstatuscode >= 400) {
			//Return http error and message
			$message = json_decode($result);
			$returnobject['msg'] = trim($message->message); //Can contain linebreaks

		} else {
			//Set return value to true and return message
			$returnobject['result'] = true;
			$returnobject['msg'] = __d('feedback', 'Thank you. Your feedback was saved.');

			if (Configure::read('Feedback.returnlink')) {
				$returnobject['msg'] .= '<br/>';
				$returnobject['msg'] .= __d('feedback', 'View your feedback on: ');

				//Get response from github api
				$answer = json_decode($result);

				//Create new url:
				//Replace api prefix with GitHub public domain:
				$url = str_replace('/api.', '/', $api_url);
				$url = str_replace('/repos/', '/', $url);

				//Append issue number
				$url .= '/' . $answer->number;

				$returnobject['msg'] .= '<a target="_blank" href="' . $url . '">' . $url . '</a>';
			}

		}

		return $returnobject;
	}

	/**
	 * Bitbucket API
	 *
	 * @deprecated Make a Store class
	 *
	 * @param array $feedbackObject
	 * @return array
	 */
	public function bitbucket(array $feedbackObject) {
		$returnobject = [];
		$returnobject['result'] = false;
		$returnobject['msg'] = '';

		if (empty($feedbackObject)) {
			return $returnobject;
		}

		//Read settings
		$api_url			= Configure::read('Feedback.methods.bitbucket.api_url');
		$username = Configure::read('Feedback.methods.bitbucket.username');
		$password = Configure::read('Feedback.methods.bitbucket.password');
		$localimagestore = Configure::read('Feedback.methods.bitbucket.localimagestore');

		//Append browser, browser version and URL to feedback:
		$feedbackObject['feedback'] .= sprintf("**By**: %s\n\n", $feedbackObject['name']);
		$feedbackObject['feedback'] .= sprintf("**Browser**: %s %s\n\n", $feedbackObject['browser'], $feedbackObject['browser_version']);
		$feedbackObject['feedback'] .= sprintf("**OS**: %s\n\n", $feedbackObject['os']);
		$feedbackObject['feedback'] .= sprintf("**Url**: %s\n\n", $feedbackObject['url']);

		// WARNING: This may not work for sites with different domains (or dev environments)
		//          If the given URL is not public, Bitbucket won't display the screenshot
		if ($localimagestore) {
			//Create filename based on timestamp and random number (to prevent collisions)
		if ($imagename = $this->saveScreenshot($feedbackObject)) {
				$viewimageUrl = Router::url("/img/screenshots/$imagename", true);

				$feedbackObject['feedback'] .= sprintf("**Screenshot**:\n![screenshot](%s)", $viewimageUrl);
			}
		}
		// Bitbucket still doesn't support this kind of image format in Markup Language
		// $content = '[screenshot]: data:image/png;base64,'. $feedbackObject['screenshot'] . " \n\n";

		//Prepare data
		$data = ['title' => $feedbackObject['subject'], 'content' => $feedbackObject['feedback']];

		$HttpSocket = new Client(['ssl_verify_peer' => false, 'auth' => ['username' => $username, 'password' => $password]]);
		$result = $HttpSocket->post($api_url, $data);

		// TODO: A better error management
		if ($result->getStatusCode() !== 200 && $result->getStatusCode() !== 201) {
			$returnobject['msg'] = 'Error ' . $result->getStatusCode() . ':' . $result->getReasonPhrase();
		} else {
			$returnobject['result'] = true;
			$returnobject['msg'] = __d('feedback', 'Thank you. Your feedback was saved.');

		if (Configure::read('Feedback.returnlink')) {
				$returnobject['msg'] .= '<br/>';
				$returnobject['msg'] .= __d('feedback', 'View your feedback on: ');

				//Get response from github api
				$answer = json_decode($result->getBody());

				//Create new url:
				//Replace api prefix with bitbucket public domain:
				$url = str_replace('/api/1.0/repositories/', '/', $api_url);
				$url = str_replace('/issues', '/issue', $url);

				//Append issue number
				$url .= '/' . $answer->local_id;

				$returnobject['msg'] .= '<a target="_blank" href="' . $url . '">' . $url . '</a>';
			}
		}

		return $returnobject;
	}

	/**
	 * JIRA API v2
	 * https://developer.atlassian.com/display/JIRADEV/JIRA+REST+APIs
	 *
	 * @deprecated Make a Store class
	 *
	 * @param array $feedbackObject
	 * @return array
	 */
	public function jira(array $feedbackObject) {
		$returnobject = [];
		$returnobject['result'] = false;
		$returnobject['msg'] = '';

		if (empty($feedbackObject)) {
			return $returnobject;
		}

		//Read settings
		$api_url			= Configure::read('Feedback.methods.jira.api_url');
		$username			= Configure::read('Feedback.methods.jira.username');
		$password			= Configure::read('Feedback.methods.jira.password');
		$project_id			= Configure::read('Feedback.methods.jira.project_id');
		$issuetype			= Configure::read('Feedback.methods.jira.issuetype');
		$localimagestore = Configure::read('Feedback.methods.jira.localimagestore');

		//Mantis specific: append browser, browser version and URL to feedback:
		$feedbackObject['feedback'] .= "\n\n";
		$feedbackObject['feedback'] .= sprintf("**Browser**: %s %s\n\n", $feedbackObject['browser'], $feedbackObject['browser_version']);
		$feedbackObject['feedback'] .= sprintf("**Url**: %s\n\n", $feedbackObject['url']);
		$feedbackObject['feedback'] .= sprintf("**OS**: %s\n\n", $feedbackObject['os']);
		$feedbackObject['feedback'] .= sprintf("**By**: %s\n\n", $feedbackObject['name']);

		// WARNING: This may not work for sites with different domains (or dev environments)
		//          If the given URL is not public, Jira won't display the screenshot
		if ($localimagestore) {
			//Create filename based on timestamp and random number (to prevent collisions)
		if ($imagename = $this->saveScreenshot($feedbackObject)) {
				$viewimageUrl = Router::url("/img/screenshots/$imagename", true);

				$feedbackObject['feedback'] .= sprintf("**Screenshot**:\n![screenshot](%s)", $viewimageUrl);
			}
		}
		// Jira still doesn't support this kind of image format in Markup Language
		// $content = '[screenshot]: data:image/png;base64,'. $feedbackObject['screenshot'] . " \n\n";

		//Prepare data
		$data = [];
		$data['fields']['project']['id'] = $project_id;
		$data['fields']['issuetype']['name'] = $issuetype;
		$data['fields']['summary'] = $feedbackObject['subject'];
		$data['fields']['description'] = $feedbackObject['feedback'];
		$data_string = json_encode($data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

		$result = curl_exec($ch);
		$curlstatuscode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if (!$result) {
			//Return curl error
			$returnobject['msg'] = curl_error($ch);

		} elseif ($curlstatuscode >= 400) {
			//Return http error and message
			$returnobject['msg'] = 'Error in Jira API call'; //Can contain linebreaks

		} else {
			//Set return value to true and return message
			$returnobject['result'] = true;
			$returnobject['msg'] = __d('feedback', 'Thank you. Your feedback was saved.');

		if (Configure::read('Feedback.returnlink')) {
				$returnobject['msg'] .= '<br/>';
				$returnobject['msg'] .= __d('feedback', 'View your feedback on: ');

				//Get response from jira api
				$answer = json_decode($result);

				//Create new url:
				//Replace api prefix with GitHub public domain:
				$url = str_replace('/rest/api/2/issue/', '/browse/', $api_url);

				//Append issue number
				$url .= $answer->key;

				$returnobject['msg'] .= '<a target="_blank" href="' . $url . '">' . $url . '</a>';
			}

		}

		return $returnobject;
	}

	/**
	 * Redmine API
	 *
	 * @deprecated Make a Store class
	 *
	 * @param array $feedbackObject
	 * @return array
	 */
	public function redmine(array $feedbackObject) {
		$returnobject = [];
		$returnobject['result'] = false;
		$returnobject['msg'] = '';

		if (empty($feedbackObject)) {
			return $returnobject;
		}

		//Read settings
		$api_url			= Configure::read('Feedback.methods.redmine.api_url');
		$username			= Configure::read('Feedback.methods.redmine.username');
		$password			= Configure::read('Feedback.methods.redmine.password');
		$project_id			= Configure::read('Feedback.methods.redmine.project_id');
		$tracker_id			= Configure::read('Feedback.methods.redmine.tracker_id');

		//Redmine specific: append browser, browser version and URL to feedback:
		$feedbackObject['feedback'] .= "\n\n";
		$feedbackObject['feedback'] .= sprintf("**Browser**: %s %s\n\n", $feedbackObject['browser'], $feedbackObject['browser_version']);
		$feedbackObject['feedback'] .= sprintf("**Url**: %s\n\n", $feedbackObject['url']);
		$feedbackObject['feedback'] .= sprintf("**OS**: %s\n\n", $feedbackObject['os']);
		$feedbackObject['feedback'] .= sprintf("**By**: %s\n\n", $feedbackObject['name']);

		//Prepare data
		$data = [];
		$data['issue']['project_id'] = $project_id;
		$data['issue']['tracker_id'] = $tracker_id;
		$data['issue']['subject'] = $feedbackObject['subject'];
		$data['issue']['description'] = $feedbackObject['feedback'];
		$data_string = json_encode($data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

		$result = curl_exec($ch);
		$curlstatuscode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if (!$result) {
			//Return curl error
			$returnobject['msg'] = curl_error($ch);

		} elseif ($curlstatuscode >= 400) {
			//Return http error and message
			$returnobject['msg'] = "Error in Redmine API call ($curlstatuscode)"; //Can contain linebreaks

		} else {
			//TODO: Update with image

			//Set return value to true and return message
			$returnobject['result'] = true;
			$returnobject['msg'] = __d('feedback', 'Thank you. Your feedback was saved.');

		if (Configure::read('Feedback.returnlink')) {
				$returnobject['msg'] .= '<br/>';
				$returnobject['msg'] .= __d('feedback', 'View your feedback on: ');

				//Get response from jira api
				$answer = json_decode($result);

				//Create new url:
				//Replace api prefix with GitHub public domain:
				$url = str_replace('.json', '/', $api_url);

				//Append issue number
				$url .= $answer->issue->id;

				$returnobject['msg'] .= '<a target="_blank" href="' . $url . '">' . $url . '</a>';
			}

		}

		return $returnobject;
	}

	/**
   	 * Auxiliary function that save screenshot as image in webroot
	 *
	 * @deprecated Make part of a Store class
	 *
	 * @param array $feedbackObject
	 *
	 * @return string|null
	 */
	private function saveScreenshot(array $feedbackObject) {
		//Get save path from config
		$savepath = ROOT . DS . 'files' . DS . 'img' . DS . 'screenshots' . DS;

		//Serialize and save the object to a store in the Cake's tmp dir.
		if (!file_exists($savepath)) {
			if (!mkdir($savepath)) {
				//Throw error, directory is requird
				throw new NotFoundException('Could not create directory to save screenshots in. Please provide write rights to webserver user on directory: ' . $savepath);
			}
		}

		$screenshotname = $this->generateScreenshotName();

		if (file_put_contents($savepath . $screenshotname, base64_decode($feedbackObject['screenshot']))) {
			//Return the screenshotname
			return $screenshotname;
		}

		return null;
	}

	/**
	 * Auxiliary function that creates screenshot name
	 *
	 * @deprecated Make part of a Store class
	 *
	 * @return string
	 */
	private function generateScreenshotName() {
		return time() . '-' . rand(1000, 9999) . '.png';
	}

}
