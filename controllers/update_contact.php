<?php
class Update_contact extends CI_Controller
{
	function __construct(){
        parent::__construct();
		ini_set('memory_limit', '-1'); 	
		set_time_limit(0); 
	}
	function contact_analysis(){
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', '-1');
		set_time_limit(0); 		
		$mid = 5930;	
		$ignoreCount = 0;	
		for($i=0;$i < 5	;$i++){
			$rsEachContacts = $this->db->query("select subscriber_id,subscriber_email_address from red_email_subscribers where subscriber_created_by='$mid' and subscriber_status=1 and is_deleted=0 and is_analysed=0 limit 20000");	
			foreach($rsEachContacts->result_array() as $recContacts){		
				$sidForContact = $recContacts['subscriber_id'];
				$emlForContact = $recContacts['subscriber_email_address'];	
				
				// repeated or virgin
				$rsForUnresponsives = $this->db->query("select sum(sent) sentcount,sum(`read`) readdcount from red_email_subscribers where subscriber_created_by !='$mid'  and subscriber_email_address='$emlForContact' limit 1");	
				$countSentForUnresponsives = $rsForUnresponsives->row()->sentcount;
				$countReadForUnresponsives = $rsForUnresponsives->row()->readdcount;
				if($countReadForUnresponsives == 0  and $countSentForUnresponsives > 4){							
					$ignoreCount++;
					$this->db->query("update red_email_subscribers set `ignore`= 1,is_analysed=1 where `subscriber_id`='$sidForContact'");			
				}else{
					$this->db->query("update red_email_subscribers set is_analysed=1 where `subscriber_id`='$sidForContact'");			
				}
				$rsForUnresponsives->free_result();					
			}
			$rsEachContacts->free_result();	
		}		
		echo 'Ignored contacts count='.$ignoreCount;			
	}

	function yahoo_delivered(){
		$arrMembers = array(562,1436,2060,2270,2282,2316,2391,2595,3197,3252,3302,3352,3379,3382,3458,3487,3491,3588,3653,3672,3740,3795,3833,3870,4080,4166,4211,4319,4331,5180,5204,5267,5289,5340,5589,5656,5657,5698,5719);
		foreach($arrMembers as $m){
			$rsCampaign = $this->db->query("select campaign_id from red_email_campaigns where campaign_status='active' and campaign_created_by='$m' order by email_send_date desc limit 5");
			
			$arrCid = array();
			$countDelivered = 0;
			foreach($rsCampaign->result_array() as $mail_row){
				$thisCid = $mail_row['campaign_id'];
				$countDelivered += $this->db->query("select count(queue_id) as delivered from red_email_track where campaign_id = '$thisCid' and email_delivered > 0 and subscriber_email_domain like'%yahoo%' ")->row()->delivered;
			}
		
			echo $m.', '.$countDelivered."<br/>";
			
		}
	}	
	function paid_users_responsive_contacts(){ 
		
		//$arrMembers = array(100, 338, 368, 562, 612, 628, 658, 817, 996,1179,1436,1523,1573,1658,1683,1796,2060,2270,2282,2316,2467,2595,2703,2719,3058,3083,3197,3215,3243,3252,3288,3302,3333,3352,3379,3382,3434,3439,3445,3458,3462,3487,3489,3491,3523,3532,3550,3588,3615,3653,3664,3672,3700,3740,3788,3795,3833,3856,3870,3871,3876,4019,4032,4055,4080,4088,4118,4138,4146,4166,4204,4205,4206,4208,4211,4217,4252,4305,4319,4320,4322,4331,4332,4344,4345,4353,4359,4368,4386,4408,4448,4459,4462,4466,4483,4486,4519,4567,4596,4666,4704,4806,4843,4867,4907,4919,4934,4972,4988,5025,5035,5102,5103,5109,5170,5180,5193,5204,5255,5267,5289,5305,5340,5377,5385,5472,5478,5487,5575,5582,5584,5589,5629,5635,5656,5657,5674,5698,5707,5710,5714,5719,5750,5782,5791,5800,5803,5829,5841,5843,5844);
		$arrMembers = array(368, 562, 996,1436,1573,2060,2270,2282,2316,2467,2595,2703,2719,3197,3215,3243,3252,3288,3302,3333,3352,3379,3382,3439,3445,3458,3487,3489,3491,3550,3588,3653,3740,3833,3856,3870,3871,3876,4032,4080,4166,4204,4211,4252,4319,4331,4353,4359,4462,4466,4486,4596,4666,4704,4919,5025,5109,5180,5267,5289,5305,5340,5472,5582,5584,5589,5629,5656,5657,5698,5707,5719,5843);
		
		//echo "user_id, username, responsive_count \n<br/>";
		echo "last_2months \n<br/>";
		foreach($arrMembers as $m){
		
			//$uname = $this->db->query("select member_username from red_members where member_id='$m'")->row()->member_username;
			$rsMembers = $this->db->query("select ifnull(count(subscriber_id),0) ct from red_email_subscribers where subscriber_created_by ='$m' and subscriber_status=1 and is_deleted=0 and `read` > 0 and sent > 0 and last_read_date > '2014-11-20'");
			$responsive_count = $rsMembers->row()->ct;			 
			$rsMembers->free_result();
			//echo "$m, $uname, $responsive_count \n<br/>";				
			echo "$responsive_count \n<br/>";				
		}		
	}
	function last_five_campaign_detail(){ 
		
		$arrMembers = array(3197,3458,3487,3833,3870,4166,4211,4353,4462,4596,4704,5109,5267,5340,5478,5487,5707);
		
		echo "user_id, username, URL, email_dt,subject,from_name, from_email \n<br/>";
		foreach($arrMembers as $m){
		
			//$rsMembers = $this->db->query("select member_username,vmta from red_members where member_id='$m'");
			$rsMembers = $this->db->query("select member_username,concat('http://www.red7.me/c/',campaign_id) curl, email_send_date, sender_name,sender_email, email_subject from red_email_campaigns c inner join red_members m on c.campaign_created_by=m.member_id where campaign_status='active' and campaign_created_by='$m' order by email_send_date desc limit 5;
");
			foreach($rsMembers->result_array() as $m_row){
				
				$uname	= $m_row['member_username'];
				$curl	= $m_row['curl'];
				$senddt	= $m_row['email_send_date'];
				$email_subject	= $m_row['email_subject'];
				$sender_name	= $m_row['sender_name'];
				$sender_email	= $m_row['sender_email'];
			echo "$m, $uname, $curl, $senddt, $email_subject, $sender_name, $sender_email \n<br/>";			
			}
			$rsMembers->free_result();
			
		}		
	}
	function paiduser_pipeline_deliveredcount(){ 
		//$arrMembers = array(100,338,368,562,612,628,658,817,996,1179,1404,1436,1523,1573,1658,1683,1796,2060,2270,2282,2391,2467,2531,2595,2703,2719,3058,3083,3197,3215,3243,3252,3288,3302,3333,3352,3379,3382,3434,3439,3445,3458,3462,3487,3489,3491,3523,3532,3550,3588,3615,3672,3700,3740,3788,3795,3833,3856,3870,3876,3917,4019,4032,4055,4080,4088,4118,4138,4146,4166,4193,4205,4206,4208,4211,4217,4252,4305,4319,4320,4322,4331,4332,4345,4353,4368,4408,4448,4459,4462,4466,4483,4486,4519,4566,4567,4596,4666,4704,4806,4843,4867,4907,4919,4934,4972,4988,5025,5035,5053,5059,5102,5103,5109,5170,5180,5187,5193,5204,5249,5255,5267,5289,5305,5316,5340,5377,5385,5443,5472,5478,5487,5502,5575,5582,5584,5589,5592,5629,5656,5657,5674,5698,5707,5710,5714,5719,5733);
		//$arrMembers = array(3197,3458,3487,3833,3870,4166,4211,4353,4462,4596,4704,5109,5267,5340,5478,5487,5707);
		$arrMembers = array(5109,5267,5340,5478,5487,5707);
		echo "user_id, username, pipeline, delivered \n";
		foreach($arrMembers as $m){
		
			$rsMembers = $this->db->query("select member_username,vmta from red_members where member_id='$m'");
			$uname	= $rsMembers->row()->member_username;
			$vmta	= $rsMembers->row()->vmta;
			$rsMembers->free_result();
			
			$rsDelivered = $this->db->query("select count(queue_id) as ct from red_email_track where user_id='$m' and email_sent_date > '2014-12-20' and email_delivered > 0");
			$delivered	= $rsDelivered->row()->ct;
			$rsDelivered->free_result();
			echo "$m, $uname, $vmta, $delivered \n";			
		}
		
	}
	function signup_show_form($l=5){ 
		$qForm = "select id,field_sequence from red_signup_form limit $l";
		$rsForm = $this->db->query($qForm);
		foreach($rsForm->result_array() as $form_row){
				$arrNEWFldSequence = array();
				echo $id = $form_row['id'];		
				echo "===";				
				$field_sequence = $form_row['field_sequence'];		
				$arrFieldSequence = unserialize($field_sequence);
				foreach($arrFieldSequence as $k=>$v){
					$arrNEWFldSequence['fld_name'][]= str_replace(' ','_',$k);
					$arrNEWFldSequence['fld_type'][]= 'text';
					$arrNEWFldSequence['fld_required'][]= ($k == 'email')?'Y':'N';
					$arrNEWFldSequence['fld_options'][]= '';
				}
			print_r($arrNEWFldSequence);
			echo "===";			
			echo $NewFldSequence =  serialize($arrNEWFldSequence);
			$this->db->query("update red_signup_form set fld_sequence='$NewFldSequence' where id='$id'");
				echo "<br/><hr/><br/>";
		}	
	
	$rsForm->free_result();
	}
	//a:4:{s:8:"fld_name";a:6:{i:0;s:4:"name";i:1;s:5:"email";i:2;s:3:"DOB";i:3;s:10:"first_name";i:4;s:9:"last_name";i:5;s:3:"Ram";}s:8:"fld_type";a:6:{i:0;s:4:"text";i:1;s:4:"text";i:2;s:13:"date_dropdown";i:3;s:4:"text";i:4;s:4:"text";i:5;s:4:"text";}s:12:"fld_required";a:6:{i:0;s:1:"Y";i:1;s:1:"Y";i:2;s:1:"N";i:3;s:1:"N";i:4;s:1:"N";i:5;s:1:"Y";}s:11:"fld_options";a:6:{i:0;s:0:"";i:1;s:0:"";i:2;s:0:"";i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";}}
	
	//a:4:{s:8:"fld_name";a:2:{i:0;s:5:"email";i:1;s:4:"name";}s:8:"fld_type";a:2:{i:0;s:4:"text";i:1;s:4:"text";}s:12:"fld_required";a:2:{i:0;s:1:"Y";i:1;s:1:"N";}s:11:"fld_options";a:2:{i:0;s:0:"";i:1;s:0:"";}}
	function show_stats($mon=1){ 
		$arrMembers = array(3,   76,  100,  206,  305,  338,  368,  384,  562,  628,  645,  658,  817,  996, 1120, 1179, 1209, 1214, 1404, 1435, 1436, 1523, 1573, 1621, 1658, 1659, 1683, 1796, 1962, 2060, 2064, 2104, 2237, 2251, 2256, 2270, 2278, 2292, 2316, 2423, 2455, 2467, 2571, 2595, 2703, 2719, 2741, 2913, 3020, 3083, 3197, 3215, 3243, 3252, 3288, 3302, 3313, 3333, 3334, 3352, 3379, 3382, 3387, 3388, 3398, 3403, 3434, 3439, 3445, 3446, 3458, 3462, 3487, 3489, 3491, 3496, 3515, 3523, 3530, 3532, 3550, 3567, 3571, 3588, 3615, 3653, 3671, 3672, 3676, 3692, 3696, 3698, 3700, 3726, 3740, 3747, 3757, 3758, 3788, 3791, 3795, 3833, 3870, 3876, 3882, 3887, 3917, 3931, 3950, 3966, 3976, 3982, 3991, 3998, 4019, 4029, 4032, 4035, 4055, 4069, 4080, 4118, 4138, 4146, 4166, 4193, 4205, 4211, 4217);
		
		foreach($arrMembers as $m){
			//$rsCampaigns = $this->db->query("select campaign_id from red_email_campaigns where campaign_created_by='$m' and email_send_date  between '2014-01-01 00:00:01' and '2014-01-1' and campaign_status='active'");
			$rsCampaigns = $this->db->query("select campaign_id from red_email_campaigns where campaign_created_by='$m' and YEAR(email_send_date) = '2014' and  MONTH(email_send_date)='$mon' and campaign_status='active'");
			$arrCampaigns =  array();
			unset($arrCampaigns);
			foreach($rsCampaigns->result_array() as $campaign_row){
				$arrCampaigns[] = $campaign_row['campaign_id'];		
			}	
			if(count($arrCampaigns) > 0){
				$strCampaigns  = @implode(', ',$arrCampaigns);
				
				$rsStats = $this->db->query("select user_id, count(queue_id) as sent, sum(email_delivered) as delivered, sum(email_track_read) as opened, (sum(email_delivered) - sum(email_track_read)) as unopened, sum(email_track_click) as clicks, sum(email_track_forward) as forwards, sum(email_track_unsubscribes) as unsubscribes, sum(email_track_bounce) as bounced, sum(email_track_complaint) as complaints from red_email_track where user_id='$m' and campaign_id in($strCampaigns) group by user_id");
				foreach($rsStats->result_array() as $stats_row){
					$strTable .= "<tr><td>".$stats_row['user_id']."</td><td>\"".$strCampaigns."\"</td><td>".$stats_row['sent']."</td><td>".$stats_row['delivered']."</td><td>".$stats_row['opened']."</td><td>".$stats_row['unopened']."</td><td>".$stats_row['clicks']."</td><td>".$stats_row['forwards']."</td><td>".$stats_row['unsubscribes']."</td><td>".$stats_row['bounced']."</td><td>".$stats_row['complaints']."</td></tr>"	;
				}
			}	
		}
		
		echo "<table cellspacing='5' cellpadding='5' border='0'><tr><th>Member-ID</th><th>Campaigns</th><th>Sent</th><th>Delivered</th><th>Opened</th><th>Unopened</th><th>Clicks</th><th>Forwards</th><th>Unsubscribes</th><th>Bounces</th><th>Complaints</th></tr>".$strTable."</table>";
		
	}
	function show_campaigns($mid=0){ 
		$strCampaigns ='';
		$rsShowCampaigns = $this->db->query("select email_subject, campaign_content from red_email_campaigns where campaign_created_by='$mid' and campaign_status='active' and is_deleted=0 and is_status=0");
		foreach($rsShowCampaigns->result_array() as $row){
			$strCampaigns = "<h2>".$row['email_subject']."</h2>";
			$strCampaigns .= $row['campaign_content']."<br /><hr />";		
		}	
		echo $strCampaigns ;
	}
	function signupform_update(){ 
		$rsSignupforms = $this->db->query("select * from red_signup_form where  1");
		foreach($rsSignupforms->result_array() as $row){
			$arrFlds = array();
			$strId = $row['id'];
			if($row['is_email'])$arrFlds['email']='';
			if($row['is_name'])$arrFlds['name']='';
			if($row['is_first_name'])$arrFlds['first_name']='';
			if($row['is_last_name'])$arrFlds['last_name']='';
			if($row['is_company'])$arrFlds['company']='';
			if($row['is_address'])$arrFlds['address']='';
			if($row['is_city'])$arrFlds['city']='';
			if($row['is_state  '])$arrFlds['state']='';
			if($row['is_zip_code'])$arrFlds['zip_code']='';
			if($row['is_country'])$arrFlds['country']='';

			if($row['custom_field'] !=''){
				$arrCustomFld = explode(';',$row['custom_field']);
				foreach($arrCustomFld as $cfld)
				$arrFlds["$cfld"]='';
			}
			echo"<pre>";
			print_r($arrFlds);
			echo"</pre>";
			$fld_sequence_serialized = serialize($arrFlds);
			$this->db->query("update red_signup_form set `field_sequence`= '$fld_sequence_serialized' where `id` = '$strId'");
			unset($arrFlds);
		}
		
	}
	function index1(){ 
		$totContacts = $this->db->query("select max(subscriber_id) as totContact from red_email_subscribers")->row()->totContact;
		$x = $this->db->query("select min(subscriber_id)-1 as minId from red_email_subscribers")->row()->minId -1;
		$chunkSize = 1000;		
		while($x < $totContacts){
			
			$sqlCount = "update red_email_subscribers set subscriber_email_domain= substring_index(subscriber_email_address,'@',-1) where subscriber_email_domain is NULL and subscriber_id >$x and subscriber_id <= ($x + $chunkSize)";
			echo $sqlCount;
			$this->db->query($sqlCount);
			echo "---".$this->db->affected_rows()."<br/>";
			$x += $chunkSize;		
		}
		
	}
 	function index2(){ 
		$totContacts = $this->db->query("select max(queue_id) as totContact from red_email_track where subscriber_email_domain is NULL")->row()->totContact;
		$x = $this->db->query("select min(queue_id)-1 as minId from red_email_track where subscriber_email_domain is NULL")->row()->minId;
		$chunkSize = 1000;		 
		while($x < $totContacts){
			
			$sqlCount = "update red_email_track set subscriber_email_domain= substring_index(subscriber_email_address,'@',-1) where subscriber_email_domain is NULL and queue_id >$x and queue_id <= ($x + $chunkSize)";
			#echo $sqlCount;
			$this->db->query($sqlCount);
			echo "---".$this->db->affected_rows()."<br/>";
			$x += $chunkSize;		
		}
		echo '-- DONE --';
	}
 	
}	
?>