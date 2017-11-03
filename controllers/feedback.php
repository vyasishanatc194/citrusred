<?php
class Feedback extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		// Load the feedback model which interact with database
		$this->load->model('Feedback_Model');
		$this->load->library('messages');
		force_ssl();		
	}
	/**
		Function submit to post feedback of user
	**/
	function create(){ 
		if($this->input->post('submit_action')=='submit'){
			// Validation rules are applied
			$this->form_validation->set_rules('email_address', 'Email Address', 'required|valid_email');
			$this->form_validation->set_rules('subject', 'Subject', 'required');
			$this->form_validation->set_rules('message', 'Message', 'required');
			// To check form is validated
			if($this->form_validation->run()){
				// Retrieve data posted in form posted by user using input class
				$input_array=array('email_address'=>$this->input->get_post('email_address', TRUE),
				'subject'=>$this->input->get_post('subject', TRUE),
				'message'=>$this->input->get_post('message', TRUE)
				);
				$this->feedback_notification();
				// Sends form input data to database via model object
				$this->Feedback_Model->create_feedback($input_array);
				
				// print success message
				echo "success:Feedback sent successfully";
				exit;
			}else{
				// print validation errors
				echo "error:".validation_errors();
				exit;
			}
		}
		//Loads  view.
		$this->load->view('feedback_create');
	}
	/**
		Function to send feedback  notification email to admin 
	**/
	function feedback_notification(){
		$email_address 	= 	$this->input->get_post('email_address', TRUE);
		$subject 		= 	$this->input->get_post('subject', TRUE);
		$message		=	$this->input->get_post('message', TRUE);
		$message .= ($this->session->userdata('member_id')!='')? "<br/><br/>Member-ID = ".$this->session->userdata('member_id').'['.$this->session->userdata('member_username').']':'';
		$feedback_info	=	array($email_address,$subject,$message);	
		
		$this->load->helper('notification');	// Load notification plugin
		create_notification("feedback",$feedback_info);
	}
}
?>