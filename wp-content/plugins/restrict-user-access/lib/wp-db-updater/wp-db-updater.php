<?php
/**
 * @package wp-db-updater
 * @version 1.0
 * @copyright Joachim Jensen <jv@intox.dk>
 * @license GPLv3
 */

if(!class_exists("WP_DB_Updater")) {
	class WP_DB_Updater {

		/**
		 * Required capability to run updates
		 */
		const CAPABILITY  = "update_plugins";

		/**
		 * Key where installed_version is stored
		 * @var string
		 */
		protected $version_key;

		/**
		 * Version in database
		 * @var string
		 */
		protected $installed_version;

		/**
		 * Version of plugin
		 * @var string
		 */
		protected $plugin_version;

		/**
		 * Versions to be installed
		 * @var array
		 */
		protected $versions = array();

		/**
		 * Constructor
		 *
		 * @since 1.0
		 * @param string  $version_key
		 * @param string  $plugin_version
		 */
		public function __construct($version_key,$plugin_version) {
			$this->version_key = $version_key;
			$this->plugin_version = $plugin_version;
			
			add_action('wp_loaded',array($this,'run'));
		}

		/**
		 * Run updates
		 *
		 * @since  1.0
		 * @return void
		 */
		public function run() {
			if(current_user_can(self::CAPABILITY)) {
				$install_success = true;
				// Check if database is up to date
				if(!$this->is_version_installed($this->plugin_version)) {
					//Run update installations
					foreach ($this->versions as $version => $callback) {
						if(!$this->is_version_installed($version) && function_exists($callback)) {
							$install_success = $callback();
							if($install_success) {
								$this->set_installed_version($version);
							} else {
								break;
							}
						}
					}
				}
				//If no update exist for current version, just set it
				if($install_success && !$this->is_version_installed($this->plugin_version)) {
					$this->set_installed_version($this->plugin_version);
				}
			}
		}

		/**
		 * Register versions and their callbacks
		 * to update queue
		 *
		 * @since  1.0
		 * @param  string  $version
		 * @param  string  $callback
		 * @return void
		 */
		public function register_version_update($version,$callback) {
			$this->versions[$version] = $callback;
		}

		/**
		 * Get installed version locally
		 * Fetches installed version from database
		 * on first use
		 *
		 * @since  1.0
		 * @return string
		 */
		protected function get_installed_version() {
			return $this->installed_version != null ? $this->installed_version : $this->fetch_installed_version();
		}

		/**
		 * Fetch installed version from database
		 * and refresh locally
		 *
		 * @since  1.0
		 * @return string
		 */
		protected function fetch_installed_version() {
			return ($this->installed_version = get_option($this->version_key,'0'));
		}

		/**
		 * Set installed version locally and in db
		 *
		 * @since 1.0
		 * @param string  $version
		 */
		protected function set_installed_version($version) {
			$this->installed_version = $version;
			$this->sync_installed_version();
		}

		/**
		 * Sync local installed version with db
		 *
		 * @since  1.0
		 * @return void
		 */
		protected function sync_installed_version() {
			update_option($this->version_key,$this->installed_version);
		}

		/**
		 * Check if a version is installed in db
		 *
		 * @since  1.0
		 * @param  string  $version
		 * @return boolean
		 */
		protected function is_version_installed($version) {
			return version_compare($this->get_installed_version(),$version,">=");
		}
	}
}

//eol