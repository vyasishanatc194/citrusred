<?php
/**
* A Payment_cronjob class
*
* This class is to to make a payment using cronjob
*
* @version 1.0
* @author Pravin Jha <pravinjha@gmail.com>
* @project Redcappi
*/
class Payment_cronjob extends CI_Controller
{
	var $loginname;
    var $transactionkey;
    var $host;
	var $path;	
	var	$payment_err_msg;
	var $error;
	var $post_url;
	function __construct(){
		parent::__construct();		
		global $CI;
        $CI =& get_instance();
        
		//collect config varibales
        $this->loginname    	 = $CI->config->item('loginname');	#Collect loginame
        $this->transactionkey    = $CI->config->item('transactionkey');	#Collect transactionkey
		
		$CI->load->library('KM');
		// KM::init("0a3afcfb8bd28bda7d820a02efc3bf70dbd06ea2", array('log_dir' => '/home/redcappi/km/'));
		KM::init('0a3afcfb8bd28bda7d820a02efc3bf70dbd06ea2');
		
		$this->load->library('Billingcim'); # load billing library
		$this->billingcim->loginKey($this->loginname, $this->transactionkey, $CI->config->item('test_mode'));
		
		$this->load->model('ConfigurationModel');
		$this->load->model('UserModel');
		
		$this->load->helper('transactional_notification');
		$this->load->helper('notification');
		
		$this->session->set_userdata('member_time_zone', 'GMT');
		date_default_timezone_set('GMT');
		
		
		 
	}
	function index($user_id=0,$first_payment=0){	
		#Fetch Mmaximum Declined Paymnet counter from site configuration
		$site_configuration_array=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'maximum_payment_declined'));
		$maximum_payment_declined=$site_configuration_array[0]['config_value'];
		
		########################################################################
		#Fetch CustomerProfileId and CustomerPaymentProfileId From database  ###
		########################################################################
		
		$user_porfile_count=$this->UserModel->get_user_packages_count(array('is_admin'=>0,'package_id >'=>0,'is_status'=>1,'DATEDIFF(next_payement_date, CURDATE()) <='=>0,'member_payment_declined_count <'=>$maximum_payment_declined));	#Count User
		 
		if($user_porfile_count>0){
			$user_profile_arr=$this->UserModel->get_user_packages_with_canceldt(array('is_admin'=>0,'p.package_id >'=>0,'is_status'=>1,'DATEDIFF(next_payement_date, CURDATE()) <='=>0,'member_payment_declined_count <'=>$maximum_payment_declined),$user_porfile_count);	#Fetch user info			
			 
			#####################
			# Make a payement  ##
			#####################
			foreach($user_profile_arr as $user){
				if ($user_profile_arr['payment_type'] == 0) {
					if($user['cancel_subscription_date']!== NULL){
						#cancler payment profile of user's who have cacel their subscription
						$this->cancel_subscription($user['member_id']);
					}else{
						#echo $user['member_id']."<br/>";
						###############################################
						# Do Payment if amount is greate than 0		  #
						###############################################
						if($user['amount']>0){
							if(!$this->createCustomerProfileTransactionRequest($user,$user['member_id'],$first_payment)){
								if($user_id>0){
									redirect('newsletter/campaign');
								}
							}
						}else if($user['amount']<=0){
							#######################################
							# Calculate Next payment Date		  #
							#######################################
							if($user_id<=0){
								$selected_package_price=$this->calculatePackageDetail($user['package_id']);
								#######################################
								# Calculate Next payment Date		  #
								#######################################
								/* $current_payement_date = $user_profile_arr['next_payement_date'];// current date
								$next_payement_timestamp=strtotime(date("Y-m-d", strtotime($current_payement_date)) . "+1 month");
								$next_payement_date=date('Y-m-d', $next_payement_timestamp); */
								$current_payement_date=date("Y-m-d");		#Next month payment date
								$next_payement_timestamp=strtotime(date("Y-m-d", strtotime($current_payement_date)) . "+1 month");
								$next_payement_date=date('Y-m-d', $next_payement_timestamp);
								 
									$update_array=array(
										'next_payement_date'=>$next_payement_date,
										'package_id'=>$user['package_id'],				
										'amount'=>$selected_package_price,
										'campaign_sent_counter'=>0,
										'amount_to_member'=>0,
										'is_payment'=>1,
										'member_payment_declined_count'=>0
									);
								 
							}else{
								$selected_package_price=$this->calculatePackageDetail($user['package_id'])+$user['amount'];
								$update_array=array(
									'package_id'=>$user['package_id'],
									'amount'=>$selected_package_price,
									'campaign_sent_counter'=>0,
									'is_payment'=>1,
									'member_payment_declined_count'=>0
								);
							}
							# update package id in member package table
							$this->UserModel->update_member_package($update_array,array('member_id'=>$user['member_id']));
							if($user_id>0){
								##################################################
								# Find out previous package maxixmum contacts    #
								##################################################
								$package_array=$this->UserModel->get_packages_data(array('package_id'=>$user['package_id']));
								$previous_package_max_contacts=$package_array[0]['package_max_contacts'];
								##################################################
								# Find out current package detail				 #
								##################################################
								$package_array=$this->UserModel->get_packages_data(array('package_id'=>$user['selected_package_id']));
								$current_package_max_contacts=$package_array[0]['package_max_contacts'];			
								$current_package_min_contacts=$package_array[0]['package_min_contacts'];			
								
								##################################################
								# Send Upgraded notification to admin			 #
								##################################################
								$this->upgraded_package_notification($previous_package_max_contacts,$current_package_max_contacts,$user['member_id']);
								#######################
								# Set success message #
								#######################
								if($current_package_max_contacts>=$previous_package_max_contacts){
									
									$this->messages->add('Your plan was upgraded to the "'.$current_package_min_contacts.'-'.$current_package_max_contacts.'" plan', 'success');
								}else{
									$this->messages->add('Your plan was downgraded to the "'.$current_package_min_contacts.'-'.$current_package_max_contacts.'" plan', 'success');
								}
								
								#############################
								# create activity log		#
								#############################
								$this->createActivityLog($user['member_id']);
							}
							//move user's to redcappi paid account subscription list
							$this->paid_member_to_redcappi_account($user['member_id']);
						}
					}
				}
			}
			if($user_id>0){
				redirect('newsletter/campaign');
			}
		}else{
			$update_array=array( 'is_payment'=>0);

			# update package id in memeber package table
			$this->UserModel->update_member_package($update_array,array('(DATEDIFF(next_payement_date, CURDATE())-30) >'=>0));
		}
		
		$this->resetMembersCampaignCounter();
		
	}
	/**
	*	This function is used to create a payment transaction from an existing customer profile
	**/
	function createCustomerProfileTransactionRequest($user_profile_arr,$user_id=0,$first_payment=0){
		
		/**
		Modifications done for Coupon's module
		on 31Jan 2013 by pravinjha@gmail.com
		*/
		$this->load->model('payment/payment_model');
		$user_package_price=$this->calculatePackageDetail($user_profile_arr['package_id']);
		$user_profile_arr['amount'] = $this->payment_model->getDiscountedAmountForSubsequentPayments($user_id,$user_package_price);
		 
		/* Coupon Module Updation Ends*/
		
		$user_profile_arr['amount']=floatval($user_profile_arr['amount']);
		
		if(is_float($user_profile_arr['amount'])){
			$user_profile_arr['amount']=round($user_profile_arr['amount'], 2);			
			//$user_profile_arr['amount']=23;			
			$pos =strpos($user_profile_arr['amount'], ".");
			if ($pos>0) {
				$amount_arr=explode(".",$user_profile_arr['amount']);
				$amount_len=strlen($amount_arr[1]);
				if($amount_len==0){
					$user_profile_arr['amount']=$user_profile_arr['amount'].".0000";
				}elseif($amount_len==1){
					$user_profile_arr['amount']=$user_profile_arr['amount']."000";
				}elseif($amount_len==2){
					$user_profile_arr['amount']=$user_profile_arr['amount']."00";
				}elseif($amount_len==3){
					$user_profile_arr['amount']=$user_profile_arr['amount']."0";
				}
			}else{
				$user_profile_arr['amount']=$user_profile_arr['amount'].".0000";
			}
		}else{
			$user_profile_arr['amount']=$user_profile_arr['amount'].".0000";
		}
		$amount=$user_profile_arr['amount'];		#Conver amount to float point number		
		
		# Total Amount: This amount should include all other amounts such as tax amount, shipping amount, etc.
		$this->billingcim->setParameter('transaction_amount', $amount); # Up to 4 digits with a decimal (required)		
		
		# transactionType = (profileTransCaptureOnly, profileTransAuthCapture or profileTransAuthOnly)
		$this->billingcim->setParameter('transactionType', 'profileTransAuthCapture');
		# Payment gateway assigned ID associated with the customer profile
		$this->billingcim->setParameter('customerProfileId', $user_profile_arr['customer_profile_id']); # Numeric (required)
		
		# Payment gateway assigned ID associated with the customer payment profile
		$this->billingcim->setParameter('customerPaymentProfileId', $user_profile_arr['customer_payment_profile_id']); # Numeric (required)	

		// The tax exempt status
		$this->billingcim->setParameter('transactionTaxExempt', 'false');
		
		// The recurring billing status
		$this->billingcim->setParameter('transactionRecurringBilling', 'false');
		// Kissmetrics starts
		$this_user_info=$this->UserModel->get_user_account_info(array('m.member_id'=>$user_profile_arr['member_id']));
		KM::identify($this_user_info[0]['member_username']);		 
				 
		// Kissmetrics ends
		$this->billingcim->createCustomerProfileTransactionRequest();
		if ($this->billingcim->isSuccessful()){
			// Calculate selected package Amount			
			$selected_package_price=$this->calculatePackageDetail($user_profile_arr['package_id']);			
			//  Calculate Next payment Date	
			$current_payement_date=date("Y-m-d");		// Next month payment date
			$next_payement_timestamp= strtotime(date("Y-m-d", strtotime($current_payement_date)) . "+1 month");
			$next_payement_date=date('Y-m-d', $next_payement_timestamp);
			$update_array=array(
				'next_payement_date'=>$next_payement_date,
				'package_id'=>$user_profile_arr['package_id'],				
				'amount'=>$selected_package_price,
				'campaign_sent_counter'=>0,
				'is_payment'=>1,
				'member_payment_declined_count'=>0
			);
			// update package id in member package table
			$this->UserModel->update_member_package($update_array,array('member_id'=>$user_profile_arr['member_id']));
			// Active user account
			$this->UserModel->update_user(array('status'=>'active','login_expiration_notification_date'=>NULL,'cancel_subscription_date'=>NULL),array('member_id'=>$user_profile_arr['member_id']));
			KM::record('Payment', array('package' => $user_profile_arr['package_id'], 'amount' => $selected_package_price, 'status' => 'SUCCESS','payment_type'=>2));			
			$this->session->set_userdata('member_status','active');		

			$response="";
			$response.=$this->billingcim->response;
			$response.="|".$this->billingcim->directResponse;
			$response.="|".$this->billingcim->validationDirectResponse;
			$response.="|".$this->billingcim->resultCode;
			$response.="|".$this->billingcim->code;
			$response.="|".$this->billingcim->text;
			$response = preg_replace("/\xef\xbb\xbf/","",$response);			 
			// Insert Payment Success transaction in database	
			$input_data = array ( 'user_id'=>$user_profile_arr['member_id'],'package_id'=>$user_profile_arr['package_id'],'gateway'=>'AUTHORIZE','amount_paid'=>$amount, 'payment_type'=>2,'status'=>'SUCCESS','gateway_response'=>$response);
			$this->UserModel->insert_payment_transactions($input_data);			
			send_mail(DEVELOPER_EMAIL, SYSTEM_EMAIL_FROM  ,'system' , SYSTEM_DOMAIN_NAME.': Redcappi payment Success - '.$selected_package_price,$user_profile_arr['member_id'].":$response",$user_profile_arr['member_id'].":$response");
			return true;
		}else{
			$response="";
			$response.=$this->billingcim->response;
			$response.="|".$this->billingcim->directResponse;
			$response.="|".$this->billingcim->validationDirectResponse;
			$response.="|".$this->billingcim->resultCode;
			$response.="|".$this->billingcim->code;
			$response.="|".$this->billingcim->text;
			$response = preg_replace("/\xef\xbb\xbf/","",$response);			
			KM::record('Payment', array('package' => $user_profile_arr['package_id'], 'amount' => 0, 'status' => 'FAILURE','payment_type'=>2));
			//  Insert PAyment failure transaction in database	
			$input_data = array ( 'user_id'=>$user_profile_arr['member_id'],'package_id'=>$user_profile_arr['package_id'],'gateway'=>'AUTHORIZE','amount_paid'=>'0','payment_type'=>2, 'status'=>'FAILURE','gateway_response'=>$response);			
			$this->UserModel->insert_payment_transactions($input_data);
			$member_payment_declined_count=$user_profile_arr['member_payment_declined_count']+1;
			$update_array=array('member_payment_declined_count'=>$member_payment_declined_count	);
			// update package id in memeber package table
			$this->UserModel->update_member_package($update_array,array('member_id'=>$user_profile_arr['member_id']));		
			// admin notification
			$strAdminEmails = get_Admin_notification_email();	
			$arrAdminEmails	 = @explode(',',$strAdminEmails);
			foreach($arrAdminEmails as $adminEmail){
				if(trim($adminEmail) != '')
				send_mail($adminEmail, SYSTEM_EMAIL_FROM  ,'system' , SYSTEM_DOMAIN_NAME.': Redcappi payment Fail - '.$selected_package_price,$user_profile_arr['member_id'].":$response",$user_profile_arr['member_id'].":$response");
			}
			//send_mail(DEVELOPER_EMAIL, SYSTEM_EMAIL_FROM  ,'system' , SYSTEM_DOMAIN_NAME.': Redcappi payment Fail',$user_profile_arr['member_id'].":$response",$user_profile_arr['member_id'].":$response");
			//transactional_notification mail
			if($member_payment_declined_count > 1){
				$user_info=$this->UserModel->get_user_account_info(array('m.member_id'=>$user_profile_arr['member_id']));
				foreach($user_info as $user){
					$user_arr=array($user['member_username'],$user['email_address']);
					@create_transactional_notification("redcappi_payment_failure",$user_arr);
					/* foreach($arrAdminEmails as $adminEmail){
						if(trim($adminEmail) != ''){
							$user_arr=array($user['member_username'],$adminEmail);
							@create_transactional_notification("redcappi_payment_failure",$user_arr);
						}
					} */
				}	
			}	
			return false;			
		}
	}
	
	function cancel_subscription($user_id=0){
		# Load the user model which interact with database
		$this->load->model('UserModel');
		$update_array=array(
						'package_id'=>-1,						
					);
		$this->UserModel->update_member_package($update_array,array('member_id'=>$user_id));
		/* $update_member_array=array(
								'cancel_subscription_date'=>NULL
							);
		$this->UserModel->update_user($update_member_array,array('member_id'=>$user_id)); */
		$this->UserModel->cancel_subscription($user_id);
	}
	/**
	* Function calculatePackageDetail to fetch package deail from database using package id
	**/
	function calculatePackageDetail($selected_package_id=0){
		$selected_package_array=$this->UserModel->get_packages_data(array('package_id'=>$selected_package_id));
		$selected_package_price=$selected_package_array[0]['package_price'];
		return $selected_package_price;
	}
	
	/**
	*	Function to send  notification email to admin for upgraded package
	*
	*	@param integer $previous_package_max_contacts  previous package maximum contacts
	*	@param integer $current_package_max_contacts  selected package maximum contacts
	**/
	function upgraded_package_notification($previous_package_max_contacts=0,$current_package_max_contacts=0,$member_id=0){
		# Load the user model which interact with database
		$this->load->model('UserModel');
		# Fetch user data from database
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$member_id));
		$user_info=array($user_data_array[0]['member_username'],$previous_package_max_contacts,$current_package_max_contacts);
		
		create_notification("upgraded",$user_info);
	}
	/**
	*	Function createActivityLog to create activity history in database
	**/
	function createActivityLog($member_id=0){
		# Load log activity model class which handles database interaction
		$this->load->model('Activity_Model');
		# create array for insert values in activty table
		$values=array('user_id'=>$member_id,'activity'=>'upgrade');
		$this->Activity_Model->create_activity($values);
	}
	/**
		Function paid_member_to_redcappi_account to move user's to redcappi paid account subscription list
	*/
	function paid_member_to_redcappi_account($user_id=0){
		// Load the user model which interact with database
		$this->load->model('UserModel');
		
		$subscriber_created_by=157;
		
		//Get registered users from database
		$user_count=$this->UserModel->get_user_count(array('is_deleted'=>0,'member_id'=>$user_id));
		$users_array=$this->UserModel->get_user_data(array('is_deleted'=>0,'member_id'=>$user_id),$user_count);
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
					$flds .=  'subscriber_created_by = '.$subscriber_created_by ;
					$qry .=  $flds .' ON DUPLICATE KEY UPDATE ' . $flds . ', is_deleted = 0,subscriber_status=1,is_signup=1 , subscriber_id=LAST_INSERT_ID(subscriber_id)';
					$this->db->query($qry);
					$last_inserted_id = $this->db->insert_id();
					if($member_id==157){
						$del_sublistid=122;
						$sublistid=245;
					}else{
						$del_sublistid=78;
						$sublistid=79;
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
	
		
	function resetMembersCampaignCounter(){
		//$sqlMember = "SELECT member_id,  DATE_FORMAT( IF( ISNULL( start_payment_date ) , date_added, start_payment_date ) ,  '%Y-%m-%d' ) AS checkdt FROM `red_member_packages`";
		$sqlMember = "SELECT member_id,  DATE_FORMAT( IF( ISNULL( next_payement_date ) , date_added, next_payement_date ) ,  '%Y-%m-%d' ) AS checkdt FROM `red_member_packages`";
		
		$rsSubscriptionCycle = $this->db->query($sqlMember);
		
		if ($rsSubscriptionCycle->num_rows() > 0){
			foreach($rsSubscriptionCycle->result_array() as $rowSubscriptionCycle){			 
				$member_id = $rowSubscriptionCycle['member_id'];  
				$payment_date = $rowSubscriptionCycle['checkdt'];  
				 
				
				if($payment_date == date('Y-m-d')){					
					$this->db->query("update `red_member_packages` set `campaign_sent_counter`=0 where `member_id`='$member_id'");
				}	
				 
			}		
		}
	}
	
	
}
?>