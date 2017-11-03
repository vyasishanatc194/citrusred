<?php
class Sitesetting_Manage extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');
			
		# HTTPS/SSL enabled
		force_ssl();
		$this->load->model('UserModel');
		$this->load->model('webmaster/Account_Model');
		$this->load->model('ConfigurationModel');
	}
	function change_password(){
		// To check if form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied			
			$this->form_validation->set_rules('webmaster_password', 'Current Password', 'required|min_length[2]|max_length[250]|trim');
			$this->form_validation->set_rules('webmaster_new_password', 'New Password', 'required|min_length[2]|max_length[250]|trim|matches[webmaster_confirm_password]');
			$this->form_validation->set_rules('webmaster_confirm_password', 'Confirm New Password', 'required|min_length[2]|max_length[250]|trim');			
			
			// To check form is validated
			if($this->form_validation->run()==true)
			{
				// Fetch user data from database
				$user_data_array=$this->Account_Model->get_account_data(array('webmaster_id'=>$this->session->userdata('webmaster_id')));

				$webmaster_password=md5($this->input->post('webmaster_password', TRUE));
				
				if($user_data_array[0]['webmaster_password']==$webmaster_password)
				{
					// Retrieve data posted in form posted by user using input class
					$user_credentails=array('webmaster_password'=>md5($this->input->post('webmaster_new_password', TRUE)));
					
					$this->Account_Model->update_account($user_credentails,array('webmaster_id'=>$this->session->userdata('webmaster_id')));
					
					$this->messages->add('Password Successfully Changed', 'success');
					
					//Redirect to campaign page
					redirect('webmaster/sitesetting_manage/change_password');
				}
				else
				{
					// Assign message in case of invalid username or pass
					$this->messages->add('Wrong Current Password', 'error');
				} 
			}
		}
		
		// Recieve any messages to be shown, when campaign is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, campaign and footer view.
		$this->load->view('webmaster/header',array('title'=>'Change Password','logo_link'=>$logo_link));
		$this->load->view('webmaster/site_change_password',array('messages' =>$messages));
		$this->load->view('webmaster/footer');
		
	}
	function general_setting(){
		 
		// To check if form is submitted
		if($this->input->post('action')=='submit'){
			// Validation rules are applied			
			$this->form_validation->set_rules('maximum_payment_declined', 'Maximum Payment Declined', 'required|integer');
			$this->form_validation->set_rules('maximum_add_contact', 'Maximum Add Contact', 'required|integer');
			$this->form_validation->set_rules('maximum_delete_contact', 'Maximum Delete Contact', 'required|integer');
			
			$this->form_validation->set_rules('maximum_bounce_contact', 'Maximum Bounce Contact', 'required|integer');
			$this->form_validation->set_rules('fbl_critical_limit', 'FBL Critical Level', 'required|numeric');
			$this->form_validation->set_rules('unsubscribe_critical_level_to_alert', 'Unsubscribe Critical Level', 'required|numeric');
			
			$this->form_validation->set_rules('unsubscribe_critical_level_to_pause', 'Unsubscribe Critical Level to pause', 'required|numeric');
			$this->form_validation->set_rules('fbl_critical_level_to_pause', 'FBL Critical Level to pause', 'required|numeric');
			$this->form_validation->set_rules('bounce_critical_level_to_pause', 'Bounce Critical Level to pause', 'required|numeric');
			
			
			$this->form_validation->set_rules('max_soft_bounce', 'Maximum Soft Bounce Contact', 'required|integer');
			$this->form_validation->set_rules('ignore_softbounced_for_x_days', 'Ignore Soft Bounce for X Days', 'required|integer');
			$this->form_validation->set_rules('admin_notification_email', 'Admin Notification Email', 'required|valid_emails');
			$this->form_validation->set_rules('comment_close_days', 'Comment Close Days', 'required|integer');
			$this->form_validation->set_rules('send_notification_to_user_after_xx_days', 'Send notification to user after xxx days', 'required|integer');
			$this->form_validation->set_rules('user_account_inactive_after_xx_days', 'User Account Inactive Days', 'required|integer');
			$this->form_validation->set_rules('delete_unconfirmed_users_after_xx_days', 'Unconfimed user after xxx days', 'required|integer');
			$this->form_validation->set_rules('cancel_subscription_after_xx_days', 'Cancel subscription after xxx days', 'required|integer');
			$this->form_validation->set_rules('campaign_stat_archive_after_xx_days', 'Campaign Stat Archive After xx days', 'required|integer');
			$this->form_validation->set_rules('max_contacts_to_unauthenticate', 'Unauthenticate account after adding contacts more than', 'required|integer');
			$this->form_validation->set_rules('max_contacts_for_list_growing_alert', 'Apply list-growing message after adding contacts more than', 'required|integer');
			$this->form_validation->set_rules('do_not_mail_list', 'Do Not Mail List Strings', 'required');
			$this->form_validation->set_rules('campaign_forward_limit', 'Maximum Friends to Forward a campaign', 'required');
			$this->form_validation->set_rules('seedlist', 'Seed-list contacts', 'required');
			$this->form_validation->set_rules('per_mail_price_for_credit', 'Per Email Price for Credit Plan', 'required');
			$this->form_validation->set_rules('max_contact_add_by_credit', 'Maximum Add Contacts By user With Credit Plan', 'required');
			
			
			// To check form is validated
			if($this->form_validation->run()==true){
				$input_array=array(
					'maximum_payment_declined'=>$this->input->post('maximum_payment_declined', TRUE),
					'maximum_add_contact'=>$this->input->post('maximum_add_contact', TRUE),
					'maximum_delete_contact'=>$this->input->post('maximum_delete_contact', TRUE),
					'maximum_bounce_contact'=>$this->input->post('maximum_bounce_contact', TRUE),
					'fbl_critical_limit'=>$this->input->post('fbl_critical_limit', TRUE),
					'unsubscribe_critical_level_to_alert'=>$this->input->post('unsubscribe_critical_level_to_alert', TRUE),
					'unsubscribe_critical_level_to_pause'=>$this->input->post('unsubscribe_critical_level_to_pause', TRUE),
					'fbl_critical_level_to_pause'=>$this->input->post('fbl_critical_level_to_pause', TRUE),
					'bounce_critical_level_to_pause'=>$this->input->post('bounce_critical_level_to_pause', TRUE),
					'max_soft_bounce'=>$this->input->post('max_soft_bounce', TRUE),
					'ignore_softbounced_for_x_days'=>$this->input->post('ignore_softbounced_for_x_days', TRUE),
					'admin_notification_email'=>$this->input->post('admin_notification_email', TRUE),
					'comment_close_days'=>$this->input->post('comment_close_days', TRUE),
					'send_notification_to_user_after_xx_days'=>$this->input->post('send_notification_to_user_after_xx_days', TRUE),
					'user_account_inactive_after_xx_days'=>$this->input->post('user_account_inactive_after_xx_days', TRUE),
					'default_allowed_limit_for_send_email'=>$this->input->post('default_allowed_limit_for_send_email', TRUE),
					'delete_unconfirmed_users_after_xx_days'=>$this->input->post('delete_unconfirmed_users_after_xx_days', TRUE),
					'cancel_subscription_after_xx_days'=>$this->input->post('cancel_subscription_after_xx_days', TRUE),
					'campaign_stat_archive_after_xx_days'=>$this->input->post('campaign_stat_archive_after_xx_days', TRUE),
					'max_contacts_to_unauthenticate'=>$this->input->post('max_contacts_to_unauthenticate', TRUE),
					'max_contacts_for_list_growing_alert'=>$this->input->post('max_contacts_for_list_growing_alert', TRUE),
					'do_not_mail_list'=>$this->input->post('do_not_mail_list', TRUE),
					'campaign_forward_limit'=>$this->input->post('campaign_forward_limit', TRUE),
					'unresponsive_ignored'=>$this->input->post('unresponsive_ignored', TRUE),
					'seedlist'=>$this->input->post('seedlist', TRUE),
					'per_mail_price_for_credit'=>$this->input->post('per_mail_price_for_credit', TRUE),
					'max_contact_add_by_credit'=>$this->input->post('max_contact_add_by_credit', TRUE)
				);
				foreach($input_array as $key=>$val){
					$this->ConfigurationModel->update_site_configuration(array('config_value'=>$val),array('config_name'=>$key));
					if($key == 'max_contact_add_by_credit'){
						$this->UserModel->update_package(array('package_max_contacts'=>$this->input->post('max_contact_add_by_credit', TRUE)),array('package_recurring_interval'=> 'credit'));
					}
				}
			}
			$maximum_payment_declined=$this->input->post('maximum_payment_declined', TRUE);
			$maximum_add_contact=$this->input->post('maximum_add_contact', TRUE);
			$maximum_delete_contact=$this->input->post('maximum_delete_contact', TRUE);
			$maximum_bounce_contact=$this->input->post('maximum_bounce_contact', TRUE);
			$fbl_critical_limit=$this->input->post('fbl_critical_limit', TRUE);
			$unsubscribe_critical_level_to_alert=$this->input->post('unsubscribe_critical_level_to_alert', TRUE);
			$unsubscribe_critical_level_to_pause=$this->input->post('unsubscribe_critical_level_to_pause', TRUE);
			$fbl_critical_level_to_pause=$this->input->post('fbl_critical_level_to_pause', TRUE);
			$bounce_critical_level_to_pause=$this->input->post('bounce_critical_level_to_pause', TRUE);
			$max_soft_bounce=$this->input->post('max_soft_bounce', TRUE);
			$ignore_softbounced_for_x_days=$this->input->post('ignore_softbounced_for_x_days', TRUE);
			$admin_notification_email=$this->input->post('admin_notification_email', TRUE);
			$comment_close_days=$this->input->post('comment_close_days', TRUE);
			$send_notification_to_user_after_xx_days=$this->input->post('send_notification_to_user_after_xx_days', TRUE);
			$user_account_inactive_after_xx_days=$this->input->post('user_account_inactive_after_xx_days', TRUE);
			$default_allowed_limit_for_send_email=$this->input->post('default_allowed_limit_for_send_email', TRUE);
			$delete_unconfirmed_users_after_xx_days=$this->input->post('delete_unconfirmed_users_after_xx_days', TRUE);
			$cancel_subscription_after_xx_days=$this->input->post('cancel_subscription_after_xx_days', TRUE);
			$campaign_stat_archive_after_xx_days=$this->input->post('campaign_stat_archive_after_xx_days', TRUE);
			$max_contacts_to_unauthenticate=$this->input->post('max_contacts_to_unauthenticate', TRUE);
			$max_contacts_for_list_growing_alert=$this->input->post('max_contacts_for_list_growing_alert', TRUE);
			$do_not_mail_list=$this->input->post('do_not_mail_list', TRUE);
			$campaign_forward_limit=$this->input->post('campaign_forward_limit', TRUE);
			$unresponsive_ignored=$this->input->post('unresponsive_ignored', TRUE);
			$seedlist=$this->input->post('seedlist', TRUE);
			$per_mail_price_for_credit=$this->input->post('per_mail_price_for_credit', TRUE);
			$max_contact_add_by_credit=$this->input->post('max_contact_add_by_credit', TRUE);
		}else{
			$site_configuration_array=$this->ConfigurationModel->get_site_configuration_data();
			foreach($site_configuration_array as $val){
				${$val['config_name']}=$val['config_value'];
			}
		}
		// Recieve any messages to be shown, when campaign is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, campaign and footer view.
		$this->load->view('webmaster/header',array('title'=>'General Setting','logo_link'=>$logo_link));
		$this->load->view('webmaster/site_general_setting',array('messages' =>$messages,'max_soft_bounce'=>$max_soft_bounce,'ignore_softbounced_for_x_days'=>$ignore_softbounced_for_x_days,'maximum_add_contact'=>$maximum_add_contact,'maximum_payment_declined'=>$maximum_payment_declined,'maximum_delete_contact'=>$maximum_delete_contact,'maximum_bounce_contact'=>$maximum_bounce_contact,'fbl_critical_limit'=>$fbl_critical_limit,'unsubscribe_critical_level_to_alert'=>$unsubscribe_critical_level_to_alert,'unsubscribe_critical_level_to_pause'=>$unsubscribe_critical_level_to_pause,'fbl_critical_level_to_pause'=>$fbl_critical_level_to_pause,'bounce_critical_level_to_pause'=>$bounce_critical_level_to_pause,'admin_notification_email'=>$admin_notification_email,'comment_close_days'=>$comment_close_days,'send_notification_to_user_after_xx_days'=>$send_notification_to_user_after_xx_days,'user_account_inactive_after_xx_days'=>$user_account_inactive_after_xx_days,'cancel_subscription_after_xx_days'=>$cancel_subscription_after_xx_days,'delete_unconfirmed_users_after_xx_days'=>$delete_unconfirmed_users_after_xx_days,'campaign_stat_archive_after_xx_days'=>$campaign_stat_archive_after_xx_days,'max_contacts_to_unauthenticate'=>$max_contacts_to_unauthenticate,'max_contacts_for_list_growing_alert'=>$max_contacts_for_list_growing_alert,'default_allowed_limit_for_send_email'=>$default_allowed_limit_for_send_email,'do_not_mail_list'=>$do_not_mail_list,'unresponsive_ignored'=>$unresponsive_ignored,'campaign_forward_limit'=>$campaign_forward_limit,'seedlist'=>$seedlist,'max_contact_add_by_credit'=>$max_contact_add_by_credit,'per_mail_price_for_credit'=>$per_mail_price_for_credit));
		$this->load->view('webmaster/footer');
	}
	function cron_setting(){
		 
		// To check if form is submitted
		if($this->input->post('action')=='submit'){			
			$input_array=array(
				'continue_campaign_send'=>$this->input->post('continue_campaign_send', TRUE),
				'cronjob_status'=>$this->input->post('cronjob_status', TRUE),
				
				'continue_pmtalog_import'=>$this->input->post('continue_pmtalog_import', TRUE),
				'pmtalog_import_acct_even'=>$this->input->post('pmtalog_import_acct_even', TRUE),
				'pmtalog_import_acct_odd'=>$this->input->post('pmtalog_import_acct_odd', TRUE),
				'pmtalog_import_fbl'=>$this->input->post('pmtalog_import_fbl', TRUE),
				'pmtalog_import_bounced'=>$this->input->post('pmtalog_import_bounced', TRUE),
				
				'continue_autoresponder_send'=>$this->input->post('continue_autoresponder_send', TRUE),
				'continue_singup_form'=>$this->input->post('continue_singup_form', TRUE),
				'continue_singup_form'=>$this->input->post('maintenance_mode', TRUE)
			);
			foreach($input_array as $key=>$val){
				$this->ConfigurationModel->update_site_configuration(array('config_value'=>$val),array('config_name'=>$key));
				//echo"<br/>". $this->db->last_query();
			} 
			
		}
		$site_configuration_array=$this->ConfigurationModel->get_site_configuration_data();
		foreach($site_configuration_array as $val){
			${$val['config_name']}=$val['config_value'];
		}
		
		// Recieve any messages to be shown, when campaign is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, campaign and footer view.
		$this->load->view('webmaster/header',array('title'=>'Cronjob Setting','logo_link'=>$logo_link));
		$this->load->view('webmaster/site_cron_setting',array('messages' =>$messages,
		'continue_campaign_send'=>$continue_campaign_send,
		'cronjob_status'=>$cronjob_status,
		'campaign_cron_status_change_time'=>$campaign_cron_status_change_time,
		
		'continue_pmtalog_import'=>$continue_pmtalog_import,
		'pmtalog_import_acct_odd'=>$pmtalog_import_acct_odd,
		'pmtalog_import_acct_even'=>$pmtalog_import_acct_even,
		'pmtalog_import_acct_odd_change_time'=>$pmtalog_import_acct_odd_change_time,
		'pmtalog_import_acct_even_change_time'=>$pmtalog_import_acct_even_change_time,
		'pmtalog_cron_status_change_time'=>$pmtalog_cron_status_change_time,
		'pmtalog_import_fbl'=>$pmtalog_import_fbl,
		'pmtalog_import_fbl_change_time'=>$pmtalog_import_fbl_change_time,
		'pmtalog_import_bounced'=>$pmtalog_import_bounced,
		'pmtalog_import_bounced_change_time'=>$pmtalog_import_bounced_change_time,
		
		'queueing_cron'=>$queueing_cron,
		'queueing_start_date'=>$queueing_start_date,
		'continue_autoresponder_send'=>$continue_autoresponder_send,
		'continue_singup_form'=>$continue_singup_form,
		'maintenance_mode'=>$maintenance_mode		
		));
		$this->load->view('webmaster/footer');
	}
	function ajaxSaveSetting(){
		$cname = $this->input->post('cname');
		$cval = $this->input->post('cval');
		$sqlUpdate = "Update red_site_configurations set config_value = '$cval' where config_name='$cname'";
		$this->db->query($sqlUpdate);
		echo "Updated: $cname to $cval";
	}
	function ajaxShowQueue(){
		$rsQueue = $this->db->query("Select campaign_id, member_username,member_id, subscription_list, sent_counter, campaign_contacts, tobe_campaign_status from red_email_campaigns t1 inner join red_members t2 on t1.campaign_created_by=t2.member_id where campaign_status='queueing' and t1.is_deleted=0");
		if($rsQueue->num_rows() > 0){
			$strTable ='<table class="tbl_forms"><tr><th>Campaign-ID</th><th>User</th><th>List</th><th>Sent-count</th><th>Total-contacts</th><th>Final Status</th></tr>';
			foreach($rsQueue->result_array() as $rowQueingCampaign){
				$strTable .= '<tr><td>'.$rowQueingCampaign['campaign_id'].'</td><td>'.$rowQueingCampaign['member_username'].'['.$rowQueingCampaign['member_id'].']</td><td>'.$rowQueingCampaign['subscription_list'].'</td><td>'.$rowQueingCampaign['sent_counter'].'</td><td>'.$rowQueingCampaign['campaign_contacts'].'</td><td>'.$rowQueingCampaign['tobe_campaign_status'].'</td></tr>';
			}
			$strTable .= '</table>';
		}else{
			$strTable .= 'No Queue....';
		}
		$rsQueue->free_result();	
		echo $strTable;
	}
	function email_personalize(){
		
		// To check if form is submitted
		if($this->input->post('action')=='submit')
		{
			# Validation rules are applied			
			$this->form_validation->set_rules('subscriber_first_name', 'First Name', 'required');
			$this->form_validation->set_rules('subscriber_last_name', 'Last Name', 'required');
			$this->form_validation->set_rules('subscriber_email_address', 'Email Address', 'required');
			$this->form_validation->set_rules('subscriber_state', 'State', 'required');
			$this->form_validation->set_rules('subscriber_zip_code', 'Zip Code', 'required');
			$this->form_validation->set_rules('subscriber_country', 'Country', 'required');
			$this->form_validation->set_rules('subscriber_city', 'City', 'required');
			$this->form_validation->set_rules('subscriber_company', 'Company', 'required');
			$this->form_validation->set_rules('subscriber_dob', 'DOB', 'required');
			$this->form_validation->set_rules('subscriber_phone', 'Phone', 'required');
			$this->form_validation->set_rules('subscriber_address', 'Address', 'required');
			
			// To check form is validated
			if($this->form_validation->run()==true)
			{
				$input_array=array(
					'subscriber_first_name'=>$this->input->post('subscriber_first_name', TRUE),
					'subscriber_last_name'=>$this->input->post('subscriber_last_name', TRUE),
					'subscriber_email_address'=>$this->input->post('subscriber_email_address', TRUE),
					'subscriber_state'=>$this->input->post('subscriber_state', TRUE),
					'subscriber_zip_code'=>$this->input->post('subscriber_zip_code', TRUE),
					'subscriber_country'=>$this->input->post('subscriber_country', TRUE),
					'subscriber_city'=>$this->input->post('subscriber_city', TRUE),
					'subscriber_company'=>$this->input->post('subscriber_company', TRUE),
					'subscriber_dob'=>$this->input->post('subscriber_dob', TRUE),
					'subscriber_phone'=>$this->input->post('subscriber_phone', TRUE),
					'subscriber_address'=>$this->input->post('subscriber_address', TRUE),
				);
				foreach($input_array as $key=>$val){
					$this->ConfigurationModel->update_email_personalize(array('value'=>$val),array('name'=>$key));
				}
				$default_value_arr=array(
					'subscriber_first_name'=>$this->input->post('subscriber_first_name_default_value', TRUE),
					'subscriber_last_name'=>$this->input->post('subscriber_last_name_default_value', TRUE),
					'subscriber_email_address'=>$this->input->post('subscriber_email_address_default_value', TRUE),
					'subscriber_state'=>$this->input->post('subscriber_state_default_value', TRUE),
					'subscriber_zip_code'=>$this->input->post('subscriber_zip_code_default_value', TRUE),
					'subscriber_country'=>$this->input->post('subscriber_country_default_value', TRUE),
					'subscriber_city'=>$this->input->post('subscriber_city_default_value', TRUE),
					'subscriber_company'=>$this->input->post('subscriber_company_default_value', TRUE),
					'subscriber_dob'=>$this->input->post('subscriber_dob_default_value', TRUE),
					'subscriber_phone'=>$this->input->post('subscriber_phone_default_value', TRUE),
					'subscriber_address'=>$this->input->post('subscriber_address_default_value', TRUE),
				);
				foreach($default_value_arr as $key=>$val){
					$this->ConfigurationModel->update_email_personalize(array('default_value'=>$val),array('name'=>$key));
				}
			}
			$subscriber_first_name		=	$this->input->post('subscriber_first_name', TRUE);
			$subscriber_last_name		=	$this->input->post('subscriber_last_name', TRUE);
			$subscriber_email_address	=	$this->input->post('subscriber_email_address', TRUE);
			$subscriber_state			=	$this->input->post('subscriber_state', TRUE);
			$subscriber_zip_code		=	$this->input->post('subscriber_zip_code', TRUE);
			$subscriber_country			=	$this->input->post('subscriber_country', TRUE);
			$subscriber_city			=	$this->input->post('subscriber_city', TRUE);
			$subscriber_company			=	$this->input->post('subscriber_company', TRUE);
			$subscriber_dob				=	$this->input->post('subscriber_dob', TRUE);
			$subscriber_phone			=	$this->input->post('subscriber_phone', TRUE);
			$subscriber_address			=	$this->input->post('subscriber_address', TRUE);
		}else{
			$email_configuration_array=$this->ConfigurationModel->get_email_personalize_data();
			$default_value_arr=array();
			foreach($email_configuration_array as $val){
				${$val['name']}=$val['value'];
				$default_value_arr[$val['name']]=$val['default_value'];
			}
		}
		// Recieve any messages to be shown, when campaign is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, campaign and footer view.
		$this->load->view('webmaster/header',array('title'=>'General Setting','logo_link'=>$logo_link));
		$this->load->view('webmaster/email_personalize',array('messages' =>$messages,'subscriber_first_name'=>$subscriber_first_name,'subscriber_last_name'=>$subscriber_last_name,'subscriber_email_address'=>$subscriber_email_address,'subscriber_state'=>$subscriber_state,'subscriber_zip_code'=>$subscriber_zip_code,'subscriber_country'=>$subscriber_country,'subscriber_city'=>$subscriber_city,'subscriber_company'=>$subscriber_company,'subscriber_dob'=>$subscriber_dob,'subscriber_phone'=>$subscriber_phone,'subscriber_address'=>$subscriber_address,'default_value_arr'=>$default_value_arr));
		$this->load->view('webmaster/footer');
	}
		
	function showRunningPS(){	
		echo "<b>Time now: </b>". date("l M d, Y, h:i A", time()) ."<br/><br/>";
		echo "Each cronjob has two entity recorded here:<br/>";
		$cmd = 'ps auxwww|grep "/var/www/redcappi/docroot/index.php"|grep -v grep';
		exec($cmd,$op);
		$i = 1;
		foreach( $op as $p){	
			echo "<br/>".$i++ .". ";	
			if(strstr($p, 'campaign_thread') !== FALSE)
				echo " Campaign Thread";
			elseif(strstr($p, 'sendDNM') !== FALSE)
				echo " Campaign Preprocessing";
			elseif(strstr($p, 'send') !== FALSE)
				echo " Campaign Sending";
			elseif(strstr($p, 'acct_odd') !== FALSE)
				echo " PMTA: delivery log import";
			elseif(strstr($p, 'acct_even') !== FALSE)
				echo " PMTA: delivery log import";
			elseif(strstr($p, 'addToQueueCron') !== FALSE)
				echo " Queueing";
			elseif(strstr($p, 'resegment_new') !== FALSE)
				echo " Segmentation";
			elseif(strstr($p, 'bounced') !== FALSE)
				echo " PMTA: Bounced log import";
			elseif(strstr($p, 'fbl') !== FALSE)
				echo " PMTA: Complaint log import";
			elseif(strstr($p, 'payment_cronjob') !== FALSE)
				echo " Payment cronjob";
			elseif(strstr($p, 'removeOldLog') !== FALSE)
				echo " PMTA-log removal from DB";
			elseif(strstr($p, 'removeOldLogFiles') !== FALSE)
				echo " PMTA-log file removal";
			elseif(strstr($p, 'send_autoresponder') !== FALSE)
				echo " Autoresponder send";
			elseif(strstr($p, 'contact_analysis') !== FALSE)
				echo " Contact analysis";
			elseif(strstr($p, 'campaign_email_track_restorage') !== FALSE)
				echo " Freezing";				 				
		}
			
	}
}
?>