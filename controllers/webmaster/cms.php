<?php
class Cms extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');

		// Load the template category model which interact with database
		$this->load->model('SeoModel');
		# HTTPS/SSL enabled
		force_ssl();

	}
	function index(){
		$this->page_list();
	}
	function page_list($start=0){
	
		$fetch_conditions_array=array('is_delete'=>0);
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/cms/page_list';
		$config['total_rows']=$this->SeoModel->get_seo_data_count($fetch_conditions_array);
		$config['per_page']=10;
		$config['uri_segment']=4;

		// Initialize paging with above parameters
		$this->pagination->initialize($config);
		
		//Create paging inks
		$paging_links=$this->pagination->create_links();
		
		$pages_array=$this->SeoModel->get_seo_data($fetch_conditions_array,false,"",$config['per_page'],$start);

		// Recieve any messages to be shown
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, template header  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage template headers','logo_link'=>$logo_link));
		$this->load->view('webmaster/page_list',array('pages_array'=>$pages_array,'paging_links'=>$paging_links,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	function page_create(){
		$pages_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('page', 'Page', 'required');
			$this->form_validation->set_rules('title', 'Title', 'required');
			$this->form_validation->set_rules('keyword', 'Keyword', 'required');
			$this->form_validation->set_rules('description', 'Description', 'required');
			$this->form_validation->set_rules('h1', 'H1 Tag', 'required');	
			$content=str_replace("../../../asset/images", base_url()."webappassets/images", $this->input->post('content',true)); 
			$input_array = array(
				'page' => $this->input->post('page',true),
				'title' => $this->input->post('title',true),
				'keyword' => $this->input->post('keyword',true),
				'description' => $this->input->post('description',true),
				'h1' => $this->input->post('h1',true),
				'content' => $content,
			);	
			if($this->form_validation->run()) {
				
				//check page exists by loading page from database
				$page_exists=$this->SeoModel->get_seo_data(array('page'=>$this->input->post('page',true),'is_delete'=>0));			
				//check page exists
				if(count($page_exists)) {
					$this->messages->add('Page already exists', 'error');
					
				}
				else
				{	
					$this->SeoModel->create_seo($input_array);
					$this->messages->add('Page created successfully', 'success');
					redirect('webmaster/cms/page_list');
				}
			}
			
			$pages_array=$input_array;
		}
		
		// Recieve any messages to be shown
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, template header  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Create cms pages','logo_link'=>$logo_link));
		$this->load->view('webmaster/page_create',array('page'=>$pages_array,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	function page_edit($id=0){
		$seo_data_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('page', 'Page', 'required');
			$this->form_validation->set_rules('title', 'Title', 'required');
			$this->form_validation->set_rules('keyword', 'Keyword', 'required');
			$this->form_validation->set_rules('description', 'Description', 'required');
			$this->form_validation->set_rules('h1', 'H1 Tag', 'required');
			$content=str_replace("../../../asset/images", base_url()."webappassets/images", $this->input->post('content',true)); 
			$input_array = array(
				'page' => $this->input->post('page',true),
				'title' => $this->input->post('title',true),
				'keyword' => $this->input->post('keyword',true),
				'description' => $this->input->post('description',true),
				'h1' => $this->input->post('h1',true),
				'content' => $content,
			);	
			
			if($this->form_validation->run()) {
				
				$id=$this->input->post('id');
				
				//check page exists by loading page from database
				$page_exists=$this->SeoModel->get_seo_data(array('page'=>$this->input->post('page',true),'id !='=>$id,'is_delete'=>0));
				//check page exists
				if(count($page_exists)) {
					$this->messages->add('Page already exists', 'error');
					
				}else
				{
					$this->SeoModel->update_seo($input_array,array('id'=>$id));				
					$this->messages->add('Page updated successfully', 'success');
					redirect('webmaster/cms/page_list');
				}
			}			
			$page_array=$input_array;
		}
		
		if(!count($page_array)) {
			$fetch_conditions_array=array('id'=>$id);
			$page_array=$this->SeoModel->get_seo_data($fetch_conditions_array,$config['per_page']);
			$page_array=$page_array[0];
		}
		// Recieve any messages to be shown, when campaign is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, template header  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Edit cms pages','logo_link'=>$logo_link));
		$this->load->view('webmaster/page_edit',array('page'=>$page_array,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
}
/* End of file */
?>