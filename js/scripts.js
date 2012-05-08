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
  
  $("#logout-button").click(function(e){
		$("#logout").submit()
	});
	
	$('#room-list').hide();
  $('#viewmap-button').hide();
	$("#viewlist-button").click(function(e){
		$('#room-map').hide();
		$('#room-list').show();
    $('#viewlist-button').hide();
    $('#viewmap-button').show();
	});
  $("#viewmap-button").click(function(e){
    $('#room-list').hide();
    $('#room-map').show();
    $('#viewmap-button').hide();
    $('#viewlist-button').show();
  });
  
});


//--------------------------------
//-- Gets Lat/Long and passes on -
//--------------------------------
function setpoint( lat, lng , content ){

	
		point= new google.maps.LatLng(lat,lng);		//Makes a point
		map.setCenter(point);						//Centers map at that point
		zoom = 6;									//Sets the zoom
		marker = createMarker(point,content,zoom);	//Passes information

	
}


//--------------------------------
//-- Creates the marker ----------
//--------------------------------
function createMarker(latlng, content, zoom) {

	//Creates the marker
	var marker = new google.maps.Marker({
		position: latlng,
		map: map,
	});

	//Adds a listener for the infowindow
	google.maps.event.addListener(marker, 'click', function() {
		infowindow.setContent(content); 
		infowindow.open(map,marker);
	});
}