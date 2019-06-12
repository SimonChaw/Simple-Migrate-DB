<?php
/**
 * Simple Migrate DB API
 *
 * For accepting database packages from the other site.
 *
 * @package     SMDB
 * @subpackage  Functions/API
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function table_names( ) {
    $db_handler = SMDB()->dbhandler;
    $db_handler->get_all_tables();

    $output = array(
      'success' => true,
      'tables'  => $db_handler->tables
    );

    return $output;
}

add_action( 'rest_api_init', function () {
	register_rest_route( 'smdb-api/v1', '/table-names', array(
		'methods' => 'GET',
		'callback' => 'table_names',
	) );
} );

function db_dump( $request ) {
    $output = array();
    $req_tables = $request->get_json_params();

    //Set up tables and collect the data
    $db_handler = SMDB()->dbhandler;
    $db_handler->set_table_names( $req_tables );
    $db_handler->get_all_data();
    $data = $db_handler->tables;
    $output['success'] = true;
    $output['tables'] = $data;

    return $output;
}

add_action( 'rest_api_init', function () {
	register_rest_route( 'smdb-api/v1', '/db-dump', array(
		'methods' => 'POST',
		'callback' => 'db_dump',
	) );
} );
