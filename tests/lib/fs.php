<?php

/**
 * Recursively remove the contents of a directory including the directory itself.
 *
 * @param {string} $dir The directory to remove.
 */
function rrmdir( $dir ) {
	$removed_dir = false;
	$dir_res = @opendir( $dir );

	if ( $dir_res !== false ) {
		while ( $file = readdir( $dir_res ) ) {
			if ( $file === false ) {
				break;
			}
			if ( ( $file !== '.' ) && ( $file !== '..' ) ) {
				$tbd_file = $dir . '/' . $file;
				if ( is_dir( $tbd_file ) ) {
					rrmdir( $tbd_file );
				}
				else {
					$removed = unlink( $tbd_file );
					if ( ! $removed ) {
						error_log( "Failed to remove '{$tbd_file}'." );
						break;
					}
				}
			}
		}

		closedir( $dir_res );

		$removed_dir = rmdir( $dir );
		if ( ! $removed_dir ) {
			error_log( "Failed to remove '{$dir}'." );
		}
	}
	return $removed_dir;
}
