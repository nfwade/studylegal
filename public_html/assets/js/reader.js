/*
Style Guide
  RULES
    > only Controller unites Model and View.
    > Small simple objects are contained in the general page_C Controller

  SYNTAX
    > AJAX Responses: 'response'
    > Two spaces between Object MVC Groups
    > One space between Objects
    > Object MVCs are documented by commented Titles.
    > The V method for showing loading is named "clear"
    > Model = _M, View = _V, Controller = _C

*/


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
                "' onClick='materialList_C.addMaterial(this.id);'><i class='plus icon'></i>" +
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
								'" onClick="materialList_C.addMaterial(this.id);"><i class="plus icon"></i>' +
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

/*Add Course Menu*/
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
        //then start the materials _C
        materialList_C.loadMaterials();
      }
    });
  },

  setActive: function(id){
  //change the _M
  userCourses_M.activeCourse = id;
  //trigger the _V
  userCourses_V.setActive();
  //start the materials _C
  materialList_C.loadMaterials();
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


/* Material List */
var materialList_M = {
  materials: [],
  activeMaterial: '',
};

var materialList_V = {
  buildList: function(){
  	$(".content-list").empty();
		$.each(materialList_M.materials, function(i, field){
			$(".content-list").append(
				"<a class='item content-list-item material" +field["id"] + "' id='" +
				field["id"] +
				"' onClick ='materialList_C.setActive(this.id);'>" +
				field["short_name"] +
				"<span class='delete-material-button' id='" +
				field["id"] +
				"' onClick='materialList_C.removeMaterial(this.id);'><i class='remove icon'></i></span></a>"
				);
		});
    materialList_V.makeSortable();
  },

  buildEmptyList: function(){
    $(".content-list").empty();
    //$(".content-list").html("<a class='item'>No materials yet.</a>");
  },

  blur: function(){

  },

  setActive:function(){
    $(".content-list-item").removeClass("active");
		$(".material" + materialList_M.activeMaterial).addClass("active");
  },

  makeSortable: function(){
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
				materialList_C.updateOrder();
			}
		});
  },
};

var materialList_C = {
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
        materialList_M.materials = response;
        if (materialList_M.materials.length != 0){
          materialList_V.buildList();
          materialList_C.setActive(materialList_M.materials[0]['id']);
        }
        else {
          materialList_V.buildEmptyList();
          currentMaterial_V.buildEmptyContent();
        }
      },
		});
  },

  setActive: function(materialId){
    brief_C.save();
    materialList_M.activeMaterial = materialId;
    materialList_V.setActive();
    //fire the load currrent material
    currentMaterial_C.loadMaterial();
  },

  updateMaterials: function(data){
		$.ajax({
			async: true,
			type: "POST",
			url: "/app/user/materials/update",
			data: { idcourse: userCourses_M.activeCourse, materialList: data },
      success: function(response){
        page_C.testResponse(response);
        materialList_C.loadMaterials();
      },
		});
  },

  addMaterial: function(materialId){
    data = $("#content-list").sortable('toArray');
    data.unshift(materialId);
    data = JSON.stringify(data);
    materialList_C.updateMaterials(data);
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
  			materialList_C.addMaterial(response);
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
          materialList_C.updateMaterials(data);
        }
      }).modal('show');
  },

  updateOrder: function(){
		data = $("#content-list").sortable('toArray');
    data = JSON.stringify(data);
    materialList_C.updateMaterials(data);
	},
};


/* Current Material */
var currentMaterial_M = {
  activeMaterial: [],
};

var currentMaterial_V = {
  buildContent: function(data){
    $('#tutorial-title').remove();
    //clear the fields
    $(".content-title").html("");
    $(".content-date").html("");
    $(".content-citation").html("");
    $(".content-opinion").html("");

    //put the data
    //var shortername = x['short_name'];//.substring(0,10);
    $(".content-title").append(data["full_name"]);
    $(".content-date").append(data["date_published"]);
    $(".content-citation").append(data["citation"]);
    $(".content-opinion").append(data["content_parsed"]);
  },

  buildEmptyContent: function(){
    //var theHtml = '<P>TOMPKINS, J. delivered the opinion of the court. <P>This cause comes before us on a return to a <I>certiorari</I> directed to one of the justices of <I>Queens</I> county. <P> <P>The question submitted by the counsel in this cause for our determination is, whether <I>Lodowick Post,</I> by the pursuit with his hounds in the manner alleged in his declaration, acquired such a right to, or property in, the fox, as will sustain an action against <I>Pierson</I> for killing and taking him away? <P> <P>The cause was argued with much ability by the counsel on both sides, and presents for our decision a novel and nice question.<span class="" id="tutorial-highlighted"> It is admitted that a fox is an animal <I>fer&#230; natur&#230;,</I> and that property in such animals is acquired by occupancy only.</span> These admissions narrow the discussion to the simple question of what acts amount to occupancy, applied to acquiring right to wild animals? <P> <P>If we have recourse to the ancient writers upon general principles of law, the judgment below is obviously erroneous. <I>Justinians Institutes,</I> lib. 2. tit. 1. s. 13. and <I>Fleta,</I> lib. 3. c. 2. p. 175. adopt the principle, that pursuit alone vests no property or right in the huntsman; and that even pursuit, accompanied with wounding, is equally ineffectual for that purpose, unless the animal be actually taken. The same principle is recognised by <I>Bracton,</I> lib. 2. c. 1. p. 8. <P> <P><I>Puffendorf,</I> lib. 4. c. 6. s. 2. and 10. defines occupancy of beasts <I>fer&#230; natur&#230;,</I> to be the actual corporal possession of them, and <I>Bynkershoek</I> is cited as coinciding in this definition. It is indeed with hesitation that <I>Puffendorf</I> affirms that a wild beast mortally wounded, or greatly maimed, cannot be fairly intercepted by another, whilst the pursuit <font color="#FF0000">[*178]</font> of the person inflicting the wound continues. The foregoing authorities are decisive to show that mere pursuit gave <I>Post</I> no legal right to the fox, but that he became the property of <I>Pierson,</I> who intercepted and killed him. <P> <P>It therefore only remains to inquire whether there are any contrary principles, or authorities, to be found in other books, which ought to induce a different decision. Most of the cases which have occurred in <I>England,</I> relating to property in wild animals, have either been discussed and decided upon the principles of their positive statute regulations, or have arisen between the huntsman and the owner of the land upon which beasts <I>fer&#230; natur&#230;</I> have been apprehended; the former claiming them by title of occupancy, and the latter <I>ratione soli.</I> Little satisfactory aid can, therefore, be derived from the <I>English</I> reporters. <P> <P><I>Barbeyrac,</I> in his notes on <I>Puffendorf,</I> does not accede to the definition of occupancy by the latter, but, on the contrary, affirms, that actual bodily seizure is not, in all cases, necessary to constitute possession of wild animals. He does not, however, <I>describe</I> the acts which, according to his ideas, will amount to an appropriation of such animals to private use, so as to exclude the claims of all other persons, by title of occupancy, to the same animals; and he is far from averring that pursuit alone is sufficient for that purpose. To a certain extent, and as far as <I>Barbeyrac</I> appears to me to go, his objections to <I>Puffendorfs</I> definition of occupancy are reasonable and correct. That is to say, that actual bodily seizure is not indispensable to acquire right to, or possession of, wild beasts; but that, on the contrary, the mortal wounding of such beasts, by one not abandoning his pursuit, may, with the utmost propriety, be deemed possession of him; since, thereby, the pursuer manifests an unequivocal intention of appropriating the animal to his individual use, has deprived him of his natural liberty, and brought him within his certain control. So also, encompassing and securing such animals with nets and toils, or otherwise intercepting them in such a manner as to deprive them of their natural liberty, and render escape impossible, may justly be deemed to give possession of them to those persons who, by their industry and labour, have used such means of apprehending them. <I>Barbeyrac</I> seems to have adopted, and had in view in his notes, <font color="#FF0000">[*179]</font> the more accurate opinion of <I>Grotius,</I> with respect to occupancy. That celebrated author, lib. 2. c. 8. s. 3. p. 309. speaking of occupancy, proceeds thus: "<I>Requiritur autem corporalis qu&#230;dam possessio ad dominium adipiscendum; atque ideo, vulnerasse non sufficit.</I>" But in the following section he explains and qualifies this definition of occupancy: "<I>Sed possessio illa potest non solis manibus, sed instrumentis, ut decipulis, retibus, laqueis dum duo adsint: primum ut ipsa instrumenta sint in nostra potestate, deinde ut fera, ita inclusa sit, ut exire inde nequeat.</I>" This qualification embraces the full extent of <I>Barbeyracs</I> objection to <I>Puffendorfs</I> definition, and allows as great a latitude to acquiring property by occupancy, as can reasonably be inferred from the words or ideas expressed by <I>Barbeyrac</I> in his notes. The case now under consideration is one of mere pursuit, and presents no circumstances or acts which can bring it within the definition of occupancy by <I>Puffendorf,</I> or <I>Grotius,</I> or the ideas of <I>Barbeyrac</I> upon that subject. <P> <P>The case cited from 11 <I>Mod.</I> 74&#151;130. I think clearly distinguishable from the present; inasmuch as there the action was for maliciously hindering and disturbing the plaintiff in the exercise and enjoyment of a private franchise; and in the report of the same case, 3 <I>Salk.</I> 9. <I>Holt,</I> Ch. J. states, that the ducks were in the plaintiffs decoy pond, and <I>so in his possession,</I> from which it is obvious the court laid much stress in their opinion upon the plaintiffs possession of the ducks, <I>ratione soli.</I> <P> <P>We are the more readily inclined to confine possession or occupancy of beasts <I>fer&#230; natur&#230;,</I> within the limits prescribed by the learned authors above cited, for the sake of certainty, and preserving peace and order in society. If the first seeing, starting, or pursuing such animals, without having so wounded, circumvented or ensnared them, so as to deprive them of their natural liberty, and subject them to the control of their pursuer, should afford the basis of actions against others for intercepting and killing them, it would prove a fertile source of quarrels and litigation. <P> <P>However uncourteous or unkind the conduct of <I>Pierson</I> towards <I>Post,</I> in this instance, may have been, yet his act was productive of no injury or damage for which a legal <font color="#FF0000">[*180]</font> remedy can be applied. We are of opinion the judgment below was erroneous, and ought to be reversed.';;
		$(".content-title").html("No materials yet.");
		$(".content-date").html("");
		$(".content-citation").html("");
		$(".content-opinion").html("<b><i class='ui icon chevron left'></i>Add materials here by searching for the case name or selecting a code.</b>");
		//$(".content-opinion").append(theHtml);
    //$("#content-segment").prepend('<h3 id="tutorial-title" style="text-decoration:underline; text-align:center;"></h3>');
  },
};

var currentMaterial_C = {
  loadMaterial: function(){
    page_C.showLoading('#content-segment');
		$.ajax({
			async: true,
			type: "GET",
      dataType: "json",
			url: "/app/static/material/" + materialList_M.activeMaterial,
      success: function(response) {
        page_C.testResponse(response);
        currentMaterial_M.activeMaterial = response;
        currentMaterial_V.buildContent(currentMaterial_M.activeMaterial[0]);
        page_C.hideLoading('#content-segment');
        toolbar_C.refreshSticky();
        brief_C.loadBrief();
      }
		});
  },
};


/* Brief */
var brief_M= {
  activeBrief: [],
  updatedBrief: [],
  needSave: false,
};

var brief_V= {
  buildBrief: function(x){
    factsEditor.setHTML('');
    issuesEditor.setHTML('');
    holdingEditor.setHTML('');
    rulesEditor.setHTML('');
    analysisEditor.setHTML('');
    notesEditor.setHTML('');
    if (x != '0'){
      factsEditor.setHTML(x["facts"]);
  		issuesEditor.setHTML(x["issues"]);
  		holdingEditor.setHTML(x["holding"]);
  		rulesEditor.setHTML(x["rules"]);
  		analysisEditor.setHTML(x["analysis"]);
  		notesEditor.setHTML(x["notes"]);
    }
  },
};

var brief_C= {
  init: function(){
    this.staticBehavior();
  },

  loadBrief: function(){
    //make sure to save it before you load another
    if (brief_M.needSave == true){
      brief_C.save();
    }
		$.ajax({
			async: true,
			type: "GET",
      dataType: "json",
			url: "/app/user/notes/" + userCourses_M.activeCourse + "/" + materialList_M.activeMaterial,
      success: function(response) {
        page_C.testResponse(response);
        brief_M.activeBrief = response;
        brief_V.buildBrief(brief_M.activeBrief);
        toolbar_C.refreshSticky();
      }
		});
  },

  save: function(){
    if(brief_M.needSave == true) {
      var fac = factsEditor.getHTML();
  		var iss = issuesEditor.getHTML();
  		var hol = holdingEditor.getHTML();
  		var rul = rulesEditor.getHTML();
  		var ana = analysisEditor.getHTML();
  		var not = notesEditor.getHTML();
      //call.updateBrief(fac,iss,hol,rul,ana,not)
  		$.ajax({
  			async: true,
  			type: "POST",
  			url: "/app/user/update/brief",
  			data: {
  				crid: userCourses_M.activeCourse,
  				cnid: materialList_M.activeMaterial,
  				f: fac,
  				i: iss,
  				h: hol,
  				r: rul,
  				a: ana,
  				n: not
  				},
          success: function(){
            brief_C.deactivateAutosave();
          },
  		});
		}
  },

  activateAutosave: function(){
    brief_M.needSave = true;
		$("#auto-save-status").html("<i class='close icon'></i>Saving...");
		setTimeout(brief_C.save, 4000);
  },

  deactivateAutosave: function(){
    brief_M.needSave = false;
		$("#auto-save-status").html("<i class='check icon'></i>Saved");
  },

  staticBehavior: function(){
    $("#brief-save-button").click( function() {
			brief_C.save();
		});

		notesEditor.on('text-change', function(delta, source) {
		  if (source == 'user') {
		  	brief_C.activateAutosave();
  			}
		});

		factsEditor.on('text-change', function(delta, source) {
		  if (source == 'user') {
		  	brief_C.activateAutosave();
  			}
		});

		analysisEditor.on('text-change', function(delta, source) {
		  if (source == 'user') {
		  	brief_C.activateAutosave();
  			}
		});

		holdingEditor.on('text-change', function(delta, source) {
		  if (source == 'user') {
		  	brief_C.activateAutosave();
  			}
		});

		rulesEditor.on('text-change', function(delta, source) {
		  if (source == 'user') {
		  	brief_C.activateAutosave();
  			}
		});

		issuesEditor.on('text-change', function(delta, source) {
		  if (source == 'user') {
		  	brief_C.activateAutosave();
  			}
		});
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
};


/* Toolbar */
var toolbar_M = {
  //active _V can have values 'read', 'brief', or 'dual\'
  active_V: 'read',
};

var toolbar_V = {};

var toolbar_C = {
  init: function(){
    this.staticBehavior();
    this.showContent();
  },

  staticBehavior: function(){
    $('#content-toggle').click(function(){
      toolbar_C.showContent();
    });
    $('#brief-toggle').click(function(){
      toolbar_C.showBrief();
    });
    $('#dual-toggle').click(function(){
      toolbar_C.showDual();
    });
  },

  refreshSticky: function(){
    $('#sticky').sticky('refresh');
  },

  stickyContent: function(){
    $('#sticky')
    .sticky({
      context: '#context-content'
    });
  },

  stickyBrief: function(){
    $('#sticky')
    .sticky({
      context: '#context-brief'
    });
  },

  showBrief: function(){
    $('.brief-toolbar > .item').removeClass('darker');
    $('.brief-toolbar > #brief-toggle').addClass('darker');

    $('.content-wrapper').removeClass('eight wide column').addClass('eleven wide column');

    $('.brief-wrapper').removeClass('six wide column').addClass('eleven wide column');

    $('#materials-column').show();
    $('.content-wrapper').hide();
    $('.brief-wrapper').show();
    toolbar_C.stickyBrief();
    toolbar_M.active_V = 'brief';
  },

  showContent: function(){
    $('.brief-toolbar > .item').removeClass('darker');
    $('.brief-toolbar > #content-toggle').addClass('darker');

    $('.content-wrapper').removeClass('eight wide column').addClass('eleven wide column');

    $('.brief-wrapper').removeClass('six wide column').addClass('eleven wide column');

    $('#materials-column').show();
    $('.brief-wrapper').hide();
    $('.content-wrapper').show();
    toolbar_C.stickyContent();
    toolbar_M.active_V = 'read';
  },

  showDual: function(){
    $('#materials-column').hide();
    $('.content-wrapper').show();
    $('.brief-wrapper').show();

    $('.brief-toolbar > .item').removeClass('darker');
    $('.brief-toolbar > #dual-toggle').addClass('darker');

    $('.content-wrapper').removeClass('eleven wide column').addClass('eight wide column');

    $('.brief-wrapper').removeClass('eleven wide column').addClass('six wide column');
    toolbar_M.active_V = 'dual';
  },
};


/* Highlight bar */
var highlight_M = {
  selectedText: [],
}

var highlight_V = {
  showMenu: function(pageX, pageY){
    horizAdjust = 200;
    if (toolbar_M.active_V == 'dual'){
      horizAdjust = 50;
    }
    $('#highlight-menu').css({
        'left': pageX - horizAdjust,
        'top' : pageY - 145
    }).fadeIn(200);
  },

  hideMenu: function(){
    $('#highlight-menu').fadeOut(200);
  },
};

var highlight_C = {
	init: function(){
    $('body').bind('mousedown', function(){
      highlight_V.hideMenu();
    });
		var pageX;
		var pageY;
		$('.content-wrapper').bind('mouseup', function() {
				var selectedText = highlight_C.getSelectedText();
				if(selectedText != ''){
          highlight_V.showMenu(pageX, pageY);
				} else {
          highlight_V.hideMenu();
				}
		});
		$(document).on('mousedown', function(e){
				pageX = e.pageX;
				pageY = e.pageY;
		});

    this.initBriefButtons();
	},

  initBriefButtons: function(){
    $(".f").mousedown(function() {
      highlight_C.addFact();
    });
    $(".i").mousedown(function() {
      highlight_C.addIssue();
    });
    $(".r").mousedown(function() {
      highlight_C.addRule();
    });
    $(".a").mousedown(function() {
      highlight_C.addAnalysis();
    });
    $(".h").mousedown(function() {
      highlight_C.addHolding();
    });
    $(".n").mousedown(function() {
      highlight_C.addNote();
    });
  },

  getSelectedText: function(){
    var text = "";
  	if (window.getSelection) {
  		text = window.getSelection().toString();
  	} else if (document.selection && document.selection.type != "Control") {
  		text = document.selection.createRange().text;
  	}
  	return text;
  },

  addFact: function(){
    text = highlight_C.getSelectedText();
		if(text != ""){
  	$.ajax({
  			async: true,
  			type: "POST",
  			url: "/app/user/add/brief/fact",
  			data: { afact: text, crid: userCourses_M.activeCourse, cnid: currentMaterial_M.activeMaterial[0].id },
    		success: function( response ) {
    				page_C.testResponse(response);
    				if(response == "1"){
    					alertify.success("Fact added successfully.");
              brief_C.loadBrief();
    				}
    				else {
    					alertify.error("Something went wrong.");
    				}
    			}
      });
		}
  },

  addIssue: function(){
    text = highlight_C.getSelectedText();
    if(text != ""){
    $.ajax({
        async: true,
        type: "POST",
        url: "/app/user/add/brief/issue",
        data: { aissue: text, crid: userCourses_M.activeCourse, cnid: currentMaterial_M.activeMaterial[0].id },
        success: function( response ) {
            page_C.testResponse(response);
            if(response == "1"){
              alertify.success("Issue added successfully.");
              brief_C.loadBrief();
            }
            else {
              alertify.error("Something went wrong.");
            }
          }
      });
    }
  },

  addRule: function(){
    text = highlight_C.getSelectedText();
    if(text != ""){
    $.ajax({
        async: true,
        type: "POST",
        url: "/app/user/add/brief/rule",
        data: { arules: text, crid: userCourses_M.activeCourse, cnid: currentMaterial_M.activeMaterial[0].id },
        success: function( response ) {
            page_C.testResponse(response);
            if(response == "1"){
              alertify.success("Rule added successfully.");
              brief_C.loadBrief();
            }
            else {
              alertify.error("Something went wrong.");
            }
          }
      });
    }
  },

  addAnalysis: function(){
    text = highlight_C.getSelectedText();
    if(text != ""){
    $.ajax({
        async: true,
        type: "POST",
        url: "/app/user/add/brief/analysis",
        data: { aanalysis: text, crid: userCourses_M.activeCourse, cnid: currentMaterial_M.activeMaterial[0].id },
        success: function( response ) {
            page_C.testResponse(response);
            if(response == "1"){
              alertify.success("Analysis added successfully.");
              brief_C.loadBrief();
            }
            else {
              alertify.error("Something went wrong.");
            }
          }
      });
    }
  },

  addHolding: function(){
    text = highlight_C.getSelectedText();
		if(text != ""){
  	$.ajax({
  			async: true,
  			type: "POST",
  			url: "/app/user/add/brief/holding",
  			data: { aholding: text, crid: userCourses_M.activeCourse, cnid: currentMaterial_M.activeMaterial[0].id },
    		success: function( response ) {
    				page_C.testResponse(response);
    				if(response == "1"){
    					alertify.success("Holding added successfully.");
              brief_C.loadBrief();
    				}
    				else {
    					alertify.error("Something went wrong.");
    				}
    			}
      });
		}
  },

  addNote: function(){
    text = highlight_C.getSelectedText();
    if(text != ""){
    $.ajax({
        async: true,
        type: "POST",
        url: "/app/user/add/brief/note",
        data: { anotes: text, crid: userCourses_M.activeCourse, cnid: currentMaterial_M.activeMaterial[0].id },
        success: function( response ) {
            page_C.testResponse(response);
            if(response == "1"){
              alertify.success("Note added successfully.");
              brief_C.loadBrief();
            }
            else {
              alertify.error("Something went wrong.");
            }
          }
      });
    }
  },

};


/* SL Tutorial */
var tutorial_M = {
  content: [
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
  	]],
};

var tutorial_C = {
  init: function(){
    $('#tutorial-start-button').click(function(){
      tutorial_C.showTutorial(0);
    })
  },

  showTutorial: function(iter){
		$('body').dimmer({
			opacity: .3,
			closable: false,
		}).dimmer('show');
		$('#tutorial-highlighted').addClass('tutorial-highlighted');
		$('.tutorial-popup').remove();
		//make the cycle work
		totalIter = tutorial_M.content.length - 1;
		nextIter = iter + 1;
		previousIter = iter -1;
		//get the info from the array
		item = tutorial_M.content[iter];
		theHtml = 	'	<div class="ui wide popup ' + item[2] +' visible tutorial-popup"><h4 class="ui header">' + item[0] + '</h4><div class="content">'
								+ '<img class="ui centered bordered rounded image ' + item[4] +' tutorial-image" src="/assets/img/' + item[3] + '"  /><p>' + item[1] + '</p>';
		if (iter == 0) {
			//the first one
			theHtml = theHtml +
			'<button class="ui left floated labeled icon button" onClick="javascript:tutorial_C.exitTutorial();"><i class="left close icon"></i>Skip</button><button class="ui right floated positive right labeled icon button" onClick="javascript:tutorial_C.showTutorial(' + nextIter + ')"><i class="right arrow icon"></i>Begin Tour</button></div></div>';
		}
		else if (iter == totalIter) {
      //the last one
			theHtml = theHtml +
			'<button class="ui left floated labeled icon button" onClick="javascript:tutorial_C.showTutorial(' + previousIter + ');"><i class="left arrow icon"></i>Back</button>' +
			'<button class="ui right floated positive right labeled icon button" onClick="javascript:tutorial_C.exitTutorial();"><i class="close arrow icon"></i>Done</button></div></div>';
		}
		else {
      //an inbetween one
			theHtml = theHtml +
			'<button class="ui left floated labeled icon button" onClick="javascript:tutorial_C.showTutorial(' + previousIter + ');"><i class="left arrow icon"></i>Back</button>' +
			'<button class="ui right floated positive right labeled icon button" onClick="javascript:tutorial_C.showTutorial(' + nextIter + ');"><i class="right arrow icon"></i>Next</button></div></div>';
		}
		$('body').append(theHtml);
		$('.tutorial-popup').position({
				  my: item[5],
				  at: item[6],
				  of: item[7],
				});
  },

  exitTutorial: function(){
	//	$('.tutorial-modal').modal('hide');
		$('body').dimmer('hide');
		//$('.tutorial-modal').remove();
		$('.tutorial-popup').remove();
		$('#tutorial-highlighted').removeClass('tutorial-highlighted');
	},
};

/* Start all _Cs */
$(document).ready(function(){
page_C.init();
courseMenu_C.init();
userCourses_C.init();
brief_C.init();
toolbar_C.init();
highlight_C.init();
tutorial_C.init();
console.log('All Objects Initialized.')
});
/*Remove page loader*/
$(window).load(function() {
   $(".sl-loader").fadeOut("slow");
});
