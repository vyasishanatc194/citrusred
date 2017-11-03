<?php
		//error_reporting(E_ALL);
/************About Us class********************/
class CreateXlsx extends CI_Controller{
	private $domainsToShow;
	function __construct(){
        parent::__construct();
		$this->load->model('newsletter/Emailreport_Model');
		$this->load->helper('phpexcel');
		//$this->domainsToShow = array('gmail.com','yahoo.com','hotmail.com','aol.com');	
		$this->domainsToShow = array('hotmail.com','aol.com');	
	}
	/**
	* updateSubscriber updates all subscribers with their total campaign sent, read, clicked, forwarded etc.
	*/
	function listSoftbounces($x=8){
		ini_set('memory_limit', '-1');
		set_time_limit(0); 
		$sqlCountSubscribers = "select count(subscriber_id) as totcontact from red_email_subscribers where subscriber_status=1 and is_deleted=0 ";
		$rsCountSubscribers = $this->db->query($sqlCountSubscribers);
		$totalSubscribers = $rsCountSubscribers->row()->totcontact;
		
		$rsCountSubscribers->free_result(); 
		$pageSize =10000;
		echo "<br/>Starts at:".date('Y-m-d H:i:s');
		for($start=0; $start < $totalSubscribers; $start += $pageSize){
			$sqlSubscribers = "select subscriber_id from red_email_subscribers where subscriber_status=1 and is_deleted=0 limit $start, $pageSize";
			$starttime = now(); 
		
			
			$rsSubscribers = $this->db->query($sqlSubscribers);
			foreach($rsSubscribers->result_array() as $contact){
				$contact_id = $contact['subscriber_id'];
				$sqlGetStats = "select count(queue_id) as bounced, subscriber_id from red_email_track where email_track_bounce=2 and subscriber_id='$contact_id' group by subscriber_id";
				$rsGetStats = $this->db->query($sqlGetStats);
				if($rsGetStats->num_rows() > 0){
					$bounced = $rsGetStats->row()->bounced;
					if($bounced >=$x)
					echo "<br/>".$contact_id.', '.$bounced;				
				}
				$rsGetStats->free_result();			
			}
			$rsSubscribers->free_result(); 
			//----------------------------------
		//	$executionTime = now()- $starttime;
		//echo "<br/>Execution time: $executionTime seconds -- $sqlSubscribers";
		}
		echo "<br/>Ends at:".date('Y-m-d H:i:s');		
	
	}




/**
	* updateSubscriber updates all subscribers with their total campaign sent, read, clicked, forwarded etc.
	*/
	function updateSubscriber(){
		ini_set('memory_limit', '-1');
		set_time_limit(0); 
		$sqlCountSubscribers = "select count(subscriber_id) as totcontact from red_email_subscribers where subscriber_status=1 and is_deleted=0 ";
		$rsCountSubscribers = $this->db->query($sqlCountSubscribers);
		$totalSubscribers = 5000000;//$rsCountSubscribers->row()->totcontact;
		$rsCountSubscribers->free_result(); 
		$pageSize =25000;
		echo "<br/>Starts at:".date('Y-m-d H:i:s');
		for($start=0; $start < $totalSubscribers; $start += $pageSize){
			$sqlSubscribers = "select subscriber_id from red_email_subscribers where subscriber_status=1 and is_deleted=0 limit $start, $pageSize";
			$starttime = now(); 
		
			
			$rsSubscribers = $this->db->query($sqlSubscribers);
			foreach($rsSubscribers->result_array() as $contact){
				$contact_id = $contact['subscriber_id'];
				$sqlGetStats = "select count(campaign_id) as sent, sum(email_track_read) as totread,sum(email_track_click) as clicked,sum(email_track_forward) as forwarded from red_email_track where subscriber_id='$contact_id' group by subscriber_id";
				$rsGetStats = $this->db->query($sqlGetStats);
				if($rsGetStats->num_rows() > 0){
				$sent = $rsGetStats->row()->sent;
				$read = $rsGetStats->row()->totread;
				$clicked = $rsGetStats->row()->clicked;
				$forwarded = $rsGetStats->row()->forwarded;
				 
				$this->db->query("update red_email_subscribers set sent='$sent', `read`='$read', clicked='$clicked', forwarded='$forwarded' where subscriber_id='$contact_id'");
				}
				$rsGetStats->free_result();			
			}
			$rsSubscribers->free_result(); 
			//----------------------------------
			$executionTime = now()- $starttime;
		echo "<br/>Execution time: $executionTime seconds -- $sqlSubscribers";
		}
		echo "<br/>Ends at:".date('Y-m-d H:i:s');		
	
	}







	
	function totalSent(){
		/** Error reporting */
		//error_reporting(E_ALL);
		//ini_set('display_errors', TRUE);
		//ini_set('display_startup_errors', TRUE);
		ini_set('memory_limit', '-1');
		set_time_limit(0); 
		foreach($this->domainsToShow as $isp){	
			$totalSent = 0;
			$sqlSent = "select campaign_id from red_email_campaigns where campaign_status ='active' and  `email_send_date` between '2013-03-23 00:00:01' and '2013-06-23 00:00:01'";
			$resSent = $this->db->query($sqlSent);
			if($resSent->num_rows()>0){	
				foreach($resSent->result_array() as $row){
					$campaign_id = $row['campaign_id'];
					$totalSent += $this->getSent($campaign_id, '@'.$isp);					
				}
			}
			echo "<br/>".$isp.'--'. $totalSent;
			
		}
		
	}
	function opened_report(){
		/** Error reporting */
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		ini_set('memory_limit', '-1');
		set_time_limit(0); 
		
		$objPHPExcel = new PHPExcel();		
		$objPHPExcel->getProperties()->setCreator("Pravin Jha")
									 ->setLastModifiedBy("Pravin Jha")
									 ->setTitle("RedCappi Devliverability Document")
									 ->setSubject("RedCappi Devliverability Document")
									 ->setDescription("Devliverability document for campaigns having more than 500 contacts, generated for RedCappi.")
									 ->setKeywords("RedCappi Devliverability Document")
									 ->setCategory("Devliverability Document");
									 
		$objPHPExcel->setActiveSheetIndex(0)					
					->setCellValue('A1', 'ISP')
					->setCellValue('B1', 'Read');
		$excel_data_row = 2;			
		foreach($this->domainsToShow as $isp){	
			$totalOpens = 0;
			$sqlOpens = "select campaign_id from red_email_campaigns_scheduled where email_track_read > 0 and  `campaign_scheduled_date` between '2013-03-23 00:00:01' and '2013-06-23 00:00:01'";
			$resOpens = $this->db->query($sqlOpens);
			if($resOpens->num_rows()>0){	
				foreach($resOpens->result_array() as $row){
					$campaign_id = $row['campaign_id'];
					$totalOpens += $this->getOpens($campaign_id, '@'.$isp);					
				}
			}
			$objPHPExcel->setActiveSheetIndex(0)								
						->setCellValue('A'.$excel_data_row, $isp)								
						->setCellValue('B'.$excel_data_row, $totalOpens);
			$excel_data_row++;			
		}
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		$filename = "opens_by_isp_report_".date('Ymdhis');

		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
	function bounce_report(){
		/** Error reporting */
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		ini_set('memory_limit', '-1');
		set_time_limit(0); 
		
		$objPHPExcel = new PHPExcel();		
		$objPHPExcel->getProperties()->setCreator("Pravin Jha")
									 ->setLastModifiedBy("Pravin Jha")
									 ->setTitle("RedCappi Devliverability Document")
									 ->setSubject("RedCappi Devliverability Document")
									 ->setDescription("Devliverability document for campaigns having more than 500 contacts, generated for RedCappi.")
									 ->setKeywords("RedCappi Devliverability Document")
									 ->setCategory("Devliverability Document");
									 
		$objPHPExcel->setActiveSheetIndex(0)					
					->setCellValue('A1', 'ISP')
					->setCellValue('B1', 'Bounces');
		$excel_data_row = 2;			
		foreach($this->domainsToShow as $isp){	
			$totalBounces = 0;
			$sqlBounces = "select campaign_id from red_email_campaigns_scheduled where email_track_bounce > 0 and  `campaign_scheduled_date` between '2013-03-23 00:00:01' and '2013-06-23 00:00:01'";
			$resBounces = $this->db->query($sqlBounces);
			if($resBounces->num_rows()>0){	
				foreach($resBounces->result_array() as $row){
					$campaign_id = $row['campaign_id'];
					$totalBounces += $this->getBounces($campaign_id, '@'.$isp);					
				}
			}
			$objPHPExcel->setActiveSheetIndex(0)								
						->setCellValue('A'.$excel_data_row, $isp)								
						->setCellValue('B'.$excel_data_row, $totalBounces);
			$excel_data_row++;			
		}
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		$filename = "bounces_by_isp_report_".date('Ymdhis');

		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
	function complaint_report(){
		/** Error reporting */
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		ini_set('memory_limit', '-1');
		set_time_limit(0); 
		
		$objPHPExcel = new PHPExcel();		
		$objPHPExcel->getProperties()->setCreator("Pravin Jha")
									 ->setLastModifiedBy("Pravin Jha")
									 ->setTitle("RedCappi Devliverability Document")
									 ->setSubject("RedCappi Devliverability Document")
									 ->setDescription("Devliverability document for campaigns having more than 500 contacts, generated for RedCappi.")
									 ->setKeywords("RedCappi Devliverability Document")
									 ->setCategory("Devliverability Document");
									 
		$objPHPExcel->setActiveSheetIndex(0)					
					->setCellValue('A1', 'ISP')
					->setCellValue('B1', 'Complaints');
		$excel_data_row = 2;			
		foreach($this->domainsToShow as $isp){	
			$totalComplaint = 0;
			$sqlCampaign = "select campaign_id from red_email_campaigns_scheduled where email_track_spam > 0 and  `campaign_scheduled_date` between '2013-03-23 00:00:01' and '2013-06-23 00:00:01'";
			$resCampaign = $this->db->query($sqlCampaign);
			if($resCampaign->num_rows()>0){	
				foreach($resCampaign->result_array() as $row){
					$campaign_id = $row['campaign_id'];
					$totalComplaint += $this->getComplaints($campaign_id, '@'.$isp);					
				}
			}
			$objPHPExcel->setActiveSheetIndex(0)								
						->setCellValue('A'.$excel_data_row, $isp)								
						->setCellValue('B'.$excel_data_row, $totalComplaint);
			$excel_data_row++;			
		}
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		$filename = "complaint_by_isp_report_".date('Ymdhis');

		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
	function report1(){
		
		$objPHPExcel = new PHPExcel();		
		$objPHPExcel->getProperties()->setCreator("Pravin Jha")
									 ->setLastModifiedBy("Pravin Jha")
									 ->setTitle("RedCappi Devliverability Document")
									 ->setSubject("RedCappi Devliverability Document")
									 ->setDescription("Devliverability document for campaigns having more than 500 contacts, generated for RedCappi.")
									 ->setKeywords("RedCappi Devliverability Document")
									 ->setCategory("Devliverability Document");
									 
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'Client ID')
					->setCellValue('B1', 'Client Name')
					->setCellValue('C1', 'Campaign ID')
					->setCellValue('D1', 'Sent Date')
					->setCellValue('E1', 'Total Sent')
					->setCellValue('F1', 'ISP')
					->setCellValue('G1', 'Sent')
					->setCellValue('H1', 'Delivered')
					->setCellValue('I1', 'Opened')
					->setCellValue('J1', 'Bounced')
					->setCellValue('K1', 'Complaints');

		//$sqlData = "select `campaign_id`,`campaign_created_by`, `email_send_date` from red_email_campaigns where `campaign_status`='active' and `email_send_date` > '2013-05-23 00:00:01' order by `email_send_date` desc";
		//$sqlData = "select `campaign_id`,`campaign_created_by`, `email_send_date` from red_email_campaigns where `campaign_status`='active' and `email_send_date` between '2013-04-23 00:00:01' and '2013-05-23 00:00:01' order by `email_send_date` desc";
		$sqlData = "select `campaign_id`,`campaign_created_by`, `email_send_date` from red_email_campaigns where `campaign_status`='active' and `email_send_date` between '2013-03-23 00:00:01' and '2013-04-23 00:00:01' order by `email_send_date` desc";
		$result=$this->db->query($sqlData);

		$excel_data_row = 	2;
		if($result->num_rows()>0){	
			foreach($result->result_array() as $row){
				$campaign_id = $row['campaign_id'];
				$sent_date = $row['email_send_date'];
				$client_id = $row['campaign_created_by'];
				$total_sent =$this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$campaign_id));
				if($total_sent > 5000){	
					foreach($this->domainsToShow as $isp){
						$IPR 	= $this->getIPR($campaign_id, '@'.$isp);	
						$objPHPExcel->setActiveSheetIndex(0)
									->setCellValue('A'.$excel_data_row, $client_id)
									->setCellValue('B'.$excel_data_row, $this->getClientName($client_id))
									->setCellValue('C'.$excel_data_row, $campaign_id)
									->setCellValue('D'.$excel_data_row, $sent_date)
									->setCellValue('E'.$excel_data_row, $total_sent)
									->setCellValue('F'.$excel_data_row, $isp)
									->setCellValue('G'.$excel_data_row, $IPR['sent'])
									->setCellValue('H'.$excel_data_row, $IPR['delivered'])
									->setCellValue('I'.$excel_data_row, $IPR['opened'])
									->setCellValue('J'.$excel_data_row, $IPR['bounced'])
									->setCellValue('K'.$excel_data_row, $IPR['complaints']);
						
						$excel_data_row++;
					}
				}
			}
		}


		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		$filename = "delivery_report_".date('Ymdhis');

		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
	
	function getClientName($member_id){
		return $this->db->query("select member_username from red_members where member_id='$member_id'")->row()->member_username;	
	}
	function getIPR($cid=0, $domain=''){
		$arrRetVal = array();
		$sqlDomainwiseForCampaign ="select count(queue_id) as sent,sum(`email_track_read`) as opened,sum(`email_delivered`) as delivered,sum(`email_track_bounce`) as bounced,sum(`email_track_complaint`) as complaints, SUBSTRING(subscriber_email_address, instr(subscriber_email_address, '@'), LENGTH(subscriber_email_address)) AS domainname from red_email_track where `campaign_id`='$cid' and SUBSTRING(subscriber_email_address, instr(subscriber_email_address, '@'), LENGTH(subscriber_email_address)) = '$domain' group by domainname";
		$rsDomainwiseForCampaign = $this->db->query($sqlDomainwiseForCampaign);
		if($rsDomainwiseForCampaign->num_rows()>0){
			foreach($rsDomainwiseForCampaign->result() as $row){
				$arrRetVal['ISP'] 		= $row->domainname;
				$arrRetVal['sent'] 		= $row->sent;
				$arrRetVal['delivered'] = $row->delivered;
				$arrRetVal['opened'] 	= $row->opened;
				$arrRetVal['bounced'] 	= $row->bounced;
				$arrRetVal['complaints']= $row->complaints;	
			}
		}
		return $arrRetVal;
	}
	function getSent($cid=0, $domain=''){
		ini_set('memory_limit', '-1');
		set_time_limit(0); 
		$arrRetVal = 0;
		$sqlDomainwiseForCampaign ="select count(`queue_id`) as sent, SUBSTRING(subscriber_email_address, instr(subscriber_email_address, '@'), LENGTH(subscriber_email_address)) AS domainname from red_email_track where `campaign_id`='$cid' and SUBSTRING(subscriber_email_address, instr(subscriber_email_address, '@'), LENGTH(subscriber_email_address)) = '$domain' group by domainname";
		$rsDomainwiseForCampaign = $this->db->query($sqlDomainwiseForCampaign);
		if($rsDomainwiseForCampaign->num_rows()>0){
			foreach($rsDomainwiseForCampaign->result() as $row){
				//$arrRetVal['ISP'] 		= $row->domainname;				
				$arrRetVal= $row->sent;	
			}
		}
		return $arrRetVal;
	}
	function getBounces($cid=0, $domain=''){
		$arrRetVal = 0;
		$sqlDomainwiseForCampaign ="select sum(`email_track_bounce`) as bounce, SUBSTRING(subscriber_email_address, instr(subscriber_email_address, '@'), LENGTH(subscriber_email_address)) AS domainname from red_email_track where `campaign_id`='$cid' and SUBSTRING(subscriber_email_address, instr(subscriber_email_address, '@'), LENGTH(subscriber_email_address)) = '$domain' group by domainname";
		$rsDomainwiseForCampaign = $this->db->query($sqlDomainwiseForCampaign);
		if($rsDomainwiseForCampaign->num_rows()>0){
			foreach($rsDomainwiseForCampaign->result() as $row){
				//$arrRetVal['ISP'] 		= $row->domainname;				
				$arrRetVal= $row->bounce;	
			}
		}
		return $arrRetVal;
	} 
	function getOpens($cid=0, $domain=''){
		$arrRetVal = 0;
		$sqlDomainwiseForCampaign ="select sum(`email_track_read`) as opens, SUBSTRING(subscriber_email_address, instr(subscriber_email_address, '@'), LENGTH(subscriber_email_address)) AS domainname from red_email_track where `campaign_id`='$cid' and SUBSTRING(subscriber_email_address, instr(subscriber_email_address, '@'), LENGTH(subscriber_email_address)) = '$domain' group by domainname";
		$rsDomainwiseForCampaign = $this->db->query($sqlDomainwiseForCampaign);
		if($rsDomainwiseForCampaign->num_rows()>0){
			foreach($rsDomainwiseForCampaign->result() as $row){
				//$arrRetVal['ISP'] 		= $row->domainname;				
				$arrRetVal= $row->opens;	
			}
		}
		return $arrRetVal;
	} 
	function getComplaints($cid=0, $domain=''){
		$arrRetVal = 0;
		$sqlDomainwiseForCampaign ="select sum(`email_track_complaint`) as complaints, SUBSTRING(subscriber_email_address, instr(subscriber_email_address, '@'), LENGTH(subscriber_email_address)) AS domainname from red_email_track where `campaign_id`='$cid' and SUBSTRING(subscriber_email_address, instr(subscriber_email_address, '@'), LENGTH(subscriber_email_address)) = '$domain' group by domainname";
		$rsDomainwiseForCampaign = $this->db->query($sqlDomainwiseForCampaign);
		if($rsDomainwiseForCampaign->num_rows()>0){
			foreach($rsDomainwiseForCampaign->result() as $row){
				//$arrRetVal['ISP'] 		= $row->domainname;				
				$arrRetVal= $row->complaints;	
			}
		}
		return $arrRetVal;
	} 
}
