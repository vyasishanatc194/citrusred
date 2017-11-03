<?php
class Autoresponder extends CI_Controller{

	function __construct(){
        parent::__construct();

		//check via common model
		if(!$this->is_authorized->check_user())
			redirect('user/index');

		$this->load->library('upload');
		//Load page model
		$this->load->model('newsletter/Page_Model');
		$this->load->model('newsletter/Autoresponder_Model');
		$this->load->model('newsletter/Subscription_Model');
		$this->load->model('UserModel');
		$this->load->model('newsletter/Campaign_Model');
		$this->load->model('newsletter/Campaign_Autoresponder_Model');
		$this->load->model('Activity_Model');
		$this->load->model('newsletter/Subscriber_Model');
		$this->load->model('newsletter/Emailreport_Model');
		
		// Check if folder with modulo of User ID exists on server
		$user_dir = $this->session->userdata('member_id') % 1000;
		#Get absolute path for uploading
		$this->upload_path= $this->config->item('user_public').$user_dir .'/'.$this->session->userdata('member_id');
		
		force_ssl();	
	 }

	function index(){
		$this->display();
	}

	/**
	*	'Create' controller function to create new autoresponder.
	*	This controller is used to create new autoresponder.
	*/

	function create($grp_id){
		if($this->session->userdata('member_id')=='')
			redirect('user/index');		
		//get autoresponder groups info
		$autoresponder_group=$this->Autoresponder_Model->get_autoresponder_group(array('is_deleted'=>0,'id'=>$grp_id));
		// Retrieve data posted in form posted by user using input class
		$input_array=array('campaign_title'=>'Unnamed',
			'campaign_created_by'=>$this->session->userdata('member_id'),
			'campaign_theme_id'=>'-1', // Default theme id
			'campaign_template_id'=>'-1',//Default template id
			'campaign_template_option'=>'3',
			'campaign_color_theme_id'=>'-1',
			'autoresponder_group_id'=>$grp_id,
			'autoresponder_subscription_id'=>$autoresponder_group[0]['autoresponder_subscription_id'],
			'campaign_content'=>NULL,
			'campaign_email_content'=>NULL,
			'campaign_text_content'=>NULL,
			'email_subject'=>null,
			'sender_email'=>null,
			'sender_name'=>null,
			'click_url'=>NULL,
			'campaign_after_encode_url'=>NULL
		);

		// Sends form input data to database via model object
		$autoresponder_id=$this->Autoresponder_Model->create_autoresponder($input_array);
		$conditions_array=array("type='folder'");

		//Fetch max of right and position from pages table table for default
		$row=$this->Page_Model->getMax('right',$conditions_array);
		$right=$row[0]['getmax']+1;
		$row=$this->Page_Model->getMax('position',$conditions_array);
		$position=$row[0]['getmax']+1;

		//Prepare input array for home page
		$input_array=array(
			'title'=>'home',
			'name'=>'home',
			'meta_description'=>NULL,
			'meta_keyword'=>NULL,
			'site_id'=>$autoresponder_id,
			'page_position'=>1,
			'is_published'=>'yes',
			'parent_id'=>1,
			'position'=>0,
			'`left`'=>$right,
			'`right`'=>$right+1,
			'level'=>2,
			'type'=>'default',
			'is_autoresponder'=>1
		);
		//Insert record	for home page
		$page_id=$this->Page_Model->create_page($input_array);
		$email_template_info=$this->Autoresponder_Model->get_autoresponder_data(array('campaign_id'=>$autoresponder_id,'campaign_created_by'=>$this->session->userdata('member_id')));

		$insert_array=array('red_background_color_page_id'=>$page_id,'red_background_color_block_name'=>'footer_font_txt','red_background_color_block_content'=>'font-size::#2');
		$this->Campaign_Model->add_background_color_content($insert_array);
		// Redirect to listing of campaigns
		redirect('newsletter/campaign_template_options/autoresponder/'.$autoresponder_id);
	}
	/**
	* 'theme' controller function to select the theme   .
	*/
	function theme(){
		if($this->input->post('action')=='save'){
			
			$autoresponder_id=$this->input->get_post('campaign_id', TRUE);
			if($autoresponder_id){
				// Retrieve data posted in form posted by user using input class
				$input_array=array(
				'campaign_theme_id'=>$this->input->get_post('red_theme_name', TRUE),
				'campaign_template_id'=>$this->input->get_post('red_template_name', TRUE),
				'campaign_template_option'=>'3'
				);
				// Update autoresponder by data posted by user
				$this->Autoresponder_Model->update_autoresponder($input_array,array('campaign_id'=>$autoresponder_id));
				// Redirect to listing of autoresponder
				redirect('newsletter/autoresponder/autoresponder_editor/'.$autoresponder_id);
			}

		}
	}

	/**
	 *	Function Get_html
	 *
	 *	'get_html' controller function for get campaign content
	 *
	 *	@param (int) (id)  campaign id
	 *
	 */
	function get_html($id=0){
		if($this->session->userdata('member_id')=='')
			redirect('user/index');
		//	Collect campaign id
		//Protecting MySQL from query string sql injection Attacks
		if(!is_numeric($id)){
			redirect('newsletter/autoresponder/display');
		}
		$campaign_array=$this->Autoresponder_Model->get_autoresponder_data(array('campaign_id'=>$id,'campaign_created_by'=>$this->session->userdata('member_id')));
		if(!count($campaign_array)){
			redirect('newsletter/autoresponder/display');
		}
		if($campaign_array[0]['campaign_template_option']==4){
			echo header('Content-Type:text/html; charset=UTF-8');
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.html_entity_decode($campaign_array[0]['campaign_content'], ENT_QUOTES, "utf-8" );	// display campaign content
		}else if($campaign_array[0]['campaign_template_option']==2){
			echo header('Content-Type:text/html; charset=UTF-8');
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.html_entity_decode($campaign_array[0]['campaign_content'], ENT_QUOTES, "utf-8" );	// display campaign content
		}else if($campaign_array[0]['campaign_template_option']==5){
			$user=$this->UserModel->get_user_data(array('member_id'=>$campaign_array[0]['campaign_created_by']));
			$campaign_footer_text_only = $this->Campaign_Autoresponder_Model->campaign_footer_text_only($user, $campaign_array[0]['campaign_id'], true, true);
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><pre>'.html_entity_decode($campaign_array[0]['campaign_text_content'].$campaign_footer_text_only, ENT_QUOTES, "utf-8" )."</pre>";	// display campaign content
		}else if($campaign_array[0]['campaign_template_option']==1){
			echo ''.html_entity_decode($campaign_array[0]['campaign_content'], ENT_QUOTES, "utf-8" )."";	// display campaign content
		}else{
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.$campaign_array[0]['campaign_content'];	// display campaign content
		}
	}
	/**
	* 'autoresponder_update' controller function to update  autoresponder title.
	*/
	function autoresponder_update($autoresponder_id=0){
		if($this->session->userdata('member_id')=='')
			redirect('user/index');
		//Fetch autoresponder data from database by autoresponder ID
		$autoresponder_array=$this->Autoresponder_Model->get_autoresponder_data(array('campaign_id'=>$autoresponder_id,'campaign_created_by'=>$this->session->userdata('member_id')));
			//Redirects user to listing page if user have not created this autoresponder or autoresponder does not exists
			if(!count($autoresponder_array)){
				$this->messages->add('Autoresponder does not exists or you have not created this autoresponder', 'error');
				redirect('newsletter/autoresponder/display');
			}else{
			$input_array=array(	'campaign_title'=>$this->input->get_post('campaign_title', TRUE));
			// Update autoresponder by data posted by user
			$this->Autoresponder_Model->update_autoresponder($input_array,array('campaign_id'=>$autoresponder_id));
			}
	}
	/**
	* 'autoresponder_status' controller function to update  autoresponder staus.
	*/
	function autoresponder_status($autoresponder_id=0){
		if($this->session->userdata('member_id')=='')
			redirect('user/index');
		
		$autoresponder_array=$this->Autoresponder_Model->get_autoresponder_data(array('campaign_id'=>$autoresponder_id,'campaign_created_by'=>$this->session->userdata('member_id')));
		//Redirects user to listing page if user have not created this autoresponder or autoresponder does not exists

		if(!count($autoresponder_array)){
			$this->messages->add('Autoresponder does not exists or you have not created this autoresponder', 'error');
			redirect('newsletter/autoresponder/display');
		}else{
			if($autoresponder_array[0]['campaign_status']==1){
				$status=0;
				$msg="Inactive";
			}else{
				$status=1;
				$msg="Active";
				$this->Activity_Model->create_activity(array('user_id'=>$this->session->userdata('member_id'), 'activity'=>'autoresponder_activated', 'campaign_id'=>$autoresponder_id));
			}
			
			// Update autoresponder by data posted by user
			$this->Autoresponder_Model->update_autoresponder(array(	'campaign_status'=>$status), array('campaign_id'=>$autoresponder_id));
			echo $msg;
		}

	}

	/**
	* 'Dislay' controller function for listing of autoresponders.
	*/
	function display($start=0){
		if($this->session->userdata('member_id')=='')
			redirect('user/index');
		// Recieve any messages to be shown, when campaign is added or updated

		$messages=$this->messages->get();

		/******Get Maximum Contacts according to session package id************/
		//Get Package id
		$user_packages_array=$this->UserModel->get_user_packages(array('member_id'=>$this->session->userdata('member_id'),'is_deleted'=>0));
		$package_array=$this->UserModel->get_packages_data(array('package_id'=>$user_packages_array[0]['package_id']));	
		
		$package_price=$package_array[0]['package_price'];
		$autoresponder_data['package_max_contacts']=$package_array[0]['package_max_contacts'];

		$fetch_condiotions_array=array('campaign_created_by'=>$this->session->userdata('member_id'),'is_deleted'=>0);

		// Define config parameters for paging like base url, total rows and record per page.

		$config['base_url']=base_url().'newsletter/autoresponder/display';

		$config['total_rows']=$this->Autoresponder_Model->get_autoresponder_count($fetch_condiotions_array);


		// Fetches campaign data from database

		$autoresponder_data['autoresponders']=$this->Autoresponder_Model->get_autoresponder_data($fetch_condiotions_array,$config['total_rows']);
		$i=0;
		foreach($autoresponder_data['autoresponders'] as $autoresponder){
			// Fetches campaign scheduled data from database
			$schedule_date=$this->Autoresponder_Model->get_shchedule_autoresponder_data(array('autoresponder_id'=>$autoresponder['campaign_id']));

			$cnt=count($schedule_date);

			if($cnt){
			$cnt--;
			$autoresponder_data['autoresponders'][$i]['schedule_date']= $schedule_date[$cnt]['campaign_scheduled_date'];
			}
			$i++;
		}
		//get autoresponder groups
		$autoresponder_group=$this->Autoresponder_Model->get_autoresponder_group(array('is_deleted'=>0,'autoresponder_created_by'=>$this->session->userdata('member_id')));
		//collect in array
		$autoresponder_data['autoresponder_group']=$autoresponder_group;
		//Assign messages to array to be send to view.
		$autoresponder_data['messages'] =$messages;
		// Fetch user data from database
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));
		$autoresponder_data['extra']=$user_data_array[0];

		/*
			Creating array of conditions to checked with database with conditions.

		*/
		$fetch_condiotions_array=array(	'subscription_created_by'=>$this->session->userdata('member_id'),'is_deleted'=>0);

		//Count toal number of subscriptions from database
		$config['total_rows']=$this->Subscription_Model->get_subscription_count($fetch_condiotions_array);
		// Fetches subscription data from database
		$autoresponder_data['select_subscriptions']=$this->Subscription_Model->get_subscription_data($fetch_condiotions_array,$config['total_rows']);
		$this->load->view('header',array('title'=>'List Autoresponder'));
		$this->load->view('newsletter/autoresponder_list',array('autoresponder_data'=>$autoresponder_data));
		$this->load->view('footer');
	}
	/*
		'Display_autoresponder function for listing of autoresponder using ajax'
	*/
	function display_autoresponder($grp_id=0){
  
		#Get shoreten url
		$shorten_url=get_shorten_url();
		//group status
		$grp_status=$_POST['status'];
		// Get Maximum Contacts according to session package id
		//Get Package id
		$user_packages_array=$this->UserModel->get_user_packages(array('member_id'=>$this->session->userdata('member_id'),'is_deleted'=>0));
		$package_array=$this->UserModel->get_packages_data(array('package_id'=>$user_packages_array[0]['package_id']));	
			
		$package_price=$package_array[0]['package_price'];
		$autoresponder_data['package_max_contacts']=$package_array[0]['package_max_contacts'];

		$fetch_condiotions_array=array('autoresponder_group_id'=>$grp_id,'campaign_created_by'=>$this->session->userdata('member_id'),'is_deleted'=>0,'is_status'=>0);
		// Define config parameters for paging like base url, total rows and record per page.

		$config['base_url']=base_url().'newsletter/autoresponder/display';

		$config['total_rows']=$this->Autoresponder_Model->get_autoresponder_count($fetch_condiotions_array);
		// Fetches campaign data from database
		$autoresponder_data['autoresponders']=$this->Autoresponder_Model->get_autoresponder_data($fetch_condiotions_array,$config['total_rows']);
		$i=0;
		foreach($autoresponder_data['autoresponders'] as $autoresponder){
			// Fetches campaign scheduled data from database
			$schedule_date=$this->Autoresponder_Model->get_shchedule_autoresponder_data(array('autoresponder_id'=>$autoresponder['campaign_id']));
			$cnt=count($schedule_date);
			if($cnt){
				$cnt--;
				$this_scheduled_id = $schedule_date[$cnt]['autoresponder_scheduled_id'];
				$mail_sent_yet = $this->db->query("select count(`autoresponder_scheduled_id`) as totcontact from `red_autoresponder_signup` where `autoresponder_scheduled_id` ='$this_scheduled_id'")->row(0)->totcontact ;

				$autoresponder_data['autoresponders'][$i]['mail_sent_yet']= $mail_sent_yet;
				$autoresponder_data['autoresponders'][$i]['schedule_date']= $schedule_date[$cnt]['campaign_scheduled_date'];
			}
				$i++;
		}

		//Fetch autoresponder from campaigns array
		if(count($autoresponder_data['autoresponders'])) {
			$i=2;
			$rsAutoresponderGroup =$this->Autoresponder_Model->get_autoresponder_group(array('id'=>$grp_id));
			$autoresponder_group_name = $rsAutoresponderGroup[0]['group_name'];
			$autoresponder_list='<tr class="'.$grp_id.'"><th colspan="3">
						<a href="'.site_url("newsletter/autoresponder/create/".$grp_id).'" class="btn cancel inline-block"><i class="icon-plus"></i>Add Campaign</a>
						<h2>'.$autoresponder_group_name.'</h2>
				  </th></tr>';
			foreach($autoresponder_data['autoresponders'] as $autoresponder){

				if($autoresponder['campaign_status']){
					$msg_status= '<i class="icon-pause"></i><i class="icon-play" style="display: none"></i>';
					$campaign_status = '';
				}else{
					$msg_status= '<i class="icon-play"></i><i class="icon-pause" style="display: none"></i>';
					$campaign_status = 'campaign-inactive';
				}
				if($i%2==0){
					$class="class='autoresponder_list  ".$grp_id." ".$campaign_status." campaign_".$autoresponder['campaign_id']."'";
				}else{
					$class="class='autoresponder_list ".$grp_id." ".$campaign_status." campaign_".$autoresponder['campaign_id']."'";
				}
				// calculate autoresponder scheduled interval
				if($autoresponder['autoresponder_scheduled_id']==0){
					$day='<br/><a class="edit-interval" href="'.site_url('newsletter/campaign_email_setting/autoresponder/'.$autoresponder['campaign_id']).'">Edit</a>';
				}else{
					if($autoresponder['autoresponder_scheduled_interval']==0){
						$day='<small>Send <strong class="day"> within 24 hours  after</strong> signup</small><br/><a class="fancybox_edit_inerval edit-interval" href="javascript:void(0);">Edit</a><span class="hide_autoresponder_id" style="display:none;">'.$autoresponder['campaign_id'].'</span>';
					}else if($autoresponder['autoresponder_scheduled_interval']==1){
						$day='<small>Send <strong class="day">'.$autoresponder['autoresponder_scheduled_interval'].' day after</strong> signup</small> <br/><a class="fancybox_edit_inerval edit-interval" href="javascript:void(0);">Edit Interval</a><span class="hide_autoresponder_id" style="display:none;">'.$autoresponder['campaign_id'].'</span>';
					}else{
						$day='<small>Send <strong class="day">'.$autoresponder['autoresponder_scheduled_interval'].' days after</strong> signup</small> <br/><a class="fancybox_edit_inerval edit-interval" href="javascript:void(0);">Edit Interval</a><span class="hide_autoresponder_id" style="display:none;">'.$autoresponder['campaign_id'].'</span>';
					}
				}

				if($autoresponder['campaign_template_option']==3){
					$edit='<a class="btn cancel" href="'.site_url('newsletter/autoresponder/autoresponder_editor/'.$autoresponder['campaign_id']).'"><i class="icon-pencil"></i></a>';
				}else{
					$edit='<a class="btn cancel" href="'.site_url('newsletter/campaign_template_options/autoresponder/'.$autoresponder['campaign_id']).'"><i class="icon-pencil"></i></a>';
				}
				if($autoresponder['mail_sent_yet'] > 0){
					$stats_url =  'href="'.site_url('newsletter/emailreport_autoresponder/display/'.$autoresponder['campaign_id']).'"';
				}else{
					$stats_url = 'href="javascript:void(0);" onclick="javascript:noStats();"';
				}
				$autoresponder_preview_link= CAMPAIGN_DOMAIN.'a/'.$autoresponder['campaign_id'];
				$autoresponder_list.= '<tr valign="top" '.$class.'>
				<td width="50%"> <a target="_blank" href="'.$autoresponder_preview_link.'" rel="facebox"><span class="text-title">'. ucfirst(substr($autoresponder['campaign_title'],0,35)).'</span></a><br/>
				<h4><span><strong>Interval:</strong></span>'.$day.' </h4>
				</td>
				<td width="50%" valign="top">
				<ul class="list-icons">
				<li><span  class="autoreponder_status btn cancel" id="status_'.$autoresponder['campaign_id'].'"  onclick="update_status('.$autoresponder['campaign_id'].')">'.$msg_status.'</span></li>
				<li>'.$edit.'</li>
				<li><a href="'. site_url('newsletter/autoresponder/copy_archived/'.$autoresponder['campaign_id']).'" class="btn cancel"><i class="icon-copy"></i></a></li>
				<li><a target="_blank" href="'.$autoresponder_preview_link.'" rel="facebox" class="btn cancel"><i class="icon-eye-open"></i></a></li>
				<li><a  '.$stats_url.' class="btn cancel"><i class="icon-bar-chart"></i></a></li>
				<li ><a class="delete-row btn cancel" name="'.$autoresponder['campaign_id'].'" href="javascript:void(0);"><i class="icon-trash"></i></a></li>
				</ul>
				</td>
				</tr>';
				$i++;
			}
		}else{
			$autoresponder_list='<div class="empty">
          <p>No Campaigns Found. To begin adding a campaign click on "Add Campaign".</p>
          <a  href="'.site_url("newsletter/autoresponder/create/".$grp_id).'" class="btn add"><i class="icon-plus"></i>Add Campaign</a>
        </div>';
		}
		echo $autoresponder_list;
	}

	/*

		'Delete' controller function for deleting of autoresponder.

	*/

	function delete($id){
		if($this->session->userdata('member_id')=='')
			redirect('user/index');
		 

		//Fetch autoresponder data from database by autoresponder ID
		$autoresponder_array=$this->Autoresponder_Model->get_autoresponder_data(array('campaign_id'=>$id,'campaign_created_by'=>$this->session->userdata('member_id')));

		//Redirects user to listing page if user have not created this autoresponder or autoresponder does not exists

		if(!count($autoresponder_array))
		{

			// Assign  error message by message class

			$this->messages->add('Autoresponder does not exists or you have not created this autoresponder', 'error');



			// Redirect to listing of campaigns

			redirect('newsletter/autoresponder/display');

		}

		// Deletes autoresponder according to autoresponder ID

		$this->Autoresponder_Model->delete_autoresponder(array('campaign_id'=>$id));
		echo $autoresponder_array[0]['autoresponder_group_id']."|".$autoresponder_array[0]['campaign_status'];
		exit;
	}


	/*

		'Copy Archived' controller function to copy autoresponder from archived autoresponder



	*/
	function copy_archived($autoresponder_id=0,$copy_page_id=0){
		if($this->session->userdata('member_id')=='')
			redirect('user/index');		 

		// Prepare array to send to view
		$autoresponder_data=array();

		if($autoresponder_id){

			$archivedCampaignId = $autoresponder_id;
			//Fetch list of archived autoresponder
			$fetch_conditions_array=array('campaign_id'=>$archivedCampaignId, 'campaign_created_by'=>$this->session->userdata('member_id'));

			$loaded_autoresponder=$this->Autoresponder_Model->get_autoresponder_data($fetch_conditions_array);
			// get page id
			$loaded_page=$this->Page_Model->get_page_data(array('site_id'=>$autoresponder_id,'is_autoresponder'=>1));
		}

		// Retrieve data posted in form posted by user using input class

		$input_array=array('campaign_title'=>"Unnamed",
		'campaign_created_by'=>$this->session->userdata('member_id'),
		'autoresponder_group_id'=>$loaded_autoresponder[0]['autoresponder_group_id'],
		'campaign_theme_id'=>$loaded_autoresponder[0]['campaign_theme_id'],
		'campaign_template_id'=>$loaded_autoresponder[0]['campaign_template_id'],
		'campaign_template_option'=>$loaded_autoresponder[0]['campaign_template_option'],
		'campaign_text_content'=>$loaded_autoresponder[0]['campaign_text_content'],
		'autoresponder_subscription_id'=>$loaded_autoresponder[0]['autoresponder_subscription_id'],
		'campaign_content'=>$loaded_autoresponder[0]['campaign_content'],
		'campaign_email_content'=>$loaded_autoresponder[0]['campaign_email_content'],
		'campaign_color_theme_id'=>$loaded_autoresponder[0]['campaign_color_theme_id'],
		'campaign_status'=>$loaded_autoresponder[0]['campaign_status'],	 
			'email_subject'=>null,
			'sender_email'=>null,
			'sender_name'=>null,
			'click_url'=>NULL,
			'campaign_after_encode_url'=>NULL
		);

		// Sends form input data to database via model object

		$autoresponder_id=	$this->Autoresponder_Model->create_autoresponder($input_array);
		$conditions_array=array("type='folder'");

		//Fetch max of right and position from pages table table for default
		$row=$this->Page_Model->getMax('right',$conditions_array);
		$right=$row[0]['getmax']+1;
		$row=$this->Page_Model->getMax('position',$conditions_array);
		$position=$row[0]['getmax']+1;
		//Prepare input array for home page
		$input_array=array(
		'title'=>'home',
		'name'=>'home',
		'site_id'=>$autoresponder_id,
		'page_position'=>1,
		'is_published'=>'yes',
		'parent_id'=>1,
		'position'=>0,
		'`left`'=>$right,
		'`right`'=>$right+1,
		'level'=>2,
		'type'=>'default',
		'is_autoresponder'=>1,
		);

		//Insert record	for home page
		$page_id=$this->Page_Model->create_page($input_array);


		/*******Copy colors********/
		$block_data_array=array();
		$block_all_data_count=$this->Autoresponder_Model->get_background_color_content_count(array('red_background_color_page_id'=>$loaded_page[0]['id']));
		$block_all_data=$this->Autoresponder_Model->get_background_color_content_data(array('red_background_color_page_id'=>$loaded_page[0]['id']),$block_all_data_count);
		for($i=0;$i<count($block_all_data);$i++)
		{
			$insert_array=array('red_background_color_page_id'=>$page_id,'red_background_color_block_name'=>$block_all_data[$i]['red_background_color_block_name'],'red_background_color_block_content'=>$block_all_data[$i]['red_background_color_block_content']);

			$this->Autoresponder_Model->add_background_color_content($insert_array);
		}
		/* if($loaded_autoresponder[0]['campaign_template_option']==3){
			// Redirect to autoresponder editor
			redirect('newsletter/autoresponder/autoresponder_editor/'.$autoresponder_id);
		}else{
			redirect('newsletter/campaign_template_options/autoresponder_preview/'.$autoresponder_id);
		}	 */
		if($loaded_autoresponder[0]['campaign_template_option']==3){
			$this->Page_Model->copyCampaignAssets($this->upload_path.'/autoresponders/'.$archivedCampaignId, $this->upload_path.'/autoresponders/'.$autoresponder_id);
			// Redirect to campaign editor
			redirect('newsletter/autoresponder/autoresponder_editor/'.$autoresponder_id);
		}else if(($loaded_autoresponder[0]['campaign_template_option']==1)||($loaded_autoresponder[0]['campaign_template_option']==2)){
			// Redirect to campaign editor
			redirect('newsletter/campaign_template_options/autoresponder_preview/'.$autoresponder_id);
		}else{
			redirect('newsletter/campaign_template_options/autoresponder/'.$autoresponder_id);
		}
	}

	/*

		function to validate url

	*/



	function validate_url()
	{

		$url=$this->input->get_post('campaign_import_url', true);



		if(preg_match('@(http://)([^/]+)@i',$url, $matches))

		{

			return true;

		}

		else

		{

			$this->form_validation->set_message('validate_url', 'The %s is invalid');

			return false;

		}

	}


	/*

	Function for validating upload, extracting from zip archive and

	fetch html content.

	*/



	function validate_upload()
	{


		//Check if folder with name of 'imported_zip_files' exists on server

		if(!file_exists($this->upload_path.'/imported_zip_files'))

		{

			mkdir($this->upload_path.'/imported_zip_files/',0777);

			chmod($this->upload_path.'/imported_zip_files/',0777);

		}



		//Check if folder with name of 'extracted_zip_files' exists on server

		if(!file_exists($this->upload_path.'/extracted_zip_files'))
		{
			mkdir($this->upload_path.'/extracted_zip_files/',0777);
			chmod($this->upload_path.'/extracted_zip_files/',0777);
		}



		// Initialization upload configuration

		$upload_config	=array();



		$upload_config['upload_path'] = $this->upload_path.'/imported_zip_files/';

		$upload_config['allowed_types'] = 'zip|rar';

		$upload_config['max_size']	= 1024*5; //5MB

		$this->upload->initialize($upload_config);



		// New file name of zippped file

		$new_file_name=$this->session->userdata('member_id').'_'.time();


		//check if file is uploaded successfully

		if(!$this->upload->do_upload('campaign_import_zip_file'))

		{
			$this->upload->display_errors();
			//displays error message if uploading fails

			$this->form_validation->set_message('validate_upload', $this->upload->display_errors());

			return false;

		}

		{
			//Get data of uploaded file

			$uploaded_file_array=$this->upload->data();

			//Rename uploaded file with new name

			rename($uploaded_file_array['full_path'],$uploaded_file_array['file_path'].$new_file_name.$uploaded_file_array['file_ext']);

			//Initialize php archive class and extract archive

			$zip = new ZipArchive;

			if ($zip->open($uploaded_file_array['file_path'].$new_file_name.$uploaded_file_array['file_ext']) === TRUE) {

				$zip->extractTo($this->upload_path.'/extracted_zip_files/'.$new_file_name.'/');

				$zip->close();

			}
			// file names allowed for extracting campaign html

			$file_names_allowed=array('index.html','index.html');



			$files=array();

			$directories=array();


			//Open renamed directory for reading and put files in files arrray

			// and directories in directories array

			$dir_handle=opendir($this->upload_path.'/extracted_zip_files/'.$new_file_name.'/');

			 while (false !== ($file = readdir($dir_handle))) {

				 if($file!='.' && $file!='..')

				 {

					if(is_dir($this->upload_path.'/extracted_zip_files/'.$new_file_name.'/'.$file))

						$directories[]=$file;

					else

						$files[]=$file;

				 }

			}


			//Declare extracted_file variable

			$extracted_file='';



			//If extracted directory have files in it, then iterate in directory to search

			// any of allowed files in it.

			if(count($files))

			{

				foreach($files as $file)

				{

					if(in_array($file,$file_names_allowed) )

					{

						$key=array_search($file,$file_names_allowed);

						$extracted_file= $this->upload_path.'/extracted_zip_files/'.$new_file_name.'/'.$file_names_allowed[$key];

						$path_to_images=base_url().'webappassets/user_files/'.$this->session->userdata('member_id').'/extracted_zip_files/'.$new_file_name;

						break;

					}

				}

			}

			//If extracted directory have directories in it, then iterate in first directory to search

			// any of allowed files in it.

			if($extracted_file=='' && count($directories))

			{

				$directory=$directories[0];

				$dir_handle=opendir($this->upload_path.'/extracted_zip_files/'.$new_file_name.'/'.$directory);

				while (false !== ($file = readdir($dir_handle))) {

					if($file!='.' || $file!='..')

					{

						if(in_array($file,$file_names_allowed))

						{

							$key=array_search($file,$file_names_allowed);

							$extracted_file=$this->upload_path.'/extracted_zip_files/'.$new_file_name.'/'.$directory.'/'. $file_names_allowed[$key];

							$path_to_images=base_url().'webappassets/user_files/'.$this->session->userdata('member_id').'/extracted_zip_files/'.$new_file_name.'/'.$directory;

							break;

						}

					}

				}

			}



			// To check if extracted file exits in archive

			if($extracted_file!='')

			{

				// fetch html of file

				$html=file_get_contents($extracted_file);

				//replace path to images and css

				$filtered_html=preg_replace('#(href|src)=(["\'])([.\/]*)([^:"\']*)(["\'])#',

					'$1="'.$path_to_images.'/$4"',$html);



				//Assign filtered html to class variable and return true

				$this->extracted_zip_html=$filtered_html;
				return true;

			}

			else

			{

				//If file is not found , then display error message and return false.

				$this->form_validation->set_message('validate_upload', 'Zip Archive does not contain index.html');
				return false;

			}





		}
	}

	/*

		Controller function for autoresponder_subscription_list

	*/



	function autoresponder_subscription_list($autoresponder_id=0)
	{
		if($this->session->userdata('member_id')=='')
			redirect('user/index');

		// To check form is submittted and action is send

		if($this->input->post('action')=='send')
		{
			// Validation rules are applied

			$this->form_validation->set_rules('subscriptions[]', 'Subscriptions', 'required');
			/// To check form is validated

			if($this->form_validation->run()==true)

			{
				redirect('newsletter/campaign_email_setting/autoresponder/'.$autoresponder_id);
			}
		}
		//if we are passing autoresponder_id in function
		if($autoresponder_id!=''){
			 $_REQUEST['autoresponders']=$autoresponder_id;
		}
		//Fetch list of active campaign
		$fetch_conditions_array=array('campaign_created_by'=>$this->session->userdata('member_id'),	'campaign_id'=>$autoresponder_id, 'is_deleted'=>0);
		$active_autoresponders_array=array();

		$active_autoresponder_count=$this->Autoresponder_Model->get_autoresponder_count($fetch_conditions_array);
		$active_autoresponders=$this->Autoresponder_Model->get_autoresponder_data($fetch_conditions_array,$active_autoresponder_count);
		
		//Assign active autoresponder to send to view.
		$autoresponder_data['autoresponders'] =$active_autoresponders[0];

		$fetch_conditions_array=array('subscription_created_by'=>$this->session->userdata('member_id'), 'is_deleted'=>0, 'subscription_status'=>1);
		//Fetch Subscription list created by user

		$subscriptions_count=$this->Subscription_Model->get_subscription_count($fetch_conditions_array);

		$subscriptions=$this->Subscription_Model->get_subscription_data($fetch_conditions_array,$subscriptions_count);
		$subscription_data=array('subscriptions'=>$subscriptions);

		$messages=$this->messages->get();
		//Loads header, campaign and footer view.

		$this->load->view('header',array('title'=>'Send Autoresponders'));
		$this->load->view('newsletter/autoresponder_subscription_list',array('messages'=>$messages,'autoresponder_data'=>$autoresponder_data,'subscription_data'=>$subscription_data));
		$this->load->view('footer');
	}

	function removeTag($str,$id,$start_tag,$end_tag)
	{
		//str - string to search
		//id - text to search for
		//start_tag - start delimiter to remove
	   //end_tag - end delimiter to remove

	 //find position of tag identifier. loops until all instance of text removed
	 while(($pos_srch = strpos($str,$id))!==false)
	 {
			 //get text before identifier
			 $beg = substr($str,0,$pos_srch);
			 //get position of start tag
			 $pos_start_tag = strrpos($beg,$start_tag);
			 //echo 'start: '.$pos_start_tag.'<br>';
			 //extract text up to but not including start tag
			 $beg = substr($beg,0,$pos_start_tag);
			 //echo "beg: ".$beg."<br>";

			 //get text from identifier and on
			 $end = substr($str,$pos_srch);

			 //get length of end tag
			 $end_tag_len = strlen($end_tag);
			 //find position of end tag
			 $pos_end_tag = strpos($end,$end_tag);
			 //extract after end tag and on
			 $end = substr($end,$pos_end_tag+$end_tag_len);

			 $str = $beg.$end;
	 }

	 //return processed string
	 return $str;
	}


	function validate_max_email()
	{
		$last_character=substr($this->input->post('email_address'), -1);
		if($last_character==",")
		{
			$email_address=$this->input->post('email_address');
			$email_address=rtrim( $email_address,",");
		}else{
			$email_address=$this->input->post('email_address');
		}
		$email_arr=explode(",",$email_address);
		foreach($email_arr as $email){
			if($email==""){
				$this->form_validation->set_message('validate_max_email', 'The Email Address field must contain all valid email addresses.');
				return false;
			}
		}
		if((count($email_arr)+$this->test_email)>25){
			$this->form_validation->set_message('validate_max_email', 'You have reached the maximum allowed tests');
			return false;
		}
		if(count($email_arr)>5){
			$this->form_validation->set_message('validate_max_email', 'The max limit of %s field can not be more than five');
			return false;
		}else{
			return true;
		}
	}

	function autoresponder_editor($id,$content_for='',$campaign_create=""){
		if($this->session->userdata('member_id')=='')
			redirect('user/index');
		 
		$campaign_array=$this->Autoresponder_Model->get_autoresponder_data(array('campaign_id'=>$id,'campaign_created_by'=>$this->session->userdata('member_id')));
		//Redirects user to listing page if user have not created this campaign or campaign does not exists
		if(!count($campaign_array))
		{
			redirect('newsletter/autoresponder/display');
		}
		$this->session->set_userdata('email_template_id', $id);
		// Create array for use in view
		$email_template_data=array();
		if($campaign_create){
			$email_template_data['campaign_create']="copy";
		}
 
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));
		$user_info=true;
		$str_user_detail_for_footer='<div style="padding:15px 15px 15px 15px;float:left;">';
		if($user_data_array[0]['company']){
			$str_user_detail_for_footer.="<span class='company_name'><b><span class='copyright'>&copy; </span>".$user_data_array[0]['company']."</b></span><br/>";
		}else{
			$str_user_detail_for_footer.="<span class='company_name'><b><span class='copyright'>&copy; </span>Company Name</b></span><br/>";
			$user_info=false;
		}
		$str_user_detail_for_footer.='<div style="float:left;width:100%;margin-left:18px;">';
		if($user_data_array[0]['address_line_1']){
			$str_user_detail_for_footer.='<span class="address">'.$user_data_array[0]['address_line_1'].' '.$user_data_array[0]['address_line_2'].'</span>';
		}else{
			$str_user_detail_for_footer.="<span class='address'>Street Address</span>";
			$user_info=false;
		}
		if($user_data_array[0]['city']){
			$str_user_detail_for_footer.='<span class="city"> | '.$user_data_array[0]['city'].'</span>';
		}else{
			$str_user_detail_for_footer.="<span class='city'> | City</span>";
			$user_info=false;
		}
		if($user_data_array[0]['state']){
			$str_user_detail_for_footer.='<span class="state">, '.$user_data_array[0]['state'].'</span> ';
		}else{
			$str_user_detail_for_footer.="<span class='state'>, State</span> ";
			$user_info=false;
		}if($user_data_array[0]['zipcode']){
			$str_user_detail_for_footer.='<span class="zip">'.$user_data_array[0]['zipcode'].'</span>';
		}else{
			$str_user_detail_for_footer.="<span class='zip'> Zip Code</span>";
			$user_info=false;
		}
		
		if($user_data_array[0]['country_name']){
			if($user_data_array[0]['country_id']==225){
				$country=$user_data_array[0]['country_code'];
			}elseif($user_data_array[0]['country_id']==245){
				$country=$user_data_array[0]['country_custom'];
			}else{
				$country=$user_data_array[0]['country_name'];
			}
			if($country !='USA' and $country !='United States')
			$str_user_detail_for_footer.='<span class="country"> | '.$country.'</span>';
			else
			$str_user_detail_for_footer.='<span class="country"></span>';
		}else{
			$str_user_detail_for_footer.="<span class='country'></span>";
			$user_info=false;
		}
		$str_user_detail_for_footer.="</div></div>";
		$email_template_data['email_template_id']=$id;

		$fetch_conditions_array=array(
		'site_id'=>$id,
		'is_deleted'=>'No',
		'is_autoresponder'=>'1'
		);

		$total_pages=$this->Page_Model->get_page_count($fetch_conditions_array);
		$email_template_data['pages']=$this->Page_Model->get_page_data($fetch_conditions_array,$total_pages);

		/*************theme background color information********************/

		$block_all_data_count=$this->Autoresponder_Model->get_background_color_blocks_names_and_content_count(array('red_background_color_page_id'=>$email_template_data['pages'][0]['id']));

		$email_template_color_info=$this->Autoresponder_Model->get_background_color_blocks_names_and_content_data(array('red_background_color_page_id'=>$email_template_data['pages'][0]['id']),$block_all_data_count);

		foreach ($email_template_color_info AS $email_template_color){
			$expl_color=explode(":#",$email_template_color['red_background_color_block_content']);
			if($email_template_color['red_background_color_block_name']=="outer-background"){
				$email_template_data['outer_background']=$expl_color[1];
			}else{
				$email_template_data[$email_template_color['red_background_color_block_name']]=$expl_color[1];
			}
		}

		$email_template_info=$this->Autoresponder_Model->get_autoresponder_data(array('campaign_id'=>$id));
		$email_template_data['email_template_info']=$email_template_info[0];
		$template_info=$this->Autoresponder_Model->get_template_data(array('template_id'=>$email_template_data['email_template_info']['campaign_template_id']));

		$email_template_data['template_info']=$template_info[0];
		$email_template_data['member_id']=$this->session->userdata('member_id');


		#$template_base_path=base_url().'webappassets/email_templates/template';
		$template_base_path= $this->config->item('webappassets_path') .'email_templates/template';

		$template_path=$template_base_path.'/index.html';


		$email_template_data['template_info']['template_html']=file_get_contents($template_path);
		$filtered_html=preg_replace('#(href|src)=(["\'])([.\/]*)([^:"\']*)(["\'])#',

					'$1="'.$template_base_path.'/$4"',$email_template_data['template_info']['template_html']);

		$body_empty_text='<p class="empty-text">Drag here</p>';
		$search_arr=array('{logo}','{website_name}','{body_top}','{body_main}','{body_bottom}','{body_left}','{body_right}','{FOOTER}');

		$replace_arr=array('<span class="logo_txt" >click to upload logo</span>','<span class="empty-text">click to add website name</span>',$body_empty_text,$body_empty_text,"",$body_empty_text,$body_empty_text,ucwords($str_user_detail_for_footer));
		$filtered_html=str_replace($search_arr,$replace_arr,$filtered_html);

		$email_template_data['template_info']['filtered_html']= $filtered_html;

		$email_template_data['email_template_info']['body_empty_text']=$body_empty_text;

		$templates_count=$this->Autoresponder_Model->get_template_count(array('rect.is_active'=>1,'rect.is_delete'=>0));
		$template_data=$this->Autoresponder_Model->get_template_data(array('rect.is_active'=>1,'rect.is_delete'=>0),$templates_count);

		$email_template_data['template_data']=$template_data;

		//Count number of image bank
		$image_bank_count=$this->Autoresponder_Model->get_image_bank_count(array('img_is_status'=>1));

		//Get information of image bank
		$image_bank_data=$this->Autoresponder_Model->get_image_bank_data(array('img_is_status'=>1,'img_user_id'=>$this->session->userdata('member_id')),$templates_count);

		$image_bank_str='';
		$j=1;
		for($i=0;$i<count($image_bank_data);$i++)
		{
			$path_info=pathinfo($this->upload_path.'/image_bank/'.$this->session->userdata('email_template_id').'/uploaded_images/'.$image_bank_data[$i]['img_name']);
			//thumb image path
			$thumb_image_path=$path_info['filename'].".".$path_info['extension'];
			$library_images[]=$thumb_image_path;
		}
		$email_template_data['library_images']=$library_images;
		$this->session->set_userdata('show_layout_container', 1);
		$email_template_data['show_layout_container']=0;
		if($this->session->userdata('show_layout_container')==1)
		{
			$email_template_data['show_layout_container']=1;
			$this->session->set_userdata('show_layout_container', 0);
		}
		$email_template_data['theme_css']=$this->get_theme_css($email_template_data['email_template_info']['campaign_color_theme_id']);

		$email_template_data['user_info']=$user_info;
		$email_template_data['user_data']=$user_data_array[0];
		//Fetch Country name
		$country_info=$this->UserModel->get_country_data();
		$email_template_data['country_info']=$country_info;
		$email_template_data['is_auotresponder']=true;
		#Get shoreten url
		$email_template_data['shorten_url']=get_shorten_url();
		//Loads header, autoresponder and footer view.
		$this->load->view('newsletter/campaign_editor',$email_template_data);
	}
	function get_theme_colors(){
		//get themes color from database
		$theme_color=$this->Autoresponder_Model->get_theme_colors(array('is_delete'=>'0','is_active'=>'1'));
		$colors="";
		foreach ($theme_color as $color){
			$colors.='<tr id="'.$color['id'].'" class="color_theme_link" onclick="saveColorTheme('.$color['id'].',this)">
						<td>'.substr($color['theme_name'],0,15);
			if($color['member_id']!=-1){
				$colors.='<span class="close_link_span"><a  class="close-link theme_color_delete" id="theme_color_'.$color['id'].'"  ><img title="Delete" border="0"  src="'.base_url().'webappassets/images/close.gif?v=6-20-13"></a></span>';
			}
			$colors.='</td>
					<td id="outer_bg_'.$color['id'].'" style="background:'.$color['outer_bg'].';width:13px;" class="color" class="outer_bg">&nbsp; </td>
					<td id="body_bg_'.$color['id'].'"  style="background:'.$color['body_bg'].';width:13px;" class="body_bg">&nbsp;</td>
					<td id="footer_bg_'.$color['id'].'"  style="background:'.$color['footer_bg'].';width:13px;" class="footer_bg">&nbsp;</td>
					<td id="border_color_'.$color['id'].'"  style="background:'.$color['border_color'].';width:13px;" class="border_color">&nbsp;</td>
					</tr>';
		}
		echo $colors;
	}
	function get_theme_css($template_id,$method=""){
		//get themes color from database
		$theme_color= $this->Autoresponder_Model->get_theme_colors(array('id'=>$template_id));


		$style="";
		foreach($theme_color[0] as $elem=>$color){
			if($elem!='id' && $elem!='theme_name'){
				$arr_elem=explode('_',$elem,2);
				if($arr_elem[1]=="bg"){
					if($arr_elem[0]=="header"){
						$style.="<style class='header_style custome_style'>#header{
							background-color:$color;
						}</style>
						";
					}else if($arr_elem[0]=="body"){
						$style.="<style class='body_main_style custome_style'>
							#body_main{
								background-color:$color;
							}
							.body_bg{background-color:$color;}
							.body_bg_color{background-color:$color;}
						</style>
						";
					}else if($arr_elem[0]=="outer"){
						$style.="<style class='main-table_style custome_style'>
							#main-table{
								background-color:$color;
							}
							html, body{background-color:$color;}
							.diy-editor{background-color:$color;}
							#template_container{background-color:$color;}
							.outer_bg{background-color:$color;}
						</style>
						";
					}else{
						if($elem=="footer_bg"){
							$style.="<style class='footer_style custome_style'>
								#footer{
									background-color:$color;
								}
								.footer_bg{
									background-color:$color;
								}
								.footer_txt_color{
									background-color:$color;
								}
							</style>";
						}
					}
				}else if($arr_elem[1]=="color"){
					$style.="<style class='border_style custome_style'>
						#email_template_table{
							border-color:  $color;
						}
						.body_border {
							background-color:  $color;
						}
					</style>
					";
				}else if($elem=="footer_font_color"){
					if($color !='#'){
						$style.="<style class='footer_font_style custome_style'>
							#footer{
								color:  $color ;
							}
						</style>
						";
					}
				}else if($elem=="preheader_font_color"){
					if($color !='#'){
						$style.="<style class='preheader_font_color custome_style'>
						.preheader-text, .preheader-link {
							color:  $color;
						}
						</style>
						";
					}
				}
			}
		}
		if($method=="ajax"){
			echo $style;
		}else{
			return $style;
		}
	}
	/*********Function to cahnge the selected template**************/
	function change_template($template_id,$email_autoresponder_id){
		$email_template_id=$email_autoresponder_id; // emial template id

		//Fetch campaign data from database by campaign ID
		$campaign_array=$this->Autoresponder_Model->get_campaign_data(array('campaign_id'=>$email_autoresponder_id,'campaign_created_by'=>$this->session->userdata('member_id')));

		//Redirects user to listing page if user have not created this campaign or campaign does not exists
		if(!count($campaign_array))
		{
			redirect('newsletter/campaign');
		}
		//update email template id in campaign table
		$this->Autoresponder_Model->update_campaign(array('campaign_template_id'=>$template_id,'campaign_template_option'=>3),array('campaign_id'=>$email_template_id));

		$this->session->set_userdata('show_layout_container', 1);
		echo $email_template_id;
	}
	/*********Function to cahnge the selected theme**************/
	function change_theme($template_id,$email_autoresponder_id,$theme_id=0){
		$email_template_id=$email_autoresponder_id; // emial template id
		//Fetch campaign data from database by campaign ID
		$campaign_array=$this->Autoresponder_Model->get_autoresponder_data(array('campaign_id'=>$email_autoresponder_id,'campaign_created_by'=>$this->session->userdata('member_id')));

		//Redirects user to listing page if user have not created this campaign or campaign does not exists
		if(!count($campaign_array))
		{
			redirect('newsletter/autoresponder/display');
		}
		if($theme_id){
			//update email template id in campaign table
			$this->Autoresponder_Model->update_autoresponder(array('campaign_template_id'=>$template_id,'campaign_template_option'=>3,'campaign_theme_id'=>$theme_id),array('campaign_id'=>$email_template_id));
		}else{
			$this->Autoresponder_Model->update_autoresponder(array('campaign_color_theme_id'=>$template_id),array('campaign_id'=>$email_template_id));
		}
		$this->session->set_userdata('show_layout_container', 1);
		echo $email_template_id;
	}

	/**********Function to display all the email template screenshots**********/
	function get_template_data_for_ajax($campaign_id=0){
		//Fetch campaign data from database by campaign ID
		$campaign_array=$this->Autoresponder_Model->get_campaign_data(array('campaign_id'=>$campaign_id,'campaign_created_by'=>$this->session->userdata('member_id')));

		//Redirects user to listing page if user have not created this campaign or campaign does not exists
		if(!count($campaign_array))
		{
			redirect('newsletter/campaign');
		}
		//Count number of templates
		$templates_count=$this->Autoresponder_Model->get_campaign_template_count(array('rect.is_active'=>1,'rec.campaign_id'=>$campaign_id));

		//Get information of email template
		$template_data=$this->Autoresponder_Model->get_campaign_template_data(array('rect.is_active'=>1,'rec.campaign_id'=>$campaign_id),$templates_count);

		$template_str='';
		$j=1;
		for($i=0;$i<count($template_data);$i++)
		{
			//template screenshot path
			$template_img_path=base_url().'webappassets/email_templates/'.$template_data[$i]['template_name'].'/'.$template_data[$i]['screenshot'];
			$template_name=$template_data[$i]['template_name']; //template name
			$template_id=$template_data[$i]['template_id']; // template id

			$template_img='<a onclick="previewTemplate(\''.$template_name.'\','.$template_id.',this)"><img style="width:36px; height:36px;"  src="'.$template_img_path.' " ></a>';
			$template_str.='<li '.$class.'>'.$template_img.$template_links.'</li>';
			$j++;
		}
		echo $template_str;
	}

	/**
		Function to display all the email template for theme
	**/
	function get_template_data_for_theme($theme_id=0){		
		//Count number of templates
		$templates_count=$this->Campaign_Model->get_template_count(array('rect.is_active'=>1,'rect.is_delete'=>0,'rect.template_theme_id !='=>-1));
		//Get information of email template
		$template_data=$this->Campaign_Model->get_template_data(array('rect.is_active'=>1,'rect.is_delete'=>0,'rect.template_theme_id !='=>-1),$templates_count);
		//,'rect.show_on_dashboard'=>1
		if($theme_id){
			$themes_arr=array();
			foreach($template_data as $theme){
				$category_arr=explode(',',$theme['template_theme_id']);
				if (in_array($theme_id, $category_arr)) {
					$themes_arr[]=$theme;
				}
			}
			unset($template_data);
			$template_data=$themes_arr;
		}

		$template_str='';
		$j=1;
		for($i=0;$i<count($template_data);$i++)
		{
			if($template_data[$i]['template_id']!=-1){
				if($theme_id>0){
					//template screenshot path
					$template_img_path=$this->config->item('webappassets').'header-images/header-'.$template_data[$i]['template_id'].'.jpg';
					$template_name=$template_data[$i]['template_name']; //template name
					$template_id=$template_data[$i]['template_id']; // template id
					$template_theme_id=$template_data[$i]['template_theme_id']; // theme id
					$arr_template_theme_id=@explode(',',$template_theme_id); // theme id
					$template_theme_id = $arr_template_theme_id[0];

					$template_img='<div class="preview"><a href="javascript:;" onclick="saveTemplate(\''.$template_name.'\','.$template_id.','.$template_theme_id.')" class="banner-highlight"><img  src="'.$template_img_path.' " ></a></div>';
					$selectBannerButton = "<a href='javascript:;' onclick='saveTemplate(\"$template_name\",\"$template_id\",\"$template_theme_id\")' class='btn select'>Select</a>";


					$template_str.='<li class="article">'.$template_img.$template_links.$selectBannerButton.'</li>';
				}else if($template_data[$i]['show_on_dashboard']==1){
					//template screenshot path
					$template_img_path=$this->config->item('webappassets').'header-images/header-'.$template_data[$i]['template_id'].'.jpg';
					$template_name=$template_data[$i]['template_name']; //template name
					$template_id=$template_data[$i]['template_id']; // template id
					$template_theme_id=$template_data[$i]['template_theme_id']; // theme id
					$arr_template_theme_id=@explode(',',$template_theme_id); // theme id
					$template_theme_id = $arr_template_theme_id[0];

					$template_img='<div class="preview"><a href="javascript:;" onclick="saveTemplate(\''.$template_name.'\','.$template_id.','.$template_theme_id.')"><img src="'.$template_img_path.' " ></a></div>';

					$selectBannerButton = "<a href='javascript:;' onclick='saveTemplate(\"$template_name\",\"$template_id\",\"$template_theme_id\")' class='btn select'>Select</a>";



					$template_str.='<li class="article">'.$template_img.$template_links.$selectBannerButton.'</li>';
				}
			}
			$j++;
		}
		echo $template_str;
	}
	/**
	Function to display all the email template screenshots
**/
	function get_theme_data_for_ajax($category_id=0){
		//Count number of templates
		$templates_count=$this->Campaign_Model->get_theme_count(array('rect.red_is_active'=>1,'rect.red_is_delete'=>0,'rect.red_theme_id !='=>-1));
		$template_str='';
		$j=1;
		//Get information of email theme
		$campaign_data['theme_data']=$this->Campaign_Model->get_theme_data(array('rect.red_is_active'=>1,'rect.red_is_delete'=>0,'rect.red_theme_id !='=>-1),$templates_count);
		$template_str.="<span class='category_class'> Select Category: ";
		$template_str.="<select onchange='changeTheme()' id='category_list' >";
		$template_str.="<option>Select Theme</option>";
		foreach($campaign_data['theme_data'] as $theme_info){
			if(($category_id>0)&&($category_id==$theme_info['red_theme_id'])){
				$templates_count=$this->Campaign_Model->get_template_count(array('rect.is_active'=>1,'rect.is_delete'=>0,'rect.template_theme_id !='=>-1));
				$template_data=$this->Campaign_Model->get_template_data(array('rect.is_active'=>1,'rect.is_delete'=>0,'rect.template_theme_id !='=>-1),$templates_count);
				if($templates_count>0){
					$themes_arr=array();
					foreach($template_data as $theme){
						$category_arr=explode(',',$theme['template_theme_id']);
						if (in_array($category_id, $category_arr)) {
							$themes_arr[]=$theme;
						}
					}
					unset($template_data);
					$template_data=$themes_arr;
				}
				$campaign_data['template_data'][$theme_info['red_theme_id']]=$template_data;
				unset($template_data);
			}
			if(($category_id>0)&&($category_id==$theme_info['red_theme_id'])){
				$template_str.='<option value="'.$theme_info['red_theme_id'].'" selected="selected">'.$theme_info['red_theme_name'].'</option>';
			}else{
				$template_str.='<option value="'.$theme_info['red_theme_id'].'">'.$theme_info['red_theme_name'].'</option>';
			}
		}

		$template_str.="</select></span>";
		if($category_id<=0){
			$templates_count=$this->Campaign_Model->get_template_count(array('rect.is_active'=>1,'rect.is_delete'=>0,'rect.template_theme_id !='=>-1,'rect.show_on_dashboard'=>1));
			$template_data=$this->Campaign_Model->get_template_data(array('rect.is_active'=>1,'rect.is_delete'=>0,'rect.template_theme_id !='=>-1,'rect.show_on_dashboard'=>1),$templates_count);

			$campaign_data['template_data'][-1]=$template_data;
		}

		foreach($campaign_data['template_data'] as $key=>$theme_data){
			$template_str.='<div  class="div_template"><ul class="themes_ul">';
			foreach($theme_data as $theme){
				//template screenshot path
				$template_img_path=$this->config->item('webappassets').'header-images/header-'.$theme['template_id'].'.jpg';
				$template_id=$theme['template_id']; //template id
				$theme_id=$key; // template category  id
				$template_img='<img onclick="saveHeader(\''.$template_id.'\')" src="'.$template_img_path.'" border="0" />';
				$template_str.='<li '.$class.' >'.$template_img.$template_links.'</li>';
				$j++;
			}
			$template_str.='</ul></div>';
		}
		echo  $template_str;
		return $template_str;
	}
	/**********Function to display all the image  bank images************************/
	function get_image_bank_for_ajax(){

		//Count number of templates
		$templates_count=$this->Campaign_Model->get_template_count(array('rect.is_active'=>1,'rect.is_delete'=>0,'rect.template_theme_id !='=>-1));
		//Get information of email template
		$template_data=$this->Campaign_Model->get_template_data(array('rect.is_active'=>1,'rect.is_delete'=>0,'rect.template_theme_id !='=>-1),$templates_count);
		//,'rect.show_on_dashboard'=>1
		if($theme_id){
			$themes_arr=array();
			foreach($template_data as $theme){
				$category_arr=explode(',',$theme['template_theme_id']);
				if (in_array($theme_id, $category_arr)) {
					$themes_arr[]=$theme;
				}
			}
			unset($template_data);
			$template_data=$themes_arr;
		}

		$template_str='';
		$j=1;
		for($i=0;$i<count($template_data);$i++)
		{
			if($template_data[$i]['template_id']!=-1){
				if($theme_id>0){
					//template screenshot path
					$template_img_path=$this->config->item('webappassets').'header-images/header-'.$template_data[$i]['template_id'].'.jpg';
					$template_name=$template_data[$i]['template_name']; //template name
					$template_id=$template_data[$i]['template_id']; // template id
					$template_theme_id=$template_data[$i]['template_theme_id']; // theme id
					$arr_template_theme_id=@explode(',',$template_theme_id); // theme id
					$template_theme_id = $arr_template_theme_id[0];

					$template_img='<a href="javascript:;" onclick="saveTemplate(\''.$template_name.'\','.$template_id.','.$template_theme_id.')"><img  src="'.$template_img_path.' " ></a>';

					$template_str.='<li>'.$template_img.$template_links.'</li>';
				}else if($template_data[$i]['show_on_dashboard']==1){
					//template screenshot path
					$template_img_path=$this->config->item('webappassets').'header-images/header-'.$template_data[$i]['template_id'].'.jpg';
					$template_name=$template_data[$i]['template_name']; //template name
					$template_id=$template_data[$i]['template_id']; // template id
					$template_theme_id=$template_data[$i]['template_theme_id']; // theme id
					$arr_template_theme_id=@explode(',',$template_theme_id); // theme id
					$template_theme_id = $arr_template_theme_id[0];

					$template_img='<a href="javascript:;" onclick="saveTemplate(\''.$template_name.'\','.$template_id.','.$template_theme_id.')"><img  src="'.$template_img_path.' " ></a>';

					$template_str.='<li>'.$template_img.$template_links.'</li>';
				}
			}
			$j++;
		}
		echo $template_str;
	}

	/**
		Function to call information of selected template using ajax
	**/
	function get_selected_template_for_ajax($id){
		//Get content of email template
		$filtered_html=$this->autoresponder_editor($id,'ajax_content');
		echo $filtered_html;
	}
	/****************Preview of email template*************************/
	function email_template_view($id,$page_id=0)
	{
		$email_template_data=array();//Preapare array for email template data
		$email_template_data['email_template_id']=$id;  // id of email template

		// Where condition
		$fetch_conditions_array=array('site_id'=>$id, 'is_deleted'=>'No');

		//Get total number of pages in email template
		$total_pages=$this->Page_Model->get_page_count($fetch_conditions_array);

		//Get info of each page like page name ,position, id and title
		$email_template_data_pages=$this->Page_Model->get_page_data($fetch_conditions_array,$total_pages);

		if($page_id==0)
		{
			$page_id=$email_template_data_pages[0]['id'];  //Page id
		}
		//Get email template content
		$email_template_info=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$id));

		$email_template_data['email_template_info']=$email_template_info[0];

		//Get selected template information
		$template_info=$this->Campaign_Model->get_template_data(array('template_id'=>$email_template_data['email_template_info']['campaign_template_id']));

		$email_template_data['template_info']=$template_info[0];

		//Selected email template path
		#$template_base_path=base_url().'webappassets/email_templates/'.$email_template_data['template_info']['template_name'];
		$template_base_path= $this->config->item('webappassets_path') .'email_templates/'.$email_template_data['template_info']['template_name'];

		//Selected email template file path
		$template_path=$template_base_path.'/index.html';

		//Collect selected email template content
		$email_template_data['template_info']['template_html']=file_get_contents($template_path);

		$filtered_html=preg_replace('#(link href|src)=(["\'])([.\/]*)([^:"\']*)(["\'])#',

					'$1="'.$template_base_path.'/$4"',$email_template_data['template_info']['template_html']);

		$page_content_arr=array();

		//Get data of each block according to page id
		$page_content_data=$this->Campaign_Model->get_template_blocks_content_data(array('page_id'=>$page_id));

		//Store data of block  in variable according to each block name
		for($i=0;$i<count($page_content_data);$i++)
		{
			$page_content_arr[$page_content_data[$i]['block_name']]=$page_content_data[$i]['block_content'];
		}

		//Search block name
		$search_arr=array('{logo}','{website_name}','{body_top}','{body_main}','{body_bottom}','{body_left}','{body_right}','{footer}');

		//Replace block content according to block name
		$replace_arr=array($page_content_arr['logo'],$page_content_arr['website_name'],$page_content_arr['body_top'],$page_content_arr['body_main'],$page_content_arr['body_bottom'],$page_content_arr['body_left'],$page_content_arr['body_right'],$page_content_arr['footer']);

		$filtered_html=str_replace($search_arr,$replace_arr,$filtered_html);

		//Create link for each page
		for($i=0;$i<count($email_template_data_pages);$i++)
		{
			$p_id=$email_template_data_pages[$i]['id'];
			$title=$email_template_data_pages[$i]['title'];
			if($p_id==$page_id) $class='current';else $class='';
			$li_str.='<li><a href="'.base_url().'diy/website/view/'.$id.'/'.$p_id.'" class="'.$class.'">'.$title.'</a></li>';
		}

		$ul_str='<ul id="pages_menu">'.$li_str.'</ul>';
		$filtered_html=str_replace('<ul id="pages_menu"><li><a href="#" class="current">Home</a></li></ul>',$ul_str,$filtered_html);

		$email_template_data['template_info']['filtered_html']= $filtered_html;

		//Load view of email template
		$this->load->view('newsletter/email_template_view',$email_template_data);
	}


	/********Function to copy  files in folder**********************/
	function recurse_copy($src,$dst)
	{

		/***** Open source directory*****************/
		if($dir = opendir($src)){
		@mkdir($dst); // create destination directory

		/***********copy from from src directory to destination directory********************/
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					$this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
				}
				else {
					copy($src . '/' . $file,$dst . '/' . $file);
					chmod($dst . '/' . $file,0777);
				}
			}
		}
		closedir($dir);// close directory
		}
	}
	/*

		'create_group' controller function to create new autoresponder group.

		This controller is used to create new autoresponder group.

	*/


	function create_group(){
		// To check form is submittted
		if($this->input->post('action')=='submit')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('autoresponder_title', 'Autoresponder Title', 'required|min_length[2]|max_length[250]|trim|callback_validate_autoresponder_group_title');
			// To check form is validated
			if($this->form_validation->run()==true)
			{
				// Retrieve data posted in form posted by user using input class
				$input_array=array('group_name'=>$this->input->get_post('autoresponder_title', TRUE),
				'autoresponder_subscription_id'=>$this->input->get_post('autoresponder_subscription_id', TRUE),
				'autoresponder_created_by'=>$this->session->userdata('member_id')
				);

				// Sends form input data to database via model object

				$autoresponder_id=$this->Autoresponder_Model->create_autoresponder_group($input_array);
				echo 'success:'.$autoresponder_id;
			}else{
				echo 'error:'.validation_errors();
			}
		}

	}
	// update_autoresponder_grp controller function for update the status of autorespnder group
	function update_autoresponder_grp($grp_id=0){ 
		$grp_status=$_POST['status'];
		// Update autoresponder message status
		$this->Autoresponder_Model->update_autoresponder(array('campaign_status'=>$grp_status),array('autoresponder_group_id'=>$grp_id));
		// Update autoresponder group message status
		$this->Autoresponder_Model->update_autoresponder_group(array('status'=>$grp_status),array('id'=>$grp_id,'autoresponder_created_by'=>$this->session->userdata('member_id')));
	}
	// update_group controller function for update the  autorespnder group
	function update_group($grp_id=0){ 
		if(!is_numeric($grp_id)){
			redirect('/');
		}else{
			// Validation rules are applied
			$this->form_validation->set_rules('autoresponder_title', 'Autoresponder Title', 'required|min_length[2]|max_length[250]|trim|callback_validate_autoresponder_group_title');
			// To check form is validated
			if($this->form_validation->run()==true)
			{
			// Update autoresponder group message status
				$this->Autoresponder_Model->update_autoresponder_group(array('group_name'=>$this->input->get_post('autoresponder_title', TRUE),'autoresponder_subscription_id'=>$this->input->get_post('autoresponder_subscription_id', TRUE)),array('id'=>$grp_id,'autoresponder_created_by'=>$this->session->userdata('member_id')));
				// get campaigna according to autoresponder group id
				$condition_array=array('autoresponder_group_id'=>$grp_id,
										'is_deleted'=>0);
				$result=$this->Autoresponder_Model->get_autoresponder_data($condition_array);
				if(count($result)>0){
					//update scheduled autoresponder in database
					foreach($result as $campaign){
						$input_array=array('subscription_ids'=>$this->input->get_post('autoresponder_subscription_id', TRUE));
						$this->Autoresponder_Model->update_scheduled_autoresponder($input_array,array('autoresponder_id'=>$campaign['campaign_id']));
					}
				}
				echo 'success:'.$grp_id;
			}else{
				echo 'error:'.validation_errors();
			}
		}
	}
	/**
		Function validate_autoresponder_group to validate autoresponder group title
	**/
	function validate_autoresponder_group_title(){ 

		/*
			Creating array of conditions to checked with database with conditions.
			To check if title entered by user already exists in database.
			To check if ID is not as current subscriber in case of update

		*/
		$conditions_array['group_name']=$this->input->get_post('autoresponder_title', TRUE);	//Subscription title
		$conditions_array['autoresponder_created_by']=$this->session->userdata('member_id');	//member id
		$conditions_array['is_deleted']=0;	//check deleted or not

		//check subscription id exist or not
		if($this->input->get_post('autoresponder_grp_id', TRUE)!='')
			$conditions_array['id !=']=$this->input->get_post('autoresponder_grp_id', TRUE);
		//Get subscription data from database using subscription_Model
		$result=$this->Autoresponder_Model->get_autoresponder_group($conditions_array);

		// returns true if title exits and false if not exits.
		if(count($result))
		{
			$this->form_validation->set_message('validate_autoresponder_group_title', 'The %s already exists');
			return FALSE;
		}
		else
			return true;
	}

	/*

		'Delete_group' controller function for deleting of autoresponder group.

	*/

	function delete_group($id){

		//Fetch autoresponder data from database by autoresponder ID

		$autoresponder_array=$this->Autoresponder_Model->get_autoresponder_group(array('id'=>$id,'autoresponder_created_by'=>$this->session->userdata('member_id')));

		//Redirects user to listing page if user have not created this autoresponder or autoresponder does not exists
		if(!count($autoresponder_array))
		{
			// Assign  error message by message class
			$this->messages->add('Autoresponder does not exists or you have not created this autoresponder', 'error');
			// Redirect to listing of campaigns

			redirect('newsletter/autoresponder/display');

		}

		// Deletes autoresponder according to autoresponder ID

		$this->Autoresponder_Model->delete_autoresponder_group(array('id'=>$id));

	}
	/**
		function to share a link on facebook
	**/
	function share_link($id=0){
		#Get shoreten url
		$shorten_url=get_shorten_url();
		$share_url= CAMPAIGN_DOMAIN.'a/'.$id;
		echo "<div style='width:100%;float:left;'><div style='float:none;margin:20px;text-align:center;color:#000;'><div style='margin-bottom:5px;'><b>Copy & Paste URL below to Facebook</b></div><div><b> page or any other sharing medium.</b></div></div>";
		echo "<div style='float:none;margin:5px;'><input type='text' value='".$share_url."' style='width:520px;' /></div><div style='float:none;margin:13px;text-align:center;'><img src='".base_url()."webappassets/images-front/facebook-share.png' alt='Share on facebook'></div></div>";
	}
	/**
		Function to change editing of interval time.
	**/
	function edit_interval_time($id=0){
		// To check form is submittted
		if($this->input->post('action')=='save'){
			// Validation rules are applied
			$this->form_validation->set_rules('number_of_days', 'Interval Time', 'required|min_length[1]|is_natural|trim');
			// To check form is validated
			if($this->form_validation->run()==true)
			{
				//Fetch autoresponder info
				$fetch_conditions_array=array('campaign_created_by'=>$this->session->userdata('member_id'), 'campaign_id'=>$id, 'is_deleted'=>0	);
				$autoresponders_array=array();
				$autoresponders_array=$this->Autoresponder_Model->get_autoresponder_data($fetch_conditions_array);
				if($autoresponders_array[0]['autoresponder_scheduled_id']>0){
					//Create input array to send to database
					$input_array=array('autoresponder_scheduled_interval'=>$this->input->post('number_of_days'));

					//Store scheduled autoresponder in database
					$this->Autoresponder_Model->update_scheduled_autoresponder($input_array,array('autoresponder_scheduled_id'=>$autoresponders_array[0]['autoresponder_scheduled_id']));

					//Update autoresponder
					$this->Autoresponder_Model->update_autoresponder(array('campaign_status'=>'1','autoresponder_scheduled_interval'=>$this->input->post('number_of_days')),array('campaign_id'=>$id));
					echo 'success:'.$this->input->post('number_of_days');
				}
			}else{
				echo 'error:'.validation_errors();
			}
		}else{
			//Load view of email template
			$this->load->view('newsletter/interval_time',array('autoresponder_id'=>$id));
		}
	}

	/**
	*	Function update_company_info_on_campaign to update comapny info on created campaign
	**/
	function update_company_info_on_campaign($campaign_id=0){
		/**
			*Load simple_html_dom plugin for update footer*
		**/
		$this->load->helper('simple_html_dom');
		$company		=	$this->input->post('company');
		$address_line_1	=	$this->input->post('address_line_1');
		$city			=	$this->input->post('city');
		$state			=	$this->input->post('state');
		$zipcode		=	$this->input->post('zipcode');
		$country_id		=	$this->input->post('country');
		if(trim($country_id) != '245'){
			$qry="select country_name FROM red_countries WHERE country_id 	='".$country_id."'";
			$country_qry=$this->db->query($qry);	#execute query
			$country_data_array=$country_qry->result_array();	#Fetch resut
			if(count($country_data_array)>0){
				$country=$country_data_array[0]['country_name'];
			}
		}else{
				$country =$this->input->post('country_custom');		
		}	 
		//Fetch campaign data from database by campaign ID
		$campaign_array=$this->Autoresponder_Model->get_autoresponder_data(array('campaign_id'=>$campaign_id,'campaign_created_by'=>$this->session->userdata('member_id')));

		$campaign=change_footer($company,$address_line_1,$city,$state,$zipcode,$country,$campaign_array[0]['campaign_content'],$campaign_array[0]['campaign_email_content']);
		$campaign_cnt_arr=explode('xxx_campaign_content_xxx',$campaign);
		// Load Html to text plugin
		$this->load->helper('htmltotext');
		$text_html=html2text($campaign_cnt_arr[0]);
 
		// Update campaign by data posted by user
		$this->Autoresponder_Model->update_autoresponder(array('campaign_content'=>$campaign_cnt_arr[0],'campaign_email_content '=>$campaign_cnt_arr[1],'campaign_text_content '=>$text_html),array('campaign_id'=>$campaign_array[0]['campaign_id'],'campaign_created_by'=>$this->session->userdata('member_id')));
	}
}
?>
