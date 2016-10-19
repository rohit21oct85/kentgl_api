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
	
	
$oldPassword = trim($_REQUEST['op']);
$npwd = trim($_REQUEST['npwd']);
$cnpwd = trim($_REQUEST['cnpwd']);
$userId = trim($_REQUEST['ui']);



if(empty($userId)){
	echo json_encode(array("Result"=>"FALSE","msg"=>"please enter UserId"));
}else if(empty($oldPassword)){
	echo json_encode(array("Result"=>"FALSE","msg"=>"please enter old password"));
}else if(empty($npwd)){
	echo json_encode(array("Result"=>"FALSE","msg"=>"please enter new password"));
}else if(empty($cnpwd)){
	echo json_encode(array("Result"=>"FALSE","msg"=>"please enter confirm new password"));
}else{
	$res = $db->chkoldPassword($oldPassword,$userId);	
	if($res == 0){
		echo json_encode(array("Result"=>"FALSE","msg"=>"Old password not match"));
	}else if($npwd != $cnpwd){
		echo json_encode(array("Result"=>"FALSE","msg"=>"Your password not matched"));
	}else{
		$data = array("password"=>$npwd);
		$con = array("userId"=>$userId);
		$up = $db->update("tbl_user_master", $data, $con);
		if($up == 1){
			$response = json_encode(array("Result"=>"TRUE","msg"=>"Password changed successfully !"));	
			$input = json_encode(array("auth_username"=>$auth_uname,"auth_passwd"=>$auth_passwd,"v_code"=>$app_version,"device"=>$device,"model"=>$model,"password"=>$npwd,"userId"=>$userId));	
			
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
		}else{
			echo json_encode(array("Result"=>"FALSE","msg"=>"Error in change password"));
		}
	}
}

}

?>