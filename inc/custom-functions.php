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
		'rwt-menu-header' => esc_html__( 'RWT Header Menu', 'rest-api-endpoints' ),
		'rwt-menu-footer' => esc_html__( 'RWT Footer Menu', 'rest-api-endpoints' ),
	] );
}
add_action( 'init', 'rae_custom_new_menu' );

/**
 * Register Sidebar
 */

/**
 * Register widget areas.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function rae_sidebar_registration() {

	// Arguments used in all register_sidebar() calls.
	$shared_args = [
		'before_title'  => '<h2 class="widget-title subheading heading-size-3">',
		'after_title'   => '</h2>',
		'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
		'after_widget'  => '</div></div>',
	];

	// Footer #1.
	register_sidebar(
		array_merge(
			$shared_args,
			[
				'name'        => __( 'RWT Footer #1', 'rest-api-endpoints' ),
				'id'          => 'rwt-sidebar-1',
				'description' => __( 'Widgets in this area will be displayed in the first column in the footer.', 'rest-api-endpoints' ),
			]
		)
	);

	// Footer #2.
	register_sidebar(
		array_merge(
			$shared_args,
			[
				'name'        => __( 'RWT Footer #2', 'rest-api-endpoints' ),
				'id'          => 'rwt-sidebar-2',
				'description' => __( 'Widgets in this area will be displayed in the second column in the footer.', 'rest-api-endpoints' ),
			]
		)
	);

}

add_action( 'widgets_init', 'rae_sidebar_registration' );


if ( function_exists( 'register_sidebar' ) ) {
	register_sidebar();
}

