<?php
/*
Pages
*/
    $app->get('/', function($request, $response) {
      $user = new sl_Account;
      if($user->checkLoggedIn() == false){
        $user->checkRememberMe();
      }
      return $this->view->render($response, 'pages/homepage.html', [
          'page' => [
            'name' => 'home',
            'title' => 'Study Legal | Software for Law Students',
            'description' => 'Software for law students. Read cases, make briefs, assemble outlines.  All the cases from textbooks.',
          ],
          'user' => [
            'loggedin' => $user->checkLoggedIn(),
            'email' => $user->getUserEmail(),
            'pro' => $user->isPro($user->getUserId()),
          ]
        ]);
    });

    $app->get('/features', function($request, $response) {
      $user = new sl_Account;
      return $this->view->render($response, 'pages/features.html', [
            'page' => [
              'name' => 'Features',
              'title' => 'Features | Study Legal',
              'description' => 'Features and pricing for Study Legal.  Basic service users can read cases and codes for free.  Pro service users can brief and outline while they read.',
              'type' => 'primary'
            ],
            'user' => [
              'loggedin' => $user->checkLoggedIn(),
              'email' => $user->getUserEmail(),
              'pro' => $user->isPro($user->getUserId()),
            ]
        ]);
    });

    $app->get('/about', function($request, $response) {
      $user = new sl_Account;
      return $this->view->render($response, 'pages/about.html', [
            'page' => [
              'name' => 'About',
              'title' => 'About | Study Legal',
              'description' => 'About Study Legal.  Study Legal is a software service for law students.',
              'type' => 'secondary'
            ],
            'user' => [
              'loggedin' => $user->checkLoggedIn(),
              'email' => $user->getUserEmail(),
              'pro' => $user->isPro($user->getUserId()),
            ]
        ]);
    });

    $app->get('/learn', function($request, $response) {
      $user = new sl_Account;
      return $this->view->render($response, 'pages/learn.html', [
            'page' => [
              'name' => 'Learn',
              'title' => 'Learn | Study Legal',
              'description' => 'Learn how to use the Study Legal service to read, brief, and outline.',
              'type'=> 'secondary'
            ],
            'user' => [
              'loggedin' => $user->checkLoggedIn(),
              'email' => $user->getUserEmail(),
              'pro' => $user->isPro($user->getUserId()),
            ]
        ]);
    });

    $app->get('/terms', function($request, $response) {
      $user = new sl_Account;
      return $this->view->render($response, 'pages/terms.html', [
            'page' => [
              'name' => 'Terms',
              'title' => 'Terms & Conditions | Study Legal',
              'description' => 'The terms and conditions of use for the Study Legal website and service.',
              'type' => 'secondary'
            ],
            'user' => [
              'loggedin' => $user->checkLoggedIn(),
              'email' => $user->getUserEmail(),
              'pro' => $user->isPro($user->getUserId()),
            ]
        ]);
    });

    $app->get('/privacy', function($request, $response) {
      $user = new sl_Account;
      return $this->view->render($response, 'pages/privacy.html', [
            'page' => [
              'name' => 'Privacy',
              'title' => 'Privacy Policy | Study Legal',
              'description' => 'What data we collect and what we do with it.  The privacy policy of the Study Legal website and service.',
              'type' => 'secondary'
            ],
            'user' => [
              'loggedin' => $user->checkLoggedIn(),
              'email' => $user->getUserEmail(),
              'pro' => $user->isPro($user->getUserId()),
            ]
        ]);
    });

    $app->get('/updates', function($request, $response) {
      $user = new sl_Account;
      return $this->view->render($response, 'pages/updates.html', [
            'page' => [
              'name' => 'Updates',
              'title' => 'Updates | Study Legal',
              'description' => 'We periodically post updates about Study Legal.',
              'type' => 'secondary'
            ],
            'user' => [
              'loggedin' => $user->checkLoggedIn(),
              'email' => $user->getUserEmail(),
              'pro' => $user->isPro($user->getUserId()),
            ]
        ]);
    });

    $app->get('/sitemap.xml', function($request, $response) {
      $user = new sl_Account;
      return $this->view->render($response, 'pages/sitemap.xml');
    });

    $app->post('/contact/general', function($request, $response) {
      $data = $request->getParsedBody();
      $user = new sl_Account;
      $log = new sl_Log;
      $ipaddress = $request->getAttribute('ip_address');
      $log->contactGeneral($data['email'], $data['message'], $ipaddress);
      return $this->view->render($response, 'alerts/contact-successful.html', [
        'page' => [
          'name' => 'Contact',
          'title' => 'Contact Successful | Study Legal',
          'description' => 'Contact Successful.',
          'type' => 'alert',
        ],
        'user' => [
          'loggedin' => $user->checkLoggedIn(),
          'email' => $user->getUserEmail(),
          'pro' => $user->isPro($user->getUserId()),
        ],
      ]);
    });

    $app->get('/pro', function($request, $response) {
      $user = new sl_Account;
      if($user->checkLoggedIn() === true){
        return $this->view->render($response, 'pages/purchase.html', [
              'page' => [
                'name' => 'Pro',
                'title' => 'Get Pro | Study Legal',
                'description' => 'Buy Pro Service for one year.',
                'type' => 'secondary'
              ],
              'user' => [
                'loggedin' => $user->checkLoggedIn(),
                'email' => $user->getUserEmail(),
                'pro' => $user->isPro($user->getUserId()),
                'expiressoon' => $user->willExpireSoon(),
                'expiration' => $user->getExpirationFormated(),
              ],
          ]);
        }
        else {
          return $this->view->render($response, 'alerts/you-must-register.html', [
            'page' => [
              'title' => 'Register | Study Legal',
              'description' => 'You must register for Study Legal first.',
              'type' => 'alert'
            ]
          ]);
        }
    });

    $app->post('/checkout', function($request, $response)  {
      $data = $request->getParsedBody();
      $user = new sl_Account;
      $log = new sl_Log;
      $commerce = new sl_Commerce;
      if($user->checkLoggedIn() === true){
        //charge the card
        $commerce->chargeIt($data, $user->getUserEmail());
        //get data for db entry
        $name = $data['billing']['first-name'] . ", " . $data['billing']['last-name'];
        $address = $data['billing']['address'] . ", " . $data['billing']['address-2'] . ", " . $data['billing']['city'] . ", " . $data['billing']['state'] . ", " . $data['billing']['zip'];
        //record the payment
        $commerce->recordStripePayment($user->getUserId(), $data['stripeToken'], $name, $address);
        $commerce->emailReceipt($user->getUserEmail());  //comment this out if stripe starts sending the receipts.
        return $this->view->render($response, 'alerts/checkout-success.html', [
              'page' => [
                'name' => 'Payment Success',
                'title' => 'Payment Success | Study Legal',
                'description' => 'You successfully purchased Pro service.',
                'type' => 'alert',
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
