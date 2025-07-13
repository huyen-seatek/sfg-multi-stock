// Define callback function for failed attempt
export function errorCallback(error) {
  if (error.code == 1) {
    Swal.fire({
      icon: "error",
      text: "You've decided not to share your position, but it's OK. We won't ask you again.",
    });
    alies_setcookies.setcookie("wcmlim_nearby_location", " ");
    return;
  } else if (error.code == 2) {
    Swal.fire({
      icon: "error",
      text: "The network is down or the positioning service can't be reached.You've decided not to share your position, but it's OK. We won't ask you again.",
    });
    return;
  } else if (error.code == 3) {
    Swal.fire({
      icon: "error",
      text: "The attempt timed out before it could get the location data.",
    });
    return;
  } else {
    Swal.fire({
      icon: "error",
      text: "Geolocation failed due to unknown error.",
    });
    return;
  }
  localStorage.setItem("dialogShown", 1);
}
