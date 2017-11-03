<?php
class Template_color extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');

		// Load the template category model which interact with database
		$this->load->model('webmaster/TemplateColor_Model');
		$this->upload_path=substr(FCPATH,0,strrpos(FCPATH,DIRECTORY_SEPARATOR));
		
		# HTTPS/SSL enabled
		force_ssl();

	}
	
	function index()
	{
		$this->template_color_list();
	}
	
	function template_color_list($start=0)
	{
		$fetch_conditions_array=array('is_delete'=>0,'member_id'=>'-1');
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/template_color/template_color_list';
		$config['total_rows']=$this->TemplateColor_Model->get_color_count($fetch_conditions_array);
		$config['per_page']=20;
		$config['uri_segment']=4;

		// Initialize paging with above parameters
		$this->pagination->initialize($config);
		
		//Create paging inks
		$paging_links=$this->pagination->create_links();
		
		$color_data_array=$this->TemplateColor_Model->get_color_data($fetch_conditions_array,$config['per_page'],$start);		
		// Recieve any messages to be shown, when category is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, template header  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage template colors','logo_link'=>$logo_link));
		$this->load->view('webmaster/template_color_list',array('colors'=>$color_data_array,'paging_links'=>$paging_links,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	function color_edit($id=0)
	{
		//To check form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('theme_name', 'Color Template Title', 'required');
			$this->form_validation->set_rules('outer_bg', 'Outer Background Color', 'required');
			$this->form_validation->set_rules('body_bg', 'Body Background Color', 'required');
			$this->form_validation->set_rules('footer_bg', 'Footer Background Color', 'required');
			$this->form_validation->set_rules('border_color', 'Border Color', 'required');
			$this->form_validation->set_rules('footer_font_color', 'Footer Font Color', 'required');
			//Prepare category array from posted data
			$color_data = array(
				'theme_name' => $this->input->post('theme_name',true),
				'outer_bg' => ' #'.$this->input->post('outer_bg',true),
				'body_bg' => ' #'.$this->input->post('body_bg',true),
				'footer_bg' => ' #'.$this->input->post('footer_bg',true),
				'border_color' => ' #'.$this->input->post('border_color',true),
				'footer_font_color' => ' #'.$this->input->post('footer_font_color',true),
				'id' => $this->input->post('id',true),
				'is_active' => $this->input->post('is_active',true)
			);	
			
			if($this->form_validation->run()) {
				
				$id=$this->input->post('id');
				
				//check Listing category exists by loading category from database
				$category_exists=$this->TemplateColor_Model->get_color_data(array('theme_name'=>$this->input->post('theme_name',true),'is_delete'=>0,'id !='=>$id));
				
				//check category exists
				if(count($category_exists)) {
					$this->messages->add('Color theme already exists', 'error');					
				}
				else
				{
					
					// Update Listing Category in database
					$this->TemplateColor_Model->update_color($color_data,array('id'=>$id));				
					$this->messages->add('Temaplate color updated successfully', 'success');
					redirect('webmaster/template_color/template_color_list');
					
				}
			}
			
			$color_data_array=$color_data;
		}		
		if(!count($color_data_array)) {
			$fetch_conditions_array=array('is_delete'=>0,'id'=>$id);
			$color_data_array=$this->TemplateColor_Model->get_color_data($fetch_conditions_array,$config['per_page']);
			$color_data_array=$color_data_array[0];
			
		}
		$logo_link="webmaster/dashboard_stat";
		// Recieve any messages to be shown, when template color is added or updated
		$messages=$this->messages->get();		
		//Loads header, header edit  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Update Color','logo_link'=>$logo_link));
		$this->load->view('webmaster/template_color_edit',array('color'=>$color_data_array,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	function color_delete($id=0)
	{
		// Deletes category according to listing category ID
		$this->TemplateColor_Model->delete_color(array('id'=>$id));

		// Assign  success message by message class
		$this->messages->add('Template color deleted successfully', 'success');

		// Redirect to template header 
		redirect('webmaster/template_color/template_color_list');
	}
	
	function color_create()
	{
		$user_data_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('theme_name', 'Color Template Title', 'required');
			$this->form_validation->set_rules('outer_bg', 'Outer Background Color', 'required');
			$this->form_validation->set_rules('body_bg', 'Body Background Color', 'required');
			$this->form_validation->set_rules('footer_bg', 'Footer Background Color', 'required');
			$this->form_validation->set_rules('border_color', 'Border Color', 'required');
			$this->form_validation->set_rules('footer_font_color', 'Footer Font Color', 'required');
			
			//Prepare category array from posted data
			$color_data = array(
				'theme_name' => $this->input->post('theme_name',true),
				'outer_bg' => ' #'.$this->input->post('outer_bg',true),
				'body_bg' => ' #'.$this->input->post('body_bg',true),
				'footer_bg' => ' #'.$this->input->post('footer_bg',true),
				'border_color' => ' #'.$this->input->post('border_color',true),
				'footer_font_color' => ' #'.$this->input->post('footer_font_color',true),
				'member_id'=>'-1',
				'body_font_color'=>' #fff',
				'is_active' => $this->input->post('is_active',true)
			);	
			
			if($this->form_validation->run()) {
				
				//check Listing category exists by loading category from database
				$category_exists=$this->TemplateColor_Model->get_color_data(array('theme_name'=>$this->input->post('theme_name',true),'is_delete'=>0));
				
				//check category exists
				if(count($category_exists)) {
					$this->messages->add('Color theme already exists', 'error');					
				}
				else
				{
					
					// Update Listing Category in database
					$this->TemplateColor_Model->create_color($color_data);				
					$this->messages->add('Template color created successfully', 'success');
					redirect('webmaster/template_color/template_color_list');
					
				}
			}
			$color_data_array=$color_data;
		}
		
		$logo_link="webmaster/dashboard_stat";
		// Recieve any messages to be shown
		$messages=$this->messages->get();
		//Loads header, category create  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Create Template Header','logo_link'=>$logo_link));
		$this->load->view('webmaster/template_color_create',array('color'=>$color_data_array,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
}
?>