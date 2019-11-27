<?php
namespace Wpx\Scripts\v0;

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/Scripts.php';

use Composer\Script\Event;

use Wpx\Scripts\v0\Scripts;

class ComposerCallbacks {

	/**
	* A Composer event callback intended to be used with the `post-install-cmd`
	* event.
	*
	* After install, copies the contents of the "skeleton" directory to the
	* destination directory. Also, deletes the `wp-content` directory from the
	* WordPress installation directory.
	*
	* The "skeleton" directory, its destination directory, and the WordPress
	* installation directory are configurable using the `extra` key in
	* `composer.json`.
	*
	* The following `composer.json` snippet demonstrates how to provide
	* configuration.
	*
	*     {
	*     	"extra": {
	*     		"wordpress-install-dir": "wp",
	*     		"wpx-skeleton-dir": "skel",
	*     		"wpx-skeleton-destination-dir": null,
	*     	},
	*     	"scripts": {
	*     		"post-install-cmd": [
	*     			"Wpx\\Scripts\\v0\\ComposerCallbacks::onPostInstall",
	*     		],
	*     	},
	*     }
	*
	* @param {Event} The Composer script event.
	*/
	public static function onPostInstall( Event $event ) {
		// echo 'onPostInstall' . "\n";

		self::copySkeletons( $event );
		self::deleteWpContent( $event );
	}

	/**
	* A Composer event callback intended to be used with the `post-update-cmd`
	* event.
	*
	* After update, copies the contents of the "skeleton" directory to the
	* destination directory. Also, deletes the `wp-content` directory from the
	* WordPress installation directory.
	*
	* The "skeleton" directory, its destination directory, and the WordPress
	* installation directory are configurable using the `extra` key in
	* `composer.json`.
	*
	* The following `composer.json` snippet demonstrates how to provide
	* configuration.
	*
	*     {
	*     	"extra": {
	*     		"wordpress-install-dir": "wp",
	*     		"wpx-skeleton-dir": "skel",
	*     		"wpx-skeleton-destination-dir": null,
	*     	},
	*     	"scripts": {
	*     		"post-update-cmd": [
	*     			"Wpx\\Scripts\\v0\\ComposerCallbacks::onPostUpdate",
	*     		],
	*     	},
	*     }
	*
	* @param {Event} The Composer script event.
	*/
	public static function onPostUpdate( Event $event ) {
		// echo 'onPostUpdate' . "\n";

		self::copySkeletons( $event );
		self::deleteWpContent( $event );
	}

	protected static function copySkeletons( Event $event ) {
		$composer = $event->getComposer();
		$package = $composer->getPackage();
		$extra = $package->getExtra();

		$skel_dir = null;
		if ( ! empty ( $extra['wpx-skeleton-dir'] ) ) {
			$skel_dir = $extra['wpx-skeleton-dir'];
		}

		$dst_dir = null;
		if ( ! empty ( $extra['wpx-skeleton-destination-dir'] ) ) {
			$dst_dir = $extra['wpx-skeleton-destination-dir'];
		}

		$result = Scripts::copySkeletons( $skel_dir, $dst_dir );
		if ( isset( $result['errors'] ) && is_array( $result['errors'] ) ) {
			$errors = $result['errors'];

			foreach ( $errors as $error ) {
				echo $error['message'] . "\n";
			}
		}
	}

	protected static function deleteWpContent( Event $event ) {
		$composer = $event->getComposer();
		$package = $composer->getPackage();

		$wp_install_dir = self::extractWpInstallDir( $package );
		$wp_content_dir = $wp_install_dir . '/wp-content';

		$result = Scripts::deleteWpContent( $wp_content_dir );
		if ( isset( $result['errors'] ) && is_array( $result['errors'] ) ) {
			$errors = $result['errors'];

			foreach ( $errors as $error ) {
				echo $error['message'] . "\n";
			}
		}
	}

	protected static function extractWpInstallDir( $package ) {
		$extra = $package->getExtra();

		$wp_install_dir = null;
		if ( ! empty ( $extra['wordpress-install-dir'] ) ) {
			$wp_install_dir_cfg = $extra['wordpress-install-dir'];
			$wp_install_dir = $wp_install_dir_cfg;
			if ( is_array( $wp_install_dir_cfg ) ) {
				if ( ! empty ( $wp_install_dir_cfg['johnpbloch/wordpress-core'] ) ) {
					$wp_install_dir = $wp_install_dir_cfg['johnpbloch/wordpress-core'];
				}
			}
		}
		if ( is_null( $wp_install_dir ) ) {
			$wp_install_dir = 'wordpress';
		}

		return $wp_install_dir;
	}

}
