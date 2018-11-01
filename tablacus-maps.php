<?php
/*
Plugin Name: Tablacus Maps
Plugin URI: 
Description: Use OpenStreetMap instead of Google Maps
Version: 1.0.2
Author: tablacus
Author URI: https://tablacus.github.io/
License: GPL2
*/

function tablacus_maps_enqueue() {
	wp_enqueue_style( 'leaflet', plugins_url( 'leaflet/leaflet.css', __FILE__ ), array(), '1.3.4' );
	wp_enqueue_script( 'leaflet', plugins_url( 'leaflet/leaflet.js', __FILE__ ), array(), '1.3.4' );
	wp_enqueue_script( 'tablacus-maps', plugins_url( 'js/tablacusmapsapi.js', __FILE__ ) . '?alias=google', array(), '0.1.5' );
}
  
add_action( 'wp_enqueue_scripts', 'tablacus_maps_enqueue' );
add_action( 'admin_enqueue_scripts', 'tablacus_maps_enqueue' );

/**
 * Use Tablacus Maps API instead of Google Maps API.
 */
add_filter( 'script_loader_tag', function ( $tag, $handle ) {
	$result = $tag;
	if ( false !== strpos( $result, '//maps.google') ) {
		$tablacus_js = plugins_url( 'js/tablacusmapsapi.js', __FILE__ );
		$tablacus_js = preg_replace('/^[^\/]*/', '', $tablacus_js);
		$tablacus_js .= '?alias=google&';
		$result = str_replace( '//maps.google.com/maps/api/js?', $tablacus_js, $result);
		$result = str_replace( '//maps.googleapis.com/maps/api/js/?', $tablacus_js, $result);
	}
	return $result;
}, 10, 2 );

?>