<?php
/*
	Controller class for Powered by redcappi
*/
class Powered_by_redcappi extends CI_Controller
{

	/*
		Contructor for controller.		
	*/
	function __construct()
    {
        parent::__construct();
    }
	
	/**
		Mark mail as read
	**/
	function index($id=0,$subscriber_id=0){
		// Load emailreport model class which handles database interaction
		$this->load->model('newsletter/Emailreport_Model');
		// Get Total number of click  mails for subscription list
		
		
		if($subscriber_id !=''){
			$arrSubscriber = $this->is_authorized->decodeSubscriber($subscriber_id);
			list($subscriber_id,$subscriber_email) = $arrSubscriber;		
		}
		$fetch_condiotions_array=array('campaign_id'=>$id,'subscriber_id'=>$subscriber_id,'subscriber_email_address'=> $subscriber_email,'email_sent'=>1);
		$email_report=$this->Emailreport_Model->get_emailreport_data($fetch_condiotions_array);
		if($email_report[0]['email_track_read']<=0){			
			$this->Emailreport_Model->update_emailreport(array('email_track_read'=>1,'email_track_read_date'=>date('Y-m-d H:i:s',now())), array('campaign_id'=>$id,'subscriber_id'=>$subscriber_id));						
			$this->db->query("update `red_email_campaigns_scheduled` set `email_track_read` = `email_track_read`+1 where campaign_id='$id'");			 
			$this->db->query("update red_email_subscribers set `read` = `read`+1, `last_read_date`=current_timestamp() where subscriber_id='$subscriber_id'");
			// Increment OPENED global_ipr_daily for major webmails
			$arrEml = @explode('@',$subscriber_email);
			$emlDomain = $arrEml[1];
			if(in_array($emlDomain, config_item('major_domains'))){				
				$rsCampaign = $this->db->query("select DATE_FORMAT(email_send_date, '%Y-%m-%d')email_send_date, pipeline,campaign_created_by user_id  from red_email_campaigns where campaign_id='$id'" );
				$vmta = $rsCampaign->row()->pipeline;
				$email_send_date = $rsCampaign->row()->email_send_date;
				$user_id = $rsCampaign->row()->user_id;
				$rsCampaign->free_result();				
				$this->db->query("insert into red_global_ipr_daily set `mail_domain` = '$emlDomain' ,  `log_date`='$email_send_date' ,  `pipeline`='$vmta', `user_id`='$user_id', total_opened=(total_opened + 1) ON DUPLICATE  KEY UPDATE  total_opened=(total_opened + 1) ");			
			}
		}
		
		// update for subscription
		
		redirect('https://www.redcappi.com/st/pbrc/?source=pbl');
	}
	
}
?>