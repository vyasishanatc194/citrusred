<?php

class Contact extends CI_Controller
{
	function contact()
	{
		parent::__construct();
		#Load email library for sending mails
		$this->load->helper('notification');
		$this->load->model('SeoModel');
		force_ssl();	
	}

	function index(){
		$strImgPath=substr(FCPATH,0,strrpos(FCPATH,DIRECTORY_SEPARATOR));
		$strImgPath= $strImgPath.'/webappassets/captcha/';
		//echo $strImgPath=substr(FCPATH,0,strrpos(FCPATH,'/')).'/webappassets/captcha/'; 

		$cap = create_captcha(array('img_path' => $strImgPath, 'img_url'  => base_url().'webappassets/captcha/', 'img_width'     => 80, 'img_height' => 33));
		
		$data = array('word'=>$cap['word'], 'captcha'=>$cap['image']);
		 
		$seo_array=$this->SeoModel->get_seo_data(array('is_delete'=>0),true);
		if(isset($_POST)){
			$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
			$this->form_validation->set_rules('securityCode', 'Security Code', 'trim|required|xss_clean');
			$this->form_validation->set_rules('securityCode', 'Security Code', 'required|matches[word]');
			//$this->form_validation->set_rules('tickettype', 'Inquiry Reason', 'trim|required|xss_clean');
			
			$data['name']     			= $this->input->post('name');
			$data['phone']     			= $this->input->post('phone');
			$data['email']     			= $this->input->post('email');
			$data['desc']     			= $this->input->post('desc');
                                                    //$data['tickettype']     			= $this->input->post('tickettype');
                                                    
			if ( $this->form_validation->run() !== FALSE ){
				$data['msg1']		=	'Your message has been sent! Someone will get back to you shortly.';
				
				$message ='<table border="0" width="50%" cellpadding="0" cellspacing="1">';
				$message .='<tr><td width="50" align"left"><strong>Name</strong></td><td width="50" align"left">'.$data['name'].'</td></tr>';
				$message .='<tr><td width="50" align"left"><strong>Phone</strong></td><td width="50" align"left">'.$data['phone'].'</td></tr>';
                                                                     //$message .='<tr><td width="50" align"left"><strong>Inquiry Reason</strong></td><td width="50" align"left">'.$data['tickettype'].'</td></tr>';
				$message .='<tr><td width="50" align"left"><strong>Email</strong></td><td width="50" align"left">'.$data['email'].'</td></tr>';
				$message .='<tr><td width="50" align"left"><strong>Message</strong></td><td width="50" align"left">'.$data['desc'].'</td></tr>';
				$message .='</table>';

				$text_message = strip_tags($message);

				contact_mail(SYSTEM_EMAIL_FROM, $data['email']  ,$data['name']  , 'Contact Us | RedCappi',$message,$text_message); 
				 
				
				$data['name']     			= '';
				$data['phone']     			= '';
				$data['email']     			= '';
				$data['desc']     			= '';
			}
		
		}
		$this->load->view('header_outer',array('seo_array'=>$seo_array,'title'=>'Contact Us | RedCappi','show_bottom_bar'=>true));
		$this->load->view('contactus',$data);
		$this->load->view('footer_outer');
	}

	 	 
}

/* End of file */
?>
