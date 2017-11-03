<?php
class Activity_Log extends CI_Controller
{
	function __construct(){
		parent::__construct();
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');

		// Load the campaign model which interact with database
		$this->load->model('Activity_Model');
		# HTTPS/SSL enabled
		force_ssl();
	}
	
	function index(){
		$this->display();
	}
	/**
		Function to list campaign whose status is ready
	**/
	function display($start=0){
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/activity_log/display';
		$config['total_rows']=$this->Activity_Model->get_activity_log_count();
		$config['per_page']=100;
		$config['uri_segment']=4;
		// Initialize paging with above parameters
		$this->pagination->initialize($config);		
		//Create paging inks
		$paging_links=$this->pagination->create_links();
		$fetch_conditions_array=array();
		$activity_data_array=$this->Activity_Model->get_activity_log($fetch_conditions_array,$config['per_page'],$start);
		$i=0;
		foreach($activity_data_array as $activity){
			$qry="select campaign_title from red_email_campaigns WHERE campaign_id='".$activity['campaign_id']."'";	
			$campaign_qry=$this->db->query($qry);
			$campaign=$campaign_qry->result_array();	#Fetch resut
			$activity_data_array[$i]['campaign_title']=$campaign[0]['campaign_title'];		
			if($activity['contact_list_type']==2 or $activity['contact_list_type']==3){
				$arrGetImportFileDetails = $this->getFileDetails($activity['campaign_id']);
				$activity_data_array[$i]['is_undone'] = $arrGetImportFileDetails[0]['is_undone'];
				$activity_data_array[$i]['list_id'] = $arrGetImportFileDetails[0]['list_id'];
			}	
			$i++;
		}
		// Recieve any messages to be shown, when campaign is added or updated
		 
		$messages=$this->messages->get();
		if($_POST['mode']=='search' AND !isset($_POST['btn_cancel'])){
			$contacts_array['date']=$_POST['date'];
			$contacts_array['username']=$_POST['username'];
			$contacts_array['campaign_name']=$_POST['campaign_name'];
		}
		$logo_link="webmaster/dashboard_stat";
		#Get shoreten url 
		$shorten_url=get_shorten_url();
		//Loads header, users listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Activity Log','logo_link'=>$logo_link));
		$this->load->view('webmaster/activity_log_list',array('activity_logs'=>$activity_data_array,'paging_links'=>$paging_links,'messages' =>$messages,'contacts'=>$contacts_array,'shorten_url'=>$shorten_url));
		$this->load->view('webmaster/footer');		
	}
	function getFileDetails($import_batch_id){
		$arrReturn = array();
			if($import_batch_id > 0){
				$qry="select is_undone, list_id from   `red_contact_import_batch`  WHERE `import_batch_id`='$import_batch_id'";	
				$batch_qry=$this->db->query($qry);
				if($batch_qry->num_rows() > 0)
				$arrReturn = $batch_qry->result_array();
			}
		return $arrReturn ;	
	}
	function undo_import($mid, $import_batch_id ){
		if($import_batch_id > 0){
			$this->db->trans_begin();

			 $this->db->query("delete ss.* from red_email_subscription_subscriber ss inner join `red_email_subscribers` s on ss.subscriber_id=s.subscriber_id where s.`subscriber_created_by`='$mid' and s.`import_batch_id`='$import_batch_id'");
			$this->db->query("delete from red_email_subscribers where `subscriber_created_by`='$mid' and `import_batch_id`='$import_batch_id'");
			$this->db->query("Update `red_contact_import_batch` set `is_undone`=1 where `import_batch_id`='$import_batch_id'");

			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				//$msg = "Contacts could not be reverted. Please try again.";
				$msg = 'err';
			}else{
				$this->db->trans_commit();
				//$msg = "Contacts reverted.";
				$msg = 'success';
			}

		}		
		 echo $msg;
		#$this->session->set_flashdata(array('messages'=> $msg));
		#redirect('webmaster/activity_log/');
	}
	function export_file($memberid,$file_name=""){
		$this->load->helper('download');
		$user_dir = $memberid % 1000;
		$upload_path= $this->config->item('user_private').'/'.$user_dir.'/'.$memberid.'/csv_files/'.$file_name.".csv";
		$data = file_get_contents("$upload_path"); // Read the file's contents
		$name = $file_name.".csv";
		force_download($name, $data);
	}
}
?>