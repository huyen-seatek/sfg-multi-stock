<?php
global $woocommerce;
$pass_location = isset($_REQUEST['select_location']) ? $_REQUEST['select_location'] : "";
$product_id = isset($_REQUEST['add-to-cart']) ? $_REQUEST['add-to-cart'] : "";
$manage_stock = get_post_meta($product_id, '_manage_stock', true);

$passed = true; // Initialize $passed as true

wc_get_logger()->info(
    'wcmlim 1',
    array(
        'source' => 'wcmlim-stock-allowed',
        'pass_location' => $pass_location

    )
);


if ($manage_stock != 'no') {
    if ($pass_location == -1) {
        wc_get_logger()->info(
            'wcmlim 2',
            array(
                'source' => 'wcmlim-stock-allowed',
                'pass_location' => $pass_location,
            )
        );
        //wc_add_notice(__('Please select a valid location.', 'wcmlim'), 'error');
        wc_add_notice(__('Yêu cầu đổi cửa hàng.', 'wcmlim'), 'error');
        $passed = false;
        return $passed;
    }
} else {
    $slCookie = isset($_COOKIE['wcmlim_selected_location']) ? $_COOKIE['wcmlim_selected_location'] : "";
    if ($slCookie == "-1") {
        $passed = false;
        return $passed;
    }
}
wc_get_logger()->info(
    'wcmlim-stock-allowed',
    array(
        'source' => 'wcmlim-stock-allowed',
        'slCookie' => $slCookie,
    )
);

$product = wc_get_product($product_id);
if (is_a($product, 'WC_Product')) {
    $isBackorder = $product->backorders_allowed();
    if ($isBackorder) {
        return true;
    }
}

if (WC()->cart->cart_contents_count > 0) {
    foreach (WC()->cart->get_cart() as $key => $val) {
        if (isset($val['select_location']['location_qty']) && isset($val['select_location']['location_key'])) {
            $_product = $val['data'];
            $pro = wc_get_product($val['product_id']);
            $stock_invalid = get_option('wcmlim_prod_instock_valid');
            $update_cart = false;

            if ($pro->is_type('simple')) {
                $_locqty = $val['select_location']['location_qty'];
                $cart_items_count = $val['quantity'];
                $total_count = ((int)$cart_items_count + (int)$quantity);

                if ($pass_location == $val['select_location']['location_key'] && $product_id == $_product->get_id()) {
                    if ($cart_items_count >= $_locqty || $total_count > $_locqty) {
                        // Set to false
                        $passed = false;
                        // Display a message
                        wc_add_notice(__($stock_invalid, "wcmlim"), "error");
                    } else {
                        // Update the quantity in the cart
                        WC()->cart->set_quantity($key, $total_count);
                        $update_cart = true;
                        break;
                    }
                }
            } elseif ($pro->is_type('variable')) {

                $cart_items_count = $val['quantity'];
                $total_count = ((int)$cart_items_count + (int)$quantity);
                $_locqty = $val['select_location']['location_qty'];

             
                if ($pass_location == $val['select_location']['location_key'] && $_REQUEST['variation_id'] == $_product->get_id()) {
                    if ($cart_items_count >= $_locqty || $total_count > $_locqty) {
                        // Set to false
                        $passed = false;
                        // Display a message
                        wc_add_notice(__($stock_invalid, "wcmlim"), "error");
                    } else {
                        WC()->cart->set_quantity($key, $total_count);

                        $update_cart = true;
                        break;
                    }
                }
            }

            // If cart is updated, no need to add a new line item
            if ($update_cart) {
                break;
            }
        }
    }
}

return $passed;
