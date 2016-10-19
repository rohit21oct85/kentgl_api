<?php 
require_once('DBInterface.php');
$db = new Database();
$db->connect();

$auth_uname = 		trim($_REQUEST['auth_username']);
$auth_passwd = 		trim($_REQUEST['auth_passwd']);
$app_version = trim($_REQUEST['v_code']);
$device = trim($_REQUEST['device']);
$model = trim($_REQUEST['model']);

$res = $db->authUser($auth_uname,$auth_passwd,$app_version);

if($res == 0){
$input = json_encode(array("auth_username"=>$auth_uname,"auth_passwd"=>$auth_passwd,"v_code"=>$app_version,"device"=>$device,"model"=>$model));
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
$input = json_encode(array("auth_username"=>$auth_uname,"auth_passwd"=>$auth_passwd,"v_code"=>$app_version,"device"=>$device,"model"=>$model));	
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
}else if($res == 1){

if(!empty($_REQUEST['user_id'])){

$user_id			 	 = trim($_REQUEST['user_id']);
$file_name 				 = trim($_REQUEST['file_name']);


$files = $_FILES['fileUpload'];


$extension = pathinfo($file_name,PATHINFO_EXTENSION); 

$image_name = $user_id.".".$extension;

$data_input = array("auth_username"=>$auth_uname,"auth_passwd"=>$auth_passwd,"v_code"=>$app_version,"device"=>$device,"model"=>$model,"user_id"=>$user_id,"file_name"=>$file_name,"file"=>$files);

$uploaddir  = 'profilepic/';

$uploadfile = $uploaddir . basename($image_name);
if(move_uploaded_file($_FILES['fileUpload']['tmp_name'], $uploadfile)){
	$data = array("profile_pic"=>$uploadfile);
$con = array("userId"=>$user_id);

$update= $db->update("tbl_user_master",$data,$con);

if($update == true)
{
	$res = $db->getProfile_pic($user_id);
	$profilepic = $res['profile_pic'];
	$response = array("result"=>"TRUE","message"=>"profile Uploaded Successfully","ProfilePic"=>$profilepic);
}
else
{
	$response = array("result"=>"FALSE","message"=>"Error in upload profile pic");
}
}else{
	$response = array("result"=>"FALSE","message"=>"Error in moving files");
}



$page_url = $_SERVER['PHP_SELF'];
$input = json_encode($data_input);
$results = json_encode($response);
$applog_data = array(
	'service_url' => $page_url,
	'request'	=> $input,
	'response' => $results,
	'entryDate' => date("Y-m-d h:i:s")
);
$insert_log = $db->insert("tbl_app_log", $applog_data);


header('content-type: application/json');
echo json_encode($response);
}else{
	$response = array("result"=>"FALSE","message"=>"Enter user Id");
	$results = json_encode($response);
	$applog_data = array(
		'service_url' => $page_url,
		'request'	=> $input,
		'response' => $results,
		'entryDate' => date("Y-m-d h:i:s")
	);
$insert_log = $db->insert("tbl_app_log", $applog_data);

header('content-type: application/json');
echo $results;
}

}
?>
