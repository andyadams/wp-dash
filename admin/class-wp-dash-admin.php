<?php
/**
 * WP Dash
 *
 * @package   WP_Dash_Admin
 * @author    Andy Adams <andy@overthebarn.com>
 * @license   GPL-2.0+
 * @link      http://github.com/andyadams/wp-dash/
 * @copyright 2014 Andy Adams
 */

/**
 * @package WP_Dash_Admin
 * @author  Andy Adams <andy@overthebarn.com>
 */
class WP_Dash_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 * @TODO:
		 *
		 * - Rename "Plugin_Name" to the name of your initial plugin class
		 *
		 */
		$plugin = WP_Dash::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( 'admin_footer', array( $this, 'print_search_box' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @TODO:
	 *
	 * - Rename "Plugin_Name" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {
		wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), WP_Dash::VERSION );
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @TODO:
	 *
	 * - Rename "Plugin_Name" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-libs', plugins_url( 'assets/js/libs.min.js', __FILE__ ), array(), WP_Dash::VERSION );
		wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery', $this->plugin_slug . '-libs' ), WP_Dash::VERSION );

		$post_types = array();

		foreach ( get_post_types() as $post_type ) {
			if ( ! in_array( $post_type, array( 'revision', 'nav_menu_item', 'attachment' ) ) ) {
				$post_types[] = $post_type;
			}
		}

		$all_posts = get_posts( array(
			'posts_per_page' => 9999,
			'orderby' => 'title',
			'post_status' => 'any',
			'post_type' => $post_types
		) );

		$titles_and_links = array();

		foreach ( $all_posts as $post ) {
			$titles_and_links[] = array(
				'id' => $post->ID,
				'title' => strip_tags( $post->post_title ),
				'link' => admin_url( 'post.php?action=edit&post=' . $post->ID )
			);
		}

		global $menu, $submenu;

		$link_to_parent_title = array();

		foreach ( $menu as $menu_item ) {
			$link_to_parent_title[$menu_item[2]] = $menu_item[0];
			if ( ! empty( $menu_item[0] ) && current_user_can( $menu_item[1] ) ) {
				$titles_and_links[] = array(
					'title' => strip_tags( $menu_item[0] ),
					'link' => admin_url( $menu_item[2] )
				);
			}
		}

		foreach ( $submenu as $parent_link => $submenu_items ) {
			foreach ( $submenu_items as $submenu_item ) {
				if ( ! empty( $submenu_item[0] ) && current_user_can( $submenu_item[1] ) ) {
					if ( strpos( $submenu_item[2], '.php' ) === false ) {
						$link = admin_url( 'options-general.php?page=' . $submenu_item[2] );
					} else {
						$link = admin_url( $submenu_item[2] );
					}

					$titles_and_links[] = array(
						'title' => $link_to_parent_title[$parent_link] . ' &rarr; ' . strip_tags( $submenu_item[0] ),
						'link' => $link
					);
				}
			}
		}


		$wp_dash_posts = array( 'posts' => $titles_and_links );
		wp_localize_script( $this->plugin_slug . '-admin-script', 'WPDash', $wp_dash_posts );
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 * @TODO:
		 *
		 * - Change 'Page Title' to the title of your plugin admin page
		 * - Change 'Menu Text' to the text for menu item for the plugin settings page
		 * - Change 'manage_options' to the capability you see fit
		 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'WP Dash Settings', $this->plugin_slug ),
			__( 'WP Dash', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	/**
	 * NOTE:     Actions are points in the execution of a page or process
	 *           lifecycle that WordPress fires.
	 *
	 *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function print_search_box() {
		if ( is_admin() ) {
			?>
				<div id="wp-dash-search-box">
					<input type="text" name="wp_dash_search" id="wp-dash-search" class="mousetrap">
					<div id="wp-dash-results-wrapper">
						<ul id="wp-dash-results"></ul>
					</div>
				</div>
			<?php
		}
	}

}
