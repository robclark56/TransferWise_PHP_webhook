<?php
//
// Filename: .../TransferWise_callback.php
//
// See https://github.com/robclark56/TransferWise_PHP_webhook
//

//////// CHANGE ME     ////////////
define('MY_EMAIL','me@my.domain.com');

// Include the TransferWise class.
//  See https://github.com/robclark56/TransferWise_PHP_SimpleAPIclass
//  Typical File Structure
//
//   TransferWise
//       | TransferWise_callback.php  (this file)
//       | test.php
//       | includes
//            | configure.php
//            | class_TransferWise.php
//
//  Edit the line below if needed to correctly locate the class_TransferWise.php file
include('includes/class_TransferWise.php');
///////// END CHANGE ME ////////////


$msg = 'File: '.__FILE__."\n";

// Verify Signature
//  See https://api-docs.transferwise.com/#webhooks-events
$pub_key = "-----BEGIN PUBLIC KEY-----\n";
$pub_key .= 
'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvO8vXV+JksBzZAY6GhSO
XdoTCfhXaaiZ+qAbtaDBiu2AGkGVpmEygFmWP4Li9m5+Ni85BhVvZOodM9epgW3F
bA5Q1SexvAF1PPjX4JpMstak/QhAgl1qMSqEevL8cmUeTgcMuVWCJmlge9h7B1CS
D4rtlimGZozG39rUBDg6Qt2K+P4wBfLblL0k4C4YUdLnpGYEDIth+i8XsRpFlogx
CAFyH9+knYsDbR43UJ9shtc42Ybd40Afihj8KnYKXzchyQ42aC8aZ/h5hyZ28yVy
Oj3Vos0VdBIs/gAyJ/4yyQFCXYte64I7ssrlbGRaco4nKF3HmaNhxwyKyJafz19e
HwIDAQAB';
$pub_key .="\n-----END PUBLIC KEY-----";
$signature = $_SERVER['HTTP_X_SIGNATURE'];
$json      = file_get_contents('php://input');
$verify    = openssl_verify ($json , base64_decode($signature) , $pub_key, OPENSSL_ALGO_SHA1);
if(!$verify){
    $msg .= "Signature not verified. Exiting";
    commonExit($msg);
}

//Inspect payload
$payload = json_decode($json);
//$msg .= "\n\nPAYLOAD\n".print_r($payload,1);

if(!isset($payload->event_type)) {
    $msg .= "\nevent_type not set";
    commonExit($msg);
}


$profileId = $payload->data->resource->profile_id;
$tw = new TransferWise($profileId);
switch($payload->event_type){
  case 'balances#credit':
    //See https://api-docs.transferwise.com/#webhook-events-transfer-issue-event
    $amount        = $payload->data->amount;
    $currency      = $payload->data->currency;
    $occurred_at   = rtrim($payload->data->occurred_at,'Z');

    // Get statement for last hour
    $intervalStart = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 hour'));
    $statement     = json_decode($tw->getStatement($currency,'json',$intervalStart));
        
    //Go through each transaction to find a match to the transaction in this callback
    if(isset($statement->transactions) && sizeof($statement->transactions)){
        foreach($statement->transactions as $transaction){
            if($amount   != $transaction->amount->value ||
               $currency != $transaction->amount->currency ||
               strpos(rtrim($transaction->date,'Z'),$occurred_at) === false
              ){
              continue; //Not a match
            }
            //Found a match
            $paymentReference = $transaction->details->paymentReference;
            $msg .= "\ncurrency=$currency";
            $msg .= "\namount=$amount";
            $msg .= "\nsenderName=".$transaction->details->senderName;
            $msg .= "\npaymentReference=$paymentReference";
            break;
        }
    }
    break;
      
  default:
    $msg .= "\nNo handler for event_type: $payload->event_type";
}

if($paymentReference){
  // [Optional] Add code here as needed to perform
  //  some action now you know the details of the
  //  payment.
}

commonExit($msg);


function commonExit($msg){
 mail(MY_EMAIL,'TransferWise Callback',$msg);
 exit;
}
?>
