<?php 
class Account extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('cookie');
		# HTTPS/SSL enabled
		force_ssl();
		
		
	}
	
	function index()
	{
		
			$this->login();
		 
	}
	
	/*
		'Login' controller function for webmaster to login in the website 
		It matches user credentails supplied by user in database.
	*/
	
	function login()
	{
		if(WWW_AUTHENTICATE == 'YES' && !($_SERVER['PHP_AUTH_USER'] == WWW_AUTHENTICATION_UNM && $_SERVER['PHP_AUTH_PW'] == WWW_AUTHENTICATION_PWD)){
			header('WWW-Authenticate: Basic realm="HTTP Auth"');
			header('HTTP/1.0 401 Unauthorized');
			echo 'Unauthorized!';
			exit;
		}
	 
		if($this->session->userdata('webmaster_id')!=''){
			if(get_cookie('rc_referal_url')!=""){
				redirect(get_cookie('rc_referal_url'));
			}else{
				redirect('webmaster/dashboard_stat');
			}
		}
		////$this->output->enable_profiler(TRUE);
		
		// Load the user model which interact with database
		$this->load->model('webmaster/Account_Model');
		
		// To check if form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('webmaster_username', 'Username', 'required|min_length[2]|max_length[250]|trim');
			$this->form_validation->set_rules('webmaster_password', 'Password', 'required|min_length[2]|max_length[250]|trim');
			
			// To check form is validated
			if($this->form_validation->run()==true)
			{
				// Retrieve data posted in form posted by user using input class
				$webmaster_password=md5($this->input->post('webmaster_password', TRUE));
				$user_credentails=array('webmaster_username'=>$this->input->post('webmaster_username', TRUE),'webmaster_password'=>$webmaster_password);
				
				// Fetch user data from database
				$user_data_array=$this->Account_Model->get_account_data($user_credentails);
				// To check user have credentails matching in database
				if(count($user_data_array))
				{
					//Assign  session to user
					$this->session->set_userdata('webmaster_id', $user_data_array[0]['webmaster_id']);
					$this->session->set_userdata('webmaster_username', $user_data_array[0]['webmaster_username']);
					$this->session->set_userdata('webmaster_email_address', $user_data_array[0]['webmaster_id']);	
					
					//Redirect to users listing page
					if(get_cookie('rc_referal_url')!=""){
						redirect(get_cookie('rc_referal_url'));
					}elseif($user_data_array[0]['webmaster_id'] == '1'){
						redirect('webmaster/dashboard_stat');
					}else{
						redirect('webmaster/dashboard_stat/sent_campaign/');
					}
				}
				else
				{
					// Assign message in case of invalid username or pass
					$this->messages->add('Invalid Username or Password', 'error');
				}
			}
		}
		
		// Recieve any messages to be shown, when campaign is added or updated
		$messages=$this->messages->get();
		$logo_link='';
		//Loads header, campaign and footer view.
		$this->load->view('webmaster/header',array('title'=>'Webmaster Login','logo_link'=>$logo_link));
		$this->load->view('webmaster/account_login',array('user'=>$user_data_array,'messages' =>$messages));
		$this->load->view('webmaster/footer');		
	}
	
	/*
		Controller to 'log out' user from the session
	*/
	
	function logout()
	{		
		$this->messages->add('You have logged out successfully', 'success');
		
		//Ends user session
		$this->session->sess_destroy();
		delete_cookie("rc_referal_url");
		//Redirect to Login page
		redirect('webmaster/account/login');
	}
	/**
		Function set_refferal_url to set refferal url in session
	**/
	function set_referal_url(){
		if($this->session->userdata('webmaster_id')!=''){
			$time = time();	
			$cookie = array(
				'name'  => 'referal_url',
				'value'  => $_SERVER['HTTP_REFERER'],
				'expire' => $time + 3600,
				'prefix' => 'rc_',
				'secure' => TRUE
			); 
			set_cookie($cookie);	//set cookie
		}
	}
	function check_session(){
		if($this->session->userdata('webmaster_id')==''){
			echo "sessionexpire";
		}else{
			echo "success";
		}
	}
}
?>