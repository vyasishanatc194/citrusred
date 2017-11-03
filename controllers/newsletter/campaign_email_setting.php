<?php
/**
* A Campaign_email_setting class
*
* This class is for campaign email setting
*
* @version 1.0
* @author Pravin Jha <pravinjha@gmail.com>
* @project Redcappi
*/
class Campaign_email_setting extends CI_Controller
{
	function __construct(){
		parent::__construct();		
		// if memeber is not login then redirect to login page
		if($this->session->userdata('member_id')=='')
			redirect('user/index');
		$this->load->helper('transactional_notification');
		$this->load->helper('notification');
		$this->load->helper('admin_notification');
		// Load upload library for file uploading		
		$this->load->model('UserModel');
		$this->load->model('newsletter/Campaign_Model');
		$this->load->model('newsletter/contact_model');
		$this->load->model('ConfigurationModel');
		$this->load->model('newsletter/Subscription_Model');	
		$this->load->model('newsletter/Campaign_Autoresponder_Model');
		$this->load->model('newsletter/Autoresponder_Model');		
		$this->load->model('Activity_Model');
                                  $this->load->model('newsletter/Emailreport_Model');		
		$this->load->model('newsletter/Page_Model');
		$this->load->helper('transactional_notification');
		$this->load->model('newsletter/Autoresponder_Model');
		$this->load->helper('notification');	
                                  $this->load->helper('phpmailer');	

		#$this->load->model('newsletter/Emailreport_Model');
		#$this->load->model('newsletter/Subscriber_Model');
		$this->output->enable_profiler(false);
		
		force_ssl();	
	}
	/**
	*	Function index 
	*	for campaign email setting
	*
	*	@param integer $campaign_id  campaign id
	**/
	function index($campaign_id=0){
		#set execution time
		set_time_limit(0);			  
				
		# Fetch email template information
		$campaign_info=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$campaign_id,'campaign_created_by'=>$this->session->userdata('member_id')));		 
		/**
		*	Check campaign already send or new to send,	If already send then display message: 			#
		*	This campaign is already sent		 														#
		*/
		if($campaign_info[0]['campaign_status']=="active"){
			# Assign success message by message class			
			$this->messages->add('This campaign is already sent', 'success');
			#redirect to campaign detail page
			redirect('newsletter/campaign');
		}
		
		// Check  Maximum Contacts according to user selected package id			
		if($this->check_user_selected_package()){
			if($this->input->post('action')=='send_campaign'){
				#collect subscriptions id
				$campaign_data['subscription_ids_str']=$this->input->post('subscription_ids_str');
				######################################
				# Set schedule date time of campaign #
				######################################
				$scheduled_datetime=$this->input->post('scheduled_date');				
				$scheduled_date_arr=explode('/',$scheduled_datetime);
			 	$scheduled_date=$scheduled_date_arr[2].'-'.$scheduled_date_arr[0].'-'.$scheduled_date_arr[1];
				$hr = $this->input->post('sch_hours');
				$min = $this->input->post('sch_min');
				 
				if($this->input->post('sch_time') == 'pm')
				$hr = ($this->input->post('sch_hours') < 12)? $this->input->post('sch_hours') + 12 :  12;
				elseif($this->input->post('sch_time') == 'am')
				$hr =  ($this->input->post('sch_hours') < 12)? $this->input->post('sch_hours') :  0;
				
				if($hr == 24)$hr= 0;
				if($hr < 10)$hr= '0'.$hr;
				if($min < 10)$min= '0'.$min; 
				
				if($this->input->post('send_now')==1){
				$scheduled_datetime = date('Y-m-d H:i:s',now()); 
				$is_send_now = 'SEND NOW:'.$scheduled_datetime;
				}else{
				$scheduled_datetime = date('Y-m-d H:i:s',local_to_gmt(mktime($hr,$min,0, $scheduled_date_arr[0], $scheduled_date_arr[1], $scheduled_date_arr[2]))); 				
				$is_send_now = 'SCHEDULE IT:'.$scheduled_datetime;
				}
				$queued_datetime= date('Y-m-d H:i:s',local_to_gmt(time()));  
				
				// if any campaign is scheduled before time now, it should be sent/scheduled as now.
				IF(strtotime($scheduled_datetime) < strtotime($queued_datetime) ) $scheduled_datetime = $queued_datetime;
				# if user want save the email setting
				if($this->input->post('save_email')==1){ 
					$this->save_campagin_email_setting($campaign_id,$schedule_datetime_arr['scheduled_datetime']);
				}else if($this->input->post('save_email')!=1){ // If user want to schedule email					
					$this->form_validation->set_rules('email_subject', 'Email Subject', 'required');			
					$this->form_validation->set_rules('email_id', 'From Email', 'required|valid_email');
					$this->form_validation->set_rules('email_from', 'From Name', 'required');
					$this->form_validation->set_rules('subscriptions[]', 'Select List', 'required');
					if($this->input->post('send_now')!=1){
						$this->form_validation->set_rules('scheduled_date', 'Scheduled Date', 'required|callback_validate_scheduled_date');
					}
					# To check form is validated
					if($this->form_validation->run()==true){
						//$email_subject = mb_convert_encoding($this->input->post('email_subject'), 'HTML-ENTITIES', 'UTF-8');
						$email_subject = $this->input->post('email_subject');
						############################################################
						#	Fetch Login user authentication for sheduleing email   #
						############################################################
						$user=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));
						$user_is_authentic=$user[0]['is_authentic'];
						$clicktracking_status=$user[0]['clicktracking_status'];
						$is_automatic_segmentation=$user[0]['is_automatic_segmentation'];
						$segment_size=$user[0]['segment_size'];
						$campaign_priority=$user[0]['campaign_priority'];
						//$user_is_ga_enabled=$user[0]['google_analytics_status'];
						############################################################
						#	Fetch default limit for schedule a email of every user #
						############################################################
						# Load the configuration model which interact with database
						
						$site_configuration_array=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'default_allowed_limit_for_send_email'));
						$default_allowed_limit_for_send_email=$site_configuration_array[0]['config_value'];
						# checked subscription lsit have contacts or not
						$number_of_contacts=$this->selected_subscribers(false);
						
						if($number_of_contacts==0){
							# Assign success message by message class
							$this->messages->add('Please add contacts in checked Contact List', 'error');
						}else{
							#####################
							#	 schedule email #
							#####################
							#Recieve subscription and campaign posted by user					
							$subscriptions=$this->input->post('subscriptions');
							$subscription_ids_str=implode(',',$subscriptions);
							$input_array=array(	'campaign_id'=>$campaign_id,'campaign_scheduled_date'=>$scheduled_datetime);

							#Store scheduled campaign in database							
							if($campaign_info[0]['campaign_status']=="archived"){
								$this->Campaign_Model->update_scheduled_campaign($input_array,array('campaign_id'=>$campaign_info[0]['campaign_id']));
							}else{
								$this->Campaign_Model->create_scheduled_campaign($input_array);
							}
							
							 
							
							#Fetch subscriber list
							#$subscriber_array=$this->Subscriber_Model->get_distinct_email(array('subscriber_status'=>1,'res.is_deleted'=>0,'res.subscriber_created_by'=>$this->session->userdata('member_id')),$subscriptions);
							$subscriber_count=$this->contact_model->get_contacts_count_in_selected_lists(array('subscriber_status'=>1,'res.is_deleted'=>0,'res.subscriber_created_by'=>$this->session->userdata('member_id')),$subscriptions);
							$memberid = $this->session->userdata('member_id');
							$list_ids_str=implode('_',$subscriptions);
							#$subscriber_count=count($subscriber_array);	#number of subsribers
							if(($subscriber_count <= $default_allowed_limit_for_send_email)||($user_is_authentic==1)){
								$campaign_status="archived";
							}else{
								$campaign_status=($this->input->post('send_now')==1)? "ready" : "active_ready";
							}
							//if($this->input->post('is_clicktracking') !== false)
							if($clicktracking_status == 1){
								$is_clicktracking = (isset($_POST['is_clicktracking'])) ?  1 : 0 ;
							}else{
								$is_clicktracking = 1;
							}
							
							// Update campaign status to archived
							$campaign_data_update = array('campaign_sheduled'=>$scheduled_datetime,'campaign_queued'=>$queued_datetime,'email_subject'=>$email_subject,'sender_email'=>$this->input->post('email_id'),'sender_name'=>$this->input->post('email_from'),'subscription_list'=>$subscription_ids_str,'email_send_date'=>$scheduled_datetime,'is_status'=>'0','campaign_priority'=>$campaign_priority, 'is_ga_enabled'=>$this->input->post('is_ga_enabled'),'is_clicktracking'=>$is_clicktracking);
							if( '' != $this->input->post('reply_to_email') )$campaign_data_update['reply_to_email'] = trim($this->input->post('reply_to_email'));
							
							// Start adding ab testing code by cb
							if ($this->input->post('is_abtesting')) {
								$campaign_data_update['is_ab'] = $this->input->post('is_abtesting');
								if($this->input->post('ab_test_campaign') != '' && $this->input->post('ab_test_campaign') > 0 ){
									$getAbtestingCamapign = $this->Campaign_Model->get_abtesting(array('ref_campaign_id'=> $campaign_id,'is_delete'=>'0','member_id'=>$this->session->userdata('member_id')));
									if(count($getAbtestingCamapign) > 0){
										if($getAbtestingCamapign[0]['campaign_id'] != $this->input->post('ab_test_campaign')){
											$this->Campaign_Model->update_abtesting(array('is_delete'=>'1'),array('campaign_ab_id'=> $getAbtestingCamapign[0]['campaign_ab_id']));
											$this->Campaign_Model->update_abtesting(array('ref_campaign_id' => $this->input->post('ab_test_campaign'),'member_id'=>$this->session->userdata('member_id')),array('campaign_id'=> $campaign_id));
											$this->Campaign_Model->add_abtesting(array('campaign_id'=>$this->input->post('ab_test_campaign'), 'ref_campaign_id'=>  $campaign_id,'member_id'=>$this->session->userdata('member_id')));
											$this->Campaign_Model->update_campaign(array('is_ab'=>'0'),array('campaign_id'=>$getAbtestingCamapign[0]['campaign_id']));
											$this->Campaign_Model->update_campaign(array('is_ab'=>'1'),array('campaign_id'=>$this->input->post('ab_test_campaign')));
										}
									}else{
											$getAbtestingCamapign = $this->Campaign_Model->get_abtesting(array('ref_campaign_id'=> $campaign_id,'is_delete'=>'0','member_id'=>$this->session->userdata('member_id')));
											$this->Campaign_Model->update_abtesting(array('is_delete'=>'1'),array('campaign_id'=>$campaign_id));
											$this->Campaign_Model->update_abtesting(array('is_delete'=>'1'),array('ref_campaign_id'=>$campaign_id));
											$this->Campaign_Model->update_campaign(array('is_ab'=>'0'),array('campaign_id'=>$getAbtestingCamapign[0]['campaign_id']));
									}
								}
							}else{
								$this->Campaign_Model->update_abtesting(array('is_delete'=>'1'),array('campaign_id'=>$campaign_id));
								$this->Campaign_Model->update_abtesting(array('is_delete'=>'1'),array('ref_campaign_id'=>$campaign_id));
								$getAbtestingCamapign = $this->Campaign_Model->get_abtesting(array('ref_campaign_id'=> $campaign_id,'is_delete'=>'0','member_id'=>$this->session->userdata('member_id')));
								$this->Campaign_Model->update_campaign(array('is_ab'=>'0'),array('campaign_id'=>$getAbtestingCamapign[0]['campaign_id']));
							}
							
							// End adding ab testing code by cb
							$this->Campaign_Model->update_campaign($campaign_data_update,array('campaign_id'=>$campaign_id));
							//$Temp_update_query = 'Debug - Temp_update_query :'.$is_send_now . '---'.$campaign_status. '-----'.$this->db->last_query();
							//admin_notification_send_email('tech@redcappi.com', SYSTEM_EMAIL_FROM,"RedCappi", 'Debug - Temp_update_query',$Temp_update_query,$Temp_update_query);
							// Add segmentation
							if($is_automatic_segmentation > 0 && $segment_size > 0 && $campaign_status != "archived"){
								$this->db->query("insert into `red_ongoing_segmentation` set campaign_id='$campaign_id', segment_size='$segment_size', segment_interval='30' ON DUPLICATE KEY UPDATE `segment_size`='$segment_size', `segment_interval`='30' ");
								$this->Campaign_Model->update_campaign(array('is_segmentation'=>'1','number_of_contacts'=>$segment_size, 'segment_interval'=>'30'),array('campaign_id'=>$campaign_id));
							}
							
							$this->create_activity_log($campaign_id);
							$queue_log = config_item('campaign_files').'queue_log_'.date('Ymdhis');
							
							// NEW section to move queueing via cronjob: Starts
							$this->Campaign_Model->update_campaign(array('campaign_status'=>'queueing', 'sent_counter'=>0, 'campaign_contacts'=>$subscriber_count, 'tobe_campaign_status'=>$campaign_status),array('campaign_id'=>$campaign_id));
							// NEW section to move queueing via cronjob: ENDS
								
							// Since queueing is done using cronjob, following are not in use
							$command = config_item('php_path')." ".FCFOLDER."/index.php  newsletter/cronjob addToQueue $campaign_id $memberid $list_ids_str $campaign_status"	;
						 										
							if($campaign_info[0]['is_responsive'] != 1){
									
								if(($campaign_info[0]['campaign_template_option']!=3)&&($campaign_info[0]['campaign_template_option']!=5)){
								$page_html=html_entity_decode($campaign_info[0]['campaign_content'], ENT_QUOTES, "utf-8" ); 
								}else{
								$page_html=$campaign_info[0]['campaign_content'];
								}
							}else{
								$page_html=$campaign_info[0]['campaign_html'];
							}	
							
							if($page_html != '')
								$this->Campaign_Autoresponder_Model->encode_url($campaign_id,$page_html);	
								
							$affected_member_package_id=$this->UserModel->update_member_package(array('is_first_campaign_send'=>'1'),array('member_id'=>$this->session->userdata('member_id')));				
											
							if($subscriber_count<=$default_allowed_limit_for_send_email){							 
								$user_name=$user[0]['member_username'];
								# send notification email to admin
								$this->notification_subscribers_count($campaign_id,$subscriber_count,$user_name);
								//redirect('newsletter/campaign/check_campaign_status/'.$campaign_info[0]['campaign_id']);
								//exit;
							}else{
								//$affected_member_package_id=$this->UserModel->update_member_package(array('is_first_campaign_send'=>'1'),array('member_id'=>$this->session->userdata('member_id')));
								#########################################################
								#Check user is authentic or not :						#
								#If authentic then send  notfication to admin about		#
								#Toatal number of subscribers count						#
								#If not authentic then send  notfication to admin for	#
								#allow or disallow user campaign						#
								######################################################
								if($user_is_authentic==1){
									$user_name=$user[0]['member_username'];
									# send notification email to admin
									$this->notification_subscribers_count($campaign_id,$subscriber_count,$user_name);
								}else{
									$user_name=$user[0]['member_username'];
									# send notification email to admin
									$this->notification_email($campaign_id,$subscriber_count,$user_name);
								}
							}
							 
							if($affected_member_package_id > 0){
								# Redirect to first time send campaign notification
								redirect('newsletter/campaign_email_setting/first_time_user_notification');
								exit;
							}else{
								# Redirect to listing of campaigns
								redirect('newsletter/campaign/check_campaign_status/'.$campaign_id);
								exit;
							}
						}
						
					}
				}
			}
		}else{
			redirect('upgrade_package_cim/index');
			exit;
		}
		
		##LOAD GLOBAL FIELDS
                
//        $global = $this->subscriber_Model->get_global_field_segment('member_id = '.$this->session->userdata('member_id'));

		# Load subscriptions created by user
		$fetch_conditions_array=array('subscription_created_by'=>$this->session->userdata('member_id'),	'is_deleted'=>0,'subscription_status'=>1);	
		#Fetch Subscription list created by user
		$subscriptions_count=$this->Subscription_Model->get_subscription_count($fetch_conditions_array);
		$subscriptions=$this->Subscription_Model->get_subscription_data($fetch_conditions_array,$subscriptions_count);
		$i=0;
		foreach($subscriptions as $subscription){
			$subscription_id= $subscription['subscription_id'];
			$number_of_contacts=$this->selected_subscribers(false,$subscription_id);
			$subscriptions[$i]['number_of_contacts']=$number_of_contacts;
			$i++;
		}
		$subscription_data=array('subscriptions'=>$subscriptions);
		$campaign_data['email_id']	= $this->getFromEmlArray();
		$campaign_data['last_campaign_from_email']	= $this->getLastCampaignFromEmail();
		
		#Fetch Login user info for displaying on campaign footer
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));
		$user_info=true;
		$user_info=(!$user_data_array[0]['company'])?false :  true;
		$user_info=(!$user_data_array[0]['address_line_1'])?false :  true;
		$user_info=(!$user_data_array[0]['city'])?false :  true;
		$user_info=(!$user_data_array[0]['state'])?false :  true;
		$user_info=(!$user_data_array[0]['zipcode'])?false :  true;
		$user_info=(!$user_data_array[0]['country_name'])?false :  true;
		
		$campaign_data['user_info']=$user_info;
		$campaign_data['user_data']=$user_data_array[0];
		$campaign_data['email_from']=$user_data_array[0]['company'];
		
		//Fetch Country name
		$country_info=$this->UserModel->get_country_data();
		$campaign_data['country_info']=$country_info;

		$campaign_data['is_ga_enabled']=$user_data_array[0]['google_analytics_status'];
		$campaign_data['is_clicktracking']=$user_data_array[0]['clicktracking_status'];
		$campaign_data['reply_to_enabled']=$user_data_array[0]['reply_to_enabled'];
		// Collect email template information
		$email_template_info=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$campaign_id,'campaign_created_by'=>$this->session->userdata('member_id')));
		if(($email_template_info[0]['campaign_status']=='active')||(!count($email_template_info))){
			redirect('newsletter/campaign');
			exit;
		}
		$campaign_data['campaign_id']=$campaign_id;
		$campaign_data['campaign_template_option']=$campaign_info[0]['campaign_template_option'];
		$campaign_data['camapign']=$email_template_info[0];
		$subscription_array=array();
		$subscription_array=explode(",",$email_template_info[0]['subscription_list']);
		$campaign_data['camapign']['subscription_list']=$subscription_array;
		if($email_template_info[0]['email_send_date'] !== null){
			//$email_send_date= date("m/d/Y g:i:a",strtotime($email_template_info[0]['email_send_date']));
			$email_send_date= date("m/d/Y g:i:a",strtotime(getGMTToLocalTime($email_template_info[0]['email_send_date'],$this->session->userdata('member_time_zone')) ));
	 
		
			$date_arr=explode(" ",$email_send_date);
			$campaign_data['camapign']['send_time']=explode(":",$date_arr[1]);
			$campaign_data['camapign']['delivery_date']=$date_arr[0];
		}else{
			$email_send_date=date("m/d/Y g:i:a");
			$date_arr=explode(" ",$email_send_date);
			$campaign_data['camapign']['send_time']=explode(":",$date_arr[1]);
			$campaign_data['camapign']['delivery_date']=$date_arr[0];
		}
		$campaign_data['camapign']['test_email_count']=$campaign_info[0]['test_email'];
		// Start gettng campaign id for ab testing by cb
		if ($campaign_data['camapign']['is_ab'] == 1) {
			$getAbtesting = $this->Campaign_Model->get_abtesting(array('campaign_id' => $campaign_id),array('ref_campaign_id' => $campaign_id ));
			foreach($getAbtesting as $ab){
				if($ab['is_delete'] == '0'){
					$campaign_data['camapign']['campaign_abtesting'] = ($campaign_id == $ab['ref_campaign_id']) ? $ab['campaign_id'] : $ab['ref_campaign_id'];
				}
			}
			
		}
		$get_package = $this->UserModel->get_user_package(array('member_id'=>$this->session->userdata('member_id')));
		$abTestedCampaignList = $this->Campaign_Model->get_abtesting(array('member_id'=>$this->session->userdata('member_id'),'is_delete'=>'0'));
		$abcampaign_list = array();
		if(count($abTestedCampaignList)){
			foreach($abTestedCampaignList as $ablist){
				$abcampaign_list[] = $ablist['campaign_id'];
				$abcampaign_list[] = $ablist['ref_campaign_id'];
			}
		}
		$abcampaign_list = array_unique($abcampaign_list); 
		$fetch_condiotions_array = array('campaign_created_by' => $this->session->userdata('member_id'), 'rec.is_deleted' => 0, 'is_status' => 0);
		$campaigns_lists = $this->Campaign_Model->get_shchedule_campaign_id($fetch_condiotions_array, $campaign_id);
		
		foreach($campaigns_lists as $campaignlist){
			if(!in_array($campaignlist['campaign_id'],$abcampaign_list) || $campaign_data['camapign']['campaign_abtesting'] == $campaignlist['campaign_id'] ){
				$campaign_data['campaigns_list'][] = $campaignlist;
			}
		}
		//r($campaign_data['campaigns_list']);exit;
		//$campaign_data['campaigns_list'] = $this->Campaign_Model->get_shchedule_campaign_id($fetch_condiotions_array, $campaign_id);
		
		// End gettng campaign id for ab testing by cb
		// Getting subscriber count for list Camapaign Which are queue 
		$fetch_condiotions_array = array('campaign_created_by' => $this->session->userdata('member_id'), 'rec.is_deleted' => 0, 'is_status' => 0,'campaign_status'=>'queueing');
		$campaigns_queue_lists = $this->Campaign_Model->get_shchedule_campaign_id($fetch_condiotions_array, $campaign_id);
		//echo $this->db->last_query();
		$totalcontacts = 0;
		foreach($campaigns_queue_lists as $camapaignQueue){
			if($camapaignQueue['subscription_list'] != ''){
				$subscription_list = explode(',',$camapaignQueue['subscription_list']);
				for($j = 0;$j < count($subscription_list);$j++){
					$totalcontacts += $this->selected_subscribers(false,$subscription_list[$j]);
				}
			}
		}
		$number = $this->UserModel->getRemainingCampaignSendingQuota($this->session->userdata('member_id'));//-$totalcontacts;
		//$number = $get_package[0]['max_campaign_quota'] - $get_package[0]['campaign_sent_counter']-$totalcontacts;
		$quota_remaining =  ($number > 0)? $number : '0';
		# Recieve any messages to be shown, when campaign is added or updated
		$messages=$this->messages->get();
		#Get shoreten url 
		$shorten_url=get_shorten_url();
		if($get_package[0]['package_recurring_interval'] == 'credit'){ 
			$remaining = $this->UserModel->getMemberEmailSendCount($this->session->userdata('member_id'));
			$remaining = $remaining[0]['max_email'] - $remaining[0]['used_email'];
			$mode = 'credit';
		}else{
			$remaining = '';
			$mode = '';
		}
		
		
		#Loads header, campaign and footer view.
		$this->load->view('header',array('title'=>'Send Campaigns'));
		$this->load->view('newsletter/campaign_email_setting',array('campaign_data'=>$campaign_data,'subscription_data'=>$subscription_data,'messages'=>$messages,'shorten_url'=>$shorten_url,'quota_remaining'=>$quota_remaining,'mode'=>$mode,'remaining'=>$remaining));
		$this->load->view('footer');
	}
	
	/**
	*	Function called via AJAX to get List of From Emls
	*/
	function getLastCampaignFromEmail(){
	
		return $this->db->query("select sender_email from red_email_campaigns where campaign_created_by='".$this->session->userdata('member_id')."' and campaign_status='active' order by email_send_date desc limit 1")->row()->sender_email;
	
	}
	/**
	*	Function called via AJAX to get List of From Emls
	*/
	function ajaxFromEmlArray(){
		$arrEmails = $this->getFromEmlArray();
		
		echo implode(',',$arrEmails);
	}
	function getFromEmlArray(){
		$arrFromEmls = array($this->session->userdata('member_email_address'));
		$rsOtherEmailAddresses = $this->db->query("select `email_address` from `red_member_from_email` where `member_id` = '".$this->session->userdata('member_id')."' and `is_verified`=1");
		if($rsOtherEmailAddresses->num_rows() > 0){
			foreach($rsOtherEmailAddresses->result_array() as $otherEml){	
				$arrFromEmls[]	= trim($otherEml['email_address']);			
			}
		}
		$rsOtherEmailAddresses->free_result();	
		return $arrFromEmls;
	}
	/**
	*	Function called via AJAX to add from email address
	*/
	function add_another_emailid(){
		$strNewEml  = trim($this->input->post('newEml'));
		$strNewEmlDomain = substr(strrchr($strNewEml, "@"), 1);
		if(in_array($strNewEmlDomain, config_item('major_domains'))){	
			die('InvalidDomain');
		}
		if(!$this->is_authorized->ValidateAddress($strNewEml)){
			die('err');
		}else{
			$mid = $this->session->userdata('member_id');
			$strUniqueString = sha1(time());
			$rsNewEmail = $this->db->query("select * from `red_member_from_email` where member_id='$mid' and email_address='$strNewEml'");
			//echo $this->db->last_query();
			if($rsNewEmail->num_rows() > 0){
				echo 'dup';
			}elseif($this->UserModel->is_temp_mail($strNewEml)) {
				echo 'temp';
			}else{
				$this->db->query("insert into `red_member_from_email` set member_id='$mid',email_address='$strNewEml',unique_string='$strUniqueString' ON DUPLICATE KEY UPDATE unique_string='$strUniqueString'");
				create_transactional_notification('verify_other_email',array($strUniqueString,$strNewEml, $this->session->userdata('member_username')));
                                                                    $adminto = "support@redcappi.com";
                                                                    $adminsbjct = "Verify new From e-mail Address in Verificaton --> From Email";
                                                                    admin_notification_send_email($adminto, SYSTEM_EMAIL_FROM,'Redcappi App', $adminsbjct, 'Please verify the from email address in the admin panel', 'Please verify the from email address in the admn panel');
				echo 'ok';
			}
			$rsNewEmail->free_result();
		}
		exit;
	}
	
	/**
	*	Function autoresponder to set email setting for autoresponder
	*	@param int autoresponder_id contain autoresponder id
	*/
	function autoresponder($autoresponder_id=0){		 
		$fetch_condiotions_array=array('campaign_created_by'=>$this->session->userdata('member_id'), 'campaign_id'=>$autoresponder_id, 'is_deleted'=>0);
		# Fetches campaign data from database
		$autoresponder_info=$this->Autoresponder_Model->get_autoresponder_data($fetch_condiotions_array);
		# To check form is submittted and action is send
		if($this->input->post('action')=='send_autoresponder'){
			$autoresponder_data['subscription_ids_str']=$this->input->post('subscription_ids_str');
			if($this->input->post('save_email')==1){
				$this->save_autoresponder_email_setting($autoresponder_id);				
			}else{
				# Validation rules are applied
				$this->form_validation->set_rules('email_subject', 'Email Subject', 'required');
				$this->form_validation->set_rules('email_id', 'From Email', 'required');
				$this->form_validation->set_rules('email_from', 'From Name', 'required');		
				$this->form_validation->set_rules('autoresponder_schedule_interval', 'Number Of Days', 'required|is_natural|trim');
			}
			# To check form is validated
			if($this->form_validation->run()==true){
				// schedule email
				// Recieve subscription and campaign posted by user
				$subscription_ids_str=$this->input->post('subscription_ids_str');
				//$email_subject = mb_convert_encoding($this->input->post('email_subject'), 'HTML-ENTITIES', 'UTF-8');
				$email_subject = $this->input->post('email_subject');
				// Create input array to send to database
				$input_array=array('autoresponder_id'=>$autoresponder_id, 'subscription_ids'=>$subscription_ids_str, 'autoresponder_scheduled_status'=>'1',
					'autoresponder_scheduled_interval'=>$this->input->post('autoresponder_schedule_interval')
				);	
				$user_packages_array=$this->UserModel->get_user_packages(array('member_id'=>$this->session->userdata('member_id'),'is_deleted'=>0));
				//$input_array['is_verified']	=  ($user_packages_array[0]['package_id'] > 0 )? 1: 0; //paid user	
                                                                     $input_array['is_verified']	=  1; //all users verified
				
				if($autoresponder_info[0]['autoresponder_scheduled_id']>0){
					$input_array['autoresponder_scheduled_id']=$autoresponder_info[0]['autoresponder_scheduled_id'];
				}
				// Store scheduled autoresponder in database
				$autoresponder_scheduled_id=$this->Autoresponder_Model->create_scheduled_autoresponder($input_array);
				// admin alert ends
				$email_msg ="<p>Hello admin,</p>";
				$email_msg.="<p>Verify Autoresponder : created by <b>".$this->session->userdata('member_username')."</b></p>";				
				$email_msg.='<p>Regards,</p>';
				$email_msg.='<p>Redcappi Team</p>';
				
				$to=$this->get_Admin_notification_email();
				$subject="Verify Autoresponder by ".$this->session->userdata('member_username');
				admin_notification_send_email($to, SYSTEM_EMAIL_FROM,"RedCappi", $subject,$email_msg,$email_msg);
				
				
				$schedule_date=date("Y-m-d H:i:s");				
				$this->Autoresponder_Model->update_autoresponder(array('campaign_status'=>'1','autoresponder_scheduled_interval'=>$this->input->post('autoresponder_schedule_interval'),'autoresponder_scheduled_id'=>$autoresponder_scheduled_id,'campaign_sheduled'=>date('Y-m-d H:i:s',now()),'email_subject'=>$email_subject,'sender_email'=>$this->input->post('email_id'),'sender_name'=>$this->input->post('email_from'),'is_ga_enabled'=>$this->input->post('is_ga_enabled'),'is_clicktracking'=>$this->input->post('is_clicktracking')),array('campaign_id'=>$autoresponder_id));
				// Fetch Login user info for displaying on campaign footer
				$user=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));
								 
				if(($autoresponder_info[0]['campaign_template_option']!=3)&&($autoresponder_info[0]['campaign_template_option']!=5)){
					$page_html=html_entity_decode($autoresponder_info[0]['campaign_content'], ENT_QUOTES, "utf-8" ); 
				}else{
					$page_html=$autoresponder_info[0]['campaign_content'];
				}
				$this->Campaign_Autoresponder_Model->encode_url($autoresponder_id,$page_html,true);	
				//#############################
				//# create activity log		#
				//#############################
				
				// create array to insert values in activty table
				$values=array('user_id'=>$this->session->userdata('member_id'), 'activity'=>'autoresponder_schedule',  'campaign_id'=>$autoresponder_id);
				$this->Activity_Model->create_activity($values);
				// Assign success message by message class
				$this->messages->add('Autoresponder Scheduled Successfully', 'success');
					
				// Redirect to listing of autoresponders
				redirect('newsletter/autoresponder');
			}
		}
		$autoresponder_data['email_id']	= $this->getFromEmlArray();
		
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));
		$user_info=true;
		$str_user_detail_for_footer="";
		$user_info=(!$user_data_array[0]['company'])? false :  true;
		$user_info=(!$user_data_array[0]['address_line_1'])? false :  true;
		$user_info=(!$user_data_array[0]['city'])? false :  true;
		$user_info=(!$user_data_array[0]['state'])? false :  true;
		$user_info=(!$user_data_array[0]['zipcode'])? false :  true;
		$user_info=(!$user_data_array[0]['country_name'])? false :  true;
		
		$autoresponder_data['user_info']=$user_info;
		$autoresponder_data['user_data']=$user_data_array[0];
		$campaign_data['user_data']=$user_data_array[0];
		$autoresponder_data['email_from']=$user_data_array[0]['company'];
		//Fetch Country name
		$country_info=$this->UserModel->get_country_data();
		$autoresponder_data['country_info']=$country_info;
		$campaign_data['country_info']=$country_info;
		
		//$autoresponder_data['email_id']=$this->session->userdata('member_email_address');
		$autoresponder_data['campaign_id']=$autoresponder_id;
		$autoresponder_data['autoresponder']=$autoresponder_info[0];
		$autoresponder_data['camapign']['test_email_count']=$autoresponder_info[0]['test_email'];
		
		$autoresponder_data['is_ga_enabled']=$user_data_array[0]['google_analytics_status'];
		$autoresponder_data['is_clicktracking']=$user_data_array[0]['clicktracking_status'];
		//get autoresponder groups
		$autoresponder_group=$this->Autoresponder_Model->get_autoresponder_group(array('is_deleted'=>0,'id'=>$autoresponder_info[0]['autoresponder_group_id']));
		
		//collect in array
		$autoresponder_data['subscription_ids_str']=$autoresponder_group[0]['autoresponder_subscription_id'];
		# Load the configuration model which interact with database
		
		#Get shoreten url 
		$shorten_url=get_shorten_url();
		$this->load->view('header',array('title'=>'Send Autoresponder'));		
		$this->load->view('newsletter/autoresponder_email_setting',array('autoresponder_data'=>$autoresponder_data,'campaign_data'=>$campaign_data,'shorten_url'=>$shorten_url));
		$this->load->view('footer');
	}
	/**
		Function package_info to fetch selected user package info
		@return boolean return true or false
	**/
	function check_user_selected_package(){		
		$package_max_contacts = $this->UserModel->get_current_packages_maxcontact($this->session->userdata('member_id'));		 
		// Get member's actual contact count
		$subscriber_count = $this->contact_model->getContactsCount(array('subscriber_created_by'=>$this->session->userdata('member_id'),'subscriber_status'=>1,'is_deleted'=>0));
		// if actual contact count is more than member's package max contact, then send notification to admin
		if($subscriber_count > $package_max_contacts){			
			$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));
			$user_info=array($user_data_array[0]['member_username'],$subscriber_count);
			create_notification("upgradation",$user_info);			 
			return false;
		}else{
			return true;
		}
	}	
	 
	 
	/**
		Function save_campagin_email_setting to save email setting in database
	**/
	function save_campagin_email_setting($campaign_id=0,$scheduled_datetime=""){
		#Recieve subscription and campaign posted by user						
		$subscriptions=$this->input->post('subscriptions');
		if($subscriptions){
			$subscription_ids_str=implode(',',$subscriptions);
		}
		#Check email subject empty or fill
		if(!$this->input->post('email_subject')){
			$campaign_title="Unnamed";	#Set email subject unnamed if email_subject is empty
		}else{
			$campaign_title=$this->input->post('email_subject');	#Set email subject
			$email_subject=$this->input->post('email_subject');	#Set email subject
			//$email_subject = mb_convert_encoding($this->input->post('email_subject'), 'HTML-ENTITIES', 'UTF-8');
			//$campaign_title = $email_subject;
		}
		#Create input array to send to database						
		$input_array=array('email_subject'=>$email_subject,'sender_email'=>$this->input->post('email_id'),'sender_name'=>$this->input->post('email_from'),'subscription_list'=>$subscription_ids_str,'email_send_date'=>$scheduled_datetime);
		if( '' != $this->input->post('reply_to_email') )$input_array['reply_to_email'] = trim($this->input->post('reply_to_email'));
		 //		Start by cb
		$input_array['is_ab'] = 0;
		if ($this->input->post('is_abtesting')) {
			$input_array['is_ab'] = $this->input->post('is_abtesting');
			if($this->input->post('ab_test_campaign') != '' && $this->input->post('ab_test_campaign') > 0 ){
				$getAbtestingCamapign = $this->Campaign_Model->get_abtesting(array('ref_campaign_id'=> $campaign_id,'is_delete'=>'0','member_id'=>$this->session->userdata('member_id')));
				if(count($getAbtestingCamapign) > 0){
					if($getAbtestingCamapign[0]['campaign_id'] != $this->input->post('ab_test_campaign')){
						$this->Campaign_Model->update_abtesting(array('is_delete'=>'1'),array('campaign_ab_id'=> $getAbtestingCamapign[0]['campaign_ab_id']));
						$this->Campaign_Model->update_abtesting(array('ref_campaign_id' => $this->input->post('ab_test_campaign'),'member_id'=>$this->session->userdata('member_id')),array('campaign_id'=> $campaign_id));
						$this->Campaign_Model->add_abtesting(array('campaign_id'=>$this->input->post('ab_test_campaign'), 'ref_campaign_id'=>  $campaign_id,'member_id'=>$this->session->userdata('member_id')));
						$this->Campaign_Model->update_campaign(array('is_ab'=>'0'),array('campaign_id'=>$getAbtestingCamapign[0]['campaign_id']));
						$this->Campaign_Model->update_campaign(array('is_ab'=>'1'),array('campaign_id'=>$this->input->post('ab_test_campaign')));
					}
				}else{
					$this->Campaign_Model->add_abtesting(array('campaign_id'=> $campaign_id, 'ref_campaign_id'=> $this->input->post('ab_test_campaign'),'member_id'=>$this->session->userdata('member_id')));
					$this->Campaign_Model->add_abtesting(array('campaign_id'=>$this->input->post('ab_test_campaign'), 'ref_campaign_id'=>  $campaign_id,'member_id'=>$this->session->userdata('member_id')));
					$this->Campaign_Model->update_campaign(array('is_ab'=>'1'),array('campaign_id'=>$this->input->post('ab_test_campaign')));
				}
			}
		}else{
			$getAbtestingCamapign = $this->Campaign_Model->get_abtesting(array('ref_campaign_id'=> $campaign_id,'is_delete'=>'0','member_id'=>$this->session->userdata('member_id')));
			$this->Campaign_Model->update_abtesting(array('is_delete'=>'1'),array('campaign_id'=>$campaign_id));
			$this->Campaign_Model->update_abtesting(array('is_delete'=>'1'),array('ref_campaign_id'=>$campaign_id));
			$this->Campaign_Model->update_campaign(array('is_ab'=>'0'),array('campaign_id'=>$getAbtestingCamapign[0]['campaign_id']));
		}
		//end by cb
		$this->Campaign_Model->update_campaign($input_array,array('campaign_id'=>$campaign_id));
		# Assign success message by message class
		$this->messages->add('Email Saved Successfully', 'success');			
		# Redirect to listing of campaigns
		redirect('newsletter/campaign');
	}
	/**
		Function selected_subscribers to check number of subscribers in selected subscriptions list
		@param $ajax bolean check function is call for ajax or not
		@param $subscription_id int contain subscription id
	**/
	function selected_subscribers($ajax=true,$subscription_id=0){
		$where_in=array();
		if($subscription_id){			
			$where_in[]=$subscription_id;
			unset($_POST['subscriptions']);
				$_POST['subscriptions'][]=$subscription_id;
				$subscriber_count=0;
				$fetch_condiotions_array=array(	'res.subscriber_created_by'=>$this->session->userdata('member_id'),	'res.subscriber_status'=>1,	'res.is_deleted'=>0);	
				#$subscribers=$this->Subscriber_Model->get_distinct_email($fetch_condiotions_array,$_POST['subscriptions']);
				#$subscriber_count=count($subscribers);			
				$subscriber_count=$this->contact_model->get_contacts_count_in_selected_lists($fetch_condiotions_array,$_POST['subscriptions']);
				
		}else if(isset($_POST['subscriptions'])){
			$subscriber_count=0;
			$fetch_condiotions_array=array('res.subscriber_created_by'=>$this->session->userdata('member_id'), 'res.subscriber_status'=>1, 'res.is_deleted'=>0);	
			#$subscribers=$this->Subscriber_Model->get_distinct_email($fetch_condiotions_array,$_POST['subscriptions']);
			#$subscriber_count=count($subscribers);
			$subscriber_count=$this->contact_model->get_contacts_count_in_selected_lists($fetch_condiotions_array,$_POST['subscriptions']);
		}else{
			$subscriber_count=0;
		}
		if($ajax){
			echo $subscriber_count;
		}else{
			return $subscriber_count;
		}		
	}
	/**
		Function validate_scheduled_date to check that scheduled datetime should be greater than current date time
	**/
	function validate_scheduled_date(){
		if($this->scheduled_datetime !='' and $this->scheduled_datetime < time()){			 
				$this->form_validation->set_message('validate_scheduled_date', 'The %s field can not be older than current date');
				return false;
				exit;
		}		
		return true;
		exit;		
	}
	function create_activity_log($campaign_id=0){
		$this->load->model('Activity_Model');
		$site_configuration_array=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'default_allowed_limit_for_send_email'));
		$default_allowed_limit_for_send_email=$site_configuration_array[0]['config_value'];
		# Assign success message by message class
		if(trim($this->input->post('send_now'))=='1'){
			if(($subscriber_count<=$default_allowed_limit_for_send_email)||($user_is_authentic==1)){				 
				# create array for insert values in activty table
				$values=array('user_id'=>$this->session->userdata('member_id'),  'activity'=>'campaign_sent',  'campaign_id'=>$campaign_id);
				$this->Activity_Model->create_activity($values);				
			}else{				 
				# create array for insert values in activty table
				$values=array('user_id'=>$this->session->userdata('member_id'), 'activity'=>'campaign_schedule', 'campaign_id'=>$campaign_id);
				$this->Activity_Model->create_activity($values);
				$this->messages->add('Your email campaign is in queue and will be sent shortly.', 'success');
			}
		}else{
			# create array for insert values in activty table
			$values=array('user_id'=>$this->session->userdata('member_id'),  'activity'=>'campaign_schedule', 'campaign_id'=>$campaign_id);							
			$this->Activity_Model->create_activity($values);			
			$this->session->set_flashdata('campaign_status', 'scheduled');
		}
	}
	
	/**
		Function to send notification email to admin for schdule campaigns
	**/
	function notification_email($campaign_id=0,$subscriber_count=0,$user_name=""){
		//Get email template content
		$email_template_info=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$campaign_id));
		$scheduledTime = date('Y-m-d g:i a', strtotime( getGMTToLocalTime($email_template_info[0]['campaign_sheduled'], WEBMASTER_TIMEZONE )));
		
		$email_msg="";
		$email_msg.="<p>Hello admin,</p>";
		$email_msg.="<p>Campaign :<b>".$email_template_info[0]['campaign_title']."</b> created by <b>$user_name</b>, is ready to send for <b>$subscriber_count</b> subscribers.
						<br/> Campaign is sent/scheduled for <b>".$scheduledTime."</b>	</p>";
		$email_msg.="<p>Select a choice to allow or disallow it from admin panel.</p>";
		$email_msg.='<p>Regards,</p>';
		$email_msg.='<p>Redcappi Team</p>';
		
		$to=$this->get_Admin_notification_email();								 
		$message=$email_msg;						
		$text_message=$email_msg;
		// Removed by pravinjha@gmail.com
		// admin_notification_send_email($to, SYSTEM_EMAIL_FROM,'RedCappi', "Approval required for campaign sent/scheduled for ".$scheduledTime,$message,$text_message,0,0,true);
	}
	/**
		Function to send notification email to admin for user is sending campaign to xx number of subscribers
	**/
	function notification_subscribers_count($campaign_id=0,$subscriber_count=0,$user_name=""){
		//Get email template content
		$email_template_info=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$campaign_id));
		$scheduledTime = date('Y-m-d g:i a', strtotime( getGMTToLocalTime($email_template_info[0]['campaign_sheduled'], WEBMASTER_TIMEZONE )));
		
		$email_msg="";
		$email_msg.="<p>Hello admin,</p>";
		$email_msg.="<p>Campaign :<b>".$email_template_info[0]['campaign_title']."</b> created by <b>$user_name</b> is sent/scheduled for ".$subscriber_count." <br/> subscribers. <br/>Campaign is sent/scheduled for <b>".$scheduledTime."</b></p>";
		$email_msg.='<p>Regards,</p>';
		$email_msg.='<p>Redcappi Team</p>';
		
		$to=$this->get_Admin_notification_email();
		$message=$email_msg;						
		$text_message=$email_msg;		
		// Removed by pravinjha@gmail.com
		// admin_notification_send_email($to, SYSTEM_EMAIL_FROM,'RedCappi', 'Campaign sent/scheduled for '.$scheduledTime,$message,$text_message,0,0,true);
	}
	/**
		Function get_Admin_notification_email to fetch admin emails from config table
		@return string $admin_email return admin email list
	*/
	function get_Admin_notification_email(){
		$sql            = 'SELECT config_name,config_value FROM `red_site_configurations` where `config_name` = "admin_notification_email"';
		$query          = $this->db->query($sql);
		$admin_email	= "";
		if ($query->num_rows() == 1)
		{
			$row = $query->row();
			$admin_email        = $row->config_value;
		}
		return $admin_email;
	}
	/**
		Function first_time_user_notification will display notification to first time senders after "upgrade" is done
	*/
	function first_time_user_notification(){
		#Loads header, first_time_user_notification view.
		$this->load->view('header',array('title'=>'Notification'));
		$this->load->view('newsletter/first_time_user_notification',array('title'=>'Notification'));	
	}
	/**
		Function save_autoresponder_email_setting to save autoresponder email setting in database
	**/
	function save_autoresponder_email_setting($autoresponder_id=0){
		//$email_subject = mb_convert_encoding($this->input->post('email_subject'), 'HTML-ENTITIES', 'UTF-8');
		$email_subject = $this->input->post('email_subject');
		$input_array=array('email_subject'=>$email_subject,					
			'sender_email'=>$this->input->post('email_id'),
			'sender_name'=>$this->input->post('email_from'),
			'autoresponder_scheduled_interval'=>$this->input->post('autoresponder_schedule_interval')
		);
		#Store scheduled autoresponder in database
		$this->Autoresponder_Model->update_autoresponder($input_array,array('campaign_id'=>$autoresponder_id));
		# Assign success message by message class
		$this->messages->add('Email Saved Successfully', 'success');
		# Redirect to listing of campaigns
		redirect('newsletter/autoresponder');
	}
        
        function sendTestViaFrontEnd($campaign_id, $member_id){
                    
                    
                    $user=$this->UserModel->get_user_data(array('member_id'=>$member_id));
                    $sent_campaign=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$campaign_id,'campaign_created_by'=>$member_id)); 
                    
                    $vmta = $user[0]['vmta'];
                    
                    $sender_name=$sent_campaign[0]['sender_name'];				
                    $sender=$sent_campaign[0]['sender_email']; 
                    $reply_to_email= $sent_campaign[0]['reply_to_email'];
                    $subject=$sent_campaign[0]['email_subject'];
                    
                    $mail_tester_email = "redcappi-" . $campaign_id . "@mail-tester.com";
                    $mail_tester_url = "https://www.mail-tester.com/redcappi-" . $campaign_id;
                    $email_address_arr=explode(",",$mail_tester_email);
                    
                    if($sent_campaign[0]['campaign_template_option'] ==3){
                        $sent_campaign[0]['campaign_after_encode_url']=$sent_campaign[0]['campaign_content'];
                        $message_cnt=$this->Campaign_Autoresponder_Model->attach_campaign_link($sent_campaign[0],$user,'',$email_address_arr);
                    }
                        $campaign_footer_text_only = $this->Campaign_Autoresponder_Model->campaign_footer_text_only($user, $sent_campaign[0]['campaign_id'], false, true);

                        // send test  email one by one to each email address
                        foreach($email_address_arr as $to){
                                $message=$sent_campaign[0]['campaign_content'];			
                                $campaign_footer_text_only = str_replace("[CONTACT_EMAIL_ID]",$to,$campaign_footer_text_only);
                                $text_message=$sent_campaign[0]['campaign_text_content'].$campaign_footer_text_only;
                                
                                
                                $message=utf8_decode($this->is_authorized->webCompatibleString($message));						 
                                
                                
                                $subscriber_info = array('subscriber_id'=>0,'subscriber_email_address'=>$to,'subscriber_first_name'=>'','subscriber_last_name'=>'','subscriber_state'=>'','subscriber_zip_code'=>'','subscriber_country'=>'','subscriber_city'=>'','subscriber_company'=>'','subscriber_dob'=>'','subscriber_phone'=>'','subscriber_address'=>'','subscriber_extra_fields'=>'');			
                                $email_personalization = true;
                                $is_autoresponder = false;
                                $this->Campaign_Autoresponder_Model->getPersonalization($message,$text_message,$subject,$subscriber_info, $is_autoresponder, $campaign_id,$vmta, $email_personalization);
                         
                            
                            $campaign_type = 	($sent_campaign[0]['campaign_template_option'] != 5) ? 'html' : 'text' ;
                            

                            //Send test email
                            //print_r("to: " . $to ."</br>");
                            //print_r("sender: " . $sender ."</br>");
                            //print_r("sender name: " . $sender_name ."</br>");
                            //print_r("subject: " . $subject ."</br>");
                            //print_r("message: " . $message ."</br>");
                            //print_r("text_message: " . $text_message ."</br>");
                            //print_r("campaign_id: " . $campaign_id ."</br>");
                            //print_r("campaign_type: " . $campaign_type ."</br>");
                            //print_r("vmta: " . $vmta ."</br>");
                            //print_r("reply_to_email: " . $reply_to_email ."</br>");
                            send_email($to,$sender,$sender_name, '(TEST) '.$subject,$message,$text_message,0,$campaign_id, $campaign_type, false, array(),$vmta, $reply_to_email);			
                            
                        }
                        
                        $iframe_html = "<html><head><title>Mail Score</title></head><body><iframe width='100%' height='100%' align='middle' src='" . $mail_tester_url ."'></iframe></body>";
                        
                            print_r($iframe_html); 
                    
		
        }
	 	
}
?>
