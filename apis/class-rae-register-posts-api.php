<?php
/**
 * Register Posts API
 *
 * @package REST API ENDPOINT
 */

class Rae_Register_Posts_Api {
	/**
	 * Rae_Register_Posts_Api constructor.
	 */
	function __construct() {
		add_action( 'rest_api_init', array( $this, 'rae_rest_posts_endpoints' ) );
	}

	/**
	 * Register posts endpoints.
	 */
	function rae_rest_posts_endpoints() {
		/**
		 * Handle Create Post request.
		 *
		 * This endpoint takes 'title', 'content' and 'user_id' in the body of the request.
		 * Returns the user object on success
		 * Also handles error by returning the relevant error if the fields are empty.
		 *
		 * Example: http://example.com/wp-json/rae/v1/post/create
		 */
		register_rest_route(
			'wp/v2/rae',
			'/post/create',
			array(
			'methods' => 'POST',
			'callback' => array( $this, 'rae_rest_create_post_endpoint_handler' ),
		));
	}

	/**
	 * Creat Post call back.
	 *
	 * @param WP_REST_Request $request
	 */
	function rae_rest_create_post_endpoint_handler( WP_REST_Request $request ) {
		$response = array();
		$parameters = $request->get_params();

		$user_id = sanitize_text_field( $parameters['user_id'] );
		$title = sanitize_text_field( $parameters['title'] );
		$content = sanitize_text_field( $parameters['content'] );

		// Error Handling.
		$error = new WP_Error();

		if ( empty( $user_id ) ) {
			$error->add(
				400,
				__( "User ID field is required", 'rest-api-endpoints' ),
				array( 'status' => 400 )
			);

			return $error;
		}

		if ( empty( $title ) ) {
			$error->add(
				400,
				__( "Title field is required", 'rest-api-endpoints' ),
				array( 'status' => 400 )
			);

			return $error;
		}

		if ( empty( $content ) ) {
			$error->add(
				400,
				__( "Body field is required", 'rest-api-endpoints' ),
				array( 'status' => 400 )
			);

			return $error;
		}

		// Check if the user with this id can publish posts
		$user_can_publish_post = user_can( $user_id,'publish_posts' );
		if ( ! $user_can_publish_post ) {
			$error->add(
				400,
				__( "You don't have previlige to publish a post", 'rest-api-endpoints' ),
				array( 'status' => 400 )
			);

			return $error;
		}

		$my_post = array(
			'post_type' => 'post',
			'post_author' => $user_id,
			'post_title'   => sanitize_text_field( $title ),
			'post_status'   => 'publish',
			'post_content'   => $content,
		);
		// It will return the new inserted $post_id
		$post_id = wp_insert_post( $my_post );

		// If user found
		if ( ! is_wp_error( $post_id ) ) {
			$response['status'] = 200;
			$response['post_id'] = $post_id;
		} else {
			// If user not found
			$error->add( 406, __( 'Post creating failed', 'rest-api-endpoints' ) );
			return $error;
		}

		return new WP_REST_Response( $response );
	}
}

new Rae_Register_Posts_Api();
