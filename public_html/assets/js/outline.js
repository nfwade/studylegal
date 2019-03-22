/* Page */
var page_M = {};

var page_V = {
  default: function(){
  },
};

var page_C = {
  init: function(){
    this.initCodeList();
    this.initCaseSearch();
    this.staticBehavior();
    this.initCustomMaterial();
    this.initDownloadMaterial();
    /*start semantic stuff */
    $('.ui.modal').modal();
  },

  staticBehavior: function(){

  },

  initCodeList: function(){
    $.ajax({
			async: true,
			type: "GET",
			url: "/app/static/codes",
      success: function(response){
        $("#add-code-menu").html("");
        var cbSecItr = 0;
        //For each book
        x = $.parseJSON( response );
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
                "' onClick='briefs_C.addMaterial(this.id);'><i class='plus icon'></i>" +
                codeValue['fullname'] +
                "</div>"
              );
              });
              cbSecItr++;
          });
          $("#add-code-menu").append("</div>");
        });
      }
		});
  },

  initCaseSearch: function(){
    $("#content-search-input").keyup(function() {
			var keyword = $("#content-search-input").val();
			if (keyword.length >= 4) {
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
								'" onClick="briefs_C.addMaterial(this.id);"><i class="plus icon"></i>' +
								value.full_name +
								'</div>'
							);
						});
					}
				});
			}
			else {
				$('#ac-results').empty('');
			}
		});
		$("#content-search-input").blur(function(){
    		$("#ac-results").hide(500);
    	})
        .focus(function() {
    	    $("#ac-results").slideDown();
    	});
  },

  initCustomMaterial: function(){
    $('#custom-material-button').click(function(){
			$('.add-custom-material').modal({blurring: true}).modal('show');
		});
  },

  initDownloadMaterial: function(){
    $('#download-material-button').click(function(){
      return false;
    });
  },

  testResponse: function(response){
    if (response == 'expired'){
      window.location = '/';
    }
  },

  showLoading: function(element){
		$(element).dimmer('show');
	},

	hideLoading:function(element){
		$(element).dimmer('hide');
	},
};

/* Add Course Menu */
var courseMenu_M = {
  allCourses: [],
  availableCourses: [],
};

var courseMenu_V = {
  buildList: function(courses){
    $(".add-course-list").empty();
		$.each(courses, function(i, field){
			$(".add-course-list").append(
				"<a class='item' id='" +
				field.id +
				"' onClick='userCourses_C.addCourse(this.id);'>" +
				field.course_name +
				"<i class='plus icon'></i></a>"
			);
		});
  },
};

var courseMenu_C = {
  init: function(){
    $('#add-course-button').click(function(){
  		$('#add-course-wrapper').modal({blurring: true}).modal('show');
    });
    $.ajax({
			async: true,
			type: "GET",
			dataType: "json",
			url: "/app/static/courses",
      success: function(data){
        courseMenu_M.allCourses = data;
        courseMenu_V.buildList(courseMenu_M.allCourses);
      }
		});
  },

  update: function(){
    $.ajax({
			async: true,
			type: "GET",
			dataType: "json",
			url: "/app/static/courses",
      success: function(data){
        courseMenu_M.allCourses = data;
        courseMenu_V.buildList(courseMenu_M.allCourses);
      }
		});
  },
};


/* Course Lists */
var userCourses_M = {
  userCourses: [],
  activeCourse: '',
};

var userCourses_V = {
	buildUserMenu: function(){
  	//The horizontal menu at the top of the page
    x = userCourses_M.userCourses;
    $(".item.course").remove();
    $.each(x, function(i, field){
      $(".course-list").prepend(
        "<div class='item course' id='" +
        field[0] +
        "' onClick='userCourses_C.setActive(this.id);'>" +
        field[1] +
        "</div> "
      );
    });

  //The vertical menu in the modal.
  $(".current-course-list").empty();
    $.each(x, function(i, field){
    $(".current-course-list").append(
    "<a class='course item' id='" +
    field[0] +
    "' onClick='userCourses_C.removeCourse(this.id);'>" +
    field[1] +
    "<i class='minus icon'></i></a> "
    );
  });
 },

  setActive: function(){
    //make none active
    $(".course").removeClass("active");
    //make the right one active
    thestring = ".course-list #" + userCourses_M.activeCourse;
    $(thestring).addClass("active");
  },
};

var userCourses_C = {
  init: function(){
    this.loadCourses();
  },
  //get the updated course list, load it into the _M, update the _V
  loadCourses: function(){
    //make the ajax request
    $.ajax({
      async: true,
      type: "GET",
      dataType: "json",
      url: "/app/user/courses",
      success: function(response){
        page_C.testResponse(response);
        //set the _M
        userCourses_M.userCourses = response;
        userCourses_M.activeCourse = response[0][0];
        //trigger the _V build
        userCourses_V.buildUserMenu();
        userCourses_V.setActive();
      }
    });
  },

  setActive: function(id){
  //change the _M
  userCourses_M.activeCourse = id;
  //trigger the _V
  userCourses_V.setActive();
  //start the materials _C
  //materialList_C.loadMaterials();
  },

  addCourse: function(courseNumber){
		$.ajax({
			type: "GET",
			url: "/app/user/add/course/" + courseNumber,
      success: function(response){
        page_C.testResponse(response);
        if (response == "0") {
          alertify.error("Sorry, something went wrong");
        }
        if (response == "1") {
          alertify.success("Course added successfully");
          //update the course list
          userCourses_C.loadCourses();
        }
        if (response == "2") {
          alertify.error("You're already taking this course");
        }
      },
		});
  },

  removeCourse: function(courseNumber){
    $('.confirm-remove-course')
			  .modal({
					blurring: false,
					closable  : true,
					transition: 'fade',
			    onDeny    : function(){
			      return false;
			    },
			    onApprove : function() {
            $.ajax({
              type: "GET",
              url: "/app/user/remove/course/" + courseNumber,
              success: function(response){
							  page_C.testResponse(response);
  							if (response == "0") {
  								alertify.error("Sorry, something went wrong.");
  							}
  							if (response == "1") {
  								alertify.success("Course removed successfully.");
                  //update the course list
                  userCourses_C.loadCourses();
  							}
  							if (response == "2") {
  								alertify.error("You're not taking this course.");
  							}
              },
						});
						$('.confirm-remove-course').modal('hide');
			    }
			  }).modal('show');
  },
};

/* LEFT OFF HERE - NEED TO RENAME EVERYTHING TO BRIEF INSTEAD OF MATERIAL LIST*/
/* Brief List */
var briefs_M = {
  materials: [],
};

var briefs_V = {
  buildList: function(){
  	$(".sl-outline").empty();
		$.each(x, function(i, field){
			$(".sl-outline").append(
				"<div class='material' id='" +
				field["idmaterial"] +
				"'><h3>" +
				field["alias"] +
				"</h3><div class='rules'><h4>Rule: </h4><div class='rules-content'>" +
				field["rules"] +
				"</div></div><div class='notes'><h4>Notes:</h4><div class='notes-content'>" +
				field["notes"] +
				"</div></div><div class='holding'><h4>Holding:</h4><div class='holding-content'>" +
				field["holding"] +
				"</div></div><div class='issues'><h4>Issues:</h4><div class='issues-content'>" +
				field["issues"] +
				"</div></div><div class='analysis'><h4>Analysis:</h4><div class='analysis-content'>" +
				field["analysis"] +
				"</div></div><div class='facts'><h4>Facts:</h4><div class='facts-content'>" +
				field["facts"] +
				"</div></div></div>"
				);
		});
    briefs_V.makeSortable();
  },

  buildEmptyList: function(){
    $(".content-list").empty();
    //$(".content-list").html("<a class='item'>No materials yet.</a>");
  },

  blur: function(){

  },

  setActive:function(){
    $(".content-list-item").removeClass("active");
		$(".material" + briefs_M.activeMaterial).addClass("active");
  },

  makeSortable: function(){
		$('.sl-outline').sortable({
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
				briefs_C.updateOrder();
			}
		});
  },
};

var briefs_C = {
  loadMaterials: function(){
    activeCourseId = userCourses_M.activeCourse;
    //get the materials
		$.ajax({
			async: true,
      dataType: "json",
			type: "GET",
      url: "/app/user/materials/get/" + activeCourseId,
      success: function(response){
        page_C.testResponse(response);
        briefs_M.materials = response;
        if (briefs_M.materials.length != 0){
          briefs_V.buildList();
          briefs_C.setActive(briefs_M.materials[0]['id']);
        }
        else {
          briefs_V.buildEmptyList();
          //currentMaterial_V.buildEmptyContent();
        }
      },
		});
  },

  setActive: function(materialId){
    //briefs_C.save();
    //briefs_M.activeMaterial = materialId;
    //briefs_V.setActive();
    //fire the load currrent material
    //currentMaterial_C.loadMaterial();
  },

  updateMaterials: function(data){
		$.ajax({
			async: true,
			type: "POST",
			url: "/app/user/materials/update",
			data: { idcourse: userCourses_M.activeCourse, materialList: data },
      success: function(response){
        page_C.testResponse(response);
        briefs_C.loadMaterials();
      },
		});
  },

  addMaterial: function(materialId){
    data = $("#content-list").sortable('toArray');
    data.unshift(materialId);
    data = JSON.stringify(data);
    briefs_C.updateMaterials(data);
  },

  addCustomMaterial: function(customTitle, customType, customContent){
    var fields = $( "#custom-material-form" ).serializeArray();
    var title = fields[0].value;
    var type = fields[1].value;
    var content = fields[2].value;
    //ajax
		$.ajax({
			async: true,
			type: "POST",
			url: "/app/user/add/custom/material",
			data: { title: customTitle, type: customType, content: customContent },
      success: function(response){
        page_C.testResponse(response);
  			briefs_C.addMaterial(response);
        //add the material
      }
		});
  },

  removeMaterial: function(idmaterial){
    $('.remove-material-confirmation')
			.modal({
				blurring : true,
				closable  : true,
				transition: 'fade',
				onDeny    : function(){
          $('.remove-material-confirmation').modal('hide');
          return false;
				},
				onApprove : function() {
          data = $("#content-list").sortable('toArray');
          index = data.indexOf(idmaterial);
          if (index > -1) {
              data.splice(index, 1);
          }
          data = JSON.stringify(data);
          briefs_C.updateMaterials(data);
        }
      }).modal('show');
  },

  updateOrder: function(){
		data = $("#content-list").sortable('toArray');
    data = JSON.stringify(data);
    briefs_C.updateMaterials(data);
	},
};


/*Start all Controllers*/
$(document).ready(function(){
page_C.init();
courseMenu_C.init();
userCourses_C.init();
console.log('All Objects Initialized.')
});
/*Remove page loader*/
$(window).load(function() {
   $(".sl-loader").fadeOut("slow");
});


/*var activeCourseId = "0";


// -----------FUNCTIONS RESPONSIBLE FOR ASSEMBLING HTML OBJECTS
var build = {
	//use x variable

	init: function(){
	},

	courses: function(x) {
		$(".item.course").remove();
		$.each(x, function(i, field){
			$(".course-list").prepend(
					"<div class='item course' id='" +
				field[0] +
				"' onClick='update.course(this.id);'>" +
				field[1] +
				"</li> "
				);
		});
		//$(".course-list").append("<li id='adcourse-button' onClick='modal.courseAppear();'><i class='fa fa-bars'></i></li>");
		console.log("build.courses ran");
	},

	outline: function(x) {
		$(".sl-outline").html("");
		$.each(x, function(i, field){
			$(".sl-outline").append(
				"<div class='material' id='" +
				field["idmaterial"] +
				"'><h3>" +
				field["alias"] +
				"</h3><div class='rules'><h4>Rule: </h4><div class='rules-content'>" +
				field["rules"] +
				"</div></div><div class='notes'><h4>Notes:</h4><div class='notes-content'>" +
				field["notes"] +
				"</div></div><div class='holding'><h4>Holding:</h4><div class='holding-content'>" +
				field["holding"] +
				"</div></div><div class='issues'><h4>Issues:</h4><div class='issues-content'>" +
				field["issues"] +
				"</div></div><div class='analysis'><h4>Analysis:</h4><div class='analysis-content'>" +
				field["analysis"] +
				"</div></div><div class='facts'><h4>Facts:</h4><div class='facts-content'>" +
				field["facts"] +
				"</div></div></div>"
				);
		});
		//$(".course-list").append("<li id='adcourse-button' onClick='modal.courseAppear();'><i class='fa fa-bars'></i></li>");
		console.log("build.outline ran");
	},

	outlineEmpty: function(){
		$(".sl-outline").html("");
	},

};

// ----------- FUNCTIONS FOR RELOADING OBJECTS AND CHANGING ACTIVE VALUES
var update = {
	//use y variable

	course: function(y) {
		//change the active course to x
		activeCourseId = y;
		update.setActiveCourse(y);
		load.outline(y);
	},

	setActiveCourse: function(y) {
		$(".course").removeClass("active");
		thestring = ".course-list #" + y;
		$(thestring).addClass("active");
		console.log("update.setActiveCourse ran with x="+y);
	},


};

// ----------- FUNCTIONS FOR MAKING ANYTYPE OF AJAX CALLS
var call = {
	courses: function() {
		theurl = "/app/user/courses"
		return $.ajax({
			async: false,
			type: "GET",
			//dataType: "json",
			url: theurl,
		});
	},

	outline: function(x) {
		theurl = "/app/user/outline/" + activeCourseId;
		return $.ajax({
			async: false,
			type: "GET",
			//data: {crid: x},
			url: theurl,
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

	getDoc: function(f, i, h, r, a, n){
		var theurl = "/app/user/outline/download/doc";
		return $.ajax({
			async: false,
			type: "POST",
			url: theurl,
			data: {
				crid: activeCourseId,
				fa: f,
				is: i,
				ho: h,
				ru: r,
				an: a,
				no: n
			},
		});
	},

	getCsv: function(f, i, h, r, a, n){
		var theurl = "/app/user/outline/download/csv";
		return $.ajax({
			async: false,
			type: "POST",
			url: theurl,
			data: {
				crid: activeCourseId,
				fa: f,
				is: i,
				ho: h,
				ru: r,
				an: a,
				no: n
			},
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
		//console.log("ctrl.init ran");
		$(".export-doc").click( function() {
			ctrl.downloadDoc();
		});
		$(".export-csv").click( function() {
			ctrl.downloadCsv();
		});
	},


	updateBrief: function(){
		//get the values
		var fac = $("#fact").val();
		var iss = $("#issue").val();
		var hol = $("#holding").val();
		var rul = $("#rule").val();
		var ana = $("#analysis").val();
		var not = advancedEditor.getHTML();
		//send the data
		call.updateBrief(fac,iss,hol,rul,ana,not).success( function( data ) {
			if(data == "1"){
			//success alert
				alertify.success("Save successful.");
				console.log("ctrl.updateBrief finished");
			//reload brief
			}
			else{
			//fail
				alertify.error("Something went wrong.");
			}
		});
	},

	downloadDoc: function() {
		call.getDoc(
			$("#facts").prop("checked"),
			$("#issues").prop("checked"),
			$("#holding").prop("checked"),
			$("#rules").prop("checked"),
			$("#analysis").prop("checked"),
			$("#notes").prop("checked")
		).success( function( data ) {
			window.open(data, '_blank');
		});
	},

	downloadCsv: function() {
		call.getCsv(
			$("#facts").prop("checked"),
			$("#issues").prop("checked"),
			$("#holding").prop("checked"),
			$("#rules").prop("checked"),
			$("#analysis").prop("checked"),
			$("#notes").prop("checked")
		).success( function( data ) {
			window.open(data, '_blank');
		});
	},


};

// ----------- FUNCTIONS FOR CONTROLLING THE VISUAL BEHAVIOR OF THE MODALS
var modal = {
	init: function(){
		$("#contact").click( function(){
			modal.startLoading();
		});
	},

	courseAppear: function(){
		$("#overlay").fadeIn(200);
		$("#add-course-wrapper").fadeIn(200);
		$("#overlay").click(function() {
			modal.courseDisappear();
		});
	},

	courseDisappear: function(){
		$("#overlay").fadeOut(200);
		$("#add-course-wrapper").fadeOut(200);
	},

	coursePermAppear: function(){
		$("#overlay").addClass("white-out");
		$("#overlay").show();
		$("#add-course-wrapper").show();
	},

	coursePermDisappear: function(){
		$("#overlay").removeClass("white-out");
		$("#overlay").hide();
		$("#add-course-wrapper").hide();
	},

	startLoading: function(){
		$(".outline-loading").fadeIn();
		$(".loading").show();
	},
};

// ----------- FUNCTIONS FOR LOADING HTML OBJECTS (CALL + BUILD)
var load = {
	//use u variable
	init: function() {
		load.courses();
		console.log("load.init finished");
	},


	courses: function(){
		call.courses().success(function (data) {
			data = $.parseJSON( data );
			if (data == "0"){
				//show the add course screen only
				//modal.courseAppear(true)
				console.log("load.courses ran and there were no courses");

			}
			else {
				console.log("load.courses ran and there were courses");
				//modal.coursePermDisappear();
				build.courses(data);
				update.course(data[0][0]);
			}
		});
	},

	outline: function(x){
		call.outline(x).success(function (data) {
			data = $.parseJSON( data );
			if (data == "0"){
				//show the add course screen only
				//modal.courseAppear(true)
				console.log("load.outline ran and there were no data");
				alertify.log("This course doesn't have any briefs.");
				build.outlineEmpty();
			}
			else {
				//console.log("load.outline ran and it found materials");
				//build.outline(data);

				//sort the data according to the saved list
				var getOrder = [];
				getOrder = materialOrder.match(data);
				data = getOrder;
				build.outline(data);
				//update.content(data[0]["id"]);
				console.log("load.materials ran");
			}
		});
	},
};

var briefType = {
	init: function() {
		$("#brief-type-all").click( function(){
			$(".brief-type").prop('checked', true);
			briefType.display();
		});

		$("#brief-type-none").click( function(){
			$(".brief-type").prop('checked', false);
			briefType.display();
		});

		$("#outline-content-type").change( function(){
			briefType.display();
		});

		briefType.display();
	},

	display: function() {
		//NOTES
		if ($('#notes').is(':checked')) {
		 	$('.notes').show();
		}
		else {
			$('.notes').hide();
		}

		//FACTS
		if ($('#facts').is(':checked')) {
			$('.facts').show();
		}
		else {
			$('.facts').hide();
		}

		//ISSUES
		if ($('#issues').is(':checked')) {
			$('.issues').show();
		}
		else {
			$('.issues').hide();
		}

		//HOLDING
		if ($('#holding').is(':checked')) {
			$('.holding').show();
		}
		else {
			$('.holding').hide();
		}

		//RULES
		if ($('#rules').is(':checked')) {
			$('.rules').show();
		}
		else {
			$('.rules').hide();
		}

		//ANALYSIS
		if ($('#analysis').is(':checked')) {
			$('.analysis').show();
		}
		else {
			$('.analysis').hide();
		}
	},

 	isEmpty: function( el ){
		 return !$.trim(el.html())
 	},

};

var tabs = {
	init: function() {
		$('.ui.dropdown').dropdown();
	},

	sizeBrief: function(){
		var ta = $('textarea');
		autosize(ta);
	},

	updateSizeBrief: function(){
		var ta = $('textarea');
		autosize.update(ta);
	},

	material: function() {
		$("#edit-title").click(function() {
			$("#edit-title").toggleClass("subtle-button");
			$(".edit-content-list").toggle();
			$(".content-list").toggle();
		});
	},

	contentToBrief: function() {
		$("#brief-toggle").click( function() {
			tabs.brief();
		});
		$("#content-toggle").click( function() {
			tabs.content();
		});
	},
};

var materialOrder = {
	init: function(){
		//not ready
		materialOrder.updateSort();
	},

	updateSort: function() {
		$('.sl-outline').sortable({
			axis: 'y',
			update: function (event, ui) {
				var data = $(this).sortable('toArray');
				// POST to server using $.post or $.ajax
				call.updateOrder(data);
				console.log("Update Sort Happened");
			}
		});
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
						if(material["idmaterial"] == id){
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

// ----------- START THE PROCESS
$(document).ready(function(){
load.init();
modal.init();
tabs.init();
briefType.init();
ctrl.init();
modal.init();
materialOrder.init();
});
*/
