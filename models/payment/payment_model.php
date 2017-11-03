<?php
class Payment_model extends CI_Model
{	
	//Constructor class with parent constructor
	function payment_model()
	{
		parent::__construct();		
	}
	/**
	This function is used in the ajax/fetchPayableAmount/ function called 
	from view/user/upgrade_package_cim.php to display the coupon status	 
	and appropriate message for first time users.
	*/	 
	function getCouponDetail($ccode){
		$retVal = 'err';
		$sqlCouponDetail = "Select * from `red_coupons` where `coupon_code`='$ccode' and status='1' and valid_untill > now() limit 1";
		
		$rsCoupon = $this->db->query($sqlCouponDetail);
		if($rsCoupon->num_rows() > 0){			
			$arrCoupon = $rsCoupon->result_array();
			$maxnumberOfMembers = $arrCoupon[0]['max_number_of_members'];
			$totalUsedYet = $this->countCouponUsed($ccode);
			if($totalUsedYet < $maxnumberOfMembers){
				$discountValue = ($arrCoupon[0]['coupon_type']== 0)?'$'.$arrCoupon[0]['coupon_value']: $arrCoupon[0]['coupon_value'].'%';
				$retVal = 'Discount applicable is '.$discountValue.' of the first '.$arrCoupon[0]['usable_number_of_times'].' month(s) of your payment cycle.';
			}
		} 
		return $retVal;
	}
	function countCouponUsed($ccode){
		$rsIsUsed = $this->db->query("select count(member_id) as used from `red_member_packages` where `coupon_code_used` = '$ccode'");
		return $rsIsUsed->row()->used;		
	}

	
	    /*Start code by citrusbug*/
        /* get couponDetails*/
        function getCouponDetails($payableAmount, $ccode=''){
		$jsonArray = array('status' => 'failed');
		
		$sqlCouponDetail = "Select * from `red_coupons` where `coupon_code`='$ccode' and status='1' limit 1";
		$retVal = $payableAmount;
		$rsCoupon = $this->db->query($sqlCouponDetail);
                
		if($rsCoupon->num_rows() > 0){			
			$arrCoupon = $rsCoupon->result_array();
			 
			$maxnumberOfMembers = $arrCoupon[0]['max_number_of_members'];
			$totalUsedYet = $this->countCouponUsed($ccode); 
			if($totalUsedYet > $maxnumberOfMembers){
				$retVal = $payableAmount;
                $status = 'failed';
			}elseif($arrCoupon[0]['coupon_type']== 0){
				$retVal = $payableAmount - $arrCoupon[0]['coupon_value'];
                $status = 'success';
			}else{
				$retVal = $payableAmount * (100 - $arrCoupon[0]['coupon_value'])/100;
                $status = 'success';
			}
$jsonArray = array('status' => 'failed');
			if($status == 'success'){
				$noOfUses = $arrCoupon[0]['usable_number_of_times'];
				if($noOfUses <=	24){
					$currentDate = date('Y-m-d');
					$Coupon_exp_date = date('Y-m-d',strtotime($arrCoupon[0]['valid_untill']));
				
					if(strtotime($currentDate) < strtotime($Coupon_exp_date)){
						$jsonArray = array('status' => 'success','noOfUses' => $noOfUses);
					}else{
						$jsonArray = array('status' => 'failed');
					}
				}else{
					$jsonArray = array('status' => 'success','noOfUses' => 'all');
				}
			}
			
		} 
		
		return $jsonArray;	
	}
	
        
        /*End code by citrusbug*/
		
	
	  /*Start Code by citrusbug*/
        function getPaypalDiscountedAmountForSubsequentPayments($mid, $payableAmount){
		$ci=&get_instance();		
		$ci->load->model("UserModel");
		$user_transactions=$ci->UserModel->get_user_transactions(array('user_id'=>$mid),0,0,TRUE);//get count of transactions
		
		$return_payable_amount = $payableAmount;
		$monthsCount = 1;
		// get first coupon used and payment start date
		$sqlCouponUsed = "select `coupon_code_used`,`start_payment_date` from `red_member_packages` where `member_id`='$mid'";
		
		$rsCouponUsed = $this->db->query($sqlCouponUsed);
		#echo "<pre>";print_r($rsCouponUsed->row());exit;
		if($rsCouponUsed->num_rows() > 0){	
		
			$ccode = $rsCouponUsed->row()->coupon_code_used;
			$paymentStartDt = $rsCouponUsed->row()->start_payment_date;
		}
		
		// get coupon usable for how many months.
		if(!is_null($ccode) and $ccode != ''){
			$sqlCouponUsableTimes = "Select `usable_number_of_times` from `red_coupons` where `coupon_code` = '$ccode'";
			$rsCouponUsableTimes = $this->db->query($sqlCouponUsableTimes);
			if($rsCouponUsableTimes->num_rows() > 0){	
				$monthsCount = $rsCouponUsableTimes->row()->usable_number_of_times;			
			}
		}
		
		$monthsCount = ($monthsCount > 99)?99:$monthsCount;
		
		// if today's date is within the discount date range
		if($monthsCount > 1){
			$todayDt = strtotime(date("Y-m-d"));
			$discountApplicableTillDate = strtotime(date("Y-m-d", strtotime($paymentStartDt)) . " +".$monthsCount." month");
		
			if($todayDt < $discountApplicableTillDate){	
			
				$return_payable_amount = $this->getDiscountedAmountAfterFirstPayment($payableAmount, $ccode);		
			}		
		}else{
			
			//if(count($user_transactions)<1){
				
				$return_payable_amount = $this->getDiscountedAmountForFirstPayment($payableAmount, $ccode);
			//}
		}
		
		$jsonArray = array();
		$return_payable_amount = ($return_payable_amount < 0)?0:$return_payable_amount;
		
		if($monthsCount <=	24){
		}else{
			$monthsCount = 'all';
		}
		
		$jsonArray = array('monthsCount' => $monthsCount,"return_payable_amount" => $return_payable_amount);
		#echo "<pre>";print_r($jsonArray);exit;
		return $jsonArray;	
	}
        
        /*End Code by citrusbug*/
        
	
	
	function getDiscountedAmountForFirstPayment($payableAmount, $ccode=''){
		$payableAmount = floatval(preg_replace('/[^\d.]/', '', $payableAmount));
		$sqlCouponDetail = "Select * from `red_coupons` where `coupon_code`='$ccode' and status='1' and valid_untill > now() limit 1";
		$retVal = $payableAmount;
		$rsCoupon = $this->db->query($sqlCouponDetail);
	
		if($rsCoupon->num_rows() > 0){
			
			$arrCoupon = $rsCoupon->result_array();
			$maxnumberOfMembers = $arrCoupon[0]['max_number_of_members'];
			$totalUsedYet = $this->countCouponUsed($ccode); 

			if($totalUsedYet > $maxnumberOfMembers){
				$retVal = $payableAmount;
			}elseif($arrCoupon[0]['coupon_type']== 0){				
				$retVal = $payableAmount - $arrCoupon[0]['coupon_value'];
			}else{				
				$coupn_val = (100 - $arrCoupon[0]['coupon_value']);
				$pay_amt = $payableAmount * $coupn_val;
				$retVal =($pay_amt/100);			
			}	
			
		}  else{
			
			$retVal = $payableAmount;
		} 
		
		return ($retVal < 0 )?0:$retVal;	
	}
	function getDiscountedAmountAfterFirstPayment($payableAmount, $ccode=''){
		$payableAmount = floatval(preg_replace('/[^\d.]/', '', $payableAmount));
		$sqlCouponDetail = "Select * from `red_coupons` where `coupon_code`='$ccode' and status='1' limit 1";
		$retVal = $payableAmount;
		$rsCoupon = $this->db->query($sqlCouponDetail);
		if($rsCoupon->num_rows() > 0){			
			$arrCoupon = $rsCoupon->result_array();
			$maxnumberOfMembers = $arrCoupon[0]['max_number_of_members'];
			$totalUsedYet = $this->countCouponUsed($ccode); 
			if($totalUsedYet > $maxnumberOfMembers){
				$retVal = $payableAmount;
			}elseif($arrCoupon[0]['coupon_type']== 0){
				$retVal = $payableAmount - $arrCoupon[0]['coupon_value'];
			}else{
				//$retVal = $payableAmount * (100 - $arrCoupon[0]['coupon_value'])/100;
				$coupn_val = (100 - $arrCoupon[0]['coupon_value']);
				$pay_amt = $payableAmount * $coupn_val;
				$retVal =($pay_amt/100);
			}			 
		} 
		else{
			$retVal = $payableAmount;
		}
		
		return ($retVal < 0 )?0:$retVal;	
	}
	function getDiscountedAmountForSubsequentPayments($mid, $payableAmount){
		#echo 'new';exit;
		$return_payable_amount = $payableAmount;
		$monthsCount = 1;
		// get first coupon used and payment start date
		$sqlCouponUsed = "select `coupon_code_used`,`start_payment_date` from `red_member_packages` where `member_id`='$mid'";
		
		$rsCouponUsed = $this->db->query($sqlCouponUsed);
		if($rsCouponUsed->num_rows() > 0){	
			$ccode = $rsCouponUsed->row()->coupon_code_used;
			$paymentStartDt = $rsCouponUsed->row()->start_payment_date;
		}
		// get coupon usable for how many months.
		if(!is_null($ccode) and $ccode != ''){
			$sqlCouponUsableTimes = "Select `usable_number_of_times` from `red_coupons` where `coupon_code` = '$ccode'";
			$rsCouponUsableTimes = $this->db->query($sqlCouponUsableTimes);
			if($rsCouponUsableTimes->num_rows() > 0){	
				$monthsCount = $rsCouponUsableTimes->row()->usable_number_of_times;			
			}
		}
		$monthsCount = ($monthsCount > 99)?99:$monthsCount;
		
		// if today's date is within the discount date range
		if($monthsCount > 1){
			$todayDt = strtotime(date("Y-m-d"));
			$discountApplicableTillDate = strtotime(date("Y-m-d", strtotime($paymentStartDt)) . " +".$monthsCount." month");
		
			if($todayDt < $discountApplicableTillDate){			
				$return_payable_amount = $this->getDiscountedAmountAfterFirstPayment($payableAmount, $ccode);		
			}		
		}
		
		return ($return_payable_amount < 0)?0:$return_payable_amount;	
	}
	function getDiscountedAmountForSubsequentPaymentsNew($mid, $payableAmount, $nextPayDt){
		 
		
		$monthsCount = 1;
		$discountPercentage = 1;
		// get first coupon used and payment start date
		$sqlCouponUsed = "select `coupon_code_used`,`coupon_attached_on` from `red_member_packages` where `member_id`='$mid'";
		
		$rsCouponUsed = $this->db->query($sqlCouponUsed);
		if($rsCouponUsed->num_rows() > 0){	
			$ccode = $rsCouponUsed->row()->coupon_code_used;
			$discountStartDt = $rsCouponUsed->row()->coupon_attached_on;
		}
		// get coupon usable for how many months.
		if(!is_null($ccode) and $ccode != ''){
			$sqlCouponUsableTimes = "Select `usable_number_of_times`, `coupon_value` from `red_coupons` where `coupon_code` = '$ccode'";
			$rsCouponUsableTimes = $this->db->query($sqlCouponUsableTimes);
			if($rsCouponUsableTimes->num_rows() > 0){	
				$monthsCount = $rsCouponUsableTimes->row()->usable_number_of_times;	
				$discountPercentage = 	($rsCouponUsableTimes->row()->coupon_value )/100;	
			}
			$rsCouponUsableTimes->free_result();
		}
		$monthsCount = ($monthsCount > 99)?99:$monthsCount;
		//echo "<br/>monthsCount=".$monthsCount ;		 
		// if today's date is within the discount date range
		if($monthsCount == 99){
			$return_payable_amount = $payableAmount - ($payableAmount * $discountPercentage) ;
			// echo "<br/>return_payable_amount=".$return_payable_amount ;
		}elseif($monthsCount > 1){
			$todayDt = strtotime(date("Y-m-d"));
			$discountApplicableTillDate = strtotime(date("Y-m-d", strtotime($discountStartDt)) . " +".$monthsCount." month");
			// echo "<br/>discountApplicableTillDate=".$discountApplicableTillDate;		
			if($todayDt < $discountApplicableTillDate){	// That means discount is applicable
				$daysLeftForDiscount = round(($discountApplicableTillDate - $todayDt) / 86400);
				// Consider discount coupon is valid on monthly basis only
				$daysForThisPayment = round((strtotime($nextPayDt) - $todayDt )/ 86400 );				
				$oneDayAmount = ($payableAmount / $daysForThisPayment);
				
				$amountForDiscountedDays = $daysLeftForDiscount * $oneDayAmount ;
				$applicableDiscount = $amountForDiscountedDays * $discountPercentage;				
				$return_payable_amount = $payableAmount - $applicableDiscount ;	
				
				// echo"<br/>payableAmount=".$payableAmount ;			
				// echo"<br/>daysLeftForDiscount=".$daysLeftForDiscount ;			
				// echo"<br/>daysForThisPayment=".$daysForThisPayment ;			
				// echo"<br/>oneDayAmount=".$oneDayAmount;
				
				// echo"<br/>amountForDiscountedDays=".$amountForDiscountedDays ;
				// echo"<br/>applicableDiscount=".$applicableDiscount;			
				
			}		
		}else{
			$return_payable_amount = $payableAmount;
		}
		
		return ($return_payable_amount < 0)?0:$return_payable_amount;	
	}
	
}
?>