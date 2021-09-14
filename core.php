<?php

class Jokul_Core {

  //function generate digest
  public static function generateDigest($data){
    return base64_encode(hash('sha256', json_encode($data), true));
  }

  //function generate Signature
  public static function generateSignaturePost($clientid, $requestid, $requesttime, $secretkey, $path, $digest){
    $componentSignature = "Client-Id:" . $clientid . "\n"
    	                    . "Request-Id:" . $requestid . "\n"
                          . "Request-Timestamp:" . $requesttime . "\n"
    	                    . "Request-Target:" . $path ."\n"
    	                    . "Digest:" . $digest;
    return base64_encode(hash_hmac('sha256', $componentSignature, $secretkey,true));
  }

  public static function generateSignatureGet($headers, $clientid, $requestid, $requesttime, $secretkey, $path, $digest){
    $componentSignature = "Client-Id:" . $clientid . "\n"
    	                    . "Request-Id:" . $requestid . "\n"
                          . "Request-Timestamp:" . $requesttime . "\n"
    	                    . "Request-Target:" . $path;
    return base64_encode(hash_hmac('sha256', $componentSignature, $secretkey,true));
  }

  // Execute request
  public static function hitApi($url, $clientId, $requestId, $dateTimeFinal, $signature, $data){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Client-Id:' . $clientId,
        'Request-Id:' . $requestId,
        'Request-Timestamp:' . $dateTimeFinal,
        'Signature:' . "HMACSHA256=" . $signature
    ));
    // Set response json
    $responseJson = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $result = array (
      'responseJson' => $responseJson,
      'httpcode' => $httpcode
    );
    return $result;
  }

}
