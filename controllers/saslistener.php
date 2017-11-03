<?php
/************About Us class********************/
class saslistener extends CI_Controller
{	
	function __construct(){
        parent::__construct();			
	}	
	 
	function index(){
		$affiliateID 		= $_POST['userID'];
		$memberIDasLeadNo 	= $_POST['tracking'];
		$transactionDate	= $_POST['transdate'];
		$affiliateCommission = $_POST['commission'];
		$sasTransID 		= $_POST['transID'];
		 
		
		$postdata = file_get_contents("php://input");
		@mail('pravinjha@gmail.com','RC Listener',$postdata);
		
		if($affiliateID != '' and intval($memberIDasLeadNo) > 0){
			$member_id = intval($memberIDasLeadNo);
			$product_sku = ('s' == substr($memberIDasLeadNo,-1))?'sas_sale':'sas_lead';
			$commission_type = ('s' == substr($memberIDasLeadNo,-1))?1:0;
			
			if(is_null($this->db->query("select `ls_site_id` from `red_members` where `member_id`='$member_id' ")->row()->ls_site_id)){				
				$this->db->query("update `red_members` set `ls_site_id` ='$affiliateID', `ls_added_on` ='$transactionDate' where `member_id`='$member_id' ");
			}
			
			$rsSASTransaction = $this->db->query("select `affiliate_id` from `red_affiliate` where `member_id`='$member_id' and `product_sku` = '$product_sku'");	
			if($rsSASTransaction->num_rows() <= 0){
				$sqlTransactionDetail = "insert into `red_affiliate`(`member_id`,`product_sku`,`product_amount`,`ls_unique_id`,`ls_request`,`ls_response`,`commission_type`) 
						values('$member_id','$product_sku','$affiliateCommission','$sasTransID','','$postdata','$commission_type')";
				$this->db->query($sqlTransactionDetail);		
			}			
						
		}		
	}
}
/* End of file used as listner url for ShareASale*/