Website: [http://stefanvangastel.nl/feedbackitdemo/](http://stefanvangastel.nl/feedbackitdemo/)

##### Table of Contents  
* [Intro](#intro)  
* [Requirements](#requirements)  
* [Installation and setup](#installation)  
* [Usage / Demo](#usage) 
* [Examples](#examples)  

<a name="intro"/>
## Intro

This CakePHP plugin provides a static feedback tab on the side of the screen that enables website visitor to submit feedback or bugreports.
Features pure client-side screenshot function including user-placed highlight / accent.

Note: For CakePHP 3.x

**Currently saves the following on form submit:**

* Name of sender (optional, can work with AuthComponent)
* E-mail of sender (optional)
* Subject
* Message
* Current URL
* Screenshot of body DOM element
* Browser and browser version
* User OS flavor

<a name="saveoptions"/>
**Save options include (configurable):**

* Filesystem
* [Mantis Bugtracker](http://www.mantisbt.org/)
* [GitHub (repo issues)](https://help.github.com/articles/github-glossary#issue)
* Email
* [Bitbucket (repo issues)](https://confluence.atlassian.com/display/BITBUCKET/Use+the+issue+tracker)
* [Jira](https://www.atlassian.com/software/jira)
* [Redmine](http://www.redmine.org)

<a name="requirements"/>
## Requirements

This plugin is CakePHP Security component compatible.

**Required:**

* [jQuery](http://jquery.com/)

**Optional:**

* [Bootstrap](http://getbootstrap.com) (Bootstrap 2 and 3 compatible)

**Includes:**

* [html2canvas.js by niklasvh](https://github.com/niklasvh/html2canvas)

<a name="installation"/>
## Installation and Setup

1. Include the Feedback CakePHP plugin with composer in your application:
	
	composer require dereuromark/feedback": "dev-master"`

2. Load the plugin in config/bootstrap.php:

	`Plugin::load('Feedback');`

3. Copy the default feedback config file into your applications config folder:

	Copy `../vendor/dereuromark/cakephpfeedback/config/config.php` to `../config/app_feedbackit.php`

	And adjust it to your needs. You can also just include the config array into your existing app.php file.

4. Use the feedbackbar element in a view or layout to place the feedback tab on that (or those) pages. It doesn't matter where you place the following line since it uses absolute DOM element positioning.

	`<?php echo $this->element('Feedback.feedbackbar');?>`

<a name="usage"/>
## Usage

To testdrive this puppy; [http://stefanvangastel.nl/feedbackitdemo/](http://stefanvangastel.nl/feedbackitdemo) 

<a name="examples"/>
## Examples

![Example of form](https://raw.github.com/stefanvangastel/CakePHP-FeedbackIt/master/examples/feedbackit_1.png "Example of form")
![Example of result](https://raw.github.com/stefanvangastel/CakePHP-FeedbackIt/master/examples/feedbackit_2.png "Example of result")


### 2.x version

https://github.com/stefanvangastel/CakePHP-FeedbackIt
