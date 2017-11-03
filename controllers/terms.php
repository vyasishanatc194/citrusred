<?php
/************Terms & Condition class********************/
class Terms extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if(($_SERVER["SERVER_NAME"]=="red7.me")||($_SERVER["SERVER_NAME"]=="www.red7.me")){
			redirect(base_url());
		}	
		force_ssl();		
	}
	function index()
	{
		// Load the seo model which interact with database
		$this->load->model('SeoModel');
		$seo_array=$this->SeoModel->get_seo_data(array('is_delete'=>0),true);
		//Loads header, Terms Condition and footer view.
		$this->load->view('header_outer',array('seo_array'=>$seo_array,'title'=>'Terms Condition','show_bottom_bar'=>true));
		$this->load->view('terms-condition');
		$this->load->view('footer_outer');		
	}
}
?>
