<?php
require_once('vendor/autoload.php');

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$env_file = "./environment/";
$tokens_file = "tokens_";
if (getenv('ENVIRONMENT') == "sandbox"){
   $env_file .= ".env-sandbox";
   $tokens_file .= "sb.txt";
}else{
  $env_file .= ".env-production";
  $tokens_file .= "pd.txt";
}
$dotenv = new Dotenv\Dotenv(__DIR__, $env_file);
$dotenv->load();

class RingCentral {
    private $access_token = "";
    function __construct() {}

    public function authenticate(){
        global $tokens_file;
        $url = getenv("RC_SERVER_URL") . "/restapi/oauth/token";
        $basic = getenv("RC_CLIENT_ID") .":". getenv("RC_CLIENT_SECRET");
        $headers = array (
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($basic)
          );
        $body = http_build_query(array (
            'grant_type' => 'password',
            'username' => urlencode(getenv("RC_USERNAME")),
            'password' => getenv("RC_PASSWORD")
          ));
        if (file_exists($tokens_file)){
            $saved_tokens = file_get_contents($tokens_file);
            $tokensObj = json_decode($saved_tokens);
            $date = new DateTime();
            $expire_time= $date->getTimestamp() - $tokensObj->timestamp;
            if ($expire_time < $tokensObj->tokens->expires_in){
              print "Access token is still valid.\r\n";
              $this->access_token = $tokensObj->tokens->access_token;
            }else if ($expire_time <  $tokensObj->tokens->refresh_token_expires_in) {
                print "refresh_token not expired. Get access token using the refresh token.\r\n";
                $body = http_build_query(array (
                  'grant_type' => 'refresh_token',
                  'refresh_token' => $tokensObj->tokens->refresh_token
                ));
            }else{
                print "refresh_token expired. Move on to login normally.\r\n";
            }
        }

        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 600);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

            $strResponse = curl_exec($ch);
            $curlErrno = curl_errno($ch);
            if ($curlErrno) {
                throw new Exception($curlErrno);
            } else {
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
	              if ($httpCode == 200) {
                    $date = new DateTime();
                    $jsonObj = json_decode($strResponse);
                    $tokensObj = array(
                      "tokens" => $jsonObj,
                      "timestamp" => $date->getTimestamp()
                    );
                    file_put_contents($tokens_file, json_encode($tokensObj, JSON_PRETTY_PRINT));
                    $this->access_token = $jsonObj->access_token;
                }else{
                    throw new Exception($strResponse);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function get($endpoint, $params=null, $callback=""){
        try {
            $this->authenticate();
            $url = getenv("RC_SERVER_URL") . $endpoint;
            if ($params != null)
              $url .= "?".http_build_query($params);
            $headers = array (
                  'Accept: application/json',
                  'Authorization: Bearer ' . $this->access_token
                );
            try {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_TIMEOUT, 600);

                $strResponse = curl_exec($ch);
                $curlErrno = curl_errno($ch);
                if ($curlErrno) {
                    throw new Exception($ecurlError);
                } else {
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    if ($httpCode == 200) {
                      return ($callback == "") ? $strResponse : $callback($strResponse);
                    }else{
                        throw new Exception($strResponse);
                    }
                }
            } catch (Exception $e) {
                throw $e;
            }
        }catch (Exception $e) {
            throw $e;
        }
    }

    public function post($endpoint, $params=null, $callback=""){
        try {
            $this->authenticate();
            $url = getenv("RC_SERVER_URL") . $endpoint;
            $body = array();
            if ($params != null)
                $body = json_encode($params);

            $headers = array (
                  'Content-Type: application/json',
                  'Accept: application/json',
                  'Authorization: Bearer ' . $this->access_token
                );
            try {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_TIMEOUT, 600);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

                $strResponse = curl_exec($ch);
                $curlErrno = curl_errno($ch);
                if ($curlErrno) {
                    throw new Exception($curlErrno);
                } else {
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    if ($httpCode == 200) {
                        return ($callback == "") ? $strResponse : $callback($strResponse);
                    }else{
                        throw new Exception($strResponse);
                    }
                }
            }catch (Exception $e) {
                throw $e;
            }
        }catch (Exception $e) {
            throw $e;
        }
    }
}
