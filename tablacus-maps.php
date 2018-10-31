<?php
/*
Plugin Name: Tablacus Maps
Plugin URI: 
Description: Use OpenStreetMap instead of Google Maps
Version: 1.0.1
Author: tablacus
Author URI: https://tablacus.github.io/
License: GPL2
*/

$tablacus_maps = new Tablacus_Maps();

/**
 * Class Simple_Map
 */
class Tablacus_Maps {
	/**
	 * Version.
	 */
	private $ver_tablacus = '1.0.1';
	private $ver_API = '0.1.5';
	private $ver_leaflet = '1.3.4';

	/**
	 * Init function.
	 */
	public function init() {
		$option = get_option( 'tablacus_maps' );
		if ( isset( $option['leaflet_css'] ) ) {
			wp_enqueue_style( 'leaflet', $option['leaflet_css'], array(), $this->ver_tablacus );
		}
		if ( isset( $option['leaflet_js'] ) ) {
			wp_enqueue_script( 'leaflet', $option['leaflet_js'], array(), $this->ver_tablacus );
		}
		if ( isset( $option['tablacus_js'] ) ) {
			wp_enqueue_script( 'tablacus_maps', $option['tablacus_js'] . '?alias=google', array(), $this->ver_tablacus );
		}
	}
	
	/**
	 * Tablacus_Maps constructor.
	 */
	function __construct() {
		add_action( 'wp_enqueue_scripts',  array( $this, 'init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );

		add_filter( 'script_loader_tag', function ( $tag, $handle ) {
			if ( preg_match( '/\/\/maps\.google\./', $tag ) ) {
				$option = get_option( 'tablacus_maps' );
				return preg_replace( '/\/\/maps\.google\.com\/maps\/api\/js\?|\/\/maps\.googleapis\.com\/maps\/api\/js/', preg_replace( '/^[^:]*/', '', $option['tablacus_js'] ) . '?alias=google&', $tag );
			}
			return $tag;
		}, 10, 2 );
	}
	/**
	 * Add admin menu.
	 */
	public function admin_menu() {

		add_options_page(
			'Tablacus Maps',
			'Tablacus Maps',
			'manage_options',
			'tablacus_maps',
			array( $this, 'options_page' )
		);

	}

	/**
	 * Sanitize
	 */
	public function sanitize( $input ) {
		$new_input = array();
		$new_input['tablacus_js'] = esc_url_raw( $input['tablacus_js'] ); 
		$new_input['leaflet_js'] = esc_url_raw( $input['leaflet_js'] ); 
		$new_input['leaflet_css'] = esc_url_raw( $input['leaflet_css'] ); 
		return $new_input;
	}

	/**
	 * Add description of Post Notifier.
	 */
	public function section_callback() {
		echo esc_html__( 'Use OpenStreetMap instead of Google Maps.', 'tablacus_maps' );
	}

	public function section2_callback() {
	}

	/**
	 * Output text field.
	 */
	public function render_tablacus_js() {
		$option = get_option( 'tablacus_maps' );
		printf(
			'<input type="text" name="tablacus_maps[tablacus_js]" value="%s" size="70">',
			esc_html( isset( $option['tablacus_js'] ) ? $option['tablacus_js'] : "" )
		);
		printf(
			'<input type="button" onclick="document.F.elements[\'tablacus_maps[tablacus_js]\'].value=\'https://unpkg.com/tablacusmapsapi@%s/tablacusmapsapi.js\'" value="Set default">',
			$this->ver_API
		);
	}

	public function render_leaflet_js() {
		$option = get_option( 'tablacus_maps' );
		printf(
			'<input type="text" name="tablacus_maps[leaflet_js]" value="%s" size="70">',
			esc_html( isset( $option['leaflet_js'] ) ? $option['leaflet_js'] : "" )
		);
		printf(
			'<input type="button" onclick="document.F.elements[\'tablacus_maps[leaflet_js]\'].value=\'https://unpkg.com/leaflet@%s/dist/leaflet.js\'" value="Set default">',
			$this->ver_leaflet
		);
	}

	public function render_leaflet_css() {
		$option = get_option( 'tablacus_maps' );
		printf(
			'<input type="text" name="tablacus_maps[leaflet_css]" value="%s" size="70">',
			esc_html( isset( $option['leaflet_css'] ) ? $option['leaflet_css'] : "" )
		);
		printf(
			'<input type="button" onclick="document.F.elements[\'tablacus_maps[leaflet_css]\'].value=\'https://unpkg.com/leaflet@%s/dist/leaflet.css\'" value="Set default">',
			$this->ver_leaflet
		);
	}

	/**
	 * Output optoion form.
	 */
	public function options_page() {
		?>
		<form action='options.php' method='post' name='F'>
		<?php
			settings_fields( 'tablacusmapspage' );
			do_settings_sections( 'tablacusmapspage' );

			submit_button();
		?>
		</form>
		<?php
	}

    /**
     * Add options page
     */
	public function settings_init() {

		register_setting(
			'tablacusmapspage',
			'tablacus_maps',
			array( $this, 'sanitize' )
		);

		add_settings_section(
			'tablacus_maps_section',
			esc_html__( 'Tablacus Maps API', 'tablacus_maps' ),
			array( $this, 'section_callback' ),
			'tablacusmapspage'
		);

		add_settings_field(
			'tablacus_js',
			esc_html__( 'JavaScript URL', 'tablacus_maps' ),
			array( $this, 'render_tablacus_js' ),
			'tablacusmapspage',
			'tablacus_maps_section'
		);

		add_settings_section(
			'tablacus_maps_section2',
			esc_html__( 'leaflet', 'tablacus_maps' ),
			array( $this, 'section2_callback' ),
			'tablacusmapspage'
		);

		add_settings_field(
			'leaflet_js',
			esc_html__( 'JavaScript URL', 'tablacus_maps' ),
			array( $this, 'render_leaflet_js' ),
			'tablacusmapspage',
			'tablacus_maps_section2'
		);

		add_settings_field(
			'leaflet_css',
			esc_html__( 'CSS URL', 'tablacus_maps' ),
			array( $this, 'render_leaflet_css' ),
			'tablacusmapspage',
			'tablacus_maps_section2'
		);

	}
}
    
?>