<?php
class Database_backup extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');
			
		# HTTPS/SSL enabled
		force_ssl();
	
	}
	function index(){
		// To check if form is submitted
		if($this->input->post('action')=='submit'){
			set_time_limit(0);
			// Load the DB utility class
			$this->load->dbutil();
			
			$backup =& $this->dbutil->backup(); 
			// Load the file helper and write the file to your server
			$file_name=time()."_backup";
			$this->load->helper('file');
			write_file("$file_name.gz", $backup); 

			// Load the download helper and send the file to your desktop
			$this->load->helper('download');
			force_download("$file_name.gz", $backup);
		}
		// Recieve any messages to be shown, when campaign is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, campaign and footer view.
		$this->load->view('webmaster/header',array('title'=>'Database Backup','logo_link'=>$logo_link));
		$this->load->view('webmaster/database_backup',array('messages' =>$messages));
		$this->load->view('webmaster/footer');
		
	}
}
/* End of file */
?>