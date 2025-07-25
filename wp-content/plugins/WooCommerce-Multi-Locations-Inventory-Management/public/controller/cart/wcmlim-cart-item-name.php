<?php

$locescstring = __("Location : ", "wcmlim");
$locfeeescstring = __("Fee : ", "wcmlim");
$termExclude = get_option("wcmlim_exclude_locations_from_frontend");
if (!empty($termExclude)) {
    $terms = get_terms(array('taxonomy' => 'locations', 'hide_empty' => false, 'parent' => 0, 'exclude' => $termExclude));
} else {
    $terms = get_terms(array('taxonomy' => 'locations', 'hide_empty' => false, 'parent' => 0));
}
$wc_currency_symbol = get_woocommerce_currency_symbol();
$locationFee = get_option('wcmlim_location_fee');

$location_info = '';
$location_displayed = false;

if (isset($cart_item['select_location']['location_name']) && !empty($cart_item['select_location']['location_name'])) {
    $location_name = $cart_item['select_location']['location_name'];
    $location_info .= sprintf('<p>%s', $locescstring . esc_html($location_name));
    $location_displayed = true;
    // Fee
    if ($locationFee == "on") {
        foreach ($terms as $term) {
            if ($term->name == $location_name) {
                $term_id = $term->term_id;
                $location_fee = get_term_meta($term_id, 'wcmlim_location_fee', true);
                if ((!empty($location_fee)) && ($location_fee != 0)) {
                    $location_info .= sprintf('<br>%s', $locfeeescstring . $wc_currency_symbol . $location_fee);
                }
                break;
            }
        }
    }
} else {
    // If not set, get first location with stock
    foreach ($terms as $term) {
        $stock = get_post_meta($cart_item['product_id'], "wcmlim_stock_at_{$term->term_id}", true);
        if (!empty($stock) && $stock > 0) {
            $location_info .= sprintf('<p>%s', $locescstring . esc_html($term->name));
            $location_displayed = true;
            if ($locationFee == "on") {
                $location_fee = get_term_meta($term->term_id, 'wcmlim_location_fee', true);
                if ((!empty($location_fee)) && ($location_fee != 0)) {
                    $location_info .= sprintf('<br>%s', $locfeeescstring . $wc_currency_symbol . $location_fee);
                }
            }
            break;
        }
    }
    // If no location with stock, just show first location
    if (!$location_displayed && !empty($terms)) {
        $location_info .= sprintf('<p>%s', $locescstring . esc_html($terms[0]->name));
        $location_displayed = true;
        if ($locationFee == "on") {
            $location_fee = get_term_meta($terms[0]->term_id, 'wcmlim_location_fee', true);
            if ((!empty($location_fee)) && ($location_fee != 0)) {
                $location_info .= sprintf('<br>%s', $locfeeescstring . $wc_currency_symbol . $location_fee);
            }
        }
    }
}
if ($location_displayed) {
    $location_info .= sprintf('</p>');
}

// Only append location info if not already present in $name
if (strpos($name, $locescstring) === false) {
    $name .= $location_info;
}

$this->max_in_value = isset($cart_item['select_location']) ? $cart_item['select_location'] : "";
foreach ($terms as $term) {
    $this->max_value_inpl = get_post_meta($cart_item['product_id'], "wcmlim_stock_at_{$term->term_id}", true);
}

return $name;
