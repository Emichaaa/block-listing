<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://growscratch.com
 * @since             1.0.0
 * @package           Block_Listing
 *
 * @wordpress-plugin
 * Plugin Name:       Block listing
 * Plugin URI:        https://growscratch.com
 * Description:       This is a plugin with which you can find out which blocks and on which pages are used on your site.
 * Version:           2.0.0
 * Author:            Emicha
 * Author URI:        https://growscratch.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       block-listing
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BLOCK_LISTING_VERSION', '2.0.0' );
define('BLOCK_LISTING_BASENAME', plugin_basename(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-block-listing-activator.php
 */
function activate_block_listing() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-block-listing-activator.php';
	Block_Listing_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-block-listing-deactivator.php
 */
function deactivate_block_listing() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-block-listing-deactivator.php';
	Block_Listing_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_block_listing' );
register_deactivation_hook( __FILE__, 'deactivate_block_listing' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-block-listing.php';

require plugin_dir_path( __FILE__ ) . 'includes/class-block-listing-shortcodes.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_block_listing() {

	$plugin = new Block_Listing();
	$plugin->run();

	$plugin_shortcodes = new Block_Listing_Shortcodes();

}
run_block_listing();
