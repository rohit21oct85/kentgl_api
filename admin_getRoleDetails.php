<?php 
$input = file_get_contents('php://input');
//$input = '{"role_id":"1"}';
if($input!=""){
require_once('DBInterface.php');
$db = new Database();
$db->connect();
$arr = json_decode($input,true);
$role_id = trim($arr['role_id']);
$results = $db->getRoleDetails($role_id);
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
?>