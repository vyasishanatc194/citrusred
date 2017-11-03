<?php
class Blog_listing_post extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	  	// Load the listing category model which interact with database
		$this->load->model('webmaster/BlogPost_Model');
		$this->load->library(array('form_validation','Pagination')); 
		$this->load->library('messages');	
		# HTTPS/SSL enabled
		force_ssl();
			
	}
	
	/* function index()
	{
		$this->post_list();
	} */
	
	function post_list($cat_id,$start=0)
	{
		//$fetch_conditions_array=array('id'=>0,'listing_category_parent'=>0);
		$fetch_conditions_array=array('cat_id'=>$cat_id,'is_deleted'=>0,'post_archives'=>0,'status'=>1);
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'index.php/webmaster/blog_listing_post/post_list/'.$cat_id;
		$config['total_rows']=$this->BlogPost_Model->get_category_count($fetch_conditions_array);
		$config['per_page']=10;
		$config['uri_segment']=5;

		// Initialize paging with above parameters
		$this->pagination->initialize($config);

		//Create paging inks
		$paging_links=$this->pagination->create_links();

		$category_data_array=$this->BlogPost_Model->get_listing_data($fetch_conditions_array,$config['per_page'],$start);
		
		// Recieve any messages to be shown, when category is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, category listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Listing Post','logo_link'=>$logo_link));
		$this->load->view('webmaster/blog_post_list',array('category'=>$category_data_array,'paging_links'=>$paging_links,'messages' =>$messages,'cat_id'=>$cat_id,'status'=>1,'page'=>$start));
		$this->load->view('webmaster/footer');
	}
	
	
	function post_list_status($cat_id,$status,$start=0){
		//$fetch_conditions_array=array('id'=>0,'listing_category_parent'=>0);
		$fetch_conditions_array=array('cat_id'=>$cat_id,'is_deleted'=>0,'post_archives'=>0,'status'=>$status);
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'index.php/webmaster/blog_listing_post/post_list_status/'.$cat_id.'/'.$status;
		$config['total_rows']=$this->BlogPost_Model->get_category_count($fetch_conditions_array);
		$config['per_page']=10;
		$config['uri_segment']=6;

		// Initialize paging with above parameters
		$this->pagination->initialize($config);
		
		//Create paging inks
		$paging_links=$this->pagination->create_links();
		
		$category_data_array=$this->BlogPost_Model->get_listing_data($fetch_conditions_array,$config['per_page'],$start);
		
		// Recieve any messages to be shown, when category is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, category listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Listing Post','logo_link'=>$logo_link));
		$this->load->view('webmaster/blog_post_list',array('category'=>$category_data_array,'paging_links'=>$paging_links,'messages' =>$messages,'cat_id'=>$cat_id,'status'=>$status));
		$this->load->view('webmaster/footer');
	}

	function post_list_archive($cat_id,$archive,$start=0){
		//$fetch_conditions_array=array('id'=>0,'listing_category_parent'=>0);
		$fetch_conditions_array=array('cat_id'=>$cat_id,'is_deleted'=>0,'post_archives'=>1);
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'index.php/webmaster/blog_listing_post/post_list_archive/'.$cat_id.'/'.$archive;
		$config['total_rows']=$this->BlogPost_Model->get_category_count($fetch_conditions_array);
		$config['per_page']=10;
		$config['uri_segment']=6;

		// Initialize paging with above parameters
		$this->pagination->initialize($config);
		
		//Create paging inks
		$paging_links=$this->pagination->create_links();
		
		$category_data_array=$this->BlogPost_Model->get_listing_data($fetch_conditions_array,$config['per_page'],$start);
		// Recieve any messages to be shown, when category is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, category listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Listing Post','logo_link'=>$logo_link));
		$this->load->view('webmaster/blog_post_list',array('category'=>$category_data_array,'paging_links'=>$paging_links,'messages' =>$messages,'cat_id'=>$cat_id,'archive'=>1));
		$this->load->view('webmaster/footer');
	}
	
	function post_edit($cat_id,$id){
		$user_data_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit'){
			// Validation rules are applied
			$this->form_validation->set_rules('title', 'title', 'required');
			$this->form_validation->set_rules('desc', 'desc', 'required');
			
			//Prepare category array from posted data
			$category_data = array(
				'title' => $this->input->post('title',true),
				'desc' => $this->input->post('desc'),
				'meta_keywords' => $this->input->post('meta_keywords'),
				'meta_description' => $this->input->post('meta_description'),
				'id' => $this->input->post('id',true),
				'cat_id' => $this->input->post('cat_id',true),
				'status'=> $this->input->post('status',true),				
				'added_by'=> (''==$this->input->post('added_by',true))?0:$this->input->post('added_by',true)
				
			);	
			
			if($this->form_validation->run()) {
				$id=$this->input->post('id');
				
				//check Listing category exists by loading category from database
				$category_exists=$this->BlogPost_Model->get_category_data(array('title'=>$this->input->post('title',true),'id'=>0,'id !='=>$id));
				
				//check category exists
				if(count($category_exists)){
					$this->messages->add('Listing Category already exists', 'error');					
				}else{
					$category_data['desc']=str_replace("../../../../asset", base_url()."asset", $category_data['desc']);
					$category_data['desc']=str_replace("../../../asset", base_url()."asset", $category_data['desc']);
					// Update Listing Category in database
					$this->BlogPost_Model->update_category($category_data,array('id'=>$id));
					$this->messages->add('Listing Post updated successfully', 'success');
					redirect('webmaster/blog_listing_post/post_list/'.$cat_id);
				}
			}
			
			$category_data_array=$category_data;
		}
		
		if(!count($category_data_array)) {
			$fetch_conditions_array=array('id'=>$id);
			$category_data_array=$this->BlogPost_Model->get_category_data($fetch_conditions_array,$config['per_page']);
			$category_data_array=$category_data_array[0];
		}
		// Recieve any messages to be shown, when category is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, category edit  and footer view.		
		$this->load->view('webmaster/header',array('title'=>'Update Category','logo_link'=>$logo_link));
		$this->load->view('webmaster/blog_post_edit',array('category'=>$category_data_array,'messages' =>$messages,'cat_id'=>$cat_id));
		$this->load->view('webmaster/footer');
	}
	
	function mark_comment($post_id=0,$add_comment=0){
		// Update Listing Category in database
		$this->BlogPost_Model->update_category(array('add_comment'=>$add_comment),array('id'=>$post_id));
	}
	function post_delete($cat_id,$id){
		// Deletes category according to listing category ID
		$this->BlogPost_Model->update_category(array('is_deleted'=>1),array('id'=>$id));

		// Assign  success message by message class
		$this->messages->add('Listing Post deleted successfully', 'success');

		// Redirect to listing of categories
		redirect('webmaster/blog_listing_post/post_list/'.$cat_id);
	}
	function mark_archive($cat_id,$id){
		// Deletes category according to listing category ID
		$input_array = array('post_archives'=>1);
		$this->BlogPost_Model->update_archive($input_array , array('id'=>$id));

		// Assign  success message by message class
		$this->messages->add('Post Archived successfully', 'success');

		// Redirect to listing of categories
		redirect('webmaster/blog_listing_post/post_list/'.$cat_id);
	}
	
	function mark_restore($cat_id,$id){
		// Deletes category according to listing category ID
		$input_array = array('post_archives'=>0);
		$this->BlogPost_Model->update_archive($input_array , array('id'=>$id));

		// Assign  success message by message class
		$this->messages->add('Post Restore successfully', 'success');

		// Redirect to listing of categories
		redirect('webmaster/blog_listing_post/post_list_archive/'.$cat_id.'/1');
	}
	
	function mark_active($cat_id,$id){
		// Deletes category according to listing category ID
		$input_array = array('status'=>1);
		$this->BlogPost_Model->update_archive($input_array , array('id'=>$id));

		// Assign  success message by message class
		$this->messages->add('Post Status Updated successfully', 'success');

		// Redirect to listing of categories
		redirect('webmaster/blog_listing_post/post_list_status/'.$cat_id.'/0');
	}
	
	function mark_inactive($cat_id,$id){
		// Deletes category according to listing category ID
		$input_array = array('status'=>0);
		$this->BlogPost_Model->update_archive($input_array , array('id'=>$id));

		// Assign  success message by message class
		$this->messages->add('Post Status Updated successfully', 'success');

		// Redirect to listing of categories
		redirect('webmaster/blog_listing_post/post_list/'.$cat_id.'/');
	}
	
	function post_create($cat_id,$id=0){
		$user_data_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('title', 'Title', 'required');
			$this->form_validation->set_rules('desc', 'Description', 'required');
			
			//Prepare member array from posted data
			$category_data = array(
				'title' => $this->input->post('title',true),
				'desc' => $this->input->post('desc'),
				'meta_keywords' => $this->input->post('meta_keywords'),
				'meta_description' => $this->input->post('meta_description'),
				'status' => $this->input->post('status',true),
				'cat_id' => $this->input->post('cat_id',true),
				'id' => (''==$this->input->post('id',true))?NULL:$this->input->post('id',true),
				'added_on'=> $this->input->post('added_on',true),
				'added_by'=> (''==$this->input->post('added_by',true))?0:$this->input->post('added_by',true)
			);
			if($this->form_validation->run()) {				
				$id=$this->input->post('id');		
				$category_data['desc']=str_replace("../../../../asset", base_url()."asset", $category_data['desc']);
				$category_data['desc']=str_replace("../../../asset", base_url()."asset", $category_data['desc']);
				
				//Insert New Listing Category In Database
				$this->BlogPost_Model->create_category($category_data);	
				$this->messages->add('Listing Post created successfully', 'success');
				redirect('webmaster/blog_listing_post/post_list/'.$cat_id);
				exit;
			}			
			$category_data_array=$category_data;
		}
		
		
		// Recieve any messages to be shown
		//$messages=$this->messages->get();

		$logo_link="webmaster/dashboard_stat";
		//Loads header, category create  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Create Listing Category','logo_link'=>$logo_link));
		$this->load->view('webmaster/blog_post_create',array('category'=>$category_data_array,'messages' =>$messages,'cat_id'=>$cat_id));
		$this->load->view('webmaster/footer');
	}
}
?>