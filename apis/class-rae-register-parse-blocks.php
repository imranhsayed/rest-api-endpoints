<?php
/**
 * Register Parse Block by Post ID
 *
 * @package REST API ENDPOINTS
 */

class Rae_Register_Parse_Block {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->post_type     = 'post';
		$this->route         = '/parse-block';

		add_action( 'rest_api_init', [ $this, 'rest_posts_endpoints' ] );
	}

	/**
	 * Register posts endpoints.
	 */
	public function rest_posts_endpoints() {

		/**
		 * Handle Posts Request: GET Request
		 *
		 * This endpoint takes 'page_no' in query params of the request.
		 * Returns the posts data object on success
		 * Also handles error by returning the relevant error.
		 *
		 * Example: http://example.com/wp-json/rae/v1/parse-block?post_id=1
		 */
		register_rest_route(
			'rae/v1',
			$this->route,
			[
				'method'   => 'GET',
				'callback' => [ $this, 'rest_endpoint_handler' ],
			]
		);
	}

	/**
	 * Get posts call back.
	 *
	 * Returns the posts data object on success
	 *
	 * @param WP_REST_Request $request request object.
	 *
	 * @return WP_Error|WP_REST_Response response object.
	 */
	public function rest_endpoint_handler( WP_REST_Request $request ) {
		$response      = [];
		$parameters    = $request->get_params();
		$post_id = ! empty( $parameters['post_id'] ) ? intval( sanitize_text_field( $parameters['post_id'] ) ) : '';

		// Error Handling.
		$error = new WP_Error();

		$parsed_block = $this->get_parsed_block_content( $post_id );

		// If posts found.
		if ( ! empty( $parsed_block ) ) {

			$response['status']      = 200;
			$response['parsed_block']  = $parsed_block;

		} else {

			// If the posts not found.
			$error->add( 406, __( 'Post not found', 'rest-api-endpoints' ) );

			return $error;

		}

		return new WP_REST_Response( $response );

	}

	/**
	 * Get the parsed content of the block.
	 *
	 * @param {array} $post_ID post id.
	 *
	 * @return array
	 */
	public function get_parsed_block_content( $post_ID ) {

		$parsed_content = [];

		if ( empty( $post_ID ) && ! is_array( $post_ID ) ) {
			return $parsed_content;
		}

		$post_result = get_post( $post_ID );

		$parsed_content = parse_blocks( $post_result->post_content );


		return $parsed_content;
	}
}

new Rae_Register_Parse_Block();
