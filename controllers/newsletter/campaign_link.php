<?php
/**
* A Campaign_link class
*
* This class is for display campaign mail
*
* @version 1.0
* @author Pravin Jha <pravinjha@gmail.com>
* @project Redcappi
*/

class Campaign_link extends CI_Controller
{
	/**
		Contructor for controller.
	*/
	function __construct(){
		parent::__construct();
		
		$this->load->model('newsletter/Emailreport_Model');		 
		$this->load->model('newsletter/Campaign_Model');			 
		$this->load->model('UserModel');				 
		$this->load->model('newsletter/Subscriber_Model');
		$this->load->model('newsletter/Page_Model');
		$this->load->model('newsletter/Campaign_Autoresponder_Model');	
	}
	/**
		'Index' controller. By default it calls display controller.
	*/
	function index(){
		$this->display();
	}
	/**
		'Dislay' controller function for view of email camapign.	
		@param int id  contain id of campaign
	*/
	function display($id=0){
		 
		# Fetches campaign data from database
		$campaign_array=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$id, 'campaign_created_by'=>$this->session->userdata('member_id') ));
		
		#Redirects user to listing page if user have not created this campaign or campaign does not exists
		if(!count($campaign_array)){
			redirect('newsletter/campaign');
		}
		# Prepare array to send to view
		$campaign_data=array(
			'campaign_title'=>$campaign_array[0]['campaign_title'],
			'campaign_template_option'=>$campaign_array[0]['campaign_template_option'],
			'campaign_content'=>$campaign_array[0]['campaign_content'],
			'campaign_status'=>$campaign_array[0]['campaign_status'],
			'email_subject'=>$campaign_array[0]['email_subject']
		);
		#remove tabindex="-1" from campign content
		$campaign_data['campaign_content']=str_replace('tabindex="-1"','',$campaign_data['campaign_content']);
		#############################################################
		# If campign created by DIY	then remove extra characters	#
		#############################################################
		if($campaign_data['campaign_template_option']!=3){
			$campaign_data['campaign_content']=html_entity_decode($campaign_data['campaign_content'], ENT_QUOTES, "utf-8" );
		}
		# get page id from page table and check that is_autoresponder should be equal to zero
		$loaded_page=$this->Page_Model->get_page_data(array('site_id'=>$id,'is_autoresponder'=>0));
		
		#############################
		#Email Personalize campaign #
		#############################
		$email_personalize_arr=array();
		$search_email_personalize=$this->get_email_personalize_data($email_personalize_arr);

		$arrPersonalizeReplace=$this->get_fallback_value($campaign_data['campaign_content'],$email_personalize_arr);

		$campaign_data['campaign_content']=str_replace($search_email_personalize, $arrPersonalizeReplace, $campaign_data['campaign_content']);		
		# Collect Redcappi logo check
		$campaign_data['rc_logo']=1;
		#Load Campign view Link
		$this->load->view('newsletter/campaign_link',$campaign_data);		
	}
	/**
		Function share_on_facebook to display campign on facebook
		@param int id contain campign ids
	*/
	function share_on_facebook($id=0){
		# Load campaign model class which handles database interaction
		$this->load->model('newsletter/Campaign_Model');		
		# Load subscriber model class which handles database interaction
		$this->load->model('newsletter/Subscriber_Model');		
		# Load page model class which handles database interaction
		$this->load->model('newsletter/Page_Model');
		# Load user model class which handles database interaction
		$this->load->model('UserModel');
		
		#Prepare array for where condition in an campign model
		$fetch_condiotions_array=array(
		'campaign_id'=>$id,
		);
		# Fetches campaign data from database
		$campaign_array=$this->Campaign_Model->get_campaign_data($fetch_condiotions_array);			
		
		# Prepare array to send to view
		$campaign_data=array('campaign_title'=>$campaign_array[0]['campaign_title'],
		'campaign_content'=>$campaign_array[0]['campaign_content'],		
		'campaign_status'=>$campaign_array[0]['campaign_status'],
		'campaign_template_option'=>$campaign_array[0]['campaign_template_option'],
		'email_subject'=>$campaign_array[0]['email_subject']
		);
		#remove tabindex="-1" from campign content
		$campaign_data['campaign_content']=str_replace('tabindex="-1"','',$campaign_data['campaign_content']);
		#############################################################
		# If campign created by DIY	then remove extra characters	#
		#############################################################
		if($campaign_data['campaign_template_option']!=3){
			$campaign_data['campaign_content']=html_entity_decode($campaign_data['campaign_content'], ENT_QUOTES, "utf-8" );
		}
		# get page id from page table and check that is_autoresponder should be equal to zero
		$loaded_page=$this->Page_Model->get_page_data(array('site_id'=>$id,'is_autoresponder'=>0));
		
		$campaign_data['campaign_content']=$campaign_data['campaign_content'];
		#############################
		#Email Personalize campaign #
		#############################
		$email_personalize_arr=array();
		$search_email_personalize=$this->get_email_personalize_data($email_personalize_arr);

		$arrPersonalizeReplace=$this->get_fallback_value($campaign_data['campaign_content'],$email_personalize_arr);

		$campaign_data['campaign_content']=str_replace($search_email_personalize, $arrPersonalizeReplace, $campaign_data['campaign_content']);
		#Fetch user info
		$user=$this->UserModel->get_user_data(array('member_id'=>$campaign_array[0]['campaign_created_by']));
		# Collect Redcappi logo check
		$campaign_data['rc_logo']=$user[0]['rc_logo'];
		#Load Campaign view Link
		$this->load->view('newsletter/campaign_link',$campaign_data);
	}
	/**
		Function display_mail to display campaign on mail box
		@param int encrypt_camapaign_id contain encrypted campaign ids
		@param int subscriber_id contain subscriber id
	*/
	function display_mail($encrypt_camapaign_id="",$subscriber_id=0){
		# Decode campign id
		$id=$this->is_authorized->base64UrlSafeDecode($encrypt_camapaign_id);
		#Protecting MySQL from query string sql injection Attacks
		if((is_numeric($subscriber_id))&&(is_numeric($id))){
			$subscriber_id = $subscriber_id;
		}else{
			$subscriber_id=0;
			echo "error:subscriber id not exist";
			exit;
		}
		# Check subscriber id and id sholud be greater than zero else campaign not exist
		if(($subscriber_id>0)&&($id>0)){
			$subscriber_id = $subscriber_id;
		}else{
			$subscriber_id=0;
			echo "error:subscriber id not exist";
			exit;
		}		
		
		# Fetch counter of read  mails from email report table for subscriber id
		$email_report=$this->Emailreport_Model->get_emailreport_data(array('campaign_id'=>$id,'subscriber_id'=>$subscriber_id));		
		if($email_report[0]['email_track_read']<=0){				 
			$this->Emailreport_Model->update_emailreport(array('email_track_read'=>1,'email_track_read_date'=>date('Y-m-d H:i:s',now())), array('campaign_id'=>$id,'subscriber_id'=>$subscriber_id));	
			$this->db->query("update `red_email_campaigns_scheduled` set `email_track_read` = `email_track_read`+1 where campaign_id='$id'");		
			$this->db->query("update red_email_subscribers set `read` = `read`+1, `last_read_date`=current_timestamp() where subscriber_id='$subscriber_id'");
			// Increment OPENED global_ipr_daily for major webmails
			$arrEml = @explode('@',$email_report[0]['subscriber_email_address']);
			$emlDomain = $arrEml[1];
			if(in_array($emlDomain, config_item('major_domains'))){				
				$rsCampaign = $this->db->query("select DATE_FORMAT(email_send_date, '%Y-%m-%d')email_send_date, pipeline,campaign_created_by user_id from red_email_campaigns where campaign_id='$id'" );
				$vmta = $rsCampaign->row()->pipeline;
				$email_send_date = $rsCampaign->row()->email_send_date;
				$user_id = $rsCampaign->row()->user_id;
				$rsCampaign->free_result();				
				$this->db->query("insert into red_global_ipr_daily set `mail_domain` = '$emlDomain' ,  `log_date`='$email_send_date' ,  `pipeline`='$vmta', `user_id`='$user_id',   total_opened=(total_opened + 1) ON DUPLICATE  KEY UPDATE  total_opened=(total_opened + 1) ");			
			}
		}
		
		# Fetches campaign data from database
		$campaign_array=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$id));
		#Fetch user info
		$user=$this->UserModel->get_user_data(array('member_id'=>$campaign_array[0]['campaign_created_by']));
		
		# Prepare array to send to view
		$campaign_data=array('campaign_title'=>$campaign_array[0]['campaign_title'],
		'campaign_content'=>$campaign_array[0]['campaign_content'],
		'campaign_status'=>$campaign_array[0]['campaign_status'],
		'campaign_template_option'=>$campaign_array[0]['campaign_template_option'],
		'email_subject'=>$campaign_array[0]['email_subject']
		);
		#Remove tabindex="-1" from campaign content
		$campaign_data['campaign_content']=str_replace('tabindex="-1"','',$campaign_data['campaign_content']);
		#############################################################
		# If campign created by DIY	then remove extra characters	#
		#############################################################
		if($campaign_data['campaign_template_option']!=3){
			$campaign_data['campaign_content']=html_entity_decode($campaign_data['campaign_content'], ENT_QUOTES, "utf-8" );
		}
		$campaign_data['campaign_content']=$campaign_data['campaign_content'];
		
		#############################
		#Email Personalize campaign #
		#############################
		$subscriber_info=$this->Subscriber_Model->get_subscriber_info_view(array('subscriber_id'=>$subscriber_id,'subscriber_created_by'=>$campaign_array[0]['campaign_created_by']));
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
		# Collect Redcappi logo check
		$campaign_data['rc_logo']=$user[0]['rc_logo'];
		#campign view link
		$this->load->view('newsletter/campaign_link',$campaign_data);
	}
	/**
		display_test mail function for view of campign test email
		@param int id contain campaign id
		@email string id contain email id
	 */
	function display_test_mail($id="",$email=""){
		# Load emailreport model class which handles database interaction
		$this->load->model('newsletter/Emailreport_Model');
		# Load campaign model class which handles database interaction
		$this->load->model('newsletter/Campaign_Model');		
		# Load the user model which interact with database
		$this->load->model('UserModel');		
		# Load subscriber model class which handles database interaction
		$this->load->model('newsletter/Subscriber_Model');		
		# Load page model class which handles database interaction
		$this->load->model('newsletter/Page_Model');
		
		#Prepare array for where condition in an campign model
		$fetch_condiotions_array=array(
		'campaign_id'=>$id,
		);
		# Fetches campaign data from database
		$campaign_array=$this->Campaign_Model->get_campaign_data($fetch_condiotions_array);		
		# Prepare array to send to view
		$campaign_data=array('campaign_title'=>$campaign_array[0]['campaign_title'],
		'campaign_content'=>$campaign_array[0]['campaign_content'], 
		'campaign_status'=>$campaign_array[0]['campaign_status'],
		'campaign_template_option'=>$campaign_array[0]['campaign_template_option'],
		'email_subject'=>$campaign_array[0]['email_subject']
		);
		#Remove tabindex="-1" from campaign content
		$campaign_data['campaign_content']=str_replace('tabindex="-1"','',$campaign_data['campaign_content']);
		#############################################################
		# If campign created by DIY	then remove extra characters	#
		#############################################################
		if($campaign_data['campaign_template_option']!=3){
			$campaign_data['campaign_content']=html_entity_decode($campaign_data['campaign_content'], ENT_QUOTES, "utf-8" );
		}		
		$campaign_data['campaign_content']=$campaign_data['campaign_content'];
		#############################
		#Email Personalize campaign #
		#############################
		$email_personalize_arr=array();
		$search_email_personalize=$this->get_email_personalize_data($email_personalize_arr);
		
		$arrPersonalizeReplace=$this->get_fallback_value($campaign_data['campaign_content'],$email_personalize_arr);

		$campaign_data['campaign_content']=str_replace($search_email_personalize, $arrPersonalizeReplace, $campaign_data['campaign_content']);
		# FEtch user info 
		$user=$this->UserModel->get_user_data(array('member_id'=>$campaign_array[0]['campaign_created_by']));
		# Collect Redcappi logo check
		$campaign_data['rc_logo']=$user[0]['rc_logo'];
		# Load campign view link
		$this->load->view('newsletter/campaign_link',$campaign_data);
	}

	/**
		Function 'display_autoresponder_view'  function for view of autoresponder
		@param int id contain autoresponder id
	*/

	function display_autoresponder_view($id=0){
		# Load autoresponder model class which handles database interaction
		$this->load->model('newsletter/Autoresponder_Model');
		# Load campaign model class which handles database interaction
		$this->load->model('newsletter/Campaign_Model');
		#Prepare array for where condition in an autoresponder model
		$fetch_condiotions_array=array(
		'campaign_id'=>$id,
		);
		# Fetches autoresponder data from database
		$campaign_array=$this->Autoresponder_Model->get_autoresponder_data($fetch_condiotions_array);
		
		#Redirects user to listing page if user have not created this campaign or campaign does not exists
		if(!count($campaign_array)){
			redirect('newsletter/autoresponder/display');
		}
		# Prepare array to send to view
		$campaign_data=array('campaign_title'=>$campaign_array[0]['campaign_title'],
		'campaign_template_option'=>$campaign_array[0]['campaign_template_option'],
		'campaign_content'=>$campaign_array[0]['campaign_content'],
		'campaign_status'=>$campaign_array[0]['campaign_status']
		);
		#Remove tabindex="-1" from campaign content
		$campaign_data['campaign_content']=str_replace('tabindex="-1"','',$campaign_data['campaign_content']);
		
		//  If campaign created by DIY then remove extra characters			
		if($campaign_data['campaign_template_option']!=3){
			$campaign_data['campaign_content']=html_entity_decode($campaign_data['campaign_content'], ENT_QUOTES, "utf-8" );
		}
		
		// Email Personalize campaign 
		$email_personalize_arr=array();
		$search_email_personalize=$this->get_email_personalize_data($email_personalize_arr);
		
		$arrPersonalizeReplace=$this->get_fallback_value($campaign_data['campaign_content'],$email_personalize_arr);

		$campaign_data['campaign_content']=str_replace($search_email_personalize, $arrPersonalizeReplace, $campaign_data['campaign_content']);
		# Collect Redcappi logo check
		$campaign_data['rc_logo']=1;
		
		# Load campaign view link
		$this->load->view('newsletter/campaign_link',$campaign_data);
	}
	/**
		Function 'display_autoresponder'  function for view of autoresponder
		@param int id contain autoresponder id
	*/

	function display_autoresponder($id=0){
		# Load autoresponder model class which handles database interaction
		$this->load->model('newsletter/Autoresponder_Model');
		# Load campaign model class which handles database interaction
		$this->load->model('newsletter/Campaign_Model');
		# Load the user model which interact with database
		$this->load->model('UserModel');
		#Prepare array for where condition in an autoresponder model
		$fetch_condiotions_array=array(
		'campaign_id'=>$id,
		);
		# Fetches autoresponder data from database
		$campaign_array=$this->Autoresponder_Model->get_autoresponder_data($fetch_condiotions_array);
		
		#Redirects user to listing page if user have not created this campaign or campaign does not exists
		if(!count($campaign_array)){
			redirect('newsletter/autoresponder/display');
		}
		# Prepare array to send to view
		$campaign_data=array('campaign_title'=>$campaign_array[0]['campaign_title'],
		'campaign_template_option'=>$campaign_array[0]['campaign_template_option'],
		'campaign_content'=>$campaign_array[0]['campaign_content'], 
		'campaign_status'=>$campaign_array[0]['campaign_status']
		);
		#Remove tabindex="-1" from campaign content
		$campaign_data['campaign_content']=str_replace('tabindex="-1"','',$campaign_data['campaign_content']);
		#############################################################
		# If campign created by DIY	then remove extra characters	#
		#############################################################
		if($campaign_data['campaign_template_option']!=3){
			$campaign_data['campaign_content']=html_entity_decode($campaign_data['campaign_content'], ENT_QUOTES, "utf-8" );
		}
		#############################
		#Email Personalize campaign #
		#############################
		$email_personalize_arr=array();
		$search_email_personalize=$this->get_email_personalize_data($email_personalize_arr);
		
		$arrPersonalizeReplace=$this->get_fallback_value($campaign_data['campaign_content'],$email_personalize_arr);

		$campaign_data['campaign_content']=str_replace($search_email_personalize, $arrPersonalizeReplace, $campaign_data['campaign_content']);
		# Fetch user info 
		$user=$this->UserModel->get_user_data(array('member_id'=>$campaign_array[0]['campaign_created_by']));
		# Collect Redcappi logo check
		$campaign_data['rc_logo']=$user[0]['rc_logo'];
		
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
		#Protecting MySQL from query string sql injection Attacks
		if(is_numeric($id)){
			#success
		}else{
			$id=0;
			echo "error:camapign id not exist";
			exit;
		}		
		# Load emailreport model class which handles database interaction
		$this->load->model('newsletter/Emailreport_Model');
		# Load autoresponder model class which handles database interaction
		$this->load->model('newsletter/Autoresponder_Model');		
		# Load campaign model class which handles database interaction
		$this->load->model('newsletter/Campaign_Model');		
		# Load the user model which interact with database
		$this->load->model('UserModel');		
		# Load subscriber model class which handles database interaction
		$this->load->model('newsletter/Subscriber_Model');
		# check read  mails in autoresponder email track table
		$conditions_array=array('autoresponder_scheduled_id'=>$scheduled_id,'email_track_subscriber_id '=>$id);
		$email_report=$this->Emailreport_Model->update_autoresponder_emailreport(array('email_track_read'=>1,'email_delivered'=>1),$conditions_array);			
	
		#Prepare array for where condition in an autoresponder model
		$fetch_condiotions_array=array('campaign_id'=>$campaign_id);
		# Fetches campaign data from database
		$campaign_array=$this->Autoresponder_Model->get_autoresponder_data($fetch_condiotions_array);
	
		# Prepare array to send to view
		$campaign_data=array('campaign_title'=>$campaign_array[0]['campaign_title'],
		'campaign_template_option'=>$campaign_array[0]['campaign_template_option'],
		'campaign_content'=>$campaign_array[0]['campaign_content'],
		'campaign_status'=>$campaign_array[0]['campaign_status']
		);
		#Remove tabindex="-1" from campaign content
		$campaign_data['campaign_content']=str_replace('tabindex="-1"','',$campaign_data['campaign_content']);
		#############################################################
		# If campign created by DIY	then remove extra characters	#
		#############################################################
		if($campaign_data['campaign_template_option']!=3){
			$campaign_data['campaign_content']=html_entity_decode($campaign_data['campaign_content'], ENT_QUOTES, "utf-8" );
		}		
		
		$campaign_data['campaign_content']=$campaign_data['campaign_content'];

		#############################
		#Email Personalize campaign #
		#############################
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
		
		# Fetch user info 
		$user=$this->UserModel->get_user_data(array('member_id'=>$campaign_array[0]['campaign_created_by']));
		# Collect Redcappi logo check
		$campaign_data['rc_logo']=$user[0]['rc_logo'];
		# Load campign view link
		$this->load->view('newsletter/campaign_link',$campaign_data);
	}

	/**
		Function webCompatibleString to replace bad symbol with good symbol of string
		@param string str contain content
	*/
	function webCompatibleString($str){
		$badContent = array("&nbsp;");
		$str = trim(str_replace($badContent," ",$str));

		$theBad = 	array("“","”","‘","’","…","—","–");
		$theGood = array("\"","\"","'","'","...","-","-");
		$str = str_replace($theBad,$theGood,$str);

		$str = preg_replace('/[^(\x20-\x7F)\x0A]*/','', $str);
		return $str;
	}
	/**
		Function get_email_personalize_data to fetch email personalize name and value from database
	*/
	function get_email_personalize_data(&$email_personalize_arr=array()){
		$sql            = 'SELECT name,value,default_value FROM `red_email_personalization`';
		$query          = $this->db->query($sql);
		$email_personalize=array();
		if ($query->num_rows() >0)
		{
			$result_array=$query->result_array();	#Fetch resut
			foreach($result_array as $row){
				$email_personalize[]        = $row['value'];
				$email_personalize_arr[$row['name']]        = $row['default_value'];
			}
		}
		return $email_personalize;
	}
	/**
		Function get_fallback_value to fetch fallback value from campign content
	*/
	function get_fallback_value(&$campaign_content="",$arrPersonalizeReplace=array()){
		$string		=		$campaign_content;
		
		//$pattren="/\{([a-zA-Z0-9_-])*,([a-zA-Z0-9_-])*\}/";
		$pattren="/\{([a-zA-Z0-9_-])*,([^\/])*\}/";
		preg_match_all($pattren,$string,$regs);
		foreach($regs[0] as $value){
			$fallback_value=$value;
			$value=trim($value,'}');
			$expl_value=explode(",",$value,2);
			$sql            = 'SELECT name,value FROM `red_email_personalization` where value like \'%'.$expl_value[0].'%\'';
			$query          = $this->db->query($sql);	
			
			if ($query->num_rows() >0){
				$result_array=$query->result_array();	#Fetch resut
				foreach($result_array as $row){
					#Create an array of the required personalisation token and default value from CAMPAIGN
					$arrPersonalizeReplace[$row['name']] = $expl_value[1];
					$fallback_search_arr[]=$fallback_value;
					$fallback_replace_arr[]=$row['value'];
				}
			}
		}
		
		$campaign_content=str_replace($fallback_search_arr, $fallback_replace_arr, $string);
		return $arrPersonalizeReplace;
	}
}
?>