<?php
include_once __DIR__ . '/lib/user.php';
session_start();


$rooms = User::closestRooms();



?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/styles.css"/>
    <script type="text/javascript" src="js/jquery.js"></script>
    <!--<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=ABQIAAAAbxzsFE1Tnds0s8NIJqLUMxRxLUBlQ194WdVPHGj2N6p3EiVV5BRI3-2ofSS8vPN7zzz41hH1cMeUEQ"  
  type="text/javascript"></script>-->
     <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>

  </head>
  <body>
    <header>
        <div id="header-wrapper">
          <div id="header-container">
            <div id="login-button" class="button left">
              <div class="button-outer">
                <div class="button-inner">
                  <img class="left" src="img/login.png"/>
                  <span class="button-text right">Log In</span>
                </div>
              </div>
            </div>
            <div id="register-button" class="button right">
              <div class="button-outer">
                <div class="button-inner">
                  <img class="left" src="img/register.png"/>
                  <span class="button-text right">Register</span>
                </div>
              </div>
            </div>
            <h1>XYMe</h1>
          </div>
        </div>
    </header>
    <div id="main-container">
      <div class="push">
      </div>
      <div id="map_canvas" style="width:100%; height:400px"></div>
	  
	  
	  <script> 
	//Stores school info for javascript
	var locations = new Array();
	var size = 0;
</script>

<?php foreach($rooms as $key=>$row){ ?>
		<script>	
			locations[size] = [ "<?php echo $row['room_name'] ?>", "<?php echo $row['latitude'] ?>", "<?php echo $row['longitude'] ?>" ]; 
			size++;
		</script>
<?php } ?>
	  
	  <script type="text/javascript">
      
        geocoder = new google.maps.Geocoder();	//Used to find latlong
		
		//Initial Settings
		var map = new google.maps.Map(document.getElementById('map_canvas'), {
			zoom: 1,
			center: new google.maps.LatLng(42.2852, -71.7214),
			mapTypeId: google.maps.MapTypeId.ROADMAP
		});
		
		
		infowindow = new google.maps.InfoWindow();	//Window
		
		//Makes the infowindow close on clicking the map
		google.maps.event.addListener(map, 'click', function() {
			infowindow.close();
		});
		
		
		//--------------------------------
		//-- Location loop ---------------
		//--------------------------------
		for (i = 0; i < locations.length; i++) { 
		  
			//Sets the content for the info window
			var content = "";
			setpoint( locations[i][1], locations[i][2], locations[i][0]  );
			  
		}
		
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
      
    </script>
    </div>
  </body>
</html>