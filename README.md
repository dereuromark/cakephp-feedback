# CakePHP Feedback Plugin

[![CI](https://github.com/dereuromark/cakephp-feedback/workflows/CI/badge.svg?branch=master)](https://github.com/dereuromark/cakephp-feedback/actions?query=workflow%3ACI+branch%3Amaster)
[![Coverage](https://img.shields.io/codecov/c/github/dereuromark/cakephp-feedback/master.svg)](https://codecov.io/gh/dereuromark/cakephp-feedback)
[![Latest Stable Version](https://poser.pugx.org/dereuromark/cakephp-feedback/v/stable.svg)](https://packagist.org/packages/dereuromark/cakephp-feedback)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.3-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/dereuromark/cakephp-feedback/license.svg)](https://packagist.org/packages/dereuromark/cakephp-feedback)
[![Total Downloads](https://poser.pugx.org/dereuromark/cakephp-feedback/d/total.svg)](https://packagist.org/packages/dereuromark/cakephp-feedback)

This plugin provides a static feedback tab on the side of the screen that enables website visitor to submit feedback or bugreports.
Features pure client-side screenshot function including user-placed highlight / accent.

This branch is for **CakePHP 4.2+**. For details see [version map](https://github.com/dereuromark/cakephp-feedback/wiki#cakephp-version-map).

### Currently saves the following on form submit

* Name of sender (optional)
* E-mail of sender (optional)
* Subject
* Feedback message
* Current URL
* Screenshot of body DOM element (with marker as option)
* Browser and browser version
* User OS flavor

Name and E-Mail can be auto-retrieved from TinyAuth.AuthUser or plain session Auth.

### Save options (configurable and extendable)

Out of the box:

* Filesystem
* Database

Easily extendable to:

* Custom Database
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

**Optional:** [Bootstrap](http://getbootstrap.com) (Bootstrap 3/4 compatible)

**Includes:** [html2canvas.js by niklasvh](https://github.com/niklasvh/html2canvas)

## Installation and Usage

See **[Documentation](docs/README.md)**.

## Demo
https://sandbox.dereuromark.de/

## CakePHP 2.x version

This is the original project:

https://github.com/stefanvangastel/CakePHP-FeedbackIt

Website: [http://stefanvangastel.nl/feedbackitdemo/](http://stefanvangastel.nl/feedbackitdemo/)
