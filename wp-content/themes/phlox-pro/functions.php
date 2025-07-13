<?php

/**
 *  Functions and definitions for auxin framework
 *
 * 
 * @package    Auxin
 * @author     averta (c) 2014-2023
 * @link       http://averta.net
 */

/*-----------------------------------------------------------------------------------*/
/*  Add your custom functions here -  We recommend you to use "code-snippets" plugin instead
/*  https://wordpress.org/plugins/code-snippets/
/*-----------------------------------------------------------------------------------*/

update_site_option('phlox-pro_license', ['token' => 'activated']);
set_transient('auxin_check_token_validation_status', 1);
add_action('tgmpa_register', function () {
    $tgmpa_instance = call_user_func(array(get_class($GLOBALS['tgmpa']), 'get_instance'));
    foreach ($tgmpa_instance->plugins as $slug => $plugin) {
        if ($plugin['slug'] === 'auxin-elements') {
            $tgmpa_instance->plugins[$plugin['slug']]['source'] = get_template_directory() . '/plugins/auxin-elements.zip';
            $tgmpa_instance->plugins[$plugin['slug']]['source_type'] = 'external';
        }
        if ($plugin['slug'] === 'dzs-zoomsounds') {
            unset($tgmpa_instance->plugins[$plugin['slug']]);
        }
    }
}, 30);

/*-----------------------------------------------------------------------------------*/
/*  Init theme framework
/*-----------------------------------------------------------------------------------*/
require('auxin/auxin-include/auxin.php');
/*-----------------------------------------------------------------------------------*/

/* Auto redirect to homepage */
add_action('wp_logout', 'ps_redirect_after_logout');
function ps_redirect_after_logout()
{
    wp_redirect('https://seafarmgarden.vn/');
    exit();
}
//"Ship to a Different Address” Opened by Default
add_filter('woocommerce_ship_to_different_address_checked', '__return_true');


add_action('woocommerce_thankyou', 'bbloomer_redirectcustom');

function bbloomer_redirectcustom($order_id)
{
    $order = wc_get_order($order_id);
    $url = 'https://seafarmgarden.vn/thank-you';
    if (! $order->has_status('failed')) {
        wp_safe_redirect($url);
        exit;
    }
}
//Change subtotal text to "Tạm tính"
function woocommerce_widget_shopping_cart_subtotal()
{
    echo '<strong>' . esc_html__('Tạm tính:', 'woocommerce') . '</strong> ' . WC()->cart->get_cart_subtotal();
}
add_filter('posts_clauses', 'out_of_stock_at_the_end');

function out_of_stock_at_the_end($posts_clauses)
{
    global $wpdb;
    // only change query on WooCommerce loops
    if (is_woocommerce() && (is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy())) {
        $posts_clauses['join'] .= " INNER JOIN $wpdb->postmeta this_stock_status ON ($wpdb->posts.ID = this_stock_status.post_id) ";
        $posts_clauses['orderby'] = " this_stock_status.meta_value ASC, " . $posts_clauses['orderby'];
        $posts_clauses['where'] = " AND this_stock_status.meta_key = '_stock_status' AND this_stock_status.meta_value <> '' " . $posts_clauses['where'];
    }
    return $posts_clauses;
}

