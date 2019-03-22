<?php
class sl_Commerce extends sl_Database {

  public function chargeIt($token, $email){
    \Stripe\Stripe::setApiKey('sk_test_nnN3KZPF1Su85uxuFjouNUP3');

    $token  = $_POST['stripeToken'];

    $customer = \Stripe\Customer::create(array(
      'email' => $email,
      'card'  => $token
    ));

    $charge = \Stripe\Charge::create(array(
      'customer' => $customer->id,
      'amount'   => 9999,
      'currency' => 'usd',
      'description' => 'One semester of Study Legal Pro.  Expires January 1st 2017,'
    ));

  }

  protected function recordPayment($iduser, $type, $idtranasaction, $transactiondate, $amount, $expiration, $name, $address){
    $database = $this->dbCommerce();
    $addrecord = $database->insert('payments', [
      'iduser' => $iduser,
      'type' => $type,
      'transaction_id' => $idtranasaction,
      'transaction_date' => $transactiondate,
      'amount' => $amount,
      'expiration' => $expiration,
      'name' => $name,
      'address' => $address,
    ]);
  }

  //Records payments from form
  public function recordStripePayment($iduser, $idtransaction, $name, $address){
    $database = $this->dbCommerce();
    $transactiondate = time();
    $extension = "";
    //$expiration = $expirationdate + 31536000; //60 secs x 60 mins x 24 hours x 365 days = 31536000
    $expiration = 1483228800; //January 1st 2016
    $amount = 9999;
    $this->recordPayment($iduser, "stripe", $idtransaction, $transactiondate, $amount, $expiration, $name, $address);
  }

  protected function recordCredit($iduser, $idtransaction, $transactiondate, $amount, $expiration){
    $this->recordPayment($iduser, "credit", $idtransaction, $transactiondate, $amount, $expiration, 'sl-credit', 'sl-credit');
  }

  protected function generateCredit($iduser, $expiration){
    $this->recordCredit($iduser, "sl-credit", time(), "sl-credit", $expiration);
  }

  public function checkCreditExists($iduser){
    $database = $this->dbCommerce();
    $checkforcredits = $database->select("payments", "*", [
      "AND" => [
        "iduser" => $iduser,
        "type" => "credit",
      ]
    ]);
    if(empty($checkforcredits)){
      return false;
    }
    else {
      return true;
    }
  }

  public function giveFreeMonth($iduser){
    $month = 2678400; //60 seconds * 60 minutes * 24 hours * 31 days = 2678400
    $now = time();
    $expiration = $now + $month;
    $this->generateCredit($iduser, $expiration);
  }

  public function creditUntilOctober($iduser){
    $expiration =  1475280000; //October 1st, 2016, GMT
    $this->generateCredit($iduser, $expiration);
  }

  public function giveHawaiiCredit($iduser){
    $expiration = 1483228800; //January 1st, 2017
    $this->generateCredit($iduser, $expiration);
  }

  public function emailReceipt($email){
    $now = time();
    $today = date("m/d/Y");
    $messagebody = '<div style="width:100%; max-width:580px; margin:0 auto;text-align:center;font-family: Arial,Helvetica,sans-serif;color: #414141;line-height: 18px;">
      <img src="https://study.legal/assets/img/logo_study_legal.png" style="max-width:200px;margin:0 auto;" />
      <h3>
        You Got Pro | Study Legal
      </h3>
      <p style="font-size: 14px; text-align:left;">
        Your payment went through on <b>' . $today . '</b>.
        Thank you for purchasing Pro.<br/>  If you have any questions or issues feel free to contact us via the contact form at <a href="https://study.legal">Study Legal</a>.
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
        'subject' => 'Payment Confirmation | Study Legal',
        'html'    => $messagebody,
    ));
  }

//end of class
}
