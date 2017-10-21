/* global $*/
$(function() {
    // Initialize the Validator
    $('#register_form').validator();
    
    // User pressed "submit" button
    /* We use redirection on PHP level, so we have to deactivate this block:
    $('#register_form').on('submit', function(e) {
    //$('#register_form').submit(function(e) {
      // Check if the validator allows submit
      if (!e.isDefaultPrevented()) {
          var url = "/php/register.php";
          
          // POST the values in the background
          $.ajax({
              type: "POST",
              url: url,
              data: $(this).serialize(),
              success: function(data) {
                  // data contains object returned by login.php
                  
                  var messageAlert = 'alert-' + data.type;
                  var messageText = data.message;
                  
                  if (messageAlert && messageText) {
                    var alertBox =
                        '<div class="alert ' + messageAlert + ' alert-dismissable">' +
                        '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
                        messageText + '</div>';
                    $('#login_form').find('.messages').html(alertBox);
                    $('#login_form')[0].reset();
                  }
              }
          });

          return false;
      }
    });
    */
});
