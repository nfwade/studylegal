var msgError = [
	//0
	"Sorry, something went wrong. You can contact us <a href=''>here</a>.",
	//1
	"Sorry, you need a Hawaii.edu email to register with us.",
	//2
	"Sorry, you've tried to login too many times. Try back in a little while.",
	//3
	"Sorry, that account was already registered.",
	//4
	"Sorry, that confirmation expired. Click here to resend it.",
	//5
	"Sorry, that account hasn't been confirmed yet. Please check your inbox.",
	//6
	"Incorrect email or password.",
	//7
	"Sorry, that account doesn't exist.",
	//8
	"You have successfully cancelled your account.",
	//9
	"Sorry, that account is frozen. Please check your email.",
];
var msgSuccess = [
	//[0
	"Thanks for registering! Please check your email for a confirmation from us.",
	//1
	"Email successfully confirmed.  You can now login.",
	//2
	"Password successfully changed. You can now login.",
	//3
	"Password reset.  We just emailed you directions to change your password.",
	//4
	"You are now logged in!",
	//5
	"Email successfully changed",
];
var alerts = {
	init: function(){
		if (alerts.getUrlVars()["alert_error"] != null) {
			alerts.error(alerts.getUrlVars()["alert_error"]);
		}
		if (alerts.getUrlVars()["alert_success"] != null) {
			alerts.success(alerts.getUrlVars()["alert_success"]);
		}
	},
	
	getUrlVars: function() {
		var vars = [], hash;
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		for(var i = 0; i < hashes.length; i++)
		{
			hash = hashes[i].split('=');
			vars.push(hash[0]);
			vars[hash[0]] = hash[1];
		}
		return vars;
	},
	
	success: function(x) {
		if (x in msgSuccess){
			//var theStr = "<div class='alert alert_success'>" + msgSuccess[x] + "</div>" ;
			//$(".alert_space").append(theStr);
			alertify.success(msgSuccess[x]);
		}
	},
	
	error: function(x) {
		if (x in msgError){
			//var theStr = "<div class='alert alert_error'>" + msgError[x] + "</div>" ;
			//$(".alert_space").append(theStr);
			alertify.log(msgError[x], "", 0);
		}
	},
	
};

$(document).ready(function(){
	alerts.init();
});