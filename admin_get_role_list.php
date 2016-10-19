<?php 
$input = file_get_contents('php://input');
//$input = '{"action":"role"}';
if($input!=""){
require_once('DBInterface.php');
$db = new Database();
$db->connect();
$arr = json_decode($input,true);

$action = trim($arr['action']);
$inputdata = array("action"=>$action);
$input = json_encode($inputdata);

$results = $db->viewRole($action);
$response = json_encode($results);
		$page_url = $_SERVER['PHP_SELF'];
		$applog_data = array(
			'service_url' => $page_url,
			'request'	=> $input,
			'response' => $response,
			'entryDate' => date("Y-m-d h:i:s")
		);
		$insert_log = $db->insert("tbl_app_log", $applog_data);
header('content-type: application/json');
echo $results;
}