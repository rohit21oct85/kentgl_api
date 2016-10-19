<?php 
$input = file_get_contents('php://input');
/*$input = '{
			"userId":"48",
			"name":"Rohit singh",
			"email":"rohit.s@gmail.com",
			"mobile":"8470051990",
			"roleid":"4",
			"parentId":"4",
			"state":"Delhi",
			"City":"New Delhi",
			"isActive":"1"
		  }';*/
if($input!=""){
require_once('DBInterface.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');
$arr = json_decode($input,true);

$userId = $db->clean_input($arr['userId']);
$name = $db->clean_input($arr['name']);
$email = $db->clean_input_email($arr['email']);
$mobile = $db->clean_input($arr['mobile']);
$roleid = $db->clean_input($arr['roleid']);
$pid = $db->clean_input($arr['parentId']);
$state = $db->clean_input($arr['state']);
$city = $db->clean_input($arr['city']);
$isActive = $db->clean_input($arr['isActive']);
$zone = $db->clean_input($arr['zone']);
$code = $db->clean_input($arr['code']);


$data = array(
		"userId" => $userId,
		"userName"=>$name,
		"email"=>$email,
		"mobileno"=>$mobile,
		"roleId"=>$roleid,
		"isActive"=>$isActive,
		"state"=>$state,
		"city"=>$city,
		"cb"=>$pid,
		"Zone"=>$zone,
		"emp_code"=>$code
);
$input = json_encode($data);
	

if(empty($name)){
	echo  json_encode(array('result'=>'FALSE','message'=>'Please Enter Name !'));
}else if(empty($email)){
	echo  json_encode(array('result'=>'FALSE','message'=>'Please Enter Email id !'));
}else if(empty($mobile)){
	echo  json_encode(array('result'=>'FALSE','message'=>'Please Enter Mobile No !'));	
}else if(strlen($mobile) < 10 || strlen($mobile) > 10){
	echo  json_encode(array('result'=>'FALSE','message'=>'Please Enter Valid Mobile No !'));	
}else{
	
	$data = array(
		"userName"=>$name,
		"email"=>$email,
		"mobileno"=>$mobile,
		"roleId"=>$roleid,
		"isActive"=>$isActive,
		"state"=>$state,
		"city"=>$city,
		"cb"=>$pid,
		"Zone"=>$zone,
		"emp_code"=>$code
	);
	$condition = array("userId"=>$userId);
	
	//echo json_encode($data);die;
	
	$editUser = $db->update("tbl_user_master",$data,$condition);
	if($editUser == true){
	$mid = $db->getManagementId($pid);
	$panid = $db->getPanId($pid);
	$panmid = 214;
			
	$data_hierarchy = array(
		"userId"=>$userId,
		"parentUserId"=>$pid,
		"mid"=>$mid,
		"panid"=>$panid,
		"panmid"=>$panmid,
		"isActive"=>1,
		"enteryDate"=>date("Y-m-d h:i:s")
	);
	$condition = array("userId"=>$userId);	
	$updatedata = $db->update("tbl_user_hierarchy", $data_hierarchy, $condition);
	
	if($updatedata == true){
		$results = array(
				'result'=>'TRUE',
				'message'=>'Update User successfully!'
			);	
	}else{
		$results = array(
				'result'=>'FALSE',
				'message'=>'Error While updating hierarchy !'
			);	
	}
	$response = json_encode($results);
	
	}else{
		$results = array(
				'result'=>'FALSE',
				'message'=>'Error While Updating User table!'
			);
	}
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