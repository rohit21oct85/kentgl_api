<?php 
$input = file_get_contents('php://input');
//$input = '{"action":"state"}';
if($input!=""){
require_once('DBInterface.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');
$arr = json_decode($input,true);

$state = $arr['action'];

$response = $db->selectState($state);

 

$page_url = $_SERVER['PHP_SELF'];
$applog_data = array(
	'service_url' => $page_url,
	'request'	=> $input,
	'response' => $response,
	'entryDate' => date("Y-m-d h:i:s")
);
$insert_log = $db->insert("tbl_app_log", $applog_data);
header('content-type: application/json');
echo $response;

}
?>