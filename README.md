# CakePHP Feedback Plugin

[![Build Status](https://travis-ci.org/dereuromark/cakephp-feedback.svg?branch=master)](https://travis-ci.org/dereuromark/cakephp-feedback)
[![Latest Stable Version](https://poser.pugx.org/dereuromark/cakephp-feedback/v/stable.svg)](https://packagist.org/packages/dereuromark/cakephp-feedback)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%205.6-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/dereuromark/cakephp-feedback/license.svg)](https://packagist.org/packages/dereuromark/cakephp-feedback)
[![Total Downloads](https://poser.pugx.org/dereuromark/cakephp-feedback/d/total.svg)](https://packagist.org/packages/dereuromark/cakephp-feedback)

This plugin provides a static feedback tab on the side of the screen that enables website visitor to submit feedback or bugreports.
Features pure client-side screenshot function including user-placed highlight / accent.

Note: This branch is for CakePHP 3.5+

### Currently saves the following on form submit

* Name of sender (optional, can work with AuthComponent)
* E-mail of sender (optional)
* Subject
* Message
* Current URL
* Screenshot of body DOM element
* Browser and browser version
* User OS flavor

### Save options (configurable and extendable)

Out of the box:

* Filesystem

Easily extendable to:

* Database
* Email
* [Mantis Bugtracker](http://www.mantisbt.org/)
* [GitHub (repo issues)](https://help.github.com/articles/github-glossary#issue)
* [Bitbucket (repo issues)](https://confluence.atlassian.com/display/BITBUCKET/Use+the+issue+tracker)
* [Jira](https://www.atlassian.com/software/jira)
* [Redmine](http://www.redmine.org)

They can also be stacked (multiple stores at once).

### Furthermore
* This plugin is CakePHP Security and Csrf component compatible.
* Fully localizable to your language.

## Requirements

**Required:** [jQuery](http://jquery.com/)

**Optional:** [Bootstrap](http://getbootstrap.com) (Bootstrap 2 and 3 compatible)

**Includes:** [html2canvas.js by niklasvh](https://github.com/niklasvh/html2canvas)

## Installation and Usage

See **[Documentation](docs)**.

## Demo
https://sandbox.dereuromark.de/

## 2.x version

This is the original project:

https://github.com/stefanvangastel/CakePHP-FeedbackIt

Website: [http://stefanvangastel.nl/feedbackitdemo/](http://stefanvangastel.nl/feedbackitdemo/)
