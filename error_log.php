<?php
require_once('DBInterface.php');
$db = new Database();
$db->connect();

$response = array();

$user_id = trim($_REQUEST['user_id']);
$user_name = trim($_REQUEST['username']);
$date = trim($_REQUEST['date']);
$time = trim($_REQUEST['time']);
$model = trim($_REQUEST['model']);
$manufacturers = trim($_REQUEST['manufacturer']);
$error_logs = trim($_REQUEST['error_log']);
$remarks = trim($_REQUEST['remark']);
$manufacturer =  str_replace("'", "`", $manufacturers);		
$error_log =  str_replace("'", "`", $error_logs);		
$remark =  str_replace("'", "`", $remarks);


$data = array(
	'fnum_user_id' => $user_id,
	'fstr_username' => $user_name,
	'fstr_date' => $date,
	'fstr_time' => $time ,
	'fstr_model' => $model,
	'fstr_manufacturer' => $manufacturers, 
	'fstr_error_log' => $error_logs,
	'fstr_remark' => $remark,
	'fdt_entry_date' => date("Y-m-d h:i:s a")
);
?>