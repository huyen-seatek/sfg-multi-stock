var localization = {
  setting_loc_dis_unit: "km",
    away: "away"
};

export function GlobalMapList(elementId, rangeId) {
  if (jQuery("#" + elementId).length > 0) {
    var elmap = document.getElementById(elementId);

    var search_lat, search_lng;
    var infowindow = new google.maps.InfoWindow();
    
    var marker, i;
    if (elmap) {
      elmap.addEventListener("focus", (e) => {
        const input = document.getElementById(elementId);

        const options = {};
        const autocomplete = new google.maps.places.Autocomplete(
          input,
          options
        );
        google.maps.event.addListener(autocomplete, "place_changed", () => {
          const place = autocomplete.getPlace();
          var searchedaddress = jQuery("#" + elementId).val();
          search_lat = place.geometry.location.lat();
          search_lng = place.geometry.location.lng();
          for (i = 0; i < locations.length; i++) {
            if (locations[i][4] == "origin") {
              map = new google.maps.Map(document.getElementById("map"), {
                zoom: parseInt(localization.default_zoom),
                center: new google.maps.LatLng(search_lat, search_lng),
                mapTypeId: google.maps.MapTypeId.ROADMAP,
              });
              marker = new google.maps.Marker({
                position: new google.maps.LatLng(search_lat, search_lng),
                map: map,
                icon: icon,
                label: {
                  fontFamily: "'Font Awesome 5 Free'",
                  fontWeight: "900", 
                  color: "#FFFFFF", 
                },
              });

              google.maps.event.addListener(
                marker,
                "click",
                (function (marker, i) {
                  return function () {
                    infowindow.setContent(
                      "<div class='locator-store-block'><h4>" +
                        searchedaddress +
                        "</h4></div>"
                    );
                    infowindow.open(map, marker);
                  };
                })(marker)
              );
            } else {
              var markers = locations.map(function (location, i) {
                var infowindow = new google.maps.InfoWindow({
                  maxWidth: 250,
                });
                var marker = new google.maps.Marker({
                  position: new google.maps.LatLng(
                    locations[i][1],
                    locations[i][2]
                  ),
                  map: map,
                });
                google.maps.event.addListener(
                  marker,
                  "click",
                  (function (marker, i) {
                    return function () {
                      infowindow.setContent(locations[i][0]);
                      infowindow.open(map, marker);
                    };
                  })(marker, i) 
                );
                return marker;
              });
              // // Add a marker clusterer to manage the markers.
              new MarkerClusterer(map, markers, {
                imagePath:
                  "https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m",
              });
            }
          }
          alies_calDistanceSearch.calculate_distance_search(
            search_lat,
            search_lng,
            rangeId
          );
          
        });
      });
    }
    var rangeInputLength =
      jQuery("#" + rangeId).length > 0 ? jQuery("#" + rangeId).val() : 0;
    if (rangeInputLength > 0) {
      jQuery(document).on("change", "#" + rangeId, function() {
        rangeInputCallback(rangeId);
      });
        
    }
  }
}

export function rangeInputCallback(rangeId) {
  var rangeInputLength =
    jQuery("#" + rangeId).length > 0 ? jQuery("#" + rangeId).val() : 0;
  if (rangeInputLength > 0) {
    var rangeInput = document.getElementById(rangeId).value;
    jQuery(".miles").each(function () {
      if (Math.round(rangeInput) < Math.round(jQuery(this).data("value"))) {
        var divid = jQuery(this).data("id");
        jQuery("#" + divid).hide(350);
      } else {
        var divid = jQuery(this).data("id");
        jQuery("#" + divid).show(350);
      }
    });
    if (localization.setting_loc_dis_unit && localization.away) {
      jQuery("#" + rangeId).closest(".range-bar").find("#rangedisplay").html(rangeInput + " " + localization.setting_loc_dis_unit + " " + localization.away);
    }
  }
}
