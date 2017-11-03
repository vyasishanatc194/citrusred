<?php
class RC_api extends CI_Controller
{
	function __construct(){
        parent::__construct();
		force_ssl();	 	
	}
	function index()
	{
		$current_url=current_url();	#Get Current url
		
		// Load the seo model which interact with database
		$this->load->model('SeoModel');
		
		$seo_array=$this->SeoModel->get_seo_data(array('is_delete'=>0),true);

		//Load the header, register and footer view of index page
		$this->load->view('header_outer',array('seo_array'=>$seo_array,'title'=>'API - RedCappi Email Marketing','show_bottom_bar'=>true));
		$this->load->view('api');
		$this->load->view('footer_outer');
		
	}
	 
}
?>