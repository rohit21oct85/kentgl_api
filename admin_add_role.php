<?php 
$input = file_get_contents('php://input');
/*$input = '{
			"roleName":"GL_TEST",
			"roleDis":"Testing",
			"isActive":"1"
		  }';*/
if($input!=""){
require_once('DBInterface.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');
$arr = json_decode($input,true);

$roleName = $db->clean_input($arr['roleName']);
$roleDis = $db->clean_input($arr['roleDis']);
$isActive = $db->clean_input($arr['isActive']);

$data = array(
		"roleName"=>$roleName,
		"roleDis"=>$roleDis,
		"isActive"=>$isActive
);
$input = json_encode($data);
	
if(empty($roleName)){
	echo  json_encode(array('result'=>'FALSE','message'=>'Please Enter Role Name !'));
}else if(empty($roleDis)){
	echo  json_encode(array('result'=>'FALSE','message'=>'Please Enter Role Discription!'));
}else{
	
	$data = array(
		"roleName"=>$roleName,
		"roleDiscription"=>$roleDis,
		"isActive"=>$isActive
	);
	
	$chkRole = $db->checkRole($roleName);	
	if($chkRole == 0){
		$insertDataUserTable = $db->insert("tbl_role_master",$data);
		if($insertDataUserTable == true){
	
		$results = array(
				'result'=>'TRUE',
				'message'=>'Role Created Successfully!'
			);	
		}else{
			$results = array(
					'result'=>'FALSE',
					'message'=>'Error While creating Role!'
				);
		}
	}else{
		$results = array(
				'result'=>'FALSE',
				'message'=>'Role Already Exists!'
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