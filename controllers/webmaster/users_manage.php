<?php
class Users_Manage extends CI_Controller{
	function __construct(){
		parent::__construct();
		$this->load->helper('cookie');		

		// Load the user model which interact with database
		$this->load->model('UserModel');
		$this->load->model('Affiliate_Model');
		$this->load->model('newsletter/Subscriber_Model');
		$this->load->model('ConfigurationModel');					
		$this->load->model('Activity_Model');		
		$this->load->model('webmaster/Croncb_Model');
		$this->load->model('newsletter/subscription_Model');
		$this->load->model('webmaster/MessagesModel');
       
		$this->PAYPAL_PASSWORD =  $this->config->item('PAYPAL_PASSWORD') ;
        $this->PAYPAL_SIGNATURE =  $this->config->item('PAYPAL_SIGNATURE') ;
        $this->PAYPAL_USERNAME =  $this->config->item('PAYPAL_USERNAME') ;
		$this->PAYPAL_URL = $this->config->item('PAYPAL_URL') ;


        $this->session->set_userdata('webmaster_id', 1);
		$this->CreditPackage = $this->UserModel->get_packages_data(array('package_recurring_interval'=>'credit'));
		$this->CreditId = $this->CreditPackage[0]['package_id'];
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');
		# HTTPS/SSL enabled
		force_ssl();
	}
	
	function index(){
		$this->users_list();
	}
	
	function users_list($start=0){
		if(!is_numeric($start)){ 
			$_POST['mode']	= 'search';
			$_POST['field_name']= 'member_username';
			$arrUsername = explode('%20',trim($start));
			$_POST['field_value']= $arrUsername[0];	
		}

		$fetch_conditions_array=array('rm.parent_id'=>0,'rm.is_deleted'=>0);
		
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/users_manage/users_list';
		$config['total_rows']=$this->UserModel->get_user_count($fetch_conditions_array);		
		$config['per_page']=30;
		$config['uri_segment']=4;
		
		$this->pagination->initialize($config);
		//Create paging inks
		$paging_links=$this->pagination->create_links();
		$user_data_array=$this->UserModel->get_user_data($fetch_conditions_array,$config['per_page'],$start);
		
		
		$i=0;
		foreach($user_data_array as $user){
			if($user['package_id']==0){
				$user['package_id']=-1;
				# submit free pckage for user
				$member_package_id=$this->UserModel->insert_member_package(array('member_id'=>$user['member_id'],'package_id'=>-1,'credit_card_last_digit' =>NULL,
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
				$this->UserModel->update_user(array('package_id'=>$member_package_id),array('member_id'=>$user['member_id']));
			}
			// check whether contacts analyzed or not
			$user_data_array[$i]['is_analyzed'] = $this->db->query("select member_id from red_subscriber_analysis where member_id='".$user['member_id']."'")->num_rows();
			if(!$user_data_array[$i]['is_analyzed']){
				$this->db->query("insert into red_subscriber_analysis set member_id='".$user['member_id']."'");
				$user_data_array[$i]['is_analyzed'] = 1;
			}
			// check whether user is from affiliate or not
			$user_data_array[$i]['affiliate_status'] =$this->Affiliate_Model->getAffiliateStatus($user['member_id']);		
			$user_data_array[$i]['is_via_adwords'] =$this->db->query("select utm_source from red_member_track where member_id='".$user['member_id']."'")->row()->utm_source;
			
			$package_detail=$this->UserModel->get_user_package(array('member_id'=>$user['member_id']));		
			$user_data_array[$i]['payment_type'] = $package_detail[0]['payment_type'];
			$user_data_array[$i]['package']=$package_detail[0]['package_min_contacts']."-".$package_detail[0]['package_max_contacts'];
			$user_data_array[$i]['next_payement_date']=$package_detail[0]['next_payement_date'];
			if($user['status']=="unconfirmed"){
				$user_data_array[$i]['status_description']="Inactive- Unconfirmed";
			}elseif($user['status']=="inactive"){
				$user_data_array[$i]['status_description']="Inactive-Policy related";
			}elseif(($package_detail[0]['package_id']>0)&&($user['cancel_subscription_date'] !== NULL)&&($user['status']=="active")){
				$user_data_array[$i]['status_description']="Active-Paid <br/>(Canceled on ".$user['cancel_subscription_date'].")";
			}elseif(($package_detail[0]['package_id']>0)&&($package_detail[0]['next_payement_date']<date("Y-m-d H:i:s"))&&($package_detail[0]['is_admin']<=0)&&($user['status']=="active")){
				$user_data_array[$i]['status_description']="Inactive-Failed cc";
			}elseif($package_detail[0]['package_id']>0){
				$user_data_array[$i]['status_description']="Active-Paid";
			}elseif($package_detail[0]['package_id']<=0){
				$user_data_array[$i]['status_description']="Active-Free";
			}
			// Collect condition array for fetch subscribers from database		
			$user_data_array[$i]['contacts']=$this->Subscriber_Model->get_subscriber_count(array('res.subscriber_created_by'=>$user['member_id'], 'res.is_deleted'=>0, 'res.subscriber_status'=>1));	
			$user_data_array[$i]['fresh_contacts']=$this->Subscriber_Model->get_subscriber_count(array('res.subscriber_created_by'=>$user['member_id'], 'res.is_deleted'=>0, 'res.subscriber_status'=>1, 'res.sent'=>0));			
			$user_data_array[$i]['validate_contacts'] = $this->Subscriber_Model->get_subscriber_count(array('res.subscriber_created_by' => $user['member_id'], 'res.is_deleted' => 0, 'res.subscriber_status' => 1, 'res.dv_grade is Not NULL' => NULL));
			
			
			$user_data_array[$i]['total_used_contacts']=$this->Subscriber_Model->get_total_used_subscriber_count(array('res.subscriber_created_by'=>$user['member_id']));
			$user_data_array[$i]['arr_message']	= $this->MessagesModel->assignable_messages($user['member_id']);	
			
			$i++;
		}
		// Fetch Packages from database
		$packages_count=$this->UserModel->get_packages_count(array('package_deleted'=>0,'package_status'=>1));
		$packages=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),$packages_count);
		// Recieve any messages to be shown, when campaign is added or updated
		$messages=$this->messages->get();
		if($_POST['mode']=='search' AND !isset($_POST['btn_cancel'])){
			$search_array['field_name']=$_POST['field_name'];
			$search_array['field_value']= trim($_POST['field_value']);
			$search_array['select_package']=$_POST['select_package'];			
			$search_array['select_status']=$_POST['select_status'];			
		}
		$logo_link="webmaster/dashboard_stat";
		//Loads header, users listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Users','logo_link'=>$logo_link));
		$this->load->view('webmaster/users_list',array('users'=>$user_data_array,'paging_links'=>$paging_links,'messages' =>$messages,'search'=>$search_array,'packages'=>$packages));
		$this->load->view('webmaster/footer');
	}
	
	function user_edit($id){
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');		
		$user_data_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit'){
			// Validation rules are applied
			$this->form_validation->set_rules('username', 'Username', 'required');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
			
			//Prepare member array from posted data
			$member_data = array(
				'member_id' => $this->input->post('member_id',true),
				'member_username' => $this->input->post('username',true),
				'email_address' => $this->input->post('email',true),
				'phone_number' => $this->input->post('phone',true),
				'first_name' => $this->input->post('first_name',true),
				'last_name' => $this->input->post('last_name',true),
				'address_line_1' => $this->input->post('address1',true),
				'address_line_2' => $this->input->post('address2',true),
				'city' => $this->input->post('city',true),
				'state' => $this->input->post('state',true),
				'zipcode' =>$this->input->post('zipcode',true),				
				'status' => $this->input->post('status',true),
				'rc_logo' => $this->input->post('rc_logo',true),
				'member_dnm' => $this->input->post('member_dnm',true),
				'member_unresponsive' => $this->input->post('member_unresponsive',true),
				'unresponsive_release_count' => $this->input->post('unresponsive_release_count',true),
				'ls_site_id' => $this->input->post('referrer',true),
				
			);	
			if('yes' == $this->input->post('stop_campaign_approval',true)){
				$member_data['stop_campaign_approval']= 1;				
				$member_data['is_authentic'] = 0;
				$thisDate = date("Y-m-d H:i:s");
				$member_data['authenticated_on'] = "$thisDate"; 		
			}else{
				$member_data['stop_campaign_approval']= 0;
			}
			$thisDate = date("Y-m-d H:i:s");
		if($is_authentic > 0)
		$this->UserModel->update_user(array('is_authentic'=>$is_authentic,'authenticated_on' => "$thisDate",'unauthentic_contacts'=>0),array('member_id'=>$member_id));	
		
		
		
			$member_data['show_sent_counter']= ('yes' == $this->input->post('show_sent_counter',true))? 1 : 0;
			$member_data['is_pausable']= ('yes' == $this->input->post('is_pausable',true))? 1 : 0;
			$member_data['apply_unauthentication_message']= ('yes' == $this->input->post('apply_unauthentication_message',true))? 1 : 0;
			$member_data['reply_to_enabled']= ('yes' == $this->input->post('reply_to_enabled',true))? 1 : 0;
			
			$member_package_data = array(
				'max_campaign_quota' => $this->input->post('max_campaign_quota',true),
				'campaign_sent_counter' => $this->input->post('campaign_sent_counter',true),
				'user_quota_multiplier' => $this->input->post('user_quota_multiplier',true),
				'package_max_contacts' => $this->input->post('package_max_contacts',true),
				'quota_multiplier' => $this->input->post('quota_multiplier',true),				
				'next_payement_date' => $this->input->post('next_payement_date',true),
				'coupon_code_used' => $this->input->post('coupon_code',true),
				'coupon_attached_on' => $this->input->post('coupon_attached_on',true),
				'cancel_reason' => $this->input->post('cancel_reason',true),
				'cancel_type' => $this->input->post('cancel_type',true),
			);
			#echo "<pre>";print_R($member_package_data);exit;
			
			if($this->form_validation->run()) {
				
				$member_id=$this->input->post('member_id');
				
				//check username exists by loading user from database
				$username_exists=$this->UserModel->get_user_data(array('member_username'=>$this->input->post('username',true),'is_deleted'=>0,'member_id !='=>$member_id));
				
				//check email exists by loading email from database
				$email_exists=$this->UserModel->get_user_data(array('email_address'=>$this->input->post('email',true),'is_deleted'=>0,'member_id !='=>$member_id));	
				
				//check username exists
				if(count($username_exists)) {
					$this->messages->add('Username already exists', 'error');				
				//check email exists
				}elseif(count($email_exists)) {
					$this->messages->add('Email Address already exists', 'error');					
				}else{
					if($this->input->post('status',true)=='inactive'){
						$member_data['status_inactive_description'] = "policy related";
					}else{
						$member_data['status_inactive_description'] = "";
					}
					$this->UserModel->update_user($member_data,array('member_id'=>$member_id));
					if($this->input->post('status',true)=='inactive'){
						$strStatus = 0;						
					}else{
						$strStatus = 1;
					}
					$arrUpdatePackage = array('is_status'=>$strStatus,'max_campaign_quota'=>$member_package_data['max_campaign_quota'],'campaign_sent_counter'=>$member_package_data['campaign_sent_counter'],'user_quota_multiplier'=>( $member_package_data['user_quota_multiplier'] / $member_package_data['quota_multiplier']));
					if($member_package_data['next_payement_date'] != '')$arrUpdatePackage['next_payement_date']=$member_package_data['next_payement_date'];
					
					$arrUpdatePackage['coupon_code_used']=$member_package_data['coupon_code_used'];
					if($member_package_data['coupon_attached_on'] != '')$arrUpdatePackage['coupon_attached_on']=$member_package_data['coupon_attached_on'];
					
					$arrUpdatePackage['cancel_reason']=$member_package_data['cancel_reason'];
					if($member_package_data['cancel_reason'] != '')$arrUpdatePackage['cancel_reason']=$member_package_data['cancel_reason'];
					
					$arrUpdatePackage['cancel_type']=$member_package_data['cancel_type'];
					if($member_package_data['cancel_type'] != '')$arrUpdatePackage['cancel_type']=$member_package_data['cancel_type'];
					
					$this->UserModel->update_member_package($arrUpdatePackage,array('member_id'=>$member_id));
					
					$this->messages->add('User updated successfully', 'success');
					redirect('webmaster/users_manage/users_list');
				}
			}			
			$user_data_array=$member_data;
			$user_package=$member_package_data;
		}
		
		if(!count($user_data_array)) {
			$config['per_page']=10;
			$fetch_conditions_array=array('member_id'=>$id,'is_deleted'=>0);
			$user_data_array=$this->UserModel->get_user_data($fetch_conditions_array,$config['per_page']);
			$user_data_array=$user_data_array[0];
			
			
		$package_detail=$this->UserModel->get_user_package(array('member_id'=>$id));	
		#echo "<pre>";print_R($package_detail);exit;
		$user_package =  array('max_campaign_quota'=>$package_detail[0]['max_campaign_quota'],'campaign_sent_counter'=>$package_detail[0]['campaign_sent_counter'],'user_quota_multiplier'=>$package_detail[0]['user_quota_multiplier'],'package_max_contacts'=>$package_detail[0]['package_max_contacts'],'quota_multiplier'=>$package_detail[0]['quota_multiplier'],'next_payement_date'=>$package_detail[0]['next_payement_date'],'coupon_code_used'=>$package_detail[0]['coupon_code_used'],'coupon_attached_on'=>$package_detail[0]['coupon_attached_on'],'cancel_reason'=>$package_detail[0]['cancel_reason'],'cancel_type'=>$package_detail[0]['cancel_type']);
		}
		$transaction_count=$this->UserModel->get_transaction_count(array('user_id'=>$id),'like');
		$user_transactions=$this->UserModel->get_user_transactions(array('user_id'=>$id),$transaction_count,0,'like');
		$transaction_count=count($user_transactions);
		
		$rsReferrer = $this->db->query("select referrer_name from red_member_referrer where is_deleted=0");
		$arrReferrer = $rsReferrer->result_array();
		$rsReferrer->free_result();
		$arrReferrerName = array(''=>'Select Referrer');
		foreach($arrReferrer as $r){
			 $thisRName = $r['referrer_name'];
			$arrReferrerName[$thisRName] = "$thisRName";
		}
		//echo"<pre>";
		//print_r($arrReferrerName);
		//echo"</pre>";
		// Recieve any messages to be shown, when campaign is added or updated
		$messages=$this->messages->get();
	
	
		$logo_link="webmaster/dashboard_stat";
		//Loads header, users edit  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Users','logo_link'=>$logo_link));
		$this->load->view('webmaster/user_edit',array('user'=>$user_data_array,'messages' =>$messages,'transaction_count'=>$transaction_count,'user_package'=>$user_package, 'referrer'=>$arrReferrerName));
		$this->load->view('webmaster/footer');
	}
	
	/* Start Code for email_validation - CB */

    function email_validation() {
		$id = $_POST['user_id'];
		$is_paid = $_POST['is_paid'];
        $fetchData = $this->Croncb_Model->selectCron( array('rc_member_id' => $id, 'rc_status' => 0 ));
		
		if (count($fetchData) > 0) {            
			/*$last_subscriber_id = $fetchData[0]['last_subscriber_id'];
			$inputArray = array('rc_member_id' => $id, 'rc_createddate' => date('Y-m-d h:i:s'), 'rc_updateddate' => date('Y-m-d h:i:s'), 'rc_status' => 0,'last_subscriber_id'=> $last_subscriber_id, 'is_paid'=>$is_paid );
            $this->Croncb_Model->insertCron($inputArray);
            $jsonData = array('resp'=> 1,'status'=>"Success",'message' => 'User marked for DataValidation');
			*/
            $jsonData = array('resp'=> 0,'status'=>"Failed",'message' => 'User already marked for DataValidation');
        } else {
            $inputArray = array('rc_member_id' => $id, 'rc_createddate' => date('Y-m-d h:i:s'), 'rc_updateddate' => date('Y-m-d h:i:s'), 'rc_status' => 0, 'is_paid'=>$is_paid );
            $this->Croncb_Model->insertCron($inputArray);
            $jsonData = array('resp'=> 1,'status'=>"Success",'message' => 'User marked for DataValidation');			
        }
		echo json_encode($jsonData);exit;
    }

    

    function user_batch($id) {

       $new_filter_query = "SELECT COUNT(subscriber_created_by) AS total_member ,dv_createddate,dv_csv_count,dv_grade,dv_singlecsv_run,dv_csv_name,rc_member_id,dv_id,batch.dv_batch_grade,dv_scheduled FROM red_dv_cron_setup AS csv
                                      JOIN red_dv_csv AS batch ON batch.dv_rc_id = csv.rc_id
                                      JOIN red_email_subscribers AS usr ON usr.subscriber_created_by = rc_member_id and is_deleted = 0
									  WHERE csv.rc_member_id = '" . $id . "'
                                      GROUP BY dv_csv_name,rc_member_id,dv_id,batch.dv_batch_grade,dv_scheduled order by dv_createddate desc ";

        $rsContacts = $this->db->query($new_filter_query);
        if ($rsContacts->num_rows() > 0) {
            foreach ($rsContacts->result_array() as $row)
                $subscriber_data[] = $row;
        }




        $logo_link = "webmaster/dashboard_stat";
        //Loads header, users listing  and footer view.
        $this->load->view('webmaster/header', array('title' => 'Manage Users', 'logo_link' => $logo_link));
        $this->load->view('webmaster/user_batch', array('subscriber_data' => $subscriber_data, 'paging_links' => $paging_links, 'messages' => $messages, 'search' => $search_array, 'packages' => $packages));
        $this->load->view('webmaster/footer');
    }

    /* End Code for user_batch - CB */
    
    
    
     /* Start Code for scheduled cron of batch - CB */

    function user_cron_batch($id,$memberid) {

        $new_filter_query = "SELECT * FROM `red_dv_csv` where dv_id = '" . $id . "' and dv_scheduled = 0";
        $rsContacts = $this->db->query($new_filter_query);

        
        if ($rsContacts->num_rows() > 0) {
            $input_array = array(
                'dv_scheduled' => 1,
                'dv_scheduled_date' => date('Y-m-d')
            );
            
            $conditions_array = array(
                'dv_id' => $id
            );
            
            $this->Croncb_Model->update_CsvLog($input_array,$conditions_array);
            redirect('webmaster/users_manage/user_batch/' . $memberid);
        }else{
            ?>
                <script>
                 alert('Cron already scheduled');
                window.location.href = "<?php echo $base_url(); ?>webmaster/users_manage/user_batch/<?php echo $memberid; ?>";
               </script>
               
                 <?php 
        }

    }
    
    /* End Code for user_batch - CB */
    
	
	function user_delete($id){
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');
		#Delete user's data from database
		$this->UserModel->delete_user_account($id);
		# Delete user's account permanetly from database
		$this->UserModel->delete_user($id);
		// Assign  success message by message class
		$this->messages->add('User deleted successfully', 'success');
		// Redirect to listing of campaigns
		redirect('webmaster/users_manage/users_list');
	}
	
	
	/**
	*	Send registration confirmation email
	*/
	function user_confirmation_notification($user_id,$redirect=""){
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');
		
		// Fetch user data from database
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$user_id));
		$to_email=$user_data_array[0]['email_address'];
		$to_username=$user_data_array[0]['member_username'];
		$user_password=$user_data_array[0]['member_password'];
		$user_id=$this->is_authorized->base64UrlSafeEncode($user_id);
		$user_info=array($user_id,$to_email,$to_username,$user_password);
		$this->load->helper('transactional_notification');
		create_transactional_notification("user_registration_by_admin",$user_info);
	}
 
	function user_add_package($id){
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');
		//To check form is submitted
		if($this->input->post('action')=='save'){
			$this->form_validation->set_rules('packageId', 'Package', 'required');
			$this->form_validation->set_rules('gateway', 'Gateway', 'required');
			$this->form_validation->set_rules('payment_type', 'Payment Type', 'required');
			
			// To check form is validated
			if($this->form_validation->run()==true){
				$member_id		= $this->input->post('member_id',true);				
				$package_id		= $this->input->post('packageId',true);
				$amount_paid	=  $this->input->post('package_price',true);
				$gateway		= $this->input->post('gateway',true);
				$payment_type		= $this->input->post('payment_type',true);
				$transaction_date	= $this->input->post('transaction_date',true);
				$year = date("Y") + 100;
				if($this->CreditId != $package_id)
					$next_payment_date	= $this->input->post('next_payment_date',true);
				else
					$next_payment_date	= $year.'-'.date('m-d');
				
					
				$selected_package_array	= $this->UserModel->get_packages_data(array('package_id'=>$package_id));					
				$selected_package_price	= $selected_package_array[0]['package_price'];
				
				if($package_id < 0){
					$this->UserModel->update_member_package(array('package_id'=>$package_id),array('member_id'=>$member_id));
				}else{	
					if($gateway =='ADMIN')	
					$this->UserModel->update_member_package(array('package_id'=>$package_id,'is_payment'=>1,'member_payment_declined_count'=>0,'amount'=>$selected_package_price,'is_admin'=>1,'next_payement_date'=>$next_payment_date),array('member_id'=>$member_id));
					else
					$this->UserModel->update_member_package(array('package_id'=>$package_id,'is_payment'=>1,'member_payment_declined_count'=>0,'amount'=>$selected_package_price,'is_admin'=>0,'next_payement_date'=>$next_payment_date),array('member_id'=>$member_id));
				}
				// Add transaction if amount is more than 0
				if($amount_paid > 0){
					$data = array('user_id'=>$member_id,'package_id'=>$package_id,'amount_paid'=>$amount_paid,'gateway'=>$gateway,'status'=>'SUCCESS','payment_type'=>$payment_type,'gateway_response'=>$gateway,'transaction_date'=>$transaction_date);
					$this->UserModel->insert_payment_transactions( $data );					
				}
				$this->UserModel->updateMemberCampaignQuota($member_id);
				$this->messages->add('User package added successfully', 'success');					
				redirect('webmaster/users_manage/users_list');				
			}
		}
		
		// Fetch user data from database
		$packages_count=$this->UserModel->get_packages_count(array('package_deleted'=>0,'package_status'=>1));
		$packages=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),$packages_count);

		//Fetch User Data from database
		$user_packages=$this->UserModel->get_user_packages(array('member_id'=>$id));		
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$id),1,0);
		
		
		// Recieve any messages to be shown, when package is added or updated
		$messages=$this->messages->get();
		
		$logo_link="webmaster/dashboard_stat";
		//Loads header, users edit  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Add Package','logo_link'=>$logo_link));
		$this->load->view('webmaster/user_add_package',array('packages'=>$packages,'user_packages'=>$user_packages[0],'user_data_array'=>$user_data_array,'messages' =>$messages,'member_id'=>$id));
		$this->load->view('webmaster/footer');
	}
	
	
	function user_view_package($id){
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');
		//To check form is submitted
		if($this->input->post('action')=='save'){
			// To check form is validated
			if($this->form_validation->run()==true){
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
	
	function user_delete_package($id,$user_id){
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');
		$this->UserModel->delete_user_package(array('red_member_package_id'=>$id));		
		$this->messages->add('Package deleted for user successfully', 'success');
		redirect('webmaster/users_manage/user_view_package/'.$user_id);
	}
	function view($id=0){
		$config['per_page'] = 10;
		if($this->session->userdata('webmaster_id')==''){
			echo "<div style=\"margin:20px;width:240px;\">Your Session seems to have expired. Please try refreshing the page to login again.</div>";
		}else{
			$fetch_conditions_array=array('member_id'=>$id,'is_deleted'=>0);
			$user_data_array=$this->UserModel->get_user_data($fetch_conditions_array,$config['per_page']);
			$this->load->view('webmaster/user_view',array('user'=>$user_data_array[0]));
		}
	}
	
	/**
	*	function to get  contacts using paging
	*/
	function contact_details_temp(){
		$webmails = array('gmail','yahoo','hotmail','aol'); 
		//$users = array(368 , 3788 , 4055 , 4118 , 4208 , 4486 , 5025 , 5086 , 5193 , 5305 , 5467);
		$users = array(562,  612, 1436, 1523, 1573, 1659, 2060, 2270, 2282, 2316, 2467, 2595, 2703, 2719, 2913, 3197, 3243, 3252, 3302, 3352, 3379, 3382, 3439, 3445, 3458, 3487, 3491, 3515, 3523, 3530, 3550, 3571, 3588, 3615, 3672, 3833, 3870, 3871, 3876, 3882, 3917, 4080, 4138, 4166, 4193, 4211, 4319, 4331, 4345, 4350, 4353, 4368, 4462, 4466, 4483, 4566, 4596, 4666, 4806, 4919, 4934, 5073, 5109, 5180, 5204, 5251, 5267, 5289, 5316, 5340, 5377, 5390, 5443);
		foreach($users as $mid){
			$strTblBody .= '<tr><td>'.$mid.'</td>';
			foreach($webmails as $webdomain){
				$webdomainClause = " and subscriber_email_domain like'".$webdomain."%' "; 
				$rsActiveContacts = $this->db->query("select count(subscriber_id) as active_contacts from red_email_subscribers where subscriber_created_by='$mid' and subscriber_status=1 and is_deleted=0 $webdomainClause");	
				$active_contacts = $rsActiveContacts->row()->active_contacts;
				$rsActiveContacts->free_result();
						
				$strTblBody .= '<td><b>'.strtoupper($webdomain).'</td><td>'.$active_contacts.'</td>';
			}
			$strTblBody .= '</tr>';
		}
		
		
		echo '<div style="padding:20px;">
		<table cellspacing="0" cellpadding="4" border="1"><tr><th>Userid</th><th>Domain</th><th>Contacts</th><th>Domain</th><th>Contacts</th><th>Domain</th><th>Contacts</th><th>Domain</th><th>Contacts</th></tr>' .$strTblBody .'</table></div>';	
	}
	function contact_details($mid=0){
		$webmails = array('gmail','yahoo','hotmail','msn','aol','all'); 
		$strInsertKey = "insert into red_member_contact_detail SET member_id='$mid',";
		$strInsert = '';
		foreach($webmails as $webdomain){
			$webdomainClause =($webdomain != 'all')? " and substring_index(subscriber_email_address,'@',-1) like'".$webdomain."%' " : '';
			$rsActiveContacts = $this->db->query("select count(subscriber_id) as active_contacts from red_email_subscribers where subscriber_created_by='$mid' and subscriber_status=1 and is_deleted=0 $webdomainClause");	
			$active_contacts = $rsActiveContacts->row()->active_contacts;
			$rsActiveContacts->free_result();
			 
			$rsFreshContacts = $this->db->query("select count(subscriber_id) as fresh_contacts from red_email_subscribers where subscriber_created_by='$mid' and subscriber_status=1 and is_deleted=0 and `sent` =0 $webdomainClause");	
			$fresh_contacts = $rsFreshContacts->row()->fresh_contacts;
			$rsFreshContacts->free_result();
			 
			$rsResponsiveContacts = $this->db->query("select count(subscriber_id) as responsive_contacts from red_email_subscribers where subscriber_created_by='$mid' and subscriber_status=1 and is_deleted=0 and `read` > 0  $webdomainClause");	
			$responsive_contacts = $rsResponsiveContacts->row()->responsive_contacts;
			$rsResponsiveContacts->free_result();
			$unresponsive_contacts = $active_contacts - $fresh_contacts - $responsive_contacts;
			
//			$strInsert .=  $webdomain."_total = '$active_contacts',  ".$webdomain."_unsent = '$fresh_contacts',  ."$webdomain."_responsive = '$responsive_contacts',  ."$webdomain."_unresponsive = '$unresponsive_contacts',";
			$strInsert .=  " {$webdomain}_total = '$active_contacts',  {$webdomain}_unsent = '$fresh_contacts', {$webdomain}_responsive = '$responsive_contacts', {$webdomain}_unresponsive = '$unresponsive_contacts',";
			$strTblBody .= '<tr><td><b>'.strtoupper($webdomain).'</td><td>'.$active_contacts.'</td><td>'.$fresh_contacts.'</td><td>'.$responsive_contacts.'</td><td>'.$unresponsive_contacts.'</td></tr>';
		}
		$this->db->query($strInsertKey.rtrim($strInsert,',') . "  ON DUPLICATE KEY UPDATE ".rtrim($strInsert,','));
		echo '<div style="padding:20px;">
		<table cellspacing="0" cellpadding="4" border="1"><tr><th>Domain</th><th>Active</th><th>Fresh</th><th>Responsive</th><th>Un-responsive</th>' .$strTblBody .'</table></div>';	
	}
	
	function get_contacts($user_id=0,$start=0){
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');
		$fetch_condiotions_array=array('res.subscriber_created_by'=>$user_id, 'res.is_deleted'=>0, 'res.subscriber_status'=>1);
		
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/users_manage/get_contacts/'.$user_id;
		$config['total_rows']=$this->UserModel->get_subscriber_count($fetch_condiotions_array);
		$config['per_page']=20;
		$config['uri_segment']=5;

		// Initialize paging with above parameters
		$this->pagination->initialize($config);
		
		//Create paging inks
		$paging_links=$this->pagination->create_links();	
		// Get Contacts
		$subscriber_info=$this->UserModel->get_subscriber_data($fetch_condiotions_array,$config['per_page'],$start);

		$contacts_array=array();

		if($_POST['mode']=='search' AND !isset($_POST['btn_cancel'])){
			$contacts_array['keyword']=$_POST['keyword'];
			$contacts_array['subscriber_email_address']=$_POST['subscriber_email_address'];
			$contacts_array['subscriber_name']=$_POST['subscriber_name'];
		}
		$logo_link="webmaster/dashboard_stat";
		$this->load->view('webmaster/header',array('title'=>'Contacts','logo_link'=>$logo_link));
		$this->load->view('webmaster/subscriber_list',array('email_subscribers'=>$subscriber_info,'paging_links'=>$paging_links,'contacts'=>$contacts_array,'user'=>1,'user_id'=>$user_id));
		$this->load->view('webmaster/footer');
	}
	/**
	*	function to get  contacts using paging
	*/
	function analyseNewContacts($mid=0){
		$this->db->query("update `red_subscriber_analysis` set reanalyse_it=1,`analysis_date`=now() where member_id='$mid'");
		echo '<div style="padding:20px;">New contacts will be analysed soon and you will get an email alert.</div>';
	}
	function reanalysis($mid=0){
		$this->db->query("update `red_subscriber_analysis` set reanalyse_it=1, `analysis_date`=now(), 
		`yahoo_total`=0, `yahoo_new`=0, `yahoo_existing`=0, `yahoo_responsive`=0, `yahoo_unresponsive`=0, `yahoo_bounce`=0, `yahoo_complaint`=0, `yahoo_unsubscribe`=0, `yahoo_spam`=0,
		`gmail_total`=0, `gmail_new`=0, `gmail_existing`=0, `gmail_responsive`=0, `gmail_unresponsive`=0, `gmail_bounce`=0, `gmail_complaint`=0, `gmail_unsubscribe`=0, `gmail_spam`=0,
		`hotmail_total`=0, `hotmail_new`=0, `hotmail_existing`=0, `hotmail_responsive`=0, `hotmail_unresponsive`=0, `hotmail_bounce`=0, `hotmail_complaint`=0, `hotmail_unsubscribe`=0, `hotmail_spam`=0,
		`aol_total`=0, `aol_new`=0, `aol_existing`=0, `aol_responsive`=0, `aol_unresponsive`=0, `aol_bounce`=0, `aol_complaint`=0, `aol_unsubscribe`=0, `aol_spam`=0,
		`msn_total`=0, `msn_new`=0, `msn_existing`=0, `msn_responsive`=0, `msn_unresponsive`=0, `msn_bounce`=0, `msn_complaint`=0, `msn_unsubscribe`=0, `msn_spam`=0,
		`all_total`=0, `all_new`=0, `all_existing`=0, `all_responsive`=0, `all_unresponsive`=0, `all_bounce`=0, `all_complaint`=0, `all_unsubscribe`=0, `all_spam`=0
		where member_id='$mid'");
		$this->db->query("update red_email_subscribers set is_analysed=0 where subscriber_created_by='$mid' and subscriber_status=1 and is_deleted=0");
		echo '<div style="padding:20px;">All contacts will be analysed soon and you will get an email alert.</div>';
	}
	function view_contact_analysis($mid=0){
		echo $this->UserModel->contact_analysis_html($mid);
	}
	
	
	function suppress_analysed_contacts($typ){
		$arrTtype 	= explode('_',$typ);
		$mid 		= $arrTtype[0];
		$strDomain 	= $arrTtype[2];
		$strType 	= $arrTtype[3];
		$strDomainClause = '';
		if($strDomain == 'yahoo' or $strDomain == 'gmail' or $strDomain == 'hotmail' or $strDomain == 'msn' or $strDomain == 'aol'){
			$strDomainClause = "and s.subscriber_email_domain like'{$strDomain}%'";
		}elseif($strDomain == 'other'){
				$strDomainClause = "and s.subscriber_email_domain not like'yahoo%' and s.subscriber_email_domain not like'gmail%' and s.subscriber_email_domain not like'hotmail%' and s.subscriber_email_domain not like'msn%' and s.subscriber_email_domain not like'aol%' ";
		}
		if($strType == 'bounce'){		
			$new_filter_query = "UPDATE red_email_subscribers s inner join red_global_dnm dnm on s.subscriber_email_address=dnm.email_address set s.subscriber_status=5,s.status_change_date=NOW() where dnm.dnm_type=1 and s.subscriber_created_by='$mid' and s.subscriber_status=1 and s.is_deleted=0 {$strDomainClause}";
		}elseif($strType == 'complaint'){	
			$new_filter_query = "UPDATE red_email_subscribers s inner join red_global_fbl fbl on s.subscriber_email_address=fbl.email_address set s.subscriber_status=5,s.status_change_date=NOW() where  s.subscriber_created_by='$mid' and s.subscriber_status=1 and s.is_deleted=0  {$strDomainClause}";
		}elseif($strType == 'unsubscribe'){		
			$new_filter_query = "UPDATE red_email_subscribers s inner join red_email_subscribers t2 on s.subscriber_email_address=t2.subscriber_email_address  set s.subscriber_status=5, s.status_change_date=NOW() where s.subscriber_created_by='$mid' and s.subscriber_status=1 and s.is_deleted=0 and t2.subscriber_status=0  {$strDomainClause}";
		}
		$this->db->query($new_filter_query);
		if($strDomain == 'yahoo' or $strDomain == 'gmail' or $strDomain == 'hotmail' or $strDomain == 'msn' or $strDomain == 'aol'){
			$fldDomain = $strDomain.'_'.$strType;
			$fldAll = 'all_'.$strType;			
			$this->db->query("update red_subscriber_analysis set $fldAll=($fldAll - $fldDomain),$fldDomain = 0 where member_id='$mid'");
		}elseif($strDomain == 'other'){			
			$this->db->query("update red_subscriber_analysis set all_{$strType} = (yahoo_{$strType} + gmail_{$strType} + hotmail_{$strType} + msn_{$strType} + aol_{$strType} ) where member_id='$mid'");			
		}elseif($strDomain == 'all'){			
			$this->db->query("update red_subscriber_analysis set yahoo_{$strType}=0,gmail_{$strType}=0,hotmail_{$strType}=0,msn_{$strType}=0,aol_{$strType}=0,all_{$strType}=0 where member_id='$mid'");
		}
		echo 'suppressed';
		exit;
	}
	function delete_analysed_contacts($typ){
		$arrTtype 	= explode('_',$typ);
		$mid 		= $arrTtype[0];
		$strDomain 	= $arrTtype[1];
		$strType 	= $arrTtype[2];
		$strDomainClause = '';
		if($strDomain == 'yahoo' or $strDomain == 'gmail' or $strDomain == 'hotmail' or $strDomain == 'msn' or $strDomain == 'aol'){
			$strDomainClause = "and s.subscriber_email_domain like'{$strDomain}%'";
		}elseif($strDomain == 'other'){
				$strDomainClause = "and s.subscriber_email_domain not like'yahoo%' and s.subscriber_email_domain not like'gmail%' and s.subscriber_email_domain not like'hotmail%' and s.subscriber_email_domain not like'msn%' and s.subscriber_email_domain not like'aol%' ";
		}
		if($strType == 'bounce'){		
			$new_filter_query = "UPDATE red_email_subscribers s inner join red_global_dnm dnm on s.subscriber_email_address=dnm.email_address set s.is_deleted=1,s.status_change_date=NOW() where dnm.dnm_type=1 and s.subscriber_created_by='$mid' and s.subscriber_status=1 and s.is_deleted=0 {$strDomainClause}";
		}elseif($strType == 'complaint'){	
			$new_filter_query = "UPDATE red_email_subscribers s inner join red_global_fbl fbl on s.subscriber_email_address=fbl.email_address set s.is_deleted=1,s.status_change_date=NOW() where  s.subscriber_created_by='$mid' and s.subscriber_status=1 and s.is_deleted=0  {$strDomainClause}";
		}elseif($strType == 'unsubscribe'){		
			$new_filter_query = "UPDATE red_email_subscribers s inner join red_email_subscribers t2 on s.subscriber_email_address=t2.subscriber_email_address  set s.is_deleted=1, s.status_change_date=NOW() where s.subscriber_created_by='$mid' and s.subscriber_status=1 and s.is_deleted=0 and t2.subscriber_status=0  {$strDomainClause}";
		}
		$this->db->query($new_filter_query);
		if($strDomain == 'yahoo' or $strDomain == 'gmail' or $strDomain == 'hotmail' or $strDomain == 'msn' or $strDomain == 'aol'){
			$fldDomain = $strDomain.'_'.$strType;
			$fldAll = 'all_'.$strType;			
			$this->db->query("update red_subscriber_analysis set $fldAll=($fldAll - $fldDomain),$fldDomain = 0 where member_id='$mid'");
		}elseif($strDomain == 'other'){			
			$this->db->query("update red_subscriber_analysis set all_{$strType} = (yahoo_{$strType} + gmail_{$strType} + hotmail_{$strType} + msn_{$strType} + aol_{$strType} ) where member_id='$mid'");			
		}elseif($strDomain == 'all'){			
			$this->db->query("update red_subscriber_analysis set yahoo_{$strType}=0,gmail_{$strType}=0,hotmail_{$strType}=0,msn_{$strType}=0,aol_{$strType}=0,all_{$strType}=0 where member_id='$mid'");
		}
		echo 'deleted';
		exit;
	}
	function export_analysed_contacts($typ, $fordv=0){
		$arrTtype 	= explode('_',$typ);
		$mid 		= $arrTtype[0];
		$strDomain 	= $arrTtype[1];
		$strType 	= $arrTtype[2];
		$strDomainClause = '';
		if($strDomain == 'yahoo' or $strDomain == 'gmail' or $strDomain == 'hotmail' or $strDomain == 'msn' or $strDomain == 'aol'){
			$strDomainClause = "and s.subscriber_email_domain like'{$strDomain}%'";
		}elseif($strDomain == 'other'){
				$strDomainClause = "and s.subscriber_email_domain not like'yahoo%' and s.subscriber_email_domain not like'gmail%' and s.subscriber_email_domain not like'hotmail%' and s.subscriber_email_domain not like'msn%' and s.subscriber_email_domain not like'aol%' ";
		}
		if($strType == 'bounce'){		
			$new_filter_query = "Select distinct s.subscriber_id,s.subscriber_email_address from red_email_subscribers s inner join red_global_dnm dnm on s.subscriber_email_address=dnm.email_address where dnm.dnm_type=1 and s.subscriber_created_by='$mid' and s.subscriber_status=1 and s.is_deleted=0 {$strDomainClause}";
		}elseif($strType == 'complaint'){	
			$new_filter_query = "Select distinct s.subscriber_id,s.subscriber_email_address from red_email_subscribers s inner join red_global_fbl fbl on s.subscriber_email_address=fbl.email_address where  s.subscriber_created_by='$mid' and s.subscriber_status=1 and s.is_deleted=0  {$strDomainClause}";
		}elseif($strType == 'unsubscribe'){		
			$new_filter_query = "Select distinct s.subscriber_id,s.subscriber_email_address from red_email_subscribers s inner join red_email_subscribers t2 on s.subscriber_email_address=t2.subscriber_email_address where s.subscriber_created_by='$mid' and s.subscriber_status=1 and s.is_deleted=0 and t2.subscriber_status=0  {$strDomainClause}";
		}
		$rsContacts = $this->db->query($new_filter_query);
		if($rsContacts->num_rows() > 0){
			foreach($rsContacts->result_array() as $subscriber){		 
				$subscriber_id 	= $subscriber['subscriber_id'];
				$csv_output.=$subscriber['subscriber_email_address']."\n";
				if($fordv)$this->db->query("update red_email_subscribers set `ignore`=1 where subscriber_id='$subscriber_id'");	
			}
		}
		 
		
	 	$csv_output="Email Address"."\n".$csv_output;
		//Create filename and send output headers
		 
		print $csv_output;
		exit;
	
	}
	// Export contacts either responisves or unresponsives
	function export_contacts_responsive($mid, $resp='unique', $fordv=0){		
		$rsContacts = $this->db->query("select distinct subscriber_id, subscriber_email_address from red_email_subscribers where subscriber_created_by='$mid' and subscriber_status=1 and is_deleted=0");	
		 	 
		$csv_output = "Email Address"."<br/>";
		if($rsContacts->num_rows() > 0){
			foreach($rsContacts->result_array() as $subscriber){
				$eml 			= $subscriber['subscriber_email_address'];
				$subscriber_id 	= $subscriber['subscriber_id'];
				$rsContactStatus = $this->db->query("select `read` from red_email_subscribers where subscriber_email_address='$eml' and subscriber_created_by !='$mid' order by `read` desc");
				if($rsContactStatus->num_rows() > 0) {
					if(trim($resp) ==  'repeated'){ // Un-responsives
						$csv_output.= $eml."<br/>";	
						if($fordv)$this->db->query("update red_email_subscribers set `ignore`=1 where subscriber_id='$subscriber_id'");	
					}				

					if(trim($resp) ==  'responsive'){ // Responsives
						if($rsContactStatus->row()->read > 0 ) $csv_output.= $eml."<br/>";
						if($fordv)$this->db->query("update red_email_subscribers set `ignore`=1 where subscriber_id='$subscriber_id'");	
					}elseif(trim($resp) ==  'unresponsive'){ // Un-responsives
						if($rsContactStatus->row()->read < 1 ) $csv_output.= $eml."<br/>";	
						if($fordv)$this->db->query("update red_email_subscribers set `ignore`=1 where subscriber_id='$subscriber_id'");							
					}

					
				}else{
					if(trim($resp) ==  'unique')$csv_output.= $eml."<br/>";	
					if($fordv)$this->db->query("update red_email_subscribers set `ignore`=1 where subscriber_id='$subscriber_id'");	
				}
				 $rsContactStatus->free_result();				 
			}
		}
		 $rsContacts->free_result();
		
	 	 
		//Create filename and send output headers
		 
		echo $csv_output;
		exit;
	
	}
	function download(){
		$filename = "exported_".date("Y-m-d_H-i",time());
		header("Pragma: public");
		header("Expires: 0"); 
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: text/x-csv");
		header("Content-Disposition: attachment;filename=".$filename .".csv"); 

		if($_POST['data']){
			print $_POST['data'];
		}

	exit;
	}

	/**
		Function check_user_for_admin for login user from admin
	**/
	function check_user_for_admin($member_id=0){
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');
		$qry="select * FROM red_members WHERE (member_id='".$member_id."') AND `is_deleted`=0 ";
		$user_qry=$this->db->query($qry);	#execute query				
		$user_data_array=$user_qry->result_array();	#Fetch resut				
		// To check user have credentails matching in database
		if(count($user_data_array)){
			$ip_address=$this->is_authorized->getRealIpAddr();
			$this->UserModel->update_user(array('ip_address'=>$ip_address),array('member_id'=>$user_data_array[0]['member_id']));
			//Assign  session to user
			$this->session->set_userdata('member_id', $user_data_array[0]['member_id']);
			$this->session->set_userdata('member_username', $user_data_array[0]['member_username']);
			$this->session->set_userdata('member_email_address', $user_data_array[0]['email_address']);		
			$this->session->set_userdata('member_autoresponder_status', $user_data_array[0]['autoresponder_status']);
			$this->session->set_userdata('member_time_zone', $user_data_array[0]['member_time_zone']);
			// permission based sessions
			$this->session->set_userdata('manage_campaigns', 1);						
			$this->session->set_userdata('manage_contacts', 1 );						
			$this->session->set_userdata('manage_stats', 1 );						
			$this->session->set_userdata('manage_autoresponders', 1);						
			$this->session->set_userdata('manage_signupforms', 1);
			$this->session->set_userdata('manage_extra', 1);
				
			$current_date=date("Y-m-d H:i:s");
			#Calcultate number of days between current datetime and contact added datetime
			$date_diff = floor((strtotime($current_date) - strtotime($user_data_array[0]['created_on'])) / (60 * 60 * 24));
			//if(($date_diff<2)&&($user_data_array[0]['status']=='inactive')){
			if($user_data_array[0]['status']=='inactive'){
				$this->session->set_userdata('member_status','inactive');
			}else{
				$this->session->set_userdata('member_status','active');
			}
			 		
			$user_packages=array();
			$user_packages_array=$this->UserModel->get_user_packages(array('member_id'=>$user_data_array[0]['member_id'],'is_deleted'=>0));
			if(count($user_packages_array)<=0){
				// submit free pckage for register user
				$member_package_id=$this->UserModel->insert_member_package(array('member_id'=>$user_data_array[0]['member_id'],'package_id'=>-1,
				'credit_card_last_digit' =>NULL,
					'expiration_date' =>NULL,
					'card_holder_name' =>NULL,
					'first_name' =>'',
					'last_name' =>'',
					'address' =>'',
					'city' =>'',
					'state' =>'',
					'zip' =>'',
					'country' =>'',
					'subscription_id'=>''));
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
			
			//  create array for insert values in activty table
			$values=array('user_id'=>$user_data_array[0]['member_id'], 'activity'=>'login:'.$this->is_authorized->getRealIpAddr() );
			$this->Activity_Model->create_activity($values);
			redirect('newsletter/campaign');
		}
	}
	/**
		Function invoice_list to list invoices of user according to member_id
	**/
	function invoice_list($member_id=0){
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');
		 
		// Fetch user packages from database
		$user_transactions=array();
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/users_manage/invoice_list/'.$member_id;
		$config['total_rows']=$this->UserModel->get_transaction_count(array('user_id'=>$member_id),'like');
		$config['per_page']=20;
		$config['uri_segment']=5;

		// Initialize paging with above parameters
		$this->pagination->initialize($config);
		
		//Create paging inks
		$paging_links=$this->pagination->create_links();	
		
		$user_transactions=$this->UserModel->get_user_transactions(array('user_id'=>$member_id),$config['per_page'],$start,'like');
		#fetch user info
		$users_array=$this->UserModel->get_user_data(array('is_deleted'=>0,'member_id'=>$member_id));
		$logo_link="webmaster/dashboard_stat";
		$this->load->view('webmaster/header',array('title'=>'Invoice List','logo_link'=>$logo_link));
		$this->load->view('webmaster/invoice_list',array('user_transactions'=>$user_transactions,'paging_links'=>$paging_links,'member_id'=>$member_id,'user'=>$users_array[0]));
		$this->load->view('webmaster/footer');
	}
	/**
		Function billing_detail to display detail of billing
	**/
	function billing_detail($id=0){
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');
		 
		// Fetch user packages from database
		$user_packages=array();
		$user_transactions=$this->UserModel->get_user_transactions(array('transaction_id'=>$id),0);
		if ($user_transactions[0]['package_recurring_interval'] == 'credit') {
			$userCredit['credit_id'] = $user_transactions[0]['payment_table_id'];
			$getCreditDetail = $this->UserModel->getCreditPackage($userCredit);
			$user_transactions[0]['credit_count'] = $getCreditDetail[0]['credit_count'];
		}
		if($user_transactions[0]['user_id'] == 4821)$user_transactions[0]['company']='Inspired Marketer JMM LTD';
		$this->load->view('user/billing_detail',array('user_transactions'=>$user_transactions[0]));
	}
	/**
		Function update_authentic to change the authentication for schedule email 
	**/
	function update_authentic($member_id=0,$is_authentic=0){
		$thisDate = date("Y-m-d H:i:s");
		if($is_authentic > 0)
		$this->UserModel->update_user(array('is_authentic'=>$is_authentic,'authenticated_on' => "$thisDate",'unauthentic_contacts'=>0),array('member_id'=>$member_id));	
		else
		$this->UserModel->update_user(array('is_authentic'=>$is_authentic),array('member_id'=>$member_id));	
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
				}
			}
		}
	}
	/**
		Function update_user to change the user info 
	**/
	function update_user($member_id=0,$status=0){
		$thisDate = date("Y-m-d H:i:s");
		if($this->input->post('mode',true)=="authentic"){
			if($status > 0)
			$this->UserModel->update_user(array('is_authentic'=>$status,'authenticated_on' => "$thisDate",'unauthentic_contacts'=>0),array('member_id'=>$member_id));	
			else
			$this->UserModel->update_user(array('is_authentic'=>$status),array('member_id'=>$member_id));	
		}elseif($this->input->post('mode',true)=="disclaimer"){			
			$this->UserModel->update_user(array('is_disclaimer'=>$status),array('member_id'=>$member_id));				
			echo 'updated';		
		}elseif($this->input->post('mode',true)=="apply_unresponsive_filter"){			
			$this->UserModel->update_user(array('apply_unresponsive_filter'=>$status),array('member_id'=>$member_id));				
		}elseif($this->input->post('mode',true)=="unresponsive_release_count"){			
			$this->UserModel->update_user(array('unresponsive_release_count'=>$status),array('member_id'=>$member_id));	
			echo 'updated';		
		}elseif($this->input->post('mode',true)=="apply_automatic_segmentation"){			
			$this->UserModel->update_user(array('is_automatic_segmentation'=>$status),array('member_id'=>$member_id));	
			echo 'updated';		
		}elseif($this->input->post('mode',true)=="segment_size"){			
			$this->UserModel->update_user(array('segment_size'=>$status),array('member_id'=>$member_id));	
			echo 'updated';		
		}elseif($this->input->post('mode',true)=="user_note"){	
			$user_note_val = $this->input->post('user_note_val',true);
			$this->UserModel->update_user(array('campaign_approval_notes'=>$user_note_val),array('member_id'=>$member_id));	
			echo 'updated';		
		}elseif($this->input->post('mode',true)=="always_slow_release"){	
			$this->UserModel->update_user(array('always_slow_release'=>$status),array('member_id'=>$member_id));	
			echo 'updated';		
		}
	}
	function update_risky($member_id=0,$risky=0){
		$this->UserModel->update_user(array('is_risky'=>$risky),array('member_id'=>$member_id));
		echo 'Updated';
	}
	
	function attach_seedlist($member_id=0,$seedlist=0){
		$this->UserModel->update_user(array('attach_seedlist'=>$seedlist),array('member_id'=>$member_id));
		echo 'Updated';
	}
	
	function update_vmta($member_id=0,$vmta='redrotate'){
		$this->UserModel->update_user(array('vmta'=>$vmta),array('member_id'=>$member_id));
		echo 'Updated';
	}
	/**
		Function cancel_subscription to cancel the user subscription
		@member_id: member id
	*/
	function cancel_subscription($member_id=0){
		
		// 	Fetch number of days for cancel  subscription 
		
		$site_configuration_array=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'cancel_subscription_after_xx_days'));
		$cancel_subscription_date=$site_configuration_array[0]['config_value'];
		#set free package
		$update_array=array('amount'=>0,'selected_package_id'=>'-1','is_admin'=>0);
		# update package id in memeber package table
		$this->UserModel->update_member_package($update_array,array('member_id'=>$member_id));
		###########################
		#send notification to user#
		###########################
		#Prepare array for where condition in an campign model
		$fetch_conditions_array=array('m.member_id'=>$member_id);
		#Fetch user detail from database
		$user_info=$this->UserModel->get_user_account_info($fetch_conditions_array);
		
        $userPackageData = $this->UserModel->get_user_package(array('rmp.member_id' => $member_id));

        if ($userPackageData[0]['payment_type'] == 1) {
            $profileId = $userPackageData[0]['paypal_transaction_id'];
            $this->paypal_subscription_cancel($profileId, 'Cancel');
        }
        exit;

		
		
		
		#send notfication to each user who have not login for xx days
		foreach($user_info as $user){
			if(trim($user['first_name'])!=""){
				$user_name=$user['first_name'];
			}else{
				$user_name=$user['member_username'];
			}
			$user_arr=array($user_name,$user['email_address'],$cancel_subscription_date);
			$this->load->helper('transactional_notification');
			#Update notification date in database
			$this->UserModel->update_user(array('vmta'=>'rcmailer6','cancel_subscription_date'=>date("Y-m-d H:i:s")),array('member_id'=>$member_id));
			@create_transactional_notification("confirmation_of_account_termination",$user_arr);
		}
		redirect('webmaster/users_manage/index');
	}

    /* Start code -> CB
      Paypal recurring process cancel
     */

    function paypal_subscription_cancel($profile_id, $apaypal_subscription_cancelction) {




        $api_request = 'USER=' . urlencode($this->PAYPAL_USERNAME)
                . '&PWD=' . urlencode($this->PAYPAL_PASSWORD)
                . '&SIGNATURE=' . urlencode($this->PAYPAL_SIGNATURE)
                . '&VERSION=76.0'
                . '&METHOD=ManageRecurringPaymentsProfileStatus'
                . '&PROFILEID=' . urlencode($profile_id)
                . '&ACTION=' . urlencode($apaypal_subscription_cancelction)
                . '&NOTE=' . urlencode('Profile cancelled at store');



        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->PAYPAL_URL); // For live transactions, change to 'https://api-3t.paypal.com/nvp'
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        // Set the API parameters for this transaction
        curl_setopt($ch, CURLOPT_POSTFIELDS, $api_request);

        // Request response from PayPal
        $response = curl_exec($ch);

        echo '<pre>';
        print_R($response);
        exit;

        // If no response was received from PayPal there is no point parsing the response
        if (!$response) {
            //send email to admin.
            die('Calling PayPal to change_subscription_status failed: ' . curl_error($ch) . '(' . curl_errno($ch) . ')');
        } else {
            
        }
        curl_close($ch);
        // An associative array is more usable than a parameter string
        parse_str($response, $parsed_response);

        return $parsed_response;
    }

    /* End code -> CB */
	/**
		Function upgrade_users is to list upgrade users
	*/
	function upgrade_users(){
		//  needing to upgrade    				
		$user_upgrade_count=0;
		$fetch_conditions_array=array('rm.is_deleted'=>0,'rm.status'=>'active', 'rmp.package_id >'=>0, 'DATEDIFF(rmp.next_payement_date,CURDATE()) >'=>'0');
		$user_count=$this->UserModel->get_user_count($fetch_conditions_array, true);
		//echo $this->db->last_query();exit;
		$user_data_array=$this->UserModel->get_user_data($fetch_conditions_array,$user_count,0, true);
		$i=0;
		foreach($user_data_array as $user){			
			$package_detail		= $this->UserModel->get_user_package(array('member_id'=>$user['member_id']));					
			$subscriber_count	= $this->Subscriber_Model->get_subscriber_count(array('res.subscriber_created_by'=>$user['member_id'],	'res.is_deleted'=>0,'res.subscriber_status'=>1));
		
			if($subscriber_count > $package_detail[0]['package_max_contacts']){
				
				$user_data_array[$i]['status_description']	= "Active-Paid";			
				$user_data_array[$i]['package']	= $package_detail[0]['package_min_contacts']."-".$package_detail[0]['package_max_contacts'];
				$user_data_array[$i]['contacts'] = $subscriber_count;
				$user_data_array[$i]['fresh_contacts']=$this->Subscriber_Model->get_subscriber_count(array('res.subscriber_created_by'=>$user['member_id'], 'res.is_deleted'=>0, 'res.subscriber_status'=>1, 'res.sent'=>0));			
				
				$user_data_array[$i]['total_used_contacts']=$this->Subscriber_Model->get_total_used_subscriber_count(array('res.subscriber_created_by'=>$user['member_id']));		
								
						
			}else{
				unset($user_data_array[$i]);
			}
			$i++;			
		}
		 
		 
		$logo_link="webmaster/dashboard_stat";		
		$this->load->view('webmaster/header',array('title'=>'Manage Users','logo_link'=>$logo_link));
		$this->load->view('webmaster/users_list',array('users'=>$user_data_array,'paging_links'=>$paging_links,'messages' =>$messages,'packages'=>$packages,'search'=>$search_array,'mode'=>'upgrade_users'));
		$this->load->view('webmaster/footer');
	}
	/**
	*	Function paid_users is to list paid users
	*/
	function paid_users($start=0){
		// needing to upgrade    			
		//$fetch_conditions_array=array('rm.is_deleted'=>0,'rmp.package_id >'=>'0','rm.status'=>'active','is_admin'=>0,'DATEDIFF(next_payement_date,CURDATE()) <='=>'30','DATEDIFF(next_payement_date,CURDATE()) >='=>'0');
		$fetch_conditions_array=array('rm.parent_id'=>0,'rm.is_deleted'=>0,'rmp.package_id >'=>'0','rm.status'=>'active','is_admin'=>0,'DATEDIFF(next_payement_date,CURDATE()) >='=>'0');
		
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/users_manage/paid_users';
		$config['total_rows']=$this->UserModel->get_user_count($fetch_conditions_array,true);
		
		$config['per_page']=30;
		$config['uri_segment']=4;
		// Initialize paging with above parameters
		$this->pagination->initialize($config);
		//Create paging links
		$paging_links=$this->pagination->create_links();
		$user_data_array=$this->UserModel->get_user_data($fetch_conditions_array,$config['per_page'],$start,true);	
		
		$i=0;
		foreach($user_data_array as $user){
			if($user['package_id']==0){
				$user['package_id']=-1;
				# submit free package for user
				$member_package_id=$this->UserModel->insert_member_package(array('member_id'=>$user['member_id'],'package_id'=>-1,
				'credit_card_last_digit' =>NULL,
					'expiration_date' =>NULL,
					'card_holder_name' =>NULL,
					'first_name' =>'',
					'last_name' =>'',
					'address' =>'',
					'city' =>'',
					'state' =>'',
					'zip' =>'',
					'country' =>'',
					'subscription_id'=>''));
				$this->UserModel->update_user(array('package_id'=>$member_package_id),array('member_id'=>$user['member_id']));
			}
			$package_detail=$this->UserModel->get_user_package(array('member_id'=>$user['member_id']));
			$user_data_array[$i]['package']=$package_detail[0]['package_min_contacts']."-".$package_detail[0]['package_max_contacts'];
			# Collect condition array for fetch subscribers from database		
			$fetch_condiotions_array=array(	'res.subscriber_created_by'=>$user['member_id'],'res.is_deleted'=>0,'res.subscriber_status'=>1);
			$user_data_array[$i]['contacts']=$this->Subscriber_Model->get_subscriber_count($fetch_condiotions_array);
			if(($user['status']=="inactive")&&($user['status_inactive_description']=="unconfirmed")){
				$user_data_array[$i]['status_description']="Inactive- Unconfirmed";
			}else if(($user['status']=="inactive")&&($user['status_inactive_description']=="policy related")){
				$user_data_array[$i]['status_description']="Inactive-Policy related";
			}else if(($package_detail[0]['package_id']>0)&&($package_detail[0]['next_payement_date']<date("Y-m-d H:i:s"))&&($package_detail[0]['is_admin']<=0)&&($user['status']=="active")){
				$user_data_array[$i]['status_description']="Inactive-Failed cc";
			}else if($package_detail[0]['package_id']>0){
				$user_data_array[$i]['status_description']="Active-Paid";
			}else if($package_detail[0]['package_id']<=0){
				$user_data_array[$i]['status_description']="Active-Free";
			}
			$i++;
		}
		# Fetch Packages from database
		$packages_count=$this->UserModel->get_packages_count(array('package_deleted'=>0,'package_status'=>1));
		$packages=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),$packages_count);
		# Recieve any messages to be shown, when campaign is added or updated
		$messages=$this->messages->get();
		if($_POST['mode']=='search' AND !isset($_POST['btn_cancel'])){
			$search_array['field_name']=$_POST['field_name'];
			$search_array['field_value']=$_POST['field_value'];
			$search_array['select_package']=$_POST['select_package'];
			$search_array['select_status']=$_POST['select_status'];			
		}
		$logo_link="webmaster/dashboard_stat";
		#Loads header, users listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Users','logo_link'=>$logo_link));
		$this->load->view('webmaster/users_list',array('users'=>$user_data_array,'paging_links'=>$paging_links,'messages' =>$messages,'packages'=>$packages,'search'=>$search_array,'mode'=>'paid_users','totusercount'=>$config['total_rows']));
		$this->load->view('webmaster/footer');
	}
	
	
	/**
	*	Function to create sub-users for staff-members
	*/
	function subuser_create($mid=0){
		$arrMembers = array();
		$arrMembers[0] = 'Root Member';
		$rsMembers = $this->db->query("Select member_id,member_username from red_members where parent_id=0 and is_deleted=0 and status='active' order by member_username");
		foreach($rsMembers->result_array() as $recMember){
			$intMid = $recMember['member_id'];
			$strMember = $recMember['member_username'];
			$arrMembers[$intMid] = $strMember;
		}
		
		$user_data_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit'){
			// Validation rules are applied
			$this->form_validation->set_rules('username', 'Username', 'required');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
			//$this->form_validation->set_rules('member_password', 'Password', 'required|min_length[6]|max_length[250]|trim');
			
			
			if($this->form_validation->run()) {
				$member_data = array(
					'parent_id' => $this->input->post('parent_id',true),
					'member_username' => $this->input->post('username',true),
					'email_address' => $this->input->post('email',true),
					'member_password' => $this->is_authorized->hashPassword($this->input->post('member_password',true)),
					'ip_address'		=>	null, 'phone_number' => null, 'first_name' => null, 'last_name' => null, 'address_line_1' => null, 'address_line_2' => null,
					'city' => null, 'state' => null, 'country' => '245', 'zipcode' => null, 'last_login_time'=>date("Y-m-d H:i:s"), 'created_on'=>date("Y-m-d H:i:s"),
					'login_expiration_notification_date'=>NULL, 'status'=>'active', 'company' => null, 'autoresponder_status' => 0,
					'sign_up_form_status' => 0, 'contact_import' => 0,  'package_id' => 0, 'is_authentic' => 0, 'authenticated_on' =>NULL,
					'unauthentic_contacts' => 0, 'reset_password_token' => '', 'login_expiration_notification_date' =>NULL, 'cancel_subscription_date' =>NULL
				);
				if($this->input->post('parent_id') > 0){
					$member_data['manage_campaigns']= $this->input->post('manage_campaigns');
					$member_data['manage_contacts']= $this->input->post('manage_contacts');
					$member_data['manage_stats']= $this->input->post('manage_stats');
					$member_data['manage_autoresponders']= $this->input->post('manage_autoresponders');
					$member_data['manage_signupforms']= $this->input->post('manage_signupforms');
					$member_data['manage_extra']= $this->input->post('manage_extra');
				}	
				
				
					
				if($this->input->post('member_id') != '' ){
					if(trim($this->input->post('member_password')) == '')unset($member_data['member_password']);
					$this->UserModel->update_user($member_data,array('member_id'=>$this->input->post('member_id')));	
					$this->messages->add('User updated successfully', 'success');
				}else{
					//check username/email-id exists by loading user from database
					$username_exists=$this->UserModel->get_user_data(array('member_username'=>$this->input->post('username',true),'is_deleted'=>0));			
					$email_exists=$this->UserModel->get_user_data(array('email_address'=>$this->input->post('email',true),'is_deleted'=>0));
					if(count($username_exists)){ //check username exists
						$this->messages->add('Username already exists', 'error');					
					}elseif(count($email_exists)){ //check email exists
						$this->messages->add('Email Address already exists', 'error');					
					}else{
						$inserted_user_id=$this->UserModel->create_user($member_data);	
						
						if($this->input->post('parent_id') == 0){ // create default subscription
							$input_array=array('subscription_title'=>'All My Contacts','subscription_id'=>'-'.$inserted_user_id,'subscription_is_name'=>'1','subscription_created_by'=>$inserted_user_id);
							$subscription_id=$this->subscription_Model->create_subscription($input_array);
						}
						
						$this->register_member_to_redcappi_account($inserted_user_id);
						$this->messages->add('User created successfully', 'success');
						// $this->user_confirmation_notification($inserted_user_id);
					}	
				}
				redirect('webmaster/users_manage/sub_users');
				
			}
			
			$user_data_array=$member_data;
		}elseif($mid > 0){
			$user_data_array=$this->UserModel->get_user_data(array('rm.member_id'=>$mid),1,0,false);				
		}	
		
		// Recieve any messages to be shown
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, users edit  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Create User','logo_link'=>$logo_link));
		$this->load->view('webmaster/subuser_create',array('user'=>$user_data_array,'members_list'=>$arrMembers,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	/**
	*	Function to show sub-users or staff-members
	*/
	function sub_users($start=0){
		
		$fetch_conditions_array=array('rm.parent_id >'=>0,'rm.is_deleted'=>0);
		
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/users_manage/sub_users';
		$config['total_rows']=$this->UserModel->get_user_count($fetch_conditions_array,false);
		// echo $this->db->last_query();
		$config['per_page']=30;
		$config['uri_segment']=4;
		// Initialize paging with above parameters
		$this->pagination->initialize($config);
		//Create paging links
		$paging_links=$this->pagination->create_links();
		$user_data_array=$this->UserModel->get_user_data($fetch_conditions_array,$config['per_page'],$start,false);	
		
   
		$logo_link="webmaster/dashboard_stat";
		#Loads header, users listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Users','logo_link'=>$logo_link));
		$this->load->view('webmaster/subusers_list',array('users'=>$user_data_array,'paging_links'=>$paging_links,'messages' =>$messages,'mode'=>'sub_users','totusercount'=>$config['total_rows']));
		$this->load->view('webmaster/footer');
	}
	function message_list($mid=0){
		$rsMsgForMembers = $this->db->query("Select t1.message_id, t2.message_name from red_member_message t1 inner join red_messages t2 ON t1.message_id=t2.message_id where t1.member_id='$mid' and t1.is_deleted=0");
		if($rsMsgForMembers->num_rows() > 0){
		foreach($rsMsgForMembers->result_array() as $recMessage){
			$thisMsgId = $recMessage['message_id'];
			$strMsg .= "<tr><td>".$recMessage['message_name']." </td><td><a href='javascript:void(0);' onclick='javascript:remMsg($mid,$thisMsgId);'>Delete</a></td></tr>";
		}
		}else{
			$strMsg .= "<tr><td colspan='2'>There is no attached message.</td></tr>";
		}
		$rsMsgForMembers->free_result();
		echo "<div style='margin:40px;'><table cellspacing='4' cellpadding='2' class='tbl_listing' width='100%'><tr><th colspan='2'>Messages Attached</th></tr>".$strMsg . "</table></div>";
	}
	function del_msg($memId, $msgId){
		$this->db->query("update red_member_message set is_deleted=1 where member_id='$memId' and message_id='$msgId'");
		$this->message_list($memId);
	}
	
	function user_supress($id){
		$inactiveusers= $this->Subscriber_Model->get_inactivesubscriber_data($id);
		if(count($inactiveusers) > 0){		
            $dv_folder_path = $this->config->item('SUPRESS') . "/" . $id;
            @mkdir($dv_folder_path, 0777);
            $filename = $dv_folder_path . "/" . $id . ".csv";
            $fp = fopen($filename, 'w');
            $val = array();
            foreach ($inactiveusers as $r_key => $r_value) {   
               $val = array($r_value->subscriber_id, $r_value->subscriber_email_address);
                fputcsv($fp, $val);
                }
                fclose($fp); 
				}
			redirect('webmaster/users_manage/users_list');
			
	}
}
?>