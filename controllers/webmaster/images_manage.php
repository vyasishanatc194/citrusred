<?php
class Images_Manage extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');

		
		# HTTPS/SSL enabled
		force_ssl();

	}
	
	function index()
	{
		$this->images_list();
	}
	
	function images_list($start=0)
	{
		$fetch_conditions_array=array('is_deleted'=>0);
		
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/packages_manage/packages_list';
		$config['total_rows']=$this->UserModel->get_packages_count($fetch_conditions_array);
		$config['per_page']=10;
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
		$this->load->view('webmaster/packages_list',array('packages'=>$packages,'paging_links'=>$paging_links,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	  function image_upload()
	  {
		$upload_path=substr(FCPATH,0,strrpos(FCPATH,'/'));
		$upload_path= $upload_path.'/webappassets/image_library/';
		
        $config['upload_path'] = $upload_path; // server directory
        $config['allowed_types'] = 'gif|jpg|png|jpeg'; 
        $config['max_size']    = '1000'; // in kb
       // $config['max_width']  = '1024';
       // $config['max_height']  = '768';
        
        $this->load->library('upload', $config);
        $this->load->library('Multi_upload');
		
		$this->upload->initialize($config);
		
        $files = $this->multi_upload->go_upload();
        
        
        if ( ! $files )        
        {
            $error = array('error' => $this->upload->display_errors());
            $this->load->view('webmaster/image_upload', $error);
        }    
        else
        {
            $data = array('upload_data' => $files);
            $this->load->view('webmaster/image_upload_success', $data);
        }
     }    
	
	
	
	
	function image_delete($id)
	{
		$conditions_array=array('package_id'=>$id);
		$this->UserModel->delete_package($conditions_array);
				
		$this->messages->add('Package Deleted Successfully', 'success');
		redirect('webmaster/packages_manage/packages_list');
	}	



}
?>