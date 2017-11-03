<?php
/**
* A Blog class
*
* This class is for blog listing
*
* @version 1.0
* @author Pravin Jha <pravinjha@gmail.com>
* @project Redcappi
*/

class Blog extends CI_Controller
{
	function __construct(){
		parent::__construct();
		 
		$this->load->model('blog/Category_Model');	
		$this->load->model('ConfigurationModel');
		$this->load->model('SeoModel');		
		$this->load->library('Multi_upload');
		$this->load->library('image_lib');
		force_ssl();	
	}
	/**
		Function index is to display listing of Blogs
	*/
	function index(){

		$this->post_listing($start=0);

	}
	/**
		Function index is to display listing of Blogs
		@param int start : for paging
	*/
	function post_listing($start=0){
		
		#Fetch category list
		$category=$this->display_category();
		#Fetch archive list
		$archive=$this->display_archive();
		#Fetch comment posted on blog
		$post=$this->display_post($start);
		#########################
		#Set pagination setting	#
		#########################
		$config['uri_segment'] = 2;
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
		$this->pagination->initialize($config);
		# Load log configuration model class which handles database interaction
		
		# Fetch maximum comment close days
		$config_arr=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'comment_close_days'));
		$comment_close_days=$config_arr[0]['config_value'];
		$today = date('Y-m-d');	#Current date
		##	Calculate Post Comment for each blog
		foreach($post as $postVal){
			if(!empty($postVal)){
				foreach($postVal as $key=>$value){
					$post['post'][$key]['post_comment']=$this->Category_Model->get_comment_count(array('entry_id'=>$value['id'],'is_delete'=>0,'DATEDIFF(\''.$today.'\',added_on) < '=>$comment_close_days));
				}
			}
		}
		$current_url=base_url()."blog/index";	#Get Current url
		# Load the seo model which interact with database
		
		#Fetch SEO related info: Page title, Meta info etc.
		$seo_array=$this->SeoModel->get_seo_data(array('is_delete'=>0),true,'blog');
		#Set paging links
		$paging_links=$this->pagination->create_links();
		#Load the header, blog and footer view of index page
		if($start >0){
		$seo_array[0]['title'] = $seo_array[0]['title'].' - Page '.(ceil($start/4)+1);
		$blog_description ='RedCappi Blog'.': Page '.(ceil($start/4)+1). ' -  latest email marketing articles!';
		}else{
		$blog_description ='RedCappi Blog -  latest email marketing articles!';
		}
		//$blog_description = substr(strip_tags($post['post'][0]['desc']),0,150).'...';

		$this->load->view('header_outer',array('seo_array'=>$seo_array,'title'=>'Blog | RedCappi','blog_description'=>"$blog_description"));
		$this->load->view('blog/blog',array('category'=>$category,'post'=>$post,'paging_links'=>$paging_links,'archive'=>$archive));
		$this->load->view('footer_outer');

	}

	/**
		function to display category
	**/
	function display_category(){
		$fetch_condiotions_array=array(
			'is_deleted'=>0,'category_status'=>1,
		);
		#Fetch category list
		$category['category']=$this->Category_Model->get_category_data($fetch_condiotions_array);
		return $category;
	}

	/**
		function to display Archived blogs
		@return string archive : return archive blogs
	**/
	function display_archive(){		 
		$config['total_rows']=$this->Category_Model->get_archive_count(array('is_deleted'=>0,'status'=>1));
		if($config['total_rows']>0){
			$archive['archive']=$this->Category_Model->get_archive_data(array('is_deleted'=>0,'status'=>1));
		}else{
			$archive = $config['total_rows'];
		}
		return $archive;
	}
	/**
		function to display Post
		@return string post:return posts
	*/
	function display_post($start=0){
		## Load Blog General settings###
		$this->Category_Model->get_blog_setting_data();
		#set base url

		$config['base_url']=base_url().'blog';
		$config['per_page'] = 10;
		if(BLOG_FRONT_PAGE_POST_DISPLAY!=""){
			$rows_per_page = BLOG_FRONT_PAGE_POST_DISPLAY;
			$config['per_page']=BLOG_FRONT_PAGE_POST_DISPLAY;
		}

		# Define config parameters for paging like base url, total rows and record per page.
		$config['total_rows']=$this->Category_Model->get_post_count(array('is_deleted'=>0,'status'=>1,'post_archives'=>0));
			
		$this->pagination->initialize($config);		
		$post['post']=$this->Category_Model->get_post_data(array('is_deleted'=>0,'status'=>1,'post_archives'=>0), $config['per_page'],$start);

		return $post;
	}
	function limit_words($string, $word_limit){
        $words = str_word_count($string, 1);
        return implode(" ",array_splice($words,0,$word_limit));
    }
	/**
		Function archive to display listing of arhive blogs
		@param int month_index: pass month name
		@param int year_index: pass year name
		@param int start: for paging
	*/
	function archive($month_index=0,$year_index=0,$start=0){		
	    session_start();
		$messages=$this->messages->get();
		### Get Archive List ###
		$archive=$this->display_archive();

		### Get category List ###
		$category=$this->display_category();
		### Get archive blog List ###
		$blog_category=$this->blog_category_archive($month_index,$year_index,$start);

		#########################
		#Set pagination setting	#
		#########################
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

		$this->pagination->initialize($config);
		# Load log configuration model class which handles database interaction
		
		## Fetch maximum comment close days
		$config_arr=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'comment_close_days'));
		$comment_close_days=$config_arr[0]['config_value'];
		$today = date('Y-m-d');	#Current date
		##	Calculate Post Coment for each blog
		foreach($blog_category as $postVal){
			if(!empty($postVal)){
				foreach($postVal as $key=>$value){
					$blog_category['blog_category'][$key]['post_comment']=$this->Category_Model->get_comment_count(array('entry_id'=>$value['id'],'is_delete'=>0,'DATEDIFF(\''.$today.'\',added_on) < '=>$comment_close_days));
				}
			}
		}
		#Fetch paging links
		$paging_links=$this->pagination->create_links();

		$current_url=base_url()."blog/index";	#Get Current url
 
		#Fetch seo info :page title, meta info etc.
		$seo_array=$this->SeoModel->get_seo_data(array('is_delete'=>0),true,'blog');
		unset($seo_array[0]['title']);
		$month_name = date( 'F', mktime(0, 0, 0, $month_index) );
		if($start>0)
			$title="$month_name $year_index- RedCappi- Email Marketing Blog"." - Page ".(ceil($start/4)+1);
		else
			$title="$month_name $year_index- RedCappi- Email Marketing Blog";
		
		//$blog_description = substr(strip_tags($blog_category['blog_category'][0]['desc']),0,150).'...';	
		 
		$this->load->view('header_outer',array('seo_array'=>$seo_array,'title'=>$title, 'blog_description'=>"RedCappi Blog -  $month_name $year_index email marketing articles!"));
		$this->load->view('blog/blog-archive',array('category'=>$category,'paging_links'=>$paging_links,'blog_category'=>$blog_category,'messages' =>$messages,'archive'=>$archive));
		$this->load->view('footer_outer');
	}
	/**
		function to display category name
		@param int cat_id: category id
	**/
	function get_category_name($cat_id){
		$fetch_condiotions_array=array(
			'is_deleted'=>0,'category_status'=>1,'id'=>$cat_id,
		);
		#Fetch category name
		$category_name['category_name']=$this->Category_Model->get_category_name($fetch_condiotions_array);
		return $category_name;
	}

############################################################################################################
############################################################################################################
############################################################################################################
	/**
		Function email_marketing_category to list blogs according to category id
		@param string cat_name: category name
		@param int cat_id: category id
		@param int start: used for pagination
	*/
	function email_marketing_category($cat_name="",$cat_id=0,$start=0){
		
		
	    session_start();
		$messages=$this->messages->get();
		#Fetch category list
		$category=$this->display_category();
		#Fetch category name
		$category_name=$this->get_category_name($cat_id);
		 
		### Get archive blog List ###
		$blog_category=$this->blog_category1($cat_name,$cat_id,$start);
		#########################
		#Set pagination setting	#
		#########################
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
		$this->pagination->initialize($config);

		 
		## Fetch maximum comment close days
		$config_arr=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'comment_close_days'));
		$comment_close_days=$config_arr[0]['config_value'];
		$today = date('Y-m-d');	//Current date
		##	Calculate Post Coment for each blog
		foreach($blog_category as $postVal){
				if(!empty($postVal)){
					foreach($postVal as $key=>$value){
						$blog_category['blog_category'][$key]['post_comment']=$this->Category_Model->get_comment_count(array('entry_id'=>$value['id'],'is_delete'=>0,'DATEDIFF(\''.$today.'\',added_on) < '=>$comment_close_days));
					}
				}
		}
		$paging_links=$this->pagination->create_links();
		$current_url=base_url()."blog/index";	#Get Current url
 
		#Fetch seo info :page title, meta info etc.
		$seo_array=$this->SeoModel->get_seo_data(array('is_delete'=>0),true,'blog');
		unset($seo_array[0]['title']);

		if($start >0){
			$title = "$cat_name- RedCappi- Email Marketing Blog"." - Page ".(ceil($start/4)+1);
			$pg_no = ": Page ".(ceil($start/4)+1);
		}else{
			$pg_no = '';
			$title="$cat_name- RedCappi- Email Marketing Blog";
		}
		//$blog_description = substr(strip_tags($blog_category['blog_category'][0]['desc']),0,150).'...';	
		#Load the header, blogs and footer view of index page
		$this->load->view('header_outer',array('seo_array'=>$seo_array,'title'=>$title,'blog_description'=>"RedCappi Blog{$pg_no} -  ".$category_name['category_name'][0]['category_name']." email marketing articles!"));
		$this->load->view('blog/blog-category',array('category'=>$category,'paging_links'=>$paging_links,'blog_category'=>$blog_category,'cat_id'=>$cat_id,'messages' =>$messages,'category_name'=>$category_name));
		$this->load->view('footer_outer');
	}

	/**
		Function blog_article is to list blog article
	*/
	function article($article_name="",$post_id=0,$start=0){
		
	    session_start();

		$messages=$this->messages->get();

		/**
			Genrate captcha image
		*/
		$strImgPath=substr(FCPATH,0,strrpos(FCPATH,DIRECTORY_SEPARATOR));
		$strImgPath= $strImgPath.'/webappassets/captcha/';
		$captcha_defaults = array(
			'img_path' => $strImgPath,
			'img_url'  => base_url().'webappassets/captcha/',
			'img_width'     => 80,
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
		### Get Archive List ###
		$archive=$this->display_archive();
		$category=$this->display_category();

		$fetch_condiotions_array=array('is_deleted'=>0,'id'=>$post_id);
		$blog_article['blog_article']=$this->Category_Model->get_article_detail($fetch_condiotions_array);
		if(count($blog_article['blog_article'])<1){redirect('blog/');exit;}
		$category_name=$this->get_category_name($blog_article['blog_article'][0]['cat_id']);
		$this->Category_Model->get_blog_setting_data();
		$blog_description = (trim($blog_article['blog_article'][0]['meta_description'])!='')?$blog_article['blog_article'][0]['meta_description']:html_entity_decode(substr(strip_tags($blog_article['blog_article'][0]['desc']),0,150).'...',ENT_QUOTES);
		$meta_keywords = $blog_article['blog_article'][0]['meta_keywords'];
		
		$og_meta_tags = $this->is_authorized->getOGMetaTags($blog_article['blog_article'][0]['desc'], $blog_article['blog_article'][0]['title']);
		
		 
		## Fetch maximum comment close days
		$config_arr=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'comment_close_days'));
		$comment_close_days=$config_arr[0]['config_value'];
		$today = date('Y-m-d');	//Current date
		########## Fetch Comment ################
		$fetch_comment_array=array('entry_id'=>$post_id,'is_delete'=>0,'DATEDIFF(\''.$today.'\',added_on) < '=>$comment_close_days);
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'article/'.$post_id.'/'.$article_name;
		$config['total_rows']=$this->Category_Model->get_comment_count($fetch_comment_array);
		$blog_article['blog_article'][0]['post_comment']=$config['total_rows'];
		if(BLOG_FRONT_PAGE_POST_DISPLAY!=""){
			$rows_per_page = BLOG_FRONT_PAGE_POST_DISPLAY;
			$config['per_page'] = BLOG_FRONT_PAGE_POST_DISPLAY;
			$config['uri_segment'] = 5;
		}else{
			$config['per_page'] = 10;
		}

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
		// Initialize paging with above parameters
		$this->pagination->initialize($config);

		//Create paging inks
		$paging_links=$this->pagination->create_links();

		$comment_data_array=$this->Category_Model->get_comment_listing($fetch_comment_array,$config['per_page'],$start);
		$current_url=base_url()."blog/index";	#Get Current url
		#$title="$article_name- RedCappi- Email Marketing Blog";
		$title=str_replace('-',' ',$article_name);
		//Load the header, blog and footer view of index page
		$this->load->view('header_outer',array('seo_array'=>$seo_array,'title'=>"$title",'og_meta_tags'=>$og_meta_tags, 'blog_description'=>"$blog_description", 'meta_keywords'=>"$meta_keywords"));
		$this->load->view('blog/blog-article',array('category'=>$category,'blog_article'=>$blog_article,'paging_links'=>$paging_links,'cat_id'=>$blog_article['blog_article'][0]['cat_id'],'post_id'=>$post_id,'messages' =>$messages,'category_name'=>$category_name,'comment'=>$comment_data_array,'archive'=>$archive,'data'=>$data));
		$this->load->view('footer_outer');
	}

 

	/**
		function to display Post Under category
	*/
	function blog_category1($cat_name="",$cat_id,$start=0){


		$this->Category_Model->get_blog_setting_data();

		$config['base_url']=base_url().'index.php/blog/email_marketing_category/'.$cat_name."/".$cat_id;

		if(BLOG_FRONT_PAGE_POST_DISPLAY!=""){

			$rows_per_page = BLOG_FRONT_PAGE_POST_DISPLAY;;

			$config['per_page']=BLOG_FRONT_PAGE_POST_DISPLAY;
			$config['uri_segment']=5;

		}else{
			$config['per_page'] = 10;


		}

		$fetch_condiotions_array=array(
		'is_deleted'=>0,'status'=>1,'post_archives'=>0,'cat_id'=>$cat_id,
		);

		// Define config parameters for paging like base url, total rows and record per page.

		$config['total_rows']=$this->Category_Model->get_post_count($fetch_condiotions_array);

		

		// Initialize paging with above parameters
		$this->pagination->initialize($config);
		//$paging_links=$this->pagination->create_links();


		$blog_category['blog_category']=$this->Category_Model->get_post_data($fetch_condiotions_array,$config['per_page'],$start);


		return $blog_category;
	}


	function addPost($cat_id,$id=0){
		
		$this->Category_Model->get_blog_setting_data();

		$user_data_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('title', 'title', 'required');
			$this->form_validation->set_rules('desc', 'desc', 'required');


			//Prepare member array from posted data
			$post_data = array(
				'cat_id'=> $this->input->post('cat_id',true),
				'title' => $this->input->post('title',true),
				'desc' => $this->input->post('desc',true),
				'added_on'=> $this->input->post('added_on',true),
				'added_by'=> $this->input->post('added_by',true),
				'status'=> $this->input->post('status',true),
				'post_archives'=> $this->input->post('post_archives',true),
			);


			########upload


			$upload_path= $this->config->item('blog_files');

			$config['upload_path'] = $upload_path; // server directory
			$config['allowed_types'] = 'gif|jpg|png|jpeg'; // by extension, will check for whether it is an image
			$config['max_size']    = '1000'; // in kb
		   // $config['max_width']  = '1024';
		   // $config['max_height']  = '768';

			
			

			$this->upload->initialize($config);

			$files = $this->multi_upload->go_upload();

			if ( ! $files )
			{
				$error = array('error' => $this->upload->display_errors());
			}
			else
			{
				if($this->form_validation->run()) {
					$id=$this->input->post('id');
					//check category exists
					if(count($category_exists)) {
						$this->messages->add('Post already exists', 'error');
					}
					else
					{
						//Insert New Listing Category In Database
						$post_id = $this->Category_Model->create_post($post_data);

						//$data = array('upload_data' => $this->upload->data());
						$data = array('upload_data' => $files);

						$total = count($data['upload_data']);
						for($i = 0 ; $i<$total ; $i++){

							$category_data = array(
								'img_name' => $data['upload_data'][$i]['name'],
								'post_id' => $post_id,
								'img_status'=> 1,
								'img_default'=> 0
							);
							$this->Category_Model->create_image_listing($category_data);
						//	print_r($data[$i]);
						//	print_r($category_data);
							if(BLOG_IMAGE_WIDTH != 0 || BLOG_IMAGE_HEIGHT != 0){
								
								$config['image_library'] = 'gd2';
								$config['source_image'] = $upload_path.$data['upload_data'][$i]['name'];
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

						}
						$this->messages->add('Post added successfully', 'success');
						redirect('blog/email_marketing_category/'.$cat_id);
					}
				}
				//$this->load->view('upload_success', $data);
			}
			##########
			$post_data_array=$post_data;
		}

		//Loads header, category create  and footer view.
		redirect('blog/email_marketing_category/'.$cat_id,$error);
	}

	function do_upload() {


		$upload_path= $this->config->item('blog_files');

        $config['upload_path'] = $upload_path; // server directory
        $config['allowed_types'] = 'gif|jpg|png|jpeg'; // by extension, will check for whether it is an image
        $config['max_size']    = '1000'; // in kb
       // $config['max_width']  = '1024';
       // $config['max_height']  = '768';

        $this->load->library('upload', $config);
		$this->upload->initialize($config);

        $files = $this->multi_upload->go_upload();


        if ( ! $files )
        {
            $error = array('error' => $this->upload->display_errors());
            $this->load->view('upload_form', $error);
        }
        else
        {
            $data = array('upload_data' => $files);
            $this->load->view('upload_success', $data);
        }
    }
	/**
		function to display Archived
	**/
	function blog_category_archive($month_index=0,$year_index=0,$start=0){

		#Set pagination configuration
		$config['base_url']=base_url()."blog/archive/$month_index/$year_index/";
		$this->Category_Model->get_blog_setting_data();
		if(BLOG_FRONT_PAGE_POST_DISPLAY!=""){
			$rows_per_page = BLOG_FRONT_PAGE_POST_DISPLAY;
			$config['per_page']=3;//BLOG_FRONT_PAGE_POST_DISPLAY;
			$config['uri_segment']=5;
		}else{
			$config['per_page'] = 10;
		}

		$fetch_condiotions_array=array(
		'is_deleted'=>0,'status'=>1,'MONTH(added_on)'=>$month_index,'YEAR(added_on)'=>$year_index,
		);

		# Define config parameters for paging like base url, total rows and record per page.
		$config['total_rows']=$this->Category_Model->get_post_count($fetch_condiotions_array);
		
		# Initialize paging with above parameters
		$this->pagination->initialize($config);
		#Fetch blogs
		$blog_category['blog_category']=$this->Category_Model->get_post_data($fetch_condiotions_array,$config['per_page'],$start);

		return $blog_category;
	}


	function result($start=0){
		#########################
		#Set pagination setting	#
		#########################
		$config['base_url']			=	base_url().'blog/result';

		$config['total_rows']		=	$this->Category_Model->count_search_product_result(array('status'=>1,'is_deleted'=>0));

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

		$product_data=$this->Category_Model->search_product_result(array('is_deleted'=>0,'status'=>1),$config['per_page'],$start);
		$paging_links=$this->pagination->create_links();

		$this->load->view('header_outer',array('seo_array'=>$seo_array,'title'=>'Blog'));
		$this->load->view('blog/blog_search_result',array('product_data'=>$product_data,'paging_links'=>$paging_links,'search_text'=>$this->input->post('search_text')));
		$this->load->view('footer_outer');
	}

}
?>
