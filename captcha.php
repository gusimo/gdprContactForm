<?php
require_once 'config.inc.php';
require_once 'dbinit.php';
require_once dirname(__FILE__).'/securimage/securimage.php';

if(!isset($_GET['id'])){
    die('no id');
}

$captchaId = $_GET['id'];

if (empty($captchaId)) {
    die('no id');
}

// check if it is valid
if(!existsCaptcha($conn,$captchaId)){
    http_response_code(400);
    die('Captcha not found');
}

$options = array('captchaId' => $captchaId);
$captcha = new Securimage($options);

$captcha->show();
exit;

?>