<?php
session_start();

if ($_POST['captcha'] == $_SESSION['captcha']) {
    echo 'success';
} else {
    echo 'fail';
}
?>