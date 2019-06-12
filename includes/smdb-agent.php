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
	 * Target folder to wipe out
	 *
	 * @access  public
	 * @since   1.0
   * @var string
	 */
	public $target_folder;

  /**
	 * Old site Manifest - used to hold things like DB Info, Site PATH
	 *
	 * @access  public
	 * @since   1.0
   * @var array
	 */
	public $old_manifest;

  /**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function __construct($target_folder, $manifest) {
    $this->purpose = "Destroy. Rebuild. Start anew.";
    $this->$target_folder = $target_folder;
    $this->old_manifest = $manifest;
  }

  /**
	 * Erases current site.
	 *
	 * @access  public
	 * @since   1.0
	 */
  public function erase(){
    // Most likely will be removing /var/www/ or /var/www/html
    $this->recursiveRemove($this->old_manifest['SERVER_ROOT'])
    $this->rebuild();
  }

  public function recursiveRemove($dir){
    $structure = glob(rtrim($dir, "/").'/*');
    if (is_array($structure)) {
        foreach($structure as $file) {
            if (is_dir($file)) $this->recursiveRemove($file);
            elseif (is_file($file)) unlink($file);
        }
    }
  }

  /**
	 * Rebuilds migrated site from zip.
	 *
	 * @access  public
	 * @since   1.0
	 */
  public function rebuild(){
    $package = new ZipArchive();
    // Find package
    $res = $package->open('package.zip');
    if ($res === TRUE) {
      $package->extractTo($this->old_manifest['SERVER_ROOT']);
      $package->close();
    }

    // Run SQL export
    $command = "mysql --user={$this->old_manifest['DB_USER']} --password='{$this->old_manifest['DB_USER']}' "
     . "-h {$this->old_manifest['DB_HOST']} -D {$this->old_manifest['DB_NAME']} < ";
     
    $output = shell_exec($command . 'export.sql');

    $this->cleanup();
  }

  /**
	 * Removes the zip package, export sql and deletes this file from where it's not supposed to be.
	 *
	 * @access  public
	 * @since   1.0
	 */
  public function cleanup(){
    // Remove sql backup and package zip.
    unlink('package.zip');
    unlink('export.sql');
    // Goodbye world.
    unlink(__FILE__);
  }


}
