<?php 
$input = file_get_contents('php://input');
//$input = '{"dis_id":"12","distributerCode":"KT_750","distributer_name":"Water Purifier Supplier","isActive":"0"}';
if($input!=""){
require_once('DBInterface.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');
$arr = json_decode($input,true);
$distributerId = $db->clean_input($arr['dis_id']);
$distributerCode = $db->clean_input($arr['distributerCode']);
$distributer_name = $db->clean_input($arr['distributer_name']);
$state = $db->clean_input($arr['d_state']);
$city = $db->clean_input($arr['d_city']);
$isActive = $db->clean_input($arr['isActive']);

$data = array(
		"dis_id"=>$distributerId,
		"dc"=>$distributerCode,
		"distributer_name"=>$distributer_name,
		"d_state"=> $state,
		"d_city"=> $city,
		"isActive"=>$isActive
);
$input = json_encode($data);
	
if(empty($distributerCode)){
	echo  json_encode(array('result'=>'FALSE','message'=>'Please Enter distributer Code !'));
}else if(empty($distributer_name)){
	echo  json_encode(array('result'=>'FALSE','message'=>'Please Enter distributer Name!'));
}else{
	
	$data = array(
		"dc"=>$distributerCode,
		"distributer_name"=>$distributer_name,
		"d_state"=> $state,
		"d_city"=> $city,
		"isActive"=>$isActive
	);
	$condition = array("dis_id"=>$distributerId);
	$update_role = $db->update("tbl_distributor_master",$data, $condition);
	if($update_role == true){
	
		$results = array(
				'result'=>'TRUE',
				'message'=>'Distributor Updated successfully!'
			);	
		}else{
			$results = array(
					'result'=>'FALSE',
					'message'=>'Error While Updating Product name!'
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