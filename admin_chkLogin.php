<?php
$input = file_get_contents('php://input');
//$input = '{"username":"rohit21oct85@gmail.com","passwd":"123456"}';
if($input!=""){
require_once('DBInterface.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');
$arr = json_decode($input,true);

$user_name = trim($arr['username']);
$passwd 	= trim($arr['passwd']);

$page_url = $_SERVER['PHP_SELF'];

$data = array(	
		"username" => $user_name,
		"passwd" => $passwd
	);
$input = json_encode($data);

$results = $db->admin_chkLogin($user_name,$passwd);


$applog_data = array(
	'service_url' => $page_url,
	'request'	=> $input,
	'response' => $results,
	'entryDate' => date("Y-m-d h:i:s")
);
$insert_log = $db->insert("tbl_app_log", $applog_data);
header('content-type: application/json');
echo $results;
}
?>