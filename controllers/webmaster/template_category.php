<?php
class Template_category extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');

		// Load the template category model which interact with database
		$this->load->model('webmaster/TemplateCategory_Model');
		
		# HTTPS/SSL enabled
		force_ssl();

	}
	
	function index()
	{
		$this->template_category_list();
	}
	
	function template_category_list($start=0)
	{ 
		$fetch_conditions_array=array('red_is_delete'=>0,'red_theme_id >'=>0);
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/template_category/template_category_list';
		$config['total_rows']=$this->TemplateCategory_Model->get_category_count($fetch_conditions_array);
		$config['per_page']=20;
		$config['uri_segment']=4;

		// Initialize paging with above parameters
		$this->pagination->initialize($config);
		
		//Create paging inks
		$paging_links=$this->pagination->create_links();
		
		$category_data_array=$this->TemplateCategory_Model->get_category_data($fetch_conditions_array,$config['per_page'],$start);
		
		// Recieve any messages to be shown, when category is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, category listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Listing Category','logo_link'=>$logo_link));
		$this->load->view('webmaster/template_category_list',array('category'=>$category_data_array,'paging_links'=>$paging_links,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	function category_edit($id)
	{
		$user_data_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('red_theme_name', 'Category Title', 'required');
			//Prepare category array from posted data
			$category_data = array(
				'red_theme_name' => $this->input->post('red_theme_name',true),
				'red_is_active' => $this->input->post('red_is_active',true),
				
			);	
			
			if($this->form_validation->run()) {
			
				$red_theme_id=$this->input->post('red_theme_id');
				
				//check Listing category exists by loading category from database
				$category_exists=$this->TemplateCategory_Model->get_category_data(array('red_theme_name'=>$this->input->post('red_theme_name',true),'red_is_delete'=>0,'red_theme_id !='=>$red_theme_id));
				
				//check category exists
				if(count($category_exists)) {
					$this->messages->add('Category already exists', 'error');
					
				}
				else
				{
					// Update Listing Category in database
					$this->TemplateCategory_Model->update_category($category_data,array('red_theme_id'=>$red_theme_id));				
					$this->messages->add('Temaplate category updated successfully', 'success');
					redirect('webmaster/template_category/template_category_list');
				}
			}
			
			$category_data_array=$category_data;
		}
		
		if(!count($category_data_array)) {
			$fetch_conditions_array=array('red_theme_id'=>$id,'red_is_delete'=>0);
			$category_data_array=$this->TemplateCategory_Model->get_category_data($fetch_conditions_array,$config['per_page']);
			$category_data_array=$category_data_array[0];
		}
		// Recieve any messages to be shown, when category is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, category edit  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Update Category','logo_link'=>$logo_link));
		$this->load->view('webmaster/template_category_edit',array('category'=>$category_data_array,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	function category_delete($id=0)
	{
		// Deletes category according to listing category ID
		$this->TemplateCategory_Model->delete_category(array('red_theme_id'=>$id));

		// Assign  success message by message class
		$this->messages->add('Template category deleted successfully', 'success');

		// Redirect to listing of categories
		redirect('webmaster/template_category/template_category_list');
	}
	
	function category_create()
	{
		$user_data_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('red_theme_name', 'Template Category Title', 'required');
			
			//Prepare member array from posted data
			$category_data = array(
				'red_theme_name' => $this->input->post('red_theme_name',true),
				'red_is_active' => $this->input->post('red_is_active',true),				
			);	
			
			if($this->form_validation->run()) {
				
				//check Listing category exists by loading category from database
				$category_exists=$this->TemplateCategory_Model->get_category_data(array('red_theme_name'=>$this->input->post('red_theme_name',true),'red_is_delete'=>0));
				
				
				//check category exists
				if(count($category_exists)) {
					$this->messages->add('Template Category already exists', 'error');
					
				}
				else
				{
					//Insert New Listing Category In Database
					$this->TemplateCategory_Model->create_category($category_data);	
					$this->messages->add('Template category created successfully', 'success');
					redirect('webmaster/template_category/template_category_list');
				}
			}
			
			$category_data_array=$category_data;
		}
		
		$logo_link="webmaster/dashboard_stat";
		// Recieve any messages to be shown
		$messages=$this->messages->get();
		//Loads header, category create  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Create Listing Category','logo_link'=>$logo_link));
		$this->load->view('webmaster/template_category_create',array('category'=>$category_data_array,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
}
?>