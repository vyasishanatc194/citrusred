<?php
/**
* A Support class
*
* This class is for redcappi support
*
* @version 1.0
* @author Pravin Jha <pravinjha@gmail.com>
* @project Redcappi
*/
class Support extends CI_Controller
{

	function __construct(){
        parent::__construct();
		force_ssl();		
	}
	/**
		Function index to display listing of product links and categories
		@param int category_id: category id
	*/
	function index($category_id=0){
		# Load the user model which interact with database
		$this->load->model('supportModel');
		# get Category
		$support_data=$this->supportModel->get_category_data(array('is_delete'=>0,'is_active'=>1));
		if($category_id<=0){
			$category_id=$support_data[0]['id'];
		}
		# get product
		$product_data=$this->supportModel->get_category_productdata(array('rsp.is_delete'=>0,'rsp.is_active'=>1,'rsp.category_id'=>$category_id));	
		#Loads  Support view
		$this->load->view('header_outer',array('seo_array'=>$seo_array,'title'=>'Support-'.$product_data[0]['category']));		
		$this->load->view('support',array('support_data'=>$support_data,'product_data'=>$product_data));
		$this->load->view('footer_outer');
		force_ssl();	
	}
	/**
		Function detail to display description of products
		@param string title: product title
		@param int product_id: product id
	*/
	function detail($title="",$product_id=0){
		# Load the user model which interact with database
		$this->load->model('supportModel');	
		 
		 
		# get product
		$product_data=$this->supportModel->get_category_productdata(array('rsp.is_delete'=>0,'rsp.is_active'=>1,'rsp.id'=>$product_id));
		
		$seo_array[0]['title'] = substr($product_data[0]['product'],0,255); 
		
		#Loads  Support view
		$this->load->view('header_outer',array('seo_array'=>$seo_array,'title'=>'Support'));		
		$this->load->view('support_detail',array('product_data'=>$product_data[0]));
		$this->load->view('footer_outer');		
	}
	function result($start=0){
		# Load the user model which interact with database
		$this->load->model('supportModel');
		#########################
		#Set pagination setting	#
		#########################
		$config['base_url']			=	base_url().'support/result';
		$config['total_rows']		=	$this->supportModel->count_search_product_result(array('rsp.is_delete'=>0,'rsp.is_active'=>1));
		$config['per_page']			=	10;	// Max number of items you want shown per page
		$config['uri_segment']		=	3;
		$config['num_links']		=	4;	// Number of "digit" links to show before/after the currently viewed page
		$config['full_tag_open']	= 	'<ul class="pagination">';
		$config['full_tag_close'] 	= 	'</ul>';
		$config['cur_tag_open'] 	= 	'<li><a class="selected">';
		$config['cur_tag_close'] 	= 	'</a></li>';
		$config['first_tag_open'] 	= 	'<li>';
		$config['first_tag_close'] 	= 	'</li>';
		$config['last_tag_open'] 	= 	'<li>';
		$config['last_tag_close'] 	= 	'</li>';
		$config['num_tag_open'] 	= 	'<li>';
		$config['num_tag_close'] 	= 	'</li>';
		$config['next_tag_open'] 	= 	'<li>';
		$config['next_tag_close'] 	= 	'</li>';
		$config['prev_tag_open'] 	=	'<li>';
		$config['prev_tag_close'] 	= 	'</li>';
		
		// Initialize paging with above config parameters
		$this->pagination->initialize($config);
		# get search product
		$product_data=$this->supportModel->search_product_result(array('rsp.is_delete'=>0,'rsp.is_active'=>1),$config['per_page'],$start);
		$paging_links=$this->pagination->create_links();
		#Loads  Support view
		$this->load->view('header_outer',array('seo_array'=>$seo_array,'title'=>'Support'));		
		$this->load->view('support_search_result',array('product_data'=>$product_data,'paging_links'=>$paging_links,'search_text'=>$this->input->post('search_text')));
		$this->load->view('footer_outer');	
	}
}
?>
