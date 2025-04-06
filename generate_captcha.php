<?php
session_start();

$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$captcha_code = '';
for ($i = 0; $i < 5; $i++) {
    $captcha_code .= $characters[rand(0, strlen($characters) - 1)];
}
$_SESSION['captcha'] = $captcha_code;

echo $captcha_code;
?>