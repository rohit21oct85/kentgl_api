<?php 
	
	$conn = mysqli_connect("localhost","techteam","Tech@321","kentgl");
	$top_data_array = array();
	$query = "SELECT CAST(GROUP_CONCAT(userId SEPARATOR ', ') AS CHAR) as group_user from tbl_user_hierarchy WHERE parentUserId IN (SELECT userId from tbl_user_hierarchy WHERE panid = 0 group by parentUserId)";
	
	$sel_query = mysqli_query($conn, $query);
	$result = mysqli_fetch_object($sel_query);
	
	$group_user = $result->group_user; 
	
	echo $query = "SELECT * FROM `tbl_user_hierarchy` where userId IN (SELECT parentUserId from tbl_user_hierarchy WHERE userId IN ($group_user))";
	die;
		$sel_query = mysqli_query($conn, $query);
			while($result = mysqli_fetch_object($sel_query)){
				$parentUserId = $result->parentUserId;
				$query = "SELECT userId,parentUserId,panid,panmid from tbl_user_hierarchy WHERE userId = $parentUserId";
				$sel_query = mysqli_query($conn, $query);
					while($result = mysqli_fetch_object($sel_query)){
						$userId = $result->userId;
						$parentUserId = $result->parentUserId;
						$panid = $result->panid;
						$panmid = $result->panmid;
						echo $userId ."-".$parentUserId."-".$panid."-".$panmid;
					}	
			}	
		
	
	

?>