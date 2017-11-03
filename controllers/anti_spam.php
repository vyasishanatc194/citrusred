<?php
/************Privacy class********************/
class Anti_spam extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		force_ssl();			
	}
	function index()
	{
		// Load the seo model which interact with database
		$this->load->model('SeoModel');
		$seo_array=$this->SeoModel->get_seo_data(array('is_delete'=>0),true);
		//Loads header, Anti Policy and footer view.
		$this->load->view('header_outer',array('seo_array'=>$seo_array,'title'=>'Anti Policy','show_bottom_bar'=>true));
		$this->load->view('anti_policy');
		$this->load->view('footer_outer');		
	}
}
?>
