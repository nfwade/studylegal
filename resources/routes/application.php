<?php
/*
Application
*/
  $app->get('/read', function($request, $response) {
    $user = new sl_Account;
    $log = new sl_Log;
    if($user->checkLoggedIn() === true){
      return $this->view->render($response, 'app/read.html', [
            'page' => [
              'name' => 'Read',
              'title' => 'Read Law School Materials | Study Legal',
              'description' => 'Read all of your law school materials here.',
            ],
            'user' => [
              'loggedin' => $user->checkLoggedIn(),
              'email' => $user->getUserEmail(),
              'pro' => $user->isPro($user->getUserId()),
            ]
        ]);
    }
    else {
      return $response->withStatus(302)->withHeader('Location', '/');
    }
  });

  $app->get('/outline', function($request, $response) {
    $user = new sl_Account;
    if($user->checkLoggedIn() === true){
      return $this->view->render($response, 'app/outline.html', [
            'page' => [
              'name' => 'Outline',
              'title' => 'Course Outlines | Study Legal',
              'description' => 'View outlines generated from all of your briefs.',
            ],
            'user' => [
              'loggedin' => $user->checkLoggedIn(),
              'email' => $user->getUserEmail(),
              'pro' => $user->isPro($user->getUserId()),
            ]
        ]);
    }
    else {
      return $response->withStatus(302)->withHeader('Location', '/');
    }
  });

  $app->get('/read-dev', function($request, $response) {
    $user = new sl_Account;
    $log = new sl_Log;
    if($user->checkLoggedIn() === true){
      return $this->view->render($response, 'app/read-dev.html', [
            'page' => [
              'name' => 'Read',
              'title' => 'Read Law School Materials | Study Legal',
              'description' => 'Read all of your law school materials here.',
            ],
            'user' => [
              'loggedin' => $user->checkLoggedIn(),
              'email' => $user->getUserEmail(),
              'pro' => $user->isPro($user->getUserId()),
            ]
        ]);
    }
    else {
      return $response->withStatus(302)->withHeader('Location', '/');
    }
  });
