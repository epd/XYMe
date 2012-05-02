<?php
include_once __DIR__ . '/lib/user.php';

session_start();

if( isset( $_POST['login'] ) && isset( $_POST['login-user'] ) && isset( $_POST['login-pass'] ) ){

	User::login( $_POST['login-user'], $_POST['login-pass'] );
	
}

if( isset( $_POST['reg'] ) && isset( $_POST['reg-user'] ) && isset( $_POST['reg-pass'] ) ){

	User::register( $_POST['reg-user'], $_POST['reg-pass'], 1 );	

}

if( isset( $_POST['logout'] ) ){

	User::logout();

}

if( isset( $_POST['create'] ) &&  isset( $_POST['create-name'] ) ){

	User::createRoom( $_POST['create-name'] );
	echo 'Room Created<br />';

}

if( isset( $_POST['join'] ) ){

	User::joinRoom( $_POST['room_id'] );

}
	

?>





<?php if( !User::verifySession() ) { ?>
	<div id='login'>
		<form action="test.php" method="post"> 
			<label>Username:</label><input type='text' name='login-user'>
			<label>Password:</label><input type='password' name='login-pass'>
			<input type='submit' value='Login' name='login'>
		</form>
	</div>

	<div id='reg'>
		<form action="test.php" method="post"> 
			<label>Username:</label><input type='text' name='reg-user'>
			<label>Password:</label><input type='password' name='reg-pass'>
			<input type='submit' value='Register' name='reg'>
		</form>
	</div>
<?php } else { ?>
	Welcome, <?php echo $_SESSION['user'] ?>.
	<form action="test.php" method="post"> 		
		<input type='submit' value='Logout' name='logout'>
	</form>
	
			<div id='create'>
			<form action="test.php" method="post"> 
				<label>Create Room Name: </label><input type='text' name='create-name'>
				<input type='submit' value='Create' name='create'>
			</form>
		</div>
		
		<div id='closest'>
			<b> Closest Rooms: </b><br />
			<?php 
				$rooms = User::closestRooms();
				
				foreach( $rooms as $row ){
					echo "<form action='test.php' method='post'>";
					echo $row['room_name'].' - Distance: '.$row['distance'].' miles<br />';							
					if( $_SESSION['room_id'] == null  || !in_array( $row['room_id'], $_SESSION['room_id'] ) ){ 
						echo "<input type='hidden' name='room_id' value='".$row['room_id']."'>";
						echo "<input type='submit' name='join' value='Join Room'>";
					}
					echo "</form>";
				}
					
			?>
		</div>
		
		<div id='current'>
			<?php
				
				if( $_SESSION['room_id'] != null ){
					echo 'Current Room IDs:';
					foreach( $_SESSION['room_id'] as $row ){
						echo "<form action='test.php' method='post'>";
						echo $row;							
						//if( $_SESSION['room_id'] == null  || !in_array( $row['room_id'], $_SESSION['room_id'] ) ){ 
						//	echo "<input type='hidden' name='room_id' value='".$row['room_id']."'>";
						//	echo "<input type='submit' name='join' value='Join Room'>";
						//}
						echo "</form>";
					}
				}					
			?>
		</div>
		
		
<?php } ?>

<script>
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(successFunction, errorFunction);
	} 
		//Get the latitude and the longitude;
	function successFunction(position) {
		var lat = position.coords.latitude;
		var lng = position.coords.longitude;
		setCookie( 'xyme_latitude', lat, 1 );
		setCookie( 'xyme_longitude', lng, 1 );
	}
	function errorFunction(){
		alert("Geocoder failed");
	}
	
	function setCookie(c_name,value,exdays)
	{
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie=c_name + "=" + c_value;
	}	
</script>
	
	
<div id='msg'> </div>
