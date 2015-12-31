#Contribute To Ninja Forms
(adapted from Easy Digital Downloads)

## Getting Started

* Submit a ticket for your issue, assuming one does not already exist.
  * Raise it on our [Issue Tracker](https://github.com/wpninjas/ninja-forms/issues)
  * Clearly describe the issue, including steps to reproduce the bug (if applicable).
  * If it's a bug, make sure you fill in the earliest version that you know has the issue as well as the version of WordPress you're using.

## Making Changes

* Fork the Ninja Forms repository on GitHub
* From the `master` branch on your forked repository, create a new branch and make your changes
  * Your new branch should use the naming convention `issue/{issue#}` e.g. `issue/190`
  * Ensure you stick to the [WordPress Coding Standards](http://codex.wordpress.org/WordPress_Coding_Standards)
* When committing, use a [well-formed](http://robots.thoughtbot.com/5-useful-tips-for-a-better-commit-message) [commit](http://tbaggery.com/2008/04/19/a-note-about-git-commit-messages.html) [message](http://who-t.blogspot.com/2009/12/on-commit-messages.html)
* Push the changes to your fork and submit a pull request to the `master` branch of the Ninja Forms repository

## Code Documentation

* We're working on making sure that every function is documented well and follows the WordPress inline documentation standards based on phpDoc
* The WordPress Inline Documentation Standards (with examples) can be found [here](http://make.wordpress.org/core/handbook/inline-documentation-standards/php-documentation-standards/)
* Please make sure that every function is documented so that our API Documentation will be complete
    * If you're adding/editing a function in a class, make sure to add `@access {private|public|protected}`
* Finally, please use tabs and not spaces. The tab indent size should be 4 for all Ninja Forms code.

At this point you're waiting on us to merge your pull request. We'll review all pull requests, and make suggestions and changes if necessary.

# Additional Resources
* [Ninja Forms Developer API](http://ninjaforms.com/documentation/developer-api/)
* [GitHub Help — Forking](https://help.github.com/articles/fork-a-repo)
* [GitHub Help — Syncing a Fork](https://help.github.com/articles/syncing-a-fork)
* [GitHub Help — Pull Requests](https://help.github.com/articles/using-pull-requests#before-you-begin)