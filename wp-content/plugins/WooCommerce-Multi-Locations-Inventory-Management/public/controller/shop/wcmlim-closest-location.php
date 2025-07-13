<?php
$coordinates_calculator      = get_option('wcmlim_distance_calculator_by_coordinates');
if($coordinates_calculator != '')
{

    $selectedLocationId = isset($_POST['selectedLocationId']) ? $_POST['selectedLocationId'] : false;
    $nearby_location = isset($_COOKIE['wcmlim_nearby_location']) ? $_COOKIE['wcmlim_nearby_location'] : "";
    $globalPincheck = isset($_POST['globalPin']) ? $_POST['globalPin'] : false;
    $product_id  = isset($_POST['product_id']) ? intval($_POST['product_id']) : "";
    $variation_id = isset($_POST['variation_id']) ? intval($_POST['variation_id']) : "";

    if(!empty($variation_id))
    {
        $product_id = $variation_id;
    }	
    if (isset($_POST['postcode'])) {
        $ladd = str_replace(",", "", $_POST['postcode']);
        $origins = str_replace(" ", "+", $ladd);
    }
    $dis_unit = get_option("wcmlim_show_location_distance", true);


    $lat = isset($_POST['lat']) ? $_POST['lat'] : "";
    $lng = isset($_POST['lng']) ? $_POST['lng'] : "";

    $isExcLoc = get_option("wcmlim_exclude_locations_from_frontend");
    if (!empty($isExcLoc)) {
        $terms = get_terms(array('taxonomy' => 'locations', 'hide_empty' => false, 'parent' => 0, 'exclude' => $isExcLoc));
    } else {
        $terms = get_terms(array('taxonomy' => 'locations', 'hide_empty' => false, 'parent' => 0));
    }
    if (isset($product_id) && !$globalPincheck) {
    $product = wc_get_product($product_id);
    if($product){
        $backorder = $product->backorders_allowed();
    }
    }
    $google_api_key = get_option('wcmlim_google_api_key');
    // Check for the custom field value

    $sli = isset($_POST["selectedLocationId"]) ? $_POST["selectedLocationId"] : "";
    foreach ($terms as $in => $term) {
        $termid = $term->term_id;
        $postmeta_stock_at_term = get_post_meta($product_id, 'wcmlim_stock_at_' . $term->term_id, true);
        $streetNumber = get_term_meta($termid, 'wcmlim_street_number', true);
        $route = get_term_meta($termid, 'wcmlim_route', true);
        $locality = get_term_meta($termid, 'wcmlim_locality', true);
        $state = get_term_meta($termid, 'wcmlim_administrative_area_level_1', true);
        $postal_code = get_term_meta($termid, 'wcmlim_postal_code', true);
        $country = get_term_meta($termid, 'wcmlim_country', true);
        $loc_lat = get_term_meta( $termid, 'wcmlim_lat', true );
        $loc_lng = get_term_meta( $termid, 'wcmlim_lng', true );

        if ($streetNumber) {
            $streetNumber = $streetNumber . " ,";
        } else {
            $streetNumber = ' ';
        }
        if ($route) {
            $route = $route . " ,";
        } else {
            $route = ' ';
        }
        if ($locality) {
            $locality = $locality . " ,";
        } else {
            $locality = ' ';
        }
        if ($state) {
            $state = $state . " ,";
        } else {
            $state = ' ';
        }
        if ($postal_code) {
            $postal_code = $postal_code . " ,";
        } else {
            $postal_code = ' ';
        }
        if ($country) {
            $country = $country;
        } else {
            $country = ' ';
        }
        $address = $streetNumber . $route . $locality . $state . $postal_code . $country;
        $find_address = $streetNumber .'+'. $route .'+'. $locality .'+'. $state .'+'. $postal_code .'+'. $country;
        
        
    if(empty($loc_lat) || empty($loc_lng))
    {
        $address = str_replace(' ', '+', $find_address);
        $address = str_replace(',', '+', $find_address);
        $getlatlng = wcmlim_get_lat_lng($address, $termid);
        $loc_lat = get_term_meta( $termid, 'wcmlim_lat', true );
        $loc_lng = get_term_meta( $termid, 'wcmlim_lng', true );
    }
    
        if($selectedLocationId == $in)
        {
            $lat = $loc_lat;
            $lng = $loc_lng;
        }

    $distance = distance_between_coordinates($lat, $lng, $loc_lat, $loc_lng);
    $return_dis_unit = get_option("wcmlim_show_location_distance", true);
    $return_dis_unit = $distance.' ' .$return_dis_unit;
    if (!empty($postmeta_stock_at_term) || ($postmeta_stock_at_term > 0)) {
        $loc_tmp_arr[] = array(
            "key" => $in,
            "loc_id" => $termid,
            "loc_lat" => $loc_lat,
            "loc_lng" => $loc_lng,
            "distance" => $distance,
            "ret_distance" => $return_dis_unit,
            "address" => $address
        );
    }
    }

    //sort the array by distance
    function sortByDis($a, $b)
    {
        return $a['distance'] > $b['distance'];
    }
    if (isset($loc_tmp_arr)) {
        usort($loc_tmp_arr, 'sortByDis');
    }
    //get nearby loc id
    $nearby_first_loc_id = $loc_tmp_arr[0]['loc_id'];
    $nearby_first_loc_key = $loc_tmp_arr[0]['key'];
    $nearby_first_loc_ret_distance = $loc_tmp_arr[0]['ret_distance'];
    $nearby_second_loc_id = $loc_tmp_arr[1]['loc_id'];
    $nearby_second_loc_key = $loc_tmp_arr[1]['key'];
    $nearby_second_loc_ret_distance = ($loc_tmp_arr[1]['ret_distance']) ? $loc_tmp_arr[1]['ret_distance'] : "";

        $first_streetNumber = get_term_meta($nearby_first_loc_id, 'wcmlim_street_number', true);
        $first_route = get_term_meta($nearby_first_loc_id, 'wcmlim_route', true);
        $first_locality = get_term_meta($nearby_first_loc_id, 'wcmlim_locality', true);
        $first_state = get_term_meta($nearby_first_loc_id, 'wcmlim_administrative_area_level_1', true);
        $first_postal_code = get_term_meta($nearby_first_loc_id, 'wcmlim_postal_code', true);
        $first_country = get_term_meta($nearby_first_loc_id, 'wcmlim_country', true);
        $first_loc_lat = get_term_meta( $nearby_first_loc_id, 'wcmlim_lat', true );
        $first_loc_lng = get_term_meta( $nearby_first_loc_id, 'wcmlim_lng', true );


        //bind the first location parameter
        if ($first_streetNumber) {
            $first_streetNumber = $first_streetNumber . " ,";
        } else {
            $first_streetNumber = ' ';
        }
        if ($first_route) {
            $first_route = $first_route . " ,";
        } else {
            $first_route = ' ';
        }
        if ($first_locality) {
            $first_locality = $first_locality . " ,";
        } else {
            $first_locality = ' ';
        }
        if ($first_state) {
            $first_state = $first_state . " ,";
        } else {
            $first_state = ' ';
        }
        if ($first_postal_code) {
            $first_postal_code = $first_postal_code . " ,";
        } else {
            $first_postal_code = ' ';
        }
        if ($first_country) {
            $first_country = $first_country;
        } else {
            $first_country = ' ';
        }
        $first_address = $first_streetNumber . $first_route . $first_locality . $first_state . $first_postal_code . $first_country;
        
        $second_streetNumber = get_term_meta($nearby_second_loc_id, 'wcmlim_street_number', true);
        $second_route = get_term_meta($nearby_second_loc_id, 'wcmlim_route', true);
        $second_locality = get_term_meta($nearby_second_loc_id, 'wcmlim_locality', true);
        $second_state = get_term_meta($nearby_second_loc_id, 'wcmlim_administrative_area_level_1', true);
        $second_postal_code = get_term_meta($nearby_second_loc_id, 'wcmlim_postal_code', true);
        $second_country = get_term_meta($nearby_second_loc_id, 'wcmlim_country', true);
        $second_loc_lat = get_term_meta( $nearby_second_loc_id, 'wcmlim_lat', true );
        $second_loc_lng = get_term_meta( $nearby_second_loc_id, 'wcmlim_lng', true );

        //bind the second location parameter
        if ($second_streetNumber) {
            $second_streetNumber = $second_streetNumber . " ,";
        } else {
            $second_streetNumber = ' ';
        }
        if ($second_route) {
            $second_route = $second_route . " ,";
        } else {
            $second_route = ' ';
        }
        if ($second_locality) {
            $second_locality = $second_locality . " ,";
        } else {
            $second_locality = ' ';
        }
        if ($second_state) {
            $second_state = $second_state . " ,";
        } else {
            $second_state = ' ';
        }
        if ($second_postal_code) {
            $second_postal_code = $second_postal_code . " ,";
        } else {
            $second_postal_code = ' ';
        }
        if ($second_country) {
            $second_country = $second_country;
        } else {
            $second_country = ' ';
        }
        $second_address = $second_streetNumber . $second_route . $second_locality . $second_state . $second_postal_code . $second_country;

        
    if (isset($__seleOrigin[0])) {
        $origins = $__seleOrigin[0];
    }
    $nearby_location = isset($_COOKIE['wcmlim_nearby_location']) ? $_COOKIE['wcmlim_nearby_location'] : "";
    $res = array(
        "status"=> "true",
        "globalpin"=> "true",
        "loc_address"=> $first_address,
        "loc_key"=> $nearby_first_loc_key,
        "loc_dis_unit"=> $nearby_first_loc_ret_distance,
        "secNearLocAddress"=> $second_address,
        "secNearStoreDisUnit"=> $nearby_second_loc_ret_distance,
        "secNearLocKey"=> $nearby_second_loc_key,
        "cookie"=> $nearby_location		
    );
    echo json_encode($res);
    die();
        
}
else
{
    $nearby_location = isset($_COOKIE['wcmlim_nearby_location']) ? $_COOKIE['wcmlim_nearby_location'] : "";
    $globalPincheck = isset($_POST['globalPin']) ? $_POST['globalPin'] : false;
    $product_id  = isset($_POST['product_id']) ? intval($_POST['product_id']) : "";
    $variation_id = isset($_POST['variation_id']) ? intval($_POST['variation_id']) : "";

    if(empty($product_id) && empty($variation_id))
    {
            $response_array["message"] = esc_html('Not found any Product Id.', 'wcmlim');
            $response_array["status"] = "false";
    }

    if (isset($_POST['postcode'])) {
        $ladd = str_replace(",", "", $_POST['postcode']);
        $origins = str_replace(" ", "+", $ladd);
    }
    
    $dis_unit = get_option("wcmlim_show_location_distance", true);
    $lat = isset($_POST['lat']) ? $_POST['lat'] : "";
    $lng = isset($_POST['lng']) ? $_POST['lng'] : "";
    
    $isExcLoc = get_option("wcmlim_exclude_locations_from_frontend");
    if (!empty($isExcLoc)) {
        $terms = get_terms(array('taxonomy' => 'locations', 'hide_empty' => false, 'parent' => 0, 'exclude' => $isExcLoc));
    } else {
        $terms = get_terms(array('taxonomy' => 'locations', 'hide_empty' => false, 'parent' => 0));
    }

    $product = wc_get_product($product_id);

    $google_api_key = get_option('wcmlim_google_api_key');
    // Check for the custom field value
    $sli = isset($_POST["selectedLocationId"]) ? $_POST["selectedLocationId"] : "";
    
    foreach ($terms as $in => $term) {
        if ($sli != '') {
            if ($in == $sli) {
                $term_meta = get_option("taxonomy_$term->term_id");
                $term_meta = array_map(function ($term) {
                    if (!is_array($term)) {
                        return $term;
                    }
                }, $term_meta);
                $__spare = implode(" ", array_filter($term_meta));
                $__seleOrigin[] = str_replace(" ", "+", $__spare);
            }
        }
        $term_meta = get_option("taxonomy_$term->term_id");
        $term_meta = array_map(function ($term) {
            if (!is_array($term)) {
                return $term;
            }
        }, $term_meta);
       
        $spacead = implode(" ", array_filter($term_meta));
        $dest[] = str_replace(" ", "+", $spacead);

        $allterm_names[] = $term->name;
        $postcode[] = isset($term_meta['wcmlim_postcode']) ? $term_meta['wcmlim_postcode'] : "";
        $wcountry[] = isset($term_meta['wcmlim_country_state']) ? $term_meta['wcmlim_country_state'] : "";
    }
    if (isset($__seleOrigin[0])) {
        $origins = $__seleOrigin[0];
    }


    $destcount = count($dest);
    if ( $destcount <= 20 ) 
        {
        $destination = implode("|", $dest);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://maps.googleapis.com/maps/api/distancematrix/json?units=metrics&origins=" . $origins . "&destinations=" . $destination . "&key={$google_api_key}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $response = curl_exec($curl);
        $response_arr = json_decode($response);
        
        curl_close($curl);
        if (isset($response_arr->error_message)) {
            $response_array["message"] = $response_arr->error_message;
            $response_array["status"] = "false";
            echo json_encode($response_array);
            die();
        }
        
        foreach ($response_arr->rows as $r => $t) {
            foreach ($t as $key => $value) {
                foreach ($value as $a => $b) {
                    if ($b->status == "OK") {
                        $dis = explode(" ", $b->distance->text);
                        
                        $plaindis = str_replace(',', '', $dis[0]);
                        if ($dis_unit == "kms") {
                            $dis_in_un = $b->distance->text;
                        } elseif ($dis_unit == "miles") {
                            $dis_in_un = round($plaindis * 0.621, 1) . ' miles';
                        } elseif ($dis_unit == "none") {
                            $dis_in_un = $b->distance->text;
                        }
                        $isExcLoc = get_option("wcmlim_exclude_locations_from_frontend");
                        //prepare terms
                        if (!empty($isExcLoc)) {
                        $terms = get_terms(array('taxonomy' => 'locations', 'hide_empty' => false, 'parent' => 0, 'exclude' => $isExcLoc));
                        } else {
                        $terms = get_terms(array('taxonomy' => 'locations', 'hide_empty' => false, 'parent' => 0));
                        }
                        
                        if(isset($_POST['product_id']))
                            {
                                
                        foreach ($terms as $in => $term) {
                            if($a == $in)
                            {
                                if (!empty($variation_id)) {
                                    $postmeta_stock_at_term = get_post_meta($variation_id, 'wcmlim_stock_at_' . $term->term_id, true);
                                    $postmeta_backorders_product = get_post_meta($variation_id, '_backorders', true);
                                }else {
                                    $postmeta_stock_at_term = get_post_meta($product_id, 'wcmlim_stock_at_' . $term->term_id, true);
                                    $postmeta_backorders_product = get_post_meta($product_id, '_backorders', true);
                                }
                                if(((!empty($postmeta_stock_at_term)) && ($postmeta_stock_at_term != 0)) || ($postmeta_backorders_product == 'yes'))
                                {
                                    $distance[] = array("value" => $plaindis, "key" => $a, "plaindis" => $plaindis, "dis_in_un" => $dis_in_un);
                                }
                            }
                        }
                        }
                        else
                        {
                            $distance[] = array("value" => $plaindis, "key" => $a, "plaindis" => $plaindis, "dis_in_un" => $dis_in_un);
                        }
                        if ($first_route) {
                            $first_route = $first_route . " ,";
                        } else {
                            $first_route = ' ';
                        }
            
                    }
                }
            }
        }
        if(isset($distance)){
            $dis_in_unit = (is_array($distance)) ? min($distance)['dis_in_un'] : '';
            $dis_key = (is_array($distance)) ? min($distance)['key'] : '';
        }
        foreach ($response_arr->origin_addresses as $k => $v) {
            $response_array['address'] = $v;
        }
        foreach ($response_arr->destination_addresses as $k => $v) {
            if ($k == $dis_key) {
                $lcAdd = str_replace(",", "", $v);
                if ($lcAdd) {
                    // getting second nearest location
                    $secNLocation = $this->getSecondNearestLocation($distance, $dis_unit, $product_id);
                    $serviceRadius = $this->getLocationServiceRadius($dis_key);
                    $groupID =$this->getLocationgroupID($dis_key);
                    if(empty($secNLocation[0]))
                    {
                        $secNearLocAddress = $lcAdd;
                        $secNearLocKey = $dis_key;
                        $secNearStoreDisUnit = $dis_in_unit;

                    }
                    else
                    {
                        $secNearLocAddress =  $secNLocation[0];
                        $secNearLocKey = $secNLocation[2];
                        $secNearStoreDisUnit = isset($secNLocation[1]) ? $secNLocation[1] : "";
                    }
                    
                    
                    $response_array["status"] = "true";
                    $response_array["globalpin"] = "true";
                    $response_array["loc_address"] = $lcAdd;
                    $response_array['loc_key'] = $dis_key;
                    $response_array['loc_dis_unit'] = $dis_in_unit;
                    $response_array["secNearLocAddress"] = $secNearLocAddress;
                    $response_array['secNearStoreDisUnit'] = $secNearStoreDisUnit;
                    $response_array['secNearLocKey'] = $secNearLocKey;
                    $response_array["cookie"] = $nearby_location;
                    $response_array["secgrouploc"] = $groupID;

                    
                    if(isset($serviceRadius)){
                        $response_array['locServiceRadius'] = $serviceRadius;
                    }
                    if (isset($ladd)) {
                        $autodetect_by_maxmind = get_option('wcmlim_enable_autodetect_location_by_maxmind');
                        if($autodetect_by_maxmind != 'on'){
                                        setcookie("wcmlim_nearby_location", $ladd, time() + 36000, '/');
                        }
                    }
                    update_option('wcmlim_location_distance', $dis_in_unit);
                    echo json_encode($response_array);
                    wp_die();
                };
            }
        }
        if (empty($terms)) {
            $response_array["message"] = _e('Not found any location.', 'wcmlim');
            $response_array["status"] = "false";
            $response_array["cookie"] = $nearby_location;
            echo json_encode($response_array);
            die();
        }
        die();
    } else {
        
        $nodes = array_chunk($dest, 20);
        $node_count = count($nodes);
        $curl_arr = array();
        $master = curl_multi_init();			
        for($i = 0; $i < $node_count; $i++)
        {
            $url = $nodes[$i];
            $destination[$i] = implode("|", $url);						
            $curl_arr[$i] = curl_init();
        
            
            curl_setopt_array($curl_arr[$i], array(
                CURLOPT_URL => "https://maps.googleapis.com/maps/api/distancematrix/json?units=metrics&origins=" . $origins . "&destinations=" . $destination[$i] . "&key={$google_api_key}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
            ));
            curl_multi_add_handle($master, $curl_arr[$i]);				
            
        }
    

        $running = NULL;
        do {
            usleep(10000);
            curl_multi_exec($master,$running);
        } while($running > 0); 					

        $responses = array();
        for($i = 0; $i < $node_count; $i++)
        {						
            $resp = curl_multi_getcontent($curl_arr[$i]);	
            array_push($responses, json_decode($resp));	
        } 
        // all of our requests are done, we can now access the results
        for($i = 0; $i < $node_count; $i++)
        {
            curl_multi_remove_handle($master, $curl_arr[$i]);						
        }
        curl_multi_close($master);

        for($i = 0; $i < $node_count; $i++)
        {

            if (isset($responses[$i]->error_message)) {
                $response_array["message"] = $response_arr[$i]->error_message;
                $response_array["status"] = "false";
                $response_array["cookie"] = $nearby_location;
                echo json_encode($response_array);
                die();
            }

            if(isset($responses[$i]->rows))
            {
            foreach ($responses[$i]->rows as $r => $t) {
                foreach ($t as $key => $value) {

                    foreach ($value as $a => $b) {
                        if ($b->status == "OK") {
                            
                            $dis = explode(" ", $b->distance->text);
                            $plaindis = str_replace(',', '', $dis[0]);
                            if ($dis_unit == "kms") {
                                $dis_in_un = $b->distance->text;
                            } elseif ($dis_unit == "miles") {
                                $dis_in_un = round($plaindis * 0.621, 1) . ' miles';
                            } elseif ($dis_unit == "none") {
                                $dis_in_un = $b->distance->text;
                            }
                            $loc_id = $terms[$a]->term_id;

                    
                        if(!empty($variation_id) && ($variation_id != 0))
                        {
                            $loc_stock = get_post_meta($variation_id, "wcmlim_stock_at_{$loc_id}", true);
                        }
                        if(!empty($product_id) && ($product_id != 0))
                        {
                            $loc_stock = get_post_meta($product_id, "wcmlim_stock_at_{$loc_id}", true);
                        }
                        if(empty($product_id) && empty($variation_id))	
                        {
                            $distance[] = array("value" => $plaindis, "key" => $a, "dis_in_un" => $dis_in_un, "loc_id" => $terms[$a]->term_id, "loc_stock" => $loc_stock);
                        }							
                        if(($loc_stock != '') && ($loc_stock != '0'))
                        {
                            $distance[] = array("value" => $plaindis, "key" => $a, "dis_in_un" => $dis_in_un, "loc_id" => $terms[$a]->term_id, "loc_stock" => $loc_stock);

                        }
                    }
                }
            }
        }
    }
        $dis_in_unit = (is_array($distance)) ? min($distance)['plaindis'] : '';
            $dis_key = (is_array($distance)) ? min($distance)['key'] : '';
          
        
            foreach ($responses[$i]->destination_addresses as $k => $v) {
                if ($k == $dis_key) {
                    $lcAdd = str_replace(",", "", $v);
                    if ($lcAdd) {
                        // getting second nearest location
                        $secNLocation = $this->getSecondNearestLocation($distance, $dis_unit, $product_id);
                        $serviceRadius = $this->getLocationServiceRadius($dis_key);
                        $groupID =$this->getLocationgroupID($dis_key);
                        
                        if(empty($secNLocation[0]))
                        {
                            $secNearLocAddress = $lcAdd;
                            $secNearLocKey = $dis_key;
                            $secNearStoreDisUnit = $dis_in_unit;

                        }
                        else
                        {
                            $secNearLocAddress =  $secNLocation[0];
                            $secNearLocKey = $secNLocation[2];
                            $secNearStoreDisUnit = isset($secNLocation[1]) ? $secNLocation[1] : "";
                        }
                    
                        $response_array["status"] = "true";
                        $response_array["globalpin"] = "true";
                        $response_array["loc_address"] = $lcAdd;
                        $response_array['loc_key'] = $dis_key;
                        $response_array['loc_dis_unit'] = $dis_in_unit;
                        $response_array["secNearLocAddress"] = $secNearLocAddress;
                        $response_array['secNearStoreDisUnit'] = $secNearStoreDisUnit;
                        $response_array['secNearLocKey'] = $secNearLocKey;
                        $response_array["cookie"] = $nearby_location;
                        $response_array["secgrouploc"] = $groupID;
                        if (isset($ladd)) {
                            $autodetect_by_maxmind = get_option('wcmlim_enable_autodetect_location_by_maxmind');
                            if($autodetect_by_maxmind != 'on'){
                                                setcookie("wcmlim_nearby_location", $ladd, time() + 36000, '/');
                            }
                        }
                        update_option('wcmlim_location_distance', $dis_in_unit);
                        echo json_encode($response_array);
                        wp_die();
                    };
                }
            }	
                            
        }//foreach
      
        if (empty($terms)) {
            $response_array["message"] = _e('Not found any location.', 'wcmlim');
            $response_array["status"] = "false";
            $response_array["cookie"] = $nearby_location;
            echo json_encode($response_array);
            die();
        }	
        
    }

}
