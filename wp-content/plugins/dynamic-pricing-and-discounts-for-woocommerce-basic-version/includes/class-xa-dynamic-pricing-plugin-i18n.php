<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.xadapter.com
 * @since      1.0.0
 *
 * @package    xa_dynamic_pricing_plugin
 * @subpackage xa_dynamic_pricing_plugin/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    xa_dynamic_pricing_plugin
 * @subpackage xa_dynamic_pricing_plugin/includes
 * @author     Your Name <email@example.com>
 */
class xa_dynamic_pricing_plugin_i18n {

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {

        load_plugin_textdomain(
                'xa-dynamic-pricing-plugin', false, dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }

}
