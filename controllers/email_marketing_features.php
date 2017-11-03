<?php
class Email_marketing_features extends CI_Controller
{
	function __construct(){
        parent::__construct();
		if(($_SERVER["SERVER_NAME"]=="red7.me")||($_SERVER["SERVER_NAME"]=="www.red7.me")){
			redirect(base_url());
		}
		force_ssl();			
	}
	function index()
	{
		$current_url=current_url();	#Get Current url
		
		// Load the seo model which interact with database
		$this->load->model('SeoModel');
		
		$seo_array=$this->SeoModel->get_seo_data(array('is_delete'=>0),true);
		$wufoo_url = 'm1oc9fk01wcle87';
		//Load the header, register and footer view of index page
		$this->load->view('header_outer',array('seo_array'=>$seo_array,'title'=>'Feature Marketing','show_bottom_bar'=>true, 'wufoo_url'=>$wufoo_url));
		$this->load->view('feature-marketing');
		$this->load->view('footer_outer');
		
	}
	 
}
?>