<?php
/*
Account & Account Controller
*/
    //logged in useres can change account settings
    $app->get('/account', function($request, $response) {
      $user = new sl_Account;
      if($user->checkLoggedIn() === true){
        return $this->view->render($response, 'pages/account.html', [
              'page' => [
                'name' => 'Account',
                'title' => 'Account Settings | Study Legal.',
                'description' => 'On this page you can review your accounnt, change your password, buy Pro Service, or cancel your account.',
                'type' => 'alert',
              ],
              'user' => [
                'loggedin' => $user->checkLoggedIn(),
                'email' => $user->getUserEmail(),
                'pro' => $user->isPro(),
                'expiration' => $user->getExpirationFormated()
              ]
          ]);
      }
      else {
        return $response->withStatus(302)->withHeader('Location', '/');
      }
    });

    //login page
    $app->get('/login', function($request, $response) {
      $user = new sl_Account;
      if($user->checkLoggedIn() === false){
        return $this->view->render($response, 'pages/login.html', [
          'page' => [
            'title' => 'Login | Study Legal',
            'description' => 'Students can login to read, brief, and outline cases and statutes.',
            'type' => 'alert',
          ],
          ]);
      }
      else {
        return $response->withStatus(302)->withHeader('Location', '/read');
      }
    });

    //login form
    $app->post('/user/login', function($request, $response)  {
      $data = $request->getParsedBody();
      $user = new sl_Account;
      $log = new sl_Log;
      $remember = false;
      //check if remember is set
      if (isset($_POST['rm'])) {$remember = true;}
      //first check if the account isn't frozen
      if ($log->checkLoginCount($data['logemail']) < 4){
        //if the email isn't frozen, try the username and password
        if ($user->attemptLogin($data['logemail'], $data['logp'], $remember) === true){
          $log->userLoginSuccess($user->getUserId(), $remember, $request->getAttribute('ip_address'));
          //check if its the users first time logging in, ask the to choose an account type.
          if ($user->isFirstLogin()){
              return $this->view->render($response, 'alerts/first-login.html', [
                'user' => [
                  'loggedin' => $user->checkLoggedIn(),
                  'email' => $user->getUserEmail(),
                  'pro' => $user->isPro(),
                  'expiration' => $user->getExpirationFormated()
                ],
                'page' => [
                  'type' => 'alert',
                ]
              ]);
          }
          elseif ($user->isPro() && $user->willExpireSoon()){
            return $this->view->render($response, 'alerts/expire-soon.html', [
              'expiration' => $user->getExpirationFormated(),
            ]);
          }
          else {
            return $this->view->render($response, 'alerts/login-success.html', [
              'user' => [
                'loggedin' => $user->checkLoggedIn(),
                'email' => $user->getUserEmail(),
                'pro' => $user->isPro(),
                'expiration' => $user->getExpirationFormated()
              ],
              'page' => [
                'type' => 'alert',
              ]
            ]);
          }
        }
        else {
          $log->userLoginFailure($data['logemail'], $remember, $request->getAttribute('ip_address'));
          return $this->view->render($response, 'alerts/login-failure.html', [
            'page' => [
              'type' => 'alert',
            ]
          ]);
        }
      }
      else {
        return $this->view->render($response, 'alerts/login-locked.html', [
          'page' => [
            'type' => 'alert',
          ]
        ]);
      }
    });

    //logout
    $app->get('/user/logout', function($request, $response)  {
      $user = new sl_Account;
      $user->logOut();
      return $response->withStatus(302)->withHeader('Location', '/');
    });

    //register a user and send a verification email
    $app->post('/user/register', function($request, $response)  {
      $data = $request->getParsedBody();
      $user = new sl_Account;
      $log = new sl_Log;
      $commerce = new sl_Commerce;
      //verify the email
      if ($user->checkEmailExists($data['regemail']) === false){
        //if the email is verified, add the new user
        $user->addNewStudent($data['regemail'], $data['regp']);
        //check if we gave them a credit already
        if (!$commerce->checkCreditExists($user->getIdFromEmail($data['regemail']))){
          $commerce->creditUntilOctober($user->getIdFromEmail($data['regemail']));
        }
        $user->sendRegistationConfirmation($data['regemail']);
        return $this->view->render($response, 'alerts/registration-success.html',  [
          'page' => [
            'type' => 'alert',
            'title' => 'Registration Success | Study Legal'
          ],
          'data' => [
            'email' => $data['regemail'],
          ]
        ]);
      }
      else {
        $log->userRegistrationFailure($data['regemail'], $request->getAttribute('ip_address'));
        $user->sendRegistationConfirmation($data['regemail']);
        return $this->view->render($response, 'alerts/registration-failure.html', [
          'page' => [
            'type' => 'alert',
            'title' => 'Registration Failed | Study Legal'
          ]
        ]);
      }
    });

    //process an account verfication
    $app->get('/user/confirm/registration[/{reghash}[/{userhash}]]', function($request, $response, $args)  {
      $user = new sl_Account;
      if($user->confirmRegLink($args['reghash'], $args['userhash']) === true){
        return $this->view->render($response, 'alerts/confirm-registration-success.html', [
          'page' => [
            'type' => 'alert',
          ],
          'data' => [
            'email' => $user->getEmailByHash($args['reghash'], $args['userhash']),
          ]
        ]);
      }
      else {
        return $this->view->render($response, 'alerts/confirm-registration-failure.html');
      }
    });

    //send a reset link
    $app->post('/user/reset', function($request, $response)  {
      $data = $request->getParsedBody();
      $user = new sl_Account;
      $log = new sl_Log;
      //log the reset attempt
      $real = $user->checkEmailExists($data['email']);
      $ipaddress = $request->getAttribute('ip_address');
      $log->userResetAttempt($data['email'], $real, $ipaddress);
      if($log->checkResetCount($data['email'], $ipaddress)){
        if($real === true){
          $user->resetPassword($data['email']);
          return $this->view->render($response, 'alerts/reset-sent-success.html', [
            'page' => [
              'type' => 'alert',
            ]
          ]);
        }
        else {
          return $response->withStatus(302)->withHeader('Location', '/');
        }
      }
      else {
        return $this->view->render($response, 'alerts/reset-failure.html', [
          'page' => [
            'type' => 'alert',
          ]
        ]);
      }
    });

    //process a reset link
    $app->get('/user/reset/link[/{reghash}[/{userhash}]]', function($request, $response, $args)  {
      $user = new sl_Account;
      $reghash = $args['reghash'];
      $userhash = $args['userhash'];
      //check if the reset link is less than a month old
      if($user->resetLinkExpired($userhash, $reghash) == false){
        return $this->view->render($response, 'alerts/reset-password.html', [
          'userhash' => $userhash,
          'reghash' => $reghash,
          'page' => [
              'type' => 'alert',
            ],
        ]);
      }
      else {
        return $this->view->render($response, 'alerts/reset-expired.html', [
          'page' => [
            'type' => 'alert',
          ]
        ]);
      }
    });

    //form to reset password
    $app->post('/user/reset/password', function($request, $response)  {
      $data = $request->getParsedBody();
      $user = new sl_Account;
      if($data['pa'] == $data['confirmpa'] && $user->resetLinkExpired($data['uh'], $data['rh']) == false){
        $user->updatePassword($data['pa'], $data['rh'], $data['uh']);
        return $this->view->render($response, 'alerts/password-reset-successful.html', [
          'page' => [
            'type' => 'alert',
          ]
        ]);
      } else {
        return $response->withStatus(302)->withHeader('Location', '/');
      }
    });

    //Cancel account
    $app->post('/user/cancel/account', function($request, $response, $args)  {
      $user = new sl_Account;
      $log = new sl_Log;
      $data = $request->getParsedBody();
      if($user->checkLoggedIn() === true || $user->getUserEmail() === $data['em']){
        //cancel the account and send the Email
        $user->cancelAccount($user->getUserId(), $user->getUserEmail(), $data['p']);
        //log the cancelation
        $log->userCancelation($user->getUserId(), $user->getUserEmail(), $data['p'], $request->getAttribute('ip_address'));
        //log the user out
        $user->logOut();
        return $this->view->render($response, 'alerts/account-cancel-successful.html', [
          'page' => [
            'type' => 'alert',
          ]
        ]);
      }
      else {
        return $response->withStatus(302)->withHeader('Location', '/');
      }
    });

    //login page
    $app->get('/user/get/info', function($request, $response) {
      $user = new sl_Account;
      $log = new sl_Log;
      if($user->checkLoggedIn() === true){
        $info = array(
          //'id' => $user->getUserId(),
          'first_login' => $user->isFirstLogin()
      );
      return json_encode($info);
      }
      else {
        return $response->withStatus(302)->withHeader('Location', '/read');
      }
    });
