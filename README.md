# TransferWise PHP Webhook

## Introduction
If you have a (free) [TransferWise](https://transferwise.com) account, you have free access to both:
* an API interface to interact with your account, and
* a Webhook callback to notify you of events/changes in your account.

## Basic Details
In this tutorial, you will learn how to use PHP to:
* Create a webhook that will notify your webserver, 
* Parse and verify the data passed to your webserver, and
* Use the API to get details of the transaction, if appropriate.

## Reference Information
* [TransferWise API docs](https://api-docs.transferwise.com/)
* [TransferWise Webhooks](https://api-docs.transferwise.com/#webhooks)

## Requirements
1. A public webserver, responsing to https requests on port 443, and allowing you execute PHP scripts
1. A TransferWise account

## Create your PHP Endpoint
1. Login to your webserver
1. Create a file on your webserver called `TransferWise_callback.php`
1. Edit and save this file as below. Edit the CHANGE ME section as needed.
```
<?php
//////// CHANGE ME     ////////////
define('MY_EMAIL','me@my.email.domain');
//////// END CHANGE ME ////////////

$msg = 'File: '.__FILE__."\n\n";

// Verify Signature
//  We use the Public Key that TransferWise shows in their documentation
// See https://api-docs.transferwise.com/#webhooks-events
$pub_key = 
"-----BEGIN PUBLIC KEY-----\n".
'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvO8vXV+JksBzZAY6GhSO
XdoTCfhXaaiZ+qAbtaDBiu2AGkGVpmEygFmWP4Li9m5+Ni85BhVvZOodM9epgW3F
bA5Q1SexvAF1PPjX4JpMstak/QhAgl1qMSqEevL8cmUeTgcMuVWCJmlge9h7B1CS
D4rtlimGZozG39rUBDg6Qt2K+P4wBfLblL0k4C4YUdLnpGYEDIth+i8XsRpFlogx
CAFyH9+knYsDbR43UJ9shtc42Ybd40Afihj8KnYKXzchyQ42aC8aZ/h5hyZ28yVy
Oj3Vos0VdBIs/gAyJ/4yyQFCXYte64I7ssrlbGRaco4nKF3HmaNhxwyKyJafz19e
HwIDAQAB'.
"\n-----END PUBLIC KEY-----";

$signature = $_SERVER['HTTP_X_SIGNATURE'];
$payload   = file_get_contents('php://input');
$verify    = openssl_verify ($payload , base64_decode($signature) , $pub_key, OPENSSL_ALGO_SHA1);
$msg .= "\nSignature Verified = ".($verify?'Yes':'No');

$data = json_decode($payload);
$msg .= "\nDATA\n".print_r($data,1);

if(isset($data->profileId)) $profileId  = $data->profileId;
if(isset($data->resourceId))$resourceId = $data->resourceId;
$msg .= "\nProfileId = $profileId";
$msg .= "\nresourceId= $resourceId";

mail(MY_EMAIL,'TransferWise Callback',$msg);
?>
```
## Test your PHP endpoint
Use your favorite web browser to go to (e.g.): https://your.webserver.domain/TransferWise_callback.php
You should receive an email something like this:
```
xxxxx
```

## Create your Webhook
1. Login to your TransferWise account
1. Goto Settings
1. Click [Create a new webhook]
1. Give it a name and enter the URL of the Webhook end-point (i.e. your PHP file)