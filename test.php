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
	Welcome, <?php echo $_SESSION['user'] ?>
	<form action="test.php" method="post"> 
		<input type='submit' value='Logout' name='logout'>
	</form>
<?php } ?>
	
	
<div id='msg'> </div>
