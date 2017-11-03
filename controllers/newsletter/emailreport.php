<?php

/**

*	Controller class for campaigns

*	It have controller functions for email report management.

*/

class Emailreport extends CI_Controller
{

	/**
	* Contructor for controller.
	* It checks user session and redirects user if not logged in
	*/
	private $confg_arr = array();
	function __construct(){

        parent::__construct();

		# check via common model
		if(!$this->is_authorized->check_user())
			redirect('user/index');

		if(!count($this->session->userdata('user_packages')))
			redirect('user/packages');

		#$this->output->enable_profiler(TRUE);
		$this->load->library('upload');
		
		$this->load->model('newsletter/Emailreport_Model');		
		$this->load->model('newsletter/Campaign_Model');
		$this->load->model('UserModel');
		$this->load->model('newsletter/Subscriber_Model');		
		$this->load->model('newsletter/Subscription_Model');
		$this->load->model('newsletter/Campaign_email_track_restorage_Model');		
		$this->load->model('newsletter/Signup_Model');
		
		$this->load->model('ConfigurationModel');
		$this->confg_arr=$this->ConfigurationModel->get_site_configuration_data_as_array();
		if($this->confg_arr['maintenance_mode'] !='no'){
			redirect ("/site_under_maintenance/");
			exit;
		}
		force_ssl();	
		
	}



	/**
	* 'Index' controller. By default it calls display controller.
	*/

	function index(){
		$this->display();
	}


	/**
	*	'Dislay' controller function for email report.
	*/
	function display($id=0,$start=0){
		
		if($id<=0){
			$fetch_condiotions_array=array('campaign_created_by'=>$this->session->userdata('member_id'),'is_deleted'=>0,'campaign_status'=>'active');
			// Define config parameters for paging like base url, total rows and record per page.
			$config['base_url']=base_url().'newsletter/emailreport/display/'.$id;
			$config['total_rows']=$this->Campaign_Model->get_campaign_count($fetch_condiotions_array);
			$config['per_page']=10;
			$config['uri_segment']=5;
			$config['full_tag_open']	= 	'<ul class="pagination">';
			$config['full_tag_close'] 	= 	'</ul>';
			$config['cur_tag_open'] 	= 	'<li><a class="selected">';
			$config['cur_tag_close'] 	= 	'</a></li>';
			$config['first_tag_open'] 	= 	'<li>';
			$config['first_tag_close'] 	= 	'</li>';
			$config['last_tag_open'] 	= 	'<li>';
			$config['last_tag_close'] 	= 	'</li>';
			$config['num_tag_open'] 	= 	'<li>';
			$config['num_tag_close'] 	= 	'</li>';
			$config['next_tag_open'] 	= 	'<li>';
			$config['next_tag_close'] 	= 	'</li>';
			$config['prev_tag_open'] 	=	'<li>';
			$config['prev_tag_close'] 	= 	'</li>';
			// Initialize paging with above parameters
			$this->pagination->initialize($config);
			//Create paging links
			$paging_links=$this->pagination->create_links();
			// Fetches campaign data from database
			$campaign_data=$this->Campaign_Model->get_campaign_data($fetch_condiotions_array,$config['per_page'],$start,'desc','case');

		}else{
			$fetch_condiotions_array=array('campaign_created_by'=>$this->session->userdata('member_id'), 'is_deleted'=>0, 'campaign_status'=>'active', 'campaign_id'=>$id);
			$total_rows=$this->Campaign_Model->get_campaign_count($fetch_condiotions_array);
			// Fetches campaign data from database
			$campaign_data=$this->Campaign_Model->get_campaign_data($fetch_condiotions_array,$total_rows);
		}
		$campaign_id = array();
		$refrense_id = array();
		foreach($campaign_data as $campaign){
			$abrefrenseCampaign = '';
			if($campaign['is_ab'] == 1){
				$getAbtesting = $this->Campaign_Model->get_abtesting(array('campaign_id' => $campaign['campaign_id'],'is_delete'=>'0'));
				$abrefrenseCampaign = $getAbtesting[0]['ref_campaign_id'];
				
			}
			/* $campaign_id2[] = $campaign['campaign_id'];
			$refrense_id2[] = $abrefrenseCampaign; */
			if((!in_array($campaign['campaign_id'],$campaign_id) && !in_array($abrefrenseCampaign,$refrense_id)) && (!in_array($abrefrenseCampaign,$campaign_id) && !in_array($campaign['campaign_id'],$refrense_id)))
			{
			
				$campaign_id[] = $campaign['campaign_id'];
				if($abrefrenseCampaign != '')
					$refrense_id[] = $abrefrenseCampaign;
				if($campaign['is_restore']==1){
					$emailreport_data[$campaign['campaign_id']]=$this->display_email_track_from_backup($campaign['campaign_id']);
					$emailreport_data[$campaign['campaign_id']]['is_freezed']=1;
					$emailreport_data[$campaign['campaign_id']]['email_send_date']=$campaign['email_send_date'];	// send date
					$emailreport_data[$campaign['campaign_id']]['campaign_title']=($campaign['email_subject'] != '')? $campaign['email_subject'] : $campaign['campaign_title'] ;	// campaign subject
					$emailreport_data[$campaign['campaign_id']]['sender_name']=$campaign['sender_name'];	// Sender name
					$emailreport_data[$campaign['campaign_id']]['sender_email']=$campaign['sender_email'];	// Sender email
				}else{

					$emailreport_data[$campaign['campaign_id']]['is_freezed']=0;
					$total_delivered_emails=$this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$campaign['campaign_id']));
					//echo $this->db->last_query();exit;
					$total_read_emails=$this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$campaign['campaign_id'], 'email_track_read'=>1));				
					$total_complaint_emails=$this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$campaign['campaign_id'], 'email_track_complaint'=>1));
					$total_bounce_emails=$this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$campaign['campaign_id'], 'email_track_bounce >'=>0));
					$total_forward_emails=$this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$campaign['campaign_id'], 'email_track_forward >'=>0));

					$total_unread_emails = $total_delivered_emails - $total_read_emails - $total_bounce_emails;

					
					// Get Total number of  mail
					$list_emails=$this->Emailreport_Model->get_emailreport_listdata(array('campaign_id'=>$campaign['campaign_id']));
					$total_click_emails = $list_emails[0]['email_track_click'];

					// collect values in array for email report view
					$emailreport_data[$campaign['campaign_id']]['campaign_send_date']=$list_emails[0]['campaign_scheduled_date'];	// send date
					$emailreport_data[$campaign['campaign_id']]['email_send_date']=$campaign['email_send_date'];;	// send date
					$emailreport_data[$campaign['campaign_id']]['total_unsubscribes']=$list_emails[0]['email_track_unsubscribes'];	// total unsubscribes email
					$emailreport_data[$campaign['campaign_id']]['total_delivered_emails']=$total_delivered_emails;	// total delivered emails
					$emailreport_data[$campaign['campaign_id']]['total_read_emails']=$total_read_emails;	// total read emails
					$emailreport_data[$campaign['campaign_id']]['total_unread_emails']=$total_unread_emails;	// total unread emails
					$emailreport_data[$campaign['campaign_id']]['total_click_emails']=$list_emails[0]['email_track_click'];	// total click emails
					$emailreport_data[$campaign['campaign_id']]['campaign_title']=$campaign['email_subject'];	// total click emails
					$emailreport_data[$campaign['campaign_id']]['sender_name']=$campaign['sender_name'];	// Sender name
					$emailreport_data[$campaign['campaign_id']]['sender_email']=$campaign['sender_email'];	// Sender email
					$emailreport_data[$campaign['campaign_id']]['email_track_bounce']=$total_bounce_emails;	// total bounce emails
					$emailreport_data[$campaign['campaign_id']]['email_track_forward']=$total_forward_emails;	// total forward emails
					$emailreport_data[$campaign['campaign_id']]['total_complaint_emails']=$total_complaint_emails;	// total forward emails
					$emailreport_data[$campaign['campaign_id']]['per_read_emails']=0;
					$emailreport_data[$campaign['campaign_id']]['per_bounce_emails']=0;
					$emailreport_data[$campaign['campaign_id']]['per_unread_emails']=0;
					if($total_delivered_emails>0){
						//calculate percentage
						$emailreport_data[$campaign['campaign_id']]['per_read_emails']=($total_read_emails/$total_delivered_emails)*100;
						$emailreport_data[$campaign['campaign_id']]['per_bounce_emails']=($total_bounce_emails/$total_delivered_emails)*100;
						$emailreport_data[$campaign['campaign_id']]['per_unread_emails']=($total_unread_emails/$total_delivered_emails)*100;
					}
					if($campaign['is_ab'] == 1){
						$getAbtesting = $this->Campaign_Model->get_abtesting(array('campaign_id' => $campaign['campaign_id']));
						$abrefrenseCampaign = $getAbtesting[0]['ref_campaign_id'];
						
						if($abrefrenseCampaign){
							//$fetch_condiotions_array=array('campaign_created_by'=>$this->session->userdata('member_id'), 'is_deleted'=>0, 'campaign_status'=>'active', 'campaign_id'=>$abrefrenseCampaign);
							$fetch_condiotions_array=array('campaign_created_by'=>$this->session->userdata('member_id'), 'is_deleted'=>0, 'campaign_id'=>$abrefrenseCampaign);
							$total_rows=$this->Campaign_Model->get_campaign_count($fetch_condiotions_array);
							// Fetches campaign data from database
							$campaign_abrefrense=$this->Campaign_Model->get_campaign_data($fetch_condiotions_array,$total_rows);
							$campaign_abrefrense = $campaign_abrefrense[0];
							if(!empty($campaign_abrefrense)){
								$emailreport_data[$campaign['campaign_id']]['is_ab']['refrense_campaign'] = $campaign_abrefrense['campaign_id'];
								$emailreport_data[$campaign['campaign_id']]['is_ab'][$campaign_abrefrense['campaign_id']]['campaign_title']=($campaign_abrefrense['email_subject'] != '')? $campaign_abrefrense['email_subject'] : $campaign_abrefrense['campaign_title'];	// total click emails
								
								if($campaign_abrefrense['campaign_status'] == 'active'){
									$emailreport_data[$campaign['campaign_id']] ['is_ab'][$campaign_abrefrense['campaign_id']]['is_freezed']=0;
									$total_delivered_emails=$this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$campaign_abrefrense['campaign_id']));
									//echo $this->db->last_query();exit;
									$total_read_emails=$this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$campaign_abrefrense['campaign_id'], 'email_track_read'=>1));				
									$total_complaint_emails=$this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$campaign_abrefrense['campaign_id'], 'email_track_complaint'=>1));
									$total_bounce_emails=$this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$campaign_abrefrense['campaign_id'], 'email_track_bounce >'=>0));
									$total_forward_emails=$this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$campaign_abrefrense['campaign_id'], 'email_track_forward >'=>0));

									$total_unread_emails = $total_delivered_emails - $total_read_emails - $total_bounce_emails;

									
									// Get Total number of  mail
									$list_emails_abtesting=$this->Emailreport_Model->get_emailreport_listdata(array('campaign_id'=>$campaign_abrefrense['campaign_id']));
									
									$total_click_emails = $list_emails_abtesting[0]['email_track_click'];
									
									// collect values in array for email report view
									$emailreport_data[$campaign['campaign_id']]['is_ab'][$campaign_abrefrense['campaign_id']]['campaign_send_date']=$list_emails_abtesting[0]['campaign_scheduled_date'];	// send date
									$emailreport_data[$campaign['campaign_id']]['is_ab'][$campaign_abrefrense['campaign_id']]['email_send_date']=$campaign_abrefrense['email_send_date'];;	// send date
									$emailreport_data[$campaign['campaign_id']]['is_ab'][$campaign_abrefrense['campaign_id']]['total_unsubscribes']=$list_emails_abtesting[0]['email_track_unsubscribes'];	// total unsubscribes email
									$emailreport_data[$campaign['campaign_id']]['is_ab'][$campaign_abrefrense['campaign_id']]['total_delivered_emails']=$total_delivered_emails;	// total delivered emails
									$emailreport_data[$campaign['campaign_id']]['is_ab'][$campaign_abrefrense['campaign_id']]['total_read_emails']=$total_read_emails;	// total read emails
									$emailreport_data[$campaign['campaign_id']]['is_ab'][$campaign_abrefrense['campaign_id']]['total_unread_emails']=$total_unread_emails;	// total unread emails
									$emailreport_data[$campaign['campaign_id']]['is_ab'][$campaign_abrefrense['campaign_id']]['total_click_emails']=$list_emails_abtesting[0]['email_track_click'];	// total click emails
									
									$emailreport_data[$campaign['campaign_id']]['is_ab'][$campaign_abrefrense['campaign_id']]['sender_name']=$campaign_abrefrense['sender_name'];	// Sender name
									$emailreport_data[$campaign['campaign_id']]['is_ab'][$campaign_abrefrense['campaign_id']]['sender_email']=$campaign_abrefrense['sender_email'];	// Sender email
									$emailreport_data[$campaign['campaign_id']]['is_ab'][$campaign_abrefrense['campaign_id']]['email_track_bounce']=$total_bounce_emails;	// total bounce emails
									$emailreport_data[$campaign['campaign_id']]['is_ab'][$campaign_abrefrense['campaign_id']]['email_track_forward']=$total_forward_emails;	// total forward emails
									$emailreport_data[$campaign['campaign_id']]['total_complaint_emails']=$total_complaint_emails;	// total forward emails
									$emailreport_data[$campaign['campaign_id']]['is_ab'][$campaign_abrefrense['campaign_id']]['per_read_emails']=0;
									$emailreport_data[$campaign['campaign_id']]['is_ab'][$campaign_abrefrense['campaign_id']]['per_bounce_emails']=0;
									$emailreport_data[$campaign['campaign_id']]['is_ab'][$campaign_abrefrense['campaign_id']]['per_unread_emails']=0;
									if($total_delivered_emails>0){
										//calculate percentage
										$emailreport_data[$campaign['campaign_id']]['is_ab'][$campaign_abrefrense['campaign_id']]['per_read_emails']=($total_read_emails/$total_delivered_emails)*100;
										$emailreport_data[$campaign['campaign_id']]['is_ab'][$campaign_abrefrense['campaign_id']]['per_bounce_emails']=($total_bounce_emails/$total_delivered_emails)*100;
										$emailreport_data[$campaign['campaign_id']]['is_ab'][$campaign_abrefrense['campaign_id']]['per_unread_emails']=($total_unread_emails/$total_delivered_emails)*100;
									}
									$subscription_id_arr=@explode(",",$campaign_abrefrense['subscription_list']);

									#Fetch subscription List Titles
									$subscription_list_title=$this->Emailreport_Model->get_subscription_list_title(array('subscription_created_by'=>$this->session->userdata('member_id')),$subscription_id_arr);
									$emailreport_data[$campaign['campaign_id']]['is_ab'][$campaign_abrefrense['campaign_id']]['subscription_list_title']= @implode(", ",$subscription_list_title);
								}
							}else{
								$emailreport_data[$campaign['campaign_id']]['is_ab'] = '';
							}
						}else{
							$emailreport_data[$campaign['campaign_id']]['is_ab'] = '';
						}
					}else{
						$emailreport_data[$campaign['campaign_id']]['is_ab'] = '';
					}
				} 
				#prepare subscription list id
				$subscription_id_arr=@explode(",",$campaign['subscription_list']);

				#Fetch subscription List Titles
				$subscription_list_title=$this->Emailreport_Model->get_subscription_list_title(array('subscription_created_by'=>$this->session->userdata('member_id')),$subscription_id_arr);
				$emailreport_data[$campaign['campaign_id']]['subscription_list_title']= @implode(", ",$subscription_list_title);
			}
		}
		/**
		 * Fetch user data
		 */
		
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));
		$extra=$user_data_array[0];
		if($id>0){
			$this->session->set_userdata('HTTP_REFERER_EMAIL', base_url()."newsletter/emailreport/display/".$id);
		}else{
			$this->session->set_userdata('HTTP_REFERER_EMAIL',base_url()."newsletter/emailreport/display");
		}
		//Loads header, email report and footer view.

		$this->load->view('header',array('title'=>'Email Report'));

		$this->load->view('emailreport/emailreport_view',array('emailreport_data'=>$emailreport_data,'paging_links'=>$paging_links,'extra'=>$extra));

		$this->load->view('footer');

	}

	function update($id){
		
		$input_array=array('email_track_read'=>1);
		$emailtrack_insert_id=$this->Emailreport_Model->update_emailreport($input_array,array('email_track_id'=>$id,'email_track_bounce'=>0));
	}

	function view($action="",$id=0,$start=0,$url=""){
		
		$fetch_condiotions_array=array(	'campaign_created_by'=>$this->session->userdata('member_id'),'is_deleted'=>0,'campaign_status'=>'active','campaign_id'=>$id	);
		
		// Fetches campaign data from database
		$campaign_data=$this->Campaign_Model->get_campaign_data($fetch_condiotions_array);
		if(count($campaign_data) < 1){
			redirect('user/index');
			exit;
		}elseif($campaign_data[0]['is_restore']==1){
			$this->view_email_detail_from_backup($campaign_data[0],$action);
		}else{
			$sent_condiotions_array=array('campaign_id'=>$id,'user_id'=>$this->session->userdata('member_id'),	'email_sent'=>1	);
			$sent_total_count = $this->Emailreport_Model->get_emailreport_subscriber_count($sent_condiotions_array);
			
			$read_condiotions_array=array('campaign_id'=>$id,'user_id'=>$this->session->userdata('member_id'),	'email_track_read >'=>0);
			$read_total_count = $this->Emailreport_Model->get_emailreport_subscriber_count($read_condiotions_array);
			
			
			$unread_condiotions_array=array(	'campaign_id'=>$id,'user_id'=>$this->session->userdata('member_id'), 'email_track_read'=>0);
			$unread_total_count = $this->Emailreport_Model->get_emailreport_subscriber_count($unread_condiotions_array);
			
			$bounced_condiotions_array=array(	'campaign_id'=>$id,'user_id'=>$this->session->userdata('member_id'),	'email_track_bounce >'=>0);
			$bounced_total_count = $this->Emailreport_Model->get_emailreport_subscriber_count($bounced_condiotions_array);
			
			$unsubscribes_condiotions_array=array('campaign_id'=>$id,'user_id'=>$this->session->userdata('member_id'),'email_track_unsubscribes >'=>0);
			$unsubscribes_total_count = $this->Emailreport_Model->get_emailreport_subscriber_count($unsubscribes_condiotions_array);
			
			$complaints_condiotions_array=array(	'campaign_id'=>$id,'user_id'=>$this->session->userdata('member_id'),	'email_track_complaint'=>1);
			$complaints_total_count = $this->Emailreport_Model->get_emailreport_subscriber_count($complaints_condiotions_array);
			
			$click_condiotions_array=array('campaign_id'=>$id, 'counter >'=>0,'subscriber_id >'=>0,'is_autoresponder'=>0	);
			$arr_emailreport_data=$this->Emailreport_Model->get_emailreport_click($click_condiotions_array);			
			//$clicks_total_count = count($arr_emailreport_data);
			
			$list_emails=$this->Emailreport_Model->get_emailreport_listdata(array('campaign_id'=>$id));
			$clicks_total_count = $list_emails[0]['email_track_click'];
			
			
			$forward_condiotions_array=array(	'campaign_id'=>$id,'user_id'=>$this->session->userdata('member_id'),	'email_track_forward >'=>0);
			$forward_total_count = $this->Emailreport_Model->get_emailreport_subscriber_count($forward_condiotions_array);
			
			
			
			$config['base_url']=base_url().'newsletter/emailreport/view/'.$action."/".$id;	
			$config['per_page']			=	50;
			$config['uri_segment']		=	6;
			$config['num_links']		=	4;	// Number of "digit" links to show before/after the currently viewed page
			$config['full_tag_open']	= 	'<ul class="pagination">';
			$config['full_tag_close'] 	= 	'</ul>';
			$config['cur_tag_open'] 	= 	'<li><a class="selected">';
			$config['cur_tag_close'] 	= 	'</a></li>';
			$config['first_tag_open'] 	= 	'<li>';
			$config['first_tag_close'] 	= 	'</li>';
			$config['last_tag_open'] 	= 	'<li>';
			$config['last_tag_close'] 	= 	'</li>';
			$config['num_tag_open'] 	= 	'<li>';
			$config['num_tag_close'] 	= 	'</li>';
			$config['next_tag_open'] 	= 	'<li>';
			$config['next_tag_close'] 	= 	'</li>';
			$config['prev_tag_open'] 	=	'<li>';
			$config['prev_tag_close'] 	= 	'</li>';
			$i = 0;
			
			

			if($action=="sent"){
				$current_tab="send_email";
				$config['total_rows']= $sent_total_count;
				$emailreport_data=$this->Emailreport_Model->get_emailreport_subscriber($sent_condiotions_array,$config['per_page'],$start);
			}elseif($action=="read"){	
				$current_tab="read_email";
				$config['total_rows']= $read_total_count;
				$emailreport_data=$this->Emailreport_Model->get_emailreport_subscriber($read_condiotions_array,$config['per_page'],$start);
			}elseif($action=="bounced"){	
				$current_tab="bounced_email";
				$config['total_rows']= $bounced_total_count;
				$emailreport_data=$this->Emailreport_Model->get_emailreport_subscriber($bounced_condiotions_array,$config['per_page'],$start);
			}elseif($action=="unread"){	
				$current_tab="unread_email";
				$config['total_rows']= $unread_total_count;
				$emailreport_data=$this->Emailreport_Model->get_emailreport_subscriber($unread_condiotions_array,$config['per_page'],$start);
			}elseif($action=="unsubscribes"){	
				$current_tab="unsubscribes_email";
				$config['total_rows']= $unsubscribes_total_count;
				$emailreport_data=$this->Emailreport_Model->get_emailreport_subscriber($unsubscribes_condiotions_array,$config['per_page'],$start);
			}elseif($action=="complaints"){	
				$current_tab="complaints_email";
				$config['total_rows']= $complaints_total_count;
				$emailreport_data=$this->Emailreport_Model->get_emailreport_subscriber($complaints_condiotions_array,$config['per_page'],$start);
			}elseif($action=="click"){	
				// Fetches subscriber data from database
				$emailreport_data= $arr_emailreport_data;

				$current_tab="click_link";
				$this->session->set_userdata('HTTP_REFERER','http://'.$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL']);
				
				$previous_page_url=$this->session->userdata('HTTP_REFERER_EMAIL');				
				$this->load->view('header',array('title'=>'Sent Mail Report','previous_page_url'=>$previous_page_url));
				$this->load->view('emailreport/emailreport_click',array('emailreport_data'=>$emailreport_data,'current_tab'=>$current_tab,'campaign_id'=>$id,'campaign_data'=>$campaign_data[0], 'sent_total_count'=>$sent_total_count, 'read_total_count'=>$read_total_count, 'bounced_total_count'=>$bounced_total_count, 'unread_total_count'=>$unread_total_count, 'unsubscribes_total_count'=>$unsubscribes_total_count, 'complaints_total_count'=>$complaints_total_count, 'clicks_total_count'=>$clicks_total_count, 'forward_total_count'=>$forward_total_count));
				$this->load->view('footer-inner-red');
				
			}elseif($action=="forwardemail"){					
				$current_tab="forward_email";
				$config['total_rows']= $forward_total_count;
				$this->pagination->initialize($config);
				$paging_links=$this->pagination->create_links();
				$emailreport_forward_data=$this->Emailreport_Model->get_emailreport_subscriber($forward_condiotions_array,$config['per_page'],$start);
				
					foreach($emailreport_forward_data as $emailreport_info){
						$emailreport_subsciber_data[$i]['campaign_id']=$emailreport_info['campaign_id'];
						$subscriber_info=$this->Subscriber_Model->get_subscriber_info_view(array('subscriber_email_address'=>$emailreport_info['subscriber_email_address'],'is_deleted'=>0,'subscriber_created_by'=>$this->session->userdata('member_id')));
						$emailreport_forward_data[$i]['subscriber_email_address']=$emailreport_info['subscriber_email_address'];
						$emailreport_forward_data[$i]['subscriber_first_name']=$subscriber_info[0]['subscriber_first_name'];
						$emailreport_forward_data[$i]['subscriber_last_name']=$subscriber_info[0]['subscriber_last_name'];
						$i++;
					}
					$previous_page_url=$this->session->userdata('HTTP_REFERER_EMAIL');
					// Load view
					$this->load->view('header',array('title'=>'Sent Mail Report','previous_page_url'=>$previous_page_url));
					$this->load->view('emailreport/emailreport_subscriber',array('emailreport_forward_data'=>$emailreport_forward_data,'current_tab'=>$current_tab,'campaign_id'=>$id,'campaign_data'=>$campaign_data[0],'paging_links'=>$paging_links, 'sent_total_count'=>$sent_total_count, 'read_total_count'=>$read_total_count, 'bounced_total_count'=>$bounced_total_count, 'unread_total_count'=>$unread_total_count, 'unsubscribes_total_count'=>$unsubscribes_total_count, 'complaints_total_count'=>$complaints_total_count, 'clicks_total_count'=>$clicks_total_count, 'forward_total_count'=>$forward_total_count));
					$this->load->view('footer-inner-red');
					
			}			
			if($action !="click" and $action !="forwardemail")	{
				$this->pagination->initialize($config);
				$paging_links=$this->pagination->create_links();
			// For Sent, read, unread, unsubscribes, complaints, bounced, 
				foreach($emailreport_data as $email_info){
					$subscriber_info=$this->Subscriber_Model->get_subscriber_info_view(array('subscriber_email_address'=>$email_info['subscriber_email_address'],'is_deleted'=>0,'subscriber_created_by'=>$this->session->userdata('member_id')));
					if(count($subscriber_info)>0){
						$emailreport_data[$i]['subscriber_email_address']=$email_info['subscriber_email_address'];
					}
					$emailreport_data[$i]['subscriber_first_name']=$subscriber_info[0]['subscriber_first_name'];
					$emailreport_data[$i]['subscriber_last_name']=$subscriber_info[0]['subscriber_last_name'];
					$i++;
				}
				
				$previous_page_url=$this->session->userdata('HTTP_REFERER_EMAIL');

				$this->load->view('header',array('title'=>'Sent Mail Report','previous_page_url'=>$previous_page_url));
				$this->load->view('emailreport/emailreport_subscriber',array('emailreport_data'=>$emailreport_data,'total_rows'=>$config['total_rows'],'current_tab'=>$current_tab,'campaign_id'=>$id,'campaign_data'=>$campaign_data[0],'paging_links'=>$paging_links, 'sent_total_count'=>$sent_total_count, 'read_total_count'=>$read_total_count, 'bounced_total_count'=>$bounced_total_count, 'unread_total_count'=>$unread_total_count, 'unsubscribes_total_count'=>$unsubscribes_total_count, 'complaints_total_count'=>$complaints_total_count, 'clicks_total_count'=>$clicks_total_count, 'forward_total_count'=>$forward_total_count));
				$this->load->view('footer-inner-red');
			}
							
		}
	}
	function view_subscriber_click($id=0,$url="",$start=0){
		
		$fetch_condiotions_array=array('campaign_created_by'=>$this->session->userdata('member_id'), 'is_deleted'=>0, 'campaign_status'=>'active', 'campaign_id'=>$id );
		
		// Fetches campaign data from database
		$campaign_data=$this->Campaign_Model->get_campaign_data($fetch_condiotions_array);
		$fetch_condiotions_array=array('ret.campaign_id'=>$id, 'counter >'=>0, 'tiny_url'=>$url, 'is_autoresponder'=>0);

			// Define config parameters for paging like base url, total rows and record per page.
			$config['base_url']=base_url().'newsletter/emailreport/view_subscriber_click/'.$action."/".$id."/".$url;	// The page we are linking to
			$config['total_rows']=count($this->Emailreport_Model->get_emailreport_subscriber_click($fetch_condiotions_array));
			$config['per_page']			=	50;
			$config['uri_segment']		=	6;
			$config['num_links']		=	4;	// Number of "digit" links to show before/after the currently viewed page
			$config['full_tag_open']	= 	'<ul class="pagination">';
			$config['full_tag_close'] 	= 	'</ul>';
			$config['cur_tag_open'] 	= 	'<li><a class="selected">';
			$config['cur_tag_close'] 	= 	'</a></li>';
			$config['first_tag_open'] 	= 	'<li>';
			$config['first_tag_close'] 	= 	'</li>';
			$config['last_tag_open'] 	= 	'<li>';
			$config['last_tag_close'] 	= 	'</li>';
			$config['num_tag_open'] 	= 	'<li>';
			$config['num_tag_close'] 	= 	'</li>';
			$config['next_tag_open'] 	= 	'<li>';
			$config['next_tag_close'] 	= 	'</li>';
			$config['prev_tag_open'] 	=	'<li>';
			$config['prev_tag_close'] 	= 	'</li>';
			// Initialize paging with above parameters
			$this->pagination->initialize($config);

			//Create paging inks
			$paging_links=$this->pagination->create_links();
			// Fetches subscriber data from database
			$emailreport_subsciber_data=$this->Emailreport_Model->get_emailreport_subscriber_click($fetch_condiotions_array,$config['per_page'],$start);

			
			$i=0;
			foreach($emailreport_subsciber_data as $emailreport){
				$subscriber_info=$this->Subscriber_Model->get_subscriber_info_view(array('subscriber_email_address'=>$emailreport['subscriber_email_address'],'is_deleted'=>0,'subscriber_created_by'=>$this->session->userdata('member_id')));
				if(count($subscriber_info)>0){
					$emailreport_subsciber_data[$i]['subscriber_first_name']=$subscriber_info[0]['subscriber_first_name'];
					$emailreport_subsciber_data[$i]['subscriber_last_name']=$subscriber_info[0]['subscriber_last_name'];
				}
				$i++;
			}

			$current_tab="click_link";
			$previous_page_url=$this->session->userdata('HTTP_REFERER');
			
			// Start of all type of repor count			
			$sent_condiotions_array=array('campaign_id'=>$id,'user_id'=>$this->session->userdata('member_id'),	'email_sent'=>1	);
			$sent_total_count = $this->Emailreport_Model->get_emailreport_subscriber_count($sent_condiotions_array);
			
			$read_condiotions_array=array('campaign_id'=>$id,'user_id'=>$this->session->userdata('member_id'),	'email_track_read >'=>0);
			$read_total_count = $this->Emailreport_Model->get_emailreport_subscriber_count($read_condiotions_array);
			
			
			$unread_condiotions_array=array(	'campaign_id'=>$id,'user_id'=>$this->session->userdata('member_id'), 'email_track_read'=>0);
			$unread_total_count = $this->Emailreport_Model->get_emailreport_subscriber_count($unread_condiotions_array);
			
			$bounced_condiotions_array=array(	'campaign_id'=>$id,'user_id'=>$this->session->userdata('member_id'),	'email_track_bounce >'=>0);
			$bounced_total_count = $this->Emailreport_Model->get_emailreport_subscriber_count($bounced_condiotions_array);
			
			$unsubscribes_condiotions_array=array('campaign_id'=>$id,'user_id'=>$this->session->userdata('member_id'),'email_track_unsubscribes >'=>0);
			$unsubscribes_total_count = $this->Emailreport_Model->get_emailreport_subscriber_count($unsubscribes_condiotions_array);
			
			$complaints_condiotions_array=array(	'campaign_id'=>$id,'user_id'=>$this->session->userdata('member_id'),	'email_track_complaint'=>1);
			$complaints_total_count = $this->Emailreport_Model->get_emailreport_subscriber_count($complaints_condiotions_array);
			
			$click_condiotions_array=array('campaign_id'=>$id, 'counter >'=>0,'subscriber_id >'=>0,'is_autoresponder'=>0	);
			$arr_emailreport_data=$this->Emailreport_Model->get_emailreport_click($click_condiotions_array);
			
			$list_emails=$this->Emailreport_Model->get_emailreport_listdata(array('campaign_id'=>$id));
			$clicks_total_count = $list_emails[0]['email_track_click'];
			
			
			$forward_condiotions_array=array(	'campaign_id'=>$id,'user_id'=>$this->session->userdata('member_id'),	'email_track_forward >'=>0);
			$forward_total_count = $this->Emailreport_Model->get_emailreport_subscriber_count($forward_condiotions_array);
			// End of all type of repor count
			// Load view
			$this->load->view('header',array('title'=>'Sent Mail Report','previous_page_url'=>$previous_page_url));
			$this->load->view('emailreport/emailreport_subscriber',array('emailreport_subsciber_data'=>$emailreport_subsciber_data,'current_tab'=>$current_tab,'campaign_id'=>$id,'tiny_url'=>$url, 'campaign_data'=>$campaign_data[0],'paging_links'=>$paging_links, 'sent_total_count'=>$sent_total_count, 'read_total_count'=>$read_total_count, 'bounced_total_count'=>$bounced_total_count, 'unread_total_count'=>$unread_total_count, 'unsubscribes_total_count'=>$unsubscribes_total_count, 'complaints_total_count'=>$complaints_total_count, 'clicks_total_count'=>$clicks_total_count, 'forward_total_count'=>$forward_total_count));
			$this->load->view('footer-inner-red');
	}
	/**
	  * Function subscriber_view to display contact profile
	**/
	function ajaxHistory($sid, $contact_soft_bounce, $contact_bounce_status, $p=0){
		$site_configuration_array=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'max_soft_bounce'));
		$max_soft_bounce=$site_configuration_array[0]['config_value'];
		// Fetch Email Report
		$psize=5;
		if($p < 1)
		$startfrom = 0;
		else
		$startfrom =($p)* $psize;

		$email_report=$this->Emailreport_Model->get_emailreport_campaign_data(array('ret.subscriber_id'=>$sid,'campaign_created_by'=>$this->session->userdata('member_id'),'ret.email_sent'=>1),$psize, $startfrom);
		$soft_bounce=$contact_soft_bounce;
		if(count($email_report) > 0){
			foreach($email_report as $key=>$campaign_report){
				$fetch_condiotions_array=array('campaign_id'=>$campaign_report['campaign_id'],'counter >'=>0,'subscriber_id'=>$sid);

				// Fetches subscriber data from database
				$emailclickreport_data[$campaign_report['campaign_id']]=$this->Emailreport_Model->get_emailreport_click($fetch_condiotions_array);
				# Count clicks of all url
				$counter=0;
				foreach($emailclickreport_data[$campaign_report['campaign_id']] as $click){
					$counter+=$click['cnt'];
				}
				$email_report[$key]['clicks']=$counter;
				if(($contact_bounce_status==1)&&($contact_soft_bounce > $max_soft_bounce)){
					$email_report[$key]['soft_bounce_status']=$soft_bounce;
					$soft_bounce=$contact_soft_bounce--;
				}
			}
			$subscriptions=$this->Subscriber_Model->get_subscriber_info_view(array('res.subscriber_id'=>$id,'res.subscriber_created_by'=>$this->session->userdata('member_id'),'res.is_deleted'=>0));
			$shorten_url=get_shorten_url();

			$result = $this->load->view('contacts/contact_history',array('subscriptions'=>$subscriptions,'email_report'=>$email_report,'shorten_url'=>$shorten_url,'max_soft_bounce'=>$max_soft_bounce,'email_report_click'=>$emailclickreport_data,'max_soft_bounce'=>$max_soft_bounce), true);



		}else{
			$result ='';
		}
		if($p >0){
			echo $result;
		}else{
			return $result;
		}

	}
	function subscriber_view($campaign_id=0,$subscriber_id=0){
		
		$site_configuration_array=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'max_soft_bounce'));
		$max_soft_bounce=$site_configuration_array[0]['config_value'];
		#define('MAX_SOFT_BOUNCE', "$max_soft_bounce");


		//	Collect subscriber id
		//Protecting MySQL from query string sql injection Attacks
		if(is_numeric($subscriber_id)){
			$id = $subscriber_id;
		}else{
			$id=0;
			echo "error:subscriber id not exist";
			exit;
		}
		

		// Fetch Email from email report table according to campaign id and subscriber id
		$email_report=$this->Emailreport_Model->get_emailreport_campaign_data(array('ret.subscriber_id'=>$subscriber_id,'ret.email_sent'=>1,'campaign_created_by'=>$this->session->userdata('member_id')));
		$subscriptions[0]['subscriber_email_address']=$email_report[0]['subscriber_email_address'];
		$subscriptions[0]['subscriber_status']=1;
		$subscriber_info=$this->Subscriber_Model->get_subscriber_info_view(array('subscriber_id'=>$email_report[0]['subscriber_id'],'is_deleted'=>0,'subscriber_created_by'=>$this->session->userdata('member_id')));

		if(count($subscriber_info)>0){
			// Fetch Subscription list
			$subscriptions['list']=$this->Subscription_Model->get_subscription_list(array('ress.subscriber_id'=>$subscriber_info[0]['subscriber_id'],'res.is_deleted'=>0));
		}

		$subscription_title=array();
		$i=0;
		if(count($subscriptions['list'])>0){
			foreach($subscriptions['list'] as $subscription){
				if($subscription['subscription_title']=="All My Contacts"){
					$subscription_title[0]=$subscription['subscription_title'];
				}else{
					if($i==0){
						$j=$i+1;
					}else{
						$j=$i;
					}
					$subscription_title[$j]=$subscription['subscription_title'];
				}
				$i++;
			}
		}
		$subscription_title[0]="All My Contacts";
		ksort($subscription_title);
		$result=array_unique($subscription_title);
		$subscription_title=array();
		$subscription_title=$result;
		if(count($subscriber_info)<=0){
			$subscriptions['list']=$this->Subscription_Model->get_subscription_data(array('subscription_id'=>$email_report[0]['subscription_id'],'subscription_created_by'=>$this->session->userdata('member_id')));

			foreach($subscriptions['list'] as $subscription){
				$subscription_title[]=$subscription['subscription_title'];
			}
			$result=array_unique($subscription_title);
			$subscription_title=array();
			$subscription_title=$result;
			$subscriptions[0]['subscriber_status']=5;
		}else{
			$subscriptions[0]['subscriber_email_address']=$subscriber_info[0]['subscriber_email_address'];
			$subscriptions[0]['subscriber_first_name']=$subscriber_info[0]['subscriber_first_name'];
			$subscriptions[0]['subscriber_last_name']=$subscriber_info[0]['subscriber_last_name'];
			$subscriptions[0]['subscriber_state']=$subscriber_info[0]['subscriber_state'];
			$subscriptions[0]['subscriber_zip_code']=$subscriber_info[0]['subscriber_zip_code'];
			$subscriptions[0]['subscriber_country']=$subscriber_info[0]['subscriber_country'];
			$subscriptions[0]['subscriber_city']=$subscriber_info[0]['subscriber_city'];
			$subscriptions[0]['subscriber_company']=$subscriber_info[0]['subscriber_company'];
			$subscriptions[0]['subscriber_dob']=$subscriber_info[0]['subscriber_dob'];
			$subscriptions[0]['subscriber_phone']=$subscriber_info[0]['subscriber_phone'];
			$subscriptions[0]['subscriber_address']=$subscriber_info[0]['subscriber_address'];
			$subscriptions[0]['subscriber_extra_fields']=$subscriber_info[0]['subscriber_extra_fields'];
			$subscriptions[0]['subscriber_date_added']=$subscriber_info[0]['subscriber_date_added'];
			$subscriptions[0]['subscriber_id']=$subscriber_info[0]['subscriber_id'];
			$subscriptions[0]['subscriber_status']=$subscriber_info[0]['subscriber_status'];
			$subscriptions[0]['soft_bounce']=$subscriber_info[0]['soft_bounce'];
			$subscriptions[0]['subscrber_bounce']=$subscriber_info[0]['subscrber_bounce'];
			$subscriptions[0]['is_signup']=$subscriber_info[0]['is_signup'];
		}

		if(($subscriptions[0]['subscrber_bounce']==2)||(($subscriptions[0]['subscrber_bounce']==1)&&($subscriptions[0]['soft_bounce']>$max_soft_bounce))){
			$subscriptions[0]['subscriber_status']=3;
		}
		if($_SERVER['HTTP_REFERER']!=base_url()."upgrade_package_cim/index"){
			$this->session->set_userdata('HTTP_REFERERS', $_SERVER['HTTP_REFERER']);
			$previous_page_url=$_SERVER['HTTP_REFERER'];
		}else{
			$previous_page_url=$this->session->userdata('HTTP_REFERERS');
		}
		$previous_page_url="";
		$soft_bounce=$subscriptions[0]['soft_bounce'];
		foreach($email_report as $key=>$campaign_report){
			$fetch_condiotions_array=array(
				'campaign_id'=>$campaign_report['campaign_id'],
				'counter >'=>0,
				'subscriber_id'=>$subscriber_id
			);
			// Fetches subscriber data from database
			$emailreport_data[$campaign_report['campaign_id']]=$this->Emailreport_Model->get_emailreport_click($fetch_condiotions_array);
			# Count clicks of all url
			$counter=0;
			foreach($emailreport_data[$campaign_report['campaign_id']] as $click){
				$counter+=$click['cnt'];
			}
			$email_report[$key]['clicks']=$counter;
			if(($subscriptions[0]['subscrber_bounce']==1)&&($subscriptions[0]['soft_bounce']>3)){
				$email_report[$key]['soft_bounce_status']=$soft_bounce;
				$soft_bounce=$subscriptions[0]['soft_bounce']--;
			}
		}
		#Get shoreten url
		$shorten_url=get_shorten_url();
		// get Email Report/History
		$strHistory = $this->ajaxHistory($subscriptions[0]['subscriber_id'], $subscriptions[0]['soft_bounce'], $subscriptions[0]['subscrber_bounce']);
		//Loads  subscriber  view.
		$this->load->view('header',array('title'=>'Subscriber View','previous_page_url'=>$previous_page_url));
		$this->load->view('contacts/subscriber_view',array('subscriptions'=>$subscriptions,'contact_soft_bounce'=>$subscriptions[0]['soft_bounce'],'contact_bounce_status'=>$subscriptions[0]['subscrber_bounce'],'email_report'=>$email_report,'email_report_view'=>1,'subscription_title'=>$subscription_title,'email_report_click'=>$emailreport_data,'shorten_url'=>$shorten_url,'max_soft_bounce'=>$max_soft_bounce,'contact_history'=>$strHistory));
		$this->load->view('footer-inner-red');
	}
	/**
		Function display_email_track_from_backup to display stat from backup table
		@param int campaign_id: campaign id
	*/
	function display_email_track_from_backup($campaign_id=0){
		
		$campaign_stat=$this->Campaign_email_track_restorage_Model->fetch_email_report_from_backup(array('campaign_id'=>$campaign_id));
		$total_unread_emails=$total_delivered_emails-$total_read_emails-$total_bounce_emails;
		$emailreport['total_unsubscribes']=$campaign_stat[0]['unsubscribes_email_count'];	# total unsubscribes email
		$emailreport['total_delivered_emails']=$campaign_stat[0]['send_email_count'];	# total delivered emails
		$emailreport['total_read_emails']=$campaign_stat[0]['read_email_count'];	# total read emails
		$emailreport['total_bounce_emails']=$campaign_stat[0]['bounce_email_count'];# total bounce emails
		$emailreport['total_unread_emails']=$emailreport['total_delivered_emails']-$emailreport['total_read_emails']-$emailreport['total_bounce_emails'];	#total unread emails
		$emailreport['total_click_emails']=$campaign_stat[0]['click_link_count'];	# total click emails
		$emailreport['email_track_bounce']=$campaign_stat[0]['bounce_email_count'];	# total bounce emails
		$emailreport['email_track_forward']=$campaign_stat[0]['forward_email_count'];	# total forward emails
		$emailreport['total_complaint_emails']=$campaign_stat[0]['complaint_email_count'];# total complaint emails

		$emailreport['per_read_emails']=0;
		$emailreport['per_bounce_emails']=0;
		$emailreport['per_unread_emails']=0;
		if($emailreport['total_delivered_emails']>0){
			#calculate percentage
			$emailreport['per_read_emails']=($emailreport['total_read_emails']/$emailreport['total_delivered_emails'])*100;
			$emailreport['per_bounce_emails']=($emailreport['total_bounce_emails']/$emailreport['total_delivered_emails'])*100;
			$emailreport['per_unread_emails']=($emailreport['total_unread_emails']/$emailreport['total_delivered_emails'])*100;
		}
		return $emailreport;
	}
	/**
		Function view_email_detail_from_backup is to display list of subscriber list
	*/
	function view_email_detail_from_backup($campaign_array=0,$action=""){
		# Load campaign_email_track_restorage model class which handles database interaction
		$this->load->model('newsletter/Campaign_email_track_restorage_Model');
		$campaign_stat=$this->Campaign_email_track_restorage_Model->fetch_email_report_from_backup(array('campaign_id'=>$campaign_array['campaign_id']));
		$sent_total_count = $campaign_stat[0]['send_email_count'];
		$read_total_count = $campaign_stat[0]['read_email_count'];		
		$bounced_total_count = $campaign_stat[0]['bounce_email_count'];
		$unread_total_count = $sent_total_count - $read_total_count - $bounced_total_count;
		$clicks_total_count = $campaign_stat[0]['click_link_count'];
		$forward_total_count = $campaign_stat[0]['forward_email_count'];
		$unsubscribes_total_count = $campaign_stat[0]['unsubscribes_email_count'];
		$complaints_total_count = $campaign_stat[0]['complaint_email_count'];
		if($action=="sent"){
			$email_address_array=explode(",",$campaign_stat[0]['send_email_address_list']);
			$i=0;
			foreach($email_address_array as $email_address){
				$emailreport_data[$i]['subscriber_email_address']=$email_address;
				$i++;
			}
			$current_tab="send_email";
			$previous_page_url=$this->session->userdata('HTTP_REFERER_EMAIL');
			# Load view
			$this->load->view('header',array('title'=>'Sent Mail Report','previous_page_url'=>$previous_page_url));
			$this->load->view('emailreport/emailreport_subscriber',array('emailreport_data'=>$emailreport_data,'current_tab'=>$current_tab,'campaign_id'=>$campaign_array['campaign_id'],'campaign_data'=>$campaign_array,'paging_links'=>$paging_links, 'sent_total_count'=>$sent_total_count, 'read_total_count'=>$read_total_count, 'bounced_total_count'=>$bounced_total_count, 'unread_total_count'=>$unread_total_count, 'unsubscribes_total_count'=>$unsubscribes_total_count, 'complaints_total_count'=>$complaints_total_count, 'clicks_total_count'=>$clicks_total_count, 'forward_total_count'=>$forward_total_count));
			$this->load->view('footer-inner-red');
		}else if($action=="read"){
			$email_address_array=explode(",",$campaign_stat[0]['read_email_address_list']);
			$i=0;
			foreach($email_address_array as $email_address){
				$emailreport_data[$i]['subscriber_email_address']=$email_address;
				$i++;
			}
			$current_tab="read_email";
			$previous_page_url=$this->session->userdata('HTTP_REFERER_EMAIL');
			# Load view
			$this->load->view('header',array('title'=>'Sent Mail Report','previous_page_url'=>$previous_page_url));
			$this->load->view('emailreport/emailreport_subscriber',array('emailreport_data'=>$emailreport_data,'current_tab'=>$current_tab,'campaign_id'=>$campaign_array['campaign_id'],'campaign_data'=>$campaign_array,'paging_links'=>$paging_links, 'sent_total_count'=>$sent_total_count, 'read_total_count'=>$read_total_count, 'bounced_total_count'=>$bounced_total_count, 'unread_total_count'=>$unread_total_count, 'unsubscribes_total_count'=>$unsubscribes_total_count, 'complaints_total_count'=>$complaints_total_count, 'clicks_total_count'=>$clicks_total_count, 'forward_total_count'=>$forward_total_count));
			$this->load->view('footer-inner-red');
		}else if($action=="unread"){
			#fetch unread email address list
			$send_email_address_array=explode(",",$campaign_stat[0]['send_email_address_list']);
			$read_email_address_array=explode(",",$campaign_stat[0]['read_email_address_list']);
			$email_address_array=array_diff($send_email_address_array, $read_email_address_array);
			$i=0;
			foreach($email_address_array as $email_address){
				$emailreport_data[$i]['subscriber_email_address']=$email_address;
				$i++;
			}
			$current_tab="unread_email";
			$previous_page_url=$this->session->userdata('HTTP_REFERER_EMAIL');
			# Load view
			$this->load->view('header',array('title'=>'Sent Mail Report','previous_page_url'=>$previous_page_url));
			$this->load->view('emailreport/emailreport_subscriber',array('emailreport_data'=>$emailreport_data,'current_tab'=>$current_tab,'campaign_id'=>$campaign_array['campaign_id'],'campaign_data'=>$campaign_array,'paging_links'=>$paging_links, 'sent_total_count'=>$sent_total_count, 'read_total_count'=>$read_total_count, 'bounced_total_count'=>$bounced_total_count, 'unread_total_count'=>$unread_total_count, 'unsubscribes_total_count'=>$unsubscribes_total_count, 'complaints_total_count'=>$complaints_total_count, 'clicks_total_count'=>$clicks_total_count, 'forward_total_count'=>$forward_total_count));
			$this->load->view('footer-inner-red');
		}else if($action=="bounced"){
			$email_address_array=explode(",",$campaign_stat[0]['bounce_email_address_list']);
			$i=0;
			foreach($email_address_array as $email_address){
				$emailreport_data[$i]['subscriber_email_address']=$email_address;
				$i++;
			}
			$current_tab="bounced_email";
			$previous_page_url=$this->session->userdata('HTTP_REFERER_EMAIL');
			# Load view
			$this->load->view('header',array('title'=>'Sent Mail Report','previous_page_url'=>$previous_page_url));
			$this->load->view('emailreport/emailreport_subscriber',array('emailreport_data'=>$emailreport_data,'current_tab'=>$current_tab,'campaign_id'=>$campaign_array['campaign_id'],'campaign_data'=>$campaign_array,'paging_links'=>$paging_links, 'sent_total_count'=>$sent_total_count, 'read_total_count'=>$read_total_count, 'bounced_total_count'=>$bounced_total_count, 'unread_total_count'=>$unread_total_count, 'unsubscribes_total_count'=>$unsubscribes_total_count, 'complaints_total_count'=>$complaints_total_count, 'clicks_total_count'=>$clicks_total_count, 'forward_total_count'=>$forward_total_count));
			$this->load->view('footer-inner-red');
		}else if($action=="unsubscribes"){
			$email_address_array=explode(",",$campaign_stat[0]['unsubscribes_email_address_list']);
			$i=0;
			foreach($email_address_array as $email_address){
				$emailreport_data[$i]['subscriber_email_address']=$email_address;
				$i++;
			}
			$current_tab="unsubscribes_email";
			$previous_page_url=$this->session->userdata('HTTP_REFERER_EMAIL');
			# Load view
			$this->load->view('header',array('title'=>'Sent Mail Report','previous_page_url'=>$previous_page_url));
			$this->load->view('emailreport/emailreport_subscriber',array('emailreport_data'=>$emailreport_data,'current_tab'=>$current_tab,'campaign_id'=>$campaign_array['campaign_id'],'campaign_data'=>$campaign_array,'paging_links'=>$paging_links, 'sent_total_count'=>$sent_total_count, 'read_total_count'=>$read_total_count, 'bounced_total_count'=>$bounced_total_count, 'unread_total_count'=>$unread_total_count, 'unsubscribes_total_count'=>$unsubscribes_total_count, 'complaints_total_count'=>$complaints_total_count, 'clicks_total_count'=>$clicks_total_count, 'forward_total_count'=>$forward_total_count));
			$this->load->view('footer-inner-red');
		}else if($action=="complaints"){
			$email_address_array=explode(",",$campaign_stat[0]['complaint_email_address_list']);
			$i=0;
			foreach($email_address_array as $email_address){
				$emailreport_data[$i]['subscriber_email_address']=$email_address;
				$i++;
			}
			$current_tab="complaints_email";
			$previous_page_url=$this->session->userdata('HTTP_REFERER_EMAIL');
			# Load view
			$this->load->view('header',array('title'=>'Sent Mail Report','previous_page_url'=>$previous_page_url));
			$this->load->view('emailreport/emailreport_subscriber',array('emailreport_data'=>$emailreport_data,'current_tab'=>$current_tab,'campaign_id'=>$campaign_array['campaign_id'],'campaign_data'=>$campaign_array,'paging_links'=>$paging_links, 'sent_total_count'=>$sent_total_count, 'read_total_count'=>$read_total_count, 'bounced_total_count'=>$bounced_total_count, 'unread_total_count'=>$unread_total_count, 'unsubscribes_total_count'=>$unsubscribes_total_count, 'complaints_total_count'=>$complaints_total_count, 'clicks_total_count'=>$clicks_total_count, 'forward_total_count'=>$forward_total_count));
			$this->load->view('footer-inner-red');
		}else if($action=="click"){
			$emailreport_data=unserialize($campaign_stat[0]['click_link_list']);
			if(!$emailreport_data){
				$emailreport_data=array();
			}
			$current_tab="click_link";
			$this->session->set_userdata('HTTP_REFERER','http://'.$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL']);
			$previous_page_url=$this->session->userdata('HTTP_REFERER_EMAIL');
			# Load view
			$this->load->view('header',array('title'=>'Sent Mail Report','previous_page_url'=>$previous_page_url));

			$this->load->view('emailreport/emailreport_click',array('emailreport_data'=>$emailreport_data,'current_tab'=>$current_tab,'campaign_id'=>$campaign_array['campaign_id'],'campaign_data'=>$campaign_array, 'sent_total_count'=>$sent_total_count, 'read_total_count'=>$read_total_count, 'bounced_total_count'=>$bounced_total_count, 'unread_total_count'=>$unread_total_count, 'unsubscribes_total_count'=>$unsubscribes_total_count, 'complaints_total_count'=>$complaints_total_count, 'clicks_total_count'=>$clicks_total_count, 'forward_total_count'=>$forward_total_count));
			$this->load->view('footer-inner-red');
		}else if($action=="forwardemail"){
			$emailreport_forward_data=unserialize($campaign_stat[0]['forward_email_address_list']);
			if(!$emailreport_forward_data){
				$emailreport_forward_data=array();
			}
			$current_tab="forward_email";
			$previous_page_url=$this->session->userdata('HTTP_REFERER_EMAIL');
			# Load view
			$this->load->view('header',array('title'=>'Sent Mail Report','previous_page_url'=>$previous_page_url));

			$this->load->view('emailreport/emailreport_subscriber',array('emailreport_forward_data'=>$emailreport_forward_data,'current_tab'=>$current_tab,'campaign_id'=>$campaign_array['campaign_id'],'campaign_data'=>$campaign_array, 'sent_total_count'=>$sent_total_count, 'read_total_count'=>$read_total_count, 'bounced_total_count'=>$bounced_total_count, 'unread_total_count'=>$unread_total_count, 'unsubscribes_total_count'=>$unsubscribes_total_count, 'complaints_total_count'=>$complaints_total_count, 'clicks_total_count'=>$clicks_total_count, 'forward_total_count'=>$forward_total_count));
			$this->load->view('footer-inner-red');
		}
	}
	
	/**
	 *	Function Exportcsv
	 *
	 *	'Exportcsv' controller function for exporting csv file from stats data
	 *
	 *	@param (string) (action)  contains type of stats for which csv will be export
	 *	@param (int) (campaign_id)  campaign for which stats data is to be exported
	 */
	function exportcsv($action, $campaign_id, $url=''){
		//Check if user is not login then redirect to index page
		if($this->session->userdata('member_id')=='')
			redirect('user/index');

		//	Collect subscription id
		//Protecting MySQL from query string sql injection Attacks
		if(!is_numeric($campaign_id)){
			redirect('user/index');
		}
		
		if($action=="sent"){
			//$fetch_condiotions_array=array(	'campaign_id'=>$campaign_id,'user_id'=>$this->session->userdata('member_id'),'email_sent'=>1, 'email_track_bounce'=>0, 'email_track_unsubscribes'=>0);					
			$fetch_condiotions_array=array(	'campaign_id'=>$campaign_id,'user_id'=>$this->session->userdata('member_id'),'email_sent'=>1);					
			$emailreport_data=$this->Emailreport_Model->get_emailreport_subscriber($fetch_condiotions_array,$config['per_page'],$start);
		}elseif($action=="read"){
			$fetch_condiotions_array=array('campaign_id'=>$campaign_id,'user_id'=>$this->session->userdata('member_id'), 'email_track_read >'=>0);					
			$emailreport_data=$this->Emailreport_Model->get_emailreport_subscriber($fetch_condiotions_array,$config['per_page'],$start);
		}elseif($action=="unread"){
			$fetch_condiotions_array=array('campaign_id'=>$campaign_id,'user_id'=>$this->session->userdata('member_id'), 'email_track_read'=>0);	
			$emailreport_data=$this->Emailreport_Model->get_emailreport_subscriber($fetch_condiotions_array,$config['per_page'],$start);
		}elseif($action=="forwardemail"){
			$fetch_condiotions_array=array('campaign_id'=>$campaign_id,'user_id'=>$this->session->userdata('member_id'), 'email_track_forward >'=>0);
			$emailreport_data=$this->Emailreport_Model->get_emailreport_subscriber($fetch_condiotions_array,$config['per_page'],$start);
		}elseif($action=="click"){
			$fetch_condiotions_array=array('campaign_id'=>$campaign_id,'user_id'=>$this->session->userdata('member_id'), 'email_track_click >'=>0);
			$emailreport_data=$this->Emailreport_Model->get_emailreport_subscriber($fetch_condiotions_array,$config['per_page'],$start);
		}elseif($action=="clickurl"){
			$fetch_condiotions_array=array('ret.campaign_id'=>$campaign_id,  'tiny_url'=>$url);			
			$emailreport_data=$this->Emailreport_Model->get_emailreport_subscriber_click($fetch_condiotions_array,$config['per_page'],$start);
			
		}elseif($action=="unsubscribes"){
			$fetch_condiotions_array=array('campaign_id'=>$campaign_id,'user_id'=>$this->session->userdata('member_id'), 'email_track_unsubscribes >'=>0);
			$emailreport_data=$this->Emailreport_Model->get_emailreport_subscriber($fetch_condiotions_array,$config['per_page'],$start);
		}elseif($action=="bounced"){
			$fetch_condiotions_array=array('campaign_id'=>$campaign_id,'user_id'=>$this->session->userdata('member_id'), 'email_track_bounce >'=>0);
			$emailreport_data=$this->Emailreport_Model->get_emailreport_subscriber($fetch_condiotions_array,$config['per_page'],$start);
		}elseif($action=="complaints"){
			$fetch_condiotions_array=array('campaign_id'=>$campaign_id,'user_id'=>$this->session->userdata('member_id'), 'email_track_complaint'=>1);
			$emailreport_data=$this->Emailreport_Model->get_emailreport_subscriber($fetch_condiotions_array,$config['per_page'],$start);
		}elseif($action=="signup"){			
			$form_stats=$this->Signup_Model->get_signupform_stats(array('form_id'=>$campaign_id),$config['per_page'],$start);
			$emailreport_data=$this->Signup_Model->get_signupform_stats_contacts($form_stats,$this->session->userdata('member_id'));			
		}
		 
		//Create output string  with heading
		$csv_output_header="First Name,Last Name,Email Address,Address,Birthday,City,Company,Country,Phone,State,Zip Code,IP, Date Added";
		//$csv_output_header.="\n";
		$csv_output="\n";
		 
		$i=0;
		
	 
		//Append subscribers to csv output
		foreach($emailreport_data as $subscriber){
			$csv_output.=$subscriber['subscriber_first_name'].",";
			$csv_output.=$subscriber['subscriber_last_name'].",";
			$csv_output.=$subscriber['subscriber_email_address'].",";
			$csv_output.=$subscriber['subscriber_address'].",";
			$csv_output.=$subscriber['subscriber_dob'].",";
			$csv_output.=$subscriber['subscriber_city'].",";
			$csv_output.=$subscriber['subscriber_company'].",";
			$csv_output.=$subscriber['subscriber_country'].",";
			$csv_output.=$subscriber['subscriber_phone'].",";
			$csv_output.=$subscriber['subscriber_state'].",";
			$csv_output.=$subscriber['subscriber_zip_code'].",";
			$csv_output.=$subscriber['subscriber_ip'].",";
			$csv_output.=$subscriber['subscriber_date_added']."";
			  
			$csv_output.="\n";
		}
	 	$csv_output=$csv_output_header.$csv_output;
		//Create filename and send output headers
		$filename = $action."_".date("Y-m-d_H-i",time());
		header("Content-type: application/vnd.ms-excel");
		//header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header("Content-disposition: filename=".$filename.".csv");
		//print csv output
		print $csv_output;
		exit;
	}
	
	function ajx_setpagesize(){
	$ps = $this->input->post('ps');
	$this->session->set_userdata('ps', $ps);
	//echo $this->session->userdata('ps');
	}
}
?>
