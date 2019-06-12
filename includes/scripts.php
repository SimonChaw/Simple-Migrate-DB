<?php
/**
 * Scripts
 *
 * @package     SMDB
 * @subpackage  Functions
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Load Scripts
 *
 * @since 1.0
 * @global $post
 * @param string $hook Page hook
 * @return void
 */

function load_scripts( $hook ) {

        //Don'  t load the scripts unless the user is on the plugin admin page
        if ( $hook === 'toplevel_page_migrate-db' ) {
          $js_dir = SMDB_PLUGIN_URL . 'assets/js/';

          // Get Vue
          wp_enqueue_script('vue', 'https://cdn.jsdelivr.net/npm/vue@2.5.17/dist/vue.js', [], '2.5.17');
          wp_register_script( 'smdb-admin-scripts', $js_dir . 'simple-migrate-db-new.js', ['jquery', 'jquery-form'], SMDB_VERSION, false );
          // Bootstrap
          wp_enqueue_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', [], '4.3.1');
          wp_enqueue_script( 'smdb-admin-scripts' );
          wp_localize_script( 'smdb-admin-scripts', 'ajax_object', array(
              'ajaxurl' => admin_url( 'admin-ajax.php' , '')
          ) );
        }

}

add_action( 'admin_enqueue_scripts', 'load_scripts', 100 );
