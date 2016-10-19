<?php 
$input = file_get_contents('php://input');
//$input = '{"distributerCode":"KT_750","distributer_name":"Water Purifier","isActive":"1"}';
if($input!=""){
require_once('DBInterface.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');
$arr = json_decode($input,true);

$distributerCode = $db->clean_input($arr['distributerCode']);
$distributer_name = $db->clean_input($arr['distributer_name']);
$state = $db->clean_input($arr['d_state']);
$city = $db->clean_input($arr['d_city']);

$isActive = $db->clean_input($arr['isActive']);

$data = array(
		"distributerCode"=>$distributerCode,
		"distributer_name"=>$distributer_name,
		"d_state"=> $state,
		"d_city"=> $city,
		"isActive"=>$isActive,
		"entryDate"=>date("Y-m-d")
);
$input = json_encode($data);
	
if(empty($distributerCode)){
	echo  json_encode(array('result'=>'FALSE','message'=>'Please enter product Name!'));
}else if(empty($distributer_name)){
	echo  json_encode(array('result'=>'FALSE','message'=>'Please enter product discription!'));
}else{
	
	$data = array(
		"dc"=>$distributerCode,
		"distributer_name"=>$distributer_name,
		"d_state"=> $state,
		"d_city"=> $city,
		"isActive"=>$isActive,
		"entryDate"=>date("Y-m-d")
	);
	
	$chkProduct = $db->chkDCode($distributerCode);	
	if($chkProduct == 0){
		
		$insertProduct = $db->insert("tbl_distributor_master",$data);
		if($insertProduct == true){
	
		$results = array(
				'result'=>'TRUE',
				'message'=>'Distributr added successfully!'
			);	
		}else{
			$results = array(
					'result'=>'FALSE',
					'message'=>mysql_error()
				);
		}
	}else{
			$results = array(
				'result'=>'FALSE',
				'message'=>'Distributor Already Exists!'
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