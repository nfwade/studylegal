<?php
class sl_Application_Static extends sl_Database {

	//Returns JSON with all available courses (works)
	public function getAllCourses(){
    $database = $this->dbAppStatic();
    //create a blank array to return the courses in
    $returncourses = array();
    // find the active courses
		$returncourses = $database->select("courses", "*");
    //as long as there weren't any problems, return the courses in json format.
		if ($returncourses == null) {
				return "0";
			}
			else {
				return $returncourses;
			}
  }

	public function getEachCourse($idcourses){
		$database = $this->dbAppStatic();
		$returncourses = array();
		$x=0;
		//Grab each course from the usercourse array
		foreach($idcourses as $coursevalue) {
			$findcourse = $database->select("courses", "course_name", [
			"id" => $coursevalue
			]);
			$returncourses[$x] = array($coursevalue, $findcourse[0]);
			$x++;
		}
		if(!empty($returncourses)){
			return $returncourses;
		}
		else {
			return "0";
		}
	}

	//Returns a JSON file with all available books (works)
	public function getAllBooks(){
    $database = $this->dbAppStatic();
    //create a blank array to return the courses in
    $returnbooks = array();
    // find the active courses
		$returnbooks = $database->select("books", "*");
    //as long as there weren't any problems, return the courses in json format.
		if ($returnbooks == null) {
				return "0";
			}
			else {
				return $returnbooks;
			}
  }

	public function getActiveCodeBooks(){
		$database = $this->dbAppStatic();
    //create a blank array to return the courses in
    $returnbooks = array();
    // find the active courses
		$returnbooks = $database->select("books", "*", [
			"AND" => [
				"status" => "active",
				"type" => "code"
			]
		]);
    //as long as there weren't any problems, return the courses in json format.
		if ($returnbooks == null) {
				return "0";
			}
			else {
				return $returnbooks;
			}
  }

	public function getCodeBookSections($idbook){
		$database = $this->dbAppStatic();
		$allsections = $database->select("materials", [
			'section',
		], [
			'idbook' => $idbook
		]);
		$newsectionarray = array();
		foreach ($allsections as $key => $value) {
			array_push($newsectionarray, $value['section']);
		}
		$uniquesections = array_unique($newsectionarray);
		$arraywithsectionsandurls = array();
		foreach ($uniquesections as $key => $value) {
			$graburl = $database->select('materials', 'url_section', [
				'section' => $value
			]);
			$arraybothparts['section'] = $value;
			$arraybothparts['url_section'] = $graburl[0];

			array_push($arraywithsectionsandurls, $arraybothparts);
		}
		array_shift($arraywithsectionsandurls);
		return $arraywithsectionsandurls;
	}

	//Returns a JSON file with all available books
	public function getBook($idbook){
    $database = $this->dbAppStatic();
    // find the active courses
		$returnbook = $database->select("books", "*", [
			"id" => $idbook,
		]);
    //as long as there weren't any problems, return the courses in json format.
		if ($returnbook == null) {
				return "0";
			}
			else {
				return json_encode($returnbook);
			}
  }

	public function getBookByUrl($url){
		$database = $this->dbAppStatic();
		// find the active courses
		$returnbook = $database->select("books", "*", [
			"url_name" => $url,
		]);
		return $returnbook;
	}

	public function getSectionByUrl($url){
		$database = $this->dbAppStatic();
		// find the active courses
		$returnsection = $database->select("materials", [
			"full_name",
			"short_name",
			"idbook",
			"url_section",
			"section"
		], [
			"url_section" => $url,
		]);
		return $returnsection[0];
	}

	//Returns a full individual material in JSON (works)
	public function getMaterial($idmaterial){
		$database = $this->dbAppStatic();
		$appuserdb = $this->dbAppUser();
		$type = substr($idmaterial, 0, 2);
		if ($type == 'cm'){
			$materialAr = $appuserdb->select("user_custom_materials", "*" , [
					"idcustom" => $idmaterial
			]);
		}
		else {
			$materialAr = $database->select("materials", "*" , [
					"id" => $idmaterial
			]);
		}
		if ($materialAr == "false"){
			return "0";
		}
		else {
			return $materialAr;
		}
	}

	public function getEachMaterial($idmaterials){
		$database = $this->dbAppStatic();
		$appuserdb = $this->dbAppUser();
		$returnmaterials = array();
		$x=0;
		//Grab each
		if(!is_array($idmaterials)){
			return '0';
		}
		foreach($idmaterials as $materialvalue) {
			$type = substr($materialvalue, 0, 2);
			if ($type == "cm"){
				$findmaterial = $appuserdb->select('user_custom_materials', [
					'short_name',
					'full_name'
				], [
				"idcustom" => $materialvalue
				]);
			}
			else {
				$findmaterial = $database->select("materials", [
					'short_name',
					'full_name'
				], [
				"id" => $materialvalue
				]);
			}
			$helparr = array(
				'id' => $materialvalue,
				'short_name' => $findmaterial[0]['short_name'],
				'full_name' => $findmaterial[0]['full_name']
			);
			array_push($returnmaterials, $helparr);
		}
		if(!empty($returnmaterials)){
			return $returnmaterials;
		}
		else {
			return "0";
		}
	}

	public function search($keyword){
			$database = $this->dbAppStatic();
			$stmt = $database->pdo->prepare("SELECT id, short_name, full_name FROM `materials` WHERE type = 'case' AND full_name LIKE ? ORDER BY full_name ASC");
			$keyword = '%' . $keyword . '%';
			$stmt->bindParam(1, $keyword, PDO::PARAM_STR, 100);
		  $isQueryOk = $stmt->execute();
			$results = array();
		  if ($isQueryOk) {
		  	$results = $stmt->fetchAll(PDO::FETCH_CLASS);
		  } else {
		      trigger_error('Error executing statement.', E_USER_ERROR);
		  }
		  $database = null;
			if (!empty($results)){
				if(count($results) > 10){
					return "2";  //2
				}
				else {
					return $results;
				}
			} else {
				return "1";  //1
			}
	}

	public function getMaterialByUrl($materialurl){
		$database = $this->dbAppStatic();
		$material = $database->select("materials", '*', [
				"url_name" => $materialurl
		]);
		return $material[0];
	}

	public function getMaterialsBySectionUrl($sectionurl){
		$database = $this->dbAppStatic();
		$materials = $database->select("materials", [
			"full_name",
			"url_name"
			] , [
				"url_section" => $sectionurl
		]);
		return $materials;
	}

	private function convertToAscii($str, $replace=array(), $delimiter='-') {
		setlocale(LC_ALL, 'en_US.UTF8');

			if( !empty($replace) ) {
				$str = str_replace((array)$replace, ' ', $str);
			}

			$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
			$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
			$clean = strtolower(trim($clean, '-'));
			$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
		  $clean = str_replace("nbsp", "", $clean);
			return $clean;
	}

}
