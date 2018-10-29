<?php
/*
Plugin Name: Tablacus Maps
Plugin URI: 
Description: Use OpenStreetMap instead of Google Maps
Version: 1.0.0
Author: tablacus
Author URI: https://tablacus.github.io/
License: GPL2
*/

function tablacus_maps_enqueue_styles() {
	wp_enqueue_style( 'leaflet', 'https://unpkg.com/leaflet@1.3.4/dist/leaflet.css', array(), '1.3.4' );
	wp_enqueue_script( 'leaflet', 'https://unpkg.com/leaflet@1.3.4/dist/leaflet.js', array(), '1.3.4' );
	wp_enqueue_script( 'tablacus-maps', 'https://unpkg.com/tablacusmapsapi@0.1.5/tablacusmapsapi.js?alias=google', array(), '0.1.5' );
}
  
add_action( 'wp_enqueue_scripts', 'tablacus_maps_enqueue_styles' );

add_action( 'admin_enqueue_scripts', 'tablacus_maps_enqueue_styles' );

add_filter( 'script_loader_tag', function ( $tag, $handle ) {
	return preg_replace( '/\/\/maps\.google\.com\/maps\/api\/js\?|\/\/maps\.googleapis\.com\/maps\/api\/js/', '//unpkg.com/tablacusmapsapi@0.1.5/tablacusmapsapi.js?alias=google&', $tag );
}, 10, 2 );

?>