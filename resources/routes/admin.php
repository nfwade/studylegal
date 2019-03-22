<?php
/*
Admin
*/
$app->get('/apollo', function($request, $response) {
  $user = new sl_Account;
  $admin = new sl_Administration;
  if($user->isAdminLoggedIn() === true){
    return $this->view->render($response, 'admin/admin.html', [
        'page' => [
          'name' => 'Admin',
          'title' => 'Administration | Study Legal',
          'description' => 'Control the settings of Study Legal and perform maintenance.',
          'type' => 'secondary'
        ],
        'admindata' => [
          'totalusers' => $admin->getStudentCount(),
        //  'activeusers' => $admin->getActiveStudents(),
          'procount' => $admin->getProCount(),
          'briefcount' => $admin->getBriefCount(),
          'exportcount' => $admin->getExportCount(),
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
/*
//quimbee test, submit url
$app->get('/apollo/qtest', function($request, $response, $args) {
  $user = new sl_Account;
  $admin = new sl_Administration;
  if($user->isAdminLoggedIn()){
    return "<form action='/apollo/qtest' method='post'><input type='text' name='qurl'/><input type='submit' value='Submit'/></form>";
  }
  else {
    return 'expired';
  }
});
//quimbee test grab citations
$app->post('/apollo/qtest', function($request, $response) {
  $data = $request->getParsedBody();
  $user = new sl_Account;
  $admin = new sl_Administration;
  if($user->isAdminLoggedIn()){
    return $admin->qTest($data['qurl']);
  }
  else {
    return 'expired';
  }
});*/

//UI to Add citations to citation database.
$app->get('/apollo/add/citations', function($request, $response) {
  $user = new sl_Account;
  $appstatic = new sl_Application_Static;
  $admin = new sl_Administration;
  if($user->isAdminLoggedIn() === true){
    return $this->view->render($response, 'admin/parse-citations.html', [
      'page' => [
        'name' => 'Add Citations',
        'title' => 'Administration | Study Legal',
        'description' => 'Control the settings of Study Legal and perform maintenance.',
        'type' => 'secondary'
      ],
      'admindata' => [
          'books' => $appstatic->getAllBooks(),
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
//UI to View Book Info
$app->get('/apollo/view/books', function($request, $response) {
  $user = new sl_Account;
  $appstatic = new sl_Application_Static;
  $admin = new sl_Administration;
  if($user->isAdminLoggedIn() === true){
    return $this->view->render($response, 'admin/view-books.html', [
      'page' => [
        'name' => 'ViewBooks',
        'title' => 'Administration | Study Legal',
        'description' => 'Control the settings of Study Legal and perform maintenance.',
        'type' => 'secondary'
      ],
      'admindata' => [
          'books' => $appstatic->getAllBooks(),
          'completedbooks' => $admin->getCompletedBooks(),
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

/*POST TO HERE*/
$app->post('/apollo/add/citations', function($request, $response) {
  $data = $request->getParsedBody();
  $user = new sl_Account;
  $admin = new sl_Administration;
  $appstatic = new sl_Application_Static;
  if($user->isAdminLoggedIn() === true){
    return $admin->addCitations($data['formated_citations'], $data['book']);
  }
  else {
      return $response->withStatus(302)->withHeader('Location', '/');
  }
});

//UI to View Citations
$app->get('/apollo/view/citations', function($request, $response) {
  $user = new sl_Account;
  $appstatic = new sl_Application_Static;
  $admin = new sl_Administration;
  if($user->isAdminLoggedIn() === true){
    return $this->view->render($response, 'admin/view-citations.html', [
      'page' => [
        'name' => 'View Citations',
        'title' => 'Administration | Study Legal',
        'description' => 'Control the settings of Study Legal and perform maintenance.',
        'type' => 'secondary'
      ],
      'admindata' => [
          'books' => $appstatic->getAllBooks(),
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
$app->get('/apollo/view/citations/{idbook}', function($request, $response, $args) {
  $user = new sl_Account;
  $admin = new sl_Administration;
  $appstatic = new sl_Application_Static;
  if($user->isAdminLoggedIn() === true){
    return $admin->getCitationsByBook($args['idbook']);
  }
  else {
      return $response->withStatus(302)->withHeader('Location', '/');
  }
});


//NEW Test Bulk UI
$app->get('/apollo/bulk/2', function($request, $response) {
  $user = new sl_Account;
  $appstatic = new sl_Application_Static;
  $admin = new sl_Administration;
  if($user->isAdminLoggedIn() === true){
    return $this->view->render($response, 'admin/bulk_2.html', [
      'page' => [
        'name' => 'Bulk 2',
        'title' => 'Administration | Study Legal',
        'description' => 'Control the settings of Study Legal and perform maintenance.',
        'type' => 'secondary'
      ],
      'admindata' => [
          'books' => $appstatic->getAllBooks(),
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


//UI to add a single case
$app->get('/apollo/addcase', function($request, $response) {
  $user = new sl_Account;
  $admin = new sl_Administration;
  if($user->isAdminLoggedIn() === true){
    return $this->view->render($response, 'admin/add-case.html', [
      'page' => [
        'name' => 'AddCases',
        'title' => 'Administration | Study Legal',
        'description' => 'Control the settings of Study Legal and perform maintenance.',
        'type' => 'secondary'
      ],
      'admindata' => [

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

//UI to add a list of cases, separated by commas.
$app->get('/apollo/add/bulk/cases', function($request, $response) {
  $user = new sl_Account;
  $admin = new sl_Administration;
  $appstatic = new sl_Application_Static;
  if($user->isAdminLoggedIn() === true){
    return $this->view->render($response, 'admin/add-bulk-cases.html', [
      'page' => [
        'name' => 'AddBulk',
        'title' => 'Administration | Study Legal',
        'description' => 'Control the settings of Study Legal and perform maintenance.',
        'type' => 'secondary'
      ],
      'admindata' => [
        'books' => $appstatic->getAllBooks(),
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

//Parses the comma separated citations
$app->post('/apollo/test/citations', function($request, $response) {
  $data = $request->getParsedBody();
  $user = new sl_Account;
  $admin = new sl_Administration;
  $appstatic = new sl_Application_Static;
  if($user->isAdminLoggedIn() === true){
    return $this->view->render($response, 'admin/parse-citations.html', [
      'page' => [
        'name' => 'AddBulk',
        'title' => 'Administration | Study Legal',
        'description' => 'Control the settings of Study Legal and perform maintenance.',
        'type' => 'secondary'
      ],
      'admindata' => [
        'books' => $appstatic->getAllBooks(),
        'citations' => $admin->parseCitations($data['material-list'], 'test'),
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

//Do the scan with the parsed citations
$app->post('/apollo/material/bulk/scan', function($request, $response) {
  $data = $request->getParsedBody();
  $user = new sl_Account;
  $admin = new sl_Administration;
  $appstatic = new sl_Application_Static;
  if($user->isAdminLoggedIn() === true){
    $citations = $admin->parseCitations($data['material-list'], 'use');
    return $admin->bulkGSScrape($citations, $data['book']);
  }
  else {
      return $response->withStatus(302)->withHeader('Location', '/');
  }
});



//update all of the parsing in the cases (from original to parsed)
$app->get('/apollo/update/parse', function($request, $response) {
  $data = $request->getParsedBody();
  $user = new sl_Account;
  $admin = new sl_Administration;
  $appstatic = new sl_Application_Static;
  if($user->isAdminLoggedIn() === true){
    return $admin->parseCaseOpinion();
  }
  else {
      return $response->withStatus(302)->withHeader('Location', '/');
  }
});

//view all students
$app->get('/apollo/view/students', function($request, $response) {
  $user = new sl_Account;
  $admin = new sl_Administration;
  if($user->isAdminLoggedIn() === true){
    return $this->view->render($response, 'admin/view-users.html', [
      'page' => [
        'name' => 'ViewStudents',
        'title' => 'Administration | Study Legal',
        'description' => 'Control the settings of Study Legal and perform maintenance.',
        'type' => 'secondary'
      ],
      'admindata' => [
        'students' => $admin->getAllStudents(),
      ],
      'user' => [
        'loggedin' => $user->checkLoggedIn(),
        'email' => $user->getUserEmail(),
        'pro' => $user->isPro($user->getUserId()),
      ],
    ]);
  }
  else {
      return $response->withStatus(302)->withHeader('Location', '/');
  }
});

//Actually add the manual case
  $app->post('/apollo/addcase', function($request, $response) {
    $data = $request->getParsedBody();
    $user = new sl_Account;
    $admin = new sl_Administration;
    if($user->isAdminLoggedIn() === true){
      return $this->view->render($response, 'admin/add-case.html', [
        'page' => [
          'name' => 'AddCases',
          'title' => 'Administration | Study Legal',
          'description' => 'Control the settings of Study Legal and perform maintenance.',
          'type' => 'secondary'
        ],
        'admindata' => [
          'totalusers' => $admin->getStudentCount(),
        //  'activeusers' => $admin->getActiveStudents(),
          'procount' => $admin->getProCount(),
          'briefcount' => $admin->getBriefCount(),
          'exportcount' => $admin->getExportCount(),
        ],
        'user' => [
          'loggedin' => $user->checkLoggedIn(),
          'email' => $user->getUserEmail(),
          'pro' => $user->isPro($user->getUserId()),
        ],
        'admindata' => [
          'case' => $admin->parseGoogleScholarLink($data['url']),
        ]
        ]);
    }
    else {
        return $response->withStatus(302)->withHeader('Location', '/');
    }
});

$app->post('/apollo/add/case', function($request, $response) {
  $data = $request->getParsedBody();
  $user = new sl_Account;
  $admin = new sl_Administration;
  if($user->isAdminLoggedIn() === true){
    $thecasedata = $admin->parseGoogleScholarLink($data['source']);
    if($admin->addCaseToDb($data['idbook'], $thecasedata)){
      return $this->view->render($response, 'alerts/addcase-success.html');
    }
    else{
      return $this->view->render($response, 'alerts/addcase-failure.html');
    }
  }
  else {
      return $response->withStatus(302)->withHeader('Location', '/');
  }
});

  //function routes
  $app->get('/apollo/minify/js', function($request, $response) {
    $user = new sl_Account;
    $admin = new sl_Administration;
    if($user->isAdminLoggedIn() === true){
      return $admin->minifyJS();
    }
    else {
      return $response->withStatus(302)->withHeader('Location', '/');
    }
  });
  $app->get('/apollo/minify/css', function($request, $response) {
    $user = new sl_Account;
    $admin = new sl_Administration;
    if($user->isAdminLoggedIn() === true){
      return $admin->minifyCSS();
    }
    else {
      return $response->withStatus(302)->withHeader('Location', '/');
    }
  });
  $app->get('/apollo/clear/exports', function($request, $response) {
    $user = new sl_Account;
    $admin = new sl_Administration;
    if($user->isAdminLoggedIn() === true){
      return $admin->clearExportFolder();
    }
    else {
      return $response->withStatus(302)->withHeader('Location', '/');
    }
  });

/*
SCRAPER
*/
  //a tester
  $app->get('/apollo/test', function($request, $response, $args) {
    $user = new sl_Account;
    $admin = new sl_Administration;
    if($user->isAdminLoggedIn() === true){
      return $admin->testingAndMaintenance();
    }
    else {
      return $response->withStatus(302)->withHeader('Location', '/');
    }
  });

  //The Check
  $app->get('/apollo/scan/check/{idcitation}', function($request, $response, $args) {
    $user = new sl_Account;
    $admin = new sl_Administration;
    echo json_encode($admin->checkCitationGS($args['idcitation']));
    /*TESTING
    $checkResult = array(
        'result' => 'found',  //should be found, citationonly, notfound, blocked, notNew
        'url' => 'http://wademediagroup.com', //this is the link to the article page
        'data' => '', //this is empty unless it's a citation.
      );
      echo json_encode($checkResult);*/
  });

  //The Grab
  $app->get('/apollo/scan/grab/{idcitation}', function($request, $response, $args) {
    $user = new sl_Account;
    $admin = new sl_Administration;
    echo $admin->grabCaseGS($args['idcitation']);
    /* TESTING
    echo 'success';
    */
  });
