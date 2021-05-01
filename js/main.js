var $j = jQuery.noConflict();

$j(document).ready(function(){
  $j(".custom-login-form").validate({
    rules: {
      custom_user: {
        required: true
      },
      custom_pass: {
        required: true
      }
    },
    messages: {
      custom_user: {
        required: "field is required"
      },
      custom_pass: {
        required: "password is required"
      }
    },

    submitHandler: function (form, e) { 
      e.preventDefault();
      /*
      $j('.indicator').show();
      $j('.indicator').html('Please wait...');
      $j('.result-message').hide();
      */
      var nonce  = $j('#custom_login_nonce').val();

      var user  = $j('#custom-user').val();
      var pass  = $j('#custom-pass').val();
      
       /**
       * AJAX URL where to send data
       * (from localize_script)
       **/
   
      // Data to send
      data = {
        action: 'myaction',
        custom_login_nonce: nonce,
        custom_user: user,
        custom_pass: pass,
      };

      $j.ajax({
        type: "POST",
        dataType: 'json',
        url: ajax_object.ajaxurl,
        data: data,
        success: function(data) {
          //console.log(data);
          //if(data) {$j('.indicator').hide();}
          if(data.logged_in == true) {
            console.log(data.message)
            document.location.href = 'http://localhost/wordpress';
            //$j('.result-message').show(); // Show results div
            //$j('.result-message').html(data.message); // Add success message to results div
            //$j('.result-message').addClass('alert-success'); // Add class success to results div
          } else {
            console.log(data.message)
            //$j('.result-message').show(); // Show results div
            //$j('.result-message').html(data.message);
            //$j('.result-message').addClass('alert-danger'); // Add class failed to results div
          }
          
        }
      });      
      return false;
    }
  });
});