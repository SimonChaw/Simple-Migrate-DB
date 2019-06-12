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
  <div id="app" class="wrap">
    <h1>Simple Database Migration</h1>
    <br>
    <hr class="wp-header-end">
    <div class="">Your SMDB Key : {{secure_key}}</div>
    <div class="card"  style="padding:0;">
      <div v-if="currentStage == 1">
        <div class="card-body">
          <div class="card-title">Current Migrations:</div>
          <div class="card-text text-center ">
            No pending migration requests.
          </div>
        </div>
        <div class="card-footer text-center">
          <div v-on:click="changeStage" class="btn btn-primary">Migrate this site.</div>
        </div>
      </div>
      <div v-if="currentStage == 2">
        <div class="card-body">
          <div class="card-title">Site Migration:</div>
          <div class="card-text">
            <div class="text-muted">
              Please note: The site you are migrating to must already have SMDB installed.
              Upon migration the site's files and it's database will be completely wiped and replaced by this site's.
            </div>
            <div class="flex flex-col w-100 mt-5">
              <div class="mb-2">
                <label for="url">Enter the URL of the site you are migrating to:</label>
                <input id="url" type="text" class="form-control" name="" value="" placeholder="Ex: http://www.newsite.com">
              </div>
              <div class="mb-2">
                <label for="securekey">Enter the SMDB Secure Key of the site you are migrating to:</label>
                <input id="securekey" type="text" class="form-control" name="" value="" placeholder="Enter the 16 digit secure key">
              </div>
              <div class="mb-2">
                <label for="username">Enter your FTP Username:</label>
                <input id="username" type="text" class="form-control" name="" value="" placeholder="Username">
              </div>
              <div>
                <label for="password">Enter your FTP Password:</label>
                <input id="password" type="password" class="form-control" name="" value="" placeholder="Password">
                <p class="text-muted">Your FTP Username & Password will not be saved anywhere. It will be used to send your files over and that's all.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer text-right">
          <div v-on:click="packfiles" class="btn btn-primary" disabled>Migrate!</div>
        </div>
      </div>
      <div v-if="currentStage == 3">
        <div class="card-body">
          <div class="card-title font-bold">Working on it!</div>
          <div class="card-text">
            <div class="w-100 text-center">
              <?php
                echo '<img src="' . plugins_url( 'loading.svg', __FILE__ ) . '" > ';
                ?>
            </div>
            <div class="text-muted text-center">
              {{currentProcess.processName}}
            </div>
          </div>
        </div>
      </div>
      <div v-if="currentStage == 4">
        <div class="card-body card-success">
          <div class="card-title font-bold">Good News!</div>
          <div class="card-text">
            <div class="alert alert-success">
              Your site has been packed up and sent on its way to your new server! <br />
              Please use your SMDB plugin on that site to finish the migration.
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
  <?php
}

function setup_menu(){
  add_menu_page( 'Migrate Database', 'Simple Database Migration', 'manage_options', 'migrate-db', 'init', 'dashicons-migrate', 1 );
}

function secure_key_in_header(){
  echo '<script type="javascript/text">var secure_key =' . SMDB()->securekey . '</script>';
}

add_action('admin_head', 'secure_key_in_header', 1);

function init(){
  render_admin_view();
}

add_action('admin_menu', 'setup_menu');
