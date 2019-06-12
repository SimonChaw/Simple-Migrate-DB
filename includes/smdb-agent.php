<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 *
 * @package     SMDB
 * @subpackage  Classes/SMDB Agent
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

class SMDB_Agent {

  /**
	 * Purpose of the agent
	 *
	 * @access  public
	 * @since   1.0
   * @var string
	 */
	public $purpose;

  /**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function __construct() {
    $this->purpose = "Destroy. Rebuild. Start A new"
  }

}
