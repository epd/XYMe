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
			echo 'Invalid username and/or password';
			self::getLocation();
			return 1;
		}	
		self::getLocation( $dbconn );
		return 0;		
	}
	
	/**
	* Implements logout().
	*
	*/
	public static function logout (){
		unset( $_SESSION['user'] );
		unset( $_SESSION['uid'] );
		unset( $_SESSION['gid'] );
		setcookie("xyme_user", FALSE, time() - 3600, '/');
		setcookie("xyme_uid", FALSE, time() - 3600, '/');
		setcookie("xyme_gid", FALSE, time() - 3600, '/');
		session_destroy();	
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
			echo 'Please Fill in all Fields';
			return 1;
		}
		
		//Checks if username is taken
		if (self::checkUsernameTaken($dbconn, $user)){
			echo 'Username already exists';
			return 1;
		}	
		
		//Makes sure pass is at least 3 characters
		if( strlen( $pass ) < 3 ){
			echo 'Password must contain at least 3 characters';
			return 1;
		}
		
		//Stores the new user
		self::storeUser($dbconn, $user, $pass, $group);
		return 0;
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
			setcookie("xyme_user", $_SESSION['user'], time() + 3600, '/');
			setcookie("xyme_uid", $_SESSION['gid'], time() + 3600, '/');
			setcookie("xyme_gid", $_SESSION['gid'], time() + 3600, '/');
			return 0;
		}
		else {
			unset( $_SESSION['user'] );
			unset( $_SESSION['uid'] );
			unset( $_SESSION['gid'] );
			session_destroy();
			setcookie("xyme_user", FALSE, time() - 3600, '/');
			setcookie("xyme_uid", FALSE, time() - 3600, '/');
			setcookie("xyme_gid", FALSE, time() - 3600, '/');
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
	
	/**
	* Implements getLocation().
	*
	*/
	private static function getLocation( $dbconn ){
		
		//Find IP address
		$ip = $_SERVER['REMOTE_ADDR'];

		//Loopback IP due to Apache Fallback
		if( $ip == '127.0.0.1' || $ip == '::1' )
			$ip = $hosts=gethostbynamel('');
		$location = (unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip)));

		//Set session variables
		$_SESSION['city'] = $location['geoplugin_city'];
		$_SESSION['state'] = $location['geoplugin_region'];
		$_SESSION['latitude'] = $location['geoplugin_latitude'];
		$_SESSION['longitude'] = $location['geoplugin_longitude'];
		
		//Set cookies
		setcookie("xyme_city", $_SESSION['city'], time() + 3600, '/');
		setcookie("xyme_state", $_SESSION['state'], time() + 3600, '/');
		setcookie("xyme_latitude", $_SESSION['latitude'], time() + 3600, '/');
		setcookie("xyme_longitude", $_SESSION['longitude'], time() + 3600, '/');
		
		//Add to database
		$taken_stmt = $dbconn->prepare('UPDATE users SET latitude=:latitude, longitude=:longitude WHERE username = :user');
		$taken_stmt->execute(array(
		  ':latitude' => $_SESSION['latitude'], ':longitude' => $_SESSION['longitude'], ':user' => $_SESSION['user']
		));
	
	}
	
	
	
}
?>
