<?php 
require_once('DBInterface.php');
$db = new Database();
$db->connect();

$otp =  mt_rand(5, 9999);
$text_msg = "Please enter your OTP: ".$otp;
$text_msg = trim(str_replace(' ', '%20', $text_msg));
$mobile = "8470051985";

$response = $db->sendMsg($mobile,$text_msg);



?>