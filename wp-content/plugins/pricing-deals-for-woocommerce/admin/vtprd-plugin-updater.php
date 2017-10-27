<?php
/*
//v1.1.5 NEW FILE

 *  ALL LICENSING FUNCTIONS ARE PRO-ONLY
 *  AND DO NOT RUN WEHN ONLY THE FREE VERSION
 *  HOSTED AT WORDPRESS.ORG IS INSTALLED
 *  
 *  Installation and activation of tHE PURCHASABLE PRO VERSION ACTIVATES ALL LICENSING CODE   

*/


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Allows plugins to use their own update API.
 * Originally an EDD file by Pippin Williamson @version 1.6.2 
 * 
 *
 * VERSION CHECKING
 * 
 *  The system phones home at least 3 times for every version check.  To make this more efficient,
 *  keep track of current_version, new_version and last_check timestamp.
 *  
 *  Begin each function phoning home:  
 * if current_version == new_version and timestamp < 1 hour old, exit function
 *  at End of function
 * if version found > new_version, UPDATE new_version  
 * 
 *  Last call to me in sequence is show_update_notification.
 *    in show_update_notification,  
 *    if current_version < new_version 
 *      set current_version = new_version  
 *      update last_check timestamp        
 *    
 */
 
class VTPRD_Plugin_Updater {
	private $api_url   = '';    //v1.1.5 Load in PRO and pass here
	private $api_data  = array(); //v1.1.5 Load in PRO and pass here
	private $name      = '';
	private $slug      = '';
	private $version   = '';

	/**
	 * Class constructor.
	 *
	 * @uses plugin_basename()
	 * @uses hook()
	 *
	 * @param string  $_api_url     The URL pointing to the custom API endpoint.
	 * @param string  $_plugin_file Path to the plugin file.
	 * @param array   $_api_data    Optional data to send with API calls.
	 */
	function __construct( $_api_url, $_plugin_file, $_api_data = null ) {
		$this->api_url  = trailingslashit( VTPRD_STORE_URL );
		$this->api_data = $_api_data;
    //$this->name     = VTPRD_PRO_PLUGIN_ADDRESS ;  // commented out this definition, in favor of that BELOW:
		$this->name     = VTPRD_PRO_PLUGIN_FOLDER.'/'.VTPRD_PRO_PLUGIN_FILE ;
		$this->slug     = VTPRD_PRO_SLUG ;
		$this->version  = $_api_data['version'];

 
		// Set up hooks.
		$this->init();
    
    
  //Don't care about changelog, too much resources right now... 
	//	add_action( 'admin_init', array( $this, 'show_changelog' ) );

	}

	/**
	 * Set up WordPress filters to hook into WP's update process.
	 *
	 * @uses add_filter()
	 *
	 * @return void
	 */
	public function init() {
     //error_log( print_r(  'VTPRD_Plugin_Updater BEGIN init ' , true ) );   
   
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
		add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );

		remove_action( 'after_plugin_row_' . $this->name, 'wp_plugin_update_row', 10, 2 );
		add_action( 'after_plugin_row_' . $this->name, array( $this, 'show_update_notification' ), 10, 2 );
	}

	/**
	 * Check for Updates at the defined API endpoint and modify the update array.
	 *
	 * This function dives into the update API just when WordPress creates its update array,
	 * then adds a custom API call and injects the custom plugin data retrieved from the API.
	 * It is reassembled from parts of the native WordPress plugin update code.
	 * See wp-includes/update.php line 121 for the original wp_update_plugins() function.
	 *
	 * @uses api_request()
	 *
	 * @param array   $_transient_data Update array build by WordPress.
	 * @return array Modified update array with custom plugin data.
	 */
	function check_update( $_transient_data ) {

     //error_log( print_r(  'Begin check_update, $data= '  , true ) ); 
     //error_log( var_export($_transient_data, true ) );
     
    global $pagenow;
   //error_log( print_r(  'Begin check_update, $pagenow= ' .$pagenow, true ) ); 
		if( ! is_object( $_transient_data ) ) {
			$_transient_data = new stdClass;
		}

		if( 'plugins.php' == $pagenow && is_multisite() ) {     
			return $_transient_data;
		}


    //v1.1.6  BEGIN
    
    
    //v1.1.6 END



		//v1.1.5 begin verify - saves an additional call if already invalid...
  
    //apply to PRO plugin if active or inactive!!
    
    //however, if previously invalid, do not apply   
    global $vtprd_license_options;
    if (!$vtprd_license_options) {
      $vtprd_license_options = get_option( 'vtprd_license_options' ); 
    }   
    

    //$vtprd_license_options == get_option( 'vtprd_license_options' ); 
    if ( (isset($vtprd_license_options['status'])) && 
         ($vtprd_license_options['status'] == 'valid') && 
         ($vtprd_license_options['state']  == 'active') ) {
      $carry_on = true;
    } else {
      //admin messages have already been loaded in vtprd_get_other_options, just return 'no update' from here
      return $_transient_data;
    }    
   //demo licenses are never updated!!!!!!!!!
   if ( (isset($vtprd_license_options['prod_or_test'])) && 
        ($vtprd_license_options['prod_or_test'] == 'demo')  ) { 
      //admin messages have already been loaded in vtprd_get_other_options, just return 'no update' from here
      return $_transient_data;
    }    
    //v1.1.5 end


		if ( empty( $_transient_data->response ) || empty( $_transient_data->response[ $this->name ] ) ) {
			$version_info = $this->api_request( 'plugin_latest_version', array( 'slug' => $this->slug ) );

      global $vtprd_license_options; //v1.1.5 just in case
      $vtprd_license_options = get_option( 'vtprd_license_options' );  //v1.1.5 just in case
			if ( false !== $version_info && is_object( $version_info ) && isset( $version_info->new_version ) ) {

				if( version_compare( $this->version, $version_info->new_version, '<' ) ) {

					$_transient_data->response[ $this->name ] = $version_info;

    
          //v1.1.6.3  begin - refactored
              //v1.1.5 begin
              //$vtprd_license_options['plugin_current_version']  = $version_info->new_version;                    
              
              //v1.1.5 end
          update_option('vtprd_new_version_in_progress', $version_info->new_version);    
          update_option('vtprd_new_version_access_count', 3); //number of times Vark host can be called to effect the update!
          update_option('vtprd_license_count', 0 ); //clear error count to allow update to happen, just in case.
          
   //error_log( print_r(  'UPDATER new version FOUND', true ) );
    //error_log( print_r(  '$this->version,= ' .$this->version, true ) );
    //error_log( print_r(  '$version_info->new_version,= ' .$version_info->new_version, true ) );
              
          //v1.1.6.3  end

				}

				$_transient_data->last_checked = time();
				$_transient_data->checked[ $this->name ] = $this->version;

			}

		}
 
		return $_transient_data;
	}

	/**
	 * show update nofication row -- needed for multisite subsites, because WP won't tell you otherwise!
	 *
	 * @param string  $file
	 * @param array   $plugin
	 */
	public function show_update_notification( $file, $plugin ) {

     //error_log( print_r(  'Begin show_update_notification, $file= '  , true ) ); 
     //error_log( var_export($file, true ) );
     //error_log( var_export($plugin, true ) );
 
		if( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		if( ! is_multisite() ) {
			return;
		}

		if ( $this->name != $file ) {
			return;
		}

    //however, if previously invalid, do not apply   
    global $vtprd_license_options;
    if (!$vtprd_license_options) {
      $vtprd_license_options = get_option( 'vtprd_license_options' ); 
    }    
    //$vtprd_license_options == get_option( 'vtprd_license_options' ); 
    if ( (isset($vtprd_license_options['status'])) && 
         ($vtprd_license_options['status'] == 'valid') && 
         ($vtprd_license_options['state']  == 'active') ) {
      $carry_on = true;
    } else { 
      //admin messages have already been loaded in vtprd_get_other_options, just return 'no update' from here
      return;
    }
    //v1.1.5 end

		// Remove our filter on the site transient
		remove_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ), 10 );

		$update_cache = get_site_transient( 'update_plugins' );
		
		$update_cache = is_object( $update_cache ) ? $update_cache : new stdClass();

		if ( empty( $update_cache->response ) || empty( $update_cache->response[ $this->name ] ) ) {

			$cache_key    = md5( 'edd_plugin_' .sanitize_key( $this->name ) . '_version_info' );
			$version_info = get_transient( $cache_key );

			if( false === $version_info ) {

				$version_info = $this->api_request( 'plugin_latest_version', array( 'slug' => $this->slug ) );

				set_transient( $cache_key, $version_info, 3600 );
			}


			if( ! is_object( $version_info ) ) {
				return;
			}

			if( version_compare( $this->version, $version_info->new_version, '<' ) ) {

				$update_cache->response[ $this->name ] = $version_info;

			}

			$update_cache->last_checked = time();
			$update_cache->checked[ $this->name ] = $this->version;

			set_site_transient( 'update_plugins', $update_cache );

		} else {

			$version_info = $update_cache->response[ $this->name ];

		}

		// Restore our filter
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );

		if ( ! empty( $update_cache->response[ $this->name ] ) && version_compare( $this->version, $version_info->new_version, '<' ) ) {

			// build a plugin list row, with update notification
			$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
			echo '<tr class="plugin-update-tr"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange"><div class="update-message">';

			$changelog_link = self_admin_url( 'index.php?edd_sl_action=view_plugin_changelog&plugin=' . $this->name . '&slug=' . $this->slug . '&TB_iframe=true&width=772&height=911' );

			if ( empty( $version_info->download_link ) ) {
				printf(
					__( 'There is a new version of %1$s available. <a target="_blank" class="thickbox" href="%2$s">View version %3$s details</a>.', 'edd' ),
					esc_html( $version_info->name ),
					esc_url( $changelog_link ),
					esc_html( $version_info->new_version )
				);
			} else {
				printf(
					__( 'There is a new version of %1$s available. <a target="_blank" class="thickbox" href="%2$s">View version %3$s details</a> or <a href="%4$s">update now</a>.', 'edd' ),
					esc_html( $version_info->name ),
					esc_url( $changelog_link ),
					esc_html( $version_info->new_version ),
					esc_url( wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $this->name, 'upgrade-plugin_' . $this->name ) )
				);
			}

			echo '</div></td></tr>';
		}
	}


	/**
	 * Updates information on the "View version x.x details" page with custom data.
	 *
	 * @uses api_request()
	 *
	 * @param mixed   $_data
	 * @param string  $_action
	 * @param object  $_args
	 * @return object $_data
	 */
	function plugins_api_filter( $_data, $_action = '', $_args = null ) {

     //error_log( print_r(  'Begin plugins_api_filter,  $_action= ' . $_action , true ) ); 
     //error_log( var_export($_data, true ) );
     //error_log( var_export($_args, true ) );

		if ( $_action != 'plugin_information' ) {

			return $_data;

		}

		if ( ! isset( $_args->slug ) || ( $_args->slug != $this->slug ) ) {

			return $_data;

		}

		$to_send = array(
			'slug'   => $this->slug,
			'is_ssl' => is_ssl(),
			'fields' => array(
				'banners' => false, // These will be supported soon hopefully
				'reviews' => false
			)
		);

		$api_response = $this->api_request( 'plugin_information', $to_send );

		if ( false !== $api_response ) {
			$_data = $api_response;
		}


		return $_data;
	}


	/**
	 * Disable SSL verification in order to prevent download update failures
	 *
	 * @param array   $args
	 * @param string  $url
	 * @return object $array
	 */
	function http_request_args( $args, $url ) {
     //error_log( print_r(  'VTPRD_Plugin_Updater BEGIN http_request_args ' , true ) );    
		// If it is an https request and we are performing a package download, disable ssl verification
		if ( strpos( $url, 'https://' ) !== false && strpos( $url, 'edd_action=package_download' ) ) {
			$args['sslverify'] = false;
		}
		return $args;
	}


	/**
	 * Calls the API and, if successfull, returns the object delivered by the API.
	 *
	 * @uses get_bloginfo()
	 * @uses wp_remote_post()
	 * @uses is_wp_error()
	 *
	 * @param string  $_action The requested action.
	 * @param array   $_data   Parameters for the API action.
	 * @return false|object
	 */
	private function api_request( $_action, $_data ) {

     //error_log( print_r(  'BEGIN api_request, $_action= ' .$_action . ' $_data=' , true ) );  
     //error_log( var_export($_data, true ) );
    
    //v1.1.6.3  begin  - is Woocommerce installed
    if ( ! class_exists( 'WooCommerce' ) )  {
      return false;
    }
    //v1.1.6.3  end
    
		global $wp_version;

		$data = array_merge( $this->api_data, $_data );

		if ( $data['slug'] != $this->slug ) {
			return;
		}

		if( $this->api_url == home_url() ) {     
			return false; // Don't allow a plugin to ping itself
		}
      

		//v1.1.5 begin verify - saves an additional call if already invalid...
  
    //apply to PRO plugin if active or inactive!!
    
    //however, if previously invalid, do not apply   
    //$vtprd_license_options == get_option( 'vtprd_license_options' );
    /*
    global $vtprd_license_options;
    if (!$vtprd_license_options) {
      $vtprd_license_options = get_option( 'vtprd_license_options' ); 
    } 
    */ 
    global $vtprd_license_options; //v1.1.6
    $vtprd_license_options = get_option( 'vtprd_license_options' );  
    //$vtprd_license_options == get_option( 'vtprd_license_options' ); 
    
    //SO we START with ONLY valid and active licenses!
    if ( (isset($vtprd_license_options['status'])) && 
         ($vtprd_license_options['status'] == 'valid') && 
         ($vtprd_license_options['state']  == 'active') ) {   
      $carry_on = true;
    } else {
			return false; 
    }


/*  
    //TEST for duplicative api call testing - only do this ONCE per 10 second interval:
    $today= time(); 
    if (($today - $vtprd_license_options['last_successful_rego_ts']) < 10)  { 
       //error_log( print_r(  'api_request time interval exit, Exit 0002a' , true ) );
			return false; 
    }
*/
            
    //v1.1.5 end
/*
CLIENT - All the Plugin Updater does is run the function - api_request -.
By **hardcode**, all that function does is send a -'get_version' - request.

HOST 
SOOOO the default check_license is NEVER done...

get_version ==>> get_latest_version_remote
which does NOT do check_license ...


		// data to send in our API request
		$api_params = array(
			'edd_action'   => $action,
			'license' 	   => $license,
			'item_name'    => urlencode( VTPRD_ITEM_NAME ), // the name of our product in VTPRD
      'item_id'      => urlencode( VTPRD_ITEM_ID ), // the name of our product in VTPRD
			'url'          => $url,
      'prod_or_test' => $prod_or_test,
      'test_url'     => $test_url,
      'email'        => urlencode($email),
      'ip_address'   => vtprd_get_ip_address()     
		);


vark_get_latest_version_remote
*/  

    //********************************************
    //$url ALWAYS has the PROD url in it 
    //********************************************  
    if ($vtprd_license_options['prod_or_test'] == 'prod') {
      $url = $vtprd_license_options['url'];
      $test_url = '';    
    } else {    
      $test_url = $vtprd_license_options['url']; 
      $url = $vtprd_license_options['prod_url_supplied_for_test_site']; 
    }

    
		$api_params = array(
			'edd_action' => 'get_version',
			//'license'    => ! empty( $data['license'] ) ? $data['license'] : '',  //v1.1.5
      'license'    => trim( $vtprd_license_options['key'] ) ,  //v1.1.5
			'item_name'  => urlencode( VTPRD_ITEM_NAME ) ,
			'item_id'    => VTPRD_ITEM_ID  ,
			'slug'       => $data['slug'],
			'author'     => $data['author'],
			'url'        => $url,
      'prod_or_test'     =>  $vtprd_license_options['prod_or_test'],
      'test_url'         =>  $test_url,
      'email'            =>  $vtprd_license_options['email'],
      'ip_address'       =>  vtprd_get_ip_address()      
 
      
		);
        
    //v1.1.6.3 begin
    if (defined('VTPRD_PRO_VERSION')) { 
      $version = VTPRD_PRO_VERSION;
    } else {
      global $vtprd_setup_options;
      $version   = $vtprd_setup_options['current_pro_version'];
    } 
    //v1.1.6.3 end   
        
  //v1.1.6 begin 
	//	$request = wp_remote_post( VTPRD_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

      $request = wp_remote_post( VTPRD_STORE_URL, array(
    			'method' => 'POST',
    			'timeout' => 45,
    			'redirection' => 5,
    			'httpversion' => '1.0',
    			//v1.1.6.3 switched to array value for PRO version, to allow for DEACTIVATED plugin -> this already containse the version from $plugin_data['Version']
          'headers' => array( 'user-agent' => 'Aardvark/Updater/vtprd/Free/V' . VTPRD_VERSION . '/Pro/V' . $version  .';'. $vtprd_license_options['url'] ),
          //'headers' => array( 'user-agent' => 'Aardvark/Updater/vtprd/Free/V' . VTPRD_VERSION . '/Pro/V' . VTPRD_PRO_VERSION .';'. $vtprd_license_options['url'] ),
    			'body' => $api_params,
    			'sslverify' => false
    			) );
      /*
      from woothemes-updater/classes/class-woothemes-update-checker.php	
      	$request = wp_remote_post( ( $api == 'info' ) ? $this->api_url : $this->update_check_url, array(
      			'method' => 'POST',
      			'timeout' => 45,
      			'redirection' => 5,
      			'httpversion' => '1.0',
      			'headers' => array( 'user-agent' => 'WooThemesUpdater/' . $this->version ),
      			'body' => $args,
      			'sslverify' => false
      			) );

        */
  //v1.1.6 end 


   
   //error_log( print_r(  'api_request after wp_remote_post, $request= ' .$_action , true ) );  
   //error_log( var_export($request, true ) );
   //error_log( var_export($api_params, true ) );
 
		if ( ! is_wp_error( $request ) ) {
			$request = json_decode( wp_remote_retrieve_body( $request ) );
		} else {
      /*
      $vtprd_license_options['status']  = 'invalid';
      $vtprd_license_options['state']   = 'pending';
      $vtprd_license_options['last_failed_rego___ts'] = time(); 
      $vtprd_license_options['last_failed_rego___date_time'] = date("Y-m-d H:i:s");
      $vtprd_license_options['diagnostic_msg'] = 'error contacting host, please try again'; 
      update_option('vtprd_license_options', $vtprd_license_options);
      */
      return 'false';   
    }
    //v1.1.6 begin
    //used to control license checks during Admin and Cron
    $today = time();
    update_option('vtprd_last_license_check_ts', $today);
    //v1.1.6 end
 
    //global $vtprd_license_options; //just in case
    $vtprd_license_options = get_option( 'vtprd_license_options' ); //just in case
    
    //date time stamp
    If ($request->status == 'valid') {
      $vtprd_license_options['last_successful_rego_ts'] = $today; //v1.1.6 
      $vtprd_license_options['last_successful_rego_date_time'] = date("Y-m-d H:i:s");
      
      //Can't update vtprd_license_options here, things explode!! Store for update in main plugin php file
      update_option('vtprd_license_checked', $vtprd_license_options);
      
    } else {
      $vtprd_license_options['last_failed_rego___ts'] = $today; //v1.1.6 
      $vtprd_license_options['last_failed_rego___date_time'] = date("Y-m-d H:i:s");     
    } 
    
   //error_log( print_r(  'api_request*2* after wp_remote_post, $request*2*= ' .$_action , true ) );  
   //error_log( var_export($request, true ) );

    //v1.1.5 begin     
    if ($request->status == 'invalid') {
      // v1.1.6 pulled out of here, put in mainline - now picked up when 'vtprd_license_suspended' is processed
      /*
      if ($request->state == 'suspended-by-vendor') {
        vtprd_deactivate_pro_plugin();
        vtprd_increment_license_count();       
      }
      */ 
      
      $vtprd_license_options['status']  = $request->status;
      $vtprd_license_options['state']   = $request->state;
      $vtprd_license_options['strikes'] = $request->strikes;
      
      //******************************
      //v1.1.6 begin  refactored
      if (isset($request->diagnostic_msg)) {
        $vtprd_license_options['diagnostic_msg'] =  $request->diagnostic_msg;
      } else {
        if (isset($request->verify_response)) {
          $vtprd_license_options['diagnostic_msg'] =  $request->verify_response;
        }       
      }
      //$vtprd_license_options['diagnostic_msg'] = $request->diagnostic_msg;

      $vtprd_license_options['msg'] = $request->msg; 
      $vtprd_license_options['expires'] = $request->expires;  
      $vtprd_license_options['last_response_from_host'] = $request;  
      $vtprd_license_options['last_failed_rego_ts'] = time();   
      $vtprd_license_options['last_failed_rego_date_time'] = date("Y-m-d H:i:s");    
      //v1.1.6 end
      //******************************
  
       //Can't update vtprd_license_options here, things explode!! Store for update in main plugin php file
      update_option('vtprd_license_suspended', $vtprd_license_options);
      
      

  
      return 'false';
    }
          

      //update for date time stamp 
      
 
/* TEST TEST TEST    
    if ( (get_option('vtprd_host_has_new_version') !== false ) //v1.1.6.3
    
    //version update 1st time, nothing to download
    if ($vtprd_license_options['plugin_current_version'] <= ' ') {
      $vtprd_license_options['plugin_current_version'] = $request->new_version;
      update_option('vtprd_license_options', $vtprd_license_options);
      return 'false';
    } 
    
    //IF NO CHANGE, nothing to do.
    if ($vtprd_license_options['plugin_current_version'] == $request->new_version) {
      return 'false';
    }     
*/
    //v1.1.5 end

		if ( $request && isset( $request->sections ) ) {
			$request->sections = maybe_unserialize( $request->sections );
       
		} else {
    
			$request = false;
		}
/*
    //error_log( print_r(  'after wp_remote_retrieve_body prod_or_test 003= ' .$vtprd_license_options['prod_or_test'] , true ) ); 


$vtprd_license_options2 = get_option( 'vtprd_license_options' ); //just in case
   //error_log( print_r(  'api_request, Exit 0005, $vtprd_license_options2 = ' , true ) );
   //error_log( var_export($vtprd_license_options2, true ) ); 
 
 global $vtprd_license_options; //just in case
 $vtprd_license_options = $vtprd_license_options2;
*/ 
		return $request;
	}

	//v1.1.5  add_action for this currently commented out!!
  public function show_changelog() {
    
    global $vtprd_license_options;
    if (!$vtprd_license_options) {
      $vtprd_license_options = get_option( 'vtprd_license_options' ); 
    }    
    //$vtprd_license_options == get_option( 'vtprd_license_options' ); 
    if ( (isset($vtprd_license_options['status'])) && 
         ($vtprd_license_options['status'] == 'valid') && 
         ($vtprd_license_options['state']  == 'active') ) {
      $carry_on = true;
    } else {
      //admin messages have already been loaded in vtprd_get_other_options, just return 'no update' from here
      return;
    }
    //v1.1.5 end

		if( empty( $_REQUEST['edd_sl_action'] ) || 'view_plugin_changelog' != $_REQUEST['edd_sl_action'] ) {
			return;
		}

		if( empty( $_REQUEST['plugin'] ) ) {
			return;
		}

		if( empty( $_REQUEST['slug'] ) ) {
			return;
		}

		if( ! current_user_can( 'update_plugins' ) ) {
			wp_die( __( 'You do not have permission to install plugin updates', 'edd' ), __( 'Error', 'edd' ), array( 'response' => 403 ) );
		}

		$response = $this->api_request( 'plugin_latest_version', array( 'slug' => $_REQUEST['slug'] ) );

		if( $response && isset( $response->sections['changelog'] ) ) {
			echo '<div style="background:#fff;padding:10px;">' . $response->sections['changelog'] . '</div>';
		}


		exit;
	}



} //end class
