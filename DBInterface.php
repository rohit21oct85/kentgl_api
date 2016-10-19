
<?php

class Database
{
	var $rs;
	var $dbh;
	 
	
	function Database()
	{
		$this->rs = "";
		$this->dbh = "";
	}
		
	
	//Connect to Database
	
	function connect()
	{
		//crossAhead#mysql1 - .in password 
     	//$this->dbh = mysqli_connect('localhost', 'techteam' , 'Tech@321') or die('Not connected');
     	$this->dbh = mysqli_connect('localhost', 'root' , '','kentgl') or die('Not connected');
		
	    
		
		return $this->dbh;
    }	

	public function insert( $table, $variables = array() )
    {
        //self::$counter++;
        //Make sure the array isn't empty
        if( empty( $variables ) )
        {
            return false;
        }
        
        $sql = "INSERT INTO ". $table;
        $fields = array();
        $values = array();
        foreach( $variables as $field => $value )
        {
            $fields[] = $field;
            $values[] = "'".$value."'";
        }
        $fields = ' (' . implode(', ', $fields) . ')';
        $values = '('. implode(', ', $values) .')';
        
        $sql .= $fields .' VALUES '. $values;

        
        if( mysqli_query( $this->dbh , $sql ) )
        {
            
            return true;
        }
        else
        {
            return false;
        }
		
    }
	// update 
	public function update( $table, $variables = array(), $where = array() )
    {
        //self::$counter++;
        //Make sure the required data is passed before continuing
        //This does not include the $where variable as (though infrequently)
        //queries are designated to update entire tables
        if( empty( $variables ) )
        {
            return false;
        }
        $sql = "UPDATE ". $table ." SET ";
        foreach( $variables as $field => $value )
        {
            
            $updates[] = "`$field` = '$value'";
        }
        $sql .= implode(', ', $updates);
        
        //Add the $where clauses as needed
        if( !empty( $where ) )
        {
            foreach( $where as $field => $value )
            {
                $value = $value;

                $clause[] = "$field = '$value'";
            }
            $sql .= ' WHERE '. implode(' AND ', $clause);   
        }

       if(mysql_query( $sql ,$this->dbh ))
		{
			return true;
		}
		else
		{
			return false;
		}

    }
	
 public function delete( $table, $where = array(), $limit = '' )
    {
        
        //Delete clauses require a where param, otherwise use "truncate"
        if( empty( $where ) )
        {
            return false;
        }
        
        $sql = "DELETE FROM ". $table;
        foreach( $where as $field => $value )
        {
            $value = $value;
            $clause[] = "$field = '$value'";
        }
        $sql .= " WHERE ". implode(' AND ', $clause);
        
        if( !empty( $limit ) )
        {
            $sql .= " LIMIT ". $limit;
        }
            
        if(mysql_query( $sql ,$this->dbh ))
		{
			return true;
		}
		else
		{
			return false;
		}
    }
	
	// clean input 
	function clean_input($data){
		$data = strip_tags($data);
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		$data = preg_replace("/[^a-zA-Z0-9\s]/", "", $data);
		return $data;
	}
	// clean input email
	function clean_input_email($data){
		$data = strip_tags($data);
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	
    // Check email 
	function chkEmail($emailid){
		$sql = "SELECT userId FROM tbl_user_master WHERE email = '".$emailid."'";
		$sql_query = mysql_query($sql,$this->dbh);
		$num_rows = mysql_num_rows($sql_query);
		return $num_rows;
	}
	
	// chkOdlPassword($user_odlPassword)
	function chkoldPassword($opwd,$userId){
		$sql = "SELECT userId FROM tbl_user_master WHERE password = '".$opwd."' and userId = '".$userId."'";
		$sql_query = mysql_query($sql,$this->dbh);
		$num_rows = mysql_num_rows($sql_query);
		return $num_rows;
	}
	
	// check mobile no
	function chkMobile($mobile){
		$sql = "SELECT userId FROM tbl_user_master WHERE mobileno = '".$mobile."'";
		$sql_query = mysql_query($sql,$this->dbh);
		$num_rows = mysql_num_rows($sql_query);
		return $num_rows;
	}
	function chkCustMobile($mobile){
		$sql = "SELECT userId FROM tbl_water_test WHERE customer_mobile = '".$mobile."'";
		$sql_query = mysql_query($sql,$this->dbh);
		$num_rows = mysql_num_rows($sql_query);
		return $num_rows;
	}
	public function forgetPassword($username){

		if(is_numeric($username)){
			$query = "SELECT um.userName,um.password,um.isActive,um.mobileno,rm.roleName FROM tbl_user_master as um LEFT JOIN tbl_role_master as rm ON rm.roleId = um.roleId WHERE um.mobileno = '$username' and um.roleId = 5";		
		}else{
			$query = "SELECT um.userName,um.password,um.isActive,um.mobileno,um.email,rm.roleName FROM tbl_user_master as um LEFT JOIN tbl_role_master as rm ON rm.roleId = um.roleId WHERE um.email = '$username' and um.roleId = 4";	
		}
		
		$result = mysqli_query($this->dbh,$query);
		if( mysqli_num_rows( $result ) > 0 ){
			$query_result = mysqli_fetch_assoc($result);
			return $query_result;
		}else{
			$data = array("result"=>"FALSE");
			return $data;
		}
	}
	
    public function chkLogin($username,$passwd){
		
		if(empty($username)){
			return json_encode(array('result'=>'FALSE','msg'=>'Please send email/mobileno.'));
		}else if(empty($passwd)){
			return json_encode(array('result'=>'FALSE','msg'=>'Please send password.'));
		}else{

			if(is_numeric($username)){
				$query = "SELECT um.userId,um.emp_code,um.zone,um.userName,um.city,um.state,c.city_name,s.state_name,um.profile_pic,um.email,um.mobileno,um.isActive,um.otp_status,rm.roleId,rm.roleName,uh.parentUserId,d.dis_id,dm.dc,um.password,um.weekOff FROM tbl_user_master as um LEFT JOIN tbl_role_master as rm ON um.roleId = rm.roleId LEFT JOIN tbl_user_hierarchy as uh ON um.userId = uh.userId LEFT JOIN tbl_distributor as d ON um.userId = d.userId LEFT JOIN tbl_distributor_master as dm ON dm.dis_id = d.dis_id LEFT JOIN tbl_state as s ON s.state_id = um.state LEFT JOIN tbl_city as c ON c.city_id = um.city WHERE um.mobileno = '$username' AND um.password = '$passwd' and um.roleId = 5 ";
			}else{
				$query = "SELECT um.userId,um.emp_code,um.zone,um.userName,um.city,um.state,c.city_name,s.state_name,um.profile_pic,um.email,um.mobileno,um.isActive,rm.roleId,rm.roleName,uh.parentUserId,d.dis_id,dm.dc,um.password,um.weekOff FROM tbl_user_master as um LEFT JOIN tbl_role_master as rm ON um.roleId = rm.roleId LEFT JOIN tbl_user_hierarchy as uh ON um.userId = uh.userId LEFT JOIN tbl_distributor as d ON um.userId = d.userId LEFT JOIN tbl_distributor_master as dm ON dm.dis_id = d.dis_id LEFT JOIN tbl_state as s ON s.state_id = um.state LEFT JOIN tbl_city as c ON c.city_id = um.city WHERE um.email = '$username' AND um.password = '$passwd' and um.roleId = 4 ";
			}

			$result = mysql_query($query,$this->dbh);
			if( mysql_num_rows( $result ) > 0 ){
				$query_result = mysql_fetch_assoc($result);
				if($query_result['isActive']==1){
					if($query_result['roleId'] == 5 or $query_result['roleId'] == 4 ){
						if(!empty($query_result['dc'])){
							$dc = $query_result['dc'];
						}else{
							$dc = "";
						}
				if($query_result['roleName'] == "gl"){

					$results = array(
						'result'=>'TRUE',
						'msg'=>'Successfully login',
						'userId' => $query_result['userId'],
						'emp_code' => $query_result['emp_code'],
						'zone' => $query_result['zone'],
						'userName' => $query_result['userName'],
						'profile_pic'=>$query_result['profile_pic'],
						'roleName' => $query_result['roleName'],
						'ParentId' => $query_result['parentUserId'],
						'mobile' => $query_result['mobileno'],
						'email' => $query_result['email'],
						'password'=>$query_result['password'],
						'weekOff' => $query_result['weekOff'],
						'city_id'=>$query_result['city'],
						'state_id'=>$query_result['state'],
						'city'=>$query_result['city_name'],
						'state'=>$query_result['state_name'],
						'DC' => $dc
					);
					
				}else{
				
				if($query_result['otp_status'] == 0){

					$results = array(
						'result'=>'TRUE',
						'msg'=>'Please Enter Your OTP',
						'roleName' => $query_result['roleName'],
						'otp_status'=>$query_result['otp_status'],
						'city_id'=>$query_result['city'],
						'state_id'=>$query_result['state'],
						'city'=>$query_result['city_name'],
						'state'=>$query_result['state_name']
					);

					$otp =  rand ( 1000 , 9999 );
					$text_msg = "Please enter your OTP: ".$otp;
					$text_msg = trim(str_replace(' ', '%20', $text_msg));
					$mobile = $query_result['mobileno'];
					$userId = $query_result['userId'];
					$response = $this->sendMsg($mobile,$text_msg);
					$update_otp = array(
						'OTP' =>$otp,
						'msg_response'=>$response
					);
					$condition = array(
						'mobileno' => $mobile,
						'userId'   => $userId	
					);

					$update = $this->update("tbl_user_master", $update_otp, $condition);

				}else{

					$results = array(
					'result'=>'TRUE',
					'msg'=>'You are successfully loggedin',
						'userId' => $query_result['userId'],
						'userName' => $query_result['userName'],
						'profile_pic'=>$query_result['profile_pic'],
						'roleName' => $query_result['roleName'],
						'ParentId' => $query_result['parentUserId'],
						'mobile' => $query_result['mobileno'],
						'email' => $query_result['email'],
						'password'=>$query_result['password'],
						'weekOff' => $query_result['weekOff'],
						'otp_status'=>$query_result['otp_status'],
						'zone' => $query_result['zone'],
						'city_id'=>$query_result['city'],
						'state_id'=>$query_result['state'],
						'city'=>$query_result['city_name'],
						'state'=>$query_result['state_name']
					);	


				}	
				
					
					
					
				}
				
				$data_login = array(
					'loginDate' => date('Y-m-d H:i:s'),
					'loginUser' => $query_result['userId'],
					'process'	=> "Login",
					'isActive'  => 1,
					'entryDate' => date('Y-m-d H:i:s')
					
				);
				$query = $this->insert('tbl_login_details',$data_login);	
					
				return json_encode($results);	
					}else{
						return json_encode(array('result'=>'FALSE','msg'=>'You are not authorised User'));
					}
				}else{
					return json_encode(array('result'=>'FALSE','msg'=>'Account Is Deactivated'));	
				}
			}else{
				return  json_encode(array('result'=>'FALSE','msg'=>'You have entered a wrong username or password'));
			}
		}
	}
function checkOTPStaus($otp){
	$update = "select otp_status from tbl_user_master WHERE OTP = $otp LIMIT 1";
	$query = mysql_query($update, $this->dbh);
	$no = mysql_num_rows($query);
	return $no;
}

function verifiedOTP($username, $passwd ){
	$query = "SELECT um.userId,um.emp_code,um.zone,um.userName,um.city,um.state,c.city_name,s.state_name,um.profile_pic,um.email,um.mobileno,um.isActive,um.otp_status,rm.roleId,rm.roleName,uh.parentUserId,d.dis_id,dm.dc,um.password,um.weekOff FROM tbl_user_master as um LEFT JOIN tbl_role_master as rm ON um.roleId = rm.roleId LEFT JOIN tbl_user_hierarchy as uh ON um.userId = uh.userId LEFT JOIN tbl_distributor as d ON um.userId = d.userId LEFT JOIN tbl_distributor_master as dm ON dm.dis_id = d.dis_id LEFT JOIN tbl_state as s ON s.state_id = um.state LEFT JOIN tbl_city as c ON c.city_id = um.city WHERE um.mobileno = '$username' AND um.password = '$passwd' and um.roleId = 5 AND otp_status = 1 limit 1";
	$result = mysql_query($query,$this->dbh);
	if( mysql_num_rows( $result ) > 0 ){
		$query_result = mysql_fetch_assoc($result);
		$results = array(
			"result"=>"TRUE","msg"=>"OTP Verified Successfully",
			'userId' => $query_result['userId'],
			'userName' => $query_result['userName'],
			'profile_pic'=>$query_result['profile_pic'],
			'roleName' => $query_result['roleName'],
			'ParentId' => $query_result['parentUserId'],
			'mobile' => $query_result['mobileno'],
			'email' => $query_result['email'],
			'password'=>$query_result['password'],
			'weekOff' => $query_result['weekOff'],
			'otp_status'=>$query_result['otp_status'],
			'zone' => $query_result['zone'],
			'city_id'=>$query_result['city'],
			'state_id'=>$query_result['state'],
			'city'=>$query_result['city_name'],
			'state'=>$query_result['state_name']
		);
		return $results;	
	}

}
public function sendMsg($mobile,$text_msg){
	$url = "http://enterprise.smsgupshup.com/GatewayAPI/rest?method=SendMessage&send_to='".$mobile."'&msg='".$text_msg."'&msg_type=TEXT&userid=2000161731&auth_scheme=plain&password=R6kkZC&v=1.1&format=text";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result_curl = curl_exec($ch);
	curl_close($ch);
	return $result_curl;
}

	

 public function authUser($username,$passwd,$version){
	if(is_numeric($username)){
		$query = "SELECT um.isActive,(select version_name from app_version) as v_code FROM tbl_user_master as um WHERE um.isActive = 1 and um.roleId = 5 and um.mobileno = '$username' AND um.password = '$passwd' LIMIT 1";
	}else{
		$query = "SELECT um.isActive,(select version_name from app_version) as v_code FROM tbl_user_master as um WHERE um.isActive = 1 and um.roleId = 4 and um.email = '$username' AND um.password = '$passwd' LIMIT 1";
	}
	$mysql_query = mysql_query($query,$this->dbh);
	if( mysql_num_rows( $mysql_query ) > 0 ){
		$result = mysql_fetch_array($mysql_query);
		if($version != $result['v_code']){
			return 2;
		}else{
			return 1;
		}
	}else{
		return 0;
	}
}	
	
	
	



	
	public function chkLogout($username,$passwd){
		
		if(empty($username)){
			echo  json_encode(array('result'=>'FALSE','msg'=>'Please send email/Mobile no'));
		}else if(empty($passwd)){
			echo  json_encode(array('result'=>'FALSE','msg'=>'Please send password.'));
		}else{
			

			if(is_numeric($username)){

				$query = "SELECT um.userId FROM tbl_user_master as um  WHERE um.mobileno = '$username' AND um.password = '$passwd' and um.roleId = 5 ";
			}else{
				$query = "SELECT um.userId FROM tbl_user_master as um  WHERE um.email = '$username' AND um.password = '$passwd' and um.roleId = 4 ";
			}
			//$query = "SELECT userId FROM tbl_user_master WHERE email = '$username' AND password = '$passwd' ";
			
			$result = mysql_query($query,$this->dbh);
			if( mysql_num_rows( $result ) > 0 ){
				$query_result = mysql_fetch_assoc($result);
				
				$data_login = array(
					'loginDate' => date('Y-m-d H:i:s'),
					'loginUser' => $query_result['userId'],
					'process'	=> "Logout",
					'isActive'  => 1,
					'entryDate' => date('Y-m-d H:i:s')
					
				);
				$query = $this->insert('tbl_login_details',$data_login);	
				$results = array(
					'result'=>'TRUE',
					'msg'=>'You are successfully log out'
				);	
				return json_encode($results);
			}else{
				return  json_encode(array('result'=>'FALSE','msg'=>'Entered email/mobile or passord is not correct, please try again !'));
			}
		}
		
	}
	
	function getAllSalePersonByGlId($glid){
		
		if(empty($glid)){
			return json_encode(array('result'=>'FALSE','msg'=>'Enter Your Group leader id'));
		}else{
		$return_results_top = array();
		$query = "select distinct(h.parentUserId) as parent_id,
		`GetFamilyTree`(u.userId) as sale_person_id
		from tbl_user_hierarchy h,tbl_user_master u  where h.parentUserId = u.userId and h.parentUserId = $glid";
		
		$sql_query = mysql_query($query,$this->dbh);
			if( mysql_num_rows( $sql_query ) > 0 ){
				$x =0;
				while($data_result = mysql_fetch_assoc($sql_query)){
					
					$sale_person_Id = $data_result['sale_person_id'];
					$query_sales = "SELECT um.userId,um.userName,um.email,um.mobileno,um.weekOff,um.isActive,um.password,um.city,um.state ,rm.roleName,rm.role_seq , uh.parentUserId , c.city_name ,s.state_name FROM tbl_user_master as um 
						LEFT JOIN tbl_role_master as rm ON um.roleId = rm.roleId 
						LEFT JOIN tbl_user_hierarchy as uh ON um.userId = uh.userId 
						LEFT JOIN tbl_city as c ON um.city = c.city_id
						LEFT JOIN tbl_state as s ON um.state = s.state_Id
						WHERE um.userId IN ($sale_person_Id) ORDER BY rm.role_seq ASC, um.userName ASC";
					$sql_query = mysql_query($query_sales);
					if( mysql_num_rows($sql_query) > 0){
						$data_result = array();
						while($result = mysql_fetch_assoc($sql_query)){
							$data_result = $result;
							
							if($result['city_name']== null){
								$data_result['city'] = "";	
							}else{
								$data_result['city'] = $result['city_name'];	
							}
							if($result['state_name']==null){
								$data_result['state'] = "";	
							}else{
								$data_result['state'] = $result['state_name'];	
							}
							
							$return_results_top[] = $data_result;	
						$x++;	
						}
						return  json_encode(array('result'=>'TRUE','PersonalData'=>$return_results_top));
					}
				else{
				return  json_encode(array('result'=>'FALSE','msg'=>'No more Results'));
			}
				}	
				
			}else{
			return  json_encode(array('result'=>'FALSE','msg'=>'No more Results'));	
			}
		}	
			
	}

	
	
	


	function getLastUser($pid){
		$query = "select MAX(userId) as uid from tbl_user_master where cb = $pid";
		$sql_query = mysql_query($query,$this->dbh);
		$row = mysql_fetch_array($sql_query);
		$uid = $row['uid'];
		return $uid;
	}

	function getLastInsertedProduct($cbId,$userId){
		$query = "select MAX(dailyReportId) as repid from tbl_daily_report where cb = $cbId and userId = $userId";
		$sql_query = mysql_query($query,$this->dbh);
		if(mysql_num_rows($sql_query) > 0){
			$row = mysql_fetch_array($sql_query);
			$uid = $row['repid'];
			return $uid;
		}
	}
	
	function viewAllProducts(){
		$return_results_top = array();
		$query = "select productId,productName,productDiscription,isActive from tbl_product_master where isActive = 1 order by rand()";
		$sql_query = mysql_query($query, $this->dbh);
		if( mysql_num_rows( $sql_query ) > 0 ){
		$data_result = array();
		$x = 0;
		while($row = mysql_fetch_array($sql_query)){
			$data_result['productId'] = $row['productId'];
			$data_result['productName'] = $row['productName'];
			$data_result['productDiscription'] = $row['productDiscription'];
			$data_result['isActive'] = $row['isActive'];
			$return_results_top[] = $data_result;
		$x++;	
		}
		return  json_encode(array('result'=>'TRUE','ProductList'=>$return_results_top));
		}else{
			return  json_encode(array('result'=>'FALSE','msg'=>'No more Products'));	
		}
	}
	
   


	function viewSaleReportParentId($pid,$fdate,$tdate){
		
		if(empty($pid)){
			return json_encode(array('result'=>'FALSE','msg'=>'Enter parent Id'));	
		}else{

		$minEntryDate = $this->getMinEntrydate();	
		if(empty($fdate) && empty($tdate)){
			$cdate = date("Y-m-d");
			$return_results_top = array();
				$query = "select distinct(h.parentUserId) as parent_id,
				GetFamilyTree(u.userId) as sale_person_id
				from tbl_user_hierarchy h,tbl_user_master u  where h.parentUserId = u.userId and h.parentUserId = $pid";
				
				$sql_query = mysql_query($query, $this->dbh);
				$rcount = mysql_num_rows($sql_query);
				
				if($rcount == 1){
					while($result = mysql_fetch_array($sql_query)){
					$sale_person_id = explode(",",$result['sale_person_id']);	
					$parentid = $result['parent_id'];	
					$all_id = array_push($sale_person_id, $parentid);
					
					foreach($sale_person_id as $value){
							
					$query_dr = "select dr.dailyReportId, date(dr.reportDate) as reportDate, dr.userId, dr.attendance, dr.noOfDemo, dr.noOfSales, um.userName,(select count(userId) FROM tbl_water_test WHERE date(entryDate) = '$cdate' AND userId = dr.userId ) as total_water_test,(select count(userId) FROM tbl_water_test WHERE product_purchased = 1 and date(entryDate) = '$cdate' AND userId = dr.userId ) as total_sale,(select count(userId) FROM tbl_water_test WHERE product_purchased = 0 and date(entryDate)='$cdate' AND userId = dr.userId ) as total_not_sold from tbl_daily_report as dr LEFT outer JOIN tbl_user_master as um ON um.userId = dr.userId where date(reportDate) = '$cdate' and dr.userId = $value";
					
						$sql_query_dr = mysql_query($query_dr, $this->dbh);
						$no = mysql_num_rows($sql_query_dr);
						
							$data_result = array();
							$x = 0;
							while($result = mysql_fetch_array($sql_query_dr)){
								$repDate = date("Y-m-d",strtotime($result['reportDate']));
								$data_result['userId'] = $result['userId'];
								$data_result['userName'] = $result['userName'];
								$data_result['attendance'] = $result['attendance'];
								$data_result['Demo'] = $result['noOfDemo'];
								$data_result['Sales'] = $result['noOfSales'];
								$data_result['total_watertest'] = $result['total_water_test'];
								$data_result['sold'] = $result['total_sale'];
								$data_result['no_sold'] = $result['total_not_sold'];
								if($result['noOfSales'] != 0){
									$data_result['product_list'] = $this->getProductList($value,$cdate);	
								}
								
								
								$return_results_top[] = $data_result;	
								$x++;	
							}
						}
						return  json_encode(array('result'=>'TRUE','ReportList'=>$return_results_top));
					}
				}else{
					$query_dr = "select dr.dailyReportId, date(dr.reportDate) as reportDate, dr.userId, dr.attendance, dr.noOfDemo, dr.noOfSales, um.userName,(select count(userId) FROM tbl_water_test WHERE date(entryDate) = '$cdate' AND userId = dr.userId ) as total_water_test,(select count(userId) FROM tbl_water_test WHERE product_purchased = 1 and date(entryDate) = '$cdate' AND userId = dr.userId ) as total_sale,(select count(userId) FROM tbl_water_test WHERE product_purchased = 0 and date(entryDate)='$cdate' AND userId = dr.userId ) as total_not_sold from tbl_daily_report as dr LEFT outer JOIN tbl_user_master as um ON um.userId = dr.userId where date(reportDate) = '$cdate' and dr.userId = $pid";
					
					$sql_query_dr = mysql_query($query_dr, $this->dbh);
					$no = mysql_num_rows($sql_query_dr);
					
						$data_result = array();
						$x = 0;
						while($result = mysql_fetch_array($sql_query_dr)){
							$repDate = date("Y-m-d",strtotime($result['reportDate']));
							$data_result['userId'] = $result['userId'];
							$data_result['userName'] = $result['userName'];
							$data_result['attendance'] = $result['attendance'];
							$data_result['Demo'] = $result['noOfDemo'];
							$data_result['Sales'] = $result['noOfSales'];
							$data_result['total_watertest'] = $result['total_water_test'];
							$data_result['sold'] = $result['total_sale'];
							$data_result['no_sold'] = $result['total_not_sold'];
							if($result['noOfSales'] != 0){
								$data_result['product_list'] = $this->getProductList($pid,$cdate);	
							}
							
							
							$return_results_top[] = $data_result;	
							$x++;	
						}
						return  json_encode(array('result'=>'TRUE','ReportList'=>$return_results_top));
				}
		}else if(empty($fdate)){
			if($tdate < $minEntryDate){
				$errormsg = "No Sale Report Available for this date: ".$tdate;	
				return json_encode(array('result'=>'FALSE','msg'=>$errormsg));		
			}else{
				$return_results_top = array();
				$query = "select distinct(h.parentUserId) as parent_id,
				GetFamilyTree(u.userId) as sale_person_id
				from tbl_user_hierarchy h,tbl_user_master u  where h.parentUserId = u.userId and h.parentUserId = $pid";
				
				$sql_query = mysql_query($query, $this->dbh);
				$rcount = mysql_num_rows($sql_query);
				if($rcount == 1){
					while($result = mysql_fetch_array($sql_query)){
					$sale_person_id = explode(",",$result['sale_person_id']);	
					$parentid = $result['parent_id'];	
					$all_id = array_push($sale_person_id, $parentid);
					
					foreach($sale_person_id as $value){
							
					$query_dr = "select dr.dailyReportId, dr.reportDate, dr.userId, dr.attendance, dr.noOfDemo, dr.noOfSales, um.userName,(select count(userId) FROM tbl_water_test WHERE date(entryDate) = '$tdate' AND userId = dr.userId ) as total_water_test,(select count(userId) FROM tbl_water_test WHERE product_purchased = 1 and date(entryDate) = '$tdate' AND userId = dr.userId ) as total_sale,(select count(userId) FROM tbl_water_test WHERE product_purchased = 0 and date(entryDate)='$tdate' AND userId = dr.userId ) as total_not_sold from tbl_daily_report as dr LEFT outer JOIN tbl_user_master as um ON um.userId = dr.userId where date(reportDate) = '$tdate' and dr.userId = $value";
					
						$sql_query_dr = mysql_query($query_dr, $this->dbh);
						$no = mysql_num_rows($sql_query_dr);
						
							$data_result = array();
							$x = 0;
							while($result = mysql_fetch_array($sql_query_dr)){
								$repDate = date("Y-m-d",strtotime($result['reportDate']));
								$data_result['reportDate'] = date("Y-m-d",strtotime($result['reportDate']));
								$data_result['userId'] = $result['userId'];
								$data_result['userName'] = $result['userName'];
								$data_result['attendance'] = $result['attendance'];
								$data_result['Demo'] = $result['noOfDemo'];
								$data_result['Sales'] = $result['noOfSales'];
								$data_result['total_watertest'] = $result['total_water_test'];
								$data_result['sold'] = $result['total_sale'];
								$data_result['no_sold'] = $result['total_not_sold'];
								if($result['noOfSales'] != 0){
									$data_result['product_list'] = $this->getProductList($value,$repDate);	
								}
								
							
								$return_results_top[] = $data_result;	
								$x++;	
							}
						}
						return  json_encode(array('result'=>'TRUE','ReportList'=>$return_results_top));
					}
				}else{
					$query_dr = "select dr.dailyReportId, date(dr.reportDate) as reportDate, dr.userId, dr.attendance, dr.noOfDemo, dr.noOfSales, um.userName,(select count(userId) FROM tbl_water_test WHERE date(entryDate) = '$cdate' AND userId = dr.userId ) as total_water_test,(select count(userId) FROM tbl_water_test WHERE product_purchased = 1 and date(entryDate) = '$cdate' AND userId = dr.userId ) as total_sale,(select count(userId) FROM tbl_water_test WHERE product_purchased = 0 and date(entryDate)='$cdate' AND userId = dr.userId ) as total_not_sold from tbl_daily_report as dr LEFT outer JOIN tbl_user_master as um ON um.userId = dr.userId where date(reportDate) = '$cdate' and dr.userId = $pid";
					
					$sql_query_dr = mysql_query($query_dr, $this->dbh);
					$no = mysql_num_rows($sql_query_dr);
					
						$data_result = array();
						$x = 0;
						while($result = mysql_fetch_array($sql_query_dr)){
							$repDate = date("Y-m-d",strtotime($result['reportDate']));
							$data_result['userId'] = $result['userId'];
							$data_result['userName'] = $result['userName'];
							$data_result['attendance'] = $result['attendance'];
							$data_result['Demo'] = $result['noOfDemo'];
							$data_result['Sales'] = $result['noOfSales'];
							$data_result['total_watertest'] = $result['total_water_test'];
							$data_result['sold'] = $result['total_sale'];
							$data_result['no_sold'] = $result['total_not_sold'];
							if($result['noOfSales'] != 0){
								$data_result['product_list'] = $this->getProductList($pid,$cdate);	
							}
							
							
							$return_results_top[] = $data_result;	
							$x++;	
						}
						return  json_encode(array('result'=>'TRUE','ReportList'=>$return_results_top));
				}
			}
		}else if(empty($tdate)){
			if($fdate < $minEntryDate){
				$errormsg = "No Sale Report Available for this date: ".$fdate;
				return json_encode(array('result'=>'FALSE','msg'=>$errormsg));		
			}else{
				$return_results_top = array();
				$query = "select distinct(h.parentUserId) as parent_id,
				GetFamilyTree(u.userId) as sale_person_id
				from tbl_user_hierarchy h,tbl_user_master u  where h.parentUserId = u.userId and h.parentUserId = $pid";
				
				$sql_query = mysql_query($query, $this->dbh);
				$rcount = mysql_num_rows($sql_query);
				if($rcount == 1){
					while($result = mysql_fetch_array($sql_query)){
					$sale_person_id = explode(",",$result['sale_person_id']);	
					$parentid = $result['parent_id'];	
					$all_id = array_push($sale_person_id, $parentid);
					
					foreach($sale_person_id as $value){
							
					$query_dr = "select dr.dailyReportId, dr.reportDate, dr.userId, dr.attendance, dr.noOfDemo, dr.noOfSales, um.userName,(select count(userId) FROM tbl_water_test WHERE date(entryDate) = '$fdate' AND userId = dr.userId ) as total_water_test,(select count(userId) FROM tbl_water_test WHERE product_purchased = 1 and date(entryDate) = '$fdate' AND userId = dr.userId ) as total_sale,(select count(userId) FROM tbl_water_test WHERE product_purchased = 0 and date(entryDate)='$cdate' AND userId = dr.userId ) as total_not_sold from tbl_daily_report as dr LEFT outer JOIN tbl_user_master as um ON um.userId = dr.userId where date(reportDate) = '$fdate' and dr.userId = $value";
					
						$sql_query_dr = mysql_query($query_dr, $this->dbh);
						$no = mysql_num_rows($sql_query_dr);
						
							$data_result = array();
							$x = 0;
							while($result = mysql_fetch_array($sql_query_dr)){
								$repDate = date("Y-m-d",strtotime($result['reportDate']));
								$data_result['reportDate'] = date("Y-m-d",strtotime($result['reportDate']));
								$data_result['userId'] = $result['userId'];
								$data_result['userName'] = $result['userName'];
								$data_result['attendance'] = $result['attendance'];
								$data_result['Demo'] = $result['noOfDemo'];
								$data_result['Sales'] = $result['noOfSales'];
								$data_result['total_watertest'] = $result['total_water_test'];
								$data_result['sold'] = $result['total_sale'];
								$data_result['no_sold'] = $result['total_not_sold'];
								if($result['noOfSales'] != 0){
									$data_result['product_list'] = $this->getProductList($value,$repDate);
								}
								
								
								$return_results_top[] = $data_result;	
								$x++;	
							}
						}
						return  json_encode(array('result'=>'TRUE','ReportList'=>$return_results_top));
					}
				}else{
					$query_dr = "select dr.dailyReportId, date(dr.reportDate) as reportDate, dr.userId, dr.attendance, dr.noOfDemo, dr.noOfSales, um.userName,(select count(userId) FROM tbl_water_test WHERE date(entryDate) = '$cdate' AND userId = dr.userId ) as total_water_test,(select count(userId) FROM tbl_water_test WHERE product_purchased = 1 and date(entryDate) = '$cdate' AND userId = dr.userId ) as total_sale,(select count(userId) FROM tbl_water_test WHERE product_purchased = 0 and date(entryDate)='$cdate' AND userId = dr.userId ) as total_not_sold from tbl_daily_report as dr LEFT outer JOIN tbl_user_master as um ON um.userId = dr.userId where date(reportDate) = '$cdate' and dr.userId = $pid";
					
					$sql_query_dr = mysql_query($query_dr, $this->dbh);
					$no = mysql_num_rows($sql_query_dr);
					
						$data_result = array();
						$x = 0;
						while($result = mysql_fetch_array($sql_query_dr)){
							$repDate = date("Y-m-d",strtotime($result['reportDate']));
							$data_result['userId'] = $result['userId'];
							$data_result['userName'] = $result['userName'];
							$data_result['attendance'] = $result['attendance'];
							$data_result['Demo'] = $result['noOfDemo'];
							$data_result['Sales'] = $result['noOfSales'];
							$data_result['total_watertest'] = $result['total_water_test'];
							$data_result['sold'] = $result['total_sale'];
							$data_result['no_sold'] = $result['total_not_sold'];
							if($result['noOfSales'] != 0){
								$data_result['product_list'] = $this->getProductList($pid,$cdate);	
							}
							
							
							$return_results_top[] = $data_result;	
							$x++;	
						}
						return  json_encode(array('result'=>'TRUE','ReportList'=>$return_results_top));
				}
			}
		}else{
			$demo = "";
			$sale = "";
			$team = "";
			$total_water_test = "";
			$total_sale = "";
			$total_not_sold = "";

			$return_results_top = array();
			$query = "select distinct(h.parentUserId) as parent_id,
			GetFamilyTree(u.userId) as sale_person_id
			from tbl_user_hierarchy h,tbl_user_master u  where h.parentUserId = u.userId and h.parentUserId = $pid";
			
			$sql_query = mysql_query($query, $this->dbh);
			$rcount = mysql_num_rows($sql_query);
			if($rcount == 1){
				while($result = mysql_fetch_array($sql_query)){
				$parentid = $result['parent_id'];	
				$sale_person_id = explode(",",$result['sale_person_id']);	
				$all_id = array_push($sale_person_id, $parentid);

				foreach($sale_person_id as $value){
						
					$query_dr = "select date(reportDate) as reportDate ,CAST(GROUP_CONCAT(dr.dailyReportId SEPARATOR ', ') AS CHAR) as dailyReportId, dr.userId, dr.attendance, sum(dr.noOfDemo) as noOfDemo,sum(dr.noOfSales) as noOfSales, um.userName,(select count(userId) FROM tbl_water_test WHERE date(entryDate) between '$fdate' and '$tdate' AND userId = dr.userId group by userId) as total_water_test,(select count(userId) FROM tbl_water_test WHERE product_purchased = 1 and date(entryDate) between '$fdate' and '$tdate' AND userId = dr.userId group by userId) as total_sale,(select count(userId) FROM tbl_water_test WHERE product_purchased = 0 and date(entryDate) between '$fdate' and '$tdate' AND userId = dr.userId group by userId) as total_not_sold from tbl_daily_report as dr LEFT outer JOIN tbl_user_master as um ON um.userId = dr.userId where date(reportDate) between '$fdate' and '$tdate' and dr.userId = $value group by um.userId";
					
						$sql_query_dr = mysql_query($query_dr, $this->dbh);
						$no = mysql_num_rows($sql_query_dr);
						
							$data_result = array();
							$x = 0;

							while($result = mysql_fetch_array($sql_query_dr)){
								
							$data_result['userId'] = $result['userId'];
							$data_result['userName'] = $result['userName'];
							$data_result['attendance'] = $result['attendance'];
							$data_result['Demo'] = $result['noOfDemo'];
							$data_result['Sales'] = $result['noOfSales'];
							
							if($result['total_sale'] == ""){
								$data_result['total_watertest'] = "0";
							}else{
								$data_result['total_watertest'] = $result['total_water_test'];
							}
							if($result['total_sale'] == ""){
								$data_result['sold'] = "0";
							}else{
								$data_result['sold'] = $result['total_sale'];
							}
							
							if($result['total_not_sold'] == ""){
								$data_result['no_sold'] = "0";
							}else{
								$data_result['no_sold'] = $result['total_not_sold'];
							}
							
							$repDate = $result['repDate'];
							
							if($result['noOfSales'] != 0){
								$data_result['product_list'] = $this->getProductList_tdate($value,$fdate,$tdate);	
							}
							
							$return_results_top[] = $data_result;
							$x++;
							
							$demo1 = $result['noOfDemo'];
							$sale1 = $result['noOfSales'];
							$team1 = $result['userId'];
							
							if($result['total_water_test'] == ""){
								$total_water_test1 = 0;
							}else{
								$total_water_test1 = $result['total_water_test'];	
								
							}
							if($result['total_sale'] == ""){
								$total_sale1 = 0;
							}else{
								$total_sale1 = $result['total_sale'];	
								
							}
							if($result['total_not_sold'] == ""){
								$total_not_sold1 = 0;
							}else{
								$total_not_sold1 = $result['total_not_sold'];
								
							}
							
							
							
							
							$demo += $demo1 ;
							$sale += $sale1 ;
							$total_water_test += $total_water_test1;
							$total_sale += $total_sale1;
							$total_not_sold += $total_not_sold1;
								

							}
								$total_reportgl_new = array();
								$team = sizeof($sale_person_id);
								$total_reportgl['from_date'] = $fdate;
								$total_reportgl['to_date'] = $tdate;
								$total_reportgl['team_size'] = $team;
								$total_reportgl['total_demo'] = $demo;
								$total_reportgl['total_sales'] = $sale;
								$total_reportgl['total_watertest'] = $total_water_test;
								$total_reportgl['total_sold'] = $total_sale;
								$total_reportgl['total_no_sold'] = $total_not_sold;
								$total_reportgl_new[] = $total_reportgl;
						}
						
						return  json_encode(array('result'=>'TRUE','Total_Report'=>$total_reportgl_new,'ReportList'=>$return_results_top));
				}
			
			}else{
				$query_dr = "select date(reportDate) as reportDate ,CAST(GROUP_CONCAT(dr.dailyReportId SEPARATOR ', ') AS CHAR) as dailyReportId, dr.userId, dr.attendance, sum(dr.noOfDemo) as noOfDemo,sum(dr.noOfSales) as noOfSales, um.userName,(select count(userId) FROM tbl_water_test WHERE date(entryDate) between '$fdate' and '$tdate' AND userId = dr.userId group by userId) as total_water_test,(select count(userId) FROM tbl_water_test WHERE product_purchased = 1 and date(entryDate) between '$fdate' and '$tdate' AND userId = dr.userId group by userId) as total_sale,(select count(userId) FROM tbl_water_test WHERE product_purchased = 0 and date(entryDate) between '$fdate' and '$tdate' AND userId = dr.userId group by userId) as total_not_sold from tbl_daily_report as dr LEFT outer JOIN tbl_user_master as um ON um.userId = dr.userId where date(reportDate) between '$fdate' and '$tdate' and dr.userId = $pid group by um.userId";
					
				$sql_query_dr = mysql_query($query_dr, $this->dbh);
				$no = mysql_num_rows($sql_query_dr);
				
					$data_result = array();
					$x = 0;

					while($result = mysql_fetch_array($sql_query_dr)){
						
					$data_result['userId'] = $result['userId'];
					$data_result['userName'] = $result['userName'];
					$data_result['attendance'] = $result['attendance'];
					$data_result['Demo'] = $result['noOfDemo'];
					$data_result['Sales'] = $result['noOfSales'];
					
					if($result['total_sale'] == ""){
						$data_result['total_water_test'] = "0";
					}else{
						$data_result['total_watertest'] = $result['total_water_test'];
					}
					if($result['total_sale'] == ""){
						$data_result['sold'] = "0";
					}else{
						$data_result['sold'] = $result['total_sale'];
					}
					
					if($result['total_not_sold'] == ""){
						$data_result['no_sold'] = "0";
					}else{
						$data_result['no_sold'] = $result['total_not_sold'];
					}
					
					$repDate = $result['repDate'];
					
					if($result['noOfSales'] != 0){
						$data_result['product_list'] = $this->getProductList_tdate($pid,$fdate,$tdate);	
					}
					
					$return_results_top[] = $data_result;
					$x++;
					
					$demo1 = $result['noOfDemo'];
					$sale1 = $result['noOfSales'];
					$team1 = $result['userId'];
					
					if($result['total_water_test'] == ""){
						$total_water_test1 = 0;
					}else{
						$total_water_test1 = $result['total_water_test'];	
						
					}
					if($result['total_sale'] == ""){
						$total_sale1 = 0;
					}else{
						$total_sale1 = $result['total_sale'];	
						
					}
					if($result['total_not_sold'] == ""){
						$total_not_sold1 = 0;
					}else{
						$total_not_sold1 = $result['total_not_sold'];
						
					}
					
					$demo += $demo1 ;
					$sale += $sale1 ;
					$total_water_test += $total_water_test1;
					$total_sale += $total_sale1;
					$total_not_sold += $total_not_sold1;
						

					}
					$total_reportgl_new = array();
					$team = sizeof($sale_person_id);
					$total_reportgl['from_date'] = $fdate;
					$total_reportgl['to_date'] = $tdate;
					$total_reportgl['team_size'] = $team;
					$total_reportgl['total_demo'] = $demo;
					$total_reportgl['total_sales'] = $sale;
					$total_reportgl['total_watertest'] = $total_water_test;
					$total_reportgl['total_sold'] = $total_sale;
					$total_reportgl['total_no_sold'] = $total_not_sold;
					$total_reportgl_new[] = $total_reportgl;
			return  json_encode(array('result'=>'TRUE','Total_Report'=>$total_reportgl_new,'ReportList'=>$return_results_top));
			}

		}
		
		
	}
	}
	



	function getMinEntrydate(){
		$query = "SELECT MIN(date(reportDate)) as minReportDate FROM tbl_daily_report";
		$sql_query = mysql_query($query, $this->dbh);
		$result = mysql_fetch_array($sql_query);
		$date = $result['minReportDate'];
		return $date;
	}
	
	function getCustomerList($spid,$fdate){
		
		$query = "select wt.customer_name,wt.customer_mobile,wt.address , c.city_name , s.state_name , wt.product_purchased , wt.electrolysis , wt.remark from tbl_water_test as wt LEFT OUTER JOIN tbl_city as c ON c.city_id = wt.city_id LEFT OUTER JOIN tbl_state as s ON s.state_Id = wt.state_id
			where date(wt.entryDate) = '$fdate' and wt.userId = $spid";
		$sql_query = mysql_query($query, $this->dbh);
		if(mysql_num_rows($sql_query) > 0){
			//echo $r_count;
			$data_result = array();
			while($result = mysql_fetch_array($sql_query)){
				$results['customer_name'] = $result['customer_name'];
				$results['customer_mobile'] = $result['customer_mobile'];	
				$results['address'] = $result['address'];	
				$results['city_name'] = $result['city_name'];	
				$results['state_name'] = $result['state_name'];	
				if($result['product_purchased'] == 0){
					$product = "No Sold";
				}else{
					$product = "Sold Out";
				}
				$results['product_purchased'] = $product;	
				if($result['electrolysis'] == 0){
					$electrolysis = "Test Negative";
				}else{
					$electrolysis = "Test Positive";
				}
				$results['electrolysis'] = $electrolysis;	
				if(!empty($result['remark'])){
					$remark = $result['remark'];
				}else{
					$remark = "";
				}
				$results['remark'] = $remark;	
				$data_result[] = $results;
			}
			return $data_result;
		}
		
	}
	function getProductList_tdate($spid,$fdate,$tdate){
		$top_array = array();
		$query = "SELECT date(sr.entryDate) as rep_date ,CAST(GROUP_CONCAT(tpm.productName SEPARATOR ', ') AS CHAR) as product_name,CAST(GROUP_CONCAT(sr.quantitySale SEPARATOR ', ') AS CHAR) as  quantity , (select sum(noOfDemo) from tbl_daily_report WHERE userId = $spid and date(reportDate) =  date(dr.reportDate)) as noOfDemo , (select sum(noOfSales) from tbl_daily_report WHERE userId = $spid and date(reportDate) =  date(dr.reportDate)) as noOfSales, (SELECT count(userId) from tbl_water_test WHERE userId = $spid AND date(entryDate) = date(dr.reportDate)) as noOfWaterTest FROM tbl_daily_report as dr LEFT JOIN tbl_sales_report as sr ON dr.dailyReportId = sr.dailyReportId LEFT JOIN tbl_product_master as tpm ON sr.productId = tpm.productId where dr.userId = $spid and date(sr.entryDate) Between '$fdate' and '$tdate' group by date(sr.entryDate)";
		
		$sql_query = mysql_query($query, $this->dbh);
		if($r_count = mysql_num_rows($sql_query) > 0){
			//echo $r_count;
			$data_result = array();
			while($result = mysql_fetch_object($sql_query)){
				$results['date'] = $result->rep_date;
				$results['noOfDemo'] = $result->noOfDemo;
				$results['noOfSales'] = $result->noOfSales;
				$results['noOfWaterTest'] = $result->noOfWaterTest;
				$results['product_name'] = $result->product_name;
				$results['product_quantity'] = $result->quantity;
				$data_result[] = $results;
			}
			
			return $data_result;
		}
		
	}
	
	function getProductList($spid,$fdate){
		
		$query = "SELECT date(sr.entryDate) as rep_date ,CAST(GROUP_CONCAT(tpm.productName SEPARATOR ', ') AS CHAR) as product_name,CAST(GROUP_CONCAT(sr.quantitySale SEPARATOR ', ') AS CHAR) as  quantity , (select sum(noOfDemo) from tbl_daily_report WHERE userId = $spid and date(reportDate) =  date(dr.reportDate)) as noOfDemo , (select sum(noOfSales) from tbl_daily_report WHERE userId = $spid and date(reportDate) =  date(dr.reportDate)) as noOfSales, (SELECT count(userId) from tbl_water_test WHERE userId = $spid AND date(entryDate) = date(dr.reportDate)) as noOfWaterTest FROM tbl_daily_report as dr LEFT JOIN tbl_sales_report as sr ON dr.dailyReportId = sr.dailyReportId LEFT JOIN tbl_product_master as tpm ON sr.productId = tpm.productId where dr.userId = $spid and date(sr.entryDate) = '$fdate' group by date(sr.entryDate)";
		$sql_query = mysql_query($query, $this->dbh);
		if($r_count = mysql_num_rows($sql_query) > 0){
			//echo $r_count;
			$data_result = array();
			while($result = mysql_fetch_object($sql_query)){
				
				if(!empty($result->product_name) && !empty($result->quantity)){
					$results['date'] = $result->rep_date;
				$results['noOfDemo'] = $result->noOfDemo;
				$results['noOfSales'] = $result->noOfSales;
				$results['noOfWaterTest'] = $result->noOfWaterTest;
				$results['product_name'] = $result->product_name;
				$results['product_quantity'] = $result->quantity;
					$data_result[] = $results;
				}
			}
			return $data_result;
		}
		
	}

function getProductListsp($spid,$fdate){
		
		$query = "SELECT 
					tpm.productName as product_name,
					sr.quantitySale as quantity
				  FROM tbl_daily_report as dr
				  LEFT JOIN tbl_sales_report as sr ON dr.dailyReportId = sr.dailyReportId
				  LEFT JOIN tbl_product_master as tpm ON sr.productId = tpm.productId
				where dr.userId = $spid and date(sr.entryDate) = '$fdate'";
		$sql_query = mysql_query($query, $this->dbh);
		if($r_count = mysql_num_rows($sql_query) > 0){
			//echo $r_count;
			$data_result = array();
			while($result = mysql_fetch_array($sql_query)){
				
				if(!empty($result['product_name']) && !empty($result['quantity'])){
					$results['product_name'] = $result['product_name'];
					$results['quantity'] = $result['quantity'];	
					$data_result[] = $results;
				}
			}
			return $data_result;
		}
		
	}
	
	
	function chkdailyReportInsert($userId,$parentId){
		$query = "SELECT dailyReportId FROM tbl_daily_report WHERE date(reportDate) = date(CURRENT_DATE()) AND userId = $userId AND cb = $parentId";
		
		$sqlQuery = mysql_query($query, $this->dbh);
		$result = mysql_numrows($sqlQuery);
		return $result;
	
	}
	
	function viewSalePersonList($pid){
		if(empty($pid)){
			return  json_encode(array('result'=>'FALSE','msg'=>'Enter parent Id'));	
		}else{
		
		$return_results_top = array();
		
		$query_dr = "SELECT um.userId,um.userName,um.weekOff,uh.parentUserId FROM tbl_user_master as um LEFT JOIN tbl_role_master as rm ON um.roleId = rm.roleId LEFT JOIN tbl_user_hierarchy as uh ON um.userId = uh.userId where um.userId IN ( select um.userId from tbl_user_master as um LEFT JOIN tbl_user_hierarchy as uh ON uh.userId = um.userId WHERE uh.parentUserId = $pid or um.userId = $pid)";
		
		$sql_query_dr = mysql_query($query_dr, $this->dbh);
		$no = mysql_num_rows($sql_query_dr);
		$data_result = array();
		$x = 0;
		while($result = mysql_fetch_array($sql_query_dr)){
		$parentid = $result['parentUserId'];
		$userid = $result['userId'];
		$data_result['userId'] = $result['userId'];
		$data_result['userName'] = $result['userName'];
		$data_result['parentUserId'] = $result['parentUserId'];
		$data_result['weekOff'] = $result['weekOff'];
		$data_result['dailySale'] = $this->chkdailyReportInsert($userid,$parentid);
		$return_results_top[] = $data_result;	
		$x++;	
		}
		return  json_encode(array('result'=>'TRUE','ReportList'=>$return_results_top));
		}
	}
	
	function viewMySaleReport($uid,$fdate,$tdate){
		
		if(empty($uid)){
			
			return  json_encode(array('result'=>'FALSE','msg'=>'Enter User Id'));	
			
		}else{
		
		$return_results_top = array();
		
		$minEntryDate = $this->getMinEntrydate();	
		if(empty($fdate) && empty($tdate)){
			$cd = date("y-m-d");
			$query_dr = "select dr.dailyReportId, dr.reportDate, dr.userId, dr.attendance, dr.noOfDemo, dr.noOfSales , um.userName, (select count(userId) from tbl_water_test WHERE userId = $uid AND date(entryDate) = '$cd' ) as total_watertest , (select count(product_purchased) from tbl_water_test WHERE product_purchased = 0 AND userId = $uid AND date(entryDate) = '$cd' ) as no_sale , (select count(product_purchased) from tbl_water_test WHERE product_purchased = 1 AND userId = $uid AND date(entryDate) = '$cd') as sale
				from tbl_daily_report as dr 
				LEFT OUTER JOIN tbl_user_master as um ON um.userId = dr.userId 
				LEFT OUTER JOIN tbl_water_test as wt ON wt.userId = dr.userId
				where date(reportDate) = '$cd' and dr.userId = $uid group by dr.userId";
				$sql_query_dr = mysql_query($query_dr, $this->dbh);
				$no = mysql_num_rows($sql_query_dr);
				if($no >0){
					$data_result = array();
					$x = 0;
					while($result = mysql_fetch_array($sql_query_dr)){
						$data_result['reportDate'] = date("Y-m-d", strtotime($result['reportDate']));
						$data_result['userId'] = $result['userId'];
						$data_result['userName'] = $result['userName'];
						$data_result['attendance'] = $result['attendance'];
						$data_result['Demo'] = $result['noOfDemo'];
						$data_result['Sales'] = $result['noOfSales'];
						$data_result['product_list'] = $this->getProductListsp($uid,$cd);
						$data_result['total_watertest'] = $result['total_watertest'];
						$data_result['sold'] = $result['sale'];
						$data_result['no_sold'] = $result['no_sale'];
						
						$return_results_top[] = $data_result;	
						$x++;	
					}
					return  json_encode(array('result'=>'TRUE','ReportList'=>$return_results_top));
				}else{
					return  json_encode(array('result'=>'FALSE','msg'=>"No Report Found"));
				}
				
		}else if(empty($fdate)){
			if($tdate < $minEntryDate){
				$errormsg = "No Sale Report Available for this date: ".$tdate;	
				return json_encode(array('result'=>'FALSE','msg'=>$errormsg));		
			}else{
				$query_dr = "select dr.dailyReportId, dr.reportDate, dr.userId, dr.attendance, dr.noOfDemo, dr.noOfSales , um.userName, (select count(userId) from tbl_water_test WHERE userId = $uid AND date(entryDate) = '$tdate' ) as total_watertest , (select count(product_purchased) from tbl_water_test WHERE product_purchased = 0 AND userId = $uid AND date(entryDate) = '$tdate' ) as no_sale , (select count(product_purchased) from tbl_water_test WHERE product_purchased = 1 AND userId = $uid AND date(entryDate) = '$tdate') as sale
				from tbl_daily_report as dr 
				LEFT OUTER JOIN tbl_user_master as um ON um.userId = dr.userId 
				LEFT OUTER JOIN tbl_water_test as wt ON wt.userId = dr.userId
				where date(reportDate) = '$tdate' and dr.userId = $uid group by dr.userId";
				$sql_query_dr = mysql_query($query_dr, $this->dbh);
				$no = mysql_num_rows($sql_query_dr);
				if($no >0){
					$data_result = array();
					$x = 0;
					while($result = mysql_fetch_array($sql_query_dr)){
						$cd = date("Y-m-d", strtotime($result['reportDate']));
						$data_result['reportDate'] = date("Y-m-d", strtotime($result['reportDate']));
						$data_result['userId'] = $result['userId'];
						$data_result['userName'] = $result['userName'];
						$data_result['attendance'] = $result['attendance'];
						$data_result['Demo'] = $result['noOfDemo'];
						$data_result['Sales'] = $result['noOfSales'];
						
						$data_result['product_list'] = $this->getProductListsp($uid,$tdate);

						$data_result['total_watertest'] = $result['total_watertest'];
						$data_result['sold'] = $result['sale'];
						$data_result['no_sold'] = $result['no_sale'];
						
						
						$return_results_top[] = $data_result;	
						$x++;	
					}
					return  json_encode(array('result'=>'TRUE','ReportList'=>$return_results_top));
				}else{
					return  json_encode(array('result'=>'FALSE','msg'=>"No Report Found"));
				}
			}
		}else if(empty($tdate)){
			if($fdate < $minEntryDate){
				$errormsg = "No Sale Report Available for this date: ".$fdate;	
				return json_encode(array('result'=>'FALSE','msg'=>$errormsg));		
			}else{
				$query_dr = "select dr.dailyReportId, dr.reportDate, dr.userId, dr.attendance, dr.noOfDemo, dr.noOfSales , um.userName, (select count(userId) from tbl_water_test WHERE userId = $uid AND date(entryDate) = '$fdate' ) as total_watertest , (select count(product_purchased) from tbl_water_test WHERE product_purchased = 0 AND userId = $uid AND date(entryDate) = '$fdate' ) as no_sale , (select count(product_purchased) from tbl_water_test WHERE product_purchased = 1 AND userId = $uid AND date(entryDate) = '$fdate') as sale
				from tbl_daily_report as dr 
				LEFT OUTER JOIN tbl_user_master as um ON um.userId = dr.userId 
				LEFT OUTER JOIN tbl_water_test as wt ON wt.userId = dr.userId
				where date(reportDate) = '$fdate' and dr.userId = $uid group by dr.userId";
				
				$sql_query_dr = mysql_query($query_dr, $this->dbh);
				$no = mysql_num_rows($sql_query_dr);
				if($no >0){
					$data_result = array();
					
					while($result = mysql_fetch_array($sql_query_dr)){
						$cd = date("Y-m-d", strtotime($result['reportDate']));
						$data_result['reportDate'] = date("Y-m-d", strtotime($result['reportDate']));
						$data_result['userId'] = $result['userId'];
						$data_result['userName'] = $result['userName'];
						$data_result['attendance'] = $result['attendance'];
						$data_result['Demo'] = $result['noOfDemo'];
						$data_result['Sales'] = $result['noOfSales'];
						
						$data_result['product_list'] = $this->getProductListsp($uid,$fdate);

						$data_result['total_watertest'] = $result['total_watertest'];
						$data_result['sold'] = $result['sale'];
						$data_result['no_sold'] = $result['no_sale'];
						

						$return_results_top[] = $data_result;	
					
					}
					return  json_encode(array('result'=>'TRUE','ReportList'=>$return_results_top));
				}else{
					return  json_encode(array('result'=>'FALSE','msg'=>"No Report Found"));
				}
			}
		}else{
			$query_dr = "select dr.dailyReportId, dr.reportDate, dr.userId, dr.attendance, dr.noOfDemo, dr.noOfSales, (select count(userId) from tbl_water_test WHERE userId = $uid AND date(entryDate) = date(dr.reportDate) ) as total_watertest,(select count(userId) from tbl_water_test WHERE product_purchased = 0 AND userId = $uid AND date(entryDate) = date(dr.reportDate) ) as nosale,(select count(userId) from tbl_water_test WHERE product_purchased = 1 AND userId = $uid AND date(entryDate) = date(dr.reportDate) ) as sale ,um.userName from tbl_daily_report as dr LEFT outer JOIN tbl_user_master as um ON um.userId = dr.userId LEFT OUTER JOIN tbl_water_test as wt ON wt.userId = dr.userId where date(reportDate) between '$fdate' and '$tdate' and dr.userId = $uid GROUP by date(reportDate)";
				$sql_query_dr = mysql_query($query_dr, $this->dbh);
				$no = mysql_num_rows($sql_query_dr);
				if($no >0){
					$data_result = array();
					$x = 0;
					while($result = mysql_fetch_array($sql_query_dr)){
						$cd = date("Y-m-d", strtotime($result['reportDate']));
						$data_result['reportDate'] = date("Y-m-d", strtotime($result['reportDate']));
						$data_result['userId'] = $result['userId'];
						$data_result['userName'] = $result['userName'];
						$data_result['attendance'] = $result['attendance'];
						$data_result['Demo'] = $result['noOfDemo'];
						$data_result['Sales'] = $result['noOfSales'];
						
						$data_result['product_list'] = $this->getProductListsp($uid,$cd);
						$data_result['total_watertest'] = $result['total_watertest'];
						$data_result['sold'] = $result['sale'];
						$data_result['no_sold'] = $result['nosale'];
						
						$return_results_top[] = $data_result;	
						$total_report = $this->getTotalReport($uid,$fdate,$tdate);
						$x++;	
					}
					return  json_encode(array('result'=>'TRUE',"Total_Report"=>$total_report,'ReportList'=>$return_results_top));
				}else{
					return  json_encode(array('result'=>'FALSE','msg'=>"No Report Found"));
				}
		}
			
					
			
				
			}
			
		}	

	
		function getTotalReport($uid,$fdate,$tdate)
		{
			$return_results_top = array();
			$query = "select MIN(date(dr.reportDate)) as from_date,MAX(date(dr.reportDate)) as to_date, um.userName,(select sum(dr.noOfDemo) from tbl_daily_report WHERE userId = $uid GROUP BY userId) as total_demo, (select sum(dr.noOfSales) from tbl_daily_report WHERE userId = $uid GROUP BY userId) as total_sales , (SELECT count(userId) FROM tbl_water_test WHERE userId = $uid AND date(entryDate) between '$fdate' AND '$tdate') as total_water_test, (SELECT count(userId) FROM tbl_water_test WHERE userId = $uid AND product_purchased = 0 AND date(entryDate) between '$fdate' AND '$tdate') as total_not_sold ,(SELECT count(userId) FROM tbl_water_test WHERE userId = $uid AND product_purchased = 1 AND date(entryDate) between '$fdate' AND '$tdate') as total_sold 
				from tbl_daily_report as dr 
				LEFT JOIN tbl_user_master as um ON um.userId = dr.userId 
				where date(reportDate) between '$fdate' AND '$tdate' and dr.userId = $uid";
				$sql_query_dr = mysql_query($query, $this->dbh);
					$no = mysql_num_rows($sql_query_dr);
					if($no >0){
						$data_result = array();
						$x = 0;
						$result = mysql_fetch_array($sql_query_dr);
						$data_result['userName'] = $result['userName'];
						$data_result['from_date'] = $fdate;
						$data_result['to_date'] = $tdate;
						$data_result['total_demo'] = $result['total_demo'];
						$data_result['total_sales'] = $result['total_sales'];
						$data_result['total_watertest'] = $result['total_water_test'];
						$data_result['total_sold'] = $result['total_sold'];
						$data_result['total_no_sold'] = $result['total_not_sold'];
						$return_results_top[] = $data_result;
					}
					return $return_results_top;
		}



		function getProfile_pic($uid){
			$select = "SELECT profile_pic FROM tbl_user_master WHERE userId = $uid LIMIT 1";
			$query = mysql_query($select, $this->dbh);
			$result = mysql_fetch_array($query);
			
			return $result;
		}


	
// admin methods	
function viewRole($action){
		$return_results_top = array();
		$query = "SELECT roleId,roleName,roleDiscription,isActive,role_seq FROM tbl_role_master where isActive = 1 order by role_seq ASC";
		$sql_query = mysql_query($query, $this->dbh);
		if(mysql_num_rows($sql_query) > 0){
			$data_result = array();
			while($result = mysql_fetch_array($sql_query)){
				$data_result['roleId'] = trim($result['roleId']);
				$data_result['roleName'] = trim($result['roleName']);
				$data_result['roleDiscription'] = trim($result['roleDiscription']);
				$data_result['isActive'] = trim($result['isActive']);
				$data_result['role_seq'] = trim($result['role_seq']);
				
				$return_results_top[] = $data_result;	
			}
			return  json_encode(array('result'=>'TRUE','RoleList'=>$return_results_top));
		}
		$return_msg = "No Data Found ";
		return  json_encode(array('result'=>'False','msg'=>$return_msg));
	}
	
function getReportingName($role_id){
		$return_results_top = array();
		$query = "SELECT um.userId,um.userName,rm.roleName FROM tbl_user_master as um LEFT JOIN tbl_role_master as rm ON um.roleId = rm.roleId LEFT JOIN tbl_user_hierarchy as uh ON um.userId = uh.userId  WHERE rm.roleId < '$role_id' ORDER BY um.userId ASC";
		$sql_query = mysql_query($query, $this->dbh);
		if(mysql_num_rows($sql_query) > 0){
			$data_result = array();
			while($result = mysql_fetch_array($sql_query)){
				$data_result['userId'] = trim($result['userId']);
				$data_result['userName'] = trim($result['userName']);
				$data_result['roleName'] = trim($result['roleName']);
				$return_results_top[] = $data_result;	
			}
			return  json_encode(array('result'=>'TRUE','ReportingList'=>$return_results_top));
		}
		$return_msg = "No Data Found ";
		return  json_encode(array('result'=>'False','msg'=>$return_msg));
}
	
function checkRole($roleName){
	$query = "SELECT roleId FROM tbl_role_master WHERE roleName LIKE '$roleName' ";
	$sqlQuery = mysql_query($query, $this->dbh);
	$result = mysql_num_rows($sqlQuery);
	return $result;
}	
		
function checkProduct($productName){
	$query = "SELECT productId FROM tbl_product_master WHERE productName LIKE '$productName' ";
	$sqlQuery = mysql_query($query, $this->dbh);
	$result = mysql_numrows($sqlQuery);
	return $result;
}	
		
function selectState($state){
	$return_results_top = array();
	$query = "SELECT state_name,state_Id FROM tbl_state group by state_name";
	$sqlQuery = mysql_query($query, $this->dbh);
	if(mysql_num_rows($sqlQuery) > 0){
		$data_result = array();
		while($result = mysql_fetch_array($sqlQuery)){
			$data_result['stateId'] = trim($result['state_Id']);
			$data_result['stateName'] = trim($result['state_name']);
			$return_results_top[] = $data_result;	
		}
		return  json_encode(array('result'=>'TRUE','StateList'=>$return_results_top));
	}else{
		return  json_encode(array('result'=>'FALSE','Errormsg'=> mysql_error()));
	}
}

function selectCity($state_id){
	$return_results_top = array();
	$query = "SELECT distinct(city_name),city_id FROM tbl_city where state_id = $state_id order by city_name ASC";
	$sqlQuery = mysql_query($query, $this->dbh);
	if(mysql_num_rows($sqlQuery) > 0){
		$data_result = array();
		while($result = mysql_fetch_array($sqlQuery)){
			$data_result['city_id'] = trim($result['city_id']);
			$data_result['city_name'] = trim($result['city_name']);
			$return_results_top[] = $data_result;	
		}
		return  json_encode(array('result'=>'TRUE','CityList'=>$return_results_top));
	}
}

function getUserDetails($uid){
	$return_results_top = array();
	$select = "SELECT um.userId,um.userName,um.zone,um.emp_code,um.email,um.mobileno,um.isActive,rm.roleId,rm.roleName, uh.parentUserId,(select userName from tbl_user_master WHERE userId = uh.parentUserId) as reporting_to ,um.city as city_id,um.state as state_id, c.city_name ,s.state_name
					FROM tbl_user_master as um 
					LEFT JOIN tbl_role_master as rm ON um.roleId = rm.roleId 
					LEFT JOIN tbl_user_hierarchy as uh ON um.userId = uh.userId 
					LEFT JOIN tbl_city as c ON um.city = c.city_id
					LEFT JOIN tbl_state as s ON um.state = s.state_Id
					where um.userId = $uid";
	$query = mysql_query($select, $this->dbh);
	if(mysql_num_rows($query) >0){
		$data_result = array();
		$result = mysql_fetch_array($query);
		$data_result['zone'] = trim($result['zone']);
		$data_result['emp_code'] = trim($result['emp_code']);
		$data_result['userId'] = trim($result['userId']);
		$data_result['userName'] = trim($result['userName']);
		$data_result['email'] = trim($result['email']);
		$data_result['mobileno'] = trim($result['mobileno']);
		$data_result['isActive'] = trim($result['isActive']);
		$data_result['roleId'] = trim($result['roleId']);
		$data_result['roleName'] = trim($result['roleName']);
		$data_result['parentUserId'] = trim($result['parentUserId']);
		$data_result['reporting_to'] = trim($result['reporting_to']);
		$data_result['city_id'] = trim($result['city_id']);
		$data_result['city_name'] = trim($result['city_name']);
		$data_result['state_id'] = trim($result['state_id']);
		$data_result['state_name'] = trim($result['state_name']);
		$return_results_top[] = $data_result;	
		return json_encode(array("Result"=> "TURE","UserDetails"=>$return_results_top));
		
	}
	
		
}	
		
function getRoleDetails($role_id){
	$return_results_top = array();
	$select = "SELECT * from tbl_role_master where roleId = $role_id ";
	$query = mysql_query($select, $this->dbh);
	if(mysql_num_rows($query) >0){
		$data_result = array();
		$result = mysql_fetch_array($query);
		$data_result['roleId'] = trim($result['roleId']);
		$data_result['roleName'] = trim($result['roleName']);
		$data_result['roleDiscription'] = trim($result['roleDiscription']);
		$data_result['isActive'] = trim($result['isActive']);
		$return_results_top[] = $data_result;	
		return json_encode(array("Result"=> "TURE","RoleDetails"=>$return_results_top));
	}
}	
		
		

// date difference 
	function dateDiff($date)
	{
	  $mydate= date("Y-m-d H:i:s");
	  $theDiff="";
	  //echo $mydate;//2014-06-06 21:35:55
	  $datetime1 = date_create($date);
	  $datetime2 = date_create($mydate);
	  $interval = date_diff($datetime1, $datetime2);
	  //echo $interval->format('%s Seconds %i Minutes %h Hours %d days %m Months %y Year    Ago')."<br>";
	  $min=$interval->format('%i');
	  $sec=$interval->format('%s');
	  $hour=$interval->format('%h');
	  $mon=$interval->format('%m');
	  $day=$interval->format('%d');
	  $year=$interval->format('%y');
	  if($interval->format('%i%h%d%m%y')=="00000")
	  {
		//echo $interval->format('%i%h%d%m%y')."<br>";
		return $sec." sec";
	
	  } 
	
	else if($interval->format('%h%d%m%y')=="0000"){
	   return $min." Min";
	   }
	
	
	else if($interval->format('%d%m%y')=="000"){
	   return $hour." hr";
	   }
	
	
	else if($interval->format('%m%y')=="00"){
	   return $day." d";
	   }
	
	else if($interval->format('%y')=="0"){
	   return $mon." m";
	   }
	
	else{
	   return $year." y";
	   }
	
	}
	
	function getGl($action){

		$return_results_top = array();
		$select = "SELECT um.userName,um.emp_code,um.zone,um.userId,um.mobileno,um.email,um.state,s.state_name,c.city_name,um.city,um.weekOff,um.roleId,rm.roleName,d.dis_id,dm.dc,dm.distributer_name,uh.parentUserId,um.isActive,(select userName from tbl_user_master WHERE userId = uh.parentUserId) as reporting_to  from tbl_user_master as um LEFT JOIN tbl_role_master as rm ON rm.roleId = um.roleId LEFT JOIN tbl_distributor as d ON d.userId = um.userId LEFT JOIN tbl_distributor_master as dm ON dm.dis_id = d.dis_id LEFT JOIN tbl_state as s ON s.state_Id = um.state LEFT JOIN tbl_city as c ON c.city_id = um.city LEFT JOIN tbl_user_hierarchy as uh ON um.userId = uh.userId WHERE um.roleId = 4";
		$sql_query = mysql_query($select, $this->dbh);

		if(mysql_num_rows($sql_query) > 0){
			$result_array = array();
			while($result = mysql_fetch_array($sql_query)){
				$result_array['id'] = $result['userId'];
				$result_array['emp_code'] = $result['emp_code'];
				$result_array['zone'] = $result['zone'];
				$result_array['name'] = $result['userName'];
				$result_array['mobileno'] = $result['mobileno'];
				$result_array['email'] = $result['email'];
				$result_array['state_id'] = $result['state'];
				$result_array['state'] = $result['state_name'];
				$result_array['city_id'] = $result['city'];
				$result_array['city'] = $result['city_name'];
				$result_array['weekOff'] = $result['weekOff'];
				$result_array['roleId'] = $result['roleId'];
				$result_array['roleName'] = $result['roleName'];
				$result_array['parentUserId'] = $result['parentUserId'];
				$result_array['reporting_to'] = $result['reporting_to'];
				$result_array['dis_id'] = $result['dis_id'];
				$result_array['isActive'] = $result['isActive'];
				if(!empty($result['dc'])){
					$result_array['DistributorCode'] = $result['dc'];
				}else{
					$result_array['DistributorCode'] = "NA";
				}
				if(!empty($result['distributer_name'])){
					$result_array['distributer_name'] = $result['distributer_name'];
				}else{
					$result_array['distributer_name'] = "NA";
				}
				
				$return_results_top[] = $result_array;
			}
			return json_encode(array("result"=>"TRUE","GlList"=>$return_results_top));
		}else{
			return json_encode(array("result"=>"False","msg"=>mysql_error()));
		}

	}

	


	function getDistributorList($action){
		$return_results_top = array();
		$select = "SELECT dis_id,dc,distributer_name,isActive FROM tbl_distributor_master WHERE isActive = 1";
		$query = mysql_query($select, $this->dbh);
		if(mysql_num_rows($query) > 0){
			$result_array = array();
			while ($result = mysql_fetch_array($query)) {
				$result_array['dis_id'] = $result['dis_id'];
				$result_array['dis_code'] = $result['dc'];
				$result_array['dis_name'] = $result['distributer_name'];
				$result_array['isActive'] = $result['isActive'];
				$return_results_top[] = $result_array;
			}
			return json_encode(array("result"=> "TRUE","DisList"=>$return_results_top));
		}else{
			return json_encode(array("result"=> "False","errormsg"=>mysql_error()));
		}
	}

	

	function chkDistributorCode($glid){

		$select = "SELECT dsitributor_Id FROM `tbl_distributor` WHERE userId = $glid";
		$query = mysql_query($select, $this->dbh);
		if(mysql_num_rows($query) > 0){
			
			return 1;
		}else{
			return 0;
		}

	}

	function chkDCode($dc){
		$select = "SELECT dis_id FROM tbl_distributor_master WHERE dc LIKE '$dc'";
		$query = mysql_query($select, $this->dbh);
		if(mysql_num_rows($query) > 0){
			return 1;
		}else{
			return 0;
		}
	}
	
	function getManagementId($uid){
		$query = "SELECT mid from tbl_user_hierarchy WHERE userId = $uid limit 1";
		$sql_query = mysql_query($query, $this->dbh);
		$result = mysql_fetch_object($sql_query);
		$mid = $result->mid;
		return $mid;
	}
	
	function getPanId($uid){
		$query = "SELECT panid from tbl_user_hierarchy WHERE userId = $uid limit 1";
		$sql_query = mysql_query($query, $this->dbh);
		$result = mysql_fetch_object($sql_query);
		$panid = $result->panid;
		return $panid;
	}
	
	
	
	
}    

?>