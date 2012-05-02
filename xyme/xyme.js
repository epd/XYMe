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

        // Make sure we scroll to the bottom
        $(document).scrollTop($("#main-container").height());
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
    }

    // Make sure we scroll to the bottom
    setTimeout(function () {
      $(document).scrollTop($("#main-container").height());
    }, 500);
  });
}

if (Meteor.is_server) {
  Meteor.startup(function () { /* ... */ });
}
