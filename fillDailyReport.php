<?php 
$input = file_get_contents('php://input');

$input = $_REQUEST['data'];

//'{"auth_username":"8470051985","auth_passwd":"rohit@123456","v_code":"1.0.1","device":"","model":"","pid":"7","uid":"9","weekOff":"Friday","att":"0","demo":"0","sale":"0","saleProduct":[]}';

if($input!=""){
require_once('DBInterface.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');
$arr = json_decode($input,true);
//print_r($arr);


$auth_uname = 		trim($arr['auth_username']);
$auth_passwd = 		trim($arr['auth_passwd']);
$app_version = trim($arr['v_code']);
$device = trim($arr['device']);
$model = trim($arr['model']);

$res = $db->authUser($auth_uname,$auth_passwd,$app_version);

if($res == 0){
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

}else{



$uid = $arr['uid'];
$att = $arr['att'];
$demo = $arr['demo'];
$sale = $arr['sale'];
$reportDate = date("Y-m-d h:i:s");
$pid = $arr['pid'];
$weekOff = $arr['weekOff'];
$currentday = date("l");

if($att == 0){
	if($weekOff == $currentday){
		$att = 2;
	}else{
		$att = 0;
	}
}
$data_dailyreport = array(
	"reportDate"=>$reportDate,
	"userId"=>$uid,
	"attendance"=> $att,
	"noOfDemo"=> $demo,
	"noOfSales"=> $sale,
	"cb"=>$pid
);

$chkentry = $db->chkdailyReportInsert($uid,$pid);

if($chkentry == 0){
	
$insert_dailyReport = $db->insert("tbl_daily_report", $data_dailyreport);
if($insert_dailyReport == true){
	if($sale > 0){
		$dailyRepId = $db->getLastInsertedProduct($pid,$uid);
	$productNO = $arr['saleProduct'];
	$prefix = '';
	foreach($productNO as $key => $values){
		$pid = $values['productId'];
		$qua =	$values['quantity'];
		$data = array(
			"dailyReportId" => $dailyRepId,
			"productId" => $pid,
			"quantitySale" => $qua,
			"isActive" => 1 , 
			"entryDate" => date("Y-m-d h:i:s")
		);
		$insert = $db->insert("tbl_sales_report", $data);
	}
	
	if($insert == true)
	{
		$response = array('result'=>'TRUE','msg'=>'Successfully uploaded daily report');
		
	}else{
		$response = array('result'=>'FALSE','msg'=>'Error in Fill daily report');
	}
	}else{
		$response = array('result'=>'TRUE','msg'=>'daily report Updated Successfully');
	}
	
	
}else{
	$response = array('result'=>'FALSE','msg'=>'Error in Fill daily report');
}
$results = json_encode($response);
$page_url = $_SERVER['PHP_SELF'];
		$applog_data = array(
			'service_url' => $page_url,
			'request'	=> $input,
			'response' => $results,
			'entryDate' => date("Y-m-d h:i:s")
		);
$insert_log = $db->insert("tbl_app_log", $applog_data);
header('content-type: application/json');
echo $results;
}else{
	header('content-type: application/json');
	echo json_encode(array('result'=>'FALSE','msg'=>'You Cannot Update daily report twice' ));
}

}

}

