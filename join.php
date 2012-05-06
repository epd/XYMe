<?php
include_once __DIR__ . '/lib/user.php';

session_start();

// Logout
if(isset($_POST['logout']) && $_POST['logout'] == 'true' ) {
  User::logout();
}

// User not verified
if( !User::verifySession() ) {
  header("Location: http://" . $_SERVER['SERVER_NAME'] );
}

// User added room
if( isset( $_POST['join'] ) ){

	User::joinRoom( $_POST['room_id'] );
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
      <section id="login">
        <h3> Closest Rooms: </h3>
			<?php 
				$rooms = User::closestRooms();
				
				echo "<table><tr><th>Room Name</th><th>Distance</th><th>Join Room</th></tr>";
				
				foreach( $rooms as $row ){
				
					echo "<tr><form action='join.php' method='post'>";
					echo "<td>".$row['room_name']."</td>"; 
					echo "<td>".number_format($row['distance'], 2, '.', '')." miles</td>";							
					if( $_SESSION['room_id'] == null  || !in_array( $row['room_id'], $_SESSION['room_id'] ) ){ 
						echo "<input type='hidden' name='room_id' value='".$row['room_id']."'>";
						echo "<td><input type='submit' name='join' value='Join'></td>";
					} else {
						echo "<td>In Room</td>";
					}
					
					echo "</form></tr>";
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

<script>
	$("#logout-button").click(function(e){
		$("#logout").submit()
	});
</script>



