<?php
/**
* A Campaign_email_track_restorage class
*
* This class is for taking campaign email stat backup
*
* @version 1.0
* @author Pravin Jha <pravinjha@gmail.com>
* @project Redcappi
*/
class Campaign_email_track_restorage extends CI_Controller
{
	function __construct(){
		parent::__construct();
		$this->load->model('ConfigurationModel');
		$this->load->model('newsletter/Campaign_email_track_restorage_Model');
	}
	/**
	*	Function email_count to count number of email for: send, read, unsubscribe,complaint,bounce, forward emails
	*	@param string mode : for which email list(send, read, unsubscribe,complaint,bounce, forward) fetching email count
	*	@param int campaign_id : campaign id
	*/
	function email_count($mode="",$campaign_id=0){
		$retval = 0;
		if($mode=="send"){		
			$retval = $this->Campaign_email_track_restorage_Model->email_sent_count(array('campaign_id'=>$campaign_id,	'email_sent >'=>0));			
		}elseif($mode=="delivered"){			
			$retval = $this->Campaign_email_track_restorage_Model->email_sent_count(array('campaign_id'=>$campaign_id, 'email_delivered >'=>0));			
		}elseif($mode=="read"){			
			$retval = $this->Campaign_email_track_restorage_Model->email_read_count(array('campaign_id'=>$campaign_id, 'email_sent >'=>0));			
		}elseif($mode=="complaint"){
			$retval = $this->Campaign_email_track_restorage_Model->email_complaint_count(array('campaign_id'=>$campaign_id, 'email_sent >'=>0));
		}elseif($mode=="bounce"){
			$retval = $this->Campaign_email_track_restorage_Model->email_bounce_count(array('campaign_id'=>$campaign_id,'email_sent >'=>0));			
		}elseif($mode=="unsubscribe"){			
			$retval = $this->Campaign_email_track_restorage_Model->email_unsubscribe_count(array('campaign_id'=>$campaign_id, 'email_sent >'=>0));			
		}elseif($mode=="click"){			
			$retval = $this->Campaign_email_track_restorage_Model->email_click_count(array('campaign_id'=>$campaign_id,	'email_sent >'=>0));			
		}elseif($mode=="forward"){			
			$retval = $this->Campaign_email_track_restorage_Model->email_forward_count(array('campaign_id'=>$campaign_id,'email_sent >'=>0));			
		}
		return is_null($retval)?0:$retval;		
	}
	/**
	*	Function index for backup of campaign stat
	*/
	function index(){ 
		set_time_limit(0); 
		// Check cronjob status :completed or working
		$cronjob_status=$this->check_cronjob_status();
		if($cronjob_status=="working"){
			exit;
		}else{
			// update cronjob status to completed
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>'working'),array('config_name'=>'campaign_stat_cronjob_status'));
			
			$site_configuration_array=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'campaign_stat_archive_after_xx_days'));
			$campaign_stat_archive_after_xx_days=$site_configuration_array[0]['config_value'];		
			
			// $fetch_conditions_array=array('email_send_date IS NOT NULL '=>NULL,	'DATEDIFF(CURDATE(),email_send_date)>'=>$campaign_stat_archive_after_xx_days, 'is_deleted'=>0, 'campaign_status'=>'active',	'is_restore'=>'0');			
			//$campaigns=$this->Campaign_email_track_restorage_Model->get_campaign_data($fetch_conditions_array);
			$campaigns=$this->Campaign_email_track_restorage_Model->get_campaign_data($campaign_stat_archive_after_xx_days);			
			// Stroe each campaign stat detail in backup table
			foreach($campaigns as $campaign){				
				// Fetch campaign stats counts
				$email_send_count=$this->email_count("send",$campaign['campaign_id']);				
				$delivered_count=$this->email_count("delivered",$campaign['campaign_id']);				
				$email_read_count=$this->email_count("read",$campaign['campaign_id']);
				$email_complaint_count=$this->email_count("complaint",$campaign['campaign_id']);
				$email_bounce_count=$this->email_count("bounce",$campaign['campaign_id']);				
				$email_unsubscribe_count=$this->email_count("unsubscribe",$campaign['campaign_id']);
				$email_click_count=$this->email_count("click",$campaign['campaign_id']);
				$email_forward_count=$this->email_count("forward",$campaign['campaign_id']);				
				$strIPR ='';
				if($email_send_count>0){
				$strIPR	= $this->ipr($campaign['campaign_id'],'save');
				}
					#Prepare array to send insert values in Campaign_email_track_restorage Model
					$input_array=array(
						'campaign_id'=>$campaign['campaign_id'],
						'user_id'=>$campaign['campaign_created_by'],
						'send_email_count'=>$email_send_count,
						'delivered_count'=>$delivered_count,
						'read_email_count'=>$email_read_count,
						'click_link_count'=>$email_click_count,
						'bounce_email_count'=>$email_bounce_count,
						'complaint_email_count'=>$email_complaint_count,
						'unsubscribes_email_count'=>$email_unsubscribe_count,
						'forward_email_count'=>$email_forward_count,
						'ipr'=>$strIPR
					);
					// freeze stats
					$this->Campaign_email_track_restorage_Model->add_email_track_restorage($input_array);
					 
					// Delete stat from email track table
					$this->Campaign_email_track_restorage_Model->delete_email_track(array('campaign_id'=>$campaign['campaign_id']));
					// Delete stat from red_email_campaigns_scheduled table
					$this->Campaign_email_track_restorage_Model->delete_email_track_list(array('campaign_id'=>$campaign['campaign_id']));
					// Delete stat from email click link table
					$this->Campaign_email_track_restorage_Model->delete_email_click_rate(array('campaign_id'=>$campaign['campaign_id']));
					$this->Campaign_email_track_restorage_Model->update_campaign(array('is_restore'=>'1'),array('campaign_id'=>$campaign['campaign_id']));
				}			
			
			// update cronjob status to completed
			$this->ConfigurationModel->update_site_configuration(array('config_value'=>'completed'),array('config_name'=>'campaign_stat_cronjob_status'));
		}
	}
	

	/**
	*	Function ipr to get popup HTML string for Inbox Placement Ratio	
	*	@param int campaign_id : campaign id
	*/
	function ipr($cid=0, $mode=''){		
		$whereClause = " and `campaign_id`='$cid'";
		$rsPipeline = $this->db->query("select ifnull(`pipeline`,'') as pipeline from red_email_campaigns where campaign_id='$cid'");	
		$pipeline = $rsPipeline->row()->pipeline;
		$rsPipeline->free_result();
		
		$sqlTotalForCampaign = "select sum(email_sent) as sent,sum(email_delivered) as delivered,sum(`email_track_read`) as opened,sum(`email_track_click`) as clicks,sum(`email_track_bounce`) as bounced,sum(`email_track_complaint`) as complaints,sum(`email_track_unsubscribes`) as unsubscribes from red_email_track where 1 $whereClause  order by sent";
		$rsTotalForCampaign = $this->db->query($sqlTotalForCampaign);
		if($rsTotalForCampaign->num_rows()>0){
			$intTotalSent_0 = $rsTotalForCampaign->row()->sent;			
			$intTotalSent = $rsTotalForCampaign->row()->delivered;
			if($intTotalSent_0 <= 0 )die('<div style="padding:20px;width:200px;">No records found!</div>');
		$strTblFooter = '<tr><td>TOTAL:</td><td>'.$rsTotalForCampaign->row()->sent.'</td><td>'.$rsTotalForCampaign->row()->delivered.'('.number_format(($rsTotalForCampaign->row()->delivered)*100/$intTotalSent_0,2).'%)</td><td>'.$rsTotalForCampaign->row()->opened.'</td><td>'.$rsTotalForCampaign->row()->clicks.'</td><td>'.$rsTotalForCampaign->row()->bounced.'</td><td>'.$rsTotalForCampaign->row()->complaints.'</td><td>'.$rsTotalForCampaign->row()->unsubscribes.'</td></tr>';
		
		}
		 // domain wise counter
		$sqlDomainwiseForCampaign ="select sum(email_sent) as sent,sum(email_delivered) as delivered,sum(`email_track_read`) as opened,sum(`email_track_click`) as clicks, sum(`email_track_bounce`) as bounced,sum(`email_track_complaint`) as complaints,sum(`email_track_unsubscribes`) as unsubscribes, SUBSTRING(subscriber_email_address, instr(subscriber_email_address, '@'), LENGTH(subscriber_email_address)) AS domainname from red_email_track where 1 $whereClause group by domainname having count(queue_id)>50 order by sent desc";
		$rsDomainwiseForCampaign = $this->db->query($sqlDomainwiseForCampaign);
		if($rsDomainwiseForCampaign->num_rows()>0){
			foreach($rsDomainwiseForCampaign->result() as $row){
				$strTblBody .= '<tr><td>'.$row->domainname.'</td>';
				$strTblBody .= '<td>'.$row->sent.'('.number_format(($row->sent)*100/$intTotalSent,2).'%)</td>';
				$strTblBody .= '<td>'.$row->delivered.'('.number_format(($row->delivered)*100/$intTotalSent,2).'%)</td>';
				if($row->opened > 0 && $row->delivered > 0)
				$strTblBody .= '<td>'.$row->opened.'('.number_format(($row->opened)*100/($row->delivered),2).'%)</td>';
				else
				$strTblBody .= '<td>'.$row->opened.'(0%)</td>';
				if($row->clicks > 0 && $row->delivered > 0)
				$strTblBody .= '<td>'.$row->clicks.'('.number_format(($row->clicks)*100/($row->delivered),2).'%)</td>';
				else
				$strTblBody .= '<td>'.$row->clicks.'(0%)</td>';
				if($row->bounced > 0 && $row->delivered > 0)
				$strTblBody .= '<td>'.$row->bounced.'('.number_format(($row->bounced)*100/($row->delivered),2).'%)</td>';
				else
				$strTblBody .= '<td>'.$row->bounced.'(0%)</td>';
				if($row->complaints > 0 && $row->delivered > 0)
				$strTblBody .= '<td>'.$row->complaints.'('.number_format(($row->complaints)*100/($row->delivered),2).'%)</td>';
				else
				$strTblBody .= '<td>'.$row->complaints.'(0%)</td>';
				if($row->unsubscribes > 0 && $row->delivered > 0)
				$strTblBody .= '<td>'.$row->unsubscribes.'('.number_format(($row->unsubscribes)*100/($row->delivered),2).'%)</td></tr>';		
				else
				$strTblBody .= '<td>'.$row->unsubscribes.'(0%)</td></tr>';		
			}
		}
		if($pipeline !='')$pipeline = "<span style='float:right;margin:10px 0;'><b>Pipeline:</b> $pipeline</span>";	
		$strIPR =  '<div style="padding:20px;">'.$pipeline.'		
		<table cellspacing="0" cellpadding="4" border="1"><tr><th>Domain</th><th>Sent</th><th>Delivered</th><th>Opened</th><th>Clicks</th><th>Bounced</th><th>Complaint</th><th>Unsubscribed</th>' .$strTblBody . $strTblFooter.'</table></div>';
		if($mode=='save')return $strIPR; else echo $strIPR;
	}
	/**
	*	Function check_cronjob_status to fetch cronjob status
	*	@ return string cronjob_status
	*/
	function check_cronjob_status(){
		// Load the user model which interact with database
		$this->load->model('ConfigurationModel');
		$confg_arr=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'campaign_stat_cronjob_status'));
		return $confg_arr[0]['config_value'];
	}
}
?>