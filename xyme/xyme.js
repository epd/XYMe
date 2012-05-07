// Our collection via MongoDB
Messages = new Meteor.Collection("messages");
Locations = new Meteor.Collection("locations");

if (Meteor.is_client) {
  // Expose object to the client
  window.Messages = Messages;
  window.Locations = Locations;

  // Return all of our messages in order of time
  Template.messages.messages = function () {
    return Messages.find({room: Session.get('room_id')}, {sort: {time: 1}});
  };
  Template.message.time = function () {
    var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    var date = new Date(this.time);
    var day = date.getDate();
    var month = date.getMonth();
    var year = date.getFullYear();
    var am_pm = date.getHours() > 11 ? 'PM' : 'AM';
    var hours = date.getHours() > 12 ? date.getHours() - 12 : date.getHours();
    var minutes = date.getMinutes(); 
    return months[month] + ' ' + day + ', ' + year + ' @ ' + hours + ':' + minutes + am_pm;
  };

  // Listen for "Enter" pressed on message input
  Template.input.events = {
    'keyup #message': function (e) {
      if (e.type === 'keyup' && e.which === 13) {
        // Insert our message to our collection
        Messages.insert({
          name: Session.get('name'),
          message: e.target.value,
          time: new Date(),
          room: Session.get('room_id'),
        });

        // Reset the input for the next message
        e.target.value = '';
        e.target.focus();

        // Make sure we scroll to the bottom
        setTimeout(function () {
          if ($("#messages").height() > $(window).height() - $("#header-container").height() - $("#input").height()) {
            $(document).scrollTop($("#messages").height());
          }
        }, 200);
      }
    }
  };

  
	Locations.insert({
		user: Session.get('name'),
		latitude: Session.get('latitude'),
		longitude: Session.get('longitude'),
		time: new Date()
	});

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
      if (data[0] === 'xyme_php_path') {
        Session.set('php_path', data[1]);
      }
      if (data[0] === 'xyme_room_id') {
        Session.set('room_id', data[1]);
      }
    }

    // Make sure we scroll to the bottom
    setTimeout(function () {
      if ($("#messages").height() > $(window).height() - $("#header-container").height() - $("#input").height()) {
        $(document).scrollTop($("#messages").height());
      }
    }, 600);

    // Click to logout
    $(document).on('click', '#button-left', function(e) {

      var form = document.createElement("form");
      form.setAttribute("method", "post");
      form.setAttribute("action", Session.get('php_path'));

      var hiddenField = document.createElement("input");
      hiddenField.setAttribute("type", "hidden");
      hiddenField.setAttribute("name", "logout");
      hiddenField.setAttribute("value", 1);

      form.appendChild(hiddenField);

      document.body.appendChild(form);
      form.submit();
    });
  });
}

if (Meteor.is_server) {
  Meteor.startup(function () { /* ... */ });
}
