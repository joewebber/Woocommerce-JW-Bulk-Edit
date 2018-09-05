<?php
/**
 * JW Bulk Edit Plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class JW_Bulk_Edit_Plugin {

  /**
   * Constructor.
   *
   */
  function __construct() {

    // Add 'JW Bulk Import' link under WooCommerce menu
    add_action( 'admin_menu', array( $this, '_add_menu_link' ) );

  }

  /**
  * Add menu link.
  *
  * @return null
  */
  function _add_menu_link() {

    // Add link in Woocommerce sub menu
    add_submenu_page( 'woocommerce', __( 'WooCommerce JW Bulk Edit ', 'woocommerce-jwbulkedit' ), __( 'JW Bulk Edit', 'woocommerce-jwbulkedit' ), 'manage_woocommerce', 'woocommerce-jwbulkedit', 'wcjwbulkedit_panel' );

  }

}