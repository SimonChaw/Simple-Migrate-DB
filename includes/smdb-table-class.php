<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Table class for preparing data
 *
 * @package     SMDB
 * @subpackage  Classes/SMDB DB Table
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

class SMDB_Table{

  /**
	 * Name of table
	 *
	 * @access  public
	 * @since   1.0
	 */
  public $name;

  /**
	 * Array of column names
	 *
	 * @access  public
	 * @since   1.0
	 */
  public $columns;

  /**
	 * Array of table data
	 *
	 * @access  public
	 * @since   1.0
	 */
  public $rows;

  public function __construct(){
    $this->columns = array();
    $this->rows = array();
  }

}
