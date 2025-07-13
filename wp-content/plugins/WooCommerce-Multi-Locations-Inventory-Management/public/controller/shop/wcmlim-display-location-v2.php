<?php
/**
 * Hiển thị tồn kho theo Location cho cả sản phẩm đơn giản và biến thể.
 * WooCommerce + Multi Locations Inventory Management Plugin.
 */

global $post, $product;

// Lấy danh sách kho (locations) theo cấu hình
$restrictGuest = get_option('wcmlim_enable_restrict_guestuser_location');
$selectedGuestLoc = get_option('wcmlim_restrict_guest_user_location');
$excludeLocations = get_option("wcmlim_exclude_locations_from_frontend");

if ($restrictGuest === 'on' && !empty($selectedGuestLoc) && !is_user_logged_in()) {
    $terms = get_terms(array(
        'taxonomy' => 'locations',
        'hide_empty' => false,
        'parent' => 0,
        'include' => $selectedGuestLoc
    ));
} elseif (!empty($excludeLocations)) {
    $terms = get_terms(array(
        'taxonomy' => 'locations',
        'hide_empty' => false,
        'parent' => 0,
        'exclude' => $excludeLocations
    ));
} else {
    $terms = get_terms(array(
        'taxonomy' => 'locations',
        'hide_empty' => false,
        'parent' => 0
    ));
}

$enable_price = 'on'; 

if ($product && $product->is_type('variable')) {
    $available_variations = $product->get_available_variations();

    foreach ($terms as $term) {
        $has_stock = false;

        foreach ($available_variations as $variation) {
            $variation_id = $variation['variation_id'];
            $stock = get_post_meta($variation_id, "wcmlim_stock_at_{$term->term_id}", true);
            if ($stock !== '') {
                $has_stock = true;
                break;
            }
        }

        if ($has_stock) {
            echo '<h2 class="locationName-single">' . esc_html($term->name) . '</h2>';
        }
    }
} else {
    foreach ($terms as $term) {
        $stock = get_post_meta($post->ID, "wcmlim_stock_at_{$term->term_id}", true);
        $regular_price = get_post_meta($post->ID, "wcmlim_regular_price_at_{$term->term_id}", true);
        $sale_price = get_post_meta($post->ID, "wcmlim_sale_price_at_{$term->term_id}", true);

        if ($stock !== '') {
            echo '<div class="wcmlim-stock-info">';
            echo '<h2 class="locationName-single">' . esc_html($term->name) . '</h2>';
            if ($enable_price === 'on') {
                if ($regular_price !== '') {
                    echo '<p>' . esc_html__('Regular Price:', 'woocommerce') . ' ' . wc_price($regular_price) . '</p>';
                }
                if ($sale_price !== '') {
                    echo '<p>' . esc_html__('Sale Price:', 'woocommerce') . ' ' . wc_price($sale_price) . '</p>';
                }
            }
            echo '</div>';
        }
    }
}
?>
