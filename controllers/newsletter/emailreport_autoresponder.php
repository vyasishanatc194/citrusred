<?php
class Emailreport_autoresponder extends CI_Controller{
	function __construct(){

        parent::__construct();

		if($this->session->userdata('member_id')=='')
			redirect('user/index');

		if(!count($this->session->userdata('user_packges')))	
		redirect('user/packages');	

		$this->load->model('newsletter/Emailreport_Model');
		$this->load->model('newsletter/Autoresponder_Model');	
		$this->load->model('newsletter/Subscription_Model');						
		$this->load->model('newsletter/Subscriber_Model');					
		$this->load->model('UserModel');
		$this->load->model('ConfigurationModel');
		 
	 }

	function index(){
		$this->display();
	}

	/*

		'Dislay' controller function for email report.

		

	*/
	function display($id=0){		
		if($id<=0){
			$fetch_condiotions_array=array('campaign_created_by'=>$this->session->userdata('member_id'),'is_deleted'=>0	);
			$total_rows=$this->Autoresponder_Model->get_autoresponder_count($fetch_condiotions_array);
			
			$campaign_data=$this->Autoresponder_Model->get_autoresponder_data($fetch_condiotions_array,$total_rows,0,'desc','email_send_date');
			
		}else{
			$fetch_condiotions_array=array('campaign_id'=>$id,'campaign_created_by'=>$this->session->userdata('member_id'),'is_deleted'=>0);
			$total_rows=$this->Autoresponder_Model->get_autoresponder_count($fetch_condiotions_array);
			
			$campaign_data=$this->Autoresponder_Model->get_autoresponder_data($fetch_condiotions_array,$total_rows);
		}
		
		foreach($campaign_data as $campaign){	
			
			// Get Total number of delivered mail 
			$total_delivered_emails=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber_count(array('autoresponder_scheduled_id'=>$campaign['autoresponder_scheduled_id']));						
			// Get Total number of  mail 
			$total_read_emails=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber_count(array('autoresponder_scheduled_id'=>$campaign['autoresponder_scheduled_id'],'email_track_read'=>1));		
			// Get Total number of complaint mail 
			$total_complaint_emails=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber_count(array('autoresponder_scheduled_id'=>$campaign['autoresponder_scheduled_id'],'email_track_complaint'=>1));						
			// Get Total number of  unsubscribe mail 
			$total_unsubscribes=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber_count(array('autoresponder_scheduled_id'=>$campaign['autoresponder_scheduled_id'],'email_track_unsubscribes'=>1));		
			
			// Get Total number of  mail 
			$list_emails=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber(array('autoresponder_scheduled_id'=>$campaign['autoresponder_scheduled_id']));
			
			$total_forward_emails=0;
			$total_click_emails=0;
			$total_bounce_emails=0;
			foreach($list_emails as $email){
				$total_forward_emails+=$email['email_track_forward'];
				$total_click_emails+=$email['email_track_click'];
				$total_bounce_emails+= ($email['email_track_bounce'] > 0)?1:0;
			}
			$total_unread_emails = $total_delivered_emails - $total_read_emails - $total_bounce_emails;
			// collect values in array for email report view
			$emailreport_data[$campaign['campaign_id']]['autoresponder_scheduled_id']=$campaign['autoresponder_scheduled_id'];	// scheduled_id
			$emailreport_data[$campaign['campaign_id']]['campaign_send_date']=$campaign['campaign_sheduled'];	// send date
			$emailreport_data[$campaign['campaign_id']]['total_unsubscribes']=$total_unsubscribes;	// total unsubscribes email
			$emailreport_data[$campaign['campaign_id']]['total_delivered_emails']=$total_delivered_emails;	// total delivered emails
			$emailreport_data[$campaign['campaign_id']]['total_read_emails']=$total_read_emails;	// total read emails			
			$emailreport_data[$campaign['campaign_id']]['total_unread_emails']=$total_unread_emails;	// total bounce emails
			$emailreport_data[$campaign['campaign_id']]['total_click_emails']=$total_click_emails;	// total click emails
			$emailreport_data[$campaign['campaign_id']]['campaign_title']=$campaign['campaign_title'];	// total camapign title
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
				$emailreport_data[$campaign['campaign_id']]['per_unread_emails']=($total_unread_emails-$total_bounce_emails)/$total_delivered_emails*100;
			}
			$scheduled_id=$campaign['autoresponder_scheduled_id'];
		}
		/**
		 * Fetch user data 
		 */
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));	
		$extra=$user_data_array[0];
		if($id>0){
			$this->session->set_userdata('HTTP_REFERER_EMAIL', base_url()."newsletter/emailreport_autoresponder/display/".$id);
		}else{
			$this->session->set_userdata('HTTP_REFERER_EMAIL',base_url()."newsletter/emailreport_autoresponder/display");
		}
		//Loads header, email report and footer view.
		$this->load->view('header',array('title'=>'Email Report'));
		$this->load->view('emailreport/emailreport_autoresponder_view',array('emailreport_data'=>$emailreport_data,'extra'=>$extra,'scheduled_id'=>$scheduled_id));
		$this->load->view('footer');
		
	}
	
	function update($id){		
			$input_array=array('email_track_read'=>1
				);
		$emailtrack_insert_id=$this->Emailreport_Model->update_emailreport($input_array,array('email_track_id'=>$id,'email_track_bounce'=>0));
		
	}	
	
	function view($action="",$id=0,$scheduled_id=0,$start=0,$url=""){
		// All counts starts here
		// Get Total number of delivered mail 
		$autoresponder_report['total_delivered_emails']=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber_count(array('autoresponder_scheduled_id'=>$scheduled_id));		
		
		// Get Total number of  mail 
		$autoresponder_report['total_read_emails']=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber_count(array('autoresponder_scheduled_id'=>$scheduled_id,'email_track_read'=>1));		
		// Get Total number of complaint mail 
		$autoresponder_report['total_complaint_emails']=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber_count(array('autoresponder_scheduled_id'=>$scheduled_id,'email_track_complaint'=>1));						
		// Get Total number of  unsubscribe mail 
		$autoresponder_report['total_unsubscribes']=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber_count(array('autoresponder_scheduled_id'=>$scheduled_id,'email_track_unsubscribes'=>1));		
		
		// Get Total number of  mail 
		$list_emails=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber(array('autoresponder_scheduled_id'=>$scheduled_id));
		
		$total_forward_emails=0;
		$total_click_emails=0;
		$total_bounce_emails=0;
		foreach($list_emails as $email){
			$total_forward_emails+=$email['email_track_forward'];
			$total_click_emails+=$email['email_track_click'];
			$total_bounce_emails+=$email['email_track_bounce'];
		}
		$autoresponder_report['total_forward_emails'] = $total_forward_emails;
		$autoresponder_report['total_click_emails'] = $total_click_emails;
		$autoresponder_report['total_bounce_emails'] = $total_bounce_emails;
		 
		$total_unread_emails = $autoresponder_report['total_delivered_emails'] - $autoresponder_report['total_read_emails'];
		$autoresponder_report['total_unread_emails'] = $total_unread_emails;
		// All counts ends here		
		$fetch_condiotions_array=array('campaign_created_by'=>$this->session->userdata('member_id'),'is_deleted'=>0,'campaign_status'=>'1','campaign_id'=>$id);
			
		
		// Fetches campaign data from database
		$campaign_data=$this->Autoresponder_Model->get_autoresponder_data($fetch_condiotions_array);
		if($action=="sent"){
			$fetch_condiotions_array=array('autoresponder_scheduled_id'=>$scheduled_id,	'res.subscriber_created_by'=>$this->session->userdata('member_id'));
			// Define config parameters for paging like base url, total rows and record per page.
				$config['base_url']=base_url().'newsletter/emailreport_autoresponder/view/'.$action."/".$id."/".$scheduled_id;	// The page we are linking to
				$config['total_rows']=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber_count($fetch_condiotions_array);
				 
				$config['per_page']			=	50;
				$config['uri_segment']		=	7;
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
			$emailreport_data=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber($fetch_condiotions_array,$config['per_page'],$start);
			
			$i=0;
			foreach($emailreport_data as $email_info){
				$subscriber_info=$this->Subscriber_Model->get_subscriber_info_view(array('subscriber_email_address'=>$email_info['subscriber_email'],'is_deleted'=>0,'subscriber_created_by'=>$this->session->userdata('member_id')));
				$emailreport_data[$i]['subscriber_email_address']=$email_info['subscriber_email'];
				$emailreport_data[$i]['subscriber_first_name']=$subscriber_info[0]['subscriber_first_name'];
				$emailreport_data[$i]['subscriber_last_name']=$subscriber_info[0]['subscriber_last_name'];
				$i++;
			}
			
			$current_tab="send_email";			
			$previous_page_url=$this->session->userdata('HTTP_REFERER_EMAIL');
		
			// Load view
			$this->load->view('header',array('title'=>'Sent Mail Report','previous_page_url'=>$previous_page_url));
			$this->load->view('emailreport/emailreport_autoresponder_subscriber',array('autoresponder_report'=>$autoresponder_report, 'emailreport_data'=>$emailreport_data,'current_tab'=>$current_tab,'action'=>$action,'campaign_id'=>$id,'campaign_data'=>$campaign_data[0],'scheduled_id'=>$scheduled_id,'paging_links'=>$paging_links));
			$this->load->view('footer-inner-red');
		}else if($action=="read"){
			$fetch_condiotions_array=array(
			'autoresponder_scheduled_id'=>$scheduled_id,
			'email_track_read'=>1,
			'res.subscriber_created_by'=>$this->session->userdata('member_id')
			);
			// Define config parameters for paging like base url, total rows and record per page.
				$config['base_url']=base_url().'newsletter/emailreport_autoresponder/view/'.$action."/".$id."/".$scheduled_id;	// The page we are linking to
				$config['total_rows']=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber_count($fetch_condiotions_array);
				 
				$config['per_page']			=	50;
				$config['uri_segment']		=	7;
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
			$emailreport_data=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber($fetch_condiotions_array,$config['per_page'],$start);
			$i=0;
			foreach($emailreport_data as $email_info){
				$subscriber_info=$this->Subscriber_Model->get_subscriber_info_view(array('subscriber_email_address'=>$email_info['subscriber_email'],'is_deleted'=>0,'subscriber_created_by'=>$this->session->userdata('member_id')));
				$emailreport_data[$i]['subscriber_email_address']=$email_info['subscriber_email'];
				$emailreport_data[$i]['subscriber_first_name']=$subscriber_info[0]['subscriber_first_name'];
				$emailreport_data[$i]['subscriber_last_name']=$subscriber_info[0]['subscriber_last_name'];
				$i++;
			}
			$current_tab="read_email";
			$previous_page_url=$this->session->userdata('HTTP_REFERER_EMAIL');
			// Load view
			$this->load->view('header',array('title'=>'Sent Mail Report','previous_page_url'=>$previous_page_url));
			$this->load->view('emailreport/emailreport_autoresponder_subscriber',array('autoresponder_report'=>$autoresponder_report, 'emailreport_data'=>$emailreport_data,'current_tab'=>$current_tab,'action'=>$action,'campaign_id'=>$id,'campaign_data'=>$campaign_data[0],'scheduled_id'=>$scheduled_id,'paging_links'=>$paging_links));
			$this->load->view('footer-inner-red');
		}else if($action=="bounced"){
			$fetch_condiotions_array=array(
			'autoresponder_scheduled_id'=>$scheduled_id,
			'email_track_bounce >'=>0,
			'res.subscriber_created_by'=>$this->session->userdata('member_id')
			);
			// Define config parameters for paging like base url, total rows and record per page.
				$config['base_url']=base_url().'newsletter/emailreport_autoresponder/view/'.$action."/".$id."/".$scheduled_id;	// The page we are linking to
				$config['total_rows']=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber_count($fetch_condiotions_array);
				 
				$config['per_page']			=	50;
				$config['uri_segment']		=	7;
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
			$emailreport_data=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber($fetch_condiotions_array,$config['per_page'],$start);
			$i=0;
			foreach($emailreport_data as $email_info){
				$subscriber_info=$this->Subscriber_Model->get_subscriber_info_view(array('subscriber_email_address'=>$email_info['subscriber_email'],'is_deleted'=>0,'subscriber_created_by'=>$this->session->userdata('member_id')));
				$emailreport_data[$i]['subscriber_email_address']=$email_info['subscriber_email'];
				$emailreport_data[$i]['subscriber_first_name']=$subscriber_info[0]['subscriber_first_name'];
				$emailreport_data[$i]['subscriber_last_name']=$subscriber_info[0]['subscriber_last_name'];
				$i++;
			}
			$current_tab="bounced_email";
			$previous_page_url=$this->session->userdata('HTTP_REFERER_EMAIL');
			// Load view
			$this->load->view('header',array('title'=>'Sent Mail Report','previous_page_url'=>$previous_page_url));
			$this->load->view('emailreport/emailreport_autoresponder_subscriber',array('autoresponder_report'=>$autoresponder_report, 'emailreport_data'=>$emailreport_data,'current_tab'=>$current_tab,'action'=>$action,'campaign_id'=>$id,'campaign_data'=>$campaign_data[0],'scheduled_id'=>$scheduled_id,'paging_links'=>$paging_links));
			$this->load->view('footer-inner-red');
		}else if($action=="unread"){
			$fetch_condiotions_array=array(
			'autoresponder_scheduled_id'=>$scheduled_id,
			'email_track_bounce'=>0,
			'email_track_read'=>0,
			'res.subscriber_created_by'=>$this->session->userdata('member_id')
			);
			// Define config parameters for paging like base url, total rows and record per page.
				$config['base_url']=base_url().'newsletter/emailreport_autoresponder/view/'.$action."/".$id."/".$scheduled_id;	// The page we are linking to
				$config['total_rows']=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber_count($fetch_condiotions_array);
				 
				$config['per_page']			=	50;
				$config['uri_segment']		=	7;
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
			$emailreport_data=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber($fetch_condiotions_array,$config['per_page'],$start);
			$i=0;
			foreach($emailreport_data as $email_info){
				$subscriber_info=$this->Subscriber_Model->get_subscriber_info_view(array('subscriber_email_address'=>$email_info['subscriber_email'],'is_deleted'=>0,'subscriber_created_by'=>$this->session->userdata('member_id')));
				$emailreport_data[$i]['subscriber_email_address']=$email_info['subscriber_email'];
				$emailreport_data[$i]['subscriber_first_name']=$subscriber_info[0]['subscriber_first_name'];
				$emailreport_data[$i]['subscriber_last_name']=$subscriber_info[0]['subscriber_last_name'];
				$i++;
			}
			$current_tab="unread_email";
			$previous_page_url=$this->session->userdata('HTTP_REFERER_EMAIL');
			// Load view
			$this->load->view('header',array('title'=>'Sent Mail Report','previous_page_url'=>$previous_page_url));
			$this->load->view('emailreport/emailreport_autoresponder_subscriber',array('autoresponder_report'=>$autoresponder_report, 'emailreport_data'=>$emailreport_data,'current_tab'=>$current_tab,'action'=>$action,'campaign_id'=>$id,'campaign_data'=>$campaign_data[0],'scheduled_id'=>$scheduled_id,'paging_links'=>$paging_links));
			$this->load->view('footer-inner-red');
		}else if($action=="unsubscribes"){
			$fetch_condiotions_array=array('autoresponder_scheduled_id'=>$scheduled_id,'email_track_unsubscribes'=>1,'res.subscriber_created_by'=>$this->session->userdata('member_id'));
			// Define config parameters for paging like base url, total rows and record per page.
				$config['base_url']=base_url().'newsletter/emailreport_autoresponder/view/'.$action."/".$id."/".$scheduled_id;	// The page we are linking to
				$config['total_rows']=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber_count($fetch_condiotions_array);
				 
				$config['per_page']			=	50;
				$config['uri_segment']		=	7;
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
			$emailreport_data=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber($fetch_condiotions_array,$config['per_page'],$start);
			
			$i=0;
			foreach($emailreport_data as $email_info){
				$subscriber_info=$this->Subscriber_Model->get_subscriber_info_view(array('subscriber_email_address'=>$email_info['subscriber_email'],'is_deleted'=>0,'subscriber_created_by'=>$this->session->userdata('member_id')));
				$emailreport_data[$i]['subscriber_email_address']=$email_info['subscriber_email'];
				$emailreport_data[$i]['subscriber_first_name']=$subscriber_info[0]['subscriber_first_name'];
				$emailreport_data[$i]['subscriber_last_name']=$subscriber_info[0]['subscriber_last_name'];
				$i++;
			}
			$current_tab="unsubscribes_email";
			$previous_page_url=$this->session->userdata('HTTP_REFERER_EMAIL');
			// Load view
			$this->load->view('header',array('title'=>'Sent Mail Report','previous_page_url'=>$previous_page_url));
			$this->load->view('emailreport/emailreport_autoresponder_subscriber',array('autoresponder_report'=>$autoresponder_report, 'emailreport_data'=>$emailreport_data,'current_tab'=>$current_tab,'action'=>$action,'campaign_id'=>$id,'campaign_data'=>$campaign_data[0],'scheduled_id'=>$scheduled_id,'paging_links'=>$paging_links));
			$this->load->view('footer-inner-red');
		}else if($action=="click"){
			$fetch_condiotions_array=array('campaign_id'=>$id,	'counter >'=>0,	'is_autoresponder'=>1);
			
			// Fetches subscriber data from database
			$emailreport_data=$this->Emailreport_Model->get_emailreport_click($fetch_condiotions_array);
			
			$current_tab="click_link";
			$this->session->set_userdata('HTTP_REFERER','http://'.$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL']);
			$previous_page_url=$this->session->userdata('HTTP_REFERER_EMAIL');
			// Load view
			$this->load->view('header',array('title'=>'Sent Mail Report','previous_page_url'=>$previous_page_url));			
			$this->load->view('emailreport/emailreport_autoresponder_click',array('autoresponder_report'=>$autoresponder_report, 'emailreport_data'=>$emailreport_data,'current_tab'=>$current_tab,'action'=>$action,'campaign_id'=>$id,'campaign_data'=>$campaign_data[0],'scheduled_id'=>$scheduled_id));
			$this->load->view('footer-inner-red');
		}else if($action=="view_subscriber_click"){
			$fetch_condiotions_array=array(	'campaign_id'=>$id,	'counter >'=>0,	'tiny_url'=>$url,'is_autoresponder'=>1,	'autoresponder_scheduled_id'=>$scheduled_id	);
			
			// Fetches subscriber data from database
			$emailreport_subsciber_data=$this->Emailreport_Model->get_emailreport_autoresponder_subscriber_click($fetch_condiotions_array);
			$i=0;
			
			foreach($emailreport_subsciber_data as $emailreport_info){				
				$emailreport_subsciber_data[$i]['campaign_id']=$emailreport_info['campaign_id'];
				$subscriber_info=$this->Subscriber_Model->get_subscriber_info_view(array('subscriber_email_address'=>$emailreport_info['subscriber_email'],'is_deleted'=>0,'subscriber_created_by'=>$this->session->userdata('member_id')));
				$emailreport_subsciber_data[$i]['subscriber_email_address']=$emailreport_info['subscriber_email'];
				$emailreport_subsciber_data[$i]['subscriber_first_name']=$subscriber_info[0]['subscriber_first_name'];
				$emailreport_subsciber_data[$i]['subscriber_last_name']=$subscriber_info[0]['subscriber_last_name'];
				$i++;
			}			
			
			$current_tab="click_link";			
			$previous_page_url=$this->session->userdata('HTTP_REFERER');
			// Load view
			$this->load->view('header',array('title'=>'Sent Mail Report','previous_page_url'=>$previous_page_url));
			$this->load->view('emailreport/emailreport_autoresponder_subscriber',array('autoresponder_report'=>$autoresponder_report, 'emailreport_subsciber_data'=>$emailreport_subsciber_data,'current_tab'=>$current_tab,'action'=>$action,'campaign_id'=>$id,'campaign_data'=>$campaign_data[0],'scheduled_id'=>$scheduled_id));
			$this->load->view('footer-inner-red');
		}else if($action=="forwardemail"){
			$fetch_condiotions_array=array(
			'autoresponder_scheduled_id'=>$scheduled_id,
			'email_track_forward >'=>0
			);
			// Define config parameters for paging like base url, total rows and record per page.
				$config['base_url']=base_url().'newsletter/emailreport_autoresponder/view/'.$action."/".$id."/".$scheduled_id;	// The page we are linking to
				$config['total_rows']=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber_count($fetch_condiotions_array);
				 
				$config['per_page']			=	50;
				$config['uri_segment']		=	7;
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
			// Fetches subscriber  data from database
			$emailreport_forward_data=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber($fetch_condiotions_array,$config['per_page'],$start);
			$i=0;
			foreach($emailreport_forward_data as $emailreport_info){
				$emailreport_subsciber_data[$i]['campaign_id']=$emailreport_info['campaign_id'];
				$subscriber_info=$this->Subscriber_Model->get_subscriber_info_view(array('subscriber_email_address'=>$emailreport_info['subscriber_email'],'is_deleted'=>0,'subscriber_created_by'=>$this->session->userdata('member_id')));
				$emailreport_forward_data[$i]['subscriber_email_address']=$emailreport_info['subscriber_email'];
				$emailreport_forward_data[$i]['subscriber_first_name']=$subscriber_info[0]['subscriber_first_name'];
				$emailreport_forward_data[$i]['subscriber_last_name']=$subscriber_info[0]['subscriber_last_name'];
				$i++;
			}
			$current_tab="forward_email";
			$previous_page_url=$this->session->userdata('HTTP_REFERER_EMAIL');
			// Load view
			$this->load->view('header',array('title'=>'Sent Mail Report','previous_page_url'=>$previous_page_url));
			$this->load->view('emailreport/emailreport_autoresponder_subscriber',array('autoresponder_report'=>$autoresponder_report, 'emailreport_forward_data'=>$emailreport_forward_data,'current_tab'=>$current_tab,'action'=>$action,'campaign_id'=>$id,'campaign_data'=>$campaign_data[0],'scheduled_id'=>$scheduled_id,'paging_links'=>$paging_links));
			$this->load->view('footer-inner-red');
		}else if($action=="complaints"){
			$fetch_condiotions_array=array(
			'autoresponder_scheduled_id'=>$scheduled_id,
			'email_track_complaint'=>1
			);			
			// Fetches subscriber  data from database
			$emailreport_data=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber($fetch_condiotions_array);
			$i=0;
			foreach($emailreport_data as $emailreport_info){
				$emailreport_subsciber_data[$i]['campaign_id']=$emailreport_info['campaign_id'];
				$subscriber_info=$this->Subscriber_Model->get_subscriber_info_view(array('subscriber_email_address'=>$emailreport_info['subscriber_email'],'is_deleted'=>0,'subscriber_created_by'=>$this->session->userdata('member_id')));
				$emailreport_data[$i]['subscriber_email_address']=$emailreport_info['subscriber_email'];
				$emailreport_data[$i]['subscriber_first_name']=$subscriber_info[0]['subscriber_first_name'];
				$emailreport_data[$i]['subscriber_last_name']=$subscriber_info[0]['subscriber_last_name'];
				$i++;
			}
			$current_tab="complaints_email";
			$previous_page_url=$this->session->userdata('HTTP_REFERER_EMAIL');
			// Load view
			$this->load->view('header',array('title'=>'Sent Mail Report','previous_page_url'=>$previous_page_url));
			$this->load->view('emailreport/emailreport_autoresponder_subscriber',array('autoresponder_report'=>$autoresponder_report, 'emailreport_data'=>$emailreport_data,'current_tab'=>$current_tab,'action'=>$action,'campaign_id'=>$id,'campaign_data'=>$campaign_data[0],'paging_links'=>$paging_links, 'scheduled_id'=>$scheduled_id));
			$this->load->view('footer-inner-red');
		}
	}
	/**
	  * Function subscriber_view to display contact profile
	**/
	function subscriber_view($campaign_id=0,$subscriber_id=0,$scheduled_id=0)
	{
		
		$site_configuration_array=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'max_soft_bounce'));
		$max_soft_bounce=$site_configuration_array[0]['config_value'];
		#define('MAX_SOFT_BOUNCE', "$max_soft_bounce");
		//Check if user is not login then redirect to index page
		if($this->session->userdata('member_id')=='')
			redirect('user/index');	
		
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
		$autoresponder_email_report=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber(array('autoresponder_scheduled_id'=>$scheduled_id,'email_track_subscriber_id'=>$subscriber_id));		
		
		$subscriptions[]=$autoresponder_email_report[0];
		$subscriptions[0]['subscriber_email_address']=$autoresponder_email_report[0]['subscriber_email'];
		$subscriptions[0]['subscriber_status']=1;
		$subscriber_info=$this->Subscriber_Model->get_subscriber_info_view(array('subscriber_id'=>$subscriber_id,'is_deleted'=>0,'subscriber_created_by'=>$this->session->userdata('member_id')));
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
			$subscriber_info=$this->Subscriber_Model->get_subscriber_info_view(array('subscriber_id'=>$autoresponder_email_report[0]['subscriber_id'],'subscriber_created_by'=>$this->session->userdata('member_id')));
			if(count($subscriber_info)>0){
				$subscriber_info[0]['subscriber_status']=5;
			}else{
				$subscriber_info[0]['subscriber_status']=6;				
				$subscriber_info[0]['subscriber_email_address']=$autoresponder_email_report[0]['subscriber_email'];
			}
		}
		if(count($subscriber_info)>0){
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
		
			/* if(($subscriptions[0]['subscrber_bounce']==2)||(($subscriptions[0]['subscrber_bounce']==1)&&($subscriptions[0]['soft_bounce']>3))){
				$subscriptions[0]['subscriber_status']=3;
			} */
		}else{
			$subscriptions[0]['subscriber_status']=5;
		}
		if($_SERVER['HTTP_REFERER']!=base_url()."upgrade_package_cim/index"){
			$this->session->set_userdata('HTTP_REFERERS', $_SERVER['HTTP_REFERER']);
			$previous_page_url=$_SERVER['HTTP_REFERER'];
		}else{
			$previous_page_url=$this->session->userdata('HTTP_REFERERS');
		}
		foreach($autoresponder_email_report as $key=>$campaign_report){
			$fetch_condiotions_array=array(
				'campaign_id'=>$campaign_report['campaign_id'],
				'counter >'=>0
			);
			
			// Fetches subscriber data from database
			$emailreport_data[$campaign_report['campaign_id']]=$this->Emailreport_Model->get_emailreport_click($fetch_condiotions_array);
			# Count clicks of all url
			$counter=0;
			foreach($emailreport_data[$campaign_report['campaign_id']] as $click){
				$counter+=$click['cnt'];
			}
			$autoresponder_email_report[$key]['clicks']=$counter;
		}
		/**
			Fetch email report of contact
		**/
		// Fetch Email Report 
		$email_report=$this->Emailreport_Model->get_emailreport_campaign_data(array('ret.subscriber_id'=>$subscriptions[0]['subscriber_id'],'campaign_created_by'=>$this->session->userdata('member_id'),'ret.email_sent'=>1));
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
			if(($subscriptions[0]['subscrber_bounce']==1)&&($subscriptions[0]['soft_bounce']>$max_soft_bounce)){
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
		$this->load->view('contacts/subscriber_view',array('subscriptions'=>$subscriptions,'contact_soft_bounce'=>$subscriptions[0]['soft_bounce'],'contact_bounce_status'=>$subscriptions[0]['subscrber_bounce'],'autoresponder_email_report'=>$autoresponder_email_report,'email_report_view'=>1,'subscription_title'=>$subscription_title,'email_report_click'=>$emailreport_data,'email_report'=>$email_report,'shorten_url'=>$shorten_url,'max_soft_bounce'=>$max_soft_bounce,'contact_history'=>$strHistory));
		$this->load->view('footer-inner-red');
	}
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
	/**
	 *	Function Exportcsv
	 *
	 *	'Exportcsv' controller function for exporting csv file from stats data
	 *
	 *	@param (string) (action)  contains type of stats for which csv will be export
	 *	@param (int) (campaign_id)  campaign for which stats data is to be exported
	 */
	function exportcsv($action, $campaign_id, $scheduled_id, $url=''){
		//Check if user is not login then redirect to index page
		if($this->session->userdata('member_id')=='')
			redirect('user/index');

		//	Collect subscription id
		//Protecting MySQL from query string sql injection Attacks
		if(!is_numeric($campaign_id)){
			redirect('user/index');
		}
		if($action=="sent")	$fetch_condiotions_array=array(	'autoresponder_scheduled_id'=>$scheduled_id, 'res.subscriber_created_by'=>$this->session->userdata('member_id'));
		elseif($action=="read")$fetch_condiotions_array=array('autoresponder_scheduled_id'=>$scheduled_id, 'email_track_read'=>1,'res.subscriber_created_by'=>$this->session->userdata('member_id'));
		elseif($action=="unread")$fetch_condiotions_array=array('autoresponder_scheduled_id'=>$scheduled_id, 'email_track_read'=>0,'res.subscriber_created_by'=>$this->session->userdata('member_id'));
		elseif($action=="forwardemail")$fetch_condiotions_array=array('autoresponder_scheduled_id'=>$scheduled_id, 'email_track_forward >'=>0,'res.subscriber_created_by'=>$this->session->userdata('member_id'));
		elseif($action=="click")$fetch_condiotions_array=array('autoresponder_scheduled_id'=>$scheduled_id, 'email_track_click >'=>0,'res.subscriber_created_by'=>$this->session->userdata('member_id'));
		elseif($action=="unsubscribes")$fetch_condiotions_array=array('autoresponder_scheduled_id'=>$scheduled_id, 'email_track_unsubscribes >'=>0,'res.subscriber_created_by'=>$this->session->userdata('member_id'));
		elseif($action=="complaints")$fetch_condiotions_array=array('autoresponder_scheduled_id'=>$scheduled_id, 'email_track_complaint >'=>0,'res.subscriber_created_by'=>$this->session->userdata('member_id'));
		elseif($action=="bounced")$fetch_condiotions_array=array('autoresponder_scheduled_id'=>$scheduled_id, 'email_track_bounce >'=>0,'res.subscriber_created_by'=>$this->session->userdata('member_id'));				
		
		$emailreport_data=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber($fetch_condiotions_array);
				
		//Create output string  with heading
		$csv_output_header="First Name,Last Name,Email Address,Address,Birthday,City,Company,Country,Phone,State,Zip Code";
		//$csv_output_header.="\n";
		$csv_output="\n";
		 
		$i=0;
		
	 
		//Append subscribers to csv output
		foreach($emailreport_data as $subscriber){
			$csv_output.=$subscriber['subscriber_first_name'].",";
			$csv_output.=$subscriber['subscriber_last_name'].",";
			$csv_output.=$subscriber['subscriber_email'].",";
			/* $csv_output.=$subscriber['subscriber_address'].",";
			$csv_output.=$subscriber['subscriber_dob'].",";
			$csv_output.=$subscriber['subscriber_city'].",";
			$csv_output.=$subscriber['subscriber_company'].",";
			$csv_output.=$subscriber['subscriber_country'].",";
			$csv_output.=$subscriber['subscriber_phone'].",";
			$csv_output.=$subscriber['subscriber_state'].",";
			$csv_output.=$subscriber['subscriber_zip_code'].""; */
			  
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
}
?>