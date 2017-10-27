<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.xadapter.com
 * @since      1.0.0
 *
 * @package    xa_dynamic_pricing_plugin
 * @subpackage xa_dynamic_pricing_plugin/admin
 */
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    xa_dynamic_pricing_plugin
 * @subpackage xa_dynamic_pricing_plugin/admin
 * @author     Your Name <email@example.com>
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class xa_dynamic_pricing_plugin_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $xa_dynamic_pricing_plugin    The ID of this plugin.
     */
    private $xa_dynamic_pricing_plugin;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $xa_dynamic_pricing_plugin       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($xa_dynamic_pricing_plugin, $version) {

        $this->xa_dynamic_pricing_plugin = $xa_dynamic_pricing_plugin;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in xa_dynamic_pricing_plugin_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The xa_dynamic_pricing_plugin_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->xa_dynamic_pricing_plugin, plugin_dir_url(__FILE__) . 'css/xa-dynamic-pricing-plugin-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in xa_dynamic_pricing_plugin_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The xa_dynamic_pricing_plugin_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script($this->xa_dynamic_pricing_plugin, plugin_dir_url(__FILE__) . 'js/xa-dynamic-pricing-plugin-admin.js', array('jquery'), $this->version, false);
        wp_enqueue_script('jquery-ui-sortable');
    }

}
