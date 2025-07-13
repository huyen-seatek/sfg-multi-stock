<?php
// use \Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use \Automattic\WooCommerce\Utilities\OrderUtil;

/**
 * Copyright: (c)  [2020] - Techspawn Solutions Private Limited ( contact@techspawn.com  ) 
 *  All Rights Reserved.
 * 
 * NOTICE:  All information contained herein is, and remains
 * the property of Techspawn Solutions Private Limited,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to Techspawn Solutions Private Limited,
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Techspawn Solutions Private Limited
 *
 * @link              http://www.techspawn.com
 * @since             1.0.0
 * @package           Wcmlim
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Multi Locations Inventory Management
 * Plugin URI:        http://www.techspawn.com
 * Description:       This plugin will help you manage WooCommerce Products stocks through locations.
 * Version:           4.1.3
 * Requires at least: 4.9
 * Author:            Techspawn Solutions
 * Author URI:        http://www.techspawn.com
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       wcmlim
 * Domain Path:       /languages
 * WC requires at least:	3.4
 * WC tested up to: 	5.8.0
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}
update_option('wcmlim_license', "valid");

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('WCMLIM_VERSION', '3.5.9');
/**
 * Define default path and url for plugin.
 * 
 * @since    1.1.5
 */
define('WCMLIM_DIR_PATH', plugin_dir_path(__FILE__));
define('WCMLIM_URL_PATH', plugins_url('/', __FILE__));
define('WCMLIM_BASE', plugin_basename(__FILE__));
define('WCMLIM_SVALIDATOR', 'wcmlim_support_validater');

add_action("enqueue_block_editor_assets", "wcmlim_blocks_enqueue");

function wcmlim_blocks_enqueue()
{
	wp_enqueue_script('wcmlim-switch-block', plugin_dir_url(__FILE__) . 'admin/blocks/switch-block.js', array('wp-blocks', 'wp-i18n', 'wp-editor'), true, true);

	wp_enqueue_script('wcmlim-popup-block', plugin_dir_url(__FILE__) . 'admin/blocks/popup-block.js', array('wp-blocks', 'wp-i18n', 'wp-editor'), true, true);

	wp_enqueue_script('wcmlim-location-finder-block', plugin_dir_url(__FILE__) . 'admin/blocks/loc-finder-block.js', array('wp-blocks', 'wp-i18n', 'wp-editor'), true, true);

	wp_enqueue_script('wcmlim-lflv-block', plugin_dir_url(__FILE__) . 'admin/blocks/lflv-block.js', array('wp-blocks', 'wp-i18n', 'wp-editor'), true, true);

	wp_enqueue_script('wcmlim-locinfo-block', plugin_dir_url(__FILE__) . 'admin/blocks/locinfo-block.js', array('wp-blocks', 'wp-i18n', 'wp-editor'), true, true);

	wp_enqueue_script('wcmlim-prod-by-id-block', plugin_dir_url(__FILE__) . 'admin/blocks/prod-by-id-block.js', array('wp-blocks', 'wp-i18n', 'wp-editor'), true, true);

	wp_enqueue_style('wcmlim-popup-block', plugin_dir_url(__FILE__) . 'admin/css/wcmlim-popup-block.css', array('wp-blocks', 'wp-i18n', 'wp-editor'), true, false);
}


function wcmlim_block_category($categories)
{
	$custom_block = array(
		'slug' => 'amultilocation',
		'title' => 'Multilocations For WooCommerce'
	);
	$categories_sorted = array();
	$categories_sorted[0] = $custom_block;
	foreach ($categories as $category) {
		$categories_sorted[] = $category;
	}
	return $categories_sorted;
}
add_filter('block_categories', 'wcmlim_block_category', 10, 2);

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wcmlim-activator.php
 */
function wcmlim_activate()
{
	$active_plugins = get_option('active_plugins');
	$wooactive_plugins = is_woocommerce_activated();
	/**
	 * Check if WooCommerce is active
	 **/
	
	$locationCookieTime = get_option('wcmlim_set_location_cookie_time');
	if ($locationCookieTime == '') {
		update_option('wcmlim_set_location_cookie_time', '30');
	}

	if ( is_multisite() ) { 
		require_once plugin_dir_path(__FILE__) . 'includes/class-wcmlim-activator.php';
		Wcmlim_Activator::activate();
	} else {    
		if ($wooactive_plugins == 0) {
			deactivate_plugins(__FILE__);
			$error_message = esc_html_e('WooCommerce has not yet been installed or activated. WooCommerce Multi Locations Inventory Management is a WooCommerce Extension that will only function if WooCommerce is installed. Please first install and activate the WooCommerce Plugin.', 'wcmlim');
			wp_die($error_message, 'Plugin dependency check', array('back_link' => true));
		} else {
			
			$soldoutbutton_text = get_option('wcmlim' . '_soldout_button_text');
			if ($soldoutbutton_text  == false) {
				update_option('wcmlim' . '_soldout_button_text', 'Sold Out');
			}
			
		  $stockbutton_text = get_option('wcmlim' . '_instock_button_text');
		  if ($stockbutton_text  == false) {
			update_option('wcmlim' . '_instock_button_text', 'In Stock');
		  }
	
		  $backorder_text = get_option('wcmlim' . '_onbackorder_button_text');
		  if ($backorder_text  == false) {
			update_option('wcmlim' . '_onbackorder_button_text', 'Available on backorder');
		  }
	
			require_once plugin_dir_path(__FILE__) . 'includes/class-wcmlim-activator.php';
			Wcmlim_Activator::activate();
		} }
	
}
add_action('admin_init', 'wcmlim_deactivate_self');
function wcmlim_deactivate_self()
{		
	$plugins_dir = basename(dirname(__FILE__));
	$woocommerce_active_plugins = is_woocommerce_activated();
	if ($woocommerce_active_plugins == 0) {
		if (is_plugin_active($plugins_dir.'/wcmlim.php')) {
			
			deactivate_plugins($plugins_dir.'/wcmlim.php');
		}
	}
}
if (!function_exists('is_woocommerce_activated')) {
    function is_woocommerce_activated()
    {
        $active_plugins = get_option('active_plugins');
        $wooactive_plugins = 0;
        foreach ($active_plugins as $key => $value) {
            if (strpos($value, 'woocommerce.php') !== false) {
				
				$wooactive_plugins = 1;
			}
        }
        update_option('woocommerce_active_plugins', $wooactive_plugins);
        return $wooactive_plugins;
    }
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wcmlim-deactivator.php
 */
function wcmlim_deactivate()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-wcmlim-deactivator.php';
	Wcmlim_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'wcmlim_activate');
register_deactivation_hook(__FILE__, 'wcmlim_deactivate');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-wcmlim.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function wcmlim_run()
{
	$plugin = new Wcmlim();
	$plugin->run();
	$hpos_enabled = get_option('woocommerce_custom_orders_table_enabled');
	$wcmlim_hpos_enabled = get_option('wcmlim_hpos_enabled');	 
	if ($wcmlim_hpos_enabled == 'on' || $hpos_enabled == 'yes') {
		// Declare compatibility with WooCommerce features. KKW
		add_action(
			'before_woocommerce_init',
			function() {
				if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
				}

				if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
					update_option('wcmlim_hpos_enabled', 'on');
				} else {
					update_option('wcmlim_hpos_enabled', 'off');
				}
			}
			
		);
	}
}
wcmlim_run();

//write shortcode for wocommerce related products
function wcmlim_related_products_shortcode()
{
    ob_start();
	$product_id = get_the_ID();
	$terms = get_the_terms( $product_id, 'product_cat' );
	$term_names = array();
	if ( $terms && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			if ( $term->name !== 'Uncategorized' ) {
				$term_names[] = $term->name;
			}
		}
	}
	
	$selected_loc_id = isset($_COOKIE['wcmlim_selected_location']) ? (int)$_COOKIE['wcmlim_selected_location'] : 0;
	$locations = get_terms(array('taxonomy' => 'locations', 'hide_empty' => false, 'parent' => 0));
	//get hide out of stock settings
	foreach($locations as $key => $term){     
		if($key == $selected_loc_id){
			$term_slug = $term->slug;
			$term_id = $term->term_id;
			break;
		}
	}
    $q = new WP_Query(array(
        'post_type'      => 'product',
        'posts_per_page' => '4',
		'tax_query' => array(
			'relation' => "AND",
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'name',
				'terms'    => $term_names,
				'operator' => 'IN'
			),

			array(
			'taxonomy' => 'product_visibility',
			'field'    => 'name',
			'terms'    => array('outofstock'),
			'operator' => 'NOT IN'
			),
			array(
				'taxonomy' => 'locations',
				'field'    => 'slug',
				'terms'    => array($term_slug),
				'operator' => 'IN'	
			),

	),
	'meta_query'    => array(
		'relation' => 'AND',
		array(
			'key'       => 'wcmlim_stock_at_'.$term_id,
			'value'     => 0,
			'compare'   => '!=',
		),
		array(
			'key'       => 'wcmlim_stock_at_'.$term_id,
			'value'     => '',
			'compare'   => '!=',
		),

	),
    ));

    if ($q->have_posts()) {
        echo '<div class="related-products-grid">'; 

        $counter = 0;
        while ($q->have_posts()) {
            $q->the_post();

            echo '<div class="related-product-item">';
            wc_get_template_part('content', 'product');
            echo '</div>';

            $counter++;
            if ($counter % 3 === 0) {
                echo '<div class="related-products-grid"></div>'; 
            }
        }

        echo '</div>'; 

        wp_reset_postdata();
    } else {
        echo 'No related products found.';
    }

    return ob_get_clean();
}

add_shortcode('wcmlim_related_products_shortcode', 'wcmlim_related_products_shortcode');