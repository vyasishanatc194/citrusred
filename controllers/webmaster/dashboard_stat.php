<?php
class Dashboard_Stat extends CI_Controller{
	function __construct(){
		parent::__construct();
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');

		# Load the user model which interact with database
		$this->load->model('UserModel');
		$this->load->model('newsletter/Emailreport_Model');
		$this->load->model('webmaster/Campaigns_Model');
		$this->load->model('newsletter/Campaign_Model');	
		$this->load->model('newsletter/Subscriber_Model');	
		$this->load->model('ConfigurationModel');		
		# HTTPS/SSL enabled
		force_ssl();
		$this->output->enable_profiler(false);
	}
	
	function index(){
		$this->stats();
	}
	function stats(){
	$this->output->enable_profiler(false);
		$stats = array();
		$today 		= date("Y-m-d");
		$yesterday 	= date("Y-m-d", strtotime("-1 days"));
		$last7days 	= date("Y-m-d", strtotime("-7 days")); 
		$last30days = date("Y-m-d", strtotime("-30 days"));
		//Get date for sunday(week-start) and saturday (week-end)
		$day = date('w'); // contains a number from 0 to 6 representing the day of the week (Sunday = 0, Monday = 1, etc.).
		$week_start = date('Y-m-d', strtotime('-'.$day.' days')); //contains the date for Sunday
		$week_end = date('Y-m-d', strtotime('+'.(6-$day).' days')); //contains the date for Saturday
		
		$stats['total_users_count']	= $this->UserModel->get_member_packages_count(array('m.is_deleted'=>0,'m.status'=>'active'), true);		
		$stats['free_users_count']	= $this->UserModel->get_member_packages_count(array('rp.package_id'=>'-1','m.is_deleted'=>0,'m.status'=>'active'), true);		
		//$paid_users_count	= $this->UserModel->get_member_packages_count(array('rp.package_id >'=>'0','m.is_deleted'=>0,'m.status'=>'active','is_admin'=>0,'DATEDIFF(next_payement_date,CURDATE()) >'=>'0'));	
		//==============
                    $rsPaidUsers = $this->db->query("select m.member_id,m.member_username,mp.package_id,mp.amount,date_format(mp.next_payement_date, '%Y-%m-%d') pt_date, mp.payment_type from red_members m inner join red_member_packages mp on m.member_id=mp.member_id where mp.package_id >0 and mp.next_payement_date >= '$today' order by package_id");
		//echo $this->db->last_query();
		$totalAmount = 0;		$totalAmountExpectedToday = 0;		$totalAmountExpectedWeek = 0;
		$totalPaidUsers = $rsPaidUsers->num_rows();		 
		foreach($rsPaidUsers->result() as $row){
			$thisMid = $row->member_id; 
			$thisAmount = $row->amount;	
			$thisNextPtDate = $row->pt_date;
                                  $thisPaymentType = $row->payment_type;
			$this->load->model('payment/payment_model');			 
			$totalAmount += floatval($this->payment_model->getDiscountedAmountForSubsequentPayments($thisMid,$thisAmount));	
			// For Today			
			if($thisNextPtDate == $today){
				$totalAmountExpectedToday += floatval($this->payment_model->getDiscountedAmountForSubsequentPayments($thisMid,$thisAmount));
                                if ($thisPaymentType == 'PayPal'){
                                    $totalAmountExpectedTodayPayPal  += floatval($this->payment_model->getDiscountedAmountForSubsequentPayments($thisMid,$thisAmount));
                                }
                        }
                                
			
			 		
			if(strtotime($thisNextPtDate) >= strtotime($week_start) and strtotime($thisNextPtDate) <= strtotime($week_end)){
				$totalAmountExpectedWeek += floatval($this->payment_model->getDiscountedAmountForSubsequentPayments($thisMid,$thisAmount));	
			}
		}
		$rsPaidUsers->free_result();
		
		$totalAmountAlreadyPaidToday = $this->db->query("select sum(amount_paid)rs from red_member_transactions where gateway IN ('AUTHORIZE','PAYPAL') and status='SUCCESS' and date_format(transaction_date,'%Y-%m-%d') = '$today'")->row()->rs;
		 $totalAmountAlreadyPaidWeek = $this->db->query("select sum(amount_paid)rs from red_member_transactions where gateway IN ('AUTHORIZE','PAYPAL') and status='SUCCESS' and date_format(transaction_date,'%Y-%m-%d') >= '$week_start' and date_format(transaction_date,'%Y-%m-%d') <= '$week_end'")->row()->rs;
		 $totalAmountAlreadyPaidTodayPayPal = $this->db->query("select sum(amount_paid)rs from red_member_transactions where gateway IN ('PAYPAL') and status='SUCCESS' and date_format(transaction_date,'%Y-%m-%d') = '$today'")->row()->rs;
                 
		//==============
		$stats['paid_users_count']	= $totalPaidUsers ;
		$stats['mrr_expected'] = $totalAmount ; 
		$stats['mrr_expected_today'] = $totalAmountAlreadyPaidToday + $totalAmountExpectedToday;
                $stats['mrr_expected_today_paypal'] = $totalAmountAlreadyPaidTodayPayPal + $totalAmountExpectedTodayPayPal;
                $stats['mrr_expected_today_credit_card'] = $stats['mrr_expected_today'] - $stats['mrr_expected_today_paypal'];
		$stats['mrr_expected_week_sun_sat'] = $totalAmountExpectedWeek + $totalAmountAlreadyPaidWeek;
		$stats['monthly_avg_revenue_per_customer'] =  ($totalPaidUsers > 0)? $totalAmount / $totalPaidUsers : $totalAmount; 
		$stats['fail_cc_users_total']	= $this->UserModel->get_member_packages_count(array('rp.package_id >'=>'0','m.is_deleted'=>0,'m.status'=>'active','is_admin'=>0,'DATEDIFF(next_payement_date,CURDATE()) <='=>'0'));	 
		$stats['fail_cc_users_today']	= $this->UserModel->get_member_packages_count(array('rp.package_id >'=>'0','m.is_deleted'=>0,'m.status'=>'active','is_admin'=>0,'DATEDIFF(next_payement_date,CURDATE())'=>-1));	 
		$stats['fail_cc_users_last7days']	= $this->UserModel->get_member_packages_count(array('rp.package_id >'=>'0','m.is_deleted'=>0,'m.status'=>'active','is_admin'=>0,'DATEDIFF(next_payement_date,CURDATE()) <='=>'0','DATEDIFF(next_payement_date,CURDATE()) >='=>-7));
		$stats['fail_cc_users_last30days']	= $this->UserModel->get_member_packages_count(array('rp.package_id >'=>'0','m.is_deleted'=>0,'m.status'=>'active','is_admin'=>0,'DATEDIFF(next_payement_date,CURDATE()) <='=>'0','DATEDIFF(next_payement_date,CURDATE()) >='=>-30));	
		
		$stats['admin_comped_users_count']	= $this->UserModel->get_member_packages_count(array('rp.package_id >'=>'0','m.is_deleted'=>0,'m.status'=>'active','is_admin'=>1));				
		$stats['paid_users_count_from_beginning']	= $this->UserModel->get_paid_user_from_beginning();		
		$stats['avg_subscription_lifetime']	= ($stats['paid_users_count_from_beginning'] > 0)?($this->UserModel->get_avg_subscription_lifetime() / $stats['paid_users_count_from_beginning']):0;		 
		
		//$stats['campaign_count']				= $this->Campaign_Model->get_campaign_count(array('rec.is_deleted'=>0,'is_status'=>0));	
		 
		$thisNow = date('Y-m-d H:i:s',now());
		//$thisNow = now();
		$stats['campaign_approval_count']	= $this->Campaigns_Model->get_campaign_count("(rec.campaign_status = 'ready' or rec.campaign_status ='active_ready') and rec.is_deleted=0",true);	
		$stats['campaign_approval_delayed']	= $this->Campaigns_Model->get_campaign_count("(rec.campaign_status = 'ready' or rec.campaign_status ='active_ready') and rec.is_deleted=0 and rec.is_segmentation = 0 and (rec.campaign_sheduled) < '$thisNow'",true);	
		// echo $this->db->last_query();
		//$campaign_approval_count	= $this->Campaign_Model->get_campaign_count(array(	'rec.campaign_status'=>'ready',	'rec.is_deleted'=>0,'is_status'=>0	));		
		$stats['sent_campaign_count']		= $this->Campaign_Model->get_campaign_count(array('rec.campaign_status'=>'active',	'is_status'=>0));
		
		$stats['sent_campaign_today']		= $this->Campaign_Model->get_campaign_count(array('rec.campaign_status'=>'active',	'is_status'=>0, 'email_send_date >' => date("Y-m-d", strtotime("-1 days"))));
		
		$stats['sent_campaign_last7days']		= $this->Campaign_Model->get_campaign_count(array('rec.campaign_status'=>'active',	'is_status'=>0, 'email_send_date >' => date("Y-m-d", strtotime("-7 days"))));
		
		$stats['sent_email_count']		= $this->db->query("SELECT SUM(campaign_contacts) AS emls FROM (red_email_campaigns as rec) WHERE rec.campaign_status = 'active' AND is_status = 0")->row()->emls; 
		
		$stats['sent_email_today']		= $this->db->query("SELECT SUM(campaign_contacts) AS emls FROM (red_email_campaigns as rec) WHERE rec.campaign_status = 'active' AND is_status = 0 and email_send_date > '$yesterday'")->row()->emls;
		
		$stats['sent_email_last7days']		= $this->db->query("SELECT SUM(campaign_contacts) AS emls FROM (red_email_campaigns as rec) WHERE rec.campaign_status = 'active' AND is_status = 0 and email_send_date > '$last7days'")->row()->emls;
		
		
		
		$stats['registered_user_count']		= $this->UserModel->get_user_count(array('DATE(rm.created_on)'=> date( 'Y-m-d')));
		$stats['this_month_users']			= $this->UserModel->get_user_count(array('DATE(rm.created_on) >='=> date( 'Y-m-1')));
		
		$last_month_start = date("Y-m-1", strtotime("last month"));
		$last_month_end = date("Y-m-t", strtotime("last month"));
		
		$stats['last_month_users']		= $this->UserModel->get_user_count(array('DATE(rm.created_on) >='=> $last_month_start ,'DATE(rm.created_on) <='=> $last_month_end ));
		 
		$stats['last_7days_users']		= $this->UserModel->get_user_count(array('DATE(rm.created_on) >='=> date("Y-m-d", strtotime("-7 days"))));
		
		$stats['last_30days_users']		= $this->UserModel->get_user_count(array('DATE(rm.created_on) >='=> date("Y-m-d", strtotime("-30 days"))));
		
		
		// customer churn and revenue churn
                $churnEndDate = $today;
                $churnStartDate = date("Y-m-d",strtotime("-6 months"));
                $statsSqlChurn = "CALL uspR_GetCustomerChurn('$churnStartDate',' $churnEndDate')";
               //$rs = $this->db->query($statsSqlChurn);
                

                //echo $statsSqlChurn;
                //$rsChurn = $this->db->query($statsSqlChurn);
		
                
                
		$stats['signupform_unverified'] = $this->db->query("select count(id) c from red_signup_form where is_verified=0 and is_deleted=0 and is_hidden=0 and date_added > '2016-11-01'")->row()->c;
		
		$stats['autoresponders_unverified'] = $this->db->query("select count(campaign_id) c from red_email_autoresponders where is_verified=0 and is_deleted=0 and campaign_date_added > '2016-11-01'")->row()->c;
		
                $stats['fromemail_unverified'] = $this->db->query("select 10 as x from red_member_from_email where is_verified = 1 and added_date > '2016-11-01'")->row()->c;
		
		$stats['free_to_paid_today'] = $this->db->query("select count(m.member_id) c from red_members m inner join red_member_transactions t on m.member_id=t.user_id where t.transaction_date > '$yesterday' and t.payment_type=1 and t.status='success'")->row()->c;
		
		$stats['free_to_paid_last7days'] = $this->db->query("select count(m.member_id) c from red_members m inner join red_member_transactions t on m.member_id=t.user_id where t.transaction_date > '$last7days' and t.payment_type=1 and t.payment_type=1 and t.status='success'")->row()->c;
		
		$stats['free_to_paid_last30days'] = $this->db->query("select count(m.member_id) c from red_members m inner join red_member_transactions t on m.member_id=t.user_id where t.transaction_date > '$last30days' and t.payment_type=1 and t.payment_type=1 and t.status='success'")->row()->c;
		
		
		$stats['fail_to_paid_today'] = $this->getFailCCToPaid($yesterday);
		
		$stats['fail_to_paid_last7days'] = $this->getFailCCToPaid($last7days);
		
		$stats['fail_to_paid_last30days'] = $this->getFailCCToPaid($last30days); 
		
		$rsProblematicCampaigns = $this->db->query("SELECT distinct c.campaign_id, email_send_date ,`email_track_sent`,email_track_bounce,email_track_spam,email_track_unsubscribes FROM `red_email_campaigns` c inner join red_email_campaigns_scheduled cs on c.campaign_id=cs.campaign_id WHERE c.email_send_date >= DATE_SUB(CURRENT_DATE(),INTERVAL 1 DAY) and campaign_status='active'");
                

		$stats['high_bounces'] = 0;
		$stats['high_spams'] = 0;
		$stats['high_unsubscribes'] = 0;
		$arrConfig = $this->ConfigurationModel->get_site_configuration_data_as_array();
		 
		foreach($rsProblematicCampaigns->result() as $row){
			$thisSent = $row->email_track_sent;
			$thisBounces = $row->email_track_bounce;
			$thisSpam = $row->email_track_spam;
			$thisUnsubscribes = $row->email_track_unsubscribes;
			
			if($thisSent > 0){
				$thisBouncePercentage = ($thisBounces / $thisSent)*100;
				if($thisBouncePercentage > $arrConfig['maximum_bounce_contact'])	$stats['high_bounces']++;
				$thisSpamPercentage = ($thisSpam / $thisSent)*100;
				if($thisSpamPercentage > $arrConfig['fbl_critical_limit'])	$stats['high_spams']++;
				$thisUnsubPercentage = ($thisUnsubscribes / $thisSent)*100;
				if($thisUnsubPercentage > $arrConfig['unsubscribe_critical_level_to_alert'])	$stats['high_unsubscribes']++;				
			}
			
		}
		$rsProblematicCampaigns->free_result();
		
		/*
		echo "<br/>";
		echo $totalMemberInMonthEnd 				= $this->db->query("select count(distinct user_id) m from red_member_transactions where date_format(transaction_date,'%Y-%m') = '$customerChurnMonth' and payment_type=2 and status='SUCCESS' and gateway='AUTHORIZE'")->row()->m;
		echo "<br/>";
		echo $totalMemberInMonthEnd 				= $this->db->query("select count(distinct user_id) m from red_member_transactions where date_format(transaction_date,'%Y-%m') = '$customerChurnMonth' and payment_type=1 and status='SUCCESS' and gateway='AUTHORIZE'")->row()->m;
		echo "<br/>";
		echo $totalMemberInMonthEnd 				= $this->db->query("select count(distinct user_id) m from red_member_transactions where date_format(transaction_date,'%Y-%m') = '$customerChurnMonth' and payment_type > 2 and status='SUCCESS' and gateway='AUTHORIZE'")->row()->m;
		*/
		$stats['lifetime_value'] = number_format(($stats['mrr_expected'] * $stats['avg_subscription_lifetime']),2);
		
		$this->load->view('webmaster/header',array('title'=>'RedCappi Stats','logo_link'=> "webmaster/dashboard_stat"));
$this->load->view('webmaster/dashboard_stats',array('stats'=>$stats));		//$this->load->view('webmaster/dashboard_stats',array('total_users_count'=>$total_users_count, 'free_users_count'=>$free_users_count,'paid_users_count'=>$paid_users_count,'fail_cc_users_count'=>$fail_cc_users_count, 'admin_comped_users_count'=>$admin_comped_users_count, 'campaign_count'=>$campaign_count,'campaign_approval_count'=>$campaign_approval_count,'user_upgrade_count'=>$user_upgrade_count,'registered_user_count'=>$registered_user_count, 'this_month_users'=>$this_month_users, 'last_month_users'=>$last_month_users, 'mrr_expected'=> '$'.$mrr_expected, 'monthly_avg_revenue_per_customer'=>$monthly_avg_revenue_per_customer, 'lifetime_value'=> $lifetime_value, 'sent_campaign_count'=>$sent_campaign_count, 'paid_users_count_from_beginning'=>$paid_users_count_from_beginning, 'avg_subscription_lifetime'=>$avg_subscription_lifetime , 'customerChurn' => $customerChurn , 'customerChurnMonthStr' => $customerChurnMonthStr));
		$this->load->view('webmaster/footer');	
	}
	
 	function getFailCCToPaid($lastDays = 1){
 		$c = 0;
 		$rsFailToPaid = $this->db->query("select distinct m.member_id from red_members m inner join red_member_transactions t on m.member_id=t.user_id where t.transaction_date > '$lastDays' and t.status='failure'");
 		if($rsFailToPaid->num_rows() > 0){
			foreach($rsFailToPaid->result() as $row){
				$thisMid = $row->member_id;
				$lastPaymentStatus = $this->db->query("select status from red_member_transactions where user_id='$thisMid' order by transaction_id desc limit 1,1")->row()->status;
				
				if($lastPaymentStatus == 'SUCCESS'){
				//echo $this->db->last_query();echo ";<br/>";
				$c++;
				}
			}
 		}
 		$rsFailToPaid->free_result();
 		return $c;
 	}
	function view($id=0){
		$fetch_conditions_array=array('member_id'=>$id,'is_deleted'=>0);
		$user_data_array=$this->UserModel->get_user_data($fetch_conditions_array,$config['per_page']);
		$this->load->view('webmaster/user_view',array('user'=>$user_data_array[0]));
	}
	
	function sent_campaign($start=0){		
		# Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/dashboard_stat/sent_campaign';
		$config['total_rows']=$this->Campaigns_Model->get_campaign_count(array(	'campaign_status'=>'active'	),true);
		$config['per_page']=25;
		$config['uri_segment']=4;
		# Initialize paging with above parameters
		$this->pagination->initialize($config);
		
		#Create paging inks
		$paging_links=$this->pagination->create_links();
		# Fetches campaign data from database
		$campaign_data=$this->Campaigns_Model->get_campaign_data_for_sentmails(array('campaign_status'=>'active'),$config['per_page'],$start);
		#Fetch stat of campaigns
		foreach($campaign_data as $campaign){
						
			# Get Total number of delivered mail 
			$total_sent_emails=$this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$campaign['campaign_id'],'email_sent'=>1	));
			$total_delivered_emails=$this->Emailreport_Model->get_emailreport_count(array(	'campaign_id'=>$campaign['campaign_id'],'email_delivered'=>1	));
			$fetch_condiotions_array=array('campaign_id'=>$campaign['campaign_id'],	'email_track_read'=>1);
			# Get Total number of  mail 
			$total_read_emails=$this->Emailreport_Model->get_emailreport_count($fetch_condiotions_array);			
			
			# Count total bounce emails
			$fetch_condiotions_array=array(
			'campaign_id'=>$campaign['campaign_id'],
			'email_track_bounce >'=>0
			);
			# Get Total number of bounce mail 
			$total_bounce_emails=$this->Emailreport_Model->get_emailreport_count($fetch_condiotions_array);
			
			$total_unread_emails=$total_delivered_emails-$total_read_emails-$total_bounce_emails;
			$fetch_condiotions_array=array(	'campaign_id'=>$campaign['campaign_id'],'email_track_complaint'=>1);
			# Get Total number of complaint mail
			$total_complaint_emails=$this->Emailreport_Model->get_emailreport_count($fetch_condiotions_array);		
			$fetch_condiotions_array=array(	'campaign_id'=>$campaign['campaign_id']);
			# Get Total number of  mail 
			$list_emails=$this->Emailreport_Model->get_emailreport_listdata($fetch_condiotions_array);
			$total_click_emails=$list_emails[0]['email_track_read'];

			# collect values in array for email report view
			# schedule date
			$emailreport_data[$campaign['campaign_id']]['campaign_send_date']=$list_emails[0]['campaign_scheduled_date'];
			# send date
			$emailreport_data[$campaign['campaign_id']]['email_send_date']=$campaign['email_send_date'];
			# total unsubscribes email
			$emailreport_data[$campaign['campaign_id']]['total_unsubscribes']=$list_emails[0]['email_track_unsubscribes'];
			# total delivered emails
			$emailreport_data[$campaign['campaign_id']]['total_sent_emails']=$total_sent_emails;
			$emailreport_data[$campaign['campaign_id']]['total_delivered_emails']=$total_delivered_emails;
			# total read emails
			$emailreport_data[$campaign['campaign_id']]['total_read_emails']=$total_read_emails;
			# total unread emails
			$emailreport_data[$campaign['campaign_id']]['total_unread_emails']=$total_unread_emails;
			# total click emails
			$emailreport_data[$campaign['campaign_id']]['total_click_emails']=$list_emails[0]['email_track_click'];
			#Campaign title
			$emailreport_data[$campaign['campaign_id']]['campaign_title']=$campaign['campaign_title'];
			# total bounce emails
			$emailreport_data[$campaign['campaign_id']]['email_track_bounce']=$total_bounce_emails;
			# total bounce emails
			$emailreport_data[$campaign['campaign_id']]['total_complaint_emails']=$total_complaint_emails;
			# total forward emails
			$emailreport_data[$campaign['campaign_id']]['email_track_forward']=$list_emails[0]['email_track_forward'];
			$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$campaign['campaign_created_by']));
			$emailreport_data[$campaign['campaign_id']]['member_username']=$user_data_array[0]['member_username'];
			$emailreport_data[$campaign['campaign_id']]['member_id']=$user_data_array[0]['member_id'];
		}
	 
		 
		 
 		
		if($_POST['mode']=='search' AND !isset($_POST['btn_cancel'])){
			$contacts_array['username']=$_POST['username'];
			$contacts_array['date_from']=$_POST['date_from'];
			$contacts_array['date_to']=$_POST['date_to'];
			$contacts_array['sort_by']=$_POST['sort_by'];
			if(trim($contacts_array['sort_by']) != '' and count($emailreport_data)>0)
			$emailreport_data = $this->sort_td_array($emailreport_data,$contacts_array['sort_by'],true);		
		}else{
			//$contacts_array['date_from']=date('Y-m-d',strtotime("-1 month"));
			//$contacts_array['date_to']=date('Y-m-d');
		}
		 
		$logo_link="webmaster/dashboard_stat";
		#Get shoreten url 
		$shorten_url=get_shorten_url();
		#Loads header, users edit  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Sent Campaign List','logo_link'=>$logo_link));
		$this->load->view('webmaster/sent_campaign_stat',array('emailreport_data'=>$emailreport_data,'paging_links'=>$paging_links,'contacts_array'=>$contacts_array,'shorten_url'=>$shorten_url));
		$this->load->view('webmaster/footer');
	}
	function sent_campaign_new($start=0){	 
		
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/dashboard_stat/sent_campaign_new';
		$config['total_rows']=$this->Campaigns_Model->get_campaign_count(array(	'campaign_status'=>'active'	),true);
		$config['per_page']=25;
		$config['uri_segment']=4;
		
		$this->pagination->initialize($config);
		 
		$paging_links=$this->pagination->create_links();
		$sort_by = (isset($_POST['sort_by']) and trim($_POST['sort_by']) !='')?$_POST['sort_by']:'email_send_date';
		
		$campaign_data=$this->Campaigns_Model->get_campaign_data_for_sentmails_new(array('campaign_status'=>'active'),$sort_by,$config['per_page'],$start);
		
		
		foreach($campaign_data as $campaign){
		
			
			 $total_sent_emails = $this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$campaign['campaign_id'],'email_sent'=>1	));

			
			$emailreport_data[$campaign['campaign_id']]['is_deleted']=$campaign['is_deleted']; 
			// collect values in array for email report view			
			$emailreport_data[$campaign['campaign_id']]['campaign_send_date']=$campaign['campaign_scheduled_date']; // schedule date			
			$emailreport_data[$campaign['campaign_id']]['email_send_date']=$campaign['email_send_date']; // send date			
			$emailreport_data[$campaign['campaign_id']]['total_released_emails']=$campaign['email_track_released']; // total released email
			$emailreport_data[$campaign['campaign_id']]['total_delivered_emails']=$campaign['email_track_delivered']; // total delivered email			
			$emailreport_data[$campaign['campaign_id']]['total_unsubscribes']=$campaign['email_track_unsubscribes']; // total unsubscribes email			
			$emailreport_data[$campaign['campaign_id']]['total_sent_emails']= ($total_sent_emails > 0)?$total_sent_emails: $campaign['email_track_delivered']; // total sent emails			
			$emailreport_data[$campaign['campaign_id']]['total_read_emails']=$campaign['email_track_read']; // total read emails			
			$emailreport_data[$campaign['campaign_id']]['total_unread_emails']=$campaign['email_track_delivered'] - $campaign['email_track_read'] - $campaign['email_track_bounce']; // total unread emails			
			$emailreport_data[$campaign['campaign_id']]['total_click_emails']=$campaign['email_track_click']; // total click emails			
			$emailreport_data[$campaign['campaign_id']]['campaign_title']=$campaign['campaign_title']; // Campaign title	
			$emailreport_data[$campaign['campaign_id']]['sender_name']=$campaign['sender_name']; // Campaign title			
			$emailreport_data[$campaign['campaign_id']]['sender_email']=$campaign['sender_email']; // Campaign title			
			$emailreport_data[$campaign['campaign_id']]['pipeline']=$campaign['pipeline']; // Campaign pipeline			
			$emailreport_data[$campaign['campaign_id']]['segment_interval']=$campaign['segment_interval']; // Segmentation Interval	
			$emailreport_data[$campaign['campaign_id']]['campaign_contacts']=$campaign['campaign_contacts']; // campaign's contacts			
			$emailreport_data[$campaign['campaign_id']]['number_of_contacts']=$campaign['number_of_contacts']; // Segmentation size			
			$emailreport_data[$campaign['campaign_id']]['email_track_bounce']=$campaign['email_track_bounce']; // total bounce emails			
			$emailreport_data[$campaign['campaign_id']]['total_complaint_emails']=$campaign['email_track_spam']; // total bounce emails			
			$emailreport_data[$campaign['campaign_id']]['email_track_forward']=$campaign['email_track_forward']; // total forward emails
			
			$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$campaign['campaign_created_by']));
			$emailreport_data[$campaign['campaign_id']]['member_username']=$user_data_array[0]['member_username'];
			$emailreport_data[$campaign['campaign_id']]['member_id']=$user_data_array[0]['member_id'];
		}		 
 		
		if($_POST['mode']=='search' AND !isset($_POST['btn_cancel'])){
			$contacts_array['username']=$_POST['username'];
			$contacts_array['pipeline']=$_POST['pipeline'];
			$contacts_array['date_from']=$_POST['date_from'];
			$contacts_array['date_to']=$_POST['date_to'];
			$contacts_array['sort_by']=$_POST['sort_by'];			 	
			$contacts_array['keyword']=$_POST['keyword'];			 	
			$contacts_array['above_critical_level']=$_POST['above_critical_level'];			 	
		}
		
		 
		$logo_link="webmaster/dashboard_stat";
		
		$shorten_url=get_shorten_url();
		
		$this->load->view('webmaster/header',array('title'=>'Sent Campaign List','logo_link'=>$logo_link));
		$this->load->view('webmaster/sent_campaign_stat_new',array('emailreport_data'=>$emailreport_data,'paging_links'=>$paging_links,'contacts_array'=>$contacts_array,'shorten_url'=>$shorten_url));
		$this->load->view('webmaster/footer');
	}
	function sent_campaign_beta($start=0){	 
		
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/dashboard_stat/sent_campaign_beta';
		$config['total_rows']=$this->Campaigns_Model->get_campaign_count(array(	'campaign_status'=>'active'	),true);
		$config['per_page']=25;
		$config['uri_segment']=4;
		
		$this->pagination->initialize($config);
		 
		$paging_links=$this->pagination->create_links();
		$sort_by = (isset($_POST['sort_by']) and trim($_POST['sort_by']) !='')?$_POST['sort_by']:'email_send_date';
		
		$campaign_data=$this->Campaigns_Model->get_campaign_data_for_sentmails_new(array('campaign_status'=>'active'),$sort_by,$config['per_page'],$start);
		
		
		foreach($campaign_data as $campaign){
		
			
			$total_sent_emails=$this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$campaign['campaign_id'],'email_sent'=>1	));
			$thisUser 		= $campaign['campaign_created_by'];
			$thisCampaignId = $campaign['campaign_id'];
			$this_email_hard_unsubscribes =  $this->db->query("select count(subscriber_id) fd from red_unsubscribe_feedback where member_id='$thisUser' and campaign_id='$thisCampaignId' and (feedback_id=3 or feedback_id=4)  ")->row()->fd;
			 
			// collect values in array for email report view			
			$emailreport_data[$thisCampaignId]['campaign_send_date']=$campaign['campaign_scheduled_date']; // schedule date			
			$emailreport_data[$thisCampaignId]['email_send_date']=$campaign['email_send_date']; // send date			
			$emailreport_data[$thisCampaignId]['total_delivered_emails']=$campaign['email_track_delivered']; // total delivered email
			$emailreport_data[$thisCampaignId]['total_released_emails']=$campaign['email_track_released']; // total released email
			$emailreport_data[$thisCampaignId]['total_unsubscribes']=$campaign['email_track_unsubscribes']; // total unsubscribes email			
			$emailreport_data[$thisCampaignId]['total_hard_unsubscribes']=$this_email_hard_unsubscribes; // total unsubscribes email			
			$emailreport_data[$thisCampaignId]['total_sent_emails']= ($total_sent_emails > 0)?$total_sent_emails: $campaign['email_track_delivered']; // total sent emails			
			$emailreport_data[$thisCampaignId]['total_read_emails']=$campaign['email_track_read']; // total read emails			
			$emailreport_data[$thisCampaignId]['total_unread_emails']=$campaign['email_track_delivered'] - $campaign['email_track_read'] - $campaign['email_track_bounce']; // total unread emails			
			$emailreport_data[$thisCampaignId]['total_click_emails']=$campaign['email_track_click']; // total click emails			
			$emailreport_data[$thisCampaignId]['campaign_title']=$campaign['campaign_title']; // Campaign title	
			$emailreport_data[$thisCampaignId]['sender_name']=$campaign['sender_name']; // Campaign title			
			$emailreport_data[$thisCampaignId]['sender_email']=$campaign['sender_email']; // Campaign title			
			$emailreport_data[$thisCampaignId]['segment_interval']=$campaign['segment_interval']; // Segmentation Interval			
			$emailreport_data[$thisCampaignId]['number_of_contacts']=$campaign['number_of_contacts']; // Segmentation size			
			$emailreport_data[$thisCampaignId]['email_track_bounce']=$campaign['email_track_bounce']; // total bounce emails			
			$emailreport_data[$thisCampaignId]['total_complaint_emails']=$campaign['email_track_spam']; // total bounce emails			
			$emailreport_data[$thisCampaignId]['email_track_forward']=$campaign['email_track_forward']; // total forward emails
			
			$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$thisUser));
			$emailreport_data[$thisCampaignId]['member_username']=$user_data_array[0]['member_username'];
			$emailreport_data[$thisCampaignId]['member_id']=$user_data_array[0]['member_id'];
		}		 
 		
		if($_POST['mode']=='search' AND !isset($_POST['btn_cancel'])){
			$contacts_array['username']=$_POST['username'];
			$contacts_array['date_from']=$_POST['date_from'];
			$contacts_array['date_to']=$_POST['date_to'];
			$contacts_array['sort_by']=$_POST['sort_by'];			 	
			$contacts_array['keyword']=$_POST['keyword'];			 	
			$contacts_array['above_critical_level']=$_POST['above_critical_level'];			 	
		}
		
		 
		$logo_link="webmaster/dashboard_stat";
		
		$shorten_url=get_shorten_url();
		
		$this->load->view('webmaster/header',array('title'=>'Sent Campaign List','logo_link'=>$logo_link));
		$this->load->view('webmaster/sent_campaign_stat_beta',array('emailreport_data'=>$emailreport_data,'paging_links'=>$paging_links,'contacts_array'=>$contacts_array,'shorten_url'=>$shorten_url));
		$this->load->view('webmaster/footer');
	}
	function bad_campaign_report($typ='spam'){
		  
		$rsProblematicCampaigns = $this->db->query("SELECT distinct c.campaign_id, email_track_sent, email_track_bounce,email_track_spam,email_track_unsubscribes FROM `red_email_campaigns` c inner join red_email_campaigns_scheduled cs on c.campaign_id=cs.campaign_id WHERE c.email_send_date >= DATE_SUB(CURRENT_DATE(),INTERVAL 1 DAY) and campaign_status='active'");
		 
		$arrConfig = $this->ConfigurationModel->get_site_configuration_data_as_array();
		
		$bouncedCampaigns = $fblCampaigns = $unsubscribeCampaigns =''; 
		foreach($rsProblematicCampaigns->result() as $row){
			$thisCampaignId = $row->campaign_id;		
			$thisSent = $row->email_track_sent;
			$thisBounces = $row->email_track_bounce;
			$thisSpam = $row->email_track_spam;
			$thisUnsubscribes = $row->email_track_unsubscribes;
			
			if($thisSent > 0){
				$thisBouncePercentage = ($thisBounces / $thisSent)*100;
				if($thisBouncePercentage > $arrConfig['maximum_bounce_contact']){$bouncedCampaigns .= $thisCampaignId.',';	$stats['high_bounces']++;}
				$thisSpamPercentage = ($thisSpam / $thisSent)*100;
				if($thisSpamPercentage > $arrConfig['fbl_critical_limit']){$fblCampaigns .= $thisCampaignId.',';	$stats['high_spams']++;}
				$thisUnsubPercentage = ($thisUnsubscribes / $thisSent)*100;
				if($thisUnsubPercentage > $arrConfig['unsubscribe_critical_level_to_alert']){ $unsubscribeCampaigns .= $thisCampaignId.',';	$stats['high_unsubscribes']++;}
			}
			
		}
		$rsProblematicCampaigns->free_result();
		if($typ == 'bounce')$strCid =  rtrim($bouncedCampaigns,',');
		if($typ == 'spam')$strCid =  rtrim($fblCampaigns,',');
		if($typ == 'unsubscribe')$strCid =  rtrim($unsubscribeCampaigns,',');
		//-----------------------------------------
		if($strCid !=''){  
		$campaign_data=$this->Campaigns_Model->get_campaign_data_for_sentmails_new(array('campaign_status'=>'active', "c.campaign_id in ($strCid)"=>null),'email_send_date',1000,0);
		 
		
		foreach($campaign_data as $campaign){
		
			
			 $total_sent_emails=$this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$campaign['campaign_id'],'email_sent'=>1	));
			 
			 
					$thisUser 		= $campaign['campaign_created_by'];
					$thisCampaignId = $campaign['campaign_id'];
					$this_email_hard_unsubscribes =  $this->db->query("select count(subscriber_id) fd from red_unsubscribe_feedback where member_id='$thisUser' and campaign_id='$thisCampaignId' and (feedback_id=3 or feedback_id=4)  ")->row()->fd;
			 
					// collect values in array for email report view			
					$emailreport_data[$thisCampaignId]['campaign_send_date']=$campaign['campaign_scheduled_date']; // schedule date			
					$emailreport_data[$thisCampaignId]['email_send_date']=$campaign['email_send_date']; // send date			
					$emailreport_data[$thisCampaignId]['total_delivered_emails']=$campaign['email_track_delivered']; // total delivered email
					$emailreport_data[$thisCampaignId]['total_unsubscribes']=$campaign['email_track_unsubscribes']; // total unsubscribes email			
					$emailreport_data[$thisCampaignId]['total_hard_unsubscribes']=$this_email_hard_unsubscribes; // total unsubscribes email			
					$emailreport_data[$thisCampaignId]['total_sent_emails']= ($total_sent_emails > 0)?$total_sent_emails: $campaign['email_track_delivered']; // total sent emails			
					$emailreport_data[$thisCampaignId]['total_read_emails']=$campaign['email_track_read']; // total read emails			
					$emailreport_data[$thisCampaignId]['total_unread_emails']=$campaign['email_track_delivered'] - $campaign['email_track_read'] - $campaign['email_track_bounce']; // total unread emails			
					$emailreport_data[$thisCampaignId]['total_click_emails']=$campaign['email_track_click']; // total click emails			
					$emailreport_data[$thisCampaignId]['campaign_title']=$campaign['campaign_title']; // Campaign title	
					$emailreport_data[$thisCampaignId]['sender_name']=$campaign['sender_name']; // Campaign title			
					$emailreport_data[$thisCampaignId]['sender_email']=$campaign['sender_email']; // Campaign title			
					$emailreport_data[$thisCampaignId]['segment_interval']=$campaign['segment_interval']; // Segmentation Interval			
					$emailreport_data[$thisCampaignId]['number_of_contacts']=$campaign['number_of_contacts']; // Segmentation size			
					$emailreport_data[$thisCampaignId]['email_track_bounce']=$campaign['email_track_bounce']; // total bounce emails			
					$emailreport_data[$thisCampaignId]['total_complaint_emails']=$campaign['email_track_spam']; // total bounce emails			
					$emailreport_data[$thisCampaignId]['email_track_forward']=$campaign['email_track_forward']; // total forward emails
			
					$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$thisUser));
					$emailreport_data[$thisCampaignId]['member_username']=$user_data_array[0]['member_username'];
					$emailreport_data[$thisCampaignId]['member_id']=$user_data_array[0]['member_id'];
				 
		}		 
 		}
		 
		 
		$logo_link="webmaster/dashboard_stat";
		
		$shorten_url=get_shorten_url();
		
		$this->load->view('webmaster/header',array('title'=>'Sent Campaign List','logo_link'=>$logo_link));
		$this->load->view('webmaster/bad_campaign_report',array('emailreport_data'=>$emailreport_data,'typ'=>$typ,'shorten_url'=>$shorten_url));
		$this->load->view('webmaster/footer');
	}
	function pmta_diag($ip,$cid,$webmail){
		$dlvSourceIp = long2ip($ip);
		$strTable = '<div class="tblheading">Blocked Campaign Detail </div>
<table class="tbl_listing" width="100%"> 
	<thead><tr><th>Status</th><th>Report</th></tr></thead>
	<tbody>';
		$rsBlockedStats = $this->db->query("SELECT distinct dsnStatus, dsnDiag FROM `red_pmtalog_blocked` where dlvSourceIp='$dlvSourceIp' and envId='$cid' and SUBSTRING_INDEX(rcpt,'@',-1)='$webmail'");
		foreach($rsBlockedStats->result() as $row){
			$strTable .= "<tr><td>".$row->dsnStatus."</td><td>".$row->dsnDiag."</td></tr>";
		}
		$rsBlockedStats->free_result();
		echo $strTable;
	}
	function blocked_contacts(){
	
		$strTable = '<table class="tbl_listing" width="100%"> 
	<thead><tr><th>User</th><th>vmta</th><th>Campaign</th><th>Webmail</th><th>Count</th></tr></thead>
	<tbody>';	
		$rsBlockedStats = $this->db->query("SELECT b.vmta,dlvSourceIp,envId,SUBSTRING_INDEX(rcpt,'@',-1)webmail,count(SUBSTRING_INDEX(rcpt,'@',-1)) x  
FROM `red_pmtalog_blocked` b where date_format(timeLogged,'%Y-%m-%d')>= DATE_SUB(CURRENT_DATE(),INTERVAL 1 DAY) and dsnDiag like'%block%'
group by vmta,dlvSourceIp,envId,webmail order by x desc");
		foreach($rsBlockedStats->result() as $row){
			$cid = $row->envId;
			$dlvSourceIp = $row->dlvSourceIp;
			$dlvSourceIpLong = ip2long($dlvSourceIp);
			$webmail = $row->webmail;
			$mname = $this->db->query("select member_username from red_email_campaigns c inner join red_members m on c.campaign_created_by=m.member_id where c.campaign_id='$cid'")->row()->member_username;
			$strTable .= "<tr><td>$mname</td><td>".$row->vmta." [$dlvSourceIp] </td><td>$cid</td><td>$webmail</td>
							<td><a class=\"fancybox\" href='". site_url('webmaster/dashboard_stat/pmta_diag/'.$dlvSourceIpLong.'/'.$cid.'/'.$webmail)."'>".$row->x."</a></td></tr>";
		}
		$strTable .= "</tbody></table>";
		$rsBlockedStats->free_result();
		
		$logo_link="webmaster/dashboard_stat";
		 
		$this->load->view('webmaster/header',array('title'=>'Blocked Contacts','logo_link'=>$logo_link));		
		$this->load->view('webmaster/blocked_contacts',array('ReportTable'=>$strTable));
		$this->load->view('webmaster/footer',null);
	
	}
	// Function to show stats as User version
	function user_campaign_stats($start=0){	 
		
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/dashboard_stat/user_campaign_stats';
		$config['total_rows']=$this->Campaigns_Model->get_campaign_count(array(	'campaign_status'=>'active'	),true);
		$config['per_page']=25;
		$config['uri_segment']=4;
		
		$this->pagination->initialize($config);
		 
		$paging_links=$this->pagination->create_links();
		$sort_by = (isset($_POST['sort_by']) and trim($_POST['sort_by']) !='')?$_POST['sort_by']:'email_send_date';
		
		$campaign_data=$this->Campaigns_Model->get_campaign_data_for_sentmails_new(array('campaign_status'=>'active'),$sort_by,$config['per_page'],$start);
		
		
		foreach($campaign_data as $campaign){
		
			
			$total_sent_emails=$this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$campaign['campaign_id'],'email_sent'=>1	));
			
			 
			// collect values in array for email report view			
			$emailreport_data[$campaign['campaign_id']]['campaign_send_date']=$campaign['campaign_scheduled_date']; // schedule date			
			$emailreport_data[$campaign['campaign_id']]['email_send_date']=$campaign['email_send_date']; // send date			
			$emailreport_data[$campaign['campaign_id']]['total_delivered_emails']=$campaign['email_track_delivered']; // total delivered email
			$emailreport_data[$campaign['campaign_id']]['total_released_emails']=$campaign['email_track_released']; // total released email
			$emailreport_data[$campaign['campaign_id']]['total_unsubscribes']=$campaign['email_track_unsubscribes']; // total unsubscribes email			
			$emailreport_data[$campaign['campaign_id']]['total_sent_emails']= ($total_sent_emails > 0)?$total_sent_emails: $campaign['email_track_delivered']; // total sent emails			
			$emailreport_data[$campaign['campaign_id']]['total_read_emails']=$campaign['email_track_read']; // total read emails			
			$emailreport_data[$campaign['campaign_id']]['total_unread_emails']=$campaign['email_track_delivered'] - $campaign['email_track_read'] - $campaign['email_track_bounce']; // total unread emails			
			$emailreport_data[$campaign['campaign_id']]['total_click_emails']=$campaign['email_track_click']; // total click emails			
			$emailreport_data[$campaign['campaign_id']]['campaign_title']=$campaign['campaign_title']; // Campaign title	
			$emailreport_data[$campaign['campaign_id']]['sender_name']=$campaign['sender_name']; // Campaign title			
			$emailreport_data[$campaign['campaign_id']]['sender_email']=$campaign['sender_email']; // Campaign title			
			$emailreport_data[$campaign['campaign_id']]['number_of_contacts']=$campaign['number_of_contacts']; // Campaign title			
			$emailreport_data[$campaign['campaign_id']]['email_track_bounce']=$campaign['email_track_bounce']; // total bounce emails			
			$emailreport_data[$campaign['campaign_id']]['total_complaint_emails']=$campaign['email_track_spam']; // total bounce emails			
			$emailreport_data[$campaign['campaign_id']]['email_track_forward']=$campaign['email_track_forward']; // total forward emails
			
			$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$campaign['campaign_created_by']));
			$emailreport_data[$campaign['campaign_id']]['member_username']=$user_data_array[0]['member_username'];
			$emailreport_data[$campaign['campaign_id']]['member_id']=$user_data_array[0]['member_id'];
		}		 
 		
		if($_POST['mode']=='search' AND !isset($_POST['btn_cancel'])){
			$contacts_array['username']=$_POST['username'];
			$contacts_array['date_from']=$_POST['date_from'];
			$contacts_array['date_to']=$_POST['date_to'];
			$contacts_array['sort_by']=$_POST['sort_by'];			 	
			$contacts_array['keyword']=$_POST['keyword'];			 	
			$contacts_array['above_critical_level']=$_POST['above_critical_level'];			 	
		}
		
		 
		$logo_link="webmaster/dashboard_stat";
		
		$shorten_url=get_shorten_url();
		
		$this->load->view('webmaster/header',array('title'=>'Sent Campaign List','logo_link'=>$logo_link));
		$this->load->view('webmaster/user_campaign_stats',array('emailreport_data'=>$emailreport_data,'paging_links'=>$paging_links,'contacts_array'=>$contacts_array,'shorten_url'=>$shorten_url));
		$this->load->view('webmaster/footer');
	}
	
	
	
	function sent_campaign_user_stats($mid=0){			
		$campaign_data=$this->Campaigns_Model->get_campaign_data_for_sentmails_new(array('campaign_created_by'=>$mid,'campaign_status'=>'active'),'email_send_date',10,0);
		//echo $this->db->last_query();	 
		foreach($campaign_data as $campaign){
			$total_sent_emails=$this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$campaign['campaign_id'],'email_sent'=>1	));			
			// collect values in array for email report view			
			$emailreport_data[$campaign['campaign_id']]['campaign_send_date']=$campaign['campaign_scheduled_date']; // schedule date			
			$emailreport_data[$campaign['campaign_id']]['email_send_date']=$campaign['email_send_date']; // send date	
			$emailreport_data[$campaign['campaign_id']]['total_released_emails']=$campaign['email_track_released']; // total released email			
			$emailreport_data[$campaign['campaign_id']]['total_delivered_emails']=$campaign['email_track_delivered']; // total delivered email
			$emailreport_data[$campaign['campaign_id']]['total_unsubscribes']=$campaign['email_track_unsubscribes']; // total unsubscribes email			
			$emailreport_data[$campaign['campaign_id']]['total_sent_emails']= ($total_sent_emails > 0)?$total_sent_emails: $campaign['email_track_delivered']; // total sent emails			
			$emailreport_data[$campaign['campaign_id']]['total_read_emails']=$campaign['email_track_read']; // total read emails			
			$emailreport_data[$campaign['campaign_id']]['total_unread_emails']=$campaign['email_track_delivered'] - $campaign['email_track_read'] - $campaign['email_track_bounce']; // total unread emails			
			$emailreport_data[$campaign['campaign_id']]['total_click_emails']=$campaign['email_track_click']; // total click emails			
								
			$emailreport_data[$campaign['campaign_id']]['number_of_contacts']=$campaign['number_of_contacts']; // Campaign title			
			$emailreport_data[$campaign['campaign_id']]['email_track_bounce']=$campaign['email_track_bounce']; // total bounce emails			
			$emailreport_data[$campaign['campaign_id']]['total_complaint_emails']=$campaign['email_track_spam']; // total bounce emails			
			$emailreport_data[$campaign['campaign_id']]['email_track_forward']=$campaign['email_track_forward']; // total forward emails
			
			$emailreport_data[$campaign['campaign_id']]['campaign_title']=$campaign['campaign_title']; // Campaign title			
			$emailreport_data[$campaign['campaign_id']]['email_subject']=$campaign['email_subject']; // Campaign subject			
			$emailreport_data[$campaign['campaign_id']]['sender_name']=$campaign['sender_name']; // Campaign from-name			
			$emailreport_data[$campaign['campaign_id']]['sender_email']=$campaign['sender_email']; // Campaign from-email
			$emailreport_data[$campaign['campaign_id']]['subscription_list']= $this->Campaigns_Model->getContactList($campaign['subscription_list']); // Contacts list
			
			$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$campaign['campaign_created_by']));
			$emailreport_data[$campaign['campaign_id']]['member_username']=$user_data_array[0]['member_username'];
			$emailreport_data[$campaign['campaign_id']]['member_id']=$user_data_array[0]['member_id'];
		}		 		
		
		#Loads header, users edit  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Sent Campaign List','logo_link'=>'webmaster/dashboard_stat'));
		$this->load->view('webmaster/sent_campaign_user_stats',array('emailreport_data'=>$emailreport_data,'paging_links'=>$paging_links,'contacts_array'=>$contacts_array,'shorten_url'=>get_shorten_url()));
		$this->load->view('webmaster/footer');
	}
	
	function sumit($dt=0, $oprate_morethan=0, $oprate_lessthan=3 , $vmta='all'){
		$dt = intval($dt);
		if($dt > 0)
		$stats_dt = date('Y-m-d',strtotime((0-$dt).' days'));
		else
		$stats_dt = date('Y-m-d');
		
		$pipeline_clause =($vmta == 'all')?"" : " and c.pipeline='$vmta' ";
		$sqlDailyStats = "select member_username, c.pipeline as vmta, date_format(email_send_date,'%Y-%m-%d') dt, date_format(email_send_date,'%h:%i %p') tm, c.campaign_id, email_track_delivered, sender_email,sender_name, (100*(email_track_read/email_track_delivered)) as 'open_rate' from red_email_campaigns c inner join red_email_campaigns_scheduled s on c.campaign_id=s.campaign_id inner join red_members m on c.campaign_created_by=m.member_id where campaign_status='active' and date_format(email_send_date,'%Y-%m-%d')='$stats_dt' $pipeline_clause order by email_send_date asc";
		
		$rs_campaign_data=$this->db->query($sqlDailyStats);
		$tblStats = '<div style="padding:20px;"><b>URL Detail : sumit/ days_gap /  open_rate_morethan / open_rate_lessthan / pipeline</b> <br/><br/><table cellspacing="0" cellpadding="4" border="1"><tr><th>User</th><th>Date</th><th>Time</th><th>Campaign id</th><th>Pipeline</th><th>Sent</th><th>Delivered</th><th>From Name</th><th>From Email</th><th>Open-rate</th></tr>';
		
		if($rs_campaign_data->num_rows()>0){
			
			foreach($rs_campaign_data->result() as $row){
				$total_sent_emails=$this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$row->campaign_id,'email_sent'=>1));
				if($row->open_rate > $oprate_morethan  and $row->open_rate < $oprate_lessthan){
				$tblStats .= "<tr><td>".$row->member_username.'</td><td>'.$row->dt.'</td><td>'.$row->tm.'</td><td>'.$row->campaign_id.'</td><td>'.$row->vmta.'</td><td>'.$total_sent_emails.'</td><td>'.$row->email_track_delivered.'</td><td>'.$row->sender_name.'</td><td>'.$row->sender_email.'</td><td>'.round($row->open_rate,2).'%</td></tr>';
				}
			}
		}		
		$tblStats .= '</table></div>';
		$rs_campaign_data->free_result();		 
		 
		//echo $this->load->view('webmaster/header',array('title'=>'Sent Campaign List','logo_link'=>$logo_link),true);
		echo $tblStats;
		//echo $this->load->view('webmaster/footer',,true);
	}
	
	function sumit2($dt=0){
		$dt = intval($dt);
		if($dt > 0)
		$stats_dt = date('Y-m-d',strtotime((0-$dt).' days'));
		else
		$stats_dt = date('Y-m-d');
		$sqlDailyStats = "select member_username,date_format(email_send_date,'%Y-%m-%d') dt, date_format(email_send_date,'%h:%i %p') tm, c.campaign_id, email_track_delivered, sender_email,sender_name, email_track_read, email_track_bounce, email_track_unsubscribes, email_track_spam from red_email_campaigns c inner join red_email_campaigns_scheduled s on c.campaign_id=s.campaign_id inner join red_members m on c.campaign_created_by=m.member_id where campaign_status='active' and date_format(email_send_date,'%Y-%m-%d')='$stats_dt' order by member_username asc";
		
		$rs_campaign_data=$this->db->query($sqlDailyStats);
		$tblStats = '<div style="padding:20px;"><table cellspacing="0" cellpadding="4" border="1"><tr><th>User</th><th>Date</th><th>Campaign id</th><th>From Name</th><th>From Email</th><th>Sent</th><th>Delivered</th><th>Opens</th><th>Bounces</th><th>Unsubscribes</th><th>Complaints</th></tr>';
		
		if($rs_campaign_data->num_rows()>0){			
			foreach($rs_campaign_data->result() as $row){
				$total_sent_emails=$this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$row->campaign_id,'email_sent'=>1));
				
				$tblStats .= "<tr><td>".$row->member_username.'</td><td>'.$row->dt.'</td><td>'.$row->campaign_id.'</td><td>'.$row->sender_name.'</td><td>'.$row->sender_email.'</td><td>'.$total_sent_emails.'</td><td>'.$row->email_track_delivered.'</td><td>'.$row->email_track_read.'</td><td>'.$row->email_track_bounce.'</td><td>'.$row->email_track_unsubscribes.'</td><td>'.$row->email_track_spam.'</td></tr>';
				
			}
		}	
		$tblStats .= '</table></div>';
		$rs_campaign_data->free_result();		 
		 
		//echo $this->load->view('webmaster/header',array('title'=>'Sent Campaign List','logo_link'=>$logo_link),true);
		echo $tblStats;
		//echo $this->load->view('webmaster/footer',,true);
	}
	function sumit3(){
		$arrMid = array(368,1179,1436,2282,2595,2719,3083,3439,3491,3506,3523,3550,3664,3700,3740,3788,3856,3876,3917,4019,4032,4055,4118,4138,4204,4205,4206,4208,4305,4319,4320,4332,4344,4345,4448,4483,4486,4519,4843,4907,5008,5025,5035,5102,5103,5193,5305,5377,5385,5478,5575,5582,5584,5635,5674,5710,5714,5768,5800,5803,5825,5890,5917,5921,5939,5943,5962,5964,5974,6007,6022,6027,6039,6049,6126,6136,6151,6152,6207,6209,6221,6239,6247,6252,6290,6293,6299,6310,6315,6338,6375,6384);
		
		$arrDomain = array('@gmail.','@yahoo.','@hotmail.','@aol');		
		$tblStats = '<div style="padding:20px;"><table cellspacing="0" cellpadding="4" border="1"><tr><th>User</th><th>Gmail-Sent</th><th>Gmail-Deliv</th><th>yahoo-sent</th><th>yahoo-del</th><th>hotmail-sent</th><th>hotmail-Deli</th><th>aol-sent</th><th>aol-del</th></tr>';
		
		foreach($arrMid as $mid){
			$thisUsername = $this->db->query("select member_username from red_members where member_id ='$mid'")->row()->member_username;
			$thisCampaignID = $this->db->query("select campaign_id from red_email_campaigns where campaign_created_by ='$mid' and campaign_status='active' order by email_send_date desc limit 1")->row()->campaign_id;			
			
			//$thisCampaignSent =$this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$thisCampaignID));
			//$thisCampaignDelivered = $this->db->query("select email_track_delivered from red_email_campaigns_scheduled where campaign_id ='$thisCampaignID'")->row()->email_track_delivered;	
			
			$gmailSent = $this->db->query("select count(queue_id) sent from red_email_track where campaign_id='$thisCampaignID' and email_sent > 0 and subscriber_email_address like'%@gmail.%' ")->row()->sent;	
			$gmailDelivered = $this->db->query("select count(queue_id) dl from red_email_track where campaign_id='$thisCampaignID' and email_delivered > 0 and subscriber_email_address like'%@gmail.%' ")->row()->dl;	
			
			$yahooSent = $this->db->query("select count(queue_id) sent from red_email_track where campaign_id='$thisCampaignID' and email_sent > 0 and subscriber_email_address like'%@yahoo.%'  ")->row()->sent;	
			$yahooDelivered = $this->db->query("select count(queue_id) dl from red_email_track where campaign_id='$thisCampaignID' and email_delivered > 0 and subscriber_email_address like'%@yahoo.%' ")->row()->dl;	
			
			$hotmailSent = $this->db->query("select count(queue_id) sent from red_email_track where campaign_id='$thisCampaignID' and email_sent > 0 and subscriber_email_address like'%@hotmail.%'  ")->row()->sent;	
			$hotmailDelivered = $this->db->query("select count(queue_id) dl from red_email_track where campaign_id='$thisCampaignID' and email_delivered > 0 and subscriber_email_address like'%@hotmail.%' ")->row()->dl;	
			
			$aolSent = $this->db->query("select count(queue_id) sent from red_email_track where campaign_id='$thisCampaignID' and email_sent > 0 and subscriber_email_address like'%@aol.%'  ")->row()->sent;	
			$aolDelivered = $this->db->query("select count(queue_id) dl from red_email_track where campaign_id='$thisCampaignID' and email_delivered > 0 and subscriber_email_address like'%@aol.%' ")->row()->dl;	
					
			$tblStats .= "<tr><td>$thisUsername</td><td>$gmailSent</td><td>$gmailDelivered</td><td>$yahooSent</td><td>$yahooDelivered</td><td>$hotmailSent</td><td>$hotmailDelivered</td><td>$aolSent</td><td>$aolDelivered</td></tr>";			 
		} 
		$tblStats .= '</table></div>';
		
		echo $tblStats;
		
	}
	
	function sumit4($dt=0){
		$dt = intval($dt);
		if($dt > 0)
		$stats_dt = date('Y-m-d',strtotime((0-$dt).' days'));
		else
		$stats_dt = date('Y-m-d');
		$sqlDailyStats = "select distinct member_username, vmta, campaign_id from red_email_campaigns c inner join red_members m on c.campaign_created_by=m.member_id inner join red_member_packages mp on m.member_id=mp.member_id where mp.next_payement_date > now() and mp.package_id > 0 and campaign_status='active' and vmta='redrotate' and date_format(email_send_date,'%Y-%m-%d') > '$stats_dt' order by member_username asc";
		
		$rs_campaign_data=$this->db->query($sqlDailyStats);
		$tblStats = '<div style="padding:20px;"><table cellspacing="0" cellpadding="4" border="1"><tr><th>User</th><th>Pipeline</th><th>Total Delivered</th><th>Gmail Delivered</th></tr>';
		$uname = '';
		
		if($rs_campaign_data->num_rows()>0){			
			foreach($rs_campaign_data->result() as $row){
				$cid = $row->campaign_id;				
				if( $uname ==''){				
					$uname =  $row->member_username;
					$vmta = $row->vmta;
					
					$total_delivered = 0;
					$gmail_delivered = 0;				
				}elseif($row->member_username != $uname and $uname !=''){					
					$tblStats .= "<tr><td>$uname</td><td>$vmta</td><td>$total_delivered</td><td>$gmail_delivered</td></tr>";					
					$uname =  $row->member_username;
					$vmta = $row->vmta;
					
					$total_delivered = 0;
					$gmail_delivered = 0;
				}
				$total_delivered += $this->db->query("select count(queue_id) as tdel from red_email_track where campaign_id='$cid' and email_delivered > 0 ")->row()->tdel;
				$gmail_delivered += $this->db->query("select count(queue_id) as gdel from red_email_track where campaign_id='$cid' and email_delivered > 0 and subscriber_email_address like'%@gmail.%'")->row()->gdel;						
			}
		}	
		$tblStats .= '</table></div>';
		$rs_campaign_data->free_result();		 		
		echo $tblStats;		
	}
	// Function to show pipeline-based mail-domain specific weekly sent, delivered, opened, bounced, unsubscribed, spam counts
	function sumit5($vmta='mailsvrc.com',$d='gmail.com',$dtfrom='20150601',$dtto='20150607'){
		$dtfrom = substr($dtfrom,0,4).'-'.substr($dtfrom,4,2).'-'.substr($dtfrom,6,2);
		$dtto = substr($dtto,0,4).'-'.substr($dtto,4,2).'-'.substr($dtto,6,2);
		$mail_domain_clause = ($d != 'all')?" and mail_domain='$d'" : "";
		
		
		
		if($vmta == 'redrotate'){
		$pipelineClause = "(pipeline='redrotate' or pipeline='redrotate2' or pipeline='redrotate3' or pipeline='rcmailer2' or pipeline='rcmailer3' or pipeline='rcmailer4' or pipeline='rcmailer6' or pipeline='rcmailer7' or pipeline='rcmailer8' or pipeline='rcmailer9' or pipeline='rcmailer10' or pipeline='rcmailer11' or pipeline='rcmailer12')";
		}elseif($vmta == 'rcmailsv.com'){
		$pipelineClause = "(pipeline='rcmailsv.com' or pipeline='rc73' or pipeline='rc74' or pipeline='rc75' or pipeline='rc76')";
		}elseif($vmta == 'mailsvrc.com'){
		$pipelineClause = "(pipeline='rc33' or pipeline='rc34' or pipeline='rc35' or pipeline='rc36' or pipeline='mailsvrc.com')";	
		}
		
		//$sqlDailyStats = "select t3.member_id, t4.member_username, SUM(email_track_delivered) delivered,SUM(email_track_click)clicks, SUM(email_track_unsubscribes)unsubscribes, SUM(email_track_spam) spams, SUM(email_track_bounce)bounces from red_email_campaigns_scheduled t1 inner join red_email_campaigns t2 on t1.campaign_id=t2.campaign_id inner join red_member_packages t3 on t2.campaign_created_by=t3.member_id inner join red_members t4 on t3.member_id=t4.member_id where t2.pipeline='$vmta' and t2.campaign_status = 'active' and t2.email_send_date >'2015-05-11' and t2.email_send_date < '2015-05-18' and next_payement_date > now() and t3.package_id > 0 group by t3.member_id,t4.member_username;";
		$sqlDailyStats = "select member_id, member_username, sum(total_sent)total_sent, sum(total_delivered)total_delivered, sum(total_opened)total_opened, sum(total_bounced)total_bounced, sum(total_unsubscribed)total_unsubscribed, sum(total_complaint)total_complaint from red_global_ipr_daily t1 inner join red_members t2 on t1.user_id=t2.member_id where log_date > '$dtfrom 23:59:59' and  log_date < '$dtto 23:59:59' $mail_domain_clause and $pipelineClause and user_id in(select member_id from  red_member_packages where package_id > 3  and next_payement_date > now())  group by member_id,member_username";
		
		$rs_campaign_data=$this->db->query($sqlDailyStats);
		$tblStats = '<div style="padding:20px;">'.
		"<b>URL: sumit5/pipeline/domain/date-from/date-to   <br/>
		Pipelines: redrotate / rcmailsv.com / mailsvrc.com   <br/>
		domain: all / gmail.com / yahoo.com / hotmail.com etc   <br/>
		date-from/date-to: 20150518 / 20150524  etc.  <br/>
		<b>Function to show pipeline-based($vmta) mail-domain($d) specific weekly ($dtfrom is excluded while $dtto date is included) sent, delivered, opened, bounced, unsubscribed, spam counts:</b><br/><br/>".
		'<table cellspacing="0" cellpadding="4" border="1"><tr><th>User</th><th>Pipeline</th><th>Domain</th><th>Sent</th><th>Delivered</th><th>Read</th><th>Unsubscribes</th><th>Spams</th><th>Bounce</th></tr>';
		$uname = '';
			$netSent = 0;	$netDelivered = 0;	$netOpened = 0;	$netUnsub = 0;	$netComplaint = 0;	$netBounced = 0;
		if($rs_campaign_data->num_rows()>0){
		
			foreach($rs_campaign_data->result() as $row){
					$vmta = $vmta;				 			
					$uid 	=  $row->member_id;
					$uname 	=  $row->member_username;
					$sent 	=  $row->total_sent;
					$delivered 	=  $row->total_delivered;
					$opened 	=  $row->total_opened;					
					$unsubscribes 	=  $row->total_unsubscribed;
					$spams 		=  $row->total_complaint;
					$bounces 	=  $row->total_bounced;	
					$tblStats .= "<tr><td>$uname - $uid</td><td>$vmta</td><td>$d</td><td>$sent</td><td>$delivered</td><td>$opened</td><td>$unsubscribes</td><td>$spams</td><td>$bounces</td></tr>";
					$netSent += $sent;	$netDelivered += $delivered;	$netOpened += $opened;	$netUnsub += $unsubscribes;	$netComplaint += $spams;	$netBounced += $bounces;	
			}
		}	
		$tblStats .= "<tr><th>Grand Total</th><th>$vmta</th><th>$netSent</th><th>$netDelivered</th><th>$netOpened</th><th>$netUnsub</th><th>$netComplaint</th><th>$netBounced</th></tr>";
		$tblStats .= '</table></div>';
		$rs_campaign_data->free_result();		 		
		echo $tblStats;		
	}
	
	// Paid user's latest 5 campaign's stats
	function sumit6(){
		$arrMid = array(368,1436,2019,2060,2316,2595,2719,3197,3215,3252,3333,3352,3382,3439,3487,3489,3588,3740,3870,4065,4252,4319,4486,4666,4919,5109,5180,5385,5582,5900,5928,6028,6049,6375,6475,6571,6590);
		
		
		$tblStats = '<div style="padding:20px;"><table cellspacing="0" cellpadding="4" border="1">
				<tr><th>User</th><th>Campaign-id</th><th>Send-dt</th><th>Sent</th><th>Delivered</th><th>Opened</th><th>Unsubscribed</th><th>Spam</th><th>Bounces</th></tr>';
		
		foreach($arrMid as $mid){
			$thisUsername = $this->db->query("select member_username from red_members where member_id ='$mid'")->row()->member_username;
			$rsCampaignDetail = $this->db->query("select campaign_id,email_send_date from red_email_campaigns where campaign_created_by ='$mid' and campaign_status='active' order by email_send_date desc limit 5");			
			if($rsCampaignDetail->num_rows() > 0){
				foreach($rsCampaignDetail->result() as $row){
					$cid = $row->campaign_id;
					$dt = $row->email_send_date;
					$thisCampaignSent =$this->Emailreport_Model->get_emailreport_count(array('campaign_id'=>$cid));
					 
					 
					$rsCampaignStats = $this->db->query("select email_track_delivered,email_track_read,email_track_unsubscribes,email_track_spam,email_track_bounce from red_email_campaigns_scheduled where campaign_id ='$cid'");						
						$d = $rsCampaignStats->row()->email_track_delivered;
						$o = $rsCampaignStats->row()->email_track_read;
						$u = $rsCampaignStats->row()->email_track_unsubscribes;
						$s = $rsCampaignStats->row()->email_track_spam;
						$b = $rsCampaignStats->row()->email_track_bounce;
						$tblStats .= "<tr><td>$thisUsername</td><td>$cid</td><td>$dt</td><td>$thisCampaignSent</td><td>$d</td><td>$o</td><td>$u</td><td>$s</td><td>$b</td></tr>";	
					$rsCampaignStats->free_result(); 	
				}
			}
			$rsCampaignDetail->free_result(); 	
			
		} 
		$tblStats .= '</table></div>';
		
		echo $tblStats;
		
	}
	
	// Monthly Paid user's list 
	function sumit7($dt=0){
		$dt = intval($dt);
		if($dt <= 0)	$dt = date('Ym');
		$paidMonthlyUsers ="Select t2.member_id,t2.member_username,t1.package_id, date_format(t1.transaction_date,'%Y-%m-%d')dt from red_member_transactions t1 inner join red_members t2 on t1.user_id=t2.member_id where gateway='AUTHORIZE' and t1.status='SUCCESS' and date_format(t1.transaction_date,'%Y%m') = '$dt' order by t2.member_id";
		$rsMonthlyUsers = $this->db->query($paidMonthlyUsers);
		 
		$tblStats = '<div style="padding:20px;">'.
		'<b>URL: sumit7/YearMonth   <br/>
		Example: sumit7/201509 <br/>
		<table cellspacing="0" cellpadding="4" border="1">
				<tr><th>Sl.No.</th><th>UserId</th><th>Username</th><th>Transaction-dt</th><th>Package</th></tr>';
		
		if($rsMonthlyUsers->num_rows() > 0){
		$i = 1;
				foreach($rsMonthlyUsers->result() as $row){
					$thisUsername = $row->member_username ;
					$thisUserId = $row->member_id ;
					$thisTransactionDt = $row->dt;
					$thisPackage = $row->package_id;
					$tblStats .= "<tr><td>".$i++."</td><td>$thisUserId</td><td>$thisUsername</td><td>$thisTransactionDt</td><td>$thisPackage</td></tr>";						 	
				}
		}
		$rsMonthlyUsers->free_result(); 	
		 
		$tblStats .= '</table></div>';
		
		echo $tblStats;
		
	}
	
	// Monthly Paid user's list 
	function sumit8(){
		
		$paidUsers ="Select t2.member_id,t1.member_username from red_members t1 inner join red_member_packages t2 on t1.member_id=t2.member_id where t2.package_id > 0 and t2.next_payement_date > now() order by t2.member_id";
		$rsPaidUsers = $this->db->query($paidUsers);
		 
		$tblStats = '<div style="padding:20px;">'.
		
		'<table cellspacing="0" cellpadding="4" border="1">
				<tr><th>Sl.No.</th><th>UserId</th><th>Username</th><th>AOL</th><th>AOL-Bekaar</th><th>Yahoo</th><th>Yahoo-Bekaar</th></tr>';
		
		if($rsPaidUsers->num_rows() > 0){
		$i = 1;
				foreach($rsPaidUsers->result() as $row){
					$thisUsername = $row->member_username ;
					$thisUserId = $row->member_id ;
					$rsTotalAOL = $this->db->query("Select count(subscriber_id) tot from red_email_subscribers where subscriber_created_by='$thisUserId' and subscriber_status=1 and is_deleted=0 and subscriber_email_address like'%@aol.com'");
					$totalAOL	= $rsTotalAOL->row()->tot;
					$rsTotalAOL->free_result();
					
					$rsUnresponsiveAOL = $this->db->query("Select count(subscriber_id) tot from red_email_subscribers where subscriber_created_by='$thisUserId' and subscriber_status=1 and is_deleted=0 and `read` = 0 and subscriber_email_address like'%@aol.com'");
					$unresponsiveAOL	= $rsUnresponsiveAOL->row()->tot;
					$rsUnresponsiveAOL->free_result();
					
					$rsTotalYahoo = $this->db->query("Select count(subscriber_id) tot from red_email_subscribers where subscriber_created_by='$thisUserId' and subscriber_status=1 and is_deleted=0 and subscriber_email_address like'%@yahoo.%'");
					$totalYahoo	= $rsTotalYahoo->row()->tot;
					$rsTotalYahoo->free_result();
					
					$rsUnresponsiveYahoo = $this->db->query("Select count(subscriber_id) tot from red_email_subscribers where subscriber_created_by='$thisUserId' and subscriber_status=1 and is_deleted=0 and `read` = 0  and subscriber_email_address like'%@yahoo.%'");
					$unresponsiveYahoo	= $rsUnresponsiveYahoo->row()->tot;
					$rsUnresponsiveYahoo->free_result();
					
					$tblStats .= "<tr><td>".$i++."</td><td>$thisUserId</td><td>$thisUsername</td><td>$totalAOL</td><td>$unresponsiveAOL</td><td>$totalYahoo</td><td>$unresponsiveYahoo</td></tr>";						 	
				}
		}
		$rsPaidUsers->free_result(); 	
		 
		$tblStats .= '</table></div>';
		
		echo $tblStats;
		
	}
	
	// Get New/Unique contacts from any contact
	function getNewContacts($uid =0){
		if($uid > 0){
		$rsContacts = $this->db->query("select subscriber_id, subscriber_email_address from red_email_subscribers where subscriber_created_by='$uid' and subscriber_status=1 and is_deleted=0");
		foreach($rsContacts->result() as $rowContact){
			$eml = $rowContact->subscriber_email_address;
			$sid = $rowContact->subscriber_id;
			$rsCompareEml = $this->db->query("select subscriber_id from red_email_subscribers where subscriber_created_by !='$uid' and subscriber_email_address='$eml'");
			if($rsCompareEml->num_rows() < 1){
				echo $eml . "<br/>";
				$this->db->query("insert into red_email_subscription_subscriber(subscription_id,subscriber_id)  values(7694, '$sid')");
			}
			$rsCompareEml->free_result();
		}
		$rsContacts->free_result();
		}
	}
	
	function sent_autoresponders($start=0){	 
		
		# Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'webmaster/dashboard_stat/sent_autoresponders';
		$config['total_rows']=$this->Campaigns_Model->get_autoresponder_count(array( 'rec.is_deleted'=>0 ),true);
		
		$config['per_page']=25;
		$config['uri_segment']=4;
		# Initialize paging with above parameters
		$this->pagination->initialize($config);
		
		#Create paging inks
		$paging_links=$this->pagination->create_links();
		$sort_by = (isset($_POST['sort_by']) and trim($_POST['sort_by']) !='')?$_POST['sort_by']:'campaign_sheduled';
		# Fetches campaign data from database
		$campaign_data=$this->Campaigns_Model->get_autoresponder_data_for_sentmails(array( 'rec.is_deleted'=>0, 'rm.is_deleted'=>0),$sort_by,$config['per_page'],$start);
		//echo $this->db->last_query();
		 
		#Fetch stat of campaigns
		foreach($campaign_data as $campaign){
			// common queries
			$querySent = "select count(autoresponder_scheduled_id) totrec from `red_autoresponder_signup` where `autoresponder_scheduled_id` = '".$campaign['autoresponder_scheduled_id']."'";
			$queryRead = "select count(autoresponder_scheduled_id) totrec from `red_autoresponder_signup` where `autoresponder_scheduled_id` = '".$campaign['autoresponder_scheduled_id']."' and `email_track_read` > 0";
			$queryClicks = "select count(autoresponder_scheduled_id) totrec from `red_autoresponder_signup` where `autoresponder_scheduled_id` = '".$campaign['autoresponder_scheduled_id']."' and `email_track_click` > 0";
			$queryForwards = "select count(autoresponder_scheduled_id) totrec from `red_autoresponder_signup` where `autoresponder_scheduled_id` = '".$campaign['autoresponder_scheduled_id']."' and `email_track_forward` > 0";
			$queryUnsubscribes = "select count(autoresponder_scheduled_id) totrec from `red_autoresponder_signup` where `autoresponder_scheduled_id` = '".$campaign['autoresponder_scheduled_id']."' and `email_track_unsubscribes` > 0";
			$queryBounce = "select count(autoresponder_scheduled_id) totrec from `red_autoresponder_signup` where `autoresponder_scheduled_id` = '".$campaign['autoresponder_scheduled_id']."' and `email_track_bounce` > 0";
			$queryComplaints = "select count(autoresponder_scheduled_id) totrec from `red_autoresponder_signup` where `autoresponder_scheduled_id` = '".$campaign['autoresponder_scheduled_id']."' and `email_track_complaint` > 0";
			
			// Get Total number of delivered mail 			
			$total_sent_emails= $this->db->query($querySent)->row()->totrec;			
			$total_read_emails= $this->db->query($queryRead)->row()->totrec;		
			$total_click_emails= $this->db->query($queryClicks)->row()->totrec;			
			$total_forwards= $this->db->query($queryForwards)->row()->totrec;			
			$total_unsubscribes= $this->db->query($queryUnsubscribes)->row()->totrec;			
			$total_bounce= $this->db->query($queryBounce)->row()->totrec;			
			$total_complaints= $this->db->query($queryComplaints)->row()->totrec;			
			 
			// stats for today
			$todayDateClause = " and email_receive_date is not null and date_format(email_receive_date, '%Y-%m-%d')= DATE_ADD(CURDATE(), INTERVAL (0) DAY) "; 			
			$today_sent_emails		= $this->db->query($querySent .$todayDateClause)->row()->totrec;			
			$today_read_emails		= $this->db->query($queryRead .$todayDateClause)->row()->totrec;		
			$today_click_emails		= $this->db->query($queryClicks .$todayDateClause)->row()->totrec;			
			$today_forward_emails	= $this->db->query($queryForwards .$todayDateClause)->row()->totrec;			
			$today_unsubscribes		= $this->db->query($queryUnsubscribes .$todayDateClause)->row()->totrec;			
			$today_bounce			= $this->db->query($queryBounce .$todayDateClause)->row()->totrec;			
			$today_complaint_emails	= $this->db->query($queryComplaints .$todayDateClause)->row()->totrec; 
			// stats for Week
			$weekDateClause = " and email_receive_date is not null and date_format(email_receive_date, '%Y-%m-%d') >  DATE_ADD(CURDATE(), INTERVAL (-7) DAY) ";			
			$week_sent_emails= $this->db->query($querySent .$weekDateClause)->row()->totrec;			
			$week_read_emails= $this->db->query($queryRead .$weekDateClause)->row()->totrec;		
			$week_click_emails= $this->db->query($queryClicks .$weekDateClause)->row()->totrec;			
			$week_forwards= $this->db->query($queryForwards .$weekDateClause)->row()->totrec;			
			$week_unsubscribes= $this->db->query($queryUnsubscribes .$weekDateClause)->row()->totrec;			
			$week_bounce= $this->db->query($queryBounce .$weekDateClause)->row()->totrec;			
			$week_complaints= $this->db->query($queryComplaints .$weekDateClause)->row()->totrec;
			
			// collect values in array for email report view						 	
			$emailreport_data[$campaign['campaign_id']]['autoresponder_scheduled_id']=$campaign['autoresponder_scheduled_id']; 
			$emailreport_data[$campaign['campaign_id']]['campaign_title']=$campaign['campaign_title']; 
			$emailreport_data[$campaign['campaign_id']]['email_send_date']=$campaign['campaign_sheduled']; 
			$emailreport_data[$campaign['campaign_id']]['campaign_status']=$campaign['campaign_status']; 
			
			$emailreport_data[$campaign['campaign_id']]['total_sent_emails']=$total_sent_emails; 
			$emailreport_data[$campaign['campaign_id']]['total_read_emails']=$total_read_emails;
			$emailreport_data[$campaign['campaign_id']]['total_unread_emails']=$total_sent_emails - $total_read_emails - $total_bounce; // total unread emails			
			$emailreport_data[$campaign['campaign_id']]['total_click_emails']=$total_click_emails;
			$emailreport_data[$campaign['campaign_id']]['email_track_forward']=$total_forwards;
			$emailreport_data[$campaign['campaign_id']]['total_unsubscribes']=$total_unsubscribes;
			$emailreport_data[$campaign['campaign_id']]['email_track_bounce']=$total_bounce;
			$emailreport_data[$campaign['campaign_id']]['total_complaint_emails']=$total_complaints;
			// today's data
			$emailreport_data[$campaign['campaign_id']]['today_sent_emails']=$today_sent_emails; 
			$emailreport_data[$campaign['campaign_id']]['today_read_emails']=$today_read_emails;
			$emailreport_data[$campaign['campaign_id']]['today_unread_emails']=$today_sent_emails - $today_read_emails - $today_bounce; // total unread emails			
			$emailreport_data[$campaign['campaign_id']]['today_click_emails']=$today_click_emails;
			$emailreport_data[$campaign['campaign_id']]['today_forward_emails']=$today_forward_emails;
			$emailreport_data[$campaign['campaign_id']]['today_unsubscribes']=$today_unsubscribes;
			$emailreport_data[$campaign['campaign_id']]['today_bounce']=$today_bounce;
			$emailreport_data[$campaign['campaign_id']]['today_complaint_emails']=$today_complaint_emails;
			// Week's data
			$emailreport_data[$campaign['campaign_id']]['week_sent_emails']=$week_sent_emails; 
			$emailreport_data[$campaign['campaign_id']]['week_read_emails']=$week_read_emails;
			$emailreport_data[$campaign['campaign_id']]['week_unread_emails']=$week_sent_emails - $week_read_emails - $week_bounce; // total unread emails			
			$emailreport_data[$campaign['campaign_id']]['week_click_emails']=$week_click_emails;
			$emailreport_data[$campaign['campaign_id']]['week_forwards']=$week_forwards;
			$emailreport_data[$campaign['campaign_id']]['week_unsubscribes']=$week_unsubscribes;
			$emailreport_data[$campaign['campaign_id']]['week_bounce']=$week_bounce;
			$emailreport_data[$campaign['campaign_id']]['week_complaints']=$week_complaints;
			
			$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$campaign['campaign_created_by']));
			$emailreport_data[$campaign['campaign_id']]['member_username']=$user_data_array[0]['member_username'];
			$emailreport_data[$campaign['campaign_id']]['member_id']=$user_data_array[0]['member_id'];
		}		 
 		
		if($_POST['mode']=='search' AND !isset($_POST['btn_cancel'])){
			$contacts_array['username']=$_POST['username'];	 		 	
		}
		
		 
		$logo_link="webmaster/dashboard_stat";
		#Get shoreten url 
		$shorten_url=get_shorten_url();
		#Loads header, users edit  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Sent Campaign List','logo_link'=>$logo_link));
		$this->load->view('webmaster/sent_autoresponder_stats',array('emailreport_data'=>$emailreport_data,'paging_links'=>$paging_links,'contacts_array'=>$contacts_array,'shorten_url'=>$shorten_url));
		$this->load->view('webmaster/footer');
	}
	function auto_ipr($aid=0){	
		$whereClause ='';
		 
		$sqlTotalForCampaign = "select count(autoresponder_scheduled_id) sent, sum(email_track_read) opened,sum(email_track_click) clicks, sum(email_track_unsubscribes) unsubscribes, sum(email_track_bounce) bounces, sum(email_track_complaint) complaints, sum(email_track_forward) forwards from `red_autoresponder_signup` where `autoresponder_scheduled_id` = '$aid' order by sent";
		$rsTotalForCampaign = $this->db->query($sqlTotalForCampaign);
		if($rsTotalForCampaign->num_rows()>0){
			//$intTotalSent = $rsTotalForCampaign->row()->sent;
			$intTotalSent = $rsTotalForCampaign->row()->sent;
		$strTblFooter = '<tr><td>TOTAL:</td><td>'.$rsTotalForCampaign->row()->sent.'</td><td>'.$rsTotalForCampaign->row()->opened.'</td><td>'.$rsTotalForCampaign->row()->bounced.'</td><td>'.$rsTotalForCampaign->row()->complaints.'</td><td>'.$rsTotalForCampaign->row()->unsubscribes.'</td></tr>';
		
		}
		 // domain wise counter
		$sqlDomainwiseForCampaign ="select count(autoresponder_scheduled_id) sent, sum(email_track_read) opened,sum(email_track_click) clicks, sum(email_track_unsubscribes) unsubscribes, sum(email_track_bounce) bounces, sum(email_track_complaint) complaints, sum(email_track_forward) forwards, SUBSTRING(subscriber_email, instr(subscriber_email, '@'), LENGTH(subscriber_email)) AS domainname from red_autoresponder_signup where `autoresponder_scheduled_id` = '$aid' group by domainname having count(autoresponder_scheduled_id)>5 order by sent desc";
		
		$rsDomainwiseForCampaign = $this->db->query($sqlDomainwiseForCampaign);
		if($rsDomainwiseForCampaign->num_rows()>0){
			foreach($rsDomainwiseForCampaign->result() as $row){
				$strTblBody .= '<tr><td>'.$row->domainname.'</td>';
				$strTblBody .= '<td>'.$row->sent.'('.number_format(($row->sent)*100/$intTotalSent,2).'%)</td>';				
				$strTblBody .= '<td>'.$row->opened.'('.number_format(($row->opened)*100/($row->sent),2).'%)</td>';
				$strTblBody .= '<td>'.$row->bounces.'('.number_format(($row->bounces)*100/($row->sent),2).'%)</td>';
				$strTblBody .= '<td>'.$row->complaints.'('.number_format(($row->complaints)*100/($row->sent),2).'%)</td>';
				$strTblBody .= '<td>'.$row->unsubscribes.'('.number_format(($row->unsubscribes)*100/($row->sent),2).'%)</td></tr>';		
			}
		}		  
		echo '<div style="padding:20px;"><table cellspacing="0" cellpadding="4" border="1"><tr><th>Domain</th><th>Sent</th><th>Opened</th><th>Bounced</th><th>Complaint</th><th>Unsubscribed</th></tr>' .$strTblBody . $strTblFooter.'</table></div>';		
	}
	function sort_td_array($arr,$element_key,$reverse=false){
		foreach($arr as $key=>$value){
			$element_arr[$key]=$value[$element_key];
		}
		if($reverse){
			arsort($element_arr);
		}else{
			asort($element_arr);
		}
		foreach($element_arr as $key=>$value){
			$sorted_array[$key]=$arr[$key];
		}
	return $sorted_array;
	}
	
}
?>