<?php
class Signupforms extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');

		// Load the campaign model which interact with database
		$this->load->model('newsletter/Signup_Model');
		
		# HTTPS/SSL enabled
		force_ssl();
		$this->output->enable_profiler(false);

	}
	/**
		Function to list campaign whose status is ready
	**/
	function index($mode, $start=0){
		if($mode == 'verified')$is_verified = 1;else $is_verified = 0;
		if($mode == 'hidden')$is_hidden = 1;else $is_hidden = 0;
		$membername_tag = trim($this->input->post('username'));
		if($membername_tag != ''){
			$sqlSignupForm = "from red_signup_form t1 inner join red_members t2 on t1.member_id=t2.member_id where t1.is_verified='$is_verified' and t1.is_hidden ='$is_hidden' and t1.is_deleted=0 and t2.member_username like'$membername_tag%'";		
		}else{
			//$sqlSignupForm = "from red_signup_form t1 where is_verified='$is_verified' and is_hidden ='$is_hidden' and is_deleted=0";
			$sqlSignupForm = "from red_signup_form t1 inner join red_members t2 on t1.member_id=t2.member_id where t1.is_verified='$is_verified' and t1.is_hidden ='$is_hidden' and t1.is_deleted=0";		
		}
		$rsSignupCount = $this->db->query("select count(t1.id) ct $sqlSignupForm");
		$totForm = $rsSignupCount->row()->ct;
		$rsSignupCount->free_result();
		$fetch_conditions_array=array('is_verified'=>$is_verified, 'is_hidden'=>$is_hidden,'is_deleted'=>0);		 
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/signupforms/index/'.$mode;
		$config['total_rows']= 	$totForm ; //$this->Signup_Model->get_signup_count($fetch_conditions_array);
		$config['per_page']= $per_page = 20;
		$config['uri_segment']=5;
		
		$this->pagination->initialize($config);			
		$paging_links=$this->pagination->create_links();	
		
		$rsSignupForms = $this->db->query("select t1.*,t2.member_username $sqlSignupForm order by id desc limit $start, $per_page");
		$signupform_data_array = $rsSignupForms->result_array();
		$rsSignupForms->free_result();
		//print_r($signupform_data_array);
		
		//$signupform_data_array=$this->Signup_Model->get_signupform_toverify($fetch_conditions_array,$config['per_page'],$start);
	
		$i=0;
		foreach($signupform_data_array as $signupform){		
			//$signupform_data_array[$i]['member_username'] = $this->db->query("select member_username from red_members where member_id='".$signupform['member_id']."'")->row()->member_username;	 
			$signupform_data_array[$i]['sent_confirmation_email'] = $this->db->query("select count(subscriber_id)ct from red_email_subscribers where subscriber_created_by='".$signupform['member_id']."' and signup_form_id='".$signupform['id']."' and is_signup=1")->row()->ct;	 
			$signupform_data_array[$i]['clicked_confirmation_email'] = $this->db->query("select count(subscriber_id)ct from red_email_subscribers where subscriber_created_by='".$signupform['member_id']."' and signup_form_id='".$signupform['id']."' and is_signup=1 and is_deleted=0")->row()->ct;	 
			$signupform_data_array[$i]['signle_optin_count'] = $this->db->query("select count(subscriber_id)ct from red_email_subscribers where subscriber_created_by='".$signupform['member_id']."' and signup_form_id='".$signupform['id']."' and is_signup=1 and is_single_optin=1")->row()->ct;	 			
			$i++;			
		}
		/* echo "<pre>";	
		print_r($signupform_data_array);
		echo "</pre>"; */	
		// Recieve any messages to be shown, when campaign is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		if($_POST['mode']=='search' AND !isset($_POST['btn_cancel'])){
			$contacts_array['username']=$_POST['username'];
		}
		#Get shoreten url 
		$shorten_url=get_shorten_url();
		//Loads header, users listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Signup-forms','logo_link'=>$logo_link));
		$this->load->view('webmaster/signupform_list',array('signupforms'=>$signupform_data_array,'contacts_array'=>$contacts_array,'paging_links'=>$paging_links,'messages' =>$messages,'mode'=>$mode));
		$this->load->view('webmaster/footer');
	}
	
	/**
	*	Function delete to remove the signup-form
	*/
	function delete($mode, $id=0){
		# Load the campaign model which interact with database
		
		$this->Signup_Model->delete_signup(array('id'=>$id));
		
		$this->messages->add('Signup-form deleted successfully', 'success');
		
		redirect('webmaster/signupforms/index/'.$mode);
	}
	/**
	*	Function to verify the signup-form
	*/
	function verifyit($mode, $id=0){	
		$this->db->query("update red_signup_form set is_verified=1 where id='$id'");		
		$this->messages->add('Signup-form verified successfully', 'success');
		
		redirect('webmaster/signupforms/index/'.$mode);
	}
	function update_signupform($mode){	
		$sid = $this->input->post('sid');
		$is_verified = $this->input->post('is_verified');
		$singleoptin = $this->input->post('singleoptin');
		if($mode == 'verify')
			$sqlupdate = "update red_signup_form set is_verified='$is_verified' where id='$sid'";
		elseif($mode == 'singleoptin')	
			$sqlupdate = "update red_signup_form set single_opt_in='$singleoptin' where id='$sid'";
			
		$this->db->query($sqlupdate);		
		echo 'Updated';
		
	}
	function view($id=0){
		
		if($this->session->userdata('webmaster_id')==''){
			echo "<div style=\"margin:20px;width:240px;\">Your Session seems to have expired. Please try refreshing the page to login again.</div>";
		}else{
			
			$signupform_data_array = $this->Signup_Model->get_signup_data(array('id'=>$id));
			$this->load->view('webmaster/signupform_view',array('signupform'=>$signupform_data_array[0]));
		}
	}
	
	/**
		Function to count number of contacts for campaign
	**/
	function count_subscribers($id=0){
		$fetch_conditions_array=array('campaign_id'=>$id,'rec.is_deleted'=>0);
		$campaign=$this->Campaign_Model->get_campaign_data($fetch_conditions_array,$config['per_page']);
		
		$where_in=explode(',',$campaign[0]['subscription_list']);

		// Load subscriber model class which handles database interaction
		$this->load->model('newsletter/Subscriber_Model');
			
		$fetch_condiotions_array=array(
			'res.subscriber_created_by'=>$this->session->userdata('member_id'),
			'res.subscriber_status'=>1,
			'res.is_deleted'=>0,
			'res.subscrber_bounce'=>0,
		);
		$subscriber_count=$this->Subscriber_Model->get_subscriber_count($fetch_condiotions_array,"",$where_in);		
		return $subscriber_count;
	}
	/**
		function to get all contacts
	**/
	function get_all_contacts($id=0,$user_id=0,$type=""){
		// Load emailreport model class which handles database interaction
		$this->load->model('newsletter/Emailreport_Model');	
		
		// Get Contacts
		$email_subscribers=$this->Emailreport_Model->get_emailreport_data(array('campaign_id'=>$id));
		if($type=="function"){
			return count($email_subscribers);
		}else{
			$logo_link="webmaster/dashboard_stat";
			$this->load->view('webmaster/header',array('title'=>'Manage Campaign','logo_link'=>$logo_link));
			$this->load->view('webmaster/subscriber_list',array('email_subscribers'=>$email_subscribers));
			$this->load->view('webmaster/footer');
		}
	}	
}
?>