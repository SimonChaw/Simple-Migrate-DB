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
  $table = SMDB()->dbhandler->get_test_table();
  $tables = array();
  array_push($tables, $table);
  var_dump($tables);
  echo SMDB()->dbhandler->migrate($tables);
  ?>
  <div class="wrap">
    <h1>Simple Database Migration</h1>
    <br>
    <hr class="wp-header-end">
    <div class="" style="width:60%; height:auto; background-color:white;border: 2px solid #ccc;padding:20px;">
      <div id="smdb-1" style="width:auto;">
        <h3>First what's the url of your old site?</h3>
        <input id="txt-site" style="width:60%;" type="text" placeholder="http://www.newwordpress.com" value="">
        <a href="#" id="btn-request" class="button button-primary" style="float:right;">Request</a>
        <div id="smdb-1-warnings"></div>
      </div>
      <div id="smdb-2" style="display:none;">
        <div id="selection" style="display:none;">
          <h4>Excellent! Here are the tables that are currently on your old site. Select which ones you want moved to your new site!</h4>
          <table class="wp-list-table widefat fixed striped pages">
            <thead>
              <tr>
                <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all" type="checkbox"></td>
                <th>Table Names</th>
              </tr>
            </thead>
            <tbody id="tbl-list">

            </tbody>
            <tfoot>
                <tr>
                  <td></td>
                  <td><a href="#" id="btn-finish-tables" class="button button-primary" style="float:right">Confirm Selection</a></div></td>
                </tr>
            </tfoot>
          </table>
        </div>
      <div id="smdb-3" class="" style="display:none;">
        <div style="background-color:#ccc;border:2px solid white;width:60%; margin: 20px auto 0 auto; padding: 5px 10px;">
          <h4 style="margin-top:0;">Progress...</h4>
          <div class="spinner"></div>
          <div style="clear:both;"></div>
        </div>
      </div>
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
