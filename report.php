<?php  
	$con = mysql_connect("localhost","techteam","Tech@321");
	if($con == true){
		mysql_select_db("kentgl");
	}else{
		echo error_log();
	}

	$select = "select distinct(h.parentUserId) as parent_id,u.userName,u.email,
		GetFamilyTree(u.userId) as sale_person_id
		from tbl_user_hierarchy h,tbl_user_master u  where h.parentUserId = u.userId and u.roleId < 4";

	$query = mysql_query($select);
	while($row = mysql_fetch_array($query))
	{
		$sale_person_id =  $row['sale_person_id'];
		$parent_id = $row['parent_id'];
		$file_name = $row['userName'];
		//echo $file_name."-".$parent_id."--[".$sale_person_id."]"."<br>";
		$sel_gl = "SELECT s.state_name,c.city_name,um.userId,um.userName,um.mobileno ,uh.parentUserId, ( select userName from 	tbl_user_master WHERE userId = uh.parentUserId ) as asm_name ,( SELECT parentUserId from tbl_user_hierarchy WHERE userId = uh.parentUserId) as rm_id , ( select userName from tbl_user_master WHERE userId = rm_id) as rm_name , d.dis_id,dm.dc,dm.distributer_name , (SELECT COUNT(userId) FROM tbl_user_hierarchy WHERE parentUserId = um.userId ) as No_of_executive ,(SELECT sum(noOfDemo) FROM `tbl_daily_report` WHERE date(reportDate) = date(CURRENT_DATE()-3) and cb=um.userId) as No_OF_DEMO,(SELECT sum(noOfSales) FROM `tbl_daily_report` WHERE date(reportDate) = date(CURRENT_DATE()-3) and cb=um.userId) as No_OF_SALES , (SELECT SUM(noOfDemo) FROM tbl_daily_report  WHERE cb = um.userId AND reportDate 
					between DATE_SUB(CURRENT_DATE, INTERVAL DAYOFMONTH(CURRENT_DATE)-1 DAY) and CURRENT_DATE) as MTD_DEMO,(SELECT SUM(noOfSales) FROM tbl_daily_report  WHERE cb = um.userId AND reportDate between DATE_SUB(CURRENT_DATE, INTERVAL DAYOFMONTH
					(CURRENT_DATE)-1 DAY) and CURRENT_DATE) as MTD_SALES ,floor(((SELECT SUM(noOfSales) FROM tbl_daily_report  WHERE cb = um.userId AND reportDate between DATE_SUB(CURRENT_DATE, INTERVAL DAYOFMONTH(CURRENT_DATE)-1 DAY) and CURRENT_DATE) / 
					(SELECT SUM(noOfDemo) FROM tbl_daily_report  WHERE cb = um.userId AND reportDate between DATE_SUB(CURRENT_DATE, INTERVAL DAYOFMONTH(CURRENT_DATE)-1 DAY) and CURRENT_DATE) * 100)) as MTD_SALE_PER
 					FROM tbl_user_master as um 
					LEFT JOIN tbl_city as c ON c.city_id = um.city
					LEFT JOIN tbl_state as s ON s.state_Id = um.state
					LEFT JOIN tbl_user_hierarchy as uh ON uh.userId = um.userId
					LEFT JOIN tbl_distributor as d ON d.userId = um.userId
					LEFT JOIN tbl_distributor_master as dm ON dm.dis_id = d.dis_id
 					WHERE um.userId IN($sale_person_id) and um.roleId = 4"; 
		//echo "<br>";
		$query_gl = mysql_query($sel_gl);

		while($row = mysql_fetch_array($query_gl)){

			$userId = $row['userId'];
			$userName = $row['userName'];
			$state = $row['state_name'];
			$city = $row['city_name'];
			$mobileno = $row['mobileno'];
			$asm_name = $row['asm_name'];
			$rm_name = $row['rm_name'];
			$distributorCode = $row['dc'];
			$distributer_name = $row['distributer_name'];
			$No_of_executive = $row['No_of_executive'];
			$No_OF_DEMO = $row['No_OF_DEMO'];
			$No_OF_SALES = $row['No_OF_SALES'];
			$MTD_DEMO = $row['MTD_DEMO'];
			$MTD_SALES = $row['MTD_SALES'];
			$MTD_SALE_PER = $row['MTD_SALE_PER'];

			echo $state."--".$city."--".$userId."--".$userName."--".$mobileno."--".$asm_name."--".$rm_name."--".$distributorCode."--".$distributer_name."--".$No_of_executive."--".$No_OF_DEMO."--".$No_OF_SALES."--".$MTD_DEMO."--".$MTD_SALES."--".$MTD_SALE_PER."%"."<br>";

		}
		
	}
	