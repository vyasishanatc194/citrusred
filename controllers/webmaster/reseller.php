<?php
class Reseller extends CI_Controller
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
	
	
	function index($start=0){		
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/reseller/index';
		$config['total_rows']=$this->db->query("select count(id)ct from red_member_referrer where is_deleted=0")->row()->ct ;
		$config['per_page']=20;
		$config['uri_segment']=4;

		// Initialize paging with above parameters
		$this->pagination->initialize($config);
		
		//Create paging inks
		$paging_links=$this->pagination->create_links();
		$condition = array('is_deleted'=>0);
		$resellers=$this->db->get_where('red_member_referrer',$condition,$config['per_page'],$start)->result_array();		
		
		// Recieve any messages to be shown, when package is added or updated
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, users listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Reseller','logo_link'=>$logo_link));
		$this->load->view('webmaster/reseller_list',array('resellers'=>$resellers,'paging_links'=>$paging_links,'messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	
	function reseller_create(){		
		if($this->input->post('action')=='submit'){
			// Validation rules are applied
			$this->form_validation->set_rules('referrer_name', 'Referrer name', 'required|trim');
			$this->form_validation->set_rules('referrer_string', 'Referrer string', 'required|min_length[4]|max_length[40]trim');
			$this->form_validation->set_rules('commission', 'Commission', 'required|trim');
			$this->form_validation->set_rules('commission_months', 'No. of months', 'required|trim');
			
			// To check form is validated
			if($this->form_validation->run()==true){
				$referrer_name = $this->input->post('referrer_name',true);
				$referrer_email = $this->input->post('referrer_email',true);
				$referrer_string = $this->input->post('referrer_string',true);
				$commission = $this->input->post('commission',true);
				$commission_type = $this->input->post('commission_type',true);
				$commission_months = $this->input->post('commission_months',true);
			
				$sqlAddReseller = "insert into red_member_referrer set referrer_name='$referrer_name', referrer_email='$referrer_email', referrer_string='$referrer_string', commission='$commission', commission_type='$commission_type',commission_months='$commission_months' ";
				$this->db->query($sqlAddReseller);
				 
				$this->messages->add('Reseller added successfully', 'success');
				redirect('webmaster/reseller');
			}
		}
		
		// Recieve any messages to be shown
		$messages=$this->messages->get();
		$logo_link="webmaster/reseller";
		//Loads header, users listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Create New Reseller','logo_link'=>$logo_link));
		$this->load->view('webmaster/reseller_create',array('messages' =>$messages));
		$this->load->view('webmaster/footer');
	}
	function report(){
		$rsReferrer = $this->db->query("select referrer_name from red_member_referrer where is_deleted=0");
		$arrReferrer = $rsReferrer->result_array();
		$rsReferrer->free_result();
		$arrReferrerName = array(''=>'Select Referrer');
		$arrReseller = array();
		$arrSelectedReseller = array();
		$mid =  '';
		foreach($arrReferrer as $r){
			 $thisRName = $r['referrer_name'];
			$arrReferrerName[$thisRName] = "$thisRName";
		}
		
		$fdate = date('Y-m-d', strtotime('7 days ago'));
		$tdate = date('Y-m-d', time());
	
		if($this->input->post('btn_search')=='Search' and trim($this->input->post('referrer',true)) != ''){			
				$referrer = trim($this->input->post('referrer',true));
				$fdate = $this->input->post('from_date',true);
				$tdate = $this->input->post('to_date',true);
				$rseller_clause = " and ls_site_id='$referrer' ";
				$totAmount = 0;
				$j =0;	
			$rsReseller = $this->db->query("select member_id,member_username,referrer_name,commission,commission_type, commission_months from red_members m inner join red_member_referrer r on m.ls_site_id= r.referrer_name where m.is_deleted=0 and r.is_deleted=0 and r.commission > 0 $rseller_clause");
			//echo $this->db->last_query();
			$arrReseller = $rsReseller->result_array();
			$rsReseller->free_result();
			for($i=0; $i < count($arrReseller); $i++){
				$mid = $arrReseller[$i]['member_id'];
				
				$rsAmount = $this->db->query("select amount_paid,transaction_date from red_member_transactions where status='SUCCESS' and amount_paid > 0 and gateway !='ADMIN' and user_id ='$mid'  and transaction_date between '$fdate 00:00:00' and '$tdate 23:59:59' order by transaction_id desc");	
				//echo $this->db->last_query();echo"<br/>";
				
				if($rsAmount->num_rows() > 0){
									
					foreach($rsAmount->result_array() as $r){						 
							$arrSelectedReseller[$j]['transaction_amount'] = $r['amount_paid'];
							$arrSelectedReseller[$j]['transaction_date'] = $r['transaction_date'];
							$arrSelectedReseller[$j]['member_id'] = $mid;
							$arrSelectedReseller[$j]['member_username'] = $arrReseller[$i]['member_username'];
							$arrSelectedReseller[$j]['commission'] = $arrReseller[$i]['commission'];
							$arrSelectedReseller[$j]['commission_type'] = $arrReseller[$i]['commission_type'];
							$arrSelectedReseller[$j]['commission_months'] = $arrReseller[$i]['commission_months'];
							$totAmount += 1 * $r['amount_paid']; 
							$j++;
						
					}             
				}
				$rsAmount->free_result();	
			}
			// echo"<pre>";
			// print_r($arrSelectedReseller);
			// echo"</pre>";
			
		}
		// Recieve any messages to be shown
		$messages=$this->messages->get();
		$logo_link="webmaster/reseller";
		//Loads header, users listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Reseller-Report','logo_link'=>$logo_link));
		$this->load->view('webmaster/reseller_report',array('messages' =>$messages,'arrReferrerName'=>$arrReferrerName,'referrer'=>$referrer,'arrReseller'=>$arrSelectedReseller, 'totAmount'=>$totAmount, 'fdate'=>$fdate,'tdate'=>$tdate));
		$this->load->view('webmaster/footer');	
	}
}
?>