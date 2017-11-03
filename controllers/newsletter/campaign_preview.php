<?php
/**
  *	Controller class for campaigns preview
  *	It have controller functions for campaign preview management.
 */
class Campaign_preview extends CI_Controller
{
	/**
		Contructor for controller.
	*/
	function __construct(){
		parent::__construct();
		
		
		$this->load->model('newsletter/Campaign_Model');		
		$this->load->model('newsletter/Campaign_Autoresponder_Model');		
		$this->load->model('UserModel');
		$this->load->model('newsletter/Emailreport_Model'); 	
		$this->load->model('newsletter/Subscriber_Model');
		$this->load->helper('simple_html_dom');
			
	}
	/**
		'index' controller function for view of email camapign.	
		@param int id  contain id of campaign
	*/
	function index($id='',$subsriber_id=0){
	 //remove_ssl();
		if(trim($id) != '' and !is_numeric($id) and base64_encode(base64_decode($id, true)) === $id)
		$id = base64_decode($id);
		
		if(trim($subsriber_id) != '' and base64_encode(base64_decode($subsriber_id)) == $subsriber_id)
		$subsriber_id = base64_decode($subsriber_id);
		
		if($subsriber_id>0){ 
			$this->display_mail($id,$subscriber_id);
		}else{
			// Prepare array for where condition in an campign model
			$fetch_condiotions_array=array('campaign_id'=>$id);
			 
			//  Fetches campaign data from database
			$campaign_array=$this->Campaign_Model->get_campaign_data($fetch_condiotions_array);
		 
			// Redirects user to listing page if user have not created this campaign or campaign does not exists
			if(!count($campaign_array)){
				redirect('newsletter/campaign');
				exit;
			}
			$user=$this->UserModel->get_user_data(array('member_id'=>$campaign_array[0]['campaign_created_by']));	
			
			// Prepare array to send to view
			$campaign_data=array(
				'campaign_title'=>$campaign_array[0]['campaign_title'],
				'campaign_template_option'=>$campaign_array[0]['campaign_template_option'],
				'campaign_content'=>$campaign_array[0]['campaign_content'],
				'campaign_text_content'=>$campaign_array[0]['campaign_text_content'],				
				'campaign_status'=>$campaign_array[0]['campaign_status'],
				'email_subject'=>$campaign_array[0]['email_subject'],
				'rc_logo'=>$user[0]['rc_logo']
			);
			// remove tabindex="-1" from campign content
			$campaign_data['campaign_content']=str_replace('tabindex="-1"','',$campaign_data['campaign_content']);
			
			// If campign created by DIY	then remove extra characters	
			if($campaign_data['campaign_template_option']!=3){				
				$campaign_data['campaign_content']=html_entity_decode( $campaign_data['campaign_content'], ENT_QUOTES, "utf-8" ); 
			}
			$campaign_data['campaign_content']= urldecode( $campaign_data['campaign_content']); 
			// Email Personalize campaign 
			$subscriber_info = array('subscriber_id'=>0,'subscriber_email_address'=>'','subscriber_first_name'=>'','subscriber_last_name'=>'','subscriber_state'=>'','subscriber_zip_code'=>'','subscriber_country'=>'','subscriber_city'=>'','subscriber_company'=>'','subscriber_dob'=>'','subscriber_phone'=>'','subscriber_address'=>'','subscriber_extra_fields'=>'');
			$vmta = $user[0]['vmta'];
			$email_personalization = true;
			$is_autoresponder = false;
			$this->Campaign_Autoresponder_Model->getPersonalization($campaign_data['campaign_content'],$campaign_data['campaign_text_content'],$campaign_data['email_subject'],$subscriber_info, $is_autoresponder, $id,$vmta, $email_personalization);
			// Activate "view-in-broswesr" link
			$mail_view_link="<table width='100%' style='xxxbackground_colorxxx'><tr><td align='center'><a href='".CAMPAIGN_DOMAIN."c/{$id}'><font size='1' style='font-family:helvetica;font-size:11px;line-height:125%;'>Email not displaying correctly? View in browser</font></a></td></tr></table>";
			$campaign_data['campaign_content'] = replaceTopLinks($campaign_data['campaign_content'], $mail_view_link);
			$campaign_data['campaign_content'] = removeDefaultPreheader( $campaign_data['campaign_content']);
			//$campaign_data['campaign_content'] = removeHTMLElement( $campaign_data['campaign_content'], '.mailTopLink');
			
			/**
			For text only campaign show footer content here only with word-wrap. And also don't show the logo.
			*/			
			if($campaign_data['campaign_template_option'] == 5){
				$campaign_data['campaign_content'] = wordwrap ( $campaign_data['campaign_text_content'] ,75 ,"\n",true );				 
				$campaign_footer_text_only = $this->Campaign_Autoresponder_Model->campaign_footer_text_only($user, $campaign_array[0]['campaign_id'], false, true);
				$campaign_data['campaign_content'] .= $campaign_footer_text_only;
				
				$campaign_data['rc_logo']= 0;				
			}
			
			
			#Load Campign view Link
			$this->load->view('newsletter/campaign_link',$campaign_data);
		}
	}
	/**
		Function display_mail to display campaign on mail box
		@param int encrypt_camapaign_id contain encrypted campaign ids
		@param int subscriber_id contain subscriber id
	*/
	function display_mail($encrypt_camapaign_id="",$subscriber_id=0){
		//remove_ssl();
		if(trim($encrypt_camapaign_id) != '' and $this->is_authorized->base64UrlSafeEncode($this->is_authorized->base64UrlSafeDecode($encrypt_camapaign_id)) == $encrypt_camapaign_id){
			$id=$this->is_authorized->base64UrlSafeDecode($encrypt_camapaign_id);		
		}else{
			$id= $encrypt_camapaign_id;
		}
		if(trim($subscriber_id) != '' and $this->is_authorized->base64UrlSafeEncode($this->is_authorized->base64UrlSafeDecode($subscriber_id)) == $subscriber_id){
			list($subscriber_id,$subscriber_email) = @explode('-',trim($this->is_authorized->base64UrlSafeDecode($subscriber_id)));		
		}else{
			$subscriber_id= $subscriber_id;
		}
		 
		//if(!is_numeric($id) or !is_numeric($subscriber_id)){			 
		if(!is_numeric($id)){			 
			//echo "Error: page not found";
			redirect('newsletter/campaign');
			exit;
		}
		
		# Fetch counter of read  mails from email report table for subscriber id	 
		$email_report=$this->Emailreport_Model->get_emailreport_data(array('campaign_id'=>$id,	'subscriber_id'=>$subscriber_id));		
		if($email_report[0]['email_track_read'] <= 0){						
			$this->Emailreport_Model->update_emailreport(array('email_track_read'=>1,'email_track_read_date'=>date('Y-m-d H:i:s',now())),array('campaign_id'=>$id,'subscriber_id'=>$subscriber_id));					
			$this->db->query("update `red_email_campaigns_scheduled` set `email_track_read` = `email_track_read`+1 where campaign_id='$id'");
			$this->db->query("update red_email_subscribers set `read` = `read`+1, `last_read_date`=current_timestamp() where subscriber_id='$subscriber_id'");
			// Increment OPENED global_ipr_daily for major webmails
			$arrEml = @explode('@',$subscriber_email);
			$emlDomain = $arrEml[1];
			if(in_array($emlDomain, config_item('major_domains'))){				
				$rsCampaign = $this->db->query("select DATE_FORMAT(email_send_date, '%Y-%m-%d')email_send_date, pipeline,campaign_created_by user_id  from red_email_campaigns where campaign_id='$id'" );
				$vmta = $rsCampaign->row()->pipeline;
				$email_send_date = ('' != $rsCampaign->row()->email_send_date)?$rsCampaign->row()->email_send_date : date('Y-m-d');
				$user_id = $rsCampaign->row()->user_id;
				$rsCampaign->free_result();				
				$this->db->query("insert into red_global_ipr_daily set `mail_domain` = '$emlDomain' ,  `log_date`='$email_send_date' ,  `pipeline`='$vmta', `user_id`='$user_id', total_opened=(total_opened + 1) ON DUPLICATE  KEY UPDATE  total_opened=(total_opened + 1) ");			
			}
		}		
		
		// Fetches campaign data from database
		$campaign_array=$this->Campaign_Model->get_campaign_data(array(	'campaign_id'=>$id));		
		$user=$this->UserModel->get_user_data(array('member_id'=>$campaign_array[0]['campaign_created_by']));		
		// Prepare array to send to view
		$campaign_data=array('campaign_title'=>$campaign_array[0]['campaign_title'],
		'campaign_content'=>$campaign_array[0]['campaign_content'],
		'campaign_status'=>$campaign_array[0]['campaign_status'],
		'campaign_template_option'=>$campaign_array[0]['campaign_template_option'],
		'email_subject'=>$campaign_array[0]['email_subject'],
		'rc_logo'=>$user[0]['rc_logo']
		);
		// Remove tabindex="-1" from campaign content
		$campaign_data['campaign_content']=str_replace('tabindex="-1"','',$campaign_data['campaign_content']);
		
		// If campign created by DIY	then remove extra characters			
		if($campaign_data['campaign_template_option']!=3){
			$campaign_data['campaign_content']=html_entity_decode( $campaign_data['campaign_content'], ENT_QUOTES, "utf-8" ); 
		}
		$campaign_data['campaign_content']= urldecode( $campaign_data['campaign_content']); 
		// Remove "view-in-broswesr" link
		$campaign_data['campaign_content'] = removeDefaultPreheader( $campaign_data['campaign_content']);
		//$campaign_data['campaign_content'] = removeHTMLElement( $campaign_data['campaign_content'], '.mailTopLink');		 
		$subscriber_info=$this->Subscriber_Model->get_subscriber_info_view(array('subscriber_id'=>$subscriber_id,'subscriber_created_by'=>$campaign_array[0]['campaign_created_by']));		
	 
		$vmta = $user[0]['vmta'];
		$text_message = '';
		$email_personalization = true;
		$is_autoresponder = false;
		$this->Campaign_Autoresponder_Model->getPersonalization($campaign_data['campaign_content'],$text_message,$campaign_data['email_subject'],$subscriber_info[0], $is_autoresponder, $id,$vmta, $email_personalization); 		
		
		$this->load->view('newsletter/campaign_link',$campaign_data);
	}
 	function campaign_view($id){
		# Fetches campaign data from database
		$campaign_array=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$id));
		
		 
		# Prepare array to send to view
		$campaign_data=array(
			'campaign_title'=>$campaign_array[0]['campaign_title'],
			'campaign_template_option'=>$campaign_array[0]['campaign_template_option'],
			'campaign_content'=>$campaign_array[0]['campaign_content'],
			'campaign_status'=>$campaign_array[0]['campaign_status'],
			'email_subject'=>$campaign_array[0]['email_subject']
		);
		 
		$campaign_data['campaign_content']=str_replace('tabindex="-1"','',$campaign_data['campaign_content']);
		 
		if($campaign_data['campaign_template_option']!=3){
			$campaign_data['campaign_content']=html_entity_decode($campaign_data['campaign_content'], ENT_QUOTES, "utf-8" );
		}
		 
		#Load Campign view Link
		$this->load->view('newsletter/campaign_view',$campaign_data);	
	} 
}
?>