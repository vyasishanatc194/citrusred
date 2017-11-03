<?php
class Support_content extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');

		// Load the support content model which interact with database
		$this->load->model('webmaster/SupportContent_Model');
		# HTTPS/SSL enabled
		force_ssl();

	}
	
	function index()
	{
		$this->support_content_list();
	}
	
	function support_content_list($start=0)
	{
		$fetch_conditions_array=array('rsp.is_delete'=>0);
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/support_content/support_content_list';
		$config['total_rows']=$this->SupportContent_Model->get_product_count($fetch_conditions_array);
		$config['per_page']=20;
		$config['uri_segment']=4;

		// Initialize paging with above parameters
		$this->pagination->initialize($config);
		
		//Create paging inks
		$paging_links=$this->pagination->create_links();
		
		$content_data_array=$this->SupportContent_Model->get_product_data($fetch_conditions_array,$config['per_page'],$start);
		
		// Recieve any messages to be shown, when content is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, template header  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Support Content','logo_link'=>$logo_link));
		$this->load->view('webmaster/support_content_list',array('contents'=>$content_data_array,'paging_links'=>$paging_links,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	function support_content_edit($id=0){
		
		//To check form is submitted
		if($this->input->post('action')=='submit')
		{	
			// Validation rules are applied
			$this->form_validation->set_rules('product', 'Support Product', 'required|max_length[255]|trim');
			$this->form_validation->set_rules('description', 'Support Content', 'required');
			$this->form_validation->set_rules('category_id', 'Support Category', 'required');
			
			$description=$this->input->get_post('description');
			$description=html_entity_decode($description, ENT_QUOTES, "utf-8" );
			$description=str_replace(array('[removed]'),array(''),$description);
			$description=$this->webCompatibleString($description);
			//Prepare category array from posted data
			$content_data = array(
				'product' => $this->input->post('product',true),
				'description' => $description,
				'category_id' => $this->input->post('category_id',true),
				'is_active' => $this->input->post('is_active',true)
			);	
			
			if($this->form_validation->run()) {
				
				$id=$this->input->post('id');					
				// Update Support Content in database
				$this->SupportContent_Model->update_product($content_data,array('id'=>$id));				
				$this->messages->add('Support Content updated successfully', 'success');
				redirect('webmaster/support_content/support_content_list');
			}
			
			$content_data_array=$content_data;
		}		
		if(!count($content_data_array)) {
			$fetch_conditions_array=array('rsp.is_delete'=>0,'rsp.id'=>$id);
			$content_data_array=$this->SupportContent_Model->get_product_data($fetch_conditions_array,$config['per_page']);
			$content_data_array=$content_data_array[0];
			$fetch_conditions_array=array('is_delete'=>0,'id'=>$id);			
		}
		// Load the support category model which interact with database
		$this->load->model('webmaster/SupportCategory_Model');
		$category_data_array=$this->SupportCategory_Model->get_category_data(array('is_delete'=>0));
		// Recieve any messages to be shown, when category is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, category edit  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Update Support Content','logo_link'=>$logo_link));
		$this->load->view('webmaster/support_content_edit',array('content'=>$content_data_array,'categories'=>$category_data_array,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	function support_content_delete($id=0)
	{
		// Deletes support content according to support content ID
		$this->SupportContent_Model->delete_product(array('id'=>$id));

		// Assign  success message by message class
		$this->messages->add('Support Content deleted successfully', 'success');

		// Redirect to support content
		redirect('webmaster/support_content/support_content_list');
	}
	
	function support_content_create()
	{		
		$content_data_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('product', 'Support Product', 'required|max_length[255]|trim');
			$this->form_validation->set_rules('description', 'Support Content', 'required');
			$this->form_validation->set_rules('category_id', 'Support Category', 'required');
			
			$description=$this->input->get_post('description');
			$description=html_entity_decode($description, ENT_QUOTES, "utf-8" );
			$description=str_replace(array('[removed]'),array(''),$description);
			$description=$this->webCompatibleString($description);
			//Prepare content array from posted data
			$content_data = array(
				'product' => $this->input->post('product',true),
				'description' => $description,
				'category_id' => $this->input->post('category_id',true),
				'is_active' => $this->input->post('is_active',true)
			);	
			
			if($this->form_validation->run()) {
				// Update Support Category in database
				$template_id=$this->SupportContent_Model->create_product($content_data);
				$this->messages->add('Support Content created successfully', 'success');
				redirect('webmaster/support_content/support_content_list');
				
			}
			$content_data_array=$content_data;
		}
		// Load the support category model which interact with database
		$this->load->model('webmaster/SupportCategory_Model');
		$category_data_array=$this->SupportCategory_Model->get_category_data(array('is_delete'=>0));
		// Recieve any messages to be shown
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, category create  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Create Template Header','logo_link'=>$logo_link));
		$this->load->view('webmaster/support_content_create',array('content'=>$content_data_array,'categories'=>$category_data_array,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	function webCompatibleString($str){
		$badContent = array("&nbsp;");
		$str = trim(str_replace($badContent," ",$str));

		$theBad = 	array("“","”","‘","’","…","—","–","");
		$theGood = array("\"","\"","'","'","...","-","-");
		$str = str_replace($theBad,$theGood,$str);

		$str = preg_replace('/[^(\x20-\x7F)\x0A]*/','', $str);
		return $str;
	}
}
?>