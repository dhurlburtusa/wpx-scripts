<?php
namespace Wpx\Scripts\v0;

require_once __DIR__ . '/bootstrap.php';

if ( ! class_exists( __NAMESPACE__ . '\Scripts' ) ) {

	class Scripts {

		const SKELETON_DIR_DEFAULT = 'skel';
		const WP_CONTENT_DIR_DEFAULT = 'wp/wp-content';

		/**
		* Deletes the entire `wp-content` directory.
		*
		* **Why?**
		*
		* When using Composer and the `johnpbloch/wordpress` package, it is common to
		* store the real `wp-content` directory separate from the rest of WordPress
		* core. This is done because updating with Composer will erase all changes done
		* to the `wp-content` directory that comes with WordPress core. To avoid
		* confusion with having a separate `wp-content` directory outside of core and
		* having one in core, it is recommended to remove the `wp-content` from core
		* after core has been updated.
		*
		* @param {string} $wp_content_dir Optional. The path to the `wp-content`
		* 	directory to be deleted. Defaults to `wp/wp-content`, a directory relative
		* 	to the current working directory. Set to `null` as an indicator to use the
		* 	default value.
		*
		* @return {array} The result of the operation.
		* @return {bool} $return['success']
		* @return {array|null} $return['errors']
		* @return {string} $return['errors'][i]['message']
		*/
		public static function deleteWpContent( $wp_content_dir = null ) {
			// error_log( 'Wpx\Scripts\Scripts::deleteWpContent' );

			$wp_content_dir = is_null( $wp_content_dir ) ? self::WP_CONTENT_DIR_DEFAULT : $wp_content_dir;
			$result = self::removeDir( $wp_content_dir );
			if ( isset( $result['errors'] ) && is_array( $result['errors'] ) ) {
				$errors = $result['errors'];

				foreach ( $errors as $idx => $error ) {
					$error_code = $error['code'];

					if ( $error_code === 'no_del' ) {
						$file = $error['file'];
						$error['message'] = "Deleting '{$file}' failed.";
					}
					// elseif ( $error_code === 'dir_404' ) {
					// 	$dir = $error['dir'];
					// 	$error['message'] = "wp-content directory: '{$dir}' not found.";
					// }

					$errors[$idx] = $error;
				}

				$result['errors'] = $errors;
			}
			return $result;
		}

		/**
		* Copies the entire contents of the "skeleton" (aka source) directory to the
		* destination directory.
		*
		* This works very much like the Linux user skeleton directory (i.e, `etc/skel`).
		* Whenever a Composer package is updated, the package's contents are erased and
		* replaced with the updated package content. When using the `johnpbloch/wordpress`
		* package, it is not uncommon to have extra files added to the root of the
		* WordPress installation, `.htaccess`, for example.
		*
		* See https://www.google.com/search?q=linux+skel+directory for more details.
		*
		* @param {string|null} $src_dir Optional. The path to the "skeleton" (aka source)
		* 	directory containing only the content to be copied. Defaults to `skel`, a
		* 	directory relative to the current working directory. Set to `null` as an
		* 	indicator to use the default value.
		* @param {string} $dst_dir Optional. The path to the destination directory.
		* 	Defaults to the current working directory. Set to `null` as an
		* 	indicator to use the default value.
		*
		* @return {array} The result of the operation.
		* @return {bool} $return['success']
		* @return {array|null} $return['errors']
		* @return {string} $return['errors'][i]['message']
		*/
		public static function copySkeletons( $src_dir = null, $dst_dir = null) {
			// error_log( 'Wpx\Scripts\Scripts::copySkeletons' );

			$src_dir = is_null( $src_dir ) ? self::SKELETON_DIR_DEFAULT : $src_dir;
			$dst_dir = is_null( $dst_dir ) ? './' : $dst_dir;
			$result = self::copyDir( $src_dir, $dst_dir );
			if ( isset( $result['errors'] ) && is_array( $result['errors'] ) ) {
				$errors = $result['errors'];

				foreach ( $errors as $idx => $error ) {
					$error_code = $error['code'];

					if ( $error_code === 'no_copy' ) {
						$src_file = $error['src_file'];
						$dst_file = $error['dst_file'];
						$error['message'] = "Copy from '{$src_file}' to '{$dst_file}' failed.";
					}
					elseif ( $error_code === 'src_404' ) {
						$error['message'] = "Skeleton directory '{$src_dir}' not found.";
					}

					$errors[$idx] = $error;
				}

				$result['errors'] = $errors;
			}
			return $result;
		}

		protected static function copyDir( $src_dir, $dst_dir ) {
			$result = [ 'success' => false ];
			$src_dir_res = @opendir( $src_dir );

			if ( $src_dir_res !== false ) {
				@mkdir( $dst_dir );

				while ( $file = readdir( $src_dir_res ) ) {
					if ( $file === false ) {
						break;
					}
					if ( ( $file !== '.' ) && ( $file !== '..' ) ) {
						$src_file = $src_dir . '/' . $file;
						$dst_file = $dst_dir . '/' . $file;
						if ( is_dir( $src_file ) ) {
							$inner_result = self::copyDir( $src_file, $dst_file );
							if ( isset( $inner_result['errors'] ) ) {
								$errors = $inner_result['errors'];
								if ( is_array( $errors ) && count( $errors ) > 0 ) {
									$result['errors'] = $errors;
									break;
								}
							}
						}
						else {
							$copied = copy( $src_file, $dst_file );
							if ( ! $copied ) {
								$result['errors'] = [
									[
										'code' => 'no_copy',
										'src_file' => $src_file,
										'dst_file' => $dst_file,
									],
								];
								break;
							}
						}
					}
				}
				closedir( $src_dir_res );
				$result['success'] = true;
			}
			else {
				$result['errors'] = [
					[ 'code' => 'src_404' ],
				];
			}
			return $result;
		}

		protected static function removeDir( $dir ) {
			$result = [ 'success' => false ];
			$dir_res = @opendir( $dir );

			if ( $dir_res !== false ) {
				while ( $file = readdir( $dir_res ) ) {
					if ( $file === false ) {
						break;
					}
					if ( ( $file !== '.' ) && ( $file !== '..' ) ) {
						$tbd_file = $dir . '/' . $file;
						if ( is_dir( $tbd_file ) ) {
							$inner_result = self::removeDir( $tbd_file );
							if ( isset( $inner_result['errors'] ) ) {
								$errors = $inner_result['errors'];
								if ( is_array( $errors ) && count( $errors ) > 0 ) {
									$result['errors'] = $errors;
									break;
								}
							}
						}
						else {
							$removed = unlink( $tbd_file );
							if ( ! $removed ) {
								$result['errors'] = [
									[
										'code' => 'no_del',
										'file' => $tbd_file,
									],
								];
								break;
							}
						}
					}
				}

				closedir( $dir_res );

				$removed = rmdir( $dir );
				if ( ! $removed ) {
					$result['errors'] = [
						[
							'code' => 'no_del',
							'file' => $dir,
						],
					];
				}

				$result['success'] = true;
			}
			// No need to error if the directory to delete does not exist.
			// else {
			// 	$result['errors'] = [
			// 		[
			// 			'code' => 'dir_404',
			// 			'dir' => $dir,
			// 		],
			// 	];
			// }
			return $result;
		}

	} // eo class Scripts

} // eo if ( class_exists )
