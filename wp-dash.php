<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   Plugin_Name
 * @author    Andy Adams <andyadamscp@gmail.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 *
 * @wordpress-plugin
 * Plugin Name:       WP Dash
 * Plugin URI:        @TODO
 * Description:       A simple productivity plugin to get you around the WP dashboard quickly
 * Version:           1.0.0
 * Author:            Andy Adams
 * Author URI:        http://andyadams.org
 * Text Domain:       wp-dash
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/andyadams/wp-dash
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-plugin-name.php` with the name of the plugin's class file
 *
 */
require_once( plugin_dir_path( __FILE__ ) . 'public/class-wp-dash.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 * @TODO:
 *
 * - replace Plugin_Name with the name of the class defined in
 *   `class-plugin-name.php`
 */
//register_activation_hook( __FILE__, array( 'WP_Dash', 'single_activate' ) );
register_activation_hook( __FILE__, 'wp_dash_activate' );

function wp_dash_activate() {
	add_option( 'wp_dash_do_activation_redirect', true );
}

function wp_dash_redirect_on_activate() {
	if ( get_option('wp_dash_do_activation_redirect', false ) ) {
		delete_option('wp_dash_do_activation_redirect' );
		wp_redirect( admin_url( 'options-general.php?page=wp-dash' ) );
		exit;
	}
}
add_action( 'plugins_loaded', 'wp_dash_redirect_on_activate' );
// register_deactivation_hook( __FILE__, array( 'WP_Dash', 'deactivate' ) );

/*
 * @TODO:
 *
 * - replace Plugin_Name with the name of the class defined in
 *   `class-plugin-name.php`
 */
add_action( 'plugins_loaded', array( 'WP_Dash', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-plugin-name-admin.php` with the name of the plugin's admin file
 * - replace Plugin_Name_Admin with the name of the class defined in
 *   `class-plugin-name-admin.php`
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-wp-dash-admin.php' );
	add_action( 'plugins_loaded', array( 'WP_Dash_Admin', 'get_instance' ) );

}
