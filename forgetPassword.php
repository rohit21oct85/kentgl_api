<?php 
require_once('mailer/PHPMailerAutoload.php');
//require_once('DBInterface.php');
//$db = new Database();
//$db->connect();


/*if(!empty($_REQUEST['email'])){
	
	$email = $_REQUEST['email'];	
	$data = array("email"=>$email);	
	$result = $db->forgetPassword($email);
	//print_r($result);
	
	if($result['result'] == "FALSE"){
		$resposne = array("result"=>"FALSE","msg"=>"Email does not exists");
	}else if($result['isActive'] == 0){
		$resposne = array("result"=>"FALSE","msg"=>"Your accoutn is deactive. Please contact to your administrator"); 
	}else{		
		if($result['roleName'] == "sp"){			
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
				$resposne = array("result"=>"TRUE","msg"=>"Check your message for password");
			}else{
				$resposne = array("result"=>"FALSE","msg"=>"Error in sending msg");
			}			
		}else{*/			
		$name = "rohit singh";//$result['userName'];
		$password = 123456;//$result['password'];
		$emailid = 'rohit.s@techradiation.com';//$result['email'];		
		$mail = new PHPMailer;
		$mail->isSMTP();
		$mail->Host = 'exchange.kent.co.in';
		$mail->SMTPAuth = true;
		$mail->Username = 'glmailer@kent.co.in';
		$mail->Password = 'KenT@12#';
		$mail->SMTPSecure = 'tls';
		$mail->Port = 587;
		$mail->setFrom('glmailer@kent.co.in','Mailer');
		$mail->addAddress($emailid, $name);
		$mail->isHTML(true);
		$mail->Subject = "Password Information";
		$mail->Body    = '<b>Hello Dear, <br>'.$name.'<br> Your Currrent Password Is: <br> '.$password.' </b>';
		if(!$mail->send()) {
			$resposne = array("result"=>"FALSE","message"=>"Error Found: ".$mail->ErrorInfo);
		}else{
			$resposne = array("result"=>"TRUE","message"=>"Plz check your email for password information");
		}			
	/*	}
	}	
}
$page_url = $_SERVER['PHP_SELF'];
$input = json_encode($data);
$results = json_encode($resposne);
$applog_data = array(
	'service_url' => $page_url,
	'request'	=> $input,
	'response' => $results,
	'entryDate' => date("Y-m-d h:i:s")
);
$insert_log = $db->insert("tbl_app_log", $applog_data);*/
header('content-type: application/json');
echo json_encode($resposne);


