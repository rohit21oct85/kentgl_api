<?php 
$input = file_get_contents('php://input');
//$input = '{"name":"Rohit$#$#Singh","email":"rohit.singh@gmail.com","mobile":"8470056990","roleid":"$4","parentId":"3","state":"Delhi","City":"New Delhi","isActive":"1"}';
if($input!=""){
require_once('DBInterface.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');
$arr = json_decode($input,true);

$name = $db->clean_input($arr['name']);
$email = $db->clean_input_email($arr['email']);
$mobile = $db->clean_input($arr['mobile']);
$roleid = $db->clean_input($arr['roleid']);
$pid = $db->clean_input($arr['parentId']);
$state = $db->clean_input($arr['state']);
$city = $db->clean_input($arr['city']);
$isActive = $db->clean_input($arr['isActive']);
$pwd = explode("@", $email);

$pass = $pwd[0];


$data = array(
		"userName"=>$name,
		"email"=>$email,
		"mobileno"=>$mobile,
		"roleId"=>$roleid,
		"isActive"=>$isActive,
		"state"=>$state,
		"city"=>$city,
		"cb"=>$pid
);
$input = json_encode($data);
	
$res_email = $db->chkEmail($email);
$res_mob = $db->chkMobile($mobile);

if(empty($name)){
	echo  json_encode(array('result'=>'FALSE','message'=>'Please Enter Name !'));
}else if(empty($email)){
	echo  json_encode(array('result'=>'FALSE','message'=>'Please Enter Email id !'));
}else if($res_email == 1){
	echo  json_encode(array('result'=>'FALSE','message'=>'Email id Already Exists !'));
}else if(empty($mobile)){
	echo  json_encode(array('result'=>'FALSE','message'=>'Please Enter Mobile No !'));	
}else if(strlen($mobile) < 10 || strlen($mobile) > 10){
	echo  json_encode(array('result'=>'FALSE','message'=>'Please Enter Valid Mobile No!'));	
}else if($res_mob == 1){
	echo  json_encode(array('result'=>'FALSE','message'=>'Mobile No Already Exists !'));	
}else{
	
	$data = array(
		"userName"=>$name,
		"email"=>$email,
		"mobileno"=>$mobile,
		"password" =>$pass,
		"roleId"=>$roleid,
		"isActive"=>$isActive,
		"state"=>$state,
		"city"=>$city,
		"cb"=>$pid
	);
	
	$insertDataUserTable = $db->insert("tbl_user_master",$data);
	if($insertDataUserTable == true){
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
				'message'=>'User created successfully!'
			);	
	}else{
		$results = array(
				'result'=>'FALSE',
				'message'=>'Error While Adding hierarchy !'
			);	
	}
	
	
	}else{
		$results = array(
				'result'=>'FALSE',
				'message'=>'Error While Adding User table!'
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