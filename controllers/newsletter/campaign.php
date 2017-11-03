<?php
/**
  *	Controller class for campaigns
  *	It have controller functions for campaign management.
 */
class Campaign extends CI_Controller
{
	/**
	  *	Contructor for controller.
	  *	It checks user session and redirects user if not logged in
	 */
	private $confg_arr = array(); 
	function __construct(){
        parent::__construct();
		// check via common model
		if(!$this->is_authorized->check_user())
			redirect('user/index');
		// Create user's folders
		$this->is_authorized->createUserFiles();
		
		// Load libraries, models and helpers
		$this->load->library('upload');
		$this->load->model('newsletter/Page_Model');
		$this->load->model('newsletter/Campaign_Model');
		$this->load->model('newsletter/contact_model');
		$this->load->model('newsletter/subscription_Model');
		$this->load->model('UserModel');
		$this->load->model('ConfigurationModel');
		$this->confg_arr=$this->ConfigurationModel->get_site_configuration_data_as_array();
		if($this->confg_arr['maintenance_mode'] !='no'){
			redirect ("/site_under_maintenance/");
			exit;
		}
		
		$this->output->enable_profiler(false);

		// Get absolute path for uploading
		$user_dir = $this->session->userdata('member_id') % 1000;
		$this->upload_path= $this->config->item('user_public').$user_dir .'/'.$this->session->userdata('member_id');
		// Force SSL
		force_ssl();
	}

	/**
	 * Function Index
	 *
	 * function for listing of campaigns.
	 */
	function index($start=0){
		$thisMid = $this->session->userdata('member_id');
		// Delete Campaign which are not save(is_status=1)
		//$condition_array=array('is_status'=>1, 'campaign_created_by'=>$thisMid,  'DATEDIFF(campaign_date_added, CURDATE())>'=>1);
		$condition_array=array('is_status'=>1, 'campaign_created_by'=>$thisMid,  'campaign_date_added > DATE_ADD(concat(CURDATE(), " 00:00:00"), INTERVAL 1 DAY)'=>NULL);
		$this->Campaign_Model->delete($condition_array);

		$this->load->model('Lshare_Model');
		$isFirstTimeUser = $this->Lshare_Model->showSASTrackingCodeLead($thisMid);
		// Starts: Google Adword tracking by @pravin on 2015-07-05
		if($isFirstTimeUser != ''){
			$encodedTrackDetail = get_cookie('rc_rctrack');
			$decodedTrackDetail = $this->encrypt->decode($encodedTrackDetail);
			$arrTrackDetail = explode(':-:',$decodedTrackDetail);
			$thisSignupTime = $arrTrackDetail[0];
			$thisIp = $arrTrackDetail[1];
			$thisSource = $arrTrackDetail[2];
			$thisMedium = $arrTrackDetail[3];
			$thisCampaign = $arrTrackDetail[4];
			$this->db->query("insert ignore into red_member_track(member_id, date_added, signup_ip, utm_source, utm_medium, utm_campaign) values('$thisMid', '$thisSignupTime', '$thisIp', '$thisSource', '$thisMedium', '$thisCampaign')");
		}
		// Ends: Google adword tracking
		$this->load->view('header',array('title'=>'List Campaigns'));
		$this->display(0,false);
		$this->load->view('footer',array('isFirstTimeUser'=>$isFirstTimeUser));

		// Load LinkShare model class which handles Affiliate functions
		
		$this->Lshare_Model->showSASTrackingCodeSale($thisMid);
		 

	}


	function display($start=0,$is_ajax=true){
	
		// Get Maximum Contacts according to session package id
		$user_packages_array=$this->UserModel->get_user_packages(array('member_id'=>$this->session->userdata('member_id'),'is_deleted'=>0));
		$package_array=$this->UserModel->get_packages_data(array('package_id'=>$user_packages_array[0]['package_id']));		

		$campaign_data['package_max_contacts']= $package_array[0]['package_max_contacts'];

		// Function to get total contacts and contacts count list wise
		$subscriber_data=$this->display_subscriptions();
		$subscriber_count= $subscriber_data['sum_first_two_subscriber'];

		if($subscriber_count > $campaign_data['package_max_contacts']){
			$campaign_data['upgrade_package']=1;
			// attach Upgrade your package message with member
			$this->UserModel->attachMessage(array('member_id'=>$this->session->userdata('member_id'), 'message_id'=>2),array('member_id'=>$this->session->userdata('member_id'), 'message_id'=>2));
		}else{
			$this->UserModel->detachMessage(array('member_id'=>$this->session->userdata('member_id'), 'message_id'=>2));
			$campaign_data['upgrade_package']=0;
		}

		$fetch_condiotions_array=array('campaign_created_by'=>$this->session->userdata('member_id'),'rec.is_deleted'=>0,'is_status'=>0);
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'newsletter/campaign/display';
		$config['total_rows']=$this->Campaign_Model->get_shchedule_campaign_count($fetch_condiotions_array);
		$config['per_page']=25;
		$config['uri_segment']=4;
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
		$campaign_data['campaigns']=$this->Campaign_Model->get_shchedule_campaign_data($fetch_condiotions_array,$config['per_page'],$start);
		$campaign_data['active_campaign_count']=$this->Campaign_Model->get_campaign_count(array('campaign_created_by'=>$this->session->userdata('member_id'),'rec.is_deleted'=>0,'is_status'=>0,'campaign_status'=>'archived','campaign_sheduled <'=>date('Y-m-d H:i:s',now()),'campaign_sheduled IS NOT NULL'=>NULL));
		
		$campaign_data['ready_campaign_count']=$this->Campaign_Model->get_campaign_count(array('campaign_created_by'=>$this->session->userdata('member_id'),'rec.is_deleted'=>0,'is_status'=>0,'campaign_status'=>'ready','campaign_sheduled <'=>date('Y-m-d H:i:s',now()),'campaign_sheduled IS NOT NULL'=>NULL));
		
		$campaign_data['queueing_campaign_count']=$this->Campaign_Model->get_campaign_count(array('campaign_created_by'=>$this->session->userdata('member_id'),'rec.is_deleted'=>0,'is_status'=>0,'campaign_status'=>'queueing','campaign_sheduled <'=>date('Y-m-d H:i:s',now()),'campaign_sheduled IS NOT NULL'=>NULL));
                                    
                                  $campaign_data['sent_campaign_count'] = $this->Campaign_Model->get_campaign_count(array('campaign_created_by'=>$this->session->userdata('member_id'),'rec.is_deleted'=>0,'is_status'=>0,'campaign_status'=>'active','campaign_sheduled <'=>date('Y-m-d H:i:s',now()),'campaign_sheduled IS NOT NULL'=>NULL));
                                  $campaign_data['first_payment_date']  = '';
                                  $sqlfirstpaymentdate = "select transaction_date from red_member_transactions where status = 'SUCCESS' and amount_paid > 0 and user_id = " . $this->session->userdata('member_id') . " ORDER BY transaction_date asc LIMIT 1";
                                  $query =  $this->db->query($sqlfirstpaymentdate);
                                  
		
		if ($query->num_rows() == 1){
			$row = $query->row();
			$campaign_data['first_payment_date']  = $row->transaction_date;
                                   }
                                  $campaign_data['paid_campaign_count']=$this->Campaign_Model->get_campaign_count(array('campaign_created_by'=>$this->session->userdata('member_id'),'rec.is_deleted'=>0,'is_status'=>0,'campaign_sheduled >'=>$campaign_data['first_payment_date']));
                                  
                                  $campaign_data['free_first_campaign_msg'] = '';
                                  $campaign_data['paid_first_campaign_msg'] = '';
                                  
                                  if ($campaign_data['queueing_campaign_count'] <= 1 && $campaign_data['active_campaign_count'] == 0 && $campaign_data['sent_campaign_count'] == 0 && $campaign_data['ready_campaign_count'] <= 1){
                                      if ($campaign_data['paid_campaign_count'] == 1){
                                          $campaign_data['paid_first_campaign_msg'] = "<div class='info'>Thanks for scheduling your first campaign with us.  Since this is your first paid campaign with us, we wanted to introduce you to our slow-release process. To protect the sending reputation for all of our customers, your first campaign will be slow-released over a period of time.<li>Slow-releasing your campaign allows our automated system to track your bounces,complaints, and unsubscribes.</li><li>Having a high number of bounces, complaints, or unsubscribes, can cause seriously harm your sending reputation. </li><li>Your campaign may be paused if any of the below thresholds are met while your campaign is slow-released:</li><li>Bounces >= 5%</li><li>Complaints >= .5%</li><li>Unsubscribes >= 2.5%</li><br>Typically a first campaign without any issues is released over a 4-24 hour depending upon your list size. Future campaigns will be sent 4-5 times faster than this one unless a batch of new contacts is uploaded into your RedCappi account in which case slow release occurs again. Please email support@redcappi.com if you have questions.</div>";
                                      }
                                      else{
                                      $campaign_data['free_first_campaign_msg'] = "<div class='info'>Thanks for scheduling your first campaign with us.  Since this is your first campaign with us, sending may be a bit delayed as our support team reviews your campaign and ensures it complies with our sending policy.  Usually there aren’t any issues, but if your campaign has any issues, our support team will reach out via email. You’ll receive an email notification once your campaign has been sent.</div>";
                                      }
                                  }
                                  //when queing count = 1 and active = 0 and ready = <=1 and campaigns_sent_count = 0
                                  //$campaign_data['campaign_paid_count'] = 0;
                                  
                                  
		// Recieve any messages to be shown, when campaign is added or updated
		$messages=$this->messages->get();
		// Assign messages to array to be send to view.
		$campaign_data['messages'] =$messages;
		// Fetch user data from database
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));
		$campaign_data['extra']=$user_data_array[0];
		

		// Get shoreten url
		$shorten_url=get_shorten_url();
		// Convert to Local Time for display to users
		for($i=0; $i<count($campaign_data['campaigns']); $i++){
		$campaign_data['campaigns'][$i]['draftDate'] = ('0000-00-00 00:00:00' == $campaign_data['campaigns'][$i]['campaign_date_updated'])?$campaign_data['campaigns'][$i]['campaign_date_added']: $campaign_data['campaigns'][$i]['campaign_date_updated'];
		$campaign_data['campaigns'][$i]['draftDate'] = getGMTToLocalTime($campaign_data['campaigns'][$i]['draftDate'], $this->session->userdata('member_time_zone'));
		$campaign_data['campaigns'][$i]['campaign_date_added'] = getGMTToLocalTime($campaign_data['campaigns'][$i]['campaign_date_added'], $this->session->userdata('member_time_zone'));
		$campaign_data['campaigns'][$i]['campaign_date_updated'] = getGMTToLocalTime($campaign_data['campaigns'][$i]['campaign_date_updated'], $this->session->userdata('member_time_zone'));
		$campaign_data['campaigns'][$i]['campaign_sheduled'] = getGMTToLocalTime($campaign_data['campaigns'][$i]['campaign_sheduled'], $this->session->userdata('member_time_zone'));
		$campaign_data['campaigns'][$i]['email_send_date'] = getGMTToLocalTime($campaign_data['campaigns'][$i]['email_send_date'], $this->session->userdata('member_time_zone'));
		}
		
		// Loads header, campaign and footer view.
		if($is_ajax)
		echo $this->load->view('newsletter/campaign_list_ajax',array('paging_links'=>$paging_links,'subscriber_data'=>$subscriber_data,'campaign_data'=>$campaign_data,'shorten_url'=>$shorten_url), true);
		else
		$this->load->view('newsletter/campaign_list',array('paging_links'=>$paging_links,'subscriber_data'=>$subscriber_data,'campaign_data'=>$campaign_data,'shorten_url'=>$shorten_url));

	}



	/**
	 *	Function Create
	 *
	 *	'Create' controller function to create new campaign.
	 */
	function create($ctyp=''){

		//collect input data in an array
		$input_array=array('campaign_title'=>'Unnamed', 'campaign_created_by'=>$this->session->userdata('member_id'), // loign user id
		'campaign_theme_id'=>'-1', 'campaign_template_id'=>'-1', 'campaign_template_option'=>'3','campaign_color_theme_id'=>'-1', 'campaign_date_added'=>date('Y-m-d H:i:s', now()), 'campaign_content'=>NULL,'campaign_email_content'=>NULL,'campaign_text_content'=>NULL,	'email_subject'=>null,'sender_email'=>null,'sender_name'=>null,	'subscription_list'=>null,'click_url'=>NULL,'campaign_after_encode_url'=>NULL);
		
		// Sends form input data to database via model object
		$campaign_id=$this->Campaign_Model->create_campaign($input_array);
		// insert page in database for email editor
		$conditions_array=array("type='folder'");
		//Fetch max of right and position from pages table table for default
		$row=$this->Page_Model->getMax('right',$conditions_array);
		$right=$row[0]['getmax']+1;
		$row=$this->Page_Model->getMax('position',$conditions_array);
		$position=$row[0]['getmax']+1;
		//Prepare input array
		$input_array=array(	'title'=>'home','name'=>'home',	'meta_description'=>NULL,'meta_keyword'=>NULL,'site_id'=>$campaign_id,'page_position'=>1,'is_published'=>'yes','parent_id'=>1,'position'=>0,'`left`'=>$right,'`right`'=>$right+1,'level'=>2,'type'=>'default');
		//Insert record	for creating page
		$page_id=$this->Page_Model->create_page($input_array);
		// Collect email template information
		$email_template_info=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$campaign_id,'campaign_created_by'=>$this->session->userdata('member_id')));
		$insert_array=array('red_background_color_page_id'=>$page_id,'red_background_color_block_name'=>'footer_font_txt','red_background_color_block_content'=>'font-size::#2');
		$this->Campaign_Model->add_background_color_content($insert_array);
		// Redirect to campaign template option page
		redirect('newsletter/campaign_template_options/index/'.$campaign_id.'/'.$ctyp);
	}
	function create_diy_campaign(){	
		// Create campaign
		$campaign_id=$this->Campaign_Model->create_campaign(array('campaign_title'=>'Unnamed', 'campaign_created_by'=>$this->session->userdata('member_id'), 'campaign_theme_id'=>'-1', 'campaign_template_id'=>'-1', 'campaign_template_option'=>'3','campaign_color_theme_id'=>'-1', 'campaign_date_added'=>date('Y-m-d H:i:s', now()), 'campaign_content'=>NULL,'campaign_email_content'=>NULL,'campaign_text_content'=>NULL,	'email_subject'=>null,'sender_email'=>null,'sender_name'=>null,	'subscription_list'=>null,'click_url'=>NULL,'campaign_after_encode_url'=>NULL));
		 
		//Fetch max of right and position from pages table table for default
		$row=$this->Page_Model->getMax('right',array("type='folder'"));
		$right=$row[0]['getmax']+1;
		$row=$this->Page_Model->getMax('position',array("type='folder'"));
		$position=$row[0]['getmax']+1;
		 
		//Insert record	for creating page
		$page_id=$this->Page_Model->create_page(array(	'title'=>'home','name'=>'home',	'meta_description'=>NULL,'meta_keyword'=>NULL,'site_id'=>$campaign_id,'page_position'=>1,'is_published'=>'yes','parent_id'=>1,'position'=>0,'`left`'=>$right,'`right`'=>$right+1,'level'=>2,'type'=>'default'));
		// Collect email template information
		$email_template_info=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$campaign_id,'campaign_created_by'=>$this->session->userdata('member_id')));
		
		$this->Campaign_Model->add_background_color_content(array('red_background_color_page_id'=>$page_id,'red_background_color_block_name'=>'footer_font_txt','red_background_color_block_content'=>'font-size::#2'));
		// Redirect to DIY with blank editor
		redirect('newsletter/campaign/campaign_editor/'.$campaign_id);		
	}

	/**
	 *	Function Theme
	 *
	 *	'theme' controller function to select the theme.
	 */
	function theme(){
		// To check form is submittted for saving theme
		if($this->input->post('action')=='save'){
			// get campaign id
			$campaign_id=$this->input->get_post('campaign_id', TRUE);
			//check if campaign exist then update campaign in database
			if($campaign_id){
				// Retrieve data posted in form posted by user using input class
				$input_array=array(
				'campaign_theme_id'=>$this->input->get_post('red_theme_name', TRUE),
				'campaign_template_id'=>$this->input->get_post('red_template_name', TRUE),
				'campaign_template_option'=>'3'
				);
				// Update campaign by data posted by user
				$this->Campaign_Model->update_campaign($input_array,array('campaign_id'=>$campaign_id,'campaign_created_by'=>$this->session->userdata('member_id')));
				// Redirect to listing of campaigns
				redirect('newsletter/campaign/campaign_editor/'.$campaign_id);
			}
		}else{
		
		
		}
	}
  	 
	 
	/*
		'Dislay' controller function for listing of subscriptions.

	*/

	function display_subscriptions($start=0){
		$subscriber_data = array();
		$fetch_condiotions_array=array('subscription_created_by'=>$this->session->userdata('member_id'),'is_deleted'=>0,'subscription_status'=>1);

			$subscription_data['subscriptions']=$this->subscription_Model->get_subscription_data($fetch_condiotions_array);
			if(count($subscription_data['subscriptions']) > 0){
				foreach($subscription_data['subscriptions'] AS $subscription){
					$contact_data['subscription_title']=$subscription['subscription_title'];
					$contact_data['subscription_id']=$subscription['subscription_id'];

					$contact_data['total_subscriber']=$this->contact_model->get_contacts_count_in_list(array('subscriber_created_by'=>$this->session->userdata('member_id'),'subscriber_status'=>1,'is_deleted'=>0),$subscription['subscription_id']);
					// Collect all the values in an array for use it in view of my-account
					$subscriber_data['subscribers'][]=$contact_data;
					if($subscription['subscription_id'] < 0)
					$subscriber_data['sum_first_two_subscriber']	= $contact_data['total_subscriber']; // total Contacts
				}

			}

			return ($subscriber_data);

	}


	/*

		'Delete' controller function for deleting of campaign.

	*/



	function delete($id){
	//Fetch campaign data from database by campaign ID

		$campaign_array=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$id,'campaign_created_by'=>$this->session->userdata('member_id')));

		//Redirects user to listing page if user have not created this campaign or campaign does not exists

		if(!count($campaign_array))

		{
			// Assign  error message by message class

			$this->messages->add('Campaign does not exists or you have not created this campaign', 'error');

			// Redirect to listing of campaigns

			redirect('newsletter/campaign');

		}



		// Deletes campaign according to campaign ID

		$this->Campaign_Model->delete_campaign(array('campaign_id'=>$id));
		# Load emailreport model class which handles database interaction
		$this->load->model('newsletter/Emailreport_Model');
		$where_condition=array('campaign_id'=>$id);
		$this->Emailreport_Model->delete_emailqueue($where_condition);
		#############################
		# create activity log		#
		#############################
		// Load log activity model class which handles database interaction
		$this->load->model('Activity_Model');
		# create array for insert values in activty table
		$values=array('user_id'=>$this->session->userdata('member_id'),
					  'activity'=>'campaign_delete',
					  'campaign_id'=>$id
				);
		$this->Activity_Model->create_activity($values);
	}


	/*

		'Copy Archived' controller function to copy campaign from archived campaign



	*/

	function copy_archived($campaign_id=0,$copy_page_id=0){

		// Prepare array to send to view
		$campaign_data=array();

		if($campaign_id){

			$archivedCampaignId = $campaign_id;
			//Fetch list of archived campaign

			$fetch_conditions_array=array(
			'campaign_id'=>$archivedCampaignId,
			'campaign_created_by'=>$this->session->userdata('member_id')
			);

			$loaded_campaign=$this->Campaign_Model->get_campaign_data($fetch_conditions_array);

			//Redirects user to listing page if user have not created this campaign or campaign does not exists

			if(!count($loaded_campaign))

			{
				// Assign  error message by message class

				$this->messages->add('Campaign does not exists or you have not created this campaign', 'error');

				// Redirect to listing of campaigns

				redirect('newsletter/campaign');

			}
			// get page id
			$loaded_page=$this->Page_Model->get_page_data(array('site_id'=>$campaign_id,'is_autoresponder'=>0));
		}

		// Retrieve data posted in form posted by user using input class

		$input_array=array('campaign_title'=>$loaded_campaign[0]['campaign_title'],
			'campaign_created_by'=>$this->session->userdata('member_id'),
			'campaign_theme_id'=>$loaded_campaign[0]['campaign_theme_id'],
			'campaign_template_id'=>$loaded_campaign[0]['campaign_template_id'],
			'campaign_template_option'=>$loaded_campaign[0]['campaign_template_option'],
			'campaign_text_content'=>$loaded_campaign[0]['campaign_text_content'],			
			'campaign_outer_bg'=>$loaded_campaign[0]['campaign_outer_bg'],
			'campaign_content'=>$loaded_campaign[0]['campaign_content'],
			'campaign_email_content'=>$loaded_campaign[0]['campaign_email_content'],
			'campaign_color_theme_id'=>$loaded_campaign[0]['campaign_color_theme_id'],
			'campaign_status'=>'draft',
			'campaign_date_added'=> date('Y-m-d H:i:s', now()),		
		'email_subject'=>$loaded_campaign[0]['email_subject'],
		'sender_email'=> $loaded_campaign[0]['sender_email'],
		'sender_name'=>$loaded_campaign[0]['sender_name'],
		'subscription_list'=>null,
		'click_url'=>NULL,
		'campaign_after_encode_url'=>NULL
		);

		// Sends form input data to database via model object

		$campaign_id=	$this->Campaign_Model->create_campaign($input_array);

		$conditions_array=array("type='folder'");

		//Fetch max of right and position from pages table table for default
		$row=$this->Page_Model->getMax('right',$conditions_array);
		$right=$row[0]['getmax']+1;

		$row=$this->Page_Model->getMax('position',$conditions_array);
		$position=$row[0]['getmax']+1;

		//Prepare input array for home page
		$input_array=array(
			'title'=>'home',
			'name'=>'home',
			'site_id'=>$campaign_id,
			'page_position'=>1,
			'is_published'=>'yes',
			'parent_id'=>1,
			'position'=>0,
			'`left`'=>$right,
			'`right`'=>$right+1,
			'level'=>2,
			'type'=>'default',
			'is_autoresponder'=>0,
		);

		//Insert record	for home page
		$page_id=$this->Page_Model->create_page($input_array);

		#Copy colors
		$block_data_array=array();
		$block_all_data_count=$this->Campaign_Model->get_background_color_content_count(array('red_background_color_page_id'=>$loaded_page[0]['id']));
		$block_all_data=$this->Campaign_Model->get_background_color_content_data(array('red_background_color_page_id'=>$loaded_page[0]['id']),$block_all_data_count);
		for($i=0;$i<count($block_all_data);$i++)
		{
			$insert_array=array('red_background_color_page_id'=>$page_id,'red_background_color_block_name'=>$block_all_data[$i]['red_background_color_block_name'],'red_background_color_block_content'=>$block_all_data[$i]['red_background_color_block_content']);

			$this->Campaign_Model->add_background_color_content($insert_array);
		}
		if($loaded_campaign[0]['campaign_template_option']==3){
			$this->Page_Model->copyCampaignAssets($this->upload_path.'/email_templates/'.$archivedCampaignId, $this->upload_path.'/email_templates/'.$campaign_id);
			# archivedCampaignId and $newCampaignId
			// Redirect to campaign editor
			redirect('newsletter/campaign/campaign_editor/'.$campaign_id);
		}else if(($loaded_campaign[0]['campaign_template_option']==1)||($loaded_campaign[0]['campaign_template_option']==2)){
			// Redirect to campaign preview
			redirect('newsletter/campaign_template_options/campaign_preview/'.$campaign_id);
		}else{
			redirect('newsletter/campaign_template_options/index/'.$campaign_id);
		}
	}


	 
	function selected_subscribers($ajax=true,$subscription_id=0){
		$where_in=array();
		$subscriber_count=0;
		$fetch_condiotions_array=array('subscriber_created_by'=>$this->session->userdata('member_id'),'subscriber_status'=>1,'is_deleted'=>0);

		if($subscription_id){
			$where_in[]=$subscription_id;
			unset($_POST['subscriptions']);
			$_POST['subscriptions'][]=$subscription_id;

			$subscriber_count= $this->contact_model->get_contacts_count_in_selected_lists($fetch_condiotions_array,$_POST['subscriptions']);

		}else if(isset($_POST['subscriptions'])){

			$subscriber_count= $this->contact_model->get_contacts_count_in_selected_lists($fetch_condiotions_array,$_POST['subscriptions']);
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
		Function Campaign editor to create email
	*/
	function campaign_editor($id=0,$content_for='',$campaign_create=""){

		//Fetch campaign data from database by campaign ID
		$campaign_array=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$id,'campaign_created_by'=>$this->session->userdata('member_id')));

		//Redirects user to listing page if user have not created this campaign or campaign does not exists
		if(!count($campaign_array))
		{
			redirect('newsletter/campaign');
		}
		if((($campaign_array[0]['campaign_status']=='archived')&&(date('Y-m-d H:i:s', strtotime( $campaign_array[0]['campaign_sheduled']))<date("Y-m-d H:i:s")))||($campaign_array[0]['campaign_status']=='ready')||($campaign_array[0]['campaign_status']=='active')||($campaign_array[0]['campaign_status']=='unapproved') ){
			redirect('newsletter/campaign');
		}
		$this->session->set_userdata('email_template_id', $id);
		// Create array for use in view
		$email_template_data=array();
		if($campaign_create){
			$email_template_data['campaign_create']="copy";
		}

		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));
		$user_info=true;
		$str_user_detail_for_footer='<div style="padding:15px 15px 15px 15px;float:left;">';
		if($user_data_array[0]['company']){
			$str_user_detail_for_footer.="<span class='company_name'><b><span class='copyright'>&copy; </span>".$user_data_array[0]['company']."</b></span><br/>";
		}else{
			$str_user_detail_for_footer.="<span class='company_name'><b><span class='copyright'>&copy; </span>Company Name</b></span><br/>";
			$user_info=false;
		}
		$str_user_detail_for_footer.='<div style="float:left;width:100%;margin-left:18px;">';
		if($user_data_array[0]['address_line_1']){
			$str_user_detail_for_footer.='<span class="address">'.$user_data_array[0]['address_line_1'].' '.$user_data_array[0]['address_line_2'].'</span>';
		}else{
			$str_user_detail_for_footer.="<span class='address'>Street Address</span>";
			$user_info=false;
		}
		if($user_data_array[0]['city']){
			$str_user_detail_for_footer.='<span class="city"> | '.$user_data_array[0]['city'].'</span>';
		}else{
			$str_user_detail_for_footer.="<span class='city'> | City</span>";
			$user_info=false;
		}
		if($user_data_array[0]['state']){
			$str_user_detail_for_footer.='<span class="state">, '.$user_data_array[0]['state'].'</span> ';
		}else{
			$str_user_detail_for_footer.="<span class='state'>, State</span> ";
			$user_info=false;
		}if($user_data_array[0]['zipcode']){
			$str_user_detail_for_footer.='<span class="zip">'.$user_data_array[0]['zipcode'].'</span>';
		}else{
			$str_user_detail_for_footer.="<span class='zip'> Zip Code</span>";
			$user_info=false;
		}
		if($user_data_array[0]['country_name']){
			if($user_data_array[0]['country_id']==225){
				$country=$user_data_array[0]['country_code'];
			}elseif($user_data_array[0]['country_id']==245){
				$country=$user_data_array[0]['country_custom'];
			}else{
				$country=$user_data_array[0]['country_name'];
			}
			if($country !='USA' and $country !='United States')
			$str_user_detail_for_footer.='<span class="country"> | '.$country.'</span>';
			else
			$str_user_detail_for_footer.='<span class="country"></span>';
		}else{
			$str_user_detail_for_footer.="<span class='country'></span>";
			$user_info=false;
		}
		$str_user_detail_for_footer.="</div></div>";
		// Add email template ID to email_template_data array

		$email_template_data['email_template_id']=$id;
		$fetch_conditions_array=array(
		'site_id'=>$id,
		'is_deleted'=>'No',
		'is_autoresponder'=>'0'
		);
		$total_pages=$this->Page_Model->get_page_count($fetch_conditions_array);

		$email_template_data['pages']=$this->Page_Model->get_page_data($fetch_conditions_array,$total_pages);
		/*************theme background color information********************/

		$block_all_data_count=$this->Campaign_Model->get_background_color_blocks_names_and_content_count(array('red_background_color_page_id'=>$email_template_data['pages'][0]['id']));

		$email_template_color_info=$this->Campaign_Model->get_background_color_blocks_names_and_content_data(array('red_background_color_page_id'=>$email_template_data['pages'][0]['id']),$block_all_data_count);

		foreach ($email_template_color_info AS $email_template_color){
			$expl_color=explode(":#",$email_template_color['red_background_color_block_content']);
			if($email_template_color['red_background_color_block_name']=="outer-background"){
				$email_template_data['outer_background']=$expl_color[1];
			}else{
				$email_template_data[$email_template_color['red_background_color_block_name']]=$expl_color[1];
			}
		}

		$email_template_info=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$id));
		$email_template_data['email_template_info']=$email_template_info[0];
		$template_info=$this->Campaign_Model->get_template_data(array('template_id'=>$email_template_data['email_template_info']['campaign_template_id']));

		$email_template_data['template_info']=$template_info[0];
		$email_template_data['member_id']=$this->session->userdata('member_id');

		$template_default_data=array();

		#$template_base_path=base_url().'webappassets/email_templates/template';
		$template_base_path= $this->config->item('webappassets_path') .'email_templates/template';

		$template_path=$template_base_path.'/index.html';


		$email_template_data['template_info']['template_html']= @file_get_contents($template_path);
		$filtered_html=preg_replace('#(href|src)=(["\'])([.\/]*)([^:"\']*)(["\'])#',

					'$1="'.$template_base_path.'/$4"',$email_template_data['template_info']['template_html']);


		$body_empty_text='<p class="empty-text">Drag here</p>';
		$search_arr=array('{logo}','{website_name}','{body_top}','{body_main}','{body_bottom}','{body_left}','{body_right}','{FOOTER}');

		$replace_arr=array('<span class="logo_txt" >click to upload logo</span>','<span class="empty-text">click to add website name</span>',$body_empty_text,$body_empty_text,"",$body_empty_text,$body_empty_text,ucwords($str_user_detail_for_footer));
		$filtered_html=str_replace($search_arr,$replace_arr,$filtered_html);

		/******* if templete contents are displaying using ajax**************************/

		$email_template_data['template_info']['filtered_html']= $filtered_html;

		$email_template_data['email_template_info']['body_empty_text']=$body_empty_text;

		$templates_count=$this->Campaign_Model->get_template_count(array('rect.is_active'=>1,'rect.is_delete'=>0));
		#echo $templates_count;
		$template_data=$this->Campaign_Model->get_template_data(array('rect.is_active'=>1,'rect.is_delete'=>0),$templates_count);

		$email_template_data['template_data']=$template_data;



		//Count number of image bank
		$image_bank_count=$this->Campaign_Model->get_image_bank_count(array('img_is_status'=>1));
		//Get information of image bank
		$image_bank_data=$this->Campaign_Model->get_image_bank_data(array('img_is_status'=>1,'img_user_id'=>$this->session->userdata('member_id')),$templates_count);

		$image_bank_str='';
		$j=1;
		for($i=0;$i<count($image_bank_data);$i++)
		{
			$path_info=pathinfo($this->session->userdata('member_id').'/image_bank/'.$this->session->userdata('email_template_id').'/uploaded_images/'.$image_bank_data[$i]['img_name']);
			//thumb image path
			$thumb_image_path=$path_info['filename'].".".$path_info['extension'];
			$library_images[]=$thumb_image_path;
		}
		$email_template_data['library_images']=$library_images;
		$this->session->set_userdata('show_layout_container', 1);
		$email_template_data['show_layout_container']=0;
		if($this->session->userdata('show_layout_container')==1)
		{
			$email_template_data['show_layout_container']=1;
			$this->session->set_userdata('show_layout_container', 0);
		}
		$email_template_data['theme_css']=$this->get_theme_css($email_template_data['email_template_info']['campaign_color_theme_id']);

		$email_template_data['user_info']=$user_info;
		$email_template_data['user_data']=$user_data_array[0];
		//Fetch Country name
		$country_info=$this->UserModel->get_country_data();
		$email_template_data['country_info']=$country_info;
		#Get shoreten url
		$email_template_data['shorten_url']=get_shorten_url();
		//Loads header, campaign and footer view.		
		$this->load->view('newsletter/campaign_editor',$email_template_data);
	}
	function get_theme_colors(){

		//get themes color from database
		$theme_color=$this->Campaign_Model->get_theme_colors(array('is_delete'=>'0','is_active'=>'1'));
		$colors="";
		foreach ($theme_color as $color){
			$colors.='<tr id="'.$color['id'].'" onclick="saveColorTheme('.$color['id'].',this)" class="color_theme_link"><td>'.substr($color['theme_name'],0,15);
			if($color['member_id']!=-1){
				$colors.='<span class="close_link_span"><a  class="close-link theme_color_delete" id="theme_color_'.$color['id'].'"  ><i class="icon-remove-sign"></i></a></span>';
			}
			$colors.='</td>
					<td id="outer_bg_'.$color['id'].'" style="background:'.$color['outer_bg'].';width:13px;" class="color" class="outer_bg">&nbsp; </td>
					<td id="body_bg_'.$color['id'].'"  style="background:'.$color['body_bg'].';width:13px;" class="body_bg">&nbsp;</td>
					<td id="footer_bg_'.$color['id'].'"  style="background:'.$color['footer_bg'].';width:13px;" class="footer_bg">&nbsp;</td>
					<td id="border_color_'.$color['id'].'"  style="background:'.$color['border_color'].';width:13px;" class="border_color">&nbsp;</td>
					</tr>';
		}
		echo $colors;
	}
	function get_theme_css($template_id,$method=""){

		//get themes color from database
		$theme_color= $this->Campaign_Model->get_theme_colors(array('id'=>$template_id));

		$style="";
		foreach($theme_color[0] as $elem=>$color){
			if($elem!='id' && $elem!='theme_name'){
				$arr_elem=explode('_',$elem,2);
				if($arr_elem[1]=="bg"){
					if($arr_elem[0]=="header"){
						$style.="<style class='header_style custome_style'>#header{
							background-color:$color;
						}</style>
						";
					}else if($arr_elem[0]=="body"){
						$style.="<style class='body_main_style custome_style'>
							#body_main{
								background-color:$color;
							}
							.body_bg{background-color:$color;}
							.body_bg_color{background-color:$color;}
						</style>
						";
					}else if($arr_elem[0]=="outer"){
						$style.="<style class='main-table_style custome_style'>
							#main-table{
								background-color:$color;
							}
							html, body{background-color:$color;}
							.diy-editor{background-color:$color;}
							#template_container{background-color:$color;}
							.outer_bg{background-color:$color;}
						</style>
						";
					}else{
						if($elem=="footer_bg"){
							$style.="<style class='footer_style custome_style'>
								#footer{
									background-color:$color;
								}
								.footer_bg{
									background-color:$color;
								}
								.footer_txt_color{
									background-color:$color;
								}
							</style>";
						}
					}
				}else if($arr_elem[1]=="color"){
					$style.="<style class='border_style custome_style'>
						#email_template_table{
							border-color:  $color;
						}
						.body_border {
							background-color:  $color;
						}
					</style>
					";
				}else if($elem=="footer_font_color"){
					if($color !='#'){
						$style.="<style class='footer_font_style custome_style'>
							#footer{
								color:  $color ;
							}
						</style>
						";
					}
				}else if($elem=="preheader_font_color"){
					if($color !='#'){
						$style.="<style class='preheader_font_color custome_style'>
						.preheader-text, .preheader-link {
							color:  $color;
						}
						</style>
						";
					}
				}
			}
		}
		if($method=="ajax"){
			echo $style;
		}else{
			return $style;
		}
	}
	/*********Function to cahnge the selected template**************/
	function change_template($template_id,$email_campaign_id){

		$email_template_id=$email_campaign_id; // emial template id

		//Fetch campaign data from database by campaign ID
		$campaign_array=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$email_campaign_id,'campaign_created_by'=>$this->session->userdata('member_id')));

		//Redirects user to listing page if user have not created this campaign or campaign does not exists
		if(!count($campaign_array))
		{
			redirect('newsletter/campaign');
		}
		//update email template id in campaign table
		$this->Campaign_Model->update_campaign(array('campaign_template_id'=>$template_id,'campaign_template_option'=>3),array('campaign_id'=>$email_template_id));

		$this->session->set_userdata('show_layout_container', 1);
		echo $email_template_id;
	}
	/*********Function to cahnge the selected theme**************/
	function change_theme($template_id,$email_campaign_id,$theme_id=0){

		$email_template_id=$email_campaign_id; // emial template id
		//Fetch campaign data from database by campaign ID
		$campaign_array=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$email_campaign_id,'campaign_created_by'=>$this->session->userdata('member_id')));

		//Redirects user to listing page if user have not created this campaign or campaign does not exists
		if(!count($campaign_array))
		{
			redirect('newsletter/campaign');
		}
		if($theme_id){
		//update email template id in campaign table
		$this->Campaign_Model->update_campaign(array('campaign_template_id'=>$template_id,'campaign_template_option'=>3,'campaign_theme_id'=>$theme_id),array('campaign_id'=>$email_template_id));
		}else{
		$this->Campaign_Model->update_campaign(array('campaign_color_theme_id'=>$template_id),array('campaign_id'=>$email_template_id));
		}
		$this->session->set_userdata('show_layout_container', 1);
		echo $email_template_id;
	}

	/**********Function to display all the email template screenshots**********/
	function get_template_data_for_ajax($campaign_id=0){


		//Fetch campaign data from database by campaign ID
		$campaign_array=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$campaign_id,'campaign_created_by'=>$this->session->userdata('member_id')));

		//Redirects user to listing page if user have not created this campaign or campaign does not exists
		if(!count($campaign_array))
		{
			redirect('newsletter/campaign');
		}
		//Count number of templates
		$templates_count=$this->Campaign_Model->get_campaign_template_count(array('rect.is_active'=>1,'rec.campaign_id'=>$campaign_id));

		//Get information of email template
		$template_data=$this->Campaign_Model->get_campaign_template_data(array('rect.is_active'=>1,'rec.campaign_id'=>$campaign_id),$templates_count);

		$template_str='';
		$j=1;
		for($i=0;$i<count($template_data);$i++)
		{
			//template screenshot path
			$template_img_path=base_url().'webappassets/email_templates/'.$template_data[$i]['template_name'].'/'.$template_data[$i]['screenshot'];
			$template_name=$template_data[$i]['template_name']; //template name
			$template_id=$template_data[$i]['template_id']; // template id

			$template_img='<a onclick="previewTemplate(\''.$template_name.'\','.$template_id.',this)"><img style="width:36px; height:36px;"  src="'.$template_img_path.' " alt=""></a>';
			$template_str.='<li '.$class.'>'.$template_img.$template_links.'</li>';
			$j++;
		}
		echo $template_str;
	}
/**********Function to display all the email template for theme************************/
	function get_template_data_for_theme($theme_id=0){


		//Count number of templates
		$templates_count=$this->Campaign_Model->get_template_count(array('rect.is_active'=>1,'rect.is_delete'=>0,'rect.template_theme_id !='=>-1));
		//Get information of email template
		$template_data=$this->Campaign_Model->get_template_data(array('rect.is_active'=>1,'rect.is_delete'=>0,'rect.template_theme_id !='=>-1),$templates_count);
		//,'rect.show_on_dashboard'=>1
		if($theme_id){
			$themes_arr=array();
			foreach($template_data as $theme){
				$category_arr=explode(',',$theme['template_theme_id']);
				if (in_array($theme_id, $category_arr)) {
					$themes_arr[]=$theme;
				}
			}
			unset($template_data);
			$template_data=$themes_arr;
		}

		$template_str='';
		$j=1;
		for($i=0;$i<count($template_data);$i++)
		{
			if($template_data[$i]['template_id']!=-1){
				if($theme_id>0){
					//template screenshot path
					$template_img_path=$this->config->item('webappassets').'header-images/header-'.$template_data[$i]['template_id'].'.jpg';
					$template_name=$template_data[$i]['template_name']; //template name
					$template_id=$template_data[$i]['template_id']; // template id
					$template_theme_id=$template_data[$i]['template_theme_id']; // theme id
					$arr_template_theme_id=@explode(',',$template_theme_id); // theme id
					$template_theme_id = $arr_template_theme_id[0];

					$template_img='<div class="preview"><a href="javascript:;" onclick="saveTemplate(\''.$template_name.'\','.$template_id.','.$template_theme_id.')" class="banner-highlight"><img  src="'.$template_img_path.' " width="595" alt=""></a></div>';
					$selectBannerButton = "<a href='javascript:;' onclick='saveTemplate(\"$template_name\",\"$template_id\",\"$template_theme_id\")' class='btn select'>Select</a></div></div>";


					$template_str.='<li class="article">'.$template_img.$template_links.$selectBannerButton.'</li>';
				}else if($template_data[$i]['show_on_dashboard']==1){
					//template screenshot path
					$template_img_path=$this->config->item('webappassets').'header-images/header-'.$template_data[$i]['template_id'].'.jpg';
					$template_name=$template_data[$i]['template_name']; //template name
					$template_id=$template_data[$i]['template_id']; // template id
					$template_theme_id=$template_data[$i]['template_theme_id']; // theme id
					$arr_template_theme_id=@explode(',',$template_theme_id); // theme id
					$template_theme_id = $arr_template_theme_id[0];

					$template_img='<div class="preview"><a href="javascript:;" onclick="saveTemplate(\''.$template_name.'\','.$template_id.','.$template_theme_id.')"><img src="'.$template_img_path.' " width="595" alt=""></a></div>';

					$selectBannerButton = "<a href='javascript:;' onclick='saveTemplate(\"$template_name\",\"$template_id\",\"$template_theme_id\")' class='btn select'>Select</a>";



					$template_str.='<li class="article">'.$template_img.$template_links.$selectBannerButton.'</li>';
				}
			}
			$j++;
		}
		echo $template_str;
	}
/**
	Function to display all the email template screenshots
**/
	function get_theme_data_for_ajax($category_id=0)
	{

		//Count number of templates
		$templates_count=$this->Campaign_Model->get_theme_count(array('rect.red_is_active'=>1,'rect.red_is_delete'=>0,'rect.red_theme_id !='=>-1));
		$template_str='';
		$j=1;
		//Get information of email theme
		$campaign_data['theme_data']=$this->Campaign_Model->get_theme_data(array('rect.red_is_active'=>1,'rect.red_is_delete'=>0,'rect.red_theme_id !='=>-1),$templates_count);
		$template_str.="<span class='category_class'> Select Category: ";
		$template_str.="<select onchange='changeTheme()' id='category_list' >";
		$template_str.="<option>Select Theme</option>";
		foreach($campaign_data['theme_data'] as $theme_info){
			if(($category_id>0)&&($category_id==$theme_info['red_theme_id'])){
				$templates_count=$this->Campaign_Model->get_template_count(array('rect.is_active'=>1,'rect.is_delete'=>0,'rect.template_theme_id !='=>-1));
				$template_data=$this->Campaign_Model->get_template_data(array('rect.is_active'=>1,'rect.is_delete'=>0,'rect.template_theme_id !='=>-1),$templates_count);
				if($templates_count>0){
					$themes_arr=array();
					foreach($template_data as $theme){
						$category_arr=explode(',',$theme['template_theme_id']);
						if (in_array($category_id, $category_arr)) {
							$themes_arr[]=$theme;
						}
					}
					unset($template_data);
					$template_data=$themes_arr;
				}
				$campaign_data['template_data'][$theme_info['red_theme_id']]=$template_data;
				unset($template_data);
			}
			if(($category_id>0)&&($category_id==$theme_info['red_theme_id'])){
				$template_str.='<option value="'.$theme_info['red_theme_id'].'" selected="selected">'.$theme_info['red_theme_name'].'</option>';
			}else{
				$template_str.='<option value="'.$theme_info['red_theme_id'].'">'.$theme_info['red_theme_name'].'</option>';
			}
		}

		$template_str.="</select></span>";
		if($category_id<=0){
			$templates_count=$this->Campaign_Model->get_template_count(array('rect.is_active'=>1,'rect.is_delete'=>0,'rect.template_theme_id !='=>-1,'rect.show_on_dashboard'=>1));
			$template_data=$this->Campaign_Model->get_template_data(array('rect.is_active'=>1,'rect.is_delete'=>0,'rect.template_theme_id !='=>-1,'rect.show_on_dashboard'=>1),$templates_count);

			$campaign_data['template_data'][-1]=$template_data;
		}

		foreach($campaign_data['template_data'] as $key=>$theme_data){
			$template_str.='<div  class="div_template"><ul class="themes_ul">';
			foreach($theme_data as $theme){
				//template screenshot path
				$template_img_path=$this->config->item('webappassets').'header-images/header-'.$theme['template_id'].'.jpg';
				$template_id=$theme['template_id']; //template id
				$theme_id=$key; // template category  id
				$template_img='<img onclick="saveHeader(\''.$template_id.'\')" src="'.$template_img_path.'" border="0" alt="" />';
				$template_str.='<li '.$class.' >'.$template_img.$template_links.'</li>';
				$j++;
			}
			$template_str.='</ul></div>';
		}
		echo  $template_str;
	}
	/**********Function to display all the image  bank images************************/
	function get_image_bank_for_ajax()
	{


		//Count number of image bank
		$image_bank_count=$this->Campaign_Model->get_image_bank_count(array('img_is_status'=>1,'img_is_delete'=>0,'img_user_id'=>$this->session->userdata('member_id')));

		//Get information of image bank
		$image_bank_data=$this->Campaign_Model->get_image_bank_data(array('img_is_status'=>1,'img_is_delete'=>0,'img_user_id'=>$this->session->userdata('member_id')),$image_bank_count);

		$image_bank_str='';
		$j=1;
		if(count($image_bank_data)>0){
			for($i=0;$i<count($image_bank_data);$i++)
			{
				$path_info=pathinfo($this->session->userdata('member_id').'/image_bank/'.$this->session->userdata('email_template_id').'/uploaded_images/'.$image_bank_data[$i]['img_name']);
				//thumb image path
				$thumb_image_path=base_url().'asset/user_files/'.$this->session->userdata('member_id').'/image_bank/'.$path_info['filename'].".".$path_info['extension'];
				$img_path=base_url().'asset/user_files/'.$this->session->userdata('member_id').'/image_bank/'.$path_info['filename'].".".$path_info['extension'];
				// Check if file exists or not
				if(file_exists(str_replace(base_url().'asset/user_files/'.$this->session->userdata('member_id').'/',$this->upload_path.'/', $img_path))){

					list($width, $height, $type, $attr) = getimagesize(str_replace(base_url().'asset/user_files/'.$this->session->userdata('member_id').'/',$this->upload_path.'/', $img_path));
					$image_bank_img=' <div class="image_bank_div"><img class="image_bank draggable1"  src="'.$thumb_image_path.'" name="'.$img_path.','.$width.','.$height.'" style="width:100px" alt="></div>';
					$class="class='li_draggable' ";
					$img_remove='<div  class="del_image_link"><a href="javascript:void(0);"  class="remove-img-link image_bank_unlink" id="'.$image_bank_data[$i]['img_id'].'"><i class="icon-remove-sign"></i></a></div>';
					$image_bank_str.='<li '.$class.'  title="Click & Drag" ><div  class="img_slide">'.$img_remove.$image_bank_img.$image_bank_links.'</div></li>';
					$j++;
				}//check for Image file exists or not
			}
		}else{
			$image_bank_str='<li class="load_images">
								<b>Click "Upload Images" button to upload your images</b>
							</li>';
		}
		echo $image_bank_str;
	}

	/**
		Function to call information of selected template using ajax
	*/
	function get_selected_template_for_ajax($id){
		//Get content of email template
		$filtered_html=$this->campaign_editor($id,'ajax_content');
		echo $filtered_html;
	}
	/**
		Preview of email template
	*/
	function email_template_view($id=0,$page_id=0)
	{
		$email_template_data=array();//Preapare array for email template data


		//Fetch campaign data from database by campaign ID
		$campaign_array=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$id,'campaign_created_by'=>$this->session->userdata('member_id')));

		//Redirects user to listing page if user have not created this campaign or campaign does not exists
		if(!count($campaign_array))
		{
			redirect('newsletter/campaign');
		}
		$email_template_data['email_template_id']=$id;  // id of email template

		// Where condition
		$fetch_conditions_array=array(
		'site_id'=>$id,
		'is_deleted'=>'No',
		);

		//Get total number of pages in email template
		$total_pages=$this->Page_Model->get_page_count($fetch_conditions_array);

		//Get info of each page like page name ,position, id and title
		$email_template_data_pages=$this->Page_Model->get_page_data($fetch_conditions_array,$total_pages);
		if($page_id==0)
		{
			$page_id=$email_template_data_pages[0]['id'];  //Page id
		}
		//Get email template content
		$email_template_info=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$id));

		$email_template_data['email_template_info']=$email_template_info[0];

		//Get selected template information
		$template_info=$this->Campaign_Model->get_template_data(array('template_id'=>$email_template_data['email_template_info']['campaign_template_id']));

		$email_template_data['template_info']=$template_info[0];

		//Selected email template path
#		$template_base_path=base_url().'webappassets/email_templates/'.$email_template_data['template_info']['template_name'];
		$template_base_path= $this->config->item('webappassets_path') .'email_templates/'.$email_template_data['template_info']['template_name'];
		//Selected email template file path
		$template_path=$template_base_path.'/index.html';

		//Collect selected email template content
		$email_template_data['template_info']['template_html']=file_get_contents($template_path);

		$filtered_html=preg_replace('#(link href|src)=(["\'])([.\/]*)([^:"\']*)(["\'])#',

					'$1="'.$template_base_path.'/$4"',$email_template_data['template_info']['template_html']);

		$page_content_arr=array();

		//Get data of each block according to page id
		$page_content_data=$this->Campaign_Model->get_template_blocks_content_data(array('page_id'=>$page_id));

		//Store data of block  in variable according to each block name
		for($i=0;$i<count($page_content_data);$i++)
		{
			$page_content_arr[$page_content_data[$i]['block_name']]=$page_content_data[$i]['block_content'];
		}

		//Search block name
		$search_arr=array('{logo}','{website_name}','{body_top}','{body_main}','{body_bottom}','{body_left}','{body_right}','{footer}');

		//Replace block content according to block name
		$replace_arr=array($page_content_arr['logo'],$page_content_arr['website_name'],$page_content_arr['body_top'],$page_content_arr['body_main'],$page_content_arr['body_bottom'],$page_content_arr['body_left'],$page_content_arr['body_right'],$page_content_arr['footer']);

		$filtered_html=str_replace($search_arr,$replace_arr,$filtered_html);

		//Create link for each page
		for($i=0;$i<count($email_template_data_pages);$i++)
		{
			$p_id=$email_template_data_pages[$i]['id'];
			$title=$email_template_data_pages[$i]['title'];
			if($p_id==$page_id) $class='current';else $class='';
			$li_str.='<li><a href="'.base_url().'diy/website/view/'.$id.'/'.$p_id.'" class="'.$class.'">'.$title.'</a></li>';
		}
		$ul_str='<ul id="pages_menu">'.$li_str.'</ul>';
		$filtered_html=str_replace('<ul id="pages_menu"><li><a href="#" class="current">Home</a></li></ul>',$ul_str,$filtered_html);
		$email_template_data['template_info']['filtered_html']= $filtered_html;

		//Load view of email template
		$this->load->view('newsletter/email_template_view',$email_template_data);
	}

	/**
		Function to copy  files in folder
	**/
	function recurse_copy($src,$dst)
	{
		#Open source directory
		if($dir = opendir($src)){
			@mkdir($dst); // create destination directory

			#copy from from src directory to destination directory
			while(false !== ( $file = readdir($dir)) ) {
				if (( $file != '.' ) && ( $file != '..' )) {
					if ( is_dir($src . '/' . $file) ) {
						$this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
					}
					else {
						copy($src . '/' . $file,$dst . '/' . $file);
						chmod($dst . '/' . $file,0777);
					}
				}
			}
			closedir($dir);// close directory
		}
	}
	/**
		function to share a link on facebook
	**/
	function share_link($id=0){
		#Get shoreten url
		$shorten_url=get_shorten_url();
		echo "<div style='width:100%;float:left;'><div style='float:none;margin:20px;text-align:center;color:#000;'><div style='margin-bottom:5px;'><b>Copy & Paste URL below to Facebook</b></div><div><b> page or any other sharing medium.</b></div></div>";
		echo "<div style='float:none;margin:5px;'><input type='text' value='".CAMPAIGN_DOMAIN."c/".$id."' style='width:520px;' /></div><div style='float:none;margin:13px;text-align:center;'><img src='".base_url()."webappassets/images/facebook-share.png' alt='Share on facebook'></div></div>";
	}
	/**
		Function to send notification email to admin for schdule campaigns
	**/
	function notification_email($campaign_id=0){
		//Get email template content
		$email_template_info=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$campaign_id));
		$email_msg="";
		$email_msg.="<p>Hello admin,</p>";
		$email_msg.="<p>Campaign :".$email_template_info[0]['campaign_title']." is ready to sent</p>";
		$email_msg.="<p>Select a choice to allow or disallow it from admin</p>";
		$email_msg.='<p>Regards,</p>';
		$email_msg.='<p>Redcappi Team</p>';
		#Load Phpmail plugin for sending mail
		$this->load->helper('admin_notification');
		#set receiver of email
		$to=$this->get_Admin_notification_email();
		#set sender of email
		$sender=SYSTEM_EMAIL_FROM;
		$sender_name="RedCappi";
		#set subject of email
		$subject="Campaign Approval";
		#set message of email
		$message=$email_msg;
		$text_message=$email_msg;
		#send email
		admin_notification_send_email($to, $sender,$sender_name, $subject,$message,$text_message);
	}
	function get_Admin_notification_email(){
		$sql            = 'SELECT config_name,config_value FROM `red_site_configurations` where `config_name` = "admin_notification_email"';
		$query          = $this->db->query($sql);
		$admin_email	= "";
		if ($query->num_rows() == 1){
			$row = $query->row();
			$admin_email        = $row->config_value;
		}
		return $admin_email;
	}
	/**
		Function to send  notification email to admin for upgradation of package
	**/
	/* function upgradation_package_notification($number_of_contacts=0){

		// Fetch user data from database
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));
		$user_info=array($user_data_array[0]['member_username'],$number_of_contacts);
		$this->load->helper('notification');
		create_notification("upgradation",$user_info);
	} */
	
	



	function campaign_editor_style(){
		$this->load->view('newsletter/campaign_editor_style');
	}
	function campaign_editor_js(){
		$this->load->view('newsletter/campaign_editor_js');
	}
	/**
	*	Function update_company_info_on_campaign to update comapny info on created campaign
	**/
	function update_company_info_on_campaign($campaign_id=0){
		/**
			*Load simple_html_dom plugin for update footer*
		**/
		$this->load->helper('simple_html_dom');
		$company		=	$this->input->post('company');
		$address_line_1	=	$this->input->post('address_line_1');
		$city			=	$this->input->post('city');
		$state			=	$this->input->post('state');
		$zipcode		=	$this->input->post('zipcode');
		$country_id		=	$this->input->post('country');
		if(trim($country_id) != '245'){
			$qry="select country_name FROM red_countries WHERE country_id 	='".$country_id."'";
			$country_qry=$this->db->query($qry);	#execute query
			$country_data_array=$country_qry->result_array();	#Fetch resut
			if(count($country_data_array)>0){
				$country=$country_data_array[0]['country_name'];
			}
		}else{
				$country =$this->input->post('country_custom');
		}

		//Fetch campaign data from database by campaign ID
		$campaign_array=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$campaign_id,'campaign_created_by'=>$this->session->userdata('member_id')));

		$campaign=change_footer($company,$address_line_1,$city,$state,$zipcode,$country,$campaign_array[0]['campaign_content'],$campaign_array[0]['campaign_email_content']);
		$campaign_cnt_arr=explode('xxx_campaign_content_xxx',$campaign);
		// Load Html to text plugin
		$this->load->helper('htmltotext');
		$text_html=html2text($campaign_cnt_arr[0]);

		 
		// Update campaign by data posted by user
		$this->Campaign_Model->update_campaign(array('campaign_content'=>$campaign_cnt_arr[0],'campaign_email_content '=>$campaign_cnt_arr[1],'campaign_text_content '=>$text_html),array('campaign_id'=>$campaign_array[0]['campaign_id'],'campaign_created_by'=>$this->session->userdata('member_id')));
	}

	/**
		Function check_campaign_status to check campaign has been send successfully or not
	*/
	function check_campaign_status($campaign_id=0){

		$campaign_info=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$campaign_id,'campaign_created_by'=>$this->session->userdata('member_id')));
		if(($this->session->flashdata('campaign_status')=="scheduled")&&($campaign_info[0]['campaign_status']!="active")){
			$this->messages->add('Campaign Scheduled Successfully', 'success');
		}else if($campaign_info[0]['campaign_status']=="active"){
			$this->messages->add('Your email campaign was sent successfully.', 'success');
		}
		# Redirect to listing of campaigns
		redirect('newsletter/campaign');
	}
	/**
		Function cancel_campaign_delivery to cancel your scheduled email campaign and revert back to
draft mode.
	*/
	function cancel_campaign_delivery($campaign_id=0){
		# Load emailreport model class which handles database interaction
		$this->load->model('newsletter/Emailreport_Model');
		$where_condition=array('campaign_id'=>$campaign_id);
		#Delete from email queue table
		$this->Emailreport_Model->delete_emailqueue($where_condition);

		$input_array=array('campaign_status'=>'draft');
		# Update campaign status to draft
		$this->Campaign_Model->update_campaign($input_array,array('campaign_id'=>$campaign_id,'campaign_created_by'=>$this->session->userdata('member_id')));
	}

	function getImageBankSize(){
		$file_directory = $this->upload_path.'/image_bank';
		$filesize = 0;
		if(CAMPAIGN_HEADER_SUFFIX == 'PRVN'){//WINDOWS
			/* $obj = new COM ( 'scripting.filesystemobject' );
			if ( is_object ( $obj ) ){
				$ref = $obj->getfolder ( $file_directory);
				$filesize = $ref->size;
				$obj = null;
			}	 */
			$filesize = (1024 * 1024 * 200)+0;
		}else{
			$output = exec('du -sk ' . $file_directory);
			$filesize = trim(str_replace($file_directory, '', $output)) * 1024;
		}

		echo (IMAGE_BANK_QUOTA < $filesize)?'exceeded':'ok';
	}

	

}
?>
