<?php
/************Refer Friend class********************/
class Refer_friend extends CI_Controller
{
	/*

		Contructor for controller.

		It checks user session and redirects user if not logged in

	*/

	function __construct(){
        parent::__construct();
		if(!$this->is_authorized->check_user())
			redirect('user/index');
		if($this->session->userdata('member_id')=='')
			redirect('user/index');	
			
		$this->load->model('UserModel');
		$this->load->helper('transactional_notification');
	 }
	function index(){	
		$messages = '';
		if($this->input->post('submit')=='Send'){
			 
			$this->form_validation->set_rules('your_name', 'Name', 'required');
			$this->form_validation->set_rules('to', 'Email Address', 'required|valid_emails');
			$this->form_validation->set_rules('message', 'Message', 'required');
		
			if($this->form_validation->run()==true){				
				$user_info=array($this->input->post('your_name'),$this->input->post('message'));				 
				create_transactional_notification("refer_freind",$user_info,$this->input->post('to'));
				$messages = 'Mail has been Sent';	
				
				 $this->session->set_flashdata('message', $messages);
				redirect(current_url());
				exit;
			} 
		}
		 
		if($_SERVER['HTTP_REFERER']!=base_url()."refer_friend/index"){
			$this->session->set_userdata('HTTP_REFERER', $_SERVER['HTTP_REFERER']);
			$previous_page_url=$_SERVER['HTTP_REFERER'];
		}else{
			$previous_page_url=$this->session->userdata('HTTP_REFERER');
		}
		$send_message="I thought you might want to use RedCappi, and see just how easy it is to create and send amazing email campaigns in minutes!
		
		
Sign up free and check it out at http://www.".SYSTEM_DOMAIN_NAME;
		//Loads  Refer Friend  view.
		//Loads header, my account and footer view.
		$this->load->view('header',array('title'=>'Refer Friend','previous_page_url'=>$previous_page_url));
		$this->load->view('refer-friend',array('title'=>'Refer Friend','send_message'=>$send_message));		
		$this->load->view('footer-inner-red');
	}
}
?>