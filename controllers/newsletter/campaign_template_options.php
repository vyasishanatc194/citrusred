<?php
/**
* A Campaign_template_options class
*
* This class is for selecting campaign template
*
* @version 1.0
* @author Pravin Jha <pravinjha@gmail.com>
* @project Redcappi
*/
class Campaign_template_options extends CI_Controller
{
	/**
	  *	Contructor for controller.
	  *	It checks user session and redirects user if not logged in
	 */
	function __construct(){
        parent::__construct();
		// if memeber is not login then redirect to login page
		if($this->session->userdata('member_id')=='')
			redirect('user/index');
		
		$this->load->library('upload');
		$this->load->helper('notification');
		$this->load->helper('htmltotext');
		$this->load->model('newsletter/Autoresponder_Model');
		$this->load->model('newsletter/Campaign_Model');
		$this->load->model('newsletter/Subscriber_Model');
		$this->load->model('Activity_Model');					
		$this->load->model('UserModel');		
		$this->load->model('newsletter/Campaign_Autoresponder_Model');
		
				
		
		// Check if folder with modulo of User ID exists on server
		$user_dir = $this->session->userdata('member_id') % 1000;
		
		// Get absolute path for uploading		
		$this->upload_path= $this->config->item('user_public').$user_dir .'/'.$this->session->userdata('member_id');		 
	}
	 
	/**
	 *	Function index
	 *
	 *	'index' controller function to display the campaign various options 
	 *	like header theme, import from url, import from zip file, paste in html code
	 *
	 *	@param (int) (id)  contains campaign id for which template options will be display
	 */
	function index($id=0,$ctyp=''){		
		$campaign_data=array();								
		// Fetch campaign data from database by campaign ID
		$campaign_array=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$id,'campaign_created_by'=>$this->session->userdata('member_id')));
		// Redirects user to listing page if user have not created this campaign or campaign does not exists
		if(count($campaign_array)){
			// Prepare array to send to view
			$campaign_data=array(
								'campaign_template_option'=>$campaign_array[0]['campaign_template_option'],
								'import_campaign_url'=>$campaign_array[0]['import_campaign_url'],
								'campaign_theme_id'=>$campaign_array[0]['campaign_theme_id'],
								'html'=>$campaign_array[0]['campaign_content'],
								'campaign_text_content'=>$campaign_array[0]['campaign_text_content'],
								'campaign_title'=>$campaign_array[0]['campaign_title'],
								'is_status'=>$campaign_array[0]['is_status']
							);
			// Add campaign ID to campaign array
			$campaign_data['campaign_id']=$id;
		}else{
			redirect('newsletter/campaign');
		}
		# To check form is submittted to import from URL
		if($this->input->post('campaign_import_url_submit')=='Import'){
			$this->import_url($id,$campaign_array);
		}
		# To check form is submittted to import from zip
		else if($this->input->post('campaign_import_zip_file_submit')=='Import'){
			$this->import_zip_file($id,$campaign_array);
		}
		# To check form is submittted to import from paste code
		else if($this->input->post('paste_html')=='submit'){
			$this->import_paste_html($id,$campaign_array);
		}
		# To check form is submittted to text email
		else if($this->input->post('text_email')=='Next' or $this->input->post('text_email')=='Submit'){
			$this->text_email($id,$campaign_array);
		}
		#Count number of theme
		$theme_count=$this->Campaign_Model->get_theme_count(array('rect.red_is_active'=>1,'rect.red_is_delete'=>0));
		#Get information of email theme
		$campaign_data['theme_data']=$this->Campaign_Model->get_theme_data(array('rect.red_is_active'=>1,'rect.red_is_delete'=>0),$theme_count);
		
		foreach($campaign_data['theme_data'] as $theme_info){
		
			$templates_count=$this->Campaign_Model->get_template_count(array('rect.is_active'=>1,'rect.is_delete'=>0,'rect.template_theme_id'=>$theme_info['red_theme_id'],'template_id >'=>0));
			$template_data=$this->Campaign_Model->get_template_data(array('rect.is_active'=>1,'rect.is_delete'=>0,'rect.template_theme_id'=>$theme_info['red_theme_id'],'template_id >'=>0),$templates_count);
			$campaign_data['template_data'][$theme_info['red_theme_id']]=$template_data;
		}
		
		$campaign_data['is_autoresponder']=false;
		$previous_page_url=base_url()."newsletter/campaign/".$id;
		 
		// Loads header, campaign and footer view.
		$this->load->view('header',array('title'=>'Campaign Template','previous_page_url'=>$previous_page_url));
		if($ctyp=='import_url'){
		$this->load->view('newsletter/campaign_import_url',array('campaign_data'=>$campaign_data,'ctyp'=>$ctyp));
		}elseif($ctyp=='html_code'){
		$this->load->view('newsletter/campaign_html_code',array('campaign_data'=>$campaign_data,'ctyp'=>$ctyp));
		}elseif($ctyp=='plain_text'){
		$this->load->view('newsletter/campaign_plain_text',array('campaign_data'=>$campaign_data,'ctyp'=>$ctyp));
		}elseif($ctyp=='import_zip'){
		$this->load->view('newsletter/campaign_import_zip',array('campaign_data'=>$campaign_data,'ctyp'=>$ctyp));
		}else{
		$this->load->view('newsletter/campaign_template_options',array('campaign_data'=>$campaign_data,'ctyp'=>$ctyp));		
		}
		$this->load->view('footer');
	}
	/**
	 *	Function autoresponder
	 *
	 *	'index' controller function to display the autoresponder various options 
	 *	like header theme, import from url, import from zip file, paste in html code
	 *
	 *	@param (int) (id)  contains campaign id for which template options will be display
	 */
	function autoresponder($id=0){
		#Initialize autoresponder data array to store data for campaign to be edited
		$campaign_data=array();							
		#Fetch campaign data from database by campaign ID
		$campaign_array=$this->Autoresponder_Model->get_autoresponder_data(array('campaign_id'=>$id,'campaign_created_by'=>$this->session->userdata('member_id')));
		#Redirects user to listing page if user have not created this campaign or campaign does not exists
		if(count($campaign_array)){
			# Prepare array to send to view
			$campaign_data=array(
								'campaign_template_option'=>$campaign_array[0]['campaign_template_option'],
								'import_campaign_url'=>$campaign_array[0]['import_campaign_url'],
								'campaign_theme_id'=>$campaign_array[0]['campaign_theme_id'],
								'html'=>$campaign_array[0]['campaign_content'],
								'campaign_text_content'=>$campaign_array[0]['campaign_text_content'],
								'campaign_title'=>$campaign_array[0]['campaign_title'],
								'is_status'=>$campaign_array[0]['is_status']
							);
			# Add campaign ID to campaign array
			$campaign_data['campaign_id']=$id;
		}else{
			redirect('newsletter/autoresponder/display');
		}
		# To check form is submittted to import from URL
		if($this->input->post('campaign_import_url_submit')=='Import'){
			$this->import_url($id,$campaign_array,true);
		}
		# To check form is submittted to import from zip
		else if($this->input->post('campaign_import_zip_file_submit')=='Import'){
			$this->import_zip_file($id,$campaign_array,true);
		}
		# To check form is submittted to import from paste code
		else if($this->input->post('paste_html')=='submit'){
			$this->import_paste_html($id,$campaign_array,true);
		}
		# To check form is submittted to text email
		else if($this->input->post('text_email')=='Submit'){
			$this->text_email($id,$campaign_array,true);
		}
		 	
		#Count number of theme
		$theme_count=$this->Campaign_Model->get_theme_count(array('rect.red_is_active'=>1,'rect.red_is_delete'=>0));
		#Get information of email theme
		$campaign_data['theme_data']=$this->Campaign_Model->get_theme_data(array('rect.red_is_active'=>1,'rect.red_is_delete'=>0),$theme_count);
		
		foreach($campaign_data['theme_data'] as $theme_info){
			$templates_count=$this->Campaign_Model->get_template_count(array('rect.is_active'=>1,'rect.is_delete'=>0,'rect.template_theme_id'=>$theme_info['red_theme_id'],'template_id >'=>0));
			$template_data=$this->Campaign_Model->get_template_data(array('rect.is_active'=>1,'rect.is_delete'=>0,'rect.template_theme_id'=>$theme_info['red_theme_id'],'template_id >'=>0),$templates_count);
			$campaign_data['template_data'][$theme_info['red_theme_id']]=$template_data;
		}
		$campaign_data['is_autoresponder']=true;
		$previous_page_url=base_url()."newsletter/autoresponder/display/".$id;
		
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));
		$user_info=true;
		$str_user_detail_for_footer="";
		if(!$user_data_array[0]['company']){
			$user_info=false;
		}
		if(!$user_data_array[0]['address_line_1']){
			$user_info=false;
		}
		if(!$user_data_array[0]['city']){
			$user_info=false;
		}
		if(!$user_data_array[0]['state']){
			$user_info=false;
		}
		if(!$user_data_array[0]['zipcode']){
			$user_info=false;
		}
		if(!$user_data_array[0]['country_name']){
			$user_info=false;
		}
		$campaign_data['user_info']=$user_info;
		$campaign_data['user_data']=$user_data_array[0];
		#Fetch Country name
		$country_info=$this->UserModel->get_country_data();
		$campaign_data['country_info']=$country_info;
		#Loads header, campaign and footer view.
		$this->load->view('header',array('title'=>'Campaign Template','previous_page_url'=>$previous_page_url));
		$this->load->view('newsletter/campaign_template_options',array('campaign_data'=>$campaign_data));
		$this->load->view('footer');
	}
	/**
	 	Function Campaign_preview to display the campaign preview 
		@param (int) (id)  contains campaign id for which campaign preview will be display
	 */
	function campaign_preview($id=0){
		#Initialize campaign data array to store data for campaign to be edited
		$campaign_data=array();		
				
		#Fetch campaign data from database by campaign ID
		$campaign_array=$this->Campaign_Model->get_campaign_data(array('campaign_id'=>$id,'campaign_created_by'=>$this->session->userdata('member_id')));
		#Redirects user to listing page if user have not created this campaign or campaign does not exists

		if(count($campaign_array)){
			# Prepare array to send to view
			$campaign_data=array(
								'campaign_template_option'=>$campaign_array[0]['campaign_template_option'],
								'campaign_theme_id'=>$campaign_array[0]['campaign_theme_id'],
								'html'=>$campaign_array[0]['campaign_content'],
								'campaign_text_content'=>$campaign_array[0]['campaign_text_content'],
								'campaign_title'=>$campaign_array[0]['campaign_title'],
							);
			$campaign_data['campaign_id']=$id;
		}else{
			redirect('newsletter/campaign');
		}
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));		
		$campaign_data['user_data']=$user_data_array[0];
		$country_info=$this->UserModel->get_country_data();
		$campaign_data['country_info']=$country_info;
		
		# To check user have submit the form
		if($this->input->post('action')=='submit'){
			$campaign_id=$id;
			# Validation rules are applied
			$this->form_validation->set_rules('campaign_title', 'Campaign Title', 'required');
			$this->form_validation->set_rules('campaign_text_content', 'Campaign Text Generated ', 'required');	
			# To check form is validated
			if($this->form_validation->run()==true){
				if($this->input->get_post('regenerate_text', TRUE)==1){
					$campaign_content=html_entity_decode($campaign_array[0]['campaign_content'], ENT_QUOTES, "utf-8" ); 
					$htmlWithCss=$this->automatice_css_inliner($campaign_content);
					
					//$text_html=html2text($htmlWithCss,false,false);
					$text_html=html2text($campaign_content,false,false);
					$input_array=array(	'campaign_title'=>$this->input->get_post('campaign_title', TRUE),'is_status'=>'0','campaign_text_content'=>$text_html);					
				}else if($campaign_array[0]['is_status']==1){
					$input_array=array(	'campaign_title'=>$this->input->get_post('campaign_title', TRUE),'is_status'=>'0','campaign_text_content'=>$this->input->get_post('campaign_text_content', TRUE));
				}else{
					$input_array=array(	'campaign_title'=>$this->input->get_post('campaign_title', TRUE),	'is_status'=>'0','campaign_text_content'=>$this->input->get_post('campaign_text_content', TRUE));
				}
				# Update campaign by data posted by user
				$this->Campaign_Model->update_campaign($input_array,array('campaign_id'=>$campaign_id));
				if($campaign_array[0]['is_status']==1){					
					// create array for insert values in activty table
					$values=array('user_id'=>$this->session->userdata('member_id'),'activity'=>'campaign_created',  'campaign_id'=>$campaign_id	);
					$this->Activity_Model->create_activity($values);
				}
				
				$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));
				
				 
				if(($campaign_array[0]['campaign_template_option']!=3)&&($campaign_array[0]['campaign_template_option']!=5)){
					$page_html=html_entity_decode( $campaign_array[0]['campaign_content'], ENT_QUOTES, "utf-8" ); 
				}else{
					$page_html=$campaign_array[0]['campaign_content'];
				}
				$this->Campaign_Autoresponder_Model->encode_url($campaign_id,$page_html);	
				if($this->input->get_post('action_save', TRUE)=="save"){
					redirect('newsletter/campaign_template_options/campaign_preview/'.$campaign_id);
				}else{
					redirect('newsletter/campaign_email_setting/index/'.$campaign_id);
				}
			}
		}		
		
		#Count number of theme
		$theme_count=$this->Campaign_Model->get_theme_count(array('rect.red_is_active'=>1));
		
		#Get information of email theme
		$campaign_data['theme_data']=$this->Campaign_Model->get_theme_data(array('rect.red_is_active'=>1),$templates_count);		
		foreach($campaign_data['theme_data'] as $theme_info){
			$templates_count=$this->Campaign_Model->get_template_count(array('rect.is_active'=>1,'rect.template_theme_id'=>$theme_info['red_theme_id']));
			$template_data=$this->Campaign_Model->get_template_data(array('rect.is_active'=>1,'rect.template_theme_id'=>$theme_info['red_theme_id']),$templates_count);
			$campaign_data['template_data'][$theme_info['red_theme_id']]=$template_data;
		}
		// Get Maximum Contacts according to session package id
		// Get Package id
		$user_packages_array=$this->UserModel->get_user_packages(array('member_id'=>$this->session->userdata('member_id'),'is_deleted'=>0));
		$package_array=$this->UserModel->get_packages_data(array('package_id'=>$user_packages_array[0]['package_id']));		
		
		$package_price=$package_array[0]['package_price'];
		$package_max_contacts=$package_array[0]['package_max_contacts'];
		// Get Total Subscribers created by user
		$fetch_condiotions_array=array(	'res.subscriber_created_by'=>$this->session->userdata('member_id'), 'res.is_deleted'=>0, 'res.subscriber_status'=>0);
		$subscriber_count=$this->Subscriber_Model->get_subscriber_count($fetch_condiotions_array);

		if($subscriber_count>$package_max_contacts){
			$campaign_data['upgrade_package']=1;
		}else{
			$campaign_data['upgrade_package']=0;
		}
		$campaign_data['is_autoresponder']=false;
		$previous_page_url=base_url()."newsletter/campaign_template_options/index/".$id;
		#Get shoreten url 
		$shorten_url=get_shorten_url();	
		#Loads header, campaign and footer view.		
		$this->load->view('header',array('title'=>'Campaign Template','previous_page_url'=>$previous_page_url));
		$this->load->view('newsletter/campaign_preview',array('campaign_data'=>$campaign_data,'shorten_url'=>$shorten_url));
		$this->load->view('footer');
	}
	function autoresponder_preview($id=0){
		if($this->session->userdata('member_id')=='')
			redirect('user/index');	
		#Initialize autoresponder data array to store data for autoresponder to be edited
		$autoresponder_data=array();				
		#Fetch campaign data from database by campaign ID
		$campaign_array=$this->Autoresponder_Model->get_autoresponder_data(array('campaign_id'=>$id,'campaign_created_by'=>$this->session->userdata('member_id')));
		#Redirects user to listing page if user have not created this campaign or campaign does not exists
		if(count($campaign_array)){
			# Prepare array to send to view
			$campaign_data=array(
								'campaign_template_option'=>$campaign_array[0]['campaign_template_option'],
								'campaign_theme_id'=>$campaign_array[0]['campaign_theme_id'],
								'html'=>$campaign_array[0]['campaign_content'],
								'campaign_text_content'=>$campaign_array[0]['campaign_text_content'],
								'campaign_title'=>$campaign_array[0]['campaign_title'],
							);			
			$campaign_data['campaign_id']=$id;
		}else{
			redirect('newsletter/autoresponder/display');
		}		
		# To check user have submit the form
		if($this->input->post('action')=='submit'){
			$campaign_id=$id;
			# Validation rules are applied
			$this->form_validation->set_rules('campaign_title', 'Campaign Title', 'required');
			$this->form_validation->set_rules('campaign_text_content', 'Campaign Text Generated ', 'required');				# To check form is validated
			if($this->form_validation->run()==true){
				if($this->input->get_post('regenerate_text', TRUE)==1){
					$campaign_content=html_entity_decode($campaign_array[0]['campaign_content'], ENT_QUOTES, "utf-8" ); 
					$htmlWithCss=$this->automatice_css_inliner($campaign_content);					
					
					$text_html=html2text($htmlWithCss,false,false);					
					$input_array=array(
						'campaign_title'=>$this->input->get_post('campaign_title', TRUE),
						'is_status'=>'0',
						'campaign_text_content'=>$text_html
					);					
				}else if(($campaign_array[0]['is_status']==1)||($campaign_array[0]['campaign_template_option']!=4)){
					$input_array=array(
						'campaign_title'=>$this->input->get_post('campaign_title', TRUE),
						'is_status'=>'0',
						'campaign_text_content'=>$this->input->get_post('campaign_text_content', TRUE)
					);
				}else{
					$input_array=array(
						'campaign_title'=>$this->input->get_post('campaign_title', TRUE),
						'is_status'=>'0'
					);
				}
				# Update campaign by data posted by user
				$this->Autoresponder_Model->update_autoresponder($input_array,array('campaign_id'=>$campaign_id));
				
				$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));
				
				 
				if(($campaign_array[0]['campaign_template_option']!=3)&&($campaign_array[0]['campaign_template_option']!=5)){
					$page_html=html_entity_decode($campaign_array[0]['campaign_content'], ENT_QUOTES, "utf-8" ); 
				}else{
					$page_html=$campaign_array[0]['campaign_content'];
				}
				$this->Campaign_Autoresponder_Model->encode_url($campaign_id,$page_html,true);
				if($this->input->get_post('action_save', TRUE)=="save"){
					redirect('newsletter/campaign_template_options/autoresponder_preview/'.$campaign_id);
				}else{
					redirect('newsletter/campaign_email_setting/autoresponder/'.$campaign_id);
				}				
			}
		}
		
		#Count number of theme
		$theme_count=$this->Campaign_Model->get_theme_count(array('rect.red_is_active'=>1));
		#Get information of email theme
		$campaign_data['theme_data']=$this->Campaign_Model->get_theme_data(array('rect.red_is_active'=>1),$theme_count);
		foreach($campaign_data['theme_data'] as $theme_info){
			$templates_count=$this->Campaign_Model->get_template_count(array('rect.is_active'=>1,'rect.template_theme_id'=>$theme_info['red_theme_id']));
			$template_data=$this->Campaign_Model->get_template_data(array('rect.is_active'=>1,'rect.template_theme_id'=>$theme_info['red_theme_id']),$templates_count);
			$campaign_data['template_data'][$theme_info['red_theme_id']]=$template_data;
		}
		
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));		
		$campaign_data['user_data']=$user_data_array[0];
		$country_info=$this->UserModel->get_country_data();
		$campaign_data['country_info']=$country_info;
		
		
		// Get Maximum Contacts according to session package id		
		$user_packages_array=$this->UserModel->get_user_packages(array('member_id'=>$this->session->userdata('member_id'),'is_deleted'=>0));
		$package_array=$this->UserModel->get_packages_data(array('package_id'=>$user_packages_array[0]['package_id']));		
		
		$package_price=$package_array[0]['package_price'];
		$package_max_contacts=$package_array[0]['package_max_contacts'];
		// Get Total Subscribers created by user
		$fetch_condiotions_array=array('res.subscriber_created_by'=>$this->session->userdata('member_id'), 'res.is_deleted'=>0, 'res.subscriber_status'=>1	);
		$subscriber_count=$this->Subscriber_Model->get_subscriber_count($fetch_condiotions_array);
			
		if($subscriber_count>$package_max_contacts){
			$campaign_data['upgrade_package']=1;
		}else{
			$campaign_data['upgrade_package']=0;
		}
		$campaign_data['is_autoresponder']=true;
		#Get shoreten url 
		$shorten_url=get_shorten_url();	
		#Loads header, campaign and footer view.		
		$this->load->view('header',array('title'=>'Campaign Template'));
		$this->load->view('newsletter/campaign_preview',array('campaign_data'=>$campaign_data,'shorten_url'=>$shorten_url));
		$this->load->view('footer');
	}
	/**
		Function import_url to import url for creating campaign
		@param int id: Campaign id
	*/
	function import_url($id=0,$campaign_array=array(),$is_autoresponder=false){
		# Validation rules are applied
		$this->form_validation->set_rules('campaign_import_url', 'Campaign Import URL', 'required|callback_validate_url|trim');
		# To check form is validated
		if($this->form_validation->run()==true){
			$url=$this->input->get_post('campaign_import_url', true);
			$campaign_id=$this->input->get_post('campaign_id', true);
			$htmlWithCss=$this->automatice_css_inliner();
			if($campaign_array[0]['is_status']==1){
				
				$text_html=html2text($htmlWithCss,false,false);								
				$input_array['campaign_text_content']=$text_html;				
			}
			if($is_autoresponder){
				
				if($id){
					# Retrieve data posted in form posted by user using input class
					$input_array['campaign_content']=htmlentities($htmlWithCss, ENT_QUOTES, "UTF-8");
					$input_array['import_campaign_url']=$url;
					$input_array['campaign_template_option']='1';
						
					# Update campaign by data posted by user
					$this->Autoresponder_Model->update_autoresponder($input_array,array('campaign_id'=>$id,'campaign_created_by'=>$this->session->userdata('member_id')));
						
					# Redirect to  campaign preview
					redirect('newsletter/campaign_template_options/autoresponder_preview/'.$campaign_id);
				}
			}else{
				
				if($id){
					# Retrieve data posted in form posted by user using input class
					$input_array['campaign_content']=htmlentities($htmlWithCss, ENT_QUOTES, "UTF-8");
					$input_array['import_campaign_url']=$url;
					$input_array['campaign_template_option']='1';					
					# Update campaign by data posted by user
					$this->Campaign_Model->update_campaign($input_array,array('campaign_id'=>$id,'campaign_created_by'=>$this->session->userdata('member_id')));
						
					# Redirect to  campaign preview
					redirect('newsletter/campaign_template_options/campaign_preview/'.$campaign_id);
				}
			}
		}
	}
	/**
		Function import_zip_file to import zip file for creating campaign
		@param int id: Campaign id
	*/
	function import_zip_file($id=0,$campaign_array=array(),$is_autoresponder=false){
		
		$this->form_validation->set_rules('campaign_import_zip_file', 'Campaign Import Zip File', 'callback_validate_upload');		
		if($this->form_validation->run()==true){
			$html=$this->extracted_zip_html;
			$htmlWithCss=$this->automatice_css_inliner($html);
			if($campaign_array[0]['is_status']==1){
				
				$text_html=html2text($htmlWithCss,false,false);				
				$input_array['campaign_text_content']= $this->is_authorized->webCompatibleString(@mb_convert_encoding($text_html, 'HTML-ENTITIES', "UTF-8"));				
			}
			if($is_autoresponder){
				
				if($id){
					$htmlWithCss=htmlentities($htmlWithCss, ENT_QUOTES | ENT_IGNORE, "UTF-8");					 
					$input_array['campaign_content']=$htmlWithCss;
					$input_array['campaign_template_option']='2';					
					$this->Autoresponder_Model->update_autoresponder($input_array,array('campaign_id'=>$id,'campaign_created_by'=>$this->session->userdata('member_id')));			
					redirect('newsletter/campaign_template_options/autoresponder_preview/'.$id);
				}
			}else{				
				if($id){
					$htmlWithCss=htmlentities($htmlWithCss, ENT_QUOTES | ENT_IGNORE, "UTF-8");
					$input_array['campaign_content']=$htmlWithCss;
					$input_array['campaign_template_option']='2';
					$this->Campaign_Model->update_campaign($input_array,array('campaign_id'=>$id,'campaign_created_by'=>$this->session->userdata('member_id')));
					redirect('newsletter/campaign_template_options/campaign_preview/'.$id);
				}
			}
		}
	}
	/**
		Function import_paste_html to import paste HTML for creating campaign
		@param int id: Campaign id
	*/
	function import_paste_html($id=0,$campaign_array=array(),$is_autoresponder=false){
		$campaign_id=$id;
		# Validation rules are applied
		$this->form_validation->set_rules('paste_code', 'Campaign Paste Code', 'required');
		# To check form is validated
		if($this->form_validation->run()==true){		
			# remove css and javascript
			//$html=$this->removeCssScript();
			//$htmlWithCss=htmlentities ($html, ENT_QUOTES, 'utf-8', false)	;				
			
			$html=$this->input->post('paste_code');			 
			$htmlWithCss=$this->automatice_css_inliner();
			
			$input_array=array(	'campaign_content'=>$htmlWithCss, 'campaign_template_option'=>'4');
			if($campaign_array[0]['is_status']==1){				
				$text_html=html2text($html,false,false);				
				$input_array['campaign_text_content']=$text_html;								
			}
			if($id){
				if($is_autoresponder){					
													 						
					$this->Autoresponder_Model->update_autoresponder($input_array,array('campaign_id'=>$id,'campaign_created_by'=>$this->session->userdata('member_id')));			
					redirect('newsletter/campaign_template_options/autoresponder_preview/'.$campaign_id);
					exit;
				}else{
					
					$this->Campaign_Model->update_campaign($input_array,array('campaign_id'=>$id,'campaign_created_by'=>$this->session->userdata('member_id')));					
					redirect('newsletter/campaign_template_options/campaign_preview/'.$campaign_id);
					exit;
				}
			}
		}
	}
	/**
		Function text_email to import text email for creating campaign
		@param int id: Campaign id
	*/
	function text_email($id=0,$campaign_array=array(),$is_autoresponder=false){
		$campaign_id=$id;
		# Validation rules are applied
		$this->form_validation->set_rules('campaign_text_email', 'Campaign Text Email', 'required');
		# To check form is validated
		if($this->form_validation->run()==true){
			$html=$this->input->post('campaign_text_email');
			if($campaign_array[0]['is_status']==1){				 
				$campaign_data=array('campaign_content'=>$text_html);	
				$input_array['campaign_text_content']=$html;				
			}
			if($is_autoresponder){
				if($id){					
					# Retrieve data posted in form posted by user using input class
					$input_array['campaign_content']=$html;
					$input_array['campaign_template_option']='5';				
					# Update campaign by data posted by user
					$this->Autoresponder_Model->update_autoresponder($input_array,array('campaign_id'=>$id,'campaign_created_by'=>$this->session->userdata('member_id')));						
					# Redirect to  campaign preview
					redirect('newsletter/campaign_template_options/autoresponder_preview/'.$campaign_id);
				}
			}else{
				if($id){
					# Retrieve data posted in form posted by user using input class
					$input_array['campaign_content']=$html;
					$input_array['campaign_template_option']='5';
					# Update campaign by data posted by user
					$this->Campaign_Model->update_campaign($input_array,array('campaign_id'=>$campaign_id,'campaign_created_by'=>$this->session->userdata('member_id')));
					# Redirect to  campaign preview
					redirect('newsletter/campaign_template_options/campaign_preview/'.$campaign_id);
				}
			}
		}
	}
	/**
		function validate_url to validate url
		@return boolean :return true or false (url validate successfully or not)
	*/
	function validate_url(){
		$url=$this->input->get_post('campaign_import_url', true);	#get import url
		if(preg_match('@(http://)([^/]+)@i',$url, $matches)){
			return true;
		}else{
			$this->form_validation->set_message('validate_url', 'The %s is invalid');
			return false;
		}
	}
	/**
		Function for validating upload, extracting from zip archive and fetch html content.
	*/	

	function validate_upload(){		
		if(!file_exists($this->upload_path)){
			mkdir($this->upload_path,0777);
			chmod($this->upload_path,0777);
		}
		#Check if folder with name of 'imported_zip_files' exists on server
		if(!file_exists($this->upload_path.'/imported_zip_files')){
			mkdir($this->upload_path.'/imported_zip_files/',0777);
			chmod($this->upload_path.'/imported_zip_files/',0777);
		}
		#Check if folder with name of 'extracted_zip_files' exists on server
		if(!file_exists($this->upload_path.'/extracted_zip_files')){
			mkdir($this->upload_path.'/extracted_zip_files/',0777);	
			chmod($this->upload_path.'/extracted_zip_files/',0777);
		}
		# Initialization upload configuration

		$upload_config	=array();
		$upload_config['upload_path'] = $this->upload_path.'/imported_zip_files/';
		$upload_config['allowed_types'] = 'zip|rar';
		$upload_config['max_size']	= 1024*5; #5MB		
		$this->upload->initialize($upload_config);
		# New file name of zippped file
		$new_file_name=$this->session->userdata('member_id').'_'.date('YmdHis');
		#check if file is uploaded successfully
		if(!$this->upload->do_upload('campaign_import_zip_file')){
			$this->upload->display_errors();
			#displays error message if uploading fails	
			$this->form_validation->set_message('validate_upload', $this->upload->display_errors());
			return false;
		}else{
			#Get data of uploaded file
			$uploaded_file_array=$this->upload->data();
			#Rename uploaded file with new name
			rename($uploaded_file_array['full_path'],$uploaded_file_array['file_path'].$new_file_name.$uploaded_file_array['file_ext']);
			#Initialize php archive class and extract archive
			$zip = new ZipArchive;
			if ($zip->open($uploaded_file_array['file_path'].$new_file_name.$uploaded_file_array['file_ext']) === TRUE) {
				$zip->extractTo($this->upload_path.'/extracted_zip_files/'.$new_file_name.'/');
				$zip->close();
			}
			
			
			# file names allowed for extracting campaign html
			$file_names_allowed=array('index.html','index.html');
			$file_ext_allowed=array('html','jpg','png','gif','css');
			$files=array();
			$directories=array();
			/**
			* Get Files array in extracted folder
			* Delete Unwanted/dangerous files
			*/	
			$extracted_files_array = $this->dirtoarray($this->upload_path.'/extracted_zip_files/'.$new_file_name.'/');
			#print_r($extracted_files_array);
			#exit;
			// Cycle through all source files to copy them in Destination
			foreach ($extracted_files_array as $each_file) {
				if(!in_array(end(@explode('.',$each_file)),$file_ext_allowed)){
					@unlink($each_file);
				}else{
					$f_content = @file_get_contents($each_file);
					if(stripos($f_content, "<?php") !== false) {
						@unlink($each_file);
						send_mail(SYSTEM_NOTICE_EMAIL_TO, SYSTEM_EMAIL_FROM  ,'system' , SYSTEM_DOMAIN_NAME.': Hacking Attempt',"Campaign zip file(".$each_file.") having PHP into it tried to upload","Campaign zip file(".$each_file.") having PHP into it tried to upload");
				$data1=array('error' => 'Your file could not be imported');
					}
				}				
			}
			
			#Open renamed directory for reading and put files in files arrray and directories in directories array

			$dir_handle=opendir($this->upload_path.'/extracted_zip_files/'.$new_file_name.'/');

			while (false !== ($file = readdir($dir_handle))) {
				 if($file!='.' && $file!='..'){
					if(is_dir($this->upload_path.'/extracted_zip_files/'.$new_file_name.'/'.$file))
						$directories[]=$file;
					else
						$files[]=$file;
				 }
			}
			#Declare extracted_file variable
			$extracted_file='';
			#If extracted directory have files in it, then iterate in directory to search any of allowed files in it.
			if(count($files)){
				foreach($files as $file){
					if(in_array($file,$file_names_allowed) ){
						$key=array_search($file,$file_names_allowed);
						$extracted_file= $this->upload_path.'/extracted_zip_files/'.$new_file_name.'/'.$file_names_allowed[$key];
						$path_to_images=base_url().'webappassets/user_files/'.$this->session->userdata('member_id').'/extracted_zip_files/'.$new_file_name;
						break;
					}
				}
			}
			

			#If extracted directory have directories in it, then iterate in first directory to search any of allowed files in it.
			if($extracted_file=='' && count($directories)){
				$directory=$directories[0];
				$dir_handle=opendir($this->upload_path.'/extracted_zip_files/'.$new_file_name.'/'.$directory);
				while (false !== ($file = readdir($dir_handle))) {
					if($file!='.' || $file!='..'){
						if(in_array($file,$file_names_allowed)){
							$key=array_search($file,$file_names_allowed);
							$extracted_file=$this->upload_path.'/extracted_zip_files/'.$new_file_name.'/'.$directory.'/'. $file_names_allowed[$key];
							$path_to_images=base_url().'webappassets/user_files/'.$this->session->userdata('member_id').'/extracted_zip_files/'.$new_file_name.'/'.$directory;
							break;
						}
					}
				}
			}		

			# To check if extracted file exits in archive
			if($extracted_file!=''){
				# fetch html of file
				$html=file_get_contents($extracted_file);
				#replace path to images and css
				$filtered_html=preg_replace('#(href|src)=(["\'])([.\/]*)([^:"\']*)(["\'])#',

					'$1="'.$path_to_images.'/$4"',$html);
				#Assign filtered html to class variable and return true
				$this->extracted_zip_html=$filtered_html;
				return true;
			}else{
				#If file is not found , then display error message and return false.
				$this->form_validation->set_message('validate_upload', 'Zip Archive does not contain index.html or is corrupt');
				return false;
			}
		}
	}
	/**
	 *	Function dirtoarray for converting css to inline css
	 *
	 *	@param (string) (html_content)  contains html content
	 *	@return (string) processedHTML: return filtter html content
	 */	
	function dirtoarray($dir, $recursive=true) {
    $array_items = array();
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                if (is_dir($dir. "/" . $file)) {
                    if($recursive) {
                        $array_items = array_merge($array_items, $this->dirtoarray($dir. "/" . $file, $recursive));
                    }
                } else {
                    $file = $dir . "/" . $file;
                    $array_items[] = preg_replace("/\/\//si", "/", $file);
                }
            }
        }
        closedir($handle);
    }
    return $array_items;
	}
	/**
	 *	Function Automatice_css_inliner for converting css to inline css
	 *
	 *	@param (string) (html_content)  contains html content
	 *	@return (string) processedHTML: return filtter html content
	 */
	function automatice_css_inliner($html_content=""){
		# Load library for converting css to inline css
		$this->load->library('CSSToInlineStyles');
		$dom = new DOMDocument();
		if(isset($_POST['paste_code'])){			
			$html=$this->input->post('paste_code');
			//$html=preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $html);
		}else if(isset($_POST['campaign_import_url'])){
			$url=$this->input->get_post('campaign_import_url', true);
			$html=$this->get_html_from_url($url);
		}else{
			$html=$html_content;
		}
		
		# May need to disable error checking if the HTML isn't fully valid
		$dom->recover = true;
		$dom->strictErrorChecking = false;	
		libxml_use_internal_errors(true);
		//$html = preg_replace('/&#?+(\w)+;/', ' ', $html); 
		//$html = str_replace('&nbsp;',' ',$html);
		$html = str_replace('&copy;','',$html);
		//$html = utf8_encode(html_entity_decode($html));		

		$html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");
		 
		

		@$dom->loadHTML('<?xml encoding="UTF-8">'.$html); 		
		libxml_clear_errors();
		# get stylesheet of extrnal files		
		$link_tags = $dom->getElementsByTagName('style');
		$css="";
		for($i = 0; $i < $link_tags->length; $i++){
			$css.=$link_tags->item($i)->nodeValue;
			$link_tags->item($i)->nodeValue="";
		}
		
		# get stylesheet of link tag
		$link_tags = $dom->getElementsByTagName('link');
		for($i = 0; $i < $link_tags->length; $i++){
			$url= $link_tags->item($i)->getAttribute('href'); 
			$result = $this->validateURL($url);
			if($result){
				$css.=file_get_contents($url);
			}
		}
		# convert css to inline style
		$this->csstoinlinestyles->setHTML($html);
		$this->csstoinlinestyles->setCSS($css);
		$this->csstoinlinestyles->setCleanup(false);
		# grab the processed HTML
		$processedHTML =  $this->csstoinlinestyles->convert();
		# remove link tag css 
		$processedHTML= preg_replace ('/<link[^>]+\>/i', "", $processedHTML);
		# remove javascript from page
		$processedHTML= preg_replace ('/(\\s*)(<script\\b[^>]*?>)([\\s\\S]*?)<\\/script>(\\s*)/i'
	, "", $processedHTML);
		# remove javascript from page
		$processedHTML= preg_replace ('/<script[^>]+\>/i', "", $processedHTML); 
		$processedHTML=preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', "", $processedHTML);
		$processedHTML=preg_replace('|\<style.*\>(.*\n*)\</style\>|isU', "", $processedHTML);
		# return filtter html content
		return $processedHTML; 
	}
	
	/**
		Function get_html_from_url to fetch HTML source from URL
	*/
	function get_html_from_url($url=""){
		#get html source of URL 
		//$html= file_get_contents($url);
		$html= $this->curl_file_get_contents($url);
		#fetch domain name from URL like http://www.domain.com from http://www.domain.com/page.html
		/* $url_arr=pathinfo($url);
		if(!isset($url_arr['extension'])){
			$url_arr['dirname']=$url_arr['dirname']."/".$url_arr['filename'];
		}
		#Filter HTML by replacing relative path of images, css, javascript to absolute path by appending domain name to them
		$filtered_html=preg_replace('#(href|src)=(["\'])([.\/]*)([^:"\']*)(["\'])#',		'$1="'.$url_arr['dirname'].'/$4"',$html);
		 */
		$url_arr=parse_url($url);
		$strUrl = $url_arr['scheme'].'://'.$url_arr['host'].'/'.$url_arr['path'];
		#Filter HTML by replacing relative path of images, css, javascript to absolute path by appending domain name to them
		$filtered_html=preg_replace('#(href|src)=(["\'])([.\/]*)([^:"\']*)(["\'])#',		'$1="'.$strUrl.'/$4"',$html);
		 
	 
		#return filtered HTML
		return $filtered_html;
	}
	
		
	 
	function curl_file_get_contents($url){
		 $curl = curl_init();
		 $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
		 
		 curl_setopt($curl,CURLOPT_URL,$url); //The URL to fetch. This can also be set when initializing a session with curl_init().
		 curl_setopt($curl,CURLOPT_RETURNTRANSFER,TRUE); //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
		 curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,5); //The number of seconds to wait while trying to connect.	
		 
		 curl_setopt($curl, CURLOPT_USERAGENT, $userAgent); //The contents of the "User-Agent: " header to be used in a HTTP request.
		 curl_setopt($curl, CURLOPT_FAILONERROR, TRUE); //To fail silently if the HTTP code returned is greater than or equal to 400.
		 curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE); //To follow any "Location: " header that the server sends as part of the HTTP header.
		 curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE); //To automatically set the Referer: field in requests where it follows a Location: redirect.
		 curl_setopt($curl, CURLOPT_TIMEOUT, 10); //The maximum number of seconds to allow cURL functions to execute.	
		 
		 $contents = curl_exec($curl);
		 curl_close($curl);
		 return $contents;
	}
	function removeCssScript(){
		$processedHTML=$this->input->post('paste_code');
		$processedHTML= preg_replace ('/<link[^>]+\>/i', "", $processedHTML); // remove link tag css 
		$processedHTML= preg_replace ('/(\\s*)(<script\\b[^>]*?>)([\\s\\S]*?)<\\/script>(\\s*)/i'
	, "", $processedHTML); // remove javascript from page
		$processedHTML= preg_replace ('/<script[^>]+\>/i', "", $processedHTML); // remove javascript from page
		$processedHTML=preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', "", $processedHTML);
		$processedHTML=preg_replace('|\<style.*\>(.*\n*)\</style\>|isU', "", $processedHTML);
		return $processedHTML; // return filtter html content
	}
	function validateURL($url){
		$pattern = '/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&amp;?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?$/';
		return preg_match($pattern, $url);
	}
}
?>
