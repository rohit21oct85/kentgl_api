<?php  
require_once('DBInterface.php');
$db = new Database();
$db->connect();

$pid = $_REQUEST['pid'];
$result = $db->adminSaleReport($pid);
header('content-type: application/json');
echo $result;
?>