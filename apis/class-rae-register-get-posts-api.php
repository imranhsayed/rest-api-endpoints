<?php
/**
 * Register Get Posts Api
 *
 * @package REST API ENDPOINT
 */

/**
 * Class Rae_Register_Get_Posts_Api
 */
class Rae_Register_Get_Posts_Api {

	/**
	 * Rae_Register_Get_Posts_Api constructor.
	 */
	public function __construct() {

		$this->post_type     = 'post';
		$this->route         = '/posts';

		add_action( 'rest_api_init', array( $this, 'rest_posts_endpoints' ) );

	}

	/**
	 * Register posts endpoints.
	 */
	public function rest_posts_endpoints() {

		/**
		 * Handle Get Case Studies Posts Request: GET Request
		 *
		 * This endpoint takes 'categories_child_id', 'audience_child_id' both optionally in query params of the request.
		 * Returns the user object on success
		 * Also handles error by returning the relevant error if the fields are empty.
		 *
		 * Example: http://example.com/wp-json/rae/v1/posts?page_no=1
		 */
		register_rest_route(
			'rae/v1',
			$this->route,
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'rest_endpoint_handler' ),
			)
		);
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
	public function rest_endpoint_handler( WP_REST_Request $request ) {

		$response      = [];
		$parameters    = $request->get_params();
		$posts_page_no = ! empty( $parameters['page_no'] ) ? intval( sanitize_text_field( $parameters['page_no'] ) ) : 1;

		// Error Handling.
		$error = new WP_Error();

		$cases_data = $this->get_posts( $posts_page_no );

		// If posts found.
		if ( ! is_wp_error( $cases_data['posts_data'] ) && ! empty( $cases_data['posts_data'] ) ) {
			$response['status']      = 200;
			$response['posts_data'] = $cases_data['posts_data'];
			$response['found_posts'] = $cases_data['found_posts'];

			$total_found_posts      = intval( $cases_data['found_posts'] );
			$response['page_count'] = $this->calculate_page_count( $total_found_posts, 9 );

		} else {
			// If posts not found.
			$error->add( 406, __( 'Posts not found', 'rest-api-endpoints' ) );
			return $error;
		}

		return new WP_REST_Response( $response );
	}

	/**
	 * Calculate page count.
	 *
	 * @param int $total_found_posts Total posts found.
	 * @param int $post_per_page Post per page count.
	 *
	 * @return int
	 */
	public function calculate_page_count( $total_found_posts, $post_per_page ) {

		return ( (int) ( $total_found_posts / $post_per_page ) ) + ( ( $total_found_posts % $post_per_page ) ? 1 : 0 );
	}

	/**
	 * Get case studies cpt posts.
	 * Call back function: Not to be called directory.
	 * Use get_profiles_by_slug() instead.
	 *
	 * @param array   $post_term_ids Category term id array.
	 * @param integer $page_no page no.
	 * @return array Case Studies posts.
	 */
	public function get_posts( $page_no = 1 ) {

		$args = [
			'post_type'              => $this->post_type,
			'post_status'            => 'publish',
			'posts_per_page'         => 9,
			'fields'                 => 'ids',
			'orderby'                => 'date',
			'paged'                  => $page_no,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		];

		$latest_posts_by_category = new \WP_Query( $args );

		$posts_result = $this->get_required_posts_data( $latest_posts_by_category->posts );
		$found_posts  = $latest_posts_by_category->found_posts;

		return [
			'posts_data' => $posts_result,
			'found_posts' => $found_posts,
		];
	}

	/**
	 * Construct a post array that contains, title, excerpt and featured image.
	 *
	 * @param {array} $post_IDs post ids.
	 *
	 * @return array
	 */
	public function get_required_posts_data( $post_IDs ) {

		$posts_result = [];

		if ( ! empty( $post_IDs ) && is_array( $post_IDs ) ) {
			foreach ( $post_IDs as $post_ID ) {

				$author_id = get_post_field( 'post_author', $post_ID );

				$post_data                     = [];
				$post_data['id']               = $post_ID;
				$post_data['title']            = get_the_title( $post_ID );
				$post_data['excerpt']          = get_the_excerpt( $post_ID );
				$post_data['date']             = get_the_date( '', $post_ID );
				$post_data['attachment_image'] = [
					'img_sizes'  => wp_get_attachment_image_sizes( get_post_thumbnail_id( $post_ID ) ),
					'img_src'    => wp_get_attachment_image_src( get_post_thumbnail_id( $post_ID ), 'full' ),
					'img_srcset' => wp_get_attachment_image_srcset( get_post_thumbnail_id( $post_ID ) ),
				];
				$post_data['categories']       = get_the_category( $post_ID );
				$post_data['meta']             = [
					'author_id'   => $author_id,
					'author_name' => get_the_author_meta( 'display_name', $author_id ),
				];

				array_push( $posts_result, $post_data );

			}
		}

		return $posts_result;
	}

}

new Rae_Register_Get_Posts_Api();
