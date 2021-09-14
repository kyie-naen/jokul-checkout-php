<?php
require_once('core.php');
$headers = getallheaders();
$secretkey = 'SK-oqadmqpeZpMDa21lvEgo'; //set your secretkey
$path = '/notify.php'; //set your path notify

$digest = Jokul_Core::generateDigest(file_get_contents('php://input'));

$signature = Jokul_Core::generateSignaturePost($headers['Client-Id'], $headers['Request-Id'], $headers['Request-Timestamp'], $secretkey, $path, $digest);

$signatureHMAC ="HMACSHA256=".$signature ;

//Something to write to txt log
if ($signatureHMAC == $headers['Signature']) {
    $log  = "Signature Generate: ".$signatureHMAC.PHP_EOL.
        "Signature DOKU: ".$headers['Signature'].PHP_EOL.
        "Berhasil".PHP_EOL.
        "-------------------------".PHP_EOL;
    //edit
} else {
    $log  = "Signature Generate: ".$signatureHMAC.PHP_EOL.
        "Signature DOKU: ".$headers['Signature'].PHP_EOL.
        "Gagal".PHP_EOL.
        "-------------------------".PHP_EOL;
}

//Save string to log, use FILE_APPEND to append.
file_put_contents('./log_'.date("j.n.Y").'.txt', $log, FILE_APPEND);
?>
