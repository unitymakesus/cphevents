<?php
/**
* Plugin Name: Abandoned Cart Reports For WooCommerce
* Description: A simple plugin to see how many and what carts your customers are abandoning
* Version: 2.4.0
* Author: Small Fish Analytics
* Email: mike@smallfishanalytics.com
* WC requires at least: 3.0.0
* WC tested up to: 3.3.4
*/

if ( !defined( 'ABSPATH' ) ) { 
    exit; 
}

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	
	add_action('admin_menu', 'sfa_add_plugin_menu_item');
	add_action('plugins_loaded', 'sfa_abandoned_carts_install');
	
	include_once('class-sfa-abandoned-carts-recorder.php');
	$recorder = new SFA_Abandoned_Carts_Recorder();
}

function sfa_add_plugin_menu_item() {
	wp_register_style('sfa-abandoned-carts-style', plugin_dir_url(__FILE__) . '/assets/sfa-styles.css');
	wp_enqueue_style('sfa-abandoned-carts-style');
	
	add_submenu_page('woocommerce', 'WooCommerce Abandoned Cart Reports By Small Fish Analytics', 'SFA Abandoned Cart', 'manage_woocommerce', 'sfa-abandoned-carts', 'render_sfa_dashboard');
}

function render_sfa_dashboard(){
	if(isset($_REQUEST['sfa_report_start_date'])) {
		$start_date = $_REQUEST['sfa_report_start_date'];
	}
	else {
		$start_date = date('Y-m-d', strtotime(date('Y-m-d') . ' -30 days'));
	}
	
	if(isset($_REQUEST['sfa_report_end_date'])) {
		$end_date = $_REQUEST['sfa_report_end_date'];
	}
	else {
		$end_date = date('Y-m-d');
	}
	
	if(isset($_REQUEST['sfa_delete_carts']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'sfa_delete_carts')) {
		include_once('class-sfa-abandoned-carts-recorder.php');
		$recorder = new SFA_Abandoned_Carts_Recorder();
		$recorder->sfa_remove_all_data();
	}

	include_once('class-sfa-abandoned-carts-cart.php');
	include_once('class-sfa-abandoned-carts-item.php');
	include_once('admin/class-sfa-wp-list-table.php');
	include_once('admin/tables/class-sfa-abandoned-carts-table.php');
	
	include_once('admin/class-sfa-abandoned-carts-dashboard.php');
	$dashboard = new SFA_Abandoned_Carts_Dashboard($start_date, $end_date);
	
	include_once('admin/class-sfa-abandoned-carts-reports.php');
	$reports = new SFA_Abandoned_Carts_Reports($start_date, $end_date);
	
	if (isset($_GET['tab'])) {
		$tab = $_GET['tab'];
	}
	else {
		$tab = 'sfa_dashboard';
	}
	?> 
	<h1>WooCommerce Abandoned Cart Reports By Small Fish Analytics</h1>
	
	<h2 class="nav-tab-wrapper sfa_nav_tab_wrapper">
	    <a href="?page=sfa-abandoned-carts&tab=sfa_dashboard" class="nav-tab <?php echo($tab == 'sfa_dashboard' ? 'nav-tab-active sfa-nav-tab-active' : ''); ?>">Dashboard</a>
		<?php do_action('sfa-abandoned-carts-render-menu', $tab); ?>
		<a href="?page=sfa-abandoned-carts&tab=sfa_products" class="nav-tab <?php echo($tab == 'sfa_products' ? 'nav-tab-active sfa-nav-tab-active' : ''); ?>">Products</a>
		<a href="?page=sfa-abandoned-carts&tab=sfa_data" class="nav-tab <?php echo($tab == 'sfa_data' ? 'nav-tab-active sfa-nav-tab-active' : ''); ?>">Carts</a>
		<a href="?page=sfa-abandoned-carts&tab=sfa_help" class="nav-tab <?php echo($tab == 'sfa_help' ? 'nav-tab-active sfa-nav-tab-active' : ''); ?>">Help</a>
	</h2>
	<?php
	
	if ($tab == 'sfa_data') {
		$reports->render();
	}
	else if ($tab == 'sfa_help') {
		include_once('admin/class-sfa-abandoned-carts-help.php');
	}
	else if ($tab == 'sfa_funnel') {
		do_action('sfa-abandoned-carts-render-funnel-report', $start_date, $end_date);
	}
	else if ($tab == 'sfa_products') {
		include_once('admin/tables/class-sfa-abandoned-products-table.php');
		new SFA_Abandoned_Products_Table($start_date, $end_date);
	}
	else {
		$dashboard->render();
	}
	
}

function sfa_abandoned_carts_install() {
	
	$current_version = get_option('sfa_abandoned_carts_version');
	
	if ($current_version != '2.4.0') {
		
		global $wpdb;
	
		$table_name = $wpdb->prefix . "sfa_abandoned_carts";
		$charset_collate = $wpdb->get_charset_collate();
	
		$sql = "CREATE TABLE $table_name (
			id int AUTO_INCREMENT, 
			customer_key char(32) NOT NULL, 
			cart_contents longtext NOT NULL, 
			cart_expiry bigint(20) NOT NULL, 
			cart_is_recovered tinyint(1) NOT NULL, 
			ip_address char(32), 
			item_count int NOT NULL, 
			order_id int,
			viewed_checkout tinyint(1) NOT NULL DEFAULT 0,
			show_on_funnel_report tinyint(1) NOT NULL DEFAULT 0,
			cart_total decimal(15,2),
			PRIMARY KEY  (id)) $charset_collate;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	
		update_option('sfa_abandoned_carts_version', '2.4.0');
	}
}

