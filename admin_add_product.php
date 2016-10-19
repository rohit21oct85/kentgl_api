<?php 
$input = file_get_contents('php://input');
//$input = '{"productName":"Kent$)(#Ultra","productDiscription":"3-Stage Water Purifier","isActive":"1"}';
if($input!=""){
require_once('DBInterface.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');
$arr = json_decode($input,true);

$productCode = $db->clean_input($arr['productCode']);
$productName = $db->clean_input($arr['productName']);
$productDiscription = $db->clean_input($arr['productDiscription']);
$isActive = $arr['isActive'];

$data = array(
		"productCode"=>$productCode,
		"productName"=>$productName,
		"productDiscription"=>$productDiscription,
		"isActive"=>$isActive,
		"entryDate"=>date("Y-m-d")
);
$input = json_encode($data);
//echo $input; die;	
if(empty($productName)){
	echo  json_encode(array('result'=>'FALSE','message'=>'Please enter product Name!'));
}else if(empty($productDiscription)){
	echo  json_encode(array('result'=>'FALSE','message'=>'Please enter product discription!'));
}else{
	
	$data = array(
		"product_code"=>$productCode,
		"productName"=>$productName,
		"productDiscription"=>$productDiscription,
		"isActive"=>$isActive,
		"entryDate"=>date("Y-m-d")
	);
	
	$chkProduct = $db->checkProduct($productName);	
	if($chkProduct == 0){
		
		$insertProduct = $db->insert("tbl_product_master",$data);
		if($insertProduct == true){
	
		$results = array(
				'result'=>'TRUE',
				'message'=>'Product added successfully!'
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
				'message'=>'Product Already Exists!'
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