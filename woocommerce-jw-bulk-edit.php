<?php
/**
 * Plugin Name: WooCommerce JW Bulk Edit
 * Plugin URI: http://joewebber.co.uk/
 * Description: A fully functional bulk edit plugin for Woocommerce
 * Version: 1.0.0
 * Author: Joe Webber
 * Author URI: http://joewebber.co.uk
 * Copyright: © 2018 Joe Webber.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: jw-bulk-import
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Load plugin.
 *
 * @return null
 */
function wcjwbulkedit_load() {

  // Check that Woocommerce is active
  if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    // Load plugin files
    require_once( 'includes/class-jw-bulk-edit-plugin.php' );

    // Initialise class
    $plugin = new JW_Bulk_Edit_Plugin;

    return;

  }

}

// Add action to load plugin when plugins load
add_action( 'plugins_loaded', 'wcjwbulkedit_load', 0 );