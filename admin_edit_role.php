<?php 
$input = file_get_contents('php://input');
/*$input = '{
			"roleId":"8",
			"roleName":"GL_TEST",
			"roleDis":"Testing role created",
			"isActive":"0"
		  }';*/
if($input!=""){
require_once('DBInterface.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');
$arr = json_decode($input,true);
$roleId = $arr['roleId'];
$roleName = $arr['roleName'];
$roleDis = $arr['roleDis'];
$isActive = $arr['isActive'];

$data = array(
		"roleId"=>$roleId,
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
	$condition = array("roleId"=>$roleId);
	$update_role = $db->update("tbl_role_master",$data, $condition);
	if($update_role == true){
	
		$results = array(
				'result'=>'TRUE',
				'message'=>'Role Updated successfully!'
			);	
		}else{
			$results = array(
					'result'=>'FALSE',
					'message'=>'Error While Updating Role!'
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