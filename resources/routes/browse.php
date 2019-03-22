<?php
/*
Browse
*/
  $app->get('/browse[/{book}[/{section}[/{material}]]]', function ($request, $response, $args) {
    $user = new sl_Account;
    $static = new sl_Application_Static;
    if(!isset($args['book']) && !isset($args['section']) && !isset($args['material'])){
      return $this->view->render($response, 'browse/browse.html', [
            'page' => [
              'name' => 'Browse',
              'title' => 'Browse Law | Study Legal',
              'description' => 'Browse the Study Legal database of law.  We offer cases, the FRCP, and the FRCrP.',
              'type' => 'secondary',
            ],
            'user' => [
              'loggedin' => $user->checkLoggedIn(),
              'email' => $user->getUserEmail(),
              'pro' => $user->isPro($user->getUserId()),
            ],
            'data' => [
              'books' => $static->getActiveCodeBooks()
            ]
        ]);
    }
    if(isset($args['book']) && !isset($args['section'])){
      $idbook = $static->getBookByUrl($args['book']);
      return $this->view->render($response, 'browse/book.html', [
            'page' => [
              'name' => 'Browse',
              'title' => 'Browse Law | Study Legal',
              'description' => 'Browse the Study Legal database of law.  We offer cases, the FRCP, and the FRCrP.',
              'type' => 'secondary',
            ],
            'user' => [
              'loggedin' => $user->checkLoggedIn(),
              'email' => $user->getUserEmail(),
              'pro' => $user->isPro($user->getUserId()),
            ],
            'data' => [
              'book' => $static->getBookByUrl($args['book'])[0],
              'sections' => $static->getCodeBookSections($idbook[0])
            ]
        ]);
    }
    if(isset($args['section']) && !isset($args['material'])){
      return $this->view->render($response, 'browse/section.html', [
            'page' => [
              'name' => 'Browse',
              'title' => 'Browse Law | Study Legal',
              'description' => 'Browse the Study Legal database of law.  We offer cases, the FRCP, and the FRCrP.',
              'type' => 'secondary',
            ],
            'user' => [
              'loggedin' => $user->checkLoggedIn(),
              'email' => $user->getUserEmail(),
              'pro' => $user->isPro($user->getUserId()),
            ],
            'data' => [
              'book' => $static->getBookByUrl($args['book'])[0],
              'section' => $static->getSectionByUrl($args['section']),
              'materials' => $static->getMaterialsBySectionUrl($args['section'])
            ]
        ]);
    }
    if(isset($args['material'])){
      return $this->view->render($response, 'browse/material.html', [
            'page' => [
              'name' => 'Browse',
              'title' => 'Browse Law | Study Legal',
              'description' => 'Browse the Study Legal database of law.  We offer cases, the FRCP, and the FRCrP.',
              'type' => 'secondary',
            ],
            'user' => [
              'loggedin' => $user->checkLoggedIn(),
              'email' => $user->getUserEmail(),
              'pro' => $user->isPro($user->getUserId()),
            ],
            'data' => [
              'book' => $static->getBookByUrl($args['book'])[0],
              'section' => $static->getSectionByUrl($args['section']),
              'material' => $static->getMaterialByUrl($args['material'])
            ]
        ]);
    }
});
