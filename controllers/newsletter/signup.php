<?php
/*
	Controller class for signup form
	It have controller functions for signup form management.
*/
class Signup extends CI_Controller{
	/*
		Contructor for controller.
		It checks user session and redirects user if not logged in
	*/
	function __construct(){
        parent::__construct();

		// Load the signup form model which interact with database
		$this->load->model('ConfigurationModel');
		$this->load->model('newsletter/Signup_Model');
		$this->load->model('UserModel');
		$this->load->model('newsletter/subscription_Model');
		$this->load->model('newsletter/Subscriber_Model');
		$this->load->library('MY_Form_validation');
		// These users will have single-optin without captcha
		$this->arrMemberHavingSingleOptinWithoutCaptcha = array(12099, 9714, 8932, 6584, 6521, 7179, 7446, 5999, 8175, 8597, 8504);
		
		// force_ssl();	
    }

	function index(){
		$this->display();
	}
	/**
	*	'Display' function for listing of signup forms
	*/
	function display(){
		if($this->session->userdata('member_id')=='')
			redirect('user/index');

		force_ssl();	
		//Where condition
		$fetch_conditions_array=array('is_deleted'=>0,'member_id'=>$this->session->userdata('member_id'));
		// Define config parameters for paging like base url, total rows and record per page.
		$config['base_url']=base_url().'newsletter/signup';
		$config['total_rows']=$this->Signup_Model->get_signup_count($fetch_conditions_array);
		$config['per_page']=100;
		$config['uri_segment']=4;

		// Initialize paging with above parameters
		$this->pagination->initialize($config);

		//Create paging inks
		$paging_links=$this->pagination->create_links();

		//Fetch data from database
		$signup_data_array['forms']=$this->Signup_Model->get_signup_data($fetch_conditions_array,$config['per_page'],$start);
		// Fetch user data from database
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));
		$signup_data_array['extra']=$user_data_array[0];

		// Recieve any messages to be shown, when listing is added or updated
		$messages=$this->messages->get();

		//Loads header,  listing  and footer view.
		$this->load->view('header',array('title'=>'Signup Forms'));
		$this->load->view('signup/signup_list',array('signup_froms'=>$signup_data_array,'paging_links'=>$paging_links,'messages' =>$messages));
		$this->load->view('footer');
	}

	/**
	*	'Signup_Create' function for creating new signup form
	*/
	function signup_create(){
		if($this->session->userdata('member_id')=='')
			redirect('user/index');
/*
		$thisMid = $this->session->userdata('member_id');
		$allMyContactsID = 0 - $this->session->userdata('member_id');
		$this->db->query("INSERT INTO `red_signup_form` (`form_name`, `form_title`, `form_button_text`, `form_background_color`, `header_background_color`, `header_text_color`, `member_id`, `subscription_id`, `custom_field`, `is_email`, `field_sequence`, `is_verified`) VALUES ('Unnamed', 'Join our mailing list', 'Subscribe', '#F0F1F3', '#FFFFFF', '#454545', '$thisMid', '$allMyContactsID', '', 1, 'a:1:{s:5:\"email\";s:0:\"\";}', 0)");

		$thisSignupFormId = $this->db->insert_id();
*/
		
		$this->session->set_userdata('signup_id', 0);
		redirect("/newsletter/signup/signup_edit/") ;
		exit;
	}

	function signup_edit($signup_id=0,$show_code=false,$type=""){
		if($this->session->userdata('member_id')=='')
			redirect('user/index');
		
		force_ssl();	
		
		if($signup_id > 0){
			$this->session->set_userdata('signup_id', $signup_id);
		}else{	
			$signup_id = $this->session->userdata('signup_id');
		}
		// Validation rules are applied
		$this->form_validation->set_rules('selectedSubscriptionValues', '"Select a list" of contacts', 'required|xss_clean');
		$this->form_validation->set_rules('form_name', 'Form Name', 'required|max_length[25]|xss_clean');
		//$this->form_validation->set_rules('form_title', 'Form Title', 'required|xss_clean');
		$this->form_validation->set_rules('form_button_text', 'Form Button Text', 'required|max_length[25]|xss_clean');

		if($this->input->post('action')=='save' && $this->form_validation->run()){
			$signup_data = array();
			$signup_data['form_name']=htmlentities($this->input->post('form_name',true));
			$signup_data['form_title']=htmlentities($this->input->post('form_title',true));
			$signup_data['form_button_text']=htmlentities ($this->input->post('form_button_text',true));
			$signup_data['form_background_color']=$this->input->post('form_background_color',true);
			$signup_data['header_text_color']=$this->input->post('header_text_color',true);
			$signup_data['header_background_color']=$this->input->post('header_background_color',true);
			$signup_data['header_background_image']=$this->input->post('header_background_image',true);			
			$signup_data['background_background_image']=$this->input->post('background_background_image',true);
			$signup_data['background_background_tile_image']=$this->input->post('background_background_tile_image',true);
			
			$user_packages_array=$this->UserModel->get_user_packages(array('member_id'=>$this->session->userdata('member_id'),'is_deleted'=>0));
			//$signup_data['is_verified']	=  ($user_packages_array[0]['package_id'] > 0 )? 1: 0; //paid user
			 $signup_data['is_verified']	=  1; //paid user
			$signup_data['member_id']= $this->session->userdata('member_id');
			$signup_data['subscription_id']=$this->input->post('selectedSubscriptionValues',true);
			if(trim($this->input->post('form_language')) !='')
			$signup_data['form_language']= $this->input->post('form_language');  
		
			 
			$signup_data['fld_sequence']= serialize($this->input->post('fld_sequence'));
 
			//To insert listing data in database
			if($signup_id > 0){
				$inserted_signup_id=$this->Signup_Model->update_signup($signup_data,array('id'=>$signup_id));				
			}else{
				$signup_data['is_stats']=1;
				$signup_id = $this->Signup_Model->create_signup($signup_data);
				$this->session->set_userdata('signup_id', $signup_id);
			}
			// echo $this->db->last_query();
 
			// admin alert ends
			$email_msg ="<p>Hello admin,</p>";
			$email_msg.="<p>Verify Signup form :<b>".$signup_data['form_name']."</b> created by <b>".$this->session->userdata('member_username')."</b></p>";
			$email_msg.="<p>Select a choice to allow or disallow it from admin panel.</p>";
			$email_msg.='<p>Thanks,</p>';
			$email_msg.='<p>Redcappi Team</p>';
			$this->load->helper('admin_notification');
			$to=$this->get_Admin_notification_email();
			$subject="Verify Signup form by ".$this->session->userdata('member_username');
			admin_notification_send_email($to, SYSTEM_EMAIL_FROM,"RedCappi", $subject,$email_msg,$email_msg);
			// admin alert ends
		}
		if(validation_errors()){
			echo 'error|'.validation_errors();
		}else{
			// Fetch user data from database
			$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));
			
			if($signup_id > 0){
				$signup_data_array['form']=$this->Signup_Model->get_signup_data(array('is_deleted'=>0,'id'=>$signup_id));
			}
			
			$signup_data_array['form'][0]['form_name'] = (isset($signup_data_array['form'][0]['form_name']))?$signup_data_array['form'][0]['form_name']:'Unnamed';
			$signup_data_array['form'][0]['form_title'] = (isset($signup_data_array['form'][0]['form_title']))?$signup_data_array['form'][0]['form_title']:'Join our mailing list';
			$signup_data_array['form'][0]['form_button_text'] = (isset($signup_data_array['form'][0]['form_button_text']))?$signup_data_array['form'][0]['form_button_text']:'Subscribe';
			$signup_data_array['form'][0]['form_background_color'] = (isset($signup_data_array['form'][0]['form_background_color']))?$signup_data_array['form'][0]['form_background_color']:'#F0F1F3';
			$signup_data_array['form'][0]['header_background_color'] = (isset($signup_data_array['form'][0]['header_background_color']))?$signup_data_array['form'][0]['header_background_color']:'#FFFFFF';
			$signup_data_array['form'][0]['header_text_color'] = (isset($signup_data_array['form'][0]['header_text_color']))?$signup_data_array['form'][0]['header_text_color']:'#454545';
			
			
			$signup_data_array['form'][0]['form_language'] = (isset($signup_data_array['form'][0]['form_language']))?$signup_data_array['form'][0]['form_language']:'en';
			$this->lang->load($signup_data_array['form'][0]['form_language'], 'signup');
			
			
			$signup_data_array['form'][0]['confirmation_emai_message']=(isset($signup_data_array['form'][0]['confirmation_emai_message']))?$signup_data_array['form'][0]['confirmation_emai_message']:"To activate your subscription, please follow the link below.
If you can't click it, please copy the entire link and paste it into your browser.

Thank You!
";
			$signup_data_array['form'][0]['confirmation_thanks_you_message_url']=(isset($signup_data_array['form'][0]['confirmation_thanks_you_message_url']) && trim($signup_data_array['form'][0]['confirmation_thanks_you_message_url']) !='')?$signup_data_array['form'][0]['confirmation_thanks_you_message_url']:"http://";
			$signup_data_array['form'][0]['singup_thank_you_message_url']=(isset($signup_data_array['form'][0]['singup_thank_you_message_url']) && trim($signup_data_array['form'][0]['singup_thank_you_message_url']) != '')?$signup_data_array['form'][0]['singup_thank_you_message_url']:"http://";
			
			$signup_data_array['form'][0]['from_email']=(isset($signup_data_array['form'][0]['from_email']) && $signup_data_array['form'][0]['from_email']!='')?$signup_data_array['form'][0]['from_email']:$user_data_array[0]['email_address'];
			
			
			
			
			$signup_data_array['form'][0]['from_name'] = (isset($signup_data_array['form'][0]['from_name']) && $signup_data_array['form'][0]['from_name'] != '')?$signup_data_array['form'][0]['from_name']:trim($user_data_array[0]['company']);
			
			$signup_data_array['form'][0]['from_name'] = ($signup_data_array['form'][0]['from_name'] != '')?$signup_data_array['form'][0]['from_name']:trim($user_data_array[0]['member_username']);
			
			$signup_data_array['form'][0]['subject']=(isset($signup_data_array['form'][0]['subject']) && $signup_data_array['form'][0]['subject'] !='')?$signup_data_array['form'][0]['subject']:"Please Confirm Your Subscription";			
				
			$signup_data_array['subscription_id_arr']= (isset($signup_data_array['form'][0]['subscription_id']) && $signup_data_array['form'][0]['subscription_id'] !='')?explode(",",$signup_data_array['form'][0]['subscription_id']):array();
			/**
			*	Get List of Contact-lists
			*/
			
			// Define config parameters for paging like base url, total rows and record per page.
			$config['total_rows']=$this->subscription_Model->get_subscription_count(array( 'subscription_created_by'=>$this->session->userdata('member_id'), 'is_deleted'=>0));
			// Fetches subscription data from database
			$signup_data_array['subscriptions']=$this->subscription_Model->get_subscription_data(array( 'subscription_created_by'=>$this->session->userdata('member_id'), 'is_deleted'=>0),$config['total_rows']);
						
			if(isset($signup_data_array['form'][0]['fld_sequence']) && !is_null($signup_data_array['form'][0]['fld_sequence'])){
				$arrSignupFormFields = unserialize($signup_data_array['form'][0]['fld_sequence']);
				$frmContent = $this->getSignupForm($arrSignupFormFields);
			}

			$rsLanguage = $this->db->query("select * from red_language order by language");
			$arrLanguage = array();
			foreach($rsLanguage->result_array() as $langRow){
				$langCode = $langRow['language_code'];
				$arrLanguage[$langCode] = $langRow['language'] ;
			}
			$rsLanguage->free_result();
			$signup_data_array['email_id']	= $this->getFromEmlArray();
			$signup_data_array['show_code'] = $show_code;
			if($type!="ajax"){
				if($signup_data_array['form'][0]['custom_field']!=""){
					$expl_arr=explode(";",$signup_data_array['form'][0]['custom_field']);
					$signup_data_array['custom_fld']=$expl_arr;
				}
				$previous_page_url=base_url()."newsletter/signup/index";
				#Get shoreten url
				$shorten_url=get_shorten_url();
				$this->load->view('header',array('title'=>'Update Signup Forms','previous_page_url'=>$previous_page_url));
				$this->load->view('signup/signup_edit',array('signup_from'=>$signup_data_array, 'signup_froms_language'=>$arrLanguage, 'messages' =>$messages,'shorten_url'=>$shorten_url));
				$this->load->view('footer');
			}else{
				echo '1|'.$signup_id;
			}
		}
	}

	function getFromEmlArray(){
		$arrFromEmls = array($this->session->userdata('member_email_address'));
		$rsOtherEmailAddresses = $this->db->query("select `email_address` from `red_member_from_email` where `member_id` = '".$this->session->userdata('member_id')."' and `is_verified`=1");
		if($rsOtherEmailAddresses->num_rows() > 0){
			foreach($rsOtherEmailAddresses->result_array() as $otherEml){
				$arrFromEmls[]	= $otherEml['email_address'];
			}
		}
		$rsOtherEmailAddresses->free_result();
		return $arrFromEmls;
	}
	/**
	*	Function to generate Signup-Form from the provided array of fields as parameter
	*/
	function getSignupForm($arrFields){
		$arrSignupFormFieldLabels = array('email'=>'Email', 'name'=>'Name', 'first_name'=>'First Name', 'last_name'=>'Last Name', 'company'=>'Company', 'address'=>'Address', 'city'=>'City', 'state'=>'State', 'zip_code'=>'Zip Code', 'country'=>'Country');
		$retVal = '';
		$arrFldName 	= $arrFields['fld_name'];
		$arrFldType 	= $arrFields['fld_type'];
		$arrFldRequired = $arrFields['fld_required'];
		$arrFldOptions 	= $arrFields['fld_options'];
		// $retVal = "<tr><td><label>".$this->lang->line('email')." <span>*</span></label><br/><input type='text' id='signup_eml' name='email'>$validation_error</td></tr>\n";
		$retVal = '';
		if(is_array($arrFldName) && count($arrFldName) > 0){
		 foreach($arrFldName as $fld => $fldVal){
			$isRequired ='';	
			if(array_key_exists($fldVal,$arrSignupFormFieldLabels)){
				$retVal.= "<tr><td><label><span class='form-label'>".$this->lang->line($fldVal)."</span>";
				$retVal.= ($arrFldRequired[$fld] =="Y")?"<span>*</span>":'';
				$retVal.= "</label><br/><input type='text' id='signup_{$fldVal}' name='$fldVal' /></td></tr>\n";
			}else{
				$fldVal = urlencode($fldVal);
				$retVal.= "<tr><td><label><span class='form-label'>".str_replace('_',' ',rawurldecode($fldVal))."</span>";
				if($arrFldRequired[$fld] =="Y"){
					$retVal.= "<span>*</span>";
					$isRequired= 'required';
				}	
				$retVal.= "</label><br/>";
				if($arrFldType[$fld] =="text"){
					$retVal.= "<input type='text' id='signup_{$fldVal}' name='$fldVal' $isRequired /></td></tr>\n";
				}elseif($arrFldType[$fld] =="textarea"){
					$retVal.= "<textarea id='signup_{$fldVal}' name='$fldVal' $isRequired></textarea></td></tr>\n";
				}elseif($arrFldType[$fld] =="dropdown"){
					$retVal.= "<select id='signup_{$fldVal}' name='$fldVal' $isRequired><option value=''>--</option>";
					$arrThisFldOptions =(trim($arrFldOptions[$fld]) != '')? array_filter(explode(',',$arrFldOptions[$fld])):'';
					if(is_array($arrThisFldOptions) && count($arrThisFldOptions) > 0){
						foreach($arrThisFldOptions as $thisOpt)
						$retVal.= "<option value='$thisOpt'>$thisOpt</option>";
					}
					$retVal.= "</select>";
					$retVal.= "</td></tr>\n";
				}elseif($arrFldType[$fld] =="checkbox"){					
					$arrThisFldOptions =(trim($arrFldOptions[$fld]) != '')? array_filter(explode(',',$arrFldOptions[$fld])):'';
					for($i=0;$i < count($arrThisFldOptions);$i++){
						$retVal.= "<input type='checkbox' name='{$fldVal}[]' id='signup_{$fldVal}{$i}' value='".$arrThisFldOptions[$i]."' $isRequired /> ";
						$retVal.= "<label for='signup_{$fldVal}{$i}'>".$arrThisFldOptions[$i]."</label> ";
						$retVal.= "<br/>";
					}
				}elseif($arrFldType[$fld] =="radio"){
					$arrThisFldOptions =(trim($arrFldOptions[$fld]) != '')? array_filter(explode(',',$arrFldOptions[$fld])):'';
					for($i=0;$i < count($arrThisFldOptions);$i++){
						$retVal.= "<input type='radio' name='{$fldVal}[]' id='signup_{$fldVal}{$i}' value='".$arrThisFldOptions[$i]."' $isRequired /> ";
						$retVal.= "<label for='signup_{$fldVal}{$i}'>".$arrThisFldOptions[$i]."</label> ";
						$retVal.= "<br/>";
					}
				}elseif($arrFldType[$fld] =="date_dropdown"){
						$retVal.= "<select class='input-option-date' id='signup_{$fldVal}' name='{$fldVal}[m]'  $isRequired><option value=''>Month</option>";
						for($i=1;$i < 13;$i++){
							$retVal.= "<option value='$i'>$i</option>";
						}
						$retVal.= "</select>";
						$retVal.= "<select class='input-option-date' id='signup_{$fldVal}' name='{$fldVal}[d]'  $isRequired><option  value=''>Day</option>";
						for($i=1;$i < 32;$i++){
							$retVal.= "<option value='$i'>$i</option>";
						}
						$retVal.= "</select>";

						$retVal.= "<select class='input-option-date' id='signup_{$fldVal}' name='{$fldVal}[Y]' $isRequired><option  value=''>Year</option>";
						for($i=1900;$i < date('Y')+20;$i++){
							$retVal.= "<option value='$i'>$i</option>";
						}
						$retVal.= "</select>";
				}
			  $retVal.= "</td></tr>\n";
			}
		}
		}
		return $retVal;
	}
	/**
	*	Function to generate Signup-Form Validation JS from the provided array of fields as parameter
	*/
	function getSignupFormJs($arrFields){
		$arrSignupFormFieldLabels = array('email'=>'Email', 'name'=>'Name', 'first_name'=>'First Name', 'last_name'=>'Last Name', 'company'=>'Company', 'address'=>'Address', 'city'=>'City', 'state'=>'State', 'zip_code'=>'Zip Code', 'country'=>'Country');
		$jsRules = 'rules: {'."\n";
		$jsMsg = 'messages: {'."\n";

		$arrFldName 	= $arrFields['fld_name'];
		$arrFldType 	= $arrFields['fld_type'];
		$arrFldRequired = $arrFields['fld_required'];
		$arrFldOptions 	= $arrFields['fld_options'];
		if(is_array($arrFldName) && count($arrFldName) > 0){
			$i = 0;
			foreach($arrFldName as $fld => $fldVal){
				if($i > 0) $comma = ", \n";
				if($fldVal =='email'){
					$jsRules .= $comma."'email': { required: true, email: true}";
					$jsMsg .= $comma."'email'".': { required: "Required", email: "Require a valid email address."}';
				$i++;	
				}elseif($arrFldRequired[$fld] =="Y"){
					$jsRules .= $comma. "'".rawurlencode ($fldVal)."': 'required'";
					$jsMsg .= $comma. "'".rawurlencode ($fldVal)."': { required: \"Required\"}";
				$i++;	
				}
			}
		}
		$jsRules .= "\n },";
		$jsMsg .= "\n },";
		
		$appendJs = "	highlight: function(element) {
			$(element).css('border', '2px solid red');
		},
		success: function(element) {
			$(element).css('border', '1px solid #ebebeb');
		}, 

		submitHandler: function( form ) {   
			var ajaxurl = $('#signupform').attr('action');
			var x=$('#signupform').serialize();
	
			$.ajax({
				url : ajaxurl,
				dataType: 'jsonp',
				data : x,
				type: 'GET',				
				success : function(data){  
					var msg = data.msg;
					var redirect_url = data.url;
					
					
					if(msg == 'err'){
						$('#validation-error').html(data.validation);
					}else if(msg == ''){
						$('#validation-error').html(data);
					}else{
						window.location.href= redirect_url;
					}	
				}
				})
			return false;
			}
		});";
		
		return "$('#signupform').validate({ \n". $jsRules ."\n". $jsMsg. $appendJs;
	}
	
	
	function getSignupFormInlineJs($arrFields) {
        $arrSignupFormFieldLabels = array('email' => 'Email', 'name' => 'Name', 'first_name' => 'First Name', 'last_name' => 'Last Name', 'company' => 'Company', 'address' => 'Address', 'city' => 'City', 'state' => 'State', 'zip_code' => 'Zip Code', 'country' => 'Country');
        $jsRules = 'rules: {' . "\n";
        $jsMsg = 'messages: {' . "\n";

        $arrFldName = $arrFields['fld_name'];
        $arrFldType = $arrFields['fld_type'];
        $arrFldRequired = $arrFields['fld_required'];
        $arrFldOptions = $arrFields['fld_options'];
        if (is_array($arrFldName) && count($arrFldName) > 0) {
            $i = 0;
            foreach ($arrFldName as $fld => $fldVal) {
                if ($i > 0)
                    $comma = ", \n";
                if ($fldVal == 'email') {
                    $jsRules .= $comma . "'email': { required: true, email: true}";
                    $jsMsg .= $comma . "'email'" . ': { required: "Required", email: "Require a valid email address."}';
                    $i++;
                } elseif ($arrFldRequired[$fld] == "Y") {
                    $jsRules .= $comma . "'" . rawurlencode($fldVal) . "': 'required'";
                    $jsMsg .= $comma . "'" . rawurlencode($fldVal) . "': { required: \"Required\"}";
                    $i++;
                }
            }
        }
        $jsRules .= "\n },";
        $jsMsg .= "\n },";

        $appendJs = "	highlight: function(element) {
			$(element).css('border', '2px solid red');
		},
		success: function(element) {
			$(element).css('border', '1px solid #ebebeb');
		}, 

		submitHandler: function( form ) {   
			var ajax_url = ($( '#signupform' ).attr( 'action' ));
			var x=$('#signupform').serialize();
	
			$.ajax({
				url : ajax_url,
				data : x,
				type: 'POST',				
				success : function(data){  
					var msg = data.split('|');					
					if(msg[0] == 'ok'){
						window.location.href= msg[1];					
					}else if(msg[0] == 'err'){
						$('#validation-error').html(msg[1]);
					}else{
						//$('#signupform').hide('slow');
						$('#validation-error').html(data);
					}
				}
				})
			return false;
			}
		});";

        return "$('#signupform').validate({ \n" . $jsRules . "\n" . $jsMsg . $appendJs;
    }
	
	
	function subscribe($form_id=0,$type=""){
		// Fetch data from database
		$signup_data_array['form']=$this->Signup_Model->get_signup_data(array('is_deleted'=>0,'id'=>$form_id));
		//echo $this->db->last_query();
		if(count($signup_data_array['form'])<1){
			echo $validation_error= '<div style="color:#FF0000;display: inline-block;font-size: 15px;font-weight: bold;  line-height: 20px;" >This email list no longer exists.</div>';
		exit;
		}	

		$this->lang->load($signup_data_array['form'][0]['form_language'], 'signup');
		$this->member_id=$signup_data_array['form'][0]['member_id'];
		
		if($type == "valid"){
			
			if(isset($_GET['Email'])){
			$_GET['email'] = $_GET['Email'];
			unset($_GET['Email']);
			}
			
			$_GET['email'] = urldecode($_GET['email']);
			
			//$_POST['email'] = $this->input->get('email');
			$email = urldecode($_GET['email']);
			$checkmail = $this->checkemailexistinlist($form_id,$email);
			//echo $checkmail;exit;
			/* if($signup_data_array['form'][0]['single_opt_in'] == '1'){
				$this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
			}else{
				$this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_email_check['.$form_id.']|trim');
			} */
			
			
		// if ($this->form_validation->run()) {
			 
			/*   $jsonArray['msg'] = 'success';
					$jsonArray['url'] = 'https://www.redcappi.us/newsletter/signup/signup_confirmation/'.$subscriber_id.'/'.$form_id.'';
					echo $_GET['callback']."(".json_encode($jsonArray).");"; exit; */
			
			if($checkmail == 'no') {
				
				if($signup_data_array['form'][0]['single_opt_in'] == '1'  and !in_array($this->member_id, $this->arrMemberHavingSingleOptinWithoutCaptcha)){ 
				$captcha = (isset($_GET['g-recaptcha-response']))?$_GET['g-recaptcha-response']: false;

					if(!$captcha){ 
					  redirect(base_url().'newsletter/signup/signupform_url/'.$form_id);
					  echo '<p style="color:red">Please check the captcha form.</p>';
					  exit;
					}
					$response=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LfJ8wgTAAAAAL3feWmqDYLOoY2zSAsk4gkt9que&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
					if($response['success'] === false){
					  redirect(base_url().'newsletter/signup/signupform_url/'.$form_id);
					  echo '<P style="color:red">You are spammer ! Get the @$%K out</P>';
					  exit;
					} 									
				}
				$signup_data = array();
				$serialize_data = array();
				$signup_data['subscriber_first_name']="";
				$signup_data['subscriber_last_name']="";
				foreach($_GET as $k=>$v){
					
					if($k!='listing_submit'){
						$k = trim($k);					
						if(strtolower($k)=='email'){
							$signup_data['subscriber_email_address']=mysql_real_escape_string(trim($email));
							$arrEmailExploded = explode( '@',$signup_data['subscriber_email_address'] );
							$signup_data['subscriber_email_domain'] = $arrEmailExploded[1];
						}elseif($k=='name'){
							$name=mysql_real_escape_string(trim($this->input->get('name',true)));
							$name_arr=explode(" ",$name,2);
							$signup_data['subscriber_first_name']=trim($name_arr[0]);
							if($name_arr[1]!="")
							$signup_data['subscriber_last_name']=trim($name_arr[1]);

						}elseif($k=='first_name'){
							if($this->input->get('first_name',true)!="")
								$signup_data['subscriber_first_name']=mysql_real_escape_string(trim($this->input->get('first_name',true)));
						}elseif($k=='last_name'){
							if($this->input->get('last_name',true)!="")
							$signup_data['subscriber_last_name']=mysql_real_escape_string(trim($this->input->get('last_name',true)));
						}elseif($k=='company'){
							$signup_data['subscriber_company']=mysql_real_escape_string(trim($this->input->get('company',true)));
						}elseif($k=='address'){
							$signup_data['subscriber_address']=mysql_real_escape_string(trim($this->input->get('address',true)));
						}elseif($k=='city'){
							$signup_data['subscriber_city']=mysql_real_escape_string(trim($this->input->get('city',true)));
						}elseif($k=='state'){
							$signup_data['subscriber_state']=mysql_real_escape_string(trim($this->input->get('state',true)));
						}elseif($k=='zip_code'){
							$signup_data['subscriber_zip_code']=mysql_real_escape_string(trim($this->input->get('zip_code',true)));
						}elseif($k=='country'){
							$signup_data['subscriber_country']=mysql_real_escape_string(trim($this->input->get('country',true)));
						}elseif($this->isPhone($k)){
							$signup_data['subscriber_phone']=trim($v);
						}else{
							
							$findme   = 'jsonp';
							$pos = strpos($v, $findme);
							
							if ($pos === false) {
								if($v !== FALSE && is_array($v)){						
									if(array_keys($v) == array('m','d','Y')){
										$v = date("F d, Y", mktime(null, null, null, $v['m'],$v['d'],$v['Y']));
									}else{
										$v = array_map('trim', $v);
										$v	= implode(',', $v) ;
									}
								}	
								$serialize_data[$k]=$v;
							}else{	
								
							}
						}
					}					
				}
			
				$signup_data['subscriber_extra_fields']=serialize($serialize_data);
				$signup_data['is_signup']=1;
				$signup_data['signup_form_id']=$form_id;
				$signup_data['subscriber_status']=1;
				$signup_data['is_deleted']=1;
				$signup_data['subscriber_created_by']=$signup_data_array['form'][0]['member_id'];
				$signup_data['subscriber_ip']= $this->is_authorized->getRealIpAddr();
				// IF ADMIN has disallowed signup form, then disable it
				$config_arr=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'continue_singup_form'));
				if( trim($config_arr[0]['config_value']) !="1"){
					redirect(base_url().'newsletter/signup/verify_subscription/qweq');
					die('ok|'.CAMPAIGN_DOMAIN.'newsletter/signup/verify_subscription/qweq');
					exit;
				}
				// IF ADMIN has disallowed signup form, then disable it
				// Check eligibility
				if($this->isEligibleToSubscribe($signup_data['subscriber_created_by'], $signup_data_array['form'][0]['is_verified'])){
					#Insert subscriber in "All My List"
					$qry = "INSERT INTO red_email_subscribers SET ";
					$flds = '';
					foreach ($signup_data as $key=>$val) $flds .= $key . ' = \'' . mysql_real_escape_string(trim($val)) . '\', ';
					$flds .=  'subscription_id = -'.$this->member_id ;
					$qry .=  $flds .' ON DUPLICATE KEY UPDATE ' . $flds . ', subscriber_id=LAST_INSERT_ID(subscriber_id)';
					$this->db->query($qry);
					$subscriber_id = $this->db->insert_id();
					$is_stats = $this->db->query("select is_stats from red_signup_form where `id`='$form_id'")->row()->is_stats;
					if($is_stats > 0){
					// increment form-submssion-counter
					$this->db->query("update red_signup_form set form_submission_counter = (form_submission_counter + 1) where id='$form_id'");
					$IPasLong = ip2long($this->is_authorized->getRealIpAddr());
					$this->db->query("insert into red_signup_form_stats set `form_id` = $form_id ,  `ip_address`='$IPasLong' ,  `activity`=2, subscriber_id='$subscriber_id', activity_date=curdate() ON DUPLICATE  KEY UPDATE  activity_date=now() ");	
					}
					// add to specific lists
					$subscription_id_arr= array_filter(explode(",",$signup_data_array['form'][0]['subscription_id']));
					#Insert subscriber in "Other List"
					foreach($subscription_id_arr as $subscription_id){
						if($subscription_id>0){
							$input_array=array('subscriber_id'=>$subscriber_id,'subscription_id'=>$subscription_id);
							$this->Subscriber_Model->replace_subscription_subscriber($input_array);
						}
					}
					//if($form_id == 946)	echo base_url().'newsletter/signup/signup_confirmation/'.$subscriber_id.'/'.$form_id;
					//redirect(base_url().'newsletter/signup/signup_confirmation/'.$subscriber_id.'/'.$form_id);
					//redirect('http://www.red7.me/newsletter/signup/signup_confirmation/'.$subscriber_id.'/'.$form_id);
					
					$jsonArray['msg'] = 'success';
					$jsonArray['url'] = base_url().'newsletter/signup/signup_confirmation/'.$subscriber_id.'/'.$form_id.'';
					echo $_GET['callback']."(".json_encode($jsonArray).");"; 
					/* echo "<script language='javascript'>window.location.href='https://www.redcappi.us/newsletter/signup/signup_confirmation/".$subscriber_id."/".$form_id."';</script>";
					die('ok|newsletter/signup/signup_confirmation/'.$subscriber_id.'/'.$form_id); */
					exit;
				}else{
					$jsonArray['msg'] = 'exist';
					$jsonArray['url'] = base_url().'newsletter/signup/signup_confirmation/';
					echo $_GET['callback']."(".json_encode($jsonArray).");"; 
					/* redirect(base_url().'newsletter/signup/signup_confirmation/');
					die('ok|newsletter/signup/signup_confirmation/');  */
					exit;
				}
				
			 }else{
				
			//	if (validation_errors()) {
					$jsonArray['msg'] = 'err';
					$jsonArray['validation'] = '<div style="color:#FF0000;display: inline-block;font-size: 15px;font-weight: bold;  line-height: 20px;" >Your already signed up for this mailing list. Please enter a different email address.</div>';
					echo $_GET['callback']."(".json_encode($jsonArray).");"; 
					exit;
					$border_style = 'style="border:1px solid red !important;"';
				/* } else {
					$jsonArray['msg'] = '';
					echo $_GET['callback']."(".json_encode($jsonArray).");"; 
					exit;
				} */
			
			} 
			
		}
		
		
		if($type =="ajax"){
			if(isset($_POST['Email'])){
			$_POST['email'] = $_POST['Email'];
			unset($_POST['Email']);
			}
			// Validation rules are applied
			if($signup_data_array['form'][0]['single_opt_in'] == '1'){
				$this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
			}else{
				$this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_email_check['.$form_id.']|trim');
			}
			if($this->form_validation->run()){
				if($signup_data_array['form'][0]['single_opt_in'] == '1'  and !in_array($this->member_id, $this->arrMemberHavingSingleOptinWithoutCaptcha)){ 
					$captcha = (isset($_POST['g-recaptcha-response']))?$_POST['g-recaptcha-response']: false;

					if(!$captcha){
					  redirect(base_url().'newsletter/signup/signupform_url/'.$form_id);
					  echo '<p style="color:red">Please check the captcha form.</p>';
					  exit;
					}
					$response=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LfJ8wgTAAAAAL3feWmqDYLOoY2zSAsk4gkt9que&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
					if($response['success'] === false){
					  redirect(base_url().'newsletter/signup/signupform_url/'.$form_id);
					  echo '<P style="color:red">You are spammer ! Get the @$%K out</P>';
					  exit;
					} 									
				}
				$signup_data = array();
				$serialize_data = array();
				$signup_data['subscriber_first_name']="";
				$signup_data['subscriber_last_name']="";
				foreach($_POST as $k=>$v){
					
					if($k!='listing_submit'){
						$k = trim($k);					
						if(strtolower($k)=='email'){
							$signup_data['subscriber_email_address']=mysql_real_escape_string(trim($this->input->post('email',true)));
							$arrEmailExploded = explode( '@',$signup_data['subscriber_email_address'] );
							$signup_data['subscriber_email_domain'] = $arrEmailExploded[1];
						}elseif($k=='name'){
							$name=mysql_real_escape_string(trim($this->input->post('name',true)));
							$name_arr=explode(" ",$name,2);
							$signup_data['subscriber_first_name']=trim($name_arr[0]);
							if($name_arr[1]!="")
							$signup_data['subscriber_last_name']=trim($name_arr[1]);

						}elseif($k=='first_name'){
							if($this->input->post('first_name',true)!="")
								$signup_data['subscriber_first_name']=mysql_real_escape_string(trim($this->input->post('first_name',true)));
						}elseif($k=='last_name'){
							if($this->input->post('last_name',true)!="")
							$signup_data['subscriber_last_name']=mysql_real_escape_string(trim($this->input->post('last_name',true)));
						}elseif($k=='company'){
							$signup_data['subscriber_company']=mysql_real_escape_string(trim($this->input->post('company',true)));
						}elseif($k=='address'){
							$signup_data['subscriber_address']=mysql_real_escape_string(trim($this->input->post('address',true)));
						}elseif($k=='city'){
							$signup_data['subscriber_city']=mysql_real_escape_string(trim($this->input->post('city',true)));
						}elseif($k=='state'){
							$signup_data['subscriber_state']=mysql_real_escape_string(trim($this->input->post('state',true)));
						}elseif($k=='zip_code'){
							$signup_data['subscriber_zip_code']=mysql_real_escape_string(trim($this->input->post('zip_code',true)));
						}elseif($k=='country'){
							$signup_data['subscriber_country']=mysql_real_escape_string(trim($this->input->post('country',true)));
						}elseif($this->isPhone($k)){
							$signup_data['subscriber_phone']=trim($v);
						}else{
							if($v !== FALSE && is_array($v)){						
								if(array_keys($v) == array('m','d','Y')){
									$v = date("F d, Y", mktime(null, null, null, $v['m'],$v['d'],$v['Y']));
								}else{
									$v = array_map('trim', $v);
									$v	= implode(',', $v) ;
								}
							}	
							$serialize_data[$k]=$v;
						}
					}					
				}

				$signup_data['subscriber_extra_fields']=serialize($serialize_data);
				$signup_data['is_signup']=1;
				$signup_data['signup_form_id']=$form_id;
				$signup_data['subscriber_status']=1;
				$signup_data['is_deleted']=1;
				$signup_data['subscriber_created_by']=$signup_data_array['form'][0]['member_id'];
				$signup_data['subscriber_ip']= $this->is_authorized->getRealIpAddr();
				// IF ADMIN has disallowed signup form, then disable it
				$config_arr=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'continue_singup_form'));
				if( trim($config_arr[0]['config_value']) !="1"){
					redirect(base_url().'newsletter/signup/verify_subscription/qweq');
					die('ok|'.CAMPAIGN_DOMAIN.'newsletter/signup/verify_subscription/qweq');
					exit;
				}
				// IF ADMIN has disallowed signup form, then disable it
				// Check eligibility
				if($this->isEligibleToSubscribe($signup_data['subscriber_created_by'], $signup_data_array['form'][0]['is_verified'])){
					#Insert subscriber in "All My List"
					$qry = "INSERT INTO red_email_subscribers SET ";
					$flds = '';
					foreach ($signup_data as $key=>$val) $flds .= $key . ' = \'' . mysql_real_escape_string(trim($val)) . '\', ';
					$flds .=  'subscription_id = -'.$this->member_id ;
					$qry .=  $flds .' ON DUPLICATE KEY UPDATE ' . $flds . ', subscriber_id=LAST_INSERT_ID(subscriber_id)';
					$this->db->query($qry);
					$subscriber_id = $this->db->insert_id();
					$is_stats = $this->db->query("select is_stats from red_signup_form where `id`='$form_id'")->row()->is_stats;
					if($is_stats > 0){
					// increment form-submssion-counter
					$this->db->query("update red_signup_form set form_submission_counter = (form_submission_counter + 1) where id='$form_id'");
					$IPasLong = ip2long($this->is_authorized->getRealIpAddr());
					$this->db->query("insert into red_signup_form_stats set `form_id` = $form_id ,  `ip_address`='$IPasLong' ,  `activity`=2, subscriber_id='$subscriber_id', activity_date=curdate() ON DUPLICATE  KEY UPDATE  activity_date=now() ");	
					}
					// add to specific lists
					$subscription_id_arr= array_filter(explode(",",$signup_data_array['form'][0]['subscription_id']));
					#Insert subscriber in "Other List"
					foreach($subscription_id_arr as $subscription_id){
						if($subscription_id>0){
							$input_array=array('subscriber_id'=>$subscriber_id,'subscription_id'=>$subscription_id);
							$this->Subscriber_Model->replace_subscription_subscriber($input_array);
						}
					}
					//if($form_id == 946)	echo base_url().'newsletter/signup/signup_confirmation/'.$subscriber_id.'/'.$form_id;
					//redirect(base_url().'newsletter/signup/signup_confirmation/'.$subscriber_id.'/'.$form_id);
					//redirect('http://www.red7.me/newsletter/signup/signup_confirmation/'.$subscriber_id.'/'.$form_id);
					echo "<script language='javascript'>window.location.href='http://www.red7.me/newsletter/signup/signup_confirmation/".$subscriber_id."/".$form_id."';</script>";
					//die('ok|newsletter/signup/signup_confirmation/'.$subscriber_id.'/'.$form_id);
					exit;
				}else{
					redirect(base_url().'newsletter/signup/signup_confirmation/');
					die('ok|newsletter/signup/signup_confirmation/');
					exit;
				}
			}
			if(validation_errors()){
				redirect(base_url().'newsletter/signup/signupform_url/'.$form_id);
				$validation_error= 'err|<div style="color:#FF0000;display: inline-block;font-size: 15px;font-weight: bold;  line-height: 20px;" >'.validation_errors().'</div>';
				die($validation_error);
				exit;
				$border_style='style="border:1px solid red !important;"';
			}
			
		}
		if($type=="view_code"){
			if(!is_null($signup_data_array['form'][0]['fld_sequence'])){
				$arrSignupFormFields = unserialize($signup_data_array['form'][0]['fld_sequence']);
				$frmJs = $this->getSignupFormInlineJs($arrSignupFormFields);
				$frmContent = $this->getSignupForm($arrSignupFormFields);
			} 
			$bgBackgroundImgStyle = (trim($signup_data_array['form'][0]['background_background_image']) !='')?"url({$signup_data_array['form'][0]['background_background_image']})" : 'none';
			$bgBackgroundImgRepeat = (trim($signup_data_array['form'][0]['background_background_tile_image']) ===0)?"background-repeat:no-repeat":"background-repeat:repeat";
			$copy_code = " \n <script type=\"text/javascript\"> \n $(document).ready(function() { \n";
			$copy_code.= $frmJs ;
			$copy_code.= " \n }); \n </script>";
			
			$copy_code .="<form action='".CAMPAIGN_DOMAIN."newsletter/signup/subscribe/{$form_id}'  method='post' class='signupform' id='signupform'  accept-charset='UTF-8'>
					<table width='100%' border='0' cellspacing='0' cellpadding='0' class='formTable'>
					<tr><td class='form_title' style='font-weight:bold;font-size:27px;text-align:center;'><div class='header-txt' style='padding:20px 0 15px;background-color:{$signup_data_array['form'][0]['header_background_color']};color:{$signup_data_array['form'][0]['header_text_color']};'>{$signup_data_array['form'][0]['form_title']}</div>";
			if($signup_data_array['form'][0]['header_background_image'] !='')
            $copy_code.= "<img src='". $signup_data_array['form'][0]['header_background_image']."' id='header-img' style='width:100%; height:auto;margin-top:-71px;' />";			
			$copy_code.= "</td></tr>";
			
			$copy_code.= "<tr><td><div id='validation-error'></div></td></tr>";
			
			$copy_code.= $frmContent;
			
			
			$copy_code.= ' <tr><td><input type="submit" name="listing_submit" value="'.$signup_data_array['form'][0]['form_button_text'].'" id="btnSubmit" class="submit_button"  content="Submit"></td></tr>
			   </table></form>';
			$subscriptions =$this->subscription_Model->get_subscription_data(array( 'subscription_created_by'=>$signup_data_array['form'][0]['member_id'], 'is_deleted'=>0),$config['total_rows']);
			// echo $this->db->last_query();
			$signup_form_subscription_array = (isset($signup_data_array['form'][0]['subscription_id']) && $signup_data_array['form'][0]['subscription_id'] !='')?explode(",",$signup_data_array['form'][0]['subscription_id']):array();
			$lists = '';
			// print_r($signup_form_subscription_array);
			// print_r($subscriptions);
			foreach($subscriptions as $each_subscription){
				if(in_array($each_subscription['subscription_id'],$signup_form_subscription_array)){ 
				$lists .= $each_subscription['subscription_title'] . ' ,';
				}
			}
			$lists = rtrim($lists,','); 
			$form_overview = '<table class="report stats">
              <tbody>
                <tr>
                  <td>
                    <strong>Created at:</strong> <span>'.date('F j, Y \a\t g:i a',strtotime(getGMTToLocalTime($signup_data_array['form'][0]['date_added'], $this->session->userdata('member_time_zone')))).'</span>
                  </td>
                </tr>
                <tr>
                  <td>
                    <strong>List:</strong> <span>'.$lists.'</span>
                  </td>
                </tr>
				<tr><td>';
		if($signup_data_array['form'][0]['is_stats'] == 0){
			$form_overview .= '<p class="report-conversion-required info">
								You must "Enable Form Monitoring" for older Signup Forms to start tracking.
							</p>

							<a href="javascript:void(0);" onclick="javascript:enable_stats('.$signup_data_array['form'][0]['id'].');" class="btn add report-convert"><i class="icon-dashboard"></i>Enable Form Monitoring</a>';
		}else{

			$form_overview .= $this->signup_form_stats($signup_data_array['form'][0]['id']);
		}	
			$form_overview .= '</td></tr>
				</tbody>
                  </table>';

				$data=array('view_code'=>$copy_code,'view_overview'=>$form_overview,'background_color'=>$signup_data_array['form'][0]['form_background_color'],'background_image'=>$bgBackgroundImgStyle,'background_repeat'=>$bgBackgroundImgRepeat);
				echo json_encode($data);
		}elseif($type=="code"){			 
			if(!is_null($signup_data_array['form'][0]['fld_sequence'])){
				$arrSignupFormFields = unserialize($signup_data_array['form'][0]['fld_sequence']);
				$frmJs = $this->getSignupFormJs($arrSignupFormFields);
				$frmContent = $this->getSignupForm($arrSignupFormFields);
			}
			$copy_code='<!-- Begin Redcappi Signup-form -->';
			if($signup_data_array['form'][0]['single_opt_in'] == '1'  and !in_array($this->member_id, $this->arrMemberHavingSingleOptinWithoutCaptcha)){
				$copy_code.= "\n".'<iframe src="'.CAMPAIGN_DOMAIN.'s/'.$form_id.'" style="border:0;" name="myiFrame"  frameborder="0" marginheight="0px" marginwidth="0px" height="460px" width="468px"></iframe>';
				$copy_code.="\n".'<!-- End Redcappi Signup Form -->';
			}else{			
			$copy_code.= '<!DOCTYPE html><html><head><meta content="text/html; charset=UTF-8" http-equiv="content-type">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">';
			if($signup_data_array['form'][0]['single_opt_in'] == '1'   and !in_array($this->member_id, $this->arrMemberHavingSingleOptinWithoutCaptcha) ){ 
			$copy_code.= '<script type="text/javascript" src="https://www.google.com/recaptcha/api.js"></script>';
			}
			$copy_code.= link_tag('webappassets/css/signup_form_enc.css?v=6-20-13');
			$copy_code.= '</head><body>';
			//$copy_code.= " \n <script type=\"text/javascript\" src=\"".base_url()."webappassets/js/jquery-1.4.4.min.js?v=6-20-13\" ></script> \n";
			//$copy_code.= " \n <script type=\"text/javascript\" src=\"".base_url()."webappassets/js/jquery.validate.js?v=6-20-13\" ></script> \n";
			//$copy_code.= " \n <script type=\"text/javascript\"> \n $(document).ready(function() { \n";
			//$copy_code.= $frmJs ;
			//$copy_code.= " \n }); \n </script>";
			$copy_code .= "\n <script type=\"text/javascript\"> \n";
			$copy_code .= "\n var jq = document.createElement(\"script\"); \n";
			$copy_code .= "\n jq.addEventListener(\"load\", proceed);  \n";
			$copy_code .= "\n jq.src = '".site_url()."webappassets/js/jquery-1.4.4.min.js?v=6-20-13'; \n"; 
			//$copy_code .= "\n jq.src = 'https://www.redcappi.us/webappassets/js/jquery.validate.js?v=6-20-13'; \n";  
			$copy_code .= "\n document.querySelector(\"head\").appendChild(jq); \n";
			
			
			$copy_code.= "\n function proceed () { \n $(document).ready(function() { \n";
			$copy_code.= "\n $.getScript('".site_url()."webappassets/js/jquery.validate.js?v=6-20-13', function() { \n";
			$copy_code.= $frmJs ;
			$copy_code.= "\n });";
			$copy_code.= " \n }); \n } </script>";
			
			
			$copy_code.= "<form action='https://red7.me/newsletter/signup/subscribe/{$form_id}/valid'  method='post' class='signupform' id='signupform'  accept-charset='UTF-8'>
					<table width='100%' border='0' cellspacing='0' cellpadding='0' class='formTable'>
					<tr><td class='form_title' style='font-weight:bold;font-size:27px;text-align:center;'><div class='header-txt' style='padding:20px 0 15px;background-color:{$signup_data_array['form'][0]['header_background_color']};color:{$signup_data_array['form'][0]['header_text_color']};'>{$signup_data_array['form'][0]['form_title']}</div>";
			if(trim($signup_data_array['form'][0]['header_background_image']) !=''){
			$copy_code.= "<img src='".$signup_data_array['form'][0]['header_background_image']."' id='header-img' style='width:100%; height:auto;margin-top:-71px;' />";				
			}
			$copy_code.= "</td></tr>";

			$copy_code.= "<tr><td><div id='validation-error'></div></td></tr>";
			
			$copy_code.= $frmContent;
			if($signup_data_array['form'][0]['single_opt_in'] == '1'  and !in_array($this->member_id, $this->arrMemberHavingSingleOptinWithoutCaptcha)){ 
			$copy_code.= '<tr><td><div class="g-recaptcha" style="margin: 0 auto;display: table;" data-sitekey="6LfJ8wgTAAAAAGp850U6FvVn5TnBuQ2JaPb_kiJm"></div></td></tr>';		
			}
            $copy_code.= ' <tr><td><input type="submit" name="listing_submit" value="'.$signup_data_array['form'][0]['form_button_text'].'" id="btnSubmit" class="submit_button"  content="Submit"></td></tr>
			   </table></form>';
			$copy_code.= '<div class="footlink">Powered by <a href="http://www.'.SYSTEM_DOMAIN_NAME.'" target="_blank">RedCappi</a><img src="'. CAMPAIGN_DOMAIN.'newsletter/signup/showpblogo/'.$signup_data_array['form'][0]['id'].'" alt="" title="" border="0"></div>';	
		
			$copy_code.='</body>';
			$copy_code.='</html>';
			$copy_code.='<!-- End Redcappi Signup Form -->';
				
		}
		echo htmlspecialchars ($copy_code);
		}
	}


	function signup_form_stats($form_id=0){
		$is_stats = $this->db->query("select count(*)as rec from red_signup_form_stats where form_id = '$form_id'")->row()->rec;
		if($is_stats > 0){		
		$visit_count = $this->db->query("select count(*) as rec from red_signup_form_stats where form_id = '$form_id' and `activity`=1")->row()->rec;
		// $submission_count = $this->db->query("select (count(*) - 1)as rec from red_signup_form_stats where form_id = '$form_id' and `activity`=2")->row()->rec;		
		$submission_count = $this->db->query("select count(*) as rec from red_signup_form_stats where form_id = '$form_id' and `activity`=2")->row()->rec;		
		$confirmation_count = $this->db->query("select count(*)as rec from red_signup_form_stats where form_id = '$form_id' and `activity`=3")->row()->rec;
		$conversion = 0;
		//$visit_count = $visit_count + $submission_count;
		$visit_count = $visit_count ;
		if($visit_count > 0  and $confirmation_count > 0)
		$conversion = round(($confirmation_count/$visit_count)*100,1);
		$str_stats ='<ul class="over_view_contact">
                      <li>
                        <span>Visits</span>
                        <h5>'.$visit_count.'</h5>
                        <!-- a href="'.site_url("newsletter/signup/stats_detail/view/$form_id/").'" class="report-view-list view">view</a -->
                      </li>
                      <li>
                        <span>Signups</span>
                        <h5>'.$confirmation_count.'</h5>';
		if($confirmation_count > 0){				
						$str_stats .='<a href="'.site_url("newsletter/signup/stats_detail/$form_id").'" class="report-view-list view">view</a>';
		}				
		$str_stats .='</li>
                      <li>
                        <span>Conversion Rate</span>
                        <h5>'.$conversion.'%</h5>
                      </li>
                    </ul>';
		
		}else{
		$str_stats = '<p class="report-conversion-required info">
                      We have not yet recieved any tracking information. Please ensure you are using the latest code by clicking <i class="iconx-share-ex"></i> icon under the signup form.
                    </p>';
		}			
		return $str_stats;				
	}
	function enable_stats($form_id=0){
		$this->db->query("update red_signup_form set is_stats=1 where `id`='$form_id'");
	}
	function signupform_url($form_id=0){
		
		$signup_data_array=$this->Signup_Model->get_signup_data(array('is_deleted'=>0,'id'=>$form_id));
		if(count($signup_data_array)<1){
			redirect(base_url());
			exit;
		}
		if($signup_data_array[0]['form_language'] != ''){
			$this->lang->load($signup_data_array[0]['form_language'], 'signup');
		}else{
			$this->lang->load('en', 'signup');
		}
		
		$this->member_id=$signup_data_array[0]['member_id'];


		if(isset($_POST['Email'])){
			$_POST['email'] = $_POST['Email'];
			unset($_POST['Email']);
		}
		if($signup_data_array[0]['single_opt_in'] == '1'){
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
		}else{
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_email_check['.$form_id.']|trim');
		}
		//var_dump($signup_data_array);

		if($this->form_validation->run()){
			if($signup_data_array[0]['single_opt_in'] == '1'  and !in_array($this->member_id, $this->arrMemberHavingSingleOptinWithoutCaptcha)){ 
				$captcha = (isset($_POST['g-recaptcha-response']))?$_POST['g-recaptcha-response']: false;

				if(!$captcha){
				  echo '<p style="color:red">Please check the captcha form.</p>';
				  exit;
				}
				$response=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LfJ8wgTAAAAAL3feWmqDYLOoY2zSAsk4gkt9que&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
				if($response['success'] === false){
				  echo '<P style="color:red">You are spammer ! Get the @$%K out</P>';
				  exit;
				} 									
			}
		
		
			$signup_data = array();
			$serialize_data = array();
			$signup_data['subscriber_first_name']="";
			$signup_data['subscriber_last_name']="";
			
			foreach($_POST as $k=>$v){
				if($k!='listing_submit'){
					$k = trim($k);					
					if(strtolower($k)=='email'){
						$signup_data['subscriber_email_address']=mysql_real_escape_string(trim($this->input->post('email',true)));
						$arrEmailExploded = explode( '@',$signup_data['subscriber_email_address'] );
						$signup_data['subscriber_email_domain'] = $arrEmailExploded[1];
					}elseif($k=='name'){
						$name=mysql_real_escape_string(trim($this->input->post('name',true)));
						$name_arr=explode(" ",$name,2);
						$signup_data['subscriber_first_name']=trim($name_arr[0]);
						if($name_arr[1]!="")
						$signup_data['subscriber_last_name']=trim($name_arr[1]);

					}elseif($k=='first_name'){
						if($this->input->post('first_name',true)!="")
							$signup_data['subscriber_first_name']=mysql_real_escape_string(trim($this->input->post('first_name',true)));
					}elseif($k=='last_name'){
						if($this->input->post('last_name',true)!="")
						$signup_data['subscriber_last_name']=mysql_real_escape_string(trim($this->input->post('last_name',true)));
					}elseif($k=='company'){
						$signup_data['subscriber_company']=mysql_real_escape_string(trim($this->input->post('company',true)));
					}elseif($k=='address'){
						$signup_data['subscriber_address']=mysql_real_escape_string(trim($this->input->post('address',true)));
					}elseif($k=='city'){
						$signup_data['subscriber_city']=mysql_real_escape_string(trim($this->input->post('city',true)));
					}elseif($k=='state'){
						$signup_data['subscriber_state']=mysql_real_escape_string(trim($this->input->post('state',true)));
					}elseif($k=='zip_code'){
						$signup_data['subscriber_zip_code']=mysql_real_escape_string(trim($this->input->post('zip_code',true)));
					}elseif($k=='country'){
						$signup_data['subscriber_country']=mysql_real_escape_string(trim($this->input->post('country',true)));
					}elseif($this->isPhone($k)){
						$signup_data['subscriber_phone']=trim($v);
					}else{
						if($v !== FALSE && is_array($v)){						
							if(array_keys($v) == array('m','d','Y')){
								$v = date("F d, Y", mktime(null, null, null, $v['m'],$v['d'],$v['Y']));
							}else{
								$v = array_map('trim', $v);
								$v	= implode(',', $v) ;
							}
						}	
						$serialize_data[$k]=$v;
					}
				}
			} 
			
			
			$signup_data['subscriber_extra_fields']= serialize($serialize_data);

			$signup_data['is_signup']=1;
			$signup_data['signup_form_id']=$form_id;
			$signup_data['subscriber_status']=1;
			$signup_data['is_deleted']=1;
			$signup_data['subscriber_created_by']=$signup_data_array[0]['member_id'];
			$signup_data['subscriber_ip']= $this->is_authorized->getRealIpAddr();
			// IF ADMIN has disallowed signup form, then disable it
				$config_arr=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'continue_singup_form'));
				if( trim($config_arr[0]['config_value']) !="1"){
					die('ok|'.CAMPAIGN_DOMAIN.'newsletter/signup/verify_subscription/qweq');
					exit;
				}
			// IF ADMIN has disallowed signup form, then disable it
			// Check eligibility
			
			if($this->isEligibleToSubscribe($signup_data['subscriber_created_by'], $signup_data_array[0]['is_verified'])){
				#Insert subscriber in "All My List"
				$qry = "INSERT INTO red_email_subscribers SET ";
				$flds = '';
				foreach ($signup_data as $key=>$val) $flds .= $key . ' = \'' . mysql_real_escape_string(trim($val)) . '\', ';
				$flds .=  'subscription_id = -'.$this->member_id ;
				$qry .=  $flds .' ON DUPLICATE KEY UPDATE ' . $flds . ', subscriber_id=LAST_INSERT_ID(subscriber_id)';
				//echo $qry;
				$this->db->query($qry);
				// echo $this->db->last_query();exit;
				$subscriber_id = $this->db->insert_id();
				$is_stats = $this->db->query("select is_stats from red_signup_form where `id`='$form_id'")->row()->is_stats;
				if($is_stats > 0){
				// increment form-submssion-counter
				$this->db->query("update red_signup_form set form_submission_counter = (form_submission_counter + 1) where id='$form_id'");
				$IPasLong = ip2long($this->is_authorized->getRealIpAddr());
				$this->db->query("insert into red_signup_form_stats set `form_id` = $form_id ,  `ip_address`='$IPasLong' ,  `activity`=2, subscriber_id='$subscriber_id', activity_date=now() ON DUPLICATE  KEY UPDATE  activity_date=now() ");	
				$this->db->query("insert into red_signup_form_stats set `form_id` = $form_id ,  `ip_address`='$IPasLong' ,  `activity`=3, subscriber_id='$subscriber_id', activity_date=now() ON DUPLICATE  KEY UPDATE  activity_date=now() ");	
				}
				$subscription_id_arr=array_filter(explode(",",$signup_data_array[0]['subscription_id']));
				#Insert subscriber in "Other List"
				foreach($subscription_id_arr as $subscription_id){
					if($subscription_id>0){
						$input_array=array('subscriber_id'=>$subscriber_id,'subscription_id'=>$subscription_id);
						$this->Subscriber_Model->replace_subscription_subscriber($input_array);
					}
				}
				
				
				die('ok|'.CAMPAIGN_DOMAIN.'newsletter/signup/signup_confirmation/'.$subscriber_id.'/'.$form_id);
				exit;
			}else{
				die('ok|'.CAMPAIGN_DOMAIN.'newsletter/signup/signup_confirmation/');
				exit;
			}
		}else{
			if(validation_errors()){
				$validation_error= 'err|<div style="color:#FF0000;display: inline-block;font-size: 15px;font-weight: bold;  line-height: 20px;" >'.validation_errors().'</div>';
				die($validation_error);
				exit;
				$border_style='style="border:1px solid red !important;"';
			}else{
				$validation_error='';
				$border_style='';
			}
		}
				
		$copy_code='';

		if($form_id == '557')
		$copy_code.= "<form action='' method='post' class='signupform' style='width: 570px;height: 550px;background: #ffffff  url(\"https://www.redcappi.com/asset/user_files/4727/image_bank/20140709121147-MailingHeader.jpg\") no-repeat;'><table width='100%' border='0' cellspacing='0' cellpadding='0' class='formTable'  style='margin-top:220px;'>";
		else
		$copy_code.= '<form action="" method="post" class="signupform" id="signupform" accept-charset="UTF-8">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="formTable">';


		$copy_code.= "<tr><td class='form_title' style='font-weight:bold;font-size:27px;text-align:center;'><div class='header-txt' style='padding:20px 0 15px;background-color:{$signup_data_array[0]['header_background_color']};color:{$signup_data_array[0]['header_text_color']};'>{$signup_data_array[0]['form_title']}</div>";
		if(trim($signup_data_array[0]['header_background_image']) !=''){
			$copy_code.= "<img src='".$signup_data_array[0]['header_background_image']."' id='header-img' style='width:100%; height:auto;margin-top:-71px;' />";				
		}
		
		$copy_code.= "</td></tr>";
		$copy_code.= "<tr><td><div id='validation-error'></div></td></tr>";
		$arrSignupFormFields = unserialize($signup_data_array[0]['fld_sequence']);
		$copy_code.= $this->getSignupForm($arrSignupFormFields);
		if($signup_data_array[0]['single_opt_in'] == '1'   and !in_array($this->member_id, $this->arrMemberHavingSingleOptinWithoutCaptcha)){
			$copy_code.= '<tr><td><div class="g-recaptcha" style="transform:scale(0.77);transform-origin:0 0;/*margin: 0 auto;display: table;*/" data-sitekey="6LfJ8wgTAAAAAGp850U6FvVn5TnBuQ2JaPb_kiJm"></div></td></tr>';		
		}
		if(trim($signup_data_array[0]['form_button_img']) != ''){
		//$copy_code.= ' <tr><td><input type="image" name="listing_submit" src="'.$signup_data_array[0]['form_button_img'].'" id="btnSubmit"   content="Submit"></td></tr>';
		$copy_code.= ' <tr><td><button type="submit"><img src="'.$signup_data_array[0]['form_button_img'].'" id="btnImg" alt="Submit"></button></td></tr>';
		}else{
		$copy_code.= ' <tr><td><input type="submit" name="listing_submit" value="'.$signup_data_array[0]['form_button_text'].'" id="btnSubmit" class="submit_button"  content="Submit"></td></tr>';
		}
		$copy_code.= '</table></form>';
		$signup_data_array[0]['copy_code']=$copy_code;
		$signup_data_array[0]['copy_js']= $this->getSignupFormInlineJs($arrSignupFormFields);
		$signup_data_array[0]['bg_css']= (trim($signup_data_array[0]['background_background_image']) !='')?"background-image:url({$signup_data_array[0]['background_background_image']})" : 'background-image:none';
		$signup_data_array[0]['bg_css'] .= (trim($signup_data_array[0]['background_background_tile_image'])=='0')?";background-repeat:no-repeat":";background-repeat:repeat";


		$this->load->view('signup/signupform_url',array('signup_form'=>$signup_data_array[0]));
	}
	/**
	* function to Reate_limit the signup-form submission
	*/

	function isEligibleToSubscribe($mid, $isVerified){
		// If free user restrict after 100 Signup form submission
		// Restrict per minute 5 submission
		// Restrict per day 100 submission

		$retVal = false;
		if(!$isVerified){
			$rsCountSignupContact = $this->db->query("Select count(subscriber_id) as totSubscribed from red_email_subscribers where subscriber_created_by='$mid' and is_signup=1 and date_format(subscriber_date_added,'%Y-%m-%d') = curdate()");
			$intTotalSubscribed = $rsCountSignupContact->row()->totSubscribed;
			$rsCountSignupContact->free_result();
			//if($intTotalSubscribed < 26){
			if($intTotalSubscribed < 6){
				$retVal =  true;
			}
		}else{
			$retVal =  true;

			/* $user_packages_array=$this->UserModel->get_user_packages(array('member_id'=>$mid,'is_deleted'=>0));
			$package_id=$user_packages_array[0]['package_id'];
			if($package_id > 0){
				$retVal =  true;
			}else{
				$rsCountSignupContact = $this->db->query("Select count(subscriber_id) as totSubscribed from red_email_subscribers where subscriber_created_by='$mid' and is_signup=1 and is_deleted=1");
				$intTotalSubscribed = $rsCountSignupContact->row()->totSubscribed;
				$rsCountSignupContact->free_result();
				if($intTotalSubscribed < 3){
					$retVal =  true;
				}
			} */


		}
		return $retVal ;
	}
	/**
	* Function to check if email already exists in database before updating database
	* by input from user.
	*/
	function email_check($email="", $formid=0){

		// Check if contact already added or not
		$conditions_array['subscriber_email_address']=$email;
		$conditions_array['subscriber_created_by']=$this->member_id;
		$conditions_array['res.is_deleted']=0;

		$subscriber_array=$this->Subscriber_Model->get_subscriber_data($conditions_array);
		if(count($subscriber_array)>0){
			//	set validation message
			if($subscriber_array[0]['subscriber_status']==1){
				if($this->checkSubscriberInList($formid, $subscriber_array[0]['subscriber_id']) == 'yes'){
					$this->form_validation->set_message('email_check', 'Your already signed up for this mailing list. Please enter a different %s address.');
					return FALSE;
				}else{
					return true;
				}
			}else{
				//$this->form_validation->set_message('email_check', '%s already exists in the Do Not Mail List of your account.');
				return true;
			}
		}
		return true;
	}

	function checkSubscriberInList($formId=0,$subscriberid=0){
		$retVal = 'yes'; // assume contact is already in the list(s)

		// Get SignupForm Data
		$signup_data_array=$this->Signup_Model->get_signup_data(array('is_deleted'=>0,'id'=>$formId));
		if(count($signup_data_array) > 0){
			$subscription_id_arr=array_filter(explode(",",$signup_data_array[0]['subscription_id']));
			// Check subscriber in "Other List"
			foreach($subscription_id_arr as $subscription_id){
				if($subscription_id>0){
					$sqlCheck = "select subscriber_id from `red_email_subscription_subscriber` where `subscriber_id` = '$subscriberid' and `subscription_id`='$subscription_id'";
					$rsCheckSubscriberInList = $this->db->query($sqlCheck);
					if($rsCheckSubscriberInList->num_rows() <= 0){
						if($subscription_id>0){
							$input_array=array('subscriber_id'=>$subscriberid,'subscription_id'=>$subscription_id);
							$this->Subscriber_Model->replace_subscription_subscriber($input_array);
						}

						$retVal = 'no';// if any one list is found which has no record for contact, set the flag as no
					}
				}
			}
			if($retVal == 'no'){
				$eml = $this->Subscriber_Model->getSubscriberEmailId($subscriberid);
				$encodedURLData = $this->is_authorized->base64UrlSafeEncode($subscriberid."-".$eml."-".$formId);
				die('ok|'.CAMPAIGN_DOMAIN.'newsletter/signup/verify_subscription/'.$encodedURLData);
				exit;
			}
		}

		return $retVal;
	}
	function signup_delete($id=0){
		//Check if user is not login then redirect to index page
		if($this->session->userdata('member_id')=='')
			redirect('user/index');

		//	Collect signup form id
		//Protecting MySQL from query string sql injection Attacks
		if(is_numeric($id)){
			$form_id = $id;
		}else{
			$form_id=0;
		}

		//Fetch subscription data from database by subscription ID
		$signup_array=$this->Signup_Model->get_signup_data(array('id'=>$form_id,'member_id'=>$this->session->userdata('member_id')));

		//Redirects user to listing page if user have not created this form or form does not exists
		if(!count($signup_array))
		{
			// Assign  error message by message class
			echo 'error|Signup form does not exists or you have not created this signup form';
		}

		// Deletes subscription according to subscription ID
		$this->Signup_Model->delete_signup(array('id'=>$form_id));
		// Assign  success message by message class
		echo 'success|Signup form deleted successfully';
	}
	/**
	*	Signup confirmation  function
	*/
	function signup_confirmation_de(){
		$msg= '<h3>N�chster Schritt, um den Anmeldeprozess abzuschlie�en...</h3>
				<div style="clear:both;margin-bottom:40px;">
				Sie m�ssen auf den Link klicken, dass <br/>wir nur Ihre E-Mail-Adresse gesendet.
			   </div>';

		$this->load->view('signup/sign_up_confirmation',array('msg'=>utf8_encode($msg),'rc_logo'=>1));
	}
	function signup_confirmation_pt(){
		$msg= '<h3>Mais um passo para concluir o processo de sinal para cima...</h3>
				<div style="clear:both;margin-bottom:40px;">
				Voc� ter� que clicar no link que <br/>acabamos de enviar para seu endere�o de e-mail.
			   </div>';

		$this->load->view('signup/sign_up_confirmation',array('msg'=>utf8_encode($msg),'rc_logo'=>1));
	}
	function signup_confirmation($subscriber_id=0,$sigup_form_id=0){
		//Protecting MySQL from query string sql injection Attacks
		if(!is_numeric($subscriber_id) || $subscriber_id==0){
		// This is a false message to subscribers			 
			$msg= '<h3>One more step in order to complete the sign up process...</h3>
				<div style="clear:both;margin-bottom:40px;">
				You will need to click on the link that <br/>we just sent to your email address.
			   </div>';

			echo $this->load->view('signup/sign_up_confirmation',array('signup_id'=>0,'msg'=>$msg,'rc_logo'=>1),true);
			exit;
		}

		//Fetch data from database

		$subscriber_data_array=$this->Subscriber_Model->get_subscriber_data(array('res.subscriber_id'=>$subscriber_id));
		$user_id=$subscriber_data_array[0]['subscriber_created_by'];
		$to_email=$subscriber_data_array[0]['subscriber_email_address'];

		// Fetch sigh-up form  data from database
		$signup_array=$this->Signup_Model->get_signup_data(array('id'=>$sigup_form_id,'member_id'=>$user_id));
		$signup_array[0]['form_language'] = ($signup_array[0]['form_language'] !='')?$signup_array[0]['form_language']:'en';
		
		if($signup_array[0]['single_opt_in'] == '1'){
			
			$this->subscriber_update($subscriber_id, $sigup_form_id);
		}else{
			
			$this->confirm_subscription_notification($subscriber_id,$sigup_form_id,$user_id,$to_email);

			if((trim($signup_array[0]['confirmation_thanks_you_message_url'])!="")&&(trim(strtolower($signup_array[0]['confirmation_thanks_you_message_url']))!="http:/")){
				
				//redirect($signup_array[0]['confirmation_thanks_you_message_url']);
				echo "<script language='javascript'>window.location.href='".$signup_array[0]['confirmation_thanks_you_message_url']."';</script>";
				exit;
			}else{
				
				if($signup_array[0]['form_language'] != ''){
					$this->lang->load($signup_array[0]['form_language'], 'signup');
				}else{
					$this->lang->load('en', 'signup');
				}
				$msg= '<h3>'. $this->lang->line('form_submit_confirmation_heading').'</h3>'.
					'<div style="clear:both;margin-bottom:40px;">'.
					 $this->lang->line('form_submit_confirmation_content').
				   '</div>';
				# Fetch user data from database
				$user=$this->UserModel->get_user_data(array('member_id'=>$user_id));
				$rc_logo=$user[0]['rc_logo'];	#set rc logo
				$this->load->view('signup/sign_up_confirmation',array('signup_id'=>$subscriber_id,'msg'=>$msg,'rc_logo'=>$rc_logo));
			}
		}
	}
	
	/**
	*	Function to send signup notification email to user
	*/
	function confirm_subscription_notification($subscriber_id=0,$sigup_form_id=0,$user_id=0,$to_email=""){
		// Fetch sigup form  data from database by signup form ID
		$signup_array=$this->Signup_Model->get_signup_data(array('id'=>$sigup_form_id,'member_id'=>$user_id));
		
		// Fetch user data from database
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$user_id));
		$vmta = $user_data_array[0]['vmta'];
		$from_email = (trim($signup_array[0]['from_email'])!="")? $signup_array[0]['from_email'] : $user_data_array[0]['email_address'];
		if(trim($signup_array[0]['from_name'])!=""){
			$from_name=$signup_array[0]['from_name'];
		}else{
			$from_name =($user_data_array[0]['company']!="")? $user_data_array[0]['company']: $user_data_array[0]['member_username'];
		}
		// $user_info=array($from_name,$from_email,$subscriber_id,$to_email,$sigup_form_id,$signup_array[0]['confirmation_emai_message'],$signup_array[0]['subject'], $vmta);
		// $this->load->helper('transactional_notification');
		// create_transactional_notification("confirm_subscription",$user_info);


		// Following Code  updated on 20140912
		$encodedURLData = $this->is_authorized->base64UrlSafeEncode($subscriber_id."-".$to_email."-".$sigup_form_id);
		$link=site_url("newsletter/signup/verify_subscription/".$encodedURLData);

		if(trim($signup_array[0]['confirmation_emai_message'])==""){
			 if($signup_array[0]['form_language'] != ''){
					$this->lang->load($signup_array[0]['form_language'], 'signup');
				}else{
					$this->lang->load('en', 'signup');
				} 
			$confirmationMailBody = $this->lang->line('form_submit_email_content_1')."\r\n".
$this->lang->line('form_submit_email_content_2')."\r\n\r\n
$link
							\r\n\r\n
".$this->lang->line('form_submit_email_content_3')."
							\r\n";
		}else{
			$confirmationMailBody = $signup_array[0]['confirmation_emai_message']."\r\n\r\n
$link
						\r\n\r\n	";
		}
		
		$subject = ('' != trim($signup_array[0]['subject']))?trim($signup_array[0]['subject']):'Please Confirm Your Subscription';
		
		$this->load->helper('transactional_notification');
		send_tmail_plain_text($to_email, $from_email, $from_name, $subject,  $confirmationMailBody, $vmta);
	}
	function subscriber_update($subscriber_id=0,$sigup_form_id=0){
		if(is_numeric($subscriber_id)){
			$id = $subscriber_id;
		}else{
			$id=0;
			echo "error:subscriber id not exist";
			exit;
		}

		# get subscreber email
		$subscriber_data_array=$this->Subscriber_Model->get_subscriber_data(array('res.subscriber_id'=>$id,'res.is_deleted'=>1));

		# Update subscriber
		$this->Subscriber_Model->update_subscriber(array('is_deleted'=>0,'is_single_optin'=>1),array('subscriber_id'=>$subscriber_id,'subscriber_created_by'=>$subscriber_data_array[0]['subscriber_created_by']));
		
		$is_stats = $this->db->query("select is_stats from red_signup_form where `id`='$sigup_form_id'")->row()->is_stats;
		if($is_stats > 0){
		$IPasLong = ip2long($this->is_authorized->getRealIpAddr());
		$this->db->query("insert into red_signup_form_stats set `form_id` = $sigup_form_id ,  `ip_address`='$IPasLong' ,  `activity`=3, subscriber_id='$id', activity_date=now() ON DUPLICATE  KEY UPDATE  activity_date=now() ");	
		}
		
		
		if(count($subscriber_data_array)>0){
			# Load log activity model class which handles database interaction
			$this->load->model('Activity_Model');
			# create array for insert values in activty table
			$values=array('user_id'=>$subscriber_data_array[0]['subscriber_created_by'], 'activity'=>'contact_add: '.$subscriber_data_array[0]['subscriber_email_address'],		  'number_of_contacts'=>1,  'contact_list_type'=>4	);
			$this->Activity_Model->create_activity($values);
		}
		#Fetch sigup form  data from database by signup form ID
		$signup_array=$this->Signup_Model->get_signup_data(array('id'=>$sigup_form_id,'member_id'=>$subscriber_data_array[0]['subscriber_created_by']));
		$this->lang->load($signup_array[0]['form_language'], 'signup');
		#Check user singup thank you message url exist or not
		if((trim($signup_array[0]['singup_thank_you_message_url'])!="")&&(trim(strtolower($signup_array[0]['singup_thank_you_message_url']))!="http:/")){
			redirect($signup_array[0]['singup_thank_you_message_url']);
		}else{
			# print success message
			$msg= '<h3>'.$this->lang->line('link_clicked_confirmation_content').'</h3>';
			# Fetch user data from database
			$user=$this->UserModel->get_user_data(array('member_id'=>$subscriber_data_array[0]['subscriber_created_by']));
			$rc_logo=$user[0]['rc_logo'];	#set rc logo
			$this->load->view('signup/sign_up_confirmation',array('msg'=>$msg,'rc_logo'=>$rc_logo));
		}
	}
	function get_text_in_user_selected_language($user_id=0,$text=""){

		# Fetch user data from database
		$user=$this->UserModel->get_user_data(array('member_id'=>$user_id));
		$user_language=$user[0]['language'];


		#fetch text according to user selected language
		$language_text_arr=$this->UserModel->get_language_text(array('language'=>$user_language,'text_code'=>$text));

		if(count($language_text_arr)<=0){
			$language_text=$this->UserModel->get_language_text(array('language'=>'en','text_code'=>$text));
			$language_code=$user_language;

			$this->load->library('Languagetranslator'); # load language translate library
			$source = 'en';		#source language
			$target = $language_code;	#target language
			#Covert text language in target language
			foreach($language_text as $text){
				$sourceData = $text['text'];
				$targetData = $this->languagetranslator->translate($sourceData,$target, $source);
				$input_array=array('language'=>strtolower($language_code),'text_code'=>$text['text_code'],'text'=>$targetData);
				#update text in language table
				$this->UserModel->create_language($input_array);
				return $targetData;
			}
		}else{
			return $language_text_arr[0]['text'];
		}
	}
	/**
	 *	Function isPhone
	 *
	 *	'isPhone' controller function supporting phone heading for import csv file
	 *
	 *	@param (string) (strCol)  contains phone heading of subscriber
	 *
	 *	@return (bool)  return true if validate true otherwise false
	 */
	function isPhone($strCol){
		$strCol = trim($strCol);
		if($strCol != '' and (stripos(strtolower($strCol), 'phone')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'phone')!== false and stripos(strtolower($strCol), 'number')!== false ) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'phone')!== false and stripos(strtolower($strCol), 'no')!== false ) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'phone_number')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'phone-number')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'phone')!== false and stripos(strtolower($strCol), '#')!== false ) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'phone_#')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'phone-#')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'telephone')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'telephone')!== false and stripos(strtolower($strCol), 'number')!== false ) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'telephone')!== false and stripos(strtolower($strCol), '#')!== false ) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'Telephone_Number')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'Telephone-Number')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'tel')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'tel.')!== false) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'contact')!== false and stripos(strtolower($strCol), 'number')!== false ) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'contact')!== false and stripos(strtolower($strCol), 'no')!== false ) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'cell')!== false and stripos(strtolower($strCol), 'number')!== false ) ){
			return true;
		}else if($strCol != '' and (stripos(strtolower($strCol), 'mobile')!== false and stripos(strtolower($strCol), 'number')!== false ) ){
			return true;
		}else{
			return false;
		}
	}
	/**
	*	Function signup_custom_frm to save advance setting of signup form
	*/
	function signup_custom_frm($signup_form_id=0){
		
		$signup_form_id = $this->session->userdata('signup_id');
		
		if($signup_form_id == 0){
			$thisMid = $this->session->userdata('member_id');
			$user_packages_array=$this->UserModel->get_user_packages(array('member_id'=>$thisMid,'is_deleted'=>0));
			$is_verified	=  ($user_packages_array[0]['package_id'] > 0 )? 1: 0; //paid user
			$allMyContactsID = 0 - $this->session->userdata('member_id');
			$this->db->query("INSERT INTO `red_signup_form` (`form_name`, `form_title`, `form_button_text`, `form_background_color`, `header_background_color`, `header_text_color`, `member_id`, `subscription_id`, `custom_field`, `is_email`, `field_sequence`, `is_verified`) VALUES ('Unnamed', 'Join our mailing list', 'Subscribe', '#F0F1F3', '#FFFFFF', '#454545', '$thisMid', '$allMyContactsID', '', 1, 'a:1:{s:5:\"email\";s:0:\"\";}', '$is_verified')");

			$signup_form_id = $this->db->insert_id();
		
			$this->session->set_userdata('signup_id', $signup_form_id);
		}
	
		$this->form_validation->set_rules('from_name', 'From Name', 'max_length[100]|xss_clean');
		$this->form_validation->set_rules('from_email', 'Email', 'max_length[100]|valid_email|trim');
		$this->form_validation->set_rules('subject', 'Subject', 'max_length[100]|xss_clean');
		//$this->form_validation->set_rules('confirmation_thanks_you_message_url', 'Confirmation Thank You Url', 'xss_clean|valid_url');
		//$this->form_validation->set_rules('singup_thank_you_message_url', 'Signup Thank You Url', 'xss_clean|valid_url');

		if(($this->input->post('custom_frm_action')=='submit')&&($this->form_validation->run()==true)){
			$signup_data['confirmation_thanks_you_message_url']=$this->input->post('singup_thank_you_message_url');
			$signup_data['singup_thank_you_message_url']=$this->input->post('confirmation_thanks_you_message_url');
			$signup_data['form_language']=$this->input->post('form_language');
			$signup_data['from_name']=$this->input->post('from_name');
			$signup_data['from_email']=$this->input->post('from_email');
			$signup_data['subject']=$this->input->post('subject');
			$signup_data['confirmation_emai_message']=$this->input->post('confirmation_emai_message');
			$signup_data['form_language']=$this->input->post('form_language');
			#To update signup form in database
			$this->Signup_Model->update_signup($signup_data,array('id'=>$signup_form_id));
			
			echo "success";
		}else{
			echo 'error:'.validation_errors();
		}
	}
	function verify_subscription($sid){
		$encDataBlock = trim($this->is_authorized->base64UrlSafeDecode($sid));
		if($encDataBlock != ''){
			$arrDataBlock = @explode('-',$encDataBlock);
			$subscriber_id 		= $arrDataBlock[0];
			$subscriber_email 	= $arrDataBlock[1];
			$subscriber_form_id = $arrDataBlock[2];



			# get subscriber email
			#$subscriber_data_array=$this->Subscriber_Model->get_subscriber_data(array('res.subscriber_id'=>$subscriber_id,'res.is_deleted'=>1));
			$subscriber_data_array=$this->Subscriber_Model->get_subscriber_data(array('res.subscriber_id'=>$subscriber_id,'res.subscriber_email_address'=>$subscriber_email));

			// Activate subscriber
			$this->Subscriber_Model->update_subscriber(array('is_deleted'=>0,'subscriber_status'=>1),array('subscriber_id'=>$subscriber_id,'subscriber_created_by'=>$subscriber_data_array[0]['subscriber_created_by']));
			$is_stats = $this->db->query("select is_stats from red_signup_form where `id`='$subscriber_form_id'")->row()->is_stats;
			if($is_stats > 0){
			$IPasLong = ip2long($this->is_authorized->getRealIpAddr());
			$this->db->query("insert into red_signup_form_stats set `form_id` = $subscriber_form_id ,  `ip_address`='$IPasLong' ,  `activity`=3, subscriber_id='$subscriber_id', activity_date=now() ON DUPLICATE  KEY UPDATE  activity_date=now() ");	
			}
			if(count($subscriber_data_array)>0){
				// log activity
				$this->load->model('Activity_Model');
				$this->Activity_Model->create_activity(array('user_id'=>$subscriber_data_array[0]['subscriber_created_by'], 'activity'=>'contact_add:'.$subscriber_email, 'number_of_contacts'=>1, 'contact_list_type'=>4 ));
				// increment contact_confirmation_counter
				$this->db->query("update red_signup_form set contact_confirmation_counter = (contact_confirmation_counter + 1) where id='$subscriber_form_id'");
			}
			// Fetch sigup form details
			$signup_array=$this->Signup_Model->get_signup_data(array('id'=>$subscriber_form_id,'member_id'=>$subscriber_data_array[0]['subscriber_created_by']));
			
			// Check user singup thank you message url exist or not
			if((!is_null($signup_array[0]['singup_thank_you_message_url']))&&(trim($signup_array[0]['singup_thank_you_message_url'])!="")&&(trim(strtolower($signup_array[0]['singup_thank_you_message_url']))!="http://")){
				redirect($signup_array[0]['singup_thank_you_message_url']);
				exit;
			}else{
				if($signup_array[0]['form_language'] != ''){
				$this->lang->load($signup_array[0]['form_language'], 'signup');
				}else{
					$this->lang->load('en', 'signup');
				}
				# print success message
				$msg= '<h3>'.$this->lang->line('link_clicked_confirmation_content').'</h3>';
				# Fetch user data from database
				$user=$this->UserModel->get_user_data(array('member_id'=>$subscriber_data_array[0]['subscriber_created_by']));
				$rc_logo=$user[0]['rc_logo'];	#set rc logo
				$this->load->view('signup/sign_up_confirmation',array('msg'=>$msg,'rc_logo'=>$rc_logo));
			}

		}

	}
	function thanks_de(){
		$msg = '<h3>Vielen Dank f�r Ihre Teilnahme!</h3>';
		$this->load->view('signup/sign_up_confirmation',array('msg'=>$msg,'rc_logo'=>1));
	}
	function thanks_pt(){
		$msg = '<h3>Obrigado pela sua participa��o!</h3>';
		$this->load->view('signup/sign_up_confirmation',array('msg'=>$msg,'rc_logo'=>1));
	}
	function get_Admin_notification_email(){
		$sql            = 'SELECT config_name,config_value FROM `red_site_configurations` where `config_name` = "admin_notification_email"';
		$query          = $this->db->query($sql);
		$admin_email	= "";
		if ($query->num_rows() == 1){
			$row = $query->row();
			$admin_email        = $row->config_value;
		}
		return $admin_email;
	}

	function signup_translate(){
		$language_code=strtolower($this->input->post('language'));		
		$this->lang->load("{$language_code}", 'signup');
		
		echo $this->lang->line('name').' = '.$this->lang->line('email').' = '.$this->lang->line('first_name').' = '.$this->lang->line('last_name').' = '.$this->lang->line('company').' = '.$this->lang->line('address').' = '.$this->lang->line('city').' = '.$this->lang->line('state').' = '.$this->lang->line('zip_code').' = '.$this->lang->line('country').' = '.$this->lang->line('form_submit_email_content_1').' = '.$this->lang->line('form_submit_email_content_2').' = '.$this->lang->line('form_submit_email_content_3');

		// echo $lang;
	}
	function getAdvanced(){
		$signup_id = $this->session->userdata('signup_id');
		
			// Fetch user data from database
			$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));
			
			if($signup_id > 0){
				$signup_data_array['form']=$this->Signup_Model->get_signup_data(array('is_deleted'=>0,'id'=>$signup_id));
			}			
			$signup_data_array['form'][0]['form_language'] = (isset($signup_data_array['form'][0]['form_language']))?$signup_data_array['form'][0]['form_language']:'en';
			$this->lang->load($signup_data_array['form'][0]['form_language'], 'signup');
			
			
			$signup_data_array['form'][0]['confirmation_emai_message']=(isset($signup_data_array['form'][0]['confirmation_emai_message']))?$signup_data_array['form'][0]['confirmation_emai_message']:"To activate your subscription, please follow the link below.
If you can't click it, please copy the entire link and paste it into your browser.

Thank You!
";
			$signup_data_array['form'][0]['confirmation_thanks_you_message_url']=(isset($signup_data_array['form'][0]['confirmation_thanks_you_message_url']) && trim($signup_data_array['form'][0]['confirmation_thanks_you_message_url']) !='')?$signup_data_array['form'][0]['confirmation_thanks_you_message_url']:"http://";
			$signup_data_array['form'][0]['singup_thank_you_message_url']=(isset($signup_data_array['form'][0]['singup_thank_you_message_url']) && trim($signup_data_array['form'][0]['singup_thank_you_message_url']) != '')?$signup_data_array['form'][0]['singup_thank_you_message_url']:"http://";
			
			$signup_data_array['form'][0]['from_email']=(isset($signup_data_array['form'][0]['from_email']) && $signup_data_array['form'][0]['from_email']!='')?$signup_data_array['form'][0]['from_email']:$user_data_array[0]['email_address'];
			
			
			
			
			$signup_data_array['form'][0]['from_name'] = (isset($signup_data_array['form'][0]['from_name']) && $signup_data_array['form'][0]['from_name'] != '')?$signup_data_array['form'][0]['from_name']:trim($user_data_array[0]['company']);
			
			$signup_data_array['form'][0]['from_name'] = ($signup_data_array['form'][0]['from_name'] != '')?$signup_data_array['form'][0]['from_name']:trim($user_data_array[0]['member_username']);
			
			$signup_data_array['form'][0]['subject']=(isset($signup_data_array['form'][0]['subject']) && $signup_data_array['form'][0]['subject'] !='')?$signup_data_array['form'][0]['subject']:"Please Confirm Your Subscription";			
				
			 

			$rsLanguage = $this->db->query("select * from red_language order by language");
			$arrLanguage = array();
			foreach($rsLanguage->result_array() as $langRow){
				$langCode = $langRow['language_code'];
				$arrLanguage[$langCode] = $langRow['language'] ;
			}
			$rsLanguage->free_result();
			$signup_data_array['email_id']	= $this->getFromEmlArray();
			
		
				
			echo	$this->load->view('signup/signup_advanced',array('signup_from'=>$signup_data_array, 'signup_froms_language'=>$arrLanguage, 'messages' =>$messages),true);
				
			 
	}
	function showpblogo($fid=0){
		header("Content-Type: image/gif");
		readfile($this->config->item('webappassets_path').'images/pix.gif');		 
		if($fid > 0){
			$is_stats = $this->db->query("select is_stats from red_signup_form where `id`='$fid'")->row()->is_stats;
			if($is_stats > 0){
			$IPasLong = ip2long($this->is_authorized->getRealIpAddr());
			$this->db->query("insert into red_signup_form_stats set `form_id` = $fid ,  `ip_address`='$IPasLong' ,  `activity`=1, activity_date=now() ON DUPLICATE  KEY UPDATE  activity_date=now()");		
			}
		}
	}
	
	function stats_detail($fid=0,$start=0){
		
		$mid	= $this->session->userdata('member_id');		
		$rs_signupform_data=$this->db->query("select id,form_name,date_added from red_signup_form where `id`='$fid' and is_stats=1 and is_deleted=0 and member_id='$mid'");	
		$numrec = $rs_signupform_data->num_rows();	
		$form_row =  array();		
		foreach($rs_signupform_data->result_array() as $row){
			$form_row[]=$row;
		}
		$rs_signupform_data->free_result();
		
		if($numrec  <= 0){
			redirect('user/index');
			exit;		
		} 
			
			
			
			$config['base_url']=base_url().'newsletter/signup/stats_detail/'.$action."/".$fid;	
			$config['per_page']			=	50;
			$config['uri_segment']		=	6;
			$config['num_links']		=	4;	// Number of "digit" links to show before/after the currently viewed page
			$config['full_tag_open']	= 	'<ul class="pagination">';
			$config['full_tag_close'] 	= 	'</ul>';
			$config['cur_tag_open'] 	= 	'<li><a class="selected">';
			$config['cur_tag_close'] 	= 	'</a></li>';
			$config['first_tag_open'] 	= 	'<li>';
			$config['first_tag_close'] 	= 	'</li>';
			$config['last_tag_open'] 	= 	'<li>';
			$config['last_tag_close'] 	= 	'</li>';
			$config['num_tag_open'] 	= 	'<li>';
			$config['num_tag_close'] 	= 	'</li>';
			$config['next_tag_open'] 	= 	'<li>';
			$config['next_tag_close'] 	= 	'</li>';
			$config['prev_tag_open'] 	=	'<li>';
			$config['prev_tag_close'] 	= 	'</li>';
			$i = 0;
			
			
			$visit_count = $this->db->query("select count(form_id)ct from red_signup_form_stats where `form_id`='$fid' and activity='1'")->row()->ct;
			$signup_count = $this->db->query("select count(form_id)ct from red_signup_form_stats where `form_id`='$fid' and activity='3'")->row()->ct;
			
			$current_tab="signups";
			$config['total_rows']= $signup_count;
			$signupform_data=$this->Signup_Model->get_signupform_stats(array('form_id'=>$fid,'activity'=>'3'),$config['per_page'],$start);
			     
			
			
				$this->pagination->initialize($config);
				$paging_links=$this->pagination->create_links();
			// For Sent, read, unread, unsubscribes, complaints, bounced, 
				foreach($signupform_data as $contact){
					$subscriber_info=$this->Subscriber_Model->get_subscriber_info_view(array('subscriber_id'=>$contact['subscriber_id'],'is_deleted'=>0,'subscriber_created_by'=>$mid));
					$signupform_contacts[$i]['subscriber_id']=$contact['subscriber_id'];
			
					if(count($subscriber_info)>0){
						$signupform_contacts[$i]['subscriber_email_address']=$subscriber_info[0]['subscriber_email_address'];
					}
					$signupform_contacts[$i]['subscriber_first_name']=$subscriber_info[0]['subscriber_first_name'];
					$signupform_contacts[$i]['subscriber_last_name']=$subscriber_info[0]['subscriber_last_name'];
					$i++;
				}
			 
				$previous_page_url=$this->session->userdata('HTTP_REFERER_EMAIL');

				$this->load->view('header',array('title'=>'Signup form stats','previous_page_url'=>$previous_page_url));
				$this->load->view('signup/signupform_stats',array('emailreport_data'=>$signupform_contacts,'form_detail'=>$form_row[0],'total_rows'=>$config['total_rows'],'signupform_id'=>$fid,'paging_links'=>$paging_links, 'visit_count'=>$visit_count, 'signup_count'=>$signup_count, 'current_tab'=>$current_tab));
				$this->load->view('footer-inner-red');
			
							
		
	}
	
	function checkemailexistinlist($form_id = 0,$email = ''){
		$retval = 'yes';
		$signup_data_array = $this->Signup_Model->get_signup_data(array('is_deleted' => 0, 'id' => $form_id));
		
		
		if (count($signup_data_array) > 0) {
			$subscriber_id = $this->db->query("select subscriber_id from red_email_subscribers where subscriber_email_address = '".$email."' and subscriber_created_by = ".$signup_data_array[0]['member_id']." and is_deleted = 0")->row()->subscriber_id;
			
		
			$subscription_id_arr = array_filter(explode(",", $signup_data_array[0]['subscription_id']));
			if($subscriber_id){
			
				foreach ($subscription_id_arr as $subscription_id) {
					if ($subscription_id > 0) {
						$sqlCheck = "select subscriber_id from `red_email_subscription_subscriber` where `subscriber_id` = '$subscriber_id' and `subscription_id`='$subscription_id'";
						
						$rsCheckSubscriberInList = $this->db->query($sqlCheck);
						
						if ($rsCheckSubscriberInList->num_rows() <= 0) {
							/* if ($subscription_id > 0) {
								$input_array = array('subscriber_id' => $subscriber_id, 'subscription_id' => $subscription_id);
								$this->Subscriber_Model->replace_subscription_subscriber($input_array);
							} */

							$retval = 'no'; // if any one list is found which has no record for contact, set the flag as no
						}
					}	
				}
			
				
				
			}else{
				
				$retval = 'no';
			}
			
			//echo $retval;exit;
				 
			
		}
		return $retval;
		
	}
 
}
?>
