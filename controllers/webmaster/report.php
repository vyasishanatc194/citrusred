<?php
class Report extends CI_Controller
{
	function __construct(){
		parent::__construct();
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');

		// Load the campaign model which interact with database
		$this->load->model('UserModel');
		$this->load->model('newsletter/Campaign_Model');
		$this->load->model('webmaster/Campaigns_Model');
		$this->load->model('newsletter/Emailreport_Model');		
		$this->load->model('newsletter/Page_Model');
		$this->load->model('newsletter/Subscriber_Model');
		
		# HTTPS/SSL enabled
		force_ssl();
		$this->output->enable_profiler(false);

	}
	/**
	*	Function to show Global IPR
	*/
		
	function global_ipr($interval=0){	
		$arrPools = array_keys(config_item('pool_vmta'));		
		$arrRcusers = array();
		$rsRCUsers = $this->db->query("select m.member_id,member_username from red_members m inner join red_member_packages mp on m.member_id=mp.member_id where m.status='active' and m.is_deleted=0 and mp.package_id > 0 and mp.next_payement_date > now() order by member_username");
		foreach($rsRCUsers->result() as $rcuserrow){
			$arrRcusers[$rcuserrow->member_id] = $rcuserrow->member_username;
		}
		$pipeline = '';
		$rcuser = '';
		
		$date_to = gmdate('Y-m-d');
		$date_from = gmdate('Y-m-d',(strtotime($date_to) - (24*60*60)));
		$previousDay = gmdate('Y-m-d',(strtotime($date_to) - (24*60*60)));		
		$nextDay = gmdate('Y-m-d',(strtotime($date_to) + (24*60*60)));		
		
		$this->load->view('webmaster/header',array('title'=>'Campaign Segmentation','logo_link'=>'webmaster/dashboard_stat'));
		$this->load->view('webmaster/global_ipr_daily',array('arrPools'=>$arrPools,'pipeline'=>$pipeline,'arrRcusers'=>$arrRcusers,'rcuser'=>$rcuser,'date_from'=>$date_from,'date_to'=>$date_to,'previousDay'=>$previousDay,'nextDay'=>$nextDay, 'strGlobalIPR'=>$this->showIPR()));
			
		$this->load->view('webmaster/footer');
	} 
	function showIPR($typ=''){
		
		
		if($this->input->post('btn_search')=='Search'){
			$pipeline = $this->input->post('pipeline');
			$rcuser = $this->input->post('rcuser');
			$date_from = $this->input->post('date_from');
			$date_to = $this->input->post('date_to');
		}else{
			$pipeline = '';			
			$rcuser = '';			
			$date_to = gmdate('Y-m-d');			
			$date_from = gmdate('Y-m-d',(strtotime($date_to) - (24*60*60)));
		}	
			$thisUser = ($rcuser != '')?$this->db->query("select member_username from red_members where member_id='$rcuser'")->row()->member_username : '';
			$clausePipeline ='';
			if($pipeline =='redrotate' or $pipeline =='redrotate2' or $pipeline =='redrotate3'){
				$clausePipeline = " and pipeline in('redrotate','redrotate2','redrotate3','rcmailer2','rcmailer3','rcmailer4','rcmailer6','rcmailer7','rcmailer8','rcmailer9','rcmailer10','rcmailer11','rcmailer12')";	
			}elseif($pipeline =='rcmailsv.com'){
				$clausePipeline = " and pipeline in('rcmailsv.com','rc73','rc74','rc75','rc76')";
			}elseif($pipeline =='redcappi.com'){
				$clausePipeline = " and pipeline in('redcappi.com','rcorp73')";
			}elseif($pipeline =='rcmailcorp.com'){
				$clausePipeline = " and pipeline in('rcmailcorp.com','rcorp74')";
			}elseif($pipeline =='mailsvrc.com'){
				$clausePipeline = " and pipeline in('mailsvrc.com','rc33','rc34','rc35','rc36', 'rc172', 'rc173')";
			}elseif($pipeline =='mailsvrc2.com'){
				$clausePipeline = " and pipeline  in('mailsvrc2.com','rc13','rc88','rc89') ";
			}elseif($pipeline =='mailsvrc3.com'){
				$clausePipeline = " and pipeline  in('mailsvrc3.com','rc110','rc111') ";
			}elseif($pipeline =='mailsvrc4.com'){
				$clausePipeline = " and pipeline  in('mailsvrc4.com','rc112','rc113') ";
			}elseif($pipeline =='mailsvrc5.com'){
				$clausePipeline = " and pipeline  in('mailsvrc5.com','rc114','rc115') ";
			}elseif($pipeline =='mailsvrc6.com'){
				$clausePipeline = " and pipeline  in('mailsvrc6.com','rc116','rc117','rc118') ";
			} 
			$clauseUsers = ($rcuser != '')?" and `user_id` = '$rcuser'" : '';
			//$rsDailyIPR = $this->db->query("select mail_domain, pipeline as vmta, sum(total_sent)total_sent, sum(total_delivered)total_delivered, sum(total_opened)total_opened, sum(total_bounced)total_bounced, sum(total_unsubscribed)total_unsubscribed, sum(total_complaint)total_complaint from red_global_ipr_daily where log_date between '$date_from' and '$date_to' $clausePipeline group by mail_domain,pipeline");
			if($clausePipeline ==''){ 
				$sqlIPR = "select mail_domain, Case WHEN(mail_domain='gmail.com')then 1 WHEN(mail_domain='yahoo.com')then 2 WHEN(mail_domain='hotmail.com')then 3 WHEN(mail_domain='aol.com')then 4 WHEN(mail_domain='msn.com')then 5 WHEN(mail_domain='outlook.com')then 6 WHEN(mail_domain='windowslive.com')then 7 WHEN(mail_domain='live.com')then 8 WHEN(mail_domain='mail.ru')then 9 WHEN(mail_domain='me.com')then 10 WHEN(mail_domain='mac.com')then 11 WHEN(mail_domain='comcast.net')then 12 WHEN(mail_domain='cox.net')then 13 END as ord, sum(total_sent)total_sent, sum(total_delivered)total_delivered, sum(total_opened)total_opened, sum(total_bounced)total_bounced, sum(total_unsubscribed)total_unsubscribed, sum(total_complaint)total_complaint from red_global_ipr_daily where log_date > '$date_from 23:59:59' and  log_date < '$date_to 23:59:59' $clauseUsers group by mail_domain order by ord";
			}else{
				$sqlIPR = "select Case WHEN (pipeline='redrotate' or pipeline='redrotate2' or pipeline='redrotate3' or pipeline='rcmailer2' or pipeline='rcmailer3' or pipeline='rcmailer4' or pipeline='rcmailer6' or pipeline='rcmailer7' or pipeline='rcmailer8' or pipeline='rcmailer9' or pipeline='rcmailer10' or pipeline='rcmailer11' or pipeline='rcmailer12') then 'redrotate' WHEN (pipeline='rcmailsv.com' or pipeline='rc73' or pipeline='rc74' or pipeline='rc75' or pipeline='rc76') then 'rcmailsv.com' WHEN (pipeline='redcappi.com' or pipeline='rcorp73') then 'redcappi.com' WHEN (pipeline='rcorp74' or pipeline='rcmailcorp.com') then 'rcmailcorp.com' WHEN (pipeline='rc33' or pipeline='rc34' or pipeline='rc35' or pipeline='rc36' or pipeline='rc172' or pipeline='rc173' or pipeline='mailsvrc.com') then 'mailsvrc.com'  WHEN (pipeline='rc13' or pipeline='rc88' or pipeline='rc89' or pipeline='mailsvrc2.com') then 'mailsvrc2.com' END as vmta, mail_domain, Case WHEN(mail_domain='gmail.com')then 1 WHEN(mail_domain='yahoo.com')then 2 WHEN(mail_domain='hotmail.com')then 3 WHEN(mail_domain='aol.com')then 4 WHEN(mail_domain='msn.com')then 5 WHEN(mail_domain='outlook.com')then 6 WHEN(mail_domain='windowslive.com')then 7 WHEN(mail_domain='live.com')then 8 WHEN(mail_domain='mail.ru')then 9 WHEN(mail_domain='me.com')then 10 WHEN(mail_domain='mac.com')then 11 WHEN(mail_domain='comcast.net')then 12 WHEN(mail_domain='cox.net')then 13 END as ord, sum(total_sent)total_sent, sum(total_delivered)total_delivered, sum(total_opened)total_opened, sum(total_bounced)total_bounced, sum(total_unsubscribed)total_unsubscribed, sum(total_complaint)total_complaint from red_global_ipr_daily where log_date > '$date_from 23:59:59' and  log_date < '$date_to 23:59:59' $clausePipeline $clauseUsers  group by mail_domain,vmta order by ord";					
			}
			
			$rsDailyIPR = $this->db->query($sqlIPR);
			
			// echo $this->db->last_query();
			
			if($rsDailyIPR->num_rows()>0){	
				$strTblBody='';
				$net_total_sent 		= 0;
				$net_total_delivered 	= 0;
				$net_total_opened 		= 0;
				$net_total_bounced 		= 0;
				$net_total_unsubscribed = 0;
				$net_total_complaint 	= 0;
				foreach($rsDailyIPR->result() as $row){
					$net_total_sent 		+= $row->total_sent;
					$net_total_delivered 	+= $row->total_delivered;
					$net_total_opened 		+= $row->total_opened;
					$net_total_bounced 		+= $row->total_bounced;
					$net_total_unsubscribed += $row->total_unsubscribed;
					$net_total_complaint 	+= $row->total_complaint;
					
					$strTblBody .= '<tr bgcolor="#ffffff">';
					$strTblBody .= ($clausePipeline !='')?'<td>'.$row->vmta.'</td>':'';
					$strTblBody .= ($clauseUsers !='')?'<td>'.$thisUser.'</td>':'';
					$strTblBody .= '<td>'.$row->mail_domain.'</td><td>'.$row->total_sent.'</td><td>'.$row->total_delivered.'('. number_format($row->total_delivered/$row->total_sent *100 , 2).'%)</td><td>'.$row->total_opened.'('. number_format($row->total_opened/$row->total_delivered *100 , 2).'%)</td><td>'.$row->total_bounced.'('. number_format($row->total_bounced/$row->total_delivered *100 , 2).'%)</td><td>'.$row->total_complaint.'('. number_format($row->total_complaint/$row->total_delivered *100 , 2).'%)</td><td>'.$row->total_unsubscribed.'('. number_format($row->total_unsubscribed/$row->total_delivered *100 , 2).'%)</td></tr>';									
				}
			}
			$rsDailyIPR->free_result();	
		$strTblFooter = '<tr>';
		$strTblFooter .= ($clausePipeline !='')?'<th>&nbsp;</th>':'';
		$strTblFooter .= ($clauseUsers !='')?'<th>&nbsp;</th>':'';
		
		$strTblFooter .='<th>Total</th><th>'.$net_total_sent.'</th><th>'.$net_total_delivered.'('.number_format($net_total_delivered/$net_total_sent *100 , 2).'%)</th><th>'.$net_total_opened.'('.number_format($net_total_opened/$net_total_delivered *100 , 2).'%)</th><th>'.$net_total_bounced.'('.number_format($net_total_bounced/$net_total_delivered *100 , 2).'%)</th><th>'.$net_total_complaint.'('.number_format($net_total_complaint/$net_total_delivered *100 , 2).'%)</th><th>'.$net_total_unsubscribed.'('.number_format($net_total_unsubscribed/$net_total_delivered *100 , 2).'%)</th></tr>';
		
		$strGlobalIPR = '<div style="padding:20px;">';		
		$strGlobalIPR .= '<table cellspacing="1" cellpadding="4" border="0" width="100%" bgcolor="#ebebeb"><tr>';
		$strGlobalIPR .= ($clausePipeline !='')?'<th>Pipeline</th>':'';		
		$strGlobalIPR .= ($clauseUsers !='')?'<th>RC Users</th>':'';		
		$strGlobalIPR .= '<th>Domain</th><th>Sent</th><th>Delivered</th><th>Opened</th><th>Bounced</th><th>Complaint</th><th>Unsubscribed</th>';
		 
		$strGlobalIPR .= $strTblBody . $strTblFooter.'</table></div>';
		
		if($typ=='')
		return $strGlobalIPR;
		else
		echo $strGlobalIPR;

	}	
	
	function paid_users2(){
//		$sqlPaidUsers ="select m.member_id,m.member_username, m.vmta, date_format(m.created_on,'%Y-%m-%d')created_on, m.is_authentic, date_format(m.authenticated_on,'%Y-%m-%d')authenticated_on, m.is_risky, m.apply_unresponsive_filter, m.unresponsive_release_count, m.member_dnm, m.member_unresponsive, mp.package_id, mp.amount, mp.start_payment_date, mp.next_payement_date, mp.user_quota_multiplier multiplier, mp.max_campaign_quota, mp.campaign_sent_counter, mp.coupon_code_used from red_members m inner join red_member_packages mp on m.member_id=mp.member_id where mp.package_id >0 and mp.next_payement_date > now() order by created_on";
		$sqlPaidUsers ="select mcd.all_total,mcd.gmail_total, mcd.yahoo_total, mcd.hotmail_total, mcd.aol_total, mcd.msn_total, m.member_id,m.member_username, m.vmta, date_format(m.created_on,'%Y-%m-%d')created_on, m.is_authentic, date_format(m.authenticated_on,'%Y-%m-%d')authenticated_on, m.is_risky, m.apply_unresponsive_filter, m.unresponsive_release_count, m.member_dnm, m.member_unresponsive, mp.package_id, mp.amount, mp.start_payment_date, mp.next_payement_date, mp.user_quota_multiplier multiplier, mp.max_campaign_quota, mp.campaign_sent_counter, mp.coupon_code_used from red_members m inner join red_member_packages mp on m.member_id=mp.member_id left join red_member_contact_detail mcd on m.member_id=mcd.member_id where mp.package_id >0 and mp.next_payement_date > now() order by created_on";
		$rsPaidUsers = $this->db->query($sqlPaidUsers);
		$strTable = "\n\n<table cellspacing='0' cellpadding='0' border='1' class='tbl_listing'>\n";
		$strTable .= "<tr><th>Sl.No.</th><th>UserID</th><th>User</th><th>Pipeline</th><th>Authentic</th><th>Risky</th><th>Unres.Filter</th><th>Multiplier</th><th>Quota</th><th>Sent</th><th>PackageDetail</th><th>AmountPaid</th><th>Coupon</th><th>1stPayDt</th><th>LastPayDt</th><th>NextPayDt</th><th>MemberDNM</th></tr>\n";
		$totalAmount = 0;
		$totalPaidUsers = $rsPaidUsers->num_rows();
		$slno=1;
		$arrMember = array();
		foreach($rsPaidUsers->result() as $row){
			$thisMid = $row->member_id; $thisMember = $row->member_username; $thisVMTA=$row->vmta; $thisCreatedOn = $row->created_on; $thisMemberDNM = $row->member_dnm; $thisMultiplier = $row->multiplier; $thisMemberCampaignQuota = $row->max_campaign_quota; $thisMemberSentCounter = $row->campaign_sent_counter; $thisPackage = $row->package_id; $thisAmount = $row->amount; $nextPayDt = $row->next_payement_date; $firstPayDt = $row->start_payment_date; $thisCoupon = $row->coupon_code_used;
			$thisAuthentic = ($row->is_authentic)?"Yes":"No";
			$thisRisky = ($row->is_risky)?"Yes":"No";
			$thisUnresponsiveFilter = ($row->apply_unresponsive_filter)?"Yes Release-count:".$row->unresponsive_release_count:"No";			
			$rsPackage = $this->db->query("select package_min_contacts, package_max_contacts from red_packages where package_id='$thisPackage'");
			$thisPackageMinContact = $rsPackage->row()->package_min_contacts;
			$thisPackageMaxContact = $rsPackage->row()->package_max_contacts;
			$rsPackage->free_result();
			
			$rsTransaction = $this->db->query("select date_format(transaction_date,'%Y-%m-%d')transaction_date, amount_paid from red_member_transactions where user_id='$thisMid' and gateway='AUTHORIZE' and status='SUCCESS' order by transaction_date desc limit 1");
			$transactionDt = $rsTransaction->row()->transaction_date;
			$transactionAmount = $rsTransaction->row()->amount_paid;
			$rsTransaction->free_result();
			$this->load->model('payment/payment_model');
			$thisAmount = floatval($this->payment_model->getDiscountedAmountForSubsequentPayments($thisMid,$thisAmount));
					
			$totalAmount += $thisAmount;
			$arrMember[$thisMid] =  array('Sl.No.'=>$slno,'UserID'=>$thisMid,'User'=>$thisMember,'Pipeline'=>$thisVMTA,'Authentic'=>$thisAuthentic,'Risky'=>$thisRisky,'Unres.Filter'=>$thisUnresponsiveFilter,'Multiplier'=>$thisMultiplier,'Quota'=>$thisMemberCampaignQuota,'Sent'=>$thisMemberSentCounter,'PackageDetail'=>"$thisPackage [$thisPackageMinContact - $thisPackageMaxContact]<br/>$$thisAmount",'AmountPaid'=>$transactionAmount,'Coupon'=>$thisCoupon,'1stPayDt'=>$firstPayDt,'LastPayDt'=>$transactionDt,'NextPayDt'=>$nextPayDt,'MemberDNM'=>$thisMemberDNM);
			//$strTable .= "<tr><td>$slno</td><td>$thisMid</td><td>$thisMember <br/>Created-on:$thisCreatedOn</td><td>$thisVMTA</td><td>$thisAuthentic</td><td>$thisRisky</td><td>$thisUnresponsiveFilter</td><td>$thisMultiplier</td><td>$thisMemberCampaignQuota</td><td>$thisMemberSentCounter</td><td>$thisPackage [$thisPackageMinContact - $thisPackageMaxContact]<br/>$$thisAmount</td><td>$$transactionAmount</td><td>$thisCoupon</td><td>$firstPayDt</td><td>$transactionDt</td><td>$nextPayDt</td><td style='word-wrap: break-word;overflow: hidden; max-width: 100px;'>$thisMemberDNM</td></tr>\n";
		$slno++;
		}
		$strTable .= "<tr><td colspan='3' align='right'><b>Total:</b></td><td>$$totalAmount</td></tr>\n</table>\n";
		$rsPaidUsers->free_result();
		echo $this->load->view('webmaster/header',array('title'=>'Campaign Segmentation','logo_link'=>'webmaster/dashboard_stat'),true);
		echo '<div style="padding:20px;"><b>Total paid users: </b>'.$totalPaidUsers.'<br/><b>Total projected revenue:</b> $'.$totalAmount.'<br/></div>';	
		echo $strTable ;
		
		
		$arrMid =  array_keys($arrMember);
		foreach($arrMid as $mid){
			$rsTotalContacts =$this->db->query("select count(subscriber_id) c from red_email_subscribers where subscriber_created_by='$mid' and subscriber_status=1 and is_deleted=0");
			$arrMember[$mid]['Total-contacts'] = $rsTotalContacts->row()->c;
			$rsTotalContacts->free_result();
		
		}
		echo"<pre>";
		print_r(($arrMember));	
		echo"</pre>";	
		
		echo $this->load->view('webmaster/footer','',true);
	}
	function paid_users(){
                                  $day = date('w'); // contains a number from 0 to 6 representing the day of the week (Sunday = 0, Monday = 1, etc.).
		$day_today = date("Y-m-d");
                                  $day_yesterday = date('Y-m-d', strtotime('-1 day', strtotime($day_today)));

//		$sqlPaidUsers ="select m.member_id,m.member_username, m.vmta, date_format(m.created_on,'%Y-%m-%d')created_on, m.is_authentic, date_format(m.authenticated_on,'%Y-%m-%d')authenticated_on, m.is_risky, m.apply_unresponsive_filter, m.unresponsive_release_count, m.member_dnm, m.member_unresponsive, mp.package_id, mp.amount, mp.start_payment_date, mp.next_payement_date, mp.user_quota_multiplier multiplier, mp.max_campaign_quota, mp.campaign_sent_counter, mp.coupon_code_used from red_members m inner join red_member_packages mp on m.member_id=mp.member_id where mp.package_id >0 and mp.next_payement_date > now() order by created_on";
		$sqlPaidUsers ="select mcd.all_total,mcd.gmail_total, mcd.yahoo_total, mcd.hotmail_total, mcd.aol_total, mcd.msn_total, m.member_id,m.member_username, m.ls_site_id, m.vmta, date_format(m.created_on,'%Y-%m-%d')created_on, m.is_authentic, date_format(m.authenticated_on,'%Y-%m-%d')authenticated_on, m.is_risky, m.apply_unresponsive_filter, m.unresponsive_release_count, m.stop_campaign_approval, m.show_sent_counter, m.is_pausable, m.apply_unauthentication_message, m.member_dnm, m.member_unresponsive, mp.package_id, mp.amount, mp.start_payment_date, mp.next_payement_date, mp.user_quota_multiplier multiplier, mp.max_campaign_quota, mp.campaign_sent_counter, mp.coupon_code_used from red_members m inner join red_member_packages mp on m.member_id=mp.member_id left join red_member_contact_detail mcd on m.member_id=mcd.member_id where mp.package_id >0 and mp.next_payement_date >  '" . $day_yesterday . "'  order by created_on";
             
		$rsPaidUsers = $this->db->query($sqlPaidUsers);
		$strTable = "\n\n<table cellspacing='0' cellpadding='0' border='1' class='tbl_listing'>\n";
		$strTable .= "<tr><th>UserID</th><th>User</th><th>Created On</th><th>Referrer</th><th>Pipeline</th><th>Authentic</th><th>Risky</th><th>Unres.Filter</th><th>Unres.Count</th>
					<th>StopApproval</th><th>ShowSentCounter</th><th>CampaignPausable</th><th>ListGrowingMail</th>
					<th>Multiplier</th><th>Quota</th><th>Sent</th><th>Package Title</th><th>Package Type</th><th>Package Price</th><th>Min Contacts</th><th>Max Contacts</th><th>AmountPaid</th><th>Coupon</th><th>1stPayDtEver</th><th>1stPayDtAtCurPkg</th>
					<th>LastPayDt</th><th>NextPayDt</th><th>MemberDNM</th>
					<th>Contacts</th><th>GMail</th><th>Yahoo</th><th>Hotmail</th><th>AOL</th><th>MSN</th></tr>\n";
		$totalAmount = 0;
		$totalPaidUsers = $rsPaidUsers->num_rows();
		$slno=1;
		foreach($rsPaidUsers->result() as $row){
			$thisMid = $row->member_id; $thisMember = $row->member_username; $thisVMTA=$row->vmta; $thisCreatedOn = $row->created_on;  $thisReferrer = $row->ls_site_id; $thisMemberDNM = $row->member_dnm; $thisMultiplier = $row->multiplier; $thisMemberCampaignQuota = $row->max_campaign_quota; $thisMemberSentCounter = $row->campaign_sent_counter; $thisPackage = $row->package_id; $thisAmount = $row->amount; $nextPayDt = $row->next_payement_date; $firstPayDt = $row->start_payment_date; $thisCoupon = $row->coupon_code_used;
			$thisAuthentic = ($row->is_authentic)?"Yes":"No";
			$thisRisky = ($row->is_risky)?"Yes":"No";
			$thisUnresponsiveFilter = ($row->apply_unresponsive_filter)?"Yes":"No";		
			$thisUnresponsiveCount =($row->apply_unresponsive_filter)? $row->unresponsive_release_count : 0;
			$thisStop_campaign_approval = ($row->stop_campaign_approval)?"Yes":"No";
			$thisShow_sent_counter = ($row->show_sent_counter)?"Yes":"No";
			$thisIs_pausable = ($row->is_pausable)?"Yes":"No";
			$thisApply_unauthentication_message = ($row->apply_unauthentication_message)?"Yes":"No";
			
			$rsPackage = $this->db->query("select package_title, package_price, package_recurring_interval,package_min_contacts, package_max_contacts from red_packages where package_id='$thisPackage'");
			$thisPackageMinContact = $rsPackage->row()->package_min_contacts;
			$thisPackageMaxContact = $rsPackage->row()->package_max_contacts;
                                                    $thisPackageTitle   = $rsPackage->row()->package_title;
                                                    $thisPackageRecurringType = $rsPackage->row()->package_recurring_interval;
                                                    $thisPackagePrice    = $rsPackage->row()->package_price;
			$rsPackage->free_result();
			
			 $rsFirstTransaction = $this->db->query("select date_format(transaction_date,'%Y-%m-%d')transaction_date, amount_paid from red_member_transactions where user_id='$thisMid' and gateway IN ('AUTHORIZE','PayPal') and status='SUCCESS' order by transaction_date asc limit 1");
			$rsTransaction = $this->db->query("select date_format(transaction_date,'%Y-%m-%d')transaction_date, amount_paid from red_member_transactions where user_id='$thisMid' and gateway IN ('AUTHORIZE','PayPal') and status='SUCCESS' order by transaction_date desc limit 1");
			$transactionDt = $rsTransaction->row()->transaction_date;
                                                   $transactionDtFirst = $rsFirstTransaction->row()->transaction_date;
                                                   $thisAmount  = $rsTransaction->row()->amount_paid;
			$this->load->model('payment/payment_model');
			//$thisAmount = floatval($this->payment_model->getDiscountedAmountForSubsequentPayments($thisMid,$thisAmount));
                                                    $rsTransaction->free_result();
                                                   $rsFirstTransaction->free_result();
			if(!is_null($row->all_total)){
				$all_total = $row->all_total ;
				$gmail_total = $row->gmail_total ;
				$gmail_percent = '('.round(($gmail_total /$all_total)*100, 2) .'%)';
				$yahoo_total = $row->yahoo_total ;
				$yahoo_percent = '('.round(($yahoo_total /$all_total)*100, 2) .'%)';
				$hotmail_total = $row->hotmail_total ;
				$hotmail_percent = '('.round(($hotmail_total /$all_total)*100, 2) .'%)';
				$aol_total = $row->aol_total ;
				$aol_percent = '('.round(($aol_total /$all_total)*100, 2) .'%)';
				$msn_total = $row->msn_total ;
				$msn_percent = '('.round(($msn_total /$all_total)*100, 2) .'%)';
			}else{
				$all_total = $gmail_total = $yahoo_total = $hotmail_total = $aol_total = $msn_total = 0 ;
				$gmail_percent = $yahoo_percent = $hotmail_percent = $aol_percent = $msn_percent = '' ;
			}
			$totalAmount += $thisAmount;
			$strTable .= "<tr><td>$thisMid</td><td>$thisMember</td><td>$thisCreatedOn</td><td>$thisReferrer</td><td>$thisVMTA</td>
						<td>$thisAuthentic</td><td>$thisRisky</td><td>$thisUnresponsiveFilter</td><td>$thisUnresponsiveCount</td>
						<td>$thisStop_campaign_approval</td><td>$thisShow_sent_counter</td><td>$thisIs_pausable</td><td>$thisApply_unauthentication_message</td>
						<td>$thisMultiplier</td><td>$thisMemberCampaignQuota</td><td>$thisMemberSentCounter</td>
						<td>$thisPackageTitle</td><td>$thisPackageRecurringType</td><td>$thisPackagePrice</td><td>$thisPackageMinContact</td><td> $thisPackageMaxContact</td><td>$thisAmount</td>
						<td>$thisCoupon</td><td>$transactionDtFirst</td><td>$firstPayDt</td><td>$transactionDt</td><td>$nextPayDt</td>
						<td style='word-wrap: break-word;overflow: hidden; max-width: 100px;'>$thisMemberDNM</td>
						<td>$all_total</td><td>$gmail_total{$gmail_percent}</td><td>$yahoo_total{$yahoo_percent}</td>
						<td>$hotmail_total{$hotmail_percent}</td><td>$aol_total{$aol_percent}</td><td>$msn_total{$msn_percent}</td></tr>\n";
		$slno++;
		}
		$strTable .= "<tr><td colspan='21' align='right'><b>Total:</b></td><td>$$totalAmount</td></tr>\n</table>\n";
		$rsPaidUsers->free_result();
		echo $this->load->view('webmaster/header',array('title'=>'Campaign Segmentation','logo_link'=>'webmaster/dashboard_stat'),true);
		echo '<div style="padding:20px;"><b>Total paid users: </b>'.$totalPaidUsers.'<br/><b>Total projected revenue:</b> $'.$totalAmount.'<br/><a href="'.base_url().'webmaster/report/paid_users_export" style="margin:10px; padding:5px; font-size:12px; color: blue; text-decoration:underline;" />Export to CSV</a></div>';
		echo $strTable ;
			
		echo $this->load->view('webmaster/footer','',true);
	}
                  function failed_users(){
                                  $day = date('w'); // contains a number from 0 to 6 representing the day of the week (Sunday = 0, Monday = 1, etc.).
		$week_start = date('Y-m-d', strtotime('-'.$day.' days')); //contains the date for Sunday
//		$sqlPaidUsers ="select m.member_id,m.member_username, m.vmta, date_format(m.created_on,'%Y-%m-%d')created_on, m.is_authentic, date_format(m.authenticated_on,'%Y-%m-%d')authenticated_on, m.is_risky, m.apply_unresponsive_filter, m.unresponsive_release_count, m.member_dnm, m.member_unresponsive, mp.package_id, mp.amount, mp.start_payment_date, mp.next_payement_date, mp.user_quota_multiplier multiplier, mp.max_campaign_quota, mp.campaign_sent_counter, mp.coupon_code_used from red_members m inner join red_member_packages mp on m.member_id=mp.member_id where mp.package_id >0 and mp.next_payement_date > now() order by created_on";
                                  $sqlcancelUsers ="select mcd.all_total,mcd.gmail_total, mcd.yahoo_total, mcd.hotmail_total, mcd.aol_total, mcd.msn_total, m.member_id,m.member_username, m.ls_site_id, m.vmta, date_format(m.created_on,'%Y-%m-%d')created_on, m.is_authentic, date_format(m.authenticated_on,'%Y-%m-%d')authenticated_on, m.is_risky, m.apply_unresponsive_filter, m.unresponsive_release_count, m.stop_campaign_approval, m.show_sent_counter, m.is_pausable, m.apply_unauthentication_message, m.member_dnm, m.member_unresponsive, mp.package_id, mp.amount, mp.start_payment_date, mp.next_payement_date, mp.user_quota_multiplier multiplier, mp.max_campaign_quota, mp.campaign_sent_counter, mp.coupon_code_used from red_members m inner join red_member_packages mp on m.member_id=mp.member_id left join red_member_contact_detail mcd on m.member_id=mcd.member_id where mp.next_payement_date < NOW() and mp.member_payment_declined_count> 0 and datediff(NOW(),mp.next_payement_date) < 60 order by created_on";
		$rsCancelUsers = $this->db->query($sqlcancelUsers);
		$strTable = "\n\n<table cellspacing='0' cellpadding='0' border='1' class='tbl_listing'>\n";
		$strTable .= "<tr><th>UserID</th><th>User</th><th>Created On</th><th>Referrer</th><th>Pipeline</th><th>Authentic</th><th>Risky</th><th>Unres.Filter</th><th>Unres.Count</th>
					<th>StopApproval</th><th>ShowSentCounter</th><th>CampaignPausable</th><th>ListGrowingMail</th>
					<th>Multiplier</th><th>Quota</th><th>Sent</th><th>Package Title</th><th>Package Type</th><th>Package Price</th><th>Min Contacts</th><th>Max Contacts</th><th>AmountPaid</th><th>Coupon</th><th>1stPayDtEver</th><th>1stPayDtAtCurPkg</th>
					<th>LastPayDt</th><th>NextPayDt</th><th>MemberDNM</th>
					<th>Contacts</th><th>GMail</th><th>Yahoo</th><th>Hotmail</th><th>AOL</th><th>MSN</th></tr>\n";
		$totalAmount = 0;
		$totalPaidUsers = $rsCancelUsers->num_rows();
		$slno=1;
		foreach($rsCancelUsers->result() as $row){
			$thisMid = $row->member_id; $thisMember = $row->member_username; $thisVMTA=$row->vmta; $thisCreatedOn = $row->created_on;  $thisReferrer = $row->ls_site_id; $thisMemberDNM = $row->member_dnm; $thisMultiplier = $row->multiplier; $thisMemberCampaignQuota = $row->max_campaign_quota; $thisMemberSentCounter = $row->campaign_sent_counter; $thisPackage = $row->package_id; $thisAmount = $row->amount; $nextPayDt = $row->next_payement_date; $firstPayDt = $row->start_payment_date; $thisCoupon = $row->coupon_code_used;
			$thisAuthentic = ($row->is_authentic)?"Yes":"No";
			$thisRisky = ($row->is_risky)?"Yes":"No";
			$thisUnresponsiveFilter = ($row->apply_unresponsive_filter)?"Yes":"No";		
			$thisUnresponsiveCount =($row->apply_unresponsive_filter)? $row->unresponsive_release_count : 0;
			$thisStop_campaign_approval = ($row->stop_campaign_approval)?"Yes":"No";
			$thisShow_sent_counter = ($row->show_sent_counter)?"Yes":"No";
			$thisIs_pausable = ($row->is_pausable)?"Yes":"No";
			$thisApply_unauthentication_message = ($row->apply_unauthentication_message)?"Yes":"No";
			
			$rsPackage = $this->db->query("select package_title, package_price, package_recurring_interval,package_min_contacts, package_max_contacts from red_packages where package_id='$thisPackage'");
			$thisPackageMinContact = $rsPackage->row()->package_min_contacts;
			$thisPackageMaxContact = $rsPackage->row()->package_max_contacts;
                                                    $thisPackageTitle   = $rsPackage->row()->package_title;
                                                    $thisPackageRecurringType = $rsPackage->row()->package_recurring_interval;
                                                    $thisPackagePrice    = $rsPackage->row()->package_price;
			$rsPackage->free_result();
			
			 $rsFirstTransaction = $this->db->query("select date_format(transaction_date,'%Y-%m-%d')transaction_date, amount_paid from red_member_transactions where user_id='$thisMid' and gateway IN ('AUTHORIZE','PayPal') and status='SUCCESS' order by transaction_date asc limit 1");
			$rsTransaction = $this->db->query("select date_format(transaction_date,'%Y-%m-%d')transaction_date, amount_paid from red_member_transactions where user_id='$thisMid' and gateway IN ('AUTHORIZE','PayPal') and status='SUCCESS' order by transaction_date desc limit 1");
			$transactionDt = $rsTransaction->row()->transaction_date;
                                                   $transactionDtFirst = $rsFirstTransaction->row()->transaction_date;
                                                   $thisAmount  = $rsTransaction->row()->amount_paid;
			$this->load->model('payment/payment_model');
			//$thisAmount = floatval($this->payment_model->getDiscountedAmountForSubsequentPayments($thisMid,$thisAmount));
                                                    $rsTransaction->free_result();
                                                   $rsFirstTransaction->free_result();
			if(!is_null($row->all_total)){
				$all_total = $row->all_total ;
				$gmail_total = $row->gmail_total ;
				$gmail_percent = '('.round(($gmail_total /$all_total)*100, 2) .'%)';
				$yahoo_total = $row->yahoo_total ;
				$yahoo_percent = '('.round(($yahoo_total /$all_total)*100, 2) .'%)';
				$hotmail_total = $row->hotmail_total ;
				$hotmail_percent = '('.round(($hotmail_total /$all_total)*100, 2) .'%)';
				$aol_total = $row->aol_total ;
				$aol_percent = '('.round(($aol_total /$all_total)*100, 2) .'%)';
				$msn_total = $row->msn_total ;
				$msn_percent = '('.round(($msn_total /$all_total)*100, 2) .'%)';
			}else{
				$all_total = $gmail_total = $yahoo_total = $hotmail_total = $aol_total = $msn_total = 0 ;
				$gmail_percent = $yahoo_percent = $hotmail_percent = $aol_percent = $msn_percent = '' ;
			}
			$totalAmount += $thisAmount;
			$strTable .= "<tr><td>$thisMid</td><td>$thisMember</td><td>$thisCreatedOn</td><td>$thisReferrer</td><td>$thisVMTA</td>
						<td>$thisAuthentic</td><td>$thisRisky</td><td>$thisUnresponsiveFilter</td><td>$thisUnresponsiveCount</td>
						<td>$thisStop_campaign_approval</td><td>$thisShow_sent_counter</td><td>$thisIs_pausable</td><td>$thisApply_unauthentication_message</td>
						<td>$thisMultiplier</td><td>$thisMemberCampaignQuota</td><td>$thisMemberSentCounter</td>
						<td>$thisPackageTitle</td><td>$thisPackageRecurringType</td><td>$thisPackagePrice</td><td>$thisPackageMinContact</td><td> $thisPackageMaxContact</td><td>$thisAmount</td>
						<td>$thisCoupon</td><td>$transactionDtFirst</td><td>$firstPayDt</td><td>$transactionDt</td><td>$nextPayDt</td>
						<td style='word-wrap: break-word;overflow: hidden; max-width: 100px;'>$thisMemberDNM</td>
						<td>$all_total</td><td>$gmail_total{$gmail_percent}</td><td>$yahoo_total{$yahoo_percent}</td>
						<td>$hotmail_total{$hotmail_percent}</td><td>$aol_total{$aol_percent}</td><td>$msn_total{$msn_percent}</td></tr>\n";
		$slno++;
		}
		$strTable .= "<tr><td colspan='21' align='right'><b>Total:</b></td><td>$$totalAmount</td></tr>\n</table>\n";
		$rsCancelUsers->free_result();
		echo $this->load->view('webmaster/header',array('title'=>'Campaign Segmentation','logo_link'=>'webmaster/dashboard_stat'),true);
		echo '<div style="padding:20px;"><b>Total Failed users past 60 days: </b>'.$totalPaidUsers.'<br/><b>Total revenue loss:</b> $'.$totalAmount.'<br/><a href="'.base_url().'webmaster/report/failed_users_export" style="margin:10px; padding:5px; font-size:12px; color: blue; text-decoration:underline;" />Export to CSV</a></div>';
		echo $strTable ;
			
		echo $this->load->view('webmaster/footer','',true);
	}
        
                 function failed_users_export($limit=0){
                                  $day = date('w'); // contains a number from 0 to 6 representing the day of the week (Sunday = 0, Monday = 1, etc.).
		$week_start = date('Y-m-d', strtotime('-'.$day.' days')); //contains the date for Sunday
		$sqlPaidUsers ="select mcd.all_total,mcd.gmail_total, mcd.yahoo_total, mcd.hotmail_total, mcd.aol_total, mcd.msn_total, m.member_id,m.member_username, m.ls_site_id, m.vmta, date_format(m.created_on,'%Y-%m-%d')created_on, m.is_authentic, date_format(m.authenticated_on,'%Y-%m-%d')authenticated_on, m.is_risky, m.apply_unresponsive_filter, m.unresponsive_release_count, m.stop_campaign_approval, m.show_sent_counter, m.is_pausable, m.apply_unauthentication_message, m.member_dnm, m.member_unresponsive, mp.package_id, mp.amount, mp.start_payment_date, mp.next_payement_date, mp.user_quota_multiplier multiplier, mp.max_campaign_quota, mp.campaign_sent_counter, mp.coupon_code_used from red_members m inner join red_member_packages mp on m.member_id=mp.member_id left join red_member_contact_detail mcd on m.member_id=mcd.member_id where mp.next_payement_date < NOW() and mp.member_payment_declined_count> 0 and datediff(NOW(),mp.next_payement_date) < 60 order by created_on";
		$rsPaidUsers = $this->db->query($sqlPaidUsers);
		
		$totalAmount = 0;
		$totalPaidUsers = $rsPaidUsers->num_rows();
		
		
		$csv_output = '"UserID","User","Created-on"," Referrer","Pipeline","Authentic","Risky","Unres.Filter","Unres.Counter","StopApproval","ShowSentCounter","CampaignPausable","ListGrowingMail"," Multiplier","Quota","Sent","Package Name","Package Type","Package Price","Min Contacts","Max Contacts","AmountPaid","Coupon","1stPayDtEver","1stPayDtAtCurPkg","LastPayDt","NextPayDt","MemberDNM","Contacts","GMail","Yahoo","Hotmail","AOL","MSN"';
		$csv_output .= "\n";
		$slno=1;
		foreach($rsPaidUsers->result() as $row){
			$thisMid = $row->member_id; $thisMember = $row->member_username; $thisVMTA=$row->vmta; $thisCreatedOn = $row->created_on;  $thisReferrer = $row->ls_site_id; $thisMemberDNM = $row->member_dnm; $thisMultiplier = $row->multiplier; $thisMemberCampaignQuota = $row->max_campaign_quota; $thisMemberSentCounter = $row->campaign_sent_counter; $thisPackage = $row->package_id; $thisAmount = $row->amount; $nextPayDt = $row->next_payement_date; $firstPayDt = $row->start_payment_date; $thisCoupon = $row->coupon_code_used;
			$thisAuthentic = ($row->is_authentic)?"Yes":"No";
			$thisRisky = ($row->is_risky)?"Yes":"No";
			$thisUnresponsiveFilter = ($row->apply_unresponsive_filter)?"Yes":"No";		
			$thisUnresponsiveCount =($row->apply_unresponsive_filter)? $row->unresponsive_release_count : 0;	

			$thisStop_campaign_approval = ($row->stop_campaign_approval)?"Yes":"No";
			$thisShow_sent_counter = ($row->show_sent_counter)?"Yes":"No";
			$thisIs_pausable = ($row->is_pausable)?"Yes":"No";
			$thisApply_unauthentication_message = ($row->apply_unauthentication_message)?"Yes":"No";
			
			$rsPackage = $this->db->query("select package_title, package_price, package_recurring_interval,package_min_contacts, package_max_contacts from red_packages where package_id='$thisPackage'");
			$thisPackageMinContact = $rsPackage->row()->package_min_contacts;
			$thisPackageMaxContact = $rsPackage->row()->package_max_contacts;
                                                    $thisPackageTitle   = $rsPackage->row()->package_title;
                                                    $thisPackageRecurringType = $rsPackage->row()->package_recurring_interval;
                                                    $thisPackagePrice    = $rsPackage->row()->package_price;
			$rsPackage->free_result();
			
                                                    $rsFirstTransaction = $this->db->query("select date_format(transaction_date,'%Y-%m-%d')transaction_date, amount_paid from red_member_transactions where user_id='$thisMid' and gateway IN ('AUTHORIZE','PayPal') and status='SUCCESS' order by transaction_date asc limit 1");
			$rsTransaction = $this->db->query("select date_format(transaction_date,'%Y-%m-%d')transaction_date, amount_paid from red_member_transactions where user_id='$thisMid' and gateway IN ('AUTHORIZE','PayPal') and status='SUCCESS' order by transaction_date desc limit 1");
			$transactionDt = $rsTransaction->row()->transaction_date;
                                                   $transactionDtFirst = $rsFirstTransaction->row()->transaction_date;
			$transactionAmount = $rsTransaction->row()->amount_paid;
			$rsTransaction->free_result();
                                                   $rsFirstTransaction->free_result();
			$this->load->model('payment/payment_model');
			//$thisAmount = floatval($this->payment_model->getDiscountedAmountForSubsequentPayments($thisMid,$thisAmount));
			if(!is_null($row->all_total)){
				$all_total = $row->all_total ;
				$gmail_total = $row->gmail_total ;
				$gmail_percent = '('.round(($gmail_total /$all_total)*100, 2) .'%)';
				$yahoo_total = $row->yahoo_total ;
				$yahoo_percent = '('.round(($yahoo_total /$all_total)*100, 2) .'%)';
				$hotmail_total = $row->hotmail_total ;
				$hotmail_percent = '('.round(($hotmail_total /$all_total)*100, 2) .'%)';
				$aol_total = $row->aol_total ;
				$aol_percent = '('.round(($aol_total /$all_total)*100, 2) .'%)';
				$msn_total = $row->msn_total ;
				$msn_percent = '('.round(($msn_total /$all_total)*100, 2) .'%)';
			}else{
				$all_total = $gmail_total = $yahoo_total = $hotmail_total = $aol_total = $msn_total = 0 ;
				$gmail_percent = $yahoo_percent = $hotmail_percent = $aol_percent = $msn_percent = '' ;
			}
			$totalAmount += $transactionAmount;
			$csv_output.= "\"$thisMid\",\"$thisMember\",\"$thisCreatedOn\",\"$thisReferrer\",\"$thisVMTA\",\" $thisAuthentic\",\"$thisRisky\",\"$thisUnresponsiveFilter\",\"$thisUnresponsiveCount\",\"$thisStop_campaign_approval\",\"$thisShow_sent_counter\",\"$thisIs_pausable\",\"$thisApply_unauthentication_message\",\" $thisMultiplier\",\"$thisMemberCampaignQuota\",\" $thisMemberSentCounter\",\"$thisPackageTitle\",\"$thisPackageRecurringType\",\"$thisPackagePrice\",\"$thisPackageMinContact\",\"$thisPackageMaxContact\",\"$transactionAmount\",\" $thisCoupon\",\"$transactionDtFirst\",\"$firstPayDt\",\"$transactionDt\",\"$nextPayDt\",\"$thisMemberDNM\",\"$all_total\",\"$gmail_total{$gmail_percent}\",\"$yahoo_total{$yahoo_percent}\",\" $hotmail_total{$hotmail_percent}\",\"$aol_total{$aol_percent}\",\"$msn_total{$msn_percent}\"\n";
		$slno++;
		} 
		 
		
		//Create filename and send output headers
		//header("Content-type: application/vnd.ms-excel");		
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");		
		header("Content-type: text/csv");
		header("Content-disposition: attachment; filename=failed_users_".date("Y-m-d_H-i",time()).".csv");	
		header("Expires: 0");		
		print $csv_output;
		exit;
	
	}
                 function canceled_users(){
                                  $day = date('w'); // contains a number from 0 to 6 representing the day of the week (Sunday = 0, Monday = 1, etc.).
		$week_start = date('Y-m-d', strtotime('-'.$day.' days')); //contains the date for Sunday
//		$sqlPaidUsers ="select m.member_id,m.member_username, m.vmta, date_format(m.created_on,'%Y-%m-%d')created_on, m.is_authentic, date_format(m.authenticated_on,'%Y-%m-%d')authenticated_on, m.is_risky, m.apply_unresponsive_filter, m.unresponsive_release_count, m.member_dnm, m.member_unresponsive, mp.package_id, mp.amount, mp.start_payment_date, mp.next_payement_date, mp.user_quota_multiplier multiplier, mp.max_campaign_quota, mp.campaign_sent_counter, mp.coupon_code_used from red_members m inner join red_member_packages mp on m.member_id=mp.member_id where mp.package_id >0 and mp.next_payement_date > now() order by created_on";
                                  $sqlcancelUsers ="select mcd.all_total,mcd.gmail_total, mcd.yahoo_total, mcd.hotmail_total, mcd.aol_total, mcd.msn_total, m.member_id,m.member_username, m.ls_site_id, m.vmta, date_format(m.created_on,'%Y-%m-%d')created_on, m.is_authentic, date_format(m.authenticated_on,'%Y-%m-%d')authenticated_on, m.is_risky, m.apply_unresponsive_filter, m.unresponsive_release_count, m.stop_campaign_approval, m.show_sent_counter, m.is_pausable, m.apply_unauthentication_message, m.member_dnm, m.member_unresponsive, mp.package_id, mp.amount, mp.start_payment_date, mp.next_payement_date, mp.user_quota_multiplier multiplier, mp.max_campaign_quota, mp.campaign_sent_counter, mp.coupon_code_used from red_members m inner join red_member_packages mp on m.member_id=mp.member_id left join red_member_contact_detail mcd on m.member_id=mcd.member_id where mp.package_id = -1 and DATEDIFF( mp.next_payement_date, NOW( ) ) > -30 AND DATEDIFF( mp.next_payement_date, NOW( ) ) <0 order by created_on";
		$rsCancelUsers = $this->db->query($sqlcancelUsers);
		$strTable = "\n\n<table cellspacing='0' cellpadding='0' border='1' class='tbl_listing'>\n";
		$strTable .= "<tr><th>UserID</th><th>User</th><th>Created On</th><th>Referrer</th><th>Pipeline</th><th>Authentic</th><th>Risky</th><th>Unres.Filter</th><th>Unres.Count</th>
					<th>StopApproval</th><th>ShowSentCounter</th><th>CampaignPausable</th><th>ListGrowingMail</th>
					<th>Multiplier</th><th>Quota</th><th>Sent</th><th>Package Title</th><th>Package Type</th><th>Package Price</th><th>Min Contacts</th><th>Max Contacts</th><th>AmountPaid</th><th>Coupon</th><th>1stPayDtEver</th><th>1stPayDtAtCurPkg</th>
					<th>LastPayDt</th><th>NextPayDt</th><th>MemberDNM</th>
					<th>Contacts</th><th>GMail</th><th>Yahoo</th><th>Hotmail</th><th>AOL</th><th>MSN</th></tr>\n";
		$totalAmount = 0;
		$totalPaidUsers = $rsCancelUsers->num_rows();
		$slno=1;
		foreach($rsCancelUsers->result() as $row){
			$thisMid = $row->member_id; $thisMember = $row->member_username; $thisVMTA=$row->vmta; $thisCreatedOn = $row->created_on;  $thisReferrer = $row->ls_site_id; $thisMemberDNM = $row->member_dnm; $thisMultiplier = $row->multiplier; $thisMemberCampaignQuota = $row->max_campaign_quota; $thisMemberSentCounter = $row->campaign_sent_counter; $thisPackage = $row->package_id; $thisAmount = $row->amount; $nextPayDt = $row->next_payement_date; $firstPayDt = $row->start_payment_date; $thisCoupon = $row->coupon_code_used;
			$thisAuthentic = ($row->is_authentic)?"Yes":"No";
			$thisRisky = ($row->is_risky)?"Yes":"No";
			$thisUnresponsiveFilter = ($row->apply_unresponsive_filter)?"Yes":"No";		
			$thisUnresponsiveCount =($row->apply_unresponsive_filter)? $row->unresponsive_release_count : 0;
			$thisStop_campaign_approval = ($row->stop_campaign_approval)?"Yes":"No";
			$thisShow_sent_counter = ($row->show_sent_counter)?"Yes":"No";
			$thisIs_pausable = ($row->is_pausable)?"Yes":"No";
			$thisApply_unauthentication_message = ($row->apply_unauthentication_message)?"Yes":"No";
			
			$rsPackage = $this->db->query("select package_title, package_price, package_recurring_interval,package_min_contacts, package_max_contacts from red_packages where package_id='$thisPackage'");
			$thisPackageMinContact = $rsPackage->row()->package_min_contacts;
			$thisPackageMaxContact = $rsPackage->row()->package_max_contacts;
                                                    $thisPackageTitle   = $rsPackage->row()->package_title;
                                                    $thisPackageRecurringType = $rsPackage->row()->package_recurring_interval;
                                                    $thisPackagePrice    = $rsPackage->row()->package_price;
			$rsPackage->free_result();
			
			 $rsFirstTransaction = $this->db->query("select date_format(transaction_date,'%Y-%m-%d')transaction_date, amount_paid from red_member_transactions where user_id='$thisMid' and gateway IN ('AUTHORIZE','PayPal') and status='SUCCESS' order by transaction_date asc limit 1");
			$rsTransaction = $this->db->query("select date_format(transaction_date,'%Y-%m-%d')transaction_date, amount_paid from red_member_transactions where user_id='$thisMid' and gateway IN ('AUTHORIZE','PayPal') and status='SUCCESS' order by transaction_date desc limit 1");
			$transactionDt = $rsTransaction->row()->transaction_date;
                                                   $transactionDtFirst = $rsFirstTransaction->row()->transaction_date;
                                                   $thisAmount  = $rsTransaction->row()->amount_paid;
			$this->load->model('payment/payment_model');
			//$thisAmount = floatval($this->payment_model->getDiscountedAmountForSubsequentPayments($thisMid,$thisAmount));
                                                    $rsTransaction->free_result();
                                                   $rsFirstTransaction->free_result();
			if(!is_null($row->all_total)){
				$all_total = $row->all_total ;
				$gmail_total = $row->gmail_total ;
				$gmail_percent = '('.round(($gmail_total /$all_total)*100, 2) .'%)';
				$yahoo_total = $row->yahoo_total ;
				$yahoo_percent = '('.round(($yahoo_total /$all_total)*100, 2) .'%)';
				$hotmail_total = $row->hotmail_total ;
				$hotmail_percent = '('.round(($hotmail_total /$all_total)*100, 2) .'%)';
				$aol_total = $row->aol_total ;
				$aol_percent = '('.round(($aol_total /$all_total)*100, 2) .'%)';
				$msn_total = $row->msn_total ;
				$msn_percent = '('.round(($msn_total /$all_total)*100, 2) .'%)';
			}else{
				$all_total = $gmail_total = $yahoo_total = $hotmail_total = $aol_total = $msn_total = 0 ;
				$gmail_percent = $yahoo_percent = $hotmail_percent = $aol_percent = $msn_percent = '' ;
			}
			$totalAmount += $thisAmount;
			$strTable .= "<tr><td>$thisMid</td><td>$thisMember</td><td>$thisCreatedOn</td><td>$thisReferrer</td><td>$thisVMTA</td>
						<td>$thisAuthentic</td><td>$thisRisky</td><td>$thisUnresponsiveFilter</td><td>$thisUnresponsiveCount</td>
						<td>$thisStop_campaign_approval</td><td>$thisShow_sent_counter</td><td>$thisIs_pausable</td><td>$thisApply_unauthentication_message</td>
						<td>$thisMultiplier</td><td>$thisMemberCampaignQuota</td><td>$thisMemberSentCounter</td>
						<td>$thisPackageTitle</td><td>$thisPackageRecurringType</td><td>$thisPackagePrice</td><td>$thisPackageMinContact</td><td> $thisPackageMaxContact</td><td>$thisAmount</td>
						<td>$thisCoupon</td><td>$transactionDtFirst</td><td>$firstPayDt</td><td>$transactionDt</td><td>$nextPayDt</td>
						<td style='word-wrap: break-word;overflow: hidden; max-width: 100px;'>$thisMemberDNM</td>
						<td>$all_total</td><td>$gmail_total{$gmail_percent}</td><td>$yahoo_total{$yahoo_percent}</td>
						<td>$hotmail_total{$hotmail_percent}</td><td>$aol_total{$aol_percent}</td><td>$msn_total{$msn_percent}</td></tr>\n";
		$slno++;
		}
		$strTable .= "<tr><td colspan='21' align='right'><b>Total:</b></td><td>$$totalAmount</td></tr>\n</table>\n";
		$rsCancelUsers->free_result();
		echo $this->load->view('webmaster/header',array('title'=>'Campaign Segmentation','logo_link'=>'webmaster/dashboard_stat'),true);
		echo '<div style="padding:20px;"><b>Total Cancelled users past 30 days: </b>'.$totalPaidUsers.'<br/><b>Total revenue loss:</b> $'.$totalAmount.'<br/><a href="'.base_url().'webmaster/report/canceled_users_export" style="margin:10px; padding:5px; font-size:12px; color: blue; text-decoration:underline;" />Export to CSV</a></div>';
		echo $strTable ;
			
		echo $this->load->view('webmaster/footer','',true);
	}
        
                 function canceled_users_export($limit=0){
                                  $day = date('w'); // contains a number from 0 to 6 representing the day of the week (Sunday = 0, Monday = 1, etc.).
		$week_start = date('Y-m-d', strtotime('-'.$day.' days')); //contains the date for Sunday
		$sqlPaidUsers ="select mcd.all_total,mcd.gmail_total, mcd.yahoo_total, mcd.hotmail_total, mcd.aol_total, mcd.msn_total, m.member_id,m.member_username, m.ls_site_id, m.vmta, date_format(m.created_on,'%Y-%m-%d')created_on, m.is_authentic, date_format(m.authenticated_on,'%Y-%m-%d')authenticated_on, m.is_risky, m.apply_unresponsive_filter, m.unresponsive_release_count, m.stop_campaign_approval, m.show_sent_counter, m.is_pausable, m.apply_unauthentication_message, m.member_dnm, m.member_unresponsive, mp.package_id, mp.amount, mp.start_payment_date, mp.next_payement_date, mp.user_quota_multiplier multiplier, mp.max_campaign_quota, mp.campaign_sent_counter, mp.coupon_code_used from red_members m inner join red_member_packages mp on m.member_id=mp.member_id left join red_member_contact_detail mcd on m.member_id=mcd.member_id where mp.package_id = -1 and  DATEDIFF( mp.next_payement_date, NOW( ) ) > -30 and DATEDIFF( mp.next_payement_date, NOW( ) ) < 0 order by created_on";
		$rsPaidUsers = $this->db->query($sqlPaidUsers);
		
		$totalAmount = 0;
		$totalPaidUsers = $rsPaidUsers->num_rows();
		
		
		$csv_output = '"UserID","User","Created-on"," Referrer","Pipeline","Authentic","Risky","Unres.Filter","Unres.Counter","StopApproval","ShowSentCounter","CampaignPausable","ListGrowingMail"," Multiplier","Quota","Sent","Package Name","Package Type","Package Price","Min Contacts","Max Contacts","AmountPaid","Coupon","1stPayDtEver","1stPayDtAtCurPkg","LastPayDt","NextPayDt","MemberDNM","Contacts","GMail","Yahoo","Hotmail","AOL","MSN"';
		$csv_output .= "\n";
		$slno=1;
		foreach($rsPaidUsers->result() as $row){
			$thisMid = $row->member_id; $thisMember = $row->member_username; $thisVMTA=$row->vmta; $thisCreatedOn = $row->created_on;  $thisReferrer = $row->ls_site_id; $thisMemberDNM = $row->member_dnm; $thisMultiplier = $row->multiplier; $thisMemberCampaignQuota = $row->max_campaign_quota; $thisMemberSentCounter = $row->campaign_sent_counter; $thisPackage = $row->package_id; $thisAmount = $row->amount; $nextPayDt = $row->next_payement_date; $firstPayDt = $row->start_payment_date; $thisCoupon = $row->coupon_code_used;
			$thisAuthentic = ($row->is_authentic)?"Yes":"No";
			$thisRisky = ($row->is_risky)?"Yes":"No";
			$thisUnresponsiveFilter = ($row->apply_unresponsive_filter)?"Yes":"No";		
			$thisUnresponsiveCount =($row->apply_unresponsive_filter)? $row->unresponsive_release_count : 0;	

			$thisStop_campaign_approval = ($row->stop_campaign_approval)?"Yes":"No";
			$thisShow_sent_counter = ($row->show_sent_counter)?"Yes":"No";
			$thisIs_pausable = ($row->is_pausable)?"Yes":"No";
			$thisApply_unauthentication_message = ($row->apply_unauthentication_message)?"Yes":"No";
			
			$rsPackage = $this->db->query("select package_title, package_price, package_recurring_interval,package_min_contacts, package_max_contacts from red_packages where package_id='$thisPackage'");
			$thisPackageMinContact = $rsPackage->row()->package_min_contacts;
			$thisPackageMaxContact = $rsPackage->row()->package_max_contacts;
                                                    $thisPackageTitle   = $rsPackage->row()->package_title;
                                                    $thisPackageRecurringType = $rsPackage->row()->package_recurring_interval;
                                                    $thisPackagePrice    = $rsPackage->row()->package_price;
			$rsPackage->free_result();
			
                                                    $rsFirstTransaction = $this->db->query("select date_format(transaction_date,'%Y-%m-%d')transaction_date, amount_paid from red_member_transactions where user_id='$thisMid' and gateway IN ('AUTHORIZE','PayPal') and status='SUCCESS' order by transaction_date asc limit 1");
			$rsTransaction = $this->db->query("select date_format(transaction_date,'%Y-%m-%d')transaction_date, amount_paid from red_member_transactions where user_id='$thisMid' and gateway IN ('AUTHORIZE','PayPal') and status='SUCCESS' order by transaction_date desc limit 1");
			$transactionDt = $rsTransaction->row()->transaction_date;
                                                   $transactionDtFirst = $rsFirstTransaction->row()->transaction_date;
			$transactionAmount = $rsTransaction->row()->amount_paid;
			$rsTransaction->free_result();
                                                   $rsFirstTransaction->free_result();
			$this->load->model('payment/payment_model');
			//$thisAmount = floatval($this->payment_model->getDiscountedAmountForSubsequentPayments($thisMid,$thisAmount));
			if(!is_null($row->all_total)){
				$all_total = $row->all_total ;
				$gmail_total = $row->gmail_total ;
				$gmail_percent = '('.round(($gmail_total /$all_total)*100, 2) .'%)';
				$yahoo_total = $row->yahoo_total ;
				$yahoo_percent = '('.round(($yahoo_total /$all_total)*100, 2) .'%)';
				$hotmail_total = $row->hotmail_total ;
				$hotmail_percent = '('.round(($hotmail_total /$all_total)*100, 2) .'%)';
				$aol_total = $row->aol_total ;
				$aol_percent = '('.round(($aol_total /$all_total)*100, 2) .'%)';
				$msn_total = $row->msn_total ;
				$msn_percent = '('.round(($msn_total /$all_total)*100, 2) .'%)';
			}else{
				$all_total = $gmail_total = $yahoo_total = $hotmail_total = $aol_total = $msn_total = 0 ;
				$gmail_percent = $yahoo_percent = $hotmail_percent = $aol_percent = $msn_percent = '' ;
			}
			$totalAmount += $transactionAmount;
			$csv_output.= "\"$thisMid\",\"$thisMember\",\"$thisCreatedOn\",\"$thisReferrer\",\"$thisVMTA\",\" $thisAuthentic\",\"$thisRisky\",\"$thisUnresponsiveFilter\",\"$thisUnresponsiveCount\",\"$thisStop_campaign_approval\",\"$thisShow_sent_counter\",\"$thisIs_pausable\",\"$thisApply_unauthentication_message\",\" $thisMultiplier\",\"$thisMemberCampaignQuota\",\" $thisMemberSentCounter\",\"$thisPackageTitle\",\"$thisPackageRecurringType\",\"$thisPackagePrice\",\"$thisPackageMinContact\",\"$thisPackageMaxContact\",\"$transactionAmount\",\" $thisCoupon\",\"$transactionDtFirst\",\"$firstPayDt\",\"$transactionDt\",\"$nextPayDt\",\"$thisMemberDNM\",\"$all_total\",\"$gmail_total{$gmail_percent}\",\"$yahoo_total{$yahoo_percent}\",\" $hotmail_total{$hotmail_percent}\",\"$aol_total{$aol_percent}\",\"$msn_total{$msn_percent}\"\n";
		$slno++;
		} 
		 
		
		//Create filename and send output headers
		//header("Content-type: application/vnd.ms-excel");		
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");		
		header("Content-type: text/csv");
		header("Content-disposition: attachment; filename=cancelled_users_".date("Y-m-d_H-i",time()).".csv");	
		header("Expires: 0");		
		print $csv_output;
		exit;
	
	}
	function paid_users_export($limit=0){
                                    $day = date('w'); // contains a number from 0 to 6 representing the day of the week (Sunday = 0, Monday = 1, etc.).
		$day_today = date("Y-m-d");
                                  $day_yesterday = date('Y-m-d', strtotime('-1 day', strtotime($day_today)));
		$week_start = date('Y-m-d', strtotime('-'.$day.' days')); //contains the date for Sunday
		$sqlPaidUsers ="select mcd.all_total,mcd.gmail_total, mcd.yahoo_total, mcd.hotmail_total, mcd.aol_total, mcd.msn_total, m.member_id,m.member_username, m.ls_site_id, m.vmta, date_format(m.created_on,'%Y-%m-%d')created_on, m.is_authentic, date_format(m.authenticated_on,'%Y-%m-%d')authenticated_on, m.is_risky, m.apply_unresponsive_filter, m.unresponsive_release_count, m.stop_campaign_approval, m.show_sent_counter, m.is_pausable, m.apply_unauthentication_message, m.member_dnm, m.member_unresponsive, mp.package_id, mp.amount, mp.start_payment_date, mp.next_payement_date, mp.user_quota_multiplier multiplier, mp.max_campaign_quota, mp.campaign_sent_counter, mp.coupon_code_used from red_members m inner join red_member_packages mp on m.member_id=mp.member_id left join red_member_contact_detail mcd on m.member_id=mcd.member_id where mp.package_id >0 and mp.next_payement_date >  '" . $day_yesterday . "'  order by created_on";
		$rsPaidUsers = $this->db->query($sqlPaidUsers);
		
		$totalAmount = 0;
		$totalPaidUsers = $rsPaidUsers->num_rows();
		
		
		$csv_output = '"UserID","User","Created-on"," Referrer","Pipeline","Authentic","Risky","Unres.Filter","Unres.Counter","StopApproval","ShowSentCounter","CampaignPausable","ListGrowingMail"," Multiplier","Quota","Sent","Package Name","Package Type","Package Price","Min Contacts","Max Contacts","AmountPaid","Coupon","1stPayDtEver","1stPayDtAtCurPkg","LastPayDt","NextPayDt","MemberDNM","Contacts","GMail","Yahoo","Hotmail","AOL","MSN"';
		$csv_output .= "\n";
		$slno=1;
		foreach($rsPaidUsers->result() as $row){
			$thisMid = $row->member_id; $thisMember = $row->member_username; $thisVMTA=$row->vmta; $thisCreatedOn = $row->created_on;  $thisReferrer = $row->ls_site_id; $thisMemberDNM = $row->member_dnm; $thisMultiplier = $row->multiplier; $thisMemberCampaignQuota = $row->max_campaign_quota; $thisMemberSentCounter = $row->campaign_sent_counter; $thisPackage = $row->package_id; $thisAmount = $row->amount; $nextPayDt = $row->next_payement_date; $firstPayDt = $row->start_payment_date; $thisCoupon = $row->coupon_code_used;
			$thisAuthentic = ($row->is_authentic)?"Yes":"No";
			$thisRisky = ($row->is_risky)?"Yes":"No";
			$thisUnresponsiveFilter = ($row->apply_unresponsive_filter)?"Yes":"No";		
			$thisUnresponsiveCount =($row->apply_unresponsive_filter)? $row->unresponsive_release_count : 0;	

			$thisStop_campaign_approval = ($row->stop_campaign_approval)?"Yes":"No";
			$thisShow_sent_counter = ($row->show_sent_counter)?"Yes":"No";
			$thisIs_pausable = ($row->is_pausable)?"Yes":"No";
			$thisApply_unauthentication_message = ($row->apply_unauthentication_message)?"Yes":"No";
			
			$rsPackage = $this->db->query("select package_title, package_price, package_recurring_interval,package_min_contacts, package_max_contacts from red_packages where package_id='$thisPackage'");
			$thisPackageMinContact = $rsPackage->row()->package_min_contacts;
			$thisPackageMaxContact = $rsPackage->row()->package_max_contacts;
                                                    $thisPackageTitle   = $rsPackage->row()->package_title;
                                                    $thisPackageRecurringType = $rsPackage->row()->package_recurring_interval;
                                                    $thisPackagePrice    = $rsPackage->row()->package_price;
			$rsPackage->free_result();
			
                                                    $rsFirstTransaction = $this->db->query("select date_format(transaction_date,'%Y-%m-%d')transaction_date, amount_paid from red_member_transactions where user_id='$thisMid' and gateway IN ('AUTHORIZE','PayPal') and status='SUCCESS' order by transaction_date asc limit 1");
			$rsTransaction = $this->db->query("select date_format(transaction_date,'%Y-%m-%d')transaction_date, amount_paid from red_member_transactions where user_id='$thisMid' and gateway IN ('AUTHORIZE','PayPal') and status='SUCCESS' order by transaction_date desc limit 1");
			$transactionDt = $rsTransaction->row()->transaction_date;
                                                   $transactionDtFirst = $rsFirstTransaction->row()->transaction_date;
			$transactionAmount = $rsTransaction->row()->amount_paid;
			$rsTransaction->free_result();
                                                   $rsFirstTransaction->free_result();
			$this->load->model('payment/payment_model');
			//$thisAmount = floatval($this->payment_model->getDiscountedAmountForSubsequentPayments($thisMid,$thisAmount));
			if(!is_null($row->all_total)){
				$all_total = $row->all_total ;
				$gmail_total = $row->gmail_total ;
				$gmail_percent = '('.round(($gmail_total /$all_total)*100, 2) .'%)';
				$yahoo_total = $row->yahoo_total ;
				$yahoo_percent = '('.round(($yahoo_total /$all_total)*100, 2) .'%)';
				$hotmail_total = $row->hotmail_total ;
				$hotmail_percent = '('.round(($hotmail_total /$all_total)*100, 2) .'%)';
				$aol_total = $row->aol_total ;
				$aol_percent = '('.round(($aol_total /$all_total)*100, 2) .'%)';
				$msn_total = $row->msn_total ;
				$msn_percent = '('.round(($msn_total /$all_total)*100, 2) .'%)';
			}else{
				$all_total = $gmail_total = $yahoo_total = $hotmail_total = $aol_total = $msn_total = 0 ;
				$gmail_percent = $yahoo_percent = $hotmail_percent = $aol_percent = $msn_percent = '' ;
			}
			$totalAmount += $transactionAmount;
			$csv_output.= "\"$thisMid\",\"$thisMember\",\"$thisCreatedOn\",\"$thisReferrer\",\"$thisVMTA\",\" $thisAuthentic\",\"$thisRisky\",\"$thisUnresponsiveFilter\",\"$thisUnresponsiveCount\",\"$thisStop_campaign_approval\",\"$thisShow_sent_counter\",\"$thisIs_pausable\",\"$thisApply_unauthentication_message\",\" $thisMultiplier\",\"$thisMemberCampaignQuota\",\" $thisMemberSentCounter\",\"$thisPackageTitle\",\"$thisPackageRecurringType\",\"$thisPackagePrice\",\"$thisPackageMinContact\",\"$thisPackageMaxContact\",\"$transactionAmount\",\" $thisCoupon\",\"$transactionDtFirst\",\"$firstPayDt\",\"$transactionDt\",\"$nextPayDt\",\"$thisMemberDNM\",\"$all_total\",\"$gmail_total{$gmail_percent}\",\"$yahoo_total{$yahoo_percent}\",\" $hotmail_total{$hotmail_percent}\",\"$aol_total{$aol_percent}\",\"$msn_total{$msn_percent}\"\n";
		$slno++;
		} 
		 
		
		//Create filename and send output headers
		//header("Content-type: application/vnd.ms-excel");		
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");		
		header("Content-type: text/csv");
		header("Content-disposition: attachment; filename=paid_users_".date("Y-m-d_H-i",time()).".csv");	
		header("Expires: 0");		
		print $csv_output;
		exit;
	
	}
	// last 15 days complaint counts against paid users
	function showPaidusersWithComplaintCount(){
		$rspaiduser = $this->db->query("select member_id from red_member_packages where package_id > 0 and next_payement_date > now()");
		$arrMembers = $rspaiduser->result_array();
		$totalUser = $rspaiduser->num_rows();
		$rspaiduser->free_result();
		echo "member_id, domain, unsub_count <br/>";
		for($i=0; $i < $totalUser; $i++ ){
			$mid = $arrMembers[$i]['member_id'];
			$rsSpamCount = $this->db->query("SELECT subscriber_email_domain, count(subscriber_id)c FROM `red_email_subscribers` where subscriber_created_by ='$mid' and subscriber_status= 0 and last_unsubscribed_date > '2016-02-03' and (subscriber_email_address like'%@gmail.com' or subscriber_email_address like'%@yahoo.com' or subscriber_email_address like'%@aol.com') group by subscriber_created_by,subscriber_email_domain");
			foreach($rsSpamCount->result() as $r){
				echo $mid.', '.$r->subscriber_email_domain . ', '. $r->c."<br/>";
				flush();ob_flush();
			}
			$rsSpamCount->free_result();
		}
					echo 'done';flush();ob_flush();
		//echo $arrMembers[0]['member_id'];
		//print_r($arrMembers);
	}
	function showCountersTemp2(){
	error_reporting(E_ALL);
		$arrUsers = array(3487,3352,4596,3588,3530,3243,4080,4331,3882,3439,3833,2719,5180,2595,1436,4353,3379,3515,4211,3252,5443,3302,3458,4350,2703,2316,3445,2467,3876,2282,5073,5109,3571,2060,4666,4193,3197,3870,4462,5267,3550,4566,3523,4345,5340,4166,3491,5377,3871,4919,2913,4806,1659,612,4138,3917,562,4319,2270,4483,4934,1523,3672,3382,5251,5204,5289);
		//$arrUsers = array(4486,5305,368,5025,4208,3788,4055,4118,5193);
		$arrDomains = array('gmail.com','yahoo.com','hotmail.com','aol.com','Total');
		$strTable = "<table cellspacing='0' cellpadding='0' border='1'>";
		$strTable .= "<tr><th>User-ID</th><th>Username</th><th>Domains</th><th>Delivered</th><th>Opens</th><th>Bounces</th><th>Complaints</th></tr>";
		foreach($arrUsers as $uid){
			$totDelivered = $totOpened = $tobounced = $toComplaints = 0;
			foreach($arrDomains as $d){
			$domainClause = ('Total' != $d)?" and subscriber_email_domain like'%$d%'" : '';
				$rsUser = $this->db->query("select member_username from red_members where member_id='$uid'");
				$username = $rsUser->row()->member_username;
				$rsUser->free_result();
			
				$rsStats = $this->db->query("select user_id, sum(email_delivered)delivered, sum(email_track_read)opens, sum(email_track_complaint)complaints from red_email_track where user_id=$uid and email_sent_date > '2015-04-01' $domainClause group by user_id");				 
				$delivered = $rsStats->row()->delivered;
				$opens = $rsStats->row()->opens;
				$complaints = $rsStats->row()->complaints;
				$rsStats->free_result();
				
				
				$rsbounce = $this->db->query("select count(email_track_bounce) as bounced from red_email_track where user_id=$uid and email_sent_date > '2015-04-01' and email_track_bounce > 0 $domainClause");
				$bounced = $rsbounce->row()->bounced;
				$rsbounce->free_result();
				
				$totDelivered += $delivered; $totOpened += $opens; $tobounced += $bounced; $toComplaints += $complaints;
					
				$strTable .= "<tr><td>$uid</td><td>$username</td><td>$d</td><td>$delivered</td><td>$opens</td><td>$bounced</td><td>$complaints</td></tr>";
			}
			//$strTable .= "<tr><td>$cid</td><td>$username</td><td>Total</td><td>$totDelivered</td><td>$totOpened</td><td>$tobounced</td><td>$toComplaints</td></tr>";
		}
		$strTable .= "</table>";
		echo $strTable;
	}
	function showCountersTemp(){
		//$arrCampaigns = array(62287,60962,62413,62545,61492,51016,61685,62498,62269,62484,62311,62553,62499,61940,62296,62489,62554,61984,62459,62621,62501,62595,62097,61659,60492,61607,40595,51794,62606,62332,56671,61671,56549,61835,61836,61065,61977,61469,62531,62483,58674,61403,62487,49667,62233,62229,62482,60067,61531,62492,62025,62181,30034,60240,62327,60887,55514,62464,62514,62341,62462,60632,54714,60480,59677,57972,62294);
		$arrCampaigns = array(62523,62393,62324,62436,61282,62475,61260,61021,59753);
		$arrDomains = array('gmail.com','yahoo.com','hotmail.com','aol.com','Total');
		$strTable = "<table cellspacing='0' cellpadding='0' border='1'>";
		$strTable .= "<tr><th>Campaign-ID</th><th>Username</th><th>Domains</th><th>Delivered</th><th>Opens</th><th>Bounces</th><th>Complaints</th></tr>";
		foreach($arrCampaigns as $cid){
			$totDelivered = $totOpened = $tobounced = $toComplaints = 0;
			foreach($arrDomains as $d){
			$domainClause = ('Total' != $d)?" and subscriber_email_domain like'%$d%'" : '';
				$rsStats = $this->db->query("select campaign_id, user_id, sum(email_delivered)delivered, sum(email_track_read)opens, sum(email_track_complaint)complaints from red_email_track where campaign_id=$cid $domainClause group by campaign_id, user_id");
				$uid = $rsStats->row()->user_id;
				$delivered = $rsStats->row()->delivered;
				$opens = $rsStats->row()->opens;
				$complaints = $rsStats->row()->complaints;
				$rsStats->free_result();
				
				$rsUser = $this->db->query("select member_username from red_members where member_id='$uid'");
				$username = $rsUser->row()->member_username;
				$rsUser->free_result();
				$rsbounce = $this->db->query("select count(email_track_bounce) as bounced from red_email_track where campaign_id=$cid and email_track_bounce > 0 $domainClause");
				$bounced = $rsbounce->row()->bounced;
				$rsbounce->free_result();
				
				$totDelivered += $delivered; $totOpened += $opens; $tobounced += $bounced; $toComplaints += $complaints;
					
				$strTable .= "<tr><td>$cid</td><td>$username</td><td>$d</td><td>$delivered</td><td>$opens</td><td>$bounced</td><td>$complaints</td></tr>";
			}
			//$strTable .= "<tr><td>$cid</td><td>$username</td><td>Total</td><td>$totDelivered</td><td>$totOpened</td><td>$tobounced</td><td>$toComplaints</td></tr>";
		}
		$strTable .= "</table>";
		echo $strTable;
	} 	
	function showCountersTemp1(){
		$arrCampaigns = array(62287,60962,62413,62545,61492,51016,61685,62498,62269,62484,62311,62553,62499,61940,62296,62489,62554,61984,62459,62621,62501,62595,62097,61659,60492,61607,40595,51794,62606,62332,56671,61671,56549,61835,61836,61065,61977,61469,62531,62483,58674,61403,62487,49667,62233,62229,62482,60067,61531,62492,62025,62181,30034,60240,62327,60887,55514,62464,62514,62341,62462,60632,54714,60480,59677,57972,62294);
		//$arrCampaigns = array(62523,62393,62324,62436,61282,62475,61260,61021,59753);
		$arrDomains = array('Total','gmail.com','yahoo.com','hotmail.com','aol.com');
		$strTable = "<table cellspacing='0' cellpadding='0' border='1'>";
		$strTable .= "<tr><th>Campaign-ID</th><th>Username</th><th>Total Delivered</th><th>Gmail Delivered</th><th>Yahoo Delivered</th><th>Hotmail Delivered</th><th>AOL Delivered</th><th>Total Opens</th><th>Total Bounces</th><th>Total Complaints</th></tr>";
		foreach($arrCampaigns as $cid){			
				$rsTotal = $this->db->query("select campaign_id, user_id, sum(email_delivered)delivered, sum(email_track_read)opens, sum(email_track_complaint)complaints from red_email_track where campaign_id=$cid group by campaign_id, user_id");
				$uid = $rsTotal->row()->user_id;
				$totalDelivered = $rsTotal->row()->delivered;
				$totalOpened = $rsTotal->row()->opens;
				$totalComplaints = $rsTotal->row()->complaints;
				$rsTotal->free_result();
				
				$rsbounce = $this->db->query("select count(email_track_bounce) as bounced from red_email_track where campaign_id=$cid and email_track_bounce > 0");
				$totalBounced = $rsbounce->row()->bounced;
				$rsbounce->free_result();
				
				$rsUser = $this->db->query("select member_username from red_members where member_id='$uid'");
				$username = $rsUser->row()->member_username;
				$rsUser->free_result();
				
				$rsGmail = $this->db->query("select campaign_id, sum(email_delivered)delivered from red_email_track where campaign_id=$cid and subscriber_email_domain like'%gmail%' group by campaign_id");				
				$gmailDelivered = $rsGmail->row()->delivered;				
				$rsGmail->free_result();			
				$rsYahoo = $this->db->query("select campaign_id, sum(email_delivered)delivered from red_email_track where campaign_id=$cid and subscriber_email_domain like'%yahoo%' group by campaign_id");				
				$yahooDelivered = $rsYahoo->row()->delivered;				
				$rsYahoo->free_result();			
							
				$rsHotmail = $this->db->query("select campaign_id, sum(email_delivered)delivered from red_email_track where campaign_id=$cid and subscriber_email_domain like'%hotmail%' group by campaign_id");				
				$hotmailDelivered = $rsHotmail->row()->delivered;				
				$rsHotmail->free_result();			
							
				$rsAOL = $this->db->query("select campaign_id, sum(email_delivered)delivered from red_email_track where campaign_id=$cid and subscriber_email_domain like'%aol%' group by campaign_id");				
				$aolDelivered = $rsAOL->row()->delivered;				
				$rsAOL->free_result();			
				
					
				$strTable .= "<tr><td>$cid</td><td>$username</td><td>$totalDelivered</td><td>$gmailDelivered</td><td>$yahooDelivered</td><td>$hotmailDelivered</td><td>$aolDelivered</td><td>$totalOpened</td><td>$totalBounced</td><td>$totalComplaints</td></tr>";			
		}
		$strTable .= "</table>";
		echo $strTable;
	} 	
	function showCountersTemp3(){
		//$arrUsers = array(562,   612,  1179,  1523,  1573,  1658,  1683,  1796,  2060,  2270,  2316);
		$arrUsers = array(3058,  3083,  3197, 3252,  3352,  3379,  3382,  3462,  3487,  3532,  3588,  3672,  3700,  3795,  3833,  3870);
		
		//$arrUsers = array(4080,  4088,  4146,  4205,  4217,  4320,  4331,  4345,  4353, 4362,  4386,  4408,  4459,  4462,  4466,  4483);
		//$arrUsers = array(4666,  4806,  4919,  4934,  5109,  5204,  5255,  5267,  5340,  5791,  5928,  6028,  6032,  6054,  6067,	6213);
		
		$arrDomains = array('Total','gmail.com','yahoo.com','hotmail.com','aol.com');
		$strTable = "<table cellspacing='0' cellpadding='0' border='1'>";
		$strTable .= "<tr><th>User-ID</th><th>Username</th><th>Total Delivered</th><th>Gmail Delivered</th><th>Yahoo Delivered</th><th>Hotmail Delivered</th><th>AOL Delivered</th><th>Total Opens</th><th>Total Bounces</th><th>Total Complaints</th></tr>";
		foreach($arrUsers as $uid){			
				$rsUser = $this->db->query("select member_username from red_members where member_id='$uid'");
				$username = $rsUser->row()->member_username;
				$rsUser->free_result();
				
				$rsTotal = $this->db->query("select user_id, sum(email_delivered)delivered, sum(email_track_read)opens, sum(email_track_complaint)complaints from red_email_track where user_id=$uid and email_sent_date > '2015-04-01' group by user_id");				
				$totalDelivered = $rsTotal->row()->delivered;
				$totalOpened = $rsTotal->row()->opens;
				$totalComplaints = $rsTotal->row()->complaints;
				$rsTotal->free_result();
				
				$rsbounce = $this->db->query("select count(email_track_bounce) as bounced from red_email_track where user_id=$uid  and email_sent_date > '2015-04-01' and email_track_bounce > 0");
				$totalBounced = $rsbounce->row()->bounced;
				$rsbounce->free_result();			
				
				$rsGmail = $this->db->query("select user_id, sum(email_delivered)delivered from red_email_track where user_id=$uid and subscriber_email_domain like'%gmail%' and email_sent_date > '2015-04-01' group by user_id");				
				$gmailDelivered = $rsGmail->row()->delivered;				
				$rsGmail->free_result();			
				$rsYahoo = $this->db->query("select user_id, sum(email_delivered)delivered from red_email_track where user_id=$uid and subscriber_email_domain like'%yahoo%' and email_sent_date > '2015-04-01' group by user_id");				
				$yahooDelivered = $rsYahoo->row()->delivered;				
				$rsYahoo->free_result();			
							
				$rsHotmail = $this->db->query("select user_id, sum(email_delivered)delivered from red_email_track where user_id=$uid and subscriber_email_domain like'%hotmail%' and email_sent_date > '2015-04-01' group by user_id");				
				$hotmailDelivered = $rsHotmail->row()->delivered;				
				$rsHotmail->free_result();			
							
				$rsAOL = $this->db->query("select user_id, sum(email_delivered)delivered from red_email_track where user_id=$uid and subscriber_email_domain like'%aol%' and email_sent_date > '2015-04-01' group by user_id");				
				$aolDelivered = $rsAOL->row()->delivered;				
				$rsAOL->free_result();			
				
					
				$strTable .= "<tr><td>$uid</td><td>$username</td><td>$totalDelivered</td><td>$gmailDelivered</td><td>$yahooDelivered</td><td>$hotmailDelivered</td><td>$aolDelivered</td><td>$totalOpened</td><td>$totalBounced</td><td>$totalComplaints</td></tr>";			
		}
		$strTable .= "</table>";
		echo $strTable;
	} 
	
	function leftUsers(){
		set_time_limit(0); 	
		//$arrUserLeftRC = array(4,   7,   8,  12,  14,  15,  16,  19,  20,  21,  22,  24,  25,  26,  27,  28,  29,  30,  31,  32,  34,  35,  36,  38,  39,  41,  42,  43,  44,  45,  46,  47,  48,  49,  51,  52,  53,  54,  55,  56,  57,  58,  59,  60,  66,  67,  68,  69,  70,  72,  75,  76,  77,  81,  83,  86,  88,  89,  93,  95,  96, 104, 109, 111, 112, 113, 114, 116, 117, 119, 123, 124, 125, 126, 127, 131, 132, 133, 134, 135, 136, 137, 139, 142, 143, 148, 152, 153, 154, 156, 159, 161, 162, 165, 168, 170, 171, 172, 173, 175, 176, 177, 179, 180, 181, 182, 183, 184, 185, 186, 187, 188, 189, 190, 191, 192, 194, 195, 197, 199, 200, 201, 202, 203, 204, 205, 207, 208, 209, 210, 211, 212, 213, 214, 215, 227, 229, 232, 233, 235, 236, 238, 241, 243, 244, 246, 247, 248, 250, 251, 252, 255, 256, 257, 258, 260, 262, 264, 266, 267, 276, 278, 279, 281, 282, 283, 287, 289, 291, 294, 295, 296, 297, 298, 299, 300, 301, 304, 305, 306, 307, 311, 312, 314, 315, 316, 318, 320, 322, 323, 325, 326, 328, 329, 331, 332, 333, 334, 335, 337, 342, 343, 347, 348, 349, 352, 353, 355, 357, 360, 362, 363, 367, 369, 370, 371, 372, 373, 374, 375, 376, 377, 378, 379, 380, 381, 382, 385, 387, 389, 390, 395, 396, 398, 399, 400, 403, 404, 406, 407, 409, 412, 414, 416, 418, 419, 420, 427, 434, 437, 439, 440, 441, 442, 444, 447, 452, 458, 459, 460, 461, 462, 464, 465, 466, 468, 470, 472, 474, 479, 480, 482, 483, 487, 489, 492, 498, 499, 502, 506, 508, 509, 510, 511, 512, 514, 515, 516, 517, 518, 519, 520, 522, 523, 524, 525, 526, 529, 531, 535, 536, 540, 543, 545, 546, 550, 554, 557, 559, 560, 561, 568, 569, 570, 571, 572, 573, 577, 578, 579, 581, 583, 584, 586, 587, 588, 590, 592, 598, 602, 603, 604, 605, 607, 609, 610, 613, 614, 616, 617, 618, 621, 622, 623, 624, 628, 631, 632, 634, 637, 639, 640, 641, 642, 643, 646, 647, 648, 650, 651, 654, 655, 656, 657, 659, 668, 670, 671, 672, 673, 674, 676, 682, 685, 686, 687, 688, 689, 693, 695, 696, 697, 699, 700, 701, 702, 705, 706, 707, 709, 714, 715, 716, 717, 718, 719, 720, 722, 725, 726, 727, 728, 729, 731, 734, 736, 737, 738, 740, 741, 743, 744, 747, 751, 753, 755, 758, 759, 760, 761, 766, 767, 770, 778, 779, 780, 781, 782, 785, 787, 788, 790, 791, 796, 798, 802, 803, 805, 807, 808, 809, 810, 811, 819, 820, 821, 822, 823, 824, 834, 835, 836, 837, 840, 843, 845, 846, 847, 850, 851, 852, 854, 858, 859, 860, 861, 862, 865, 866, 869, 871, 872, 873, 874, 880, 882, 883, 885, 887, 889, 890, 891, 893, 896, 897, 898, 899, 900, 901, 902, 903, 904, 905, 906, 908, 911, 914, 917, 919, 920, 923, 925, 927, 931, 933, 934, 936, 943, 944, 945, 952, 953, 955, 956, 958, 959, 961, 962, 963, 964, 965, 966, 967, 968, 969, 970, 972, 974, 976, 977, 978, 980, 981, 982, 983, 984, 985, 986, 987, 988, 990, 993, 994, 997, 998,1001,1004,1007,1008,1009,1010,1013,1014,1016,1018,1019,1020,1021,1022,1023,1025,1026,1027,1030,1031,1032,1033,1035,1038,1039,1040,1044,1045,1046,1048,1049,1050,1051,1052,1054,1056,1057,1058,1061,1062,1064,1065,1066,1069,1070,1071,1072,1074,1075,1076,1078,1080,1081,1082,1083,1084,1087,1088,1090,1091,1094,1095,1096,1097,1100,1101,1102,1103,1105,1108,1109,1110,1111,1112,1113,1114,1115,1116,1117,1118,1119,1124,1125,1126,1127,1129,1130,1131,1134,1136,1137,1138,1139,1141,1142,1143,1145,1147,1148,1151,1154,1156,1157,1158,1159,1160,1161,1162,1164,1165,1167,1168,1170,1172,1174,1175,1176,1177,1178,1180,1183,1184,1186,1188,1199,1201,1202,1203,1204,1206,1207,1208,1209,1211,1212,1213,1216,1217,1219,1220,1221,1223,1225,1226,1227,1228,1233,1238,1243,1246,1249,1252,1254,1256,1258,1259,1260,1261,1262,1264,1265,1267,1268,1270,1272,1275,1276,1277,1280,1281,1282,1283,1284,1286,1287,1291,1292,1293,1294,1295,1296,1298,1300,1303,1307,1309,1310,1311,1313,1314,1315,1316,1317,1319,1320,1321,1324,1326,1328,1330,1331,1332,1335,1336,1339,1340,1342,1343,1344,1346,1347,1348,1349,1350,1351,1352,1353,1358,1359,1364,1365,1366,1368,1370,1371,1372,1375,1376,1378,1382,1383,1387,1388,1389,1390,1391,1394,1396,1397,1398,1399,1402,1405,1408,1409,1410,1414,1415,1416,1417,1419,1420,1421,1423,1425,1427,1428,1429,1431,1432,1434,1437,1438,1439,1440,1441,1443,1444,1445,1446,1448,1451,1452,1453,1454,1455,1458,1460,1462,1463,1464,1465,1466,1467,1468,1469,1471,1472,1473,1474,1475,1476,1477,1478,1480,1482,1483,1484,1486,1487,1488,1490,1492,1493,1494,1495,1497,1498,1500,1502,1504,1507,1508,1509,1512,1515,1525,1526,1527,1529,1530,1532,1533,1534,1535,1537,1539,1540,1543,1544,1545,1549,1551,1552,1557,1558,1561,1562,1563,1564,1565,1567,1572,1573,1574,1576,1579,1580,1585,1586,1591,1593,1594,1597,1598,1599,1600,1601,1602,1603,1604,1605,1606,1607,1610,1611,1612,1613,1614,1616,1617,1618,1619,1624,1625,1629,1630,1635,1636,1637,1638,1639,1641,1642,1643,1644,1645,1646,1647,1648,1651,1652,1654,1655,1656,1660,1661,1662,1663,1666,1667,1668,1671,1676,1678,1679,1680,1684,1689,1691,1692,1694,1695,1696,1698,1699,1702,1703,1704,1706,1709,1710,1713,1714,1715,1716,1719,1720,1721,1725,1728,1729,1730,1732,1734,1735,1736,1737,1742,1743,1744,1745,1746,1747,1748,1749,1751,1753,1755,1763,1767,1769,1770,1771,1772,1773,1774,1775,1778,1779,1780,1781,1783,1784,1785,1787,1791,1792,1794,1797,1798,1802,1804,1808,1811,1812,1813,1815,1816,1817,1818,1819,1820,1821,1823,1828,1830,1831,1832,1835,1836,1838,1839,1842,1843,1845,1846,1847,1848,1851,1852,1853,1855,1856,1860,1863,1864,1865,1870,1871,1874,1875,1877,1880,1881,1882,1883,1884,1885,1886,1888,1891,1892,1896,1897,1899,1903,1905,1906,1907,1908,1910,1911,1913,1916,1918,1919,1921,1923,1924,1927,1929,1931,1932,1933,1934,1935,1938,1940,1942,1944,1945,1946,1947,1948,1949,1950,1951,1954,1956,1958,1960,1961,1962,1963,1965,1967,1970,1971,1972,1973,1974,1975,1976,1983,1985,1986,1987,1990,1991,1992,1994,1995,1998,1999,2001,2009,2010,2013,2014,2017,2020,2023,2025,2027,2028,2030,2031,2034,2043,2044,2049,2052,2055,2057,2058,2059,2061,2062,2063,2065,2066,2067,2071,2072,2073,2078,2079,2081,2084,2087,2088,2092,2093,2096,2097,2098,2100,2102,2103,2105,2107,2110,2118,2119,2121,2122,2132,2133,2136,2137,2138,2139,2140,2142,2143,2145,2146,2147,2151,2152,2153,2154,2155,2158,2159,2161,2163,2164,2165,2167,2170,2171,2172,2173,2174,2175,2176,2178,2179,2186,2188,2189,2191,2193,2194,2197,2199,2200,2201,2205,2208,2209,2210,2212,2215,2216,2217,2218,2219,2220,2221,2222,2224,2225,2226,2227,2228,2230,2231,2233,2234,2235,2236,2237,2238,2239,2240,2242,2244,2246,2247,2254,2255,2256,2258,2260,2262,2263,2265,2267,2269,2272,2275,2278,2281,2283,2284,2285,2286,2289,2290,2291,2296,2297,2300,2301,2302,2304,2305,2309,2312,2317,2319,2320,2322,2323,2326,2327,2329,2332,2333,2334,2335,2336,2337,2338,2339,2340,2341,2342,2343,2344,2345,2346,2348,2349,2350,2351,2352,2353,2354,2355,2356,2357,2358,2359,2360,2361,2362,2363,2364,2365,2366,2367,2368,2370,2371,2373,2374,2376,2380,2382,2383,2384,2385,2387,2390,2392,2394,2396,2397,2398,2399,2401,2402,2404,2406,2408,2409,2411,2412,2413,2414,2417,2418,2419,2420,2426,2428,2429,2430,2432,2433,2436,2437,2439,2440,2442,2443,2446,2447,2448,2449,2451,2453,2455,2456,2458,2460,2463,2465,2469,2471,2474,2475,2477,2478,2481,2482,2483,2485,2486,2487,2488,2490,2494,2496,2498,2499,2502,2505,2506,2507,2512,2514,2515,2519,2520,2521,2524,2525,2527,2528,2529,2534,2535,2536,2537,2538,2539,2544,2545,2546,2547,2549,2552,2554,2557,2558,2560,2561,2562,2563,2564,2565,2569,2572,2573,2574,2575,2576,2577,2579,2582,2584,2587,2588,2602,2603,2605,2607,2608,2610,2613,2615,2617,2619,2620,2621,2622,2625,2628,2629,2632,2633,2635,2636,2637,2641,2643,2644,2646,2647,2649,2650,2651,2656,2657,2661,2664,2665,2667,2670,2678,2679,2682,2684,2685,2690,2693,2696,2698,2699,2700,2702,2704,2706,2708,2710,2712,2713,2715,2716,2717,2720,2722,2723,2724,2728,2731,2732,2735,2736,2737,2739,2740,2741,2743,2744,2746,2748,2749,2751,2753,2754,2765,2766,2767,2768,2769,2774,2776,2780,2781,2782,2784,2785,2786,2788,2789,2790,2792,2793,2794,2796,2799,2800,2801,2802,2803,2804,2805,2809,2811,2812,2816,2822,2823,2825,2828,2829,2830,2832,2833,2834,2837,2838,2839,2847,2850,2851,2852,2854,2855,2856,2857,2858,2863,2864,2865,2866,2868,2869,2870,2873,2876,2877,2880,2885,2886,2890,2893,2894,2895,2896,2898,2899,2900,2901,2902,2904,2905,2908,2909,2910,2912,2920,2923,2925,2927,2930,2934,2936,2937,2939,2940,2941,2944,2945,2946,2947,2948,2949,2950,2951,2953,2956,2957,2959,2962,2965,2966,2968,2969,2974,2975,2976,2978,2981,2982,2983,2984,2989,2999,3000,3002,3003,3007,3009,3010,3011,3012,3014,3016,3018,3019,3024,3025,3026,3027,3030,3031,3042,3048,3052,3056,3057,3059,3060,3061,3065,3066,3067,3069,3070,3071,3075,3076,3077,3080,3081,3082,3086,3088,3090,3092,3096,3098,3099,3100,3102,3107,3108,3109,3110,3111,3114,3115,3116,3121,3122,3123,3125,3127,3129,3131,3132,3136,3137,3138,3139,3141,3144,3146,3147,3152,3153,3154,3155,3156,3157,3158,3159,3162,3164,3165,3166,3167,3169,3170,3172,3173,3174,3175,3176,3178,3180,3183,3184,3187,3189,3190,3201,3202,3204,3207,3208,3210,3214,3217,3218,3222,3223,3224,3226,3227,3229,3230,3231,3232,3233,3236,3238,3239,3240,3241,3242,3244,3248,3249,3250,3251,3253,3254,3256,3257,3260,3261,3262,3264,3265,3266,3267,3268,3271,3273,3275,3276,3277,3281,3283,3285,3289,3290,3291,3293,3294,3295,3297,3300,3303,3304,3305,3306,3307,3308,3309,3310,3312,3313,3314,3315,3316,3317,3320,3321,3322,3326,3328,3329,3330,3334,3335,3339,3340,3342,3343,3344,3345,3346,3347,3348,3349,3350,3351,3353,3354,3355,3356,3357,3358,3360,3363,3365,3367,3368,3369,3370,3371,3373,3374,3375,3376,3377,3378,3380,3383,3387,3389,3390,3391,3392,3393,3394,3395,3398,3399,3401,3402,3403,3404,3405,3407,3408,3409,3413,3415,3416,3418,3419,3420,3421,3422,3423,3424,3425,3427,3429,3430,3431,3432,3433,3435,3436,3438,3441,3442,3443,3444,3447,3448,3449,3450,3452,3453,3454,3455,3456,3457,3459,3461,3464,3465,3466,3469,3470,3471,3472,3473,3474,3475,3477,3478,3479,3481,3482,3483,3484,3485,3488,3493,3494,3495,3496,3497,3498,3499,3500,3501,3502,3503,3504,3505,3507,3508,3511,3512,3513,3514,3516,3518,3520,3521,3522,3524,3525,3526,3528,3532,3533,3534,3535,3536,3538,3539,3541,3542,3543,3544,3546,3547,3548,3549,3551,3554,3555,3556,3557,3559,3561,3562,3563,3564,3565,3568,3570,3572,3573,3574,3575,3576,3579,3580,3581,3582,3583,3584,3585,3586,3587,3589,3590,3591,3595,3596,3597,3598,3599,3600,3601,3602,3603,3604,3605,3607,3608,3609,3610,3612,3613,3616,3617,3618,3619,3620,3621,3623,3624,3625,3626,3627,3628,3632,3633,3635,3636,3637,3638,3639,3641,3642,3643,3645,3646,3647,3648,3649,3650,3651,3652,3654,3657,3658,3660,3662,3663,3665,3666,3667,3668,3669,3670,3673,3674,3675,3677,3678,3679,3680,3683,3684,3686,3688,3690,3693,3694,3695,3697,3698,3699,3701,3702,3703,3704,3705,3706,3707,3708,3709,3710,3713,3714,3717,3718,3720,3725,3726,3727,3730,3731,3732,3733,3734,3735,3736,3741,3743,3744,3745,3746,3748,3750,3751,3752,3754,3756,3758,3759,3761,3762,3764,3765,3766,3770,3771,3772,3773,3774,3776,3777,3778,3779,3780,3781,3782,3783,3784,3785,3786,3787,3789,3790,3791,3792,3793,3794,3797,3798,3799,3800,3801,3802,3803,3804,3805,3806,3807,3810,3812,3813,3814,3815,3816,3817,3822,3823,3824,3825,3826,3827,3828,3829,3830,3831,3832,3834,3835,3837,3839,3840,3841,3842,3844,3845,3846,3848,3849,3850,3851,3853,3854,3855,3857,3858,3859,3860,3861,3862,3863,3865,3866,3867,3868,3869,3872,3873,3874,3875,3877,3878,3879,3880,3881,3884,3886,3887,3889,3892,3893,3894,3895,3896,3897,3898,3899,3900,3901,3902,3903,3904,3905,3906,3907,3908,3909,3910,3911,3912,3913,3914,3915,3916,3918,3919,3920,3921,3922,3923,3924,3925,3926,3927,3928,3929,3931,3933,3934,3935,3937,3940,3941,3942,3943,3944,3945,3946,3947,3948,3949,3951,3954,3955,3956,3957,3958,3959,3960,3961,3962,3964,3965,3966,3968,3969,3970,3971,3972,3973,3974,3975,3977,3978,3980,3981,3983,3985,3986,3988,3989,3991,3992,3994,3995,3996,3997,3999,4000,4001,4002,4003,4004,4005,4008,4010,4011,4012,4013,4014,4017,4018,4020,4023,4025,4026,4030,4031,4033,4034,4035,4036,4038,4039,4040,4041,4042,4043,4046,4047,4048,4049,4050,4051,4052,4053,4054,4056,4057,4058,4059,4060,4061,4062,4063,4066,4067,4068,4070,4071,4073,4074,4075,4076,4077,4078,4081,4083,4084,4085,4087,4089,4090,4091,4092,4093,4094,4095,4096,4097,4098,4100,4101,4103,4105,4107,4108,4109,4113,4114,4116,4117,4119,4120,4121,4122,4123,4124,4125,4127,4128,4133,4135,4140,4141,4143,4145,4147,4148,4149,4151,4153,4156,4157,4158,4160,4161,4162,4163,4165,4168,4169,4171,4172,4173,4174,4175,4177,4178,4179,4181,4182,4184,4185,4188,4189,4190,4191,4192,4195,4196,4197,4198,4200,4201,4202,4203,4210,4212,4213,4214,4216,4218,4220,4221,4222,4224,4227,4230,4231,4232,4233,4235,4236,4237,4238,4239,4240,4243,4245,4246,4247,4249,4253,4255,4257,4258,4261,4262,4263,4264,4265,4266,4267,4268,4270,4272,4273,4275,4276,4277,4278,4280,4281,4282,4284,4285,4286,4287,4288,4289,4290,4291,4292,4293,4294,4295,4296,4297,4298,4299,4300,4304,4306,4307,4308,4309,4310,4311,4312,4313,4315,4316,4318,4321,4324,4325,4327,4330,4333,4334,4335,4336,4337,4339,4340,4341,4343,4346,4347,4348,4349,4351,4352,4354,4355,4357,4358,4360,4365,4366,4367,4370,4372,4373,4377,4379,4383,4384,4385,4387,4388,4389,4390,4391,4393,4396,4397,4399,4400,4401,4402,4403,4405,4406,4407,4409,4410,4411,4412,4413,4414,4416,4419,4420,4421,4422,4423,4426,4427,4428,4430,4432,4433,4434,4436,4437,4438,4439,4440,4441,4442,4443,4444,4445,4446,4447,4449,4450,4452,4453,4454,4455,4456,4457,4458,4460,4461,4463,4464,4465,4467,4468,4469,4471,4472,4473,4474,4475,4476,4477,4478,4479,4480,4481,4482,4484,4485,4488,4489,4491,4492,4494,4495,4496,4497,4498,4499,4500,4501,4502,4503,4504,4506,4508,4509,4510,4511,4512,4513,4514,4517,4518,4520,4521,4523,4524,4525,4527,4528,4529,4531,4534,4536,4538,4541,4542,4543,4544,4545,4550,4551,4552,4553,4554,4557,4559,4561,4562,4563,4564,4569,4571,4573,4576,4577,4578,4579,4580,4581,4582,4584,4585,4586,4587,4588,4590,4591,4592,4593,4594,4595,4597,4598,4599,4604,4605,4606,4607,4609,4611,4613,4614,4615,4616,4618,4621,4622,4623,4626,4627,4628,4629,4630,4631,4632,4633,4637,4640,4641,4642,4643,4644,4645,4646,4648,4650,4651,4652,4656,4657,4661,4663,4664,4667,4668,4669,4670,4671,4672,4674,4677,4678,4679,4680,4681,4682,4683,4684,4687,4690,4691,4692,4693,4695,4696,4697,4699,4700,4701,4702,4703,4706,4708,4709,4710,4711,4712,4713,4715,4716,4717,4718,4719,4720,4721,4723,4725,4728,4729,4730,4731,4732,4733,4735,4736,4740,4741,4742,4743,4744,4745,4746,4748,4752,4755,4756,4758,4759,4760,4761,4762,4763,4765,4766,4767,4768);
		//$arrUserLeftRC = array(2, 13,  206,  221,  645,  912,  937, 1017, 1089, 1120, 1215, 1306, 1435, 1873, 2104, 2251, 2292, 2293, 2423, 2425, 2462, 2681, 2892, 2942, 2964, 3034, 3058, 3087, 3269, 3280, 3341, 3397, 3426, 3428, 3446, 3451, 3480, 3515, 3530, 3540, 3567, 3571, 3578, 3634, 3676, 3692, 3696, 3715, 3721, 3724, 3739, 3747, 3753, 3757, 3763, 3811, 3838, 3843, 3852, 3950, 3976, 3982, 3990, 3998, 4015, 4022, 4029, 4037, 4069, 4086, 4106, 4183, 4186, 4187, 4241, 4244, 4260, 4271, 4279, 4302, 4314, 4323, 4326, 4338, 4350, 4371, 4375, 4376, 4398, 4404, 4415, 4424, 4429, 4431, 4451, 4470, 4487, 4493, 4532, 4533, 4535, 4546, 4548, 4549, 4555, 4556, 4565, 4600, 4602, 4617, 4625, 4635, 4649, 4654, 4665, 4685, 4686, 4688, 4694, 4722, 4724, 4727, 4739, 4747, 4750, 4753, 4754, 4757, 4769, 4770, 4771, 4772, 4773, 4774, 4775, 4776, 4778, 4780, 4781, 4783, 4784, 4786, 4787, 4789, 4790, 4791, 4793, 4794, 4796, 4797, 4799, 4801, 4802, 4803, 4804, 4805, 4807, 4808, 4809, 4810, 4811, 4812, 4813, 4814, 4815, 4817, 4819, 4820, 4822, 4823, 4824, 4826, 4827, 4828, 4829, 4831, 4832, 4833, 4835, 4837, 4838, 4839, 4840, 4841, 4842, 4844, 4845, 4846, 4848, 4849, 4850, 4852, 4853, 4854, 4855, 4858, 4860, 4861, 4862, 4863, 4864, 4865, 4869, 4870, 4871, 4872, 4873, 4874, 4876, 4877, 4879, 4880, 4881, 4882, 4883, 4884, 4885, 4886, 4887, 4889, 4890, 4892, 4893, 4895, 4896, 4900, 4901, 4902, 4903, 4904, 4905, 4906, 4908, 4910, 4911, 4912, 4913, 4914, 4916, 4917, 4918, 4920, 4921, 4922, 4923, 4924, 4926, 4927, 4929, 4930, 4931, 4932, 4933, 4935, 4936, 4937, 4938, 4939, 4940, 4941, 4942, 4943, 4944, 4946, 4947, 4948, 4949, 4950, 4951, 4953, 4954, 4955, 4956, 4958, 4959, 4960, 4961, 4963, 4964, 4965, 4966, 4967, 4968, 4970, 4971, 4973, 4976, 4978, 4979, 4980, 4981, 4983, 4985, 4986, 4987, 4990, 4991, 4992, 4993, 4994, 4996, 4998, 4999, 5000, 5002, 5003, 5004, 5005, 5006, 5007, 5009, 5010, 5011, 5013, 5015, 5016, 5017, 5018, 5020, 5021, 5023, 5024, 5027, 5028, 5029, 5032, 5033, 5034, 5037, 5040, 5041, 5042, 5043, 5045, 5048, 5049, 5051, 5052, 5053, 5054, 5055, 5057, 5058, 5060, 5061, 5062, 5063, 5064, 5065, 5067, 5068, 5069, 5070, 5071, 5072, 5073, 5074, 5075, 5076, 5077, 5078, 5079, 5080, 5082, 5083, 5084, 5086, 5087, 5088, 5089, 5090, 5091, 5092, 5093, 5094, 5095, 5096, 5097, 5099, 5100, 5101, 5105, 5106, 5107, 5111, 5112, 5113, 5115, 5116, 5118, 5119, 5120, 5122, 5123, 5124, 5125, 5126, 5128, 5129, 5130, 5131, 5132, 5133, 5134, 5135, 5136, 5137, 5138, 5139, 5140, 5141, 5142, 5143, 5144, 5146, 5147, 5149, 5150, 5151, 5152, 5154, 5156, 5158, 5160, 5161, 5162, 5164, 5165, 5166, 5167, 5168, 5169, 5171, 5172, 5175, 5176, 5178, 5179, 5183, 5186, 5188, 5190, 5192, 5194, 5195, 5197, 5198, 5199, 5200, 5203, 5205, 5206, 5207, 5208, 5209, 5210, 5211, 5212, 5213, 5214, 5215, 5216, 5217, 5218, 5219, 5220, 5221, 5222, 5225, 5226, 5227, 5229, 5230, 5231, 5232, 5233, 5235, 5236, 5237, 5238, 5239, 5240, 5242, 5244, 5247, 5249, 5250, 5251, 5252, 5253, 5254, 5256, 5257, 5258, 5259, 5260, 5261, 5262, 5263, 5264, 5265, 5266, 5268, 5269, 5270, 5271, 5272, 5273, 5274, 5275, 5276, 5277, 5278, 5280, 5281, 5283, 5284, 5285, 5286, 5290, 5291, 5292, 5293, 5295, 5296, 5297, 5298, 5299, 5300, 5301, 5302, 5303, 5304, 5306, 5307, 5308, 5309, 5310, 5311, 5312, 5313, 5314, 5315, 5318, 5319, 5322, 5323, 5324, 5325, 5326, 5327, 5329, 5330, 5331, 5332, 5333, 5334, 5336, 5337, 5338, 5339, 5341, 5342, 5343, 5344, 5345, 5347, 5348, 5350, 5351, 5353, 5355, 5375, 5376, 5379, 5380, 5382, 5384, 5386, 5387, 5388, 5390, 5391, 5392, 5394, 5397, 5400, 5401, 5402, 5403, 5405, 5406, 5407, 5408, 5411, 5413, 5414, 5415, 5416, 5417, 5418, 5419, 5420, 5421, 5423, 5425, 5426, 5427, 5428, 5429, 5430, 5431, 5432, 5433, 5434, 5436, 5438, 5440, 5441, 5442, 5444, 5445, 5448, 5449, 5450, 5451, 5452, 5453, 5454, 5456, 5457, 5458, 5459, 5460, 5461, 5462, 5463, 5467, 5469, 5470, 5473, 5474, 5475, 5476, 5477, 5480, 5481, 5482, 5484, 5486, 5488, 5489, 5490, 5491, 5492, 5493, 5494, 5495, 5496, 5497, 5498, 5499, 5500, 5501, 5503, 5504, 5505, 5506, 5507, 5509, 5510, 5512, 5514, 5516, 5517, 5518, 5519, 5520, 5521, 5523, 5524, 5525, 5526, 5527, 5528, 5529, 5530, 5532, 5533, 5534, 5535, 5536, 5537, 5538, 5539, 5540, 5541, 5545, 5547, 5548, 5550, 5551, 5553, 5554, 5557, 5558, 5559, 5560, 5561, 5562, 5563, 5564, 5565, 5566, 5567, 5568, 5569, 5570, 5572, 5573, 5574, 5579, 5580, 5583, 5585, 5588, 5592, 5593, 5594, 5595, 5596, 5597, 5598, 5599, 5600, 5601, 5602, 5605, 5606, 5607, 5608, 5609, 5610, 5611, 5612, 5613, 5616, 5617, 5618, 5619, 5620, 5621, 5624, 5625, 5626, 5628, 5629, 5630, 5631, 5633, 5634, 5636, 5637, 5638, 5639, 5640, 5641, 5643, 5644, 5647, 5649, 5651, 5653, 5658, 5659, 5660, 5661, 5662, 5664, 5665, 5666, 5667, 5668, 5669, 5670, 5672, 5673, 5675, 5676, 5678, 5680, 5683, 5685, 5686, 5687, 5688, 5689, 5690, 5692, 5695, 5696, 5697, 5699, 5701, 5703, 5704, 5706, 5708, 5709, 5711, 5712, 5713, 5716, 5717, 5720, 5721, 5722, 5723, 5725, 5726, 5727, 5728, 5729, 5732, 5733, 5734, 5735, 5736, 5737, 5738, 5739, 5740, 5741, 5743, 5744, 5745, 5746, 5747, 5748, 5749, 5753, 5756, 5758, 5759, 5760, 5762, 5764, 5765, 5766, 5767, 5771, 5772, 5773, 5774, 5777, 5778, 5779, 5780, 5781, 5783, 5785, 5786, 5787, 5789, 5792, 5794, 5795, 5796, 5797, 5798, 5801, 5802, 5804, 5806, 5807, 5809, 5811);
		//$arrUserLeftRC = array(3,   10,    9,   18,   17,  100,   79,  157,  384,  368,  476,  365,  542,  562,  338,  612,  658,  817,  913,  996, 1153, 1179, 1404, 1436, 1658, 1683, 1659, 1523, 1621, 2019, 2060, 1796, 2157, 2064, 2282, 2270, 2316, 2391, 1214, 2467, 2531, 2571, 2595, 2703, 2719,  327, 2979, 3020, 3215, 3243, 3252, 3197, 3302, 3288, 3333, 3083, 3379, 3089, 3388, 3382, 3439, 3445, 3434, 3462, 3487, 3523, 3550, 3491, 3615, 3458, 3588, 3671, 3653, 3700, 3672, 3664, 3740, 3795, 3788, 2913, 3870, 3882, 3917, 3876, 3489, 3833, 4019, 3352, 4032, 4055, 4080, 4118, 4138, 4146, 4166, 4193, 4205, 4211, 4217, 4204, 4206, 4208, 4088, 4252, 4065, 4331, 4332, 4319, 4344, 4353, 4359, 4368, 4345, 4386, 4362, 4283, 4448, 4459, 4462, 4320, 4322, 4483, 4408, 4486, 4519, 4515, 4305, 3211, 4566, 4567, 4596, 4466, 3871, 4666, 4194, 4704, 4714, 4673, 4806, 4834, 4867, 4843, 4919, 4934, 3888, 4907, 4972, 4988, 5008, 5025, 3337, 5059, 5035, 5109, 5102, 3856, 5159, 5180, 5193, 5170, 5255, 5204, 5267, 5187, 5289, 5305, 5340, 5377, 5316, 5443, 5385, 5478, 5487, 5575, 5582, 5589, 5472, 5584, 5502, 5674, 5657, 5656, 5698, 5707, 5714, 5710, 5719, 5103, 5782, 5750, 5791, 5635, 5803, 5800, 5841, 5844, 5843, 5829, 5881, 5890, 5900, 5889, 5910, 5917, 5922, 5825, 5928, 5921, 3506, 5939, 5946, 5671, 5962, 5974, 5930, 5989, 5998, 6004, 5971, 6022, 6032, 6027, 5964, 6028, 6039, 6042, 6049, 6054, 6067, 6007, 5398, 6126, 6094, 6130, 6134, 6120, 5768, 6073, 6147, 6152, 5941, 6136, 6209, 6213, 6221, 6207, 6239, 6244, 6151, 6252, 6247, 6261, 6293, 5943, 6310, 6299, 6290, 6315, 6338, 6375, 6381, 6386, 6384, 6394, 5882, 6400, 6392, 6401, 6405, 6409, 6390, 6416, 6433, 5935, 2492, 6320, 5615, 6478, 6278, 6515, 6521, 6523, 6418, 6520, 6531, 6475, 6266, 6542, 6236, 4104, 6517, 6551, 6560, 6571, 6599, 6584, 6609, 6525, 6107, 6613, 6624, 6634, 6454, 6583, 6631, 6649, 6592, 6684, 6689, 6702, 6705, 6706, 6707, 6708, 6709, 6651, 6664, 6717, 6663, 6214, 4821, 6267, 6724, 6736, 6695, 6759, 6373, 6822, 6748, 6838, 6844, 6848, 6856, 6858, 6743, 6874, 6901, 6905, 6911, 6904, 4601, 6927, 6648);
		$arrUserLeftRC = array(6373, 6822, 6748, 6838, 6844, 6848, 6856, 6858, 6743, 6874, 6901, 6905, 6911, 6904, 4601, 6927, 6648);
		$thisTbl = "<table cellspacing='3' border='1' cellpadding='5'>";
		$thisTbl .= "<tr><th>Sl.No.</th><th>User-id</th><th>Username</th><th>Campaigns</th><th>Contacts</th><th>Stats</th></tr>";
		$x = 0;
		$totCampaigns = $totContacts = $totStats = 0;
		foreach($arrUserLeftRC as $u){
			$thisUsername = $this->db->query("select member_username un from red_members where member_id='$u'")->row()->un; 
			$thisCampaigns = $this->db->query("select count(campaign_id) c from red_email_campaigns where campaign_created_by='$u'")->row()->c; 
			$thisContacts = $this->db->query("select count(subscriber_id) contact from red_email_subscribers where subscriber_created_by='$u'")->row()->contact; 
			$thisStats = $this->db->query("select count(subscriber_id) stats from red_email_track where user_id='$u'")->row()->stats; 
		$x++;
		$totCampaigns += $thisCampaigns;  $totContacts += $thisContacts;  $totStats += $thisStats;  
		$thisTbl .= "<tr><th>$x</th><th>$u</th><th>$thisUsername</th><th>$thisCampaigns</th><th>$thisContacts</th><th>$thisStats</th></tr>";

		}
		$thisTbl .= "<tr><th colspan='3'>TOTAL:</th><th>$totCampaigns</th><th>$totContacts</th><th>$totStats</th></tr>";
		$thisTbl .= "</table>";

		echo $thisTbl;	
	}	
	
	function getBouncedContacts(){
		$rsContacts = $this->db->query("Select subscriber_email_address from red_email_subscribers where subscriber_created_by=6401 and subscriber_status=1 and is_deleted=0 and subscriber_date_added > '2015-09-21'");
		$i=1;
		foreach($rsContacts->result() as $row){
			$thisEml = $row->subscriber_email_address;
			$rsCheckHardBounce = $this->db->query("select * from red_global_dnm where email_address='$thisEml' and dnm_type=1");
			if($rsCheckHardBounce->num_rows() > 0){
				//$this->db->query("update red_email_subscribers set subscriber_status=3,status_change_date='2015-09-28' where subscriber_created_by=6401 and subscriber_status=1 and is_deleted=0 and subscriber_email_address='$thisEml'");
				echo $i++. ' ' .$thisEml . "<br/>";
			}
			$rsCheckHardBounce->free_result();
		}
		$rsContacts->free_result();
	}
	function getPaidUsersCommaSeparated(){
		$m = '';
		$rsMembers = $this->db->query("Select m.member_id from red_members m inner join red_member_packages mp on m.member_id=mp.member_id where mp.package_id > 0 and mp.next_payement_date > now()");
		foreach($rsMembers->result() as $row){
			$m .= $row->member_id . ',';		
		}
		$rsMembers->free_result();	
		return  rtrim($m ,',');
	}
	function getLast5FromEmail(){
		$commaSeparatedMember = $this->getPaidUsersCommaSeparated();
		$arrMid = explode(',', $commaSeparatedMember);
		$thisTbl = "<table cellspacing='3' border='1' cellpadding='5'>";
		$thisTbl .= "<tr><th>User-id</th><th>Username</th><th>Campaigns</th><th>FromEmail</th><th>SendDate</th></tr>";
		foreach($arrMid as $mid){
			
			$rsCdetail = $this->db->query("select campaign_id, campaign_created_by, member_username, sender_email, email_send_date from red_email_campaigns t1 right join red_members t2 on t1.campaign_created_by=t2.member_id where campaign_created_by =$mid and t1.campaign_status='active' and email_send_date is not null order by email_send_date desc limit 5");
			if($rsCdetail->num_rows() > 0){
			foreach($rsCdetail->result() as $row){
				$thisTbl .= "<tr><td>".$row->campaign_created_by."</td>";
				$thisTbl .= "<td>".$row->member_username."</td>";
				$thisTbl .= "<td>".$row->campaign_id."</td>";
				$thisTbl .= "<td>".$row->sender_email."</td>";
				$thisTbl .= "<td>".$row->email_send_date."</td></tr>";				
			}
			$thisTbl .= "<tr><td colspan='5'><hr/></td></tr>";
			}
			$rsCdetail->free_result();
		}
	$thisTbl .= "</table>";
	echo $thisTbl;
	}
	function getReportLast30days($typ='s'){
		// u = unsubscribe, b = bounce, c or s = spam
		// Critical-levels: u = 5/2, b=7/2, s= 0.1/2
		if($typ == 'u'){
			$strHeading = 'Unsubscribes';
			$per = 0.0025; // 5/(2*100)
			$clause = 't2.email_track_unsubscribes';
		}elseif($typ == 'b'){
			$strHeading = 'Bounce';				
			$per = 0.035; // 7/(2*100)
			$clause = 't2.email_track_bounce';
		}else{
			$strHeading = 'Spam';				
			$per = 0.0005; // 0.1/(2*100)
			$clause = 't2.email_track_spam';
		}
		$commaSeparatedMember = $this->getPaidUsersCommaSeparated();
		$arrMid = explode(',', $commaSeparatedMember);		
		$thisTbl = "<h1>$strHeading [".($per *100)."%]</h1>"; 
		$thisTbl .= "<table cellspacing='3' border='1' cellpadding='5'>";
		$thisTbl .= "<tr><th>User-id</th><th>Username</th><th>Campaigns</th><th>SendDate</th><th>Sent</th><th>Released</th><th>Delivered</th><th>Unsub</th><th>Bounce</th><th>Spam</th></tr>";
		foreach($arrMid as $mid){	 	
			$sql = "select t1.campaign_created_by, t1.campaign_id, t2.email_track_sent, t2.email_track_released, t2.email_track_delivered, t1.email_send_date, t2.email_track_bounce, t2.email_track_spam, t2.email_track_unsubscribes from red_email_campaigns t1 inner join red_email_campaigns_scheduled t2 on t1.campaign_id=t2.campaign_id where t1.campaign_created_by='$mid' and t1.campaign_status='active' and t1.email_send_date > (NOW() - INTERVAL 30 DAY) and ($clause / email_track_released) > $per";
			$rsCdetail = $this->db->query($sql);
		// echo "<br/>".$this->db->last_query(); 
			if($rsCdetail->num_rows() > 0){			
			foreach($rsCdetail->result() as $row){
				$mid = $row->campaign_created_by;
				$mname = $this->db->query("select member_username from red_members where member_id='$mid'")->row()->member_username;
				$thisTbl .= "<tr><td>$mid</td>";
				$thisTbl .= "<td>$mname</td>";
				$thisTbl .= "<td>".$row->campaign_id."</td>";
				$thisTbl .= "<td>".$row->email_send_date."</td>";				
				$thisTbl .= "<td>".$row->email_track_sent."</td>";
				$thisTbl .= "<td>".$row->email_track_released."</td>";
				$thisTbl .= "<td>".$row->email_track_delivered."</td>";
				$thisTbl .= "<td>".$row->email_track_unsubscribes."</td>";
				$thisTbl .= "<td>".$row->email_track_bounce."</td>";
				$thisTbl .= "<td>".$row->email_track_spam."</td></tr>";				
			}
			$thisTbl .= "<tr><td colspan='10'><hr/></td></tr>";
			}
			$rsCdetail->free_result();
		}
	$thisTbl .= "</table>";
	echo $thisTbl;
	}
	
	function importFromStats(){
		$minqueue = 2756921803;
		//for($i =0 ;$i < 31 , $i++){
		$arrmember = array(4919,9926,10832,157,2595,3805,7818,9232,6937,5999,10143,8723,9517,5478,6521,5385,11113,10209,7269,6207,5710,9544,6475,6724,8932,10687,5890,1436,9492,3774,4596,7804,8664,3487,4821,7770,7162,6759,9837,8794,7773,4462,10129,4666,7128,2492,11123,6320,6238,10287,7415,6525,7917,4448,4138,6927,7950,9082,10466,5584,10484,9663,2719,8054,7446,3870,7664,4088,9808,11175,4217,3379,8872,1683,6631,8805,11133,11124,10774,10387,10004,3664,11028,10101,3917,4483,4601,9718,3788,7131,6963,6779,9597,7324,6901,3571,11145,5635,6856,8669,5989,7777,10364,6401,5768,10073,6390,6384,6039,4907,9093,3439,6648,5803,4486,2270,10034,7680,8510,9668,6705,10401,9714,11049,5922,8612,10,8868,7650,9146,10154,368,7731,3523,7262,11191,10701,8920,7602,7648,7006,11192,5800,7084,5992,7105,3506,7598,11189,3288,11197,10120,11223,8175,11199,6620,7428,11232,11011,9642,11220,8232,6707,11239,10360,7848,3740,11262,10843,11268,4332,6386,9190,11272,6708,10525,996,11274,11216,11279,11280,5939,10379,11287,7120,7858);
		foreach($arrmember as $m){
			echo "<br/>Starts for member-id:".$m;
			$rsContact = $this->db->query("select distinct subscriber_id,subscriber_email_address,subscriber_email_domain from red_email_track where queue_id >  '$minqueue' and user_id = '$m' ");
			foreach($rsContact->result_array() as $row){
				$subscriber_id = $row['subscriber_id'];
				$subscriber_email_address = $row['subscriber_email_address'];
				$subscriber_email_domain = $row['subscriber_email_domain'];
				$subscriber_created_by = $row['user_id'];
				
				$rsCheckContact =$this->db->query("select subscriber_id from red_email_subscribers where subscriber_id='$subscriber_id'");
				if($rsCheckContact->num_rows() <= 0){
					//$this->db->query("insert ignore into red_email_subscribers set subscriber_id='$subscriber_id',subscriber_email_address='$subscriber_email_address', subscriber_email_domain = '$subscriber_email_domain', subscriber_created_by='$subscriber_created_by'");
					echo "insert ignore into red_email_subscribers set subscriber_id='$subscriber_id',subscriber_email_address='$subscriber_email_address', subscriber_email_domain = '$subscriber_email_domain', subscriber_created_by='$subscriber_created_by' <br/>";
		
				}
		
			}
			$rsContact->free_result();	
			echo "<br/>Ends for member-id:".$m;
		}
		
		//}
		
	}
	function bouncing_domain_contact_count($breason=1, $page_start=0, $page_size=10){
		if($breason == 1){
			$breason = 'spam-related';
		}elseif($breason == 2){
			$breason = 'policy-related';
		}else{
			$breason = 'spam-related';
		}
		
		
		// Bouncing Domains ==============================		
		$rsBouncingDomain = $this->db->query("select  substring_index(rcpt,'@',-1)bouncing_domains from red_pmtalog where (`type`='b' or `type`='rb') and bounceCat='$breason' group by bouncing_domains  having count(jobId) > 14 ");
		$arrBouncingDomains = array();	
		foreach($rsBouncingDomain->result_array() as $row){
			$arrBouncingDomains[] = $row['bouncing_domains'];
		}
		$rsBouncingDomain->free_result();
		
		// Paid Users ==============================
		$rspaidUsers = $this->db->query("Select t1.member_id,t1.member_username from red_members t1 inner join red_member_packages t2 on t1.member_id=t2.member_id where t2.package_id > 0 and t2.next_payement_date > now() limit $page_start, $page_size");
		 $arrMembers = array();
		foreach($rspaidUsers->result_array() as $row){
			$mid = $row['member_id'];
			$mname = $row['member_username'];
			$arrMembers[$mid] = $mname;			
		}
		$rspaidUsers->free_result();
		//print_r($arrBouncingDomains);
		//print_r($arrMembers);		
		// Report ===============================
		$thisTbl = "<h1>$breason</h1>This report can be fetched for <a href='/webmaster/report/bouncing_domain_contact_count/1' target='_blank'>spam-related</a>=>1,<a href='/webmaster/report/bouncing_domain_contact_count/2' target='_blank'>policy-related=>2</a>,<a href='/webmaster/report/bouncing_domain_contact_count/3' target='_blank'>others</a>=>3. Domains are fetched when bounce-count is more than 14 and users are listed when contact-count for bouncing domain is more than 10.<br/>"; 
		$thisTbl .= "<table cellspacing='3' border='1' cellpadding='5'>";
		$thisTbl .= "<tr><th>UserId</th><th>Username</th><th>Domain</th><th>Count</th></tr>";
		foreach($arrBouncingDomains as $d){
			foreach($arrMembers as $k=>$v){
		 		$rsReport = $this->db->query("Select count(subscriber_id)c,subscriber_email_domain from red_email_subscribers where subscriber_created_by='$k' and subscriber_status=1 and is_deleted=0 and subscriber_email_domain ='$d'");
		 		$thisCount = $rsReport->row()->c;
				$rsReport->free_result();
				if($thisCount > 10)
				$thisTbl .= "<tr><td>$k</td><td>$v</td><td>$d</td><td>$thisCount</td></tr>";
			}
		}
		$thisTbl .= '</table>';
		echo $thisTbl ;
	}
}
?>