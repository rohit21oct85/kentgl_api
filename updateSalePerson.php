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
	
	$userId = $_REQUEST['userId'];
	
	$name = $_REQUEST['name'];
	$email = $_REQUEST['email'];
	$mobile = $_REQUEST['Mobile'];	
	$isActive = $_REQUEST['isActive'];
	$weekOff = $_REQUEST['weekOff'];
	$passwd = $_REQUEST['passwd'];
	
	$chkStatus = $db->chkMobile($mobile);
	
	if(empty($name)){
		header('content-type: application/json');
		echo  json_encode(array('result'=>'FALSE','msg'=>'Please Enter Name !'));
	}else if(empty($mobile)){
		header('content-type: application/json');
		echo  json_encode(array('result'=>'FALSE','msg'=>'Please Enter Mobile No !'));	
	}else if(strlen($mobile) < 10 || strlen($mobile) > 10){
		header('content-type: application/json');
		echo  json_encode(array('result'=>'FALSE','msg'=>'Please Enter Valid Mobile No !'));	
	}else if(empty($weekOff)){
		header('content-type: application/json');
		echo  json_encode(array('result'=>'FALSE','msg'=>'Please Select Weekoff !'));
	}else{
		
		
		if($chkStatus == 0 ){
			$otp_status = 0;
		}else{
			$otp_status = 1;
		}
		$data = array(
			"userName"=>$name,
			"email"=>$email,
			"mobileno"=>$mobile,
			"isActive"=>$isActive,
			"weekOff"=>$weekOff,
			"password"=>$passwd,
			"otp_status"=>$otp_status
		);
		$condition = array(
			"userId"=>$userId
		);
		$updateData = $db->update("tbl_user_master",$data, $condition);
		
		if($updateData == true){
			$results = array(
					'result'=>'TRUE',
					'message'=>'Saleperson profile Updated successfully!'
				);	
		}else{
			$results = array(
					'result'=>'FALSE',
					'message'=>'Error While Updating Saleperson profile!'
				);	
		}
		$response = json_encode($results);
		$page_url = $_SERVER['PHP_SELF'];
		$input = json_encode(array("auth_username"=>$auth_uname,"auth_passwd"=>$auth_passwd,"v_code"=>$app_version,"device"=>$device,"model"=>$model,"userName"=>$name,
			"email"=>$email,"mobileno"=>$mobile,"isActive"=>$isActive,"weekOff"=>$weekOff,
			"password"=>$passwd,"userId"=>$userId));	
		
		$applog_data = array(
			'service_url' => $page_url,
			'request'	=> $input,
			'response' => $response,
			'entryDate' => date("Y-m-d h:i:s")
		);
		$insert_log = $db->insert("tbl_app_log", $applog_data);
		echo $response;
	}
	
}
?>