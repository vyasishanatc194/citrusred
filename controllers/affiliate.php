<?php
class Affiliate extends CI_Controller
{
	function __construct()
	{
		parent::__construct();	
		force_ssl();		
	}
	function index(){
		$this->load->model('SeoModel');
		$seo_array=$this->SeoModel->get_page('12');		
		//Loads header, About us and footer view.
		$this->load->view('header_outer',array('seo_array'=>$seo_array,'title'=>'Affiliate','show_bottom_bar'=>true));
		$this->load->view('affiliate');
		$this->load->view('footer_outer');		
	}
}
?>
