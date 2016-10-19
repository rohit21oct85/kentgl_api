<?php  
	session_start();
	require_once('DBInterface.php');
	$db = new Database();
	$db->connect();
	$pid = 1;
	$result = $db->loginReport($pid);
	header('content-type: application/json');
	echo $result;
?>