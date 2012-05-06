<?php
include_once __DIR__ . '/lib/user.php';
session_start();

/*if(!User::verifySession()) {
  header("Location: http://" . $_SERVER['SERVER_NAME'] . ":8888/xyme");
}*/
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/styles.css"/>
    <script type="text/javascript" src="js/jquery.js"></script>
    <!--<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=ABQIAAAAbxzsFE1Tnds0s8NIJqLUMxRxLUBlQ194WdVPHGj2N6p3EiVV5BRI3-2ofSS8vPN7zzz41hH1cMeUEQ"  
  type="text/javascript"></script>-->
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBwej5YKskjPelBYV9R1L0W6Hbp5tqkiNg&sensor=false"></script>
    <script type="text/javascript" src="js/maps.js"></script>
    <script type="text/javascript" src="js/scripts.js"></script>
    <script type="text/javascript">
      function initialize() {
        var myOptions = {
          center: new google.maps.LatLng(42.7299534,-73.6767395),
          zoom: 10,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById("map_canvas"),
            myOptions);
      }
    </script>
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
    </div>
  </body>
</html>