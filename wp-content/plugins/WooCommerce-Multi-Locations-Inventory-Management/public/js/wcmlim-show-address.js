jQuery(document).ready(function (c) {
  jQuery(document).on("click", "#wcmlim_pickup", function (a) {
    var l = c("#wcmlim_pickup :selected").attr("data-termid");
    jQuery.ajax({
      type: "POST",
      url: admin_url.ajax_url,
      data: { action: "wcmlim_show_address", location_id: l },
      success: function (c) {
        var a = JSON.parse(c),
          l = a.street_address,
          e = a.wcmlim_city,
          i = a.wcmlim_postcode,
          t = a.wcmlim_state;
        a.wcmlim_state_code;
        var s = a.wcmlim_country_state;
        a.wcmlim_country_code;
        var m = a.wcmlim_email,
          r = a.wcmlim_phone;
        if ("" == e) {
          var d = "";
          "" != l && (d += l + ","),
            "" != i && (d += i + ","),
            "" != t && (d += t + ","),
            "" != s && (d += s + ",");
        } else {
          var d = "";
          "" != l && (d += l + ","),
            "" != e && (d += e + ","),
            "" != i && (d += i + ","),
            "" != t && (d += t + ","),
            "" != s && (d += s + ",");
        }
        "" != m && (d = d + "<br> <b>Email Address:</b> " + m),
          "" != r && (d = d + "<br> <b>Phone No</b> - " + r),
          "" != d &&
            jQuery(".local_pickup_address").html(
              '<p class="local_pickup_address_html"> <small>Pickup Address for ' +
                e +
                "</small> : " +
                d +
                "</p>"
            ),
          void 0 == i &&
            jQuery(".local_pickup_address").html(
              '<p class="local_pickup_address_html"></p>'
            ),
          jQuery("body").trigger("update_checkout");
      },
      error(c) {
        console.log(c);
      },
    });
  });
});
