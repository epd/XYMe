<?php
include_once __DIR__ . '/database.php';

class User {

	/**
	* Implements login().
	* 
	* @param $user
	* The users username.
	* @param $pass
	* The users password.
	*/
	public static function login ( $user, $pass ){
		
		//Connects to database
		$dbconn = self::connect();
		
		//Performs Login
		if( self::performLogin( $dbconn, $user, $pass ) ){
			//Login Failed
			return 'Invalid username and/or password';
		}	
	}
	
	/**
	* Implements logout().
	*
	*/
	public static function logout (){
		unset( $_SESSION['user'] );
		unset( $_SESSION['uid'] );
		unset( $_SESSION['gid'] );
		unset( $_SESSION['latitude'] );
		unset( $_SESSION['longitude'] );
		unset( $_SESSION['room_id'] );
		session_destroy();
		setcookie("xyme_user", FALSE, time() - 3600, '/');
		setcookie("xyme_uid", FALSE, time() - 3600, '/');
		setcookie("xyme_gid", FALSE, time() - 3600, '/');
		setcookie("xyme_latitude", FALSE, time() - 3600, '/');
		setcookie("xyme_longitude", FALSE, time() - 3600, '/');
		setcookie("xyme_room_id", FALSE, time() - 3600, '/');
	}
	
	/**
	* Implements register().
	*
	* @param $user
	* The users username.
	* @param $pass
	* The users password
	* @param $group
	* The users group
	*
	*/
	public static function register ( $user, $pass, $group ){
	
		//Connects to Database
		$dbconn = self::connect();	
		
		//Makes sure all variables set
		if( $user == null || $pass == null || $group == null ){
			return 'Please Fill in all Fields';
		}
		
		//Checks if username is taken
		if (self::checkUsernameTaken($dbconn, $user)){
			return 'Username already exists';
		}	
		
		//Makes sure pass is at least 3 characters
		if( strlen( $pass ) < 3 ){
			return 'Password must contain at least 3 characters';
		}
		
		//Stores the new user
		self::storeUser($dbconn, $user, $pass, $group);
	}
	
	/**
	* Implements verifySession().
	*
	*/
	public static function verifySession(){
		
		//No session
		if( !isset( $_SESSION['user'] ) )
			return 0;
		
		//Connects to Database
		$dbconn = self::connect();
		
		//Performs Query
		$verify_stmt = $dbconn->prepare('SELECT * FROM users WHERE username=:username');
		$verify_stmt->execute(array(
		  ':username' => $_SESSION['user'],
		));
		
		$row = $verify_stmt->fetch();
		
		//Invalid Session - Fake user
		if( $row == null || $row['user_id'] != $_SESSION['uid'] || $row['group_id'] != $_SESSION['gid']  ){
			return 0;
		}
		
		return 1;	
	}
	
	/**
	* Implements closestRooms().
	*
	*@return
	* Array of 5 closest rooms, distance is in MILES
	*
	*/
	public static function closestRooms(){
	
		//Connects to Database
		$dbconn = self::connect();
		
		//Query for rooms
		$rooms_stmt = $dbconn->prepare("
						SELECT room_id, room_name, latitude, longitude, SQRT(
							POW(69.1 * (latitude - :startlat ), 2) +
							POW(69.1 * ( :startlng - longitude) * COS(latitude / 57.3), 2)) AS distance
						FROM rooms 
						ORDER BY distance ASC
						LIMIT 5
					");
		$rooms_stmt->execute(array(
		  ':startlat' => $_SESSION['latitude'],
		  ':startlng' => $_SESSION['longitude'],
		));	
	
		return $rooms_stmt->fetchAll();	
	}
	
	/**
	* Implements createRoom().
	*
	*@param $room_name
	* Name of room
	*
	*/
	public static function createRoom( $room_name ){
	
		//Connects to Database
		$dbconn = self::connect();
		
		//Query for rooms
		$rooms_stmt = $dbconn->prepare('
						INSERT INTO	rooms ( room_name, latitude, longitude )
						  VALUES ( :name, :lat, :lng )
					');
		$rooms_stmt->execute(array(
		  ':name' => $room_name,
		  ':lat' => $_SESSION['latitude'],
		  ':lng' => $_SESSION['longitude'],		  
		));	
		
		$return_stmt = $dbconn->prepare('
						SELECT LAST_INSERT_ID()
					');
		$return_stmt->execute();	
		
		$room_id = $return_stmt->fetch();
		return $room_id[0];
		
	}
	
	public static function joinRoom( $room_id ){
		
		$_SESSION['room_id'] = $room_id;
		setcookie("xyme_room_id", $_SESSION['room_id'], time() + 3600, '/');
					
	}
	
		public static function leaveRoom(  ){
		
		$_SESSION['room_id'] = null;
		setcookie("xyme_room_id", null, time() + 3600, '/');
					
	}
	
	//---------------------------------------------------------------
	//------------------- Private Funcitons -------------------------
	//---------------------------------------------------------------
	
	/**
	* Implements connect().
	*
	*/
	private static function connect() {
		return Database::connect();
	}
	
	/**
	* Implements performLogin().
	*
	* @param $dbconn
	* The database connection resource.
	* @param $user
	* The users username.
	* @param $pass
	* The users password
	*
	*/
	private static function performLogin ( $dbconn, $user, $pass ){
		require __DIR__ . '/../config.php';
		
		$salted = sha1($config['salt'] . $pass);

		//Performs Query
		$login_stmt = $dbconn->prepare('SELECT * FROM users WHERE username=:username AND password=:password');
		$login_stmt->execute(array(
		  ':username' => $user,
		  ':password' => $salted,
		));	
		
		//Checks if user information is correct
		$row = $login_stmt->fetch();
		if( $row ){
			$_SESSION['user'] = $row['username'];
			$_SESSION['uid'] = $row['user_id'];
			$_SESSION['gid'] = $row['group_id'];
			$_SESSION['latitude'] = $_COOKIE['xyme_latitude'];
			$_SESSION['longitude'] = $_COOKIE['xyme_longitude'];
			$_SESSION['room_id'] = null;
			setcookie("xyme_user", $_SESSION['user'], time() + 3600, '/');
			setcookie("xyme_uid", $_SESSION['gid'], time() + 3600, '/');
			setcookie("xyme_gid", $_SESSION['gid'], time() + 3600, '/');
			setcookie("xyme_room_id", json_encode($_SESSION['room_id']) , time() + 3600, '/');
			return 0;
		}
		else {
			self::logout();
			return 1;		
		}		
	}
	
	/**
	* Implements storeUser().
	*
	* @param $dbconn
	* The database connection resource.
	* @param $user
	* The users username.
	* @param $pass
	* The users password
	* @param $group
	* The users group
	*
	*/
	private static function storeUser ( $dbconn, $user, $pass, $group ){
		require __DIR__ . '/../config.php';
		
		$salted = sha1($config['salt'] . $pass);

		$register_stmt = $dbconn->prepare('insert into users (group_id, username, password) values( :groupid,
		  :username, :password)');
		$register_stmt->execute(array(
		  ':groupid' => $group,
		  ':username' => $user,
		  ':password' => $salted,
		));	
	}
	
	/**
	* Implements checkUsernameTaken().
	*
	* @param $dbconn
	* The database connection resource.
	* @param $user
	* The users username.
	*
	*/
	private static function checkUsernameTaken( $dbconn, $user ){
		require __DIR__ . '/../config.php';

		$taken_stmt = $dbconn->prepare('SELECT * FROM users WHERE username=:username');
		$taken_stmt->execute(array(
		  ':username' => $user
		));
		
		if( $taken_stmt->fetch() != null ){
			return 1;
		}
		
		return 0;	
	}
}
?>
