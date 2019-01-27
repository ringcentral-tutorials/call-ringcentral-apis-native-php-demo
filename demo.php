<?php
include('ringcentral.php');

$rc = new RingCentral();

function callback($response){
    print $response."\r\n";
}

function get_account_extension() {
    global $rc;
    try{
        $endpoint = "/restapi/v1.0/account/~/extension";
        $params = array('status' => 'Enabled');
        $rc->get($endpoint, $params, 'callback');
    }catch (Exception $e) {
        print $e->getMessage();
    }
}

function send_sms($recipientNumber, $message){
    global $rc;
    try{
        $endpoint = "/restapi/v1.0/account/~/extension/~/sms";
        $params = array('from' => array('phoneNumber' => getenv("RC_USERNAME")),
                        'to' => array(array('phoneNumber' => $recipientNumber)),
                        'text' => $message
                      );
        $rc->post($endpoint, $params, 'callback');
    }catch (Exception $e) {
        print $e->getMessage();
    }
}

get_account_extension();
//send_sms('recipientNumber', "Hello World!");
