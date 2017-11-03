<?php
// error_reporting(E_ALL ^ E_NOTICE);
class Contacts_segmentation extends CI_Controller
{
	private $package_id ; 
	private $dateBefore ; 
	function __construct(){
		parent::__construct();	
 
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');
		// Load the user model which interact with database
		$this->load->model('UserModel');
		$this->load->model('Affiliate_Model');
		$this->load->model('newsletter/Subscriber_Model');
		$this->load->model('newsletter/Subscription_Model');
		$this->load->model('newsletter/contact_model');
		
		 
		force_ssl();
	}
	
	function index($member_id=0){
		if(intval($member_id) <= 0){
			redirect('webmaster/users_manage/users_list/');
			exit;
		}
		$contactLists = $this->Subscription_Model->get_subscription_data(array('subscription_created_by'=>$member_id, 'subscription_status'=>1, 'is_deleted'=>0));
		for($i=0;$i<count($contactLists);$i++){
		$strOptions .= "<option value='{$contactLists[$i]['subscription_id']}'>{$contactLists[$i]['subscription_title']}</option>";
			if($contactLists[$i]['subscription_id'] > 0){
			$fetch_condiotions_array=array('res.subscriber_created_by'=>$member_id,'res.subscriber_status'=>1,'res.is_deleted'=>0);						
			$contactLists[$i]['total_contact_in_list']= $this->contact_model->get_contacts_count_in_list($fetch_condiotions_array,$contactLists[$i]['subscription_id']);
			}else{
			$contactLists[$i]['total_contact_in_list']= $this->contact_model->getContactsCount(array('res.subscriber_created_by'=>$member_id,'res.subscriber_status'=>1,'res.is_deleted'=>0));				
			}
		}
		 
		
		$user_data_array=$this->UserModel->get_user_data(array('rm.member_id'=>$member_id));
				  
		//Loads header, users listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Users','logo_link'=>'webmaster/dashboard_stat'));
		$this->load->view('webmaster/contacts_segmentation',array('users'=>$user_data_array, 'contact_list'=>$contactLists));
		$this->load->view('webmaster/footer');
	}
	
	function filter($member_id=0){
		if(intval($member_id) <= 0){
			redirect('webmaster/users_manage/users_list/');
			exit;
		}
		$contactLists = $this->Subscription_Model->get_subscription_data(array('subscription_created_by'=>$member_id, 'subscription_status'=>1, 'is_deleted'=>0));
		for($i=0;$i<count($contactLists);$i++){
		$strOptions .= "<option value='{$contactLists[$i]['subscription_id']}'>{$contactLists[$i]['subscription_title']}</option>";
			if($contactLists[$i]['subscription_id'] > 0){
			$fetch_condiotions_array=array('res.subscriber_created_by'=>$member_id,'res.subscriber_status'=>1,'res.is_deleted'=>0);						
			$contactLists[$i]['total_contact_in_list']= $this->contact_model->get_contacts_count_in_list($fetch_condiotions_array,$contactLists[$i]['subscription_id']);
			}else{
			$contactLists[$i]['total_contact_in_list']= $this->contact_model->getContactsCount(array('res.subscriber_created_by'=>$member_id,'res.subscriber_status'=>1,'res.is_deleted'=>0));				
			}
		}
		 
		
		$user_data_array=$this->UserModel->get_user_data(array('rm.member_id'=>$member_id));
				  
		//Loads header, users listing  and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Users','logo_link'=>'webmaster/dashboard_stat'));
		$this->load->view('webmaster/contacts_filter',array('users'=>$user_data_array, 'contact_list'=>$contactLists));
		$this->load->view('webmaster/footer');
	}
	function importContacts($mid=0){		 
		$messages=$this->messages->get();	
		$this->session->set_userdata('member_id', $mid);	
		$fetch_condiotions_array=array('subscription_created_by'=>$mid,'is_deleted'=>0	);		 
		// Fetches subscription data from database		
		$subscription_data['select_subscriptions']=$this->Subscription_Model->get_subscription_data($fetch_condiotions_array);		 
		/**
		 * Fetch user data 
		 */
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$mid));
		$subscription_data['extra']=$user_data_array[0];
		//Loads header, subscription and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Users','logo_link'=>'webmaster/dashboard_stat'));
		$this->load->view('webmaster/contacts_add',$subscription_data);
		$this->load->view('webmaster/footer');
	
	}
	function addDNM($mid=0){		 
		$messages=$this->messages->get();	
		$this->session->set_userdata('member_id', $mid);	
		$fetch_condiotions_array=array('subscription_created_by'=>$mid,'is_deleted'=>0	);		 
		// Fetches subscription data from database		
		$subscription_data['select_subscriptions']=$this->Subscription_Model->get_subscription_data($fetch_condiotions_array);		 
		/**
		 * Fetch user data 
		 */
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$mid));		 
		$subscription_data['extra']=$user_data_array[0];
		//Loads header, subscription and footer view.
		$this->load->view('webmaster/header',array('title'=>'Manage Users','logo_link'=>'webmaster/dashboard_stat'));
		$this->load->view('webmaster/addDNM',$subscription_data);
		$this->load->view('webmaster/footer');
	
	}
	
	function addDNMSubmit(){
		$mid = $this->input->post('mid');
		$dnmContacts = $this->input->post('addDNM');
		$arrDNMContacts = explode(',',$dnmContacts);
		
		$c = 0;
		foreach($arrDNMContacts as $e){
			$e = trim($e);			
			if($this->is_authorized->ValidateAddress($e)){			
				$this->db->query("INSERT INTO red_email_subscribers SET subscriber_email_address='".mysql_real_escape_string($e) ."', subscriber_created_by = '$mid' ON DUPLICATE KEY UPDATE  is_deleted = 0,subscriber_status=5 ");							
				$c++;
			}		
		}
		echo $this->load->view('webmaster/header',array('title'=>'Manage Users','logo_link'=>'webmaster/dashboard_stat'), true);
		echo "<p>".$c. " contacts added to DNM </p>";
		echo $this->load->view('webmaster/footer',null,true);
		
	}
	
	function get_contacts_count(){
	ini_set('memory_limit', '-1');
	set_time_limit(0); 
	//print_r($_POST);
	
		$mid = $this->input->post('hidMemberId');
		$whereClause = '';
		$whereClause .= (trim($this->input->post('subscriber_status')) ==1)?" and subscriber_status=1":" and subscriber_status != 1";
		$sentCounter = $this->input->post('sent_counter');
		$whereClause .= (trim($sentCounter) !='')?" and sent >= $sentCounter ":'';
		$whereClause .= (($this->input->post('added_on_after')) !='')?" and subscriber_date_added >= '".$this->input->post('added_on_after')." 23:59:59'":'';
		$whereClause .= (($this->input->post('added_on_before')) !='')?" and subscriber_date_added <= '".$this->input->post('added_on_before')." 23:59:59'":'';
		 
		$whereClause .= (trim($this->input->post('search_key')) !='')?" and  subscriber_email_address like '%".trim($this->input->post('search_key'))."%'":'';
		
		/* 
		if(trim($this->input->post('is_responsive')) =='y')
		$whereClause .= " and s.subscriber_id in(select distinct subscriber_id from red_email_track where user_id='$mid' and email_track_read=1)";
		elseif(trim($this->input->post('is_responsive')) =='n')
		$whereClause .= " and s.subscriber_id not in(select distinct subscriber_id from red_email_track where user_id='$mid' and email_track_read=1)";
		 */
		if(trim($this->input->post('is_responsive')) =='y')
		$whereClause .= " and (s.`read`+s.clicked+s.forwarded) > 0";
		elseif(trim($this->input->post('is_responsive')) =='n')
		$whereClause .= " and (s.`read`+s.clicked+s.forwarded) = 0";
		
		$listId = $this->input->post('contact_lists');
		if($this->input->post('contact_lists') >0){		
		$sqlTotalContacts = "Select count(`s`.`subscriber_id`) as totcontacts from `red_email_subscribers` as s INNER JOIN `red_email_subscription_subscriber` as ss ON s.subscriber_id=ss.subscriber_id where ss.subscription_id='".$listId."' and s.subscriber_created_by='$mid' and s.is_deleted=0 $whereClause";
		}else{
		$sqlTotalContacts = "Select count(`s`.`subscriber_id`) as totcontacts from `red_email_subscribers` as s where subscriber_created_by='$mid' and is_deleted=0 $whereClause";
		}
		// Query is kept in session
		$this->session->set_userdata('filter_query', $sqlTotalContacts); 
		// echo $sqlTotalContacts;
		$rsContacts = $this->db->query($sqlTotalContacts);
		//echo $this->db->last_query();
		$totalContacts = $rsContacts->row()->totcontacts;		
		$rsContacts->free_result();
		$moveToDpd = '';
		$copyToDpd = '';
		if($totalContacts > 0){
			$listOfContacts = $this->Subscription_Model->get_subscription_data(array('subscription_created_by'=>$mid, 'subscription_status'=>1, 'is_deleted'=>0));
			$listOption ='';
			 
			for($i=0; $i < count($listOfContacts); $i++){
				$thisLid = $listOfContacts[$i]['subscription_id'];
				$thisLname = $listOfContacts[$i]['subscription_title'];
				if($thisLid > 0 and  $thisLid != $listId){
					$listOption .= "<option value='$thisLid'>$thisLname</option>";
				}
			}
			
			$moveToDpd .= "<tr><th>Act for contacts: </th><td><input name='txtLimit' id='txtLimit' value='$totalContacts' /> out of $totalContacts</td></tr>";
			$moveToDpd .= "<tr><th>Move To: </th><td><select name='dpdMoveTo' id='dpdMoveTo' onchange='javascript:movelist();'><option value=''>Change to move</option>";
			if($listId > 0)	$moveToDpd .= "$listOption";				
			$moveToDpd .= "<option value='dnm'>DNM</option></select></td></tr>";
			
			$copyToDpd = " <tr><th>Copy To: </th><td><select name='dpdCopyTo' id='dpdCopyTo' onchange='javascript:copylist();'><option value=''>Change to copy</option>$listOption</select></td></tr>";	
			
			
		
		}
		$delete_button = "<input type='button' id='deleteContacts' name='deleteContacts' value= 'Delete Permanently' onclick='javascript:deletecontacts();' />";
		$delete_from_list_button = "<input type='button' id='deleteFromList' name='deleteFromList' value= 'Delete From List' onclick='javascript:deletefromlist();' />";
		$export_button = "<input type='button' id='exportToCsv' name='exportToCsv' value= 'Export to CSV' onclick='javascript:exportit();' />";
		$mark_unresponsive = "<input type='button' id='mark_unresponsive' name='mark_unresponsive' value= 'Mark Unresponsive' onclick='javascript:fn_mark_unresponsive();' />";
		
		$chart_button = "<input type='button' id='chart_button' name='chart_button' value= 'Report of Grade' onclick='javascript:reportgrade();' />";
		$batch_button = "<input type='button' id='batch_button' name='batch_button' value= 'Report of Batch' onclick='javascript:batchgrade();' />";
		
		
		echo "<div class='tblheading'>Result  [<a href='/webmaster/contacts_segmentation/index/$mid'>Filter again</a>] </div><table class='tbl_listing' width='100%'><tr><th>Total Contacts: </th><td>
		<table style='width:70%;border:0;padding:0;margin:0;'><tr><td style='border:0;padding:0;margin:0;'>$totalContacts</td><td style='border:0;padding:0;margin:0;'>$export_button $delete_button $delete_from_list_button $mark_unresponsive $chart_button $batch_button</td></tr></table>		
		</td></tr> $moveToDpd $copyToDpd </table> ";
	
	}
	
	function get_contacts_filter_count(){
	ini_set('memory_limit', '-1');
	set_time_limit(0); 
	// print_r($_POST);
	
		$mid = $this->input->post('hidMemberId');
		$whereClause = '';
		$whereClauseEmail = '';
		if(trim($this->input->post('search_key')) !=''){
			$strEmailOperator = 	trim($this->input->post('email_operator'));
			$arrEmails = explode(',', $this->input->post('search_key'));
			foreach($arrEmails as $strEmailToken){
				$strEmailToken = trim($strEmailToken);
				if(substr($strEmailOperator,0,3)=='not' ){
				$whereClauseEmail .= ($whereClauseEmail != '')? " and subscriber_email_address $strEmailOperator '%{$strEmailToken}%' ": " subscriber_email_address $strEmailOperator '%{$strEmailToken}%' ";
				}else{
				$whereClauseEmail .= ($whereClauseEmail != '')? " or subscriber_email_address $strEmailOperator '%{$strEmailToken}%' ": " subscriber_email_address $strEmailOperator '%{$strEmailToken}%' ";				
				}			
			}
		}
		if(array_key_exists('grade',$_POST)){
			$gradeImplode = implode("','",$_POST['grade']);
			$gradeImplodefinal = "'".$gradeImplode."'";
			$whereClause .= 'and dv_grade IN ('.$gradeImplodefinal.')';
		}
		if($whereClauseEmail !='')
		$whereClause .= ' and ('. $whereClauseEmail . ')';
		
		$subscriber_status_operator = $this->input->post('subscriber_status_operator'); 
		$subscriber_status = $this->input->post('subscriber_status'); 
		if($subscriber_status == ''){
			$whereClause .= " and is_deleted = 1 ";
		}elseif($subscriber_status == '-999'){
			$whereClause .= " and is_deleted = 1 ";
		}else{
			$whereClause .= " and is_deleted = 0 and subscriber_status $subscriber_status_operator $subscriber_status";
		}
		if(trim($this->input->post('is_signup')) != '')
		$whereClause .= " and is_signup = '".trim($this->input->post('is_signup'))."' ";
		
		if(trim($this->input->post('sent_operator')) != ''){
		$sentCounter = $this->input->post('sent_counter');
		$whereClause .= " and sent ".trim($this->input->post('sent_operator'))." $sentCounter ";
		}
		/*if(trim($this->input->post('delivered_operator')) != ''){
		$deliveredCounter = $this->input->post('delivered_counter');
		$whereClause .= " and sent ".trim($this->input->post('delivered_operator'))." $deliveredCounter ";
		}*/
		
		if(trim($this->input->post('is_responsive')) =='y')
		$whereClause .= " and s.`read` > 0";
		elseif(trim($this->input->post('is_responsive')) =='n')
		$whereClause .= " and s.`read` = 0";
		
		if(trim($this->input->post('clicked_operator')) != ''){
		$clickedCounter = $this->input->post('clicked_counter');
		$whereClause .= " and clicked ".trim($this->input->post('clicked_operator'))." $clickedCounter ";
		}
		
		$subscriber_date_added_operator = $this->input->post('subscriber_date_added_operator');
		if($subscriber_date_added_operator != '')
		$whereClause .= " and str_to_date(subscriber_date_added,'%Y-%m-%d') $subscriber_date_added_operator '".$this->input->post('subscriber_date_added')."'";
		
		$last_sent_date_operator = $this->input->post('last_sent_date_operator');
		if($last_sent_date_operator != '')
		$whereClause .= " and str_to_date(last_sent_date,'%Y-%m-%d') $last_sent_date_operator '".$this->input->post('last_sent_date')."'";
		
		$last_read_date_operator = $this->input->post('last_read_date_operator');
		if($last_read_date_operator != '')
		$whereClause .= " and str_to_date(last_read_date,'%Y-%m-%d') $last_read_date_operator '".$this->input->post('last_read_date')."'";
		
		$status_change_date_operator = $this->input->post('status_change_date_operator');
		if($status_change_date_operator != '')
		$whereClause .= " and str_to_date(status_change_date,'%Y-%m-%d') $status_change_date_operator '".$this->input->post('status_change_date')."'";
				
		$listId = $this->input->post('contact_lists');
		if($this->input->post('contact_lists') >0){		
		$sqlTotalContacts = "Select count(`s`.`subscriber_id`) as totcontacts from `red_email_subscribers` as s INNER JOIN `red_email_subscription_subscriber` as ss ON s.subscriber_id=ss.subscriber_id where ss.subscription_id='".$listId."' and s.subscriber_created_by='$mid' $whereClause";
		}else{
		$sqlTotalContacts = "Select count(`s`.`subscriber_id`) as totcontacts from `red_email_subscribers` as s where subscriber_created_by='$mid' $whereClause";
		}
		// Query is kept in session
		$this->session->set_userdata('filter_query', $sqlTotalContacts); 
		 echo $sqlTotalContacts;
		$rsContacts = $this->db->query($sqlTotalContacts);
		//echo $this->db->last_query();
		$totalContacts = $rsContacts->row()->totcontacts;		
		$rsContacts->free_result();
		$moveToDpd = '';
		$copyToDpd = '';
		if($totalContacts > 0){
			$listOfContacts = $this->Subscription_Model->get_subscription_data(array('subscription_created_by'=>$mid, 'subscription_status'=>1, 'is_deleted'=>0));
			$listOption ='';
			 
			for($i=0; $i < count($listOfContacts); $i++){
				$thisLid = $listOfContacts[$i]['subscription_id'];
				$thisLname = $listOfContacts[$i]['subscription_title'];
				if($thisLid > 0 and  $thisLid != $listId){
					$listOption .= "<option value='$thisLid'>$thisLname</option>";
				}
			}
			
			$moveToDpd .= "<tr><th>Act for contacts: </th><td><input name='txtLimit' id='txtLimit' value='$totalContacts' /> out of $totalContacts</td></tr>";
			$moveToDpd .= "<tr><th>Move To: </th><td><select name='dpdMoveTo' id='dpdMoveTo' onchange='javascript:movelist();'><option value=''>Change to move</option>";
			if($listId > 0)	$moveToDpd .= "$listOption";				
			$moveToDpd .= "<option value='dnm'>DNM</option></select></td></tr>";
			
			$copyToDpd = " <tr><th>Copy To: </th><td><select name='dpdCopyTo' id='dpdCopyTo' onchange='javascript:copylist();'><option value=''>Change to copy</option>$listOption</select></td></tr>";	
			$attachVMTA = " <tr><th>Attach Pipeline: </th><td><select name='vmta' id='vmta'  style='width:150px;' onchange='javascript:attachContactVmta();'><optgroup label='LiquidWeb'></optgroup>";
		 
			 
			foreach($this->config->item('lw_vmta')as $this_vmta){				 
				$attachVMTA .= "<option value='$this_vmta'>$this_vmta</option>";
			}
			$attachVMTA .=	"</optgroup><optgroup label='LiquidWeb-2'></optgroup>";
		
			
			foreach($this->config->item('lw2_vmta')as $this_vmta){			
				$attachVMTA .= "<option value='$this_vmta'>$this_vmta</option>";
			}
			$attachVMTA .= "</optgroup>	<optgroup label='LiquidWeb-3'></optgroup>";
		
			foreach($this->config->item('lw3_vmta')as $this_vmta){		
				$attachVMTA .="<option value='$this_vmta'>$this_vmta</option>";
			}
			$attachVMTA .= "</optgroup>	<optgroup label='PeakHost'></optgroup>";
		
			foreach($this->config->item('ph_vmta')as $this_vmta){
				$attachVMTA .= "<option value='$this_vmta'>$this_vmta</option>";
			}
			$attachVMTA .= "</optgroup> </select></td></tr>";				
		
		}
		$delete_button = "<input type='button' id='deleteContacts' name='deleteContacts' value= 'Delete Permanently' onclick='javascript:deletecontacts();' />";
		$delete_from_list_button = "<input type='button' id='deleteFromList' name='deleteFromList' value= 'Delete From List' onclick='javascript:deletefromlist();' />";
		$export_button = "<input type='button' id='exportToCsv' name='exportToCsv' value= 'Export to CSV' onclick='javascript:exportit();' />";
		$mark_unresponsive = "<input type='button' id='mark_unresponsive' name='mark_unresponsive' value= 'Mark Unresponsive' onclick='javascript:fn_mark_unresponsive();' />";
		
		$chart_button = "<input type='button' id='chart_button' name='chart_button' value= 'Report of Grade' onclick='javascript:reportgrade();' />";
		$batch_button = "<input type='button' id='batch_button' name='batch_button' value= 'Report of Batch' onclick='javascript:batchgrade();' />";
		
		
		echo "<div class='tblheading'>Result  [<a href='/webmaster/contacts_segmentation/filter/$mid'>Filter again</a>] </div><table class='tbl_listing' width='100%'><tr><th>Total Contacts: </th><td>
		<table style='width:70%;border:0;padding:0;margin:0;'><tr><td style='border:0;padding:0;margin:0;'>$totalContacts</td><td style='border:0;padding:0;margin:0;'>$export_button $delete_button $delete_from_list_button $mark_unresponsive $chart_button $batch_button</td></tr></table>		
		</td></tr> $moveToDpd $copyToDpd $attachVMTA</table> ";
	
	}
	/**
	 * Function Create
	 *
	 * 'Create' controller function to create new subscription list.
	 */
	function create(){
		if($this->input->post('action')=='submit'){
			
			$this->form_validation->set_rules('subscription_title', 'List Name', 'required|min_length[2]|max_length[45]|callback_title_check|trim');

			// To check form is validated true
			if($this->form_validation->run()==true){
				// Retrieve data posted in form posted by user using input class
				$input_array=array('subscription_title'=>$this->input->get_post('subscription_title', TRUE),
				'subscription_is_name'=>'1',
				'subscription_created_by'=>$this->input->get_post('mid', TRUE)
				);


				// Sends form input data to database via model object
				$subscription_id=$this->Subscription_Model->create_subscription($input_array);

				//Success message
				echo 'success:'.$subscription_id;
			}else if(validation_errors()){	//To display form  validation error
				if($this->input->get_post('subscription_title', TRUE)){
					echo 'error:'.validation_errors();
				}else{
					echo 'error:List Name is required';
				}
			}
		}
	}

	function move_contacts($fromLid, $toLid){
		$filter_query =  $this->session->userdata('filter_query');
		$limit = $this->input->post('l');
		$new_filter_query = "Select s.subscriber_id ". stristr($filter_query,'from') . " limit 0, $limit";
		$rsContacts = $this->db->query($new_filter_query);
		if($rsContacts->num_rows() > 0){
			foreach($rsContacts->result_array() as $recContacts){
				$input_array=array('subscriber_id'=>$recContacts['subscriber_id'], 'subscription_id'=>$toLid );
				$this->Subscriber_Model->replace_subscription_subscriber($input_array);
				$condition_array=array('subscriber_id'=>$recContacts['subscriber_id'],	'subscription_id'=>$fromLid);
				$this->Subscriber_Model->delete_subscription_subscriber($condition_array);
			}	
		}
		$rsContacts->free_result();
	echo 'ok';
	}
	
	function move_to_dnm($fromLid){
		$filter_query =  $this->session->userdata('filter_query');
		$limit = $this->input->post('l');
		$new_filter_query = "Select s.subscriber_id ". stristr($filter_query,'from') . " limit 0, $limit";
		$rsContacts = $this->db->query($new_filter_query);
		if($rsContacts->num_rows() > 0){
			foreach($rsContacts->result_array() as $recContacts){
				$sid = $recContacts['subscriber_id'];
				 $this->db->query("update red_email_subscribers set subscriber_status=5, status_change_date=current_timestamp() where subscriber_id='$sid' and subscriber_status =1");
			}	
		}
		$rsContacts->free_result();
	echo 'ok';
	}
	function copy_contacts($fromLid, $toLid){
		$filter_query =  $this->session->userdata('filter_query');
		$limit = $this->input->post('l');
		$new_filter_query = "Select s.subscriber_id ". stristr($filter_query,'from'). " limit 0, $limit";
		
		$rsContacts = $this->db->query($new_filter_query);
		if($rsContacts->num_rows() > 0){
			foreach($rsContacts->result_array() as $recContacts){
				$input_array=array('subscriber_id'=>$recContacts['subscriber_id'], 'subscription_id'=>$toLid );
				$this->Subscriber_Model->replace_subscription_subscriber($input_array);				 
			}	
		}
		$rsContacts->free_result();
	echo 'ok';
	}
	function attachContactVmta($vmta){
		$filter_query =  $this->session->userdata('filter_query');
		$limit = $this->input->post('l');		
		$this->db->query("update red_email_subscribers set subscriber_vmta='$vmta' ". stristr($filter_query,'where'). " limit  $limit");
		 
	echo  'ok';
	}
	
	function delete_contacts(){
		$filter_query =  $this->session->userdata('filter_query');
		$limit = $this->input->post('l');
		$new_filter_query = "Select s.subscriber_id ". stristr($filter_query,'from'). " limit 0, $limit";
		
		$rsContacts = $this->db->query($new_filter_query);
		if($rsContacts->num_rows() > 0){
			foreach($rsContacts->result_array() as $recContacts){
				$sid = $recContacts['subscriber_id'];
				$this->db->query("update red_email_subscribers set is_deleted=1, status_change_date=current_timestamp() where subscriber_id='$sid'");		 
			}	
		}
		$rsContacts->free_result();
	echo 'ok';
	}
	
	function delete_from_list($lid){
		$filter_query =  $this->session->userdata('filter_query');
		$limit = $this->input->post('l');
		$new_filter_query = "Select s.subscriber_id ". stristr($filter_query,'from'). " limit 0, $limit";
		
		$rsContacts = $this->db->query($new_filter_query);
		if($rsContacts->num_rows() > 0){
			foreach($rsContacts->result_array() as $recContacts){
				$sid = $recContacts['subscriber_id'];
				$this->db->query("delete from red_email_subscription_subscriber where subscription_id='$lid' and subscriber_id='$sid'");		 
			}	
		}
		$rsContacts->free_result();
	echo 'ok';
	}
	function mark_unresponsive(){
		$filter_query =  $this->session->userdata('filter_query');
		$limit = $this->input->post('l');
		$new_filter_query = "Select s.subscriber_id ". stristr($filter_query,'from'). " order by rand() limit 0, $limit";
		
		$rsContacts = $this->db->query($new_filter_query);
		if($rsContacts->num_rows() > 0){
			foreach($rsContacts->result_array() as $recContacts){
				$sid = $recContacts['subscriber_id'];
				$this->db->query("update red_email_subscribers set sent='100',`read`=0,clicked=0,forwarded=0 where subscriber_id='$sid' and subscriber_status=1 and is_deleted=0");		 
			}	
		}
		$rsContacts->free_result();
	echo 'ok';
	}
	/* START Code of report chart*/
	function report_grade(){
		$filter_query =  $this->session->userdata('filter_query');
		
	#	$new_filter_query =  "SELECT dv_grade,COUNT('*')AS totaluser FROM `red_email_subscribers` AS s where subscriber_created_by='8908' and is_deleted = 0 and subscriber_status = 1  GROUP BY dv_grade";
		
		$new_filter_query = "Select dv_grade,COUNT('*')AS totaluser ". stristr($filter_query,'from'). " GROUP BY dv_grade";
		
		
	#	echo $new_filter_query;exit;
		$rsContacts = $this->db->query($new_filter_query);
		if($rsContacts->num_rows() > 0){
			foreach($rsContacts->result_array() as $row)
			$subscriber_data[]=$row;
		}
		
		
		
		$gradeArray = array(
			array('Task','Hours per Day')
		);
		$i = 1;
		foreach($subscriber_data as $key => $subscriber)
		{
			$gradeArray[$i][] = $subscriber['dv_grade'];
			$gradeArray[$i][] = (int)$subscriber['totaluser'];
		$i++;
		}
		
		echo json_encode(array_values($gradeArray));exit;
		
	}
	/* END Code of report chart*/
        
        
        
        /* START Code of batch chart*/
	function batch_grade(){
		$filter_query =  $this->session->userdata('filter_query');
		
		$new_filter_query =  "SELECT COUNT(subscriber_created_by) AS total_member ,dv_csv_name FROM red_dv_cron_setup AS csv
                                      JOIN red_dv_csv AS batch ON batch.dv_rc_id = csv.rc_id
                                      JOIN red_email_subscribers AS usr ON usr.subscriber_created_by = rc_member_id and is_deleted = 0 and subscriber_status = 1
                                      WHERE csv.rc_member_id = '8909'
                                      GROUP BY dv_csv_name";
		
		$rsContacts = $this->db->query($new_filter_query);
		if($rsContacts->num_rows() > 0){
			foreach($rsContacts->result_array() as $row)
			$subscriber_data[]=$row;
		}
		
               
		
		$gradeArray = array(
			array('Task','Hours per Day')
		);
		$i = 1;
		foreach($subscriber_data as $key => $subscriber)
		{
			$gradeArray[$i][] = $subscriber['dv_csv_name'];
			$gradeArray[$i][] = (int)$subscriber['total_member'];
		$i++;
		}
		
		echo json_encode(array_values($gradeArray));exit;
		
	}
	/* END Code of Batch chart*/
        
        
        
	function export_contacts($limit=0){
		$filter_query =  $this->session->userdata('filter_query');
		
		$new_filter_query = "Select s.* ". stristr($filter_query,'from'). " limit 0, $limit";
		$rsContacts = $this->db->query($new_filter_query);
		if($rsContacts->num_rows() > 0){
			foreach($rsContacts->result_array() as $row)
			$subscriber_data[]=$row;
		}
		//Create output string  with heading
		$csv_output_header="\"First Name\",\"Last Name\",\"Email Address\",\"Address\",\"Birthday\",\"City\",\"Company\",\"Country\",\"Phone\",\"State\",\"Zip Code\"";
		//$csv_output_header.="\n";
		$csv_output="\n";
		 
		 
		$i=0;
		$header=array();
		foreach($subscriber_data as $subscriber){
			if(($subscriber['subscriber_extra_fields']!="")&&($subscriber['subscriber_extra_fields']!="b:0;")){
				foreach(unserialize($subscriber['subscriber_extra_fields']) as $col=>$val){
					if(!in_array($col,$header)){
						$csv_output_header.=",\"".$col."\"";
						$header[]=$col;
						$i++;
					}
				}
			}
		}
		//Append subscribers to csv output
		foreach($subscriber_data as $subscriber){
			$csv_output.="\"".$subscriber['subscriber_first_name']."\",\"";
			$csv_output.=$subscriber['subscriber_last_name']."\",\"";
			$csv_output.=$subscriber['subscriber_email_address']."\",\"";
			$csv_output.=$subscriber['subscriber_address']."\",\"";
			$csv_output.=$subscriber['subscriber_dob']."\",\"";
			$csv_output.=$subscriber['subscriber_city']."\",\"";
			$csv_output.=$subscriber['subscriber_company']."\",\"";
			$csv_output.=$subscriber['subscriber_country']."\",\"";
			$csv_output.=$subscriber['subscriber_phone']."\",\"";
			$csv_output.=$subscriber['subscriber_state']."\",\"";
			$csv_output.=$subscriber['subscriber_zip_code']."\",\"";
			$csv_output.=$subscriber['dv_grade']."";
			if($subscriber['subscriber_extra_fields']!=""){
				$extra_field=unserialize($subscriber['subscriber_extra_fields']);
			}else{
				$extra_field=array();
			}
			 foreach($header as $value){
				if(array_key_exists($value,$extra_field) ){
					$csv_output.="\",\"".$extra_field[$value];
				}else{
					$csv_output.="\",\"";
				}
			}
			$csv_output .= "\"\n";
		}
	 	$csv_output=$csv_output_header.$csv_output;
		
		//Create filename and send output headers
		//header("Content-type: application/vnd.ms-excel");		
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");		
		header("Content-type: text/csv");
		header("Content-disposition: attachment; filename=contacts_".date("Y-m-d_H-i",time()).".csv");	
		header("Expires: 0");		
		print $csv_output;
		exit;		
	}
	
	
	function index_0($offset=0, $psize=10){
	ini_set('memory_limit', '-1');
	set_time_limit(0); 
		$sqlMembersToTrack = "select m.member_id, m.member_username from red_members m INNER JOIN red_member_packages mp ON m.member_id=mp.member_id where mp.package_id >= '".$this->package_id."' and m.status='active' and m.is_deleted=0 limit $offset, $psize";
		$rsMembersToTrack = $this->db->query($sqlMembersToTrack);
		
		if($rsMembersToTrack ->num_rows() > 0){
			$strTable = "<p>You can put record start from and no. of records in URL as:<br/>
			https://www.redcappi.com/webmaster/contacts_unresponsive/0/10/ <br/>
			https://www.redcappi.com/webmaster/contacts_unresponsive/10/10/ <br/>
			<br/>
			by default it is <br/>
			https://www.redcappi.com/webmaster/contacts_unresponsive/0/10
			</p>".
			'<table id="table1" class="tbl_listing" width="100%">';
			$strTable .= "<tr><th>Member-ID</th><th>Member-Name</th><th>Total Contacts</th><th>Gmail</th><th>Gmail Dormant</th><th>Yahoo</th><th>Yahoo Dormant</th><th>Hotmail</th><th>Hotmail Dormant</th><th>AOL</th><th>AOL Dormant</th></tr>";
			foreach($rsMembersToTrack->result_array() as $rowMembersToTrack){
				$mem_id = $rowMembersToTrack['member_id'];
				$mem_name = $rowMembersToTrack['member_username'];
				
				$totalContats = $this->getTotalContacts($mem_id);
				
				$totalContatsGmail = $this->getTotalContacts($mem_id, 'gmail.com');
				$totalContatsYahoo = $this->getTotalContacts($mem_id, 'yahoo.com');
				$totalContatsHotmail = $this->getTotalContacts($mem_id, 'hotmail.com');
				$totalContatsAOL = $this->getTotalContacts($mem_id, 'aol.com');
				
				$totalUnresponsiveGmail = $this->getTotalUnresponsive($mem_id, 'gmail.com');
				$totalUnresponsiveYahoo = $this->getTotalUnresponsive($mem_id, 'yahoo.com');
				$totalUnresponsiveHotmail = $this->getTotalUnresponsive($mem_id, 'hotmail.com');
				$totalUnresponsiveAOL = $this->getTotalUnresponsive($mem_id, 'aol.com');
				
				
				
				$strTable .= "<tr><td>$mem_id</td><td>$mem_name</td><td>$totalContats</td><td>$totalContatsGmail</td><td>$totalUnresponsiveGmail</td><td>$totalContatsYahoo</td><td>$totalUnresponsiveYahoo</td><td>$totalContatsHotmail</td><td>$totalUnresponsiveHotmail</td><td>$totalContatsAOL</td><td>$totalUnresponsiveAOL</td></tr>";
				
			}
			$strTable .= "</table>";
			
		}	
		
		$this->load->view('webmaster/header',array('title'=>'Report unresponsive','logo_link'=>"webmaster/dashboard_stat"));
		$this->load->view('webmaster/contacts_unresponsive',array('strTable'=>$strTable));
		$this->load->view('webmaster/footer');
	}
	
	function getTotalContacts($mid, $domain=''){
		if($domain !=''){
		$sqlTotalContacts = "Select count(`subscriber_id`) as totcontacts, SUBSTRING(subscriber_email_address, instr(subscriber_email_address, '@'), LENGTH(subscriber_email_address)) AS domainname from `red_email_subscribers` where subscriber_created_by='$mid' and subscriber_status='1' and is_deleted=0 and SUBSTRING(subscriber_email_address, instr(subscriber_email_address, '@'), LENGTH(subscriber_email_address)) = '@$domain' group by domainname";
		}else{
		$sqlTotalContacts = "Select count(`subscriber_id`) as totcontacts from `red_email_subscribers` where subscriber_created_by='$mid' and subscriber_status='1' and is_deleted=0 group by subscriber_created_by";
		}
		$rsTotalContacts = $this->db->query($sqlTotalContacts);
		$intCount = $rsTotalContacts->row()->totcontacts;
		$rsTotalContacts->free_result();
		return (null == $intCount)?0: $intCount;
	
	}
	function getTotalUnresponsive($mid, $domain=''){
		if($domain !=''){
		$sqlTotalUnresponsiveContacts = "Select count(`subscriber_id`) as totcontacts from `red_email_subscribers` where subscriber_created_by='$mid' and subscriber_status='1' and is_deleted=0 and subscriber_date_added < '".$this->dateBefore."' and SUBSTRING(subscriber_email_address, instr(subscriber_email_address, '@'), LENGTH(subscriber_email_address)) = '@$domain'  and subscriber_id not in(select distinct subscriber_id from red_email_track where user_id='$mid' and email_track_read=1)";
		}else{
		$sqlTotalUnresponsiveContacts = "Select count(`subscriber_id`) as totcontacts from `red_email_subscribers` where subscriber_created_by='$mid' and subscriber_status='1' and is_deleted=0  and subscriber_date_added < '".$this->dateBefore."' and subscriber_id not in(select distinct subscriber_id from red_email_track where user_id='$mid' and email_track_read=1)";
		}
		$rsTotalUnresponsiveContacts = $this->db->query($sqlTotalUnresponsiveContacts);
		$intCount = $rsTotalUnresponsiveContacts->row()->totcontacts;
		$rsTotalUnresponsiveContacts->free_result();
		return (null == $intCount)?0: $intCount;
	
	}
	function searchContact(){
		$this->load->view('webmaster/header',array('title'=>'Manage Users','logo_link'=>'webmaster/dashboard_stat'));		
		$this->load->view('webmaster/searchContact');
		$this->load->view('webmaster/footer');
	
	}
	function srchcontactnow(){
		$e = $this->input->post('e');
		$rsContact = $this->db->query("select subscriber_id, member_username,if(subscriber_status = 1 and t1.is_deleted=0, 'active','inactive') c, status_change_date from red_email_subscribers t1 inner join red_members t2 on t1.subscriber_created_by=t2.member_id where subscriber_email_address='$e'");
		$tbl = '<table border="0" cellspacing="0" cellpadding="0" class="tbl_forms">';
		if($rsContact->num_rows() > 0){
			foreach($rsContact->result_array() as $con){
				$thisContact = $con['subscriber_id'];
				$tbl .= "<tr><td>".$con['member_username']."</td><td>".$con['c']."</td>";
				if($con['c'] == 'active'){
					$tbl .= "<td><a href='javascript:unsubIt(".$thisContact.");'>Unsubscribe It</a></td>";
				}else{	
					$tbl .= "<td>".$con['status_change_date']."</td>";
				}
				
				$tbl .= "</tr>";	
			}
		}
		$tbl .= "<tr><td colspan='3'><input id='btnUnsubAll' type='button' value='Unsubscribe All' onclick='javascript:unsuball();' /></td></tr>";
		$tbl .= "</table>";
		echo $tbl;
	}
	function unsubit(){
		$cid = $this->input->post('cid');
		$this->db->query("update red_email_subscribers set subscriber_status=5 where subscriber_id='$cid'");
		$this->srchcontactnow();
	}
	function unsuball(){
		$e = $this->input->post('e');
		$this->db->query("update red_email_subscribers set subscriber_status=5 where subscriber_email_address='$e' and subscriber_status=1 and is_deleted=0");
		$this->srchcontactnow();
	}
	function unsubscribe_feedback($start=0){
		$feedback_id = 0;
		$contacts_array =  array();
		if(isset($_POST['mode']) and $_POST['mode'] == 'search'){
			$whereClauseMember = '';
			$whereClause = '';
			$username = trim($_POST['username']);
			
			if($username !=''){
				$rsMembers = $this->db->query("select member_id from red_members where member_username like'%$username%'");
				if($rsMembers->num_rows() > 0){	
					$arrMid =  array();
					foreach($rsMembers->result_array() as $row)
					$arrMid[] = $row['member_id'];				
				}
				$rsMembers->free_result();	
				if(count($arrMid) > 0){
					$intMid =  implode(", ",$arrMid );
					$whereClauseMember .= " and member_id in($intMid)";	
				}
			}
			$dtfrom = trim($_POST['date_from']);
			$whereClause .= (trim($_POST['date_from']) != '')? " and feedback_date > '$dtfrom'" : "";
			$dtto = trim($_POST['date_to']);
			$whereClause .= (trim($_POST['date_to']) != '')? " and feedback_date < '$dtto'" : "";
			$pipeline = trim($_POST['pipeline']);
			$whereClause .= ($pipeline  != '')? " and t1.vmta = '$pipeline'" : "";
			$feedback_id = trim($_POST['feedback_id']);
			$feedback_id_morethan = intval($_POST['feedback_id_morethan']); 		
		
		
			$limitClause = '';
			$contacts_array = array('username'=>$username, 'date_from'=>$dtfrom, 'date_to'=>$dtto, 'pipeline'=>$pipeline, 'feedback_id'=>$feedback_id, 'feedback_id_morethan'=>$feedback_id_morethan );
		}else{	
			$config['base_url']=base_url().'webmaster/contacts_segmentation/unsubscribe_feedback';
			$config['per_page']=20;
			$config['uri_segment']=4;	
			//$whereClauseMember = " and member_id in(7179, 3740, 6049, 6822, 7356, 7266, 7262, 6634, 7084, 6706, 7410, 6390, 6207, 6475, 4821, 5478, 7278, 6384, 7418, 6520, 7283, 7265, 6521, 2060, 3352, 3379, 3487, 3870, 4088, 4386, 4486, 4666, 6028, 6631, 6838, 7278)";
			//$whereClause = " and feedback_date > '2015-09-30' and  feedback_date <'2015-11-01' ";
			$config['total_rows']= $this->db->query("select count(*) ct from red_unsubscribe_feedback t1 where 1 $whereClauseMember $whereClause ")->row()->ct;
		
	 
			$this->pagination->initialize($config);				
			$paging_links=$this->pagination->create_links();	
			$limitClause =  " limit $start,".$config['per_page'];
		}
		$tbl = '<table border="0" cellspacing="0" cellpadding="0" class="tbl_forms">
				<tr><th>Member</th><th>Pipeline</th><th>Unsubscribes</th><th>Feedback 1</th><th>Feedback 2</th><th>Feedback 3</th><th>Feedback 4</th><th>Feedback 5</th><th>Feedback 6</th></tr>';
		$whereClauseForjoin = str_replace('member_id','t1.member_id', $whereClauseMember);		
		//echo "select distinct t1.member_id,member_username from red_unsubscribe_feedback t1 inner join red_members t2 on t1.member_id=t2.member_id where 1 $whereClauseForjoin $whereClause order by member_username $limitClause";
		$rsUsersList = $this->db->query("select distinct t1.member_id,member_username,t1.vmta from red_unsubscribe_feedback t1 inner join red_members t2 on t1.member_id=t2.member_id where 1 $whereClauseForjoin $whereClause order by member_username $limitClause");
		// echo $this->db->last_query();
		
		if($rsUsersList->num_rows() > 0){			 
			foreach($rsUsersList->result_array() as $con){
				$thisUser = $con['member_id'];
				$thisUserName = $con['member_username'];
				$thisVMTA = $con['vmta'];
				
				$totalUnsubscribes = $this->db->query("select count(subscriber_id) contact from red_email_subscribers where subscriber_created_by='$thisUser' and subscriber_status=0")->row()->contact;
				$feedbackCountForThisUser = $this->db->query("select count(subscriber_id) fd from red_unsubscribe_feedback where member_id='$thisUser'")->row()->fd;
				if($feedbackCountForThisUser > 0){
					  
					$feedback_1 = $this->db->query("select count(subscriber_id) fd from red_unsubscribe_feedback t1 where member_id='$thisUser' and feedback_id=1 $whereClause")->row()->fd;
					$feedback_2 = $this->db->query("select count(subscriber_id) fd from red_unsubscribe_feedback t1 where member_id='$thisUser' and feedback_id=2 $whereClause")->row()->fd;
					$feedback_3 = $this->db->query("select count(subscriber_id) fd from red_unsubscribe_feedback t1 where member_id='$thisUser' and feedback_id=3 $whereClause")->row()->fd;
					$feedback_4 = $this->db->query("select count(subscriber_id) fd from red_unsubscribe_feedback t1 where member_id='$thisUser' and feedback_id=4 $whereClause")->row()->fd;
					$feedback_5 = $this->db->query("select count(subscriber_id) fd from red_unsubscribe_feedback t1 where member_id='$thisUser' and feedback_id=5 $whereClause")->row()->fd;
					$feedback_6 = $this->db->query("select count(subscriber_id) fd from red_unsubscribe_feedback t1 where member_id='$thisUser' and feedback_id=6 $whereClause")->row()->fd;
					
					$feedback_1_percentage = ($feedback_1 > 0 and $totalUnsubscribes > 0)? round(($feedback_1 / $totalUnsubscribes) * 100, 2) : 0;
					$feedback_2_percentage = ($feedback_2 > 0 and $totalUnsubscribes > 0)? round(($feedback_2 / $totalUnsubscribes) * 100, 2) : 0;
					$feedback_3_percentage = ($feedback_3 > 0 and $totalUnsubscribes > 0)? round(($feedback_3 / $totalUnsubscribes) * 100, 2) : 0;
					$feedback_4_percentage = ($feedback_4 > 0 and $totalUnsubscribes > 0)? round(($feedback_4 / $totalUnsubscribes) * 100, 2) : 0;
					$feedback_5_percentage = ($feedback_5 > 0 and $totalUnsubscribes > 0)? round(($feedback_5 / $totalUnsubscribes) * 100, 2) : 0;
					$feedback_6_percentage = ($feedback_6 > 0 and $totalUnsubscribes > 0)? round(($feedback_6 / $totalUnsubscribes) * 100, 2) : 0;
					if($feedback_id > 0){
						if(${'feedback_'.$feedback_id}  > $feedback_id_morethan){
							$tbl .= "<tr><td>$thisUserName</td><td>$thisVMTA</td><td>$totalUnsubscribes</td>
										<td><a href='javascript:void(0);' onclick='javascript:showFeedback($thisUser,1);'>$feedback_1 [$feedback_1_percentage %]</a></td>
										<td><a href='javascript:void(0);' onclick='javascript:showFeedback($thisUser,2);'>$feedback_2 [$feedback_2_percentage %]</a></td>
										<td><a href='javascript:void(0);' onclick='javascript:showFeedback($thisUser,3);'>$feedback_3 [$feedback_3_percentage %]</a></td>
										<td><a href='javascript:void(0);' onclick='javascript:showFeedback($thisUser,4);'>$feedback_4 [$feedback_4_percentage %]</a></td>
										<td><a href='javascript:void(0);' onclick='javascript:showFeedback($thisUser,5);'>$feedback_5 [$feedback_5_percentage %]</a></td>
										<td><a href='javascript:void(0);' onclick='javascript:showFeedback($thisUser,6);'>$feedback_6 [$feedback_6_percentage %]</a></td></tr>";
						}	
					}else{ 
							$tbl .= "<tr><td>$thisUserName</td><td>$thisVMTA</td><td>$totalUnsubscribes</td>
										<td><a href='javascript:void(0);' onclick='javascript:showFeedback($thisUser,1);'>$feedback_1 [$feedback_1_percentage %]</a></td>
										<td><a href='javascript:void(0);' onclick='javascript:showFeedback($thisUser,2);'>$feedback_2 [$feedback_2_percentage %]</a></td>
										<td><a href='javascript:void(0);' onclick='javascript:showFeedback($thisUser,3);'>$feedback_3 [$feedback_3_percentage %]</a></td>
										<td><a href='javascript:void(0);' onclick='javascript:showFeedback($thisUser,4);'>$feedback_4 [$feedback_4_percentage %]</a></td>
										<td><a href='javascript:void(0);' onclick='javascript:showFeedback($thisUser,5);'>$feedback_5 [$feedback_5_percentage %]</a></td>
										<td><a href='javascript:void(0);' onclick='javascript:showFeedback($thisUser,6);'>$feedback_6 [$feedback_6_percentage %]</a></td></tr>";
					}	
				}
								
			}
				 
		
		

		}
		$tbl .= "</table>";
		 
		$this->load->view('webmaster/header',array('title'=>'Manage Users','logo_link'=>'webmaster/dashboard_stat'));		
		$this->load->view('webmaster/unsubscribe_feedback', array('tbl'=>$tbl,'contacts_array'=>$contacts_array, 'paging_links'=>$paging_links));
		$this->load->view('webmaster/footer');	
	}
	
	function showFeedback($mid, $fid){
		$rsFeedback = $this->db->query("select subscriber_id,feedback_text from red_unsubscribe_feedback t1 inner join red_email_campaigns t2 on t1.campaign_id=t2.campaign_id  where t2.campaign_created_by='$mid' and feedback_id='$fid'");
		if($rsFeedback->num_rows() > 0){		
			$tbl = '<div style="padding:15px;width: 500px; height: 400px; overflow: auto;"><table border="0" cellspacing="0" cellpadding="0" class="tbl_forms">';
			foreach($rsFeedback->result_array() as $fb){
				$cid = $fb['subscriber_id'];	
				$ceml = $this->db->query("select subscriber_email_address from red_email_subscribers where subscriber_id='$cid'")->row()->subscriber_email_address;
				$feedback = $fb['feedback_text'];	
				$tbl .= "<tr><td>$ceml</td><td>$feedback</td></tr>";	
			}
			$tbl .= "</table></div>";
		}
			echo $tbl;
	}
}
?>