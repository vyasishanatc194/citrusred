<?php
/**
* A Ipn class
*
* This class is to  ipn get response from paypal
*
* @version 1.0
* @author Pravin Jha <pravinjha@gmail.com>
* @project Redcappi
*/
class Ipn extends CI_Controller{

    var $loginname;     // loginname for authorize.net
    var $transactionkey;    // transactionkey for authorize.net
    
    function __construct(){
        parent::__construct();
        
        if(!$this->is_authorized->check_user()){
            //redirect('user/index');exit;
        }   
        if($this->session->userdata('member_id')==''){      
            //redirect('user/index');exit;
        }   
        force_ssl(); 
        
         
     
        // $this->TEST_URL = $this->config->item('TEST_URL');
        //Paypal Details
        $this->PAYPAL_URL = $this->config->item('PAYPAL_URL') ;
        $this->PAYPAL_EMAIL =  $this->config->item('PAYPAL_EMAIL') ;
        $this->PAYPAL_SUCCESS_URL =  $this->config->item('PAYPAL_SUCCESS_URL') ;
        $this->PAYPAL_CANCEL_URL =  $this->config->item('PAYPAL_CANCEL_URL') ;
        $this->PAYPAL_NOTIFY_URL =  $this->config->item('PAYPAL_NOTIFY_URL') ;
        $this->PAYPAL_PASSWORD =  $this->config->item('PAYPAL_PASSWORD') ;
        $this->PAYPAL_SIGNATURE =  $this->config->item('PAYPAL_SIGNATURE') ;
        $this->PAYPAL_USERNAME =  $this->config->item('PAYPAL_USERNAME') ;
        
        //  Collect login info to authorize payment
        
        
        // collect config varibales
        $this->loginname         = $this->config->item('loginname');     
        $this->transactionkey    = $this->config->item('transactionkey');    
        
         
        
        $this->load->library('Billingcim'); # load billing library
        $this->billingcim->loginKey($this->loginname, $this->transactionkey, $this->config->item('test_mode'));
        
        $this->load->model('UserModel');
        $this->load->model('BillingModel');     
        $this->load->model('newsletter/Subscriber_Model');
        $this->load->model('Activity_Model');
        $this->load->model('ConfigurationModel');
        
        $this->load->helper('notification'); 
        $this->load->helper('transactional_notification');  
        $this->load->helper('admin_notification');
        $this->confg_arr=$this->ConfigurationModel->get_site_configuration_data_as_array();
    }
    /**
    * Function index to display view of upgrade packages and
    * to submit selected package in database    
    *   
    */
   

    function notify_paypal_url() {
		$data = $_POST;
		if(empty($data) && $data == ''){
			$data = $_GET;
		}
		/*$data2 = '==========<><><><><>####<><><><><><><>========================';
        $data2 .= file_get_contents('php://input');
        $myfile = fopen("/var/www/html/response.txt", "w") or die("Unable to open file!");
        $last_id = '';
        $data2 .= "TESTMSG<><><><><><><><>======@@@#####============================" . $last_id;
        fwrite($myfile, $data2);
        fclose($myfile);
		exit;*/
		$header = '';
        $req = 'cmd=_notify-validate';
		foreach ($data as $key => $value) {
        
			$getRequest[$key] = $value;	
            //All PayPal reqs must be URL encoded
            $value = urlencode(stripslashes($value));
            //Append key => value pair to CMD string
			if($key != '/ipn/notify_paypal_url')
				$req .= "&$key=$value";
        }
		
		$customArray = explode("|", $getRequest['custom']);
        $member_id = $customArray[0];
        $current_package_interval = $customArray[1];
        $transaction_id = $customArray[2];
        $package_id = $customArray[3];
		$userPackageArray = $this->UserModel->get_user_packages(array('member_id' => $member_id));
		if($package_id == ''){
			$package_id = $userPackageArray[0]['package_id'];
		} 
		if (array_key_exists('amount1', $getRequest)) {//$_REQUEST)) {
			$payable_amount = $getRequest['amount1'];//$_REQUEST['amount1'];
		} elseif (array_key_exists('amount3', $getRequest)) {
			$payable_amount = $getRequest['amount3'];//$_REQUEST['amount3'];
		}else{
			$payable_amount = $getRequest['payment_gross'];//$_REQUEST['payment_gross'];
		}
		if($payable_amount == ''){
			$payable_amount = 0;
		}
		
		//$this->Activity_Model->create_activity_payment(array('user_id' => '00'.$member_id,'amount' => '00'.$payable_amount ,'payment'=> 'paypal-notify-top','response' => $req));
		 
//      exit;
		
		
		/*$raw_post_data = "transaction_subject=New Plan 2 for new user&payment_date=03:11:25 Aug 09, 2017 PDT&txn_type=subscr_payment&subscr_id=I-STP1GWFTGLPA&last_name=Villa&residence_country=US&item_name=New Plan 2 for new user&payment_gross=200.00&mc_currency=USD&business=wikitudedeveloper-facilitator@gmail.com&payment_type=instant&protection_eligibility=Ineligible&verify_sign=ABr7TF1VNJRHrFfJkVMfCcSa87ETAo75qoBkQJn9Zyt9p2ilz42f-QSw&payer_status=verified&test_ipn=1&payer_email=wikitudedev@gmail.com&txn_id=06Y56864EL4467844&receiver_email=wikitudedeveloper-facilitator@gmail.com&first_name=James&payer_id=6VH2L7HEDY2AJ&receiver_id=RP3JQM8E96GYG&payment_status=Completed&payment_fee=6.10&mc_fee=6.10&mc_gross=200.00&custom=11600|months|13299|74&charset=windows-1252&notify_version=3.8&ipn_track_id=38141d7992a68";
		
		$raw_post_array = explode('&', $raw_post_data);
		$myPost = array();
		foreach ($raw_post_array as $keyval) {
		  $keyval = explode ('=', $keyval);
		  if (count($keyval) == 2)
			 $myPost[$keyval[0]] = urldecode($keyval[1]);
		}
		// read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
		$req = 'cmd=_notify-validate';
		if(function_exists('get_magic_quotes_gpc')) {
		   $get_magic_quotes_exists = true;
		}
		foreach ($myPost as $key => $value) {	*/
		
        $configurationModels = $this->ConfigurationModel->get_site_configuration_data_as_array();
        $to_mails = $configurationModels['admin_notification_email'];
        $from_emails = $configurationModels['admin_email'];
        /* Insert value of payment Response in table "red_paypal_response" */
        $params = array(
            'red_member_package_id' => $userPackageArray[0]['red_member_package_id'],
            'member_id' => $member_id,
            'package_id' => $package_id,
            'paypal_profile_id' => $getRequest['subscr_id'],//$_REQUEST['subscr_id'],
            'response' => serialize($getRequest),//serialize($_REQUEST),
            'createddate' => date('Y-m-d H:i:s'),
        );
		
		$this->db->insert('red_paypal_response', $params);
		$last_id = $this->db->insert_id();
		
		//$this->Activity_Model->create_activity_payment(array('user_id' => $member_id,'amount' => $payable_amount ,'payment'=> 'paypal-notify-top','response' => serialize($getRequest)  ));//serialize($_REQUEST)  ));
		
		//$ch = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');
		$ch = curl_init('https://www.paypal.com/cgi-bin/webscr');
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
		// In wamp-like environments that do not come bundled with root authority certificates,
		// please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set 
		// the directory path of the certificate as shown below:
		// curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
		if( !($res = curl_exec($ch)) ) {
			error_log("Got " . curl_error($ch) . " when processing IPN data");
			$this->Activity_Model->create_activity_payment(array('user_id' => $member_id,'amount' => $payable_amount ,'payment'=> 'paypal-curl-error','response' => serialize(curl_error($ch))  ));
			curl_close($ch);
			exit;
		}
		
		if (strcmp ($res, "VERIFIED") == 0 ) {
				
			
            //VERIFIED TRANSACTION
            // if ($_REQUEST['payment_status'] == 'Completed' || 1 == 1) {
            if ($getRequest['payment_status'] == 'Completed' ) {
				
				$transactionData = $this->UserModel->get_payment_transactions(array('transaction_id' =>$transaction_id));
				if (array_key_exists('amount1', $getRequest) && $getRequest['amount1'] > 0 ) {//$_REQUEST)) {
					$payable_amount = $getRequest['amount1'];//$_REQUEST['amount1'];
				} elseif (array_key_exists('amount3', $getRequest)) {
					$payable_amount = $getRequest['amount3'];//$_REQUEST['amount3'];
				}else{
					$payable_amount = $getRequest['payment_gross'];
				}
				if($payable_amount == ''){
					$payable_amount = '0';//$_REQUEST['payment_gross'];
				}
				if($current_package_interval == 'credit'){
					
					$transactionData = $transactionData[0];
					
					if($transactionData['status'] == 'FAILURE'){
						$subscr_id = $getRequest['subscr_id'];
						if($getRequest['subscr_id'] == ''){
							$subscr_id = $getRequest['payer_id'];
						}
						$current_date = date('Y-m-d h:i:s');
						$start_payment_date = $current_date;
						if ($current_package_interval == 'months') {
							$next_payement_timestamp = strtotime(date("Y-m-d", strtotime($current_date)) . "+1 month");
						} elseif($current_package_interval == 'years') {
							$next_payement_timestamp = strtotime(date("Y-m-d", strtotime($current_date)) . "+1 year");
						}elseif($current_package_interval == 'daily'){
							$next_payement_timestamp = strtotime(date("Y-m-d", strtotime($current_date)) . "+1 day");
						}
						if($next_payement_timestamp ==''){
							$year = date('Y') + 100;
							$next_payement_date = $year.'-'.date("m-d") ;
						}else
							$next_payement_date = date('Y-m-d h:i:s',$next_payement_timestamp);
						$update_array = array('payment_paypal_status' => 1,
							'is_payment' => 1,
							'package_id' => $package_id,
							'start_payment_date' => $start_payment_date,
							'next_payement_date' => $next_payement_date,
							'paypal_transaction_id' => $getRequest['subscr_id'],//$_REQUEST['subscr_id'],
							'paypal_payer_email' => $getRequest['payer_email'],//$_REQUEST['payer_email'],
						);
						
						$user_profile_arr = array('response' => serialize($getRequest),//serialize($_REQUEST),
							'paypal_profile_id' => $subscr_id,//$_REQUEST['subscr_id'],
							'status' => 'SUCCESS',
							'member_id' => $member_id,
							'transaction_id' => $transaction_id
						);
						if($transactionData['payment_from_table'] == 'red_package_credit'){
							$creditId = $transactionData['payment_table_id'];
							$userCredit['payment_process'] = '1';
							$getCredit = $this->UserModel->getCreditPackage(array('member_id'=>$user_profile_arr['member_id'],'credit_id'=>$creditId));
							if($getCredit[0]['credit_id'] == $creditId && $getCredit[0]['payment_process'] == '0' ){
								$credit = $this->UserModel->creditUpdatePackageCredit($userCredit,$member_id);
								$getCredit = $this->UserModel->getMemberEmailSendCount($user_profile_arr['member_id']);
								if($getCredit){
									$update_array['max_campaign_quota'] = $getCredit[0]['max_email'] - $userPackageArray[0]['campaign_sent_counter'];
									$update_array['campaign_sent_counter'] = 0;
								}
							}
						}
						
						$this->UserModel->update_member_package($update_array, array('member_id' => $member_id));
						$customerProfileTransaction = $this->BillingModel->createCustomerPaypalProfileTransactionRequest($payable_amount, $package_id, $user_profile_arr, $first_payment); 
						
						//$this->Activity_Model->create_activity_payment(array('user_id' => $member_id,'amount' => $payable_amount ,'payment'=> 'paypal-notify-credit','response' => serialize($getRequest)));//serialize($_REQUEST)  ));
					} 
					
				}else{ 
				$transactionData = $this->UserModel->get_payment_transactions(array('transaction_id' =>$transaction_id));
					
					$user_profile_arr = array('response' => serialize($getRequest),//serialize($_REQUEST),
						'paypal_profile_id' => $getRequest['subscr_id'],//$_REQUEST['subscr_id'],
						'status' => 'SUCCESS',
						'member_id' => $member_id,
					);
					$current_date = date('Y-m-d h:i:s');
					$start_payment_date = $current_date;
					if ($current_package_interval == 'months') {
						$next_payement_timestamp = strtotime(date("Y-m-d", strtotime($current_date)) . "+1 month");
					} elseif($current_package_interval == 'years') {
						$next_payement_timestamp = strtotime(date("Y-m-d", strtotime($current_date)) . "+1 year");
					}elseif($current_package_interval == 'daily'){
						$next_payement_timestamp = strtotime(date("Y-m-d", strtotime($current_date)) . "+1 day");
					}
					if($next_payement_timestamp =='')
						$next_payement_date = '';
					else
						$next_payement_date = date('Y-m-d h:i:s',$next_payement_timestamp);
					$transaction_date = date('Y-m-d',strtotime($transactionData[0]['transaction_date']));
					$today = date('Y-m-d',time());
					if($transaction_date == $today){
						if($transactionData[0]['status'] == 'FAILURE'){
							$user_profile_arr['transaction_id'] = $transaction_id;
						}else{
							die();
						}
					}
					
					// completed
					//$customerProfileTransaction = $this->BillingModel->createCustomerPaypalProfileTransactionRequest($payable_amount, $package_id, $user_profile_arr, $first_payment);
					/* Update payment status and txn id in "red_member_packages" */
					$current_date = date('Y-m-d h:i:s');
					$start_payment_date = $current_date;
					if ($current_package_interval == 'months') {
						$next_payement_timestamp = strtotime(date("Y-m-d", strtotime($current_date)) . "+1 month");
					} elseif($current_package_interval == 'years') {
						$next_payement_timestamp = strtotime(date("Y-m-d", strtotime($current_date)) . "+1 year");
					}elseif($current_package_interval == 'daily'){
						$next_payement_timestamp = strtotime(date("Y-m-d", strtotime($current_date)) . "+1 day");
					}
					if($next_payement_timestamp =='')
						$next_payement_date = '';
					else
						$next_payement_date = date('Y-m-d h:i:s',$next_payement_timestamp);
					$update_array = array('payment_paypal_status' => 1,
						'is_payment' => 1,
						'package_id' => $package_id,
						'start_payment_date' => $start_payment_date,
						'next_payement_date' => $next_payement_date,
						'paypal_transaction_id' => $getRequest['subscr_id'],//$_REQUEST['subscr_id'],
						'paypal_payer_email' => $getRequest['payer_email'],//$_REQUEST['payer_email'],
					);
					
					//$this->UserModel->update_member_package($update_array, array('member_id' => $member_id));
					
					
					//send mail to admin and user
					$body = 'Hello Admin,<br/><br/>';
					//Your payment done successfully.
					//your next payment Date: '.$userPackageArray[0]['next_payement_date'].'<br/><br/>Thank You<br/>Redcappi Team ';
					$body .= '<b>UserName:</b> ' . $userPackageArray[0]['first_name'] . ' ' . $userPackageArray[0]['last_name'] . '<br/>';
					$body .= '<b>Email:</b> ' . $this->session->userdata('member_email_address') . '<br/>';
					$body .= '<b>Address:</b> ' . $userPackageArray[0]['address'] . '<br/>';
					$body .= '<b>City:</b> ' . $userPackageArray[0]['city'] . '<br/>';
					$body .= '<b>State:</b> ' . $userPackageArray[0]['state'] . '<br/>';
					$body .= '<b>Zip:</b> ' . $userPackageArray[0]['zip'] . '<br/>';
					$body .= '<b>Country:</b> ' . $userPackageArray[0]['country'] . '<br/>';
					$body .= '<b>Payment Type:</b> Paypal<br/>';
					$body .= '<b>Payment Response:</b> ' . json_decode($getRequest) . '<br/>';//json_decode($_REQUEST) . '<br/>';
					$body .= '<b>Next Payment Type:</b> ' . $userPackageArray[0]['next_payement_date'] . '<br/>';

					//send_mail($to="", $sender="",$sender_name="", $subject="",$message="",$text_message="")
					//$this->Activity_Model->create_activity_payment(array('user_id' => $member_id,'amount' => $payable_amount ,'payment'=> 'paypal-Completed-notify','response' => serialize($getRequest)  ));//serialize($_REQUEST)  ));
					//send_mail($to_mails, $from_emails, 'Redcappi', 'Redcappi paypal payment Success', $body);
				}
                    //} elseif ($_REQUEST['payment_status'] == 'failed' || $_REQUEST['payment_status'] == 'expired' || $_REQUEST['payment_status'] == 'voided') {
            } elseif ($getRequest['payment_status'] == 'failed' || $getRequest['payment_status'] == 'expired' || $getRequest['payment_status'] == 'voided') {
				
				$user_profile_arr = array('response' => serialize($getRequest),//serialize($_REQUEST),
					'paypal_profile_id' => $getRequest['subscr_id'],//$_REQUEST['subscr_id'],
					'status' => 'FAILURE',
					'member_id' => $member_id,
				);
				$transaction_date = date('Y-m-d',strtotime($transactionData[0]['transaction_date']));
				$today = date('Y-m-d');
				if($transaction_date == $today){
					if($transactionData[0]['status'] == 'FAILURE'){
						$user_profile_arr['transaction_id'] = $transaction_id;
					}else{
						exit;
					}
				}
				
				$profileId = $userPackageArray[0]['paypal_transaction_id'];
				/** Stop previous recurring method
				 *
				$this->paypal_subscription_cancel($profileId, 'Cancel');
				/*                         * */
				$update_package_array = array();

				$update_package_array = array("red_member_packages" => 3);

				//$this->UserModel->update_member_package($update_package_array, array('member_id' => $member_id));

// failed
				$update_array = array('payment_paypal_status' => 2,
					'is_payment' => 0,
					'package_id' => $userPackageArray[0]['package_id'],
					'paypal_transaction_id' => $getRequest['subscr_id'],//$_REQUEST['subscr_id'],
					'paypal_payer_email' => $getRequest['payer_email'],//$_REQUEST['payer_email'],
				);
				$this->UserModel->update_member_package($update_array, array('member_id' => $member_id));

				

				//send mail to admin and user
				$body = 'Hello Admin,<br/><br/>';
				//Your payment done successfully.
				//your next payment Date: '.$userPackageArray[0]['next_payement_date'].'<br/><br/>Thank You<br/>Redcappi Team ';
				$body .= '<b>UserName:</b> ' . $userPackageArray[0]['first_name'] . ' ' . $userPackageArray[0]['last_name'] . '<br/>';
				$body .= '<b>Email:</b> ' . $this->session->userdata('member_email_address') . '<br/>';
				$body .= '<b>Address:</b> ' . $userPackageArray[0]['address'] . '<br/>';
				$body .= '<b>City:</b> ' . $userPackageArray[0]['city'] . '<br/>';
				$body .= '<b>State:</b> ' . $userPackageArray[0]['state'] . '<br/>';
				$body .= '<b>Zip:</b> ' . $userPackageArray[0]['zip'] . '<br/>';
				$body .= '<b>Country:</b> ' . $userPackageArray[0]['country'] . '<br/>';
				$body .= '<b>Payment Type:</b> Paypal<br/>';
				$body .= '<b>Payment Response:</b> ' . json_decode($data) . '<br/>';
				$body .= '<b>Next Payment Type:</b> ' . $userPackageArray[0]['next_payement_date'] . '<br/>';

				//send_mail($to="", $sender="",$sender_name="", $subject="",$message="",$text_message="")
				 //$this->Activity_Model->create_activity_payment(array('user_id' => $member_id,'amount' => $payable_amount ,'payment'=> 'paypal-failed-notify' ,'response' => serialize($getRequest) ));//serialize($_REQUEST) ));
				send_mail($to_mails, $from_emails, 'Redcappi', 'Redcappi paypal payment Failed', $body);
			} elseif (!strcmp($res, "INVALID")) {
				// INvalid
				$user_profile_arr = array('response' => serialize($getRequest),//serialize($_REQUEST),
					'paypal_profile_id' => $getRequest['subscr_id'],//$_REQUEST['subscr_id'],
					'status' => 'FAILURE',
					'member_id' => $member_id,
				);
				$transaction_date = date('Y-m-d',strtotime($transactionData[0]['transaction_date']));
				$today = date('Y-m-d');
				if($transaction_date == $today){
					if($transactionData[0]['status'] == 'FAILURE'){
						$user_profile_arr['transaction_id'] = $transaction_id;
					}else{
						exit;
					}
				}
				$profileId = $userPackageArray[0]['paypal_transaction_id'];
				/** Stop previous recurring method
				 *
				$this->paypal_subscription_cancel($profileId, 'Cancel');
				/*                         * */

				$update_package_array = array();

				$update_package_array = array("red_member_packages" => 3);

				//$this->UserModel->update_member_package($update_package_array, array('member_id' => $member_id));


				$update_array = array('payment_paypal_status' => 3,
					'is_payment' => 0,
					'package_id' => $userPackageArray[0]['package_id'],
					'paypal_transaction_id' => $getRequest['subscr_id'],//$_REQUEST['subscr_id'],
					'paypal_payer_email' => $getRequest['payer_email'],//$_REQUEST['payer_email'],
				);
				$this->UserModel->update_member_package($update_array, array('member_id' => $member_id));

				$user_profile_arr = array('response' => serialize($getRequest),//serialize($_REQUEST),
					'paypal_profile_id' => $getRequest['subscr_id'],//$_REQUEST['subscr_id'],
					'status' => 'FAILURE',
					'member_id' => $member_id,
				);
				$transaction_date = date('Y-m-d',strtotime($transactionData[0]['transaction_date']));
				$today = date('Y-m-d');
				if($transaction_date == $today){
					if($transactionData[0]['status'] == 'FAILURE'){
						$user_profile_arr['transaction_id'] = $transaction_id;
					}
				}
				$customerProfileTransaction = $this->BillingModel->createCustomerPaypalProfileTransactionRequest($payable_amount, $package_id, $user_profile_arr, $first_payment);


				//send mail to admin and user
				$body = 'Hello Admin,<br/><br/>';
				//Your payment done successfully.
				//your next payment Date: '.$userPackageArray[0]['next_payement_date'].'<br/><br/>Thank You<br/>Redcappi Team ';
				$body .= '<b>UserName:</b> ' . $userPackageArray[0]['first_name'] . ' ' . $userPackageArray[0]['last_name'] . '<br/>';
				$body .= '<b>Email:</b> ' . $this->session->userdata('member_email_address') . '<br/>';
				$body .= '<b>Address:</b> ' . $userPackageArray[0]['address'] . '<br/>';
				$body .= '<b>City:</b> ' . $userPackageArray[0]['city'] . '<br/>';
				$body .= '<b>State:</b> ' . $userPackageArray[0]['state'] . '<br/>';
				$body .= '<b>Zip:</b> ' . $userPackageArray[0]['zip'] . '<br/>';
				$body .= '<b>Country:</b> ' . $userPackageArray[0]['country'] . '<br/>';
				$body .= '<b>Payment Type:</b> Paypal<br/>';
				$body .= '<b>Payment Response:</b> ' . json_decode($_REQUEST) . '<br/>';
				$body .= '<b>Next Payment Type:</b> ' . $userPackageArray[0]['next_payement_date'] . '<br/>';
				//send_mail($to="", $sender="",$sender_name="", $subject="",$message="",$text_message="")
				//$this->Activity_Model->create_activity_payment(array('user_id' => $member_id,'amount' => $payable_amount ,'payment'=> 'paypal-INVALID-notify' ,'response' => serialize($getRequest) ));//serialize($_REQUEST) ));
				send_mail($to_mails, $from_emails, 'Redcappi', 'Redcappi paypal payment Failed', $body);
			}
					
			//$first_payment
			$userTransactionCount = $this->UserModel->get_user_transaction_count(array('user_id' => $member_id));
			if ($userTransactionCount > 0) {
				$first_payment = false;
			} else {
				$first_payment = true;
			}
			$customerProfileTransaction = $this->BillingModel->createCustomerPaypalProfileTransactionRequest($payable_amount, $package_id, $user_profile_arr, $first_payment);
			
		} else if (strcmp ($res, "INVALID") == 0) {
			// IPN invalid, log for manual investigation
			echo "The response from IPN was: <b>" .$res ."</b>";
		}
    }

    
}
?>