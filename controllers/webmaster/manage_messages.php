<?php
class Manage_messages extends CI_Controller{
	function __construct(){
		parent::__construct();
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');
			
		$this->load->helper('cookie');		

		// Load the user model which interact with database
		$this->load->model('webmaster/MessagesModel');
		
		 
		# HTTPS/SSL enabled
		force_ssl();
	}
	/**
	* List of messages
	*/	
	function index(){
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');
		
		$message_data_array=$this->MessagesModel->read_messages();		  
		 
		$logo_link="webmaster/dashboard_stat";
		//Loads header, users listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Users','logo_link'=>$logo_link));
		$this->load->view('webmaster/manage_messages',array('message_list'=>$message_data_array));	 
		$this->load->view('webmaster/footer');
	}
	
	function message_edit($message_id){
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');		
		$user_data_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit'){
			// Validation rules are applied
			$this->form_validation->set_rules('message_name', 'Heading', 'required');
			
			//Prepare member array from posted data
			$message_data = array(array(
				'message_id' => $message_id,
				'message_name' => $this->input->post('message_name',true),
				//'message_type' => $this->input->post('message_type',true),
				//'user_type' => $this->input->post('user_type',true),
				//'message_body_type' => $this->input->post('message_body_type',true),
				'message_body' => $this->input->post('message_body',true),
				'email_subject' => $this->input->post('email_subject',true),
				'email_body' => $this->input->post('email_body',true),
				'is_mail_notification' => $this->input->post('is_mail_notification',true),
				'message_status' => $this->input->post('message_status',true),				
			));	 
			
			if($this->form_validation->run()) {
				
				 
				$this->MessagesModel->update_message($message_data[0],array('message_id'=>$message_id));
				  
				$this->messages->add('User updated successfully', 'success');
				redirect('webmaster/manage_messages');
				
			}			
			 
			 
		}
		
		if(!count($message_data)) {
			$message_data=$this->MessagesModel->read_messages(array('message_id'=>$message_id));	 
		} 
			 
		// Recieve any messages to be shown, when campaign is added or updated
		$messages=$this->messages->get();
	
	
		$logo_link="webmaster/dashboard_stat";
		//Loads header, users edit  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Message','logo_link'=>$logo_link));
		$this->load->view('webmaster/message_edit',array('message_detail'=>$message_data[0],'messages' =>$messages));
		$this->load->view('webmaster/footer');
	} 
	function message_create(){
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');
		 
		
		$message_data_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit'){
			// Validation rules are applied
			$this->form_validation->set_rules('message_name', 'Heading', 'required');
			$this->form_validation->set_rules('message_body', 'Message Content', 'required');
			
				//Prepare member array from posted data
			$message_data = array(array(				 
				'message_name' => $this->input->post('message_name',true),
				//'message_type' => $this->input->post('message_type',true),
				//'user_type' => $this->input->post('user_type',true),
				//'message_body_type' => $this->input->post('message_body_type',true),
				'message_body' => $this->input->post('message_body',true),
				'email_subject' => $this->input->post('email_subject',true),
				'email_body' => $this->input->post('email_body',true),
				'is_mail_notification' => $this->input->post('is_mail_notification',true),
				'message_status' => $this->input->post('message_status',true),				
			));	 
			if($this->form_validation->run()) {				 
					$inserted_user_id=$this->MessagesModel->create_message($message_data[0]);						 
					$this->messages->add('Message created successfully', 'success');
					redirect('webmaster/manage_messages/');				 
			}
			
			 
		}		
		
		// Recieve any messages to be shown
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, users edit  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Create User','logo_link'=>$logo_link));
		$this->load->view('webmaster/message_add',array('message_detail'=>$message_data[0],'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	function member_message($mid=1){
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');
		
		$member_message_data=$this->MessagesModel->read_member_message($mid);		  
		$message_data_array=$this->MessagesModel->read_messages();		  
		
		$logo_link="webmaster/dashboard_stat";
		//Loads header, users listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Users','logo_link'=>$logo_link));
		$this->load->view('webmaster/member_message',array('mid'=>$mid, 'member_message_list'=>$member_message_data, 'message_list'=>$message_data_array));	 
		$this->load->view('webmaster/footer');
	}
	
	function assign_message(){
		$this->load->helper('transactional_notification');	
		$this->load->model('UserModel');
		
		if($this->input->post('action')=='Submit'){
			// Validation rules are applied
			$this->form_validation->set_rules('member_id', 'Member', 'required');
			$this->form_validation->set_rules('message_id', 'Message', 'required');
			
			if($this->form_validation->run()) {			
				$memid = $this->input->post('member_id');
				$msgid = $this->input->post('message_id');
				$this->UserModel->attachMessage(array('member_id'=>$memid, 'message_id'=>$msgid),array('member_id'=>$memid, 'message_id'=>$msgid));
			
				
				// Send member-message notification-email
				$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$memid));				
				$user_name = ($user_data_array[0]['first_name'] != '')? $user_data_array[0]['first_name'] : $user_data_array[0]['member_username'] ;
				 
				$message_data = $this->MessagesModel->read_messages(array( 'message_id'=>$msgid));
				$message_mail_subject 	= $message_data[0]['email_subject'];
				$message_mail_body		= $message_data[0]['email_body'];
				$message_mail_body = str_replace('[FIRSTNAME_OR_USERNAME]',$user_name , $message_mail_body);
				send_member_message_email($user_data_array[0]['email_address'], 'support@redcappi.com', 'RedCappi Support', $message_mail_subject, nl2br($message_mail_body), $message_mail_body);				
				redirect('webmaster/manage_messages/member_message');				 
			}
		}
	}
	function remove_member_message($memberid, $messageid){
		//$this->db->query("delete from red_member_message where `member_id`='{$memberid}' and `message_id`='{$messageid}'");
		$this->db->query("update red_member_message set is_deleted=1 where `member_id`='{$memberid}' and `message_id`='{$messageid}' and is_deleted=0");
		redirect('webmaster/manage_messages/member_message/');		
		exit;
	}
}
?>
