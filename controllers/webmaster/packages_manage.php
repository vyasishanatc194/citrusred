<?php
class Packages_Manage extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');

		// Load the user model which interact with database
		$this->load->model('UserModel');
		# HTTPS/SSL enabled
		force_ssl();

	}
	
	function index()
	{
		$this->packages_list();
	}
	function packages($start=0){
		$fetch_conditions_array=array();
		
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/packages_manage/packages';
		$config['total_rows']=$this->UserModel->get_packages_count($fetch_conditions_array);
		$config['per_page']=50;
		$config['uri_segment']=4;

		// Initialize paging with above parameters
		$this->pagination->initialize($config);
		
		//Create paging inks
		$paging_links=$this->pagination->create_links();
		
		$packages=$this->UserModel->get_packages_data($fetch_conditions_array,$config['per_page'],$start);
		// Recieve any messages to be shown, when package is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, users listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Packages','logo_link'=>$logo_link));
		$this->load->view('webmaster/packages',array('packages'=>$packages,'paging_links'=>$paging_links,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	function packages_list($start=0)
	{
		$fetch_conditions_array=array('package_deleted'=>0);
		
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/packages_manage/packages_list';
		$config['total_rows']=$this->UserModel->get_packages_count($fetch_conditions_array);
		$config['per_page']=20;
		$config['uri_segment']=4;

		// Initialize paging with above parameters
		$this->pagination->initialize($config);
		
		//Create paging inks
		$paging_links=$this->pagination->create_links();
		
		$packages=$this->UserModel->get_packages_data($fetch_conditions_array,$config['per_page'],$start,'desc');
		// Recieve any messages to be shown, when package is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, users listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Packages','logo_link'=>$logo_link));
		$this->load->view('webmaster/packages_list',array('packages'=>$packages,'paging_links'=>$paging_links,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	function package_create()
	{	
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('package_type', 'Package Type', 'required|trim');
			$this->form_validation->set_rules('package_title', 'Package Title', 'required|min_length[2]|max_length[250]|trim');
			$this->form_validation->set_rules('package_summary', 'Package Summary', 'required|min_length[2]|max_length[250]|trim');
			
			$this->form_validation->set_rules('package_recurring_interval', 'Package Recurring Interval', 'required|trim');
			$this->form_validation->set_rules('package_price', 'Package Price', 'required|min_length[2]|max_length[250]|trim|numeric');
			$this->form_validation->set_rules('package_price_summary', 'Package Price Summary', 'required|trim');
			$this->form_validation->set_rules('package_min_contacts', 'Package Min Contacts', 'required|trim|integer');
			$this->form_validation->set_rules('package_max_contacts', 'Package Max Contacts', 'required|min_length[2]|max_length[250]|trim|integer');
			$this->form_validation->set_rules('quota_multiplier', 'Campaign Sending quota multiplier', 'required|trim|integer');
			$this->form_validation->set_rules('package_status', 'Package Status', 'required|trim');
			
			// To check form is validated
			if($this->form_validation->run()==true)
			{
				$input_array=array(
				'package_type'=>$this->input->post('package_type',true),
				'package_title'=>$this->input->post('package_title',true),
				'package_summary'=>$this->input->post('package_summary',true),
				'package_recurring_interval'=>$this->input->post('package_recurring_interval',true),
				'package_price'=>$this->input->post('package_price',true),
				'package_price_summary'=>$this->input->post('package_price_summary',true),
				'package_min_contacts'=>$this->input->post('package_min_contacts',true),
				'package_max_contacts'=>$this->input->post('package_max_contacts',true),
				'quota_multiplier'=>$this->input->post('quota_multiplier',true),
				'package_status'=>$this->input->post('package_status',true)
				);
				
				$this->UserModel->create_package($input_array);
				
				$this->messages->add('Package Added Successfully', 'success');
				redirect('webmaster/packages_manage/packages_list');
			}
		}
		
		// Recieve any messages to be shown
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, users listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Create New Package','logo_link'=>$logo_link));
		$this->load->view('webmaster/package_create',array('messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	function package_edit($id)
	{		
		$package_data=array();	
		$package_id=$id;
		if($this->input->post('action')=='submit')
		{
			$package_id=$this->input->post('package_id');
			
			// Validation rules are applied
			$this->form_validation->set_rules('package_type', 'Package Type', 'required|trim');
			$this->form_validation->set_rules('package_title', 'Package Title', 'required|min_length[2]|max_length[250]|trim');
			$this->form_validation->set_rules('package_summary', 'Package Summary', 'required|min_length[2]|max_length[250]|trim');
			
			$this->form_validation->set_rules('package_recurring_interval', 'Package Recurring Interval', 'required|trim');
			$this->form_validation->set_rules('package_price', 'Package Price', 'required|min_length[2]|max_length[250]|trim|numeric');
			$this->form_validation->set_rules('package_price_summary', 'Package Price Summary', 'required|trim');
			$this->form_validation->set_rules('package_min_contacts', 'Package Min Contacts', 'required|trim|integer');
			$this->form_validation->set_rules('package_max_contacts', 'Package Max Contacts', 'required|trim|integer');
			$this->form_validation->set_rules('quota_multiplier', 'Campaign Sending quota multiplier', 'required|trim|integer');
			$this->form_validation->set_rules('package_status', 'Package Status', 'required|trim');
			
			// To check form is validated
			if($this->form_validation->run()==true)
			{
				$input_array=array(
				'package_type'=>$this->input->post('package_type',true),
				'package_title'=>$this->input->post('package_title',true),
				'package_summary'=>$this->input->post('package_summary',true),
				'package_recurring_interval'=>$this->input->post('package_recurring_interval',true),
				'package_price'=>$this->input->post('package_price',true),
				'package_price_summary'=>$this->input->post('package_price_summary',true),
				'package_min_contacts'=>$this->input->post('package_min_contacts',true),
				'package_max_contacts'=>$this->input->post('package_max_contacts',true),
				'quota_multiplier'=>$this->input->post('quota_multiplier',true),
				'package_status'=>$this->input->post('package_status',true)
				);
				
				$conditions_array=array('package_id'=>$package_id);
				$this->UserModel->update_package($input_array,$conditions_array);
				
				$this->messages->add('Package Updated Successfully', 'success');
				redirect('webmaster/packages_manage/packages_list');
			}
			
			
			$package_data=$input_array;
		}
		
		if(!count($package_data))
		{
			$conditions_array=array('package_id'=>$package_id);
			$package_data_arr=$this->UserModel->get_packages_data($conditions_array);
			$package_data=$package_data_arr[0];
		}
		
	
		// Recieve any messages to be shown
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, users listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Edit Package','logo_link'=>$logo_link));
		$this->load->view('webmaster/package_edit',array('messages' =>$messages,'package_data'=>$package_data,'package_id'=>$package_id));
		$this->load->view('webmaster/footer');
	}
	
	function package_delete($id)
	{
		$conditions_array=array('package_id'=>$id);
		$this->UserModel->delete_package($conditions_array);
				
		$this->messages->add('Package Deleted Successfully', 'success');
		redirect('webmaster/packages_manage/packages_list');
	}

}
?>