<?php
class Pmta_log extends CI_Controller {
 
	// num of records per page
	private $limit = 100;
 
	// empty array for search terms
	var $terms     = array();		
 
	function __construct(){
		parent::__construct();
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');
			
		$this->load->model('webmaster/pmta_log_model','',TRUE); 
		$this->load->helper('url');	
		$this->load->library('pagination');
		# HTTPS/SSL enabled
		force_ssl();		
		$this->output->enable_profiler(false);
	}
	
	 
 
	function index(){
	
	
		// offset
		$uri_segment = 4;
 
		// return third URI segment, if no third segment returns '' 
		$offset = $this->uri->segment($uri_segment,'');			
		$offset = ($offset == '')?0:$offset ;
		
		$sqlCounter = "select count(`logid`) as totRec from `red_pmtalog` where 1";
		$sql = "select * from `red_pmtalog` where 1";
		$sqlWhere = '';
		// gets total URI segments
		$total_seg          = $this->uri->total_segments();			 
		 
		if($this->input->post('resetThisForm') == 'notok'){	
			// assign posted valued
			$this->session->set_userdata('type',trim($this->input->post('type')));
			$this->session->set_userdata('rcpt',trim($this->input->post('rcpt')));
			$this->session->set_userdata('envId',trim($this->input->post('envId')));
			$this->session->set_userdata('jobId',trim($this->input->post('jobId')));
			$this->session->set_userdata('dsnDiag',trim($this->input->post('dsnDiag')));
			 
			$sqlWhere .= ($this->session->userdata('type') != '')?" and type='".$this->session->userdata('type')."'":"";
			$sqlWhere .= ($this->session->userdata('rcpt') != '')?" and rcpt like'%".$this->session->userdata('rcpt')."%'":"";
			$sqlWhere .= ($this->session->userdata('envId') != '')?" and envId='".$this->session->userdata('envId')."'":"";
			$sqlWhere .= ($this->session->userdata('jobId') != '')?" and jobId='".$this->session->userdata('jobId')."'":"";
			$sqlWhere .= ($this->session->userdata('dsnDiag') != '')?" and dsnDiag like'%".$this->session->userdata('jobId')."%'":"";
				
			$this->session->set_userdata("where_clause",$sqlWhere);
			
		}elseif($this->input->post('resetThisForm') == 'ok'){
			$this->session->unset_userdata('type');		
			$this->session->unset_userdata('rcpt');		
			$this->session->unset_userdata('envId');		
			$this->session->unset_userdata('jobId');		
			$this->session->unset_userdata('dsnDiag');		
			$this->session->unset_userdata('where_clause');		
		}
		
		if($this->session->userdata('where_clause') != ''){
			$sql .= $this->session->userdata('where_clause');
			$sqlCounter .= $this->session->userdata('where_clause');
			$data['type'] = $this->session->userdata('type');
			$data['rcpt'] = $this->session->userdata('rcpt');
			$data['envId'] = $this->session->userdata('envId');
			$data['jobId'] = $this->session->userdata('jobId');
			$data['dsnDiag'] = $this->session->userdata('dsnDiag');
		}else{			
			$data['type'] 	= '';
			$data['rcpt'] 	= '';
			$data['envId'] 	= '';
			$data['jobId']	= '';		
			$data['dsnDiag']	= '';		
		}
			 
		
 
 
 
			//if($total_seg <= 4){			
				
				$config['total_rows'] = $this->pmta_log_model->count_all_records($sqlCounter);
				
				$parents = $this->pmta_log_model->get_search_pagedlist($sql, $this->limit, $offset)->result();	 
				 
			//}
	 
		$data['total_rows'] = $config['total_rows']; 
 
		//$config['base_url'] = base_url().'webmaster/pmta_log/index/'.$keys.'/';
		$config['base_url'] = base_url().'webmaster/pmta_log/index/';
  		$config['per_page'] = $this->limit;
		$config['uri_segment'] = $uri_segment;
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
 
		// generate table data
		$this->load->library('table');
		$this->table->set_empty("&nbsp;");
 
		$heading = array('No','Type', 'Time-Logged','Sending-IP','Recipient','DSN Action/Status','DSN Message','Bounce-Reason','Contact-Id','Campaign-Id');				
 
		$this->table->set_heading($heading);
		$i = 0 + $offset;
		foreach ($parents as $parent){			
			$this->table->add_row(++$i, $parent->type, $parent->timeLogged,$parent->dlvSourceIp, $parent->rcpt, $parent->dsnAction."<br/>".$parent->dsnStatus, $parent->dsnDiag, $parent->bounceCat, $parent->jobId, $parent->envId);
		}
		
		
		$tmpl = array ( 'table_open'          => '<table border="1" cellpadding="4" cellspacing="1" class="tbl_listing" width="100%">');

		$this->table->set_template($tmpl);
		$data['table'] = $this->table->generate();		
 
		// load view
		$this->load->view('webmaster/header',array('title'=>'View PMTA Log','logo_link'=>'webmaster/pmta_log/'));
		$this->load->view('webmaster/pmta_log', $data);		
		$this->load->view('webmaster/footer');
		
		
	}
	
}
?>