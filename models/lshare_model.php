<?php
class Lshare_Model extends CI_Model
{	
	//Constructor class with parent constructor
	function Lshare_Model()
	{
		parent::__construct();		 
	}
	function showSASTrackingCodeLead($memid){
		$retval = '';
		// Removed on 19 February, 2016 by @pravinjha	
		/*
		//$sqlCheckPublisher = "select affiliate_tracking_status, created_on, DATE_FORMAT(`last_login_time`,'%Y-%m-%d %H:%i:%s')as last_login_tm from `red_members` where `member_id`='$memid'";
		$sqlCheckPublisher = "select affiliate_tracking_status, DATE_FORMAT(`created_on`,'%Y-%m-%d')as created_on, DATE_FORMAT(`last_login_time`,'%Y-%m-%d')as last_login_tm from `red_members` where `member_id`='$memid'";
		$rsCheckPublisher = $this->db->query($sqlCheckPublisher);	
		$arrCheckPublisher = $rsCheckPublisher->result_array();
		 
		if(($arrCheckPublisher[0]['last_login_tm'] == $arrCheckPublisher[0]['created_on'])and ($arrCheckPublisher[0]['affiliate_tracking_status'] == 0)){
			$retval =  "<img src='https://shareasale.com/sale.cfm?amount=1.00&tracking=".$memid."_l&transtype=lead&merchantID=48250' width='1' height='1'>";	
			$this->db->query("update `red_members` set `affiliate_tracking_status`=1 where `member_id`='$memid' ");			
		}	
		$rsCheckPublisher->free_result();
		
		*/
		return $retval ;
	}
	function showSASTrackingCodeSale($memid){
		// Removed on 19 February, 2016 by @pravinjha	
		
		/*
	
		$sqlCheckPublisher = "select `ls_site_id`, `affiliate_tracking_status` from `red_members` where `member_id`='$memid'";
		$rsCheckPublisher = $this->db->query($sqlCheckPublisher);	
		$arrCheckPublisher = $rsCheckPublisher->result_array();
		if(($this->totalPaymentCount($memid) == 1)and ($arrCheckPublisher[0]['affiliate_tracking_status'] == 1) and (!is_null($arrCheckPublisher[0]['ls_site_id'])) ){
			//echo "<img src='https://shareasale.com/sale.cfm?amount=50.00&tracking=".$memid."_s&transtype=sale&merchantID=48250' width='1' height='1'>";	
			$affiliate_id = $arrCheckPublisher[0]['ls_site_id'];
			$ch = curl_init();

			// Set query data here with the URL
			//curl_setopt($ch, CURLOPT_URL, 'https://shareasale.com/sale.cfm?amount=10.00&tracking=".$memid."_s&transtype=sale&merchantID=48250'); 
			curl_setopt($ch, CURLOPT_URL, "https://shareasale.com/q.cfm?amount=10.00&tracking=".$memid."_s&transtype=sale&merchantID=48250&userID=$affiliate_id"); 

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, '3');
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			$content = trim(curl_exec($ch));
			curl_close($ch);
			//print $content;
			$this->db->query("update `red_members` set `affiliate_tracking_status`=2 where `member_id`='$memid' ");
		
		}
		*/
	}
	
	// Code below this was used for LinkShare Tracking. Now not used.
	function mopDateFormat($dt){
		return str_replace(array('-',':','T','Z'),array('','','_',''),$dt);

	}	

	# This Function will be called on Dashboard to provide the tracking/report of affiliates
	function showMediaPixle($memid){
		 
			/**
 			 Check for this member, whether affiliate detail is already set or not. 
			 Also check if this member is added after the Affiliate detail's cookie added_on date
			 So, any old user should not be marked as successful user for affiliate. 
			*/
			// Removed on 19 February, 2016 by @pravinjha	
			/*
			$sqlCheckPublisher = "select `ls_site_id`,`ls_added_on`,created_on, DATE_FORMAT(`last_login_time`,'%Y-%m-%d %H:%i:%s')as last_login_tm from `red_members` where `member_id`='$memid'";
			$rsCheckPublisher = $this->db->query($sqlCheckPublisher);	
			$arrCheckPublisher = $rsCheckPublisher->result_array();
			 
			if(!is_null($arrCheckPublisher[0]['ls_site_id']) and $arrCheckPublisher[0]['ls_site_id']!='' and $arrCheckPublisher[0]['last_login_tm'] == $arrCheckPublisher[0]['created_on']){			
				// If user is not already marked as Driven from affiliate and user's date added is after the cookoie was added then, update member DB table with affiliate detail and show Media-Pixle for affiliate tracking in LS.
				$mop_time_entered = $this->mopDateFormat($arrCheckPublisher[0]['ls_added_on']);
				echo "<img src='http://track.linksynergy.com/eventnvppixel?mid=38024&ord=$memid&tr=$siteid&land=$mop_time_entered&skulist=Free1&qlist=1&amtlist=0&cur=USD&namelist=Free%20Plan' width='1' height='1' border='0' />";			
			}
		*/
	
	}
	# This Function will be called on Dashboard, when conditions like a/c confirmation and campaign-sent is achieved
	function postForQualifiedLead($memid){
		// Removed on 19 February, 2016 by @pravinjha	
		/*
		$member_id = (intval($memid) > 0)?intval($memid):0;	
		$product_sku	= 'Free1';
		$product_amount	= 0;
		$product_name	= 'Free Plan';
		// and red_affiliate table has no record as Free-Plan for this user 		
		if(!$this->isAffiliatePaid($member_id, 0) and $this->isMemberQualifiedLead($member_id)){
			
			$this->postToLinkShare($member_id, $product_sku,$product_amount,$product_name, 0 );
			
		}
		*/						
	}
	
	# This Function will be called on Dashboard, when "payment" is done.
	function postForPaidUsers($memid){
		// Removed on 19 February, 2016 by @pravinjha	
		/*
		$member_id = (intval($memid) > 0)?intval($memid):0;	
		
		$arrPackageDetail = $this->getMemberPackageDetail($member_id);
		
		$product_amount	= $arrPackageDetail['package_price'];
		
		if($product_amount > 0){
			$product_name	= $arrPackageDetail['package_title'];
			if(!$this->isAffiliatePaid($member_id, 1)  and $this->totalPaymentCount($member_id) > 0){		
				$product_sku	= 'setup'.$arrPackageDetail['package_id'];
				$this->postToLinkShare($member_id, $product_sku,$product_amount,$product_name, 1 );			
			}else{
				$product_sku	= 'subscription'.$arrPackageDetail['package_id'].date('Ym');				
				
				if($this->isMemberPaidNextSubscription($member_id))				
				$this->postToLinkShare($member_id, $product_sku,$product_amount,$product_name, 2 );			
			}				
		}
		*/			
	}
	
	function postToLinkShare($member_id, $product_sku, $product_amount, $product_name, $commission_type){
		$member_id = (intval($member_id) > 0)?intval($member_id):0;
		/**
		 if member table has Affiliate Details 		
		 Then Post to LisnkShare 
		*/
		// Removed on 19 February, 2016 by @pravinjha	
		/*
		$sqlCheckPublisher = "select `ls_site_id`,`ls_added_on`,`created_on` from `red_members` where `member_id`='$member_id'";
		$rsCheckPublisher = $this->db->query($sqlCheckPublisher);	
		$arrCheckPublisher = $rsCheckPublisher->result_array();
		$site_id = $arrCheckPublisher[0]['ls_site_id'];
		$added_on = $arrCheckPublisher[0]['ls_added_on'];
		if( !is_null($site_id) and $site_id!=''){
		
		
			$msg_raw = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<message xmlns=\"http://www.linkshare.com/namespaces/realtime-transactions-1.0\">
			  <sku_order>
				<orderid>" . $member_id . "</orderid>
				<siteid>" . $site_id . "</siteid>
				<time_entered>" . $added_on . "</time_entered>
				<currency>USD</currency>
				<trans_date>" . gmdate('Y-m-d\TH:i:s\Z', gmmktime()) . "</trans_date>
					<item>
					  <sku>" . $product_sku . "</sku>
					  <quantity>1</quantity>
					  <amount>" . ($product_amount * 100) . "</amount>
					  <product_name>" . $product_name . "</product_name>
					</item> 
			  </sku_order>
			</message>";

			 
		
			// Apply md5 hash to $msg_raw above to get md5_raw (binary data).
			//$md5_raw = mhash(MHASH_MD5, $msg_raw, $key); // mHash exntension must be installed.
			$md5_raw = hash_hmac("md5", $msg_raw, "5iESy3T7", true); // Supported by version PHP 5.1.2 or above.
		
			// Base 64 encode msg_raw/md5_raw created above.
			$msg= base64_encode($msg_raw);
			$md5= base64_encode($md5_raw);
			$msg = urlencode($msg);
			$md5 = urlencode($md5);
		
			// Generate name value pairs for URL. $data will become the URL for the Web Services call.
			$data = "http://track.linksynergy.com/xml?mid=38024&msg=" . $msg . "&md5=" . $md5 . "&xml=1";
		
			// Grab the response (if xml=1). The variable names are as follows:
			////////////////////////////////////////////////////////////////////////////
			// uIdConf = unique ID
			// goodTrans = number of transactions sent over successfully.
			// badTrans = number of transactions sent over unsuccessfully.
			////////////////////////////////////////////////////////////////////////////
		
			if(($responseXML =@file_get_contents($data))!== false){			
				$x = xml_parser_create();
				xml_parse_into_struct($x,$responseXML,$response,$i);
				xml_parser_free($x);
				$uIdConf = $response[1][value];
				If ($uIdConf == "Access denied") {
					$goodTrans = 0;
					//$badTrans = 0;
				} else {
					$goodTrans = $response[5][value];
					$badTrans = $response[7][value];
				}
				if($badTrans == 0){
				$sqlAddPaidCommission = "insert into `red_affiliate`(`member_id`,`product_sku`,`product_amount`,`ls_unique_id`,`ls_request`,`ls_response`,`commission_type`) 
				values('$member_id','$product_sku','$product_amount','$uIdConf','$msg_raw','$responseXML','$commission_type')";
				
				$this->db->query($sqlAddPaidCommission);
				
				$mop_time_entered = $this->mopDateFormat($added_on);
				echo "<img src='http://track.linksynergy.com/eventnvppixel?mid=38024&ord=$member_id&tr=$site_id&land=$mop_time_entered&skulist=$product_sku&qlist=1&amtlist=$product_amount&cur=USD&namelist=$product_name' width='1' height='1' border='0' />";			
				}
			}
			 
			// Test value 
			//	echo "<b>URL Generated:</b> " . $data . "<br />";
			//	echo "<b>Decoded &msg:</b> " . $msg_raw . "<br />";
			//	echo "<b>Unique ID:</b> " . $uIdConf . "<br />";
			//	echo "<b>Number of good transactions:</b> " . $goodTrans . "<br />";
			//	echo "<b>Number of bad transactions:</b> " . $badTrans . "<br />";
			 
		}
		*/
	}
	function getMemberPackageDetail($mid){
		
		$sqlGetPackageID = "select `package_id` from `red_member_packages` where `member_id`='$mid' ";
		$rsGetPackageID	 = $this->db->query($sqlGetPackageID);	
		$arrPackageID = $rsGetPackageID->result_array();
		$intPackageID = (count($arrPackageID) > 0)?$arrPackageID[0]['package_id']:0;
		if($intPackageID > 0){
		$sqlPackageDetail = "select `package_id`,`package_price`,`package_title` from `red_packages` where `package_id`='$intPackageID' ";
		$rsPackageDetails = $this->db->query($sqlPackageDetail);	
		$arrPackageDetail = $rsPackageDetails->result_array();
		}else{
			$arrPackageDetail =  array(array('package_id'=>0,'package_price'=>0.00,'package_title'=>''));		 
		}	
		return $arrPackageDetail[0];		 
	}
	
	function isAffiliatePaid($memid, $commission_type ){
		$sqlIsAffiliatePaid = "select `affiliate_id` from `red_affiliate` where `member_id`='$memid' and `commission_type`='$commission_type'";
		$rsIsAffiliatePaid = $this->db->query($sqlIsAffiliatePaid);	
		if($rsIsAffiliatePaid->num_rows() <= 0) {
			return false;
		}else{
			return true;
		}
	}
	
	function totalPaymentCount($member_id){
		$sqlTotalPaidTransaction = "select count(`transaction_id`) as totTransactions from `red_member_transactions` where `user_id`='$member_id' and `gateway`='AUTHORIZE' and `status`='SUCCESS'";
		$rsTotalPaidTransaction = $this->db->query($sqlTotalPaidTransaction);
		$rec = $rsTotalPaidTransaction->row();
		return $rec->totTransactions;	
	}
	
	
	function isMemberPaidNextSubscription($member_id){
		$arrPackagePrice = array();
		$totalTransaction =0;
		$sqlgetPackagePrices = "select `package_price` from `red_packages` where `package_status`='1' and `package_deleted`='0' and `package_price` > 0 ";
		$rsGetPackagePrices = $this->db->query($sqlgetPackagePrices);
		foreach ($rsGetPackagePrices->result_array() as $rowPackageAmount){
		   $arrPackagePrice[] = $rowPackageAmount['package_price'];
		}
		
		
		$sqlTotalPaidTransaction = "select `amount_paid` from `red_member_transactions` where `user_id`='$member_id' and `gateway`='AUTHORIZE' and `status`='SUCCESS'";
		$rsTotalPaidTransaction = $this->db->query($sqlTotalPaidTransaction);
		foreach ($rsTotalPaidTransaction->result_array() as $rowAmount){
		   if(in_array($rowAmount['amount_paid'],$arrPackagePrice))
		   $totalTransaction++;		   
		}	
		
		
		$sqlTotalPaidCommission = "select count(`affiliate_id`) as totPaidCommission from `red_affiliate` where `member_id`='$member_id' and 
		`commission_type`='2'";
		$rsTotalPaidCommission = $this->db->query($sqlTotalPaidCommission);
		$recCommission = $rsTotalPaidCommission->row();
		$totalCommission = $recCommission->totPaidCommission;	
		
		if($totalTransaction > ($totalCommission + 1))
		return true;
		else
		return false;
		
	}
	
	function isAlreadyPaidThisMonth($member_id){
		$sqlLastCommissionPaidDate = "select `posted_on` from `red_affiliate` where `member_id`='$member_id' and `commission_type`='2' order by `affiliate_id` desc limit 0,1";
		$rsLastCommissionDt = $this->db->query($sqlLastCommissionPaidDate);
		if($rsIsAffiliatePaid->num_rows() <= 0) {
			# commission-type=2 not paid yet
			return false;
			exit;
		}else{
			$arrPackageDetail = $rsPackageDetails->result_array();
			if((time() - strtotime($arrPackageDetail[0]['posted_on']) ) > 30 ){
				return false;
				exit;
			}else{
				return true;
				exit;
			}
		}
	}	
	
	function isMemberQualifiedLead($memid){
		$retVal = false;
		$sqlGetActiveMember = "select `member_id` from `red_members` where `member_id`='$memid' and `status`='active'";
		$rsGetActiveMember = $this->db->query($sqlGetActiveMember);
		if($rsGetActiveMember->num_rows() > 0) {
			$sqlIsQualifiedLead = "select `member_id` from `red_member_packages` where `member_id`='$memid' and `is_first_campaign_send`='1'";
			$rsIsQualifiedLead = $this->db->query($sqlIsQualifiedLead);
			if($rsIsQualifiedLead->num_rows() > 0) {
				$retVal = true;
			}
		}
		return $retVal;		
	}
}
?>