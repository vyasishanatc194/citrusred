<?php
class Fromemail extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');
		$this->load->helper('transactional_notification'); 
		force_ssl();
		$this->output->enable_profiler(false);

	}
	/**
	*	Function to list campaign whose status is ready
	*/
	function index( $start=0){		
		$membername_tag = trim($this->input->post('username'));
		if($membername_tag != ''){
			$sqlFormEmail = "from red_member_from_email t1 inner join red_members t2 on t1.member_id=t2.member_id where t1.is_verified='0' and t1.is_deleted=0 and (domain_reason is not null and domain_reason !='') and t2.member_username like'$membername_tag%'";		
		}else{
			$sqlFormEmail = "from red_member_from_email t1 inner join red_members t2 on t1.member_id=t2.member_id where t1.is_verified='0' and t1.is_deleted=0 and (domain_reason is not null and domain_reason !='')";		
		}
		
		$rsFEmailCount = $this->db->query("select count(*) ct $sqlFormEmail");
		$totForm = $rsFEmailCount->row()->ct;
		$rsFEmailCount->free_result();
		$fetch_conditions_array=array('is_verified'=>$is_verified);		 
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/fromemail/index/';
		$config['total_rows']= 	$totForm ;  
		$config['per_page']= $per_page = 20;
		$config['uri_segment']=5;
		
		$this->pagination->initialize($config);			
		$paging_links=$this->pagination->create_links();	
		
		$rsFEmail = $this->db->query("select t1.*,t2.member_username $sqlFormEmail limit $start, $per_page");
		$femail_data_array = $rsFEmail->result_array();
		$rsFEmail->free_result();
		

		// Recieve any messages to be shown, when campaign is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		if($_POST['mode']=='search' AND !isset($_POST['btn_cancel'])){
			$contacts_array['username']=$_POST['username'];
		}
		#Get shoreten url 
		$shorten_url=get_shorten_url();
		//Loads header, users listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Verify From Email','logo_link'=>$logo_link));
		$this->load->view('webmaster/fromemail_list',array('fromemails'=>$femail_data_array,'contacts_array'=>$contacts_array,'paging_links'=>$paging_links,'messages' =>$messages,'mode'=>$mode));
		$this->load->view('webmaster/footer');
	}
	
	/**
	*	Function to verify the signup-form
	*/
	function verifyit( $aid=''){
		$rsGetMember = $this->db->query("select t1.member_id, t1.first_name, t1.member_username, t1.email_address,t2.email_address new_email from red_members t1 inner join red_member_from_email t2 on t1.member_id=t2.member_id where unique_string='$aid'");
		if($rsGetMember->num_rows() > 0){
			$fname = $rsGetMember->row()->first_name;
			$uname = $rsGetMember->row()->member_username;
			$email_to = $rsGetMember->row()->email_address;
			$new_email = $rsGetMember->row()->new_email;
			$name = ( $fname !='')? $fname : $uname;
			
			
			$this->db->query("update red_member_from_email set is_verified=1 where unique_string='$aid'");		
			$this->messages->add('From Email verified successfully', 'success');
			
			
			
			// START: mail to user that, he can send campaign using newly added from-email
			$textMessage ="Hello ".$name.", \r\n\r\n\r\n";
			//$textMessage .="Thank you for your patience and cooperation.\r\n";
			$textMessage .= "Your request to add a new from-email \"{$new_email}\" for your campaigns, is approved now. \r\n Now you can use this address in your RedCappi account and send emails from this address. \r\n\r\n\r\n";
			$textMessage .="Thanks,\r\n\r\n";
			$textMessage .="The RedCappi Team\r\n";
			
			$message ="Hello ".$name.", <br/><br/>";
			$message .="Your request to add a new from-email \"{$new_email}\" for your campaigns, is approved now.<br/>";
			$message .="Now you can use this address in your RedCappi account and send emails from this address.<br/><br/>";
			$message .="Thanks,<br/>";
			$message .="The RedCappi Team<br/><br/>";

			// send email using transactional_notification_helper
			send_tmail($email_to, SYSTEM_EMAIL_FROM, 'RedCappi', 'New sending email verified!',$message,$textMessage,'redcappi.com');
			// END
		}
		$rsGetMember->free_result();
		
		redirect('webmaster/fromemail/index/');
	}
	 /**
	*	Function to Disallow the from-email
	*/
	function disallow( $aid=''){
		$rsGetMember = $this->db->query("select t1.member_id, t1.first_name, t1.member_username, t1.email_address,t2.email_address new_email from red_members t1 inner join red_member_from_email t2 on t1.member_id=t2.member_id where unique_string='$aid'");
		if($rsGetMember->num_rows() > 0){
			$fname = $rsGetMember->row()->first_name;
			$uname = $rsGetMember->row()->member_username;
			$email_to = $rsGetMember->row()->email_address;
			$new_email = $rsGetMember->row()->new_email;
			$name = ( $fname !='')? $fname : $uname;
			
			
			
			$this->db->query("update red_member_from_email set is_deleted=1 where unique_string='$aid'");	
			$this->messages->add('From Email disallowed & removed successfully', 'success');
			
			
			
			// START: mail to user that, he can send campaign using newly added from-email
			$textMessage ="Hello ".$name.", \r\n\r\n\r\n";
			//$textMessage .="Thank you for your patience and cooperation.\r\n";
			$textMessage .= "Your request to add a new from-email \"{$new_email}\" for your campaigns, is not approved and disallowed. \r\n";
			$textMessage .= "Please contacts us at \"support@redcappi.com\" for more details. \r\n\r\n\r\n";
			$textMessage .="Thanks,\r\n\r\n";
			$textMessage .="The RedCappi Team\r\n";
			
			$message ="Hello ".$name.", <br/><br/>";
			$message .="Your request to add a new from-email \"{$new_email}\" for your campaigns, is not approved and disallowed.<br/>";
			$message .="Please contacts us at \"support@redcappi.com\" for more details.<br/><br/>";			
			$message .="Thanks,<br/>";
			$message .="The RedCappi Team<br/><br/>";

			// send email using transactional_notification_helper
			send_tmail($email_to, SYSTEM_EMAIL_FROM, 'RedCappi', 'New sending email disallowed!',$message,$textMessage,'redcappi.com');
			// END
		}
		$rsGetMember->free_result();
		
		redirect('webmaster/fromemail/index/');
	}
	 /**
	*	Function to Disallow the from-email
	*/
	function remove( $aid=''){
		$rsGetMember = $this->db->query("select t1.member_id, t1.first_name, t1.member_username, t1.email_address,t2.email_address new_email from red_members t1 inner join red_member_from_email t2 on t1.member_id=t2.member_id where unique_string='$aid'");
		if($rsGetMember->num_rows() > 0){
			$fname = $rsGetMember->row()->first_name;
			$uname = $rsGetMember->row()->member_username;
			$email_to = $rsGetMember->row()->email_address;
			$new_email = $rsGetMember->row()->new_email;
			$name = ( $fname !='')? $fname : $uname;
			
			$this->db->query("update red_member_from_email set is_deleted=1 where unique_string='$aid'");	
			
				
			$this->messages->add('From-email removed successfully', 'success');			 
		}
		$rsGetMember->free_result();
		
		redirect('webmaster/fromemail/index/');
	}
 
}
?>