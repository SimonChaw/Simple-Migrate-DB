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

function setup_menu(){
        add_menu_page( 'Migrate Database', 'Simple Database Migration', 'manage_options', 'migrate-db', 'init', 'dashicons-migrate' );
}

function init(){
        $db_handler = new SMDB_DB_Handler();
        $db_handler->get_all_tables();
}

add_action('admin_menu', 'setup_menu');
