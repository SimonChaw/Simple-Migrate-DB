<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * EDD DB base class
 *
 * @package     SMDB
 * @subpackage  Classes/SMDB DB Handler
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

class SMDB_DB_Handler {

  /**
	 * Variable for holding all tables
	 *
	 * @access  public
	 * @since   1.0
	 */
	public $tables;

  /**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function __construct() {
    $this->tables = array();
  }

  /**
   * Get all of tables used by this wordpress site
   *
   * @access  public
   * @since   1.0
   */
  public function get_all_tables() {
      global $wpdb;
      $sql = "SHOW TABLES LIKE '%'";
      $results = $wpdb->get_results($sql);

      foreach($results as $index => $value) {
          foreach($value as $table_name) {
              array_push($this->tables, $table_name );
          }
      }
      var_dump($this->tables);
  }

  /**
   * Get all of the data for each table;
   *  $this->table['name']['row']['column_value'] = result['column_value']
   * @access  public
   * @since   1.0
   */
  public function get_all_data() {

  }

}
