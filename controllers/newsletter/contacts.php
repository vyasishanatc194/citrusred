<?php
/**
  *	Controller class for subscriptions
  *	It have controller functions for subscription management.
 */
class Contacts extends CI_Controller
{
	/**
	  *	Contructor for controller.
	  *	It checks user session and redirects user if not logged in
	 */
	private $confg_arr = array(); 
	function __construct() {
        parent::__construct();
		# check via common model
		if(!$this->is_authorized->check_user())
			redirect('user/index');

		# Create user's folders
		$this->is_authorized->createUserFiles();

		$this->load->model('newsletter/subscription_Model');
		$this->load->model('newsletter/Subscriber_Model');
		$this->load->model('newsletter/contact_model');
		$this->load->model('newsletter/Signup_Model');
		$this->load->model('UserModel');
		$this->load->model('newsletter/Emailreport_Model');

		$this->output->enable_profiler(false);
		// Force SSL
		force_ssl();
		$this->load->model('ConfigurationModel');
		$this->confg_arr=$this->ConfigurationModel->get_site_configuration_data_as_array();
		if($this->confg_arr['maintenance_mode'] !='no'){
			redirect ("/site_under_maintenance/");
			exit;
		}
    }


	/**
	 * Function index
	 *
	 * 'Index' controller function for listing of subscriptions.
	 *
	 * @param (int) (subscription_id)  for displaying subscription selectable(blue color)  in view of subscription list
	 */

	function index($subscription_id=0,$scroll=0,$action=""){		
		
		$subscription_id = (is_numeric($subscription_id))?$subscription_id:0;
		
		// Recieve any messages to be shown, when subscription is added or updated
		$messages=$this->messages->get();
		if($this->session->userdata('member_id') == 157){		
			$this->populateRCMembers();
		}

		// Get Maximum Contacts according to session package id		
		$user_packages_array=$this->UserModel->get_user_packages(array('member_id'=>$this->session->userdata('member_id'),'is_deleted'=>0));
		$package_array=$this->UserModel->get_packages_data(array('package_id'=>$user_packages_array[0]['package_id']));				 
		$package_price=$package_array[0]['package_price'];
		$subscription_data['package_max_contacts']=$package_array[0]['package_max_contacts'];
		

		// Get Total Subscribers created by login user
		$fetch_condiotions_array=array('subscriber_created_by'=>$this->session->userdata('member_id'),'subscriber_status'=>1,'is_deleted'=>0);
		$subscription_data['subscriber_count']=$this->contact_model->getContactsCount($fetch_condiotions_array);

		$fetch_condiotions_array=array('subscription_created_by'=>$this->session->userdata('member_id'),'is_deleted'=>0	);

		// Fetches subscription data from database
		$subscription_data['subscriptions']=$this->subscription_Model->get_subscription_data($fetch_condiotions_array);
	 
		if(count($subscription_data['subscriptions']) == 0){
			$input_array=array('subscription_title'=>'All My Contacts','subscription_id'=>'-'.$this->session->userdata('member_id'),'subscription_is_name'=>'1','subscription_created_by'=>$this->session->userdata('member_id'));
			$subscription_id=$this->subscription_Model->create_subscription($input_array);	
			$subscription_data['subscriptions']=$this->subscription_Model->get_subscription_data($fetch_condiotions_array);				
		}
		$all_my_contacts=$subscription_data['subscriptions'][0];
		$subscription_order_by_name=$subscription_data['subscriptions'];
		unset($subscription_order_by_name[0]);
		if(count($subscription_order_by_name)>0){
			$subscription_order_by_name=$this->subval_sort($subscription_order_by_name,'subscription_title');
		}
		$subscription_order_by_name[0]=$all_my_contacts;
		// Assign all the subscription for displaying in selectbox list
		$subscription_data['select_subscriptions']=$subscription_order_by_name;

		// Collect subscription id for displaying subscription selectable(blue color) in view of subscription list

		$subscription_id_array=array();
		$autoresponder_subscriptions_array=array();
		foreach($subscription_data['subscriptions'] as $subscription){
			$subscription_id_array[]=$subscription['subscription_id'];
		}
		if($subscription_id){
			$subscription_data['subscription_first_id']=$subscription_id;
		}else{
			$subscription_data['subscription_first_id']=$subscription_id_array[0];
		}
		// Collect Total number of subscriber(contacts) for each subscription

		foreach($subscription_data['subscriptions'] AS $subscription){			
			if($subscription['subscription_id'] > 0){
			$fetch_condiotions_array=array('res.subscriber_created_by'=>$this->session->userdata('member_id'),'res.subscriber_status'=>1,'res.is_deleted'=>0);			
			$subscribers=$this->contact_model->get_contacts_count_in_list($fetch_condiotions_array,$subscription['subscription_id']);
			$subscription_data['total']["'".$subscription['subscription_id']."'"]= $subscribers;
			}else{
			$subscription_data['total']["'".$subscription['subscription_id']."'"]= $subscription_data['subscriber_count'];			
			}
		}

		/**
		 * Fetch user data
		 */
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));
		$subscription_data['extra']=$user_data_array[0];
		//Loads header, subscription and footer view.
		$this->load->view('header',array('title'=>'List subscriptions'));
		$this->load->view('contacts/contacts',$subscription_data);
		$this->load->view('footer');
	}

	/**
	 * Function Dislay_ajax
	 *
	 * 'Dislay_ajax' controller function for listing of subscriptions using ajax.
	 *
	 * @param (int) (subscription_id)  for displaying subscription selectable(blue color)  in view of subscription list
	 */

	function display_ajax($subscription_id=0){
		$subscription_id = (is_numeric($subscription_id))?$subscription_id:0;
		
		// Recieve any messages to be shown, when subscription is added or updated
		$messages=$this->messages->get();

		// Creating array of conditions to checked with database with conditions.

		$fetch_condiotions_array=array('subscription_created_by'=>$this->session->userdata('member_id'), 'is_deleted'=>0);
		$subscription_data['subscriptions']=$this->subscription_Model->get_subscription_data($fetch_condiotions_array);
		
		$all_my_contacts=$subscription_data['subscriptions'][0];
		$subscription_order_by_name=$subscription_data['subscriptions'];
		unset($subscription_order_by_name[0]);
		if(count($subscription_order_by_name)>0){
			$subscription_order_by_name=$this->subval_sort($subscription_order_by_name,'subscription_title');
		}
		$subscription_order_by_name[0]=$all_my_contacts;
		// Assign all the subscription for displaying in selectbox list
		$subscription_data['select_subscriptions']=$subscription_order_by_name;

		/*
			Collect subscription id for displaying subscription selectable(blue color) in view of subscription list
		*/
		$subscription_id_array=array();
		$select_subscriptions="";	//Collect subscription title for displaying in select box
		foreach($subscription_data['subscriptions'] as $subscription){
			$subscription_id_array[]=$subscription['subscription_id'];
			$select_subscriptions.=$subscription['subscription_id']."=".ucfirst(substr($subscription['subscription_title'],0,25))."|";
		}
		//remove last character '|' from select_subscriptions string
		$select_subscriptions=substr_replace($select_subscriptions,"",-1);
		if($subscription_id){
			$subscription_data['subscription_first_id']=$subscription_id;
		}else{
			$subscription_data['subscription_first_id']=$subscription_id_array[0];
		}
		foreach($subscription_data['subscriptions'] AS $subscription){			
			if($subscription['subscription_id'] > 0){
			$fetch_condiotions_array=array('res.subscriber_created_by'=>$this->session->userdata('member_id'),'res.subscriber_status'=>1,'res.is_deleted'=>0);			
			$subscribers=$this->contact_model->get_contacts_count_in_list($fetch_condiotions_array,$subscription['subscription_id']);
			$subscription_data['total']["'".$subscription['subscription_id']."'"]= $subscribers;
			}else{
			$fetch_condiotions_array=array('subscriber_created_by'=>$this->session->userdata('member_id'),'subscriber_status'=>1,'is_deleted'=>0);
			$subscription_data['total']["'".$subscription['subscription_id']."'"]= $this->contact_model->getContactsCount($fetch_condiotions_array);
			}
		}
		 
		/*
			Collect subscription list for displaying in subscription view using ajax
		*/
		if(count($subscription_data['subscriptions'])) {
			$i=1;	// variable i is used for change the class of tr altrnatively

			foreach($subscription_data['subscriptions'] as $subscription){
				// if variable i is odd then class will be "nomargin-right" else empty
				if($subscription['subscription_id']=='-'.$this->session->userdata('member_id')){
					if($subscription['subscription_id']>0){
						$delete_link='<a  href="javascript:void(0);" class="delete_contact" ><img class="campion_send" src="'. $this->config->item('webappassets').'images/new_png_design/Email-Icon-Trash.png?v=6-20-13" alt="campaigns"/></a>';
						$delete_link='<li>'.$delete_link.'</li>';
					}
				}else{
					if($subscription['subscription_id']>0){
						$delete_link='<li><a class="delete-list fancybox btn cancel delete_contact" href="'.base_url().'newsletter/contacts/delete/'.$subscription['subscription_id'].'" name="'.$subscription['subscription_id'].'"><img class="campion_send" src="'. $this->config->item('webappassets').'images/new_png_design/Email-Icon-Trash.png?v=6-20-13" alt="campaigns"/></a></li>';
					}
				}
				if($subscription['subscription_id']>0){
					$edit_link='<li><a id="subscriber_edit" name="'.$subscription['subscription_id'].'"  class="subscriber_edit btn cancel delete_contact" href="javascript:void(0);"><img class="campion_send" src="'.$this->config->item('webappassets').'images/new_png_design/Email-Icon-Edit.png?v=6-20-13" alt="campaigns"/></a></li>';
				}
				$subscription_list.= '<div class="editing-theme-box '. $class.'" id="'.$subscription['subscription_id'].'">
                    <div class="listname-no" onclick="display_contacts('.$subscription['subscription_id'].')" style="cursor:pointer;"><span class="right-no">'.$subscription_data['total']["'".$subscription['subscription_id']."'"].'</span> <strong class="subscription_strong" name="'.$subscription['subscription_id'].'" id="subscription_id_'.$subscription['subscription_id'].'">'.ucfirst(substr ($subscription['subscription_title'],0,15)).'<input type="hidden" name="subscription_title_'. $subscription['subscription_id'].'" id="subscription_title_'. $subscription['subscription_id'].'" value="'.$subscription['subscription_title'].'" /></strong><input type="text" name="subscription_text_'.$subscription['subscription_id'].'" id="subscription_text_'.$subscription['subscription_id'].'"  class="subscription_text" value="'.$subscription['subscription_title'].'" style="display:none;padding:0px; margin:0px;border:none;"  maxlength="25"/></div>
                    <div class="icon-listing">
                      <ul class="list-icons contacts">
                        '.$edit_link.'
                        '.$delete_link.'
                      </ul>
					   <ul class="list-icons edit_subscription" style="display:none;">
                        <li><a class="btn confirm" onclick="saveSubscriptionTitle(\''.$subscription['subscription_id'].'\');" href="javascript:void(0);">Save</a></li>
                        <li><a class="btn cancel" onclick="javascript:jQuery(this).parents(\'.editing-theme-box\').find(\'.list-icons\').show();jQuery(this).parent().parent().hide();jQuery(\'#subscription_text_'.$subscription['subscription_id'].'\').hide();jQuery(\'#subscription_id_'.$subscription['subscription_id'].'\').show();jQuery(\'#subscription_text_'.$subscription['subscription_id'].'\');$(\'.right-no\').show();" href="javascript:void(0);">Cancel</a></li>
                      </ul>
                    </div>
                  </div>';
					$i++;	//increment i
			}
			echo $select_subscriptions.'/\\'.$subscription_list.'<div class="backdrop"></div>';	//print subscription list
		} else {
			// if subscription list is empty
			echo "No record found";
		}
	}


	/**
	 * Function Create
	 *
	 * 'Create' controller function to create new subscription list.
	 */
	function create(){


		// To check form is submittted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('subscription_title', 'List Name', 'required|min_length[2]|max_length[45]|callback_title_check|trim');

			// To check form is validated true
			if($this->form_validation->run()==true)
			{
				// Retrieve data posted in form posted by user using input class
				$input_array=array('subscription_title'=>$this->input->get_post('subscription_title', TRUE),
				'subscription_is_name'=>'1',
				'subscription_created_by'=>$this->session->userdata('member_id')
				);


				// Sends form input data to database via model object
				$subscription_id=$this->subscription_Model->create_subscription($input_array);

				//Success message
				echo 'success:'.$subscription_id;
			}else if(validation_errors()){	//To display form  validation error
				if($this->input->get_post('subscription_title', TRUE)){
					echo 'error:'.validation_errors();
				}else{
					echo 'error:List Name is required';
				}
			}
		}
	}

	/**
	 * Function title_check
	 *
	 * Function to check if title already exists in database before updating database by input from user.
	 *
	 * @return (bool)	if title already exists in database then return false else return true
	 */
	function title_check(){
		$conditions_array['subscription_title']=$this->input->get_post('subscription_title', TRUE);	//Subscription title
		$conditions_array['subscription_created_by']=$this->session->userdata('member_id');	//member id
		$conditions_array['is_deleted !=']=1;	//member id

		//check subscription id exist or not
		if($this->input->get_post('subscription_id', TRUE)!='')
			$conditions_array['subscription_id !=']=$this->input->get_post('subscription_id', TRUE);
		//Get subscription data from database using subscription_Model
		$subscription_array=$this->subscription_Model->get_subscription_data($conditions_array);

		// returns true if title exits and false if not exits.
		if(count($subscription_array))
		{
			$this->form_validation->set_message('title_check', 'The %s already exists');
			return FALSE;
		}
		else
			return true;
	}

	/**
	 * Function Edit
	 *
	 * 'Edit' controller function to edit existing subscription.
	 *
	 * @param (int) (subscription_id)  contains subscription_id which is used for edit the subscription data
	 */

	function edit($subscription_id=0){


		//	Collect subscription id
		//Protecting MySQL from query string sql injection Attacks
		if(is_numeric($subscription_id)){
			$id = $subscription_id;
		}else{
			$id=0;
		}
		//Initialize subscription data array to store data for subscription to be edited
		$subscription_data=array();



		// To check form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('subscription_title', 'Subscription Title', 'required|min_length[2]|max_length[100]|callback_title_check|trim');
			// Retrieve data posted in form posted by user using input class
			$input_array=array('subscription_title'=>$this->input->get_post('subscription_title', TRUE),
			'subscription_is_Email'=>'1',
			'subscription_is_name'=>'1'
			);

			// To check form is validated
			if($this->form_validation->run()==true)
			{
				// Update subscription by data posted by user
				$this->subscription_Model->update_subscription($input_array,array('subscription_id'=>$id));
				// Display success message by message class
				echo "success:subscription updated successfully";
				exit;
			}else{
				$conditions_array['subscription_id']=$this->input->get_post('subscription_id', TRUE);

				//Get subscription data from database using subscription_Model
				$subscription_array=$this->subscription_Model->get_subscription_data($conditions_array);
				echo "error:".strip_tags(validation_errors()).":".$subscription_array[0]['subscription_title'];
				exit;
			}
			$subscription_data=$input_array;
		}

		/* subscriptions will have count as zero or null when form is not posted.
		   In this case, retreive subscription data from database according to subscription ID.
		*/
		if(!count($subscription_data))
		{
			//Fetch subscription data from database by subscription ID
			$subscription_array=$this->subscription_Model->get_subscription_data(array('subscription_id'=>$id,'subscription_created_by'=>$this->session->userdata('member_id')));
			//Redirects user to listing page if user have not created this subscription or subscription does not exists
			if(!count($subscription_array))
			{
				// Assign  error message by message class
				$this->messages->add('Subscription does not exists or you have not created this Subscription', 'error');
			}

			// Prepare array to send to view
			$subscription_data=array('subscription_title'=>$subscription_array[0]['subscription_title']);
		}

		// Recieve any messages to be shown, when subscription is added or updated
		$messages=$this->messages->get();
		// Add subscription ID to subscription array
		$subscription_data['subscription_id']=$id;

		//Loads  subscription view.
		$this->load->view('contacts/subscription_edit',array('subscription_data'=>$subscription_data,'messages'=>$messages));
	}


	/**
	 * Function Delete
	 *
	 * 'Delete' controller function to Delete existing subscription.
	 *
	 * @param (int) (subscription_id)  contains subscription_id which is used for delete the subscription from database
	 */

	function delete($subscription_id=0)	{


		//	Collect subscription id
		//Protecting MySQL from query string sql injection Attacks
		if(is_numeric($subscription_id)){
			$id = $subscription_id;
		}else{
			$id=0;
		}
		//Check subscription_id not attach with autoresponder
		// Load autoresponder model class which handles database interaction
		$this->load->model('newsletter/Autoresponder_Model');
		$autoresponder_subscription=0;
		//get autoresponder groups
		$autoresponder_group=$this->Autoresponder_Model->get_autoresponder_group(array('is_deleted'=>0,'autoresponder_created_by'=>$this->session->userdata('member_id'),'autoresponder_subscription_id'=>$subscription_id));
		if(count($autoresponder_group)>0){
			$autoresponder_subscription=1;
		}else if($this->input->post('submit_action')=='submit'){

			$fetch_conditions_array=array('subscription_id'=>$id,'subscription_created_by'=>$this->session->userdata('member_id'));
			$subscription_cnt=$this->subscription_Model->get_subscription_count($fetch_conditions_array);
			//check subscription id for login user
			if($subscription_cnt>0){
				// Deletes subscription according to subscription ID
				$this->subscription_Model->delete_subscription(array('subscription_id'=>$id));

				// Deletes subscriber from "subscription List"
				$this->Subscriber_Model->delete_subscriber_from_list(array('subscription_id'=>$id));
			}else{
				echo "subscription id not exist";
			}
		}
		$this->load->view('contacts/subscription_delete',array('subscription_id'=>$subscription_id,'autoresponder_subscription'=>$autoresponder_subscription));
	}
	/**
		Function subval_sort for sorting multi dimensional array
	*/
	function subval_sort($a,$subkey) {
		$c[0]="";
		foreach($a as $k=>$v) {
			$b[$k] = strtolower($v[$subkey]);
		}
		@asort($b);
		foreach($b as $key=>$val){
			$c[] = $a[$key];
		}
		return $c;
	}
	/**
	*	Function add_emailreport_to_contact_list to add subscribers  in Contact list
	*/
	function add_emailreport_to_contact_list($action="",$campaign_id=0, $tinyUrl = ''){	
		
		if($this->input->post('action')=='submit'){
			if($this->input->post('subscription_title')!=''){
				$this->form_validation->set_rules('subscription_title', 'List Name', 'required|min_length[5]|max_length[100]|callback_title_check|trim');
				if($this->form_validation->run()==true){
					$input_array=array('subscription_title'=>$this->input->get_post('subscription_title', TRUE), 'subscription_is_name'=>'1', 'subscription_created_by'=>$this->session->userdata('member_id') );
					$subscription_id=$this->subscription_Model->create_subscription($input_array);
				}	
			}else{
				$this->form_validation->set_rules('subscriptions', ' Contact List', 'required');
				if($this->form_validation->run()==true){
					$subscription_id=$this->input->post('subscriptions');
				}
			}
			if(intval($subscription_id) <= 0){
				echo 'error: '.validation_errors();
				exit;
			}
			if($action=="clickurl"){					
				$fetch_condiotions_array=array('ret.campaign_id'=>$campaign_id, 'counter >'=>0, 'tiny_url'=>$tinyUrl, 'is_autoresponder'=>0);	
				$emailreport_data=$this->Emailreport_Model->get_emailreport_subscriber_click($fetch_condiotions_array);
			}else{
				if($action=="sent"){
					$fetch_condiotions_array=array('campaign_id'=>$campaign_id, 'user_id'=>$this->session->userdata('member_id'));
				}elseif($action=="read"){
					$fetch_condiotions_array=array('campaign_id'=>$campaign_id,'user_id'=>$this->session->userdata('member_id'), 'email_track_read'=>1);
				}elseif($action=="unread"){
					$fetch_condiotions_array=array('campaign_id'=>$campaign_id,'user_id'=>$this->session->userdata('member_id'), 'email_track_read'=>0);
				}elseif($action=="forwardemail"){
					$fetch_condiotions_array=array('campaign_id'=>$campaign_id,'user_id'=>$this->session->userdata('member_id'), 'email_track_forward >'=>0);				
				}elseif($action=="click"){
					$fetch_condiotions_array=array('campaign_id'=>$campaign_id,'user_id'=>$this->session->userdata('member_id'), 'email_track_click >'=>0);
				}elseif($action=="unsubscribes"){
					$fetch_condiotions_array=array('campaign_id'=>$campaign_id,'user_id'=>$this->session->userdata('member_id'), 'email_track_unsubscribes >'=>0);
				}elseif($action=="complaints"){
					$fetch_condiotions_array=array('campaign_id'=>$campaign_id,'user_id'=>$this->session->userdata('member_id'), 'email_track_complaint'=>1);
				}elseif($action=="bounced"){
					$fetch_condiotions_array=array('campaign_id'=>$campaign_id, 'user_id'=>$this->session->userdata('member_id'),'email_track_bounce >'=>0);
				} 
				$emailreport_data=$this->Emailreport_Model->get_emailreport_subscriber($fetch_condiotions_array);
			}
			if(count($emailreport_data)>0){
				foreach ($emailreport_data as $emailreport){
					$arrEmailExploded = explode( '@',$emailreport['subscriber_email_address'] );
					$emailreport['subscriber_email_domain'] = $arrEmailExploded[1];
				
				
					$qry = "INSERT INTO red_email_subscribers SET ";
					$flds = '';
					$flds .=  'subscriber_email_address = \'' . mysql_real_escape_string($emailreport['subscriber_email_address']) . '\', ';
					$flds .=  'subscriber_email_domain = \'' . mysql_real_escape_string($emailreport['subscriber_email_domain']) . '\', ';

					$flds .=  'subscriber_created_by = ' . $this->session->userdata('member_id') ;
					$qry .=  $flds .' ON DUPLICATE KEY UPDATE ' . $flds . ', is_deleted = 0 , subscriber_id=LAST_INSERT_ID(subscriber_id)';
					$this->db->query($qry);
					$last_inserted_id = $this->db->insert_id();
					if (($last_inserted_id > 0) &&($subscription_id>0)){
						$input_array=array('subscriber_id'=>$last_inserted_id,'subscription_id'=>$subscription_id);
						$this->Subscriber_Model->replace_subscription_subscriber($input_array);
					}
				}
			}
			echo "success:Contacts added to the created list successfully!";
			//echo "success:".$this->db->last_query();
			exit;
		}
		
		

		$fetch_condiotions_array=array('subscription_created_by'=>$this->session->userdata('member_id'), 'is_deleted'=>0, 'subscription_id >'=>0);		
		$subscription_list=$this->subscription_Model->get_subscription_data($fetch_condiotions_array);
		
		// Recieve any messages to be shown, when category is added or updated
		$messages=$this->messages->get();
		
		$this->load->view('emailreport/add_emailreport_view',array('action'=>$action,'campaign_id'=>$campaign_id,'tinyUrl'=>$tinyUrl,'messages' =>$messages,'subscription_list'=>$subscription_list));
		
	}
	/**
	*	Function add_emailreport_to_contact_list to add subscribers  in Contact list
	*/
	function add_signupform_contact_to_list($action="",$fid=0){	
		
		if($this->input->post('action')=='submit'){
			if($this->input->post('subscription_title')!=''){
				$this->form_validation->set_rules('subscription_title', 'List Name', 'required|min_length[5]|max_length[100]|callback_title_check|trim');
				if($this->form_validation->run()==true){
					$input_array=array('subscription_title'=>$this->input->get_post('subscription_title', TRUE), 'subscription_is_name'=>'1', 'subscription_created_by'=>$this->session->userdata('member_id') );
					$subscription_id=$this->subscription_Model->create_subscription($input_array);
				}	
			}else{
				$this->form_validation->set_rules('subscriptions', ' Contact List', 'required');
				if($this->form_validation->run()==true){
					$subscription_id=$this->input->post('subscriptions');
				}
			}
			if(intval($subscription_id) <= 0){
				echo 'error: '.validation_errors();
				exit;
			}
			if($action=="view"){				
				$signupform_data=$this->Signup_Model->get_signupform_stats(array('form_id'=>$fid,'activity'=>'1'),$config['per_page'],$start);
			}elseif($action=="confirmation"){	
				$signupform_data=$this->Signup_Model->get_signupform_stats(array('form_id'=>$fid,'activity'=>'3'),$config['per_page'],$start);
			} 
			 
			if(count($signupform_data)>0){
				foreach ($signupform_data as $contact){					
					if (($contact['subscriber_id'] > 0) &&($subscription_id>0)){						
						$this->Subscriber_Model->replace_subscription_subscriber(array('subscriber_id'=>$contact['subscriber_id'],'subscription_id'=>$subscription_id));
					}
				}
			}
			echo "success:Contacts added to the created list successfully!";
			//echo "success:".$this->db->last_query();
			exit;
		}
		
		

		$fetch_condiotions_array=array('subscription_created_by'=>$this->session->userdata('member_id'), 'is_deleted'=>0, 'subscription_id >'=>0);		
		$subscription_list=$this->subscription_Model->get_subscription_data($fetch_condiotions_array);
		
		// Recieve any messages to be shown, when category is added or updated
		$messages=$this->messages->get();
		
		$this->load->view('signup/add_signupform_contacts',array('action'=>$action,'form_id'=>$fid,'messages' =>$messages,'subscription_list'=>$subscription_list));
		
	}
	/**
		Function add_autoresponder_emailreport_to_contact_list to add subscribers  in Contact list
	*/
	function add_autoresponder_emailreport_to_contact_list($action="",$campaign_id=0,$scheduled_id=0){
		if($this->input->post('action')=='submit'){
			if($this->input->post('subscription_title')!=''){			
				$this->form_validation->set_rules('subscription_title', 'List Name', 'required|min_length[5]|max_length[100]|callback_title_check|trim');
				if($this->form_validation->run()==true){
					$input_array=array('subscription_title'=>$this->input->get_post('subscription_title', TRUE), 'subscription_is_name'=>'1', 'subscription_created_by'=>$this->session->userdata('member_id') );
					$subscription_id=$this->subscription_Model->create_subscription($input_array);
				}
			}else{
				$this->form_validation->set_rules('subscriptions', ' Contact List', 'required');
				if($this->form_validation->run()==true){
					$subscription_id=$this->input->post('subscriptions');
				}
			}
			if(intval($subscription_id) <= 0){
				echo 'error: '.validation_errors();
				exit;
			}
				
			if($action=="clickemail"){
				$fetch_condiotions_array=array('campaign_id'=>$id,'counter >'=>0,'is_autoresponder'=>1);
				$emailreport_data=$this->Emailreport_Model->get_emailreport_click($fetch_condiotions_array);
				
			}else{	
				if($action=="sent")	$fetch_condiotions_array=array(	'autoresponder_scheduled_id'=>$scheduled_id, 'res.subscriber_created_by'=>$this->session->userdata('member_id'));
				elseif($action=="read")$fetch_condiotions_array=array('autoresponder_scheduled_id'=>$scheduled_id, 'email_track_read'=>1,'res.subscriber_created_by'=>$this->session->userdata('member_id'));
				elseif($action=="unread")$fetch_condiotions_array=array('autoresponder_scheduled_id'=>$scheduled_id, 'email_track_read'=>0,'res.subscriber_created_by'=>$this->session->userdata('member_id'));
				elseif($action=="forwardemail")$fetch_condiotions_array=array('autoresponder_scheduled_id'=>$scheduled_id, 'email_track_forward >'=>0,'res.subscriber_created_by'=>$this->session->userdata('member_id'));
				elseif($action=="click")$fetch_condiotions_array=array('autoresponder_scheduled_id'=>$scheduled_id, 'email_track_click >'=>0,'res.subscriber_created_by'=>$this->session->userdata('member_id'));
				elseif($action=="unsubscribes")$fetch_condiotions_array=array('autoresponder_scheduled_id'=>$scheduled_id, 'email_track_unsubscribes >'=>0,'res.subscriber_created_by'=>$this->session->userdata('member_id'));
				elseif($action=="complaints")$fetch_condiotions_array=array('autoresponder_scheduled_id'=>$scheduled_id, 'email_track_complaint >'=>0,'res.subscriber_created_by'=>$this->session->userdata('member_id'));
				elseif($action=="bounced")$fetch_condiotions_array=array('autoresponder_scheduled_id'=>$scheduled_id, 'email_track_bounce >'=>0,'res.subscriber_created_by'=>$this->session->userdata('member_id'));				
				
				$emailreport_data=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber($fetch_condiotions_array);
			}		
				
				 
			if(count($emailreport_data)>0){
				foreach ($emailreport_data as $emailreport){				 
					$last_inserted_id = $emailreport['email_track_subscriber_id'];
					if (($last_inserted_id > 0) &&($subscription_id>0)){
						$input_array=array('subscriber_id'=>$last_inserted_id,'subscription_id'=>$subscription_id);
						$this->Subscriber_Model->replace_subscription_subscriber($input_array);
					}
				}
			}
			echo "success:Contacts added to the list successfully!";
			exit;
			
		}
		 

		/**
			Creating array of conditions to checked with database with conditions.

		*/

		$fetch_condiotions_array=array('subscription_created_by'=>$this->session->userdata('member_id'),'is_deleted'=>0,'subscription_id >'=>0);
		
		// Fetches subscription data from database
		$subscription_list=$this->subscription_Model->get_subscription_data($fetch_condiotions_array);
		 
		$messages=$this->messages->get();
		if(count($subscription_list)>0){
			//Loads  view.
			$this->load->view('emailreport/add_emailreport_view',array('action'=>$action,'campaign_id'=>$campaign_id,'messages' =>$messages,'subscription_list'=>$subscription_list,'autoresponder'=>1,'scheduled_id'=>$scheduled_id));
		}else{
			echo "<div style=\"margin:20px;width:240px;\">Subscription List not exist</div>";
		}
	}
		/**
	* Populate RedCappi account for Free & Paid users
	*/
		function populateRCMembers(){
		// Delete records for RedCappi Free & Paid users
		$this->db->query("delete from red_email_subscription_subscriber where subscription_id=122 or subscription_id=245");
		// Populate free users
		$rsFreeMemberAsContact = $this->db->query("select email_address from red_members m inner join red_member_packages mp on m.member_id=mp.member_id where m.is_deleted=0 and m.status='active' and mp.package_id < 1");
		foreach($rsFreeMemberAsContact->result_array() as $m_rec){	
			$member_email = $m_rec['email_address'];
			$sid = $this->addMemberAsRCContact($member_email);
			$this->Subscriber_Model->replace_subscription_subscriber(array('subscriber_id'=>$sid,'subscription_id'=>122)); // Free Users
		}
		$rsFreeMemberAsContact->free_result();
		// Populate paid users
		$yesterday 	= date("Y-m-d", strtotime("-1 days"));
		$rsPaidMemberAsContact = $this->db->query("select email_address from red_members m inner join red_member_packages mp on m.member_id=mp.member_id where m.is_deleted=0 and m.status='active' and mp.package_id > 0 and next_payement_date > '$yesterday'");
		foreach($rsPaidMemberAsContact->result_array() as $m_rec){	
			$member_email = $m_rec['email_address'];
			$sid = $this->addMemberAsRCContact($member_email);
			$this->Subscriber_Model->replace_subscription_subscriber(array('subscriber_id'=>$sid,'subscription_id'=>245)); // Paid Users
		}
		$rsPaidMemberAsContact->free_result();
		// Populate failed-cc users
		$rsFailedccPaidMemberAsContact = $this->db->query("select email_address from red_members m inner join red_member_packages mp on m.member_id=mp.member_id where m.is_deleted=0 and m.status='active' and mp.package_id > 0 and next_payement_date < now()");
		foreach($rsFailedccPaidMemberAsContact->result_array() as $m_rec){	
			$member_email = $m_rec['email_address'];
			$sid = $this->addMemberAsRCContact($member_email);
			$this->Subscriber_Model->replace_subscription_subscriber(array('subscriber_id'=>$sid,'subscription_id'=>123)); // Failed-cc Users
		}
		$rsFailedccPaidMemberAsContact->free_result();
		
	}
	function addMemberAsRCContact($eml){
		$rsCheckMemberAsContact = $this->db->query("select subscriber_id from red_email_subscribers where subscriber_created_by=157 and subscriber_email_address='$eml'");
		if($rsCheckMemberAsContact->num_rows() > 0){
			$sid =$rsCheckMemberAsContact->row()->subscriber_id;
		}else{
			$arrEmailExploded = explode( '@',$eml );
			$eml_domain = $arrEmailExploded[1];
			$qry = "INSERT INTO red_email_subscribers SET ";
			$flds = '';
			$flds .=  'subscriber_email_address = \'' . mysql_real_escape_string($eml) . '\', ';
			$flds .=  'subscriber_email_domain = \'' . mysql_real_escape_string($eml_domain) . '\', ';

			$flds .=  'subscriber_created_by = 157' ;
			$qry .=  $flds .' ON DUPLICATE KEY UPDATE ' . $flds . ', is_deleted = 0 , subscriber_id=LAST_INSERT_ID(subscriber_id)';
			$this->db->query($qry);
			
			$sid = $this->db->insert_id();	
		}
		$rsCheckMemberAsContact->free_result();
		return $sid;
	}
}
?>
