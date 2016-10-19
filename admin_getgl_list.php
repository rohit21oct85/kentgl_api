<?php  
$input = file_get_contents('php://input');
$input = '{"action":"gllist"}';
if($input!=""){
require_once('DBInterface.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');
$arr = json_decode($input,true);

$action = $arr['action'];	

$Result = $db->getGl($action);
header('content-type: application/json');
echo $Result;

}
?>
