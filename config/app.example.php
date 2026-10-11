<?php

/**
 * Feedback Example Configuration
 *
 * Merge the keys below into your application's config/app.php (or
 * config/app_local.php) and do not replace the whole file, since this snippet
 * only contains this plugin's configuration. When copying entries that
 * reference imported classes, use fully-qualified class names or move the
 * `use` imports to the top of the target file. Customize the values as needed.
 */
use Feedback\Store\FilesystemStore;

return [
	'Feedback' => [

		'stores' => [], // FQCN. Only the first method will be checked and reported back to user

		// Session key prefix used to read the logged-in user's name/email when no AuthUser component is present.
		// FeedbackController::save() reads "<sessionKey>.username" and "<sessionKey>.email".
		'sessionKey' => 'Auth.User', // Defaults to 'Auth.User' when not set

		'returnlink' => true, // Return a link (if any) to the created ticket or report.
		'skipCss' => false, // For manually including stylesheet

		'enableacceptterms' => true, // If set to true, visitors have to check an accept terms checkbox
		'termstext' => true, // The text to display on the terms button, using __d makes translation possible

		'autoLink' => true, // Auto link the URL given

		//TODO: enable again using Email class or even store
		'enablecopybyemail' => false, // If set to true, visitors can check a 'send me a copy' button

		'authMap' => [
			'username' => 'Auth.User.username',
			'email' => 'Auth.User.email',
		],
		'forceauthusername' => false, //If set to true, the Auth user('name') var or equivalent is made un-editable in the form
		'forceemail' => false, //If set to true, the Auth user('email') var or equivalent is made un-editable in the form

		'priorities' => false,
		'displayExisting' => true, // Set to false to hide username/email when they are filled by session etc

		/*
		 * Configure the different methods
		 */
		'configuration' => [

			// DatabaseStore: which table the admin backend reads pending items from.
			// Read by Admin\FeedbackController::index(); defaults to 'Feedback.FeedbackItems' when not set.
			'Database' => [
				'table' => 'Feedback.FeedbackItems',
			],

			FilesystemStore::NAME => [
				'location' => ROOT . DS . 'tmp' . DS . 'feedback' . DS,
			],
		],
	],
];
