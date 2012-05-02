// Our collection via MongoDB
Messages = new Meteor.Collection("messages");
Locations = new Meteor.Collection("locations");

if (Meteor.is_client) {
  // Expose object to the client
  window.Messages = Messages;
  window.Locations = Locations;

  // Return all of our messages in order of time
  Template.messages.messages = function () {
    return Messages.find({}, {sort: {time: 1}});
  };

  // Listen for "Enter" pressed on message input
  Template.input.events = {
    'keyup #message': function (e) {
      if (e.type === 'keyup' && e.which === 13) {
        // Insert our message to our collection
        Messages.insert({
          name: Session.get('name'),
          message: e.target.value,
          time: new Date()
        });

        // Reset the input for the next message
        e.target.value = '';
        e.target.focus();

        // Make sure we scroll to the bottom
        setTimeout(function () {
          $(document).scrollTop($("#main-container").height());
        }, 100);
      }
    }
  };

  Meteor.startup(function () {
    // Parse our cookie to grab username
    var cookie = document.cookie.split('; ');
    for (var c in cookie) {
      var data = unescape(cookie[c]).split('=');

      // Grab our username
      if (data[0] === 'xyme_user') {
        Session.set('name', data[1]);
      }
	  if (data[0] === 'xyme_latitude') {
		Session.set('latitude', data[1]);
	  }
	  if (data[0] === 'xyme_longitude') {
		Session.set('longitude', data[1]);
	  }
    }

	Locations.insert({
		user: Session.get('name'),
		latitude: Session.get('latitude'),
		longitude: Session.get('longitude'),
		time: new Date()
	});
	
	/*var mapOptions = {
		center: new google.maps.LatLng(Session.get('latitude'), Session.get('longitude')),
		zoom: 14,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	
	var map = new google.maps.Map(document.getElementById("map_canvas"),
        mapOptions);
	
	var marker = new google.maps.Marker({
	  position: mapOptions.center,
	  map: map,
	  title: "Hello, bitches!"	
	});*/

    // Make sure we scroll to the bottom
    setTimeout(function () {
      $(document).scrollTop($("#main-container").height());
    }, 500);
   $(document).on('click', '#button-left', function(e) {

      var form = document.createElement("form");
      form.setAttribute("method", "post");
      form.setAttribute("action", "http://129.161.32.148:8888/xyme");

      var hiddenField = document.createElement("input");
      hiddenField.setAttribute("type", "hidden");
      hiddenField.setAttribute("name", "logout");
      hiddenField.setAttribute("value", 1);

      form.appendChild(hiddenField);

      document.body.appendChild(form);
      //form.submit();
    });
  });
}

if (Meteor.is_server) {
  Meteor.startup(function () { /* ... */ });
}
