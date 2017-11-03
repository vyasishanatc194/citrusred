<?php

/*
 * Class to be used as cronjobs for datavalidation 
 * This validated email-addresses for Users
 * */

class datavalidation_cron extends CI_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('webmaster/Croncb_Model');
    }

    /*  Cron1: Start Code for create_csv - CB (Create csv) */
    /*
     * Function to create CSV of contacts to be graded
     **/

    function create_csv() {
        $finalArray = array();
        $newFinalArray = array();

        // Get Total count of contact 
        $totalCount = $this->Croncb_Model->get_count_cron_data(array('subscriber_status' => 1, 'rc_status'=> 0,'is_deleted' => 0, 'dv_batch_grade is NULL' => null));
				
	  if(count($totalCount) > 0){
	  
        foreach ($totalCount as $ttl_key => $ttl_value) {
            $conditions_memb_array = array('subscriber_created_by' => $ttl_key, 'subscriber_status' => 1, 'is_deleted' => 0, 'dv_batch_grade is NULL' => NULL);
            // Paging settings 
            $itemsPerPageReviews = $this->config->item('DV_CSV_COUNT');
            $currentPageReviews = ceil($ttl_value['totalcount'] / $itemsPerPageReviews);

            for ($i = 1; $i <= $currentPageReviews; $i++) {
                $offset = ($i - 1) * $itemsPerPageReviews;
                $ContactArray = $this->Croncb_Model->get_cron_data($conditions_memb_array, $offset, $itemsPerPageReviews);
				
				
                // create folder as per cron list (cron_tbl primary id)
				
                $dv_folder_path = $this->config->item('DV_UPLOAD_PATH') . "/" . $ttl_value['rc_id'];
                @mkdir($dv_folder_path, 0777);
                $filename = $dv_folder_path . "/" . $i . ".csv";
                $fp = fopen($filename, 'w');
				
                // Add record to red_dv_csv*/
                $inputCsvArray = array('dv_rc_id' => $ttl_value['rc_id'],'dv_csv_count'=>count($ContactArray), 'dv_csv_name' => $i . ".csv",'dv_cron_status' => 1,'dv_createddate' => date('Y-m-d H:i:s'), 'dv_updateddate' => date('Y-m-d H:i:s'));
               $this->Croncb_Model->insertCsvLog($inputCsvArray);

                foreach ($ContactArray as $r_key => $r_value) {
                    // Update csv number in red_email_subscribers table
                    $this->Croncb_Model->update_account(array("dv_batch_number" => $ttl_value['rc_id'] . "__" . $i . ".csv"), array("subscriber_id" => $r_value['subscriber_id']));

                    $val = array($r_value['subscriber_id'], $r_value['subscriber_email_address']);
                    fputcsv($fp, $val);
					
					//$last_subscriber_id = $r_value['subscriber_id'] ;
                }				
                fclose($fp);
            }	

			/* Update Cron run status in red_dv_cron_setup */
            $this->Croncb_Model->updateCron(array("rc_cron_runstatus" => 1), array("rc_id" => $ttl_value['rc_id']));
			
			
		}
		exit;
	}
    }

    /*  Cron2:
     * 	Call for Batch setup
     * */

    function csv_dv_post() {
        $DV_API_KEY = $this->config->item('DV_API_KEY');

        $headers = array('Content-Type: text/csv', 'Authorization: bearer ' . $DV_API_KEY);
        $url = 'https://api.datavalidation.com/1.0/list/?pretty=true&header=false&email=1&metadata=true&slug_col=0';

        $fetchData = $this->Croncb_Model->selectSingleCsvCron(array("dv_cron_status" => 1));

		
		 if(count($fetchData) > 0){
		
        // Start upload csv to datavalidation API
        foreach ($fetchData as $f_key => $f_value) {
            $folder_path = $this->config->item('DV_UPLOAD_PATH') . "/" . $f_value['rc_id'] . "/";
            $filesArray = scandir($folder_path);

          //  foreach ($filesArray as $fa_key => $fa_value) {
               // if ($fa_key > 1) {
                    $filename = $folder_path . $f_value['dv_csv_name'];
					
					
                    $params = array('file' => '@' . realpath($filename));

                    // Generate curl request
                    $session = curl_init($url);
                    curl_setopt($session, CURLOPT_POST, true);
                    curl_setopt($session, CURLOPT_POSTFIELDS, $params);
                    curl_setopt($session, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($session, CURLOPT_CAINFO, $this->config->item('rcdata') . 'cacert.pem');
                    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($session);
                    
                  
                    if ($response === false) {
                        $inputArray = array('err_rc_id' => $f_value['rc_id'], 'err_type' => "Error when bulk csv called", 'err_response' => curl_error($session), 'err_createddate' => date('Y-m-d H:i:s'));
                        $this->Croncb_Model->insertErrorLog($inputArray);
                    } else {
                        $json_response = json_decode($response);
                        $slug = $json_response->list[0]->slug;
						
						
                        $this->Croncb_Model->update_CsvLog(array("dv_slug" => $slug, "dv_response" => json_encode($response), "dv_updateddate" => date('Y-m-d H:i:s')), array("dv_rc_id" => $f_value['rc_id'], 'dv_csv_name' => $f_value['dv_csv_name']));
                    }
					
					#echo $slug."===".$f_value['rc_id']."====".;exit;
					
                    curl_close($session);
              //  }
          //  }
        }
		
		 }
        // END upload csv to datavalidation API
        // START job call to datavalidation API
    if(count($fetchData) > 0){
        $fetchCsvData = $this->Croncb_Model->selectCsvLog(array('dv_job_status' => 0,"dv_cron_status" => 1));
		
        foreach ($fetchCsvData as $cv_key => $cv_value) {
            //Create Job to post on datavalidation
            $headers = array('Content-Type: application/json', 'Authorization: bearer ' . $DV_API_KEY);
            $slug = $cv_value['dv_slug'];
            $url = 'https://api.datavalidation.com/1.0/list/' . $slug . '/job/?pretty=true';
            // Generate curl request
            $params = array();
            $session = curl_init($url);
            curl_setopt($session, CURLOPT_POST, true);
            curl_setopt($session, CURLOPT_POSTFIELDS, $params);
            curl_setopt($session, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($session, CURLOPT_CAINFO, $this->config->item('rcdata') . 'cacert.pem');
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($session);

            if ($response === false) {
                $this->Croncb_Model->insertErrorLog(array('err_rc_id' => $cv_value['dv_rc_id'], 'err_type' => "Error when job called",'err_response' => curl_error($session), 'err_createddate' => date('Y-m-d H:i:s')));
            } else {
                $json_job_response = json_decode($response);
                $job_slug = $json_job_response->job[0]->slug;

				if($job_slug != ''){
					$this->Croncb_Model->update_CsvLog(array("dv_job_slug" => $job_slug,"dv_cron_status" => 2, "dv_job_status" => 1, "dv_job_response" => json_encode($response), "dv_updateddate" => date('Y-m-d H:i:s')), array("dv_id" => $cv_value['dv_id']));
				}
            }
            curl_close($session);
        }
    }
		//END job call to datavalidation API
    }

    /* Cron3:
     * Start Code For get Batch Grade (getting grade of all csv data)
     * */

    function csv_fetch_grade() {
        $DV_API_KEY = $this->config->item('DV_API_KEY');
		$fetchCsvData = array();	
        $headers = array('Content-Type: text/csv', 'Authorization: bearer ' . $DV_API_KEY);
        $fetchCsvData = $this->Croncb_Model->selectSingleCsvCron(array('dv_job_status' => 1,"dv_cron_status" => 2,'dv_batch_grade is NULL' => null));		
		$finalCountOfcsv = array();
	if(count($fetchCsvData)>0){
        foreach ($fetchCsvData as $cv_key => $cv_value) {
            //New Code - 07-10-2016
			$finalCountOfcsv[$cv_value['dv_rc_id']][] = $cv_value['dv_id'];
			//End Code - 07-101-2016
			
			$list_slug = $cv_value['dv_slug'];
            $job_slug = $cv_value['dv_job_slug'];
            $headers = array('Content-Type: application/json', 'Authorization: bearer ' . $DV_API_KEY);
            $url = 'https://api.datavalidation.com/1.0/list/' . $list_slug . '/job/' . $job_slug . '/?pretty=true';
            // Generate curl request
            $session = curl_init($url);
            curl_setopt($session, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($session, CURLOPT_CAINFO, $this->config->item('rcdata') . 'cacert.pem');
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($session);
			if ($response === false) {
                $this->Croncb_Model->insertErrorLog(array('err_rc_id' => $cv_value['dv_rc_id'], 'err_type' => "Error when job with slug called", 'err_response' => curl_error($session), 'err_createddate' => date('Y-m-d H:i:s')));
            } else {
                // $job_slug = $json_response->job[0]->slug;

				$json_response = json_decode($response);             
				
                $gradeArray = $json_response->job[0]->stats->grade;
                $unquieGradeId = $gradeArray[0]->name; //key((array)$gradeArray);

			   if($unquieGradeId != ''){
					
					$this->Croncb_Model->update_CsvLog(array('dv_export_status' => 1,"dv_cron_status" => 3, 'dv_batch_run' => 1, 'dv_batch_rundate' => date('Y-m-d H:i:s'), 'dv_batch_grade' => $unquieGradeId), array('dv_id' => $cv_value['dv_id']));
					
					//New 
					$batch_number = $cv_value['dv_rc_id']."__".$cv_value['dv_csv_name'];
					$this->Croncb_Model->update_account(array("dv_batch_grade" => $unquieGradeId), array("dv_batch_number" => $batch_number));
					
				} else{
					 $this->Croncb_Model->update_CsvLog(array('dv_export_status' => 1, 'dv_batch_run' => 1,  'dv_batch_rundate' => date('Y-m-d H:i:s')), array('dv_id' => $cv_value['dv_id']));
				}
				
			}
            curl_close($session);
        }
		
		foreach($finalCountOfcsv as $cc_key => $cc_value){
			/* Update Cron run status in red_dv_cron_setup */
            $this->Croncb_Model->updateCron(array("rc_status" => 1), array("rc_id" => $cc_key));
			
		}
		
	}
		
	
    }

    /* Cron4:
     * Start Code For get indivisual csv Grade (which is set via admin) (getting grade of contact)
     * */
    function contact_fetch_grade() {
        $DV_API_KEY = $this->config->item('DV_API_KEY');
        $headers = array('Content-Type: text/csv', 'Authorization: bearer ' . $DV_API_KEY);

        $fetchCsvData = $this->Croncb_Model->selectSingleCsvCron(array('dv_job_status' => 1, 'dv_scheduled' => 1, 'dv_singlecsv_run' => 0,"dv_cron_status" => 3));
        

        if (count($fetchCsvData) > 0) {
            foreach ($fetchCsvData as $cv_key => $cv_value) {
                $slug = $cv_value['dv_slug'];
                $headers = array('Content-Type: application/json', 'Authorization: bearer ' . $DV_API_KEY);
                $url = 'https://api.datavalidation.com/1.0/list/' . $slug . '/export.csv?pretty=true';
                // Generate curl request
                $session = curl_init($url);
                curl_setopt($session, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($session, CURLOPT_CAINFO, $this->config->item('rcdata') . 'cacert.pem');
                curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($session);
                /* Start Code to write data in csv */
                $newCsvArray = explode(".", $cv_value['dv_csv_name']);
                $newCsvName = $newCsvArray[0] . "_response.csv";

                $folder_path = $this->config->item('DV_UPLOAD_PATH') . "/" . $cv_value['dv_rc_id'] . "/";
                $filename = $folder_path . $newCsvName;

                $dataa = nl2br($response);
                $dArray = explode('<br />', $dataa);
                $fp = fopen($filename, 'w');
                foreach ($dArray as $a_key => $a_value) {
                    fputcsv($fp, explode(',', trim($a_value)), ',');
                }
                fclose($fp);
                /* End Code to write data in csv */

                if ($response === false) {
                    $this->Croncb_Model->insertErrorLog(array('err_rc_id' => $cv_value['dv_rc_id'], 'err_type' => "Error when export called", 'err_response' => curl_error($session), 'err_createddate' => date('Y-m-d H:i:s')));
                } else {
                    $this->Croncb_Model->update_CsvLog(array("dv_export_status" => 1,'dv_singlecsv_run' => 1,"dv_singlecsv_date" => date('Y-m-d H:i:s'), "dv_export_response" => $newCsvName, "dv_updateddate" => date('Y-m-d H:i:s')), array("dv_id" => $cv_value['dv_id']));
                    
                    /* Start Code to read data in csv */
                    $fp = fopen($filename, 'r');
                    $readCsv = array_map('str_getcsv', file($filename));
                    foreach ($readCsv as $r_key => $r_value) {
                        $unquieId = $r_value[0];
                        $unquieGradeId = $r_value[2];
                        if ($unquieGradeId == 'C' || $unquieGradeId == 'D' || $unquieGradeId == 'F' or trim($r_value[8]) == 'T1') {
							if($cv_value['is_paid']){
								$inputArray_csv = array("dv_grade" => $unquieGradeId, "subscriber_status" => 5, "status_change_date"=> date('Y-m-d H:i:s'));
							}else{	
								$inputArray_csv = array("dv_grade" => $unquieGradeId, "ignore" => 1);
							}	
                        } else {
                            $inputArray_csv = array("dv_grade" => $unquieGradeId);
                        }
                        $this->Croncb_Model->update_account($inputArray_csv, array("subscriber_id" => $unquieId));
						
                        /* Update status in red_dv_cron_setup */
                        $this->Croncb_Model->update_CsvLog(array("dv_cron_status" => 4), array("dv_id" => $cv_value['dv_id']));
                
                
                    }
                    /* End Code to read data in csv */
                }
                curl_close($session);
            }
        }
    }
    
	/* End Code for email_validation_cron - CB */
}

?>
