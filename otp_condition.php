<?php 
if($query_result['otp_status'] == 0){

	$results = array(
		'result'=>'TRUE',
		'message'=>'Please Enter Your OTP',
		'roleName' => $query_result['roleName'],
		'otp_status'=>$query_result['otp_status']
	);

	$otp =  mt_rand(5, 9999);
	$text_msg = "Please enter your OTP: ".$otp;
	$text_msg = trim(str_replace(' ', '%20', $text_msg));
	$mobile = $query_result['mobileno'];
	$userId = $query_result['userId'];
	$response = $this->sendMsg($mobile,$text_msg);
	$update_otp = array(
		'OTP' =>$otp,
		'msg_response'=>$response
	);
	$condition = array(
		'mobileno' => $mobile,
		'userId'   => $userId	
	);

	$update = $this->update("tbl_user_master", $update_otp, $condition);



}else{
}
 ?>
