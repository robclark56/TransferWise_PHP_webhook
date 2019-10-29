# WORK IN PROGRESS - TRANSFERWISE HAS RECENTLY MOVED TO V3 API. WAITING ON THEM TO ADD MECHANISM TO CREATE V3 PROFILE WEBHOOKS IN THE ONLINE APP.
## THEN WILL DOCUMENT HOW TO GET THE ALL IMPORTANT (!) PAYMENT_REFERENCE FOR THE INCOMING PAYMENT USING THE GETSTATEMENT API CALL

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

Importantly, all code here is standalone, and does not use Composer to pull in other code. As such it is light-weight and easy to re-use.

## Reference Information
* [TransferWise API docs](https://api-docs.transferwise.com/)
* [TransferWise Profile Webhooks](https://api-docs.transferwise.com/#profile-webhooks)

## Requirements
1. A public webserver, responding to https requests on port 443, with a valid HTTPS certificate, and allowing you execute PHP scripts
1. A TransferWise account
1. You have previously installed and tested [TransferWise Simple API PHP Class](https://github.com/robclark56/TransferWise_PHP_SimpleAPIclass)

## Create your PHP Endpoint
1. Login to your webserver
1. Create [TransferWise_callback.php](code/TransferWise_callback.php) on your webserver
1. Edit the CHANGE ME section of `TransferWise_callback.php`

## Test your PHP endpoint
Use your favorite web browser to go to (e.g.): https://your.webserver.domain/TransferWise/TransferWise_callback.php

You should see something like this:
```
Signature not verified. Exiting 
```

## Create your Webhook
1. Login to your TransferWise account (Note: Webhooks will not be called from sandbox accounts)
1. Goto Settings
1. Click [Create a new webhook]
1. Give it a name and enter the URL of the Webhook end-point you tested in the step above
1. Check both **Transfer events** and **Balance events**
1. Click [Create webhook]
1. Click [Edit webhook]
1. Click [Test webhook]

You should see on the TransferWise acct page:  **Everything looks good!**

You should receive an email as below. Unlike the first test, this one says the callback signature was verified, and some data was passed into the callback.
```
File: /xxx/xxx/TransferWise/TransferWise_callback.php

Signature Verified = Yes

DATA
stdClass Object
(
    [message] => this is a test request
)

ProfileId = 
ResourceId= 
```

## Test your Webhook
Sandbox accounts do NOT call Webhooks. The only way to fully test the webhook is to cause a real financial payment in or out of your TransferWise account. When that happens you should receive an email something like this:
```
tba
```

