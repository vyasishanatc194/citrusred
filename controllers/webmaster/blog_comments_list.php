<?php
class Blog_comments_list extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	  	// Load the listing category model which interact with database
		$this->load->model('blog/Category_Model');
		$this->load->library(array('form_validation','Pagination')); 
		$this->load->library('messages');
		
		# HTTPS/SSL enabled
		force_ssl();
	
	}
	
	function display($entry_id=0,$start=0)
	{
		$fetch_conditions_array=array('entry_id'=>$entry_id,'is_delete'=>0);
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'index.php/webmaster/blog_comments_list/display/'.$entry_id;
		$config['total_rows']=$this->Category_Model->get_comment_count($fetch_conditions_array);
		$config['per_page']=10;
		$config['uri_segment']=5;

		// Initialize paging with above parameters
		$this->pagination->initialize($config);

		//Create paging inks
		$paging_links=$this->pagination->create_links();

		$comment_data_array=$this->Category_Model->get_comment_listing($fetch_conditions_array,$config['per_page'],$start);
		
		// Recieve any messages to be shown
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, commnets listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Commnets ','logo_link'=>$logo_link));
		$this->load->view('webmaster/blog_comment_list',array('comments'=>$comment_data_array,'paging_links'=>$paging_links,'messages' =>$messages,'entry_id'=>$entry_id,'page'=>$start));
		$this->load->view('webmaster/footer');
	}
	
	function delete($entry_id=0,$id=0)
	{
		// Deletes comment according to comment ID
		$this->Category_Model->update_comment(array('is_delete'=>1),array('id'=>$id));

		// Assign  success message by message class
		$this->messages->add('Comment deleted successfully', 'success');

		// Redirect to listing of categories
		redirect('webmaster/blog_comments_list/display/'.$entry_id);
	}
	
	function mark_archive($cat_id,$id)
	{
		// Deletes category according to listing category ID
		$input_array = array('post_archives'=>1);
		$this->BlogPost_Model->update_archive($input_array , array('id'=>$id));

		// Assign  success message by message class
		$this->messages->add('Post Archived successfully', 'success');

		// Redirect to listing of categories
		redirect('webmaster/blog_listing_post/post_list/'.$cat_id);
	}
	
	function mark_restore($cat_id,$id)
	{
		// Deletes category according to listing category ID
		$input_array = array('post_archives'=>0);
		$this->BlogPost_Model->update_archive($input_array , array('id'=>$id));

		// Assign  success message by message class
		$this->messages->add('Post Restore successfully', 'success');

		// Redirect to listing of categories
		redirect('webmaster/blog_listing_post/post_list_archive/'.$cat_id.'/1');
	}
	
	function mark_active($cat_id,$id)
	{
		// Deletes category according to listing category ID
		$input_array = array('status'=>1);
		$this->BlogPost_Model->update_archive($input_array , array('id'=>$id));

		// Assign  success message by message class
		$this->messages->add('Post Status Updated successfully', 'success');

		// Redirect to listing of categories
		redirect('webmaster/blog_listing_post/post_list_status/'.$cat_id.'/0');
	}
	
	function mark_inactive($cat_id,$id)
	{
		// Deletes category according to listing category ID
		$input_array = array('status'=>0);
		$this->BlogPost_Model->update_archive($input_array , array('id'=>$id));

		// Assign  success message by message class
		$this->messages->add('Post Status Updated successfully', 'success');

		// Redirect to listing of categories
		redirect('webmaster/blog_listing_post/post_list/'.$cat_id.'/');
	}
	
	function post_create($cat_id,$id=0)
	{
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
				'desc' => $this->input->post('desc',true),
				'status' => $this->input->post('status',true),
				'cat_id' => $this->input->post('cat_id',true),
				'id' => $this->input->post('id',true),
				'added_on'=> $this->input->post('added_on',true),
				'added_by'=> $this->input->post('added_by',true),
			);	
			
			if($this->form_validation->run()) {
			
				
				
				$id=$this->input->post('id');
				
				//check Listing category exists by loading category from database
				$category_exists=$this->BlogPost_Model->get_category_data(array('title'=>$this->input->post('title',true),'desc'=>$this->input->post('desc',true),'id'=>0));
				
				
				//check category exists
				if(count($category_exists)) {
					$this->messages->add('Listing Post already exists', 'error');
					
				}
				else
				{
					//Insert New Listing Category In Database
					$this->BlogPost_Model->create_category($category_data);	
					$this->messages->add('Listing Post created successfully', 'success');
					redirect('webmaster/blog_listing_post/post_list/'.$cat_id);
				}
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