<?php

namespace Pritect\ExampleLoader\v2_5_0;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Base {

	private static $instance;

	// For testing to verify which version is being used
	private $where_from = __FILE__;

	private $api = null;
	public function __construct() {

	}

	private static function autoloader( $class ) {
		// project-specific namespace prefix
		$prefix = __NAMESPACE__;

		// does the class use the namespace prefix?
		$len = strlen( $prefix );
		if ( 0 !== strncmp( $prefix, $class, $len ) ) {
			// no, move to the next registered autoloader
			return;
		}

		// get the relative class name
		$relative_class = substr( $class, $len );

		// replace the namespace prefix with the base directory, replace namespace
		// separators with directory separators in the relative class name, append
		// with .php
		$file = __DIR__ . str_replace( '\\', '/', $relative_class ) . '.php';

		if ( file_exists( $file ) ) {
			require_once $file;
		}

	}
	public static function instance( $params = null ) {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {
			self::$instance = new self;

			spl_autoload_register( array( __CLASS__, 'autoloader' ) );

			self::$instance->api = new API;
			Admin\Test::some_function();
		}

		return self::$instance;
	}

}