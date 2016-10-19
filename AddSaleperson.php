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


	$name = $_REQUEST['name'];
	$email = $_REQUEST['email'];
	$mobile = $_REQUEST['Mobile'];
	$roleid = $_REQUEST['roleid'];
	$isActive = $_REQUEST['isActive'];
	$weekOff = $_REQUEST['weekOff'];
	$username = $_REQUEST['username'];
	$passwd = $_REQUEST['passwd'];
	$pid = $_REQUEST['parentId'];
	$zone = $_REQUEST['zone'];
	$city_id = $_REQUEST['city_id'];
	$state_id = $_REQUEST['state_id'];
	
	$data = array(
		"auth_username"=>$auth_uname,
		"auth_passwd"=>$auth_passwd,
		"v_code"=>$app_version,
		"device"=>$device,
		"model"=>$model,
		"name"=>$name,
		"email"=>$email,
		"Mobile"=>$mobile,
		"roleid"=>$roleid,
		"isActive"=>$isActive,
		"weekOff"=>$weekOff,
		"username"=>$username,
		"passwd"=>$passwd,
		"parentId"=>$pid,
		"zone"=>$zone,
		"city_id"=>$city_id,
		"state_id"=>$state_id
	);
	$input = json_encode($data);
	
	$res_email = $db->chkEmail($email);
	$res_mob = $db->chkMobile($mobile);
	
	if(empty($name)){
		header('content-type: application/json');
		echo  json_encode(array('result'=>'FALSE','msg'=>'Please Enter Name !'));
	}else if(empty($mobile)){
		header('content-type: application/json');
		echo  json_encode(array('result'=>'FALSE','message'=>'Please Enter Mobile No !'));	
	}else if(strlen($mobile) < 10 || strlen($mobile) > 10){
		header('content-type: application/json');
		echo  json_encode(array('result'=>'FALSE','msg'=>'Please Enter Valid Mobile No !'));	
	}else if($res_mob == 1){
		header('content-type: application/json');
		echo  json_encode(array('result'=>'FALSE','msg'=>'Mobile No Already Exists !'));	
	}else if(empty($weekOff)){
		header('content-type: application/json');
		echo  json_encode(array('result'=>'FALSE','msg'=>'Please Select Weekoff !'));
	}else if(empty($username)){
		header('content-type: application/json');
		echo  json_encode(array('result'=>'FALSE','msg'=>'Please Enter username !'));
	}else if(empty($passwd)){
		header('content-type: application/json');
		echo  json_encode(array('result'=>'FALSE','msg'=>'Please Enter password !'));
	}else{
		
		$data = array(
			"userName"=>$name,
			"email"=>$email,
			"mobileno"=>$mobile,
			"roleId"=>$roleid,
			"isActive"=>$isActive,
			"password"=>$passwd,
			"weekOff"=>$weekOff,
			"cb"=>$pid,
			"zone"=>$zone,
			"city"=>$city_id,
			"state"=>$state_id
		);
		$db->insert("tbl_user_master",$data);
		$userid = $db->getLastUser($pid);
		$mid = $db->getManagementId($pid);
		$panid = $db->getPanId($pid);
		$panmid = 214;
		
		$data_hierarchy = array(
				"userId"=>$userid,
				"parentUserId"=>$pid,
				"mid"=>$mid,
				"panid"=>$panid,
				"panmid"=>$panmid,
				"isActive"=>1,
				"enteryDate"=>date("Y-m-d h:i:s")
			);
				
		$insertdata = $db->insert("tbl_user_hierarchy", $data_hierarchy);
		if($insertdata == true){
			$results = array(
					'result'=>'TRUE',
					'msg'=>'User created successfully!'
				);	
		}else{
			$results = array(
					'result'=>'FALSE',
					'msg'=>'Error While creating User!'
				);	
		}
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
		echo $response;
	}

	
	
}	

?>