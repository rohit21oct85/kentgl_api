<?php 
require_once('mailer/PHPMailerAutoload.php');
require_once('DBInterface.php');
$db = new Database();
$db->connect();


if(!empty($_REQUEST['mobile'])){
	
	$mobile = $_REQUEST['mobile'];
	
	$result = $db->forgetPasswordSp($mobile);
	//echo $result['result']; die;
	if($result['result'] == "FALSE"){
		$resposne = array("result"=>"FALSE","message"=>"mobile does not exists");
	}else if($result['isActive'] == 0){
		$resposne = array("result"=>"FALSE","message"=>"Your accoutn is deactive. Please contact to your administrator"); 
	}else{
		
		$mobile = $result['mobileno'];
		$password = $result['password'];
		$userName = $result['userName'];
		$text_msg = "Hello , ".$userName."  your KentGL APP Password is". $password;
		$text_msg = trim(str_replace(' ', '%20', $text_msg));
		$responsemsg = $db->sendMsg($mobile,$text_msg);

		$u_data = array("msg_response"=>$responsemsg);
		$condition = array("mobileno"=>$mobile);
		$update = $db->update("tbl_user_master", $u_data, $condition);
		if($update == true){
			$resposne = array("result"=>"TRUE","message"=>"Check your mobile message for password");
		}else{
			$resposne = array("result"=>"FALSE","message"=>"Error in sending msg");
		}
	}
	
}else{
	$resposne = array("result"=>"FALSE","message"=>"Please enter your Mobile number");
}

$page_url = $_SERVER['PHP_SELF'];
$data = array("mobile"=>$mobile,"msg_response"=>$responsemsg);
$input = json_encode($data);
$results = json_encode($resposne);
$applog_data = array(
	'service_url' => $page_url,
	'request'	=> $input,
	'response' => $results,
	'entryDate' => date("Y-m-d h:i:s")
);
$insert_log = $db->insert("tbl_app_log", $applog_data);
header('content-type: application/json');
echo json_encode($resposne);


