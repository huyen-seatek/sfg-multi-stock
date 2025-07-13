import * as alies_localization from "./wcmlim_localization.js";
import * as alies_setcookies from "./wcmlim_setcookies.js";
import * as alies_getcookies from "./wcmlim_getcookies.js";
import * as alies_CommonFunction from "./wcmlim_common_functions.js"; 

var localization = alies_localization.wcmlim_localization();

export function setLocation(address) {
  let lat;
  let lng;

  var elem_Glob_return = alies_CommonFunction.elementIdGlobalFn();
  if (elem_Glob_return) {
    lat = elem_Glob_return.lat;
    lng = elem_Glob_return.lng;
  }

  var elem_return = alies_CommonFunction.elementIdFn();
  if (elem_return) {
    lat = elem_return.lat;
    lng = elem_return.lng;
  }

  if (address) {
    var postal_code = address;
  } else {
    var postal_code = jQuery(".class_post_code_global").val();
  }

  const globalPin = jQuery("#global-postal-check").val();

  if (jQuery('[name="post_code_global"]', this).val() == "") {
    jQuery(this).addClass("wcmlim-shaker");
    setTimeout(() => {
      jQuery(".postcode-checker")
        .find(".wcmlim-shaker")
        .removeClass("wcmlim-shaker");
    }, 600);
    return;
  }

  let loader = alies_CommonFunction.loaderhtml();

  jQuery(".postcode-checker-response").html(loader);

  jQuery
    .ajax({
      url: localization.ajaxurl,
      type: "post",
      data: {
        postcode: postal_code,
        globalPin,
        lat,
        lng,
        action: "wcmlim_closest_location",
      },
      dataType: "json",
      success(response) { 
        
        jQuery("#wcmlim-change-lc-select")
        .val(response.loc_key)
        .trigger("change");
        
        alies_setcookies.setcookie(
          "wcmlim_selected_location",
          response.loc_key
        );

        alies_setcookies.setcookie(
          "wcmlim_selected_location_regid",
          response.secgrouploc
        );
        jQuery(
          'select[name="wcmlim_change_sl_to"] option[value="' +
            response.secgrouploc +
            '"]'
        ).attr("selected", "selected");
        jQuery('select[name="wcmlim_change_sl_to"]').trigger("change");

        // Set the selected option in the dropdown based on the postal code
        jQuery("#select_location").val(response.loc_key); // Assuming response.loc_key is the value you want to select

        if (jQuery.trim(response.status) === "true") {
          var dunit = response.loc_dis_unit;
          if (dunit !== null) {
            var spu = dunit.split(" ");
            var n = spu[0];
          }
          if (response.locServiceRadius != "") {
            if (response.locServiceRadius <= n || !n) {
              if (response.cookie != "") {
                Swal.fire({
                  title: "Oops...!",
                  text: "We are not serving this area...",
                  icon: "info",
                  timer: 2000,
                  showConfirmButton: false,
                }).then(function () {
                  jQuery("#lc-switch-form").submit();
                });
              } else {
                jQuery(".postcode-checker-response").html();
              }
            }
          } else {
            jQuery(".postcode-checker-change").show();
            jQuery(".postcode-checker-div")
              .removeClass("postcode-checker-div-show")
              .addClass("postcode-checker-div-hide");
            if (address) {
              jQuery(".postcode-checker-response").html(
                `<i class="fa fa-search"></i> ${address}`
              );
            } else {
              jQuery(".postcode-checker-response").html(
                `<i class="fa fa-search"></i> ${postal_code}`
              );
            }
            const locationCookie = alies_getcookies.getCookie(
              "wcmlim_selected_location"
            );

            if (locationCookie == null) {
              /* do cookie doesn't exist stuff; */
              var gLocation = response.loc_key;
              jQuery("#wcmlim-change-lc-select")
                .find(":selected")
                .removeAttr("selected");
              jQuery(".rselect_location input[name=select_location]").prop(
                "checked",
                false
              );
              jQuery(".rlist_location input[name=wcmlim_change_lc_to]").prop(
                "checked",
                false
              );
              jQuery("#select_location")
                .find(":selected")
                .removeAttr("selected");
              jQuery("#wcmlim-change-lc-select")
                .find(`option[value="${gLocation}"]`)
                .prop("selected", true);
              jQuery(`#select_location option[value='${gLocation}']`).prop(
                "selected",
                true
              );
              jQuery(
                ".rselect_location input[name=select_location][value=" +
                  gLocation +
                  "]"
              ).prop("checked", true);
              jQuery(
                ".rlist_location input[name=wcmlim_change_lc_to][value=" +
                  gLocation +
                  "]"
              ).prop("checked", true);
              alies_setcookies.setcookie("wcmlim_selected_location", gLocation);
              jQuery("#lc-switch-form").submit();
            } else {
              // if(locationCookie != jQuery.trim(response.location)){
              var gLocation = response.loc_key;
              jQuery("#wcmlim-change-lc-select")
                .find(":selected")
                .removeAttr("selected");
              jQuery("#select_location")
                .find(":selected")
                .removeAttr("selected");
              jQuery(".rselect_location input[name=select_location]").prop(
                "checked",
                false
              );
              jQuery(".rlist_location input[name=wcmlim_change_lc_to]").prop(
                "checked",
                false
              ); 
              jQuery(`#wcmlim-change-lc-select option[value='${gLocation}']`) 
                .prop("selected", true);
              jQuery(`#select_location option[value='${gLocation}']`).prop(
                "selected",
                true 
              );
              jQuery(
                ".rselect_location input[name=select_location][value=" +
                  gLocation +
                  "]"
              ).prop("checked", true);
              jQuery(
                ".rlist_location input[name=wcmlim_change_lc_to][value=" +
                  gLocation +
                  "]"
              ).prop("checked", true);
            }
          }
        }
        jQuery(".wclimlocsearch").hide();
        jQuery(".class_post_code_global").val('');

      },
      error: function (data, textStatus, errorThrown) {
        jQuery(".postcode-checker-response").empty();
      },
    })
    .done((response) => {
      // if bacorder not allowed update max value of quantity field
      if (response.backorder === false) {
        const stockAvailabe = response.stock_in_location;
        jQuery(".qty").attr({ max: stockAvailabe });
      }
      // location.reload();
    });
}
