<?php
/**
 * Actions
 *
 * @package     SMDB
 * @subpackage  Functions
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */


 // Exit if accessed directly
 if ( ! defined( 'ABSPATH' ) ) exit;

 /**
  * Fetch the names of all tables for migration options
  *
  * @since  1.0
  * @return mixed
  */
function request_tables() {

        $db_handler = SMDB()->dbhandler;
        $db_handler->get_all_tables();

        $output = array(
          'success' => true,
          'tables'  => $db_handler->tables
        );

        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        	header( 'Content-Type: application/json' );
        	echo json_encode( $output );
        	wp_die();
        }
}

add_action('wp_ajax_request_tables', 'request_tables', 10);

/**
 * Send the requested tables objects to the new site to be copied over or created
 *
 * @since  1.0
 * @param  array $args  Array of arguments: url, tables
 * @return mixed
 */
function migrate_db() {

        $output = array();
        $args = $_POST;

        if( empty($args) || empty($args['url']) || empty($args['tables']) ){
            $output['success'] = false;
        } else {
            $output['success'] = true;

            //Set up tables and collect the data
            $db_handler = SMDB()->dbhandler;
            $db_handler->set_table_names( $args['tables'] );
            $db_handler->get_all_data();
            $data = $db_handler->tables;
            $url  = $args['url'];

            //Set up HTTP stream and post the data to the new site
            $options = array(
                    'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data),
                )
            );

            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            $output['result'] = $result;
            
        }


        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        	header( 'Content-Type: application/json' );
        	echo json_encode( $output );
        	wp_die();
        }
}

add_action('wp_ajax_migrate_db', 'migrate_db', 10);
