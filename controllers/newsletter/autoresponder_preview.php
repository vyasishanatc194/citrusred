<?php
/**
  *	Controller class for autoresponder preview
  *	It have controller functions for autoresponder preview management.
 */
class Autoresponder_preview extends CI_Controller
{
	/**
		Contructor for controller.
	*/
	function __construct(){
		parent::__construct();
		$this->load->model('newsletter/Autoresponder_Model');	
		$this->load->model('newsletter/Campaign_Autoresponder_Model');				
		$this->load->model('UserModel');
		$this->load->helper('simple_html_dom');
	}
	/**
		'index' controller function for view of email camapign.	
		@param int id  contain id of campaign
	*/
	function index($id=0,$scheduled_id=0,$subscriber_id=0){
		#if(($scheduled_id>0)&&($subscriber_id>0)){

		#	$this->display_autoresponder_mail($id=0,$scheduled_id=0,$subscriber_id=0);
		#}else{
			# Fetches autoresponder data from database
			$campaign_array=$this->Autoresponder_Model->get_autoresponder_data(array('campaign_id'=>$id));
			
			#Redirects user to listing page if user have not created this campaign or campaign does not exists
			if(!count($campaign_array)){
				redirect('newsletter/autoresponder/display');
				exit;
			}
			$user=$this->UserModel->get_user_data(array('member_id'=>$campaign_array[0]['campaign_created_by']));
			# Prepare array to send to view
			$campaign_data=array('campaign_title'=>$campaign_array[0]['campaign_title'],
			'campaign_template_option'=>$campaign_array[0]['campaign_template_option'],
			'campaign_content'=>$campaign_array[0]['campaign_content'],
			'campaign_text_content'=>$campaign_array[0]['campaign_text_content'],			
			'campaign_status'=>$campaign_array[0]['campaign_status'],			
			'email_subject'=>$campaign_array[0]['email_subject'],
			'rc_logo'=>$user[0]['rc_logo']
			);
			#Remove tabindex="-1" from campaign content
			$campaign_data['campaign_content']=str_replace('tabindex="-1"','',$campaign_data['campaign_content']);
			 
			//  If campaign created by DIY then remove extra characters				
			if($campaign_data['campaign_template_option']!=3){
				$campaign_data['campaign_content']=html_entity_decode($campaign_data['campaign_content'], ENT_QUOTES, "utf-8" );
			}
			
			$campaign_data['campaign_content']= urldecode( $campaign_data['campaign_content']); 
			// Email Personalize campaign 
			$subscriber_info = array('subscriber_id'=>0,'subscriber_email_address'=>'','subscriber_first_name'=>'','subscriber_last_name'=>'','subscriber_state'=>'','subscriber_zip_code'=>'','subscriber_country'=>'','subscriber_city'=>'','subscriber_company'=>'','subscriber_dob'=>'','subscriber_phone'=>'','subscriber_address'=>'','subscriber_extra_fields'=>'');
			$vmta = $user[0]['vmta'];
			$email_personalization = true;
			$is_autoresponder = true;
			$this->Campaign_Autoresponder_Model->getPersonalization($campaign_data['campaign_content'],$campaign_data['campaign_text_content'],$campaign_data['email_subject'],$subscriber_info, $is_autoresponder, $id,$vmta, $email_personalization);
			
			// Remove "view-in-broswesr" link
			$mail_view_link="<table width='100%' style='xxxbackground_colorxxx'><tr><td align='center'><a href='".CAMPAIGN_DOMAIN."a/{$id}'><font size='1' style='font-family:helvetica;font-size:11px;line-height:125%;'>Email not displaying correctly? View in browser</font></a></td></tr></table>";
			$campaign_data['campaign_content'] = replaceTopLinks($campaign_data['campaign_content'], $mail_view_link);
			$campaign_data['campaign_content'] = removeDefaultPreheader( $campaign_data['campaign_content']);
			//$campaign_data['campaign_content'] = removeHTMLElement( $campaign_data['campaign_content'], '.mailTopLink');		
			
			
			// Email Personalize campaign 			
			/* $email_personalize_arr=array();
			$search_email_personalize=$this->get_email_personalize_data($email_personalize_arr);			
			$arrPersonalizeReplace=$this->get_fallback_value($campaign_data['campaign_content'],$email_personalize_arr);
			$campaign_data['campaign_content']=str_replace($search_email_personalize, $arrPersonalizeReplace, $campaign_data['campaign_content']);
			$search_arr=array('[subscriber_id]','[scheduled_id]','[campaign_id]');			
			$replace_arr=array(0,0,$id);
			$campaign_data['campaign_content']=str_replace($search_arr,$replace_arr,$campaign_data['campaign_content']); */
						
			/**
			For text only campaign show footer content here only with word-wrap. And also don't show the logo.
			*/			
			if($campaign_data['campaign_template_option'] == 5){
				$campaign_data['campaign_content'] = wordwrap ( $campaign_data['campaign_text_content'] ,75 ,"\n",true );				
				$campaign_data['rc_logo']= 0;				
			}
			# Load campaign view link
			$this->load->view('newsletter/campaign_link',$campaign_data);
		 
	}
	/**
		display_autoresponder_mail function for view of autoresponder mail
		@param int campign_id contain autoresponder id
		@param int scheduled_id contain autoresponder scheduled id
		@param int id contain subscriber id
	 */
	function display_autoresponder_mail($campaign_id=0,$scheduled_id=0,$id=0){
		
		if(!is_numeric($id)){			
			$id=0;
			echo "error:camapign id not exist";
			exit;
		}		
		
		// check read  mails in autoresponder email track table
		$conditions_array=array('autoresponder_scheduled_id'=>$scheduled_id,'email_track_subscriber_id '=>$id);
		$email_report=$this->Emailreport_Model->update_autoresponder_emailreport(array('email_track_read'=>1,'email_delivered'=>1),$conditions_array);			
	
		// Prepare array for where condition in an autoresponder model
		$fetch_condiotions_array=array('campaign_id'=>$campaign_id);
		// Fetches campaign data from database
		$campaign_array=$this->Autoresponder_Model->get_autoresponder_data($fetch_condiotions_array);
	
		// Prepare array to send to view
		$campaign_data=array('campaign_title'=>$campaign_array[0]['campaign_title'],
		'campaign_template_option'=>$campaign_array[0]['campaign_template_option'],
		'campaign_content'=>$campaign_array[0]['campaign_content'], 
		'campaign_status'=>$campaign_array[0]['campaign_status']
		);
		// Remove tabindex="-1" from campaign content
		$campaign_data['campaign_content']=str_replace('tabindex="-1"','',$campaign_data['campaign_content']);
		// If campign created by DIY	then remove extra characters			
		if($campaign_data['campaign_template_option']!=3){
			$campaign_data['campaign_content']=html_entity_decode($campaign_data['campaign_content'], ENT_QUOTES, "utf-8" );
		}		
		
		// Remove "view-in-broswesr" link
		$campaign_data['campaign_content'] = removeHTMLElement( $campaign_data['campaign_content'], '.mailTopLink');	

		// Email Personalize campaign
		$subscriber_info=$this->Subscriber_Model->get_subscriber_info_view(array('subscriber_id'=>$id,'subscriber_created_by'=>$campaign_array[0]['campaign_created_by']));
		$subscriber_arr=array();
		if(count($subscriber_info)>0){
			foreach($subscriber_info[0] as $key=>$val){	
				$subscriber_arr[$key]=$val;
			}
			$subscriber_arr = array_filter($subscriber_arr);				
		}
		$email_personalize_arr=array();
		$search_email_personalize=$this->get_email_personalize_data($email_personalize_arr);
		
		$arrPersonalizeReplace=$this->get_fallback_value($campaign_data['campaign_content'],$email_personalize_arr);
		
		$replace_email_personalize = array_merge( $arrPersonalizeReplace,$subscriber_arr);
		
		$campaign_data['campaign_content']=str_replace($search_email_personalize, $replace_email_personalize, $campaign_data['campaign_content']);
		$search_arr=array('[subscriber_id]','[scheduled_id]','[campaign_id]');
		$replace_arr=array($id,$scheduled_id,$campaign_id);
		$campaign_data['campaign_content']=str_replace($search_arr,$replace_arr,$campaign_data['campaign_content']);
		// Fetch user info 
		$user=$this->UserModel->get_user_data(array('member_id'=>$campaign_array[0]['campaign_created_by']));
		// Collect Redcappi logo check
		$campaign_data['rc_logo']=$user[0]['rc_logo'];		 
		$this->load->view('newsletter/campaign_link',$campaign_data);
	}
	 
}
?>