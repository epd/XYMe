<?php
include_once __DIR__ . '/lib/user.php';

session_start();

User::leaveRoom();

// Logout
if(isset($_POST['logout']) && $_POST['logout'] == 'true' ) {
  User::logout();
}

// User not verified
if( !User::verifySession() ) {
  header("Location: " . $_COOKIE['xyme_php_path']);
}

// User added room
if( isset( $_POST['join'] ) ){
	User::joinRoom( $_POST['room_id'] );
	header("Location: http://" . $_SERVER['SERVER_NAME'] . ":3000/" );
}

//Create Rooms
if( isset( $_POST['create'] ) &&  isset( $_POST['create-name'] ) ){
	$room_id = User::createRoom( $_POST['create-name'] );
	User::joinRoom( $room_id );
	header("Location: http://" . $_SERVER['SERVER_NAME'] . ":3000/" );	

}


?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/styles.css"/>
    <script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/scripts.js"></script>
    
  </head>
  <body>
    <header>
        <div id="header-wrapper">
          <div id="header-container">
            <div id="logout-button" class="button left">
              <div class="button-outer">
                <div class="button-inner">
                  <img class="left" src="img/logout.png"/>
                  <span class="button-text right">Log Out</span>
                </div>
              </div>
            </div>
            <div id="viewlist-button" class="button right">
              <div class="button-outer">
                <div class="button-inner">
				          <img class="left" src="img/list.png"/>
                  <span class="button-text right">View List</span>	
                </div>
              </div>
            </div>
            <div id="viewmap-button" class="button right">
              <div class="button-outer">
                <div class="button-inner">
                  <img class="left" src="img/map.png"/>
                  <span class="button-text right">View Map</span>  
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
      <section id="room-map">
        <h3> Closest Rooms Map: </h3>
		    <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>

		<?php 
				$rooms = User::closestRooms();
		?>
		
		
		<div id="map_canvas" style="width:100%; height:400px"></div>
	  
	  
		<script> 
			//Stores school info for javascript
			var locations = new Array();
			var size = 0;
		</script>

		<?php foreach($rooms as $key=>$row){ ?>
				<script>	
					locations[size] = [ "<?php echo $row['room_name'] ?>", "<?php echo $row['latitude'] ?>", "<?php echo $row['longitude'] ?>", "<?php echo $row['room_id'] ?>",  ]; 
					size++;
				</script>
		<?php } ?>
		  
		<script type="text/javascript">
		  
			geocoder = new google.maps.Geocoder();	//Used to find latlong
			
			//Initial Settings
			var map = new google.maps.Map(document.getElementById('map_canvas'), {
				zoom: 15,
				//center: new google.maps.LatLng(42.2852, -71.7214),
				mapTypeId: google.maps.MapTypeId.ROADMAP,
        panControl: false,
        zoomControl: false,
        mapTypeControl: false,
        scaleControl: false,
        streetViewControl: false,
        overviewMapControl: true
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
				var content = "<form action='join.php' method='post'>" + locations[i][0] + "- <input type='hidden' name='room_id' value='" + locations[i][3] + "'><input type='submit' name='join' value='Join Room'></td></form>";	
				setpoint( locations[i][1], locations[i][2], content  );
				  
			} 
			
		</script>
			
      </section>

    <div id='create'>
      <form action="join.php" method="post"> 
        <label>Create Room Name: </label><input type='text' name='create-name'>
        <input type='submit' value='Create' name='create'>
      </form>
    </div>
	  
	  <section id="room-list">
        <h3> Closest Rooms: </h3>
			<?php 
				$roomslist = User::closestRooms();
				
				echo "<table><tr id='header'><th>Room Name</th><th>Distance</th><th>Join Room</th></tr>";
				
				foreach( $roomslist as $row ){
				
					echo "<tr>";
					echo "<td>".$row['room_name']."</td>"; 
					echo "<td>".number_format($row['distance'], 2, '.', '')." miles</td>";							
					echo "<form action='join.php' method='post'>";
					echo "<input type='hidden' name='room_id' value='".$row['room_id']."'>";
					echo "<td><input type='submit' name='join' value='Join'></td>";
					echo "</form>";						
					echo "</tr>";
				}
				echo "</table>";				
			?>
      </section>
	  

	  
    </div>
  </body>
</html>

<form id="logout" method="post" action="join.php" >
	<input type="hidden" name="logout" value="true">
</form>
