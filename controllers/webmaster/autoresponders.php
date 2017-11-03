<?php
class Autoresponders extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');

		// Load the campaign model which interact with database
		$this->load->model('newsletter/Cronjob_Model');		
		// HTTPS/SSL enabled
		force_ssl();
		$this->output->enable_profiler(false);

	}
	/**
		Function to list campaign whose status is ready
	**/
	function index($mode, $start=0){
		if($mode == 'verified')$is_verified = 1;else $is_verified = 0;			
		
		$fetch_conditions_array= array('recs.is_deleted'=>0,'rec.autoresponder_scheduled_id !='=>0,'recs.autoresponder_scheduled_status'=>1,'set_sheduled'=>0,'rec.campaign_status'=>1,'rec.is_deleted'=>0,'rec.is_status'=>0, 'rec.is_verified'=>$is_verified);
		
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/autoresponders/index/'.$mode;
		$config['total_rows']= 	$this->Cronjob_Model->get_autoresponder_cronjob_count($fetch_conditions_array); 
		$config['per_page']=20;
		$config['uri_segment']=5;
		$this->pagination->initialize($config);				
		$paging_links=$this->pagination->create_links();		
		
		$as_array=$this->Cronjob_Model->get_autoresponder_cronjob_data($fetch_conditions_array,$config['per_page'],$start);
	 
		$i=0;
		foreach($as_array as $thisAutoresponder){
			$as_array[$i]['member_username'] = $this->db->query("select member_username from red_members where member_id='".$thisAutoresponder['campaign_created_by']."'")->row()->member_username;	 
			$i++;			
		}
		 
		// Recieve any messages to be shown, when campaign is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		if($_POST['mode']=='search' AND !isset($_POST['btn_cancel'])){
			$contacts_array['username']=$_POST['username'];
		}
		#Get shoreten url 
		$shorten_url=get_shorten_url();
		//Loads header, users listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Autoresponders','logo_link'=>$logo_link));
		$this->load->view('webmaster/autoresponder_list',array('autoresponders'=>$as_array,'paging_links'=>$paging_links,'messages' =>$messages,'mode'=>$mode));
		$this->load->view('webmaster/footer');
	}
	
	 
	/**
	*	Function to verify the signup-form
	*/
	function verifyit($mode, $id=0){	
		$this->db->query("update red_signup_form set is_verified=1 where id='$id'");		
		$this->messages->add('Signup-form verified successfully', 'success');
		
		redirect('webmaster/signupforms/index/'.$mode);
	}
	function update_aresponder(){	
		$aid = $this->input->post('aid');
		$is_verified = $this->input->post('is_verified');		
		$sqlupdate = "update red_email_autoresponders set is_verified='$is_verified' where campaign_id='$aid'";			
		$this->db->query($sqlupdate);		
		echo 'Updated';		
	}	 
}
?>