<?php 
$input = file_get_contents('php://input');
//$input = '{"pid":"2"}';
if($input!=""){
require_once('DBInterface.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');
$arr = json_decode($input,true);
$pid = $arr['pid'];

$data = array("pid"=>$pid);

$results = $db->adminSaleReport($pid);
$page_url = $_SERVER['PHP_SELF'];
$input = json_encode($data);
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