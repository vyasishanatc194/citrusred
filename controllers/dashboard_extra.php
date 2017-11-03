<?php
/**
* A Dashboard_extra class
*
* This class is for Dashboard management.
*
* @version 1.0
* @author Pravin Jha <pravinjha@gmail.com>
* @project Redcappi
*/
class Dashboard_extra extends CI_Controller
{
	/*
		Contructor for controller.
		It checks user session and redirects user if not logged in
	*/
	function __construct(){
        parent::__construct();

		# check via common model
		if(!$this->is_authorized->check_user())
			redirect('user/index');



		if(!count($this->session->userdata('user_packages')))
			redirect('user/packages');
		$this->load->library('upload');
		//Load page model
		$this->load->model('newsletter/Page_Model');
		$this->load->model('UserModel');
		$this->upload_path=substr(FCPATH,0,strrpos(FCPATH,DIRECTORY_SEPARATOR));
		
		force_ssl();	
	}

	/*
		'Index' controller. By default it calls dashboard_extra_list function.
	*/

	function index(){
		$this->dashboard_extra_list();
	}
	function dashboard_extra_list(){
		# Fetch member data from database
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));
		if($user_data_array[0]['autoresponder_status']==0){
			$user_data['autoresponder_status']="Inactive";
			$user_data['autoresponder_class']="inactive";
		}else{
			$user_data['autoresponder_status']="Active";
			$user_data['autoresponder_class']="active";
		}
		if($user_data_array[0]['sign_up_form_status']==0){
			$user_data['sign_up_form_status']="Inactive";
			$user_data['sign_up_form_class']="inactive";
		}else{
			$user_data['sign_up_form_status']="Active";
			$user_data['sign_up_form_class']="active";
		}
		if($user_data_array[0]['google_analytics_status']==0){
			$user_data['google_analytics_status']="Inactive";
			$user_data['google_analytics_class']="inactive";
		}else{
			$user_data['google_analytics_status']="Active";
			$user_data['google_analytics_class']="active";
			$user_data['allDomains'] = $this->get_domains();
		}
		if($user_data_array[0]['clicktracking_status']==0){
			$user_data['clicktracking_status']=0;
			$user_data['clicktracking_class']="inactive";
		}else{
			$user_data['clicktracking_status']=1;
			$user_data['clicktracking_class']="active";
		}
		/* $languages=$this->UserModel->get_languages();
		foreach($languages as $language){
			$language_array[]=$language['language'];
		} */
		$user_data['languages']=$language_array;
		$user_data['selected_user_language']=$user_data_array[0]['language'];
		// User API Details
		$user_api_data_array=$this->UserModel->get_user_api();
		if(is_null($user_api_data_array)){
			$user_data['api_status']="Generate";
			$user_data['api_class']="inactive";
		}else{
			$user_data['api_status']="Re-generate";
			$user_data['api_class']="active";
			$user_data['public_api_key'] = $user_api_data_array['public_key'];
			$user_data['private_api_key'] = $user_api_data_array['private_key'];
		}

		$this->load->view('header',array('title'=>'My Dashboard'));
		$this->load->view('dashboard_extra_list',$user_data);
		$this->load->view('footer');
	}
	// update status in user table
	function update(){

		$update_array=array();
		foreach($_POST as $key=>$val){
			if($val==1){
				$update_array[$key]=0;
			}else{
				$update_array[$key]=1;
			}
			$this->UserModel->update_user($update_array,array('member_id'=>$this->session->userdata('member_id')));

			if($key=="autoresponder_status"){
				// Load the cronjob model which interact with database
				$this->load->model('newsletter/Cronjob_Model');
				$this->Cronjob_Model->update_autoresponder_cronjob(array('autoresponder_scheduled_status'=>$update_array[$key]),array('subscription_ids'=>'-'.$this->session->userdata('member_id')));
			}
			echo $update_array[$key];
		}
	}
	/**
	* update status in user table
	*/
	function generate_api(){
		$newPublicKey = hash('sha256', openssl_random_pseudo_bytes(32));
		$newPrivateKey = hash('sha256', openssl_random_pseudo_bytes(32));
		$qryAPI = "INSERT INTO `red_member_api` SET ";
		$flds = "`date_created`=now(), `public_key`='$newPublicKey', `private_key`='$newPrivateKey'";
		$qryAPI .=  $flds .',`member_id`='.$this->session->userdata('member_id').' ON DUPLICATE KEY UPDATE ' . $flds  ;

		$this->db->query($qryAPI);

		echo "<p><strong>Your API Keys:</strong><br/>
			Public Key: {$newPublicKey}<br/>
			Private Key: {$newPrivateKey}<br/>
			</p>";

	}
	/**
		Function update_language to update language for login user
	*/
	function update_language(){
		# Load the user model which interact with database
		$this->load->model('UserModel');
		$update_array=array('language'=>$this->input->post('language'));
		$updated_id=$this->UserModel->update_user($update_array,array('member_id'=>$this->session->userdata('member_id')));

		if($updated_id>0){
			#Fetch text from language table
			$language_text=$this->UserModel->get_language_text(array('language'=>strtolower($this->input->post('language'))));
			if(count($language_text)<=0){
				$language_text=$this->UserModel->get_language_text(array('language'=>'en'));
				$language_code=strtolower($this->input->post('language'));
				$this->load->library('Languagetranslator'); # load language translate library
				$source = 'en';		#source language
				$target = $language_code;	#target language
				#Covert text language in target language
				foreach($language_text as $text){
					$sourceData = $text['text'];
					$targetData = $this->languagetranslator->translate($sourceData,$target, $source);
					$input_array=array('language'=>strtolower($this->input->post('language')),'text_code'=>$text['text_code'],'text'=>$targetData);
					#update text in language table
					$this->UserModel->create_language($input_array);
				}
			}
			echo "updated";
		}
	}

	function get_domains(){
		$retval = '';
		$mid = $this->session->userdata('member_id');
		$rsGetDomains = $this->db->query("Select * from `red_ga_domains` where `member_id`='$mid'");
		if($rsGetDomains->num_rows() > 0){
			foreach($rsGetDomains->result_array() as $recGetDomains){
				$retval .= '<span>'.$recGetDomains['domain_name'].'<a class="boxclose"><i class="icon-remove"></i></a></span>';
			}
		}
		return $retval;
	}
	function add_domain(){
		$url = $this->input->post('strDomain');
		if (!preg_match('/^http(s)?:\/\//', $url))
		$url = 'http://' . $url;

		$host = parse_url($url, PHP_URL_HOST);
		$mid = $this->session->userdata('member_id');


		$input_arr = array('member_id'=>$mid,'domain_name'=>$host);
		$this->db->replace_into('red_ga_domains', $input_arr);
		echo $strDomains = $this->get_domains();
	}
	function remove_domain(){
		$url = $this->input->post('strDomain');
		$host = trim(str_replace('<a class="boxclose"><i class="icon-remove"></i></a>','',$url));


		$mid = $this->session->userdata('member_id');


		$input_arr = array('member_id'=>$mid,'domain_name'=>$host);
		$this->db->delete('red_ga_domains', $input_arr);
		echo $strDomains = $this->get_domains();
	}
	
	
	function create_lang(){
		$this->load->library('Languagetranslator'); # load language translate library
		$arrLang = array('ar', 'az', 'be', 'bg', 'ca', 'cs', 'cy', 'da', 'de', 'el', 'es', 'et', 'eu', 'fa', 'fi', 'fr', 'ga', 'gl', 'hi', 'hr', 'hu', 'hy', 'id', 'is', 'it', 'iw', 'ja', 'ka', 'ko', 'la', 'lt', 'lv', 'mk', 'mt', 'nl', 'no', 'pl', 'pt', 'ro', 'ru', 'sk', 'sl', 'sq', 'sr', 'sv', 'sw', 'th', 'tl', 'tr', 'uk', 'ur', 'vi', 'yi', 'zh-CN', 'zh-TW'); 

/*
$lang['form_submit_email_subject']	 	= "Please Confirm Your Subscription"; 
$lang['required']	 					= "Required"; 
$lang['required_email']	 				= "Require a valid email";
*/
$lang['by'] 		= "by";

$lang['disclaimer'] 		= "You are receiving this email because you signed up on our website or made a purchase from us. This is a commercial email and may contain a solicitation or advertisement.";


		foreach($arrLang as $l){
			foreach($lang as $k=>$v){
				//$trans_text = Google_Translate_API::translate($v, 'en', $l);
				//$trans_text = $translator->translate($v, "en", $l);
				$trans_text = $this->languagetranslator->translate($v, $l, 'en');
				if ($trans_text !== false){
					echo '$lang["'.$k.'"] = "'.$trans_text .'" ;'. PHP_EOL;
				} 
			}
			echo PHP_EOL . PHP_EOL . PHP_EOL ;
			echo "/* End of file {$l}_lang.php */". PHP_EOL;
			echo "/* Location: ./webapp/language/signup/{$l}_lang.php */" . PHP_EOL;
			echo PHP_EOL . PHP_EOL . PHP_EOL ;
		}
	}

}
?>
