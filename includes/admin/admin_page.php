<?php
/**
 * Admin Actions
 *
 * @package     SMDB
 * @subpackage  Admin/Page
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function render_admin_view(){
  ?>
  <div class="wrap">
    <h1>Simple Database Migration</h1>
    <hr class="wp-header-end">
    <div class="" style="width:60%; height:auto; background-color:white;border: 2px solid #ccc;padding:20px;">
      <a href="#" class="button button-primary">Get Started</a>
      <!--
      <form class="" action="index.html" method="post">
        <h4>Upload URL</h4>
        <input type="text" name="txtURL" value="">
      </form>
      -->
    </div>
  </div>
  <?php
}

function setup_menu(){
  add_menu_page( 'Migrate Database', 'Simple Database Migration', 'manage_options', 'migrate-db', 'init', 'dashicons-migrate', 1 );
}

function init(){
  render_admin_view();
}

add_action('admin_menu', 'setup_menu');
