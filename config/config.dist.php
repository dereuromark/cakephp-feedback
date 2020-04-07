<?php

use Feedback\Store\FilesystemStore;

return [
	'Feedback' => [

		'stores' => [
		], // FQCN. Only the first method will be checked and reported back to user

		'returnlink' => true, // Return a link (if any) to the created ticket or report.
		'skipCss' => false, // For manually including stylesheet

		'enableacceptterms' => true, // If set to true, visitors have to check an accept terms checkbox
		'termstext' => true, // The text to display on the terms button, using __d makes translation possible

		'autoLink' => true, // Auto link the URL given

		//TODO: enable again using Email class or even store
		'enablecopybyemail' => false, // If set to true, visitors can check a 'send me a copy' button

		//TODO: enable again
		'forceauthusername' => false, //If set to true, the AuthComponent::user('name') var or equivalent is made un-editable in the form
		'forceemail' => false, //If set to true, the AuthComponent::user('email') var or equivalent is made un-editable in the form

		/*
		 * Configure the different methods
		 */
		'configuration' => [

			FilesystemStore::NAME => [
				'location' => ROOT . DS . 'tmp' . DS . 'feedback' . DS,
			],

			//TODO: enable again
			'mantis' => [
				'api_url' => 'http://example.com/api/soap/mantisconnect.php?wsdl', //Api url
				'username' => 'foo', //The Mantis user to use
				'password' => 'bar', //The password of the above Mantis user
				'project_id' => 1, //Set the project id of the project to put this feedback in
				'category' => 'General', //Default Mantis category
				'decodeimage' => true, //Decode image or not (Seems to differ in different Mantis versions?)

				'http_username' => false, //Optional username for HTTP auth, use false if not used
				'http_password' => false //Option password for HTTP auth, use false if not used
			],

			'mail' => [
				'to' => 'foo@example.com',
				'from' => ['noreply@' . env('HTTP_HOST') => 'FeedbackIt mailer'],
			],

			'github' => [
				'api_url' => 'https://api.github.com/repos/<username>/<repo>/issues', //Github Api url (http://developer.github.com/v3/issues/#create-an-issue)
				'username' => 'foo', //The Github user to use (must have pull access to repo)
				'password' => 'bar', //The password of the above Github user

				'localimagestore' => false, //WARNING: When set to true, screenshot image will be stored to the public webroot directory of this plugin
			],
			'bitbucket' => [
				'api_url' => 'https://bitbucket.org/api/1.0/repositories/<accountname>/<repo_slug>/issues', //Bitbucket Api url (https://confluence.atlassian.com/display/BITBUCKET/issues+Resource#issuesResource-POSTanewissue)
				'username' => 'foo', //The Bitbucket user to use (must have pull access to repo)
				'password' => 'bar', //The password of the above Bitbucket user

				'localimagestore' => true, //WARNING: When set to true, screenshot image will be stored to the public webroot directory of this plugin
			],
			'jira' => [
				'api_url' => 'http://localhost:2990/jira/rest/api/2/issue/', //https://developer.atlassian.com/display/JIRADEV/JIRA+REST+APIs
				'username' => 'foo', //The Jira user to use
				'password' => 'bar', //The password of the above Jira user
				'project_id' => 10000, //Set the project id of the project to put this feedback in
				'issuetype' => 'Bug', //Set the issue type that will be used, you can use the name

				'localimagestore' => true, //WARNING: When set to true, screenshot image will be stored to the public webroot directory of this plugin
			],
			'redmine' => [
				'api_url' => 'http://example.com/redmine/issues.json', //Use REST api. http://www.redmine.org/projects/redmine/wiki/Rest_Issues#Creating-an-issue
				'username' => 'foo', //The Redmine user to use
				'password' => 'bar', //The password of the above Redmine user
				'project_id' => 1, //Set the project id of the project to put this feedback in
				'tracker_id' => 1, //Set the tracker type that will be used, 1 = bug
			],
		],
	],
];
