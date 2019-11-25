<?php
namespace Wpx\Scripts\v0;

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/Scripts.php';

// use Composer\Script\Event;

use Wpx\Scripts\v0\Scripts;

class ComposerScriptEventHandlers {

	public static function onPostInstall( /* Event */ $event ) {
		// echo 'onPostInstall' . "\n";
		// $args = $event->getArguments();
		// $eventName = $event->getName();
		// $composer = $event->getComposer();
		// echo $eventName;

		Scripts::copySkeletons();
		Scripts::deleteWpContent();
	}

	public static function onPostUpdate( /* Event */ $event ) {
		// echo 'onPostUpdate' . "\n";
		// $args = $event->getArguments();
		// $eventName = $event->getName();
		// $composer = $event->getComposer();
		// echo $eventName;

		Scripts::copySkeletons();
		Scripts::deleteWpContent();
	}

}
