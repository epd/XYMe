// Our collection via MongoDB
Messages = new Meteor.Collection("messages");

if (Meteor.is_client) {
  // Expose object to the client
  window.Messages = Messages;

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
      }
    }
  };

  Meteor.startup(function () {
    // Parse our cookie to grab username
    var cookie = document.cookie.split('&');
    for (var c in cookie) {
      var data = cookie[c].split('=');

      // Grab our name
      if (data[0] === 'name') {
        Session.set('name', data[1]);
      }
      // Grab user ID
      if (data[0] === "id") {
        Session.set('id', data[1]);
      }
    }
  });
}

if (Meteor.is_server) {
  Meteor.startup(function () { /* ... */ });
}
