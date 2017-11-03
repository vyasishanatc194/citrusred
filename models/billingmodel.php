<?php
class BillingModel extends CI_Model{
	function __construct(){
		parent::__construct();
		############################################
		# Collect login info to authorize payment  #
		############################################
		global $CI;
        $CI =& get_instance();
		  
		$this->load->library('Billingcim'); # load billing library
		$this->billingcim->loginKey($CI->config->item('loginname'), $CI->config->item('transactionkey'), $CI->config->item('test_mode'));		
		
	}
	/**
	*	This function is used to create a new customer profile along with any 
	*	customer payment profiles and customer shipping addresses for the customer profile.
	*
	*	@return 	integer	return customer profile id
	**/
	function createCustomerProfileRequest(){
		# load another model
        $ci=&get_instance();
		# Load the user model which interact with database
        $ci->load->model("UserModel");
		
		# Merchant-assigned reference ID for the request
		$this->billingcim->setParameter('refId', "ref_".$this->session->userdata('member_id')); // Up to 20 characters (optional)
		
		# merchantCustomerId must be unique across all profiles
		$this->billingcim->setParameter('merchantCustomerId', substr($this->session->userdata('member_username'),0,10)."_".$this->session->userdata('member_id'));
		$this->billingcim->setParameter('email', $this->session->userdata('member_email_address')); // A receipt from authorize.net will be sent to the email address defined here
		# Genrate customer profile id
		$this->billingcim->createCustomerProfileRequest();

		#################################################################################
		# If pofile creation is successfully then store customer profile id in database	#
		#################################################################################
		if ($this->billingcim->isSuccessful()){
			$customerProfileId=$this->billingcim->customerProfileId;
			
			$ci->UserModel->update_member_package(array('payment_type' => 0,'customer_profile_id'=>$customerProfileId),array('member_id'=>$this->session->userdata('member_id')));
			return $customerProfileId;
		}elseif($this->billingcim->code == 'E00039'){
			$customerProfileId = filter_var($this->billingcim->text, FILTER_SANITIZE_NUMBER_INT);
						
			$ci->UserModel->update_member_package(array('payment_type' => 0,'customer_profile_id'=>$customerProfileId),array('member_id'=>$this->session->userdata('member_id')));
			return $customerProfileId;
		}else{
			#################################################################################
			# If pofile creation is not successfully then store error info in varaiable		#
			#################################################################################
			$payment_err_msg=$this->billingcim->text;
			if(empty($this->billingcim->error_messages)){
				$payment_err_msg=$this->billingcim->text."<br/>";
			}else{
				foreach($this->billingcim->error_messages as $error){
					$error_arr=explode("setParameter(): ",$error);
					$payment_err_msg.=$error_arr[1]."<br/>";
				}
			}
			# if get error during payment
			$this->messages->add($payment_err_msg, 'error');
			return false;
		}
	}
	// Start: paypal update
        function createPaypalCustomerPaymentProfileRequest($customerProfileId=0){
			# load another model
			$ci=&get_instance();
			# Load the user model which interact with database
			$ci->load->model("UserModel");
                        
                        $customArray = explode("|",$_POST['custom']);
                        $member_id = $customArray[0];
                        $current_package_interval = $customArray[1];

                        
                      
                        
                        $current_date = date('Y-m-d h:i:s');
                        $start_payment_date = $current_date;
                        if ($current_package_interval == 'months'){
                            $next_payement_timestamp = strtotime(date("Y-m-d", strtotime($current_date)) . "+1 month");
                        }else{
                            $next_payement_timestamp = strtotime(date("Y-m-d", strtotime($current_date)) . "+1 year");
                        }
                        $start_payment_date = date('Y-m-d',strtotime($current_date));
                        $next_payement_date = date('Y-m-d',$next_payement_timestamp);
        
                        $update_array=array(
                                'payment_type' => 1,
                                'first_name'=>$this->input->post('first_name'),
                                'last_name'=>$this->input->post('last_name'),
                               # 'package_id' => $this->input->post('packageId'),
                                'address'=>$this->input->post('address1'),
                                'city'=>$this->input->post('city'),
                                'state'=>$this->input->post('state'),
                                'zip'=>$this->input->post('zipcode'),
                                'country'=>$this->input->post('country'),
                                'member_id'=>$this->session->userdata('member_id'),
                               ## 'customer_payment_profile_id'=>rand(1000,20000),
                               # 'start_payment_date' => $start_payment_date,
                               # 'next_payement_date' => $next_payement_date, 
                                'is_admin'=>0,
                                'is_payment'=>0,
                                'payment_paypal_status'=>0,
                             #    'coupon_code_used'=>$this->input->post('first_name'),
                        );
                        
                        
                        $ci->UserModel->update_member_package($update_array,array('member_id'=>$this->session->userdata('member_id')));                        
                        return $this->input->post('packageId');
                        
                        
	}
	
    // End: paypal update
	/**
	*	This function is used to create a new customer payment profile for an existing customer profile
	*
	*	@return 	integer	return customer payment profile id
	**/
	function createCustomerPaymentProfileRequest($customerProfileId=0){
		if($customerProfileId>0){
			# load another model
			$ci=&get_instance();
			# Load the user model which interact with database
			$ci->load->model("UserModel");
	
                        //'creditCard'
			# creditCard payment method - (aka creditcard) 
			$this->billingcim->setParameter('paymentType', 'creditCard');
			$this->billingcim->setParameter('cardNumber', $this->input->post('cc_number'));	# Credit card number
			$month=$this->input->post('ccexp_month');
			if($month<10){
				$month="0".$month;
			}
			$this->billingcim->setParameter('expirationDate', $this->input->post('ccexp_year').'-'.$month); # (YYYY-MM)			
			$this->billingcim->setParameter('billTo_firstName', $this->input->post('first_name')); # Up to 50 characters (no symbols)
			$this->billingcim->setParameter('billTo_lastName',  $this->input->post('last_name')); # Up to 50 characters (no symbols)
			$this->billingcim->setParameter('billTo_address', $this->input->post('address1')); # Up to 60 characters (no symbols)
			$this->billingcim->setParameter('billTo_city', $this->input->post('city')); # Up to 40 characters (no symbols)
			$this->billingcim->setParameter('billTo_state', $this->input->post('state')); # A valid two-character state code (US only) (optional)
			$this->billingcim->setParameter('billTo_zip', $this->input->post('zipcode')); # Up to 20 characters (no symbols)
			$this->billingcim->setParameter('billTo_country', $this->input->post('country')); # Up to 60 characters (no symbols) (optional)
			
			$this->billingcim->setParameter('email', $this->session->userdata('member_email_address')); // A receipt from authorize.net will be sent to the email address defined here
			
			$this->billingcim->setParameter('customerType', 'individual'); # individual or business (optional)
			
			# Payment gateway assigned ID associated with the customer profile
			$this->billingcim->setParameter('customerProfileId', $customerProfileId); # Numeric (required)	
			
			#  if liveMode, the billing address gets verified according to AVS settings on your Authorize.net account
			$this->billingcim->setParameter('validationMode', 'liveMode'); # required (none, testMode or liveMode)
			
			$this->billingcim->createCustomerPaymentProfileRequest();
			
			if($this->billingcim->isSuccessful()){
				// get credit card last four digit number
				$cc_number = $this->input->post('cc_number');
				$length = strlen($cc_number);
				$characters = 4;
				$start = $length - $characters;
				$cc_number = substr($cc_number , $start ,$characters);				
				$expr_date=$month."/".$this->input->post('ccexp_year');
				$start_payment_date = date("Y-m-d");		#Start Payment date
				/* $current_date=date("Y-m-d");		#Next month payment date
				$next_payement_timestamp=strtotime(date("Y-m-d", strtotime($current_date)) . "+1 month");
				$next_payement_date=date('Y-m-d', $next_payement_timestamp); */
				$update_array=array(
					'credit_card_last_digit'=>$cc_number,
					'expiration_date'=>$expr_date,
										'payment_type'=>0,
					'card_holder_name'=>$this->input->post('credit_card_holder_name'),
					'first_name'=>$this->input->post('first_name'),
					'last_name'=>$this->input->post('last_name'),
					'address'=>$this->input->post('address1'),
					'city'=>$this->input->post('city'),
					'state'=>$this->input->post('state'),
					'zip'=>$this->input->post('zipcode'),
					'country'=>$this->input->post('country'),
					'member_id'=>$this->session->userdata('member_id'),
					'customer_payment_profile_id'=>$this->billingcim->customerPaymentProfileId,
					/* 'start_payment_date'=>$start_payment_date,
					'next_payement_date'=>$start_payment_date, */
					'is_admin'=>0
				);
				// update package id in memeber package table
				$ci->UserModel->update_member_package($update_array,array('member_id'=>$this->session->userdata('member_id')));

				return $this->billingcim->customerPaymentProfileId;		
			}elseif($this->billingcim->code == 'E00039'){
				$this->billingcim->setParameter('customerProfileId', $customerProfileId); 
				$this->billingcim->getCustomerProfileRequest();
				if($this->billingcim->isSuccessful()){
						// get credit card last four digit number
						$cc_number = $this->input->post('cc_number');						
						$cc_number = substr($cc_number , -4);				
						$expr_date=$month."/".$this->input->post('ccexp_year');
						$start_payment_date = date("Y-m-d");								
						$update_array=array(
							'credit_card_last_digit'=>$cc_number,
							'expiration_date'=>$expr_date,
							'card_holder_name'=>$this->input->post('credit_card_holder_name'),
							'first_name'=>$this->input->post('first_name'),
							'last_name'=>$this->input->post('last_name'),
							'address'=>$this->input->post('address1'),
							'city'=>$this->input->post('city'),
							'state'=>$this->input->post('state'),
							'zip'=>$this->input->post('zipcode'),
							'country'=>$this->input->post('country'),
							'member_id'=>$this->session->userdata('member_id'),
							'customer_payment_profile_id'=>$this->billingcim->customerPaymentProfileId,							
							'is_admin'=>0
						);
						// update package id in memeber package table
						$ci->UserModel->update_member_package($update_array,array('member_id'=>$this->session->userdata('member_id')));

				return $this->billingcim->customerPaymentProfileId;	
				}	
			}else{
				$payment_err_msg=$this->billingcim->text;
				if(empty($this->billingcim->error_messages)){
					$payment_err_msg=$this->billingcim->text."<br/>";
				}else{
					foreach($this->billingcim->error_messages as $error){
						$error_arr=explode("setParameter(): ",$error);
						$payment_err_msg.=$error_arr[1]."<br/>";
					}
				}
                //                echo '<pre>';
                //                print_R($payment_err_msg);exit;
				# if get error during payment
				$this->messages->add($payment_err_msg, 'error');
				return false;			
			}
		}
	}
	/**
	*	Function updateCustomerPaymentProfileRequest 
	*	to update a customer payment profile for an existing customer profile.
	*
	*	@param integer $customerProfileId  customer profile id
	*	@param integer $customerPaymentProfileId  customer paymnet profile id
	**/
	function updateCustomerPaymentProfileRequest($customerProfileId=0,$customerPaymentProfileId=0){ 
		if($customerProfileId>0){
			# load another model
			$ci=&get_instance();
			# Load the user model which interact with database
			$ci->load->model("UserModel");
			# creditCard payment method - (aka creditcard) 
			$this->billingcim->setParameter('paymentType', 'creditCard');
			$this->billingcim->setParameter('cardNumber', $this->input->post('cc_number'));	# Credit card number			
			$month=$this->input->post('ccexp_month');
			if($month<10){
				$month="0".$month;
			} 
			$this->billingcim->setParameter('expirationDate', $this->input->post('ccexp_year').'-'.$month); # (YYYY-MM)
			$this->billingcim->setParameter('billTo_firstName', $this->input->post('first_name')); # Up to 50 characters (no symbols)
			$this->billingcim->setParameter('billTo_lastName',  $this->input->post('last_name')); # Up to 50 characters (no symbols)
			$this->billingcim->setParameter('billTo_address', $this->input->post('address1')); # Up to 60 characters (no symbols)
			$this->billingcim->setParameter('billTo_city', $this->input->post('city')); # Up to 40 characters (no symbols)
			$this->billingcim->setParameter('billTo_state', $this->input->post('state')); # A valid two-character state code (US only) (optional)
			$this->billingcim->setParameter('billTo_zip', $this->input->post('zipcode')); # Up to 20 characters (no symbols)
			$this->billingcim->setParameter('billTo_country', $this->input->post('country')); # Up to 60 characters (no symbols) (optional)
			$this->billingcim->setParameter('email', $this->session->userdata('member_id')); // A receipt from authorize.net will be sent to the email address defined here
			
			$this->billingcim->setParameter('customerType', 'individual'); # individual or business (optional)
			
			# Payment gateway assigned ID associated with the customer profile
			$this->billingcim->setParameter('customerProfileId', $customerProfileId); # Numeric (required)	
			
			# Payment gateway assigned ID associated with the customer payment profile
			$this->billingcim->setParameter('customerPaymentProfileId', $customerPaymentProfileId); // Numeric (required)
			
			#  if liveMode, the billing address gets verified according to AVS settings on your Authorize.net account
			$this->billingcim->setParameter('validationMode', 'liveMode'); # required (none, testMode or liveMode)

			$this->billingcim->updateCustomerPaymentProfileRequest();
			#####################################################################
			# If Payment profile created Successfully then store in database	#
			#####################################################################
			if ($this->billingcim->isSuccessful()){
				$today_date = date('Y-m-d');
				# get credit card last four digit number
				$cc_number = $this->input->post('cc_number');
				$length = strlen($cc_number);
				$characters = 4;
				$start = $length - $characters;
				$cc_number = substr($cc_number , $start ,$characters);				
				$expr_date=$month."/".$this->input->post('ccexp_year');
				$update_array=array(
					'credit_card_last_digit'=>$cc_number,
					'expiration_date'=>$expr_date,
					'card_holder_name'=>$this->input->post('credit_card_holder_name'),
					'first_name'=>$this->input->post('first_name'),
					'last_name'=>$this->input->post('last_name'),
					'address'=>$this->input->post('address1'),
					'city'=>$this->input->post('city'),
					'state'=>$this->input->post('state'),
					'zip'=>$this->input->post('zipcode'),
					'country'=>$this->input->post('country'),
					'coupon_code_used'=>$this->input->post('coupon_code'),
					'coupon_attached_on'=>$today_date
				);
				
				# update package id in memeber package table
				$ci->UserModel->update_member_package($update_array,array('member_id'=>$this->session->userdata('member_id')));
				return true;
			}else{
				#########################################################################
				# If Payment profile not created Successfully then add error in array	#
				#########################################################################
				$payment_err_msg=$this->billingcim->text;
				if(empty($this->billingcim->error_messages)){
					$payment_err_msg=$this->billingcim->text."<br/>";
				}else{
					foreach($this->billingcim->error_messages as $error){
						$error_arr=explode("setParameter(): ",$error);
						$payment_err_msg.=$error_arr[1]."<br/>";
					}
				}
				
				# if get error during payment
				$this->messages->add($payment_err_msg, 'error');
				return false;			
			}
		}
	}
	
        
    // Start: paypal update    
        /**
	*	This function is used to create a paypal payment transaction from an existing customer profile
	**/
	function createCustomerPaypalProfileTransactionRequest($amount,$package_id, $user_profile_arr, $first_payment = false,$insert_id= false){
		# load another model
		$ci=&get_instance();
		# Load the user model which interact with database
		$ci->load->model("UserModel");
		$amount=number_format($amount, 2, '.', '');
		/**
		below is temp
		*/
                /*
		if($amount > 5000){
                    # if get error during payment
                    $this->messages->add('We are unable to process your payment. Please try after some time.', 'error');
                    @mail('pravinjha@gmail.com','ERROR: over charge','RedCappi tried to charge amount:$'.$amount.' user-id is='.$user_profile_arr['member_id'].' package selected='.$package_id);
                    return false;
                    exit;
		}
                */
		/**
		Above is temp
		*/
                $customerProfileId = $user_profile_arr['customer_profile_id'];  
                $customerPaymentProfileId = $user_profile_arr['customer_payment_profile_id'];
		// Insert Payment Success transaction in database
                if($first_payment){
                   $payment_type = 1;
                }else{	
                   $payment_type = 2; // subsequent payments
                }
                $response = $user_profile_arr['response'];
                $status = $user_profile_arr['status'];
                $input_data = array ( 'user_id'=>$user_profile_arr['member_id'],
                    'package_id'=>$package_id,'gateway'=>'Paypal','gateway_response' => $response,
                    'amount_paid'=>$amount, 'status'=>$status,'payment_type'=>$payment_type);
                
                
               // $ci->UserModel->insert_payment_transactions($input_data);
                
				
				$last_id = '';
			
                if(array_key_exists('transaction_id', $user_profile_arr)  ){
                    if($user_profile_arr['transaction_id'] != ''){
						$ci->UserModel->update_payment_transactions($input_data,array("transaction_id" => $user_profile_arr['transaction_id']));
                    }else{
						$selected_package_array = $this->UserModel->get_packages_data(array('package_id' =>$package_id));
						$recuring = $selected_package_array[0]['package_recurring_interval'];
						if($recuring == 'credit'){
		                	$input_data['payment_from_table'] = 'red_package_credit';
		                	$input_data['payment_table_id'] = $insert_id;
		                }else{
		                	$input_data['payment_from_table'] = 'red_member_packages';
		                	$input_data['payment_table_id'] = $package_id;
		                }
						$last_id = $ci->UserModel->insert_payment_transactions($input_data);
                    }
                    
                }else{
					$selected_package_array = $this->UserModel->get_packages_data(array('package_id' =>$package_id));
					$recuring = $selected_package_array[0]['package_recurring_interval'];
					if($recuring == 'credit'){
						$input_data['payment_from_table'] = 'red_package_credit';
						$input_data['payment_table_id'] = $insert_id;
					}else{
						$input_data['payment_from_table'] = 'red_member_packages';
						$input_data['payment_table_id'] = $package_id;
					}
					$last_id = $ci->UserModel->insert_payment_transactions($input_data);
                }
                return $last_id;
                
	}
	
    // End: paypal update    
    
	/**
	*	This function is used to create a payment transaction from an existing customer profile
	**/
	function createCustomerProfileTransactionRequest($amount,$package_id, $user_profile_arr, $first_payment = false){
		# load another model
		$ci=&get_instance();
		# Load the user model which interact with database
		$ci->load->model("UserModel");
		$amount=number_format($amount, 2, '.', '');
		/**
		below is temp
		*/
		if($amount > 5000){
			# if get error during payment
			$this->messages->add('We are unable to process your payment. Please try after some time.', 'error');
			@mail('pravinjha@gmail.com','ERROR: over charge','RedCappi tried to charge amount:$'.$amount.' user-id is='.$user_profile_arr['member_id'].' package selected='.$package_id);
			return false;
			exit;
		}
		/**
		Above is temp
		*/
		
		# Total Amount: This amount should include all other amounts such as tax amount, shipping amount, etc.
		$this->billingcim->setParameter('transaction_amount', $amount); # Up to 4 digits with a decimal (required)		
		
		# transactionType = (profileTransCaptureOnly, profileTransAuthCapture or profileTransAuthOnly)
		$this->billingcim->setParameter('transactionType', 'profileTransAuthCapture');
		# Payment gateway assigned ID associated with the customer profile
		$this->billingcim->setParameter('customerProfileId', $user_profile_arr['customer_profile_id']); # Numeric (required)
		
		# Payment gateway assigned ID associated with the customer payment profile
		$this->billingcim->setParameter('customerPaymentProfileId', $user_profile_arr['customer_payment_profile_id']); # Numeric (required)	

		// The tax exempt status
		$this->billingcim->setParameter('transactionTaxExempt', 'false');
		
		// The recurring billing status
		$this->billingcim->setParameter('transactionRecurringBilling', 'false');
		
		$this->billingcim->createCustomerProfileTransactionRequest();
		if ($this->billingcim->isSuccessful()){
			$response="";
			$response.=$this->billingcim->response;
			$response.="|".$this->billingcim->directResponse;
			$response.="|".$this->billingcim->validationDirectResponse;
			$response.="|".$this->billingcim->resultCode;
			$response.="|".$this->billingcim->code;
			$response.="|".$this->billingcim->text;
			$response = preg_replace("/\xef\xbb\xbf/","",$response);	
			
			// Insert Payment Success transaction in database
			if($first_payment)
				$payment_type = 1;
			else	
				$payment_type = 2; // subsequent payments
			$userCredit['payment_process'] = '1';
			$credit_id = $ci->UserModel->creditUpdatePackageCredit($userCredit);
			$input_data = array ( 'user_id'=>$user_profile_arr['member_id'],'package_id'=>$package_id,'gateway'=>'AUTHORIZE','amount_paid'=>$amount, 'status'=>'SUCCESS','payment_type'=>$payment_type, 'gateway_response'=>$response);
			$selected_package_array = $this->UserModel->get_packages_data(array('package_id' =>$package_id));
			$recuring = $selected_package_array[0]['package_recurring_interval'];
			if($recuring == 'credit'){
            	$input_data['payment_from_table'] = 'red_package_credit';
            	$input_data['payment_table_id'] = $credit_id;
            }else{
            	$input_data['payment_from_table'] = 'red_member_packages';
            	$input_data['payment_table_id'] = $package_id;
            }
			$ci->UserModel->insert_payment_transactions($input_data);
			return true;
			#$this->upgradePackage($user_profile_arr,$package_id);
		}else{
			$response="";
			$response.=$this->billingcim->response;
			$response.="|".$this->billingcim->directResponse;
			$response.="|".$this->billingcim->validationDirectResponse;
			$response.="|".$this->billingcim->resultCode;
			$response.="|".$this->billingcim->code;
			$response.="|".$this->billingcim->text;
			$response = preg_replace("/\xef\xbb\xbf/","",$response);	
			#####################################################
			# Insert PAyment failure transaction in database	#	
			#####################################################
			$input_data = array ( 'user_id'=>$user_profile_arr['member_id'],'package_id'=>$user_profile_arr['selected_package_id'],'gateway'=>'AUTHORIZE','amount_paid'=>'0', 'status'=>'FAILURE','gateway_response'=>$response);
			#if($package_id > $user_profile_arr['package_id'])
			#if(!$first_payment)
			$userCredit['payment_process'] = '0';
			$credit_id = $ci->UserModel->creditUpdatePackageCredit($userCredit);
			$selected_package_array = $this->UserModel->get_packages_data(array('package_id' => $user_profile_arr['selected_package_id']));
			$recuring = $selected_package_array[0]['package_recurring_interval'];
			if($recuring == 'credit'){
            	$input_data['payment_from_table'] = 'red_package_credit';
            	$input_data['payment_table_id'] = $credit_id;
            }else{
            	$input_data['payment_from_table'] = 'red_member_packages';
            	$input_data['payment_table_id'] = $package_id;
            }
			$ci->UserModel->insert_payment_transactions($input_data);	
			$str_input_data = @implode(',',$input_data);
			$str_input_data .= "<br/>".'===================================';
			$str_input_data .= "<br/>".$amount.','.$package_id.','.$user_profile_arr['customer_profile_id'].','.$user_profile_arr['customer_payment_profile_id'];
			@mail('pravin.jha@gmail.com','ERROR: Fail-Payment',$str_input_data);
			#################################################################################
			# If Payment is not successfully then store error info in varaiable				#
			#################################################################################
			$payment_err_msg=$this->billingcim->text;
			if(empty($this->billingcim->error_messages)){
				$payment_err_msg .=$this->billingcim->text."<br/>";
			}else{
				foreach($this->billingcim->error_messages as $error){
					$error_arr=explode("setParameter(): ",$error);
					$payment_err_msg.=$error_arr[1]."<br/>";
				}
			}
			# if get error during payment
			$this->messages->add($payment_err_msg, 'error');
			return false;
		}
	}
	
	
	
	/**
	*	Function calculateProrationAmount to calculate proration amount of same package
	**/
	function calculateProrationSamePackageAmount($memberid,$newpackageid =0){
		$today_date 	= date('Y-m-d');
		$paymentMode 	= ($_POST['payment_type_name'] == 'paypal')?'paypal' : 'AUTHORIZE';
		
		$ci=&get_instance();		
		$ci->load->model("UserModel");
		$user_packages_array = $ci->UserModel->get_user_packages(array('member_id'=>$memberid,'is_deleted'=>0));
		$package = $user_packages_array[0]['package_id'];
		// Fetch last transaction detail		
		$user_transactions	= array();
		$user_transactions	= $ci->UserModel->get_user_transactions(array('user_id'=>$memberid,'gateway'=>$paymentMode),0,0,"like");
		
		if(count($user_transactions)>0){
			$previous_selected_package_date=date('Y-m-d',strtotime($user_transactions[0]['transaction_date']));		#Last transaction date
			
			$number_of_days_in_last_transaction= $this->dateDiff($previous_selected_package_date,$today_date);
			if($number_of_days_in_last_transaction<=30){
				$package = $user_transactions[0]['package_id'];
			}
		}		
		$package_detail_array=$ci->UserModel->get_packages_data(array('package_id'=>$package));
		
		$package_amount =  $package_detail_array[0]['package_price'];
		$previous_package_interval = $package_detail_array[0]['package_recurring_interval'];
		$next_payment_date =  $user_packages_array[0]['next_payement_date']; 
		$start_payment_date =  $user_packages_array[0]['start_payment_date']; 

		if($newpackageid >0 ){
			$new_package_detail_array=$ci->UserModel->get_packages_data(array('package_id'=>$newpackageid));
			$new_package_amount =  $new_package_detail_array[0]['package_price']; 
			$new_package_interval = $new_package_detail_array[0]['package_recurring_interval'];
		}else{			 
			$new_package_amount =  $package_amount; 
		}
		if($package_amount == $new_package_amount){
			if($next_payment_date == $today_date){
				$no_of_payable_months = 1;
				$payable_amount = $package_amount * $no_of_payable_months	;
			}elseif($next_payment_date > $today_date){
				$payable_amount = 0;
			}else{
				$no_of_payable_months = 1;//CEIL($this->dateDiff($next_payment_date, $today_date)/30);
				$payable_amount = $package_amount * $no_of_payable_months	;
			}
		}		
		return $payable_amount;		
	}
	
	
	/**
	*	Function getProratedPaymentDetail function to calculate proration payment details
	**/
	
	function getProratedPaymentDetail($memberid, $newpackageid =0){
		$arrRet = array();		
		$today_date = date('Y-m-d');
		$arrCurrentPackage = $this->UserModel->get_user_packages(array('member_id'=>$memberid,'is_deleted'=>0));
		$current_package = $arrCurrentPackage[0]['package_id'];
		$current_next_payment_date =  $arrCurrentPackage[0]['next_payement_date']; 
		$current_start_payment_date =  $arrCurrentPackage[0]['start_payment_date']; 	
		
		if($current_package <= 0 or $current_next_payment_date < $today_date){ // User is on FREE plan
			$remaining_period = 30;
			$remaining_amount = 0;
			$currentPackageMaxContacts =  100;
			$currentPackageAmount =  0;
			$currentPackageInterval = 'months';			
		}else{
			// current package-detail	
			$currentPackageArray 	= $this->UserModel->get_packages_data(array('package_id'=>$current_package));		
			$currentPackageMaxContacts =  $currentPackageArray[0]['package_max_contacts'];
			$currentPackageAmount =  $currentPackageArray[0]['package_price'];
			$currentPackageInterval = $currentPackageArray[0]['package_recurring_interval'];			
			$remaining_period = $this->dateDiff($today_date, $current_next_payment_date )  ;				
		}
		$currentDayDivider = ($currentPackageInterval == 'months')?30 : 365;
		$trial_period	= ceil($remaining_period / $currentDayDivider);
		$remaining_amount = $remaining_period * ($currentPackageAmount/$currentDayDivider);
		
		$arrRet['currentPackageMaxContacts'] =  $currentPackageMaxContacts;
		$arrRet['currentPackageAmount'] =  $currentPackageAmount;
		// new package-detail
		$newPackageArray 	= $this->UserModel->get_packages_data(array('package_id'=>$newpackageid));	
		$arrRet['newPackageMaxContacts']=  $newPackageArray[0]['package_max_contacts'];	
		$arrRet['newPackageAmount']		=  $newPackageArray[0]['package_price'];
		$arrRet['newPackageInterval'] 	= $newPackageArray[0]['package_recurring_interval'];
		$arrRet['new_package_id']		=	$newpackageid;
		if($newpackageid < 0){			
			$arrRet['trial_amount']	= 0;
			$arrRet['start_payment_date'] =  '';			
			$arrRet['next_payment_date'] =  '';			
		}else{			
			$newDayDivider 		= ($arrRet['newPackageInterval'] == 'months')?30 : 365;		
			$str = "<br/>current_next_payment_date=".$current_next_payment_date;	
			$str .= "<br/>trial_period=".$trial_period;	
			$str .=  "<br/>currentDayDivider=".$currentDayDivider;	
			$str .=  "<br/>remaining_period=".$remaining_period;	
			$str .=  "<br/>remaining_amount=".$remaining_amount;	
			$arrRet['trial_period'] =  $trial_period;			
			$arrRet['trial_amount']	= (($arrRet['newPackageAmount']/$newDayDivider) * ($trial_period * $currentDayDivider)) - $remaining_amount;
			$arrRet['start_payment_date'] =  $today_date;			
			$arrRet['next_payment_date'] 	=  date("Y-m-d",(strtotime(date("Y-m-d", strtotime($arrRet['start_payment_date'])) . "+".($trial_period * $currentDayDivider)." day")) );
		}	
		//file_put_contents($this->config->item('campaign_files').'payment_log', serialize($arrRet).$str);
		// echo"$str<pre>"; 		print_r($arrRet); 		echo"</pre>"; 		exit;
		return $arrRet;
		
	}
	function calculateProrationAmount($memberid,$newpackageid =0){		
		$today_date = date('Y-m-d');
		$paymentMode = ($_POST['payment_type_name'] == 'paypal')? 'paypal' : 'AUTHORIZE';
		
		$ci=&get_instance();		
		$ci->load->model("UserModel");
		$user_packages_array = $ci->UserModel->get_user_packages(array('member_id'=>$memberid,'is_deleted'=>0));
		$package = $user_packages_array[0]['package_id'];
		// Fetch last transaction detail
		$user_transactions=array();		
		$user_transactions=$ci->UserModel->get_user_transactions(array('user_id'=>$memberid),0,0,TRUE);
		if(count($user_transactions) > 0){
			$previous_selected_package_date=date('Y-m-d',strtotime($user_transactions[0]['transaction_date']));		#Last transaction date
			$number_of_days_in_last_transaction= $this->dateDiff($previous_selected_package_date,$today_date);
			if($number_of_days_in_last_transaction<=30){
				$package = $user_transactions[0]['package_id'];
			}
		}	
		$package_detail_array=$ci->UserModel->get_packages_data(array('package_id'=>$package));
		
		$package_amount =  $package_detail_array[0]['package_price'];
		$previous_package_interval = $package_detail_array[0]['package_recurring_interval'];
		$next_payment_date =  $user_packages_array[0]['next_payement_date'];

		if($newpackageid > 0 ){
			$new_package_detail_array=$ci->UserModel->get_packages_data(array('package_id'=>$newpackageid));
			$new_package_amount =  $new_package_detail_array[0]['package_price']; 
			$new_package_interval = $new_package_detail_array[0]['package_recurring_interval'];
		}
		$amount_paid_for_existing_package = 0;
			
		if($package_amount < $new_package_amount){ // Case: UPGRADE
			if($next_payment_date > $today_date){ //  upgradation is done on the date when payment-cycle has still some days left (in mid-payment-month)				
				$dayDivider = ($previous_package_interval == 'months')?30 : 365;
				$days_left_of_this_billing_cycle = $this->dateDiff($today_date, $next_payment_date )  ;				
				$amount_paid_for_existing_package = ($package_amount * $days_left_of_this_billing_cycle)/$dayDivider;
				// echo "<br/>dayDivider = ".$dayDivider;
				// echo "<br/>days_left_of_this_billing_cycle = ".$days_left_of_this_billing_cycle;
				// echo "<br/>amount_paid_for_existing_package = ".$amount_paid_for_existing_package;
				if($previous_package_interval == 'months' && $new_package_interval != 'months'  ){
				
					//$amount_payable_for_upgraded_package = ($new_package_amount * $days_left_of_this_billing_cycle)/$dayDivider;
					$amount_payable_for_upgraded_package = $new_package_amount ; // Full amount will be considered as In case of yearly package-upgrade next-pay-dt is 1yr from today.
				}else{				
					$amount_payable_for_upgraded_package = ($new_package_amount * $days_left_of_this_billing_cycle)/$dayDivider;				
				}
				//echo "<br/>amount_payable_for_upgraded_package = ".$amount_payable_for_upgraded_package;
				//$actual_amount_payable = $amount_payable_for_upgraded_package - $amount_paid_for_existing_package ;
				
				$actual_amount_payable = $new_package_amount - $amount_paid_for_existing_package ;
				$payable_amount = $actual_amount_payable;
				//exit;
			
			}else{
				$payable_amount=$new_package_amount;
			}			
		}elseif($package_amount > $new_package_amount){ // Case: DOWNGRADE
			if($next_payment_date > $today_date){
				$dayDivider = ($new_package_interval == 'months')?30 : 365;
				$days_left_of_this_billing_cycle = $this->dateDiff($today_date, $next_payment_date )  ;				
				$amount_paid_for_existing_package = ($new_package_amount * $days_left_of_this_billing_cycle)/$dayDivider;
				$payable_amount =  $new_package_amount - $amount_paid_for_existing_package ;
			}else{	
				// IF subscription is expired. Then a new subscription will start from today-dt				
				$payable_amount =  $new_package_amount ;
			}	
		}else{ // Case: change of payment-method while package is same
			// Here ($package_amount == $new_package_amount)
			if($next_payment_date > $today_date){
				$dayDivider = ($new_package_interval == 'months')?30 : 365;
				$days_left_of_this_billing_cycle = $this->dateDiff($today_date, $next_payment_date )  ;				
				$amount_paid_for_existing_package = ($new_package_amount * $days_left_of_this_billing_cycle)/$dayDivider;
				$payable_amount =  $new_package_amount ;//- $amount_paid_for_existing_package ;
			}else{	
				// IF subscription is expired. Then a new subscription will start from today-dt				
				$payable_amount =  $new_package_amount ;
			}	
				
		} 
		return $payable_amount;
	}
	/**
	*	Function dateDiff to calculate days difference between two dates
	*
	*	@param integer $start Start date
	*	@param integer $end   End date
	*	@return integer difference between start and end date
	**/
	function dateDiff($start, $end) {
		$start_ts = strtotime($start);
		$end_ts = strtotime($end);
		$diff = $end_ts - $start_ts;
		return round($diff / 86400);
	}
}
?>