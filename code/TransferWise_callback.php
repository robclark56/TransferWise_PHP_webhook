<?php
//
// Filename: .../TransferWise_callback.php
//

//////// CHANGE ME     ////////////
define('MY_EMAIL','me@my.domain.com');
//////// END CHANGE ME ////////////

$msg = 'File: '.__FILE__."\n";

// Include the TransferWise class.
//  See https://github.com/robclark56/TransferWise_PHP_SimpleAPIclass
//  Edit the line below if needed to correcly locate the class_TransferWise.php file
//
include('includes/class_TransferWise.php');

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
$payload   = file_get_contents('php://input');
$verify    = openssl_verify ($payload , base64_decode($signature) , $pub_key, OPENSSL_ALGO_SHA1);
$msg .= "\nSignature Verified = ".($verify?'Yes':'No');

$data = json_decode($payload);
$msg .= "\n\nDATA\n".print_r($data,1);

if(isset($data->profileId)) $profileId  = $data->profileId;
if(isset($data->resourceId))$resourceId = $data->resourceId;
$msg .= "\nProfileId = $profileId";
$msg .= "\nResourceId= $resourceId";

if($resourceId){
    $tw = new TransferWise($profileId);
    $msg .= "\n\nTRANSFER:\n".print_r(json_decode($tw->getTransferById($resourceId)),1);
}

mail(MY_EMAIL,'TransferWise Callback',$msg);
?>
