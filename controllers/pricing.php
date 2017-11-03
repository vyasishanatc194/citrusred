<?php
/**
* A Pricing class
*
* This class is for redcappi Pricing list
*
* @version 1.0
* @author Pravin Jha <pravinjha@gmail.com>
* @project Redcappi
*/
class Pricing extends CI_Controller
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
		if($this->session->userdata('member_id')!='') {
			redirect('upgrade_package_cim/index');
		}
		$current_url=current_url();	#Get Current url
		
		// Load the seo model which interact with database
		$this->load->model('SeoModel');
		
		$seo_array=$this->SeoModel->get_seo_data(array('is_delete'=>0),true);
		// Load the user model which interact with database
		$this->load->model('UserModel');
		// Fetch user data from database
		
		$packages=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1,'package_recurring_interval'=>'months','is_special'=>0,'package_deleted'=>0),16);
		$packages_yearly=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1,'package_recurring_interval'=>'years','is_special'=>0,'package_deleted'=>0),16);		
		
		// Recieve any messages to be shown, when package is added or updated
		$messages=$this->messages->get();
		$wufoo_url = 'zj94f0r0wc297a';
		//Load the header, register and footer view of index page
		$this->load->view('header_outer',array('seo_array'=>$seo_array,'title'=>'Pricing: Email Marketing', 'wufoo_url'=>$wufoo_url));
		$this->load->view('pricing',array('packages'=>$packages,'packages_yearly'=>$packages_yearly));
		$this->load->view('footer_outer');
		
	}
	function website_builder()
	{
		// Load the user model which interact with database
		$this->load->model('UserModel');
		// Fetch user data from database
		$packages_count=$this->UserModel->get_packages_count(array('package_deleted'=>0,'package_status'=>1,'package_type'=>'sitebuilder'));
		$packages=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1,'package_type'=>'sitebuilder'),$packages_count);
		
		// Recieve any messages to be shown, when package is added or updated
		$messages=$this->messages->get();
		//Load the header, register and footer view of index page
		$this->load->view('header_outer',array('title'=>'Pricing: Website Builder'));
		$this->load->view('pricing-website-builder',array('packages'=>$packages));
		$this->load->view('footer_outer');
		
	}
	function full_access()
	{
		// Load the user model which interact with database
		$this->load->model('UserModel');
		// Fetch user data from database
		$packages_count=$this->UserModel->get_packages_count(array('package_deleted'=>0,'package_status'=>1,'package_type'=>'combo'));
		$packages=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1,'package_type'=>'combo'),$packages_count);		
		
		// Recieve any messages to be shown, when package is added or updated
		$messages=$this->messages->get();

		//Load the header, register and footer view of index page
		$this->load->view('header',array('title'=>'Pricing: Website Builder'));
		$this->load->view('pricing-full-access',array('packages'=>$packages));
		$this->load->view('footer');
	}
	function pricing(){
		//Load the header, pricing and footer view 
		$this->load->view('header',array('title'=>'Pricing Detail'));
		$this->load->view('pricing-detail');
		$this->load->view('footer');
	}	
}	
?>