<?php
class UserModel extends CI_Model
{
	//Constructor class with parent constructor
	function UserModel(){
		parent::__construct();
		$this->load->helper('cookie');
		$this->load->library('encrypt');
	}
	function insert_user(){
        $this->title   = $_POST['title']; // please read the below note
        $this->content = $_POST['content'];
        $this->date    = time();

        $this->db->insert('red_members', $this);
    }
	
	function create_user($input_array){
	
		$this->db->insert('red_members',$input_array);
                                
                               
  
                            return $this->db->insert_id();
	}
	
	function update_user($input_array,$conditions_array){
		$this->db->update('red_members',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	//*Update user member package*/
	function update_user_package($input_array,$conditions_array){
		$this->db->update('red_member_packages',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	
	//Delete function, actually updating 'is_deleted' status of table to 1
	function delete_user($user_id=0)
	{
		# Delete user's payment profile from database
		$this->db->delete('red_member_packages',array('member_id'=>$user_id));
		#delete user account from memeber table
		$this->db->delete('red_members',array('member_id'=>$user_id));
	}
	
	function get_user_data($conditions_array,$rows_per_page=10,$start=0,$package_join=false){
		$rows=array();
		if($_POST['mode']=='search' AND !isset($_POST['btn_cancel'])){
			if($_POST['field_name']){
				$fld_value= trim($_POST['field_value']);				
				if($_POST['field_name']=="status"){
					$fld_value=$_POST['select_status'];					
					if($fld_value=="Active-Paid"){
						$this->db->join('red_member_packages as rmp','rmp.member_id =rm.member_id');
						$this->db->join('red_packages as rp','rp.package_id =rmp.package_id');
						$this->db->where('`rm.'.$_POST['field_name'].'`','active');
						$this->db->where('`rmp.package_id` >','0');
						$this->db->where('`rmp.is_admin`','0');
						$this->db->where('DATEDIFF(next_payement_date,CURDATE())<=','30');
						$this->db->where('DATEDIFF(next_payement_date,CURDATE())>=','0');
					}else if($fld_value=="Admin-comped"){
						$this->db->join('red_member_packages as rmp','rmp.member_id =rm.member_id');
						$this->db->join('red_packages as rp','rp.package_id =rmp.package_id');
						$this->db->where('`rm.'.$_POST['field_name'].'`','active');
						$this->db->where('`rmp.package_id` >','0');
						$this->db->where('`rmp.is_admin`','1'); 
					}else if($fld_value=="Active-Free"){						
						$this->db->join('red_member_packages as rmp','rmp.member_id =rm.member_id ');
						$this->db->join('red_packages as rp','rp.package_id =rmp.package_id');
						$this->db->where('`rm.'.$_POST['field_name'].'`','active');
						$this->db->where('`rmp.package_id` <=','0');
						$this->db->where('`rmp.is_admin`','0');
					}else if($fld_value=="Inactive-Policy related"){
						$this->db->where('`rm.'.$_POST['field_name'].'`','inactive');
						$this->db->where('`rm.status_inactive_description`','policy related');
					}else if($fld_value=="Inactive- Unconfirmed"){
						$this->db->where('`rm.'.$_POST['field_name'].'`','inactive');
						$this->db->where('`rm.status_inactive_description`','unconfirmed');
					}else if($fld_value=="Inactive- Failed CC"){						
						$this->db->join('red_member_packages as rmp','rmp.member_id =rm.member_id');
						$this->db->join('red_packages as rp','rp.package_id =rmp.package_id');
						$this->db->where('`rm.'.$_POST['field_name'].'`','active');
						$this->db->where('`rmp.package_id` >','0');
						$this->db->where('`rmp.is_admin`','0');
						$this->db->where('DATEDIFF(next_payement_date,CURDATE())<','0');
					}
				}else if($_POST['field_name']=="member_id"){
					$this->db->where('`rm.member_id`',$fld_value);	
				}else if($_POST['field_name']!="package_id"){
					$this->db->like('`rm.'.$_POST['field_name'].'`',$fld_value);				
				}else{
					$this->db->join('red_member_packages as rmp','rmp.member_id =rm.member_id');
					$this->db->join('red_packages as rp','rp.package_id =rmp.package_id');					
					$fld_value=$_POST['select_package'];
					$this->db->where('`rp.'.$_POST['field_name'].'`',$fld_value);
				}
			}
		}else if($package_join){
			$this->db->join('red_member_packages as rmp','rmp.member_id =rm.member_id');
			$this->db->join('red_packages as rp','rp.package_id =rmp.package_id');	
		}
		$this->db->from('red_members as rm');
		$this->db->join('red_countries as rc','rm.country=rc.country_id');
		$this->db->where($conditions_array);
		$this->db->limit($rows_per_page,$start);
		$this->db->order_by('created_on','desc');
		$result=$this->db->get();
		 
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		$result->free_result(); 
		return $rows;
	
	}
	// get paid user detail
	
	//Function to fetch Users count
	function get_user_count($conditions_array=array(),$package_join=false)
	{
		$this->db->select('count(*) as count');
		if($_POST['mode']=='search' AND !isset($_POST['btn_cancel'])){
			if($_POST['field_name']){
				$fld_value=$_POST['field_value'];				
				if($_POST['field_name']=="status"){
					$fld_value=$_POST['select_status'];						
					if($fld_value=="Active-Paid"){
						$this->db->join('red_member_packages as rmp','rmp.member_id =rm.member_id ');
						$this->db->join('red_packages as rp','rp.package_id =rmp.package_id');
						$this->db->where('`rm.'.$_POST['field_name'].'`','active');
						$this->db->where('`rmp.package_id` >','0');
						$this->db->where('`rmp.is_admin`','0');
						$this->db->where('DATEDIFF(next_payement_date,CURDATE())<=','30');
						$this->db->where('DATEDIFF(next_payement_date,CURDATE())>=','0');
					}else if($fld_value=="Active-Free"){						
						$this->db->join('red_member_packages as rmp','rmp.member_id =rm.member_id ');
						$this->db->join('red_packages as rp','rp.package_id =rmp.package_id');
						$this->db->where('`rm.'.$_POST['field_name'].'`','active');
						$this->db->where('`rmp.package_id` <=','0');
						$this->db->where('`rmp.is_admin`','0');
					}else if($fld_value=="Inactive-Policy related"){
						$this->db->where('`rm.'.$_POST['field_name'].'`','inactive');
						$this->db->where('`rm.status_inactive_description`','policy related');
					}else if($fld_value=="Inactive- Unconfirmed"){
						$this->db->where('`rm.'.$_POST['field_name'].'`','inactive');
						$this->db->where('`rm.status_inactive_description`','unconfirmed');
					}else if($fld_value=="Inactive- Failed CC"){						
						$this->db->join('red_member_packages as rmp','rmp.member_id =rm.member_id');
						$this->db->join('red_packages as rp','rp.package_id =rmp.package_id');
						$this->db->where('`rm.'.$_POST['field_name'].'`','active');
						$this->db->where('`rmp.package_id` >','0');
						$this->db->where('`rmp.is_admin`','0');
						$this->db->where('DATEDIFF(next_payement_date,CURDATE())<','0');
					}
				}else if($_POST['field_name']!="package_id"){
					$this->db->like('`rm.'.$_POST['field_name'].'`',$fld_value);
				}else{
					$this->db->join('red_member_packages as rmp','rmp.member_id =rm.member_id');
					$this->db->join('red_packages as rp','rp.package_id =rmp.package_id');					
					$fld_value=$_POST['select_package'];
					$this->db->where('`rp.'.$_POST['field_name'].'`',$fld_value);
				}
			}
		}else if($package_join){
			$this->db->join('red_member_packages as rmp','rmp.member_id =rm.member_id');
			$this->db->join('red_packages as rp','rp.package_id =rmp.package_id');	
		}
		
		$this->db->from('red_members as rm');
		$this->db->join('red_countries as rc','rm.country=rc.country_id');
		
		$this->db->where($conditions_array);
		$result=$this->db->get();
		
		$row=$result->result_array() ;
		$result->free_result();
		return $row[0]['count'];
	}
	function get_user_package($conditions_array,$rows_per_page=10,$start=0){
		$rows=array();		
		$this->db->from('red_member_packages as rmp');
		$this->db->join('red_packages as rp','rp.package_id =rmp.package_id');
		$this->db->where($conditions_array);
		$this->db->limit($rows_per_page,$start);
		$result=$this->db->get();
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	function get_packages_data($conditions_array,$rows_per_page=10,$start=0)
	{
		$rows=array();
		//$result=$this->db->order_by("package_price", "asc")->get_where('red_packages',$conditions_array,$rows_per_page,$start);
		//$result=$this->db->order_by("package_price asc,is_special asc ,package_recurring_interval asc")->get_where('red_packages',$conditions_array,$rows_per_page,$start);
		$result=$this->db->order_by("package_recurring_interval asc,package_price asc")->get_where('red_packages',$conditions_array,$rows_per_page,$start);
		# echo $this->db->last_query();exit;
		foreach($result->result_array() as $row){
			$pid = $row['package_id'];
			$row['total_members'] = $this->packageUserCount($pid);
			$row['total_transactions'] = $this->packageTransactionCount($pid);			
			$rows[]=$row;
		}
		return $rows;
	}
	function packageUserCount($pid){
		$rsPackageUsers = $this->db->query("Select count(member_id) m from red_member_packages where package_id='$pid'");
		$packageUsers = $rsPackageUsers->row()->m;
		$rsPackageUsers->free_result();
		return $packageUsers;
	}
	function packageTransactionCount($pid){
		$rsPackageTransactions = $this->db->query("Select count(transaction_id) t from red_member_transactions where package_id='$pid'");
		$packageTransactions = $rsPackageTransactions->row()->t;
		$rsPackageTransactions->free_result();
		return $packageTransactions;
	}
	
	function get_packages_data_special($conditions_array,$rows_per_page=10,$start=0)
	{
		$rows=array();
		$result=$this->db->order_by("package_price", "asc")->get_where('red_packages',$conditions_array,$rows_per_page,$start);		
		 
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	function get_current_packages_maxcontact($mid){
		$package_id = $this->db->query("select `package_id` from `red_member_packages` where `member_id`='$mid'")->row()->package_id;
  
		if($package_id > 0){
			$package = $this->db->query("select `package_max_contacts`,`package_recurring_interval` from `red_packages` where `package_id`='$package_id'");
			$package = $package->result_array();
			//echo '<pre>';print_r($_SESSION);
			if($package[0]['package_recurring_interval'] == 'credit'){
				$this->db->from('red_member_send_email');
				$this->db->where(array('member_id'=>$this->session->userdata('member_id')));
				$creditPackage=$this->db->get()->result_array();
				//echo $this->db->last_query();exit;
				//print_r($creditPackage);
				if(count($creditPackage) > 0)
					//$package_max_contacts = $creditPackage[0]['max_email'];
					$package_max_contacts = $package[0]['package_max_contacts'];
				else
					//return 100;
					$package_max_contacts = $package[0]['package_max_contacts'];
			}else{
				$package_max_contacts = $package[0]['package_max_contacts'];
			}
			
			if($package_max_contacts > 0)return $package_max_contacts;
			exit;
		}	 
		return 100;
	}
	function get_user_plan_status($mid){
		$rs_package_id = $this->db->query("select `package_id` from `red_member_packages` where `member_id`='$mid' and package_id > 0 and (DATE_ADD(next_payement_date,INTERVAL 1 DAY) > now()  or is_admin=1)");	
		
		if($rs_package_id->num_rows() > 0){
			$package_id = $rs_package_id->row()->package_id;
			$package_max_contacts = $this->db->query("select `package_max_contacts` from `red_packages` where `package_id`='$package_id'")->row()->package_max_contacts;
			if($package_max_contacts > 0)return $package_max_contacts;
			exit;
		}else{
			
			$package_id = $this->db->query("select `package_id` from `red_member_packages` where `member_id`='$mid'")->row()->package_id;
			if($package_id > 0){
				$package = $this->db->query("select `package_max_contacts`,`package_recurring_interval` from `red_packages` where `package_id`='$package_id'");
				$package = $package->result_array();
				if($package[0]['package_recurring_interval'] == 'credit'){
					$creditPackage = $this->getAllCreditPackage(array('member_id'=>$mid));
					return $creditPackage;
				}
			}
		}	
		return 100;
	}	
 
	function getAllCreditPackage($conditionArray){
		$this->db->select('red_member_send_email . *');
		$this->db->from('red_member_send_email');
		$this->db->where($conditionArray);
		$result = $this->db->get();
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		
		return $rows[0]['max_email'] - $rows[0]['used_email'];
	}
	
	//Function to fetch packages count
	function get_packages_count($conditions_array=array())
	{
		$this->db->where($conditions_array);
		return $this->db->count_all_results('red_packages');
	}
	
	function create_package($input_array)
	{
		$this->db->insert('red_packages',$input_array);
		return $this->db->insert_id();
	}
	
	function update_package($input_array,$conditions_array)
	{
		$this->db->update('red_packages',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
	
	//Delete function, actually updating 'package_deleted' status of table to 1
	function delete_package($conditions_array)
	{
		$this->db->update('red_packages',array('package_deleted'=>1),$conditions_array);
		return $this->db->affected_rows();
	}
	
	function insert_payment_transactions($input_array)
	{
		$this->db->insert('red_member_transactions',$input_array);
		return $this->db->insert_id();
	} 
	// Start code - CB 
	function update_payment_transactions($input_array,$conditionArray)
	{
		$this->db->update('red_member_transactions',$input_array,$conditionArray);
		return $this->db->affected_rows();
	} 
	function get_payment_transactions($conditionArray)
	{
		$row = $this->db->get_where('red_member_transactions',$conditionArray);
    	return $row->result_array();
	} 
	// End:  code - CB 
	function insert_member_package($input_array)
	{
		$this->db->insert('red_member_packages',$input_array);
		return $this->db->insert_id();
	}
	
	function update_member_package($input_array,$conditions_array)
	{
		$this->db->update('red_member_packages',$input_array,$conditions_array);
		return $this->db->affected_rows();
	}
       
              /**************Added By cb 
	Function for add credit deatails according to user
    *********/    
    function creditAddPackageCredit($userCreditPackage=array()){
    	$this->db->insert('red_package_credit',$userCreditPackage);
		return $this->db->insert_id();
    }

    function creditUpdatePackageCredit($user=array(),$member = false){
		
    	if($member == '' )  {
    		$member = $this->session->userdata('member_id');
    	}
		
		$userCredit = $this->getCreditPackage(array('member_id'=>$member));
		//echo $this->db->last_query();echo '</br>';
		//echo '<pre>';print_r($userCredit);
    	$this->db->update('red_package_credit',$user,array('member_id'=>$member,'credit_id' => $userCredit[0]['credit_id']));
		$userCredit = $this->getCreditPackage(array('member_id'=>$member));
		//$userCredit = $this->getCreditPackage(array('member_id'=>$member,'));	
    	$result = $this->getMemberEmailSendCount($member);
    	//var_dump($result);
    	if($userCredit[0]['payment_process'] == '1' ){
			if(is_array($result) && $result[0]['max_email'] != ''){  
				$userCreditPackage['max_email'] = $userCredit[0]['credit_count'] + $result[0]['max_email'];
				$memberCreditPackage['member_id'] = $member;
				$this->db->update('red_member_send_email',$userCreditPackage,$memberCreditPackage);
			}else{ 
			
				$userCreditPackage['max_email'] = $userCredit[0]['credit_count'];
				$userCreditPackage['member_id'] = $member;
				$this->db->insert('red_member_send_email',$userCreditPackage);
			}
		} 
    	
    	return $userCredit[0]['credit_id'];

    }

    function getMemberEmailSendCount($mid){
    	$row = $this->db->order_by("count_email_id", "desc")->get_where('red_member_send_email',array('member_id'=>$mid),1,0);
		return $row->result_array();
    }

	function updateCreditPackage($input_array = array(),$conditionArray = array()){
    	$this->db->update('red_member_send_email',$input_array,$conditionArray);
    }
	
	/***********Get credit package for user Start Code By cb*************/
	function getCreditPackage($conditional = array(),$rows_per_page = 1){
		$this->db->select('red_package_credit . *');
		$this->db->from('red_package_credit');
		$this->db->where($conditional);
		if($rows_per_page!=0){
			$this->db->limit($rows_per_page,0);		 
		} 
		$this->db->order_by("credit_id", "desc");
		
		/*$this->db->order_by("credit_id", "desc")->get_where('red_package_credit',$conditional);
		if($rows_per_page!=0){
			
			$this->db->limit($rows_per_page,0);		 
		}
		
		echo $this->db->last_query(); */
		$result = $this->db->get();
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows; 
		
	}

	/***********Ended By Cb********/  
        
	
	function get_user_packages($conditions_array,$rows_per_page=10,$start=0)
	{
		$rows=array();
		 
		$result=$this->db->get_where('red_member_packages',$conditions_array,$rows_per_page,$start);
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		
		return $rows;
	}
	
	function get_user_paypal_packages($conditions_array,$rows_per_page=10,$start=0)
	{
		$rows=array();
		 
		// $result=$this->db->get_where('red_member_packages',$conditions_array,$rows_per_page,$start);
		
		$this->db->from('red_member_packages as rmp');
		$this->db->join('red_members as rp','rp.member_id =rmp.member_id');
		$this->db->where($conditions_array);
		$this->db->limit($rows_per_page,$start);
		$result=$this->db->get();
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		
		return $rows;
	}


	
    function get_user_packages_with_canceldt($conditions_array,$rows_per_page=10,$start=0)
	{
		$rows=array();
		$this->db->select('m.cancel_subscription_date,p.*');
		$this->db->join('red_members as m','m.member_id =p.member_id');
		$result=$this->db->get_where('red_member_packages p',$conditions_array,$rows_per_page,$start);
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		
		return $rows;
	}

    


	function get_user_packages_count($conditions_array=array())
	{
		$this->db->where($conditions_array);
		return $this->db->count_all_results('red_member_packages');
	}
	// Start:  code - CB 
    function get_user_transaction_count($conditions_array=array())
	{
		$this->db->where($conditions_array);
		return $this->db->count_all_results('red_member_transactions');
	}
	// End:  code - CB 
	function get_member_packages_count($conditions_array=array())
	{
		$this->db->where($conditions_array);
		$this->db->join('red_members as m','m.member_id =rp.member_id');
		return  $this->db->count_all_results('red_member_packages as rp');
	}
	
	function get_user_packages_with_details($conditions_array,$rows_per_page=10,$start=0,$sort_by='package_type')
	{
		$rows=array();
		$this->db->from('red_member_packages as rmp');
		$this->db->join('red_packages as rp','rmp.package_id=rp.package_id');
		$this->db->where($conditions_array);
		$this->db->limit($rows_per_page,$start);
		$this->db->order_by($sort_by);
		$result=$this->db->get();
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	
	function get_user_packages_with_details_count($conditions_array,$rows_per_page=10,$start=0)
	{
		$this->db->select('count(*) as count');
		$this->db->from('red_member_packages');
		$this->db->join('red_packages','red_member_packages.package_id=red_packages.package_id');
		$this->db->where($conditions_array);
		$this->db->limit($rows_per_page,$start);
		$result=$this->db->get();
		
		$row=$result->result_array() ;
		
		return $row[0]['count'];
	}
	
	//Delete function, actually updating 'is_deleted' status of table to 1
	function delete_user_package($conditions_array)
	{
		$this->db->update('red_member_packages',array('is_deleted'=>1),$conditions_array);
		return $this->db->affected_rows();
	}
	//Fetch vrdit card info
	function get_user_credit_card_info($conditions_array){
		$rows=array();
		$this->db->from('red_members as m');
		// $this->db->join('red_member_packages as rmp','rmp.red_member_package_id=m.package_id');
		$this->db->join('red_member_packages as rmp','rmp.member_id=m.member_id');
		$this->db->join('red_packages as rp','rmp.package_id=rp.package_id');
		$this->db->where($conditions_array);
		$result=$this->db->get();
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	
	//Fetch user transaction form user table
	function get_user_transactions($conditions_array,$rows_per_page=10,$start=0,$like=""){
		$rows=array();
		$this->db->select('rmp.*,m.company,m.phone_number,m.email_address,t.*,rp.*');
		$this->db->from('red_member_transactions as t');
		$this->db->join('red_member_packages as rmp','rmp.member_id=t.user_id');
		$this->db->join('red_members as m','m.member_id=t.user_id');
		$this->db->join('red_packages as rp','t.package_id=rp.package_id');
		$this->db->where($conditions_array);
		if($rows_per_page!=0){
			$this->db->limit($rows_per_page,$start);		 
		} 
		$this->db->order_by('t.transaction_id','desc');
		
		if($like){			
			
			$this->db->where('t.status = "SUCCESS"');
			#$this->db->like('t.gateway_response',"Ok,I00001,Successful");
		}
		#echo $this->db->_compile_select(); 
		$result=$this->db->get();
#		echo $this->db->last_query();
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	
	//Fetch user transaction form user table
	function get_user_transactions_pricing($conditions_array,$rows_per_page=10,$start=0,$like=""){
		$rows=array();
		$this->db->select('rmp.*,m.company,m.phone_number,m.email_address,t.*,rp.*');
		$this->db->from('red_member_transactions as t');
		$this->db->join('red_member_packages as rmp','rmp.member_id=t.user_id');
		$this->db->join('red_members as m','m.member_id=t.user_id');
		$this->db->join('red_packages as rp','t.package_id=rp.package_id');
		$this->db->where($conditions_array);
		if($rows_per_page!=0){
			$this->db->limit($rows_per_page,$start);		 
		} 
		$this->db->order_by('t.transaction_id','asc');
		
		if($like){			
			
			$this->db->where('t.status = "SUCCESS"');
			#$this->db->like('t.gateway_response',"Ok,I00001,Successful");
		}
		#echo $this->db->_compile_select(); 
		$result=$this->db->get();
#		echo $this->db->last_query();
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	//Fetch user transaction form user table
	function get_transaction_count($conditions_array,$like=""){
		$rows=array(); 
		$this->db->select('count(*) as count');
		$this->db->from('red_member_transactions as t');
		$this->db->join('red_member_packages as rmp','rmp.member_id=t.user_id');
		$this->db->join('red_members as m','m.member_id=t.user_id');
		$this->db->join('red_packages as rp','t.package_id=rp.package_id');
		$this->db->where($conditions_array);
		$this->db->order_by('t.transaction_id','desc');
		
		if($like){
			$this->db->where('t.gateway_response like"1,%"');#'t.gateway_response',"Admin"
		}
		$this->db->group_by('user_id');
		$result=$this->db->get();

		$row=$result->result_array() ;
		
		return $row[0]['count'];
	}
	/**
		Function to get countries
	**/
	function get_country_data()
	{
		$rows=array();
		$result=$this->db->get_where('red_countries');
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	
	}
	
	/**
	 *	Function get_subscriber_data
	 *
	 *	Function to fetch subscriber data
	 *
	 *	@param (array) (conditions_array)  conditions to checked with database with conditions
	 *
	 *	@param (string) (srch) to search records according to search condition submit by user
	 *
	 *	@param (string) (order_by)  define order by "Asc" or "Desc"
	 *
	 *	@param (string) (order_by_column)  // define order by column name
	 *
	 *	@param (int) (rows_per_page)  number of record per page
	 *
	 *	@param (int) (start)  These determine which number to start the record
	 *
	 *	@return (array)	return fetch records
	 */
	function get_subscriber_data($conditions_array=array(),$rows_per_page=10,$start=0)
	{
		$rows=array();
		$this->db->select('*');
		$this->db->from('red_email_subscribers as res');
		if($_POST['mode']=='search' AND !isset($_POST['btn_cancel'])){
				if($_POST['subscriber_email_address']){
					$subscriber_email_address=$_POST['subscriber_email_address'];
					$this->db->like('res.subscriber_email_address',$subscriber_email_address);
				}
				if($_POST['subscriber_name']){
					$subscriber_name=$_POST['subscriber_name'];
					$this->db->like('subscriber_first_name',$subscriber_name);
					$this->db->or_like('subscriber_last_name',$subscriber_name);
				}
				if($_POST['keyword']!=""){
					$keyword=$this->escape_str($_POST['keyword'],true);
					$this->db->where('(`subscriber_email_address` LIKE \'%'.$keyword.'%\' OR `subscriber_first_name` LIKE \'%'.$keyword.'%\' OR `subscriber_last_name` LIKE \'%'.$keyword.'%\')');
				}
		}
		$where = "( subscrber_bounce='0' OR ( subscrber_bounce='1' AND soft_bounce <=3 ) )";
		$this->db->where($where);		
		$this->db->where($conditions_array);
		$this->db->order_by('res.subscriber_email_address');
		$this->db->limit($rows_per_page, $start);		
		$result=$this->db->get();
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	
	
	/**
	 *	Function get_subscription_count
	 *
	 *	Function to fetch count of subscriber data
	 *
	 *	@param (array) (conditions_array)  conditions to checked with database with conditions
	 *
	 *	@param (string) (srch) to search records according to search condition submit by user
	 *
	 *	@return (int)	return total number of records
	 */
	function get_subscriber_count($conditions_array=array())
	{
		$rows=array();		
		$this->db->from('red_email_subscribers as res');
		$where = "( subscrber_bounce='0' OR ( subscrber_bounce='1' AND soft_bounce <=3 ) ) and (subscriber_status = 1)";
		#$where = "( subscrber_bounce='0' OR ( subscrber_bounce='1' AND soft_bounce <=3 ) ) ";
		$this->db->where($where);
		if($_POST['mode']=='search' AND !isset($_POST['btn_cancel'])){
				if($_POST['subscriber_email_address']){
					$subscriber_email_address=$_POST['subscriber_email_address'];
					$this->db->like('res.subscriber_email_address',$subscriber_email_address);
				}
				if($_POST['subscriber_name']){
					$subscriber_name=$_POST['subscriber_name'];
					$this->db->like('subscriber_first_name',$subscriber_name);
					$this->db->or_like('subscriber_last_name',$subscriber_name);
				}
				if($_POST['keyword']!=""){
					$keyword=$this->escape_str($_POST['keyword'],true);
					$this->db->where('(`subscriber_email_address` LIKE \'%'.$keyword.'%\' OR `subscriber_first_name` LIKE \'%'.$keyword.'%\' OR `subscriber_last_name` LIKE \'%'.$keyword.'%\')');
				}
		}		
		
		$this->db->where($conditions_array);
		
		$result=$this->db->get();		
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		
		return count($rows);
	}
	function escape_str($str,$like=false)
	{
		if (is_array($str))
        {
            foreach($str as $key => $val)
            {
                $str[$key] = $this->escape_str($val, $like);
            }
           
            return $str;
        }

        if (function_exists('mysql_real_escape_string') AND is_resource($this->conn_id))
        {
            $str = mysql_real_escape_string($str, $this->conn_id);
        }
        elseif (function_exists('mysql_escape_string'))
        {
            $str = mysql_escape_string($str);
        }
        else
        {
            $str = addslashes($str);
        }
        
        // escape LIKE condition wildcards
        if ($like === TRUE)
        {
            $str = str_replace(array('%', '_'), array('\\%', '\\_'), $str);
        }
        
        return $str;
	}
	/**
		Function delete_user_account is to delete  user's: Stats, campaigns, sign up forms, contacts form database
		@param user_id: user id
	*/
	function delete_user_account($user_id=0){
		#cancel user's subscription
		$this->cancel_subscription($user_id);
		#Delete user's stat
		$this->delete_stat($user_id);
		#Delete user's campaigns
		$this->delete_campaign($user_id);
		#Delete user's autoresponders
		$this->delete_autoresponder($user_id);
		#Delete user's signup forms
		$this->delete_signup_forms($user_id);
		#Delete user's contacts from database
		$this->delete_contacts($user_id);
	}
	/**
		Function delete_stat is to delete user's stat from database
		@param user_id: user id
	*/
	function delete_stat($user_id=0){
		##########################################
		# Delete Campaign's stat from database	 #
		##########################################
		# Delete user's stat from email track table
		$this->db->delete('red_email_track',array('user_id'=>$user_id));
		 
		# Delete user's stat from email track queue table
		$this->db->delete('red_email_queue',array('user_id'=>$user_id));
		 
		#Fetch autoresponder for user
		$rows=array();
		$this->db->select('campaign_id');
		$this->db->from('red_email_campaigns');
		$this->db->where(array('campaign_created_by'=>$user_id));
		$result=$this->db->get();		
		foreach($result->result_array() as $row)
		{
			# Delete user's stat from email track list table
			$this->db->delete('red_email_campaigns_scheduled',array('campaign_id'=>$row['campaign_id']));
		}
		
		# Delete user's click link detail from click rate table
		$this->db->delete('red_click_rate',array('user_id'=>$user_id));
		#Delete user's stat from email track archive table
		$this->db->delete('red_email_track_freezed',array('user_id'=>$user_id));
		##############################################
		# Delete Autoresponder's stat from database	 #
		##############################################
		# Delete user's stat from autoresponder signup table
		$this->db->delete('red_autoresponder_signup',array('subscriber_created_by'=>$user_id));
		#Fetch autoresponder for user
		$rows=array();
		$this->db->select('autoresponder_scheduled_id');
		$this->db->from('red_email_autoresponders');
		$this->db->where(array('campaign_created_by'=>$user_id));
		$result=$this->db->get();		
		foreach($result->result_array() as $row)
		{
			# Delete user's stat from autoresponder scheduled table
			$this->db->delete('red_autoresponder_scheduled',array('autoresponder_scheduled_id'=>$row['autoresponder_scheduled_id']));
		}
	}
	/**
		Function delete_campaign is to delete user's campaigns from database
		@param user_id: user id
	*/
	function delete_campaign($user_id=0){
		#Fetch campaigns for user
		$rows=array();
		$this->db->select('campaign_id','rcp.id');
		$this->db->from('red_email_campaigns');
		$this->db->join('red_email_campaigns_pages as rcp','rcp.site_id=campaign_id AND is_autoresponder=1');
		$this->db->where(array('campaign_created_by'=>$user_id));
		$result=$this->db->get();		
		foreach($result->result_array() as $row)
		{
			# Delete block' content from database
			$this->db->delete('red_email_campaigns_background_color_block_content',array('red_background_color_page_id'=>$row['id']));
			# Delete campaign's pages from datbase
			$this->db->delete('red_email_campaigns_pages',array('site_id'=>$row['campaign_id'],'is_autoresponder'=>0));
		}
		# Delete image bank images from database
		$this->db->delete('red_image_bank',array('img_user_id'=>$user_id));
		# Delete color's theme from databse
		$this->db->delete('red_email_campaigns_color_themes',array('member_id'=>$user_id));
		# delete user's campaigns from database
		$this->db->delete('red_email_campaigns',array('campaign_created_by'=>$user_id));
	}
	/**
		Function delete_autoresponder is to delete user' autoresponders from database
		@param user_id: user id
	*/
	function delete_autoresponder($user_id=0){
		#Fetch campaigns for user
		$rows=array();
		$this->db->select('campaign_id','rcp.id');
		$this->db->from('red_email_autoresponders');
		$this->db->join('red_email_campaigns_pages as rcp','rcp.site_id=campaign_id AND is_autoresponder=1');
		$this->db->where(array('campaign_created_by'=>$user_id));
		$result=$this->db->get();		
		foreach($result->result_array() as $row)
		{
			# Delete block' content from database
			$this->db->delete('red_email_campaigns_background_color_block_content',array('red_background_color_page_id'=>$row['id']));
			# Delete campaign's pages from datbase
			$this->db->delete('red_email_campaigns_pages',array('site_id'=>$row['campaign_id'],'is_autoresponder'=>1));
		}
		# Delete image bank images from database
		$this->db->delete('red_image_bank',array('img_user_id'=>$user_id));
		# Delete color's theme from databse
		$this->db->delete('red_email_campaigns_color_themes',array('member_id'=>$user_id));
		# Delete user's campaigns from database
		$this->db->delete('red_email_autoresponders',array('campaign_created_by'=>$user_id));
		# Delete user's autoresponder group from database
		$this->db->delete('red_autoresponder_group',array('autoresponder_created_by'=>$user_id));
	}
	/**
		Function delete_signup_forms to delete signup forms from database
		@param user_id: user id
	*/
	function delete_signup_forms($user_id=0){
		# Delete user's signup form from databse
		$this->db->delete('red_signup_form',array('member_id'=>$user_id));
	}
	/**
		Function delete_contacts is to delete user's subscription list, subscribers from database
		@param user_id: user id
	*/
	function delete_contacts($user_id=0){
		#Fetch subscription list for user
		$rows=array();
		$this->db->select('subscription_id');
		$this->db->from('red_email_subscriptions');
		$this->db->where(array('subscription_created_by'=>$user_id));
		$result=$this->db->get();		
		foreach($result->result_array() as $row)
		{
			# Delete user's subscribers id from databse
			$this->db->delete('red_email_subscription_subscriber',array('subscription_id'=>$row['subscription_id']));
		}
		# Delete user's subscribers info from database
		$this->db->delete('red_email_subscribers',array('subscriber_created_by'=>$user_id));
		# Delete user's subscriptions list from database
		$this->db->delete('red_email_subscriptions',array('subscription_created_by'=>$user_id));
	}
	/**
		Function cancel_subscription is to delete coustomer profile and payment profile from CIM
		@param user_id: user id
	*/
	function cancel_subscription($user_id=0){
		global $CI;
        $CI =& get_instance();
        
		//collect config varibales
        $this->loginname    	 = $CI->config->item('loginname');	#Collect loginame
        $this->transactionkey    = $CI->config->item('transactionkey');	#Collect transactionkey
		
		$this->load->library('Billingcim'); # load billing library		
		$this->billingcim->loginKey($this->loginname, $this->transactionkey, $CI->config->item('test_mode'));
		#Fetch subscription list for user
		$rows=array();
		$this->db->select('customer_profile_id,customer_payment_profile_id');
		$this->db->from('red_member_packages');
		$this->db->where(array('member_id'=>$user_id));
		$result=$this->db->get();		
		foreach($result->result_array() as $row)
		{
			if($row['customer_profile_id']>0){
				# Merchant-assigned reference ID for the request
				$this->billingcim->setParameter('refId', "ref_".$user_id); // Up to 20 characters (optional)			
				# Payment gateway assigned ID associated with the customer profile
				$this->billingcim->setParameter('customerProfileId', $row['customer_profile_id']); // Numeric (required)		
				# Payment gateway assigned ID associated with the customer payment profile
				$this->billingcim->setParameter('customerPaymentProfileId', $row['customer_payment_profile_id']); // Numeric (required)
				
				# STOPPED ACTUAL DELETION FROM CIM ON 8th August 2012 (next 2 line commented)
				
				#$this->billingcim->deleteCustomerProfileRequest();	#delete customer profile
				#$this->billingcim->deleteCustomerPaymentProfileRequest();	#delete customer payment profile
			}
		}
	}
	/**
		Function get_user_account_info to fetch user's account information
	*/
	function get_user_account_info($conditions_array=array()){
		$rows=array();
		$this->db->select('m.member_id,m.member_username,m.email_address,last_login_time');
		$this->db->from('red_members as m');
		$this->db->join('red_member_packages as rmp','rmp.red_member_package_id=m.package_id');
		$this->db->where($conditions_array);
		$result=$this->db->get();
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	/**
		Function get_language_text to fetch languages text from database
	*/
	function get_language_text($conditions_array=array()){
		$rows=array();
		$this->db->from('red_text_translation_languages as l');
		$this->db->where($conditions_array);
		$result=$this->db->get();
		
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
	/**
		Function create_language to create language text
	*/
	function create_language($input_array){
		$this->db->insert('red_text_translation_languages',$input_array);
		return $this->db->insert_id();
	}
	/**
		Function get_languages_text to fetch languages from database
	*/
	function get_languages_text($conditions_array=array()){
		$rows=array();
		$this->db->from('red_text_translation_languages as l');
		$this->db->where($conditions_array);
		$result=$this->db->get();

		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
		
	function lastPaymentStatus(){
		$rows=array();
		$this->db->select('transaction_id, package_id, 	status');
		$this->db->from('red_member_transactions');
	 
		//$this->db->where(array('user_id'=>$this->session->userdata('member_id'),'is_deleted'=>0,'gateway'=>'AUTHORIZE'));
		$this->db->where(array('user_id'=>$this->session->userdata('member_id'),'is_deleted'=>0));
		 
			$this->db->order_by('transaction_id','desc');
		 
		 $this->db->limit(1, 0);	
		#echo $this->db->_compile_select(); 
		
		$result=$this->db->get();
		#echo $this->db->last_query();
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		 
		return $rows[0]['status'];
	
	}
	/*
		dateDiff function is for calculating number of days between two dates
	*/
	function dateDiff($dformat, $endDate, $beginDate){
		$date_parts1=explode($dformat, $beginDate);
		$date_parts2=explode($dformat, $endDate);
		$start_date=gregoriantojd($date_parts1[0], $date_parts1[1], $date_parts1[2]);
		$end_date=gregoriantojd($date_parts2[0], $date_parts2[1], $date_parts2[2]);
		return   $end_date - $start_date;
	}
	/**
	- Quota implementation based on selected package
	*/
	
	function updateMemberCampaignQuota($mid, $packageupdateType = 'downgrade'){
		$member_campaign_quota = $this->getMemberCampaignQuota($mid);
		if($packageupdateType == 'downgrade'){
			//$this->update_member_package(array('max_campaign_quota'=>$member_campaign_quota,'campaign_sent_counter'=>0),array('member_id'=>$mid));		
			$this->update_member_package(array('max_campaign_quota'=>$member_campaign_quota),array('member_id'=>$mid));		
		}else{
		$this->update_member_package(array('max_campaign_quota'=>$member_campaign_quota),array('member_id'=>$mid));		
		}
	}
	function getMemberCampaignQuota($mid){
		// We need package_max_contacts, quota_multiplier and user_quota_multiplier for calculation
		$member_campaign_quota = 0;
		$rsUserPackageDetail = $this->db->query("select `package_id`, `user_quota_multiplier` from `red_member_packages` where `member_id`='$mid'");
		if ($rsUserPackageDetail->num_rows() > 0){
			$rowUserPackageDetail = $rsUserPackageDetail->row_array(); 
			$package_id = $rowUserPackageDetail['package_id'];  
			$user_quota_multiplier = $rowUserPackageDetail['user_quota_multiplier'];  
			
			$member_campaign_quota = $this->get_package_quota($package_id) * $user_quota_multiplier;
		}	
		return $member_campaign_quota;
	}
	function get_package_quota($pid){
		$pid= intval ($pid);
		$pid = ($pid == 0)?-1:$pid;
		$max_quota =0;
		$sqlPackageQuota = "select (`package_max_contacts` * `quota_multiplier`) as max_quota from red_packages where `package_id`='$pid'";
		 
		$result=$this->db->query($sqlPackageQuota);
		if ($result->num_rows() > 0){
			$row = $result->row_array(); 
			$max_quota = $row['max_quota'];   
		}
		return $max_quota;
	}
	function incrementCampaignSentCounter($mid, $cid){
		$result= $this->db->query("select count(queue_id) as totcampaign from `red_email_track` where `campaign_id`='$cid'");
		 
		if ($result->num_rows() > 0){
			$row = $result->row_array(); 
			$totCampaignSent = $row['totcampaign'];  
			$this->db->query("update `red_member_packages` set `campaign_sent_counter`=(`campaign_sent_counter` + $totCampaignSent) where `member_id`='$mid'");
		}
	
	}
	function getRemainingCampaignSendingQuota($mid){
		$intRemainingCampaignSendingQuota = 0;
		$rsUserQuotaDetail = $this->db->query("select `max_campaign_quota`, `campaign_sent_counter` from `red_member_packages` where `member_id`='$mid'");
		if ($rsUserQuotaDetail->num_rows() > 0){
			$rowUserQuotaDetail = $rsUserQuotaDetail->row_array(); 
			$max_campaign_quota = $rowUserQuotaDetail['max_campaign_quota'];  
			$campaign_sent_counter = $rowUserQuotaDetail['campaign_sent_counter'];  
			
			$intRemainingCampaignSendingQuota = $max_campaign_quota - $campaign_sent_counter;
		}
		$rsContactsinQueue = $this->db->query("select count(`subscriber_id`) as queue from `red_email_queue` where `user_id`='$mid'");
		$intContactsInqueue = $rsContactsinQueue->row()->queue;
		return $intRemainingCampaignSendingQuota - $intContactsInqueue;
	}
	
	/**
	* Dashboard stats display function
	* to show total paid users yet from begining
	*/
	function get_paid_user_from_beginning(){
		return $this->db->query("select count( distinct user_id) as usr from  red_member_transactions t1 inner join red_members t2 on t1.user_id=t2.member_id where gateway IN ('AUTHORIZE','PayPal') and t1.status='SUCCESS' and amount_paid > 0")->row()->usr;
		
	}
	// Start code - CB 
	
	 
	// End:  code - CB 
	/**
	* Dashboard stats display function
	* to show total paid months for all the users yet from begining
	*/
	function get_avg_subscription_lifetime(){
		$this->db->select('count(transaction_id) as count');
		$this->db->from('red_member_transactions');
		$this->db->where(array('status'=>'SUCCESS','amount_paid >'=>0));		
		$result=$this->db->get();		
		$row=$result->result_array() ;
		
		return $row[0]['count'];
	}
	/**
	* User Panel - Dashboard Extra 
	* Function to check API Key
	*/
	function get_user_api(){		
		$this->db->from('red_member_api');
		$this->db->where(array('member_id'=>$this->session->userdata('member_id')));		
		$result=$this->db->get();	
		if ($result->num_rows() > 0){		
			$row=$result->result_array();		
			return $row[0];
		}else{
			return null;
		}	
	}
	
	
	/**
	* Attach message with member
	*/
	function attachMessage($input_array,$where){
		$this->db->select('member_id');
		$this->db->from('red_member_message');
		$this->db->where($where);		
		$result=$this->db->get();	
		#echo "<pre>";print_r$result();exit;
		if($result->num_rows() > 0){	
			$this->db->update('red_member_message',$input_array,$where);	
		}else{
			$this->db->replace_into('red_member_message',$input_array);
		}	
		return $this->db->affected_rows();	
	}
	/**
	* Detach message with member
	*/
	function detachMessage($input_array){
		//$this->db->delete('red_member_message',$input_array);	
		$this->db->update('red_member_message',array('is_deleted'=>1),$input_array);		
		return $this->db->affected_rows();	
	}
	/**
	* Function to show Contact Analysis in Admin panel
	*/
	function contact_analysis_html($mid=0){
		$strTblBody ='';
		
		$rsContactAnalysis = $this->db->query("select * from red_subscriber_analysis where member_id='$mid'");
		foreach($rsContactAnalysis->result_array() as $recContacts){
			$analysis_date = $recContacts['analysis_date'];
			$reanalyse_it = $recContacts['reanalyse_it'];
			$strTblBody .= '<tr><td colspan="8">Analysed on date:'.$analysis_date.' </td>';
			if($reanalyse_it){
			$strTblBody .= '<td colspan="2">Analysis is under process</td></tr>';
			}else{
			$strTblBody .= '<td><a href="javascript:void(0);" onclick="javascript:fnAnalyseNew('.$mid.')">Analyse New Contacts</a> </td>
							<td><a href="javascript:void(0);" onclick="javascript:fnReanalyse('.$mid.')">Re-analyse All Contacts</a> </td>
							</tr>';
			}
			$strTblBody .= '<tr><th>Domain</th><th>Total</th><th>Unique/Fresh</th><th>Repeated</th><th>Responsive</th><th>Un-responsive</th><th>Bounced</th><th>Complaints</th> <th>Un-subscribes</th><th>Role-based/spam</th></tr>';
			
			$strTblBody .= '<tr><td>YAHOO</td><td>'.$recContacts['yahoo_total'].'</td><td>'.$recContacts['yahoo_new'].'</td><td>'.$recContacts['yahoo_existing'].'</td><td>'.$recContacts['yahoo_responsive'].'</td>
							<td>'.$recContacts['yahoo_unresponsive'].'</td>
							<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_yahoo_bounce">'.$recContacts['yahoo_bounce'].'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_yahoo_bounce">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_yahoo_bounce">suppress</a></td>
							<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_yahoo_complaint">'.$recContacts['yahoo_complaint'].'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_yahoo_complaint">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_yahoo_complaint">suppress</a></td>
							<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_yahoo_unsubscribe">'.$recContacts['yahoo_unsubscribe'].'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_yahoo_unsubscribe">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_yahoo_unsubscribe">suppress</a></td>
							<td>'.$recContacts['yahoo_spam'].'</td></tr>';
			
			$strTblBody .= '<tr><td>GMail</td><td>'.$recContacts['gmail_total'].'</td><td>'.$recContacts['gmail_new'].'</td><td>'.$recContacts['gmail_existing'].'</td><td>'.$recContacts['gmail_responsive'].'</td><td>'.$recContacts['gmail_unresponsive'].'</td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_gmail_bounce">'.$recContacts['gmail_bounce'].'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_gmail_bounce">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_gmail_bounce">suppress</a></td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_gmail_complaint">'.$recContacts['gmail_complaint'].'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_gmail_complaint">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_gmail_complaint">suppress</a></td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_gmail_unsubscribe">'.$recContacts['gmail_unsubscribe'].'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_gmail_unsubscribe">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_gmail_unsubscribe">suppress</a></td>
			<td>'.$recContacts['gmail_spam'].'</td></tr>';
			
			$strTblBody .= '<tr><td>HotMail</td><td>'.$recContacts['hotmail_total'].'</td><td>'.$recContacts['hotmail_new'].'</td><td>'.$recContacts['hotmail_existing'].'</td><td>'.$recContacts['hotmail_responsive'].'</td><td>'.$recContacts['hotmail_unresponsive'].'</td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_hotmail_bounce">'.$recContacts['hotmail_bounce'].'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_hotmail_bounce">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_hotmail_bounce">suppress</a></td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_hotmail_complaint">'.$recContacts['hotmail_complaint'].'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_hotmail_complaint">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_hotmail_complaint">suppress</a></td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_hotmail_unsubscribe">'.$recContacts['hotmail_unsubscribe'].'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_hotmail_unsubscribe">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_hotmail_unsubscribe">suppress</a></td>
			<td>'.$recContacts['hotmail_spam'].'</td></tr>';
			
			$strTblBody .= '<tr><td>AOL</td><td>'.$recContacts['aol_total'].'</td><td>'.$recContacts['aol_new'].'</td><td>'.$recContacts['aol_existing'].'</td><td>'.$recContacts['aol_responsive'].'</td><td>'.$recContacts['aol_unresponsive'].'</td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_aol_bounce">'.$recContacts['aol_bounce'].'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_aol_bounce">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_aol_bounce">suppress</a></td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_aol_complaint">'.$recContacts['aol_complaint'].'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_aol_complaint">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_aol_complaint">suppress</a></td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_aol_unsubscribe">'.$recContacts['aol_unsubscribe'].'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_aol_unsubscribe">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_aol_unsubscribe">suppress</a></td>
			<td>'.$recContacts['aol_spam'].'</td></tr>';
			
			$strTblBody .= '<tr><td>MSN</td><td>'.$recContacts['msn_total'].'</td><td>'.$recContacts['msn_new'].'</td><td>'.$recContacts['msn_existing'].'</td><td>'.$recContacts['msn_responsive'].'</td><td>'.$recContacts['msn_unresponsive'].'</td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_msn_bounce">'.$recContacts['msn_bounce'].'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_msn_bounce">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_msn_bounce">suppress</a></td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_msn_complaint">'.$recContacts['msn_complaint'].'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_msn_complaint">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_msn_complaint">suppress</a></td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_msn_unsubscribe">'.$recContacts['msn_unsubscribe'].'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_msn_unsubscribe">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_msn_unsubscribe">suppress</a></td>
			<td>'.$recContacts['msn_spam'].'</td></tr>';
			
			$other_total = ($recContacts['all_total'] - $recContacts['yahoo_total'] - $recContacts['gmail_total'] - $recContacts['hotmail_total'] - $recContacts['msn_total']);
			$total_new = ($recContacts['all_new'] + $recContacts['yahoo_new'] + $recContacts['gmail_new'] + $recContacts['hotmail_new'] + $recContacts['msn_new']+ $recContacts['aol_new']);
			$total_existing = ($recContacts['all_existing'] + $recContacts['yahoo_existing'] + $recContacts['gmail_existing'] + $recContacts['hotmail_existing'] + $recContacts['msn_existing'] + $recContacts['aol_existing']);
			$total_responsive = ($recContacts['all_responsive'] + $recContacts['yahoo_responsive'] + $recContacts['gmail_responsive'] + $recContacts['hotmail_responsive'] + $recContacts['msn_responsive'] + $recContacts['aol_responsive']);
			$total_unresponsive = ($recContacts['all_unresponsive'] + $recContacts['yahoo_unresponsive'] + $recContacts['gmail_unresponsive'] + $recContacts['hotmail_unresponsive'] + $recContacts['msn_unresponsive'] + $recContacts['aol_unresponsive']);
			$total_bounce = ($recContacts['all_bounce'] + $recContacts['yahoo_bounce'] + $recContacts['gmail_bounce'] + $recContacts['hotmail_bounce'] + $recContacts['msn_bounce'] + $recContacts['aol_bounce']);
			$total_complaint = ($recContacts['all_complaint'] + $recContacts['yahoo_complaint'] + $recContacts['gmail_complaint'] + $recContacts['hotmail_complaint'] + $recContacts['msn_complaint'] + $recContacts['aol_complaint']);
			$total_unsubscribe = ($recContacts['all_unsubscribe'] + $recContacts['yahoo_unsubscribe'] + $recContacts['gmail_unsubscribe'] + $recContacts['hotmail_unsubscribe'] + $recContacts['msn_unsubscribe'] + $recContacts['aol_unsubscribe']);
			$total_spam = ($recContacts['all_spam'] + $recContacts['yahoo_spam'] + $recContacts['gmail_spam'] + $recContacts['hotmail_spam'] + $recContacts['msn_spam'] + $recContacts['aol_spam']);
			
			$strTblBody .= '<tr><td>Others</td><td>'.$other_total.'</td><td>'.$recContacts['all_new'].'</td><td>'.$recContacts['all_existing'].'</td><td>'.$recContacts['all_responsive'].'</td><td>'.$recContacts['all_unresponsive'].'</td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_other_bounce">'.$recContacts['all_bounce'].'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_other_bounce">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_other_bounce">suppress</a></td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_other_complaint">'.$recContacts['all_complaint'].'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_other_complaint">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_other_complaint">suppress</a></td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_other_unsubscribe">'.$recContacts['all_unsubscribe'].'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_other_unsubscribe">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_other_unsubscribe">suppress</a></td>
			<td>'.$recContacts['all_spam'].'</td></tr>';
			
			$strTblBody .= '<tr><td>ALL</td><td>'.$recContacts['all_total'].'</td><td>'.$total_new.'</td><td>'.$total_existing.'</td><td>'.$total_responsive.'</td><td>'.$total_unresponsive.'</td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_all_bounce">'.$total_bounce.'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_all_bounce">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_all_bounce">suppress</a></td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_all_complaint">'.$total_complaint.'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_all_complaint">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_all_complaint">suppress</a></td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_all_unsubscribe">'.$total_unsubscribe.'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_all_unsubscribe">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_all_unsubscribe">suppress</a></td>
			<td>'.$total_spam.'</td></tr>';
			
			/*
			$other_existing = ($recContacts['all_existing'] - $recContacts['yahoo_existing'] - $recContacts['gmail_existing'] - $recContacts['hotmail_existing'] - $recContacts['msn_existing'] - $recContacts['aol_existing']);
			$other_responsive = ($recContacts['all_responsive'] - $recContacts['yahoo_responsive'] - $recContacts['gmail_responsive'] - $recContacts['hotmail_responsive'] - $recContacts['msn_responsive'] - $recContacts['aol_responsive']);
			$other_unresponsive = ($recContacts['all_unresponsive'] - $recContacts['yahoo_unresponsive'] - $recContacts['gmail_unresponsive'] - $recContacts['hotmail_unresponsive'] - $recContacts['msn_unresponsive'] - $recContacts['aol_unresponsive']);
			$other_bounce = ($recContacts['all_bounce'] - $recContacts['yahoo_bounce'] - $recContacts['gmail_bounce'] - $recContacts['hotmail_bounce'] - $recContacts['msn_bounce'] - $recContacts['aol_bounce']);
			$other_complaint = ($recContacts['all_complaint'] - $recContacts['yahoo_complaint'] - $recContacts['gmail_complaint'] - $recContacts['hotmail_complaint'] - $recContacts['msn_complaint'] - $recContacts['aol_complaint']);
			$other_unsubscribe = ($recContacts['all_unsubscribe'] - $recContacts['yahoo_unsubscribe'] - $recContacts['gmail_unsubscribe'] - $recContacts['hotmail_unsubscribe'] - $recContacts['msn_unsubscribe'] - $recContacts['aol_unsubscribe']);
			$other_spam = ($recContacts['all_spam'] - $recContacts['yahoo_spam'] - $recContacts['gmail_spam'] - $recContacts['hotmail_spam'] - $recContacts['msn_spam'] - $recContacts['aol_spam']);
			
			
			$strTblBody .= '<tr><td>Others</td><td>'.$other_total.'</td><td>'.$other_new.'</td><td>'.$other_existing.'</td><td>'.$other_responsive.'</td><td>'.$other_unresponsive.'</td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_other_bounce">'.$other_bounce.'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_other_bounce">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_other_bounce">suppress</a></td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_other_complaint">'.$other_complaint.'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_other_complaint">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_other_complaint">suppress</a></td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_other_unsubscribe">'.$other_unsubscribe.'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_other_unsubscribe">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_other_unsubscribe">suppress</a></td>
			<td>'.$other_spam.'</td></tr>';
			
			$strTblBody .= '<tr><td>ALL</td><td>'.$recContacts['all_total'].'</td><td>'.$recContacts['all_new'].'</td><td>'.$recContacts['all_existing'].'</td><td>'.$recContacts['all_responsive'].'</td><td>'.$recContacts['all_unresponsive'].'</td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_all_bounce">'.$recContacts['all_bounce'].'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_all_bounce">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_all_bounce">suppress</a></td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_all_complaint">'.$recContacts['all_complaint'].'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_all_complaint">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_all_complaint">suppress</a></td>
			<td><a href="javascript:void(0);" class="exp" id="'.$mid.'_all_unsubscribe">'.$recContacts['all_unsubscribe'].'</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="del" id="'.$mid.'_all_unsubscribe">delete</a>&nbsp;|&nbsp;<a href="javascript:void(0);" class="sup" id="'.$mid.'_sup_all_unsubscribe">suppress</a></td>
			<td>'.$recContacts['all_spam'].'</td></tr>';
			
			*/
		}
		return '<div style="padding:20px;">
		<table cellspacing="0" cellpadding="4" border="1">' .$strTblBody .'</table></div>';
	}
		
	function is_temp_mail($mail='') {
		if($mail !=''){
			$mail_domains_ko = array('0-mail.com','027168.com','0815.ru','0815.ry','0815.su','0845.ru','0clickemail.com','0wnd.net','0wnd.org','0x207.info','1-8.biz','100likers.com','10mail.com','10mail.org','10minut.com.pl','10minutemail.cf','10minutemail.co.uk','10minutemail.co.za','10minutemail.com','10minutemail.de','10minutemail.ga','10minutemail.gq','10minutemail.ml','10minutemail.net','10minutesmail.com','10x9.com','123-m.com','12houremail.com','12minutemail.com','12minutemail.net','140unichars.com','147.cl','14n.co.uk','1ce.us','1chuan.com','1fsdfdsfsdf.tk','1mail.ml','1pad.de','1st-forms.com','1to1mail.org','1zhuan.com','20email.eu','20email.it','20mail.in','20mail.it','20minutemail.com','2120001.net','21cn.com','24hourmail.com','24hourmail.net','2fdgdfgdfgdf.tk','2prong.com','30minutemail.com','33mail.com','36ru.com','3d-painting.com','3l6.com','3mail.ga','3trtretgfrfe.tk','4-n.us','418.dk','42o.org','4gfdsgfdgfd.tk','4mail.cf','4mail.ga','4warding.com','4warding.net','4warding.org','5ghgfhfghfgh.tk','5gramos.com','5mail.cf','5mail.ga','5oz.ru','5x25.com','60minutemail.com','672643.net','675hosting.com','675hosting.net','675hosting.org','6hjgjhgkilkj.tk','6ip.us','6mail.cf','6mail.ga','6mail.ml','6paq.com','6url.com','75hosting.com','75hosting.net','75hosting.org','7days-printing.com','7mail.ga','7mail.ml','7tags.com','80665.com','8127ep.com','8mail.cf','8mail.ga','8mail.ml','99experts.com','9mail.cf','9ox.net','a-bc.net','a45.in','abakiss.com','abcmail.email','abusemail.de','abuser.eu','abyssmail.com','ac20mail.in','academiccommunity.com','acentri.com','adiq.eu','adobeccepdm.com','adpugh.org','adsd.org','advantimo.com','adwaterandstir.com','aegia.net','aegiscorp.net','aelo.es','aeonpsi.com','afrobacon.com','agedmail.com','agtx.net','ahk.jp','airsi.de','ajaxapp.net','akapost.com','akerd.com','al-qaeda.us','aligamel.com','alisongamel.com','alivance.com','alldirectbuy.com','allowed.org','allthegoodnamesaretaken.org','alph.wtf','ama-trade.de','ama-trans.de','amail.com','amail4.me','amazon-aws.org','amelabs.com','amilegit.com','amiri.net','amiriindustries.com','ampsylike.com','anappfor.com','anappthat.com','andthen.us','animesos.com','ano-mail.net','anon-mail.de','anonbox.net','anonmails.de','anonymail.dk','anonymbox.com','anonymized.org','anonymousness.com','ansibleemail.com','anthony-junkmail.com','antireg.com','antireg.ru','antispam.de','antispam24.de','antispammail.de','apfelkorps.de','aphlog.com','appc.se','appinventor.nl','appixie.com','apps.dj','arduino.hk','armyspy.com','aron.us','arroisijewellery.com','artman-conception.com','arvato-community.de','aschenbrandt.net','asdasd.nl','asdasd.ru','ashleyandrew.com','astroempires.info','asu.mx','asu.su','at0mik.org','augmentationtechnology.com','auti.st','autorobotica.com','autotwollow.com','aver.com','avls.pt','awatum.de','awiki.org','axiz.org','azcomputerworks.com','azmeil.tk','b1of96u.com','b2cmail.de','badgerland.eu','badoop.com','bareed.ws','barryogorman.com','bartdevos.be','basscode.org','bauwerke-online.com','bazaaboom.com','bcast.ws','bcb.ro','bccto.me','bearsarefuzzy.com','beddly.com','beefmilk.com','belljonestax.com','benipaula.org','bestchoiceusedcar.com','betr.co','bgx.ro','bidourlnks.com','big1.us','bigprofessor.so','bigstring.com','bigwhoop.co.za','bij.pl','binkmail.com','bio-muesli.info','bio-muesli.net','blackmarket.to','bladesmail.net','blip.ch','blogmyway.org','bluedumpling.info','bluewerks.com','bobmail.info','bobmurchison.com','bofthew.com','bonobo.email','bookthemmore.com','bootybay.de','borged.com','borged.net','borged.org','bot.nu','boun.cr','bouncr.com','boxformail.in','boximail.com','boxtemp.com.br','brandallday.net','brasx.org','breakthru.com','brefmail.com','brennendesreich.de','briggsmarcus.com','broadbandninja.com','bsnow.net','bspamfree.org','bspooky.com','bst-72.com','btb-notes.com','btc.email','buffemail.com','bugmenever.com','bugmenot.com','bulrushpress.com','bum.net','bumpymail.com','bunchofidiots.com','bund.us','bundes-li.ga','bunsenhoneydew.com','burnthespam.info','burstmail.info','businessbackend.com','businesssuccessislifesuccess.com','buspad.org','buymoreplays.com','buyordie.info','buyusedlibrarybooks.org','byebyemail.com','byespm.com','byom.de','c2.hu','c51vsgq.com','cachedot.net','californiafitnessdeals.com','cam4you.cc','cane.pw','casualdx.com','cavi.mx','cbair.com','cc.liamria','cdpa.cc','ceed.se','cek.pm','cellurl.com','centermail.com','centermail.net','ch.tc','chacuo.net','chammy.info','cheatmail.de','chickenkiller.com','chielo.com','childsavetrust.org','chilkat.com','chithinh.com','chogmail.com','choicemail1.com','chong-mail.com','chong-mail.net','chong-mail.org','chumpstakingdumps.com','cigar-auctions.com','civx.org','ckiso.com','cl-cl.org','cl0ne.net','clandest.in','clipmail.eu','clixser.com','clrmail.com','cmail.com','cmail.net','cmail.org','cnamed.com','cnew.ir','cnmsg.net','cnsds.de','codeandscotch.com','codivide.com','coieo.com','coldemail.info','com.ar','compareshippingrates.org','completegolfswing.com','comwest.de','consumerriot.com','coolandwacky.us','coolimpool.org','correo.blogos.net','cosmorph.com','courrieltemporaire.com','coza.ro','crankhole.com','crapmail.org','crastination.de','crazespaces.pw','crazymailing.com','crossroadsmail.com','cszbl.com','ctos.ch','cu.cc','cubiclink.com','curryworld.de','cust.in','cuvox.de','cylab.org','d3p.dk','dab.ro','dacoolest.com','daemsteam.com','daintly.com','dammexe.net','dandikmail.com','darkharvestfilms.com','daryxfox.net','dash-pads.com','dataarca.com','datafilehost','datarca.com','datazo.ca','davidkoh.net','davidlcreative.com','dayrep.com','dbunker.com','dcemail.com','ddcrew.com','de-a.org','deadaddress.com','deadchildren.org','deadfake.cf','deadfake.ga','deadfake.ml','deadfake.tk','deadspam.com','deagot.com','dealja.com','dealrek.com','deekayen.us','defomail.com','degradedfun.net','delayload.com','delayload.net','delikkt.de','der-kombi.de','derkombi.de','derluxuswagen.de','despam.it','despammed.com','devnullmail.com','dharmatel.net','dhm.ro','dialogus.com','diapaulpainting.com','digitalmariachis.com','digitalsanctuary.com','dildosfromspace.com','dingbone.com','discard.cf','discard.email','discard.ga','discard.gq','discard.ml','discard.tk','discardmail.com','discardmail.de','dispo.in','dispomail.eu','disposable-email.ml','disposable.cf','disposable.ga','disposable.ml','disposableaddress.com','disposableemailaddresses.com','disposableinbox.com','dispose.it','disposeamail.com','disposemail.com','dispostable.com','divermail.com','divismail.ru','dlemail.ru','dob.jp','dodgeit.com','dodgemail.de','dodgit.com','dodgit.org','dodsi.com','doiea.com','dolphinnet.net','domforfb1.tk','domforfb18.tk','domforfb19.tk','domforfb2.tk','domforfb23.tk','domforfb27.tk','domforfb29.tk','domforfb3.tk','domforfb4.tk','domforfb5.tk','domforfb6.tk','domforfb7.tk','domforfb8.tk','domforfb9.tk','domozmail.com','donemail.ru','dontreg.com','dontsendmespam.de','doquier.tk','dotman.de','dotmsg.com','dotslashrage.com','douchelounge.com','dozvon-spb.ru','dp76.com','drdrb.com','drdrb.net','dred.ru','drivetagdev.com','droolingfanboy.de','dropcake.de','droplar.com','dropmail.me','dspwebservices.com','duam.net','dudmail.com','duk33.com','dukedish.com','dump-email.info','dumpandjunk.com','dumpmail.de','dumpyemail.com','durandinterstellar.com','duskmail.com','dyceroprojects.com','dz17.net','e-mail.com','e-mail.org','e3z.de','e4ward.com','easy-trash-mail.com','easytrashmail.com','ebeschlussbuch.de','ecallheandi.com','edgex.ru','edinburgh-airporthotels.com','edu.my','edu.sg','edv.to','ee1.pl','ee2.pl','eelmail.com','efxs.ca','einmalmail.de','einrot.com','einrot.de','eintagsmail.de','elearningjournal.org','electro.mn','elitevipatlantamodels.com','email-fake.cf','email-fake.ga','email-fake.gq','email-fake.ml','email-fake.tk','email-jetable.fr','email.cbes.net','email.net','email60.com','emailage.cf','emailage.ga','emailage.gq','emailage.ml','emailage.tk','emaildienst.de','emailgo.de','emailias.com','emailigo.de','emailinfive.com','emailisvalid.com','emaillime.com','emailmiser.com','emailproxsy.com','emailresort.com','emails.ga','emailsensei.com','emailsingularity.net','emailspam.cf','emailspam.ga','emailspam.gq','emailspam.ml','emailspam.tk','emailtemporanea.com','emailtemporanea.net','emailtemporar.ro','emailtemporario.com.br','emailthe.net','emailtmp.com','emailto.de','emailwarden.com','emailx.at.hm','emailxfer.com','emailz.cf','emailz.ga','emailz.gq','emailz.ml','emeil.in','emeil.ir','emeraldwebmail.com','emil.com','emkei.cf','emkei.ga','emkei.gq','emkei.ml','emkei.tk','eml.pp.ua','emz.net','enterto.com','epb.ro','ephemail.net','ephemeral.email','ericjohnson.ml','ero-tube.org','esc.la','escapehatchapp.com','esemay.com','esgeneri.com','esprity.com','etranquil.com','etranquil.net','etranquil.org','evanfox.info','evopo.com','example.com','exitstageleft.net','explodemail.com','express.net.ua','extremail.ru','eyepaste.com','ez.lv','ezfill.com','ezstest.com','f4k.es','facebook-email.cf','facebook-email.ga','facebook-email.ml','facebookmail.gq','facebookmail.ml','fadingemail.com','fag.wf','failbone.com','faithkills.com','fake-email.pp.ua','fake-mail.cf','fake-mail.ga','fake-mail.ml','fakedemail.com','fakeinbox.cf','fakeinbox.com','fakeinbox.ga','fakeinbox.ml','fakeinbox.tk','fakeinformation.com','fakemail.fr','fakemailgenerator.com','fakemailz.com','fammix.com','fangoh.com','fansworldwide.de','fantasymail.de','farrse.co.uk','fastacura.com','fastchevy.com','fastchrysler.com','fasternet.biz','fastkawasaki.com','fastmazda.com','fastmitsubishi.com','fastnissan.com','fastsubaru.com','fastsuzuki.com','fasttoyota.com','fastyamaha.com','fatflap.com','fdfdsfds.com','fer-gabon.org','fettometern.com','fictionsite.com','fightallspam.com','figjs.com','figshot.com','fiifke.de','filbert4u.com','filberts4u.com','film-blog.biz','filzmail.com','fir.hk','fivemail.de','fixmail.tk','fizmail.com','fleckens.hu','flemail.ru','flowu.com','flurred.com','fly-ts.de','flyinggeek.net','flyspam.com','foobarbot.net','footard.com','forecastertests.com','forgetmail.com','fornow.eu','forspam.net','foxja.com','foxtrotter.info','fr.nf','fr33mail.info','frapmail.com','free-email.cf','free-email.ga','freebabysittercam.com','freeblackbootytube.com','freecat.net','freedompop.us','freefattymovies.com','freeletter.me','freemail.hu','freemail.ms','freemails.cf','freemails.ga','freemails.ml','freeplumpervideos.com','freeschoolgirlvids.com','freesistercam.com','freeteenbums.com','freundin.ru','friendlymail.co.uk','front14.org','ftp.sh','ftpinc.ca','fuckedupload.com','fuckingduh.com','fudgerub.com','fuirio.com','funnycodesnippets.com','furzauflunge.de','fux0ringduh.com','fxnxs.com','fyii.de','g4hdrop.us','gaggle.net','galaxy.tv','gally.jp','gamegregious.com','garbagecollector.org','garbagemail.org','gardenscape.ca','garizo.com','garliclife.com','garrymccooey.com','gav0.com','gawab.com','gehensiemirnichtaufdensack.de','geldwaschmaschine.de','gelitik.in','genderfuck.net','geschent.biz','get-mail.cf','get-mail.ga','get-mail.ml','get-mail.tk','get1mail.com','get2mail.fr','getairmail.cf','getairmail.com','getairmail.ga','getairmail.gq','getairmail.ml','getairmail.tk','geteit.com','getmails.eu','getonemail.com','getonemail.net','ghosttexter.de','giaiphapmuasam.com','giantmail.de','ginzi.be','ginzi.co.uk','ginzi.es','ginzi.net','ginzy.co.uk','ginzy.eu','girlsindetention.com','girlsundertheinfluence.com','gishpuppy.com','glitch.sx','globaltouron.com','glucosegrin.com','gmal.com','gmial.com','gmx.us','gnctr-calgary.com','goemailgo.com','gomail.in','gorillaswithdirtyarmpits.com','gothere.biz','gotmail.com','gotmail.net','gotmail.org','gowikibooks.com','gowikicampus.com','gowikicars.com','gowikifilms.com','gowikigames.com','gowikimusic.com','gowikinetwork.com','gowikitravel.com','gowikitv.com','grandmamail.com','grandmasmail.com','great-host.in','greensloth.com','greggamel.com','greggamel.net','gregorsky.zone','gregorygamel.com','gregorygamel.net','grish.de','grr.la','gs-arc.org','gsredcross.org','gsrv.co.uk','gudanglowongan.com','guerillamail.biz','guerillamail.com','guerillamail.de','guerillamail.info','guerillamail.net','guerillamail.org','guerillamailblock.com','guerrillamail.biz','guerrillamail.com','guerrillamail.de','guerrillamail.info','guerrillamail.net','guerrillamail.org','guerrillamailblock.com','gustr.com','gynzi.co.uk','gynzi.es','gynzy.at','gynzy.es','gynzy.eu','gynzy.gr','gynzy.info','gynzy.lt','gynzy.mobi','gynzy.pl','gynzy.ro','gynzy.sk','gzb.ro','h8s.org','habitue.net','hacccc.com','hackthatbit.ch','hahawrong.com','haltospam.com','harakirimail.com','haribu.com','hartbot.de','hat-geld.de','hatespam.org','hawrong.com','hazelnut4u.com','hazelnuts4u.com','hazmatshipping.org','headstrong.de','heathenhammer.com','heathenhero.com','hecat.es','hellodream.mobi','helloricky.com','helpinghandtaxcenter.org','herp.in','herpderp.nl','hi5.si','hiddentragedy.com','hidemail.de','hidzz.com','highbros.org','hmail.us','hmamail.com','hmh.ro','hoanggiaanh.com','hochsitze.com','hopemail.biz','hot-mail.cf','hot-mail.ga','hot-mail.gq','hot-mail.ml','hot-mail.tk','hotmai.com','hotmial.com','hotpop.com','hpc.tw','hs.vc','ht.cx','hulapla.de','humaility.com','humn.ws.gy','hungpackage.com','huskion.net','hvastudiesucces.nl','hwsye.net','ibnuh.bz','icantbelieveineedtoexplainthisshit.com','icx.in','icx.ro','id.au','ieatspam.eu','ieatspam.info','ieh-mail.de','ige.es','ignoremail.com','ihateyoualot.info','iheartspam.org','ikbenspamvrij.nl','illistnoise.com','ilovespam.com','imails.info','imgof.com','imgv.de','imstations.com','inbax.tk','inbound.plus','inbox.si','inbox2.info','inboxalias.com','inboxclean.com','inboxclean.org','inboxdesign.me','inboxed.im','inboxed.pw','inboxproxy.com','inboxstore.me','inclusiveprogress.com','incognitomail.com','incognitomail.net','incognitomail.org','incq.com','indieclad.com','indirect.ws','ineec.net','infocom.zp.ua','inoutmail.de','inoutmail.eu','inoutmail.info','inoutmail.net','insanumingeniumhomebrew.com','insorg-mail.info','instant-mail.de','instantemailaddress.com','internetoftags.com','interstats.org','intersteller.com','iozak.com','ip6.li','ipoo.org','ipsur.org','irc.so','irish2me.com','iroid.com','ironiebehindert.de','irssi.tv','is.af','isukrainestillacountry.com','it7.ovh','itunesgiftcodegenerator.com','iwi.net','j-p.us','j.svxr.org','jafps.com','jdmadventures.com','jdz.ro','jellyrolls.com','jetable.com','jetable.fr.nf','jetable.net','jetable.org','jetable.pp.ua','jmail.ro','jnxjn.com','jobbikszimpatizans.hu','jobposts.net','jobs-to-be-done.net','joelpet.com','joetestalot.com','jopho.com','jourrapide.com','jpco.org','jsrsolutions.com','jungkamushukum.com','junk.to','junk1e.com','junkmail.ga','junkmail.gq','jwork.ru','kakadua.net','kalapi.org','kamsg.com','kaovo.com','kariplan.com','kartvelo.com','kasmail.com','kaspop.com','kcrw.de','keepmymail.com','keinhirn.de','keipino.de','kemptvillebaseball.com','kennedy808.com','killmail.com','killmail.net','kimsdisk.com','kingsq.ga','kiois.com','kismail.ru','kisstwink.com','kitnastar.com','klassmaster.com','klassmaster.net','kloap.com','kludgemush.com','klzlk.com','kmhow.com','kommunity.biz','kon42.com','kook.ml','kopagas.com','kopaka.net','kosmetik-obatkuat.com','kostenlosemailadresse.de','koszmail.pl','krypton.tk','kuhrap.com','kulturbetrieb.info','kurzepost.de','kwift.net','kwilco.net','kyal.pl','l-c-a.us','l33r.eu','labetteraverouge.at','lackmail.net','lackmail.ru','lags.us','lain.ch','lakelivingstonrealestate.com','landmail.co','laoeq.com','lastmail.co','lastmail.com','lawlita.com','lazyinbox.com','ldop.com','ldtp.com','lee.mx','leeching.net','lellno.gq','letmeinonthis.com','letthemeatspam.com','lez.se','lhsdv.com','liamcyrus.com','lifebyfood.com','lifetotech.com','ligsb.com','lilo.me','lindenbaumjapan.com','link2mail.net','linuxmail.so','litedrop.com','lkgn.se','llogin.ru','loadby.us','locomodev.net','login-email.cf','login-email.ga','login-email.ml','login-email.tk','logular.com','loin.in','lolfreak.net','lolmail.biz','lookugly.com','lopl.co.cc','lortemail.dk','losemymail.com','lovemeleaveme.com','lpfmgmtltd.com','lr7.us','lr78.com','lroid.com','lru.me','luckymail.org','lukecarriere.com','lukemail.info','lukop.dk','luv2.us','lyfestylecreditsolutions.com','m21.cc','m4ilweb.info','maboard.com','macromaid.com','magamail.com','magicbox.ro','maidlow.info','mail-filter.com','mail-owl.com','mail-temporaire.com','mail-temporaire.fr','mail.by','mail114.net','mail1a.de','mail21.cc','mail2rss.org','mail333.com','mail4trash.com','mail666.ru','mail707.com','mail72.com','mailback.com','mailbidon.com','mailbiz.biz','mailblocks.com','mailbucket.org','mailcat.biz','mailcatch.com','mailchop.com','mailcker.com','mailde.de','mailde.info','maildrop.cc','maildrop.cf','maildrop.ga','maildrop.gq','maildrop.ml','maildu.de','maildx.com','maileater.com','mailed.in','mailed.ro','maileimer.de','mailexpire.com','mailfa.tk','mailforspam.com','mailfree.ga','mailfree.gq','mailfree.ml','mailfreeonline.com','mailfs.com','mailguard.me','mailhazard.com','mailhazard.us','mailhz.me','mailimate.com','mailin8r.com','mailinatar.com','mailinater.com','mailinator.co.uk','mailinator.com','mailinator.gq','mailinator.info','mailinator.net','mailinator.org','mailinator.us','mailinator2.com','mailincubator.com','mailismagic.com','mailita.tk','mailjunk.cf','mailjunk.ga','mailjunk.gq','mailjunk.ml','mailjunk.tk','mailmate.com','mailme.gq','mailme.ir','mailme.lv','mailme24.com','mailmetrash.com','mailmoat.com','mailms.com','mailnator.com','mailnesia.com','mailnull.com','mailonaut.com','mailorc.com','mailorg.org','mailpick.biz','mailproxsy.com','mailquack.com','mailrock.biz','mailsac.com','mailscrap.com','mailseal.de','mailshell.com','mailsiphon.com','mailslapping.com','mailslite.com','mailtemp.info','mailtemporaire.com','mailtemporaire.fr','mailtome.de','mailtothis.com','mailtrash.net','mailtv.net','mailtv.tv','mailzi.ru','mailzilla.com','mailzilla.org','mailzilla.orgmbx.cc','makemetheking.com','malahov.de','malayalamdtp.com','manifestgenerator.com','mansiondev.com','manybrain.com','markmurfin.com','mbx.cc','mcache.net','mciek.com','meepsheep.eu','meinspamschutz.de','meltmail.com','messagebeamer.de','messwiththebestdielikethe.rest','mezimages.net','mfsa.ru','miaferrari.com','midcoastcustoms.com','midcoastcustoms.net','midcoastsolutions.com','midcoastsolutions.net','midlertidig.com','midlertidig.net','midlertidig.org','mierdamail.com','migmail.net','migmail.pl','migumail.com','mijnhva.nl','ministry-of-silly-walks.de','minsmail.com','mintemail.com','misterpinball.de','mji.ro','mjukglass.nu','mkpfilm.com','ml8.ca','mm.my','mm5.se','moakt.com','moakt.ws','mobileninja.co.uk','moburl.com','mockmyid.com','moeri.org','mohmal.com','momentics.ru','moneypipe.net','monumentmail.com','moonwake.com','moot.es','moreawesomethanyou.com','moreorcs.com','motique.de','mountainregionallibrary.net','moza.pl','msgos.com','msk.ru','mspeciosa.com','mswork.ru','msxd.com','mt2009.com','mt2014.com','mt2015.com','mtmdev.com','muathegame.com','muchomail.com','mucincanon.com','mutant.me','mvrht.com','mwarner.org','mxfuel.com','my10minutemail.com','mybitti.de','mycleaninbox.net','mycorneroftheinter.net','mydemo.equipment','myecho.es','myemailboxy.com','mykickassideas.com','mymail-in.net','mymailoasis.com','mynetstore.de','myopang.com','mypacks.net','mypartyclip.de','myphantomemail.com','mysamp.de','myspaceinc.com','myspaceinc.net','myspaceinc.org','myspacepimpedup.com','myspamless.com','mytemp.email','mytempemail.com','mytempmail.com','mytrashmail.com','mywarnernet.net','myzx.com','n1nja.org','nabuma.com','nakedtruth.biz','nanonym.ch','nationalgardeningclub.com','naver.com','negated.com','neomailbox.com','nepwk.com','nervmich.net','nervtmich.net','net.ua','netmails.com','netmails.net','netricity.nl','netris.net','netviewer-france.com','netzidiot.de','nevermail.de','nextstopvalhalla.com','nfast.net','nguyenusedcars.com','nh3.ro','nice-4u.com','nicknassar.com','nincsmail.hu','niwl.net','nm7.cc','nmail.cf','nnh.com','nnot.net','no-spam.ws','no-ux.com','noblepioneer.com','nobugmail.com','nobulk.com','nobuma.com','noclickemail.com','nodezine.com','nogmailspam.info','nokiamail.com','nom.za','nomail.pw','nomail2me.com','nomorespamemails.com','nonspam.eu','nonspammer.de','nonze.ro','noref.in','norseforce.com','nospam.ze.tc','nospam4.us','nospamfor.us','nospamthanks.info','nothingtoseehere.ca','notmailinator.com','notrnailinator.com','notsharingmy.info','now.im','nowhere.org','nowmymail.com','ntlhelp.net','nubescontrol.com','nullbox.info','nurfuerspam.de','nuts2trade.com','nwldx.com','ny7.me','o2stk.org','o7i.net','obfusko.com','objectmail.com','obobbo.com','obxpestcontrol.com','odaymail.com','odnorazovoe.ru','oerpub.org','offshore-proxies.net','ohaaa.de','ohi.tw','okclprojects.com','okrent.us','okzk.com','olypmall.ru','omail.pro','omnievents.org','one-time.email','oneoffemail.com','oneoffmail.com','onet.pl','onewaymail.com','onlatedotcom.info','online.ms','onlineidea.info','onqin.com','ontyne.biz','oolus.com','oopi.org','opayq.com','opp24.com','ordinaryamerican.net','org.ua','oroki.de','oshietechan.link','otherinbox.com','ourklips.com','ourpreviewdomain.com','outlawspam.com','ovpn.to','owlpic.com','ownsyou.de','oxopoha.com','ozyl.de','pa9e.com','pagamenti.tk','pancakemail.com','paplease.com','pastebitch.com','pcusers.otherinbox.com','penisgoes.in','pepbot.com','peterdethier.com','petrzilka.net','pfui.ru','photomark.net','phpbb.uu.gl','pi.vu','pimpedupmyspace.com','pinehill-seattle.org','pingir.com','pisls.com','pjjkp.com','plexolan.de','plhk.ru','plw.me','pojok.ml','pokiemobile.com','politikerclub.de','pooae.com','poofy.org','pookmail.com','poopiebutt.club','popesodomy.com','popgx.com','postacin.com','postonline.me','poutineyourface.com','powered.name','powlearn.com','pp.ua','primabananen.net','privacy.net','privatdemail.net','privy-mail.com','privy-mail.de','privymail.de','pro-tag.org','procrackers.com','projectcl.com','propscore.com','proxymail.eu','proxyparking.com','prtnx.com','prtz.eu','psh.me','punkass.com','purcell.email','purelogistics.org','put2.net','putthisinyourspamdatabase.com','pwrby.com','qasti.com','qc.to','qibl.at','qipmail.net','qisdo.com','qisoa.com','qoika.com','qq.my','quadrafit.com','quickinbox.com','quickmail.nl','qvy.me','qwickmail.com','r4nd0m.de','rabin.ca','raetp9.com','raketenmann.de','rancidhome.net','randomail.net','raqid.com','rax.la','raxtest.com','rbb.org','rcpt.at','reallymymail.com','realtyalerts.ca','receiveee.com','recipeforfailure.com','recode.me','reconmail.com','recyclemail.dk','redfeathercrow.com','regbypass.com','rejectmail.com','reliable-mail.com','remail.cf','remail.ga','remarkable.rocks','remote.li','reptilegenetics.com','revolvingdoorhoax.org','rhyta.com','riddermark.de','risingsuntouch.com','rklips.com','rma.ec','rmqkr.net','rnailinator.com','ro.lt','robertspcrepair.com','ronnierage.net','rotaniliam.com','rowe-solutions.com','royal.net','royaldoodles.org','rppkn.com','rtrtr.com','ruffrey.com','rumgel.com','runi.ca','rustydoor.com','rvb.ro','s0ny.net','s33db0x.com','sabrestlouis.com','sackboii.com','safersignup.de','safetymail.info','safetypost.de','saharanightstempe.com','samsclass.info','sandelf.de','sandwhichvideo.com','sanfinder.com','sanim.net','sanstr.com','sast.ro','satukosong.com','sausen.com','saynotospams.com','scatmail.com','scay.net','schachrol.com','schafmail.de','schmeissweg.tk','schrott-email.de','sd3.in','secmail.pw','secretemail.de','secure-mail.biz','secure-mail.cc','secured-link.net','securehost.com.es','seekapps.com','sejaa.lv','selfdestructingmail.com','selfdestructingmail.org','sendfree.org','sendingspecialflyers.com','sendspamhere.com','senseless-entertainment.com','server.ms','services391.com','sexforswingers.com','sexical.com','sharedmailbox.org','sharklasers.com','shhmail.com','shhuut.org','shieldedmail.com','shieldemail.com','shiftmail.com','shipfromto.com','shiphazmat.org','shipping-regulations.com','shippingterms.org','shitmail.de','shitmail.me','shitmail.org','shitware.nl','shmeriously.com','shortmail.net','shotmail.ru','showslow.de','shrib.com','shut.name','shut.ws','sibmail.com','sify.com','simpleitsecurity.info','sin.cl','sinfiltro.cl','singlespride.com','sinnlos-mail.de','sino.tw','siteposter.net','sizzlemctwizzle.com','skeefmail.com','sky-inbox.com','sky-ts.de','slapsfromlastnight.com','slaskpost.se','slave-auctions.net','slopsbox.com','slothmail.net','slushmail.com','sly.io','smapfree24.com','smapfree24.de','smapfree24.eu','smapfree24.info','smapfree24.org','smashmail.de','smellfear.com','smellrear.com','smtp99.com','smwg.info','snakemail.com','sneakemail.com','sneakmail.de','snkmail.com','socialfurry.org','sofimail.com','sofort-mail.de','sofortmail.de','softpls.asia','sogetthis.com','sohu.com','soisz.com','solvemail.info','solventtrap.wiki','soodmail.com','soodomail.com','soodonims.com','soon.it','spam-be-gone.com','spam.la','spam.org.es','spam.su','spam4.me','spamail.de','spamarrest.com','spamavert.com','spambob.com','spambob.net','spambob.org','spambog.com','spambog.de','spambog.net','spambog.ru','spambooger.com','spambox.info','spambox.irishspringrealty.com','spambox.org','spambox.us','spamcero.com','spamcon.org','spamcorptastic.com','spamcowboy.com','spamcowboy.net','spamcowboy.org','spamday.com','spamdecoy.net','spamex.com','spamfighter.cf','spamfighter.ga','spamfighter.gq','spamfighter.ml','spamfighter.tk','spamfree.eu','spamfree24.com','spamfree24.de','spamfree24.eu','spamfree24.info','spamfree24.net','spamfree24.org','spamgoes.in','spamherelots.com','spamhereplease.com','spamhole.com','spamify.com','spaminator.de','spamkill.info','spaml.com','spaml.de','spamlot.net','spammotel.com','spamobox.com','spamoff.de','spamsalad.in','spamslicer.com','spamspot.com','spamstack.net','spamthis.co.uk','spamthisplease.com','spamtrail.com','spamtroll.net','spb.ru','speed.1s.fr','speedgaus.net','spikio.com','spoofmail.de','spr.io','spritzzone.de','spybox.de','squizzy.de','sry.li','ssoia.com','stanfordujjain.com','starlight-breaker.net','startfu.com','startkeys.com','statdvr.com','stathost.net','statiix.com','steambot.net','stexsy.com','stinkefinger.net','stop-my-spam.cf','stop-my-spam.com','stop-my-spam.ga','stop-my-spam.ml','stop-my-spam.tk','streetwisemail.com','stuckmail.com','stuffmail.de','stumpfwerk.com','suburbanthug.com','suckmyd.com','sudolife.me','sudolife.net','sudomail.biz','sudomail.com','sudomail.net','sudoverse.com','sudoverse.net','sudoweb.net','sudoworld.com','sudoworld.net','suioe.com','super-auswahl.de','supergreatmail.com','supermailer.jp','superplatyna.com','superrito.com','superstachel.de','suremail.info','svk.jp','sweetxxx.de','swift10minutemail.com','sylvannet.com','tafmail.com','tafoi.gr','tagmymedia.com','tagyourself.com','talkinator.com','tanukis.org','tapchicuoihoi.com','tb-on-line.net','techemail.com','techgroup.me','teewars.org','tefl.ro','telecomix.pl','teleworm.com','teleworm.us','temp-mail.com','temp-mail.de','temp-mail.org','temp.bartdevos.be','temp.emeraldwebmail.com','temp.headstrong.d','temp-mail.ru','tempail.com','tempalias.com','tempe-mail.com','tempemail.biz','tempemail.co.za','tempemail.com','tempemail.net','tempinbox.co.uk','tempinbox.com','tempmail.co','tempmail.eu','tempmail.it','tempmail.us','tempmail2.com','tempmaildemo.com','tempmailer.com','tempmailer.de','tempomail.fr','temporarily.de','temporarioemail.com.br','temporaryemail.net','temporaryemail.us','temporaryforwarding.com','temporaryinbox.com','temporarymailaddress.com','tempsky.com','tempthe.net','tempymail.com','testudine.com','thanksnospam.info','thankyou2010.com','thc.st','theaviors.com','thebearshark.com','thecloudindex.com','thediamants.org','thelimestones.com','thembones.com.au','themostemail.com','thereddoors.online','thescrappermovie.com','theteastory.info','thex.ro','thietbivanphong.asia','thisisnotmyrealemail.com','thismail.net','thisurl.website','thnikka.com','thraml.com','thrma.com','throam.com','thrott.com','throwam.com','throwawayemailaddress.com','throwawaymail.com','thunkinator.org','thxmate.com','tic.ec','tilien.com','timgiarevn.com','timkassouf.com','tinyurl24.com','tittbit.in','tiv.cc','tizi.com','tkitc.de','tlpn.org','tmail.com','tmail.ws','tmailinator.com','tmpjr.me','toddsbighug.com','toiea.com','tokem.co','tokenmail.de','tonymanso.com','toomail.biz','top101.de','top1mail.ru','top1post.ru','topofertasdehoy.com','topranklist.de','toprumours.com','tormail.org','toss.pw','tosunkaya.com','totalvista.com','totesmail.com','tqoai.com','tp-qa-mail.com','tradermail.info','tranceversal.com','trash-amil.com','trash-mail.at','trash-mail.cf','trash-mail.com','trash-mail.de','trash-mail.ga','trash-mail.gq','trash-mail.ml','trash-mail.tk','trash2009.com','trash2010.com','trash2011.com','trashcanmail.com','trashdevil.com','trashdevil.de','trashemail.de','trashinbox.com','trashmail.at','trashmail.com','trashmail.de','trashmail.me','trashmail.net','trashmail.org','trashmail.ws','trashmailer.com','trashymail.com','trashymail.net','trasz.com','trayna.com','trbvm.com','trbvn.com','trbvo.com','trialmail.de','trickmail.net','trillianpro.com','trollproject.com','tropicalbass.info','trungtamtoeic.com','tryalert.com','ttszuo.xyz','tualias.com','turoid.com','turual.com','twinmail.de','twoweirdtricks.com','ty.ceed.s','txtadvertise.com','tyhe.ro','tyldd.com','u14269.ml','ubismail.net','ubm.md','ufacturing.com','uggsrock.com','uguuchantele.com','uhhu.ru','uk.to','umail.net','undo.it','unimark.org','unit7lahaina.com','unmail.ru','upliftnow.com','uplipht.com','uploadnolimit.com','urfunktion.se','uroid.com','used-product.fr','username.e4ward.com','ux.dob.jp','ux.uk.to','vaasfc4.t','us.af','us.to','utiket.us','uu.gl','uwork4.us','uyhip.com','vaati.org','valemail.net','valhalladev.com','vankin.de','vda.ro','venompen.com','verdejo.com','veryday.ch','veryday.eu','veryday.info','veryrealemail.com','vesa.pw','vfemail.net','vickaentb.t','victime.ninja','victoriantwins.com','vidchart.com','viditag.com','viewcastmedia.com','viewcastmedia.net','viewcastmedia.org','visa.coms.h','vikingsonly.com','vinernet.com','vipmail.name','vipmail.pw','vipxm.net','viralplays.com','vixletdev.com','vkcode.ru','vmailing.info','vmani.com','vmpanda.com','voidbay.com','vomoto.com','vp.ycare.d','vorga.org','votiputox.org','voxelcore.com','vpn.st','vrmtr.com','vsimcard.com','vubby.com','vztc.com','w3internet.co.uk','wakingupesther.com','walala.org','walkmail.net','walkmail.ru','wazabi.club','we.qq.my','web-contact.info','web-emailbox.e','wallm.com','wasteland.rfc822.org','watch-harry-potter.com','watchever.biz','watchfull.net','watchironman3onlinefreefullmovie.com','wbml.net','web-mail.pp.ua','web-ideal.fr','webcontact-france.e','web.id','webemail.me','webm4il.info','webtrip.ch','webuser.in','wee.my','wef.gr','wefjo.grn.cc','weg-werf-email.de','wegwerf-email-addressen.de','wegwerf-email-adressen.de','wegwerf-email.de','wegwerf-email.net','wegwerf-emails.de','wegwerfadresse.de','wegwerfemail.com','wegwerfemail.de','wegwerfemail.net','wegwerfemail.org','wegwerfemailadresse.com','wegwerfmail.de','wegwerfmail.info','wegwerfmail.net','wegwerfmail.org','wegwerpmailadres.nl','wegwrfmail.de','wegwrfmail.net','wegwrfmail.org','welikecookies.com','wetrainbayarea.com','wetrainbayarea.org','wfgdfhj.t','wg0.com','wh4f.org','whatiaas.com','whatifanalytics.com','whatpaas.com','whatsaas.com','whiffles.org','whopy.com','whtjddn.33mail.com','whyspam.me','wibblesmith.com','wickmail.net','widget.gg','wilemail.com','willhackforfood.biz','willselfdestruct.com','wimsg.com','winemaven.info','wins.com.br','wmail.cf','wolfsmail.tk','wollan.info','wovz.cu.cc','wr.moeri.or','worldspace.link','wralawfirm.com','writeme.us','wronghead.com','wuzup.net','wuzupmail.net','www.e4ward.com','www.gishpuppy.com','www.mailinator.com','wwwnew.eu','x24.com','xagloo.co','xagloo.com','xcompress.com','xcpy.com','xemaps.com','xents.com','xing886.uu.g','xjoi.com','xl.cx','xmail.com','xmaily.com','xn--9kq967o.com','xoxox.cc','xrho.com','xwaretech.com','xwaretech.info','xwaretech.net','xww.ro','xy9ce.tk','xyzfree.net','yanet.me','yapped.net','yeah.ne','yaqp.com','ycare.de','ye.vc','yedi.org','yep.it','yert.ye.v','yhg.biz','ynmrealty.com','yodx.ro','yogamaven.com','yomail.info','yoo.ro','yopmail.com','yopmail.fr','yopmail.gq','yopmail.net','you-spam.com','yougotgoated.com','youmail.ga','yourlifesucks.cu.cc','ypmail.webarnak.fr.eu.or','youmailr.com','youneedmore.info','yourdomain.com','yourewronghereswhy.com','yourlms.biz','yspend.com','yugasandrika.com','yui.it','yuurok.com','yxzx.net','z1p.biz','z86.ru','za.com','zaktouni.fr','ze.gally.j','zasod.com','zebins.com','zebins.eu','zehnminuten.de','zehnminutenmail.de','zeta-telecom.co','zepp.dk','zetmail.com','zhouemail.510520.or','zfymail.com','zik.dj','zippymail.info','zipsendtest.com','zoaxe.com','zoemail.com','zoemail.net','zoemail.org','zoetropes.org','zombie-hive.com','zomg.info','zp.ua','zumpul.com','zxcv.com','zxcvbnm.com','zzz.com');
			$arrEmail = explode('@', $mail);
			if(count($arrEmail) > 1){
				return in_array($arrEmail[1], $mail_domains_ko);
				exit;
			}	
		}
		return '';
	}
	function get_coupon_code($conditions_array){
		$rows=array();		
		$this->db->from('red_coupons as rmp');
		
		$this->db->where($conditions_array);
		
		$result=$this->db->get();
		foreach($result->result_array() as $row)
		{
			$rows[]=$row;
		}
		return $rows;
	}
}
?>
