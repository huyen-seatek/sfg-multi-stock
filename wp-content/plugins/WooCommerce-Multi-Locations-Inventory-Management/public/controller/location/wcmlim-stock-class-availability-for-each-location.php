<?php

$stock_class = '';
$product_type = $product->get_type();
$instocklabel = 'in-stock';
$soldoutlabel = 'out-of-stock';
$changeStocklabel = 'in-stock';
$setLocation = isset($_COOKIE['wcmlim_selected_location_termid']) ? $_COOKIE['wcmlim_selected_location_termid'] : null;
$terms = get_terms(array('taxonomy' => 'locations', 'hide_empty' => false, 'parent' => 0));

if ($product_type === 'variable') {
    $children = $product->get_children();
    $total_qty_at_selected = 0;
    $has_empty_in_selected = false;

    // Tổng tồn kho tại location đang chọn
    foreach ($children as $child_id) {
        $child_product = wc_get_product($child_id);
        if (! $child_product) continue;

        $qty = $child_product->get_available_qty(); // dùng đúng hàm bạn định nghĩa

        if ($qty === '' || $qty === null) {
            $has_empty_in_selected = true;
        } elseif (is_numeric($qty)) {
            $total_qty_at_selected += intval($qty);
        }
    }


    // Tính tổng tồn kho ở các kho khác
    $total_qty_other_locations = 0;
    foreach ($terms as $term) {
        if ((string)$term->term_id === (string)$setLocation) continue;

        foreach ($children as $child_id) {
            $child_qty_other = get_post_meta($child_id, "wcmlim_stock_at_{$term->term_id}", true);
            $total_qty_other_locations += is_numeric($child_qty_other) ? intval($child_qty_other) : 0;
        }
    }

    // Quyết định hiển thị
    if ($has_empty_in_selected && $total_qty_at_selected === 0) {
        $stock_class = $total_qty_other_locations > 0 ? $changeStocklabel : $soldoutlabel;
    } elseif ($total_qty_at_selected === 0) {
        $stock_class = $soldoutlabel;
    } else {
        // $stock_class = $total_qty_at_selected . ' ' . $instocklabel;
        $stock_class = 'display_none_in_stock';
    }

} else {
    // Simple or variation product
    $selected_qty = $product->get_available_qty();

    $other_qty_total = 0;
    foreach ($terms as $term) {
        $term_id = $term->term_id;
        if ((string)$term_id === (string)$setLocation) continue;

        $allow = get_post_meta($product->get_id(), 'wcmlim_allow_specific_location_at_' . $term_id, true);
        if ($allow !== 'Yes') continue;

        $qty = get_post_meta($product->get_id(), "wcmlim_stock_at_{$term_id}", true);
        $other_qty_total += is_numeric($qty) ? intval($qty) : 0;
    }

    if ($selected_qty === '') {
        $stock_class = $other_qty_total > 0 ? $changeStocklabel : $soldoutlabel;
    } elseif ((int)$selected_qty === 0) {
        $stock_class = $soldoutlabel;
    } else {
        // $stock_class = intval($selected_qty) . ' ' . $instocklabel;
        $stock_class = 'display_none_in_stock';
    }
}

return $stock_class;
