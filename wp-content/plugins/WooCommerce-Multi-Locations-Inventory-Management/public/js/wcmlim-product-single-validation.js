jQuery(document).ready(($) => {
  $.noConflict();
  let lat;
  let lng;
  const { ajaxurl } = multi_inventory;
  const autoDetect = multi_inventory.autodetect;
  const { enable_price } = multi_inventory;
  const restricted = multi_inventory.user_specific_location;
  const showLocationInRestricted = multi_inventory.show_location_selection;
  const { instock } = multi_inventory;
  const { soldout } = multi_inventory;
  const stock_format = multi_inventory.stock_format;
  const { widget_select_type } = multi_inventory;
  const nextloc = multi_inventory.nxtloc;
  var store_on_map_arr = multi_inventory.store_on_map_arr;
  var default_zoom = multi_inventory.default_zoom;
  var setting_loc_dis_unit = multi_inventory.setting_loc_dis_unit;
  var listmode = multi_inventory.optiontype_loc;
  var sc_listmode = multi_inventory.scoptiontype_loc;
  var detailadd = multi_inventory.fulladd;
  var listformat = multi_inventory.viewformat;
  var wchideoosproduct = multi_inventory.wchideoosproduct;
  var NextClosestinStock = multi_inventory.NextClosestinStock;
  var isdefault = multi_inventory.isdefault;
  const { isClearCart } = multi_inventory;
  const { isLocationsGroup } = multi_inventory;
  jQuery(document).ready(function (t) {
    if ($("form.variations_form").length) {
      wcmlim_hide_location_selectbox();
    }
    $(".variations select").on("change", function () {
      wcmlim_hide_location_selectbox();
    });
  });
  function wcmlim_hide_location_selectbox() {
    let get_selected_variation_value = 0;
    $(".variations select").each(function () {
      var conceptName = $(this).find(":selected").val();
      if (conceptName != "") {
        get_selected_variation_value = conceptName;
      }
    });
    if (get_selected_variation_value != 0) {
      var value = this.value;
      get_selected_variation_value = value;
      $(".Wcmlim_container").show();
    } else {
      $(".Wcmlim_container").hide();
    }
  }
});
//hide location box for variation
