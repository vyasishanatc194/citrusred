<?php
class Home extends CI_Controller
{	
	
	function home(){
		parent::__construct();
		
		parse_str($_SERVER['QUERY_STRING'],$_GET);
		if($this->is_authorized->check_user()){
			redirect('newsletter/campaign');
			exit;
		}
		$this->load->helper('cookie');
		$this->load->library('encrypt');
		force_ssl();	
	}
	function index(){
                                    //echo("here");
                                    $this->load->helper('url');
                                    $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                                    if (strpos($url,$this->config->item('redirect_url')) > 0)
                                    {
                                        //echo("here");
                                        redirect('https://www.getredcappi.com');
                                    }
                                    
                                    
		// Start: Following code is to set cookie for users came via google adword
		$current_url_like= $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'];		
		parse_str(array_pop(explode('?',$_SERVER['REQUEST_URI'],2)),$_GET);
		//print_r($_GET);
		$thisIP = $this->is_authorized->getRealIpAddr(); 
		$utm_source = $_GET['utm_source'];
		$utm_medium = $_GET['utm_medium'];
		$utm_campaign = $_GET['utm_campaign'];
		// Set time user entered in $time_entered variable in GMT, 24 format. (Format: yyyymmdd_hhmm)
		$time_entered = gmdate('Y-m-d H:i:s', gmmktime());	
		$visitorDetail = $time_entered . ':-:' . ip2long($thisIP). ':-:' .$utm_source. ':-:' .$utm_medium. ':-:' .$utm_campaign ;
		$cookie = array('name'=>'rctrack','value'=>$this->encrypt->encode($visitorDetail),'expire' => 60*60*24*365*2,'prefix' => 'rc_','secure' => false);		
		//echo $this->encrypt->decode($this->encrypt->encode($visitorDetail));
		set_cookie($cookie);
		// End
		
		// Load the seo model which interact with database
		$this->load->model('SeoModel');
		$seo_array=$this->SeoModel->get_seo_data(array('is_delete'=>0));
		$wufoo_url = 'qm29y9s0dgz7iq';
		//Load the header, home page and footer view of index page		
		$this->load->view('header_outer',array('seo_array'=>$seo_array,'title'=>'Home Page', 'logoclass' => 'class="home"', 'wufoo_url'=>$wufoo_url));
		$this->load->view('index');
		$this->load->view('footer_outer');
	}
	// google-adword: gadw, capterra: capterra, facebook:fb
	function st($siteID='', $url_id=0){
		if($siteID != ''){
			$rsReferral = $this->db->query("select id, referrer_name from red_member_referrer where referrer_string='$siteID'");
			//$arrSite = array('gadw'=>'google_adword','capterra'=>'capterra','fb'=>'facebook','getapp'=>'GetApp');
			//$arrSiteId = array('gadw'=>1,'capterra'=>2,'fb'=>3,'getapp'=>4);
			//$thisSite = $arrSite[$siteID];
			//$thisSiteId = $arrSiteId[$siteID];
			$thisSite = $rsReferral->row()->referrer_name;
			$thisSiteId = $rsReferral->row()->id;
			
			$thisIP = ip2long($this->is_authorized->getRealIpAddr());
			$thisReferer = $_SERVER['HTTP_REFERER'];
			// Set time user entered in $time_entered variable in GMT, 24 format. (Format: yyyymmdd_hhmm)
			$time_entered = gmdate('Y-m-d\TH:i:s\Z', gmmktime());	
			$visitorDetail = $time_entered . ':-:' . $thisIP. ':-:' .$thisSite ;		
			$this->db->query("insert into red_member_referral set ip_logged = '$thisIP', referer_logged='$thisSiteId', referer_url='$thisReferer', vistor_detail='$visitorDetail' ON DUPLICATE KEY UPDATE referer_url='$thisReferer', vistor_detail='$visitorDetail'");	
			 	
			set_cookie(array('name'=>'rctrack','value'=>$this->encrypt->encode($visitorDetail),'expire' => '63072000', 'path'   => '/', 'prefix' => 'prc_','secure' => true));
		}
		header ("Location: " . urldecode($this->getURL($url_id)));
		
		exit;
	}
	 
	
	function getURL($uid){
		switch ($uid) {
			case 1:
				return urlencode(base_url().'email-marketing-features');
				break;
			case 2:
				return urlencode(base_url().'pricing');
				break;
			case 3:
				return urlencode(base_url().'signup');
				break;
			case 4:
				return urlencode(base_url().'contact');
				break;
			default:
			   return urlencode(base_url());
		}
	}	
}
?>