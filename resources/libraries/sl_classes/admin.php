<?php
class sl_Administration extends sl_Database {


/*
Metrics
*/


  public function getStudentCount(){
    $database = $this->dbAccount();
    $thecount = $database->count('users', [
      'type' => 'student'
    ]);
    return $thecount;
  }

  public function getActiveStudents(){
    $database = $this->dbLog();
    $now = time();
    $fivehrsago = $now - 18000; //60 * 60 * 5
    return $database->count('user_login_success', [
      'date[>]' => $fivehrsago
    ]);
  }

  public function getProCount(){
    $database = $this->dbCommerce();
    $now = time();
    $thecount = $database->count('payments', [
      'expiration[>]' => $now
    ]);
    return $thecount;
  }

  public function getBriefCount(){
    $database = $this->dbAppUser();
    return $database->count('notes', [
      'status' => 'active',
    ]);
  }

  public function getAllStudents(){
    $database = $this->dbAccount();
    $allstudents = $database->select('users', '*', [
      'type' => 'student',
    ]);

    foreach ($allstudents as $key => $value) {
      $email = $value['email'];
      $domain = substr(strrchr($email, "@"), 1);
      $allstudents[$key]['domain'] = $domain;
    }
    return $allstudents;
  }


/*
PARSING
*/


  protected function generateCaseShortname($name){
      //check if it's a case
      if (strpos($name, 'v.') !== false || strpos($name, 'In re') !== false ) {
      //separate the plaintiff and defendant
      $result = explode("v.", $name);
      $plaintiff = $result[0];
      $defendant = $result[1];
      //grab the uppercase words
      preg_match_all('/\b([A-Z]+)\b/', $plaintiff, $matches);
      $matches = implode(" ", $matches[0]);
      $plaintiff = $matches;
      preg_match_all('/\b([A-Z]+)\b/', $defendant, $matches2);
      $matches2 = implode(" ", $matches2[0]);
      $defendant = $matches2;
      //convert state names to initials
      $initials = array(
        'ALABAMA'=>'AL',
        'ALASKA'=>'AK',
        'ARIZONA'=>'AZ',
        'ARKANSAS'=>'AR',
        'CALIFORNIA'=>'CA',
        'COLORADO'=>'CO',
        'CONNECTICUT'=>'CT',
        'DELAWARE'=>'DE',
        'FLORIDA'=>'FL',
        'GEORGIA'=>'GA',
        'HAWAII'=>'HI',
        'IDAHO'=>'ID',
        'ILLINOIS'=>'IL',
        'INDIANA'=>'IN',
        'IOWA'=>'IA',
        'KANSAS'=>'KS',
        'KENTUCKY'=>'KY',
        'LOUISIANA'=>'LA',
        'MAINE'=>'ME',
        'MARYLAND'=>'MD',
        'MASSACHUSETTS'=>'MA',
        'MICHIGAN'=>'MI',
        'MINNESOTA'=>'MN',
        'MISSISSIPPI'=>'MS',
        'MISSOURI'=>'MO',
        'MONTANA'=>'MT',
        'NEBRASKA'=>'NE',
        'NEVADA'=>'NV',
        'NEW HAMPSHIRE'=>'NH',
        'NEW JERSEY'=>'NJ',
        'NEW MEXICO'=>'NM',
        'NEW YORK'=>'NY',
        'NORTH CAROLINA'=>'NC',
        'NORTH DAKOTA'=>'ND',
        'OHIO'=>'OH',
        'OKLAHOMA'=>'OK',
        'OREGON'=>'OR',
        'PENNSYLVANIA'=>'PA',
        'RHODE ISLAND'=>'RI',
        'SOUTH CAROLINA'=>'SC',
        'SOUTH DAKOTA'=>'SD',
        'TENNESSEE'=>'TN',
        'TEXAS'=>'TX',
        'UTAH'=>'UT',
        'VERMONT'=>'VT',
        'VIRGINIA'=>'VA',
        'WASHINGTON'=>'WA',
        'WEST VIRGINIA'=>'WV',
        'WISCONSIN'=>'WI',
        'WYOMING'=>'WY',
        //Agencys
        'ENVIRONMENTAL PROTECTION AGENCY' => 'EPA',
        //Codes
      );
      foreach ($initials as $word => $abbr) {
        if ($word == $plaintiff){
          $plaintiff = $abbr;
        }
        if ($word == $defendant){
          $defendant = $abbr;
        }
      }
      //wrap each in tags
      $plaintiff = "<span class='sn-p'>" . $plaintiff . "</span>";
      $connector = "<span class='sn-v'> v. </span>";
      $defendant = "<span class='sn-d'>" . $defendant . "</span>";
      $shortname = $plaintiff . $connector . $defendant;
      return $shortname;
      //combine them with <span class="sn-v">v.</span>
      }
    }

    public function parseFRCP(){
      $database = $this->dbAppStatic();
      $html = new simple_html_dom();
      $allfrcp = $database->select('materials', '*', [
        'idbook' => '7',
      ]);
      $retstring = '<body><table>';
      foreach ($allfrcp as $key => $value) {
        $retstring = $retstring . '<tr><td>' . $value['content_original'] . '</td>';
        //str_replace("old","new","string");
        $newvalue = str_replace('<p>&nbsp;</p>', '<br/>', $value['content_original']);
        $newvalue = str_replace('style="padding-left: 30px;"', 'class="sl_indent_1"', $newvalue);
        $newvalue = str_replace('style="padding-left: 60px;"', 'class="sl_indent_2"', $newvalue);
        $newvalue = str_replace('style="padding-left: 90px;"', 'class="sl_indent_3"', $newvalue);
        $newvalue = str_replace('style="padding-left: 90px;"', 'class="sl_indent_3"', $newvalue);
        $newvalue = str_replace('<strong>', '<span class="sl_material_heading">', $newvalue);
        $newvalue = str_replace('</strong>', '</span>', $newvalue);
        $newvalue = str_replace('<em>', '<span class="sl_emphasis">', $newvalue);
        $newvalue = str_replace('</em>', '</span>', $newvalue);
        $newvalue = str_replace('class="source-credit"', 'class="history_source"', $newvalue);
        $html->load($newvalue);
        $links = $html->find('a');
        foreach ($links as $key => $value2) {
          $value2->tag = "span";
          $value2->class = 'sl_material_link';
          $value2 ->href = null;
        }
        $addContentParsed = $database->update('materials',[
          'content_parsed' => $html->outertext
        ], [
          'id' => $value['id']
        ]);
        $retstring = $retstring . '<td>' . $html . '</td></tr>';
      }
      $retstring = $retstring . '</table></body>';
      return $retstring;
    }

    public function parseCaseOpinion(){
      $database = $this->dbAppStatic();
      $html = new simple_html_dom();
      $allcases = $database->select('materials', '*', [
        'OR' => [
          'type' => 'Case',
          'type' => 'case',
        ]
      ]);
      //THE RULES OF THE PARSED OPINION
      //remove GS P "Save trees - read court opinions online on Google Scholar."
      foreach ($allcases as $key => $value) {
        $addparsed = $database->update('materials',[
            'content_parsed' => $value['content_original']
          ], [
            'id' => $value['id']
          ]);
      }
      return 'all good in the hood';
    }


/*
MAINTENANCE FUNCTIONS
*/


    public function minifyCSS() {
      $cssfolder = __FILEPATH__ . 'public_html/assets/css/';
      $cssfiles = array(
        'style.css',
        //'reader.css',
        'semantic.min.css',
        'alertify.min.css',
        //'alertify.rtl.min.css',
        'alertify.semantic.css',
      );
      $minifier = new \MatthiasMullie\Minify\CSS();
      foreach ($cssfiles as $key => $value) {
        $loc = $cssfolder . $value;
        $minifier->add($loc);
      }
      // save minified file to disk
      $minifiedPath = __FILEPATH__ . 'public_html/assets/min/style.css';
      $minifier->minify($minifiedPath);
      // or just output the content
      //echo $minifier->minify();
      return "It worked, I guess.";
    }

    public function minifyJS() {
      $cssfolder = __FILEPATH__ . 'public_html/assets/js/';
      $cssfiles = array(
        'alertify.min.js',
        //'jquery-2.1.4.js',
        //'jquery-ui.js',
        'alertify.min.js',
        'semantic.js',
        'validate.js',
        'validate-additional-methods.js',
      );
      $minifier = new \MatthiasMullie\Minify\JS();
      foreach ($cssfiles as $key => $value) {
        $loc = $cssfolder . $value;
        $minifier->add($loc);
      }
      // save minified file to disk
      $minifiedPath = __FILEPATH__ . 'public_html/assets/min/script.js';
      $minifier->minify($minifiedPath);
      // or just output the content
      //echo $minifier->minify();
      return "It worked, I guess.";
    }

    public function getExportCount(){
      /*$dirs=0;
      $x= __FILEPATH__ . 'public_html/tmp';
      $y=   scandir($x);
      $dirs = count($y);
    return $dirs;*/
  }

    public function clearExportFolder(){
      $x = __FILEPATH__ . 'public_html/tmp';
      $y = scandir($x);
      $help = array_shift($y);
      $help = array_shift($y);
      $output = array();
      foreach ($y as $value) {
        $folder = $x . "/" . $value;
        if (filemtime($folder) < time() - 86400) { //60*60*24*15 = 1296000
          // Remove empty directories...
          if (is_dir($folder)) {
            //get all files
            $files = scandir($folder);
            $help = array_shift($files);
            $help = array_shift($files);
            //delete each file
            foreach ($files as $key => $value) {
              $file = $folder . "/" . $value;
              unlink($file);
            }
            //delet the folder
            rmdir($folder);
            //return deleted folders.
            array_push($output, $folder);
          }
        }

      }
      $output = implode($output, " , ");
      return "It worked. I guess. Deleted:<br/>" . $output;
    }


/*
SCRAPING, AND CITATIONS
*/


  public function parseCitations($text, $type){
    if($type == 'test'){
      $citations = explode(",",$text);
      return $citations;
    }
    elseif ($type == 'use') {
      $citations = explode("><",$text);
      array_pop($citations);
      return $citations;
    }
  }

  public function parseGoogleScholarLink($link) {
    $html = new simple_html_dom();
    $html->load_file($link);
    //write the result to a log file so we can monitor it.
  //  $file = __FILEPATH__ . 'resources/cache/log_scraping.txt';
    // Open the file to get existing content
  //  $current = file_get_contents($file);
    // Append a new person to the file
  //  $current .= $html->plaintext . "\n";
    // Write the contents back to the file
  //  file_put_contents($file, $current);
    $citation = $html->find('center', 0);
    $citation = $citation->plaintext;
    $date = explode('(', $citation);
    $citation = $date[0];
    $date = $date[1];
    $date = rtrim($date, ')');
    $date = rtrim($date, ')</b>');
    $fullname = $html->find('[id=gsl_case_name]');
    $fullname = $fullname[0]->plaintext;
    //$shortname = $this->generateCaseShortname($fullname);
    $opinion = $html->find('p');
    $opinionoriginal = '';
    foreach($opinion as $paragraph){
      $opinionoriginal = $opinionoriginal . '<p>' . $paragraph->plaintext . '</p>';
    }
    $opinionparsed = '';
    $return = array(
      'citation' => $citation,
      'fullname' => $fullname,
      'shortname'=> $fullname,
      'date' => $date,
      'opinionoriginal' => $opinionoriginal,
      'opinionparsed' => 'blank',
      'source' => $link
    );
    return $return;
  }

  public function searchGSByCitation($citation){
    $html = new simple_html_dom();
    $newcitation = preg_replace("/[\s_]/", "+", $citation);
    $url = 'https://scholar.google.com/scholar?hl=en&q="' . $newcitation . '"&as_sdt=2006';
    $returnarr = array(
      'result' => 'error',  //should be found, citationonly, notfound, blocked,
      'url' => '', //this is the link to the article page
      'data' => '', //this is empty unless it's a citation.
    );
    $html->load_file($url);
    //check to see if it was blocked
    if (!is_object($html)){
      $returnarr['result'] = 'blocked';
      return $returnarr;
    }
    $result = $html->find('[class=gs_r]');
    $found = $html->find('[class=gs_med]');
    $citeonly = $result[0]->find('h3[class=gs_rt]'); //this is the class they use if it's only a citation
    $link = $html->find('[class=gs_r] a');  //this is empty unless
    if(!empty($found) && empty($result)){
      if(strpos($found[0]->plaintext, 'did not match any articles') !== false  ){
        $returnarr['result'] = 'notfound';
      }
    }
    if(!empty($citeonly)){
      if(strpos($citeonly[0]->plaintext, '[CITATION]') !== false  ){
      $returnarr['result'] = 'citationonly';
      $getfullcite = $html->find('[class=gs_a]');
      $returnarr['data'] = $getfullcite[0]->plaintext;
      }
    }
    if(!empty($link)){
      $returnarr['result'] = 'found';
      $linkpart = "https://scholar.google.com";
      $returnarr['url'] = $linkpart .  $link[0]->href;
    }
    return $returnarr;
  }

  public function bulkGSScrape($citations, $idbook){
    echo "Book:" . $idbook . "<br/><table>";
    foreach ($citations as $key => $value) {
      echo "<tr>";
      echo "<td>Citation: " . $value . "</td>";
      $search = $this->searchGSByCitation($value);
      echo "<td>Result: <b>" . $search['result'] . "</b></td>";
      echo "<td>Url: " . $search['url'] . "</td>";
      if ($search['result'] == 'found') {
        $page = $this->parseGoogleScholarLink($search['url']);
        //echo "<td>Fullname: " . $page['fullname'] . "</td>";
        $this->addCaseToDb($idbook, $page);
      }
      //wait to do the next one.
      $numbers = range(30, 180);
      shuffle($numbers);
      sleep($numbers[0]);
    }
    echo "</table>";
  }

  public function addCaseToDb($idbook, $data){
    $database = $this->dbAppStatic();
    $now = time();
    $insertdata = $database->insert('materials', [
      'idbook' => $idbook,
      'type' => 'case',
      'short_name' => $data['shortname'],
      'full_name' => $data['fullname'],
      'date_published' => $data['date'],
      'citation' => $data['citation'],
      'content_original' => $data['opinionoriginal'],
      'source' => $data['source'],
      'date_updated' => $now,
    ]);
    return true;
  }

  /*USED TO EXTACT CITATIONS
  public function qTest($url){
    $html = new simple_html_dom();
    echo "<form action='/apollo/qtest' method='post'><input type='text' name='qurl'/><input type='submit' value='Submit'/></form><br/><br/>";
    echo $url;
    $html->load_file($url);
    $citations = $html->find('p[class=u-text-grey]');
    foreach ($citations as $value) {
      echo $value->plaintext . ", ";
    }
  }*/

  public function addCitations($citations, $idbook){
    echo "<h3>Book: " . $idbook . "</h3>";
    //TESTING echo $citations . "<br/><br/>";
    $citations = json_decode($citations);
    //TESTING print_r($citations);
    echo "<br/><br/>";
    echo "<table style='width:400px;'><tr><td>Citation</td><td>Result</td></tr>";
    foreach ($citations as $key => $value) {
      echo "<tr>";
      //remove blank space from front and end
      trim($value);
      echo "<td>" . $value . "</td>";
      $result = $this->addCitation($value, $idbook);
      if ($result === true){
        echo "<td>Success</td>";
      }
      else {
        echo "<td>Failure</td>";
      }
      echo "</tr>";
    }
    echo "</table>";
  }

  protected function addCitation($citation, $book){
    $database = $this->dbAppStatic();
    $check = $database->has('citations', [
      'AND' => [
        'idbook' => $book,
        'citation' => $citation,
      ]
    ]);
    if ($check === false){
      $addcitation = $database->insert('citations', [
        'idbook' => $book,
        'citation' => $citation,
        'status' => 'new',
      ]);
      return true;
    }
    else {
      return false;
    }
  }

  public function getCitationsByBook($idbook){
    $database = $this->dbAppStatic();
    $check = $database->select('citations', '*', [
      'AND' => [
        'idbook' => $idbook,
      ]
    ]);
    //return $check;
    echo json_encode($check);
  }

  protected function updateCitationById($idcitation, $newstatus, $url){
    $database = $this->dbAppStatic();
    $firstcheck = $database->has('citations', [
      'id' => $idcitation
    ]);
    if ($firstcheck == true){
      $updateit = $database->update('citations', [
        'status' => $newstatus,
        'url_found' => $url
      ], [
        'id' => $idcitation
      ]);
      return true;
    }
    else {
      return false;
    }
  }

  public function checkCitationGS($idcitation){
    $database = $this->dbAppStatic();
    $notnewarr = array(
      'result' => 'notNew',  //should be found, citationonly, notfound, blocked, notNew
      'url' => '', //this is the link to the article page
      'data' => '', //this is empty unless it's a citation.
    );
    //check that the citation hasn't been found already, that the status is 'new', and that the citation doesn't contain _ or ,
    $citation = $database->get('citations', '*', [
      'id' => $idcitation
    ]);
    if ($citation['status'] != 'new' || preg_match('/_/', $citation['citation']) || preg_match('/,/', $citation['citation'])){
      $this->updateCitationById($idcitation, 'error');
      return $notnewarr;
    }
    //search google scholar for the citation
    $checkResult = $this->searchGSByCitation($citation['citation']); //returns an array
  /*TESTing  $checkResult = array(
      'result' => 'found',  //should be found, citationonly, notfound, blocked, notNew
      'url' => 'http://wademediagroup.com', //this is the link to the article page
      'data' => '', //this is empty unless it's a citation.
    );*/
    $this->updateCitationById($idcitation, $checkResult['result'], $checkResult['url']);
    return $checkResult;
  }

  public function grabCaseGS($idcitation){
    //check that the status of the citation is 'new',
    $database = $this->dbAppStatic();
    $thecitation = $database->get('citations', '*',[
      'id' => $idcitation
    ]);
    //print_r ($thecitation);
    //check that the case isn't already in the database, if it is, just copy it.
    $checkexisting = $database->has('citations',[
      'AND' => [
          'citation' => $thecitation['citation'],
          'status' => 'grabbed',
      ]
    ]);
    if ($checkexisting == true){
      //copy it instead of going to the google link.
      return 'existing';
    }
    //go to the google link
    $data = $this->parseGoogleScholarLink($thecitation['url_found']);
    //add the case to the database
    $addit = $this->addCaseToDb($thecitation['idbook'], $data);
    //return success, existingCopied, blocked
    if ($addit == true){
      $this->updateCitationById($idcitation, 'grabbed', $thecitation['url_found']);
      return 'success';
    }
  }

  public function getCompletedBooks(){
    $database = $this->dbAppStatic();
    $allbooks = $database->select('books', '*', [
      'type' => 'Casebook'
    ]);
    $completedbooks = array();
    foreach ($allbooks as $key => $value) {
      $bookcitations = $database->select('citations', 'status', [
        'idbook' => $value['id']
      ]);
      if (!in_array('new', $bookcitations)  && !in_array('error', $bookcitations) && !empty($bookcitations)){
        //TESTING echo $value['id'] . ' | ' . $value['full_name'] .   "<br/><br/>";
        array_push($completedbooks, $value);
      }
    }
    return $completedbooks;
  }

  public function testingAndMaintenance(){

  }

}//end class
