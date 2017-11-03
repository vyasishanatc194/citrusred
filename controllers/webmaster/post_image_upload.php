<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Post_Image_Upload extends CI_Controller {
    
    	function __construct()
	{
		parent::__construct();
 
			// Load the listing category model which interact with database
		$this->load->model('webmaster/BlogPost_Model');
		$this->load->model('webmaster/BlogSetting_Model');
		$category_data_array1=$this->BlogSetting_Model->get_category_data();		
		$this->load->library(array('form_validation','Pagination')); 
		$this->load->library('messages');
		
		# HTTPS/SSL enabled
		force_ssl();

		
	}	
	
	function listing($post_id)
	{
		//$fetch_conditions_array=array('id'=>0,'listing_category_parent'=>0);
		$fetch_conditions_array=array('post_id'=>$post_id);
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/post_image_upload/listing/'.$post_id;
		$config['total_rows']=$this->BlogPost_Model->get_post_images_count($fetch_conditions_array);
		$config['per_page']=10;
		$config['uri_segment']=4;

		// Initialize paging with above parameters
		$this->pagination->initialize($config);
		
		//Create paging inks
		$paging_links=$this->pagination->create_links();
		
		$category_data_array=$this->BlogPost_Model->get_post_images_data($fetch_conditions_array,$config['per_page'],$start);
		
		// Recieve any messages to be shown, when category is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, category listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Post Image Listing','logo_link'=>$logo_link));
		$this->load->view('webmaster/post_image_list',array('category'=>$category_data_array,'paging_links'=>$paging_links,'messages' =>$messages,'post_id'=>$post_id));
		$this->load->view('webmaster/footer');
	}
	
    function do_upload()
    {
		$upload_path= $this->config->item('blog_files');
		 
		
		
        $config['upload_path'] = $upload_path; // server directory
		
        $config['allowed_types'] = 'gif|jpg|png|jpeg'; // by extension, will check for whether it is an image
		
        $config['max_size']    = '1000'; // in kb

        $this->load->library('upload', $config);
       // $this->load->library('Multi_upload');
		
		$this->upload->initialize($config);
		
        //$files = $this->multi_upload->go_upload();
		$files = $this->upload->do_upload();
        
        
        if ( ! $files )        
        { 
			$category_data = array(
				'post_id' => $this->input->post('post_id',true),
			);	
            $error = array('error' => $this->upload->display_errors());
			$this->load->view('webmaster/header',array('title'=>'Image Upload'));
           $this->load->view('webmaster/post_image_upload', array('error' => $this->upload->display_errors(),'post_id'=>$category_data['post_id']));
		
			$this->load->view('webmaster/footer');
			
			//$this->messages->add('Files Uploaded Successfully.', 'success');
        }    
        else
        {
		
            //$data = array('upload_data' => $files);
			$data = array('upload_data' => $this->upload->data());
			/* print_r($data);
			die(); */
			
		if(BLOG_IMAGE_WIDTH != 0 || BLOG_IMAGE_HEIGHT != 0){
		$this->load->library('image_lib');
		$config['image_library'] = 'gd2';
		$config['source_image'] = $upload_path.$data['upload_data']['file_name'];
		$config['create_thumb'] = TRUE;
		$config['maintain_ratio'] = TRUE;
		$config['width'] = BLOG_IMAGE_WIDTH;
		$config['height'] = BLOG_IMAGE_HEIGHT;
		$this->load->library('image_lib', $config); 
		$this->image_lib->initialize($config);
		$this->image_lib->resize();
		if ( ! $this->image_lib->resize())
		{
		echo $upload_path;
			echo $this->image_lib->display_errors();
			die();
		}
		}
			$category_data = array(
				'img_name' => $data['upload_data']['file_name'],
				'post_id' => $this->input->post('post_id',true),
				'img_status'=> $this->input->post('img_status',true),
				'img_default'=> $this->input->post('img_default',true),
			);	
			$category_data_array=$this->BlogPost_Model->create_image_listing($category_data);

			redirect('webmaster/post_image_upload/listing/'.$category_data['post_id']);
			
			$this->load->view('webmaster/footer');
				
			
        }
		$messages=$this->messages->get();
    }  
	

	
	function image_create($post_id,$id=0)
	{

		$user_data_array=array();
		// Recieve any messages to be shown
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, category create  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Image Upload','logo_link'=>$logo_link));
		$this->load->view('webmaster/post_image_upload',array('category'=>$category_data_array,'messages' =>$messages,'post_id'=>$post_id));
		$this->load->view('webmaster/footer');

	}
	
	
	function image_delete($post_id,$img_id)
	{
		// Deletes category according to listing category ID
		$this->BlogPost_Model->delete_image(array('post_id'=>$post_id,'img_id'=>$img_id));

		// Assign  success message by message class
		$this->messages->add('Image deleted successfully', 'success');

		// Redirect to listing of categories
		redirect('webmaster/post_image_upload/listing/'.$post_id);
	}
	
	
}
?>