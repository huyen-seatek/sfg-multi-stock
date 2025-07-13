<?php

$Inline_title = get_option("wcmlim_txt_inline_location", true);									
$is_preferred = get_option('wcmlim_preferred_location');
$geolocation = get_option('wcmlim_geo_location');
$useLc = get_option('wcmlim_enable_autodetect_location');
$autodetect_by_maxmind = get_option('wcmlim_enable_autodetect_location_by_maxmind');
$uspecLoc = get_option('wcmlim_enable_userspecific_location');
$restrictGuest = get_option('wcmlim_enable_restrict_guestuser_location');
$show_in_popup = get_option("wcmlim_show_in_popup");
$isLocationsGroup = get_option('wcmlim_enable_location_group');	
$isEditor = wp_get_referer();
if(strpos($isEditor, 'action=edit') || strpos($isEditor, 'post-new.php')){
    return;
}
if ($isLocationsGroup == 'on' && empty($this->wcmlim_get_all_store())) {
    echo "<p style='color: red;padding:10%;'>No location group found.</p>";
} else {    
if($restrictGuest == 'on' && !is_user_logged_in()){
    $selectedGuestLoc = get_option('wcmlim_restrict_guest_user_location');
    $locations_list = $this->wcmlim_get_all_locations();
    $selected_location = $this->get_selected_location();
    ?>
    <!-- <style>
        #set-def-store-popup-btn {
            position: absolute;
        }
        </style> -->
<div class="main-cont">
<div class="wcmlim-lc-switch">
    <form id="lc-switch-form" class="inline_wcmlim_lc" method="post">
        <div class="wcmlim_form_box">
        <div class="wcmlim_sel_location wcmlim_storeloc">
            <div class="wcmlim_change_lc_to new"><?php if ($Inline_title ) {
                echo $Inline_title;
            } else { _e('Location: ', 'wcmlim'); }?></div>
            <select name="wcmlim_change_lc_to" class="wcmlim-lc-select" id="wcmlim-change-lc-select">
            <option value="-1" <?php if (!$selected_location) echo "selected='selected'"; ?>><?php _e('Select', 'wcmlim') ?></option>
            <?php
            foreach ($locations_list as $key => $loc) {
                if($selectedGuestLoc == $loc['location_termid']){
            ?>
                <option 
                class="<?php echo 'wclimloc_'.$loc['location_slug']; ?>" 
                value="<?php echo $key; ?>" 
                data-lc-address="<?php echo base64_encode($loc['location_address']); ?>"
                data-lc-term="<?php echo $loc['location_termid']; ?>" 
                <?php if (preg_match('/^\d+$/', $selected_location)) {
                if ($selected_location == $key) 
                echo "selected='selected'";
                } ?>>
                <?php echo ucfirst($loc['location_name']); 
                ?>
                </option>
            <?php }	}?>
        </select>	
            
        <div class="er_location"></div>
                                <!-- Radio Listing Mode -->
                                <?php if ($isLocationsGroup == null || $isLocationsGroup == false ) { ?>
                                    <div class="rlist_location"></div>
                                <?php } ?>
                            </div>
                            <?php
                            
                            if ($geolocation == "on" || (is_array($show_in_popup) && in_array('location_finder_in_popup', $show_in_popup))) : ?>
                                <div class="postcode-checker">
                                    <div class="postcode_wcmliminput">
                                        <span class="postcode-checker-div postcode-checker-div-show">
                                            <?php
                                            $globpin = isset($_COOKIE['wcmlim_nearby_location']) ? $_COOKIE['wcmlim_nearby_location'] : "";
                                            $loc_dis_un = get_option('wcmlim_location_distance');
                                            ?>
                                            <input type="text" placeholder="<?php _e('Enter Pincode/Zipcode', 'wcmlim'); ?>" required class="class_post_code_global" name="post_code_global" value="<?php if($globpin != 0){esc_html_e($globpin);} ?>" id="elementIdGlobal">
                                            <input type="button" class="button" id="submit_postcode_global" value="<?php _e('Apply', 'wcmlim'); ?>">
                                            <input type='hidden' name="global_postal_check" id='global-postal-check' value='true'>
                                            <input type='hidden' name="global_postal_location" id='global-postal-location' value='<?php esc_html_e($globpin); ?>'>
                                            <input type='hidden' name="product_location_distance" id='product-location-distance' value='<?php esc_html_e($loc_dis_un); ?>'>
                                        </span>
                                    </div>
                                    <?php if ($useLc == "on") { ?>
                                        <div class="wclimlocsearch" style="display:none;">
                                        <a id="currentLoc" class="currentLoc">
                                            <i  class="fas fa-crosshairs currentLoc"></i>
                                                &nbsp; Use Current Location
                                            </a>
                                        </div>
                                    <?php } ?>
                                    <div class="search_rep">
                                        <div class="postcode-checker-response"></div>
                                        <a class="postcode-checker-change postcode-checker-change-show" href="#" data-wpzc-form-open="" style="display: none;">
                                            <i class="fa fa-edit" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                    <div class="postcode-location-distance"></div>
                                </div>
                            <?php endif; ?>
                            </div>
                        <?php wp_nonce_field('wcmlim_change_lc', 'wcmlim_change_lc_nonce'); ?>
                        <input type="hidden" name="action" value="wcmlim_location_change">
                    </form>
            </div>
        </div>
    <?php

}elseif ($is_preferred == 'on' || !empty($show_in_popup) && (empty($uspecLoc) && ( get_current_user_id() ))) {
    
    $locations_list = $this->wcmlim_get_all_locations();
    $selected_location = $this->get_selected_location();
    $storelocator_list = array();
    if ($isLocationsGroup == 'on') {
        $storelocator_list = $this->wcmlim_get_all_store();		
      						}
    
    if (sizeof($locations_list) >= 0 && sizeof($storelocator_list) >=0) { ?>		
    				<!-- <style>
        #set-def-store-popup-btn {
            position: absolute;
        }
        </style>					 -->
        <!-- default design Start-->
        <div class="main-cont">
        <div class="wcmlim-lc-switch">
            <form id="lc-switch-form" class="inline_wcmlim_lc" method="post">
                <div class="wcmlim_form_box">
                    <div class="wcmlim_sel_location wcmlim_storeloc">
                    <?php
                        $current_user = wp_get_current_user();
                        $current_user_id = get_current_user_id();
                        $current_ui = isset($current_user_id) ? $current_user_id : "";
                        $restricUsers   = get_option('wcmlim_enable_userspecific_location');
                        if($restricUsers == "on"){
                        $user_selected_location = get_user_meta($current_ui, 'wcmlim_user_specific_location', true);
                        }else{
                            $user_selected_location = "";
                        }
                        $wcmlim_selected_location_termid = isset($_COOKIE['wcmlim_selected_location_termid']) ? $_COOKIE['wcmlim_selected_location_termid'] : null;
                        $terms = get_terms(array('taxonomy' => 'location_group', 'hide_empty' => false, 'parent' => 0));
                        $roles = $current_user->roles;
                        if ($isLocationsGroup == 'on' && sizeof($terms) >0 ) { ?>
                        
                            <p class="wcmlim_change_sl_to"><?php if ($Inline_title ) {
                                                                                                                        echo $Inline_title;
                                                                                                                    } else {
                                                                                                                        _e('Region: ', 'wcmlim');
                                                                                                                    } ?></p>

                            <?php if ($restricUsers == "on" && $roles[0] == "customer") { ?>
                                                <select name="wcmlim_change_sl_to" id="wcmlim-change-sl-select">
                                                                                            <option value="-1"><?php _e('Please Select', 'wcmlim') ?></option>  
                                                                                        <?php
                                                                                        foreach ($locations_list as $key => $loc) {
                                                                                        if (preg_match('/^\d+$/',$user_selected_location)) {
                                                                                            if ($user_selected_location == $key) 
                                                                                                {
                                                                                                $rectricted_location_store_id = $loc['location_storeid'];
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                        $restricted_store = get_term($rectricted_location_store_id);?>

                                                <option class="<?php echo 'wclimstore_'.$restricted_store->term_id; ?>" value="<?php echo $restricted_store->term_id; ?>"><?php echo ucfirst($restricted_store->name); ?></option>
                                                </select>
                            <?php }
                            else {  ?>
                                <select name="wcmlim_change_sl_to" id="wcmlim-change-sl-select">
                                                                                            <option value="-1"><?php _e('Please Select', 'wcmlim') ?></option>  
                                                                                        <?php
                                                                                            foreach ($storelocator_list as $key => $loc) {
                                                                                            ?>
                                                                                                <option class="<?php echo 'wclimstore_'.$loc['store_id']; ?>" value="<?php echo $loc['store_id']; ?>"><?php echo ucfirst($loc['store_name']); ?></option>
                                                                                            <?php
                                                                                            }
                                                                                            ?>
                                                                                        </select>

                            <?php }?>
                            <p class="wcmlim_change_lc_to" id="wcmlim_store_label_popup"><?php _e('Store: ', 'wcmlim'); ?></p>	
                            <?php if ($restricUsers == "on" && $roles[0] == "customer") { ?>
                            <select name="wcmlim_change_lc_to" class="wcmlim-lc-select" id="wcmlim-change-lc-select">
                            <option value="-1" <?php if (
                                !$selected_location
                            ) {
                                echo "selected='selected'";
                            } ?>><?php _e("Select", "wcmlim"); ?></option>
                            <?php foreach (
                                $locations_list
                                as $key => $loc
                            ) {
                                if (
                                    preg_match(
                                        '/^\d+$/',
                                        $user_selected_location
                                    )
                                ) {
                                    if (
                                        $user_selected_location == $key
                                    ) { ?>
                            <option 
                            class="<?php echo "wclimloc_" .
                                $loc["location_slug"]; ?>" 
                            value="<?php echo $key; ?>" 
                            data-lc-address="<?php echo base64_encode(
                                $loc["location_address"]
                            ); ?>"
                            data-lc-term="<?php echo $loc[
                                "location_termid"
                            ]; ?>" 
                            <?php if (
                                preg_match(
                                    '/^\d+$/',
                                    $user_selected_location
                                )
                            ) {
                                if ($user_selected_location == $key) {
                                    echo "selected='selected'";
                                }
                            } ?>>
                            <?php echo ucfirst($loc["location_name"]); ?>
                            </option>
                            <?php }
                                }
                            } ?>
                        </select>
                        <?php } else { ?>
                                <select name="wcmlim_change_lc_to" class="wcmlim-lc-select <?php
                                $lcselect = get_option("wcmlim_enable_location_group");
                                if ($lcselect == "on") {
                                    echo "wcmlim-lc-select-2";
                                }
                                ?>" id="wcmlim-change-lc-select">
                                                                <option value="-1" <?php if (
                                                                    !$selected_location
                                                                ) {
                                                                    echo "selected='selected'";
                                                                } ?>><?php _e(
                                    "Please Select",
                                    "wcmlim"
                                ); ?></option>
                                                            </select>
                                <?php }

                                } else if ((!empty($current_user_id)) || (empty($current_user_id) && $restricUsers == '')){ 
                            ?>
                        <div class="wcmlim_change_lc_to"><?php if ($Inline_title ) {
                                                            echo $Inline_title;
                                                        } else {
                                                            _e('Location: ', 'wcmlim');
                                                        }?></div>
                            <?php 
                            if(isset($roles[0]) && $roles[0] == 'customer'){ 
                                ?>
                                <select name="wcmlim_change_lc_to" class="wcmlim-lc-select" id="wcmlim-change-lc-select">
                                <option value="-1" <?php if (!$selected_location) echo "selected='selected'"; ?>><?php _e('Select', 'wcmlim') ?></option>
                                <?php
                                foreach ($locations_list as $key => $loc) {
                                if (preg_match('/^\d+$/', $user_selected_location)) {
                                    if ($user_selected_location == $key) {
                                ?>
                                <option 
                                class="<?php echo 'wclimloc_'.$loc['location_slug']; ?>" 
                                value="<?php echo $key; ?>" 
                                data-lc-address="<?php echo base64_encode($loc['location_address']); ?>"
                                data-lc-term="<?php echo $loc['location_termid']; ?>" 
                                <?php
                                    if (preg_match('/^\d+$/', $user_selected_location)) {
                                if ($user_selected_location == $key) 
                                $is_loc_cookieset = $_COOKIE['wcmlim_selected_location'];
                                if($is_loc_cookieset == $key){
                                    echo "selected='selected'"; 
                                }

                                } 
                                ?>>
                                <?php echo ucfirst($loc['location_name']); 
                                ?>
                                </option>
                            <?php 
                                    }
                                }else{
                                    ?>
                                    <option 
                                class="<?php echo 'wclimloc_'.$loc['location_slug']; ?>" 
                                value="<?php echo $key; ?>" 
                                data-lc-address="<?php echo base64_encode($loc['location_address']); ?>"
                                data-lc-term="<?php echo $loc['location_termid']; ?>" 
                                <?php
                                    if (preg_match('/^\d+$/', $selected_location)) {
                                if ($selected_location == $key) 
                                echo "selected='selected'";
                                } 
                                ?>>
                                <?php echo ucfirst($loc['location_name']); 
                                ?>
                                </option>
                                    <?php
                                }
                        }	?>
                        </select>
                            <?php }
                            else if(isset($roles[0]) && $roles[0] != ''){ ?>
                            <select name="wcmlim_change_lc_to" class="wcmlim-lc-select" id="wcmlim-change-lc-select">
                            <option value="-1" <?php if (!$selected_location) echo "selected='selected'"; ?>><?php _e('Select', 'wcmlim') ?></option>
                            <?php
                            foreach ($locations_list as $key => $loc) {
                               
                            ?>
                                <option 
                                class="<?php echo 'wclimloc_'.$loc['location_slug']; ?>" 
                                value="<?php echo $key; ?>" 
                                data-lc-address="<?php echo base64_encode($loc['location_address']); ?>"
                                data-lc-term="<?php echo $loc['location_termid']; ?>" 
                                <?php if (preg_match('/^\d+$/', $selected_location)) {
                                if ($selected_location == $key) 
                                echo "selected='selected'";
                                } ?>>
                                <?php echo ucfirst($loc['location_name']); 
                                ?>
                                </option>
                            <?php }	?>
                        </select>	

                        <?php } 
                        else if ($restricUsers == '') {
                            ?>
                            <select name="wcmlim_change_lc_to" class="wcmlim-lc-select" id="wcmlim-change-lc-select">
                            <option value="-1" <?php if (!$selected_location) echo "selected='selected'"; ?>><?php _e('Select', 'wcmlim') ?></option>
                            <?php
                            foreach ($locations_list as $key => $loc) {
                            ?>
                                <option 
                                class="<?php echo 'wclimloc_'.$loc['location_slug']; ?>" 
                                value="<?php echo $key; ?>" 
                                data-lc-address="<?php echo base64_encode($loc['location_address']); ?>"
                                data-lc-term="<?php echo $loc['location_termid']; ?>" 
                                <?php if (preg_match('/^\d+$/', $selected_location)) {
                                if ($selected_location == $key) 
                                echo "selected='selected'";
                                }
                                if(isset($loc_term_id)) {
                                    if($loc_term_id == $loc['location_termid'])
                                    {
                                        echo "selected='selected'";
                                    }
                                }
                                 ?>>
                                <?php echo ucfirst($loc['location_name']); 
                                ?>
                                </option>
                            <?php }	?>
                        </select>	

                        <?php } 
                
                    }else { 
                            ?>
                            <div class="wcmlim_change_lc_to"><?php if ($Inline_title ) {
                                                            echo $Inline_title;
                                                        } else {
                                                            _e('Location: ', 'wcmlim');
                                                        }?></div>
                        <select name="wcmlim_change_lc_to" class="wcmlim-lc-select" id="wcmlim-change-lc-select">
                        <option value="-1" <?php if (!$selected_location) echo "selected='selected'"; ?>><?php _e('Select', 'wcmlim') ?></option>
                        <?php
                        foreach ($locations_list as $key => $loc) {
                        ?>
                            <option 
                            class="<?php echo 'wclimloc_'.$loc['location_slug']; ?>" 
                            value="<?php echo $key; ?>" 
                            data-lc-address="<?php echo base64_encode($loc['location_address']); ?>"
                            data-lc-term="<?php echo $loc['location_termid']; ?>" 
                            <?php if (preg_match('/^\d+$/', $selected_location)) {
                            if ($selected_location == $key) 
                            echo "selected='selected'";
                            } ?>>
                            <?php echo ucfirst($loc['location_name']); 
                            ?>
                            </option>
                        <?php }	?>
                    </select>	
                <?php 
                    } ?>
                        <div class="er_location"></div>
                        <!-- Radio Listing Mode -->
                        <?php if ($isLocationsGroup == null || $isLocationsGroup == false ) { ?>
                            <div class="rlist_location"></div>
                        <?php } ?>
                    </div>
                    <?php
                    
                    if ($geolocation == "on" || (is_array($show_in_popup) && in_array('location_finder_in_popup', $show_in_popup))) : ?>
                        <div class="postcode-checker">
                            <div class="postcode_wcmliminput">
                                <span class="postcode-checker-div postcode-checker-div-show">
                                    <?php
                                    $globpin = isset($_COOKIE['wcmlim_nearby_location']) ? $_COOKIE['wcmlim_nearby_location'] : "";
                                    $loc_dis_un = get_option('wcmlim_location_distance');
                                    ?>
                                    <input type="text" placeholder="<?php _e('Enter Pincode/Zipcode', 'wcmlim'); ?>" required class="class_post_code_global" name="post_code_global" value="<?php if($globpin != 0){esc_html_e($globpin);} ?>" id="elementIdGlobal">
                                    <input type="button" class="button" id="submit_postcode_global" value="<?php _e('Apply', 'wcmlim'); ?>">
                                    <input type='hidden' name="global_postal_check" id='global-postal-check' value='true'>
                                    <input type='hidden' name="global_postal_location" id='global-postal-location' value='<?php esc_html_e($globpin); ?>'>
                                    <input type='hidden' name="product_location_distance" id='product-location-distance' value='<?php esc_html_e($loc_dis_un); ?>'>
                                </span>
                            </div>
                            <?php if ($useLc == "on") { ?>
                                <div class="wclimlocsearch" style="display:none;">
                                    <a id="currentLoc" class="currentLoc">
                                        <i id="currentLoc" class="fas fa-crosshairs currentLoc">
                                        </i>
                                        &nbsp; Use Current Location</a> 
                                </div>
                            <?php } ?>
                            <div class="search_rep">
                                <div class="postcode-checker-response"></div>
                                <a class="postcode-checker-change postcode-checker-change-show" href="#" data-wpzc-form-open="" style="display: none;">
                                    <i class="fa fa-edit" aria-hidden="true"></i>
                                </a>
                            </div>
                            <div class="postcode-location-distance"></div>
                        </div>
                    <?php endif; ?>
                </div>
                <?php wp_nonce_field('wcmlim_change_lc', 'wcmlim_change_lc_nonce'); ?>
                <input type="hidden" name="action" value="wcmlim_location_change">
            </form>
        </div>
        </div>
        <!-- default design End-->
    <?php }	}									
} /** is_preferred */