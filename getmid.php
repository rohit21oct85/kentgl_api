<?php 
require_once('DBInterface.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');

$pid = 468;

$res = $db->getManagementId($pid);

header('content-type:application/json');
echo json_encode(array("MID"=>$res));

?>