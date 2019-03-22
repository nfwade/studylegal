<?php
/*
Application User Controller
*/
  //Get stuff
  //check if the user is still logged in.
  $app->get('/app/user/loggedin', function($request, $response) {
    $user = new sl_Account;
    if($user->checkLoggedIn()){
      return "1";
    }
    else {
      return "0";
    }
  });
  $app->get('/app/user/courses', function($request, $response) {
    $user = new sl_Account;
    $static = new sl_Application_Static;
    $appuser = new sl_Application_User;
    if($user->checkLoggedIn()){
      $usercourses = $appuser->getUserCourses($user->getUserId());
      return json_encode($static->getEachCourse($usercourses));
    }
    else {
      return 'expired';
    }
  });
  $app->get('/app/user/materials/{idcourse}', function($request, $response, $args) {
    $user = new sl_Account;
    $static = new sl_Application_Static;
    $appuser = new sl_Application_User;
    if($user->checkLoggedIn()){
      $usermaterials = $appuser->getUserMaterials($user->getUserId(), $args['idcourse']);
      $answer = $static->getEachMaterial($usermaterials);
      if ($answer == "0"){
        return $answer;
      }
      else {
        return json_encode($answer);
      }
    }
    else {
      return 'expired';
    }
  });
  $app->get('/app/user/materialsorder/{idcourse}', function($request, $response, $args) {
    $user = new sl_Account;
    $appuser = new sl_Application_User;
    if($user->checkLoggedIn()){
      return $appuser->getOrder($user->getUserId(), $args['idcourse']);
    }
    else {
      return 'expired';
    }
  });
  $app->get('/app/user/notes[/{idcourse}[/{idmaterial}]]', function($request, $response, $args) {
    $user = new sl_Account;
    $appuser = new sl_Application_User;
    if($user->checkLoggedIn()){
      return json_encode($appuser->getBrief($user->getUserId(), $args['idcourse'], $args['idmaterial']));
    }
    else {
      return 'expired';
    }
  });
  $app->get('/app/user/outline/{idcourse}', function($request, $response, $args) {
    $user = new sl_Account;
    $appuser = new sl_Application_User;
    if($user->checkLoggedIn()){
      return json_encode($appuser->getOutline($user->getUserId(), $args['idcourse']));
    }
    else {
      return 'expired';
    }
  });
  //return the link to the downloadable document
  $app->post('/app/user/outline/download/doc', function($request, $response, $args) {
    $user = new sl_Account;
    $appuser = new sl_Application_User;
    $data = $request->getParsedBody();
    if($user->checkLoggedIn()){
      return $appuser->downloadOutlineDoc($user->getUserId(), $data['crid'], $data);
    }
  });
  //return the link to the downloadable csv file
  $app->post('/app/user/outline/download/csv', function($request, $response, $args) {
    $user = new sl_Account;
    $appuser = new sl_Application_User;
    $data = $request->getParsedBody();
    if($user->checkLoggedIn()){
      return $appuser->downloadOutlineCsv($user->getUserId(), $data['crid'], $data);
    }
  });
  //Add Stuff
  $app->get('/app/user/add/course/{idcourse}', function($request, $response, $args) {
    $user = new sl_Account;
    $appuser = new sl_Application_User;
    if($user->checkLoggedIn()){
      return $appuser->addCourse($user->getUserId(), $args['idcourse']);
    }
    else {
      return 'expired';
    }
  });
  $app->get('/app/user/add/material[/{idcourse}[/{idmaterial}]]', function($request, $response, $args) {
    $user = new sl_Account;
    $appuser = new sl_Application_User;
    if($user->checkLoggedIn()){
      return $appuser->addMaterial($user->getUserId(), $args['idcourse'], $args['idmaterial']);
    }
    else {
      return 'expired';
    }
  });

  //**New API for simplifying material order
  $app->get('/app/user/materials/get/{idcourse}', function($request, $response, $args) {
    $user = new sl_Account;
    $appuser = new sl_Application_User;
    if($user->checkLoggedIn()){
      return $appuser->getUserMaterialList($user->getUserId(), $args['idcourse']);
    }
    else {
      return 'expired';
    }
  });
  $app->post('/app/user/materials/update', function($request, $response)  {
    $data = $request->getParsedBody();
    $user = new sl_Account;
    $appuser = new sl_Application_User;
    if($user->checkLoggedIn()){
      return $appuser->updateUserMaterialList($user->getUserId(), $data['idcourse'], $data['materialList']);
    }
    else {
      return 'expired';
    }
  });

  $app->post('/app/user/add/custom/material', function($request, $response)  {
    $data = $request->getParsedBody();
    $user = new sl_Account;
    $appuser = new sl_Application_User;
    if($user->checkLoggedIn()){
      return $appuser->addCustomMaterial($user->getUserId(), $data['title'], $data['type'], $data['content']);
    }
    else {
      return 'expired';
    }
  });

  //Remove Stuff
  $app->get('/app/user/remove/course/{idcourse}', function($request, $response, $args) {
    $user = new sl_Account;
    $appuser = new sl_Application_User;
    if($user->checkLoggedIn()){
      return $appuser->removeCourse($user->getUserId(), $args['idcourse']);
    }
    else {
      return 'expired';
    }
  });
  $app->get('/app/user/remove/material[/{idcourse}[/{idmaterial}]]', function($request, $response, $args) {
    $user = new sl_Account;
    $appuser = new sl_Application_User;
    if($user->checkLoggedIn()){
      return $appuser->removeMaterial($user->getUserId(), $args['idcourse'], $args['idmaterial']);
    }
    else {
      return 'expired';
    }
  });
  //Update stuff
  $app->post('/app/user/update/brief', function($request, $response)  {
    $data = $request->getParsedBody();
    $user = new sl_Account;
    $appuser = new sl_Application_User;
    if($user->checkLoggedIn()){
      return $appuser->updateNotes($user->getUserId(), $data['crid'], $data['cnid'], $data['f'], $data['i'], $data['h'], $data['r'], $data['a'], $data['n']);
    }
    else {
      return 'expired';
    }
  });
  $app->post('/app/user/update/order', function($request, $response)  {
    $data = $request->getParsedBody();
    $user = new sl_Account;
    $appuser = new sl_Application_User;
    if($user->checkLoggedIn()){
      return $appuser->updateOrder($user->getUserId(), $data['crid'], $data['order']);
    }
    else {
      return 'expired';
    }
  });
  // Add brief parts
  $app->post('/app/user/add/brief/fact', function($request, $response)  {
    $data = $request->getParsedBody();
    $user = new sl_Account;
    $appuser = new sl_Application_User;
    if($user->checkLoggedIn()){
      return $appuser->addBriefFact($user->getUserId(), $data['crid'], $data['cnid'], $data['afact']);
    }
    else {
      return 'expired';
    }
  });
  $app->post('/app/user/add/brief/issue', function($request, $response)  {
    $data = $request->getParsedBody();
    $user = new sl_Account;
    $appuser = new sl_Application_User;
    if($user->checkLoggedIn()){
      return $appuser->addBriefIssue($user->getUserId(), $data['crid'], $data['cnid'], $data['aissue']);
    }
    else {
      return 'expired';
    }
  });
  $app->post('/app/user/add/brief/holding', function($request, $response)  {
    $data = $request->getParsedBody();
    $user = new sl_Account;
    $appuser = new sl_Application_User;
    if($user->checkLoggedIn()){
      return $appuser->addBriefHolding($user->getUserId(), $data['crid'], $data['cnid'], $data['aholding']);
    }
    else {
      return 'expired';
    }
  });
  $app->post('/app/user/add/brief/rule', function($request, $response)  {
    $data = $request->getParsedBody();
    $user = new sl_Account;
    $appuser = new sl_Application_User;
    if($user->checkLoggedIn()){
      return $appuser->addBriefRule($user->getUserId(), $data['crid'], $data['cnid'], $data['arules']);
    }
    else {
      return 'expired';
    }
  });
  $app->post('/app/user/add/brief/analysis', function($request, $response)  {
    $data = $request->getParsedBody();
    $user = new sl_Account;
    $appuser = new sl_Application_User;
    if($user->checkLoggedIn()){
      return $appuser->addBriefAnalysis($user->getUserId(), $data['crid'], $data['cnid'], $data['aanalysis']);
    }
    else {
      return 'expired';
    }
  });
  $app->post('/app/user/add/brief/note', function($request, $response)  {
    $data = $request->getParsedBody();
    $user = new sl_Account;
    $appuser = new sl_Application_User;
    if($user->checkLoggedIn()){
      return $appuser->addBriefNote($user->getUserId(), $data['crid'], $data['cnid'], $data['anotes']);
    }
    else {
      return 'expired';
    }
  });
