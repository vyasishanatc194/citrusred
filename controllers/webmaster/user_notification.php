<?php
class User_Notification extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');

		// Load the user model which interact with database
		$this->load->model('UserModel');
		$this->load->model('newsletter/subscriber_Model');
		
		# HTTPS/SSL enabled
		force_ssl();

	}
	
	function index()
	{
		$this->display();
	}
	
	function display($start=0)
	{
		$fetch_conditions_array=array('is_deleted'=>0);
		
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/users_manage/users_list';
		$config['total_rows']=$this->UserModel->get_user_count($fetch_conditions_array);
		$config['per_page']=10;
		$config['uri_segment']=4;

		// Initialize paging with above parameters
		$this->pagination->initialize($config);
		
		//Create paging inks
		$paging_links=$this->pagination->create_links();
		
		$user_data_array=$this->UserModel->get_user_data($fetch_conditions_array,$config['per_page'],$start);
		
		// Recieve any messages to be shown, when campaign is added or updated
		$messages=$this->messages->get();
		$i=0;
		foreach($user_data_array as $user){
			/**
			  *	Get Maximum Contacts according to session package id
			*/
			$user_packages_array=$this->UserModel->get_user_packages(array('member_id'=>$user['member_id'],'is_deleted'=>0));
			$package_id=$user_packages_array[0]['package_id'];
			
			if($package_id!=0){
				
				$package_array=$this->UserModel->get_packages_data(array('package_id'=>$package_id));
				$package_price=$package_array[0]['package_price'];
				$package_max_contacts=$package_array[0]['package_max_contacts'];
			}else{
				$package_max_contacts=0;
			}
			/***
				Get Total Subscribers created by login user
			***/
			
			/*
				Creating array of conditions to checked with database with conditions.
				
			*/
			$fetch_condiotions_array=array(
			'resu.subscription_created_by'=>$user['member_id'],
			'res.subscriber_status'=>1,
			'res.subscrber_bounce'=>0,
			'res.is_deleted'=>0,
			'res.subscription_id'=>'-'.$user['member_id'],
			);
			
			// Fetch total number of subscriber from database
			$subscriber_count=$this->subscriber_Model->get_subscriber_count($fetch_condiotions_array);
			
			if($package_max_contacts<$subscriber_count){
				$user_data_array[$i]['upgrade_package']=true;
			}else{
				$user_data_array[$i]['upgrade_package']=false;
			}
			$i++;
		}
		$logo_link="webmaster/dashboard_stat";
		//Loads header, users edit  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Users Notifications','logo_link'=>$logo_link));
		$this->load->view('webmaster/user_notification_list',array('users'=>$user_data_array));
		$this->load->view('webmaster/footer');
	}
	
	function user_edit($id)
	{
		//$this->output->enable_profiler(TRUE);
		
		$user_data_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('username', 'Username', 'required');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
			$this->form_validation->set_rules('phone', 'Phone', 'required');
			$this->form_validation->set_rules('first_name', 'First name', 'required');
			$this->form_validation->set_rules('last_name', 'Last Name', 'required');
			$this->form_validation->set_rules('address1', 'Address1', 'required');
			$this->form_validation->set_rules('address2', 'Address2', 'required');
			$this->form_validation->set_rules('city', 'City', 'required');
			$this->form_validation->set_rules('state', 'State', 'required');
			$this->form_validation->set_rules('zipcode', 'zipcode', 'required');
			$this->form_validation->set_rules('country', 'Country', 'required');
			
			//Prepare member array from posted data
			$member_data = array(
				'member_id' => $this->input->post('member_id',true),
				'member_username' => $this->input->post('username',true),
				'member_password' => $password,
				'email_address' => $this->input->post('email',true),
				'phone_number' => $this->input->post('phone',true),
				'first_name' => $this->input->post('first_name',true),
				'last_name' => $this->input->post('last_name',true),
				'address_line_1' => $this->input->post('address1',true),
				'address_line_2' => $this->input->post('address2',true),
				'city' => $this->input->post('city',true),
				'state' => $this->input->post('state',true),
				'zipcode' =>$this->input->post('zipcode',true),
				'country' => $this->input->post('country',true),
				'status' => $this->input->post('status',true),
			);	
			
			if($this->form_validation->run()) {
			
				//Load Random password library
				$this->load->library('RandomPassword');
				$password=$this->randompassword->get_random_password();	
				
				$member_id=$this->input->post('member_id');
				
				//check username exists by loading user from database
				$username_exists=$this->UserModel->get_user_data(array('member_username'=>$this->input->post('username',true),'is_deleted'=>0,'member_id !='=>$member_id));
				
				//check email exists by loading email from database
				$email_exists=$this->UserModel->get_user_data(array('email_address'=>$this->input->post('email',true),'is_deleted'=>0,'member_id !='=>$member_id));	
				
				//check username exists
				if(count($username_exists)) {
					$this->messages->add('Username already exists', 'error');
					
				}
				//check email exists
				elseif(count($email_exists)) {
					$this->messages->add('Email Address already exists', 'error');
					
				}
				else
				{
					$this->UserModel->update_user($member_data,array('member_id'=>$member_id));				
					$this->messages->add('User updated successfully', 'success');
					redirect('webmaster/users_manage/users_list');
				}
			}
			
			$user_data_array=$member_data;
		}
		
		if(!count($user_data_array)) {
			$fetch_conditions_array=array('member_id'=>$id,'is_deleted'=>0);
			$user_data_array=$this->UserModel->get_user_data($fetch_conditions_array,$config['per_page']);
			$user_data_array=$user_data_array[0];
		}
		// Recieve any messages to be shown, when campaign is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, users edit  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Users','logo_link'=>$logo_link));
		$this->load->view('webmaster/user_edit',array('user'=>$user_data_array,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	function user_delete($id)
	{
		// Deletes campaign according to campaign ID
		$this->UserModel->delete_user(array('member_id'=>$id));

		// Assign  success message by message class
		$this->messages->add('User deleted successfully', 'success');

		// Redirect to listing of campaigns
		redirect('webmaster/users_manage/users_list');
	}
	
	function user_create()
	{
		//$this->output->enable_profiler(TRUE);
		
		$user_data_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('username', 'Username', 'required');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
			$this->form_validation->set_rules('phone', 'Phone', 'required');
			$this->form_validation->set_rules('first_name', 'First name', 'required');
			$this->form_validation->set_rules('last_name', 'Last Name', 'required');
			$this->form_validation->set_rules('address1', 'Address1', 'required');
			$this->form_validation->set_rules('address2', 'Address2', 'required');
			$this->form_validation->set_rules('city', 'City', 'required');
			$this->form_validation->set_rules('state', 'State', 'required');
			$this->form_validation->set_rules('zipcode', 'zipcode', 'required');
			$this->form_validation->set_rules('country', 'Country', 'required');
			
			//Prepare member array from posted data
			
			//Load Random password library
			$this->load->library('RandomPassword');
			$password=$this->randompassword->get_random_password();	
				
			$member_data = array(
				'member_username' => $this->input->post('username',true),
				'member_password' => $password,
				'email_address' => $this->input->post('email',true),
				'phone_number' => $this->input->post('phone',true),
				'first_name' => $this->input->post('first_name',true),
				'last_name' => $this->input->post('last_name',true),
				'address_line_1' => $this->input->post('address1',true),
				'address_line_2' => $this->input->post('address2',true),
				'city' => $this->input->post('city',true),
				'state' => $this->input->post('state',true),
				'zipcode' =>$this->input->post('zipcode',true),
				'country' => $this->input->post('country',true),
				'status' => $this->input->post('status',true),
			);	
			
			if($this->form_validation->run()) {
			
				
				
				$member_id=$this->input->post('member_id');
				
				//check username exists by loading user from database
				$username_exists=$this->UserModel->get_user_data(array('member_username'=>$this->input->post('username',true),'is_deleted'=>0));
				
				//check email exists by loading email from database
				$email_exists=$this->UserModel->get_user_data(array('email_address'=>$this->input->post('email',true),'is_deleted'=>0));	
				
				//check username exists
				if(count($username_exists)) {
					$this->messages->add('Username already exists', 'error');
					
				}
				//check email exists
				elseif(count($email_exists)) {
					$this->messages->add('Email Address already exists', 'error');
					
				}
				else
				{
					$this->UserModel->create_user($member_data);					
					//Load email library and send email
					$this->load->library('email');
					$config=array();
					$config['mailtype'] = 'html';
					$config['priority'] = 1;
					$this->email->initialize($config);						
					$this->email->from('system@t34m.net', 'T34m');
					$this->email->to($this->input->post('email',true));
					$email_str='<p>Dear '.$this->input->post('first_name',true).' '.$this->input->post('last_name',true).' </p>';
					$email_str.='<p>Thanks for registration</p>';
					$email_str.='<p>Your Login details are as follows:</p>';
					$email_str.='<p>Username:'.$this->input->post('username',true).'</p>';
					$email_str.='<p>Password:'.$password.'</p>';
					$this->email->subject('New Member Registration');
					$this->email->message($email_str);
					$this->email->send();						
					$this->messages->add('User created successfully', 'success');
					redirect('webmaster/users_manage/users_list');
				}
			}
			
			$user_data_array=$member_data;
		}
		
		
		// Recieve any messages to be shown
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, users edit  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Create User','logo_link'=>$logo_link));
		$this->load->view('webmaster/user_create',array('user'=>$user_data_array,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	function user_add_package($id)
	{
		//To check form is submitted
		if($this->input->post('action')=='save')
		{
			$this->form_validation->set_rules('packageId', 'Package', 'required');
			
			// To check form is validated
			if($this->form_validation->run()==true)
			{
				$member_id=$this->input->post('member_id',true);
				
					$package_id=$this->input->post('packageId',true);
					
					$selected_package_array=$this->UserModel->get_packages_data(array('package_id'=>$package_id));
					
					$selected_package_price=$selected_package_array[0]['package_price'];
				
					$data = array ( 'user_id'=> $member_id,
					'package_id'=>$package_id ,
					'amount_paid'=> $selected_package_price ,
					'gateway'=>'ADMIN', 
					'status'=>'SUCCESS',			
					'gateway_response'=>'ADMIN');
						
					$this->UserModel->insert_payment_transactions( $data );	
					
					//$package_number=$this->UserModel->get_packages_count(array('is_deleted'=>0,'member_id'=>$member_id,'package_id'=>$package_id));
					
					$this->UserModel->insert_member_package(array('member_id'=>$member_id,'package_id'=>$package_id,'credit_card_last_digit' =>NULL,
					'expiration_date' =>NULL,
					'card_holder_name' =>NULL,
					'first_name' =>NULL,
					'last_name' =>NULL,
					'address' =>NULL,
					'city' =>NULL,
					'state' =>NULL,
					'zip' =>NULL,
					'country' =>NULL,
					'subscription_id'=>NULL));
					
					$this->messages->add('User package added successfully', 'success');
					
					redirect('webmaster/users_manage/users_list');
				
			}
		}
		
		// Fetch user data from database
		$packages_count=$this->UserModel->get_packages_count(array('is_deleted'=>0,'package_status'=>1));
		$packages=$this->UserModel->get_packages_data(array('is_deleted'=>0,'package_status'=>1),$packages_count);
		
		//Fetch User Data from database
		$user_packages=$this->UserModel->get_user_packages(array('member_id'=>$id));
		
		$user_packages=$user_packages[0];
		
		// Recieve any messages to be shown, when package is added or updated
		$messages=$this->messages->get();
		
		$logo_link="webmaster/dashboard_stat";
		//Loads header, users edit  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Add Package','logo_link'=>$logo_link));
		$this->load->view('webmaster/user_add_package',array('packages'=>$packages,'user_packages'=>$user_packages,'messages' =>$messages,'member_id'=>$id));
		$this->load->view('webmaster/footer');
	}
	
	
	function user_view_package($id)
	{
		//To check form is submitted
		if($this->input->post('action')=='save')
		{
			// To check form is validated
			if($this->form_validation->run()==true)
			{
			}
		}
		
		//Fetch User Data from database
		$user_packages_count=$this->UserModel->get_user_packages_with_details_count(array('member_id'=>$id,'red_member_packages.is_deleted'=>0));
		$user_packages=$this->UserModel->get_user_packages_with_details(array('member_id'=>$id,'red_member_packages.is_deleted'=>0),$user_packages_count);
		
		// Recieve any messages to be shown, when package is added or updated
		$messages=$this->messages->get();
		
		$logo_link="webmaster/dashboard_stat";
		//Loads header, users edit  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Add Package','logo_link'=>$logo_link));
		$this->load->view('webmaster/user_view_package',array('user_packages'=>$user_packages,'messages' =>$messages,'member_id'=>$id));
		$this->load->view('webmaster/footer');
	}
	
	function user_delete_package($id,$user_id)
	{
		$this->UserModel->delete_user_package(array('red_member_package_id'=>$id));		
		$this->messages->add('Package deleted for user successfully', 'success');
		redirect('webmaster/users_manage/user_view_package/'.$user_id);
	}
	function view($id=0){
		$fetch_conditions_array=array('member_id'=>$id,'is_deleted'=>0);
		$user_data_array=$this->UserModel->get_user_data($fetch_conditions_array,$config['per_page']);
		$this->load->view('webmaster/user_view',array('user'=>$user_data_array[0]));
	}
}
?>