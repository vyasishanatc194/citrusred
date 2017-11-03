<?php
/*
	Controller class for campaigns_link It have controller functions for click rate management.
*/
class Clickrate extends CI_Controller
{
	/*
		Contructor for controller.
	*/
	function __construct()
    {
        parent::__construct();
		$this->load->model('newsletter/Campaign_Model');
		$this->load->model('clickrate/Clickrate_Model');
		$this->load->model('newsletter/Subscriber_Model');
		$this->load->model('newsletter/Emailreport_Model');
		$this->load->model('newsletter/Autoresponder_Model');
		$this->load->model('newsletter/Campaign_Autoresponder_Model');
	}

	/**
		Function to increase click rate
	*/
	function create($campaign_id=0,$subscriber_id=0,$url_key=''){		
		$restrictedCID = array( 77140, 77123, 77139, 77122, 77147,  77144,  77132,  77125,  77090,  77089,  77086,  77082,  77080,  77075, 77120,  77121, 77095, 77074, 77073, 77081, 77083, 77065, 77079);
		//, 80095, 80092, 80083, 80081, 80080, 80099, 80103, 80104, 80108, 80109, 80121, 80122, 80127, 80130, 80110, 80113, 80114, 80115, 80118, 80124, 80126, 80132, 80133, 80135, 80128
		if(in_array($campaign_id , $restrictedCID )){
			//redirect("http://www.provide-insurance-unsubs.com/unsub/unsub.form?id=7cebe80b19bd955ac5d3d007ccb1b507d4d2bf6672752784cf22e98a7ec072d1");
			redirect("http://www.red7.me/false_link_message");			
			exit;
		} 	
		$subscriber_id = str_replace('.html','',$subscriber_id);
		$arrSubscriber = $this->is_authorized->decodeSubscriber($subscriber_id);
		list($subscriber_id,$subscriber_email) = $arrSubscriber;	
		$subscriber_email = $this->is_authorized->webCompatibleString($subscriber_email);	
	
		// get url from campaign table			
		$campaign_array=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$campaign_id));
		 
		if(is_numeric($url_key)){
			$url_array=unserialize($campaign_array[0]['click_url']);
			$url=$url_array[$url_key];
		}else{
			$url=$url_key;
		}
		
		$url=trim($url);
		$actual_url="";
		if($url){
			$actual_url=$this->is_authorized->base64UrlSafeDecode($url);
			if (!preg_match('/^http(s)?:\/\//', $actual_url))	$actual_url = 'http://' . $actual_url;
			if($campaign_array[0]['is_ga_enabled']){
				
				$host = parse_url($actual_url, PHP_URL_HOST); 
				$mid = $campaign_array[0]['campaign_created_by'];
				
				
				if($this->db->query("select * from red_ga_domains where `member_id`='$mid' and `domain_name`='$host'")->num_rows() > 0){
					$campaign_subject = str_replace(' ','_',$campaign_array[0]['email_subject']);
					$campaign_contact_lists_id = $campaign_array[0]['subscription_list'];					
					$subscription_id_arr=@explode(",",$campaign_contact_lists_id);

					#Fetch subscription List Titles
					$subscription_list_title=$this->Emailreport_Model->get_subscription_list_title(array('subscription_created_by'=>$mid),$subscription_id_arr);
					$campaign_contact_lists = @implode("_",$subscription_list_title);
					if (strpos($actual_url, '?') === false) {
						$actual_url .= '?utm_source='.$campaign_contact_lists.'&utm_medium=email&utm_campaign='.$campaign_subject;
					}else{
						$actual_url .= '&utm_source='.$campaign_contact_lists.'&utm_medium=email&utm_campaign='.$campaign_subject;
					}
				}			
			}			
		} 
		
		//$this->Clickrate_Model->replace_clickrate($input_array);
		$orignial_url=$this->Clickrate_Model->get_encoded_url_data(array('campaign_id'=>$campaign_id,'subscriber_id'=>$subscriber_id,'tiny_url'=>$url));
		
		if(count($orignial_url)>0){		
			$counter=$orignial_url[0]['counter']+1;
			$update_array=array('counter'=>$counter, 'date_click'=>date('Y-m-d H:i:s',now()));
			$this->Clickrate_Model->update_counter($update_array,array('campaign_id'=>$campaign_id,'subscriber_id'=>$subscriber_id,'tiny_url'=>$url));
		}else{
		$actual_url = ( @mb_detect_encoding($actual_url, "UTF-8") == "UTF-8" )?$actual_url : utf8_encode($actual_url);
			
			$input_array=array('campaign_id'=>$campaign_id,'subscriber_id'=>$subscriber_id,'tiny_url'=>$url,'actual_url'=>$actual_url,'counter'=>1,'date_click'=>date('Y-m-d H:i:s',now()));
			$this->Clickrate_Model->create_clickrate($input_array);			
		}
		$email_subscriber= $this->Subscriber_Model->get_distinct_contacts(array('res.subscriber_id'=>$subscriber_id),$campaign_array[0]['campaign_created_by']);
		 		
		$actual_url = $this->Campaign_Autoresponder_Model->getURLPersonalization($actual_url, $email_subscriber[0]);
		
		// Get Total number of click mail 
		$email_report_click=$this->Emailreport_Model->get_emailreport_data(array(	'campaign_id'=>$campaign_id,'subscriber_id'=>$subscriber_id,'email_sent'=>1	));
		$email_track_click=$email_report_click[0]['email_track_click']+1;
		
		// Get Total number of click  mails for subscription list		
		$email_report=$this->Emailreport_Model->get_emailreport_listdata(array(	'campaign_id'=>$campaign_id));
		$email_track_listclick=$email_report[0]['email_track_click']+1;
		$email_track_listread=$email_report[0]['email_track_read']+1;
		
		if($email_report_click[0]['email_track_read']<=0){			
			$this->db->query("update red_email_subscribers set `read` = `read`+1, `last_read_date`=current_timestamp(), `clicked` = `clicked`+1, `last_clicked_date`=current_timestamp() where subscriber_id='$subscriber_id'");
			
			$this->Emailreport_Model->update_emailreport(array('email_track_click'=>$email_track_click,'date_click'=>date('Y-m-d H:i:s',now()),'email_track_read'=>1,'email_track_read_date'=>date('Y-m-d H:i:s',now())),array('campaign_id'=>$campaign_id,'subscriber_id'=>$subscriber_id));	
			
			$this->db->query("update `red_email_campaigns_scheduled` set `email_track_read` = `email_track_read`+1,`email_track_click`=`email_track_click`+1 where campaign_id='$campaign_id'");
			// Increment OPENED global_ipr_daily for major webmails
			$arrEml = @explode('@',$subscriber_email);
			$emlDomain = $arrEml[1];
			if(in_array($emlDomain, config_item('major_domains'))){				
				$rsCampaign = $this->db->query("select DATE_FORMAT(email_send_date, '%Y-%m-%d')email_send_date, pipeline,campaign_created_by user_id  from red_email_campaigns where campaign_id='$campaign_id'" );
				$vmta = $rsCampaign->row()->pipeline;
				$email_send_date = ('' != $rsCampaign->row()->email_send_date)?$rsCampaign->row()->email_send_date : date('Y-m-d');
				$user_id = $rsCampaign->row()->user_id;
				$rsCampaign->free_result();				
				$this->db->query("insert into red_global_ipr_daily set `mail_domain` = '$emlDomain' ,  `log_date`='$email_send_date' ,  `pipeline`='$vmta', `user_id`='$user_id', total_opened=(total_opened + 1) ON DUPLICATE  KEY UPDATE  total_opened=(total_opened + 1) ");			
			}	
		}else{
			$this->db->query("update red_email_subscribers set `clicked` = `clicked`+1, `last_clicked_date`=current_timestamp() where subscriber_id='$subscriber_id'");
			$this->db->query("update `red_email_campaigns_scheduled` set `email_track_click`=`email_track_click`+1 where campaign_id='$campaign_id'");		
			
			$this->Emailreport_Model->update_emailreport(array('email_track_click'=>$email_track_click,'date_click'=>date('Y-m-d H:i:s',now())),array('campaign_id'=>$campaign_id,'subscriber_id'=>$subscriber_id));						
		}
		
		
		redirect(html_entity_decode($actual_url));
	}
	
	/**
		Function to increase click rate for autoresponder
	*/
	function create_autoresponder($campaign_id=0,$scheduled_id=0,$subscriber_id=0,$url_key){	
		 	
			$subscriber_id = str_replace('.html','',$subscriber_id);
			$arrSubscriber = $this->is_authorized->decodeSubscriber($subscriber_id);
			list($subscriber_id,$subscriber_email) = $arrSubscriber;	
			$subscriber_email = $this->is_authorized->webCompatibleString($subscriber_email);
		
		
		// Load campaign model class which handles database interaction
		if(is_numeric($url_key)){
			# get url from campaign table			
			$campaign_array=$this->Autoresponder_Model->get_autoresponder_data(array('campaign_id'=>$campaign_id));
			$url_array=unserialize($campaign_array[0]['click_url']);
			$url=$url_array[$url_key];
		}else{
			$url=$url_key;
		}
		
		$url=trim($url);
		$actual_url="";
		if($url){
			$actual_url=$this->is_authorized->base64UrlSafeDecode($url);
			if($campaign_array[0]['is_ga_enabled']){
				if (!preg_match('/^http(s)?:\/\//', $actual_url))	$actual_url = 'http://' . $actual_url;
				
				$host = parse_url($actual_url, PHP_URL_HOST); 
				$mid = $campaign_array[0]['campaign_created_by'];
				
				
				if($this->db->query("select * from red_ga_domains where `member_id`='$mid' and `domain_name`='$host'")->num_rows() > 0){
					$campaign_subject = str_replace(' ','_',$campaign_array[0]['email_subject']);
					$campaign_contact_lists_id = $campaign_array[0]['subscription_list'];					
					$subscription_id_arr=@explode(",",$campaign_contact_lists_id);

					#Fetch subscription List Titles
					$subscription_list_title=$this->Emailreport_Model->get_subscription_list_title(array('subscription_created_by'=>$mid),$subscription_id_arr);
					$campaign_contact_lists = @implode("_",$subscription_list_title);
					if (strpos($actual_url, '?') === false) {
						$actual_url .= '?utm_source='.$campaign_contact_lists.'&utm_medium=email&utm_campaign='.$campaign_subject;
					}else{
						$actual_url .= '&utm_source='.$campaign_contact_lists.'&utm_medium=email&utm_campaign='.$campaign_subject;
					}
				}			
			}
		}
		// Collect data posted 
		$fetch_condiotions_array=array('campaign_id'=>$campaign_id, 'subscriber_id'=>$subscriber_id,'tiny_url'=>$url, 'is_autoresponder'=>1 );
		
		//$this->Clickrate_Model->replace_clickrate($input_array);
		$orignial_url=$this->Clickrate_Model->get_encoded_url_data($fetch_condiotions_array);
		
		if(count($orignial_url)>0){
		$counter=$orignial_url[0]['counter']+1;
		$update_array=array('counter'=>$counter);
		//Increase counter for click url				
			$this->Clickrate_Model->update_counter($update_array,$fetch_condiotions_array);
		}else{
		$actual_url = ( @mb_detect_encoding($actual_url, "UTF-8") == "UTF-8" )?$actual_url : utf8_encode($actual_url);
			$input_array=array('campaign_id'=>$campaign_id,'subscriber_id'=>$subscriber_id,'tiny_url'=>$url,'actual_url'=>$actual_url,'counter'=>1,'is_autoresponder'=>1);
			$this->Clickrate_Model->create_clickrate($input_array);
		}
		
		
		$fetch_condiotions_array=array(	'autoresponder_scheduled_id'=>$scheduled_id, 'email_track_subscriber_id'=>$subscriber_id);
		// Get Total number of click mail 
		$email_report=$this->Emailreport_Model->get_autoresponder_emailreport_subscriber($fetch_condiotions_array);
		$email_track_click=$email_report[0]['email_track_click']+1;		
		
		$input_array=array('email_track_click'=>$email_track_click,'email_track_read'=>1);
		
		$this->Emailreport_Model->update_autoresponder_emailreport($input_array,array('autoresponder_scheduled_id'=>$scheduled_id,'email_track_subscriber_id'=>$subscriber_id));
		
		redirect($actual_url);
	}
	function display(){
		//echo $url= $this->is_authorized->base64UrlSafeEncode("http://twitter.com/");
		echo $url= $this->is_authorized->base64UrlSafeEncode("http://www.facebook.com/");
		echo "<a href='".base_url()."newsletter/clickrate/create/8/10/$url'>url</a>";
	}
 
	
	function isBot($user_agent){
		$bots = array('bingbot', 'msn', 'abacho', 'abcdatos', 'abcsearch', 'acoon', 'adsarobot', 'aesop', 'ah-ha', 'alkalinebot', 'almaden', 'altavista', 'antibot', 'anzwerscrawl', 'aol', 'search', 'appie', 'arachnoidea', 'araneo', 'architext', 'ariadne', 'arianna', 'ask', 'jeeves', 'aspseek', 'asterias', 'astraspider', 'atomz', 'augurfind', 'backrub', 'baiduspider', 'bannana_bot', 'bbot', 'bdcindexer', 'blindekuh', 'boitho', 'boito', 'borg-bot', 'bsdseek', 'christcrawler',  'computer_and_automation_research_institute_crawler', 'coolbot', 'cosmos', 'crawler', 'crawler@fast', 'crawlerboy', 'cruiser', 'cusco', 'cyveillance', 'deepindex', 'denmex',
			'dittospyder', 'docomo', 'dogpile', 'dtsearch', 'elfinbot', 'entire', 'web', 'esismartspider', 'exalead', 'excite', 'ezresult', 'fast', 'fast-webcrawler', 'fdse', 'felix', 'fido', 'findwhat', 'finnish', 'firefly', 'firstgov', 'fluffy', 'freecrawl', 'frooglebot', 'galaxy', 'gaisbot', 'geckobot', 'gencrawler', 'geobot',
			'gigabot', 'girafa', 'goclick', 'goliat', 'googlebot', 'griffon', 'gromit', 'grub-client', 'gulliver', 'gulper', 'henrythemiragorobot', 'hometown', 'hotbot', 'htdig', 'hubater', 'ia_archiver', 'ibm_planetwide', 'iitrovatore-setaccio', 'incywincy', 'incrawler', 'indy', 'infonavirobot', 'infoseek', 'ingrid', 'inspectorwww',
			'intelliseek', 'internetseer', 'ip3000.com-crawler', 'iron33', 'jcrawler', 'jeeves', 'jubii', 'kanoodle', 'kapito', 'kit_fireball', 'kit-fireball', 'ko_yappo_robot', 'kototoi', 'lachesis', 'larbin', 'legs', 'linkwalker', 'lnspiderguy', 'look.com', 'lycos', 'mantraagent', 'markwatch', 'maxbot', 'mercator', 'merzscope',
			'meshexplorer', 'metacrawler', 'mirago', 'mnogosearch', 'moget', 'motor', 'muscatferret', 'nameprotect', 'nationaldirectory', 'naverrobot', 'nazilla', 'ncsa', 'beta', 'netnose', 'netresearchserver', 'ng/1.0', 'northerlights', 'npbot', 'nttdirectory_robot', 'nutchorg', 'nzexplorer', 'odp', 'openbot', 'openfind',
			'osis-project', 'overture', 'perlcrawler', 'phpdig', 'pjspide', 'polybot', 'pompos', 'poppi', 'portalb', 'psbot', 'quepasacreep', 'rabot', 'raven', 'rhcs', 'robi', 'robocrawl', 'robozilla', 'roverbot', 'scooter', 'scrubby', 'search.ch', 'search.com.ua', 'searchfeed', 'searchspider', 'searchuk', 'seventwentyfour',
			'sidewinder', 'sightquestbot', 'skymob', 'sleek', 'slider_search', 'slurp', 'solbot', 'speedfind', 'speedy', 'spida', 'spider_monkey', 'spiderku', 'stackrambler', 'steeler', 'suchbot', 'suchknecht.at-robot', 'suntek', 'szukacz', 'surferf3', 'surfnomore', 'surveybot', 'suzuran', 'synobot', 'tarantula', 'teomaagent', 'teradex',
			't-h-u-n-d-e-r-s-t-o-n-e', 'tigersuche', 'topiclink', 'toutatis', 'tracerlock', 'turnitinbot', 'tutorgig', 'uaportal', 'uasearch.kiev.ua', 'uksearcher', 'ultraseek', 'unitek', 'vagabondo', 'verygoodsearch', 'vivisimo', 'voilabot', 'voyager', 'vscooter', 'w3index', 'w3c_validator', 'wapspider', 'wdg_validator', 'webcrawler',
			'webmasterresourcesdirectory', 'webmoose', 'websearchbench', 'webspinne', 'whatuseek', 'whizbanglab', 'winona', 'wire', 'wotbox', 'wscbot', 'www.webwombat.com.au', 'xenu', 'link', 'sleuth', 'xyro', 'yahoobot', 'yahoo!', 'slurp', 'yandex', 'yellopet-spider', 'zao/0', 'zealbot', 'zippy', 'zyborg', 'mediapartners-google'
		);
		$user_agent = strtolower($user_agent);
		foreach($bots as $bot){
			if(strpos($user_agent, $bot) === true){
				return true;
			}
		}
	return false;
	}
	
}
?>