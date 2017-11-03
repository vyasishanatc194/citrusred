<?php
class Blog_listing_category extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		//if($this->session->userdata('webmaster_id')=='')
			    $this->load->helper('url');
		$this->load->database();
       
        $this->load->library('session');
	   /*  session_start();
		if(!$_SESSION['admin_id'])
			redirect('webmaster/account/login'); */
			$this->load->helper(array('html_helper','form_helper'));
			// Load the listing category model which interact with database
		$this->load->model('webmaster/BlogCategory_Model');
		$this->load->library(array('form_validation','Pagination')); 
		$this->load->library('messages');
		
		# HTTPS/SSL enabled
		force_ssl();
	
		
	}
	
	function index()
	{
		$this->category_list();
	}
	
	function category_list($start=0)
	{
		//$fetch_conditions_array=array('id'=>0,'listing_category_parent'=>0);
		$fetch_conditions_array=array('is_deleted'=>0);
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/blog_listing_category/category_list';
		$config['total_rows']=$this->BlogCategory_Model->get_category_count($fetch_conditions_array);
		$config['per_page']=10;
		$config['uri_segment']=4;

		// Initialize paging with above parameters
		$this->pagination->initialize($config);
		
		//Create paging inks
		$paging_links=$this->pagination->create_links();
		
		$category_data_array=$this->BlogCategory_Model->get_listing_data($fetch_conditions_array,$config['per_page'],$start);
		
		// Recieve any messages to be shown, when category is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, category listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Listing Category','logo_link'=>$logo_link));
		$this->load->view('webmaster/blog_category_list',array('category'=>$category_data_array,'paging_links'=>$paging_links,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	function category_edit($id)
	{
	
		$user_data_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('category_name', 'category_name', 'required');
			//Prepare category array from posted data
			$category_data = array(
				'category_name' => $this->input->post('category_name',true),
				'id' => $this->input->post('id',true),
				'category_status'=> $this->input->post('category_status',true),
			);	
			
			if($this->form_validation->run()) {
			
				$id=$this->input->post('id');
				
				//check Listing category exists by loading category from database
				$category_exists=$this->BlogCategory_Model->get_category_data(array('category_name'=>$this->input->post('category_name',true),'id'=>0,'id !='=>$id));
				
				//check category exists
				if(count($category_exists)) {
					$this->messages->add('Listing Category already exists', 'error');
					
				}
				else
				{
					// Update Listing Category in database
					$this->BlogCategory_Model->update_category($category_data,array('id'=>$id));				
					$this->messages->add('Listing category updated successfully', 'success');
					redirect('webmaster/blog_listing_category/category_list');
				}
			}
			
			$category_data_array=$category_data;
		}
		
		if(!count($category_data_array)) {
			$fetch_conditions_array=array('id'=>$id);
			$category_data_array=$this->BlogCategory_Model->get_category_data($fetch_conditions_array,$config['per_page']);
			$category_data_array=$category_data_array[0];
		}
		// Recieve any messages to be shown, when category is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, category edit  and footer view.		
		$this->load->view('webmaster/header',array('title'=>'Update Category','logo_link'=>$logo_link));
		$this->load->view('webmaster/blog_category_edit',array('category'=>$category_data_array,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	function category_delete($id)
	{
		// Deletes category according to listing category ID
		$this->BlogCategory_Model->update_category(array('is_deleted'=>1),array('id'=>$id));
		
		#####################################
		# Delete Post of deleted Category   #
		#####################################
		// Load the listing category model which interact with database
		$this->load->model('webmaster/BlogPost_Model');
		// Deletes category according to listing category ID
		$this->BlogPost_Model->update_category(array('is_deleted'=>1),array('cat_id'=>$id));
		
		// Assign  success message by message class
		$this->messages->add('Listing category deleted successfully', 'success');

		// Redirect to listing of categories
		redirect('webmaster/blog_listing_category/category_list');
	}
	
	function category_create($id=0)
	{
		$user_data_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('category_name', 'Category Name', 'required');
			
			//Prepare member array from posted data
			$category_data = array(
				'category_name' => $this->input->post('category_name',true),
				'category_status' => $this->input->post('category_status',true),
			);	
			
			if($this->form_validation->run()) {
			
				
				
				$id=$this->input->post('id');
				
				//check Listing category exists by loading category from database
				$category_exists=$this->BlogCategory_Model->get_category_data(array('category_name'=>$this->input->post('category_name',true),'id'=>0));
				
				
				//check category exists
				if(count($category_exists)) {
					$this->messages->add('Listing Category already exists', 'error');
					
				}
				else
				{
					//Insert New Listing Category In Database
					$this->BlogCategory_Model->create_category($category_data);	
					$this->messages->add('Listing category created successfully', 'success');
					redirect('webmaster/blog_listing_category/category_list');
				}
			}
			
			$category_data_array=$category_data;
		}
		
		
		// Recieve any messages to be shown
		//$messages=$this->messages->get();
		
		if($id){
		//$category_data_array['listing_category_parent']=$id;
		}
		$logo_link="webmaster/dashboard_stat";
		//Loads header, category create  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Create Listing Category','logo_link'=>$logo_link));
		$this->load->view('webmaster/blog_category_create',array('category'=>$category_data_array,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
}
?>