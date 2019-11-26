<?php
namespace Wpx\Scripts\v0;

require_once __DIR__ . '/bootstrap.php';

if ( ! class_exists( __NAMESPACE__ . '\Scripts' ) ) {

	class Scripts {

		/**
		* Deletes the entire `wp-content` directory.
		*
		* @param {string} $wp_content_dir Optional. The path to the `wp-content`
		* 	directory to be deleted. Defaults to `wp/wp-content`, a directory relative
		* 	to the current working directory.
		*/
		public static function deleteWpContent( $wp_content_dir = 'wp/wp-content' ) {
			// error_log( 'Wpx\Scripts\Scripts::deleteWpContent' );

			self::removeDir( $wp_content_dir );
		}

		/**
		* Copies the entire contents of the "skeleton" (aka source) directory to the
		* destination directory.
		*
		* @param {string} $src_dir Optional. The path to the "skeleton" (aka source)
		* 	directory containing only the content to be copied. Defaults to `skel`, a
		* 	directory relative to the current working directory.
		* @param {string} $dst_dir Optional. The path to the destination directory.
		* 	Defaults to the current working directory.
		*/
		public static function copySkeletons( $src_dir = 'skel', $dst_dir = './') {
			// error_log( 'Wpx\Scripts\Scripts::copySkeletons' );

			self::copyDir( $src_dir, $dst_dir );
		}

		protected static function copyDir( $src_dir, $dst_dir ) {
			$dir = @opendir( $src_dir );

			if ( $dir !== false ) {
				@mkdir( $dst_dir );

				while( $file = readdir( $dir ) ) {
					if ( $file === false ) {
						break;
					}
					if ( ( $file !== '.' ) && ( $file !== '..' ) ) {
						$src_file = $src_dir . '/' . $file;
						$dst_file = $dst_dir . '/' . $file;
						if ( is_dir( $src_file ) ) {
							self::copyDir( $src_file, $dst_file );
						}
						else {
							copy( $src_file, $dst_file );
						}
					}
				}
			}

			closedir( $dir );
		}

		protected static function removeDir( $src_dir ) {
			$dir = @opendir( $src_dir );

			if ( $dir !== false ) {
				while ( $file = readdir( $dir ) ) {
					if ( $file === false ) {
						break;
					}
					if ( ( $file !== '.' ) && ( $file !== '..' ) ) {
						$src_file = $src_dir . '/' . $file;
						if ( is_dir( $src_file ) ) {
							self::removeDir( $src_file );
						}
						else {
							unlink( $src_file );
						}
					}
				}

				closedir( $dir );

				rmdir( $src_dir );
			}
		}

	} // eo class Scripts

} // eo if ( class_exists )
