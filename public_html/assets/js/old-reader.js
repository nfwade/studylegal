var activeCourseId = "0";
var activeContentId = "0";
var searchMinLength = 4;
var	brightLevel = 0;
var	fontLevel = 50;
var firstLogin = '';

// -----------FUNCTIONS RESPONSIBLE FOR ASSEMBLING HTML OBJECTS
var build = {
	//use x variable

	init: function(){
	},

	courses: function(x) {
		$(".item.course").remove();
		if (x != "0"){
			$.each(x, function(i, field){
				$(".course-list").prepend(
					"<div class='item course' id='" +
					field[0] +
					"' onClick='update.materials(this.id);'>" +
					field[1] +
					"</div> "
					);
			});
		}
		else {
			// do something if there are no courses
		}
	},

	codeList: function(x) {
		$("#add-code-menu").html("");
		var cbSecItr = 0;
		//For each book
		x = $.parseJSON( x );
		$.each(x, function( index, value ) {
			$("#add-code-menu").append(
				"<div class='item code-book' id='cb" +
				value['id'] +
				"' onClick=''><i class='dropdown icon'></i><span class='text'>" +
				value['shortname'] +
				"</span>"
			);
			var theSelctr1 = "#cb" + value['id'];
			$(theSelctr1).append("<div class='menu subcb' id='subcb" + value['id'] + "'></div>");
			var theSelctr = "#subcb" + value['id'];
			//for each section
			$.each(value['codes'], function(lowIndex, lowValue) {
					$(theSelctr).append("<div class='item' id='cbsec-" +
					cbSecItr +
					"'><i class='dropdown icon'></i><span class='text'>" +
					lowIndex +
					"</span></div>"
					);
					var theSelctr2 = "#cbsec-" + cbSecItr;
					$(theSelctr2).append("<div class='menu cbsec' id='cbsecmenu-" + cbSecItr + "'></div>");
					var theSelctr3 = "#cbsecmenu-" + cbSecItr;
					//for each code
					$.each(lowValue, function(codeIndex, codeValue){
						$(theSelctr3).append("<div class='item ind-code' id='" +
						codeValue['id'] +
						"' onClick='ctrl.addMaterial(this.id);'><i class='plus icon'></i>" +
						codeValue['fullname'] +
						"</div>"
					);
					});
					cbSecItr++;
			});
			$("#add-code-menu").append("</div>");
		});
		console.log("build codeList ran");
	},

	materials: function(x) {
		$(".content-list").html("");
		//x = $.parseJSON( x );
		//console.log(JSON.stringify(x));
		$.each(x, function(i, field){
			$(".content-list").append(
				"<a class='item content-list-item material" +field["id"] + "' id='" +
				field["id"] +
				"' onClick ='update.content(this.id);'>" +
				field["short_name"] +
				"<span class='delete-material-button' id='" +
				field["id"] +
				"' onClick='ctrl.removeMaterial(this.id);'><i class='remove icon'></i></span></a>"
				);
		});
	},

	materialsEmpty: function(){
		$(".content-list").html("");
		$(".edit-content-list").html("");
		$(".content-list").html("<a class='item'>PIERSON v. POST (Example)</a>");
	},

	content: function(x) {
		$('#tutorial-title').remove();
		//clear the fields
		$(".content-title").html("");
		$(".content-date").html("");
		$(".content-citation").html("");
		$(".content-opinion").html("");

		//put the data
		//var shortername = x['short_name'];//.substring(0,10);
		$(".content-title").append(x["full_name"]);
		$(".content-date").append(x["date_published"]);
		$(".content-citation").append(x["citation"]);
		$(".content-opinion").append(x["content_parsed"]);
	},

	contentEmpty: function(){
		$('#tutorial-title').remove();
		var theHtml = theCase = '<P>TOMPKINS, J. delivered the opinion of the court. <P>This cause comes before us on a return to a <I>certiorari</I> directed to one of the justices of <I>Queens</I> county. <P> <P>The question submitted by the counsel in this cause for our determination is, whether <I>Lodowick Post,</I> by the pursuit with his hounds in the manner alleged in his declaration, acquired such a right to, or property in, the fox, as will sustain an action against <I>Pierson</I> for killing and taking him away? <P> <P>The cause was argued with much ability by the counsel on both sides, and presents for our decision a novel and nice question.<span class="" id="tutorial-highlighted"> It is admitted that a fox is an animal <I>fer&#230; natur&#230;,</I> and that property in such animals is acquired by occupancy only.</span> These admissions narrow the discussion to the simple question of what acts amount to occupancy, applied to acquiring right to wild animals? <P> <P>If we have recourse to the ancient writers upon general principles of law, the judgment below is obviously erroneous. <I>Justinians Institutes,</I> lib. 2. tit. 1. s. 13. and <I>Fleta,</I> lib. 3. c. 2. p. 175. adopt the principle, that pursuit alone vests no property or right in the huntsman; and that even pursuit, accompanied with wounding, is equally ineffectual for that purpose, unless the animal be actually taken. The same principle is recognised by <I>Bracton,</I> lib. 2. c. 1. p. 8. <P> <P><I>Puffendorf,</I> lib. 4. c. 6. s. 2. and 10. defines occupancy of beasts <I>fer&#230; natur&#230;,</I> to be the actual corporal possession of them, and <I>Bynkershoek</I> is cited as coinciding in this definition. It is indeed with hesitation that <I>Puffendorf</I> affirms that a wild beast mortally wounded, or greatly maimed, cannot be fairly intercepted by another, whilst the pursuit <font color="#FF0000">[*178]</font> of the person inflicting the wound continues. The foregoing authorities are decisive to show that mere pursuit gave <I>Post</I> no legal right to the fox, but that he became the property of <I>Pierson,</I> who intercepted and killed him. <P> <P>It therefore only remains to inquire whether there are any contrary principles, or authorities, to be found in other books, which ought to induce a different decision. Most of the cases which have occurred in <I>England,</I> relating to property in wild animals, have either been discussed and decided upon the principles of their positive statute regulations, or have arisen between the huntsman and the owner of the land upon which beasts <I>fer&#230; natur&#230;</I> have been apprehended; the former claiming them by title of occupancy, and the latter <I>ratione soli.</I> Little satisfactory aid can, therefore, be derived from the <I>English</I> reporters. <P> <P><I>Barbeyrac,</I> in his notes on <I>Puffendorf,</I> does not accede to the definition of occupancy by the latter, but, on the contrary, affirms, that actual bodily seizure is not, in all cases, necessary to constitute possession of wild animals. He does not, however, <I>describe</I> the acts which, according to his ideas, will amount to an appropriation of such animals to private use, so as to exclude the claims of all other persons, by title of occupancy, to the same animals; and he is far from averring that pursuit alone is sufficient for that purpose. To a certain extent, and as far as <I>Barbeyrac</I> appears to me to go, his objections to <I>Puffendorfs</I> definition of occupancy are reasonable and correct. That is to say, that actual bodily seizure is not indispensable to acquire right to, or possession of, wild beasts; but that, on the contrary, the mortal wounding of such beasts, by one not abandoning his pursuit, may, with the utmost propriety, be deemed possession of him; since, thereby, the pursuer manifests an unequivocal intention of appropriating the animal to his individual use, has deprived him of his natural liberty, and brought him within his certain control. So also, encompassing and securing such animals with nets and toils, or otherwise intercepting them in such a manner as to deprive them of their natural liberty, and render escape impossible, may justly be deemed to give possession of them to those persons who, by their industry and labour, have used such means of apprehending them. <I>Barbeyrac</I> seems to have adopted, and had in view in his notes, <font color="#FF0000">[*179]</font> the more accurate opinion of <I>Grotius,</I> with respect to occupancy. That celebrated author, lib. 2. c. 8. s. 3. p. 309. speaking of occupancy, proceeds thus: "<I>Requiritur autem corporalis qu&#230;dam possessio ad dominium adipiscendum; atque ideo, vulnerasse non sufficit.</I>" But in the following section he explains and qualifies this definition of occupancy: "<I>Sed possessio illa potest non solis manibus, sed instrumentis, ut decipulis, retibus, laqueis dum duo adsint: primum ut ipsa instrumenta sint in nostra potestate, deinde ut fera, ita inclusa sit, ut exire inde nequeat.</I>" This qualification embraces the full extent of <I>Barbeyracs</I> objection to <I>Puffendorfs</I> definition, and allows as great a latitude to acquiring property by occupancy, as can reasonably be inferred from the words or ideas expressed by <I>Barbeyrac</I> in his notes. The case now under consideration is one of mere pursuit, and presents no circumstances or acts which can bring it within the definition of occupancy by <I>Puffendorf,</I> or <I>Grotius,</I> or the ideas of <I>Barbeyrac</I> upon that subject. <P> <P>The case cited from 11 <I>Mod.</I> 74&#151;130. I think clearly distinguishable from the present; inasmuch as there the action was for maliciously hindering and disturbing the plaintiff in the exercise and enjoyment of a private franchise; and in the report of the same case, 3 <I>Salk.</I> 9. <I>Holt,</I> Ch. J. states, that the ducks were in the plaintiffs decoy pond, and <I>so in his possession,</I> from which it is obvious the court laid much stress in their opinion upon the plaintiffs possession of the ducks, <I>ratione soli.</I> <P> <P>We are the more readily inclined to confine possession or occupancy of beasts <I>fer&#230; natur&#230;,</I> within the limits prescribed by the learned authors above cited, for the sake of certainty, and preserving peace and order in society. If the first seeing, starting, or pursuing such animals, without having so wounded, circumvented or ensnared them, so as to deprive them of their natural liberty, and subject them to the control of their pursuer, should afford the basis of actions against others for intercepting and killing them, it would prove a fertile source of quarrels and litigation. <P> <P>However uncourteous or unkind the conduct of <I>Pierson</I> towards <I>Post,</I> in this instance, may have been, yet his act was productive of no injury or damage for which a legal <font color="#FF0000">[*180]</font> remedy can be applied. We are of opinion the judgment below was erroneous, and ought to be reversed.';;
		$(".content-title").html("PIERSON v. POST");
		$(".content-date").html("");
		$(".content-citation").html("");
		$(".content-opinion").html("");
		$(".content-opinion").append(theHtml);
		$("#content-segment").prepend('<h3 id="tutorial-title" style="text-decoration:underline; text-align:center;">NOTE: This is an example case, it will be deleted after you add your first material.</h3>');
	},

	courseMenuAll: function(x) {
		$(".add-course-list").empty();
		$.each(x, function(i, field){
			$(".add-course-list").append(
				"<a class='item' id='" +
				field.id +
				"' onClick='ctrl.addCourse(this.id);'>" +
				field.course_name +
				"<i class='plus icon'></i></a>"
			);
		});
	},

	courseMenuUser: function(x) {
		$(".current-course-list").empty();
		if (x  == false) {
			//do nothing
		}
		else {
			$.each(x, function(i, field){
				$(".current-course-list").append(
					"<a class='course item' id='" +
					field[0] +
					"' onClick='ctrl.removeCourse(this.id);'>" +
					field[1] +
					"<i class='minus icon'></i></a> "
				);
			});
		}
	},

	brief: function(x){
		$(".quill-wrapper").show();
		$("#auto-save-status").show();
		$(".brief-section").val("");
		$(".content-title").val(x["alias"]);
		factsEditor.setHTML(x["facts"]);
		issuesEditor.setHTML(x["issues"]);
		holdingEditor.setHTML(x["holding"]);
		rulesEditor.setHTML(x["rules"]);
		analysisEditor.setHTML(x["analysis"]);
		notesEditor.setHTML(x["notes"]);
	},

	briefEmpty: function(){
		$("#auto-save-status").hide();
		$(".quill-wrapper").hide();
		$(".brief-body-wrapper").hide();
	},

};

// ----------- FUNCTIONS FOR RELOADING OBJECTS AND CHANGING ACTIVE VALUES
var update = {
	//use y variable

	materials: function(y) {
		//change the active course to x
		monitorAccount.check();
		activeCourseId = y;
		update.setActiveCourse(y);
		//rebuild the content list
		load.materials();
		console.log("update.materials ran with x=" +y);
	},

	content: function(y) {
		//change the active content to x
		monitorAccount.check();
		activeContentId = y;
		update.setActiveContent(y);
		//rebuild the content
		load.content();
		startSemantic.makeSticky();
		console.log("update.content ran with x=" + y);
	},

	setActiveCourse: function(y) {
		monitorAccount.check();
		$(".course").removeClass("active");
		thestring = ".course-list #" + y;
		$(thestring).addClass("active");
		console.log("update.setActiveCourse ran with x="+y);
	},

	setActiveContent: function(y) {
		monitorAccount.check();
		$(".content-list-item").removeClass("active");
		thestring = ".material" + y;
		$(thestring).addClass("active");
		console.log("update.setActiveContent ran with x=" + y);
	},


};

// ----------- FUNCTIONS FOR MAKING ANYTYPE OF AJAX CALLS
var call = {
	//use z variable
	props: {
		courses: "0",
		materials: "0",
		content: "0"
	},

	userInfo: function() {
		return $.ajax({
			async: false,
			type: "GET",
			//dataType: "json",
			url: "/user/get/info",
		});
	},

	coursesAll: function() {
		return $.ajax({
			async: false,
			type: "GET",
			//dataType: "json",
			url: "/app/static/courses",
		});
	},

	courses: function() {
		theurl = "/app/user/courses"
		return $.ajax({
			async: false,
			type: "GET",
			//dataType: "json",
			url: theurl,
		});
	},

	codes: function(){
		theURL = "/app/static/codes";
		return $.ajax({
			async: false,
			type: "GET",
			//dataType: "json",
			url: theURL,
		});
	},

	materials: function(z){
		var theURL = "/app/user/materials/" + z;
		return $.ajax({
			async: false,
			type: "GET",
			//data: {courseid: z},
			url: theURL
		});
	},

	content: function(z){
		var theurl = "/app/static/material/" +z;
		return $.ajax({
			async: false,
			type: "GET",
			//data: {contentid: z},
			url: theurl,
		});
	},

	brief:function(){
		var theurl = "/app/user/notes/" + activeCourseId + "/" + activeContentId;
		return $.ajax({
			async: false,
			type: "GET",
			//data: {crid: activeCourseId, cnid: activeContentId},
			url: theurl,
		});
	},

	addCourse: function(z){
		var theurl = "/app/user/add/course/" + z;
		return $.ajax({
			type: "GET",
			url: theurl,
			//data: { ncrid: z },
		});
	},

	removeCourse: function(z){
		var theurl = "/app/user/remove/course/" + z;
		return $.ajax({
			type: "GET",
			url: theurl,
			//data: { rcrid: z },
		});
	},

	addMaterial: function(z){
		var theurl = "/app/user/add/material/" + activeCourseId + "/" + z;
		return $.ajax({
			async: false,
			type: "GET",
			url: theurl,
			//	data: { ncid: z, acrid: activeCourseId },
		});
	},

	addCustomMaterial: function(x,y,z){
		var theurl = "/app/user/add/custom/material";
		return $.ajax({
			async: false,
			type: "POST",
			url: theurl,
			data: { title: x, type: y, content: z },
		});
	},

	removeMaterial: function(z){
		var theurl = "/app/user/remove/material/" + activeCourseId + "/" + z;
		return $.ajax({
			async: false,
			type: "GET",
			url: theurl,
			//data: { rcid: z, acrid: activeCourseId },
		});
	},

	addFact: function(z){
		var theurl = "/app/user/add/brief/fact";
		return $.ajax({
			async: false,
			type: "POST",
			url: theurl,
			data: { afact: z, crid: activeCourseId, cnid: activeContentId },
		});
	},

	addIssue: function(z){
		var theurl = "/app/user/add/brief/issue";
		return $.ajax({
			async: false,
			type: "POST",
			url: theurl,
			data: { aissue: z, crid: activeCourseId, cnid: activeContentId },
		});
	},

	addHolding: function(z){
		var theurl = "/app/user/add/brief/holding";
		return $.ajax({
			async: false,
			type: "POST",
			url: theurl,
			data: { aholding: z, crid: activeCourseId, cnid: activeContentId },
		});
	},

	addRule: function(z){
		var theurl = "/app/user/add/brief/rule";
		return $.ajax({
			async: false,
			type: "POST",
			url: theurl,
			data: { arules: z, crid: activeCourseId, cnid: activeContentId },
		});
	},

	addAnalysis: function(z){
		var theurl = "/app/user/add/brief/analysis";
		return $.ajax({
			async: false,
			type: "POST",
			url: theurl,
			data: { aanalysis: z, crid: activeCourseId, cnid: activeContentId },
		});
	},

	addNote: function(z){
		var theurl = "/app/user/add/brief/note";
		return $.ajax({
			async: false,
			type: "POST",
			url: theurl,
			data: { anotes: z, crid: activeCourseId, cnid: activeContentId },
		});
	},

	updateBrief: function(fa,is,ho,ru,an,no) {
		var theurl = "/app/user/update/brief"
		return $.ajax({
			async: false,
			type: "POST",
			url: theurl,
			data: {
				crid: activeCourseId,
				cnid: activeContentId,
				f: fa,
				i: is,
				h: ho,
				r: ru,
				a: an,
				n: no
				},
		});
	},

	order: function(){
		var theurl = "/app/user/materialsorder/" + activeCourseId;
		return $.ajax({
			async: false,
			type: "GET",
			url: theurl,
			//data: { crid: activeCourseId },
		});
	},

	loggedIn: function(){
		var theurl = "/app/user/loggedin";
		return $.ajax({
			async: false,
			type: "GET",
			url: theurl,
			//data: { crid: activeCourseId },
		});
	},


	updateOrder: function(z){
		z = JSON.stringify(z);
		var theurl = "/app/user/update/order";
		return $.ajax({
			async: false,
			type: "POST",
			url: theurl,
			data: { crid: activeCourseId, order:z },
		});
	},
};

// ----------- FUNCTIONS FOR UPDATING THE DATABASE (CALL + UPDATE)
var ctrl = {

	//use v variable

	init: function(){
		$(".f").mousedown(function() {
			monitorAccount.check();
			ctrl.addFact();
			load.brief();
		});
		$(".i").mousedown(function() {
			monitorAccount.check();
			ctrl.addIssue();
			load.brief();
		});
		$(".h").mousedown(function() {
			monitorAccount.check();
			ctrl.addHolding();
			load.brief();
		});
		$(".r").mousedown(function() {
			monitorAccount.check();
			ctrl.addRule();
			load.brief();
		});
		$(".a").mousedown(function() {
			monitorAccount.check();
			ctrl.addAnalysis();
			load.brief();
		});
		$(".n").mousedown(function() {
			monitorAccount.check();
			ctrl.addNote();
			load.brief();
		});
		$("#save-brief-button").click(function(){
			monitorAccount.check();
			ctrl.updateBrief();
			load.brief();
		});

		alertify.set('notifier','position', 'bottom-left');

		$("#custom-material-form-button").click(function(){
			monitorAccount.check();
			ctrl.addCustomMaterial();
		});

		console.log("ctrl.init ran");
	},

	testExpire: function(x){
	 if (x == "expired"){
			window.location.href = "/login";
		}
	},

	addCourse: function(v) {
		call.addCourse(v).success(function( data ) {
			ctrl.testExpire(data);
			if (data == "0") {
				alertify.error("Sorry, something went wrong");
			}
			if (data == "1") {
				alertify.success("Course added successfully");
				console.log("ctr.addCourse ran with x=" +v);
				load.courses();
			}
			if (data == "2") {
				alertify.error("You're already taking this course");
			}
		});
	},

	removeCourse: function(v) {
		//alertify.confirm("Remove Course", "Are you sure you want to remove that course?", function(){
		$('.confirm-remove-course')
			  .modal({
					blurring: false,
					closable  : true,
					transition: 'fade',
			    onDeny    : function(){
			      return false;
			    },
			    onApprove : function() {
						call.removeCourse(v).success(function( data ) {
							ctrl.testExpire(data);
							if (data == "0") {
								alertify.error("Sorry, something went wrong");
							}
							if (data == "1") {
								alertify.success("Course removed successfully");
								console.log("ctrl.removeCourse ran with x=" +v);
								load.courses(true);
							}
							if (data == "2") {
								alertify.error("You're not taking this course");
							}
						});
						$('.confirm-remove-course').modal('hide');
			    }
			  })
			  .modal('show')
			;
	},

	addMaterial: function(v){
		call.addMaterial(v).success( function(data) {
			ctrl.testExpire(data);
			if (data == "0") {
				alertify.error("Sorry, something went wrong.");
			}
			if (data == "1") {
				alertify.success("Content added successfully.");
				console.log("ctr.addMaterial ran with x=" +v);
				$('#content-search-input').val('');
				$('#ac-results').html('');
				//append new course to "sort"
				materialOrder.addToSort(v);
				load.materials();
			}
			if (data == "2") {
				alertify.success("You already have that case.");
			}
		});
	},

	addCustomMaterial: function(){
		var fields = $( "#custom-material-form" ).serializeArray();
    var title = fields[0].value;
		var type = fields[1].value;
		var content = fields[2].value;
		tabs.showLoading('.add-custom-material.content');
		call.addCustomMaterial(title, type, content).success( function( data ) {
			ctrl.testExpire(data);
			ctrl.addMaterial(data);
			tabs.hideLoading('.add-custom-material.content');
			sl_modals.hideAll();
		});
	},

	removeMaterial: function(v){
		$('.remove-material-confirmation')
			.modal({
				blurring : true,
				closable  : true,
				transition: 'fade',
				onDeny    : function(){
					//User clicked no
				},
				onApprove : function() {
					call.removeMaterial(v).success( function(data) {
						ctrl.testExpire(data);
						if (data == "0") {
							alertify.error("Sorry, something went wrong.");
						}
						if (data == "1") {
							alertify.success("Material removed successfully.");
							console.log("ctrl.removeMaterial ran with v=" +v);
							load.materials();
							materialOrder.updateSort();
						}
					});
				}
			})
			.modal('show');

	},

	getSelectionText: function() {
		var text = "";
		if (window.getSelection) {
			text = window.getSelection().toString();
		} else if (document.selection && document.selection.type != "Control") {
			text = document.selection.createRange().text;
		}
		console.log("ctrl.getSelectionText ran");
		return text;
	},

	addFact: function(){
		//get the selected text
		v = ctrl.getSelectionText();
		if(v != ""){
			//make the call
			call.addFact(v).success( function( data ) {
				ctrl.testExpire(data);
				if(data == "1"){
				//success alert
					alertify.success("Fact added successfully");
					console.log("ctrl.addFact ran");
				//reload brief
				}
				else {
				//fail
					alertify.error("Something went wrong");
				}
			});
		}
	},

	addIssue: function(){
		//get the selected text
		v = ctrl.getSelectionText();
		if(v != ""){
			//make the call
			call.addIssue(v).success( function( data ) {
				ctrl.testExpire(data);
				if(data == "1"){
				//success alert
					alertify.success("Issue added successfully");
					console.log("ctrl.addIssue ran");
				//reload brief
				}
				else{
				//fail
					alertify.error("Something went wrong");
				}
			});
		}
	},

	addRule: function(){
		//get the selected text
		v = ctrl.getSelectionText();
		if(v != ""){
			//make the call
			call.addRule(v).success( function( data ) {
				ctrl.testExpire(data);
				if(data == "1"){
				//success alert
					alertify.success("Rule added successfully");
					console.log("ctrl.addRule ran");
				//reload brief
				}
				else{
				//fail
					alertify.error("Something went wrong");
				}
			});
		}
	},

	addHolding: function(){
		//get the selected text
		v = ctrl.getSelectionText();
		if(v != ""){
			//make the call
			call.addHolding(v).success( function( data ) {
				ctrl.testExpire(data);
				if(data == "1"){
				//success alert
					alertify.success("Holding added successfully");
					console.log("ctrl.addHolding ran");
				//reload brief
				}
				else{
				//fail
					alertify.error("Something went wrong");
				}
			});
		}
	},

	addAnalysis: function(){
		//get the selected text
		v = ctrl.getSelectionText();
		if(v != ""){
			//make the call
			call.addAnalysis(v).success( function( data ) {
				ctrl.testExpire(data);
				if(data == "1"){
				//success alert
					alertify.success("Analysis added successfully");
					console.log("ctrl.addAnalysis ran");
				//reload brief
				}
				else{
				//fail
					alertify.error("Something went wrong");
				}
			});
		}
	},

	addNote: function(){
		//get the selected text
		v = ctrl.getSelectionText();
		if(v != ""){
			//make the call
			call.addNote(v).success( function( data ) {
				ctrl.testExpire(data);
				if(data == "1"){
				//success alert
					alertify.success("Note added successfully");
					console.log("ctrl.addNote ran");
				//reload brief
				}
				else{
				//fail
					alertify.error("Something went wrong");
				}
			});
		}
	},

	updateBrief: function(alertSwitch){
		//get the values
		var fac = factsEditor.getHTML();
		var iss = issuesEditor.getHTML();
		var hol = holdingEditor.getHTML();
		var rul = rulesEditor.getHTML();
		var ana = analysisEditor.getHTML();
		var not = notesEditor.getHTML();
		//send the data
		call.updateBrief(fac,iss,hol,rul,ana,not).success( function( data ) {
			ctrl.testExpire(data);
			if(data == "1"){
			//success alert
				if(alertSwitch == false){
					console.log("ctrl.updateBrief ran");
				}
				else {
					alertify.success("Brief Saved.");
					console.log("ctrl.updateBrief ran");
				}
			}
			else{
			//fail
				alertify.error("Brief not saved. Copy your changes and refresh the page.");
			}
		});
	},
};

// ----------- FUNCTIONS FOR CONTROLLING THE VISUAL BEHAVIOR OF THE MODALS
var sl_modals = {
	init: function(){
		$('.ui.modal').modal();
		$('#add-course-button').click(function(){
			$('#add-course-wrapper').modal({blurring: true}).modal('show');
		});

		$('#custom-material-button').click(function(){
			$('.add-custom-material').modal({blurring: true}).modal('show');
		});

		$('.ui.cancel.button').click(function(){
			sl_modals.hideAll();
		});
	},

	hideAll: function(){
		$('.ui.modal').modal('hide');
	},

	courses: function(){
		$('#add-course-wrapper').modal({
			//blurring: true
		}).modal('show');
	},

	coursesPermanent: function(){
		$('#add-course-wrapper').modal({
			//blurring: true,
			closable: false,
		}).modal('show');
	},

};

// ----------- FUNCTIONS FOR LOADING HTML OBJECTS (CALL + BUILD)
var load = {
	//use u variable
	init: function() {
		/*call.userInfo().success(function (data) {
			ctrl.testExpire(data);
			data = $.parseJSON( data );
			if (data == "0"){
				//nothing
			}
			else {
				firstLogin = data.first_login;
			}
		});*/
		load.courseMenuAll();
		load.courses();
		load.codeBooks();
	},

	//this is ran only once because it is a json file that likel won't change.
	courseMenuAll: function(){
		call.coursesAll().success(function (data) {
			ctrl.testExpire(data);
			data = $.parseJSON( data );
			if (data == "0"){
				//nothing
			}
			else {
				//build the courseMenu
				build.courseMenuAll(data);
			}
		});
	},

	codeBooks: function(){
		call.codes().success(function (data) {
			ctrl.testExpire(data);
			//data = $.parseJSON( data );
			if (data == "") {
			}
			else {
				build.codeList(data);
			}
		});
	},

	courseMenuUser: function(){
		call.courses().success(function (data) {
			ctrl.testExpire(data);
			data = $.parseJSON( data );
			if (data == "0"){
				//nothing
				build.courseMenuUser(false);
				sl_modals.coursesPermanent();
			}
			else {
				//build the courseMenu
				build.courseMenuUser(data);
			}
		});
	},

	courses: function(){
		call.courses().success(function (data) {
			ctrl.testExpire(data);
			data = $.parseJSON( data );
			if (data == "0"){
				sl_modals.hideAll();
				build.courses(data);
				load.courseMenuUser();
				sl_modals.coursesPermanent();
			}
			else {
				call.userInfo().success(function (data) {
					ctrl.testExpire(data);
					data = $.parseJSON( data );
					if (data == "0"){
						//nothing
					}
					else {
						firstLogin = data.first_login;
					}
				});
				sl_modals.hideAll();
				build.courses(data);
				load.courseMenuUser();
				//set the first course as active
				update.materials(data[0][0]);
			}
		});
	},

	materials: function(){
		tabs.showLoading("#content-list");
		call.materials(activeCourseId).success(function (data) {
			ctrl.testExpire(data);
			if (data == "0"){
				//load a "no materials message"
				build.materialsEmpty();
				build.contentEmpty();
				load.brief();
				tabs.hideLoading("#content-list");
				if (firstLogin == true){
					startSemantic.showTutorial(0);
				}
			}
			else {
				data = $.parseJSON( data );
				//sort the data according to the saved list
				var getOrder = [];
				getOrder = materialOrder.match(data);
				data = getOrder;
				build.materials(data);
				update.content(data[0]["id"]);
				tabs.hideLoading("#content-list");
			}
		});
	},

	content: function(){
		tabs.showLoading("#content-segment");
		call.content(activeContentId).success(function (data) {
			ctrl.testExpire(data);
			data = $.parseJSON( data );
			if (data == "0"){
				//load a "no materials message"
				tabs.hideLoading("#content-segment");
			}
			else {
				build.content(data[0]);
				tabs.hideLoading("#content-segment");
				load.brief();
			}
		});
	},

	brief: function(){
		tabs.showLoading(".brief-wrapper");
		call.brief().success(function (data) {
			ctrl.testExpire(data);
			data = $.parseJSON( data );
			if (data == "0"){
				build.briefEmpty();
				tabs.hideLoading(".brief-wrapper");
			}
			else {
				build.brief(data);
				tabs.hideLoading(".brief-wrapper");
			}
		});
	},

};

// ----------- FUNCTIONALITY OF THE AUTOCOMPLETE
var searchForContent = {

	//leave this as as shorthand function
	init: function() {
		$("#content-search-input").keyup(function() {
			var keyword = $("#content-search-input").val();
			if (keyword.length >= searchMinLength) {
				var theurl = "/app/static/search/" + keyword;
				$.get(theurl)
				.done(function( data ) {
					$('#ac-results').html('');
					if (data == "1") {
						$('#ac-results').append(
						"<div class='ui segment'>Sorry, no materials found. Click the custom button to add it yourself.</div>"
						);
					}
					else if(data == "2"){
						$('#ac-results').append(
						"<div class='ui segment'>Too many results, try to narrow your search.</div>"
						);
					}
					else {
						var results = jQuery.parseJSON(data);
						$(results).each(function(key, value) {
							$('#ac-results').append(
								'<div class="ui segment" id="' +
								value.id +
								'" onClick="ctrl.addMaterial(this.id);"><i class="plus icon"></i>' +
								value.full_name +
								'</div>'
							);
						});

						$('.item').click(function() {
						});
					}

				});
			}
			else {
				$('#ac-results').html('');
			}
		});
		$("#content-search-input").blur(function(){
    		$("#ac-results").hide(500);
    	})
        .focus(function() {
    	    $("#ac-results").slideDown();
    	});
	},
};

// ----------- VISUAL TOOLS
var readingTools = {

	brightCnt: 1,

	init: function() {
		$(".reading-tools-button").click( function() {
			readingTools.buttonPress();
		});
		this.fontSize();
		$(".rt-bright-button").click( function() {
			readingTools.brightHit();
		});
	},

	buttonPress: function() {
		$(".reading-tools").toggle();
		$(".reading-tools-button").toggleClass("subtle-button");
	},

	fontSize: function() {
		$(".rt-font1").click( function() {
			$(".content-opinion").css("font-size","17px");
			$(".brief-section").css("font-size","13px");
		});
		$(".rt-font2").click( function() {
			$(".content-opinion").css("font-size","21px");
			$(".brief-section").css("font-size","16px");
		});
		$(".rt-font3").click( function() {
			$(".content-opinion").css("font-size","25px");
			$(".brief-section").css("font-size","20px");
		});
	},

	brightHit: function() {
		if (readingTools.brightCnt < 3) {
			readingTools.brightCnt++;
			var help1 = readingTools.brightCnt.toString();
			var style1 = "bright-" + help1;
			$("#reader-brightness-filter").removeClass();
			$("#reader-brightness-filter").addClass(style1);
		}
		else if (readingTools.brightCnt == 3) {
			readingTools.brightCnt = 1;
			var help1 = readingTools.brightCnt.toString();
			var style1 = "bright-" + help1;
			$("#reader-brightness-filter").removeClass();
			$("#reader-brightness-filter").addClass(style1);
		}
	},

	//Non-active
	brightUp: function() {
		//Page Brightness
		if (brightLevel < 9) {
			brightLevel = brightLevel + 1;
			var help1 = brightLevel.toString();
			var style1 = "rgba(0,0,0,." + help1 + ")";
			$('.reader-brightness-filter').css("background-color", style1);
			//Font Color
			if (fontLevel < 255) {
				fontLevel = fontLevel + 50;
				var help2 = fontLevel.toString();
				var style2 = "rgb(" + help2 + "," + help2 + "," + help2 + ")";
				$(".reader-brightness-filter").css("color", style2);
			}
		}
	},

	//Non-active
	brightDown: function() {
		//Page Brightness
		if (brightLevel > 0) {
			brightLevel = brightLevel - 1;
			var help1 = brightLevel.toString();
			var style1 = "rgba(0,0,0,." + help1 + ")";
			$('.reader-brightness-filter').css("background-color", style1);
			//Font Color
			if (fontLevel > 50) {
				fontLevel = fontLevel - 50;
				var help2 = fontLevel.toString();
				var style2 = "rgb(" + help2 + "," + help2 + "," + help2 + ")";
				$(".reader-brightness-filter").css("color", style2);
			}
		}
	},

};

var tabs = {
	init: function() {
		tabs.contentToBrief();
		tabs.appearQlToolbar();
		$(".brief-wrapper").hide();
		$(".view-brief-toolbar").hide();
		$("#content-toggle").addClass("darker");
		$('.ui.dropdown').dropdown();
		$('.ui.sticky').sticky({context: 'body'});
	},

	contentToBrief: function() {
		$("#brief-toggle").click( function() {
			tabs.brief();
		});
		$("#content-toggle").click( function() {
			tabs.content();
			startSemantic.makeSticky();
		});
	},

	brief: function() {
		$(".content-wrapper").hide();
		//$(".brief-tools-wrapper").hide();
		$(".brief-wrapper").show();
		$("#content-toggle").removeClass("darker");
		$("#brief-toggle").addClass("darker");
	},

	content: function() {
		$(".brief-wrapper").hide();
		//$(".brief-tools-wrapper").show();
		$(".content-wrapper").show();
		$("#content-toggle").addClass("darker");
		$("#brief-toggle").removeClass("darker");
	},

	appearQlToolbar: function() {
		$(".toolbar-container").hide();

		$("#ql-editor-1").focus( function() {
			$(".toolbar-container").hide();
			$("#notes-editor-toolbar").show();
		});

		$("#ql-editor-2").focus( function() {
			$(".toolbar-container").hide();
			$("#facts-editor-toolbar").show();
		});

		$("#ql-editor-3").focus( function() {
			$(".toolbar-container").hide();
			$("#issues-editor-toolbar").show();
		});

		$("#ql-editor-4").focus( function() {
			$(".toolbar-container").hide();
			$("#holding-editor-toolbar").show();
		});

		$("#ql-editor-5").focus( function() {
			$(".toolbar-container").hide();
			$("#rules-editor-toolbar").show();
		});

		$("#ql-editor-6").focus( function() {
			$(".toolbar-container").hide();
			$("#analysis-editor-toolbar").show();
		});
	},

	showLoading: function(x){
		$(x).dimmer('show');
		//$(x).append('<div class="ui active dimmer"><div class="ui text loader">Loading</div></div>');
	},

	hideLoading:function(x){
		$(x).dimmer('hide');
		//$(x).remove(".dimmer");
	},

};

var autosave = {
	init: function() {
		$("#auto-save-status").html("<i class='check icon'></i>Saved");
		/*$('.brief-section').bind('input propertychange', function() {
			if(autosave.needSave == false){
				autosave.activate();
			}
		});*/

		$("#brief-save-button").click( function() {
			autosave.save();
		});

		notesEditor.on('text-change', function(delta, source) {
		  if (source == 'user') {
		  	autosave.activate();
  			}
		});

		factsEditor.on('text-change', function(delta, source) {
		  if (source == 'user') {
		  	autosave.activate();
  			}
		});

		analysisEditor.on('text-change', function(delta, source) {
		  if (source == 'user') {
		  	autosave.activate();
  			}
		});

		holdingEditor.on('text-change', function(delta, source) {
		  if (source == 'user') {
		  	autosave.activate();
  			}
		});

		rulesEditor.on('text-change', function(delta, source) {
		  if (source == 'user') {
		  	autosave.activate();
  			}
		});

		issuesEditor.on('text-change', function(delta, source) {
		  if (source == 'user') {
		  	autosave.activate();
  			}
		});
	},

	needSave: false,

	activate: function() {
		autosave.needSave = true;
		$("#auto-save-status").html("<i class='close icon'></i>Saving...");
		setTimeout(autosave.save, 4000);
		console.log("autosave.activate finished");
	},

	deactivate: function() {
		autosave.needSave = false;
		$("#auto-save-status").html("<i class='check icon'></i>Saved");
		console.log("autosave.deactivate finished");
	},

	save: function() {
		if(autosave.needSave == true) {
			ctrl.updateBrief(false);
			autosave.deactivate();
			console.log("autosave.save finished");
		}
	},
};

var monitorAccount = {
	init: function(){
		var start = Date.now();
	},

	check: function(){
		var now = Date.now();
		var twoHoursLater = now - 3600000; //7200000
		if (monitorAccount.init.start <= twoHoursLater){
			if(monitorAccount.checkLoggedIn() == "1") {
				monitorAccount.init();
			}
			else {
				monitorAccount.makeLogin();
			}
		}
	},

	checkLoggedIn: function(){
		call.loggedIn().success( function(data) {
			//user is not logged in
			if (data == "0") {
				return "0";
			}
			//user is logged in
			if (data == "1") {
				return "1";
			}
		});
	},

	makeLogin: function(){
		$('#login-modal').modal('setting', 'closable', false).modal('show');
	},
};

var materialOrder = {
	init: function(){
		//not ready
		materialOrder.startSort();
	},

	startSort: function() {
		$('#content-list').sortable({
			axis: 'y',
			delay: 200,
			scroll: true,
			zIndex: 9999,
			forceHelperSize: true,
			forcePlaceholderSize: true,
			placeholder: "active item",
			opacity: 1,
			revert: true,
			tolerance: "pointer",
			update: function (event, ui) {
				var data = $(this).sortable('toArray');
				// POST to server using $.post or $.ajax
				call.updateOrder(data);
				console.log("Update Sort Happened");
			}
		});
	},

	updateSort: function(){
		data = $("#content-list").sortable('toArray');
		call.updateOrder(data);
		console.log("Update Sort Happened");
	},

	addToSort: function(x){
		data = $("#content-list").sortable('toArray');
		data.unshift(x);
		call.updateOrder(data);
		console.log("Update Sort Happened");
	},

	match: function(materials){
		var theorder ="";
		var finalresult = [];
		console.log(materials);
		call.order().success(function (data) {
			if(data == "0"){
				// the sort is empty
				finalresult = materials;
			}
			else {
				console.log("materialOrder.fetch ran");
				console.log(materials);
				console.log(data);
				theorder = data;
				theorder = $.parseJSON(theorder);
				var result = [];
				$.each(theorder, function(i, id){
					$.each(materials, function(it, material){
						if(material["id"] == id){
							var itnum = i ;
							result[itnum] = material;
						}
						else{
							result[itnum] = "0";
						}
					});
				});
				//console.log(result);
				//account for updated orders (new materials/deleted materials)
				result = $.grep(result,function(n){ return n == 0 || n });
				//console.log(result);
				console.log("materialOrder.match ran");
				finalresult = result;
			}
		});
	return finalresult;
	},

};

var tutorialSettings = [
	[
		'Welcome to', //0
		'Lets take a quick <b>Tour</b>.', //1
		'top center', //2  add the position for the pointer (css)
		'logo_study_legal.png', //image url //3
		'no-border', //type none, if no image. //4
		'center', //5
		'center', //6
		'body', //7
	],
	[
		'Search For Cases Here', //0
		'Search for <b>Cases</b> by name here.', //1
		'bottom left', //2
		'search-for-case-animated.gif', //3
		'', //4
		'left top', //5
		'left bottom',//6
		'#content-search-input', //7
	],
	[
		'Add Codes & Custom Materials',
		'Add a <b>Code/Statute</b> or your own <b>Material</b>.',
		'bottom left',
		'how-to-images-codes-custom.png',
		'',
		'left top',
		'left bottom',
		'.materials-wrapper',
	],
	[
		'Your Course Materials',
		'All of your <b>Materials</b> are saved in this list.',
		'right center',
		'how-to-images-your-materials.png',
		'',
		'left center',
		'right center',
		'.content-list',
	],
	[
		'Take Notes By Highlighting',
		'<b>Highlight</b> sections you want to save to your <b>Brief</b> like this.',
		'bottom center',
		'how-to-images-highlighted-text.png',
		'',
		'center top',
		'center bottom',
		'.tutorial-highlighted',
	],
	[
		'Then, Click These Buttons',
		'<b>Click</b> these buttons to save the <b>highlighted text</b> to the relevant <b>Brief</b> section',
		'left center',
		'how-to-images-save-text.png',
		'',
		'right top',
		'left top',
		'#sticky',
	],
	[
		'View Your Brief Here',
		'Click these buttons to switch between <b>Reading</b> and your <b>Brief</b>.',
		'bottom right',
		'how-to-images-read-brief-toggle.png',
		'',
		'right top',
		'right bottom',
		'#brief-toggle',
	],
];

var startSemantic = {
	init: function(){
		$('.ui.dropdown').dropdown();
		$('.cb').dropdown({
    	on: 'hover'
  	});
		$('.ui.radio.checkbox').checkbox();
		startSemantic.makeSticky();

	},

	makeSticky: function(){
		$('#sticky')
		.sticky({
			/*offset: 200, this is to adjust the height, the problem is that it works straight out the gate, not just when scrolling	*/
			context: '#context'
		});
	},

	showTutorial: function(iter){
		$('body').dimmer({
			opacity: .3,
			closable: false,
		}).dimmer('show');
		$('#tutorial-highlighted').addClass('tutorial-highlighted');
		$('.tutorial-popup').remove();
		//make the cycle work
		totalIter = tutorialSettings.length - 1;
		nextIter = iter + 1;
		previousIter = iter -1;
		//get the info from the array
		item = tutorialSettings[iter];
		theHtml = 	'	<div class="ui wide popup ' + item[2] +' visible tutorial-popup"><h4 class="ui header">' + item[0] + '</h4><div class="content">'
								+ '<img class="ui centered bordered rounded image ' + item[4] +' tutorial-image" src="/assets/img/' + item[3] + '"  /><p>' + item[1] + '</p>';
		if (iter == 0) {
			//the first one
			theHtml = theHtml +
			'<button class="ui left floated labeled icon button" onClick="javascript:startSemantic.exitTutorial();"><i class="left close icon"></i>Skip</button><button class="ui right floated positive right labeled icon button" onClick="javascript:startSemantic.showTutorial(' + nextIter + ')"><i class="right arrow icon"></i>Begin Tour</button></div></div>';
		}
		else if (iter == totalIter) {
			//previousItem = tutorialSettings[previousIter];
			//$(previousItem[4]).popup('hide');
			//the last one
			theHtml = theHtml +
			'<button class="ui left floated labeled icon button" onClick="javascript:startSemantic.showTutorial(' + previousIter + ');"><i class="left arrow icon"></i>Back</button>' +
			'<button class="ui right floated positive right labeled icon button" onClick="javascript:startSemantic.exitTutorial();"><i class="close arrow icon"></i>Done</button></div></div>';
		}
		else {
			//previousItem = tutorialSettings[previousIter];
			//$(previousItem[4]).popup('hide');
			theHtml = theHtml +
			'<button class="ui left floated labeled icon button" onClick="javascript:startSemantic.showTutorial(' + previousIter + ');"><i class="left arrow icon"></i>Back</button>' +
			'<button class="ui right floated positive right labeled icon button" onClick="javascript:startSemantic.showTutorial(' + nextIter + ');"><i class="right arrow icon"></i>Next</button></div></div>';
		}
		$('body').append(theHtml);
		$('.tutorial-popup').position({
				  my: item[5],
				  at: item[6],
				  of: item[7],
				});

		/*Rebuild as this:
		<div class="ui wide popup bottom left visible"><div class="ui three column divided center aligned grid"><div class="column"><h4 class="ui header">Basic Plan</h4>
            <p><b>2</b> projects, $10 a month</p>
            <div class="ui button">Choose</div>
          </div>
          <div class="column">
            <h4 class="ui header">Business Plan</h4>
            <p><b>5</b> projects, $20 a month</p>
            <div class="ui button">Choose</div>
          </div>
          <div class="column">
            <h4 class="ui header">Premium Plan</h4>
            <p><b>8</b> projects, $25 a month</p>
            <div class="ui button">Choose</div>
          </div>
        </div>
      </div>
			Don't use modals, or semantic 'popup'.  Use jquery position ui and just build the elements where necessary.
			*/

		/*working version with modals
		$('#tutorial-highlighted').addClass('tutorial-highlighted');
		$('.tutorial-modal').remove();
		//make the cycle work
		totalIter = tutorialSettings.length - 1;
		nextIter = iter + 1;
		previousIter = iter -1;
		//get the info from the array
		item = tutorialSettings[iter];
		theHtml = 	'<div class="ui small modal tutorial-modal"><div class="header">' + item[0] + '</div><div class="content">'
								+ '<img class="ui centered bordered rounded image" src="/assets/img/' + item[5] + '" style="display:' + item[7] + '"/><br/><h2>' + item[1] + '</h2><br/><br/></div><div class="actions">';
		if (iter == 0) {
			//the first one
			theHtml = theHtml +
			'<button class="ui left floated labeled icon button" onClick="javascript:startSemantic.exitTutorial();"><i class="left close icon"></i>Skip</button><button class="ui positive right labeled icon button" onClick="javascript:startSemantic.showTutorial(' + nextIter + ')"><i class="right arrow icon"></i>Begin Tour</button></div></div>';
		}
		else if (iter == totalIter) {
			//previousItem = tutorialSettings[previousIter];
			//$(previousItem[4]).popup('hide');
			//the last one
			theHtml = theHtml +
			'<button class="ui left floated labeled icon button" onClick="javascript:startSemantic.showTutorial(' + previousIter + ');"><i class="left arrow icon"></i>Back</button>' +
			'<button class="ui positive right labeled icon button" onClick="javascript:startSemantic.exitTutorial();"><i class="close arrow icon"></i>Done</button></div></div>';
		}
		else {
			//previousItem = tutorialSettings[previousIter];
			//$(previousItem[4]).popup('hide');
			theHtml = theHtml +
			'<button class="ui left floated labeled icon button" onClick="javascript:startSemantic.showTutorial(' + previousIter + ');"><i class="left arrow icon"></i>Back</button>' +
			'<button class="ui positive right labeled icon button" onClick="javascript:startSemantic.showTutorial(' + nextIter + ');"><i class="right arrow icon"></i>Next</button></div></div>';
		}
		$('body').prepend(theHtml);
		$('.tutorial-modal')
  		.modal({
				closable: false,
				dimmerSettings: {
					opacity: .3,
					closable: false,
					blurring: false,
				}
		  }).modal('show');*/
	},

	/*POPUP Tutorial - had issues with hover/disappear
	showTutorial: function(iter){
		$('#tutorial-highlighted').addClass('tutorial-highlighted');
		$('body').popup('destroy');
		//make the cycle work
		totalIter = tutorialSettings.length - 1;
		nextIter = iter + 1;
		previousIter = iter -1;
		//get the info from the array
		item = tutorialSettings[iter];
		theHtml = 	''
								+ '<div class="header">' + item[0] + '</div><br/>'
								+ '<div class="content"><img class="ui centered bordered rounded image" src="/assets/img/' + item[5] + '"/><br/>' + item[1] + '<br/><br/>';
		if (iter == 0) {
			//the first one
			theHtml = theHtml +
			'<button class="ui green right floated right labeled icon button" onClick="javascript:startSemantic.showTutorial(' + nextIter + ')"><i class="right arrow icon"></i>Begin Tour</button></div>';
		}
		else if (iter == totalIter) {
			//previousItem = tutorialSettings[previousIter];
			//$(previousItem[4]).popup('hide');
			//the last one
			theHtml = theHtml +
			'<button class="ui left floated labeled icon button" onClick="javascript:startSemantic.showTutorial(' + previousIter + ')"><i class="left arrow icon"></i>Back</button></div>' +
			'<button class="ui green right floated right labeled icon button" onClick="javascript:startSemantic.exitTutorial()"><i class="close arrow icon"></i>Done</button></div>';
		}
		else {
			//previousItem = tutorialSettings[previousIter];
			//$(previousItem[4]).popup('hide');
			theHtml = theHtml +
			'<button class="ui left floated labeled icon button" onClick="javascript:startSemantic.showTutorial(' + previousIter + ')"><i class="left arrow icon"></i>Back</button></div>' +
			'<button class="ui green right floated right labeled icon button" onClick="javascript:startSemantic.showTutorial(' + nextIter + ')"><i class="right arrow icon"></i>Next</button></div>';
		}
		$('body')
  		.popup({
				//exclusive: true,
		    position : item[3],
		    target   : item[4],
				hoverable: true,
				//on: 'hover',
				closable: false,
				//hideOnScroll: false,
				html: theHtml,
				distanceAway: item[6],
				className   : {
				  popup       : 'ui wide popup tutorial-popup ',
				}
		  }).popup('show');
			$('body').popup('show');
			$('body').dimmer({
				opacity: .3,
				closable: false,
			}).dimmer('show');
	},*/

	tutorialContent: function(toggl){
		//purpose was moved to build.contentEmpty;
		theCase = '';
		if (toggl == 'show'){
			build.materials([{"id":"99999","short_name":" PIERSON v. POST","full_name":" PIERSON v. POST"}]);
			build.content({'full_name' : ' PIERSON v. POST', 'content_parsed': theCase});
		}
		if (toggl == 'hide'){
			build.materialsEmpty();
			build.contentEmpty();
		}
	},

	exitTutorial: function(){
	//	$('.tutorial-modal').modal('hide');
		$('body').dimmer('hide');
		//$('.tutorial-modal').remove();
		$('.tutorial-popup').remove();
		$('#tutorial-highlighted').removeClass('tutorial-highlighted');
	},
};
// ----------- START THE PROCESS
$(document).ready(function(){
load.init();
searchForContent.init();
sl_modals.init();
readingTools.init();
tabs.init();
ctrl.init();
autosave.init();
monitorAccount.init();
materialOrder.init();
startSemantic.init();
});
