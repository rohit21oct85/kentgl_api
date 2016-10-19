<?php 
$input = file_get_contents('php://input');
//$input = '{"productId":"2/*4","productName":"Kent%Ultra","productDiscription":"3-Stage Water Purifier","isActive":"0"}';
if($input!=""){
require_once('DBInterface.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');
$arr = json_decode($input,true);

$name = $db->clean_input($arr['name']);
$email = $db->clean_input_email($arr['email']);
$mobile = $db->clean_input($arr['mobile']);	
$roleid = $db->clean_input($arr['roleId']);
$pid = $db->clean_input($arr['reporting_to']);
$state = $db->clean_input($arr['state']);
$city = $db->clean_input($arr['city']);
$isActive = $db->clean_input($arr['status']);
$dis_id = $db->clean_input($arr['dis_id']);
$woff = $db->clean_input($arr['woff']);	

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
	$results = array('result'=>'FALSE','message'=>'Please Enter Name !');
}else if(empty($email)){
	$results = array('result'=>'FALSE','message'=>'Please Enter Email id !');
}else if($res_email == 1){
	$results= array('result'=>'FALSE','message'=>'Email id Already Exists !');
}else if(empty($mobile)){
	$results = array('result'=>'FALSE','message'=>'Please Enter Mobile No !');	
}else if(strlen($mobile) < 10 || strlen($mobile) > 10){
	$results= array('result'=>'FALSE','message'=>'Please Enter Valid Mobile No!');	
}else if($res_mob == 1){
	$results = array('result'=>'FALSE','message'=>'Mobile No Already Exists !');	
}else{
	
	
	
	$data = array(
		"userName"=>$name,
		"email"=>$email,
		"mobileno"=>$mobile,
		"password" =>$pass,
		"roleId"=>$roleid,
		"isActive"=>$isActive,
		"state"=>$state,
		"weekOff"=>$woff,
		"city"=>$city,
		"cb"=>$pid
	);
	
	$insertDataUserTable = $db->insert("tbl_user_master",$data);
	
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


			$dist_id = $db->chkDistributorCode($userid);

			if($dist_id == 1){
			
			$data = array("dis_id"=>$dis_id);
			$con = array("userId"=>$userid);
			$update = $db->update("tbl_distributor",$data,$con);

			if($update == true){

				$results = array(
					'result'=>'TRUE',
					'message'=>'Gl Created successfully!'
				);	

			}else{

				$results = array(
					'result'=>'FALSE',
					'message'=>'Error While Creating Gl Profile !'
				);

			}	

		}else{
			
			$data = array("userId"=>$userid,"dis_id"=>$dis_id);

			$insert = $db->insert("tbl_distributor",$data);
			
			if($insert == true){

				$results = array(
					'result'=>'TRUE',
					'message'=>'Gl Create successfully!'
				);	

			}else{

				$results = array(
					'result'=>'FALSE',
					'message'=>'Error While Creating Gl Profile !'
				);

			}	
			

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