<?php
class Template_header extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');

		// Load the template category model which interact with database
		$this->load->model('webmaster/TemplateHeader_Model');
		$this->upload_path=substr(FCPATH,0,strrpos(FCPATH,DIRECTORY_SEPARATOR));
		
		# HTTPS/SSL enabled
		force_ssl();

	}
	
	function index()
	{
		$this->template_header_list();
	}
	
	function template_header_list($start=0)
	{
		$fetch_conditions_array=array('is_delete'=>0,'template_id >'=>0);
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/template_header/template_header_list';
		$config['total_rows']=$this->TemplateHeader_Model->get_header_count($fetch_conditions_array);
		$config['per_page']=10;
		$config['uri_segment']=4;

		// Initialize paging with above parameters
		$this->pagination->initialize($config);
		
		//Create paging inks
		$paging_links=$this->pagination->create_links();
		
		$header_data_array=$this->TemplateHeader_Model->get_header_data($fetch_conditions_array,$config['per_page'],$start);
		$i=0;
		foreach($header_data_array as $header){
			$header_category_array=explode(",",$header['template_theme_id']);
			// Load the template category model which interact with database
			$this->load->model('webmaster/TemplateCategory_Model');
			$category_data_array=$this->TemplateCategory_Model->get_category_data(array('red_is_delete'=>0,'red_theme_id >'=>0,'red_is_active'=>1),0,0,$header_category_array);
			$categories=array();
			foreach($category_data_array as $category){
				$categories[]=$category['red_theme_name'];
			}
			$header_data_array[$i]['red_theme_name']=implode(",",$categories);
			$i++;			
		}
		// Recieve any messages to be shown, when category is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, template header  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage template headers','logo_link'=>$logo_link));
		$this->load->view('webmaster/template_header_list',array('headers'=>$header_data_array,'paging_links'=>$paging_links,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	function header_edit($id=0)
	{
		//To check form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('template_name', 'Header Title', 'required');
			//Prepare category array from posted data			
			$header_data = array(				
				'template_name' => $this->input->post('template_name',true),
				'is_active' => $this->input->post('is_active',true),
				'template_theme_id' => implode(',',$this->input->post('template_theme_id',true)),
			);
			if($this->form_validation->run()) {
				$template_id=$this->input->post('template_id');
				
				if($_FILES['screenshot']['name']!=""){
					$upload_path=$this->upload_path;
					$upload_path= $upload_path.'/webappassets/header-images/';						
					$file_name=$_FILES['screenshot']['name'];
					$file_ext=substr($file_name,strrpos($file_name,'.'));
					$file_name='header-'.$id.$file_ext;
					$config['upload_path'] = $upload_path;
					$config['allowed_types'] = 'gif|jpg|png|jpeg';
					$config['file_name']	= $file_name;
					@unlink($upload_path.$file_name);
					$this->load->library('upload');
					$this->upload->initialize($config);
					if ( ! $this->upload->do_upload('screenshot'))
					{
						$this->messages->add($this->upload->display_errors(),'error');
					}
					else
					{
						$header_data['image_path']=$file_name;
						// Update Listing Category in database
						$this->TemplateHeader_Model->update_header($header_data,array('template_id'=>$template_id));
						$this->messages->add('Template header updated successfully', 'success');
						redirect('webmaster/template_header/template_header_list');
					}
				}else{
					// Update Listing Category in database
					$this->TemplateHeader_Model->update_header($header_data,array('template_id'=>$template_id));
					$this->messages->add('Temaplate header updated successfully', 'success');
					redirect('webmaster/template_header/template_header_list');
				}
			}		
			$header_data_array=$category_data;
		}
		if(!count($header_data_array)){
			$fetch_conditions_array=array('is_delete'=>0,'template_id'=>$id);
			$header_data_array=$this->TemplateHeader_Model->get_header_data($fetch_conditions_array,$config['per_page']);
			$selected_category=explode(",",$header_data_array[0]['template_theme_id']);			
			$header_data_array[0]['selected_category']=$selected_category;
			$header_data_array=$header_data_array[0];
			// Load the template category model which interact with database
			$this->load->model('webmaster/TemplateCategory_Model');
			$total_category=$this->TemplateCategory_Model->get_category_count(array('red_is_delete'=>0,'red_theme_id >'=>0,'red_is_active'=>1));
			$category_data_array=$this->TemplateCategory_Model->get_category_data(array('red_is_delete'=>0,'red_theme_id >'=>0,'red_is_active'=>1),$total_category);
		}
		// Recieve any messages to be shown, when category is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, header edit  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Update Header','logo_link'=>$logo_link));
		$this->load->view('webmaster/template_header_edit',array('header'=>$header_data_array,'categories'=>$category_data_array,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	function header_delete($id=0)
	{
		// Deletes category according to listing category ID
		$this->TemplateHeader_Model->delete_header(array('template_id'=>$id));

		// Assign  success message by message class
		$this->messages->add('Template header deleted successfully', 'success');

		// Redirect to template header 
		redirect('webmaster/template_header/template_header_list');
	}
	
	function header_create()
	{
		$user_data_array=array();
		//To check form is submitted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('template_name', 'Template Header Title', 'required');
			$this->form_validation->set_rules('template_theme_id', 'Header Category', 'required');
			
			//Prepare member array from posted data
			$header_data = array(
				'template_name' => $this->input->post('template_name',true),
				'is_active' => $this->input->post('is_active',true),
				'template_theme_id' => implode(',',$this->input->post('template_theme_id',true)),
			);	
			
			if($this->form_validation->run()) {
				$upload_path=$this->upload_path;
				$upload_path= $upload_path.'/webappassets/header-images/';						
				$file_name=$_FILES['screenshot']['name'];
				$file_ext=substr($file_name,strrpos($file_name,'.'));					
				$config['upload_path'] = $upload_path;
				$config['allowed_types'] = 'gif|jpg|png|jpeg';
				$config['file_name']	= $file_name;
				@unlink($upload_path.$file_name);
				$this->load->library('upload');
				$this->upload->initialize($config);
					
				if ( ! $this->upload->do_upload('screenshot'))
				{
					$this->messages->add($this->upload->display_errors(),'error');
				}
				else
				{						
					// Update Listing Category in database
					$template_id=$this->TemplateHeader_Model->create_header($header_data,array('template_id'=>$template_id));
					copy($upload_path.$file_name,$upload_path."header-".$template_id.$file_ext);
					@unlink($upload_path.$file_name);
					$this->messages->add('Temaplate header created successfully', 'success');
					redirect('webmaster/template_header/template_header_list');
				}
				
			}
			$header_data_array=$header_data;
		}
		// Load the template category model which interact with database
		$this->load->model('webmaster/TemplateCategory_Model');
		$total_category=$this->TemplateCategory_Model->get_category_count(array('red_is_delete'=>0,'red_theme_id >'=>0,'red_is_active'=>1));
		$category_data_array=$this->TemplateCategory_Model->get_category_data(array('red_is_delete'=>0,'red_theme_id >'=>0,'red_is_active'=>1),$total_category);
		
		// Recieve any messages to be shown
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, category create  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Create Template Header','logo_link'=>$logo_link));
		$this->load->view('webmaster/template_header_create',array('header'=>$header_data_array,'categories'=>$category_data_array,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	/**
		Function display_header_on_dashboard to display the header on Campaign Header Dashboard
	**/
	function display_header_on_dashboard($id=0,$show_on_dashboard=0){
		// Update Listing Header in database
		$this->TemplateHeader_Model->update_header(array('show_on_dashboard'=>$show_on_dashboard),array('template_id'=>$id));
	}
}
?>