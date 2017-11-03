<?php
/**
* A delete_user_account class
*
* This class is for delete users from databse If user do not log into your account for more than xx days
*
* @version 1.0
* @author Pravin Jha <pravinjha@gmail.com>
* @project Redcappi
*/
class Delete_user_account extends CI_Controller
{
	function __construct(){
		parent::__construct();
	}
	/**
		Function index for delete users from database to users who have not login for xx days
	*/
	function index(){
		#set execution time
		set_time_limit(0); 
		#Check cronjob status :completed or working
		$cronjob_status=$this->check_cronjob_status();
		if($cronjob_status=="working"){
			exit;
		}else{
			# Load the user model which interact with database
			$this->load->model('ConfigurationModel');
			#update cronjob status to completed
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>'working'),array('config_name'=>'delete_user_cronjob_status'));
			#send notification to users who have not login for xx days
			$this->send_notification();
			#Delete users from account who have not login after sending notification
			$this->delete_users();
			#Delete user from account who are not confirmed
			$this->delete_unconfirmed_users();
			#delete user's contact lists, stats, campaigns from database after canceling subscription
			$this->delete_user_data_after_cancel_subscription();
			#update cronjob status to completed
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>'completed'),array('config_name'=>'delete_user_cronjob_status'));
		}		
	}
	function send_notification(){
		# Load the user model which interact with database
		$this->load->model('UserModel');
		##############################################################
		#	Fetch number of days for sending notification 			 #	
		##############################################################
		# Load the configuration model which interact with database
		$this->load->model('ConfigurationModel');
		$site_configuration_array=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'send_notification_to_user_after_xx_days'));
		$send_notification_to_user_after_xx_days=$site_configuration_array[0]['config_value'];
		#Fetch days after that days user account will be deleted
		$site_configuration_array=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'user_account_inactive_after_xx_days'));		
		$user_account_inactive_after_xx_days=$site_configuration_array[0]['config_value'];
		#Prepare array for where condition in an campign model
		$fetch_conditions_array=array(
			'DATEDIFF(CURDATE(),last_login_time)>'=>$send_notification_to_user_after_xx_days,
			'rmp.package_id <='=>'0',
			'email_address !=' => SYSTEM_EMAIL_FROM ,
			'login_expiration_notification_date IS NULL '=>NULL,
			'status'=>'active',			
		);
		
		#Fetch user detail from database
		$user_info=$this->UserModel->get_user_account_info($fetch_conditions_array);
		
		#send notfication to each user who have not login for xx days
		foreach($user_info as $user){
			if($user['is_deleted']==0){
				if(trim($user['first_name'])!=""){
					$user_name=$user['first_name'];
				}else{
					$user_name=$user['member_username'];
				}
				$last_login_time=date('Y-m-d',strtotime($user['last_login_time']));
				$current_date=date("Y-m-d");
				$days=$this->dateDiff($last_login_time,$current_date);
				$user_arr=array($user_name,$user['email_address'],$user_account_inactive_after_xx_days,$days);
				$this->load->helper('transactional_notification');
				#Update notification date in database
				$this->UserModel->update_user(array('login_expiration_notification_date'=>date("Y-m-d H:i:s")),array('member_id'=>$user['member_id']));
				@create_transactional_notification("user_account_expire",$user_arr);
			}else{
				#Update notification date in database
				$this->UserModel->update_user(array('login_expiration_notification_date'=>date("Y-m-d H:i:s")),array('member_id'=>$user['member_id']));
			}
		}
	}
	/**
	*	Function dateDiff to calculate days difference between two dates
	*
	*	@param integer $start Start date
	*	@param integer $end   End date
	*	@return integer difference between start and end date
	**/
	function dateDiff($start, $end) {
		$start_ts = strtotime($start);
		$end_ts = strtotime($end);
		$diff = $end_ts - $start_ts;
		return round($diff / 86400);
	}
	/**
		Function delete_users to delete user from account who have not login after sending notification
	*/
	function delete_users(){
		# Load the user model which interact with database
		$this->load->model('UserModel');
		##############################################################
		#	Fetch number of days when user account will be deleted	 #	
		##############################################################
		# Load the configuration model which interact with database
		$this->load->model('ConfigurationModel');
		$site_configuration_array=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'user_account_inactive_after_xx_days'));
		$user_account_inactive_after_xx_days=$site_configuration_array[0]['config_value'];
		#Prepare array for where condition in an campign model
		$fetch_conditions_array=array(
			'DATEDIFF(CURDATE(),login_expiration_notification_date)>'=> $user_account_inactive_after_xx_days,
			'login_expiration_notification_date IS NOT NULL '=>NULL,
			'rmp.package_id <='=>'0'
		);	
		#Fetch user detail from database
		$user_info=$this->UserModel->get_user_account_info($fetch_conditions_array);
		
		#Delete user from account one by one
		foreach($user_info as $user){
			$this->UserModel->delete_user_account($user['member_id']);
			$input_array=array('subscription_title'=>'All My Contacts',
					'subscription_id'=>'-'.$user['member_id'],
					'subscription_is_name'=>'1',
					'subscription_created_by'=>$user['member_id']
			);
			# Load subscription model class which handles database interaction
			$this->load->model('newsletter/subscription_Model');
			# Sends form input data to database via model object
			$subscription_id=$this->subscription_Model->create_subscription($input_array);
		}
	}
	/**
		Function delete_unconfirmed_users is to delete inactive users which are not confirmed
	*/
	function delete_unconfirmed_users(){
		# Load the user model which interact with database
		$this->load->model('UserModel');
		######################################################
		#	Fetch 	delete_unconfirmed_users_after_xx_days	 #	
		######################################################
		# Load the configuration model which interact with database
		$this->load->model('ConfigurationModel');
		$site_configuration_array=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'delete_unconfirmed_users_after_xx_days'));
		$delete_unconfirmed_users_after_xx_days=$site_configuration_array[0]['config_value'];
		
		#Prepare array for where condition in an campign model
		$fetch_conditions_array=array(
			'DATEDIFF(CURDATE(),created_on)>'=>$delete_unconfirmed_users_after_xx_days,
			'rmp.package_id <='=>'0',
			'email_address !=' =>SYSTEM_EMAIL_FROM ,
			'status'=>'unconfirmed' 
		);	
		#Fetch user detail from database
		$user_info=$this->UserModel->get_user_account_info($fetch_conditions_array);
		
		foreach($user_info as $user){
			$this->UserModel->delete_user_account($user['member_id']);
			# Delete user's account permanetly from database
			$this->UserModel->delete_user($user['member_id']);
		}
	}
	/**
		Function delete_user_data_after_cancel_subscription is to delete user's contact lists, stats, campaigns from database after canceling subscription
	*/
	function delete_user_data_after_cancel_subscription(){
		# Load the user model which interact with database
		$this->load->model('UserModel');
		##################################################
		#	Fetch 	cancel_subscription_after_xx_days	 #	
		##################################################
		# Load the configuration model which interact with database
		$this->load->model('ConfigurationModel');
		$site_configuration_array=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'cancel_subscription_after_xx_days'));
		$cancel_subscription_after_xx_days=$site_configuration_array[0]['config_value'];
		
		#Prepare array for where condition in an campign model
		$fetch_conditions_array=array(
			'DATEDIFF(CURDATE(),cancel_subscription_date)>'=> $cancel_subscription_after_xx_days,
			'cancel_subscription_date IS NOT NULL '=>NULL,
			'rmp.package_id <='=>'0',
		);	
		#Fetch user detail from database
		$user_info=$this->UserModel->get_user_account_info($fetch_conditions_array);
		
		#Delete user's contact lists, stats, campaigns from database one by one
		foreach($user_info as $user){
			#cancel user's subscription
			$this->UserModel->cancel_subscription($user['member_id']);
			#Delete user's stat
			$this->UserModel->delete_stat($user['member_id']);
			#Delete user's campaigns
			$this->UserModel->delete_campaign($user['member_id']);
			#Delete user's autoresponders
			$this->UserModel->delete_autoresponder($user['member_id']);
			#Delete user's signup forms
			$this->UserModel->delete_signup_forms($user['member_id']);
			#Delete user's contacts from database
			$this->UserModel->delete_contacts($user['member_id']);	
			$this->UserModel->update_user(array('cancel_subscription_date'=>NULL),array('member_id'=>$user['member_id']));
			$update_arr=array(
				'package_id'=>'-1',
				'credit_card_last_digit'=>'',
				'expiration_date'=>'',
				'card_holder_name'=>'',
				'first_name'=>'',
				'last_name'=>'',
				'address'=>'',
				'city'=>'',
				'state'=>'',
				'zip'=>'',
				'country'=>'',
				'customer_profile_id'=>'',
				'customer_payment_profile_id'=>'',
				'amount_to_member'=>'',
				'amount'=>'',
			);
			$this->UserModel->update_member_package($update_arr,array('member_id'=>$user['member_id']));
			$input_array=array('subscription_title'=>'All My Contacts',
					'subscription_id'=>'-'.$user['member_id'],
					'subscription_is_name'=>'1',
					'subscription_created_by'=>$user['member_id']
			);
			# Load subscription model class which handles database interaction
			$this->load->model('newsletter/subscription_Model');
			# Sends form input data to database via model object
			$subscription_id=$this->subscription_Model->create_subscription($input_array);
		}
	}
	/**
		Function check_cronjob_status to fetch cronjob status
		@ return string cronjob_status
	*/
	function check_cronjob_status(){
		// Load the user model which interact with database
		$this->load->model('ConfigurationModel');
		$confg_arr=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'delete_user_cronjob_status'));
		return $confg_arr[0]['config_value'];
	}
}
?>