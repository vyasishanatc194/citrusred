<?php
class Blog_banned_words extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		//if($this->session->userdata('webmaster_id')=='')
		$this->load->helper('url');
		$this->load->database();
       
        $this->load->library('session');
	    session_start();
		if(!$_SESSION['admin_id'])
			redirect('webmaster/account/login');
			
		$this->load->helper(array('html_helper','form_helper'));
		// Load the Banned Words model which interact with database
		$this->load->model('webmaster/BlogBannedWords_Model');
		$this->load->library(array('form_validation','Pagination')); 
		$this->load->library('messages');
		
		# HTTPS/SSL enabled
		force_ssl();
	
		
	}
	
	function index()
	{
		$this->banned_words_list();
	}
	
	function banned_words_list($start=0)
	{
		//$fetch_conditions_array=array('ban_id'=>0,'listing_category_parent'=>0);
		$fetch_conditions_array=array();
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/blog_banned_words/banned_words_list';
		$config['total_rows']=$this->BlogBannedWords_Model->get_category_count($fetch_conditions_array);
		$config['per_page']=10;
		$config['uri_segment']=4;

		// Initialize paging with above parameters
		$this->pagination->initialize($config);
		
		//Create paging inks
		$paging_links=$this->pagination->create_links();
		
		$category_data_array=$this->BlogBannedWords_Model->get_listing_data($fetch_conditions_array,$config['per_page'],$start);
		
		// Recieve any messages to be shown, when category is added or updated
		$messages=$this->messages->get();
		
		//Loads header, category listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Banned Words'));
		$this->load->view('webmaster/blog_banned_words_list',array('category'=>$category_data_array,'paging_links'=>$paging_links,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	function banned_edit($ban_id)
	{
	
		$user_data_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('ban_word', 'ban_word', 'required');
			//Prepare category array from posted data
			$category_data = array(
				'ban_word' => $this->input->post('ban_word',true),
				'ban_id' => $this->input->post('ban_id',true),
			);	
			
			if($this->form_validation->run()) {
			
				$ban_id=$this->input->post('ban_id');
				
				//check Banned Words exists by loading category from database
				$category_exists=$this->BlogBannedWords_Model->get_category_data(array('ban_word'=>$this->input->post('ban_word',true),'ban_id'=>0,'ban_id !='=>$ban_id));
				
				//check category exists
				if(count($category_exists)) {
					$this->messages->add('Banned Words already exists', 'error');
					
				}
				else
				{
					// Update Banned Words in database
					$this->BlogBannedWords_Model->update_category($category_data,array('ban_id'=>$ban_id));				
					$this->messages->add('Banned Words updated successfully', 'success');
					redirect('webmaster/blog_banned_words/banned_words_list');
				}
			}
			
			$category_data_array=$category_data;
		}
		
		if(!count($category_data_array)) {
			$fetch_conditions_array=array('ban_id'=>$ban_id);
			$category_data_array=$this->BlogBannedWords_Model->get_category_data($fetch_conditions_array,$config['per_page']);
			$category_data_array=$category_data_array[0];
		}
		// Recieve any messages to be shown, when category is added or updated
		$messages=$this->messages->get();
		
		//Loads header, category edit  and footer view.
		
		$this->load->view('webmaster/header',array('title'=>'Update Category'));
		$this->load->view('webmaster/blog_bannedword_edit',array('category'=>$category_data_array,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	function banned_delete($ban_id)
	{
		// Deletes category according to Banned Words id
		$this->BlogBannedWords_Model->delete_category(array('ban_id'=>$ban_id));

		// Assign  success message by message class
		$this->messages->add('Banned Words deleted successfully', 'success');

		// Redirect to listing of categories
		redirect('webmaster/blog_banned_words/banned_words_list');
	}
	
	function banned_create($ban_id=0)
	{
		$user_data_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('ban_word', 'Banned Words', 'required');
			
			//Prepare member array from posted data
			$category_data = array(
				'ban_word' => $this->input->post('ban_word',true),
			);	
			
			if($this->form_validation->run()) {
			
				$ban_id=$this->input->post('ban_id');
				
				//check Banned Words exists by loading category from database
				$category_exists=$this->BlogBannedWords_Model->get_category_data(array('ban_word'=>$this->input->post('ban_word',true),'ban_id'=>0));
				
				
				//check category exists
				if(count($category_exists)) {
					$this->messages->add('Banned Words already exists', 'error');
					
				}
				else
				{
					//Insert New Banned Words In Database
					$this->BlogBannedWords_Model->create_category($category_data);	
					$this->messages->add('Banned Words created successfully', 'success');
					redirect('webmaster/blog_banned_words');
				}
			}
			
			$category_data_array=$category_data;
		}
		
		
		// Recieve any messages to be shown
		//$messages=$this->messages->get();
		
		if($ban_id){
		//$category_data_array['listing_category_parent']=$id;
		}
		
		//Loads header, category create  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Create Banned Words'));
		$this->load->view('webmaster/blog_bannedword_create',array('category'=>$category_data_array,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	


}
?>