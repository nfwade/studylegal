
/*
//Now added to the google tag manager
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-73215645-1', 'auto');
ga('send', 'pageview');
*/
$(document)
  .ready(function() {

    // fix menu when passed
    $('.masthead')
      .visibility({
        once: false,
        onBottomPassed: function() {
          $('.fixed.menu').transition('fade in');
        },
        onBottomPassedReverse: function() {
          $('.fixed.menu').transition('fade out');
        }
      });

  $('#reset-modal').modal();

  $('#contact-modal').modal();

  $('#login-modal').modal();

  $('#register-modal').modal();

  $('.contact-btn').click( function(){
    $('#contact-modal').modal({blurring: true}).modal('show');
  });

  $('.notify-btn').click( function(){
    var theemail = "";
    $(".notify-input").each(function() {
      theemail = theemail + $(this).val();
    });
    $('#regemail').val(theemail);
    $('#register-modal').modal({blurring: true}).modal('show');
  });

  $('.login-btn').click( function(){
    $('#login-modal').modal({blurring: true}).modal('show');
  });

  $('.register-btn').click( function(){
    ga('send', 'event', { eventCategory: 'registration', eventAction: 'modal-opened'});
    $('#register-modal').modal({blurring: true}).modal('show');
  });

  $('.reset-btn').click( function(){
    $('#reset-modal').modal({blurring: true}) .modal('show');
  });
  $('.ui.accordion').accordion();
  $('.ui.dropdown').dropdown();

  $('.ui.sticky.sticky_sub_menu').sticky({
    context: '.sub_page_wrapper'
  });

  $('.learn-menu > .item').click(function (){
    $('.learn-menu > .item').removeClass('active');
    $(this).addClass('active');
  })
  /*$('.bouncy').mouseenter(function(){
    $(this).transition({
    animation  : 'pulse',
    duration   : '500ms',
    onComplete : function() {

      }
    });
  });
  $('#registerform')
    .form({
      on: 'blur',
      fields: {
        theemail: {
         identifier  : 'regemail',
         rules: [
           {
             type   : 'email',
             prompt : 'Please enter a valid e-mail address.'
           }
         ]
       },
        password: {
          identifier: 'regp',
          rules: [
            {
              type   : 'minLength[8]',
              prompt : 'Your password must be at least 8 characters.'
            }
          ]
        },
        confirmPassword: {
          identifier: 'regp2',
          rules: [
            {
              type: 'match[regp]',
              prompt: 'Your passwords do not match.'
            }
          ]
        },
        terms: {
          identifier: 'tc_agreement',
          rules: [
            {
              type   : 'checked',
              prompt : 'You must agree to our Terms and Conditions & Privacy Policy to sign up.'
            }
          ]
        }
      }
    });*/


  //Validation
  jQuery.validator.setDefaults({
      debug: false,
      success: "valid"
    });

    $( "#loginform" ).validate({
    	rules: {
    		logemail: {
    			required: true,
    			email: true
    		},
        logp: {
          required: true
        }
    	}
    });

    $( "#registerform" ).validate({
      rules: {
      	email: {
      		required: true,
      		email: true
      	},
      	regemail: {
      		required: true,
      		email: true
      	},
          regp: {
      		required: true,
      		minlength: 8
      	},
      	regp2: {
      		equalTo: "#regp"
      	},
        tc_agreement: {
          required: true
        }
      },
      messages: {
        tc_agreement: "You must agree."
      }
  });

  $("#contact-form").validate({
    rules: {
      email: {
        required: true,
        email: true
      },
      message: {
        required: true,
      }
    },
    messages: {
      email: "Please enter a valid email.",
      message: "Please enter a message."
    }
  });

  $("#reset-form").validate({
    rules: {
      email: {
        required: true,
        email: true
      }
    }
  });

  $("#payment-form").validate({
    rules: {
      "billing[first-name]": {
        required: true,
      },
      "billing[last-name]": {
        required: true,
      },
      "billing[address]": {
        required: true,
      },
      "billing[state]": {
        required: true,
      },
      "billing[zip]": {
        required: true,
      },
      "card[number]": {
        required: true,
      },
      "card[cvc]": {
        required: true,
      },
      "card[expire-month]": {
        required: true,
      },
      "card[expire-year]": {
        required: true,
      },
    }
  });

});
