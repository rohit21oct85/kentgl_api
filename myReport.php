<?php 
	
require_once('DBInterface.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');
$auth_uname = 		trim($_REQUEST['auth_username']);
$auth_passwd = 		trim($_REQUEST['auth_passwd']);
$app_version = trim($_REQUEST['v_code']);
$device = trim($_REQUEST['device']);
$model = trim($_REQUEST['model']);

$res = $db->authUser($auth_uname,$auth_passwd,$app_version);

if($res == 0){
$input = json_encode(array("auth_username"=>$auth_uname,"auth_passwd"=>$auth_passwd,"v_code"=>$app_version,"device"=>$device,"model"=>$model));
$result = array("result"=>"FLASE","msg"=>"Authentication Failed");
$response = json_encode($result);

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
}else if($res == 2){
$input = json_encode(array("auth_username"=>$auth_uname,"auth_passwd"=>$auth_passwd,"v_code"=>$app_version,"device"=>$device,"model"=>$model));	
$result = array("result"=>"FLASE","msg"=>"Please Update New Version");
$response = json_encode($result);

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
}else if($res == 1){

$uid = $_REQUEST['uid'];
if(empty($_REQUEST['fdate'])){
	$fdate = "";
	$tdate = $_REQUEST['tdate'];
}else if(empty($_REQUEST['tdate'])){
	$tdate = "";
	$fdate = $_REQUEST['fdate'];
}else if(empty($_REQUEST['tdate']) && empty($_REQUEST['fdate'])){
	$tdate = "";
	$fdate = "";
}else{
	$fdate = $_REQUEST['fdate'];
	$tdate = $_REQUEST['tdate'];	
}
$data = array("uid"=>$uid,"fdate" => $fdate,"tdate"=>$tdate);
//echo json_encode($data); die;
$results = $db->viewMySaleReport($uid,$fdate,$tdate);
$page_url = $_SERVER['PHP_SELF'];
$input = json_encode(array("auth_username"=>$auth_uname,"auth_passwd"=>$auth_passwd,"v_code"=>$app_version,"device"=>$device,"model"=>$model,"uid"=>$uid,"date" => $date));	
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