<?php

class sl_Application_User extends sl_Database {

  public function getUserCourses($iduser){
    $database = $this->dbAppUser();
		//Get the user's saved courses
		$userscourses = $database->select("courses", "idcourse", [
		"AND" => [
			"iduser" => $iduser,
			"status" => "active"
			]
		]);
    return $userscourses;
  }

  public function getUserMaterials($iduser, $idcourse){
	  $database = $this->dbAppUser();
		$returnusermaterials = array();
		//Get the user's saved contents
		$usercontentbind = $database->select("materials", "idmaterial"
		, [
			"AND" => [
				"idcourse" => $idcourse ,
				"iduser" => $iduser,
				"status" => "active",
				],
			//"ORDER" => "dateadded DESC"
		]);
    if(empty($usercontentbind)){
      return "0";
    }
    else {
      return $usercontentbind;
    }
	}

  public function getOrder($iduser, $idcourse){
    $database = $this->dbAppUser();
    $courseorder = $database->get("materials_order", "sort", [
      "AND" => [
        "iduser" => $iduser,
        "idcourse" => $idcourse,
        "status" => "active",
      ]
    ]);
    if (!empty($courseorder)){
      return $courseorder;
    }
    else {
      return "0";
    }
  }

  public function getBrief($iduser, $idcourse, $idmaterial){
    $database = $this->dbAppUser();
    $checkexist = $database->count("notes", [
  		"AND" => [
  			"iduser" => $iduser,
  			"idcourse" => $idcourse,
  			"idmaterial" => $idmaterial,
  			"status" => "active"
  		],
  	]);
  	if ($checkexist == "1") {
  		$getbrief = $database->select("notes", [
  			"facts",
  			"issues",
  			"holding",
  			"rules",
  			"analysis",
  			"notes",
  			"date_updated",
  			"alias"
  		], [
  			"AND" => [
  				"iduser" => $iduser,
  				"idcourse" => $idcourse,
  				"idmaterial" => $idmaterial,
  				"status" => "active"
  			]
  		]);
  		//$getbrief[0]["updated"];
  		return $getbrief[0];
  	}
  	else {
  		return "0";
  	}
  }

  public function getOutline($iduser, $idcourse){
    $database = $this->dbAppUser();
    //get the outline data from the briefs
  	$getoutline = $database->select("notes",
  	/*[
  		"[>]contents" => ["idcontent" => "id"],
  	],*/ [
  		//"contents.fullname",
  		"idmaterial",
  		"alias",
  		"facts",
  		"issues",
  		"holding",
  		"rules",
  		"analysis",
  		"notes",
  		"date_updated",

  	], [
  		"AND" => [
  			"iduser" => $iduser,
  			"idcourse" => $idcourse,
  			"status" => "active",
  		],
  		"ORDER" => "date_updated",
  	]);

  	if (!empty($getoutline)){
  		return $getoutline;
  	}
  	else {
  		return "0";
  	}
  }

  public function addCourse($iduser, $idcourse){
    $database = $this->dbAppUser();
    //Check to see if the course already exists
    $checkusercourses = $database->select("courses", [
      "iduser",
      "idcourse"
      ], [
      "AND" => [
        "iduser" => $iduser,
        "idcourse" => $idcourse,
        "status" => "active"
        ]
    ]);

    //check to see if the result was empty
    if(!empty($checkusercourses)){
      return "2";
    }
    else {
    // add the new course
      $addcourse = $database->insert("courses", [
        "iduser" => $iduser,
        "idcourse" => $idcourse,
        "date_added" => time(),
        "status" => "active"
        ]);
      return "1";
    }
  }

  public function removeCourse($iduser, $idcourse){
    $database = $this->dbAppUser();
    $removecourse = $database->update("courses", [
      "status" => "inactive"
      ], [
      "AND" => [
        "iduser" => $iduser,
        "idcourse" => $idcourse
        ]
      ]);
      return "1";
  }

  public function addCustomMaterial($iduser, $title, $type, $content){
    $database = $this->dbAppUser();
    $now = time();
    $maxcustom = $database->max('user_custom_materials', 'id', [
      'iduser' => $iduser
    ]);
    $next = $maxcustom + 1;
    $userhide = 'cr' . $iduser . '-';
    $idcustom = 'cm-' . $userhide . $next;
    $putmaterial = $database->insert('user_custom_materials', [
      'iduser' => $iduser,
      'idcustom' => $idcustom,
      'short_name' => $title,
      'full_name' => $title,
      'type' => $type,
      'content_original' => $content,
      'content_parsed' => $content,
      'date_added' => $now
    ]);
    return $idcustom;
  }

  protected function checkForNotes($iduser, $idcourse, $idmaterial){
    $database = $this->dbAppUser();
    $databaseStatic = $this->dbAppStatic();
    $checkEntry = $database->has('notes', [
      'AND' => [
        'iduser' => $iduser,
        'idcourse' => $idcourse,
        'idmaterial' => $idmaterial,
        'status' => 'active',
      ]
    ]);
    if ($checkEntry != true){
      $alias = $databaseStatic->get('materials','short_name',[
        'id' => $idmaterial,
      ]);
      $addbriefrow = $database->insert("notes", [
        "iduser" => $iduser,
        "idcourse" => $idcourse,
        "idmaterial" => $idmaterial,
        "alias" => $alias,
        "date_updated" => time(),
        "status" => "active"
      ]);
    }
    return true;
  }

  public function updateNotes($iduser, $idcourse, $idmaterial, $fact, $issue, $holding, $rule, $analysis, $note){
    $database = $this->dbAppUser();
    $this->checkForNotes($iduser, $idcourse, $idmaterial);
		//update the brief
		$updatebrief = $database->update("notes", [
			"facts" => $fact,
			"issues" => $issue,
			"holding" => $holding,
			"rules" => $rule,
			"analysis" => $analysis,
			"notes" => $note,
      "date_updated" => time()
		], [
			"AND" => [
				"iduser" => $iduser,
				"idcourse" => $idcourse,
				"idmaterial" => $idmaterial,
				"status" => "active"
			]
		]);
		return "1";
  }

  public function addBriefFact($iduser, $idcourse, $idmaterial, $item){
    $database = $this->dbAppUser();
    $this->checkForNotes($iduser, $idcourse, $idmaterial);
    $getnote = $database->get('note','facts',[
      'AND' => [
        "iduser" => $iduser,
        "idcourse" => $idcourse,
        "idmaterial" => $idmaterial,
        "status" => "active"
      ]
    ]);
    $newnote = $getnote . "• " . $item . "<br/>";
    $updatenote = $database->update("notes", [
      "facts" => $newnote,
      "date_updated" => time()
      ], [
        "AND" => [
          "iduser" => $iduser,
          "idcourse" => $idcourse,
          "idmaterial" => $idmaterial,
          "status" => "active"
        ]
      ]);
    return "1";
  }

  public function addBriefIssue($iduser, $idcourse, $idmaterial, $item){
    $database = $this->dbAppUser();
    $this->checkForNotes($iduser, $idcourse, $idmaterial);
    $getnote = $database->get('note','issues',[
      'AND' => [
        "iduser" => $iduser,
        "idcourse" => $idcourse,
        "idmaterial" => $idmaterial,
        "status" => "active"
      ]
    ]);
    $newnote = $getnote . "• " . $item . "<br/>";
    $updatenote = $database->update("notes", [
      "issues" => $newnote,
      "date_updated" => time()
      ], [
        "AND" => [
          "iduser" => $iduser,
          "idcourse" => $idcourse,
          "idmaterial" => $idmaterial,
          "status" => "active"
        ]
      ]);
    return "1";
  }

  public function addBriefHolding($iduser, $idcourse, $idmaterial, $item){
    $database = $this->dbAppUser();
    $this->checkForNotes($iduser, $idcourse, $idmaterial);
    $getnote = $database->get('note','holding',[
      'AND' => [
        "iduser" => $iduser,
        "idcourse" => $idcourse,
        "idmaterial" => $idmaterial,
        "status" => "active"
      ]
    ]);
    $newnote = $getnote . "• " . $item . "<br/>";
    $updatenote = $database->update("notes", [
      "holding" => $newnote,
      "date_updated" => time()
      ], [
        "AND" => [
          "iduser" => $iduser,
          "idcourse" => $idcourse,
          "idmaterial" => $idmaterial,
          "status" => "active"
        ]
      ]);
    return "1";
  }

  public function addBriefRule($iduser, $idcourse, $idmaterial, $item){
    $database = $this->dbAppUser();
    $this->checkForNotes($iduser, $idcourse, $idmaterial);
    $getnote = $database->get('note','rules',[
      'AND' => [
        "iduser" => $iduser,
        "idcourse" => $idcourse,
        "idmaterial" => $idmaterial,
        "status" => "active"
      ]
    ]);
    $newnote = $getnote . "• " . $item . "<br/>";
    $updatenote = $database->update("notes", [
      "rules" => $newnote,
      "date_updated" => time()
      ], [
        "AND" => [
          "iduser" => $iduser,
          "idcourse" => $idcourse,
          "idmaterial" => $idmaterial,
          "status" => "active"
        ]
      ]);
    return "1";
  }

  public function addBriefAnalysis($iduser, $idcourse, $idmaterial, $item){
    $database = $this->dbAppUser();
    $this->checkForNotes($iduser, $idcourse, $idmaterial);
    $getnote = $database->get('note','analysis',[
      'AND' => [
        "iduser" => $iduser,
        "idcourse" => $idcourse,
        "idmaterial" => $idmaterial,
        "status" => "active"
      ]
    ]);
    $newnote = $getnote . "• " . $item . "<br/>";
    $updatenote = $database->update("notes", [
      "analysis" => $newnote,
      "date_updated" => time()
      ], [
        "AND" => [
          "iduser" => $iduser,
          "idcourse" => $idcourse,
          "idmaterial" => $idmaterial,
          "status" => "active"
        ]
      ]);
    return "1";
  }

  public function addBriefNote($iduser, $idcourse, $idmaterial, $item){
    $database = $this->dbAppUser();
    $this->checkForNotes($iduser, $idcourse, $idmaterial);
    $getnote = $database->get('note','notes',[
      'AND' => [
        "iduser" => $iduser,
        "idcourse" => $idcourse,
        "idmaterial" => $idmaterial,
        "status" => "active"
      ]
    ]);
    $newnote = $getnote . "• " . $item . "<br/>";
    $updatenote = $database->update("notes", [
      "notes" => $newnote,
      "date_updated" => time()
      ], [
        "AND" => [
          "iduser" => $iduser,
          "idcourse" => $idcourse,
          "idmaterial" => $idmaterial,
          "status" => "active"
        ]
      ]);
    return "1";
  }

  public function downloadOutlineDoc($iduser, $idcourse, $args){
    $key = "B5g4Thaf";
    $input = $iduser . $idcourse . time() . $key;
    $hash= md5(uniqid($input, true));
    $loc = __FILEPATH__ . "public_html/tmp/" . $hash;
    $url = "/tmp/" . $hash;
    //make the directory for the file
    mkdir($loc);

    //get the briefs (returns array)
    $briefs = $this->getOutline($iduser, $idcourse);
    //build the document (returns stringify)
    $document = $this->makeOutlineDoc($briefs, $args['fa'], $args['is'], $args['ho'], $args['ru'], $args['an'], $args['no']);

    //name everything
    $docname = "outline.doc";
    $docloc = $loc . "/". $docname;
    $docurl = $url . "/" . $docname;

    //create the file at the location, and add the document to it
    file_put_contents($docloc, $document);

    return $docurl;
  }

  public function downloadOutlineCsv($iduser, $idcourse, $args){
    $key = "B5g4Thaf";
    $input = $iduser . $idcourse . time() . $key;
    $hash= md5(uniqid($input, true));
    $loc = __FILEPATH__ . "public_html/tmp/" . $hash;
    $url = "/tmp" . "/" . $hash;
    //make the directory for the file
    mkdir($loc);

    $excel = new \SimpleExcel\SimpleExcel('csv');


    //get the briefs (returns array)
    $briefs = $this->getOutline($iduser, $idcourse);

    $fa =$args['fa'];
    $ih = $args['is'];
    $ho = $args['ho'];
    $ru = $args['ru'];
    $an = $args['an'];
    $no = $args['no'];

    $documentar = array();

    $headerrow = array('Name');
    if ($fa == "true") {array_push($headerrow, "Facts");}
    if ($ih == "true") {array_push($headerrow, "Issues");}
    if ($ho == "true") {array_push($headerrow, "Holding");}
    if ($ru == "true") {array_push($headerrow, "Rules");}
    if ($an == "true") {array_push($headerrow, "Analysis");}
    if ($no == "true") {array_push($headerrow, "Notes");}

    //add the header row
    $documentar[0] = $headerrow;

    //make the brief array
    foreach ($briefs as $key => $value) {

    }
    $html = new simple_html_dom();
    $counter = 1;
    foreach($briefs as $brief) {
      $html->load($brief["alias"]);
      $alias = $html->plaintext;
      $bodyrow = array($alias);
        if ($fa == "true") {array_push($bodyrow, $brief["facts"]);}
        if ($ih == "true") {array_push($bodyrow, $brief["issues"]);}
        if ($ho == "true") {array_push($bodyrow, $brief["holding"]);}
        if ($ru == "true") {array_push($bodyrow, $brief["rules"]);}
        if ($an == "true") {array_push($bodyrow, $brief["analysis"]);}
        if ($no == "true") {array_push($bodyrow, $brief["notes"]);}
      $documentar[$counter] = $bodyrow;
      $counter ++;
    }

    //add it all to the excel file;

  $excel->writer->setData($documentar);

    //name everything
    $docname = "outline.csv";
    $docloc = $loc . "/". $docname;
    $docurl = $url . "/" . $docname;

    $excel->writer->saveFile($docloc, $docloc);

    return $docurl;
    //print_r($documentar);
  }




  protected function makeOutlineDoc($briefs, $fa, $ih, $ho, $ra, $an, $no){
    $start = '<html><head>
    	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    	<title>Study Legal Outline</title>
    	<meta name="generator" content="Study Legal LLC"/>
    	<style type="text/css">
    		@page { margin: 1in }
    		p { line-height: 120% }
    	</style>
    </head><body><ol>';
    $middle = "";
    foreach($briefs as $brief) {
      //First add the alias title
      $section = "<li class='section'><span style='font-weight:bold'>" . $brief["alias"] . "</span><ol>";
        //then add each type that was selected by the user
        if ($fa == "true") {$section = $section . "<li><span style='text-decoration:underline'>Facts: </span>" . $brief["facts"] . "</li>";}
        if ($ih == "true") {$section = $section . "<li><span style='text-decoration:underline'>Issue: </span>" . $brief["issues"] . "</li>";}
        if ($ho == "true") {$section = $section . "<li><span style='text-decoration:underline'>Holding: </span>" . $brief["holding"] . "</li>";}
        if ($ra == "true") {$section = $section . "<li><span style='text-decoration:underline'>Rules: </span>" . $brief["rules"] . "</li>";}
        if ($an == "true") {$section = $section . "<li><span style='text-decoration:underline'>Analysis: </span>" . $brief["analysis"] . "</li>";}
        if ($no == "true") {$section = $section . "<li><span style='text-decoration:underline'>Notes: </span>" . $brief["notes"] . "</li>";}
        $section = $section . "</ol></li><hr/>";
      $middle = $middle . $section;
    }
    $end = '</ol></body></html>';
    $doc = $start . $middle . $end;
    return $doc;
  }

  //New Material List API (created to simplify order functioning in js file)
  public function getUserMaterialList($iduser, $idcourse) {
    $database = $this->dbAppUser();
    $databasestatic = $this->dbAppStatic();
    $checkExists = $database->has('materials', [
      'AND' => [
        'iduser' => $iduser,
        'idcourse' => $idcourse,
      ]
    ]);
    $finalResult = array();
    if ($checkExists == true){
      $materialJsonObejct = $database->get('materials', 'materials', [
        'AND' => [
          'iduser' => $iduser,
          'idcourse' => $idcourse,
        ]
      ]);
      $list = json_decode($materialJsonObejct);
      foreach ($list as $key => $value) {
        $singleResult = array();
        $shortname = $databasestatic->get('materials', 'short_name', [
          'id' => $value,
        ]);
        $singleResult['id'] = $value;
        $singleResult['short_name'] = $shortname;
        array_push($finalResult, $singleResult);
      }
      return json_encode($finalResult);
    }
    else {
      return json_encode($finalResult);
    }
  }

  public function updateUserMaterialList($iduser, $idcourse, $materialList) {
    $database = $this->dbAppUser();
    //$databasestatic = $this->dbAppStatic();
    $checkExists = $database->has('materials', [
      'AND' => [
        'iduser' => $iduser,
        'idcourse' => $idcourse,
      ]
    ]);
    if ($checkExists == true){
      //upate it
      $updateEntry = $database->update('materials',[
        'materials' => $materialList,
      ] ,[
        'AND' => [
          'iduser' => $iduser,
          'idcourse' => $idcourse,
        ]
      ]);
      return "a";
    }
    else {
      //create it
      $createEntry = $database->insert('materials', [
        'iduser' => $iduser,
        'idcourse' => $idcourse,
        'materials' => $materialList,
        'date_added' => time(),
      ]);
      return "b";
    }
  }
}
