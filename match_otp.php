<?php 
require_once('DBInterface.php');
$db = new Database();
$db->connect();

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
header('content-type:application/json');
echo $response;
}else if($res == 1){

$otp =  $_REQUEST['otp'];
if(empty($otp)){
	echo json_encode(array("result"=>"FALSE","msg"=>"Enter Your OTP"));
}else{

	$otp_data = array(
		'otp_status' => 1
	);
	$condition = array(
		'OTP'=>$otp	
	);
	
	$res = $db->checkOTPStaus($otp);
	if($res == 1){
		$updateotp = $db->update("tbl_user_master", $otp_data, $condition);
		$details = $db->verifiedOTP($auth_uname, $auth_passwd);
		header('content-type:application/json');
		echo json_encode($details);
	}else{
		header('content-type:application/json');
		echo json_encode(array("result"=>"FALSE","msg"=>"OTP NOT Matched"));
	}
	

}

}

?>