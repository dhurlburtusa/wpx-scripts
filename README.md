# WPX Scripts

A set of scripts to help make using/managing a WordPress instance easier.


## Warning

WPX Scripts is in alpha version. Breaking changes are possible and likely.

More thorough documentation will begin in the alpha version when the shape of this project is closer to being finalized. Please stay tuned as more improvements are made. Make a PR if you want to contribute.


## Installation

This package is installed like any other Composer package.

```sh
composer require --dev wpx/scripts
```


## Usage

**Composer Callbacks**

Composer fires various named events during its execution process. Callbacks to these events can be set up in the root `composer.json` file. This is done by using specific script names under the `"scripts"` key.

See https://getcomposer.org/doc/articles/scripts.md for details.

The following snippet demonstrates how to add a `post-install-cmd` and a `post-update-cmd` callback using the single-string method and the array method. Because `composer.json` is in the JSON format, backslashes must be escaped.

```
{
	"scripts": {
		"post-install-cmd": "Wpx\\Scripts\\v0\\ComposerCallbacks::onPostInstall",
		"post-update-cmd": [
			"Wpx\\Scripts\\v0\\ComposerCallbacks::onPostUpdate"
		]
  }
}
```
