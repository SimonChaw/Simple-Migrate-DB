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

            $result = json_decode($result);
            $output['tables'] = $result->tables;
            $output['errors'] = SMDB()->dbhandler->migrate($result->tables);
        }


        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        	header( 'Content-Type: application/json' );
        	echo json_encode( $output );
        	wp_die();
        }
}

add_action('wp_ajax_migrate_db', 'migrate_db', 10);

/**
 * Create jobs with ids so the user can get updates.
 *
 * @since  1.0
 * @return mixed
 */
function update_progress($id, $currentStep, $processLength) {

    $_SESSION['process-' . $id] = [
      'currentStep' => $currentStep,
      'processLength' => $processLength,
      'message' => 'Update',
    ];
}

/**
 * Return request job update by id
 *
 * @since  1.0
 * @return mixed
 */
function get_process_update(){
  $update = '';
  $id = $_POST['processId'];
  if ($id) {
    $update = $_SESSION['process-' . $id];
  }
  return $update;
}

//add_filter( 'heartbeat_received', 'get_process_update', 10, 3 );


/**
 * Pack up this site's content into a zip folder.
 *
 * @since  1.0
 * @return mixed
 */
function packup() {
  // Keep track of the process id so the client can get their updates.
  $id = $_POST['id'];

  $package = new ZipArchive();

  $destination = get_home_path();
  $result;

  if (!$package->open('package.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE)){
    $result = "Failed to create archive";
  }

  if(!isset($result)){
    $result="ok so far.";
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($destination));
    $fileCount = iterator_count($files);
    $i = 0;
    foreach ($files as $name => $file)
    {
    	if ($file->isDir()) {
    		flush();
    		continue;
    	}

      update_progress($id, $i, $fileCount);

    	$filePath = $file->getRealPath();
      $relativePath = substr($filePath, strlen($destination));

      $package->addFile($filePath, $relativePath);
      $i ++;
    }


    // Zip archive will be created only after closing object
    $package->close();
  }
  // All we want to do here is just let the client know we received the request.
  if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
    header( 'Content-Type: application/json' );
    echo json_encode( ['success' => true] );
    wp_die();
  }
}

add_action('wp_ajax_pack', 'packup', 10);


/**
 * Pack up this site's SQL into a file for export.
 *
 * @since  1.0
 * @return bool
 */
function packsql(){

  // Call our db packer class
  SMDB()->dbpacker->packup();

  if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
    header( 'Content-Type: application/json' );
    echo json_encode( ['success' => true] );
    wp_die();
  }
}

add_action('wp_ajax_packsql', 'packsql', 10);
