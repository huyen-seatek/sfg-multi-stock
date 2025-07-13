import * as alies_sucCalbk from "./wcmlim_success_callback.js";
import * as alies_errCalbk from "./wcmlim_error_callback.js";
import * as alies_setcookies from "./wcmlim_setcookies.js";

export function showPosition() { 
  if (navigator.userAgent.indexOf("Safari") != -1) {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        alies_sucCalbk.successCallback,
        showSafariBrowser
      );
    }
  } else {
    // If geolocation is available, try to get the visitor's position
    if (navigator.geolocation) {
      navigator.permissions
        .query({ name: "geolocation" })
        .then((permissionStatus) => {
          // Don't popup error notice every time a page is visited if user has already denied location request
          if (permissionStatus.state === "denied") {
            return;
          }
          navigator.geolocation.getCurrentPosition(
            alies_sucCalbk.successCallback,
            alies_errCalbk.errorCallback
          );
        });
    }
  }
}

// Show error log
function showSafariBrowser(error) {
  switch (error.code) {
    case error.PERMISSION_DENIED:
      Swal.fire({
        icon: "error",
        text: "You've decided not to share your position, but it's OK. We won't ask you again.",
      });
      alies_setcookies.setcookie("wcmlim_nearby_location", " ");
      break;
    case error.POSITION_UNAVAILABLE:
      Swal.fire({
        icon: "error",
        text: "Location information is unavailable.",
      });
      break;
    case error.TIMEOUT:
      Swal.fire({
        icon: "error",
        text: "The request to get user location timed out.",
      });
      break;
    case error.UNKNOWN_ERROR:
      Swal.fire({
        icon: "error",
        text: "An unknown error occurred.",
      });
      break;
  }
}
