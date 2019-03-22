<?php
/*
Application Static Controller
*/
  $app->get('/app/static/courses', function($request, $response) {
    $user = new sl_Account;
    $static = new sl_Application_Static;
    if($user->checkLoggedIn()){
      return json_encode($static->getAllCourses());
    }
    else {
      return 'expired';
    }
  });
  $app->get('/app/static/codes', function($request, $response) {
    $user = new sl_Account;
    $static = new sl_Application_Static;
    if($user->checkLoggedIn()){
      return $this->view->render($response, 'json/us-codes-sectionsv3.json');
    }
    else {
      return 'expired';
    }
  });
  $app->get('/app/static/material/{idmaterial}', function($request, $response, $args) {
    $user = new sl_Account;
    $static = new sl_Application_Static;
    if($user->checkLoggedIn()){
      return json_encode($static->getMaterial($args['idmaterial']));
    }
    else {
      return 'expired';
    }
  });
  $app->get('/app/static/search/{string}', function($request, $response, $args) {
    $user = new sl_Account;
    $static = new sl_Application_Static;
    if($user->checkLoggedIn()){
      $result = $static->search($args['string']);
      if ($result == "1") {
        return $result;
      }
      elseif ($result == "2"){
        return $result;
      }
      else {
          return json_encode($result);
      }
    }
    else {
      return 'expired';
    }
  });
