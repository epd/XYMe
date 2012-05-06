$(document).ready(function() {
  /* Login modal firing ----- */
  $("#login-button").click(function(e) {
    $("#register").hide();
    $("#login").show();
  });

  /* Register modal firing ----- */
  $("#register-button").click(function(e) {
    $("#login").hide();
    $("#register").show();
  });

  $("#main-container").ready(function() {
    var myLatLong = new google.maps.LatLng(42.7299534,-73.6767395);
    var myOptions = {
      zoom: 10,
      center: new google.maps.LatLng(42.7299534,-73.6767395),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    var map = new google.maps.Map(document.getElementById("map_canvas"),
            myOptions);
  })
  /*$('#map').googleMaps({
    latitude: 42.351505,
    longitude: -71.094455
  }); */
});