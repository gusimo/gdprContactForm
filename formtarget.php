<?php
require_once 'config.inc.php';
require_once 'dbinit.php';
require_once dirname(__FILE__).'/securimage/securimage.php';

if(!isset($_POST['id'])
    || !isset($_POST['value'])
    || empty($_POST['id'])
    || empty($_POST['value']))
{
    http_response_code(400);
    die('Invalid captcha data.');
}

$captchaId = $_POST['id'];
$captcha_code = $_POST['value'];

// check if it is valid
if(!existsCaptcha($conn,$captchaId)){
    http_response_code(400);
    die('Captcha not found');
}

$options = array('no_session' => true);
if (Securimage::checkByCaptchaId($captchaId, $captcha_code, $options) != true) {
    http_response_code(403);
    die('Captcha wrong');
}

// code here for successful validation

if(!isset($_POST['name'])
    || !isset($_POST['email'])
    || !isset($_POST['phone'])
    || !isset($_POST['message'])
    || !isset($_POST['accept'])
    || empty($_POST['name'])
    || empty($_POST['email'])
    || empty($_POST['message'])
    || empty($_POST['accept']))
    {
        http_response_code(400);
        die('Missing form data name, email, message or accept');
    }

$email = filter_var ( $_POST['email'],FILTER_VALIDATE_EMAIL);
$name = filter_var ( $_POST['name'],FILTER_SANITIZE_STRING);
$message = filter_var ( $_POST['message'],FILTER_SANITIZE_STRING);
$accept = filter_var ( $_POST['accept'],FILTER_VALIDATE_BOOLEAN);
$phone = filter_var ( $_POST['phone'],FILTER_SANITIZE_STRING);

if(!$email
    || !$name
    || !$message
    || !$accept)
{
    http_response_code(400);
    die('Invalid form data format.');
}

$message="$email\r\n$name\r\n$phone\r\n$message\r\n$accept";

$headers = "From: $mailFrom";

if(!mail($mailTo, $mailSubject, $message, $headers, '-f'.$mailTo)){
    http_response_code(500);
    die('Send mail failed');
}

deleteCaptcha($conn,$captchaId);

http_response_code(200);
//echo($message);
exit;
?>
