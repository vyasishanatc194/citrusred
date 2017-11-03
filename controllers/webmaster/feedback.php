<?php
class Feedback extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		// Load the template category model which interact with database
		$this->load->model('Feedback_Model');
		$this->upload_path=substr(FCPATH,0,strrpos(FCPATH,DIRECTORY_SEPARATOR));
		# HTTPS/SSL enabled
		force_ssl();

	}
	
	function index()
	{
		$this->feedback_list();
	}
	/**
		Function feedback_list to display list of user feedback
	**/
	function feedback_list($start=0)
	{
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');
		$fetch_conditions_array=array('is_delete'=>0);
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/feedback/feedback_list';
		$config['total_rows']=$this->Feedback_Model->get_feedback_count($fetch_conditions_array);
		$config['per_page']=10;
		$config['uri_segment']=4;

		// Initialize paging with above parameters
		$this->pagination->initialize($config);
		
		//Create paging inks
		$paging_links=$this->pagination->create_links();
		
		$feedback_data_array=$this->Feedback_Model->get_feedback_data($fetch_conditions_array,$config['per_page'],$start);
		
		// Recieve any messages to be shown, when category is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, template header  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Feedback','logo_link'=>$logo_link));
		$this->load->view('webmaster/feedback_list',array('feedbacks'=>$feedback_data_array,'paging_links'=>$paging_links,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	/**
		Function message to display feedback message
	**/
	function message($id=0){
		if($this->session->userdata('webmaster_id')==''){
			echo "<div style=\"margin:20px;width:240px;\">Your Session seems to have expired. Please try refreshing the page to login again.</div>";
		}else{
			$fetch_conditions_array=array('id'=>$id,'is_delete'=>0);
			$feedback_data_array=$this->Feedback_Model->get_feedback_data($fetch_conditions_array);
			$this->load->view('webmaster/feedback_message',array('message'=>$feedback_data_array[0]['message']));
		}
	}
	/**
		Function delete to delete feedback
	**/
	function delete($id=0){
		if($this->session->userdata('webmaster_id')==''){
			echo "<div style=\"margin:20px;width:240px;\">Your Session seems to have expired. Please try refreshing the page to login again.</div>";
			redirect('webmaster/feedback/feedback_list');
		}else{
			$this->Feedback_Model->delete_feedback(array('id'=>$id));		
			$this->messages->add('Feedback deleted successfully', 'success');
			redirect('webmaster/feedback/feedback_list');
		}
	}
}
?>