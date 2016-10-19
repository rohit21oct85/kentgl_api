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
	$parentId = $_REQUEST['parentId'];
	$customer_name = $_REQUEST['customer_name'];
	$customer_mobile = $_REQUEST['customer_mobile'];
	$address = $_REQUEST['address'];
	$city_id = $_REQUEST['city_id'];
	$state_id = $_REQUEST['state_id'];
	$product_purchased = $_REQUEST['product_purchased'];
	$electrolysis = $_REQUEST['electrolysis'];
	
	$indatetime = date("Y-m-d H:i:s");
	$duration='+5 minutes';
	
	$outdatetime = date("Y-m-d H:i:s",strtotime($duration, strtotime($indatetime)));
	
	$remark = trim(stripcslashes($_REQUEST['remark']));
	
	$data = array(
		"auth_username"=>$auth_uname,
		"auth_passwd"=>$auth_passwd,
		"v_code"=>$app_version,
		"device"=>$device,
		"model"=>$model,
		"userId"=>$userId,
		"parentId"=>$parentId,
		"customer_name"=>$customer_name,
		"customer_mobile"=>$customer_mobile,
		"address"=>$address,
		"city_id"=>$city_id,
		"state_id"=>$state_id,
		"product_purchased"=>$product_purchased,
		"electrolysis"=>$electrolysis,
		"indatetime"=>$indatetime,
		"outdatetime"=>$outdatetime,
		"remark"=>$remark,
		"isActive"=>1,
	);
	$input = json_encode($data);
	
	$res_mob = $db->chkCustMobile($customer_mobile);
	
	if(empty($userId)){
		header('content-type: application/json');
		echo  json_encode(array('result'=>'FALSE','msg'=>'Please Enter userId !'));
	}else if(empty($customer_name)){
		header('content-type: application/json');
		echo  json_encode(array('result'=>'FALSE','msg'=>'Please Enter customer name !'));
	}else if(empty($customer_mobile)){
		header('content-type: application/json');
		echo  json_encode(array('result'=>'FALSE','message'=>'Please Enter Mobile No !'));	
	}else if(empty($address)){
		header('content-type: application/json');
		echo  json_encode(array('result'=>'FALSE','message'=>'Please Enter Customer Address !'));	
	}else if(strlen($customer_mobile) < 10 || strlen($customer_mobile) > 10){
		header('content-type: application/json');
		echo  json_encode(array('result'=>'FALSE','msg'=>'Please Enter Valid Mobile No !'));	
	}else if($res_mob == 1){
		header('content-type: application/json');
		echo  json_encode(array('result'=>'FALSE','msg'=>'Mobile No Already Exists !'));	
	}else{
		
		$data = array(
			"userId"=>$userId,
			"parentId"=>$parentId,
			"customer_name"=>$customer_name,
			"customer_mobile"=>$customer_mobile,
			"address"=>$address,
			"state_id"=>$state_id,
			"city_id"=>$city_id,
			"product_purchased"=>$product_purchased,
			"electrolysis"=>$electrolysis,
			"indatetime"=>$indatetime,
			"outdatetime"=>$outdatetime,
			"isactive"=>1,
			"entryDate"=> $outdatetime,
			"remark"=>$remark
		);
		
		
		$insertdata = $db->insert("tbl_water_test",$data);
		if($insertdata == true){
			$results = array(
					'result'=>'TRUE',
					'msg'=>'Details inserted successfully!'
				);	
		}else{
			$results = array(
					'result'=>'FALSE',
					'msg'=>'Error While inserting customer details !'
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