<?php
class Seo extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		// Load the seo model which interact with database
		$this->load->model('SeoModel');
		
	}
	function authenticate(){
		if($this->input->post('seosub')!='Submit'){
			$msg = 'Not Authenticated!';
			$this->load->view('seo/seo_authenticate', array('msg'=>$msg));
		}elseif($this->input->post('seoguy') == 'AmitSharma'){
			$this->session->set_userdata('SEOMASTER', 'AmitSharma');		 
			redirect('/seo/index');
		}else{			
			redirect('/seo/authenticate');
		}
	}
	
	function index(){
		if($this->session->userdata('SEOMASTER')!= 'AmitSharma')redirect('/seo/authenticate');
		$this->seo_list();
	}
	function seo_list(){
	if($this->session->userdata('SEOMASTER')!= 'AmitSharma')redirect('/seo/authenticate');
		$seo_array=$this->SeoModel->get_seo_data(array('is_delete'=>0));
		// Recieve any messages to be shown
		$messages=$this->messages->get();
		//Loads  seo listing view.
		$this->load->view('seo/seo_list',array('seo_array'=>$seo_array,'messages' =>$messages));
	}
	function create_seo(){
	if($this->session->userdata('SEOMASTER')!= 'AmitSharma')redirect('/seo/authenticate');
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
			
			$input_array = array(
				'page' => $this->input->post('page',true),
				'title' => $this->input->post('title',true),
				'keyword' => $this->input->post('keyword',true),
				'description' => $this->input->post('description',true),
				'h1' => $this->input->post('h1',true),
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
					redirect('seo/seo_list');
				}
			}
			
			$seo_data_array=$input_array;
		}
		
		// Recieve any messages to be shown
		$messages=$this->messages->get();
		$this->load->view('seo/seo_create',array('seo'=>$seo_data_array,'messages' =>$messages));
	}
	
	function seo_edit($id=0){
	if($this->session->userdata('SEOMASTER')!= 'AmitSharma')redirect('/seo/authenticate');
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
			
			$input_array = array(
				'page' => $this->input->post('page',true),
				'title' => $this->input->post('title',true),
				'keyword' => $this->input->post('keyword',true),
				'description' => $this->input->post('description',true),
				'h1' => $this->input->post('h1',true),
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
					redirect('seo/seo_list');
				}
			}			
			$seo_data_array=$input_array;
		}
		
		if(!count($seo_data_array)) {
			$fetch_conditions_array=array('id'=>$id);
			$seo_data_array=$this->SeoModel->get_seo_data($fetch_conditions_array,$config['per_page']);
			$seo_data_array=$seo_data_array[0];
		}
		// Recieve any messages to be shown, when campaign is added or updated
		$messages=$this->messages->get();
		
		//Loads seo edit view.
		$this->load->view('seo/seo_edit',array('seo'=>$seo_data_array,'messages' =>$messages));
	}
	function seo_delete($id=0){
	if($this->session->userdata('SEOMASTER')!= 'AmitSharma')redirect('/seo/authenticate');
		// Deletes page according to  ID
		$this->SeoModel->update_seo(array('is_delete'=>1),array('id'=>$id));

		// Assign  success message by message class
		$this->messages->add('Page deleted successfully', 'success');

		// Redirect to listing of Pages
		redirect('seo/seo_list');
	}
	function logout(){
		$this->session->sess_destroy();
		
		//Redirect to Login page
		redirect('seo/index');
	}
}
/* End of file */
?>