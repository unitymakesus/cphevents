<?php
/*
Plugin Name: WooCommerce Product Bundle Premium
Description: WooCommerce Product Bundle is a plugin help you bundle a few products with pre-defined quantity, offer them at a discount and watch the sales go up!
Version: 2.5.2
Author: WPclever.net
Author URI: https://wpclever.net
Text Domain: woosb
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WOOSB_URI', plugin_dir_url( __FILE__ ) );
define( 'WOOSB_VERSION', '2.5.2' );
define( 'WOOSB_PRO_URL', 'https://wpclever.net/downloads/woocommerce-product-bundle' );
define( 'WOOSB_PRO_PRICE', '$19' );

add_action( 'init', 'woosb_register_product_type' );

function woosb_register_product_type() {
	if ( class_exists( 'WC_Product' ) ) {
		class WC_Product_Woosb extends WC_Product {
			public function __construct( $product = 0 ) {
				$this->supports[] = 'ajax_add_to_cart';
				parent::__construct( $product );
			}

			public function get_type() {
				return 'woosb';
			}

			public function add_to_cart_url() {
				$product_id = $this->id;
				if ( $this->is_purchasable() && $this->is_in_stock() && ! $this->has_variables() ) {
					$url = remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $product_id ) );
				} else {
					$url = get_permalink( $product_id );
				}

				return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
			}

			public function add_to_cart_text() {
				if ( $this->is_purchasable() && $this->is_in_stock() && ! $this->has_variables() ) {
					$text = esc_html__( 'Add to cart', 'woosb' );
				} else {
					$text = esc_html__( 'Select options', 'woosb' );
				}

				return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
			}

			public function is_on_sale( $context = 'view' ) {
				$product_id = $this->id;
				if ( ( $woosb_price_percent = get_post_meta( $product_id, 'woosb_price_percent', true ) ) && is_numeric( $woosb_price_percent ) && ( intval( $woosb_price_percent ) < 100 ) && ( intval( $woosb_price_percent ) > 0 ) ) {
					return true;
				}

				return parent::is_on_sale( $context );
			}

			public function is_in_stock() {
				$product_id  = $this->id;
				$woosb_items = explode( ',', get_post_meta( $product_id, 'woosb_ids', true ) );
				if ( is_array( $woosb_items ) && count( $woosb_items ) > 0 ) {
					foreach ( $woosb_items as $woosb_item ) {
						$woosb_item_arr = explode( '/', $woosb_item );
						$woosb_item_id  = $woosb_item_arr[0] ? $woosb_item_arr[0] : 0;
						$woosb_product  = wc_get_product( $woosb_item_id );
						if ( $woosb_product && ( ! $woosb_product->is_in_stock() ) ) {
							return false;
						}
					}
				}

				return parent::is_in_stock();
			}

			// extra
			public function has_variables() {
				$product_id = $this->id;
				if ( ( $woosb_ids = get_post_meta( $product_id, 'woosb_ids', true ) ) ) {
					$woosb_items = explode( ',', $woosb_ids );
					if ( is_array( $woosb_items ) && count( $woosb_items ) > 0 ) {
						foreach ( $woosb_items as $woosb_item ) {
							$woosb_item_arr     = explode( '/', $woosb_item );
							$woosb_item_id      = $woosb_item_arr[0] ? $woosb_item_arr[0] : 0;
							$woosb_item_product = wc_get_product( $woosb_item_id );
							if ( $woosb_item_product->is_type( 'variable' ) ) {
								return true;
								break;
							}
						}
					}
				}

				return false;
			}
		}
	}
}

if ( ! class_exists( 'WooSB' ) ) {
	class WooSB {
		function __construct() {
			// Enqueue frontend scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'woosb_wp_enqueue_scripts' ) );

			// Enqueue backend scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'woosb_admin_enqueue_scripts' ) );

			// Backend AJAX search
			add_action( 'wp_ajax_woosb_get_search_results', array( $this, 'woosb_get_search_results' ) );

			// Add to selector
			add_filter( 'product_type_selector', array( $this, 'woosb_product_type_selector' ) );

			// Product data tabs
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'woosb_product_data_tabs' ), 10, 1 );

			// Product data panels
			add_action( 'woocommerce_product_data_panels', array( $this, 'woosb_product_data_panels' ) );
			add_action( 'woocommerce_process_product_meta_woosb', array( $this, 'woosb_save_option_field' ) );

			// Add to cart form & button
			add_action( 'woocommerce_woosb_add_to_cart', array( $this, 'woosb_add_to_cart_form' ) );
			add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'woosb_add_to_cart_button' ) );

			// Add to cart
			add_action( 'woocommerce_add_to_cart', array( $this, 'woosb_add_to_cart' ), 10, 6 );
			add_filter( 'woocommerce_add_cart_item', array( $this, 'woosb_add_cart_item' ), 10, 1 );
			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'woosb_add_cart_item_data' ), 10, 2 );
			add_filter( 'woocommerce_get_cart_item_from_session', array(
				$this,
				'woosb_get_cart_item_from_session'
			), 10, 2 );

			// Cart item
			add_filter( 'woocommerce_cart_item_name', array( $this, 'woosb_cart_item_name' ), 10, 2 );
			add_filter( 'woocommerce_cart_item_price', array( $this, 'woosb_cart_item_price' ), 10, 3 );
			add_filter( 'woocommerce_cart_item_quantity', array( $this, 'woosb_cart_item_quantity' ), 1, 2 );
			add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'woosb_cart_item_subtotal' ), 10, 3 );
			add_filter( 'woocommerce_cart_item_remove_link', array( $this, 'woosb_cart_item_remove_link' ), 10, 3 );
			add_filter( 'woocommerce_cart_contents_count', array( $this, 'woosb_cart_contents_count' ) );
			add_action( 'woocommerce_after_cart_item_quantity_update', array(
				$this,
				'woosb_update_cart_item_quantity'
			), 10, 2 );
			add_action( 'woocommerce_before_cart_item_quantity_zero', array(
				$this,
				'woosb_update_cart_item_quantity'
			), 10, 2 );
			add_action( 'woocommerce_cart_item_removed', array( $this, 'woosb_cart_item_removed' ), 10, 2 );

			// Checkout item
			add_filter( 'woocommerce_checkout_item_subtotal', array( $this, 'woosb_cart_item_subtotal' ), 10, 3 );

			// Hide on cart & checkout page
			if ( get_option( '_woosb_hide_bundled', 'no' ) == 'yes' ) {
				add_filter( 'woocommerce_cart_item_visible', array( $this, 'woosb_item_visible' ), 10, 2 );
				add_filter( 'woocommerce_order_item_visible', array( $this, 'woosb_item_visible' ), 10, 2 );
				add_filter( 'woocommerce_checkout_cart_item_visible', array( $this, 'woosb_item_visible' ), 10, 2 );
			}

			// Hide on mini-cart
			if ( get_option( '_woosb_hide_bundled_mini_cart', 'no' ) == 'yes' ) {
				add_filter( 'woocommerce_widget_cart_item_visible', array( $this, 'woosb_item_visible' ), 10, 2 );
			}

			// Hide item meta
			add_filter( 'woocommerce_display_item_meta', array( $this, 'woosb_display_item_meta' ), 10, 2 );
			add_filter( 'woocommerce_order_items_meta_get_formatted', array(
				$this,
				'woosb_order_items_meta_get_formatted'
			), 10, 1 );

			// Order item
			add_action( 'woocommerce_add_order_item_meta', array( $this, 'woosb_add_order_item_meta' ), 10, 2 );
			add_filter( 'woocommerce_order_item_name', array( $this, 'woosb_order_item_name' ), 10, 2 );

			// Admin order
			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'woosb_hidden_order_item_meta' ), 10, 1 );
			add_action( 'woocommerce_before_order_itemmeta', array( $this, 'woosb_before_order_item_meta' ), 10, 1 );

			// Add settings link
			add_filter( 'plugin_action_links', array( $this, 'woosb_settings_link' ), 10, 2 );

			// Add settings tab
			add_filter( 'woocommerce_get_sections_products', array( $this, 'woosb_add_sections' ) );
			add_filter( 'woocommerce_get_settings_products', array( $this, 'woosb_add_settings' ), 10, 2 );

			// Add custom data
			add_action( 'wp_ajax_woosb_custom_data', array( $this, 'woosb_custom_data_callback' ) );
			add_action( 'wp_ajax_nopriv_woosb_custom_data', array( $this, 'woosb_custom_data_callback' ) );

			// Loop add-to-cart
			add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'woosb_loop_add_to_cart_link' ), 10, 2 );

			// Price HTML
			add_filter( 'woocommerce_get_price_html', array( $this, 'woosb_get_price_html' ), 10, 2 );

			// Calculate totals
			add_action( 'woocommerce_before_calculate_totals', array( $this, 'woosb_before_calculate_totals' ), 99, 1 );

			// Shipping
			add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'woosb_cart_shipping_packages' ) );
		}

		function woosb_wp_enqueue_scripts() {
			wp_enqueue_style( 'woosb-frontend', WOOSB_URI . 'css/frontend.css' );
			wp_enqueue_script( 'woosb-frontend', WOOSB_URI . 'js/frontend.js', array( 'jquery' ), WOOSB_VERSION, true );
			wp_localize_script( 'woosb-frontend', 'woosb_vars', array(
					'ajax_url'                 => admin_url( 'admin-ajax.php' ),
					'alert_text'               => esc_html__( 'Please select some product options before adding this product to your cart.', 'woosb' ),
					'bundle_price_text'        => get_option( '_woosb_bundle_price_text', '' ),
					'price_format'             => get_woocommerce_price_format(),
					'price_decimals'           => wc_get_price_decimals(),
					'price_thousand_separator' => wc_get_price_thousand_separator(),
					'price_decimal_separator'  => wc_get_price_decimal_separator(),
					'currency_symbol'          => get_woocommerce_currency_symbol(),
					'woosb_nonce'              => wp_create_nonce( 'woosb_nonce' )
				)
			);
		}

		function woosb_admin_enqueue_scripts() {
			wp_enqueue_style( 'woosb-backend', WOOSB_URI . 'css/backend.css' );
			wp_enqueue_script( 'dragarrange', WOOSB_URI . 'js/drag-arrange.min.js', array( 'jquery' ), WOOSB_VERSION, true );
			wp_enqueue_script( 'woosb-backend', WOOSB_URI . 'js/backend.js', array( 'jquery' ), WOOSB_VERSION, true );
			wp_localize_script( 'woosb-backend', 'woosb_vars', array(
					'woosb_nonce' => wp_create_nonce( 'woosb_nonce' )
				)
			);
		}

		function woosb_custom_data_callback() {
			if ( isset( $_POST['woosb_ids'] ) ) {
				if ( ! isset( $_POST['woosb_nonce'] ) || ! wp_verify_nonce( $_POST['woosb_nonce'], 'woosb_nonce' ) ) {
					die( 'Permissions check failed' );
				}
				session_start();
				$_SESSION['woosb_ids'] = $_POST['woosb_ids'];
			}
			die();
		}

		function woosb_add_sections( $sections ) {
			$sections['woosb'] = esc_html__( 'Smart Bundle', 'woosb' );

			return $sections;
		}

		function woosb_add_settings( $settings, $current_section ) {
			if ( $current_section == 'woosb' ) {
				$settings_woosb   = array();
				$settings_woosb[] = array(
					'name' => esc_html__( 'Smart Bundle Settings', 'woosb' ),
					'type' => 'title',
					'desc' => esc_html__( 'The following options are used to configure WooCommerce Product Bundle', 'woosb' ),
					'id'   => 'woosb'
				);
				$settings_woosb[] = array(
					'name'    => esc_html__( 'Show thumbnail', 'woosb' ),
					'id'      => '_woosb_bundled_thumb',
					'type'    => 'select',
					'css'     => 'min-width:150px;',
					'options' => array(
						'yes' => esc_html__( 'Yes', 'woosb' ),
						'no'  => esc_html__( 'No', 'woosb' ),
					)
				);
				$settings_woosb[] = array(
					'name'    => esc_html__( 'Show quantity', 'woosb' ),
					'id'      => '_woosb_bundled_qty',
					'type'    => 'select',
					'css'     => 'min-width:150px;',
					'options' => array(
						'yes' => esc_html__( 'Yes', 'woosb' ),
						'no'  => esc_html__( 'No', 'woosb' ),
					)
				);
				$settings_woosb[] = array(
					'name'    => esc_html__( 'Show price', 'woosb' ),
					'id'      => '_woosb_bundled_price',
					'type'    => 'select',
					'css'     => 'min-width:150px;',
					'options' => array(
						'regular'  => esc_html__( 'Regular price', 'woosb' ),
						'subtotal' => esc_html__( 'Subtotal', 'woosb' ),
						'html'     => esc_html__( 'Price HTML', 'woosb' ),
						'no'       => esc_html__( 'No', 'woosb' ),
					)
				);
				$settings_woosb[] = array(
					'name'    => esc_html__( 'Hide products in the bundle on cart & checkout page', 'woosb' ),
					'desc'    => esc_html__( 'Hide products in the bundle, just show the main product on the cart & checkout page.', 'woosb' ),
					'id'      => '_woosb_hide_bundled',
					'type'    => 'select',
					'css'     => 'min-width:150px;',
					'options' => array(
						'no'  => esc_html__( 'No', 'woosb' ),
						'yes' => esc_html__( 'Yes', 'woosb' ),
					),
					'default' => 'no',
				);
				$settings_woosb[] = array(
					'name'    => esc_html__( 'Hide products in the bundle on mini-cart', 'woosb' ),
					'desc'    => esc_html__( 'Hide products in the bundle, just show the main product on mini-cart.', 'woosb' ),
					'id'      => '_woosb_hide_bundled_mini_cart',
					'type'    => 'select',
					'css'     => 'min-width:150px;',
					'options' => array(
						'no'  => esc_html__( 'No', 'woosb' ),
						'yes' => esc_html__( 'Yes', 'woosb' ),
					),
					'default' => 'no',
				);
				$settings_woosb[] = array(
					'name'    => esc_html__( 'Bundle price text', 'woosb' ),
					'desc'    => esc_html__( 'The text before price when choosing variation in the bundle.', 'woosb' ),
					'id'      => '_woosb_bundle_price_text',
					'type'    => 'text',
					'css'     => 'min-width:150px;',
					'default' => esc_html__( 'Bundle price:', 'woosb' )
				);
				$settings_woosb[] = array( 'type' => 'sectionend', 'id' => 'woosb' );

				return $settings_woosb;
			} else {
				return $settings;
			}
		}

		function woosb_settings_link( $links, $file ) {
			static $plugin;
			if ( ! isset( $plugin ) ) {
				$plugin = plugin_basename( __FILE__ );
			}
			if ( $plugin == $file ) {
				$settings_link = '<a href="admin.php?page=wc-settings&tab=products&section=woosb">' . esc_html__( 'Settings', 'woosb' ) . '</a>';
				$links[]       = '<a href="https://wpclever.net/contact">' . esc_html__( 'Support', 'woosb' ) . '</a>';
				array_unshift( $links, $settings_link );
			}

			return $links;
		}

		function woosb_cart_contents_count( $count ) {
			$cart_contents = WC()->cart->cart_contents;
			$bundled_items = 0;
			foreach ( $cart_contents as $cart_item_key => $cart_item ) {
				if ( ! empty( $cart_item['woosb_parent_id'] ) ) {
					$bundled_items += $cart_item['quantity'];
				}
			}

			return intval( $count - $bundled_items );
		}

		function woosb_cart_item_removed( $cart_item_key, $cart ) {
			if ( isset( $cart->removed_cart_contents[ $cart_item_key ]['woosb_keys'] ) ) {
				$woosb_keys = $cart->removed_cart_contents[ $cart_item_key ]['woosb_keys'];
				foreach ( $woosb_keys as $woosb_key ) {
					unset( $cart->cart_contents[ $woosb_key ] );
				}
			}
		}

		function woosb_cart_item_name( $name, $item ) {
			if ( isset( $item['woosb_parent_id'] ) && ! empty( $item['woosb_parent_id'] ) ) {
				if ( strpos( $name, '</a>' ) !== false ) {
					return '<a href="' . get_permalink( $item['woosb_parent_id'] ) . '">' . get_the_title( $item['woosb_parent_id'] ) . '</a> &rarr; ' . $name;
				} else {
					return get_the_title( $item['woosb_parent_id'] ) . ' &rarr; ' . $name;
				}
			} else {
				return $name;
			}
		}

		function woosb_order_item_name( $name, $item ) {
			if ( isset( $item['woosb_parent_id'] ) && ! empty( $item['woosb_parent_id'] ) ) {
				if ( strpos( $name, '</a>' ) !== false ) {
					return '<a href="' . get_permalink( $item['woosb_parent_id'] ) . '">' . get_the_title( $item['woosb_parent_id'] ) . '</a> &rarr; ' . $name;
				} else {
					return get_the_title( $item['woosb_parent_id'] ) . ' &rarr; ' . $name;
				}
			} else {
				return $name;
			}
		}

		function woosb_update_cart_item_quantity( $cart_item_key, $quantity = 0 ) {
			if ( ! empty( WC()->cart->cart_contents[ $cart_item_key ] ) && ( isset( WC()->cart->cart_contents[ $cart_item_key ]['woosb_keys'] ) ) ) {
				if ( $quantity == 0 || $quantity < 0 ) {
					$quantity = 0;
				} else {
					$quantity = WC()->cart->cart_contents[ $cart_item_key ]['quantity'];
				}
				foreach ( WC()->cart->cart_contents[ $cart_item_key ]['woosb_keys'] as $woosb_key ) {
					WC()->cart->cart_contents[ $woosb_key ]['quantity'] = $quantity * ( WC()->cart->cart_contents[ $woosb_key ]['woosb_qty'] ? WC()->cart->cart_contents[ $woosb_key ]['woosb_qty'] : 1 );
				}
			}
		}

		function woosb_add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
			if ( isset( $cart_item_data['woosb_ids'] ) && ( $cart_item_data['woosb_ids'] != '' ) ) {
				$items = explode( ',', $cart_item_data['woosb_ids'] );
				if ( is_array( $items ) && ( count( $items ) > 0 ) ) {
					// add child products
					foreach ( $items as $item ) {
						$woosb_item     = explode( '/', $item );
						$woosb_item_id  = $woosb_item[0] ? $woosb_item[0] : 0;
						$woosb_item_qty = $woosb_item[1] ? $woosb_item[1] : 1;
						$woosb_product  = wc_get_product( $woosb_item_id );
						if ( $woosb_product ) {
							$woosb_product_qty = $woosb_item_qty * $quantity;
							$woosb_cart_id     = WC()->cart->generate_cart_id( $woosb_item_id, 0, '', array(
								'woosb_parent_id'  => $product_id,
								'woosb_parent_key' => $cart_item_key,
								'woosb_qty'        => $woosb_item_qty
							) );
							$woosb_item_key    = WC()->cart->find_product_in_cart( $woosb_cart_id );
							$woosb_product->set_price( 0 );
							if ( ! $woosb_item_key ) {
								$woosb_item_key                              = $woosb_cart_id;
								WC()->cart->cart_contents[ $woosb_item_key ] = array(
									'product_id'       => $woosb_item_id,
									'variation_id'     => 0,
									'variation'        => array(),
									'quantity'         => $woosb_product_qty,
									'data'             => $woosb_product,
									'woosb_parent_id'  => $product_id,
									'woosb_parent_key' => $cart_item_key,
									'woosb_qty'        => $woosb_item_qty,
								);
							}
							WC()->cart->cart_contents[ $cart_item_key ]['woosb_keys'][] = $woosb_item_key;
						}
					}
				}
			}
		}

		function woosb_add_cart_item( $cart_item ) {
			if ( isset( $cart_item['woosb_parent_key'] ) ) {
				$cart_item['data']->price = 0;
			}

			return $cart_item;
		}

		function woosb_add_cart_item_data( $cart_item_data, $product_id ) {
			session_start();
			$terms        = get_the_terms( $product_id, 'product_type' );
			$product_type = ! empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : 'simple';
			if ( $product_type == 'woosb' ) {
				if ( isset( $_SESSION['woosb_ids'] ) ) {
					$cart_item_data['woosb_ids'] = $_SESSION['woosb_ids'];
					unset( $_SESSION['woosb_ids'] );
				} else {
					$cart_item_data['woosb_ids'] = get_post_meta( $product_id, 'woosb_ids', true );
				}
			}

			return $cart_item_data;
		}

		function woosb_item_visible( $visible, $item ) {
			if ( isset( $item['woosb_parent_id'] ) ) {
				return false;
			} else {
				return $visible;
			}
		}

		function woosb_display_item_meta( $html, $item ) {
			if ( isset( $item['woosb_ids'] ) || isset( $item['woosb_parent_id'] ) ) {
				return '';
			} else {
				return $html;
			}
		}

		function woosb_order_items_meta_get_formatted( $formatted_meta ) {
			foreach ( $formatted_meta as $key => $meta ) {
				if ( ( $meta['key'] == 'woosb_ids' ) || ( $meta['key'] == 'woosb_parent_id' ) ) {
					unset( $formatted_meta[ $key ] );
				}
			}

			return $formatted_meta;
		}

		function woosb_add_order_item_meta( $item_id, $item ) {
			if ( isset( $item['woosb_parent_id'] ) ) {
				wc_add_order_item_meta( $item_id, 'woosb_parent_id', $item['woosb_parent_id'] );
			}
			if ( isset( $item['woosb_ids'] ) ) {
				wc_add_order_item_meta( $item_id, 'woosb_ids', $item['woosb_ids'] );
			}
		}

		function woosb_hidden_order_item_meta( $hidden ) {
			return array_merge( $hidden, array( 'woosb_parent_id', 'woosb_ids' ) );
		}

		function woosb_before_order_item_meta( $item_id ) {
			if ( ( $woosb_parent_id = wc_get_order_item_meta( $item_id, 'woosb_parent_id', true ) ) ) {
				echo sprintf( esc_html__( '(bundled in %s)', 'woosb' ), get_the_title( $woosb_parent_id ) );
			}
		}

		function woosb_get_cart_item_from_session( $cart_item, $item_session_values ) {
			if ( isset( $item_session_values['woosb_ids'] ) && ! empty( $item_session_values['woosb_ids'] ) ) {
				$cart_item['woosb_ids'] = $item_session_values['woosb_ids'];
			}
			if ( isset( $item_session_values['woosb_parent_id'] ) ) {
				$cart_item['woosb_parent_id']  = $item_session_values['woosb_parent_id'];
				$cart_item['woosb_parent_key'] = $item_session_values['woosb_parent_key'];
				$cart_item['woosb_qty']        = $item_session_values['woosb_qty'];
				$cart_item['data']->set_price( 0 );
				if ( isset( $cart_item['data']->subscription_sign_up_fee ) ) {
					$cart_item['data']->subscription_sign_up_fee = 0;
				}
			}

			return $cart_item;
		}

		function woosb_cart_item_subtotal( $subtotal, $cart_item, $cart_item_key ) {
			if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['woosb_parent_id'] ) ) {
				return '';
			}

			return $subtotal;
		}

		function woosb_cart_item_remove_link( $link, $cart_item_key ) {
			if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['woosb_parent_id'] ) ) {
				return '';
			}

			return $link;
		}

		function woosb_cart_item_quantity( $quantity, $cart_item_key ) {
			if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['woosb_parent_id'] ) ) {
				return WC()->cart->cart_contents[ $cart_item_key ]['quantity'];
			}

			return $quantity;
		}

		function woosb_cart_item_price( $price, $cart_item, $cart_item_key ) {
			if ( isset( WC()->cart->cart_contents[ $cart_item_key ]['woosb_parent_id'] ) ) {
				return '';
			}

			return $price;
		}

		function woosb_get_search_results() {
			if ( ! isset( $_POST['woosb_nonce'] ) || ! wp_verify_nonce( $_POST['woosb_nonce'], 'woosb_nonce' ) ) {
				die( 'Permissions check failed' );
			}
			$keyword     = $_POST['woosb_keyword'];
			$ids         = $_POST['woosb_ids'];
			$exclude_ids = array();
			$ids_arrs    = explode( ',', $ids );
			if ( is_array( $ids_arrs ) && count( $ids_arrs ) > 0 ) {
				foreach ( $ids_arrs as $ids_arr ) {
					$ids_arr_new   = explode( '/', $ids_arr );
					$exclude_ids[] = $ids_arr_new[0] ? $ids_arr_new[0] : 0;
				}
			}
			$query_args = array(
				'post_status'    => 'publish',
				's'              => $keyword,
				'posts_per_page' => 5,
				'post_type'      => 'product',
				'post__not_in'   => $exclude_ids
			);
			$query      = new WP_Query( $query_args );
			if ( $query->have_posts() ) {
				echo '<ul>';
				while ( $query->have_posts() ) {
					$query->the_post();
					$product = wc_get_product( get_the_ID() );
					if ( ! $product || $product->is_type( 'woosb' ) ) {
						continue;
					}
					if ( $product->is_in_stock() ) {
						if ( $product->is_type( 'variable' ) ) {
							echo '<li data-id="' . $product->get_id() . '" data-price="' . $product->get_variation_price( 'min' ) . '" data-price-max="' . $product->get_variation_price( 'max' ) . '"><span class="move"></span><span class="qty"></span> ' . $product->get_name() . ' (#' . $product->get_id() . ' - ' . $product->get_price_html() . ') <span class="remove">+</span></li>';
							// show all childs
							$childs = $product->get_children();
							if ( is_array( $childs ) && count( $childs ) > 0 ) {
								foreach ( $childs as $child ) {
									$product_child = wc_get_product( $child );
									echo '<li data-id="' . $child . '" data-price="' . $product_child->get_price() . '" data-price-max="' . $product_child->get_price() . '"><span class="move"></span><span class="qty"></span> ' . $product_child->get_name() . ' (#' . $product_child->get_id() . ' - ' . $product_child->get_price_html() . ') <span class="remove">+</span></li>';
								}
							}
						} else {
							echo '<li data-id="' . $product->get_id() . '" data-price="' . $product->get_price() . '" data-price-max="' . $product->get_price() . '"><span class="move"></span><span class="qty"></span> ' . $product->get_name() . ' (#' . $product->get_id() . ' - ' . $product->get_price_html() . ') <span class="remove">+</span></li>';
						}
					} else {
						echo '<li class="out-of-stock" data-id="' . $product->get_id() . '" data-price="' . $product->get_price() . '" data-price-max="' . $product->get_price() . '"><span class="move"></span><span class="qty"></span> <s> ' . $product->get_name() . ' (#' . $product->get_id() . ' - ' . $product_child->get_price_html() . ') </s> (out of stock) <span class="remove">+</span></li>';
					}
				}
				echo '</ul>';
				wp_reset_postdata();
			} else {
				echo '<ul><span>' . sprintf( esc_html__( 'No results found for "%s"', 'woosb' ), $keyword ) . '</span></ul>';
			}
			die();
		}

		function woosb_product_type_selector( $types ) {
			$types['woosb'] = esc_html__( 'Smart Bundle', 'woosb' );

			return $types;
		}

		function woosb_product_data_tabs( $tabs ) {
			$tabs['woosb'] = array(
				'label'  => esc_html__( 'Smart Bundle', 'woosb' ),
				'target' => 'woosb_settings',
				'class'  => array( 'show_if_woosb' ),
			);

			return $tabs;
		}

		function woosb_product_data_panels() {
			global $post;
			?>
			<div id='woosb_settings' class='panel woocommerce_options_panel woosb_table'>
				<table>
					<tr>
						<th>Search</th>
						<td>
							<div class="w100">
								<span class="loading" id="woosb_loading">searching...</span>
								<input type="text" id="woosb_keyword">
								<div id="woosb_results" class="woosb_results"></div>
							</div>
						</td>
					</tr>
					<tr>
						<th>Selected</th>
						<td>
							<div class="w100">
								<input type="hidden" id="woosb_ids" class="woosb_ids" name="woosb_ids"
								       value="<?php echo get_post_meta( $post->ID, 'woosb_ids', true ); ?>"
								       readonly/>
								<div id="woosb_selected" class="woosb_selected">
									<ul>
										<?php
										$woosb_price = 0;
										if ( get_post_meta( $post->ID, 'woosb_ids', true ) ) {
											$woosb_items = explode( ',', get_post_meta( $post->ID, 'woosb_ids', true ) );
											if ( is_array( $woosb_items ) && count( $woosb_items ) > 0 ) {
												foreach ( $woosb_items as $woosb_item ) {
													$woosb_item_arr = explode( '/', $woosb_item );
													$woosb_item_id  = $woosb_item_arr[0] ? $woosb_item_arr[0] : 0;
													$woosb_item_qty = $woosb_item_arr[1] ? $woosb_item_arr[1] : 1;
													$woosb_product  = wc_get_product( $woosb_item_id );
													if ( ! $woosb_product ) {
														continue;
													}
													if ( $woosb_product->is_in_stock() ) {
														$woosb_price += $woosb_product->get_price() * $woosb_item_qty;
														if ( $woosb_product->is_type( 'variable' ) ) {
															echo '<li data-id="' . $woosb_item_id . '" data-price="' . $woosb_product->get_variation_price( 'min' ) . '" data-price-max="' . $woosb_product->get_variation_price( 'max' ) . '"><span class="move"></span><span class="qty"><input type="number" value="' . $woosb_item_qty . '" min="1"/></span> ' . $woosb_product->get_name() . ' (#' . $woosb_product->get_id() . ' - ' . $woosb_product->get_price_html() . ')<span class="remove">×</span></li>';
														} else {
															echo '<li data-id="' . $woosb_item_id . '" data-price="' . $woosb_product->get_price() . '" data-price-max="' . $woosb_product->get_price() . '"><span class="move"></span><span class="qty"><input type="number" value="' . $woosb_item_qty . '" min="1"/></span> ' . $woosb_product->get_name() . ' (#' . $woosb_product->get_id() . ' - ' . $woosb_product->get_price_html() . ')<span class="remove">×</span></li>';
														}
													} else {
														echo '<li class="out-of-stock" data-id="' . $woosb_item_id . '" data-price="' . $woosb_product->get_price() . '" data-price-max="' . $woosb_product->get_price() . '"><span class="move"></span><span class="qty"><input type="number" value="' . $woosb_item_qty . '" min="1"/></span> <s>' . $woosb_product->get_name() . ' (#' . $woosb_product->get_id() . ' - ' . $woosb_product->get_price_html() . ')</s> (out of stock) <span class="remove">×</span></li>';
													}
												}
											}
										}
										?>
									</ul>
								</div>
							</div>
						</td>
					</tr>
					<tr class="woosb_tr_space">
						<th>Regular price (<?php echo get_woocommerce_currency_symbol(); ?>)</th>
						<td>
							<span id="woosb_regular_price"><?php echo esc_html( $woosb_price ); ?></span>
						</td>
					</tr>
					<tr class="woosb_tr_space">
						<th></th>
						<td>
							<input id="woosb_disable_auto_price" name="woosb_disable_auto_price"
							       type="checkbox" <?php echo( get_post_meta( $post->ID, 'woosb_disable_auto_price', true ) == 'on' ? 'checked' : '' ); ?>/>
							Disable auto calculate regular price? If yes,
							<a id="woosb_set_regular_price">click here to set regular price</a>
						</td>
					</tr>
					<tr class="woosb_tr_space">
						<th>Sale price</th>
						<td style="vertical-align: middle;line-height: 30px;">
							<input id="woosb_price_percent" name="woosb_price_percent" type="number" min="1" max="99"
							       value="<?php echo( get_post_meta( $post->ID, 'woosb_price_percent', true ) ? get_post_meta( $post->ID, 'woosb_price_percent', true ) : '' ); ?>"
							       style="width: 60px"/> % of
							Regular price
							<u>or</u>
							<a id="woosb_set_sale_price">click here to set sale price</a>
						</td>
					</tr>
				</table>
			</div>
			<?php
		}

		function woosb_save_option_field( $post_id ) {
			if ( isset( $_POST['woosb_ids'] ) ) {
				update_post_meta( $post_id, 'woosb_ids', sanitize_text_field( $_POST['woosb_ids'] ) );
			}
			if ( isset( $_POST['woosb_disable_auto_price'] ) ) {
				update_post_meta( $post_id, 'woosb_disable_auto_price', 'on' );
			} else {
				update_post_meta( $post_id, 'woosb_disable_auto_price', 'off' );
			}
			if ( isset( $_POST['woosb_price_percent'] ) ) {
				update_post_meta( $post_id, 'woosb_price_percent', sanitize_text_field( $_POST['woosb_price_percent'] ) );
			}
		}

		function woosb_add_to_cart_form() {
			global $product;
			if ( $product->has_variables() ) {
				wp_enqueue_script( 'wc-add-to-cart-variation' );
			}
			self::woosb_show_items();
			wc_get_template( 'single-product/add-to-cart/simple.php' );
		}

		function woosb_add_to_cart_button() {
			global $product;
			if ( $product->is_type( 'woosb' ) ) {
				echo '<input name="woosb_ids" id="woosb_ids" type="hidden" value="' . get_post_meta( $product->get_id(), 'woosb_ids', true ) . '"/>';
			}
		}

		function woosb_show_items() {
			global $product;
			$product_id = $product->get_id();
			if ( ( $woosb_items = explode( ',', get_post_meta( $product_id, 'woosb_ids', true ) ) ) && is_array( $woosb_items ) && count( $woosb_items ) > 0 ) {
				?>
				<table id="woosb_products" cellspacing="0" class="woosb-table woosb-products"
				       data-percent="<?php echo esc_attr( get_post_meta( $product_id, 'woosb_price_percent', true ) ); ?>"
				       data-regular="<?php echo esc_attr( get_post_meta( $product_id, '_regular_price', true ) ); ?>"
				       data-sale="<?php echo esc_attr( get_post_meta( $product_id, '_sale_price', true ) ); ?>"
				       data-variables="<?php echo( $product->has_variables() ? 'yes' : 'no' ); ?>">
					<tbody>
					<?php foreach ( $woosb_items as $woosb_item ) {
						$woosb_item_arr = explode( '/', $woosb_item );
						$woosb_item_id  = $woosb_item_arr[0] ? $woosb_item_arr[0] : 0;
						$woosb_item_qty = $woosb_item_arr[1] ? $woosb_item_arr[1] : 1;
						$woosb_product  = wc_get_product( $woosb_item_id );
						if ( ! $woosb_product ) {
							continue;
						}
						?>
						<tr class="woosb-product"
						    data-id="<?php echo esc_attr( $woosb_product->is_type( 'variable' ) ? 0 : $woosb_item_id ); ?>"
						    data-price="<?php echo esc_attr( $woosb_product->get_price() ); ?>"
						    data-qty="<?php echo esc_attr( $woosb_item_qty ); ?>">
							<?php if ( get_option( '_woosb_bundled_thumb', 'yes' ) != 'no' ) { ?>
								<td class="woosb-thumb">
									<?php echo get_the_post_thumbnail( $woosb_item_id, array( 40, 40 ) ); ?>
								</td>
							<?php } ?>
							<td class="woosb-title">
								<?php
								echo '<div class="woosb-title-inner">';
								if ( get_option( '_woosb_bundled_qty', 'yes' ) == 'yes' ) {
									echo $woosb_item_qty . ' × ';
								}
								if ( $woosb_product->is_in_stock() ) {
									echo '<a href="' . get_permalink( $woosb_item_id ) . '">' . $woosb_product->get_name() . '</a>';
								} else {
									echo '<a href="' . get_permalink( $woosb_item_id ) . '"><s>' . $woosb_product->get_name() . '</s></a>';
								}
								echo '</div>';
								if ( $woosb_product->is_type( 'variable' ) ) {
									$attributes           = $woosb_product->get_variation_attributes();
									$available_variations = $woosb_product->get_available_variations();
									if ( is_array( $attributes ) && ( count( $attributes ) > 0 ) ) {
										echo '<form class="variations_form cart" data-product_id="' . absint( $woosb_product->get_id() ) . '" data-product_variations="' . htmlspecialchars( wp_json_encode( $available_variations ) ) . '">';
										echo '<div class="variations">';
										foreach ( $attributes as $attribute_name => $options ) { ?>
											<div class="variation">
												<div
													class="label"><?php echo wc_attribute_label( $attribute_name ); ?></div>
												<div class="select">
													<?php
													$selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( stripslashes( urldecode( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ) ) : $woosb_product->get_variation_default_attribute( $attribute_name );
													wc_dropdown_variation_attribute_options( array(
														'options'   => $options,
														'attribute' => $attribute_name,
														'product'   => $woosb_product,
														'selected'  => $selected
													) );
													?>
												</div>
											</div>
										<?php }
										echo '<div class="reset">' . apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'woosb' ) . '</a>' ) . '</div>';
										echo '</div>';
										echo '</form>';
									}
								}
								?>
							</td>
							<?php if ( get_option( '_woosb_bundled_price', 'regular' ) != 'no' ) { ?>
								<td class="woosb-price">
									<?php
									if ( get_option( '_woosb_bundled_price', 'regular' ) == 'regular' ) {
										echo wc_price( $woosb_product->get_price() );
									} elseif ( get_option( '_woosb_bundled_price', 'regular' ) == 'subtotal' ) {
										echo wc_price( $woosb_product->get_price() * $woosb_item_qty );
									} elseif ( get_option( '_woosb_bundled_price', 'regular' ) == 'html' ) {
										echo $woosb_product->get_price_html();
									}
									?>
								</td>
							<?php } ?>
						</tr>
						<?php
					} ?>
					</tbody>
				</table>
				<div id="woosb_total" class="woosb-total"></div>
				<?php
			}
		}

		function woosb_loop_add_to_cart_link( $link, $product ) {
			if ( $product->is_type( 'woosb' ) && $product->has_variables() ) {
				$link = str_replace( 'ajax_add_to_cart', '', $link );
			}

			return $link;
		}

		function woosb_get_price_html( $price, $product ) {
			if ( $product->is_type( 'woosb' ) ) {
				$woosb_price  = $woosb_price_ins = 0;
				$product_data = $product->get_data();
				$product_id   = $product->get_id();
				if ( get_post_meta( $product_id, 'woosb_ids', true ) && ( get_post_meta( $product_id, 'woosb_disable_auto_price', true ) != 'on' ) ) {
					$woosb_items = explode( ',', get_post_meta( $product_id, 'woosb_ids', true ) );
					if ( is_array( $woosb_items ) && count( $woosb_items ) > 0 ) {
						foreach ( $woosb_items as $woosb_item ) {
							$woosb_item_arr = explode( '/', $woosb_item );
							$woosb_item_id  = $woosb_item_arr[0] ? $woosb_item_arr[0] : 0;
							$woosb_item_qty = $woosb_item_arr[1] ? $woosb_item_arr[1] : 1;
							$woosb_product  = wc_get_product( $woosb_item_id );
							if ( ! $woosb_product || ! $woosb_product->is_in_stock() ) {
								continue;
							}
							$woosb_price += $woosb_product->get_price() * $woosb_item_qty;
						}
					}
				} elseif ( $product_data['regular_price'] != '' ) {
					$woosb_price = $product_data['regular_price'];
				}
				$price_del = wc_price( $woosb_price );
				if ( $product->is_on_sale() ) {
					if ( $product_data['sale_price'] != '' ) {
						$woosb_price_ins = $product_data['sale_price'];
					} elseif ( ( $woosb_price_percent = get_post_meta( $product_id, 'woosb_price_percent', true ) ) && is_numeric( $woosb_price_percent ) && ( intval( $woosb_price_percent ) < 100 ) && ( intval( $woosb_price_percent ) > 0 ) ) {
						$woosb_price_ins = intval( $woosb_price_percent ) * $woosb_price / 100;
					}
					$price_ins = wc_price( $woosb_price_ins );

					return '<del>' . $price_del . '</del> <ins>' . $price_ins . '</ins>';
				} else {
					return $price_del;
				}
			}

			return $price;
		}

		function woosb_before_calculate_totals( $cart_object ) {

			//  This is necessary for WC 3.0+
			if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
				return;
			}

			foreach ( $cart_object->cart_contents as $cart_item_key => $cart_item ) {
				if ( isset( $cart_item['woosb_parent_id'] ) && ( $cart_item['woosb_parent_id'] != '' ) ) {
					$cart_item['data']->set_price( 0 );
				}
				// main product price
				if ( isset( $cart_item['woosb_ids'] ) && ( $cart_item['woosb_ids'] != '' ) ) {
					$product_id   = $cart_item['product_id'];
					$product      = wc_get_product( $product_id );
					$product_data = $product->get_data();
					$woosb_price  = 0;
					if ( $product_data['sale_price'] != '' ) {
						$woosb_price = $product_data['sale_price'];
					} else {
						if ( get_post_meta( $product_id, 'woosb_disable_auto_price', true ) != 'on' ) {
							$woosb_items = explode( ',', $cart_item['woosb_ids'] );
							if ( is_array( $woosb_items ) && count( $woosb_items ) > 0 ) {
								foreach ( $woosb_items as $woosb_item ) {
									$woosb_item_arr     = explode( '/', $woosb_item );
									$woosb_item_id      = $woosb_item_arr[0] ? $woosb_item_arr[0] : 0;
									$woosb_item_qty     = $woosb_item_arr[1] ? $woosb_item_arr[1] : 1;
									$woosb_item_product = wc_get_product( $woosb_item_id );
									if ( ! $woosb_item_product || ! $woosb_item_product->is_in_stock() ) {
										continue;
									}
									$woosb_price += $woosb_item_product->get_price() * $woosb_item_qty;
								}
							}
						} else {
							$woosb_price = $product->get_price();
						}
						if ( ( $woosb_price_percent = get_post_meta( $product_id, 'woosb_price_percent', true ) ) && is_numeric( $woosb_price_percent ) && ( intval( $woosb_price_percent ) < 100 ) && ( intval( $woosb_price_percent ) > 0 ) ) {
							$woosb_price = intval( $woosb_price_percent ) * $woosb_price / 100;
						}
					}

					$cart_item['data']->set_price( floatval( $woosb_price ) );
				}
			}
		}

		function woosb_cart_shipping_packages( $packages ) {
			if ( ! empty( $packages ) ) {
				foreach ( $packages as $package_key => $package ) {
					if ( ! empty( $package['contents'] ) ) {
						foreach ( $package['contents'] as $cart_item_key => $cart_item ) {
							if ( isset( $cart_item['woosb_parent_id'] ) && ( $cart_item['woosb_parent_id'] != '' ) ) {
								unset( $packages[ $package_key ]['contents'][ $cart_item_key ] );
							}
						}
					}
				}
			}

			return $packages;
		}
	}

	new WooSB();
}