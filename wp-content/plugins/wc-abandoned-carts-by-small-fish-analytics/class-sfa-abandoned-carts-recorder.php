<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

include_once('libraries/crawler-detect/CrawlerDetect.php');
include_once('libraries/crawler-detect/Fixtures/AbstractProvider.php');
include_once('libraries/crawler-detect/Fixtures/Crawlers.php');
include_once('libraries/crawler-detect/Fixtures/Exclusions.php');
include_once('libraries/crawler-detect/Fixtures/Headers.php');
use Jaybizzle\CrawlerDetect\CrawlerDetect;
	
class SFA_Abandoned_Carts_Recorder
{
	function __construct() {
		add_action('woocommerce_cart_updated', array($this, 'sfa_update_data'));
		add_action('woocommerce_new_order', array($this, 'sfa_complete_purchase'));
		add_action('wp_authenticate', array($this, 'sfa_user_logged_on'));
		add_action('user_register', array($this, 'sfa_user_signed_up'));
		add_filter('woocommerce_checkout_fields', array($this, 'sfa_viewed_checkout')); 
	}

	public function sfa_viewed_checkout($fields) {
		$this->sfa_update_data();
		return $fields;
	}
	
	public function sfa_complete_purchase($order_id) {
	
		global $wpdb;
		global $woocommerce;
	
		if (is_user_logged_in()) {
			$customer_id = get_current_user_id();
		}
		else {
			//Can't look up the customer in this situation.
			if (!isset($woocommerce->session)) {
				return;
			}

			$customer_id = $woocommerce->session->get_customer_id();
		}
	
		$table_name = $wpdb->prefix . "sfa_abandoned_carts";
	
		$row = $wpdb->get_row('SELECT * FROM ' . $table_name . ' WHERE customer_key = \'' 
			. $customer_id . '\' AND cart_is_recovered = 0 AND order_id IS NULL LIMIT 1', OBJECT);
	
		if (time() - 600 < $row->cart_expiry) {
			$wpdb->update(
				$table_name,
				array(
					'cart_expiry' => time(),
					'order_id' => $order_id
				),
				array(
					'id' => $row->id
				),
				array(
					'%s',
					'%d'
				)
			);
		} 
		else {
			$wpdb->update(
				$table_name,
				array(
					'cart_is_recovered' => 1,
					'cart_expiry' => time(),
					'order_id' => $order_id
				),
				array(
					'id' => $row->id
				),
				array(
					'%s',
					'%d',
					'%d'
				)
			);
		}
	}

	public function sfa_user_logged_on($username) {
		if ($username) {
			$user = get_user_by('login', $username);
			if ($user) {
				$this->sfa_user_signed_up($user->ID);
			}
		}
	}
	
	public function sfa_user_signed_up($user_id) {
		global $wpdb;
		global $woocommerce;
		
		//Don't create a record unless a user is logging in with something in their cart
		if (!$woocommerce->cart->cart_contents) {
			return;
		}

		//Can't look up the customer in this situation.
		if (!isset($woocommerce->session)) {
			return;
		}
		
		$customer_id = $woocommerce->session->get_customer_id();

		$table_name = $wpdb->prefix . "sfa_abandoned_carts";
	
		$row = $wpdb->get_row('SELECT * FROM ' . $table_name . ' WHERE customer_key = \'' 
			. $customer_id . '\' AND cart_is_recovered = 0 AND order_id IS NULL LIMIT 1', OBJECT);
		
		if ($row) {
			$wpdb->query(
				'DELETE FROM ' . $table_name . 
				' WHERE customer_key = \'' . $user_id . 
				'\' AND cart_is_recovered = 0 AND order_id IS NULL');

			$wpdb->update(
				$table_name,
				array(
					'customer_key' => $user_id
				),
				array(
					'id' => $row->id
				),
				array(
					'%s'
				)
			);
		}
	}

	public function sfa_record_cart_total($total, $cart_id) {
		global $wpdb;
		
		$table_name = $wpdb->prefix . "sfa_abandoned_carts";

		$wpdb->update(
			$table_name,
			array(
				'cart_total' => $total
			),
			array(
				'id' => $cart_id
			),
			array(
				'%d'
			)
		);
	}
	
	public function sfa_remove_all_data() {
		global $wpdb;
		global $woocommerce;
		
		$table_name = $wpdb->prefix . 'sfa_abandoned_carts';
		
		$wpdb->query('TRUNCATE TABLE ' . $table_name);
	}

	public function sfa_update_data() {
	
		global $woocommerce;
		$cart = $woocommerce->cart;
	
		global $wpdb;
		
		if (is_user_logged_in()) {
			$customer_id = get_current_user_id();
		}
		else {
			//Can't look up the customer in this situation.
			if (!isset($woocommerce->session)) {
				return;
			}

			$customer_id = $woocommerce->session->get_customer_id();
		}
	
		$table_name = $wpdb->prefix . "sfa_abandoned_carts";
	
		$row = $wpdb->get_row('SELECT * FROM ' . $table_name . ' WHERE customer_key = \'' 
			. $customer_id . '\' AND cart_is_recovered = 0 AND order_id IS NULL LIMIT 1', OBJECT);
	
		if ($row == null) {
		
			$crawler_detect = new CrawlerDetect;
		
			if ($crawler_detect->isCrawler()) {
				return;
			}

			//Don't create a record unless a user has something in their cart
			if (!$cart->cart_contents) {
				return;
			}
		
			$wpdb->insert($table_name,
				array(
					'customer_key' => $customer_id,
					'cart_contents' => serialize($cart->get_cart()),
					'cart_expiry' => time() + 600,
					'cart_is_recovered' => 0,
					'show_on_funnel_report' => 1,
					'ip_address' => $_SERVER['REMOTE_ADDR'],
					'item_count' => $cart->cart_contents_count,
					'cart_total' => $cart->cart_contents_total
				),
				array(
					'%s',
					'%s',
					'%d',
					'%d',
					'%d',
					'%s',
					'%d'
				)
			);
		}
		else {
			$update_values = null;
		
			if (is_checkout() || time() - 600 > $row->cart_expiry) {
				$update_values = array(
					'cart_contents' => serialize($cart->get_cart()),
					'ip_address' => $_SERVER['REMOTE_ADDR'],
					'item_count' => $cart->cart_contents_count,
					'cart_total' => $cart->cart_contents_total,
					'viewed_checkout' => true
				);
			}
			else {
				$update_values = array(
					'cart_contents' => serialize($cart->get_cart()),
					'cart_expiry' => time(),
					'ip_address' => $_SERVER['REMOTE_ADDR'],
					'item_count' => $cart->cart_contents_count,
					'cart_total' => $cart->cart_contents_total
				);
			}
		
			$wpdb->update(
				$table_name,
				$update_values,
				array(
					'id' => $row->id
				)
			);
		} 
	}
}
?>