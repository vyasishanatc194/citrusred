<?php
/**
* A Blog_Comment class
*
* This class is for blog comments
*
* @version 1.0
* @author Pravin Jha <pravinjha@gmail.com>
* @project Redcappi
*/
class Blog_Comment extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('url');       
        $this->load->library('session');	   
		$this->load->helper(array('html_helper','form_helper'));
		# Load the listing category model which interact with database
		$this->load->model('blog/Category_Model');
		$category=$this->display_category();
		$this->load->library(array('form_validation','Pagination')); 
		$this->load->library('messages');
		if(($_SERVER["SERVER_NAME"]=="red7.me")||($_SERVER["SERVER_NAME"]=="www.red7.me")){
			redirect(base_url());
		}
	}
	
	/**
		Function display_category to display category
	*/
	function display_category(){
		# Load category model class which handles database interaction
		$this->load->model('blog/Category_Model');
		$fetch_condiotions_array=array(
			'is_deleted'=>0,'category_status'=>1,
		);
		# Define config parameters for paging like base url, total rows and record per page.
		$config['total_rows']=$this->Category_Model->get_category_count($fetch_condiotions_array);
		$category['category']=$this->Category_Model->get_category_data($fetch_condiotions_array);
		return $category;
	}
	/**
		Function comment to display comments
	*/
	function comment($post_id,$start=0){
		/**
			Genrate captcha image
		*/
		$strImgPath=substr(FCPATH,0,strrpos(FCPATH,DIRECTORY_SEPARATOR));
		$strImgPath= $strImgPath.'/webappassets/captcha/';
		$captcha_defaults = array(
			'img_path' => $strImgPath,
			'img_url'  => base_url().'webappassets/captcha/',
			'img_width'     => 90,
			'img_height' => 33
		);
		$cap 						= create_captcha($captcha_defaults);
		$data['word']   			= $cap['word'];
		$data['captcha'] 			= $cap['image'];
		$data['name']    			= "";
		$data['phone']   			= "";
		$data['email']   			= "";
		$data['desc']    			= "";
		$data['securityCode']     	= "";
		$fetch_condiotions_array=array(
		'is_deleted'=>0,'id'=>$post_id,
		);
		$blog_article['blog_article']=$this->Category_Model->get_article_detail($fetch_condiotions_array);
		
		# Load log configuration model class which handles database interaction
		$this->load->model('ConfigurationModel');
		## Fetch maximum comment close days
		$config_arr=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'comment_close_days'));
		$comment_close_days=$config_arr[0]['config_value'];
		$today = date('Y-m-d');	//Current date
		//$fetch_conditions_array=array('id'=>0,'listing_category_parent'=>0);
		$fetch_conditions_array=array('entry_id'=>$post_id,'is_delete'=>0,'DATEDIFF(\''.$today.'\',added_on) < '=>$comment_close_days);
		// Define config parameters for paging like base url, total rows and record per page.
		$this->load->library('pagination');
		$config['base_url']=base_url().'index.php/blog_comment/comment/'.$post_id;
		$config['total_rows']=$this->Category_Model->get_comment_count($fetch_conditions_array);
		$blog_article['blog_article'][0]['post_comment']=$config['total_rows'];
		$this->Category_Model->get_blog_setting_data();
		if(BLOG_FRONT_PAGE_POST_DISPLAY!=""){
			$rows_per_page = BLOG_FRONT_PAGE_POST_DISPLAY;
			$config['per_page'] = BLOG_FRONT_PAGE_POST_DISPLAY;
			$config['uri_segment'] = 4;
		}else{
			$config['per_page']=10;
		}
		$comment_data_array=$this->Category_Model->get_comment_listing($fetch_conditions_array,$config['per_page'],$start);
		$i=0;
		foreach($comment_data_array as $comment_data){
			$fetch_conditions_array=array(
				'is_delete'=>0,'post_id'=>$comment_data['id'],
			);
			$reply_data_array=$this->Category_Model->get_reply_listing($fetch_conditions_array);			
			$comment_data_array[$i]['reply']=$reply_data_array;
			$i++;
		}

		// Initialize paging with above parameters
		$this->pagination->initialize($config);
		
		//Create paging inks
		$paging_links=$this->pagination->create_links();
		
		// Recieve any messages to be shown, when category is added or updated
		$messages=$this->messages->get();	
		
		//Loads header, category listing  and footer view.
		$category=$this->display_category();
		
		$current_url=base_url()."blog/index";	#Get Current url

		// Load the seo model which interact with database
		$this->load->model('SeoModel');
		
		$seo_array=$this->SeoModel->get_seo_data(array('is_delete'=>0),true,'blog');

		$this->load->view('header_outer',array('seo_array'=>$seo_array,'title'=>'Manage Listing Post'));
		$this->load->view('blog/blog-comment',array('category'=>$category,'comment'=>$comment_data_array,'blog_article'=>$blog_article,'paging_links'=>$paging_links,'messages' =>$messages,'post_id'=>$post_id,'data'=>$data));
		$this->load->view('footer_outer');
	}
	
	function commentPost($post_id,$id=0)
	{
		$this->load->library('form_validation'); 
		$user_data_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('email', 'email', 'required|valid_email');
			$this->form_validation->set_rules('comment', 'comment', 'required');
			$this->form_validation->set_rules('securityCode', 'Security Code', 'trim|required|xss_clean');
			$this->form_validation->set_rules('securityCode', 'Security Code', 'required|matches[word]');

			//Prepare member array from posted data
			$comment_data = array(
				'comment' => $this->input->post('comment',true),
				'name' => $this->input->post('name',true),
				'email' => $this->input->post('email',true),
				'added_on'=> $this->input->post('added_on',true),
				'entry_id'=> $this->input->post('entry_id',true),	
			);	

			if($this->form_validation->run()) {				
				$id=$this->input->post('id');
				//check category exists
				if(count($category_exists)) {
					$this->messages->add('Listing Comment already exists', 'error');					
				}
				else
				{
					$pos = strpos($_SERVER['HTTP_REFERER'], base_url());
					if($pos === false) {
						redirect('blog_comment/comment/'.$post_id);
					}else{
						//Insert New Listing Category In Database
						$this->Category_Model->create_comment($comment_data);	
						$this->messages->add('Comment Posted successfully', 'success');
						redirect('blog_comment/comment/'.$post_id);
					}
				}
			}
			$this->messages->add(validation_errors());
			$comment_data_array=$comment_data;
		}
		//Loads header, category create  and footer view.
		redirect('blog_comment/comment/'.$post_id);
	}
	function commentReply($post_id=0)
	{
		$this->load->library('form_validation'); 
		$user_data_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('email', 'email', 'required|valid_email');
			$this->form_validation->set_rules('comment', 'comment', 'required');
			$this->form_validation->set_rules('securityCode', 'Security Code', 'trim|required|xss_clean');
			$this->form_validation->set_rules('securityCode', 'Security Code', 'required|matches[word]');

			
			//Prepare reply array from posted data
			$reply_data = array(
				'comment' => $this->input->post('comment',true),
				'name' => $this->input->post('name',true),
				'email' => $this->input->post('email',true),
				'added_on'=> $this->input->post('added_on',true),
				'post_id'=> $this->input->post('postId',true),	
			);	
			
			if($this->form_validation->run()) {
				$pos = strpos($_SERVER['HTTP_REFERER'], base_url());
				if($pos === false){
					redirect('blog_comment/comment/'.$post_id);
				}else{
					//Insert New Listing Category In Database
					$this->Category_Model->create_reply($reply_data);	
					$this->messages->add('Comment Posted successfully', 'success');
					redirect('blog_comment/comment/'.$post_id);
				}
			}
			$this->messages->add(validation_errors());
			$comment_data_array=$comment_data;
		}
		//Loads header, category create  and footer view.
		redirect('blog_comment/comment/'.$post_id);
	}
}
?>