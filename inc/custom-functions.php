<?php
/**
 * Custom Functions
 *
 * @package REST API ENDPOINTS
 */

/**
 * Register Menus.
 */
function rae_custom_new_menu() {
	register_nav_menus( [
		'travel-menu-header' => esc_html__( 'Travel Header Menu', 'rest-api-endpoints' ),
		'travel-menu-footer' => esc_html__( 'Travel Footer Menu', 'rest-api-endpoints' ),
	] );
}
add_action( 'init', 'rae_custom_new_menu' );


