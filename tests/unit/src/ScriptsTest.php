<?php

declare( strict_types = 1 );

require_once __DIR__ . '/../setup.php';

require_once __DIR__ . '/../../../src/Scripts.php';

use PHPUnit\Framework\TestCase;
use Wpx\Scripts\v0\Scripts;

final class ScriptsTest extends TestCase {

	protected function setUp(): void {
		// Delete any old destination directory.
		// Add a new empty destination directory.
		rrmdir( __DIR__ . '/../assets/dest' );
		rrmdir( __DIR__ . '/../assets/wp-content' );
	}

	protected function mkDestDir(): void {
		mkdir( __DIR__ . '/../assets/dest' );
	}

	protected function mkWpContentDir(): void {
		mkdir( __DIR__ . '/../assets/wp-content' );
		mkdir( __DIR__ . '/../assets/wp-content/mu-plugins' );
		mkdir( __DIR__ . '/../assets/wp-content/plugins' );
		mkdir( __DIR__ . '/../assets/wp-content/themes' );
	}

	public function testCopySkeletons(): void {
		$this->mkDestDir();

		Scripts::copySkeletons( __DIR__ . '/../assets/skeleton', __DIR__ . '/../assets/dest' );
		$this->assertEquals( true, is_dir( __DIR__ . '/../assets/dest/wordpress' ) );
		$this->assertEquals( true, file_exists( __DIR__ . '/../assets/dest/wordpress/.htaccess' ) );
	}

	public function testDeleteWpContent(): void {
		$this->mkWpContentDir();

		$this->assertEquals( true, is_dir( __DIR__ . '/../assets/wp-content' ) );

		Scripts::deleteWpContent( __DIR__ . '/../assets/wp-content' );

		$this->assertEquals( false, file_exists( __DIR__ . '/../assets/wp-content' ) );
	}

}
