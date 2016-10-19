<?php 
$input = file_get_contents('php://input');
//$input = '{"dis_id":"5"}';
if($input!=""){
require_once('DBInterface.php');
$db = new Database();
$db->connect();
$arr = json_decode($input,true);
$dis_id = trim($arr['dis_id']);
$results = $db->getDistributorDetails($dis_id);
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