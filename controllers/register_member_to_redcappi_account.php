<?php
class Register_Member_To_Redcappi_Account extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}	
	
	function index(){
		// Load the user model which interact with database
		$this->load->model('UserModel');
		
		$member_id=157;
		
		//Get registered users from database
		echo $user_count=$this->UserModel->get_user_count(array('rm.is_deleted'=>0,'rm.member_id !='=>$member_id),true);
		$users_array=$this->UserModel->get_user_data(array('rm.is_deleted'=>0,'rm.member_id !='=>$member_id),$user_count,0,true);
		
		$signup_data=array();
		foreach($users_array as $user){
			$register_user=false;
			foreach($user as $key=>$value){
				if($key=="email_address"){
					if($value!=''){
						$signup_data['subscriber_email_address']=$value;
						$arrEmailExploded = explode( '@',$signup_data['subscriber_email_address'] );
						$signup_data['subscriber_email_domain'] = $arrEmailExploded[1];
						$register_user=true;
					}
				}
				if($register_user){
					if($key=="first_name"){
						$signup_data['subscriber_first_name']=$value;
					}
					if($key=="last_name"){
						$signup_data['subscriber_last_name']=$value;
					}
					if($key=="address_line_1"){
						$signup_data['subscriber_address']=$value;
					}
					if($key=="city"){
						$signup_data['subscriber_city']=$value;
					}
					if($key=="state"){
						$signup_data['subscriber_state']=$value;
					}
					if($key=="zipcode"){
						$signup_data['subscriber_zip_code']=$value;
					}
					if($key=="country_name"){
						$signup_data['subscriber_country']=$value;
					}
					if($key=="company"){
						$signup_data['subscriber_company']=$value;
					}
					// Load subscriber model class which handles database interaction		
					$this->load->model('newsletter/Subscriber_Model');
					//create subscriber
					$qry = "INSERT INTO red_email_subscribers SET ";
					$flds = '';
					foreach($signup_data as $key=>$val)  $flds .= $key . ' = \'' . mysql_real_escape_string($val) . '\', ';
					$flds .=  'subscriber_created_by = '.$member_id ;
					$qry .=  $flds .' ON DUPLICATE KEY UPDATE ' . $flds . ', is_deleted = 0,subscriber_status=1 ,is_signup=1, subscriber_id=LAST_INSERT_ID(subscriber_id)';
					$this->db->query($qry);
					$last_inserted_id = $this->db->insert_id();
					if(($user['package_id']>0)&&($user['is_admin']<=0)&&(strtotime($user['next_payement_date'])>=strtotime(date("Y-m-d")))){
						if($member_id==157){
							$del_sublistid=122;
							$sublistid=245;
						}else{
							$del_sublistid=78;
							$sublistid=79;
						}
					}else{
						if($member_id==157){
							$del_sublistid=245;
							$sublistid=122;
						}else{
							$del_sublistid=79;
							$sublistid=78;
						}
					}
					if ($last_inserted_id > 0 and $sublistid > 0){
						$this->Subscriber_Model->delete_subscription_subscriber(array('subscriber_id'=>$last_inserted_id,'subscription_id'=>$del_sublistid));
						$input_array=array('subscriber_id'=>$last_inserted_id,'subscription_id'=>$sublistid);
						$this->Subscriber_Model->replace_subscription_subscriber($input_array);
					}else{
						$qry="SELECT subscriber_id FROM red_email_subscribers WHERE subscriber_email_address='$value' AND is_deleted = 0 AND subscriber_status=1 AND is_signup=1";
						$subscriber_qry=$this->db->query($qry);
						$subscriber_data_array=$subscriber_qry->result_array();	#Fetch resut
						if($subscriber_data_array[0]['subscriber_id']>0){
							$this->Subscriber_Model->delete_subscription_subscriber(array('subscriber_id'=>$subscriber_data_array[0]['subscriber_id'],'subscription_id'=>$del_sublistid));
							$input_array=array('subscriber_id'=>$subscriber_data_array[0]['subscriber_id'],'subscription_id'=>$sublistid);
							$this->Subscriber_Model->replace_subscription_subscriber($input_array);
						}
					}
				}
			}
		}		
	}
}
/* End of file */
?>