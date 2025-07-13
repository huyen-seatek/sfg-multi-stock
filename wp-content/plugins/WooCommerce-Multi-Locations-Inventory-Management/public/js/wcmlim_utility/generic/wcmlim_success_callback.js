import * as wcmlim_set_location from "./wcmlim_set_location.js";

export function successCallback(position) { 
  /* Current Coordinate */
  const lat = position.coords.latitude;
  const lng = position.coords.longitude;
  const google_map_pos = new google.maps.LatLng(lat, lng);
  /* Use Geocoder to get address */
  const google_maps_geocoder = new google.maps.Geocoder();
  google_maps_geocoder.geocode(
    { latLng: google_map_pos },
    (results, status) => {
      if (status == google.maps.GeocoderStatus.OK && results[0]) {
        const pos_form_add = results[0].formatted_address;
        wcmlim_set_location.setLocation(pos_form_add);
      }
    }
  );
}
