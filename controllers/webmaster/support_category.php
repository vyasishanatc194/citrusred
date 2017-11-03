<?php
class Support_category extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');

		// Load the template category model which interact with database
		$this->load->model('webmaster/SupportCategory_Model');
		$this->upload_path=substr(FCPATH,0,strrpos(FCPATH,DIRECTORY_SEPARATOR));
		
		# HTTPS/SSL enabled
		force_ssl();

	}
	
	function index()
	{
		$this->support_category_list();
	}
	
	function support_category_list($start=0)
	{
		$fetch_conditions_array=array('is_delete'=>0);
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/support_category/support_category_list';
		$config['total_rows']=$this->SupportCategory_Model->get_category_count($fetch_conditions_array);
		$config['per_page']=20;
		$config['uri_segment']=4;

		// Initialize paging with above parameters
		$this->pagination->initialize($config);
		
		//Create paging inks
		$paging_links=$this->pagination->create_links();
		
		$cateogry_data_array=$this->SupportCategory_Model->get_category_data($fetch_conditions_array,$config['per_page'],$start);
		
		// Recieve any messages to be shown, when category is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, template header  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Support Category','logo_link'=>$logo_link));
		$this->load->view('webmaster/support_category_list',array('categories'=>$cateogry_data_array,'paging_links'=>$paging_links,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	function support_category_edit($id=0)
	{
		//To check form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('category', 'Support Category', 'required');
			//Prepare category array from posted data
			$category_data = array(
				'category' => $this->input->post('category',true),
				'is_active' => $this->input->post('is_active',true)
			);	
			
			if($this->form_validation->run()) {
				
				$id=$this->input->post('id');
				
				//check Support category exists by loading category from database
				$category_exists=$this->SupportCategory_Model->get_category_data(array('category'=>$this->input->post('category',true),'is_delete'=>0,'id !='=>$id));
				
				//check category exists
				if(count($category_exists)) {
					$this->messages->add('Support Category already exists', 'error');
					
				}
				else
				{					
					// Update Support Category in database
					$this->SupportCategory_Model->update_category($category_data,array('id'=>$id));				
					$this->messages->add('Support Category updated successfully', 'success');
					redirect('webmaster/support_category/support_category_list');
					
				}
			}
			
			$category_data_array=$category_data;
		}		
		if(!count($category_data_array)) {
			$fetch_conditions_array=array('is_delete'=>0,'id'=>$id);
			$category_data_array=$this->SupportCategory_Model->get_category_data($fetch_conditions_array,$config['per_page']);
			$category_data_array=$category_data_array[0];			
		}
		// Recieve any messages to be shown, when category is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, category edit  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Update Support Category','logo_link'=>$logo_link));
		$this->load->view('webmaster/support_category_edit',array('category'=>$category_data_array,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	function support_category_delete($id=0)
	{
		// Deletes category according to support category ID
		$this->SupportCategory_Model->delete_category(array('id'=>$id));

		// Assign  success message by message class
		$this->messages->add('Support category deleted successfully', 'success');

		// Redirect to support category
		redirect('webmaster/support_category/support_category_list');
	}
	
	function support_category_create()
	{
		$category_data_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('category', 'Support Category Title', 'required');
			
			//Prepare category array from posted data
			$category_data = array(
				'category' => $this->input->post('category',true),
				'is_active' => $this->input->post('is_active',true),				
			);	
			
			if($this->form_validation->run()) {
				// Update Support Category in database
				$template_id=$this->SupportCategory_Model->create_category($category_data);
				$this->messages->add('Support Category created successfully', 'success');
				redirect('webmaster/support_category/support_category_list');
				
			}
			$category_data_array=$category_data;
		}
		
		// Recieve any messages to be shown
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, category create  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Create Template Header','logo_link'=>$logo_link));
		$this->load->view('webmaster/support_category_create',array('category'=>$category_data_array,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
}
?>