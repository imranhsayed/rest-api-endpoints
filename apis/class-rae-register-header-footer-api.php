<?php
/**
 * Register Header and Footer Api
 *
 * Get header and footer of the site.
 *
 * @package REST API ENDPOINTS
 */

class Rae_Register_Header_Footer_Api {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->route = '/header-footer';
		add_action( 'rest_api_init', [ $this, 'rest_posts_endpoints' ] );
	}

	/**
	 * Register posts endpoints.
	 */
	public function rest_posts_endpoints() {

		/**
		 * Handle Posts Request: GET Request
		 *
		 * This api gets the header and footer of the site.
		 * The data will include:
		 * 1. Site Logo
		 * 2. Header menu with the given menu location id
		 * 3. Footer menu with the given menu location id
		 *
		 * The 'header_location_id' here is a string e.g. 'primary' or whatever 'header_location_id' name you have used at the time of registration of the menu.
		 *
		 * Example: http://example.com/wp-json/rae/v1/header-footer?header_location_id=primary&footer_location_id=secondary
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
	 * Returns the menu items array of object on success
	 *
	 * @param WP_REST_Request $request request object.
	 *
	 * @return WP_Error|WP_REST_Response response object.
	 */
	public function rest_endpoint_handler( WP_REST_Request $request ) {
		$response   = [];
		$parameters = $request->get_params();
		$header_menu_location_id   = ! empty( $parameters['header_location_id'] ) ? sanitize_text_field( $parameters['header_location_id'] ) : '';
		$footer_menu_location_id   = ! empty( $parameters['footer_location_id'] ) ? sanitize_text_field( $parameters['footer_location_id'] ) : '';

		// Error Handling.
		$error = new WP_Error();

		$header_menu_items = $this->get_nav_menu_items( $header_menu_location_id );
		$footer_menu_items = $this->get_nav_menu_items( $footer_menu_location_id );

		// If any menus found.
		if ( ! empty( $header_menu_items ) || ! empty( $footer_menu_items ) ) {

			$response['status']    = 200;
			$response['data'] = [
				'header' => [
					'siteLogoUrl' => $this->get_custom_logo_url( 'custom_logo' ),
					'siteTitle' => get_bloginfo( 'title' ),
					'siteDescription' => get_bloginfo( 'description' ),
					'favicon' => get_site_icon_url(),
					'headerMenuItems' => $header_menu_items,
				],
				'footer' => [
					'footerMenuItems' => $footer_menu_items,
					'socialLinks' => $this->get_social_icons(),
				]
			];

		} else {

			// If the posts not found.
			$error->add( 406, __( 'Data not found', 'rest-api-endpoints' ) );

			return $error;

		}

		return new WP_REST_Response( $response );

	}

	/**
	 * Construct a post data that contains, title, excerpt and featured image.
	 *
	 * @param {array} $post_ID post id.
	 *
	 * @return array
	 */
	public function get_required_post_data( $post_ID ) {

	}

	/**
	 * Get Custom logo URL.
	 *
	 * @return string
	 */
	public function get_custom_logo_url( $key ) {

		$custom_logo_id = get_theme_mod( $key );
		$image = wp_get_attachment_image_src( $custom_logo_id , 'full' );
		return $image[0];
	}

	/**
	 * Get social icons
	 *
	 * @return array $social_icons
	 */
	public function get_social_icons() {

		$social_icons = [];
		$social_icons_name = [ 'facebook', 'twitter', 'instagram', 'youtube' ];

		foreach ( $social_icons_name as $social_icon_name ) {

			$social_link = get_theme_mod( sprintf( 'rae_%s_link', $social_icon_name ) );

			if ( $social_link ) {
				array_push( $social_icons, [
					'iconName' =>esc_attr( $social_icon_name ),
					'iconUrl' => esc_url( $social_link )
				] );
			}
		}

		return $social_icons;

	}

	/**
	 * Get nav menu items by location
	 *
	 * @param string $location The menu location id
	 * @param array $args Arguments.
	 *
	 * @return array $menu_data Menu items array of Objects.
	 */
	function get_nav_menu_items( $location, $args = [] ) {

		if ( empty( $location ) ) {
			return '';
		}

		// Get all locations
		$locations = get_nav_menu_locations();

		// Get object id by location
		$object = wp_get_nav_menu_object( $locations[ $location ] );

		// Get menu items by menu name
		$menu_data = wp_get_nav_menu_items( $object->name, $args );
		$menu_items = [];
		$submenu_items = [];

		if ( ! empty( $menu_data ) ) {

			// Menus
			foreach ( $menu_data as $item ) {
				if ( empty( $item->menu_item_parent ) ) {
					$menu_items[ $item->ID ]             = [];
					$menu_items[ $item->ID ]['ID']       = $item->ID;
					$menu_items[ $item->ID ]['title']    = $item->title;
					$menu_items[ $item->ID ]['url']      = $item->url;
					$menu_items[ $item->ID ]['children'] = [];
				}
			}

			// Submenus
			foreach ( $menu_data as $item ) {
				if ( $item->menu_item_parent ) {
					$submenu_items[ $item->ID ]                                     = [];
					$submenu_items[ $item->ID ]['ID']                               = $item->ID;
					$submenu_items[ $item->ID ]['title']                            = $item->title;
					$submenu_items[ $item->ID ]['url']                              = $item->url;
					$menu_items[ $item->menu_item_parent ]['children'][ $item->ID ] = $submenu_items[ $item->ID ];
				}
			}

		}

		$menu_items = ! empty( $menu_items ) ? $menu_items : '';

		// Return menu post objects
		return $menu_items;

	}
}

new Rae_Register_Header_Footer_Api();
