<?php
class Blog_settings extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		//if($this->session->userdata('webmaster_id')=='')
			    $this->load->helper('url');
		$this->load->database();
       
        $this->load->library('session');
	    /* session_start();
		if(!$_SESSION['admin_id'])
			redirect('webmaster/account/login'); */
			$this->load->helper(array('html_helper','form_helper'));
		$this->load->model('webmaster/BlogSetting_Model');
		$this->load->library('form_validation'); 
		$this->load->library('messages');
		# HTTPS/SSL enabled
		force_ssl();

	}
	
	function index()
	{
		$this->blog_settings_edit();
	}
	

	function blog_settings_edit()
	{	
		$user_data_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('blog_gallery_type', 'blog_gallery_type', 'required');
			$this->form_validation->set_rules('blog_image_can_post', 'blog_image_can_post', 'required');
			$this->form_validation->set_rules('blog_front_page_post_display', 'blog_front_page_post_display', 'required');
			$this->form_validation->set_rules('blog_visitors_can_comment_post', 'blog_visitors_can_comment_post', 'required');
			$this->form_validation->set_rules('blog_summary_post', 'blog_visitors_can_comment_post', 'required');
			 $this->form_validation->set_rules('blog_image_width', 'blog_image_width', 'required');
			$this->form_validation->set_rules('blog_image_height', 'blog_image_height', 'required'); 

			
			//Prepare category array from posted data
			$category_data = array(
				'blog_gallery_type' => $this->input->post('blog_gallery_type',true),
				'blog_image_can_post'=> $this->input->post('blog_image_can_post',true),
				'blog_front_page_post_display'=> $this->input->post('blog_front_page_post_display',true),
				'blog_visitors_can_comment_post'=> $this->input->post('blog_visitors_can_comment_post',true),
				'blog_summary_post'=> $this->input->post('blog_summary_post',true),
				'blog_image_width'=> $this->input->post('blog_image_width',true),
				'blog_image_height'=> $this->input->post('blog_image_height',true),
			);	
			
			if($this->form_validation->run()) {
			
					// Update Listing Category in database
					foreach($category_data as $key=>$val ){
					$val = addslashes($val); 
					 mysql_query("update red_blog_tblconfigurations set config_value = $val  WHERE config_name='$key'");
					 }
					redirect('webmaster/blog_settings/blog_settings_edit');
				}else{
				//$category_data_array=$this->BlogSetting_Model->get_category_data();
				 foreach($category_data as $key=>$val )
				define(strtoupper($key), $val); 
				
				}
			
				
			$category_data_array=$category_data;
		}
		
		if(!count($category_data_array)) {
			$category_data_array=$this->BlogSetting_Model->get_category_data();
			$category_data_array=$category_data_array[0];
		}
		// Recieve any messages to be shown, when category is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, category edit  and footer view.		
		$this->load->view('webmaster/header',array('title'=>'Update Category','logo_link'=>$logo_link));
		$this->load->view('webmaster/blog_settings_edit',array('category'=>$category_data_array,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	
}
?>