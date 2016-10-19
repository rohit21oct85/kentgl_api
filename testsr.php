<?php  
require_once('DBInterface.php');
$db = new Database();
$db->connect();

$spid = 488;
$fdate = '2016-09-19';
$tdate ='2016-09-27';
$result = $db->getProductList_tdate($spid,$fdate,$tdate);
header('content-type: application/json');
echo json_encode($result);
?>
