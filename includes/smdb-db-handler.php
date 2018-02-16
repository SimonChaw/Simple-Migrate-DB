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
            $table = new SMDB_Table;
            $table->name = $table_name;
            array_push($this->tables, $table);
          }
      }

  }

  /**
   * Rewrite $tables->names based on what the user has selected
   *
   * @access  public
   * @since   1.0
   */
   public function set_table_names( $req_tables ){
      $this->tables = array();
      foreach ($req_tables as $key => $req_table) {
        $table = new SMDB_Table;
        $table->name = $req_table;
        array_push($this->tables, $table);
      }
   }

  /**
   * Get all of the data for each table;
   *
   * @access  public
   * @since   1.0
   */
  public function get_all_data() {
      global $wpdb;
      foreach ($this->tables as $key => $table) {
        $table->columns = $wpdb->get_col("DESC {$table->name}", 0);
        $rows = $wpdb->get_results("SELECT * FROM " . $table->name);
        foreach ($rows as $row_key => $row) {
          array_push($table->rows, $row);
        }
      }
  }
  public function get_test_table()
  {
      global $wpdb;
      $table = new SMDB_Table;
      $table->name = 'wp_posts';
      $table->columns = $wpdb->get_col("DESC {$table->name}", 0);
      $rows = $wpdb->get_results("SELECT * FROM " . $table->name);
      foreach ($rows as $row_key => $row) {
        array_push($table->rows, $row);
      }
      return $table;
  }
  /**
   * Overwrite tables with data from the old site.
   *
   * @access  public
   * @since   1.0
   */
  public function migrate( $tables ) {
      global $wpdb;
      $errors = array();
      foreach ($tables as $key => $table) {
          if (!empty($table->rows)) {
            //First delete all data in this table
            $wpdb->query("TRUNCATE TABLE {$table->name}");
            //Now Insert the data from the old site for this table
            $sql = "INSERT INTO {$table->name} (";
            //Prepare columns
            $col_length = count($table->columns);
            $i = 0;
            foreach ($table->columns as $column_key => $column) {
                $i ++;
                if ($i === $col_length) {
                  $sql = $sql . $column;
                } else {
                  $sql = $sql . $column . ', ';
                }
            }
            $sql = $sql . ') VALUES' ;
            $row_length = count($table->rows);
            $row_counter = 0;
            foreach ($table->rows as $row_key => $row) {
                $sql = $sql . '(';
                $i = 0;
                foreach ($table->columns as $column_key => $column) {
                  $i ++;
                  if ($i === $col_length) {
                    $sql = $sql . "'" . esc_sql($row->$column) . "'" ;
                  } else {
                    $sql = $sql . "'" . esc_sql($row->$column) . "'" . ', ';
                  }
                }
                $row_counter ++;
                if ($row_counter === $row_length) {
                    $sql = $sql . ')' ;
                } else {
                    $sql = $sql . '), ' ;
                }
            }
            $wpdb->query($sql);
            $error = $wpdb->last_error;
            array_push($errors, $error);
          }

      }
      return $errors;

  }

}
