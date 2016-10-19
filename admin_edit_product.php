<?php 
$input = file_get_contents('php://input');
//$input = '{"productId":"24","productName":"Kent Ultra","productDiscription":"3-Stage Water Purifier","isActive":"0"}';
if($input!=""){
require_once('DBInterface.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');
$arr = json_decode($input,true);
$productCode = $db->clean_input($arr['productCode']);
$productId = $db->clean_input($arr['productId']);
$productName = $db->clean_input($arr['productName']);
$productDiscription = $db->clean_input($arr['productDiscription']);
$isActive = $arr['isActive'];

$data = array(
		"productCode"=> $productCode,
		"productId"=>$productId,
		"productName"=>$productName,
		"productDiscription"=>$productDiscription,
		"isActive"=>$isActive
);
$input = json_encode($data);
	
if(empty($productName)){
	echo  json_encode(array('result'=>'FALSE','message'=>'Please Enter Product Name !'));
}else if(empty($productDiscription)){
	echo  json_encode(array('result'=>'FALSE','message'=>'Please Enter product Discription!'));
}else{
	
	$data = array(
		"product_code"=> $productCode,
		"productName"=>$productName,
		"productDiscription"=>$productDiscription,
		"isActive"=>$isActive
		
	);
	$condition = array("productId"=>$productId);
	$update_role = $db->update("tbl_product_master",$data, $condition);
	if($update_role == true){
	
		$results = array(
				'result'=>'TRUE',
				'message'=>'product Updated successfully!'
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