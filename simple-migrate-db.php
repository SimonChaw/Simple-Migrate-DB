<?php
/**
 * Plugin Name: Simple Migrate DB
 * Plugin URI: https://github.com/SimonChaw/simple-migrate-db/
 * Description: A simple tool for migrating wordpress databases from two different sites
 * Author: Simon Chawla
 * Author URI: https://simonchawla.com
 * Version: 1.0.0
 * Text Domain: simple-migrate-db
 *
 * Simple Migrate DB is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Simple Migrate DB is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * @package SMDB
 * @category Core
 * @author Simon Chawla
 * @version 1.0.0
 */

 // Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Simple_Migrate_DB' ) ) :

/**
 * Main Class.
 *
 * @since 1.0
 */
final class Simple_Migrate_DB {
	/**
	 * @since 1.0
	 */
	private static $instance;

  /**
	 * SMDB Database Handler Object.
	 *
	 * @var object|SMDB_DB_Handler
	 * @since 1.0
	 */
  public $dbhandler;

	/**
	 * SMDB HTML Session Object.
	 *
	 * For holding transfered DB
	 *
	 * @var object|SMDB_Session
	 * @since 1.5
	 */
	public $session;

	/**
	 * SMDB secure key.
	 *
	 * For authenticating external interactions
	 *
	 * @var object|SMDB_Secure_key
	 * @since 1.5
	 */
	public $securekey;

  /**
	 * Main Simple_Migrate_DB Instance.
	 *
	 * Insures that only one instance of Simple_Migrate_DB exists in memory at any one
	 * time. Removes the need for Globals
	 *
	 * @since 1.0
	 * @static
	 * @staticvar array $instance
	 * @uses Simple_Migrate_DB::includes() Include the required files.
	 * @return object|Simple_Migrate_DB
	 */
	public static function instance() {
      if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Simple_Migrate_DB ) ) {
  			self::$instance = new Simple_Migrate_DB;
  			self::$instance->setup_constants();
  			self::$instance->includes();
				self::$instance->setup_secure_key();
  			self::$instance->dbhandler = new SMDB_DB_Handler();
  		}
	    return self::$instance;
	}

  /**
	 * Setup plugin constants.
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function setup_constants() {
		// Plugin version.
		if ( ! defined( 'SMDB_VERSION' ) ) {
			define( 'SMDB_VERSION', '1.0.0' );
		}
		// Plugin Folder Path.
		if ( ! defined( 'SMDB_PLUGIN_DIR' ) ) {
			define( 'SMDB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}
		// Plugin Folder URL.
		if ( ! defined( 'SMDB_PLUGIN_URL' ) ) {
			define( 'SMDB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}
		// Plugin Root File.
		if ( ! defined( 'SMDB_PLUGIN_FILE' ) ) {
			define( 'SMDB_PLUGIN_FILE', __FILE__ );
		}

		// Meta Data Keys
		if ( ! defined( 'SMDB_METAKEY_SECURE_KEY' )) {
			define( 'SMDB_METAKEY_SECURE_KEY', 'SMDB_SECURE_KEY' );
		}
	}

	/**
	 * Generate secure key for allowing access between WP sites
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function setup_secure_key() {

		// Check if secure key already exists.
		$id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_title = 'SMDB' AND post_type = 'SMDB'" );

		if (! $id ) {
			// No post found, we have to setup.

			// Create post.
			$post_id = self::$instance->create_smdb_db_entry();

			// Generate secure key.
			self::$instance->securekey = bin2hex(random_bytes(16));

			// Store secure key
			add_post_meta($post_id, SMDB_META_SECURE_KEY, self::$instance->securekey, true);

		} else {

			// Secure key has already been setup. Let's get it.
			self::$instance->securekey = get_post_meta($post_id, SMDB_METAKEY_SECURE_KEY, 'SMDB_SECURE_KEY', )
		}
	}

	/**
	 * Setup function, used to make an entry so we have a post ID
	 * to associate with all our meta data
	 *
	 * @access private
	 * @since 1.0
	 * @return mixed
	 */
	private function create_smdb_db_entry() {
		$postarr = [
			'post_title' => 'SMDB',
			'post_type' => 'SMDB'
		];

		$post_id = wp_insert_post($postarr);

		if ($post_id != 0) {
			// Post successfully created.
			return $post_id;
		} else {
			return false;
		}
	}

  /**
	 * Include required files.
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function includes() {
    //PLUGIN_DIR . 'includes/install.php';
		//API
		require_once SMDB_PLUGIN_DIR . 'includes/api/smdb-api.php';
    //ADMIN
		require_once SMDB_PLUGIN_DIR . 'includes/actions.php';
		require_once SMDB_PLUGIN_DIR . 'includes/scripts.php';
    require_once SMDB_PLUGIN_DIR . 'includes/admin/admin_page.php';
		//DATABASE
		require_once SMDB_PLUGIN_DIR . 'includes/smdb-table-class.php';
    require_once SMDB_PLUGIN_DIR . 'includes/smdb-db-handler.php';
	}


}

endif; // End if class_exists check.

/**
 * Main function for accessing Simple_Migrate_DB instance
 *
 * @since 1.0
 * @return object|Simple_Migrate_DB The one true Simple_Migrate_DB Instance.
 */
function SMDB() {
	return Simple_Migrate_DB::instance();
}
// Get SMDB Running.
SMDB();
