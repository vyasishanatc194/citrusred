<?php

/*
Controller class for templates
It have controller functions for template management.
*/

class Templates_Manage extends CI_Controller
{
	/*
		Contructor for controller.
		It checks user session and redirects user if not logged in
	*/

	function __construct()
	{
		parent::__construct();
		if($this->session->userdata('webmaster_id')=='')
			redirect('webmaster/account/login');

		$this->load->library('upload');
		$this->load->model('webmaster/Template_Model');
		
		# HTTPS/SSL enabled
		force_ssl();

	}

	/*
		'Index' controller. By default it calls display controller.
	*/

	function index()
	{
		$this->display();
	}

	/*
		'Create' controller function to create new campaign.
		This controller is used to create new campaign.
	*/

	function template_create()
	{
		//$this->output->enable_profiler(TRUE);

		//Load FCK Editor for campaign content
		$this->load->library('fckeditor',array('instanceName' => 'template_content'));  

		// Prepare array to send to view

		$template_data=array();

		// To check form is submittted to import from zip file

		if($this->input->post('action')=='import_zip')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('template_import_zip_file', 'Template Import Zip File', 'callback_validate_upload');

			// To check form is validated
			if($this->form_validation->run()==true)
			{
				$html=$this->extracted_zip_html;
				$template_data=array(
				'template_content'=>$html,
				);
			}
		}

		// To check form is submittted for saving template
		if($this->input->post('action')=='save')
		{

			// Validation rules are applied
			$this->form_validation->set_rules('template_title', 'Template Title', 'required|min_length[2]|max_length[250]|trim');
			$this->form_validation->set_rules('template_content', 'Template Content', 'required|trim');
			$this->form_validation->set_rules('template_status', 'Template Status', 'required|trim');

			$template_content=$this->input->get_post('template_content', true);
			$template_content=html_entity_decode($template_content);
			$template_content=str_replace(array('[removed]'),array(''),$template_content);

			// Retrieve data posted in form posted by user using input class
			$input_array=array('template_title'=>$this->input->get_post('template_title', TRUE),
			'template_content'=>$template_content,
			'template_status'=>$this->input->get_post('template_status', TRUE)
			);

			// To check form is validated
			if($this->form_validation->run()==true)
			{
				// Sends form input data to database via model object
				$this->Template_Model->create_template($input_array);

				// Assign success message by message class
				$this->messages->add('Template created successfully', 'success');

				// Redirect to listing of templates
				redirect('webmaster/templates_manage/templates_list');
			}

			//If validaion fails then, then send post data to view
			$template_data=$input_array;

		}
		$logo_link="webmaster/dashboard_stat";
		//Loads header, template and footer view.
		$this->load->view('webmaster/header',array('title'=>'Create Template','logo_link'=>$logo_link));
		$this->load->view('webmaster/template_create',array('template_data'=>$template_data));
		$this->load->view('webmaster/footer');
	}

	

	/*

		'Edit' controller function to edit existing template.

		This controller is used to edit existing template.

	*/

	

	function template_edit($id)
	{

		//$this->output->enable_profiler(TRUE);
		
		//Load FCK Editor for campaign content
		$this->load->library('fckeditor',array('instanceName' => 'template_content'));  

		//Initialize template data array to store data for template to be edited
		$template_data=array();

		// To check form is submitted
		if($this->input->post('action')=='save')
		{
			// Validation rules are applied
			$this->form_validation->set_rules('template_title', 'Template Title', 'required|min_length[2]|max_length[250]|trim');
			$this->form_validation->set_rules('template_content', 'Template Content', 'required|trim');
			$this->form_validation->set_rules('template_status', 'Template Status', 'required|trim');

			$template_content=$this->input->get_post('template_content', true);
			$template_content=html_entity_decode($template_content);
			$template_content=str_replace(array('[removed]'),array(''),$template_content);

			

			// Retrieve data posted in form posted by user using input class
			$input_array=array('template_title'=>$this->input->get_post('template_title', TRUE),
			'template_content'=>$template_content,
			'template_status'=>$this->input->get_post('template_status', TRUE)
			);

			// To check form is validated
			if($this->form_validation->run()==true)
			{
				// Update template by data posted by user
				$this->Template_Model->update_template($input_array,array('template_id'=>$id));

				// Display success message by message class
				$this->messages->add('Template updated successfully', 'success');

				// Redirect to listing of templates
				redirect('webmaster/templates_manage/templates_list');

			}

			/*If form is validated then user will be redirected to listing of templates.
			But if validation fails, then form is populated by input posted by user on last form submission.
			*/

			$template_data=$input_array;

		}

		

		/* Campaigns will have count as zero or null when form is not posted.
		In this case, retreive template data from database according to template ID.
		*/

		if(!count($template_data))
		{

			//Fetch template data from database by template ID
			$template_array=$this->Template_Model->get_template_data(array('template_id'=>$id));

			// Prepare array to send to view
			$template_data=array('template_title'=>$template_array[0]['template_title'],
			'template_content'=>$template_array[0]['template_content'],
			'template_status'=>$template_array[0]['template_status'],
			);
		}

		// Add template ID to template array
		$template_data['template_id']=$id;
		$logo_link="webmaster/dashboard_stat";
		//Loads header, template and footer view.
		$this->load->view('webmaster/header',array('title'=>'Edit Template','logo_link'=>$logo_link));
		$this->load->view('webmaster/template_edit',array('template_data'=>$template_data));
		$this->load->view('webmaster/footer');

	}

	

	/*

		'Dislay' controller function for listing of campaigns.

		

	*/

	

	function templates_list($start=0)
	{
		////$this->output->enable_profiler(TRUE); 

		// Recieve any messages to be shown, when template is added or updated
		$messages=$this->messages->get();

		$fetch_conditions_array=array(
		'is_deleted'=>0
		);

		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'newsletter/template/display';
		$config['total_rows']=$this->Template_Model->get_template_count($fetch_conditions_array);
		$config['per_page']=10;
		$config['uri_segment']=4;

		// Initialize paging with above parameters
		$this->pagination->initialize($config);

 

		//Create paging inks
		$template_data['links']=$this->pagination->create_links();

		// Fetches template data from database 
		$template_data['templates']=$this->Template_Model->get_template_data($fetch_conditions_array,$config['per_page'],$start);

		//Assign messages to array to be send to view.
		$template_data['messages'] =$messages;
		$logo_link="webmaster/dashboard_stat";
		//Loads header, template and footer view.
		$this->load->view('webmaster/header',array('title'=>'List Templates','logo_link'=>$logo_link));
		$this->load->view('webmaster/templates_list',$template_data);
		$this->load->view('webmaster/footer');
	}

	

	/*

		'Delete' controller function for deleting of campaign.

	*/

	

	function delete($id)

	{

		// Load campaign model class which handles database interaction

		$this->load->model('newsletter/Template_Model');

		

		//Fetch campaign data from database by campaign ID

		$campaign_array=$this->Template_Model->get_campaign_data(array('campaign_id'=>$id,'campaign_created_by'=>$this->session->userdata('member_id')));

			

		//Redirects user to listing page if user have not created this campaign or campaign does not exists

		if(!count($campaign_array))

		{

			// Assign  error message by message class

			$this->messages->add('Template does not exists or you have not created this campaign', 'error');

		

			// Redirect to listing of campaigns

			redirect('newsletter/campaign');

		}

		

		// Deletes campaign according to campaign ID

		$this->Template_Model->delete_campaign(array('campaign_id'=>$id));

		

		// Assign  success message by message class

		$this->messages->add('Template deleted successfully', 'success');

		

		// Redirect to listing of campaigns

		redirect('newsletter/campaign');

	}

	

	

	/*

		'Copy Archived' controller function to copy campaign from archived campaign

		

	*/

	function copy_archived()

	{

		//$this->output->enable_profiler(TRUE);

		

		// Load campaign model class which handles database interaction

		$this->load->model('newsletter/Template_Model');

		

		//Load FCK Editor for campaign content

		$this->load->library('fckeditor',array('instanceName' => 'campaign_content'));  

		

		// Prepare array to send to view

		$campaign_data=array();

		

		// To check form is submittted to load campaign

		if($this->input->post('action')=='load_campaign')

		{

			//Fetch list of archived campaign

			$fetch_conditions_array=array(

			'campaign_id'=>$this->input->get_post('archived_campaigns', TRUE),

			);

			

			$loaded_campaign=$this->Campaign_Model->get_campaign_data($fetch_conditions_array);

			

			$campaign_data=array('campaign_title'=>$loaded_campaign[0]['campaign_title'],

			'campaign_content'=>$loaded_campaign[0]['campaign_content'],

			'campaign_status'=>$loaded_campaign[0]['campaign_status'],

			);

			

		}

		

		

		// To check form is submittted for saving campaign

		if($this->input->post('action')=='save')

		{

			// Validation rules are applied

			$this->form_validation->set_rules('campaign_title', 'Campaign Title', 'required|min_length[2]|max_length[250]|trim');

			$this->form_validation->set_rules('campaign_content', 'Campaign Content', 'required|trim');

			$this->form_validation->set_rules('campaign_status', 'Campaign Status', 'required|trim');

			

			$campaign_content=$this->input->get_post('campaign_content', true);

			$campaign_content=html_entity_decode($campaign_content);

			$campaign_content=str_replace(array('[removed]'),array(''),$campaign_content);

			

			

			// Retrieve data posted in form posted by user using input class

				$input_array=array('campaign_title'=>$this->input->get_post('campaign_title', TRUE),
				'campaign_content'=>$campaign_content,
				'campaign_created_by'=>$this->session->userdata('member_id'),
				'campaign_status'=>$this->input->get_post('campaign_status', TRUE),						
				'campaign_email_content'=>NULL,	'campaign_text_content'=>NULL,	'email_subject'=>null,	'sender_email'=>null,	'sender_name'=>null,'subscription_list'=>null,'click_url'=>NULL,'campaign_after_encode_url'=>NULL);

			if($this->form_validation->run()==true){ 
				$this->load->model('newsletter/Campaign_Model');			

				// Sends form input data to database via model object

				$this->Campaign_Model->create_campaign($input_array);

				

				// Assign success message by message class

				$this->messages->add('Campaign created successfully', 'success');

				

				// Redirect to listing of campaigns

				redirect('newsletter/campaign');

			}

			

			//If validaion fails then, then send post data to view

			$campaign_data=$input_array;

		}

		

		

		

		//Fetch list of archived campaign

		$fetch_condiotions_array=array(

		'campaign_created_by'=>$this->session->userdata('member_id'),

		'is_deleted'=>0,

		'campaign_status'=>'archived'

		);

		

		$archived_campaigns_array=array();

		$archived_campaign_count=$this->Campaign_Model->get_campaign_count($fetch_condiotions_array);

		$archived_campaigns=$this->Campaign_Model->get_campaign_data($fetch_condiotions_array,$archived_campaign_count);

		

		//Assign archived campaigns to send to view.

		foreach($archived_campaigns as $campaign)

			$archived_campaigns_array[$campaign['campaign_id']]=$campaign['campaign_title'];

			

		$archived_campaigns_array[0]='select one';	

		ksort($archived_campaigns_array);

		$campaign_data['archived_campaigns'] =$archived_campaigns_array;

		

		//Assign messages to array to be send to view.

		$campaign_data['messages'] =$messages;

		
		$logo_link="webmaster/dashboard_stat";
		//Loads header, campaign and footer view.

		$this->load->view('header',array('title'=>'Copy Campaigns','logo_link'=>$logo_link));

		$this->load->view('newsletter/campaign_copy_archived',array('campaign_data'=>$campaign_data));

		$this->load->view('footer');

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
		//Get absolute path for uploading
		$this->upload_path=substr(FCPATH,0,strrpos(FCPATH,'/'));

		//Append document uploading path to absolute path
		$this->upload_path= $this->upload_path.'/webappassets/email_templates/';

		
		// Initialization upload configuration
		$upload_config	=array();
		$upload_config['upload_path'] = $this->upload_path;
		$upload_config['allowed_types'] = 'zip|rar';
		$upload_config['max_size']	= 1024*5; //5MB
		
		$this->upload->initialize($upload_config);

		//check if file is uploaded successfully

		if(!$this->upload->do_upload('template_import_zip_file'))
		{
			//displays error message if uploading fails
			$this->form_validation->set_message('validate_upload', $this->upload->display_errors());
			return false;
		}
		else
		{
			//Get data of uploaded file
			$uploaded_file_array=$this->upload->data();
			

			//Initialize php archive class and extract archive

			$zip = new ZipArchive;

			if ($zip->open($uploaded_file_array['full_path']) === TRUE) {

				$zip->extractTo($uploaded_file_array['file_path'].'/'.$uploaded_file_array['raw_name']);

				$zip->close();

			}

			// file names allowed for extracting campaign html
			$file_names_allowed=array('index.htm','index.html','default.htm','default.html');

			$files=array();
			$directories=array();
			$new_file_name=$uploaded_file_array['raw_name'];
			
			//Open directory for reading and put files in files arrray
			// and directories in directories array

			$dir_handle=opendir($this->upload_path.'/'.$new_file_name);

			 while (false !== ($file = readdir($dir_handle))) 
			 {
				if($file!='.' && $file!='..')
				{
					if(is_dir($this->upload_path.'/'.$new_file_name.'/'.$file))
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
						$extracted_file= $this->upload_path.'/'.$new_file_name.'/'.$file_names_allowed[$key];
						$path_to_images=base_url().'/webappassets/email_templates/'.$new_file_name;
						break;
					}
				}
			}

			

			//If extracted directory have directories in it, then iterate in first directory to search

			// any of allowed files in it.

			if($extracted_file=='' && count($directories))

			{

				$directory=$directories[0];

				$dir_handle=opendir($this->upload_path.'/'.$new_file_name.'/'.$directory);

				while (false !== ($file = readdir($dir_handle))) {

					if($file!='.' || $file!='..')

					{

						if(in_array($file,$file_names_allowed))

						{

							$key=array_search($file,$file_names_allowed);

							$extracted_file=$this->upload_path.'/'.$new_file_name.'/'.$directory.'/'. $file_names_allowed[$key];

							$path_to_images=base_url().'/webappassets/email_templates/'.$new_file_name.'/'.$directory;

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

	function to fetch HTML source from URL

	*/

	function get_html_from_url($url)

	{

		//get html source of URL 

		$html= file_get_contents($url);

		

		//fetch domain name from URL like http://www.domain.com from http://www.domain.com/page.html

		preg_match('@(http://)([^/]+)@i',$url, $matches);

		$domain_name=$matches[0];

		

		//Filter HTML by replacing relative path of images, css, javascript to absolute path

		// by appending domain name to them

		$filtered_html=preg_replace('#(href|src)=(["\'])([.\/]*)([^:"\']*)(["\'])#',

					'$1="'.$domain_name.'/$4"',$html);

					

		//return filtered HTML			

		return $filtered_html;

	}

	

	/*

		Controller function to send campaign Newsletter

	*/

	

	function send()

	{

		//$this->output->enable_profiler(TRUE);

		

		// Load campaign model class which handles database interaction

		$this->load->model('newsletter/Campaign_Model');

		

		// Load subscriber model class which handles database interaction

		$this->load->model('newsletter/Subscriber_Model');

		

		//Load email library

		$this->load->library('email');

		

		//Fetch list of active campaign

		$fetch_conditions_array=array(

		'campaign_created_by'=>$this->session->userdata('member_id'),

		'is_deleted'=>0,

		'campaign_status'=>'active'

		);

		

		$active_campaigns_array=array();

		$active_campaign_count=$this->Campaign_Model->get_campaign_count($fetch_conditions_array);

		$active_campaigns=$this->Campaign_Model->get_campaign_data($fetch_conditions_array,$active_campaign_count);

		

		//Assign active campaigns to send to view.

		foreach($active_campaigns as $campaign)

			$active_campaigns_array[$campaign['campaign_id']]=$campaign['campaign_title'];

		

		//Assign 'select one' as first element of array

		$active_campaigns_array[0]='select one';	

		

		//sorting array by index

		ksort($active_campaigns_array);

		$campaign_data['campaigns'] =$active_campaigns_array;

		

		// Load Subscription model class which handles database interaction

		$this->load->model('newsletter/Subscription_Model');

		// Load subscriptions created by user

		$fetch_conditions_array=array(

		'subscription_created_by'=>$this->session->userdata('member_id'),

		'is_deleted'=>0,

		'subscription_status'=>1

		);

		

		//Fetch Subscription list created by user

		$subscriptions_count=$this->Subscription_Model->get_subscription_count($fetch_conditions_array);

		$subscriptions=$this->Subscription_Model->get_subscription_data($fetch_conditions_array,$subscriptions_count);

		

		$subscription_data=array('subscriptions'=>$subscriptions);

		

		

		// To check form is submittted and action is send

		if($this->input->post('action')=='send')

		{
		
				//Recieve scheduled datetime posted by user

				//Date submitted by calendar in ddmmyyyy format

				//, convert it into yyyymmdd format

				$scheduled_datetime=$this->input->post('scheduled_date');
				if($scheduled_datetime!=''){
					$scheduled_datetime_arr=explode(' ',$scheduled_datetime);

					$scheduled_date=$scheduled_datetime_arr[0];

					$scheduled_time=$scheduled_datetime_arr[1];

					$scheduled_date_arr=explode('-',$scheduled_date);

					$scheduled_date=$scheduled_date_arr[2].'/'.$scheduled_date_arr[1].'/'.$scheduled_date_arr[0];

					$scheduled_datetime=$scheduled_date.' '.$scheduled_time;
				}
				$this->scheduled_datetime=$scheduled_datetime;
				

				$scheduled_timestamp=strtotime($scheduled_datetime);

				$current_timestamp=time();

			// Validation rules are applied

			$this->form_validation->set_rules('campaigns', 'Campaign', 'is_natural_no_zero');

			$this->form_validation->set_message('is_natural_no_zero', 'The Campaign is Required Field');

			$this->form_validation->set_rules('subscriptions[]', 'Subscriptions', 'required');

			//$this->form_validation->set_rules('scheduled_date', 'Scheduled Date', 'required');
			$this->form_validation->set_rules('scheduled_date', 'Scheduled Date', 'callback_validate_scheduled_date');
			

			/// To check form is validated

			if($this->form_validation->run()==true)

			{

				//Recieve subscription and campaign posted by user

				$subscriptions=$this->input->post('subscriptions');

				$campaign_id=$this->input->post('campaigns');

				

				

				

				//To check camopaign is scheduled to sent now or in time less than now, then

				// send it now, otherwise store campaign in campaigns scheduled table

				if($scheduled_timestamp<$current_timestamp)

				{

					$subscribers_to_email_sent_array=array();

					$unique_subscriber_emails_array=array();

					//Iterate through subscriptions to be copied

					foreach($subscriptions as $subscription)

					{

						//Fetch subscribers of subscriptions

						$subscriber_count=$this->Subscriber_Model->get_subscriber_count(array('res.subscription_id'=>$subscription,'res.is_deleted'=>0));

						$subscriber_array=$this->Subscriber_Model->get_subscriber_data(array('res.subscription_id'=>$subscription,'res.is_deleted'=>0,'res.subscriber_status'=>1),$subscriber_count);

						

						//Iterate through subscribers array

						foreach($subscriber_array as $subscriber)

						{

							//Check If subscriber email address not unique email address array

							if(!in_array($subscriber['subscriber_email_address'],$unique_subscriber_emails_array))

							{

								//Create array of subscribers to whom email newsletter will be sent

								//Create array of unique email address

								$subscribers_to_email_sent_array[]=	$subscriber;

								$unique_subscriber_emails_array[]=$subscriber['subscriber_email_address'];

							}

							

						}

					}

					

					//Fetch campaign data

					$sent_campaign=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$campaign_id));

					

					

					//Iterate through array of subscribers to whom email is sent

					foreach($subscribers_to_email_sent_array as $subscriber)

					{
						
						$config=array();
						$config['mailtype'] = 'html';
						$config['priority'] = 1;
						$this->email->initialize($config);

						//Compose email and send email
						$this->email->from('system@t34m.net', 'T34m');
						$this->email->to($subscriber['subscriber_email_address']);

						$email_str=$sent_campaign[0]['campaign_content'];

						$this->email->subject($sent_campaign[0]['campaign_title']);

						$this->email->message($email_str);
						
					
						
						$this->email->send();
						
					}

					

					//Update campaign status to archived

					$this->Campaign_Model->update_campaign(array('campaign_status'=>'archived'),array('campaign_id'=>$campaign_id));
					
					// Assign  success message by message class
					$this->messages->add('Campaign Sent successfully', 'success');
		
					// Redirect to listing of campaigns
					redirect('newsletter/campaign/send');

				}

				else

				{

					//convert subsciption array into comma separated string

					$subscription_ids_str=implode(',',$subscriptions);

					

					//Create input array to send to database

					$input_array=array('campaign_id'=>$campaign_id,

					'subscription_ids'=>$subscription_ids_str,

					'campaign_scheduled_date'=>$scheduled_datetime);

					

					//Store scheduled campaign in database

					$this->Campaign_Model->create_scheduled_campaign($input_array);

					

					//Update campaign status to archived

					$this->Campaign_Model->update_campaign(array('campaign_status'=>'archived'),array('campaign_id'=>$campaign_id));
					
					// Assign  success message by message class
					$this->messages->add('Campaign Scheduled successfully', 'success');
		
					// Redirect to listing of campaigns
					redirect('newsletter/campaign/send');

				}

			}

		}

		
		$messages=$this->messages->get();
		$logo_link="webmaster/dashboard_stat";
		//Loads header, campaign and footer view.

		$this->load->view('header',array('title'=>'Send Campaigns','logo_link'=>$logo_link));

		$this->load->view('newsletter/campaign_send',array('messages'=>$messages,'campaign_data'=>$campaign_data,'subscription_data'=>$subscription_data));

		$this->load->view('footer');

		

	}
	
	function validate_scheduled_date()
	{
		if($this->scheduled_datetime=='')
		{
			return true;
		}		
		else
		{
			$current_timestamp=time();
			$scheduled_timestamp=strtotime($this->scheduled_datetime);
			if($scheduled_timestamp<$current_timestamp)
			{
				$this->form_validation->set_message('validate_scheduled_date', 'The %s field can not be older than current date');
				return false;
			}
			else			
			{
				return true;
			}
		}
	}

}
?>