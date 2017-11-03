<?php
class User extends CI_Controller
{
	function index(){
		redirect('/');
	}
	function __construct(){
		parent::__construct();
		$this->load->helper('cookie');
		$this->load->library('encrypt');
		$this->load->helper('transactional_notification');
		$this->load->model('UserModel');
		$this->load->model('newsletter/Subscriber_Model');
		$this->load->model('Activity_Model');
		$this->load->model('newsletter/subscription_Model');
		//force_ssl();			
	}

	/*
		Register controller to create new user
	*/
	function register(){
		if($this->session->userdata('member_id')!=''){
			redirect('newsletter/campaign');
		}
		//$this->output->enable_profiler(TRUE);
		// Validation rules are applied
		$this->form_validation->set_rules('username', 'Username', 'required|min_length[4]');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
		$this->form_validation->set_rules('verifycheckbox', 'Terms of Service', 'required');
		//To check form is submitted
		if($this->input->post('btnRegister')!='' && $this->form_validation->run()){
			//Prepare member array from posted data
			$member_data = array(
				   'member_username' => $this->input->post('username',true),
				   'email_address' => $this->input->post('email',true),
				   'member_password' => $this->is_authorized->hashPassword($this->input->post('password',true)),
				   'ip_address'		=>	$this->is_authorized->getRealIpAddr(),
				   'phone_number' => null,
					'first_name' => null,
					'last_name' => null,
					'address_line_1' => null,
					'address_line_2' => null,
					'city' => null,
					'state' => null,
					'zipcode' => null,
				   'last_login_time'=>date("Y-m-d H:i:s"),
				   'created_on'=>date("Y-m-d H:i:s"),
				   'login_expiration_notification_date'=>NULL,
				   'status'=>'unconfirmed',
				   'company' => null,
					'autoresponder_status' => 0,
					'sign_up_form_status' => 0,
					'contact_import' => 0,
					'package_id' => 0,
					'is_authentic' => 0,
					'segment_size' => 0,
					'authenticated_on' =>NULL,
					'unauthentic_contacts' => 0,
					'reset_password_token' => '',
					'login_expiration_notification_date' =>NULL,
					'cancel_subscription_date' =>NULL
			);

			

			//check username exists by loading user from database
			$username_exists=$this->UserModel->get_user_data(array('member_username'=>$this->input->post('username',true),'is_deleted'=>0));
			//check email exists by loading email from database
			$email_exists=$this->UserModel->get_user_data(array('email_address'=>$this->input->post('email',true),'is_deleted'=>0));
			
			//check username or email exists
			if(count($username_exists)) {
				$this->messages->add('Username already exists', 'error');
			}elseif(count($email_exists)) {
				$this->messages->add('Email Address already exists', 'error');
			}elseif($this->UserModel->is_temp_mail($this->input->post('email',true))) {
				$this->messages->add('Please use your permanent email address', 'error');
			}else{
				//To insert user data in database
				$inserted_user_id=$this->UserModel->create_user($member_data);		
				// Fetch user data from database
				$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$inserted_user_id));
				// To check user have credentails matching in database
				if(count($user_data_array)){
					$this->checkReferredMember($inserted_user_id,$member_data['member_username'],$member_data['email_address']);
					$max_quota = $this->UserModel->get_package_quota(-1);
					// submit free pckage for register user
					$member_package_id=$this->UserModel->insert_member_package(array(
					'member_id'=>$user_data_array[0]['member_id'],
					'package_id'=>-1, 'max_campaign_quota'=>$max_quota, 'credit_card_last_digit' =>NULL, 'expiration_date' =>NULL, 'card_holder_name' =>NULL, 'first_name' =>NULL, 'last_name' =>NULL, 'address' =>NULL, 'city' =>NULL, 'state' =>NULL, 'zip' =>NULL, 'country' =>NULL, 'subscription_id'=>NULL ));
					$this->UserModel->update_user(array('package_id'=>$member_package_id),array('member_id'=>$user_data_array[0]['member_id']));
					//Assign  session to user
					$this->session->set_userdata('member_id', $user_data_array[0]['member_id']);
					$this->session->set_userdata('member_username', $user_data_array[0]['member_username']);
					$this->session->set_userdata('member_email_address', $user_data_array[0]['email_address']);
					$this->session->set_userdata('member_autoresponder_status', $user_data_array[0]['autoresponder_status']);
					$this->session->set_userdata('user_packages_id', $member_package_id);
					$this->session->set_userdata('member_status','inactive');
					$this->session->set_userdata('member_time_zone',WEBMASTER_TIMEZONE);
					// fetch package information for set in session
					$user_packages=array();
					$user_packages_array=$this->UserModel->get_user_packages(array('member_id'=>$user_data_array[0]['member_id'],'is_deleted'=>0));
					//$this->is_authorized->saveCookieTocken($user_data_array[0]['member_id'], $user_data_array[0]['member_username']);

					foreach($user_packages_array as $package)
					$user_packages[]=$package['package_id'];
					$this->session->set_userdata('user_packages', $user_packages);
					// create default subscription
					$input_array=array('subscription_title'=>'All My Contacts', 'subscription_id'=>'-'.$this->session->userdata('member_id'), 'subscription_is_name'=>'1', 'subscription_created_by'=>$this->session->userdata('member_id'));
					
					// Sends form input data to database via model object
					$subscription_id=$this->subscription_Model->create_subscription($input_array);
					$thisIP = $this->is_authorized->getRealIpAddr();
					$this->Activity_Model->create_activity(array('user_id'=>$this->session->userdata('member_id'),'activity'=>'login:'.$thisIP	));					
					$this->register_member_to_redcappi_account($this->session->userdata('member_id'));
					$this->user_confirmation_notification($inserted_user_id);
				}
			}
		}

		//Get the messages
		$messages=$this->messages->get();

		$data = array('messages' =>$messages, 'title'=>"Registration - Email Marketing");
		//Load the  register  view
		$this->load->view('header_login',array('title'=>'Register','previous_page_url'=>$previous_page_url));	
		$this->load->view('user/user_register',$data);
	}
	
        function registerGetRedCappi($email, $password,$firstname= '', $phone = ''){
                                                    
                                                    $msgGRC = array();
			//Prepare member array from posted data
			$member_data = array(
				   'member_username' => $email,
				   'email_address' => $email,
				   'member_password' => $this->is_authorized->hashPassword($password),
				   'ip_address'		=>	$this->is_authorized->getRealIpAddr(),
				   'phone_number' => $phone,
					'first_name' => $firstname,
					'last_name' => null,
					'address_line_1' => null,
					'address_line_2' => null,
					'city' => null,
					'state' => null,
					'zipcode' => null,
				   'last_login_time'=>null,
				   'created_on'=>date("Y-m-d H:i:s"),
				   'login_expiration_notification_date'=>NULL,
				   'status'=>'unconfirmed',
				   'company' => null,
					'autoresponder_status' => 0,
					'sign_up_form_status' => 0,
					'contact_import' => 0,
					'package_id' => 0,
					'is_authentic' => 0,
					'segment_size' => 0,
					'authenticated_on' =>NULL,
					'unauthentic_contacts' => 0,
					'reset_password_token' => '',
					'login_expiration_notification_date' =>NULL,
					'cancel_subscription_date' =>NULL);
			

			

			//check username exists by loading user from database
			$username_exists=$this->UserModel->get_user_data(array('member_username'=>$email,'is_deleted'=>0));
			//check email exists by loading email from database
			$email_exists=$this->UserModel->get_user_data(array('email_address'=>$email,'is_deleted'=>0));
			
			//check username or email exists
			if(count($username_exists)) {
                                                                array_push($msgGRC,'Username already exists');
                                                               return $msgGRC;
			}elseif(count($email_exists)) {
				array_push($msgGRC,'Email Address already exists');
                                                                    return $msgGRC;
			}elseif($this->UserModel->is_temp_mail($this->input->post('email',true))) {
				array_push($msgGRC,'Please use your permanent email address');
                                                                    return $msgGRC;
			}else{
                            			$this->Activity_Model->create_activity(array('user_id'=>1,'activity'=>'GetRedCappi_create_user_start: Email:'.$email));					
		
				//To insert user data in database
				$inserted_user_id=$this->UserModel->create_user($member_data);		
				// Fetch user data from database
				$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$inserted_user_id));
				// To check user have credentails matching in database
				if(count($user_data_array)){
					$this->checkReferredMember($inserted_user_id,$member_data['member_username'],$member_data['email_address']);
					$max_quota = $this->UserModel->get_package_quota(-1);
					// submit free pckage for register user
					$member_package_id=$this->UserModel->insert_member_package(array(
					'member_id'=>$user_data_array[0]['member_id'],
					'package_id'=>-1, 'max_campaign_quota'=>$max_quota, 'credit_card_last_digit' =>NULL, 'expiration_date' =>NULL, 'card_holder_name' =>NULL, 'first_name' =>NULL, 'last_name' =>NULL, 'address' =>NULL, 'city' =>NULL, 'state' =>NULL, 'zip' =>NULL, 'country' =>NULL, 'subscription_id'=>NULL ));
					$this->UserModel->update_user(array('package_id'=>$member_package_id),array('member_id'=>$user_data_array[0]['member_id']));
					//Assign  session to user
					$this->session->set_userdata('member_id', $user_data_array[0]['member_id']);
					$this->session->set_userdata('member_username', $user_data_array[0]['member_username']);
					$this->session->set_userdata('member_email_address', $user_data_array[0]['email_address']);
					$this->session->set_userdata('member_autoresponder_status', $user_data_array[0]['autoresponder_status']);
					$this->session->set_userdata('user_packages_id', $member_package_id);
					$this->session->set_userdata('member_status','inactive');
					$this->session->set_userdata('member_time_zone',WEBMASTER_TIMEZONE);
					// fetch package information for set in session
					$user_packages=array();
					$user_packages_array=$this->UserModel->get_user_packages(array('member_id'=>$user_data_array[0]['member_id'],'is_deleted'=>0));
					//$this->is_authorized->saveCookieTocken($user_data_array[0]['member_id'], $user_data_array[0]['member_username']);

					foreach($user_packages_array as $package)
					$user_packages[]=$package['package_id'];
					$this->session->set_userdata('user_packages', $user_packages);
					// create default subscription
					$input_array=array('subscription_title'=>'All My Contacts', 'subscription_id'=>'-'.$this->session->userdata('member_id'), 'subscription_is_name'=>'1', 'subscription_created_by'=>$this->session->userdata('member_id'));
					
					// Sends form input data to database via model object
					$subscription_id=$this->subscription_Model->create_subscription($input_array);
					$thisIP = $this->is_authorized->getRealIpAddr();
					$this->Activity_Model->create_activity(array('user_id'=>$this->session->userdata('member_id'),'activity'=>'GetRedCappi_create_user_end:'.$email	));					
					$this->register_member_to_redcappi_account($this->session->userdata('member_id'));
					$this->user_confirmation_notification($inserted_user_id);
				}
			}
                                        
                                         
                                         return $msgGRC;
		}


		
	
	/**
		Send registration confirmation email
	**/
	function user_confirmation_notification($user_id,$redirect=""){
		if($this->session->userdata('member_id') == $user_id){
			// Fetch user data from database
			$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$user_id));
			$to_email=$user_data_array[0]['email_address'];
			$to_username=$user_data_array[0]['member_username'];
			$user_password=$user_data_array[0]['member_password'];
			//$user_id=$this->is_authorized->base64UrlSafeEncode($user_id);
			$user_id=$this->is_authorized->encryptor('encrypt',$user_id);
			$user_info=array($user_id,$to_email,$to_username,$user_password);

			create_transactional_notification("confirm_user_registration",$user_info);
			if($redirect != "confirmation_msg"){
				redirect('newsletter/campaign/');
			}else{
				echo "success";
			}
		}else{
			die("success");
		}
	}

	/**
		function registration_notification to display notification message
	**/
	function registration_notification(){		
		 
		
		$this->load->view('user/user_confirmation');
	}
        
        
                function usernameexists(){
                    
                    $username = $_REQUEST['email'];
                    $email = $username;
                    $password = $_POST['Password'];
                    
                    //print_r($username . $password);
                    
                         //check username exists by loading user from database
                        // print_r("in the usernameexists function");
                         $username_exists=$this->UserModel->get_user_data(array('member_username'=>$email,'is_deleted'=>0));
                        //check email exists by loading email from database
                        $email_exists=$this->UserModel->get_user_data(array('email_address'=>$email,'is_deleted'=>0));

                        //check username or email exists
                        if(count($username_exists)) {
                                                                print_r('Username already exists');
                                                               
                        }elseif(count($email_exists)) {
                                print_r('Email Address already exists');
                                                                    
                        }elseif($this->UserModel->is_temp_mail($this->input->post('email',true))) {
                                print_r('Please use your permanent email address');
                        
                        
                        }

                }

	function confirm_user($user_id=""){
		//$user_id=$this->is_authorized->base64UrlSafeDecode($user_id);	// Decode user id
		$user_id=$this->is_authorized->encryptor('decrypt',$user_id);
		
		// Fetch user data from database
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$user_id));
		// To check user have credentails matching in database
		if(count($user_data_array)){
		 
			if($user_data_array[0]['status']=='unconfirmed'){
				$ip_address=$this->is_authorized->getRealIpAddr();
				$this->UserModel->update_user(array('ip_address'=>$ip_address,'last_login_time'=>date("Y-m-d H:i:s"),'login_expiration_notification_date'=>NULL,'status'=>'active','status_inactive_description'=>''),array('member_id'=>$user_data_array[0]['member_id']));
				// echo $this->db->last_query();
				//Assign  session to user
				$this->session->set_userdata('member_id', $user_data_array[0]['member_id']);
				$this->session->set_userdata('member_username', $user_data_array[0]['member_username']);
				$this->session->set_userdata('member_email_address', $user_data_array[0]['email_address']);
				$this->session->set_userdata('member_autoresponder_status', $user_data_array[0]['autoresponder_status']);
				$this->session->set_userdata('user_packages_id', $user_data_array[0]['package_id']);
				$this->session->set_userdata('member_status','active');
				$this->session->set_userdata('manage_campaigns', 1);						
				$this->session->set_userdata('manage_contacts', 1 );						
				$this->session->set_userdata('manage_stats', 1 );						
				$this->session->set_userdata('manage_autoresponders', 1);						
				$this->session->set_userdata('manage_signupforms', 1);
				$this->session->set_userdata('manage_extra', 1);
				// fetch package information for set in session
				$user_packages=array();
				 
				$user_packages_array=$this->UserModel->get_user_packages(array('member_id'=>$user_data_array[0]['member_id'],'is_deleted'=>0));

				foreach($user_packages_array as $package)
				$user_packages[]=$package['package_id'];
				$this->session->set_userdata('user_packages', $user_packages);
				// create array for insert values in activty table
				$values=array('user_id'=>$this->session->userdata('member_id'), 'activity'=>'login:'.$ip_address);
				$this->Activity_Model->create_activity($values);
				$this->register_member_to_redcappi_account($user_id);
				redirect('newsletter/campaign');
			}else{
				$this->register_member_to_redcappi_account($user_id);
				redirect('/');
			}
		}else{
			redirect('/');
		}
	}

		
	/*
		'Login' controller function to login in the website
		It matches user credentails supplied by user in database.
	*/

	function login(){
		//$this->output->enable_profiler(TRUE);
		$bad_login_limit = 5;
		$lockout_time = 600;
		$user_data_array = array();
		// To check if form is submitted
		if(isset($_POST['action'])&&($_POST['action']=='save')){
		//print_r($_POST);
			// Validation rules are applied
			$this->form_validation->set_rules('member_username', 'Username', 'required|min_length[2]|max_length[250]|trim');
			$this->form_validation->set_rules('member_password', 'Password', 'required|min_length[2]|max_length[250]|trim');
			// To check form is validated
			if($this->form_validation->run()==true){
				$qry="select * FROM red_members WHERE (member_username='".$this->input->post('member_username', TRUE)."' OR email_address='".$this->input->post('member_username', TRUE)."') AND `is_deleted`=0 AND ((status='active') OR (status='failed-cc') OR (status='inactive' AND DATEDIFF(CURDATE(),created_on) < 9999) OR (status='inactive' AND status_inactive_description = 'policy related'))";
				$user_qry=$this->db->query($qry);	#execute query
				if($user_qry->num_rows() > 0){
					$user_data_array=$user_qry->result_array();	#Fetch resut
					$user_qry->free_result();
					if(($user_data_array[0]['failed_login_count'] >= $bad_login_limit) && ((time() - strtotime($user_data_array[0]['first_failed_login'])) < $lockout_time)){
						die("locked");
					  exit; // or return, or whatever.
					} 
				}
				
				// To check user have credentails matching in database
				if(count($user_data_array)){
					if((!$this->is_authorized->is_login($this->input->post('member_password', TRUE), $user_data_array[0]['member_password']))){
						$mid = $user_data_array[0]['member_id'];
						$first_failed_login = (is_null($user_data_array[0]['first_failed_login']))?0:strtotime($user_data_array[0]['first_failed_login']);
						if( (time() - $first_failed_login) > $lockout_time ) {
							// first unsuccessful login since $lockout_time on the last one expired
							$first_failed_login = now(); // commit to DB														
							$this->db->query("Update `red_members` set `first_failed_login`=current_timestamp(), `failed_login_count` = '1' where `member_id`='$mid'");
						} else {						
							$this->db->query("Update `red_members` set `failed_login_count` = `failed_login_count` + 1 where `member_id`='$mid'");
						}
						die('error');
					exit;
					}
					/**
					*	Update Last login time and IP #
					*/	
					$ip_address=$this->is_authorized->getRealIpAddr();
					$this->UserModel->update_user(array('ip_address'=>$ip_address,'last_login_time'=>date("Y-m-d H:i:s"),'login_expiration_notification_date'=>NULL),array('member_id'=>$user_data_array[0]['member_id']));
					
					if($user_data_array[0]['parent_id'] > 0){
						$parentId = $user_data_array[0]['parent_id'];
						$qryParent ="select * FROM red_members WHERE `member_id` = '$parentId' AND `is_deleted`=0";
						$rsParent = $this->db->query($qryParent);	#execute query
						if($rsParent->num_rows() > 0){
							$arrParent = $rsParent->result_array();
							$rsParent->free_result();
							$user_data_array[0]['status'] = $arrParent[0]['status'];
							$user_data_array[0]['status_inactive_description'] = $arrParent[0]['status_inactive_description'];
							$user_data_array[0]['member_time_zone'] = $arrParent[0]['member_time_zone'];
						}
					}
					#check user's status description
					if(($user_data_array[0]['status']=="inactive")&&($user_data_array[0]['status_inactive_description']=="policy related")){						
						//Assign  session to user
						$this->session->set_userdata('member_id', $user_data_array[0]['member_id']);
						$this->session->set_userdata('member_username', $user_data_array[0]['member_username']);
						$this->session->set_userdata('member_email_address', $user_data_array[0]['email_address']);
						$this->session->set_userdata('member_autoresponder_status', $user_data_array[0]['autoresponder_status']);
						$this->session->set_userdata('member_time_zone',$user_data_array[0]['member_time_zone']);
						
						echo "inactive";
					}else{
						
						// Assign  session to user						
						// Permission level sessions
						if($user_data_array[0]['parent_id'] > 0){
							$this->session->set_userdata('member_staff', $user_data_array[0]['member_id']);						
							$this->session->set_userdata('member_id', $user_data_array[0]['parent_id']);
							$this->session->set_userdata('manage_campaigns', $user_data_array[0]['manage_campaigns']);						
							$this->session->set_userdata('manage_contacts', $user_data_array[0]['manage_contacts']);						
							$this->session->set_userdata('manage_stats', $user_data_array[0]['manage_stats']);						
							$this->session->set_userdata('manage_autoresponders', $user_data_array[0]['manage_autoresponders']);						
							$this->session->set_userdata('manage_signupforms', $user_data_array[0]['manage_signupforms']);
							$this->session->set_userdata('manage_extra', $user_data_array[0]['manage_extra']);
						}else{
							$this->session->set_userdata('member_staff', $user_data_array[0]['parent_id']);						
							$this->session->set_userdata('member_id', $user_data_array[0]['member_id']);
							$this->session->set_userdata('manage_campaigns', 1);						
							$this->session->set_userdata('manage_contacts', 1 );						
							$this->session->set_userdata('manage_stats', 1 );						
							$this->session->set_userdata('manage_autoresponders', 1);						
							$this->session->set_userdata('manage_signupforms', 1);
							$this->session->set_userdata('manage_extra', 1);
						}
						// Other sessions	
						$this->session->set_userdata('member_username', $user_data_array[0]['member_username']);
						$this->session->set_userdata('member_email_address', $user_data_array[0]['email_address']);
						$this->session->set_userdata('member_autoresponder_status', $user_data_array[0]['autoresponder_status']);
						$this->session->set_userdata('member_time_zone',$user_data_array[0]['member_time_zone']);
						$current_date=date("Y-m-d H:i:s");
						// Calculate number of days between current datetime and contact added datetime
						$date_diff = floor((strtotime($current_date) - strtotime($user_data_array[0]['created_on'])) / (60 * 60 * 24));
						//if(($date_diff<2)&&($user_data_array[0]['status']=='inactive')){
						if($user_data_array[0]['status']=='inactive'){
							$this->session->set_userdata('member_status','inactive');
						}else{
							$this->session->set_userdata('member_status','active');
						}
						# SAVE COOKIE
						if($this->input->post('rem') =='Y')
						$this->is_authorized->saveCookie( $user_data_array[0]['member_id'],$this->input->post('member_password', TRUE), $user_data_array[0]['member_password'] );

						$user_packages=array();
						if($user_data_array[0]['parent_id'] > 0){$user_data_array[0]['member_id'] = $user_data_array[0]['parent_id'] ;}
						$user_packages_array=$this->UserModel->get_user_packages(array('member_id'=>$user_data_array[0]['member_id'],'is_deleted'=>0));
						if(count($user_packages_array)<=0){
							// submit free pckage for register user
							$member_package_id=$this->UserModel->insert_member_package(array('member_id'=>$user_data_array[0]['member_id'],'package_id'=>-1,
							'credit_card_last_digit' =>NULL,
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
							$this->UserModel->update_user(array('package_id'=>$member_package_id),array('member_id'=>$user_data_array[0]['member_id']));
							// fetch package information for set in session
							$user_packages=array();
							$user_packages_array=$this->UserModel->get_user_packages(array('member_id'=>$user_data_array[0]['member_id'],'is_deleted'=>0));
						}
						foreach($user_packages_array as $package)
							$user_packages[]=$package['package_id'];
						$this->session->set_userdata('user_packages', $user_packages);

						#############################
						# create activity log		#
						#############################
						
						# create array for insert values in activty table
						$values=array('user_id'=>$user_data_array[0]['member_id'],	  'activity'=>'login:'.$this->is_authorized->getRealIpAddr() );
						$this->Activity_Model->create_activity($values);
						//Redirect to my account page
						echo 'success';
					}
				}else{
					// Assign message in case of invalid username or pass
					echo 'error';
				}
			}else{
				echo 'error';
			}
		}

		// Recieve any messages to be shown, when campaign is added or updated
		echo $messages=$this->messages->get();

		//Loads header, campaign and footer view.
		#$this->load->view('header',array('title'=>'Members Login'));
		if(!(isset($_POST['action'])))
		{
			$this->load->view('header_login',array('title'=>'Login','previous_page_url'=>$previous_page_url));		
			$this->load->view('user/user_login',array('user'=>$user_data_array,'messages' =>$messages,'title'=>'Login - Email Marketing'));
		}
		#$this->load->view('footer');

	}

	/*
		Controller to 'log out' user from the session
	*/

	function logout(){
		#############################
		# create activity log		#
		#############################
		
		# create array for insert values in activty table
		$values=array('user_id'=>$this->session->userdata('member_id'),	  'activity'=>'logout' );
		$this->Activity_Model->create_activity($values);
		$this->messages->add('You have logged out successfully', 'success');
		delete_cookie("rc_username");
		delete_cookie("rc_password");
		delete_cookie("rc_utcpa");


		$this->is_authorized->removeCookieTocken($this->session->userdata('member_id'));

		//Ends user session
		$this->session->unset_userdata('member_id');
		$this->session->unset_userdata('user_packages');
		$this->session->unset_userdata('member_username');
		$this->session->unset_userdata('member_email_address');
		$this->session->unset_userdata('member_autoresponder_status');
		$this->session->unset_userdata('member_time_zone');
		$this->session->sess_destroy();

		$home_pg = base_url() ;
		//Redirect to Login page
		redirect($home_pg);

	}

	/**
		Function to change password
	*/
	function change_password(){
		$thisMemberId = ($this->session->userdata('member_staff') > 0)?$this->session->userdata('member_staff'):$this->session->userdata('member_id');
		if($this->input->post('action')=='submit'){
			// Validation rules are applied
			$this->form_validation->set_rules('member_password', 'Current Password', 'required|trim');
			$this->form_validation->set_rules('member_new_password', 'New Password', 'required|min_length[6]|max_length[250]|trim|matches[member_confirm_password]');
			$this->form_validation->set_rules('member_confirm_password', 'Confirm New Password', 'required|min_length[6]|max_length[250]|trim');

			// To check form is validated
			if($this->form_validation->run()==true){
				// Fetch user data from database
				$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$thisMemberId));
				if(($this->is_authorized->is_login($this->input->post('member_password', TRUE), $user_data_array[0]['member_password']))){

					$user_credentails=array('member_password'=>$this->is_authorized->hashPassword($this->input->post('member_new_password',true)));

					$this->UserModel->update_user($user_credentails,array('member_id'=>$thisMemberId));
					$this->messages->add('Password Successfully Changed', 'success');
					redirect('user/change_password');
				}else{
					// Assign message in case of invalid username or pass
					$this->messages->add('Wrong Current Password', 'error');
				}
			}
		}

		// Recieve any messages to be shown, when campaign is added or updated
		$messages=$this->messages->get();
		$previous_page_url=base_url()."account/index";
		//Loads  change password  view.
		$this->load->view('header',array('title'=>'Change Password','previous_page_url'=>$previous_page_url));
		$this->load->view('user/user_change_password',array('messages' =>$messages));
		$this->load->view('footer-inner-red');
	}


	/**
	*	Function to reterive forgot password
	*/
	function forgot_password(){
		
		$user_data_array = array();
		// To check if form is submitted

		if($this->input->post('action')=='submit')
		{
			$this->form_validation->set_rules('email_address', 'Email', 'required|valid_email');

			if($this->form_validation->run()==true){

				$user_credentails=array('email_address'=>$this->input->post('email_address', TRUE));

				$user_data_array=$this->UserModel->get_user_data($user_credentails);

				// To check user have credentails matching in database
				if(count($user_data_array)){
					$random_token=$this->genRandomToken();
					// update user info
					$thisMid = $user_data_array[0]['member_id'];
					$dateNow = date("Y-m-d H:i:s");					
					$dtBefore24hrs = date("Y-m-d H:i:s",strtotime("- 24 hours"));					
					
					
					$this->db->query("update `red_members` set `reset_password_token`='$random_token', `reset_password_date`='$dateNow' where `member_id`='$thisMid' and (`reset_password_date` < '$dtBefore24hrs' or `reset_password_date` is NULL)");
					
					// SEND TRANSACTIONAL MAIL
					$random_token = $this->db->query("select reset_password_token from red_members where member_id='$thisMid'")->row()->reset_password_token;
						$email_to=$this->input->post('email_address', TRUE);

						// Your message
						$name = ($user_data_array[0]['first_name'] !='')?$user_data_array[0]['first_name']: $user_data_array[0]['member_username'];

						$textMessage ="Hello ".$name.", \r\n\r\n\r\n";
						$textMessage .="To reset your password for RedCappi, please click on the link below and enter your new password.\r\n";
						$textMessage .=site_url("user/reset_password/".$random_token)."\r\n\r\n\r\n";
						$textMessage .="Thanks,\r\n\r\n";
						$textMessage .="The RedCappi Team\r\n";
						
						$message ="Hello ".$name.", <br/><br/>";
						$message .="To reset your password for RedCappi, please click on the link below and enter your new password.<br/>";
						$message .="<a href='".site_url("user/reset_password/".$random_token)."'>".site_url("user/reset_password/".$random_token)."</a><br/><br/>";
						$message .="Thanks,<br/>";
						$message .="The RedCappi Team<br/><br/>";

						// send email using transactional_notification_helper
						send_tmail($email_to,SYSTEM_EMAIL_FROM, 'RedCappi', 'RedCappi Password Reset',$message,$textMessage,'redcappi.com');


						echo 'success';
				}
				else
				{
					// Assign message in case of invalid username or pass
					echo "error";
				}
			}else{
				echo "error";
			}
		}
	}

	function genRandomToken() {
		$length = 5;
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$string = '';
		for ($p = 0; $p < $length; $p++) {
			$string .= $characters[mt_rand(0, strlen($characters))];
		}
		//Check  random created token not exist in database
		$qry="select reset_password_token FROM red_members ";
		$user_qry=$this->db->query($qry);	#execute query
		$user_data_array=$user_qry->result_array();	#Fetch resut
		$random_token_arr=array();
		foreach($user_data_array as $user){
			$random_token_arr[]=$user['reset_password_token'];
		}
		if (in_array($string,$random_token_arr)){
			$this->genRandomToken();
		}else{
			return $string;
		}
	}
 
	/**
	  * Function to reset new password
	**/
	function reset_password($random_token=""){
		if($random_token){
			$dtBefore24hrs = date("Y-m-d H:i:s",strtotime("- 24 hours"));					
			// Fetch user data from database
			$user_data_array=$this->UserModel->get_user_data(array('reset_password_token'=>$random_token,'reset_password_date >'=>$dtBefore24hrs));
			if(count($user_data_array)<=0){
				redirect('user/thanks_msg/invalid');
			}
			$user_id=$user_data_array[0]['member_id'];
			//Protecting MySQL from query string sql injection Attacks
			if(is_numeric($user_id)){
				// To check if form is submitted
				if($this->input->post('submit')=='Submit')
				{
					// Validation rules are applied
					$this->form_validation->set_rules('password', 'New Password', 'required|min_length[2]|max_length[250]|trim|matches[confirm_password]');
					$this->form_validation->set_rules('confirm_password', 'Confirm New Password', 'required|min_length[2]|max_length[250]|trim');

					// To check form is validated
					if($this->form_validation->run()==true)
					{
						// Fetch user data from database
						$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$user_id));
						// To check user have credentails matching in database
						if(count($user_data_array))
						{
							$newpwd = $this->is_authorized->hashPassword($this->input->post('password',true));
							// Retrieve data posted in form posted by user using input class
							$input_array=array('member_password'=>$newpwd,'reset_password_token'=>'','reset_password_date'=>NULL);
							// Sends form input data to database via model object
							$this->UserModel->update_user($input_array,array('member_id'=>$user_id));
							redirect('user/thanks_msg');
						}else{
							redirect('/');
						}
					}
				}
				$previous_page_url= base_url();
				//$this->load->view('header',array('title'=>'Reset Password','previous_page_url'=>$previous_page_url));
				$this->load->view('user/user_reset_password',array('token'=>$random_token));
				//$this->load->view('footer-inner-red');
			}else{
				redirect('/');
			}
		}else{
			redirect('/');
		}
	}

	function thanks_msg($invalid_url=""){
		if($invalid_url!=""){
			$msg="<h3>Invalid Request</h3>";
		}else{
			$msg= '<h3>Your password has been changed</h3>';
		}
		$this->load->view('thanks_msg',array('msg'=>$msg));
	}
	/**
		Function to send  notification email to admin for upgradation of package
	**/
	function welcome_notification(){
		
		// Fetch user data from database
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));
		$user_info=array($user_data_array[0]['member_username'],$user_data_array[0]['member_password']);



		create_transactional_notification("welcome",$user_info,$user_data_array[0]['email_address']);
	}
	function register_member_to_redcappi_account($user_id=0){
		

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
					
					//create subscriber
					$qry = "INSERT INTO red_email_subscribers SET ";
					$flds = '';
					foreach($signup_data as $key=>$val)  $flds .= $key . ' = \'' . mysql_real_escape_string($val) . '\', ';
					$flds .=  'subscriber_created_by = '.$subscriber_created_by ;
					$qry .=  $flds .' ON DUPLICATE KEY UPDATE ' . $flds . ', is_deleted = 0,subscriber_status=1,is_signup=1 , subscriber_id=LAST_INSERT_ID(subscriber_id)';
					$this->db->query($qry);
					$last_inserted_id = $this->db->insert_id();
					if($subscriber_created_by==157){
						$sublistid=122;
					}else{
						$sublistid=78;
					}
					if ($last_inserted_id > 0 and $sublistid > 0){
						$input_array=array('subscriber_id'=>$last_inserted_id,'subscription_id'=>$sublistid);
						$this->Subscriber_Model->replace_subscription_subscriber($input_array);
					}
				}
			}
		}
	}
	function user_account_inactive_message(){
		$msg= '<h3><b>Oops... Yours account has been deactivated. Please contact us at <a href="mailto:'.SYSTEM_EMAIL_FROM.'" style="color:blue">'.SYSTEM_EMAIL_FROM.'</a> so that we can get you back up and strolling again.</b></h3>';
		$this->load->view('header',array('title'=>'Notification'));
		$this->load->view('user/user_message',array('msg'=>$msg));		
	}
	
	function get_message(){
		$mid = $this->session->userdata('member_id');
		$qryMsg = "select message_body from `red_messages` m INNER JOIN `red_member_message` mm ON m.`message_id`= mm.`message_id` where mm.`member_id`='{$mid}' and mm.is_deleted=0  GROUP BY m.`message_id`";
		$rsMsg = $this->db->query($qryMsg);
		if($rsMsg->num_rows()>0){
			foreach($rsMsg->result_array() as $recmsg){
			$strMsg .= '<h2 class="new-signup" style="text-align:left;">'.nl2br($recmsg['message_body']).'</h2>';
			}
		}
		$users_array=$this->UserModel->get_user_data(array('is_deleted'=>0,'member_id'=>$mid),1);
		if($users_array[0]['status'] == 'inactive' and $users_array[0]['status_inactive_description'] == 'unconfirmed'){
			$strMsg .= '<h2 class="new-signup">A confirmation message was sent to '.$this->session->userdata('member_email_address').'. Please activate your account by clicking the link in the email sent to you. <a style="color:#FF0000 !important; text-decoration:underline;" href="javascript:void(0);" class="resend_confirmation">Click here to resend confirmation</a></h2>';
		}
		echo $strMsg;
	}
	
	
	/**
	*	Function to verify email-id
	*/
	function verify($key=''){		
		// $this->db->query("update `red_member_from_email` set is_verified=1 where  `unique_string`='$key'");
		//redirect('user/verified/'.$key);		exit;		
		$rsEml = $this->db->query("select email_address,is_account_email, member_id from `red_member_from_email` where `unique_string`='$key' and domain_reason is null");
		if($rsEml->num_rows() > 0){
			$eml = $rsEml->row()->email_address;
			$memid = $rsEml->row()->member_id;
			$is_account_email = $rsEml->row()->is_account_email;
			if($is_account_email){
				$this->db->query("update red_members set email_address='$eml' where member_id='$memid'");
				$this->db->query("delete from  red_member_from_email  where member_id='$memid' and `unique_string`='$key'");
				redirect('user/confirmed/');
				exit;
			}
			$this->load->view('newsletter/verify',array('str'=>$key,'new_email'=>$eml));
		}else{
			redirect('user/verified/');
			exit;
		}
	}	
	function domain_reason(){	
		$uniqString = trim($this->input->post('hidString'));
		$domain_reason = trim($this->input->post('domain_reason_other'));
		$rsMusername = $this->db->query("select t1.member_username from red_members t1 inner join red_member_from_email t2 on t1.member_id=t2.member_id where t2.unique_string='$uniqString' and t2.domain_reason IS NULL");
		if($rsMusername->num_rows() > 0){
			$thisMember = $rsMusername->row()->member_username;			
			$this->db->query("update `red_member_from_email` set domain_reason='$domain_reason'  where  `unique_string`='$uniqString'");
			// admin alert ends
			$email_msg ="<p>Hello RC-Support,</p>";
			$email_msg.="<p>A new From-Email is added for verification with reason:<b>".$domain_reason."</b><br/> created by <b>".$thisMember."</b></p>";
			$email_msg.="<p>Select a choice to allow or disallow it from admin panel.</p>";
			$email_msg.='<p>Thanks,</p>';
			$email_msg.='<p>Redcappi Team</p>';
			$this->load->helper('admin_notification');
			
			$subject="Verify From-Email form by ".$thisMember;
			admin_notification_send_email('support@redcappi.com', 'tech@redcappi.com',"RedCappi", $subject,$email_msg,$email_msg);
			//admin_notification_send_email('pravinjha@gmail.com', SYSTEM_EMAIL_FROM,"RedCappi", $subject,$email_msg,$email_msg);
		// admin alert ends	
		}
		$rsMusername->free_result();		
		redirect('user/verified/');
		exit;
	}
	function verified(){
		$this->load->view('newsletter/verified');
	}
	function confirmed(){
		$this->load->view('newsletter/accountEmlVerified');
	}
/*	
	function verify($key=''){		
		
		$this->db->query("update `red_member_from_email` set is_verified=1 where  `unique_string`='$key'");
		
		redirect('user/verified/'.$key);
		exit;		
	}
	function verified($str=''){	
		$eml = '';
		if($str !='')
		$eml = $this->db->query("select email_address from `red_member_from_email` where `unique_string`='$str'")->row()->email_address;
		$this->load->view('newsletter/verified',array('new_email'=>$eml));
	}
*/
	function checkReferredMember($mid,$strUsername, $strEmail ){
            
                                  $siteid = $this->encrypt->decode($this->input->cookie('rc_ls_site_id'));
		$time_entered = $this->encrypt->decode($this->input->cookie('rc_ls_added_on'));
		If ($siteid != "") {
			$input_array['ls_site_id'] = $siteid;
			$input_array['ls_added_on'] = $time_entered;
		}
                
		$encodedTrackDetail = get_cookie('prc_rctrack');
		$decodedTrackDetail = $this->encrypt->decode($encodedTrackDetail);
		$arrTrackDetail = explode(':-:',$decodedTrackDetail);
		$thisSignupTime = $arrTrackDetail[0];
		$thisIp = $arrTrackDetail[1];
		$siteid = $arrTrackDetail[2]; 
		$this->db->query("update red_members set ls_site_id='$siteid' where member_id='$mid'");
                                  $ch_lead_source = '';
                                    If ($siteid != "") 
                                    {
                                            if ($siteid == 'google_adword')
                                            {
                                                $ch_lead_source = 'Google AdWords';
                                            }
                                            if ($siteid == 'capterra')
                                            {
                                                $ch_lead_source = 'Capterra';
                                            }
                                            if ($siteid == 'Powered by RedCappi Logo')
                                            {
                                                $ch_lead_source = 'Powered by RedCappi Email Logo';
                                            }

                                    }
                                    else
                                    {
                                        $ch_lead_source = 'Other';
                                    }
                         
//                                //Send Lead to Sales Force
//                                //set POST variables
//                                $url = 'https://www.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8';
//                                $ch_register_salesforce_id = '00D41000001Mhl4';
//                                $ch_register_email = $strEmail;
//                                $ch_register_username= $strUsername;
//                              
//                                
//                                $fields = array(
//                                        //'last_name'=>urlencode($ch_register_last_name),
//                                        //'first_name'=>urlencode($ch_register_first_name),
//                                        //'street'=>urlencode($ch_register_street),
//                                        //'city'=>urlencode($ch_register_city),
//                                        //'state'=>urlencode($ch_register_state),
//                                        //'zip'=>urlencode($ch_register_zip),
//                                        //'description'=>urlencode($ch_register_event_city),
//                                        'email'=>urlencode($ch_register_email),
//                                        'RedCappi_Username__c'=>urlencode($ch_register_username),
//                                        'lead_source'=>urlencode($ch_lead_source),
//                                        'FORM_TYP__c'=>urlencode('RedCappi Homepage Sign-up'),
//                                        'APHQ_Sync__c'=>urlencode('Yes'),
//                                        //'phone'=>urlencode($ch_register_phone),
//                                        //'Event_City__c' => urlencode($ch_register_event_city), // custom fields
//                                        //'Register_DOB__c' => urlencode($ch_register_dob),
//                                        //'Register_Emergency_Contact__c' => urlencode($ch_register_emergency_name),
//                                        //'Register_Emergency_Phone__c' => urlencode($ch_register_emergency_phone),
//                                        //'Register_Release__c' => urlencode($ch_register_agree),
//                                        //'No_Text__c' => urlencode($ch_register_no_text),
//                                        //'Under_18__c' => urlencode($ch_register_under_age),
//                                        //'Volunteer_Option__c' => urlencode($ch_register_volunteer_option),
//                                        //'Register_Release_Name__c' => urlencode($ch_register_signiture),
//                                        //'Register_Release_Date__c' => urlencode($ch_register_signiture_date),
//                                        'oid' => $ch_register_salesforce_id, // insert with your id 
//                                        'retURL' => urlencode('thank-you/'), // sending this just in case
//                                        'debug' => '0',
//                                        'debugEmail' => urlencode(""), // your debugging email
//                                );
//
//                                //url-ify the data for the POST
//                                foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
//                                rtrim($fields_string,'&');
//
//
//                                //open connection
//                                $ch = curl_init();
//
//                                //set the url, number of POST vars, POST data
//                                curl_setopt($ch,CURLOPT_URL,$url);
//                                curl_setopt($ch,CURLOPT_POST,count($fields));
//                                curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
//
//                                curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, FALSE);
//                                curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
//                                curl_setopt($ch,CURLOPT_FOLLOWLOCATION, TRUE);
//
//                                //execute post
//                                $result = curl_exec($ch);
//
//                                //close connection
//                                curl_close($ch);
	}
        
        function getredcappi(){
           
            
           $msgArray = array();
           //check username exists by loading user from database
           $username = $_POST['email'];
           $email = $username;
           $password = $_POST['Password'];
		   $this->Activity_Model->create_activity(array('user_id'=>4,'activity'=>"Log2 with pwd" . $password));
           $sql = "select member_username from red_members where member_username = '$username' or email_address= '$username' ";
            $rsusername_exists=$this->db->query($sql);
            
            if($rsusername_exists->num_rows()>0){
                print_r('Email Address already exists');
            }
            else{
                $msgArray = $this->registerGetRedCappi($email, $password);
            }
//         
//           $emaillength = strlen($email);
//           $passlength  = strlen($password);
//
//           If ($emaillength == 0 || $passlength == 0 || $email == null || $password == null || $email == "" || $password == ""){
//               
//               if ($emaillength == 0 || $email == null || $email == ""){
//                   array_push($msgArray,'Email requirement not met');
//               }
//               
//               if ($passlength == 0 || $password == null ||  $password = ""){
//                   
//                    array_push($msgArray,'Password requirement not met');
//               
//               }
//               
//           }
//           else{
               
//           }
           
           
           
        
        }
        
        function getredcappi123(){
           
            $formdata = ARRAY();
            foreach ($_POST as $key => $value) {
                $value = stripslashes($value);
                $formdata[$key]=$value;
            }
            
            $email = $formdata['id123-control29009436'];
            $password = $formdata['id123-control29010304'];
            $firstname = $formdata['id123-control29010370'];
            $phone = $formdata['id123-control29010382'];
            
           $msgArray = array();
           //check username exists by loading user from database
           $username = $email;
           
           
           $sql = "select member_username from red_members where member_username = '$username' or email_address= '$username' ";
            $rsusername_exists=$this->db->query($sql);
            
            if($rsusername_exists->num_rows()>0){
                array_push($msgArray,'Email Address already exists');
            }
            else{
                $msgArray = $this->registerGetRedCappi($email, $password, $firstname, $phone );
            }

           
           
           
           echo var_dump($msgArray);
        }
	

}
	




/* End of file */
?>
