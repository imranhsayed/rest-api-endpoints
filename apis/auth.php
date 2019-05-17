<?php
/**
 * User Authentication REST API
 *
 * @package REST API ENDPOINTS
 */

add_action( 'rest_api_init', 'rae_rest_user_endpoints' );

function rae_rest_user_endpoints() {
	/**
	 * Handle User Login request.
	 */
	register_rest_route( 'wp/v2/rae', '/user/login', array(
		'methods' => 'POST',
		'callback' => 'rae_rest_user_login_endpoint_handler',
	));
}

function rae_rest_user_login_endpoint_handler() {
	$response = array();
}
