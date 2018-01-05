# Cakephp Feedback Plugin

This plugin provides a static feedback tab on the side of the screen that enables website visitor to submit feedback or bugreports.
Features pure client-side screenshot function including user-placed highlight / accent.

Note: For CakePHP 3.x

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

* Filesystem
* [Mantis Bugtracker](http://www.mantisbt.org/)
* [GitHub (repo issues)](https://help.github.com/articles/github-glossary#issue)
* Email
* [Bitbucket (repo issues)](https://confluence.atlassian.com/display/BITBUCKET/Use+the+issue+tracker)
* [Jira](https://www.atlassian.com/software/jira)
* [Redmine](http://www.redmine.org)

## Requirements

This plugin is CakePHP Security component compatible.

**Required:**

* [jQuery](http://jquery.com/)

**Optional:**

* [Bootstrap](http://getbootstrap.com) (Bootstrap 2 and 3 compatible)

**Includes:**

* [html2canvas.js by niklasvh](https://github.com/niklasvh/html2canvas)


## Installation and Usage

See [Docs](docs).

### Examples

![Example of form](https://raw.github.com/dereuromark/cakephp-feedback/master/docs/examples/feedbackit_1.png "Example of form")
![Example of result](https://raw.github.com/dereuromark/cakephp-feedback/master/docs/examples/feedbackit_2.png "Example of result")


## 2.x version

This is the original project:

https://github.com/stefanvangastel/CakePHP-FeedbackIt

Website: [http://stefanvangastel.nl/feedbackitdemo/](http://stefanvangastel.nl/feedbackitdemo/)
