<?php 
$input = file_get_contents('php://input');
//$input = '{"productId":"24","productName":"Kent Ultra","productDiscription":"3-Stage Water Purifier","isActive":"0"}';
if($input!=""){
require_once('DBInterface.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');
$arr = json_decode($input,true);

$glid = $db->clean_input($arr['glid']);

$name = $db->clean_input($arr['name']);
$email = $db->clean_input_email($arr['email']);
$mobile = $db->clean_input($arr['mobile']);	
$reporting_to = $db->clean_input($arr['reporting_to']);
$state = $db->clean_input($arr['state']);
$city = $db->clean_input($arr['city']);
$status = $db->clean_input($arr['status']);
$dis_id = $db->clean_input($arr['dis_id']);
$woff = $db->clean_input($arr['woff']);					
$zone = $db->clean_input($arr['zone']);
$code = $db->clean_input($arr['code']);

$data = array(
		"glid" => $arr['glid'],
		"name" =>$arr['name'],
		"email" => $arr['email'],
		"mobile"=>$arr['mobile'],	
		"reporting_to"=>$arr['reporting_to'],
		"state"=>$arr['state'],
		"city"=>$arr['city'],
		"status"=>$arr['status'],
		"dis_id"=> $arr['discode'],
		"woff"=> $arr['woff'],
		"Zone"=>$zone,
		"emp_code"=>$code	
);
$input = json_encode($data);


if(empty($woff)){
	echo  json_encode(array('result'=>'FALSE','message'=>'Please Select weekoff!'));
}else{
	
	$data = array(
		"weekoff"=>$woff,
		"userName"=>$name,
		"email"=>$email,
		"mobileno"=>$mobile,
		"isActive"=>$status,
		"state"=>$state,
		"city"=>$city,
		"cb"=>$reporting_to,
		"Zone"=>$zone,
		"emp_code"=>$code
	);

	$condition = array("userId"=>$glid);
	//echo json_encode($condition); die;
	$update_gl = $db->update("tbl_user_master",$data, $condition);
	$mid = $db->getManagementId($reporting_to);
	$panid = $db->getPanId($reporting_to);
	$panmid = 214;
	
		$data_hierarchy = array(
			"parentUserId"=>$reporting_to,
			"mid"=>$mid,
			"panid"=>$panid,
			"panmid"=>$panmid,
			"isActive"=>1,
			"enteryDate"=>date("Y-m-d h:i:s")
		);
		$condition = array("userId"=>$glid);	
		$updatedata = $db->update("tbl_user_hierarchy", $data_hierarchy, $condition);
		
	 	$dist_id = $db->chkDistributorCode($glid);
	
		
		if($dist_id == 0){

			$data = array("userId"=>$glid,"dis_id"=>$dis_id);

			$insert = $db->insert("tbl_distributor",$data);
			
			if($insert == true){

				$results = array(
					'result'=>'TRUE',
					'message'=>'Gl Updated successfully!'
				);	

			}else{

				$results = array(
					'result'=>'FALSE',
					'message'=>'Error While Updating Gl Profile !'
				);

			}	
			

		}else{

		
			$data = array("dis_id"=>$dis_id);
			$con = array("userId"=>$glid);
			$update = $db->update("tbl_distributor",$data,$con);

			if($update == true){

				$results = array(
					'result'=>'TRUE',
					'message'=>'Gl Updated successfully!'
				);	

			}else{

				$results = array(
					'result'=>'FALSE',
					'message'=>'Error While Updating Gl Profile !'
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