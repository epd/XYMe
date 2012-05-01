<?php
include_once __DIR__ . '/lib/user.php';

session_start();

echo '<pre>';
print_r($_POST);
echo '</pre>';

if(isset($_POST['login'])) {
  if(User::login($_POST['username'], $_POST['password']));
}
if(isset($_POST['register'])) {
  echo User::register($_POST['username'], $_POST['password'], 1);
}

if(User::verifySession()) {
?>
<script type="text/javascript">
location.href='http://localhost:3000';
</script>
<?php
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
      <section id="login">
        <h3>Login</h3>
        <form action="index.php" id="login-form" class="user-form" method="post">
            <div class="input text">
              <label for="login-username">Username</label>
              <input name="username" type="text" id="login-username"/>
            </div>
            <div class="input password">
              <label for="login-password">Password</label>
              <input name="password" type="password" id="login-password"/>
            </div>
            <div class="submit">
              <input name="login" class="login" type="submit" value="Login"/>
            </div>
        </form>
      </section>
      <section id="register">
        <h3>Register</h3>
        <form action="index.php" id="register-form" class="user-form" method="post">
            <div class="input text">
              <label for="register-username">Username</label>
              <input name="username" type="text" id="register-username"/>
            </div>
            <div class="input password">
              <label for="register-password">Password</label>
              <input name="password" type="password" id="register-password"/>
            </div>
            <div class="submit">
              <input name="register" class="register" type="submit" value="Register"/>
            </div>
        </form>
      </section>
    </div>
  </body>
</html>
<script type="text/javascript">
$('#register').hide();
</script>