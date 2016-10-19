<?php 

require_once('DBInterface.php');
$db = new Database();
$db->connect();
if(empty($_REQUEST['v_code'])){
	echo "Enter version code";
}else if(empty($_REQUEST['s_name'])){
	echo "Enter Service Name";
}else{
	$path = $_SERVER['PHP_SELF'];
	
	

	$version = trim($_REQUEST['v_code']);
	$service_name = trim($_REQUEST['s_name']);
	
	$result  = explode('.', $version);
	$versioncode =$result[0]."".$result[1]."".$result[2];
	$combine = trim($version."".$service_name);
	$api_key = md5($combine);
	
	if($version == "1.0.1" ){
		$result = array(
			"version"=>$version,
			"code"=>$versioncode,
			"service_url"=>$path,
			"service_name"=>$service_name,
			"API_KEY"=>$api_key,
			"no_param"=>"5",
			"param"=>array(
					"auth_username"=>"username",
					"auth_passwd"=>"passwd",
					"v_code"=>"app version code",
					"username"=>"username",
					"passwd"=>"passwd"
				)
		);
	}else{
		$result = array(
			"version"=>$version,
			"code"=>$versioncode,
			"service_url"=>$path,
			"service_name"=>$service_name,
			"API_KEY"=>$api_key,
			"no_param"=>"3",
			"param"=>array(
					"v_code"=>"app version code",
					"username"=>"username",
					"passwd"=>"passwd"
				)
		);
	}
	
	header('content-type:application/json');
	echo json_encode($result);
}






