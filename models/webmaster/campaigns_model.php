<?php
/*
	Model class for campaign
*/
class Campaigns_Model extends CI_Model
{
	//Constructor class with parent constructor
	function Campaigns_Model()
	{
		parent::__construct();
	}
	//Function to fetch campaign data
	function get_campaign_data($conditions_array=array(),$rows_per_page=10,$start=0,$order_by="desc",$order_field="campaign_id",$join=false){
		$rows=array();
		$case=" (CASE
					WHEN campaign_status='draft' THEN `campaign_date_added`
					WHEN campaign_status='active' THEN `email_send_date`
					WHEN campaign_status='archived' or campaign_status='active_ready' or campaign_status='ready' or campaign_status='disallow'  THEN `campaign_sheduled`
					END) conditional_order
					";
					
		if($join){
			$this->db->select("rec.*,rm.member_username,rm.member_id, $case",false) ;
		}else{
			$this->db->select("*, $case",false) ;
		}
		if($order_field=="case"){			 
			$this->db->order_by('conditional_order','desc');
			$this->db->order_by('campaign_id','desc');
		}else if($order_field!=""){
			$this->db->order_by($order_field,$order_by);
		}else{
			$this->db->order_by('campaign_status',$order_by);
		}
		if($join){
			if(isset($_POST['mode'])){
				$member_username	= $_POST['username'];				
				if($member_username != '')$this->db->like('member_username',$member_username);
				$this->db->join('red_members as rm','rec.campaign_created_by=rm.member_id');
			}else{
				$this->db->join('red_members as rm','rec.campaign_created_by=rm.member_id');
			}
		}
		$result=$this->db->get_where('red_email_campaigns as rec',$conditions_array,$rows_per_page,$start);		
		foreach($result->result_array() as $row)
		{
			$row['list_names'] =$this->getContactList($row['subscription_list']);			
			$rows[]=$row;
		}
		return $rows;
	}
	function get_campaign_data_categorized($conditions_array=array(),$rows_per_page=10,$start=0,$order_by="desc",$order_field="campaign_id",$join=false){
		$rows=array();
		$this->db->select("rec.*,rm.member_username,rm.member_id,rm.vmta,rm.campaign_approval_notes,rm.stop_campaign_approval, rm.always_slow_release ",false) ;	
		$this->db->order_by($order_field,$order_by);							
		if(trim($_POST['username']) != '')$this->db->like('member_username',trim($_POST['username']));		
		$this->db->join('red_members as rm','rec.campaign_created_by=rm.member_id');		
		$result=$this->db->get_where('red_email_campaigns as rec',$conditions_array,$rows_per_page,$start);	
		
		foreach($result->result_array() as $row){
			if('campaign_sheduled' == $order_field){
				$row['list_names'] =$this->getContactList($row['subscription_list']);			
			}
		$rows[]=$row;
		}	
		$result->free_result();		
		return $rows;
	}
	
	function get_data_campaign_management_ONGOING($conditions_array=array(),$rows_per_page=10,$start=0,$order_by="desc",$order_field="campaign_id"){
		$rows=array();		
		//$this->db->order_by($order_field,$order_by);		
		
		//$result=$this->db->get_where('red_email_campaigns as rec',$conditions_array,$rows_per_page,$start);			
		$result = $this->db->query("SELECT campaign_id, campaign_created_by, subscription_list, sender_name, sender_email, email_subject, campaign_title, is_deleted, campaign_status, is_segmentation, segment_interval, number_of_contacts, campaign_sheduled,campaign_queued,email_send_date  FROM `red_email_campaigns` WHERE `is_segmentation` =  1 AND `is_deleted` =  0 and campaign_id in(select  distinct campaign_id from red_email_queue where 1 ) ORDER BY `$order_field` $order_by  ");
		
		foreach($result->result_array() as $row){
			if('campaign_sheduled' == $order_field){
				$row['list_names'] = $this->getContactList($row['subscription_list']);			
			}
			$mid = $row['campaign_created_by'];	
                                                    $cid = $row['campaign_id'];
			$rsMember = $this->db->query("select member_username,vmta from red_members where member_id='$mid'");
                                                    
                                                    $rsReleaseInfo = $this->db->query("select last_released_on ,DATE_ADD( IFNULL( last_released_on, added_on ) , INTERVAL( segment_interval + interval_variance ) MINUTE ) next_release_at from red_ongoing_segmentation where campaign_id =$cid");
                                                   $campaign_scheduled =  getGMTToLocalTime($row['campaign_sheduled'], date_default_timezone_get() ); 
                                                    $timenow = date('Y-m-d H:i:s');
                                                    
                                                    if ($campaign_scheduled > $timenow){
                                                    $row['last_released_on'] = "null";
                                                    $row['next_release_on'] = "null";
                                                    }
                                                    else{
                                                    $row['last_released_on'] = $rsReleaseInfo->row()->last_released_on;
                                                    $row['next_release_on'] = $rsReleaseInfo->row()->next_release_at;
                                                    }
                                                    
			$row['member_username'] = $rsMember->row()->member_username;
			$row['vmta'] = $rsMember->row()->vmta;
			$rsMember->free_result();
                                                    $rsReleaseInfo->free_result();
			$row['member_id'] = $mid;
			$rows[]=$row;
		}
		$result->free_result();
			
		return $rows;	
	}
	//Function to fetch campaign data
	function get_campaign_data_for_sentmails($conditions_array=array(),$rows_per_page=10,$start=0){
		$rows=array();		 
		$this->db->select("rec.*,rm.member_username,rm.member_id",false) ;		
		$this->db->join('red_members as rm','rec.campaign_created_by=rm.member_id');				
		if(isset($_POST['mode'])){
			$member_username	= trim($_POST['username']);
			$date_from			= $_POST['date_from']; 
			$date_to			= $_POST['date_to'];
			if($date_from != '')$this->db->where("email_send_date >= '$date_from'");
			if($date_to != '')$this->db->where("email_send_date <= '$date_to'");
			if($member_username != '')$this->db->like('member_username',$member_username);			
		}	
		$this->db->order_by('email_send_date','desc');	
		$result=$this->db->get_where('red_email_campaigns as rec',$conditions_array,$rows_per_page,$start);	
		if($result->num_rows()>0){	
		foreach($result->result_array() as $row){
			$row['list_names'] =$this->getContactList($row['subscription_list']);			
			$rows[]=$row;
		}
		}
		return $rows;
	}
	//Function to fetch campaign data
	function get_campaign_data_for_sentmails_new($conditions_array=array(),$sort_by='email_send_date',$rows_per_page=10,$start=0){
	
		$arrMembers = array();
		$where_keywords = '';
		if(isset($_POST['mode'])){
			$member_username	= trim($_POST['username']);
			if($member_username != ''){			
				$rsMembers = $this->db->query("select member_id from red_members where member_username like '{$member_username}%'");				
				foreach ($rsMembers->result() as $memberRow){
				   $arrMembers[] = $memberRow->member_id;				    
				}
				$rsMembers->free_result();
			}			
			if($_POST['above_critical_level']== 'bounces'){
				$conditions_array['bounces_over_limit'] = 1;			
			}elseif($_POST['above_critical_level']== 'complaints'){
				$conditions_array['complaints_over_limit'] = 1;
			}
			if($_POST['pipeline'] != '')$conditions_array['pipeline'] = trim($_POST['pipeline']);			
			$date_from			= $_POST['date_from']; 
			if($date_from != '')$conditions_array['email_send_date >='] = $date_from; 
			$date_to			= $_POST['date_to'];
			if($date_to != '')$conditions_array['email_send_date <='] = $date_to; 
			$keyword			= $_POST['keyword'];
			if($keyword != '')	$where_keywords ="(email_subject like'%$keyword%' or campaign_content like'%$keyword%' )";
		}		
	
		$rows=array();		 
		$this->db->select("c.campaign_id,c.campaign_created_by,c.campaign_title,c.email_subject,c.is_deleted,c.email_send_date,c.sender_email,c.sender_name, c.campaign_contacts, c.number_of_contacts, c.segment_interval, c.subscription_list, c.pipeline, cs.email_track_released, cs.email_track_delivered, cs.email_track_bounce, cs.email_track_read, cs.email_track_click, cs.email_track_forward, cs.email_track_unsubscribes, cs.email_track_spam",false) ;				
		$this->db->join('red_email_campaigns_scheduled as cs','c.campaign_id=cs.campaign_id');	
		$this->db->order_by($sort_by,'desc');
		if(count($arrMembers) > 0)	
		$this->db->where_in('campaign_created_by',$arrMembers);	
		if($where_keywords !='')$this->db->where($where_keywords,null,false);	
		$result=$this->db->get_where('red_email_campaigns as c',$conditions_array,$rows_per_page,$start);	
			
		if($result->num_rows()>0){	
			foreach($result->result_array() as $row){
				 	$rows[]=$row;	
			}
			
		}else{
		
		//$this->db->select("c.campaign_id,c.campaign_created_by,c.campaign_title,c.email_subject,c.is_deleted,c.email_send_date,c.sender_email,c.sender_name, c.number_of_contacts, c.segment_interval, c.subscription_list, cs.email_track_delivered, cs.email_track_bounce, cs.email_track_read, cs.email_track_click, cs.email_track_forward, cs.email_track_unsubscribes, cs.email_track_spam",false) ;				
		$this->db->select("c.campaign_id,c.campaign_created_by,c.campaign_title,c.email_subject,c.is_deleted,c.email_send_date,c.sender_email,c.sender_name, c.number_of_contacts, c.segment_interval, c.subscription_list, cs.delivered_count email_track_delivered, cs.bounce_email_count email_track_bounce, cs.read_email_count email_track_read, cs.click_link_count email_track_click, cs.forward_email_count email_track_forward, cs.unsubscribes_email_count email_track_unsubscribes, cs.complaint_email_count email_track_spam",false) ;
		$this->db->join('red_email_track_freezed as cs','c.campaign_id=cs.campaign_id');	
		$this->db->order_by($sort_by,'desc');
		if(count($arrMembers) > 0)	
		$this->db->where_in('campaign_created_by',$arrMembers);	
		if($where_keywords !='')$this->db->where($where_keywords,null,false);	
		$resultFreezed=$this->db->get_where('red_email_campaigns as c',$conditions_array,$rows_per_page,$start);
		
			if($resultFreezed->num_rows()>0){	
				foreach($resultFreezed->result_array() as $row){
						$rows[]=$row;	
				}			
			} 
			
			$resultFreezed->free_result();
		} 		
		$result->free_result();
				
		return $rows;
	}
	//Function to fetch campaign data
	function get_campaign_data_for_sentmails_new_20160215($conditions_array=array(),$sort_by='email_send_date',$rows_per_page=10,$start=0){
	
		$arrMembers = array();
		$where_keywords = '';
		if(isset($_POST['mode'])){
			$member_username	= trim($_POST['username']);
			if($member_username != ''){
			//$conditions_array['campaign_created_by'] = $this->db->query("select member_id from red_members where member_username like '{$member_username}%'")->row()->member_id;
				$rsMembers = $this->db->query("select member_id from red_members where member_username like '{$member_username}%'");				
				foreach ($rsMembers->result() as $memberRow){
				   $arrMembers[] = $memberRow->member_id;				    
				}
				$rsMembers->free_result();
			}			
			if($_POST['above_critical_level']== 'bounces'){
				$conditions_array['bounces_over_limit'] = 1;			
			}elseif($_POST['above_critical_level']== 'complaints'){
				$conditions_array['complaints_over_limit'] = 1;
			}
			
			$date_from			= $_POST['date_from']; 
			if($date_from != '')$conditions_array['email_send_date >='] = $date_from; 
			$date_to			= $_POST['date_to'];
			if($date_to != '')$conditions_array['email_send_date <='] = $date_to; 
			$keyword			= $_POST['keyword'];
			if($keyword != '')	$where_keywords ="(email_subject like'%$keyword%' or campaign_content like'%$keyword%' )";
		}		
	
		$rows=array();		 
		$this->db->select("campaign_id,campaign_created_by,campaign_title,email_subject,is_deleted,email_send_date,sender_email,sender_name, number_of_contacts, segment_interval, subscription_list",false) ;				
		$this->db->order_by($sort_by,'desc');
		if(count($arrMembers) > 0)	
		$this->db->where_in('campaign_created_by',$arrMembers);	
		if($where_keywords !='')$this->db->where($where_keywords,null,false);	
		$result=$this->db->get_where('red_email_campaigns',$conditions_array,$rows_per_page,$start);	
			
		if($result->num_rows()>0){	
			foreach($result->result_array() as $row){
				$this_campaign_created_by = $row['campaign_created_by'];
				$this_campaign_id		 = $row['campaign_id'];
				// User detail
				//$rows[]['member_username'] = $this->db->query("select member_username from red_members where member_id='$this_campaign_created_by'")->row()->member_username;
				// Campaign Details detail
				$rsCampaignStats = $this->db->query("select email_track_delivered, email_track_bounce, email_track_read, email_track_click, email_track_forward, email_track_unsubscribes, email_track_spam from red_email_campaigns_scheduled where campaign_id = '$this_campaign_id' ");
				if($rsCampaignStats->num_rows()>0){	
					foreach($rsCampaignStats->result_array() as $campaignRow){
						$row['email_track_delivered'] = $campaignRow['email_track_delivered'];
						$row['email_track_bounce'] = $campaignRow['email_track_bounce'];
						$row['email_track_read'] = $campaignRow['email_track_read'];
						$row['email_track_click'] = $campaignRow['email_track_click'];
						$row['email_track_forward'] = $campaignRow['email_track_forward'];
						$row['email_track_unsubscribes'] = $campaignRow['email_track_unsubscribes'];
						$row['email_track_spam'] = $campaignRow['email_track_spam'];
					}
					$rsCampaignStats->free_result();
				}else{
					$this->db->from('red_email_track_freezed as ret')->where(array('campaign_id'=>$this_campaign_id));
					$freezed_result=$this->db->get();		
					foreach($freezed_result->result_array() as $campaign_stat_row){					 
						$row['email_track_delivered']=$campaign_stat_row['send_email_count'];	# total delivered emails
						$row['email_track_bounce']=$campaign_stat_row['bounce_email_count'];# total bounce emails
						$row['email_track_read']=$campaign_stat_row['read_email_count'];	# total read emails						 
						$row['email_track_click']=$campaign_stat_row['click_link_count'];	# total click emails
						$row['email_track_bounce']=$campaign_stat_row['bounce_email_count'];	# total bounce emails
						$row['email_track_forward']=$campaign_stat_row['forward_email_count'];	# total forward emails
						$row['email_track_unsubscribes']=$campaign_stat_row['unsubscribes_email_count'];	# total unsubscribes email
						$row['email_track_spam']=$campaign_stat_row['complaint_email_count'];# total complaint emails	
					}
					$freezed_result->free_result();
				} 		
				$rows[]=$row;						
			}
			$result->free_result();
		}		
		return $rows;
	}
	//Function to fetch campaign data
	function get_campaign_data_for_sentmails_new_0($conditions_array=array(),$sort_by='email_send_date',$rows_per_page=10,$start=0){
		$rows=array();		 
		$this->db->select("rec.*,rm.member_username,rm.member_id, cs.email_track_delivered, cs.email_track_bounce, cs.email_track_read, cs.email_track_click,cs.email_track_forward, cs.email_track_unsubscribes,cs.email_track_spam  ",false) ;		
		$this->db->join('red_members as rm','rec.campaign_created_by=rm.member_id');				
		$this->db->join('red_email_campaigns_scheduled as cs','rec.campaign_id=cs.campaign_id');				
		if(isset($_POST['mode'])){
			$member_username	= $_POST['username'];
			$date_from			= $_POST['date_from']; 
			$date_to			= $_POST['date_to'];
			if($_POST['above_critical_level']== 'bounces')
			$this->db->where('bounces_over_limit',1);
			elseif($_POST['above_critical_level']== 'complaints')
			$this->db->where('complaints_over_limit',1);
			
			if($date_from != '')$this->db->where("email_send_date >= '$date_from'");
			if($date_to != '')$this->db->where("email_send_date <= '$date_to'");
			if($member_username != '')$this->db->like('member_username',$member_username);			
		}	
		$this->db->order_by($sort_by,'desc');	
		$result=$this->db->get_where('red_email_campaigns as rec',$conditions_array,$rows_per_page,$start);	
		
		if($result->num_rows()>0){	
		foreach($result->result_array() as $row){
			// $row['list_names'] =$this->getContactList($row['subscription_list']);			
			$rows[]=$row;
		}
		}
		return $rows;
	}
	//Fetch total count of campaigns
	function get_autoresponder_count($conditions_array=array(),$join=false){
		$this->db->select('count(rec.campaign_id) as totCampaigns',false);
		$this->db->from('red_email_autoresponders as rec');
		$this->db->where($conditions_array);
		
			if(isset($_POST['mode'])){
				$member_username	= $_POST['username'];				 
				if($member_username != '')$this->db->like('member_username',$member_username);					
				$this->db->join('red_members as rm','rec.campaign_created_by=rm.member_id');			
			}
			
		
		$result=$this->db->get();
		return $result->row()->totCampaigns;
	}
	//Function to fetch campaign data
	function get_autoresponder_data_for_sentmails($conditions_array=array(),$sort_by='campaign_sheduled',$rows_per_page=10,$start=0){
		$rows=array();		 
		$this->db->select("rec.*,rm.member_username,rm.member_id ",false) ;		
		$this->db->join('red_members as rm','rec.campaign_created_by=rm.member_id');				
		 		
		if(isset($_POST['mode'])){
			$member_username	= $_POST['username'];			 
			if($member_username != '')$this->db->like('member_username',$member_username);			
		}	
		$this->db->order_by($sort_by,'desc');	
		$result=$this->db->get_where('red_email_autoresponders as rec',$conditions_array,$rows_per_page,$start);	
		$rows=$result->result_array();
		$result->free_result();
		/* if($result->num_rows()>0){	
		foreach($result->result_array() as $row){
			$row['list_names'] =$this->getContactList($row['subscription_list']);			
			$rows[]=$row;
		}
		} */
		return $rows;
		
	}
	/**
	* Function to return list of CONTACT-LIST-NAMES
	*/
	function getContactList($list_ids){
		$listNames ='';
		$arrLid = @explode(',',$list_ids);
		if(count($arrLid)>0){
			foreach($arrLid as $lid){
			 
				$rsList = $this->db->query("select subscription_title from red_email_subscriptions where subscription_id='$lid'");
				if($rsList->num_rows()>0){
					foreach($rsList->result() as $rec){
						$listNames .= ', '.$rec->subscription_title;
					}				
				}
			}
		}
	return $listNames;	
	}
	
	//Fetch total count of campaigns
	function get_campaign_count($conditions_array=array(),$join=false){
		$this->db->select('count(rec.campaign_id) as totCampaigns',false);
		$this->db->from('red_email_campaigns as rec');
		$this->db->where($conditions_array);
		
			if(isset($_POST['mode'])){
				$member_username	= trim($_POST['username']);
				$date_from			= $_POST['date_from']; 
				$date_to			= $_POST['date_to'];
				if($date_from != '')$this->db->where("email_send_date >= '$date_from'");
				if($date_to != '')$this->db->where("email_send_date <= '$date_to'");
				if($member_username != '')$this->db->like('member_username',$member_username);					
			$this->db->join('red_members as rm','rec.campaign_created_by=rm.member_id');			
			}
			
		
		$result=$this->db->get();
		return $result->row()->totCampaigns;			
	}
	
	/**
		Function delete_campaign is to delete campaigns from database
		@param campaign_id: campaign id
	*/
	function delete_campaign($campaign_id=0){
		#Fetch campaigns for user
		$rows=array();
		$this->db->select('campaign_id','rcp.id');
		$this->db->from('red_email_campaigns');
		$this->db->join('red_email_campaigns_pages as rcp','rcp.site_id=campaign_id AND is_autoresponder=1');
		$this->db->where(array('campaign_id'=>$campaign_id));
		$result=$this->db->get();		
		foreach($result->result_array() as $row)
		{
			# Delete block' content from database
			$this->db->delete('red_email_campaigns_background_color_block_content',array('red_background_color_page_id'=>$row['id']));
			# Delete campaign's pages from datbase
			$this->db->delete('red_email_campaigns_pages',array('site_id'=>$row['campaign_id'],'is_autoresponder'=>0));
		}
		# delete user's campaigns from database
		$this->db->delete('red_email_campaigns',array('campaign_id'=>$campaign_id));
	}
	/**
		Function delete_campaign_stat is to delete campaign stat from database
	*/
	function delete_campaign_stat($campaign_id=0){
		##########################################
		# Delete Campaign's stat from database	 #
		##########################################
		# Delete user's stat from email track table
		$this->db->delete('red_email_track',array('campaign_id'=>$campaign_id));
		# Delete user's stat from email track queue table
		$this->db->delete('red_email_queue',array('campaign_id'=>$campaign_id));
		#Fetch autoresponder for user
		$rows=array();
		$this->db->select('campaign_id');
		$this->db->from('red_email_campaigns');
		$this->db->where(array('campaign_id'=>$campaign_id));
		$result=$this->db->get();		
		foreach($result->result_array() as $row)
		{
			# Delete user's stat from email track list table
			$this->db->delete('red_email_campaigns_scheduled',array('campaign_id'=>$row['campaign_id']));
		}
		
		# Delete user's click link detail from click rate table
		$this->db->delete('red_click_rate',array('campaign_id'=>$campaign_id));
		#Delete user's stat from email track archive table
		$this->db->delete('red_email_track_freezed',array('campaign_id'=>$campaign_id));		
	}
}
?>