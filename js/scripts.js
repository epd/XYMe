$(document).ready(function() {
  /* Login modal firing ----- */
  $("#login-button").click(function(e) {
    $("#register").hide();
    $("#login").show();
  });

  /* Register modal firing ----- */
  $("#register-button").click(function(e) {
    $("#login").hide();
    $("#register").show();
  });
});