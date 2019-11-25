<?php
namespace Wpx\Scripts\v0;

require_once __DIR__ . '/bootstrap.php';

if ( ! class_exists( __NAMESPACE__ . '\Scripts' ) ) {

	class Scripts {

		public static function deleteWpContent( $wp_content_dir = 'wp/wp-content' ) {
			// error_log( 'Wpx\Scripts\Scripts::deleteWpContent' );

			// $wp_content_dir = __DIR__ . '/../../../wp/wp-content';

			self::removeDir( $wp_content_dir );
		}

		public static function copySkeletons( $src_dir = 'skel', $dst_dir = './') {
			// error_log( 'Wpx\Scripts\Scripts::copySkeletons' );

			// $src_dir = __DIR__ . '/../../../skel';
			// $dst_dir = __DIR__ . '/../../..';

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
