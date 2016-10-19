<?php 
$input = file_get_contents('php://input');
//$input = '{"glid":"22"}';
if($input!=""){
require_once('DBInterface.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');

$arr = json_decode($input,true);
$glid = $arr['glid'];


$data = array(
	"glid" => $glid
);
$results = $db->getAllSalePerson_admin($glid);

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
