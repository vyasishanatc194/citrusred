<?php
/************About Us class********************/
class Spamcheck extends CI_Controller
{
	function __construct(){
        parent::__construct();	
        $this->load->model('newsletter/Campaign_Model');
		//remove_ssl();		
	}
	function index($cid=0){
		if($cid == 0){return '';exit;}
		
		$arrCampaign = $this->Campaign_Model->get_campaign_data(array('campaign_id'=>$cid));
		$emlHTML = $arrCampaign[0]['campaign_content'];
		$emlText = $arrCampaign[0]['campaign_text_content'];
		$emlSubject = $arrCampaign[0]['email_subject'];
		
		$eml = $this->getheaders($emlSubject, $emlText) . $emlHTML;
		 
		$arrSpam = $this->filter($eml,"long");
		
		$sreport = (isset($arrSpam['report']))?$arrSpam['report'] : '';
		$sscore =  (isset($arrSpam['score']))?$arrSpam['score'] : 0;  
		$this->db->query("update red_email_campaigns set spamscore='$sscore',spamreport='$sreport' where campaign_id=$cid");
		foreach ($arrSpam as $k=>$v){
			echo $k.'===='.nl2br($v)."<br/>"; // etc.
		}		
	}
	function getheaders($subject,$textBody){
		return "Delivered-To: pravinjha@gmail.com
Date: Sun, 24 Jan 2016 22:46:58 +0000
To: redsoftsolutions@yahoo.in
From: RedSoft Solutions <pravinjha@outlook.com>
Subject: {$subject}
MIME-Version: 1.0
Content-Type: multipart/alternative;
	boundary='b1_7099735c9469f56081952e912cbc68a5'
Message-ID: <0.0.11.3AD.1D156F922C680B4.0@rc74.rcmailsv.com>

--b1_7099735c9469f56081952e912cbc68a5
Content-Type: text/plain; charset=utf-8
Content-Transfer-Encoding: 8bit

{$textBody}


--b1_7099735c9469f56081952e912cbc68a5
Content-Type: text/html; charset=utf-8
Content-Transfer-Encoding: 8bit";
	}
 	function filter($email, $options){
		if (empty($email) || empty($options)){
			return false;
		}

        if (!function_exists('curl_init')){
            return false;
        }
		$headers = array('Accept: application/json', 'Content-Type: application/json' );

		$encoded_data = json_encode(array('email'=>$email, 'options'=>$options));

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://spamcheck.postmarkapp.com/filter');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded_data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$return = curl_exec($ch);

		if (curl_error($ch) != '') {
			echo curl_error($ch);
			return false;
		}

		return json_decode($return, 1);
	}

	 
}
/* End of file */
?>