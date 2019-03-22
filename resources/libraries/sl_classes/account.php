<?php

class sl_Account extends sl_Database {

  public function addNewStudent($email, $password){
    $database = $this->dbAccount();
    $now = time();
    $now = $now - 2185334;
    $now2 = $now - 51509;
    $reg_hash = sha1($now);
    $user_hash = sha1($now2);
    $password = sha1($password);
    $create_user = $database->insert("users", [
      "email" => $email,
      "type" => "student",
      "password" => $password,
      "date_joined" => $now,
      "registration_status" => "unconfirmed",
      "reghash" => $reg_hash,
      "userhash" => $user_hash
    ]);
  }

  public function checkEmailExists($email) {
		//check to see if email already exists, or is unconfirmed, or is frozen
    if(filter_var($email, FILTER_VALIDATE_EMAIL) && $this->checkEmailEdu($email)) {
  		$database = $this->dbAccount();
  		$check_active = $database->count("users", [
  			"AND" => [
  				"email" => $email,
  				"registration_status" => "active"
  			]
  			]);
  		$check_unconfirmed = $database->count("users", [
  			"AND" => [
  				"email" => $email,
  				"registration_status" => "unconfirmed"
  			]
  			]);
  		$check_frozen = $database->count("users", [
  			"AND" => [
  				"email" => $email,
  				"registration_status" => "frozen"
  			]
  			]);
  		if ($check_active == 0 && $check_unconfirmed == 0 && $check_frozen == 0) {
  				return false;
    		}
    		else {
    			return true;
    		}
    }
    else {

    }
  }

  public function getIdFromEmail($email){
    $database = $this->dbAccount();
    $iduser = $database->get('users', 'id', [
      'email' => $email,
    ]);
    return $iduser;
  }

  protected function checkEmailEdu($email){
  $email = explode(".",$email);
  if($email[count($email)-1] == "edu")
    return true;
  else
    return true;
}

  public function attemptLogin($email, $submittedpassword, $remember) {
      $database = $this->dbAccount();
      $submittedhash = sha1($submittedpassword);
      $getuser = $database->get("users","*", [
        "AND" => [
          "email" => $email,
          "registration_status" => "active"
        ]
      ]);
      $userpassword = $getuser['password'];
      $userstatus = $getuser['registration_status'];

  		if ($this->timingSafeCompare($userpassword, $submittedhash) == true) {
  			//check if user is active
        $this->secSessionStart();
				$_SESSION["user_id"] = $getuser["id"];
				$_SESSION["email"] = $getuser["email"];
				//generate the cookie if need
				if($remember == true) {
          $this->generateRememberCookie($getuser['id']);
          return true;
				}
  			else {
  				return true;
  			}
  		}
  		else {
  			return false;
  		}
  }


    public function checkLoggedIn() {
      $this->secSessionStart();
      if (isset($_SESSION["user_id"])){
    		return true;
    	}
    	if (!isset($_SESSION["user_id"])){
    		return false;
    	}
    }

    public function isAdminLoggedIn(){
      $database = $this->dbAccount();
      if($this->checkLoggedIn() === true){
        $this->secSessionStart();
        $usertype = $database->get('users', 'type', [
          'id' => $_SESSION['user_id']
        ]);
        if($usertype === 'administrator'){
            return true;
        }
        else {
          return false;
        }
      }
      else {
        return false;
      }
    }

    public function getUserEmail(){
      $this->secSessionStart();
      if (isset($_SESSION["user_id"])){
        return $_SESSION['email'];
      }
      if (!isset($_SESSION["user_id"])){
        return false;
      }
    }

    public function getUserId(){
      $this->secSessionStart();
      if (isset($_SESSION["user_id"])){
        return $_SESSION['user_id'];
      }
      if (!isset($_SESSION["user_id"])){
        return false;
      }
    }

    public function isPro(){
      $this->secSessionStart();
      if (isset($_SESSION["user_id"])){
        $iduser = $_SESSION["user_id"];
        $now = time();
        $database = $this->dbCommerce();
        $getpayment = $database->max('payments', 'expiration', [
          "iduser" => $iduser,
        ]);
        if ($getpayment > $now) {
          return true;
        }
        else {
          return false;
        }
    	}
    	if (!isset($_SESSION["user_id"])){
    		return false;
    	}
    }

    public function getExpirationFormated(){
      $this->secSessionStart();
      $now = time();
      if (isset($_SESSION["user_id"])){
        $iduser = $_SESSION["user_id"];
        $database = $this->dbCommerce();
        $getpayment = $database->max('payments', 'expiration', [
          "iduser" => $iduser,
        ]);
        if ($getpayment > $now) {
          $getpayment = gmdate("M-d-Y", $getpayment);
          return $getpayment;
        }
        else {
          return false;
        }
      }
      if (!isset($_SESSION["user_id"])){
        return false;
      }
    }

    public function isFirstLogin(){
      $this->secSessionStart();
      $now = time();
      if (isset($_SESSION["user_id"])){
        $iduser = $_SESSION["user_id"];
        $database = $this->dbLog();
        $countlogins = $database->count('user_login_success', [
          'iduser' => $iduser,
        ]);
        if($countlogins == 1){
          return true;
        }
        else {
          return false;
        }
      }
      else {
        return false;
      }
    }

    public function willExpireSoon(){
      $this->secSessionStart();
      $now = time();
      $twodays = $now + 172800;//60 secs * 60 mins * 48 hours = 172800
      if (isset($_SESSION["user_id"])){
        $iduser = $_SESSION["user_id"];
        $database = $this->dbCommerce();
        $expiraton = $database->max('payments', 'expiration', [
          'iduser' => $iduser,
        ]);
        if ($expiraton < $twodays) {
          return true;
        }
        else {
          return false;
        }
      }
    }

    public function logOut(){
    	$this->secSessionStart();
      if(isset($_SESSION['user_id'])){
        $userid = $_SESSION["user_id"];
      	$this->deactivateAllTokens($userid);

      	// Unset all session values
      	$_SESSION = array();

      	// get session parameters
      	$params = session_get_cookie_params();

      	// Delete the actual cookie.
      	setcookie(session_name(),
      			'', time() - 42000,
      			$params["path"],
      			$params["domain"],
      			$params["secure"],
      			$params["httponly"]);

      	// Destroy session
      	session_destroy();
      }
    }

    public function deactivateAllTokens($userid) {
    	$database = $this->dbAccount();
    	$delcookie = $database->update("login_tokens", [
    		"status" => "inactive"
    	], [
    		"iduser" => $userid
    	]);
    }

    public function generateRememberCookie($userid) {
    	$database = $this->dbAccount();
    	$now = time();
    	$token = $this->generateRandomToken();
    	//store token for user
    	$storeit = $database->insert("login_tokens", [
    		"iduser" => $userid,
    		"token1" => $token,
    		"status" => "active",
    		"date" => $now
    	]);
    	$secretkey = "czhVT43e9CMtHpmmRKDM3zg9cnlT7RLqJrPd";
        $mac = hash_hmac('sha256', $token, $secretkey);
        $cookie = $userid .':' . $mac . ':' . $now;
        setcookie('rm1', $cookie, time() + (86400 * 30), '/');
    }

    protected function generateRandomToken(){
      $rand = substr(md5(microtime()),rand(0,26),26);
      return $rand;
    }

    public function checkRememberMe(){
      $explimit = 2592000;
      $now = time();
      $datecheck = $now - $explimit;
      $secretkey = "czhVT43e9CMtHpmmRKDM3zg9cnlT7RLqJrPd";
      $database = $this->dbAccount();
      if (isset($_COOKIE["rm1"])) {
        $cookie = $_COOKIE["rm1"];
        list ($user, $cookiemac, $cookiedate) = explode(':', $cookie);
        $checkactive = $database->count("users", [
          "AND" => [
            "id" => $user,
            "registration_status" => "active"
          ]
        ]);
        $dbdatecheck = $database->get("login_tokens", "date", [
          "AND" => [
            "iduser" => $user,
            "status" => "active"
          ]
        ]);
        if ($cookiedate > $datecheck && $checkactive === 1) {
              if ($dbdatecheck > $datecheck) {
                //now the hash check
                $dbtoken = $database->get("login_tokens", "token1", [
                  "AND" => [
                    "iduser" => $user,
                    "status" => "active"
                  ]
                ]);
                $dbmac = hash_hmac('sha256', $dbtoken, $secretkey);
                //and compare them
                if($this->timingSafeCompare($dbmac, $cookiemac) === true){
                  //log them in
                  return $this->autoLogin($user);
                }
                else {
                  return false;
                }
              }
        }
      }
    }

    public function timingSafeCompare($safe, $user) {
        // Prevent issues if string length is 0
        $safe .= chr(0);
        $user .= chr(0);

        $safeLen = strlen($safe);
        $userLen = strlen($user);

        // Set the result to the difference between the lengths
        $result = $safeLen - $userLen;

        // Note that we ALWAYS iterate over the user-supplied length
        // This is to prevent leaking length information
        for ($i = 0; $i < $userLen; $i++) {
            // Using % here is a trick to prevent notices
            // It's safe, since if the lengths are different
            // $result is already non-0
            $result |= (ord($safe[$i % $safeLen]) ^ ord($user[$i]));
        }
        // They are only identical strings if $result is exactly 0...
        return $result === 0;
    }

    public function autoLogin($iduser) {
      $database = $this->dbAccount();
      $checkactive = $database->count("users", [
        "AND" => [
          "id" => $iduser,
          "registration_status" => "active",
        ]
      ]);
      if($checkactive == 1) {
        $this->secSessionStart();
        $_SESSION["user_id"] = $iduser;
        $getemail = $database->get("users", "email", [
          "AND" => [
            "id" => $iduser,
            "registration_status" => "active"
          ]
        ]);
        $_SESSION["email"] = $getemail;
      }
    }

    public function sendRegistationConfirmation($mailto) {
      //gather user information
      $database = $this->dbAccount();
      $reghash = $database->get('users','reghash',[
        'email' => $mailto,
      ]);
      $userhash = $database->get('users', 'userhash', [
        'email' => $mailto,
      ]);
    	$link = __WEBPATH__ . "user/confirm/registration/" . $reghash . "/" . $userhash;
      $messagebody = '<div style="width:100%; max-width:580px; margin:0 auto;text-align:center;font-family: Arial,Helvetica,sans-serif;color: #414141;line-height: 18px;">
        <img src="https://study.legal/assets/img/logo_study_legal.png" style="max-width:200px;margin:0 auto;" />
        <h1>
          Confirm Your Email | Study Legal
        </h1>
        <h2>
          Hi ' . $mailto . ',
        </h2>
        <p style="font-size: 14px;">
          Welcome to Study Legal.  To get started please click the link below to verfiy your email. If you have any problems, feel free to contact us.
        </p>
        <p style="font-size: 14px;">
          <a href="' . $link . '" target="_blank" style="">Click Here.</a>
        </p>
        <p style="font-size:10px;">
          If you have problems with the link, copy and paste this into your browser: ' . $link .'
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
          'subject' => 'Confirm Your Email | Study Legal',
          'html'    => $messagebody,
      ));
      return true;
    }

    protected function activateUser($iduser){
      $database = $this->dbAccount();
      $activate = $database->update('users', [
        'registration_status' => 'active',
      ],
    [
      'id' => $iduser
    ]);
    }

    public function confirmRegLink($reghash, $userhash) {
      $database = $this->dbAccount();
      $dbreghash = $database->count('users', [
        'reghash'=> $reghash
      ]);
      if ($dbreghash === 1){
        $dbuserhash = $database->get('users', 'userhash',[
          'reghash' => $reghash,
        ]);
        if($this->timingSafeCompare($dbuserhash, $userhash) === true){
          //get the users id
          $iduser = $database->get('users', 'id', [
            'userhash' => $userhash
          ]);
          //activate the user
          $this->activateUser($iduser);
          return true;
        }
      }
      else {
        return false;
      }
    }

    public function getEmailByHash($reghash, $userhash){
      $database = $this->dbAccount();
      $useremail = $database->get('users', 'email', [
        'AND' => [
          'reghash' => $reghash,
          'userhash' => $userhash,
        ]
      ]);
      if($useremail !== null){
        return $useremail;
      }
      else {
        return false;
      }
    }

    public function resetPassword($email){
      $database = $this->dbAccount();
      //create a new reg hash
      $now = time();
  		$now = $now - 2185334;
  		$newreghash = sha1($now);
      // update the user status and reghash
      $updateuser = $database->update("users", [
        "reghash" => $newreghash,
        "registration_status" => "frozen",
        "date_updated" => $now,
      ],[
        "email" => $email
      ]);
      //build the email and send it
      $userhash = $database->get('users', 'userhash', [
        'email' => $email,
      ]);
    	$link = __WEBPATH__ . "user/reset/link/" . $newreghash . "/" . $userhash;
      $messagebody = '<div style="width:100%; max-width:580px; margin:0 auto;text-align:center;font-family: Arial,Helvetica,sans-serif;color: #414141;line-height: 18px;">
        <img src="https://study.legal/assets/img/logo_study_legal.png" style="max-width:200px;margin:0 auto;" />
        <h1>
          Reset Your Password | Study Legal
        </h1>
        <h2>
          Hi ' . $email . ',
        </h2>
        <p style="font-size: 14px;">
          Click the link below to reset your password.  The link will only remain active for 30 days.
        </p>
        <p style="font-size: 14px;">
          <a href="' . $link . '" target="_blank" style="">Click Here.</a>
        </p>
        <p style="font-size:10px;">
          If you have problems with the link, copy and paste this into your browser: ' . $link .'
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
          'to'      => '<' . $email . '>',
          'subject' => 'Reset Your Password | Study Legal',
          'html'    => $messagebody,
      ));
      return true;
    }

    public function resetLinkExpired($userhash, $reghash){
      $database = $this->dbAccount();
      $now = time();
      $onemonthago = $now - 2592000;// 60 secs * 60 mins * 24 hrs * 30 days =2592000
      $updateddate = $database->get('users', 'date_updated',[
        'AND' => [
          'userhash' => $userhash,
          'reghash' => $reghash,
        ]
      ]);
      if($updateddate > $onemonthago) {
        return false;
      }
      else {
        return true;
      }
    }

    public function updatePassword($password, $reghash, $userhash){
      $database = $this->dbAccount();
      $now = time();
      $password = sha1($password);
      $changepassword = $database->update('users', [
        'password' => $password,
        'date_updated' => $now,
      ], [
        'AND' => [
          'reghash' => $reghash,
          'userhash' => $userhash,
          'registration_status' => 'frozen',
        ]
      ]);
      $now = time();
      $now = $now - 2185334;
      $newreghash = sha1($now);
      $updatereghash = $database->update('users', [
        'reghash' => $newreghash,
        'registration_status' => 'active',
      ], [
        'userhash' => $userhash,
      ]);
      return true;
    }


  public function cancelAccount($iduser, $email, $password) {
    $database = $this->dbAccount();
    $encryptedpassword = sha1($password);
    //check that the user is active
    $useractive = $database->count('users', [
      'AND' => [
        'id' => $iduser,
        'password' => $encryptedpassword,
        'email' => $email,
      ]
      ]);

      if($useractive === 1){
        $canceluser = $database->update('users', [
          'registration_status' => 'canceled'
        ], [
          'AND' => [
            'id' => $iduser,
            'email' => $email,
            'password' => $encryptedpassword,
          ]
        ]);
        $messagebody = '<div style="width:100%; max-width:580px; margin:0 auto;text-align:center;font-family: Arial,Helvetica,sans-serif;color: #414141;line-height: 18px;">
          <img src="https://study.legal/assets/img/logo_study_legal.png" style="max-width:200px;margin:0 auto;" />
          <h1>
            Account Canceled | Study Legal
          </h1>
          <h2>
            Hi ' . $email . ',
          </h2>
          <p style="font-size: 14px;">
            We\'re sorry to see you go.  We canceled your account.  If you didn\'t authorize this please let us know immediately.
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
            'to'      => '<' . $email . '>',
            'subject' => 'Account Canceled | Study Legal',
            'html'    => $messagebody,
        ));
      }
      else {
        return false;
      }
  }
}
