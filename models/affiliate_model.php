<?php
class Affiliate_Model extends CI_Model
{	
	//Constructor class with parent constructor
	function Affiliate_Model()
	{
		parent::__construct();		
	}
	function getAffiliateStatus($mid){
		$retVal = false;
	 
		$sqluser = "select ls_site_id from `red_members`  where `member_id` = '$mid'";	
		$rsAffiliate = $this->db->query($sqluser);
		if($rsAffiliate->num_rows() > 0){
			$retVal = (is_null($rsAffiliate->row()->ls_site_id) or trim($rsAffiliate->row()->ls_site_id)==''  )? false: true;
		}
		
		
		return $retVal ;
	}	

}
?>