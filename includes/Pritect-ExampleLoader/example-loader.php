<?php
/*
Plugin Name: Pritect Example Loader
Version: 2.5.0
Author Name: James Golovich
License: GPL2
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Pritect_ExampleLoader_Bootstrap_v2_5_0' ) ) {
	class Pritect_ExampleLoader_Bootstrap_v2_5_0 {
		// Keep this class simple, let the real class do all the heavy lifting
		public static function go( $params, $version ) {
			if ( preg_match( '/(.*)_(.*)_Bootstrap_(.*)/', __CLASS__, $match ) ) {
				$real_class = '\\' . $match[1] . '\\' . $match[2] . '\\' . $match[3] . '\\Base';
				require_once 'includes/Pritect_ExampleLoader.php';
				return $real_class::instance( $params );
			}
			return null;
		}
	}
}

if ( ! function_exists( pritect_example_loader ) ) {
	/**
	 * Get this function right the first time, because it should never have to change
	 * If you do make a change to this function you cannot guarantee that your version will
	 * be used if there are other versions installed as libraries or as a plugin
	 *
	 * @param mixed $params
	 * @param mixed $version
	 *
	 * @return null
	 */
	function pritect_example_loader( $params = null, $version = null ) {
		$class_matches = array();
		foreach ( get_declared_classes() as $class ) {
			if ( preg_match( '/Pritect_ExampleLoader_Bootstrap_v([\w_]+)/', $class, $match ) ) {
				// $match[0]: Complete string
				// $match[1]: Version
				$class_matches[ $match[1] ] = $match[0];
			}
		}
		if ( $class_matches ) {
			$matching_version = null;
			$matching_class = null;
			uksort( $class_matches, 'version_compare' );
			if ( null === $version ) {
				// Return highest version
				$matching_class = end( $class_matches );
				$matching_version = key( $class_matches );
			} else {
				if ( ! is_array( $version ) ) {
					$version = array( $version => '<=' );
				}
				// Search for version
				foreach ( $class_matches as $test_version => $test_class ) {
					$maybe_match = false;
					foreach ( $version as $compare_version => $compare_operator ) {
						if ( version_compare( $test_version, $compare_version, $compare_operator ) ) {
							$maybe_match = true;
						} else {
							$maybe_match = false;
							break;
						}
					}
					if ( true === $maybe_match ) {
						// Only consider it a match if all cases were true
						$matching_version = $test_version;
						$matching_class   = $test_class;

					}
				}
			}
			if ( null !== $matching_class ) {
				return $matching_class::go( $params, $matching_version );
			}
		}
		return null;
	}
}