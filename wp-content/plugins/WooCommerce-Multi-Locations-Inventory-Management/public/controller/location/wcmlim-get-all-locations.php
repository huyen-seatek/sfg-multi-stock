<?php
$isLocEx = get_option("wcmlim_exclude_locations_from_frontend");
if (!empty($isLocEx)) {
    $terms = get_terms(array('taxonomy' => 'locations', 'hide_empty' => false, 'parent' => 0, 'exclude' => $isLocEx));
} else {
    $terms = get_terms(array('taxonomy' => 'locations', 'hide_empty' => false, 'parent' => 0));
}
$result = [];
$i = 0;
foreach ($terms as $k => $term) {
    $term_meta = get_option("taxonomy_$term->term_id");
    $term_locator = get_term_meta( $term->term_id , 'wcmlim_locator', true);
    $term_meta = array_map(function ($term) {
        if (!is_array($term)) {
            return $term;
        }
    }, $term_meta);
    $result[$i]['location_address'] = implode(" ", array_filter($term_meta));
    $result[$i]['location_name'] = $term->name;
    $result[$i]['location_slug'] = $term->slug;
    $result[$i]['location_storeid'] = $term_locator;
    $result[$i]['location_termid'] = $term->term_id;
    $i++;
}
return $result;
wp_die();