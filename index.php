<?php
include_once __DIR__ . '/lib/user.php';

session_start();

if(isset($_POST['login'])) {
  if(User::login($_POST['username'], $_POST['password']));
}

if(User::verifySession()) {
  header("Location: http://" . $_SERVER['SERVER_NAME'] . ":3000/");
}
else {
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/test.css"/>
  </head>
  <body>
    <div id="main-container">
      <header>
        <div id="header-container">
          <div id="button-left" class="button left">
            <div class="button-outer">
              <div class="button-inner">
                <img class="left" src="img/login.png"/>
                <span class="button-text right">Log In</span>
              </div>
            </div>
          </div>
          <div id="button-right" class="button right">
            <div class="button-outer">
              <div class="button-inner">
                <img class="left" src="img/register.png"/>
                <span class="button-text right">Register</span>
              </div>
            </div>
          </div>
          <h1>XYMe</h1>
        </div>
      </header>
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
    </div>
  </body>
</html>
<!--<!doctype html>
<html>
  <head>
    <link rel="stylesheet" href="css/styles.css"/>
  </head>
  <body>
    <div id="main-wrapper">
      <section id="main-container">
        <h1>XYMe</h1>
        <section id="login">
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
      </section>
    </div>
  </body>
</html>-->
<?php
}
?>
