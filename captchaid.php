<?php
require_once 'config.inc.php';
require_once 'dbinit.php';
require_once dirname(__FILE__).'/securimage/securimage.php';

$limit = 10;
$foundCaptcha = false;
$captchaId = null;

while(!$foundCaptcha && $limit > 0){
    $captchaId = Securimage::getCaptchaId(true);    
    if (!existsCaptcha($conn, $captchaId)){
        storeCaptcha($conn, $captchaId);
        $foundCaptcha=true;
    }
    $limit--;
}

if(!$foundCaptcha){
    http_response_code(500);
    die('No captcha found');
}

$result = array('captchaId' => $captchaId);
header("Content-type: application/json; charset=utf-8");
echo json_encode($result);
?>