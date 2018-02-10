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

        $output = array();
        $args = $_POST;

        if( empty($args) || empty($args['url']) ){
            $output['success'] = false;
        } else {
            //Setup request URL
            $url  = $args['url'] . '/wp-json/smdb-api/v1/table-names';

            //Get table names from old site
            $result = file_get_contents($url);

            $result = json_decode($result);
            $output['tables'] = $result->tables;
            $output['success'] = true;
        }


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
            $url  = $args['url'] . '/wp-json/smdb-api/v1/db-dump';

            //Set up HTTP stream and post the data to the new site
            $options = array(
                    'http' => array(
                    'header'  => "Content-type: application/json",
                    'method'  => 'POST',
                    'content' => json_encode($args['tables']),
                )
            );

            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);

            $output['result'] = json_decode($result);

            $output['sql'] = SMDB()->dbhandler->migrate();
        
        }


        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        	header( 'Content-Type: application/json' );
        	echo json_encode( $output );
        	wp_die();
        }
}

add_action('wp_ajax_migrate_db', 'migrate_db', 10);
