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

//Multi Stock
add_shortcode('wcmlim_location', function () {
    if (class_exists('Wcmlim_Public')) {
        $plugin_public = new Wcmlim_Public('wcmlim', WCMLIM_VERSION);

        ob_start();
        $plugin_public->wcmlim_display_locationV2();
        return ob_get_clean();
    } else {
        return 'Wcmlim plugin is not available.';
    }
});


//-- Check stock availability via AJAX ---
add_action('wp_ajax_wcmlim_check_stock', 'wcmlim_check_stock_ajax');
add_action('wp_ajax_nopriv_wcmlim_check_stock', 'wcmlim_check_stock_ajax');

function wcmlim_check_stock_ajax() {
    $product_id  = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $location_id = isset($_POST['location_id']) ? absint($_POST['location_id']) : 0;

    if (!$product_id || !$location_id) {
        wp_send_json(array(
            'error'   => true,
            'message' => 'Dữ liệu không hợp lệ.',
        ));
    }

    // Check tồn kho theo location:
    $_location_qty = 0;
    $location_name = '';

    $terms = get_terms(array(
        'taxonomy' => 'locations',
        'hide_empty' => false,
        'parent' => 0
    ));

    foreach ($terms as $term) {
        if ($location_id === $term->term_id) {
            $location_name = $term->name;
            $_location_qty = (int) get_post_meta($product_id, "wcmlim_stock_at_{$term->term_id}", true);
            break;
        }
    }

    if ($_location_qty <= 0) {
        wc_get_logger()->info(
            'Đề xuất đổi cửa hàng:',
            array(
                'source'  => 'wcmlim-debug',
                'context' => array(
                    'product_id'         => $product_id,
                    'location_name'      => $location_name,
                    'location_qty_value' => $_location_qty
                )
            )
        );

        wp_send_json(array(
            'error'   => true,
            'message' => sprintf(__('Sản phẩm này không có sẵn tại cửa hàng <strong>%s</strong>. Vui lòng chọn cửa hàng khác hoặc liên hệ với chúng tôi để biết thêm thông tin.', 'woocommerce'), $location_name),
        ));
    }

    // Còn hàng:
    wp_send_json(array(
        'error'   => false,
        'message' => 'Còn hàng.',
    ));
}
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script('wcmlim-stock-check', get_template_directory_uri() . '/js/wcmlim-stock-check.js', array('jquery'), '1.0', true);
    wp_localize_script('wcmlim-stock-check', 'wcmlim_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
    ));
});
//------------------------
