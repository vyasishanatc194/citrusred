<?php
/**
  *	Controller class for pmta
  *	It have controller functions for pmta management.
 */
class Pmtalogs extends CI_Controller
{
	//Define hard bounce
	var $hard_bounce_array = array('bad-domain', 'bad-mailbox', 'inactive-mailbox');
	// 'inactive-mailbox' added and 'quota-issues' removed by pravinjha@gmail.com 
	private $confg_arr = array();
	
	/**
	*	Contructor for controller.
	*	It checks user session and redirects user if not logged in
	*/
	function __construct()
    {
        parent::__construct();		
		// Load pmta model class which handles database interaction
		$this->load->model('ConfigurationModel');
		$this->load->model('newsletter/Pmta_Model');		
		$this->load->model('newsletter/Emailreport_Model');		 
		$this->load->model('newsletter/Subscriber_Model');
		$this->load->model('newsletter/Campaign_Model');
		$this->load->model('UserModel');
		
		$this->load->helper('admin_notification');
		$this->load->helper('transactional_notification');
		
		$this->confg_arr=$this->ConfigurationModel->get_site_configuration_data_as_array();
		$this->session->set_userdata('member_time_zone', 'GMT');
		date_default_timezone_set('GMT');
	}
	function acct0(){		
			set_time_limit(0); 				 
				// If stopped by admin then dont send any campaign
			if( trim($this->confg_arr['continue_pmtalog_import']) !="1"){
				exit;
			}
			//Check cronjob status: completed or working		
			if($this->confg_arr['pmta_cronjob_status'] == 'working'){
				exit;
			}else{  
				$newStatus = ($this->confg_arr['pmta_cronjob_status'] == 'even')?'working':'even';
				// update cronjob status 
				$this->ConfigurationModel->update_site_configuration(array('config_value'=>$newStatus),array('config_name'=>'pmta_cronjob_status'));
				$this->ConfigurationModel->update_site_configuration(array('config_value'=>date("Y-m-d H:i:s", time())),array('config_name'=>'pmtalog_cron_status_change_time'));
				$source_dir = $this->config->item('pmta_logs');
				$destination_dir = $this->config->item('pmta_archives');
				// lists files only for the directory which this script is run from
				$arrFiles = scandir($source_dir);
				$cnt=0;
				foreach( $arrFiles as $f){
					if($cnt > 5)break;
					if(is_dir($file)){
						continue;				
					}elseif((substr($f, 0, 4) == 'acct') && (is_readable($filename)) ){	
						$intFileCount = intval(substr($f,16,4));
						$filename = $source_dir.$f;
						
						if(($intFileCount % 2 == 0)&& ($newStatus == 'even')){				// For even files							 	
								$cnt++;	
								$this->importCsv($f);					
								// If we copied this successfully, delete it
								if (copy($source_dir.$file, $destination_dir.$file)) {
									@unlink($source_dir.$file);
								}
								
						}elseif(($intFileCount % 2 == 1)&& ($newStatus == 'working')){ 		// For odd files							 
								$cnt++;	
								$this->importCsv($f);					
								// If we copied this successfully, delete it
								if (copy($source_dir.$file, $destination_dir.$file)) {
									@unlink($source_dir.$file);
								}
								
						}						
					}
				}
				//update cronjob status to completed
				$this->ConfigurationModel->update_site_configuration(array('config_value'=>'completed'),array('config_name'=>'pmta_cronjob_status'));
				$this->ConfigurationModel->update_site_configuration(array('config_value'=>date("Y-m-d H:i:s", time())),array('config_name'=>'pmtalog_cron_status_change_time'));	
			}
	}  
 
	/**
	 *	Function acct, 'acct' controller function to import pmtalog for delivered campaigns to database.	 
	 */
	function acct_odd(){		
		set_time_limit(0); 				 
		// If stopped by admin then dont send any campaign
		if( trim($this->confg_arr['continue_pmtalog_import']) !="1"){
			exit;
		}
		//Check cronjob status: completed or working		
		if($this->confg_arr['pmtalog_import_acct_odd'] == 'working'){
			exit;
		}else{
			// update cronjob status to completed
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>'working'),array('config_name'=>'pmtalog_import_acct_odd'));
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>date("Y-m-d H:i:s", time())),array('config_name'=>'pmtalog_import_acct_odd_change_time'));
			  
			$source_dir = $this->config->item('pmta_logs');
			$destination_dir = $this->config->item('pmta_archives');
			
			// lists files only for the directory which this script is run from
			$arrFiles = scandir($source_dir);
			$cnt=0;
			foreach( $arrFiles as $f){	
				$intFilePrefixNo = intval(substr($f,16,4));
				if($cnt > 20)break;
				if(is_dir($source_dir.$f)){
					continue;				
				}elseif((substr($f, 0, 4) == 'acct') && (is_readable($source_dir.$f)) ){						
					if($intFilePrefixNo % 2 == 1){				// For odd files							 	
						$cnt++;		
						
						$this->importCsv($f);					
						// If we copied this successfully, delete it
						if (copy($source_dir.$f, $destination_dir.$f)) {
							@unlink($source_dir.$f);
							//echo "<br> imported : $source_dir.$f";
						}
						
					}	
				}
			}		

			//update cronjob status to completed
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>'completed'),array('config_name'=>'pmtalog_import_acct_odd'));
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>date("Y-m-d H:i:s", time())),array('config_name'=>'pmtalog_import_acct_odd_change_time'));
		}
	}
	/**
	 *	Function acct, 'acct' controller function to import pmtalog for delivered campaigns to database.	 
	 */
	function acct_even(){		
		set_time_limit(0); 				 
		// If stopped by admin then dont send any campaign
		if( trim($this->confg_arr['continue_pmtalog_import']) !="1"){
			exit;
		}
		//Check cronjob status: completed or working		
		if($this->confg_arr['pmtalog_import_acct_even'] == 'working'){
			exit;
		}else{
			// update cronjob status to completed
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>'working'),array('config_name'=>'pmtalog_import_acct_even'));
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>date("Y-m-d H:i:s", time())),array('config_name'=>'pmtalog_import_acct_even_change_time'));
			  
			$source_dir = $this->config->item('pmta_logs');
			$destination_dir = $this->config->item('pmta_archives');
			// lists files only for the directory which this script is run from
			$arrFiles = scandir($source_dir);			
			$cnt=0;
			foreach( $arrFiles as $f){				
				$intFilePrefixNo = intval(substr($f,16,4));
				if($cnt > 20)break;
				if(is_dir($source_dir.$f)){
					continue;				
				}elseif((substr($f, 0, 4) == 'acct') && (is_readable($source_dir.$f)) ){						
					if($intFilePrefixNo % 2 == 0){				// For even files							 	
						$cnt++;								
						$this->importCsv($f);					
						// If we copied this successfully, delete it
						if (copy($source_dir.$f, $destination_dir.$f)) {
							@unlink($source_dir.$f);
							//echo "<br> imported : $source_dir.$f";
						}						

					}	
				}
			}

			//update cronjob status to completed
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>'completed'),array('config_name'=>'pmtalog_import_acct_even'));
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>date("Y-m-d H:i:s", time())),array('config_name'=>'pmtalog_import_acct_even_change_time'));
		}
	}
	
 
	/**
	 *	Function acct, 'acct' controller function to import pmtalog for delivered campaigns to database.	 
	 */
	function acct(){		
		set_time_limit(0); 				 
		// If stopped by admin then dont send any campaign
		if( trim($this->confg_arr['continue_pmtalog_import']) !="1"){
			exit;
		}
		//Check cronjob status: completed or working		
		if($this->confg_arr['pmta_cronjob_status'] == 'working'){
			exit;
		}else{
			// update cronjob status to completed
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>'working'),array('config_name'=>'pmta_cronjob_status'));
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>date("Y-m-d H:i:s", time())),array('config_name'=>'pmtalog_cron_status_change_time'));
			  
			$source_dir = $this->config->item('pmta_logs');
			$destination_dir = $this->config->item('pmta_archives');
			// lists files only for the directory which this script is run from
				
			if(!($dp = opendir($source_dir))) die("Cannot open $source_dir.");
			$cnt=0;
			
			while($file = readdir($dp)){
				if($cnt > 5)break;
				if(is_dir($file)){
					continue;				
				}elseif(substr($file, 0, 4) == 'acct'){	
					
					$filename=$source_dir.$file;
					if (is_readable($filename)) {	
						$cnt++;	
						$this->importCsv($file);					
						// If we copied this successfully, delete it
						if (copy($source_dir.$file, $destination_dir.$file)) {
							@unlink($source_dir.$file);
						}
					}
				}
			}
			
			closedir($dp);
			//update cronjob status to completed
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>'completed'),array('config_name'=>'pmta_cronjob_status'));
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>date("Y-m-d H:i:s", time())),array('config_name'=>'pmtalog_cron_status_change_time'));
		}
	}
	/**
	*	Function acct, 'fbl' controller function to import pmtalog for FBLs to database.	 
	*/
	function fbl(){		
		set_time_limit(0); 		
		// If stopped by admin then dont send any campaign
		if( trim($this->confg_arr['continue_pmtalog_import']) !="1"){
			exit;
		}
		//Check cronjob status: completed or working		
		if($this->confg_arr['pmtalog_import_fbl'] == 'working'){
			exit;
		}else{			
			// update cronjob status to completed
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>'working'),array('config_name'=>'pmtalog_import_fbl'));
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>date("Y-m-d H:i:s", time())),array('config_name'=>'pmtalog_import_fbl_change_time'));
			  
			$source_dir = $this->config->item('pmta_logs');
			$destination_dir = $this->config->item('pmta_archives');
			// lists files only for the directory which this script is run from
				
			if(!($dp = opendir($source_dir))) die("Cannot open $source_dir.");
			while($file = readdir($dp)){
				if(is_dir($file)){
					continue;				
				}elseif(substr($file, 0, 9) == 'fblreport'){				
					$filename=$source_dir.$file;
					if (is_readable($filename)) {						 
						$this->importCsv($file);					
						// If we copied this successfully, delete it
						if (copy($source_dir.$file, $destination_dir.$file)) {
							@unlink($source_dir.$file);
						}
					}
				}
			}
			closedir($dp);
			//update cronjob status to completed
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>'completed'),array('config_name'=>'pmtalog_import_fbl'));
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>date("Y-m-d H:i:s", time())),array('config_name'=>'pmtalog_import_fbl_change_time'));
		}
	}
	/**
	*	Function acct, 'bounced' controller function to import pmtalog for bounced campaigns to database.	 
	*/
	function bounced(){		
		set_time_limit(0); 		
		// If stopped by admin then dont send any campaign
		if( trim($this->confg_arr['continue_pmtalog_import']) !="1"){
			exit;
		}
		//Check cronjob status: completed or working		
		if($this->confg_arr['pmtalog_import_bounced'] == 'working'){
			exit;
		}else{			
			// update cronjob status to completed
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>'working'),array('config_name'=>'pmtalog_import_bounced'));
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>date("Y-m-d H:i:s", time())),array('config_name'=>'pmtalog_import_bounced_change_time'));
			  
			$source_dir = $this->config->item('pmta_logs');
			$destination_dir = $this->config->item('pmta_archives');
			// lists files only for the directory which this script is run from
				
			if(!($dp = opendir($source_dir))) die("Cannot open $source_dir.");
			while($file = readdir($dp)){
				if(is_dir($file)){
					continue;				
				}elseif(substr($file, 0, 12) == 'bouncereport'){				
					$filename=$source_dir.$file;
					if (is_readable($filename)) {						 
						$this->importCsvBounced($file);					
						// If we copied this successfully, delete it
						if (copy($source_dir.$file, $destination_dir.$file)) {
							@unlink($source_dir.$file);
						}
					}
				}
			}
			closedir($dp);
			//update cronjob status to completed
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>'completed'),array('config_name'=>'pmtalog_import_bounced'));
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>date("Y-m-d H:i:s", time())),array('config_name'=>'pmtalog_import_bounced_change_time'));
		}
	}
	
	 
	/**
	 *	Function Create, 'Create' controller function to import pmtalog to database.	 
	 */
	function create(){
		
		set_time_limit(0); 
		$config_arr=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'continue_pmtalog_import'));
		
		// If stopped by admin then dont send any campaign
		if( trim($config_arr[0]['config_value']) !="1"){
			exit;
		}
		//Check cronjob status: completed or working
		$cronjob_status=$this->check_cronjob_status();
		if($cronjob_status=="working"){
			exit;
		}else{	 
						
			#update cronjob status to completed
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>'working'),array('config_name'=>'pmta_cronjob_status'));
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>date("Y-m-d H:i:s", time())),array('config_name'=>'pmtalog_cron_status_change_time'));
			  
			$source_dir = $this->config->item('pmta_logs');
			$destination_dir = $this->config->item('pmta_archives');
			// lists files only for the directory which this script is run from
				
			if(!($dp = opendir($source_dir))) die("Cannot open $source_dir.");
			while($file = readdir($dp)){
				if(is_dir($file)){
					continue;				
				}elseif((substr($file, 0, 12) == 'bouncereport') or (substr($file, 0, 9) == 'fblreport') or (substr($file, 0, 4) == 'acct')){				
					$filename=$source_dir.$file;
					if (is_readable($filename)) {						 
						$this->importCsv($file);					
						// If we copied this successfully, delete it
						if (copy($source_dir.$file, $destination_dir.$file)) {
							@unlink($source_dir.$file);
						}
					}
				}
			}
			closedir($dp);
			//update cronjob status to completed
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>'completed'),array('config_name'=>'pmta_cronjob_status'));
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>date("Y-m-d H:i:s", time())),array('config_name'=>'pmtalog_cron_status_change_time'));
		}
	}
	
	
	function importCsv($file=""){
		$file_name= $this->config->item('pmta_logs').$file;
		if (($handle = fopen($file_name, "r")) !== FALSE){
		//$handle = fopen($file_name, "r");
		$header_array=array();
		$data_array=array();
		$bounce_counter_array=array();
		$arrFBLCampaigns=array();		
		$bounce_array=array();
		$utc_str = gmdate("Y-m-d H:i:s", time());	
        //while (($data = fgetcsv($handle, 1000, ",")) !== FALSE){			
            while (($data = fgetcsv($handle,1000,",")) !== FALSE){			
                  

			// Get Associative Array
			$number_of_fields = count($data);			 
			if($data[0]=='type'){
			//Header line 
				for ($c=0; $c < $number_of_fields; $c++)
					$header_array[$c] = $data[$c]; 									
			}else{
				for ($c=0; $c < $number_of_fields; $c++)
					$data_array[$header_array[$c]] = $data[$c]; 				
			}	
			
			
			if(strpos($file, "bouncereport")!==false){	//Check bounce report			
				if($data_array['type']=='b' or $data_array['type']=='rb'){
				
				$bounce_status = $data_array['dsnStatus'];
				$bounce_detail = $data_array['dsnDiag'];
				// Get column which gives [contact-id]-[campaign-id]-[system-name]
				$extra_header_arr = explode('-',$data_array['header_x-fblid']);
				if(count($extra_header_arr) == 3 and $extra_header_arr[2]==CAMPAIGN_HEADER_SUFFIX){
				 $jobId = $extra_header_arr[0];
				 $envId = $extra_header_arr[1];
				
					$update_array=array('type'=>$data_array['type'], 'timeLogged'=>$data_array['timeLogged'], 'orig'=>$data_array['orig'], 'rcpt'=>$data_array['rcpt'], 
										'dsnAction'=>$data_array['dsnAction'], 'dsnStatus'=>$data_array['dsnStatus'], 'dsnDiag'=>$data_array['dsnDiag'], 
										'dsnMta'=>$data_array['dsnMta'], 'bounceCat'=>$data_array['bounceCat'], 'srcType'=>$data_array['srcType'], 
										'srcMta'=>$data_array['srcMta'], 'dlvType'=>$data_array['dlvType'], 'dlvSourceIp'=>$data_array['dlvSourceIp'], 
										'dlvDestinationIp'=>$data_array['dlvDestinationIp'], 'dlvSize'=>$data_array['dlvSize'], 'vmta'=>$data_array['vmta'], 
										'jobId'=>$jobId, 'envId'=>$envId,'file_name'=>$file
									);
					
					// Add record to red_pmtalog_blocked table
					$this->db->insert('red_pmtalog_blocked',$update_array);
										
					$bounce_date=substr($data_array['timeLogged'],0,strlen($data_array['timeLogged'])-5);
					
					$job_arr=explode('_',$data_array['jobId']);		
					
					if(count($job_arr)>1){	
						$subscriber_array=$this->Subscriber_Model->get_subscriber_data(array('subscriber_id'=>$job_arr[2]));
						$soft_bounce = $subscriber_array[0]['soft_bounce'];
						$status_change_date = date('Y-m-d', strtotime($subscriber_array[0]['status_change_date']));
						$bounced = $subscriber_array[0]['bounced'];
						$last_bounced_date = date('Y-m-d', strtotime($subscriber_array[0]['last_bounced_date']));
						if($soft_bounce ==0 and $status_change_date == date('Y-m-d'))$soft_bounce = $bounced;
						
						//$hard_bounce_match = preg_grep('/^'.$data_array['dsnStatus'].'.*/', $this->hard_bounce_array);
						$hard_bounce_match = preg_grep('/^'.$data_array['bounceCat'].'.*/', $this->hard_bounce_array);
						$hard_bounce=count($hard_bounce_match);
						$subscriber_status=1;
						if($hard_bounce>=1){									
							$subscrber_bounce=2;
							$email_track_bounce=1;
							$subscriber_status=3;									
							$this->Emailreport_Model->delete_emailqueue(array('subscriber_id'=>$job_arr[2]));
							$dnm_type ='hardbounce';
						}else{
							$soft_bounce = $soft_bounce + 1;									
							$subscrber_bounce=1;
							$email_track_bounce=2;									 
							$config_arr=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'max_soft_bounce'));
							if($soft_bounce > $config_arr[0]['config_value']){
								$subscriber_status=4;			
								$dnm_type ='softbounce';	
								$this->Emailreport_Model->delete_emailqueue(array('subscriber_id'=>$job_arr[2]));
							}
						}
						
						// Update subscriber
						$this->Subscriber_Model->update_subscriber(array('subscrber_bounce'=>$subscrber_bounce,'subscriber_status'=>$subscriber_status,'soft_bounce'=>$soft_bounce, 'bounce_status'=>$bounce_status, 'bounce_detail'=>$bounce_detail, 'status_change_date'=>$utc_str,'bounced'=>$soft_bounce,'last_bounced_date'=>$utc_str),array('subscriber_id'=>$job_arr[2]));					
							
						//update  email report for autoresponder
						$this->Emailreport_Model->update_autoresponder_emailreport(array('email_delivered'=>1,'email_track_bounce'=>$email_track_bounce,'bounce_date'=>$bounce_date,'email_receive_date'=>$bounce_date),array('autoresponder_scheduled_id'=>$job_arr[1],'email_track_subscriber_id'=>$job_arr[2]));
						
						// Add to Global DNM
						$this->addToGlobalDNM($data_array['rcpt'],$dnm_type);
						$this->updateDailyGlobalIPR($data_array['rcpt'],$data_array['vmta'],$dnm_type,$envId);
						
					}else{
						
						//get pmta log according to jobId(subscriber id) and envId(campaign ID)
						$pmta_log_array=$this->Pmta_Model->get_pmta_data(array('jobId'=>$jobId,'envId'=>$envId,'file_name'=>$file));
						if(count($pmta_log_array)>0){						
							//update pmta log table
							$this->Pmta_Model->update_pmta($update_array,array('jobId'=>$jobId,'envId'=>$envId));
						}else{													 						 
							//insert pmta record
							$this->Pmta_Model->create_pmta($update_array);								
						
							if(in_array($envId,$bounce_array)){
								$bounce_counter_array[$envId]++;
							}else{
								$bounce_counter_array[$envId]=1;
								$bounce_array[]=$envId;
							}							
							 
							//	update in subscriber table
							$subscriber_array=$this->Subscriber_Model->get_subscriber_data(array('subscriber_id'=>$jobId));
							$soft_bounce = $subscriber_array[0]['soft_bounce'];
							$status_change_date = date('Y-m-d', strtotime($subscriber_array[0]['status_change_date']));
							$bounced = $subscriber_array[0]['bounced'];
							$last_bounced_date = date('Y-m-d', strtotime($subscriber_array[0]['last_bounced_date']));
							if($soft_bounce ==0 and $status_change_date == date('Y-m-d'))$soft_bounce = $bounced;
							//$hard_bounce_match = preg_grep('/^'.$data_array['dsnStatus'].'.*/', $this->hard_bounce_array);								
							$hard_bounce_match = preg_grep('/^'.$data_array['bounceCat'].'.*/', $this->hard_bounce_array);
							
							if(count($hard_bounce_match) >= 1){
								$subscrber_bounce=2;
								$email_track_bounce=1;
								$subscriber_status=3;									
								$this->Emailreport_Model->delete_emailqueue(array('subscriber_id'=>$jobId));
								$dnm_type ='hardbounce';
							}else{
								$soft_bounce = $soft_bounce + 1;									
								$subscrber_bounce=1;
								$email_track_bounce=2;
								$subscriber_status=1; 
								$config_arr=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'max_soft_bounce'));
								if($soft_bounce > $config_arr[0]['config_value']){
									$subscriber_status=4;										 
									$dnm_type ='softbounce';
									$this->Emailreport_Model->delete_emailqueue(array('subscriber_id'=>$jobId));
								}
							}
							
							// Update subscriber						
							$this->Subscriber_Model->update_subscriber(array('subscrber_bounce'=>$subscrber_bounce,'subscriber_status'=>$subscriber_status,'soft_bounce'=>$soft_bounce, 'bounce_status'=>$bounce_status, 'bounce_detail'=>$bounce_detail, 'status_change_date'=>$utc_str,'bounced'=>$soft_bounce,'last_bounced_date'=>$utc_str),array('subscriber_id'=>$jobId));
							
							$this->incrementDeliveredCounter($envId, $jobId);
							$this->incrementBouncedCounter($envId, $jobId);							
							
							//update  email report	
							$this->Emailreport_Model->update_emailreport(array('email_delivered'=>1,'email_track_bounce'=>$email_track_bounce,'bounce_date'=>$bounce_date,'email_receive_date'=>$bounce_date),array('campaign_id'=>$envId,'subscriber_id'=>$jobId));	
							
							// Add to Global DNM
							$this->addToGlobalDNM($data_array['rcpt'],$dnm_type);
							$this->updateDailyGlobalIPR($data_array['rcpt'],$data_array['vmta'],$dnm_type, $envId);
						}
					}
					// Check if should be paused or not
					$this->ifCriticalPause($envId, 'b');
				}elseif($data_array['rcpt'] != ''){
					$hard_bounce_match = preg_grep('/^'.$data_array['bounceCat'].'.*/', $this->hard_bounce_array);							
					if(count($hard_bounce_match) >= 1){
						$soft_bounce=0;
						$subscrber_bounce=2;						
						$subscriber_status=3;
						$this->Subscriber_Model->update_subscriber(array('subscrber_bounce'=>$subscrber_bounce,'subscriber_status'=>$subscriber_status,'soft_bounce'=>$soft_bounce,'status_change_date'=>$utc_str,'bounced'=>$soft_bounce, 'bounce_status'=>$bounce_status, 'bounce_detail'=>$bounce_detail, 'last_bounced_date'=>$utc_str),array('subscriber_email_address'=>$data_array['rcpt']));
						// Add to Global DNM
						$this->addToGlobalDNM($data_array['rcpt'],'hardbounce');
						$this->updateDailyGlobalIPR($data_array['rcpt'],$data_array['vmta'],'hardbounce');
					}				
				}
				}
			}elseif(strpos($file, "fblreport")!==false){ # for fblreport.csv
				if($data_array['type']=='f'){
				$envid=0;	
				// Get column which gives [contact-id]-[campaign-id]-[system-name]
				$extra_header_arr = explode('-',$data_array['header_x-fblid']);				 
				if(count($extra_header_arr) == 3 and $extra_header_arr[2]==CAMPAIGN_HEADER_SUFFIX){	
					$jobid=$extra_header_arr[0];
					$envid=$extra_header_arr[1];
					if(!$envid)	$envid=$data[12];
					$email_complaint_date=substr($data_array['timeLogged'],0,strlen($data_array['timeLogged'])-5);;
					
					$update_array=array('type'=>$data_array['type'], 'timeLogged'=>$data_array['timeLogged'], 'orig'=>$data_array['orig'], 'rcpt'=>$data_array['rcpt'], 
										'jobId'=>$jobid, 'envId'=>$envid, 'file_name'=>$file
										);
						
					// Add record to red_pmtalog_blocked table
					$this->db->insert('red_pmtalog_blocked',$update_array);
					
					$job_arr=explode('_',$jobid);					
					if(count($job_arr)>1){
						$sid = $job_arr[2];
						$data_array['rcpt'] = $this->db->query("select subscriber_email_address from red_email_subscribers where subscriber_id='$sid'")->row()->subscriber_email_address;
						//update  email report for autoresponder
						$this->Emailreport_Model->update_autoresponder_emailreport(array('email_delivered'=>1,'email_track_bounce'=>0,'email_track_complaint'=>1,'bounce_date'=>NULL,'complaint_date'=>$email_complaint_date),array('autoresponder_scheduled_id'=>$job_arr[1],'email_track_subscriber_id'=>$job_arr[2]));
												
						// Update subscriber
						$this->Subscriber_Model->update_subscriber(array('subscriber_status'=>2,'status_change_date'=>$utc_str,'complaint'=>1,'last_complaint_date'=>$utc_str),array('subscriber_id'=>$job_arr[2]));
						# Delete unsubscriber subscriber from email queue table
						$this->Emailreport_Model->delete_emailqueue(array('subscriber_id'=>$job_arr[2]));
					}else{
						//get pmta log according to jobId(subscriber id) and envId(campaign ID)
						$pmta_log_array=$this->Pmta_Model->get_pmta_data(array('jobId'=>$jobid,'envId'=>$envid,'file_name'=>$file));
						$data_array['rcpt'] = $this->db->query("select subscriber_email_address from red_email_subscribers where subscriber_id='$jobid'")->row()->subscriber_email_address;
						if(count($pmta_log_array)>0){												
							$this->Pmta_Model->update_pmta($update_array,array('jobId'=>$jobid,'envId'=>$envid));
						}else{												
							$this->Pmta_Model->create_pmta($update_array);					
						
							$arrFBLCampaigns[] = $envid;
							
							$this->incrementDeliveredCounter($envid, $jobid);
							$this->incrementSpamCounter($envid, $jobid);		
							
							
							//update  email report for autoresponder											
							$this->Emailreport_Model->update_emailreport(array('email_delivered'=>1,'email_track_bounce'=>0,'email_track_complaint'=>1,'bounce_date'=>NULL,'complaint_date'=>$email_complaint_date),array('campaign_id'=>$envid,'subscriber_id'=>$jobid)) ;						
						
							// Update subscriber
							$this->Subscriber_Model->update_subscriber(array('subscriber_status'=>2,'status_change_date'=>$utc_str,'complaint'=>1,'last_complaint_date'=>$utc_str),array('subscriber_id'=>$jobid)); 
								
							
							# Delete unsubscriber subscriber from email queue table
							$this->Emailreport_Model->delete_emailqueue(array('subscriber_id'=>$jobid));
						}
						// Check if should be paused or not
						$this->ifCriticalPause($envid, 's');
					}
					// Add to Global DNM
					$this->addToGlobalDNM($data_array['rcpt'],'complaints');					 
					$this_vmta = $this->db->query("select pipeline from red_email_campaigns where campaign_id='$envid'" )->row()->pipeline;
					$this->updateDailyGlobalIPR($data_array['rcpt'],$this_vmta,'complaints', $envid);
				}	# PROCESS FOR PEAKHOST SERVER ONLY
				$eml = $data_array['rcpt'];
				$this->db->query("replace into `red_global_fbl` set `email_address`='$eml', `campaign_id`='$envid'");
				}# IF CLOSES FOR CHECKING NOT HEADER ROW				
			}else { # For acct.csv
				
				if($data_array['type']=='d'){
					// Get column which gives [contact-id]-[campaign-id]-[system-name]
					$extra_header_arr = explode('-',$data_array['header_x-fblid']);				 
					if(count($extra_header_arr) == 3 and $extra_header_arr[2]==CAMPAIGN_HEADER_SUFFIX){	
						$update_array=array('type'=>$data_array['type'], 'timeLogged'=>$data_array['timeLogged'], 'orig'=>$data_array['orig'], 'rcpt'=>$data_array['rcpt'], 
										'dsnAction'=>$data_array['dsnAction'], 'dsnStatus'=>$data_array['dsnStatus'], 'dsnDiag'=>$data_array['dsnDiag'], 
										'dsnMta'=>$data_array['dsnMta'], 'bounceCat'=>$data_array['bounceCat'], 'srcType'=>$data_array['srcType'], 
										'srcMta'=>$data_array['srcMta'], 'dlvType'=>$data_array['dlvType'], 'dlvSourceIp'=>$data_array['dlvSourceIp'], 
										'dlvDestinationIp'=>$data_array['dlvDestinationIp'], 'dlvSize'=>$data_array['dlvSize'], 'vmta'=>$data_array['vmta'], 
										'jobId'=>$data_array['jobId'], 'envId'=>$data_array['envId'],'file_name'=>$file
									);
						$email_receive_date=substr($data_array['timeLogged'],0,strlen($data_array['timeLogged'])-5);
						$job_arr=explode('_',$data_array['jobId']);	
						// if pmta for autoresponder
						if(count($job_arr)>1){
								$jobid=$job_arr[1];
								$envid=$job_arr[2];
								//update  email report for autoresponder								
								$email_receive_date=substr($data[1],0,strlen($data[1])-5);
								$this->Emailreport_Model->update_autoresponder_emailreport(array('email_delivered'=>1,'email_track_bounce'=>0,'bounce_date'=>NULL,'email_receive_date'=>$email_receive_date),array('autoresponder_scheduled_id'=>$jobid,'email_track_subscriber_id'=>$envid));
							
						}else{	
							$jobid=$extra_header_arr[0];
							$envid=$extra_header_arr[1];
							//pmta for email campaign	
							//get pmta log according to jobId(subscriber id) and envId(campaign ID)
							$pmta_log_array=$this->Pmta_Model->get_pmta_data(array('jobId'=>$jobid,'envId'=>$envid,'file_name'=>$file));
							if(count($pmta_log_array)>0){								
								$this->Pmta_Model->update_pmta($update_array,array('jobId'=>$jobid,'envId'=>$envid));
							}else{									
								$this->Pmta_Model->create_pmta($update_array);							
								
								$this->incrementDeliveredCounter($envid, $jobid);							
								 
								//update  email report for autoresponder								
								$this->Emailreport_Model->update_emailreport(array('email_delivered'=>1,'email_track_bounce'=>0,'bounce_date'=>NULL,'email_receive_date'=>$email_receive_date),array('campaign_id'=>$envid,'subscriber_id'=>$jobid));
								 							
								// Update subscriber								
								$this->Subscriber_Model->update_subscriber(array('subscrber_bounce'=>0,'soft_bounce'=>0,'status_change_date'=>$utc_str),array('subscriber_id'=>$jobid));
							}
						}
						$this->updateDailyGlobalIPR($data_array['rcpt'],$data_array['vmta'],'delivered', $envid);
					}# ROW IS FOR PEAKHOST only	
					
					// PROCESS  LIST UNSUBSCRIBE
					if(trim($data_array['rcpt']) == 'unsubscribe@redcappi.net' or trim($data_array['rcpt']) == 'unsubscribe@rcmailsv.com' or trim($data_array['rcpt']) == 'unsubscribe@mailsvrc.com' or trim($data_array['rcpt']) == 'unsubscribe@rcmailcorp.com'){
						$x = implode(',',$data_array);
						//@mail('peejha@yahoo.com','unsubscribe',$x);
						if(substr($data_array['header_Subject'],0,11) == 'Unsubscribe'){						
							$encodedID = trim(urldecode(substr($data_array['header_Subject'],11)));
							$eml = trim($data_array['orig']);
							if($this->is_authorized->base64UrlSafeEncode($this->is_authorized->base64UrlSafeDecode($encodedID)) == $encodedID){
								$arr_decoded_id = @explode('-',$this->is_authorized->base64UrlSafeDecode($encodedID));		
								$sid = $arr_decoded_id[0];
								$eml = $arr_decoded_id[1];
								$this->Subscriber_Model->update_subscriber(array('subscriber_status'=>0,'status_change_date'=>$utc_str),array('subscriber_id'=>$sid, 'subscriber_email_address'=>$eml,'subscriber_status'=>1));					
							}elseif(is_numeric($encodedID)){								
								$this->Subscriber_Model->update_subscriber(array('subscriber_status'=>0,'status_change_date'=>$utc_str),array('subscriber_id'=>$encodedID, 'subscriber_email_address'=>$eml,'subscriber_status'=>1));												
							}elseif('abuse-auto@support.netzero.com' == $eml or 'abuse-auto@support.juno.com'== $eml){
								$this->Subscriber_Model->update_subscriber(array('subscriber_status'=>0,'status_change_date'=>$utc_str),array('subscriber_email_address'=>$encodedID,'subscriber_status'=>1));
							}elseif($eml == $encodedID){
								$this->Subscriber_Model->update_subscriber(array('subscriber_status'=>0,'status_change_date'=>$utc_str),array('subscriber_email_address'=>$encodedID,'subscriber_status'=>1));							
							}elseif(trim($encodedID)==''){								
								$this->Subscriber_Model->update_subscriber(array('subscriber_status'=>0,'status_change_date'=>$utc_str),array('subscriber_email_address'=>$eml,'subscriber_status'=>1));	
							}
							$this->updateDailyGlobalIPR($eml,'NA','unsubscribes');
						}					
					}				
					
				}# ROW IS NOT HEADER
				
			}// acct complete
		}
		 
		
		$this->adminNotificationBounceCriticalLimit($bounce_counter_array);
		$this->adminNotificationFBLCriticalLimit(array_unique($arrFBLCampaigns));
		}
        fclose($handle);
	}
        function importCsvBounced($file=""){
		$file_name= $this->config->item('pmta_logs').$file;
		if (($handle = fopen($file_name, "r")) !== FALSE){
		//$handle = fopen($file_name, "r");
		$header_array=array();
		$data_array=array();
		$bounce_counter_array=array();
		$arrFBLCampaigns=array();		
		$bounce_array=array();
		$utc_str = gmdate("Y-m-d H:i:s", time());	
        //while (($data = fgetcsv($handle, 1000, ",")) !== FALSE){			
            while (($data = fgets($handle)) !== FALSE){			
                  $data = explode(',', $data);

			// Get Associative Array
			$number_of_fields = count($data);			 
			if($data[0]=='type'){
			//Header line 
				for ($c=0; $c < $number_of_fields; $c++)
					$header_array[$c] = $data[$c]; 									
			}else{
				for ($c=0; $c < $number_of_fields; $c++)
					$data_array[$header_array[$c]] = $data[$c]; 				
			}	
			
			
			if(strpos($file, "bouncereport")!==false){	//Check bounce report			
				if($data_array['type']=='b' or $data_array['type']=='rb'){
				
				$bounce_status = $data_array['dsnStatus'];
				$bounce_detail = $data_array['dsnDiag'];
				// Get column which gives [contact-id]-[campaign-id]-[system-name]
				$extra_header_arr = explode('-',$data_array['header_x-fblid']);
				if(count($extra_header_arr) == 3 and $extra_header_arr[2]==CAMPAIGN_HEADER_SUFFIX){
				 $jobId = $extra_header_arr[0];
				 $envId = $extra_header_arr[1];
				
					$update_array=array('type'=>$data_array['type'], 'timeLogged'=>$data_array['timeLogged'], 'orig'=>$data_array['orig'], 'rcpt'=>$data_array['rcpt'], 
										'dsnAction'=>$data_array['dsnAction'], 'dsnStatus'=>$data_array['dsnStatus'], 'dsnDiag'=>$data_array['dsnDiag'], 
										'dsnMta'=>$data_array['dsnMta'], 'bounceCat'=>$data_array['bounceCat'], 'srcType'=>$data_array['srcType'], 
										'srcMta'=>$data_array['srcMta'], 'dlvType'=>$data_array['dlvType'], 'dlvSourceIp'=>$data_array['dlvSourceIp'], 
										'dlvDestinationIp'=>$data_array['dlvDestinationIp'], 'dlvSize'=>$data_array['dlvSize'], 'vmta'=>$data_array['vmta'], 
										'jobId'=>$jobId, 'envId'=>$envId,'file_name'=>$file
									);
					
					// Add record to red_pmtalog_blocked table
					$this->db->insert('red_pmtalog_blocked',$update_array);
										
					$bounce_date=substr($data_array['timeLogged'],0,strlen($data_array['timeLogged'])-5);
					
					$job_arr=explode('_',$data_array['jobId']);		
					
					if(count($job_arr)>1){	
						$subscriber_array=$this->Subscriber_Model->get_subscriber_data(array('subscriber_id'=>$job_arr[2]));
						$soft_bounce = $subscriber_array[0]['soft_bounce'];
						$status_change_date = date('Y-m-d', strtotime($subscriber_array[0]['status_change_date']));
						$bounced = $subscriber_array[0]['bounced'];
						$last_bounced_date = date('Y-m-d', strtotime($subscriber_array[0]['last_bounced_date']));
						if($soft_bounce ==0 and $status_change_date == date('Y-m-d'))$soft_bounce = $bounced;
						
						//$hard_bounce_match = preg_grep('/^'.$data_array['dsnStatus'].'.*/', $this->hard_bounce_array);
						$hard_bounce_match = preg_grep('/^'.$data_array['bounceCat'].'.*/', $this->hard_bounce_array);
						$hard_bounce=count($hard_bounce_match);
						$subscriber_status=1;
						if($hard_bounce>=1){									
							$subscrber_bounce=2;
							$email_track_bounce=1;
							$subscriber_status=3;									
							$this->Emailreport_Model->delete_emailqueue(array('subscriber_id'=>$job_arr[2]));
							$dnm_type ='hardbounce';
						}else{
							$soft_bounce = $soft_bounce + 1;									
							$subscrber_bounce=1;
							$email_track_bounce=2;									 
							$config_arr=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'max_soft_bounce'));
							if($soft_bounce > $config_arr[0]['config_value']){
								$subscriber_status=4;			
								$dnm_type ='softbounce';	
								$this->Emailreport_Model->delete_emailqueue(array('subscriber_id'=>$job_arr[2]));
							}
						}
						
						// Update subscriber
						$this->Subscriber_Model->update_subscriber(array('subscrber_bounce'=>$subscrber_bounce,'subscriber_status'=>$subscriber_status,'soft_bounce'=>$soft_bounce, 'bounce_status'=>$bounce_status, 'bounce_detail'=>$bounce_detail, 'status_change_date'=>$utc_str,'bounced'=>$soft_bounce,'last_bounced_date'=>$utc_str),array('subscriber_id'=>$job_arr[2]));					
							
						//update  email report for autoresponder
						$this->Emailreport_Model->update_autoresponder_emailreport(array('email_delivered'=>1,'email_track_bounce'=>$email_track_bounce,'bounce_date'=>$bounce_date,'email_receive_date'=>$bounce_date),array('autoresponder_scheduled_id'=>$job_arr[1],'email_track_subscriber_id'=>$job_arr[2]));
						
						// Add to Global DNM
						$this->addToGlobalDNM($data_array['rcpt'],$dnm_type);
						$this->updateDailyGlobalIPR($data_array['rcpt'],$data_array['vmta'],$dnm_type,$envId);
						
					}else{
						
						//get pmta log according to jobId(subscriber id) and envId(campaign ID)
						$pmta_log_array=$this->Pmta_Model->get_pmta_data(array('jobId'=>$jobId,'envId'=>$envId,'file_name'=>$file));
						if(count($pmta_log_array)>0){						
							//update pmta log table
							$this->Pmta_Model->update_pmta($update_array,array('jobId'=>$jobId,'envId'=>$envId));
						}else{													 						 
							//insert pmta record
							$this->Pmta_Model->create_pmta($update_array);								
						
							if(in_array($envId,$bounce_array)){
								$bounce_counter_array[$envId]++;
							}else{
								$bounce_counter_array[$envId]=1;
								$bounce_array[]=$envId;
							}							
							 
							//	update in subscriber table
							$subscriber_array=$this->Subscriber_Model->get_subscriber_data(array('subscriber_id'=>$jobId));
							$soft_bounce = $subscriber_array[0]['soft_bounce'];
							$status_change_date = date('Y-m-d', strtotime($subscriber_array[0]['status_change_date']));
							$bounced = $subscriber_array[0]['bounced'];
							$last_bounced_date = date('Y-m-d', strtotime($subscriber_array[0]['last_bounced_date']));
							if($soft_bounce ==0 and $status_change_date == date('Y-m-d'))$soft_bounce = $bounced;
							//$hard_bounce_match = preg_grep('/^'.$data_array['dsnStatus'].'.*/', $this->hard_bounce_array);								
							$hard_bounce_match = preg_grep('/^'.$data_array['bounceCat'].'.*/', $this->hard_bounce_array);
							
							if(count($hard_bounce_match) >= 1){
								$subscrber_bounce=2;
								$email_track_bounce=1;
								$subscriber_status=3;									
								$this->Emailreport_Model->delete_emailqueue(array('subscriber_id'=>$jobId));
								$dnm_type ='hardbounce';
							}else{
								$soft_bounce = $soft_bounce + 1;									
								$subscrber_bounce=1;
								$email_track_bounce=2;
								$subscriber_status=1; 
								$config_arr=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'max_soft_bounce'));
								if($soft_bounce > $config_arr[0]['config_value']){
									$subscriber_status=4;										 
									$dnm_type ='softbounce';
									$this->Emailreport_Model->delete_emailqueue(array('subscriber_id'=>$jobId));
								}
							}
							
							// Update subscriber						
							$this->Subscriber_Model->update_subscriber(array('subscrber_bounce'=>$subscrber_bounce,'subscriber_status'=>$subscriber_status,'soft_bounce'=>$soft_bounce, 'bounce_status'=>$bounce_status, 'bounce_detail'=>$bounce_detail, 'status_change_date'=>$utc_str,'bounced'=>$soft_bounce,'last_bounced_date'=>$utc_str),array('subscriber_id'=>$jobId));
							
							$this->incrementDeliveredCounter($envId, $jobId);
							$this->incrementBouncedCounter($envId, $jobId);							
							
							//update  email report	
							$this->Emailreport_Model->update_emailreport(array('email_delivered'=>1,'email_track_bounce'=>$email_track_bounce,'bounce_date'=>$bounce_date,'email_receive_date'=>$bounce_date),array('campaign_id'=>$envId,'subscriber_id'=>$jobId));	
							
							// Add to Global DNM
							$this->addToGlobalDNM($data_array['rcpt'],$dnm_type);
							$this->updateDailyGlobalIPR($data_array['rcpt'],$data_array['vmta'],$dnm_type, $envId);
						}
					}
					// Check if should be paused or not
					$this->ifCriticalPause($envId, 'b');
				}elseif($data_array['rcpt'] != ''){
					$hard_bounce_match = preg_grep('/^'.$data_array['bounceCat'].'.*/', $this->hard_bounce_array);							
					if(count($hard_bounce_match) >= 1){
						$soft_bounce=0;
						$subscrber_bounce=2;						
						$subscriber_status=3;
						$this->Subscriber_Model->update_subscriber(array('subscrber_bounce'=>$subscrber_bounce,'subscriber_status'=>$subscriber_status,'soft_bounce'=>$soft_bounce,'status_change_date'=>$utc_str,'bounced'=>$soft_bounce, 'bounce_status'=>$bounce_status, 'bounce_detail'=>$bounce_detail, 'last_bounced_date'=>$utc_str),array('subscriber_email_address'=>$data_array['rcpt']));
						// Add to Global DNM
						$this->addToGlobalDNM($data_array['rcpt'],'hardbounce');
						$this->updateDailyGlobalIPR($data_array['rcpt'],$data_array['vmta'],'hardbounce');
					}				
				}
				}
			}elseif(strpos($file, "fblreport")!==false){ # for fblreport.csv
				if($data_array['type']=='f'){
				$envid=0;	
				// Get column which gives [contact-id]-[campaign-id]-[system-name]
				$extra_header_arr = explode('-',$data_array['header_x-fblid']);				 
				if(count($extra_header_arr) == 3 and $extra_header_arr[2]==CAMPAIGN_HEADER_SUFFIX){	
					$jobid=$extra_header_arr[0];
					$envid=$extra_header_arr[1];
					if(!$envid)	$envid=$data[12];
					$email_complaint_date=substr($data_array['timeLogged'],0,strlen($data_array['timeLogged'])-5);;
					
					$update_array=array('type'=>$data_array['type'], 'timeLogged'=>$data_array['timeLogged'], 'orig'=>$data_array['orig'], 'rcpt'=>$data_array['rcpt'], 
										'jobId'=>$jobid, 'envId'=>$envid, 'file_name'=>$file
										);
						
					// Add record to red_pmtalog_blocked table
					$this->db->insert('red_pmtalog_blocked',$update_array);
					
					$job_arr=explode('_',$jobid);					
					if(count($job_arr)>1){
						$sid = $job_arr[2];
						$data_array['rcpt'] = $this->db->query("select subscriber_email_address from red_email_subscribers where subscriber_id='$sid'")->row()->subscriber_email_address;
						//update  email report for autoresponder
						$this->Emailreport_Model->update_autoresponder_emailreport(array('email_delivered'=>1,'email_track_bounce'=>0,'email_track_complaint'=>1,'bounce_date'=>NULL,'complaint_date'=>$email_complaint_date),array('autoresponder_scheduled_id'=>$job_arr[1],'email_track_subscriber_id'=>$job_arr[2]));
												
						// Update subscriber
						$this->Subscriber_Model->update_subscriber(array('subscriber_status'=>2,'status_change_date'=>$utc_str,'complaint'=>1,'last_complaint_date'=>$utc_str),array('subscriber_id'=>$job_arr[2]));
						# Delete unsubscriber subscriber from email queue table
						$this->Emailreport_Model->delete_emailqueue(array('subscriber_id'=>$job_arr[2]));
					}else{
						//get pmta log according to jobId(subscriber id) and envId(campaign ID)
						$pmta_log_array=$this->Pmta_Model->get_pmta_data(array('jobId'=>$jobid,'envId'=>$envid,'file_name'=>$file));
						$data_array['rcpt'] = $this->db->query("select subscriber_email_address from red_email_subscribers where subscriber_id='$jobid'")->row()->subscriber_email_address;
						if(count($pmta_log_array)>0){												
							$this->Pmta_Model->update_pmta($update_array,array('jobId'=>$jobid,'envId'=>$envid));
						}else{												
							$this->Pmta_Model->create_pmta($update_array);					
						
							$arrFBLCampaigns[] = $envid;
							
							$this->incrementDeliveredCounter($envid, $jobid);
							$this->incrementSpamCounter($envid, $jobid);		
							
							
							//update  email report for autoresponder											
							$this->Emailreport_Model->update_emailreport(array('email_delivered'=>1,'email_track_bounce'=>0,'email_track_complaint'=>1,'bounce_date'=>NULL,'complaint_date'=>$email_complaint_date),array('campaign_id'=>$envid,'subscriber_id'=>$jobid)) ;						
						
							// Update subscriber
							$this->Subscriber_Model->update_subscriber(array('subscriber_status'=>2,'status_change_date'=>$utc_str,'complaint'=>1,'last_complaint_date'=>$utc_str),array('subscriber_id'=>$jobid)); 
								
							
							# Delete unsubscriber subscriber from email queue table
							$this->Emailreport_Model->delete_emailqueue(array('subscriber_id'=>$jobid));
						}
						// Check if should be paused or not
						$this->ifCriticalPause($envid, 's');
					}
					// Add to Global DNM
					$this->addToGlobalDNM($data_array['rcpt'],'complaints');					 
					$this_vmta = $this->db->query("select pipeline from red_email_campaigns where campaign_id='$envid'" )->row()->pipeline;
					$this->updateDailyGlobalIPR($data_array['rcpt'],$this_vmta,'complaints', $envid);
				}	# PROCESS FOR PEAKHOST SERVER ONLY
				$eml = $data_array['rcpt'];
				$this->db->query("replace into `red_global_fbl` set `email_address`='$eml', `campaign_id`='$envid'");
				}# IF CLOSES FOR CHECKING NOT HEADER ROW				
			}else { # For acct.csv
				
				if($data_array['type']=='d'){
					// Get column which gives [contact-id]-[campaign-id]-[system-name]
					$extra_header_arr = explode('-',$data_array['header_x-fblid']);				 
					if(count($extra_header_arr) == 3 and $extra_header_arr[2]==CAMPAIGN_HEADER_SUFFIX){	
						$update_array=array('type'=>$data_array['type'], 'timeLogged'=>$data_array['timeLogged'], 'orig'=>$data_array['orig'], 'rcpt'=>$data_array['rcpt'], 
										'dsnAction'=>$data_array['dsnAction'], 'dsnStatus'=>$data_array['dsnStatus'], 'dsnDiag'=>$data_array['dsnDiag'], 
										'dsnMta'=>$data_array['dsnMta'], 'bounceCat'=>$data_array['bounceCat'], 'srcType'=>$data_array['srcType'], 
										'srcMta'=>$data_array['srcMta'], 'dlvType'=>$data_array['dlvType'], 'dlvSourceIp'=>$data_array['dlvSourceIp'], 
										'dlvDestinationIp'=>$data_array['dlvDestinationIp'], 'dlvSize'=>$data_array['dlvSize'], 'vmta'=>$data_array['vmta'], 
										'jobId'=>$data_array['jobId'], 'envId'=>$data_array['envId'],'file_name'=>$file
									);
						$email_receive_date=substr($data_array['timeLogged'],0,strlen($data_array['timeLogged'])-5);
						$job_arr=explode('_',$data_array['jobId']);	
						// if pmta for autoresponder
						if(count($job_arr)>1){
								$jobid=$job_arr[1];
								$envid=$job_arr[2];
								//update  email report for autoresponder								
								$email_receive_date=substr($data[1],0,strlen($data[1])-5);
								$this->Emailreport_Model->update_autoresponder_emailreport(array('email_delivered'=>1,'email_track_bounce'=>0,'bounce_date'=>NULL,'email_receive_date'=>$email_receive_date),array('autoresponder_scheduled_id'=>$jobid,'email_track_subscriber_id'=>$envid));
							
						}else{	
							$jobid=$extra_header_arr[0];
							$envid=$extra_header_arr[1];
							//pmta for email campaign	
							//get pmta log according to jobId(subscriber id) and envId(campaign ID)
							$pmta_log_array=$this->Pmta_Model->get_pmta_data(array('jobId'=>$jobid,'envId'=>$envid,'file_name'=>$file));
							if(count($pmta_log_array)>0){								
								$this->Pmta_Model->update_pmta($update_array,array('jobId'=>$jobid,'envId'=>$envid));
							}else{									
								$this->Pmta_Model->create_pmta($update_array);							
								
								$this->incrementDeliveredCounter($envid, $jobid);							
								 
								//update  email report for autoresponder								
								$this->Emailreport_Model->update_emailreport(array('email_delivered'=>1,'email_track_bounce'=>0,'bounce_date'=>NULL,'email_receive_date'=>$email_receive_date),array('campaign_id'=>$envid,'subscriber_id'=>$jobid));
								 							
								// Update subscriber								
								$this->Subscriber_Model->update_subscriber(array('subscrber_bounce'=>0,'soft_bounce'=>0,'status_change_date'=>$utc_str),array('subscriber_id'=>$jobid));
							}
						}
						$this->updateDailyGlobalIPR($data_array['rcpt'],$data_array['vmta'],'delivered', $envid);
					}# ROW IS FOR PEAKHOST only	
					
					// PROCESS  LIST UNSUBSCRIBE
					if(trim($data_array['rcpt']) == 'unsubscribe@redcappi.net' or trim($data_array['rcpt']) == 'unsubscribe@rcmailsv.com' or trim($data_array['rcpt']) == 'unsubscribe@mailsvrc.com' or trim($data_array['rcpt']) == 'unsubscribe@rcmailcorp.com'){
						$x = implode(',',$data_array);
						//@mail('peejha@yahoo.com','unsubscribe',$x);
						if(substr($data_array['header_Subject'],0,11) == 'Unsubscribe'){						
							$encodedID = trim(urldecode(substr($data_array['header_Subject'],11)));
							$eml = trim($data_array['orig']);
							if($this->is_authorized->base64UrlSafeEncode($this->is_authorized->base64UrlSafeDecode($encodedID)) == $encodedID){
								$arr_decoded_id = @explode('-',$this->is_authorized->base64UrlSafeDecode($encodedID));		
								$sid = $arr_decoded_id[0];
								$eml = $arr_decoded_id[1];
								$this->Subscriber_Model->update_subscriber(array('subscriber_status'=>0,'status_change_date'=>$utc_str),array('subscriber_id'=>$sid, 'subscriber_email_address'=>$eml,'subscriber_status'=>1));					
							}elseif(is_numeric($encodedID)){								
								$this->Subscriber_Model->update_subscriber(array('subscriber_status'=>0,'status_change_date'=>$utc_str),array('subscriber_id'=>$encodedID, 'subscriber_email_address'=>$eml,'subscriber_status'=>1));												
							}elseif('abuse-auto@support.netzero.com' == $eml or 'abuse-auto@support.juno.com'== $eml){
								$this->Subscriber_Model->update_subscriber(array('subscriber_status'=>0,'status_change_date'=>$utc_str),array('subscriber_email_address'=>$encodedID,'subscriber_status'=>1));
							}elseif($eml == $encodedID){
								$this->Subscriber_Model->update_subscriber(array('subscriber_status'=>0,'status_change_date'=>$utc_str),array('subscriber_email_address'=>$encodedID,'subscriber_status'=>1));							
							}elseif(trim($encodedID)==''){								
								$this->Subscriber_Model->update_subscriber(array('subscriber_status'=>0,'status_change_date'=>$utc_str),array('subscriber_email_address'=>$eml,'subscriber_status'=>1));	
							}
							$this->updateDailyGlobalIPR($eml,'NA','unsubscribes');
						}					
					}				
					
				}# ROW IS NOT HEADER
				
			}// acct complete
		}
		 
		
		$this->adminNotificationBounceCriticalLimit($bounce_counter_array);
		$this->adminNotificationFBLCriticalLimit(array_unique($arrFBLCampaigns));
		}
        fclose($handle);
	}
	function ifCriticalPause($cid, $typ='b'){
		if(is_numeric($cid)){
		
			$rsCampaign = $this->db->query("select campaign_status, is_segmentation, campaign_created_by from red_email_campaigns where campaign_id='$cid'");
			$cstatus = $rsCampaign->row()->campaign_status;
			$is_ongoing = $rsCampaign->row()->is_segmentation;
			$mid = $rsCampaign->row()->campaign_created_by;
			$rsCampaign->free_result();
			
			//  Check this member's campaign is pausable or not
			$rsIsPausable = $this->db->query("select is_pausable, member_username, email_address from red_members where member_id='$mid'");
			$is_pausable = $rsIsPausable->row()->is_pausable; 	
			$member_pausable = $rsIsPausable->row()->member_username; 	
			$member_email = $rsIsPausable->row()->email_address;
			$rsIsPausable->free_result();
			
		
			
			if($is_pausable){
			
				if($is_ongoing){
					$rsOngoing = $this->db->query("select * from `red_ongoing_segmentation` where `campaign_id` = '$cid'");
					$is_ongoing = $rsOngoing->num_rows();
					$rsOngoing->free_result();
					
					// START: Campaign-Paused	notification to USER
					$txt_body = str_replace('[CID]', $cid, $this->emailContent($typ));
					$html_content = nl2br($txt_body);
					//send_member_message_email($member_email, SYSTEM_EMAIL_FROM, 'RedCappi', 'Regarding Your Latest Campaign',$html_content, $txt_body);
					// ENDS: Campaign-Paused	notification to USER
					
				}
				
					if($typ == 'b'){
						$reason = 'High Bounces';
						$dnmType = " email_track_bounce > 0" ;
						$clevel = $this->confg_arr['bounce_critical_level_to_pause']; 
					}elseif($typ == 's'){
						$reason = 'High Complaints';
						$dnmType = " email_track_complaint > 0" ;
						$clevel = $this->confg_arr['fbl_critical_level_to_pause'];  
					}	
					$rsCountDNM = $this->db->query("select count(subscriber_id) c from red_email_track where campaign_id='$cid' and $dnmType"); 
					$countDNM = $rsCountDNM->row()->c;
					$rsCountDNM->free_result();
				
					$rsCountAll = $this->db->query("select count(subscriber_id) c from red_email_track where campaign_id='$cid' "); 
					$countAll = $rsCountAll->row()->c;
					$rsCountAll->free_result();
					// Pause if critical
					if( $clevel < (($countDNM / $countAll) * 100)  ){
						// Check whether campaign is Scheduled or On-going
						// IF already paused, do nothing
						if( ($cstatus == 'archived') or($is_ongoing > 0) ){
							$this->db->query("delete from `red_ongoing_segmentation` where `campaign_id` = '$cid'");
							$this->Campaign_Model->update_campaign(array('is_segmentation'=>'1','number_of_contacts'=>0,'campaign_status'=>'active'),array('campaign_id'=>$cid));
							// Send block RC-Alert	
							$message = "<p>Hello admin,</p><p>A campaign [$cid] for <b>$member_pausable</b> is paused because of $reason </p>
							<p>User is un-authenticated (if authentic)<br/>
							Auto-segmentation disabled (If enabled in past)<br/>
							His next campaign will be un-approvable. (To approve it Admin needs to modify member-settings.)
							</p>
							<p>Regards,<br />Redcappi Team</p>";		
							$text_message= "A campaign [$cid] for $member_pausable is paused because of $reason:\n\nUser is un-authenticated (if authentic)\n
							Auto-segmentation disabled (If enabled in past)\n
							His next campaign will be un-approvable. (To approve it Admin needs to modify member-settings.)\n\n";	
										 					 	
							$to = $this->confg_arr['admin_notification_email'];
						
							admin_notification_send_email($to, SYSTEM_EMAIL_FROM,'RedCappi', "Campaign [$cid] for $member_pausable paused due to $reason",$message,$text_message);				
							// Send block RC-Alert Ends						
						}
						// For ALL mails who cross the critical level but either under-sending or already-sent
						// 1. Un-authenticate member, 2. Stop is_automatic_segmentation & 3. mark his next-campaign Un-approvable
						$approval_notes = 'Unauthenticated after high bounce/complaint received';
						$unauthenticNotes = ", campaign_approval_notes = IFNULL(concat(replace(campaign_approval_notes, '$approval_notes','') , '$approval_notes' ), '$approval_notes')";					
						$this->db->query("update red_members set is_authentic=0, is_automatic_segmentation=0, stop_campaign_approval=1 $unauthenticNotes where `member_id`='$mid'");
						//  For this RC-alert will be not sent.
						
					}
				
			}	
		}
	}
	function incrementDeliveredCounter($campaign_id, $contact_id){
		$sqlCheckContactMarkedDelivered = "select `email_delivered`,`email_track_bounce` from `red_email_track` where `campaign_id`='$campaign_id' and `subscriber_id`='$contact_id'";
		$rsCheckContactStatus = $this->db->query($sqlCheckContactMarkedDelivered);
		$isContactDelivered = $rsCheckContactStatus->row()->email_delivered;
		$isContactBounced = $rsCheckContactStatus->row()->email_track_bounce;
		if($isContactBounced > 0)$decrementBouncedCounter = ", `email_track_bounce` = `email_track_bounce` - 1 "; else $decrementBouncedCounter='';
		if($isContactDelivered == 0){
			$this->db->query("update `red_email_campaigns_scheduled` set `email_track_delivered` = `email_track_delivered`+1 ".$decrementBouncedCounter." where campaign_id='$campaign_id'");		
		}	
	}
	
	function incrementBouncedCounter($campaign_id, $contact_id){
		$sqlCheckContactMarkedBounced = "select `email_track_bounce` from `red_email_track` where `campaign_id`='$campaign_id' and `subscriber_id`='$contact_id'";
		$isContactBounced = $this->db->query($sqlCheckContactMarkedBounced)->row()->email_track_bounce;
		if($isContactBounced == 0){
			$this->db->query("update `red_email_campaigns_scheduled` set `email_track_bounce` = `email_track_bounce`+1 where campaign_id='$campaign_id'");		
		}	
	}
	function incrementSpamCounter($campaign_id, $contact_id){
		$sqlCheckContactMarkedSpam = "select `email_track_complaint` from `red_email_track` where `campaign_id`='$campaign_id' and `subscriber_id`='$contact_id'";
		$isContactSpammed = $this->db->query($sqlCheckContactMarkedSpam)->row()->email_track_complaint;
		if($isContactSpammed == 0){
			$this->db->query("update `red_email_campaigns_scheduled` set `email_track_spam` = `email_track_spam`+1 where campaign_id='$campaign_id'");		
		}	
	}

	function adminNotificationBounceCriticalLimit($arrBounceCounter){		
		#update schedule table according to campaign_id
		foreach($arrBounceCounter as $k=>$v){
			// Get Total number of bounce  mails for subscription list				
			$email_report=$this->Emailreport_Model->get_emailreport_listdata(array('campaign_id'=>$k));
			$total_bounce=$email_report[0]['email_track_bounce']+$v;				
			//$this->Emailreport_Model->update_listemailreport(array('email_track_bounce'=>$total_bounce),array('campaign_id'=>$k));			
							
			// Get Total number of delivered mail 
			$total_delivered_emails=$this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$k,'email_sent'=>1));
			if($total_delivered_emails > 50){
				# Email notification for bounce percentage too high
				$bounce_percentage=($v/$total_delivered_emails)*100;
				$config_arr=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'maximum_bounce_contact'));
				if($bounce_percentage >$config_arr[0]['config_value']){
					$this->contact_notification($k,$bounce_percentage, "bounce_percentage_high",$total_bounce, $total_delivered_emails);
					$this->db->query("update `red_email_campaigns` set `bounces_over_limit` = 1 where `campaign_id`='$k'");
				}
			}
		}	
	}
	function adminNotificationFBLCriticalLimit($arrCapaignId){		
		foreach($arrCapaignId as $cid){				 
				// Get Total number of complaint mail 
				$total_complaint_emails=$this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$cid,'email_track_complaint'=>1));
				// Get Total number of delivered mail 
				$total_delivered_emails=$this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$cid,	'email_delivered'=>1));
				if($total_delivered_emails > 50){
					# Email notification for bounce percentage too high
					$complaint_percentage=($total_complaint_emails/$total_delivered_emails)*100;
					$config_arr=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'fbl_critical_limit'));
					if($complaint_percentage >$config_arr[0]['config_value']){
						$this->contact_notification($cid,$complaint_percentage,"complaint_percentage_high", $total_complaint_emails, $total_delivered_emails);
						$this->db->query("update `red_email_campaigns` set `complaints_over_limit` = 1 where `campaign_id`='$cid'");
					}
				}
		}	
	}
	 
	function contact_notification($campaign_id=0, $percentage=0, $notification_type="bounce_percentage_high", $complaints=0, $delivered=0 ){
		
		$campaign_info=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$campaign_id,'is_deleted'=>0));
		if(count($campaign_info)>0){
			 
			// Fetch user data from database
			$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$campaign_info[0]['campaign_created_by']));
			$campaign_view_link= CAMPAIGN_DOMAIN.'c/'.$campaign_id;
			$user_info=array($user_data_array[0]['member_username'],$campaign_info[0]['campaign_title'],$campaign_view_link);
			$this->load->helper('notification');
			@create_notification($notification_type,$user_info, round($percentage,2),$complaints, $delivered);
		}
	}
	/**
		Function check_cronjob_status to fetch cronjob status
		@ return string cronjob_status
	*/
	function check_cronjob_status(){		 
		$confg_arr=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'pmta_cronjob_status'));
		return $confg_arr[0]['config_value'];
	}
	
	function addToGlobalDNM($emlid, $dnm_type='hardbounce'){
		// '1=hardbounce,2=softbounce,3=complaints,4=unsubscribes'
		$int_dnm_type = 0;
		switch($dnm_type) {
			case 'hardbounce':
				$int_dnm_type = 1;
				break;
			case 'softbounce':
				$int_dnm_type = 2;
				break;
			case 'complaints':
				$int_dnm_type = 3;
				break;
			case 'unsubscribes':
				$int_dnm_type = 4;
				break;
			default:
				$int_dnm_type = 0;
		}
		
		if(trim($emlid) != '' and $int_dnm_type > 0){
			$this->db->query("replace into `red_global_dnm` set `email_address` = '$emlid',dnm_type= '$int_dnm_type' ");				
		}
	
	}	
	// function to Update Daily-global-IPR
	function updateDailyGlobalIPR($emlid,$vmta,$recType, $campaign_id=0){
		if(intval($campaign_id) > 0){
			$rsCampaign = $this->db->query("select DATE_FORMAT(email_send_date, '%Y-%m-%d')email_send_date, campaign_created_by user_id from red_email_campaigns where campaign_id='$campaign_id'" );
			$email_send_date = $rsCampaign->row()->email_send_date;
			$user_id = $rsCampaign->row()->user_id;
			$rsCampaign->free_result();		
		}else{
			$email_send_date = date('Y-m-d');
			$user_id =0;
		}
		$arrEml = explode('@',$emlid);
			$emlDomain = $arrEml[1];
			if(in_array($emlDomain,$this->config->item('major_domains'))){
				if($recType == 'hardbounce' or $recType == 'softbounce'){
					$increment_counter = ' total_bounced = total_bounced +1 ';
				}elseif($recType == 'complaints'){
					$increment_counter = ' total_complaint = total_complaint +1 ';
				}elseif($recType == 'unsubscribes'){
					$increment_counter = ' total_unsubscribed = total_unsubscribed +1 ';
				}else{
					$increment_counter = ' total_delivered = total_delivered +1 ';
				}
				$this->db->query("insert into red_global_ipr_daily set `mail_domain` = '$emlDomain' ,  `log_date`='$email_send_date' ,  `pipeline`='$vmta', `user_id`='$user_id', $increment_counter ON DUPLICATE  KEY UPDATE  $increment_counter ");
			}		
	}
	
	// Delete Old CSV Log Files and records from the DB			
	function removeOldLogFiles(){
		$archivedLogFileDirPath = $this->config->item('pmta_archives');
		if($logs = opendir($archivedLogFileDirPath)){
			while (($log = readdir($logs)) !== false)
			{
				if ($log == '.' || $log == '..')
					continue;
			#echo "<br/>".date('Y-m-d h:i:s',filectime($archivedLogFileDirPath.$log)).'-------'.$log;
				if (filectime($archivedLogFileDirPath.$log) <= time() - 7 * 24 * 60 * 60)
				{
					unlink($archivedLogFileDirPath.$log);
				}
			}
			closedir($logs);		
		}	
	}
	function removeOldLog(){
		$dateBefore7Days = date('Y-m-d', strtotime('-7 days',time())) ; #date("Y-m-d", strtotime(date("Y-m-d"))) . " -15 day";
		$this->db->query("delete FROM `red_pmtalog` where str_to_date(substring(timeLogged,1,10),'%Y-%m-%d')< '$dateBefore7Days'") ;	
	}
	
	function emailContent($typ='b'){
		if($typ == 'b'){
			return "Hi there,
 
Unfortunately, your email campaign (http://www.red7.me/c/[CID]) resulted in a high bounce rate and our servers automatically paused it. A high bounce rate can do some serious damage to your future delivery and sender reputation.
 
Most of the time, a high Bounce rate comes from a list of contacts who may have subscribed a long time ago and not been emailed in some time. Is there a chance that some contacts on your list subscribed over 12-18 months ago, but have not been fully responsive since then?
 
We highly recommend removing any old lists and/or contacts, and make sure you are only sending emails to subscribers who have been really responsive and active within the past 12 months.
 
Can you please let us know if you have any older contacts on your list, and if you'd be able to remove them? You can also look at professional list cleaning services for a one-time scrub (we have some partners and may be able to get a discounted rate for you on this).


This way, we can get you sending again ASAP to a fully up-to-date and healthy list!
 
Cheers,
The RedCappi Team
https://www.redcappi.com
";
		
		}else{
			return "Hi there,
 
Unfortunately, your email campaign (http://www.red7.me/c/[CID]) resulted in a high complaint rate and our servers automatically paused it. A high complaint rate can do some serious damage to your future delivery and sender reputation.
 
Most of the time, a high complaint rate comes when you have purchased your email list or from a list of contacts who may have subscribed a long time ago and not been emailed in some time. Is there a chance that some contacts on your list subscribed over 12-18 months ago, but have not been fully responsive since then?
 
We highly recommend removing any old lists and/or contacts and make sure you are only sending emails to subscribers who have subscribed to receive promotional offers and newsletters from you. 
 
Can you let us know if by any chance you have purchased your list or you've got any older contacts on your list, and if you'd be able to remove them? You can also look at professional list cleaning services for a one-time scrub (we have some partners and may be able to get a discounted rate for you on this).

This way, we can get you sending again ASAP to a fully up-to-date and healthy list!
 
Cheers,
The RedCappi Team
https://www.redcappi.com
";
		
		
		}	
	}		
}
?>