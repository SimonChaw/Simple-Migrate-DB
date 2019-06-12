jQuery(document).ready(function ($) {

  let lastUpdate;
  let updateActive = false;
  let lastID;
  let me;

  // Random number from 0 to length
  const randomNumber = (length) => {
    return Math.floor(Math.random() * length)
  }

  const generateID = (length) => {
    const possible =
      "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    let text = "";

    for (let i = 0; i < length; i++) {
      text += possible.charAt(randomNumber(possible.length));
    }

    return text;
  }

  $(document).on('heartbeat-send', function(e, data) {
      if (lastID) {
        data.processId = lastID;
      }
  });

  // Listen for the custom event "heartbeat-tick" on $(document).
  $(document).on( 'heartbeat-tick', function(e, data) {

      // Log the response for easy proof it works
      console.log( data );

  });

  var app = new Vue({
    el: '#app',
    data: {
      heartBeatInterval : 1000,
      currentStage: 2,
      currentProcess : {
        processLength : 0,
        currentStep : -1,
        processName : '',
        active : false
      },
      message: 'Hello Vue!',
      secure_key: '0asdas823102131', // Need to get this loading in some other way
    },
    methods: {
      packfiles : function() {
        let id = generateID(6);
        lastID = id;
        this.currentStage = 3;
        this.currentProcess.processName = "Packing up your site!";
        var postData = {
            action : 'pack',
            id : id,
        };

        this.currentProcess.active = true;
        me = this;
        $.post(ajaxurl, postData, function(response){
            if (response.success === true) {
              me.packsql();
            }
        }, 'json');

    },
    packsql : function() {
      // use me from now on. Scope has been lost
      me.currentProcess.processName = "Preparing your SQL..."
      let id = generateID(6);
      lastID = id;

      var postData = {
          action : 'packsql',
          id : id,
      };

      $.post(ajaxurl, postData, function(response){
          if (response.success === true) {
            me.currentStage = 4;
          }
      }, 'json');
    }
  }
  });

});
