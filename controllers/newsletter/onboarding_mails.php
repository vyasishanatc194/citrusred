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
class Onboarding_mails extends CI_Controller
{
	function __construct(){
		parent::__construct();
		$this->load->model('ConfigurationModel');
		$this->load->helper('onboarding_mails');	
		$this->load->helper('admin_notification');
		$this->confg_arr=$this->ConfigurationModel->get_site_configuration_data_as_array();
	}
	function index(){
		$this->getUnconfirmedUsers();
		$this->getActiveFreeIdleUsers();
		$this->getActiveFreeAll(7);	
		$this->getActiveFreeAll(14);	
		$this->getActiveFreeAll(21);	
		$this->getActiveFreeAll(28);
		$this->getFailedCCAfter7days();		
		$this->getPaidMembersMorethan1month();
		$this->getDowngradedMembersMorethan1monthOld();	
		$this->getDowngradedMembersMorethan2monthOld();			
		$this->getPaiduserWithoutCampaign();	// Doubtful paid users	
	}
	/**
	*	Function to send onboarding mails to admin for test
	*/
	function tempOnboardingMails(){
		
		$arrOBMails = array('member_unconfirmed_yet'=>101, 'active_free_no_campaign_with_contact'=>102, 'active_free_no_campaign_no_contact'=>103, 'active_free_yet_after_7days'=>104, 'active_free_yet_after_14days'=>105, 'active_free_yet_after_21days'=>106, 'active_free_yet_after_28days'=>107, 'failed_cc'=>108 , 'active_free_with_campaign'=>109 , 'paid_more_than_1month'=>110  );
		 
		foreach($arrOBMails as $typ=>$mid){
			$mail_id = $mid;		
			$user_info=array('Admin');	
			$to = $this->confg_arr['admin_notification_email'];	
			$arrTo = explode(',', $to);			
			foreach($arrTo as $e)	
			send_onboarding_mail($typ,$user_info, $e);			
			 
		}
		
	}
	/**
	*	Function to send onboarding mails
	*/
	function sendOnboardingMails($arrUser, $typ='member_unconfirmed_yet'){
		
		$arrOBMails = array('member_unconfirmed_yet'=>101, 'active_free_no_campaign_with_contact'=>102, 'active_free_no_campaign_no_contact'=>103, 'failed_cc'=>108 , 'active_free_with_campaign'=>109 , 'paid_more_than_1month'=>110, 'downgraded_more_than_1month'=>111, 'downgraded_more_than_2month'=>112    );
		$mid = $arrUser['mid'];
		$mail_id = $arrOBMails[$typ];
		$rsCheckSent = $this->db->query("select * from red_onboarding_mails where member_id='$mid' and mail_id='$mail_id'");
		if($rsCheckSent->num_rows() <= 0){
			$user_info=array($arrUser['mname'],$arrUser['encMid']);			 
			$this->db->query("insert into red_onboarding_mails set member_id='$mid', mail_id='$mail_id'");
			
			//send_onboarding_mail($typ,$user_info, $arrUser['memail']);
			// Admin notification starts					 		
			$to = 'peejha@yahoo.com';//$this->confg_arr['admin_notification_email'];		
			$message = "<p>Hello admin,</p><p>Onboarding mail sent for <b>$typ</b>: ". implode(',',$arrUser)." </p><p>Regards,<br />Redcappi Team</p>";		
			$text_message= "Onboarding mail-sent with detail as $typ: ". implode(',',$arrUser);
			//admin_notification_send_email($to, SYSTEM_EMAIL_FROM,'RedCappi', "OB mail for $typ",$message,$text_message);
			// Admin notification ends
			
		}
		$rsCheckSent->free_result();
		
	}
	/**
	*	Function to get unconfirmed-users within past 24 to 48hrs.
	* 	Users who have not clicked on the link sent in email
	*/
	function getUnconfirmedUsers(){ 
		set_time_limit(0); 
		
		$rsUMembers = $this->db->query("select member_id, member_username, email_address,created_on from red_members where status='unconfirmed' and created_on < DATE_SUB(CURDATE(), INTERVAL 24 HOUR) and created_on > DATE_SUB(CURDATE(), INTERVAL 48 HOUR) LIMIT 10");
		
		if($rsUMembers->num_rows()>0){
			$arrUser = array();
			foreach($rsUMembers->result() as $row){
				$encMid = $this->is_authorized->encryptor('encrypt', $row->member_id);				
				$arrUser = array('mid'=>$row->member_id, 'mname'=>$row->member_username, 'memail'=>$row->email_address, 'encMid'=>$encMid);				
				$this->sendOnboardingMails($arrUser, 'member_unconfirmed_yet');				
			}
		}	
		$rsUMembers->free_result();
		
	}
	/**
	*	Function to get [active_free_no_campaign_no_contact] & [active_free_no_campaign_with_contact] after 48hrs. to 72hrs.
	*/
	function getActiveFreeIdleUsers(){ 
		set_time_limit(0);
		
		$rsUMembers = $this->db->query("select m.member_id, m.member_username, m.email_address, m.created_on, mp.is_first_campaign_send from red_members m inner join red_member_packages mp on m.member_id=mp.member_id where m.status='active' and mp.package_id < 1 and m.created_on < DATE_SUB(CURDATE(), INTERVAL 48 HOUR) and created_on > DATE_SUB(CURDATE(), INTERVAL 72 HOUR) ");
		
		if($rsUMembers->num_rows()>0){
			$arrUser = array();
			foreach($rsUMembers->result() as $row){
				$thisMid = $row->member_id;
				
					$isCampaignSent = $row->is_first_campaign_send;
					if($isCampaignSent > 0){
						$this->sendOnboardingMails($arrUser, 'active_free_with_campaign');
					}else{
						$arrUser = array('mid'=>$row->member_id, 'mname'=>$row->member_username, 'memail'=>$row->email_address);
						$rsCheckContact = $this->db->query("select count(subscriber_id)tot from red_email_subscribers where subscriber_created_by='$mid' and subscriber_status=1 and is_deleted=0" );
						$intCheckContact = $rsCheckContact->row()->tot;
						$rsCheckContact->free_result();
						if($intCheckContact > 0){
							$this->sendOnboardingMails($arrUser, 'active_free_no_campaign_with_contact');
						}else{
							$this->sendOnboardingMails($arrUser, 'active_free_no_campaign_no_contact');				
						}
					}
					
			}
		}	
		$rsUMembers->free_result();
		
	}
	/**
	*	Function to get active_free_users within past 7days to 30days.
	*/
	function getActiveFreeAll($d=7){ 
		set_time_limit(0); 
		$days_upper_limit =$d + 7;
		//$rsUMembers = $this->db->query("select m.member_id, m.member_username, m.email_address, m.created_on from red_members m inner join red_member_packages mp on m.member_id=mp.member_id where m.status='active' and mp.package_id < 1 and m.created_on < DATE_SUB(CURDATE(), INTERVAL $d DAY) and m.created_on > DATE_SUB(CURDATE(), INTERVAL $days_upper_limit DAY)");
		$rsUMembers = $this->db->query("select m.member_id, m.member_username, m.email_address, m.created_on from red_members m inner join red_member_packages mp on m.member_id=mp.member_id where m.status='active' and mp.package_id < 1 and date_format(m.created_on, '%Y-%m-%d') = DATE_SUB(CURDATE(), INTERVAL $d DAY)");
		
		if($rsUMembers->num_rows()>0){
			$arrUser = array();
			foreach($rsUMembers->result() as $row){
				$thisMid = $row->member_id;
				$createdOnDt = $row->created_on;
				$arrUser = array('mid'=>$thisMid, 'mname'=>$row->member_username, 'memail'=>$row->email_address,'created_on'=>$createdOnDt);
				
				$this->sendOnboardingMails($arrUser, "active_free_yet_after_{$d}days");
				/*
				$dateDiff = floor((strtotime(date('Y-m-d H:i:s')) - strtotime($createdOnDt))/(24*60*60));
				
				if($dateDiff >= 7 && $dateDiff < 14){
					$this->sendOnboardingMails($arrUser, 'active_free_yet_after_7days');
				}elseif($dateDiff >= 14 && $dateDiff < 21){
					$this->sendOnboardingMails($arrUser, 'active_free_yet_after_14days');
				}elseif($dateDiff >= 21 && $dateDiff < 28){
					$this->sendOnboardingMails($arrUser, 'active_free_yet_after_21days');
				}elseif($dateDiff >= 28){
					$this->sendOnboardingMails($arrUser, 'active_free_yet_after_28days');
				}
				*/
			}
		}	
		$rsUMembers->free_result();
		
	}
	function getFailedCCAfter7days(){
		set_time_limit(0); 
		
		$rsFailedCCMembers = $this->db->query("select m.member_id, m.member_username, m.email_address, mp.next_payement_date from red_members m inner join red_member_packages mp on m.member_id=mp.member_id where m.status='active' and mp.package_id > 0 and mp.next_payement_date < DATE_SUB(CURDATE(), INTERVAL 6 DAY) and mp.next_payement_date > DATE_SUB(CURDATE(), INTERVAL 8 DAY) LIMIT 10");
	
		if($rsFailedCCMembers->num_rows()>0){			
			foreach($rsFailedCCMembers->result() as $row){
				$thisMid = $row->member_id;			
				$this->sendOnboardingMails(array('mid'=>$thisMid, 'mname'=>$row->member_username, 'memail'=>$row->email_address,'next_payement_date'=>$next_payement_date), 'failed_cc'); 				
			}
		}	
		$rsFailedCCMembers->free_result();
	
	}
	
	function getPaidMembersMorethan1month(){
		set_time_limit(0); 
		
		$rsPaidMembersMorethan1month = $this->db->query("select m.member_id, m.member_username, m.email_address, mp.start_payment_date from red_members m inner join red_member_packages mp on m.member_id=mp.member_id where m.status='active' and mp.package_id > 0 and mp.start_payment_date < DATE_SUB(CURDATE(), INTERVAL 40 DAY) and mp.next_payement_date > CURDATE() LIMIT 10");
	
		if($rsPaidMembersMorethan1month->num_rows()>0){
			
			foreach($rsPaidMembersMorethan1month->result() as $row){
				$thisMid = $row->member_id;
				$this->sendOnboardingMails(array('mid'=>$thisMid, 'mname'=>$row->member_username, 'memail'=>$row->email_address,'next_payement_date'=>$next_payement_date), 'paid_more_than_1month'); 				
			}
		}	
		$rsPaidMembersMorethan1month->free_result();
	
	}
	function getPaidDetails($mid=0,$s=0,$l=50000){
		$rsMem = $this->db->query("select subscriber_email_address,subscriber_status,is_deleted,`ignore`, subscrber_bounce, bounce_status,`read` from red_email_subscribers where subscriber_created_by='$mid' limit $s, $l");
		foreach($rsMem->result_array() as $row){
			echo "<br/>".implode(',',$row);							
		}
		$rsMem->free_result();
	}
	function getDowngradedMembersMorethan1monthOld(){
		set_time_limit(0); 
		
		$rsPaidMembersMorethan1month = $this->db->query("select m.member_id, m.member_username, m.email_address, mp.start_payment_date from red_members m inner join red_member_packages mp on m.member_id=mp.member_id where m.status='active' and mp.next_payement_date < DATE_SUB(CURDATE(), INTERVAL 30 DAY)  and mp.next_payement_date > DATE_SUB(CURDATE(), INTERVAL 60 DAY)  LIMIT 10");
		
		if($rsPaidMembersMorethan1month->num_rows()>0){
			
			foreach($rsPaidMembersMorethan1month->result() as $row){
				$thisMid = $row->member_id;			
				$this->sendOnboardingMails(array('mid'=>$thisMid, 'mname'=>$row->member_username, 'memail'=>$row->email_address,'next_payement_date'=>$next_payement_date), 'downgraded_more_than_1month'); 				
			}
		}	
		$rsPaidMembersMorethan1month->free_result();
	
	}	
	function getDowngradedMembersMorethan2monthOld(){
		set_time_limit(0); 
		
		$rsPaidMembersMorethan1month = $this->db->query("select m.member_id, m.member_username, m.email_address, mp.start_payment_date from red_members m inner join red_member_packages mp on m.member_id=mp.member_id where m.status='active' and mp.next_payement_date < DATE_SUB(CURDATE(), INTERVAL 60 DAY) and mp.next_payement_date > DATE_SUB(CURDATE(), INTERVAL 90 DAY)  LIMIT 10");
	
		if($rsPaidMembersMorethan1month->num_rows()>0){
			
			foreach($rsPaidMembersMorethan1month->result() as $row){
				$thisMid = $row->member_id;				
				$this->sendOnboardingMails(array('mid'=>$thisMid, 'mname'=>$row->member_username, 'memail'=>$row->email_address,'next_payement_date'=>$next_payement_date), 'downgraded_more_than_2month'); 				
			}
		}	
		$rsPaidMembersMorethan1month->free_result();
	
	}
	function getPaiduserWithoutCampaign(){
		set_time_limit(0); 
		
		$rsPaiduserWithoutCampaign = $this->db->query("select m.member_id, m.member_username, m.email_address from red_members m inner join red_member_packages mp on m.member_id=mp.member_id where m.status='active' and mp.package_id > 0 and mp.next_payement_date > CURDATE() and mp.is_first_campaign_send =0 ");
	
		if($rsPaiduserWithoutCampaign->num_rows()>0){
			$doubtfullUsers = '<table width="40%" cellspacing="2" cellpadding="2" border="0"><tr><th>ID</th><th>User</th></tr>';
			$doubtfullUsersText = '';
			foreach($rsPaiduserWithoutCampaign->result() as $row){
				$thisMid = $row->member_id;				
				$thisMname = $row->member_username;				
				$doubtfullUsers .= "<tr><td>$thisMid</td><td>$thisMname</td></tr>";
				$doubtfullUsersText .= "$thisMid,$thisMname \n";
			}
			$to = $this->confg_arr['admin_notification_email'];		
			$message = "<p>Hello admin,</p><p>Onboarding mail sent for <b>Doubtful Users (Paid but never sent campaign)</b><br/>". $doubtfullUsers." </p><p>Regards,<br />Redcappi Team</p>";		
			$text_message= "Onboarding mail-sent with detail as Doubtful Users (Paid but never sent campaign): $doubtfullUsersText";		
			admin_notification_send_email($to, SYSTEM_EMAIL_FROM,'RedCappi', "OB Mail for Doubtful Users",$message,$text_message);
		}	
		$rsPaiduserWithoutCampaign->free_result();
	
	}
}
?>