<?php
/************About Us class********************/
class lshare extends CI_Controller
{	
	function __construct(){
        parent::__construct();	
		$this->load->helper('cookie');
		$this->load->library('encrypt');
		$this->load->helper('transactional_notification');			
	}
	
	/* function index($mid){
		$this->load->model('lshare_model');		 
	print_r( $this->lshare_model->isMemberQualifiedLead($mid));
	}  */
	//function index($siteID, $url_id){
	function index($siteID='', $url_id=0){
		header ("Location: " . urldecode($this->getURL(5)));
		
		exit;	
		
		// Following code is discarded as WE have stopped using LinkShare for tracking affiliates 
		
		// Set time user entered in $time_entered variable in GMT, 24 format. (Format: yyyymmdd_hhmm)
		$time_entered = gmdate('Y-m-d\TH:i:s\Z', gmmktime());	
		
		
		// Removed on 19 February, 2016 by @pravinjha		
		//$cookie = array('name'=>'ls_added_on','value'=>$this->encrypt->encode($time_entered),'expire' => time() + 60*60*24*365*2,'prefix' => 'rc_','secure' => false);
		//set_cookie($cookie);
		
		//$cookie_site_id = array('name'=>'ls_site_id', 'value'=>$this->encrypt->encode($siteID), 'expire'=>time() + 60*60*24*365*2, 'prefix'=>'rc_', 'secure'=>false);
		//set_cookie($cookie_site_id);
		// Removed on 19 February, 2016 by @pravinjha
		
		// send email using transactional_notification_helper						
		//$message = "A user got redirected to redcappi.com from linkshare publisher site.";
		//send_tmail_plain_text('rcalerts11@gmail.com',SYSTEM_EMAIL_FROM, 'RedCappi', 'RedCappi Affliate Works',$message);
		 
						
		// Redirect to URL.
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
/* End of file */
?>
