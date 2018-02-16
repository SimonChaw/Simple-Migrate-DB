jQuery(document).ready(function ($) {

  var Simple_Migrate_DB = {

    vars : {
      btn_disabled : false,
      requested_tables : [],
      all_tables : [],
      is_valid_url : function(str){ //from https://stackoverflow.com/questions/5717093/check-if-a-javascript-string-is-a-url
        var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name and extension
        '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
        '(\\:\\d+)?'+ // port
        '(\\/[-a-z\\d%@_.~+&:]*)*'+ // path
        '(\\?[;&a-z\\d%@_.,~+&:=-]*)?'+ // query string
        '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
        return pattern.test(str);
      },
      url : ''
    },

    init : function() {
        this.request_tables();
        this.migrate_db();
    },

    //Functions for selecting which tables will be migrated
    request_tables : function() {
        //Request a list of tables for the old wordpress site
        $( document.body ).on( 'click', '#btn-request', function( e ) {
                e.preventDefault();

                var site = $( '#txt-site' ).val();

                if (Simple_Migrate_DB.vars.is_valid_url(site)) {
                  console.log('accepted!');
                  Simple_Migrate_DB.vars.url = site;
                } else {
                  $('#smdb-1-warnings').html('<br /><div class="notice notice-error is-dismissible"><p><strong>Error: Please enter a valid url.</strong></p></div>');
                  return;
                }

                var postData = {
                    action : 'request_tables',
                    url : site
                };

                $.post(ajaxurl, postData, function(response){
                  if (response.success === true) {
                    console.log(response);
                    $('#smdb-1').hide();
                    $('#smdb-2').show();
                    $( '#selection' ).slideDown();
                    for (var i = 0; i < response.tables.length; i++) {
                      if (response.tables[i].name !== 'wp_users' && response.tables[i].name !== 'wp_options' && response.tables[i].name !== 'wp_usermeta') {
                        $( '#tbl-list' ).append('<tr>');
                        $( '#tbl-list' ).append('<td><input id="cb-select-' + i + '" value="' + response.tables[i].name + '" type="checkbox"></td>');
                        $( '#tbl-list' ).append('<td>' + response.tables[i].name  + '</td>');
                        $( '#tbl-list' ).append('</tr>');
                        Simple_Migrate_DB.vars.all_tables.push(response.tables[i].name);
                      }
                    }
                  }
                }, 'json');
        });
        //Determine what tables will be migrated
        $( document.body ).on( 'click', '[type=checkbox]', function( e ) {
                if (e.target.checked === true && e.target.id !== 'cb-select-all') {
                    Simple_Migrate_DB.vars.requested_tables.push(e.target.value);
                } else {
                    var index = Simple_Migrate_DB.vars.requested_tables.indexOf(e.target.value);
                    if (index > -1) {
                        Simple_Migrate_DB.vars.requested_tables.splice(index, 1);
                    }
                }
                //User is sending all tables. Requested tables = All available tables
                if (e.target.id === 'cb-select-all') {
                    $('[type=checkbox]').prop('checked', e.target.checked);
                    Simple_Migrate_DB.vars.requested_tables = e.target.checked ? Simple_Migrate_DB.vars.all_tables : [];
                }
        });

        //Confirm table choices.
        $( document.body ).on( 'click', '#btn-finish-tables', function( e ) {
                e.preventDefault();
                if (Simple_Migrate_DB.vars.requested_tables.length > 0) {

                }
        });
    },

    migrate_db : function() {
        $( document.body ).on( 'click', '#btn-finish-tables', function( e ) {
                if (Simple_Migrate_DB.vars.requested_tables.length > 0) {
                    e.preventDefault();

                    var postData = {
                        action : 'migrate_db',
                        url : Simple_Migrate_DB.vars.url,
                        tables : Simple_Migrate_DB.vars.requested_tables
                    };

                    $.post(ajaxurl, postData, function(response){
                        console.log(response);

                    }, 'json');
                }
        });
    },

  }

  Simple_Migrate_DB.init();

});
