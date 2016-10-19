<?php 
$input = file_get_contents('php://input');
//$input = '{"action":"reporting","role_id":"4"}';
if($input!=""){
require_once('DBInterface.php');
$db = new Database();
$db->connect();
$arr = json_decode($input,true);

$action = trim($arr['action']);
$roleId = $arr['role_id'];

$results = $db->getReportingName($roleId);

$inputdata = array("action"=>$action,"role_id"=>$roleId);
$input = json_encode($inputdata);
$page_url = $_SERVER['PHP_SELF'];
$applog_data = array(
	'service_url' => $page_url,
	'request'	=> $input,
	'response' => $results,
	'entryDate' => date("Y-m-d h:i:s")
);
//$insert_log = $db->insert("tbl_app_log", $applog_data);
header('content-type: application/json');
echo $results;
}
