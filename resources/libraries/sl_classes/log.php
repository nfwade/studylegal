<?php
class sl_Log extends sl_Database {

  public function contactGeneral($email, $message, $ip){
    $mailto = 'nwade@study.legal';
    $messagebody = '<div style="width:100%; max-width:580px; margin:0 auto;text-align:center;font-family: Arial,Helvetica,sans-serif;color: #414141;line-height: 18px;">
      <img src="https://study.legal/assets/img/logo_study_legal.png" style="max-width:200px;margin:0 auto;" />
      <h3>
        From ' . $email . '
      </h3>
      <p style="font-size: 14px; text-align:left;">
        ' . $message . '
      </p>
      <p class="font-size:10px;">
        <a href="' . __WEBPATH__ . 'terms" target="_blank">Terms and Conditions</a> |
        <a href="' . __WEBPATH__ . 'privacy" target="_blank">Privacy Policy</a>
      </p>
      <p class="font-size:10px;">
        &#9400; Study Legal LLC
      </p>
    </div>';

    //create the email
    # Instantiate the client.
    $client = new \Http\Adapter\Guzzle6\Client();
    $mgClient = new Mailgun\Mailgun('key-8d7383c94c33d8bc374be5737f847ee3', $client);
    $domain = "study.legal";

    # Make the call to the client.
    $result = $mgClient->sendMessage($domain, array(
        'from'    => 'Study Legal <no-reply@study.legal>',
        'to'      => '<' . $mailto . '>',
        'subject' => 'New Msg From ' . $email . ' | Study Legal',
        'html'    => $messagebody,
    ));
    return true;
  }

  /*public function contactGeneral($email, $message, $ip){
    $mailto = 'nwade@study.legal';
    // create swift mailer object
    $transport = Swift_SmtpTransport::newInstance('a2plcpnl0440.prod.iad2.secureserver.net', 465, 'ssl');
      $transport->setUsername('no-reply@study.legal');
      $transport->setPassword('Qg)!d~Z87}HJ');
    $mailer = Swift_Mailer::newInstance($transport);
    // Create the message
    $message = Swift_Message::newInstance();

      // Give the message a subject
      $message->setSubject('Contact: General | Study Legal');

      // Set the From address with an associative array
      $message->setFrom(array('no-reply@study.legal' => 'Study Legal'));

      // Set the To addresses with an associative array
      $message->setTo(array($mailto));

      $message->setContentType('text/html');

      // Give it a body
      $message->setBody('
      <div style="width:100%; max-width:580px; margin:0 auto;text-align:center;font-family: Arial,Helvetica,sans-serif;color: #414141;line-height: 18px;">
        <img src="https://study.legal/assets/img/logo_study_legal.png" style="max-width:200px;margin:0 auto;" />
        <h1>
          Contact: General | Study Legal
        </h1>
        <h2>
          Hi Admin,
        </h2>
        <p style="font-size: 14px;">
          This message is from' . $email . ' at the ip: ' . $ip . '
        </p>
        <p style="font-size:14px;">
        Message:<br/>' . $message . '
        </p>
        <p>
          Thanks,<br/>
          Study Legal
        </p>
        <p class="font-size:10px;">
          <a href="' . __WEBPATH__ . 'terms" target="_blank">Terms and Conditions</a> |
          <a href="' . __WEBPATH__ . 'privacy" target="_blank">Privacy Policy</a>
        </p>
        <p class="font-size:10px;">
          &#9400; Study Legal, LLC
        </p>
      </div>
      ');

      $result = $mailer->send($message);
    return true;
  }*/

  public function contactAttempt($email, $ip){

  }

  public function checkContactAttempt($email, $ip){

  }

  public function userResetAttempt($email, $exists, $ip) {
  	$database = $this->dbLog();
  	$now = time();
  	$log_attempt = $database->insert("user_reset_attempts", [
  		"email" => $email,
  		"date" => $now,
      "ip" => $ip,
  		"real" => $exists
  	]);
  }

  public function userLoginSuccess($iduser, $remember, $ip){
  	$database = $this->dbLog();
  	$log_success = $database->insert("user_login_success", [
  		"iduser" => $iduser,
  		"date" => time(),
  		"remember" => $remember,
      "ip" => $ip
  	]);
  }

  public function userLoginFailure($email, $remember, $ip){
    $database = $this->dbLog();
    $log_success = $database->insert("user_login_failure", [
      "email" => $email,
      "date" => time(),
      "ip" => $ip
    ]);
  }

  public function userCancelation($userid, $email, $password, $ip){
    $database = $this->dbLog();
    $now = time();
    $log_success = $database->insert("user_cancelations", [
      "iduser" => $userid,
      "password" => $password,
      "date" => $now,
      "ip" => $ip
    ]);
  }

  public function userRegistrationFailure($email, $ip){
    $database = $this->dbLog();
    $log_success = $database->insert("user_registration_failure", [
      "email" => $email,
      "date" => time(),
      "ip" => $ip
    ]);
  }

  public function checkLoginCount($email) {
    $database = $this->dbLog();
    $now = time();
    // All login attempts are counted from the past 2 hours.(2 * 60 * 60)
    $twohoursago = $now - 7200;
    $checkthelog = $database->select("user_login_failure", "*", [
  		"AND" => [
  			"email" => $email,
  			"date[>]" => $twohoursago
  		]
  	]);

    //return count($checkthelog);
    $quantity = count($checkthelog);
  	// If there have been more than 5 failed logins
    return $quantity;
  	/*if ($quantity >= 4) {
  		return true;
  	} elseif() {
  		return false;
  	}*/
  }

  public function checkRegistrationCount($email) {
    $database = $this->dbLog();
    $now = time();
    // All login attempts are counted from the past 2 hours.
    $cutoff = $now - (2 * 60 * 60);
    $checkthelog = $database->count("user_registration_failure", "*", [
  		"AND" => [
  			"email" => $email,
  			"date[>]" => $cutoff
  		]
  	]);
  	// If there have been more than 5 failed logins
  	if ($checkthelog >= 3) {
  		return true;
  	} else {
  		return false;
  	}
  }

  public function checkResetCount($email, $ip){
    $database = $this->dbLog();
    $now = time();
    $oneweekago = $now - 604800; //60 secs * 60 mins * 24 hrs * 14 days = 1209600, 7 days =604800
    $checkemail = $database->count('user_reset-attempts', '*', [
      'AND' => [
        'email' => $email,
        'date[>]' => $oneweekago
      ]
    ]);
    $checkip = $database->count('user_reset-attempts', '*', [
      'AND' => [
        'ip' => $ip,
        'date[>]' => $oneweekago
      ]
    ]);
    if($checkemail >= 5 || $checkip >= 10){
      return false;
    }
    else {
      return true;
    }
  }

  public function getNotificatin($id){
      $database = $this->dbLog();
      $getnot = $database->get('notifications', '*', [
        'id' => $id
      ]);
  }

  public function isNotified($iduser){
    $database = $this->dbLog();
    $newestview = $database->max('notification_views', 'idnotification' ,[
      'iduser' => $iduser
    ]);
    $newestnotification = $database->max('notifications', 'id');
    if ($newestview == $newestnotification){
      return true;
    }
    else {
      return false;
    }
  }

  public function getNotifications($iduser){
    $database = $this->dbLog();
    $newestview = $database->max('notification_views', 'idnotification' ,[
      'iduser' => $iduser
    ]);
    /*$unviewednotifications = array();
    $allnotifications = $database->select('notifications', '*');
    foreach ($allnotifications as $key => $value) {
      if($value['id'] > $newestview){

      }
    }*/
  }

  public function isFirstLogin($iduser){
    $database = $this->dbLog();
    $quantity = $database->count('user_login_success', [
      'iduser' => $iduser
    ]);
    if ($quantity < 200){
      return true;
    }
    else {
      return false;
    }
  }
}
