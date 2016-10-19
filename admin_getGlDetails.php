<?php  
$input = file_get_contents('php://input');
//$input = '{"glid":"7"}';
if($input!=""){
require_once('DBInterface.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');
$arr = json_decode($input,true);

$glid = $arr['glid'];	

$Result = $db->getGlDetails($glid);
header('content-type: application/json');
echo $Result;

}
?>
