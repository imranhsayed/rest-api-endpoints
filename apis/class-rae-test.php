<?php
/**
 * Register Media Releases CPT API
 *
 * @package rae
 */

use Firebase\JWT\JWT;

/**
 * Class Rae_Test
 */
class Rae_Test {

	/**
	 * Rae_Test constructor.
	 */
	public function __construct() {

		$cookie_name = "user";
		$cookie_value = "John Doe";
		setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/" );

		$this->post_type     = 'post';
		$this->route         = '/mytest';

		add_action( 'rest_api_init', array( $this, 'rae_rest_posts_endpoints' ) );

	}

	/**
	 * Register posts endpoints.
	 */
	public function rae_rest_posts_endpoints() {

		/**
		 * Handle Get Media Releases Posts Request: GET Request
		 *
		 * This endpoint takes 'categories_child_id', 'audience_child_id' both optionally in query params of the request.
		 * Returns the user object on success
		 * Also handles error by returning the relevant error if the fields are empty.
		 *
		 * Example: http://example.com/wp-json/rae/v1/mytest
		 */
		register_rest_route(
			'rae/v1',
			$this->route,
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'rae_rest_endpoint_handler' ),
			)
		);
	}

	public function get_all_cookies( $cookies_as_string ) {
		$headerCookies = explode('; ', $cookies_as_string );
		$cookies = array();
		foreach( $headerCookies as $itm ) {
			list( $key, $val ) = explode( '=', $itm,2 );
			$cookies[ $key ] = $val;
		}

		return $cookies ;
	}


	/**
	 * Get posts call back.
	 *
	 * It will return posts with given term ids, else the default posts.
	 *
	 * @param WP_REST_Request $request request object.
	 *
	 * @return WP_Error|WP_REST_Response response object.
	 */
	public function rae_rest_endpoint_handler( WP_REST_Request $request ) {

		$response      = [];
		$parameters    = $request->get_params();
		$posts_page_no = ! empty( $parameters['page_no'] ) ? intval( sanitize_text_field( $parameters['page_no'] ) ) : 1;


//		setcookie( 'hey', 'hjhj', time() + 3600, 'http://localhost:8080/' );

//		header("Set-Cookie: yourCookie=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODg4OFwvd29yZHByZXNzLWxvZ2luIiwiaWF0IjoxNTcxNDA5NzE5LCJuYmYiOjE1NzE0MDk3MTksImV4cCI6MTU3MjAxNDUxOSwiZGF0YSI6eyJ1c2VyIjp7ImlkIjoiMSJ9fX0.vcqPmj9lQaFHQ20Z_7gDXHAlW2Vev7lkht4Gj4WBqdA; httpOnly");
		header("Set-Cookie: authToken=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODg4OFwvd29yZHByZXNzLWxvZ2luIiwiaWF0IjoxNTcxNDA5NzE5LCJuYmYiOjE1NzE0MDk3MTksImV4cCI6MTU3MjAxNDUxOSwiZGF0YSI6eyJ1c2VyIjp7ImlkIjoiMSJ9fX0.vcqPmj9lQaFHQ20Z_7gDXHAlW2Vev7lkht4Gj4WBqdA; httpOnly");
//		header("Set-Cookie: myCookie='sdsd'; expires=Friday, 22-Feb-14 22:03:38 GMT; httpOnly");


		$header = $request->get_headers();
		$cookies = $this->get_all_cookies( $header['cookie'][0] );

		$result = $this->validate_token( $cookies['authToken'] );

		return new WP_REST_Response( $result );

		// Error Handling.
		$error = new WP_Error();

		$cases_data = [];

		// If posts found.
		if ( ! is_wp_error( $cases_data['cases_posts'] ) && ! empty( $cases_data['cases_posts'] ) ) {
			$response['status']      = 200;
			$response['cases_posts'] = $cases_data['cases_posts'];
			$response['found_posts'] = $cases_data['found_posts'];

			$total_found_posts      = intval( $cases_data['found_posts'] );
			$response['page_count'] = $this->calculate_page_count( $total_found_posts, 9 );

		} else {
			// If posts not found.
			$error->add( 406, __( 'Media Releases Posts not found', 'rest-api-endpoints' ) );
			return $error;
		}

		return new WP_REST_Response( $response );
	}

	/**
	 * Main validation function, this function try to get the Autentication
	 * headers and decoded.
	 *
	 * @param bool $output
	 *
	 * @return WP_Error | Object | Array
	 */
	public function validate_token( $token, $output = true)
	{

		/** Get the Secret Key */
		$secret_key = defined('JWT_AUTH_SECRET_KEY') ? JWT_AUTH_SECRET_KEY : false;

		if (!$secret_key) {
			return new WP_Error(
				'jwt_auth_bad_config',
				'JWT is not configurated properly, please contact the admin',
				array(
					'status' => 403,
				)
			);
		}

		/** Try to decode the token */
		try {
			$token = JWT::decode($token, $secret_key, array('HS256'));
			/** The Token is decoded now validate the iss */
			if ($token->iss != get_bloginfo('url')) {
				/** The iss do not match, return error */
				return new WP_Error(
					'jwt_auth_bad_iss',
					'The iss do not match with this server',
					array(
						'status' => 403,
					)
				);
			}
			/** So far so good, validate the user id in the token */
			if (!isset($token->data->user->id)) {
				/** No user id in the token, abort!! */
				return new WP_Error(
					'jwt_auth_bad_request',
					'User ID not found in the token',
					array(
						'status' => 403,
					)
				);
			}
			/** Everything looks good return the decoded token if the $output is false */
			if (!$output) {
				return $token;
			}
			/** If the output is true return an answer to the request to show it */
			return array(
				'code' => 'jwt_auth_valid_token',
				'data' => array(
					'status' => 200,
				),
			);
		} catch (Exception $e) {
			/** Something is wrong trying to decode the token, send back the error */
			return new WP_Error(
				'jwt_auth_invalid_token',
				$e->getMessage(),
				array(
					'status' => 403,
				)
			);
		}
	}


}

new Rae_Test();
