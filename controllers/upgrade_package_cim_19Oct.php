<?php
/**
* A Upgrade_package_cim class
*
* This class is to  upgrdae the memeber package
*
* @version 1.0
* @author Pravin Jha <pravinjha@gmail.com>
* @project Redcappi
*/
class Upgrade_package_cim extends CI_Controller{

	var $loginname;		// loginname for authorize.net
    var $transactionkey;	// transactionkey for authorize.net
	
	function __construct(){
		parent::__construct();
		
		if(!$this->is_authorized->check_user()){
			redirect('user/index');exit;
		}	
		if($this->session->userdata('member_id')==''){		
			redirect('user/index');exit;
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
        $this->loginname    	 = $this->config->item('loginname');	 
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
	function annual(){
		$this->index('annual');
	}

    /**
     * Function payment_by_paypal_cim to do paypal payment
     * @return boolean return payment is successfull or not 
     * 
     */
    function payment_by_paypal_cim() {


        $errorMessgeArray = array();
        if ($_POST) {
            if ($_POST['first_name'] == '') {
                $errorMessgeArray[] = '<p>Plese enter firstname</p>';
            }
            if ($_POST['last_name'] == '') {
                $errorMessgeArray[] = '<p>Plese enter lastname</p>';
            }
            if ($_POST['address1'] == '') {
                $errorMessgeArray[] = '<p>Plese enter address</p>';
            }
            if ($_POST['city'] == '') {
                $errorMessgeArray[] = '<p>Plese enter city</p>';
            }
            if ($_POST['state'] == '') {
                $errorMessgeArray[] = '<p>Plese enter state</p>';
            }
            if ($_POST['zipcode'] == '') {
                $errorMessgeArray[] = '<p>Plese enter zipcode</p>';
            }
            if ($_POST['country'] == '') {
                $errorMessgeArray[] = '<p>Plese enter country</p>';
            }
            if (!array_key_exists('terms_conditions', $_POST)) {
                $errorMessgeArray[] = '<p>Plese select terms</p>';
            }

            if (count($errorMessgeArray) > 0) {
                $errorMessage = implode("", $errorMessgeArray);
                $jsonArray = array('status' => 'failed', 'messgae' => $errorMessage);
                echo json_encode($jsonArray);
                exit;
            }
        }




        /* Start Extra Code */
        //Fetch Customer Profile id From database		
        $this->load->model('payment/payment_model');
        $strCouponCode = $this->input->post('coupon_code');

        $first_payment = false;
        $user_profile_arr = $this->UserModel->get_user_packages(array('member_id' => $this->session->userdata('member_id')));
        $customer_profile_id = $user_profile_arr[0]['customer_profile_id'];
        $customer_payment_profile_id = $user_profile_arr[0]['customer_payment_profile_id'];


        $no_of_uses = '1';
        $payment_year_month = $this->input->post('payment_year_month');
        $first_name = $this->input->post('first_name');
        $last_name = $this->input->post('last_name');
        $address1 = $this->input->post('address1');
        $city = $this->input->post('city');
        $state = $this->input->post('state');
        $zipcode = $this->input->post('zipcode');

        $payable_amount = $this->BillingModel->calculateProrationAmount($this->session->userdata('member_id'), $this->input->post('packageId'));
        $payable_amount = number_format($payable_amount, 2);

        /* regular price for next recurring */
        $selected_package_array = $this->UserModel->get_packages_data(array('package_id' => $this->input->post('packageId')));



        $selected_package_price = $selected_package_array[0]['package_price'];
        $package_regular_price = $selected_package_price;



        if ($customer_profile_id > 0) {
            $first_payment = true;
            // Genrate Customer Payment Profile id
            $last_updated_id = $this->BillingModel->createPaypalCustomerPaymentProfileRequest($customer_profile_id);
            $packagesArray = $this->UserModel->get_packages_data(array('package_id' => $this->input->post('packageId')));
            $package_title = $packagesArray[0]['package_title'];
            $package_price = $packagesArray[0]['package_price'];

            $jsonArray = array('status' => 'success', 'member_package_id' => $last_updated_id, 'package_title' => $package_title, 'package_price' => $payable_amount, 'package_regular_price' => $package_regular_price, 'first_name' => $first_name, 'last_name' => $last_name, 'address1' => $address1, 'city' => $city, 'state' => $state, 'zip' => $zipcode, 'no_of_uses' => $no_of_uses, 'payment_year_month' => $payment_year_month);
        } else {
            $first_payment = true;
            // Genrate Customer Profile id
            $customer_profile_id = $this->BillingModel->createPaypalCustomerPaymentProfileRequest();
            if ($customer_profile_id) {
                // Genrate Customer Payment Profile id
                if ($this->BillingModel->createPaypalCustomerPaymentProfileRequest($customer_profile_id)) {
                    //$payable_amount = $this->BillingModel->calculateProrationAmount($this->session->userdata('member_id'), $this->input->post('packageId'));
                    $packagesArray = $this->UserModel->get_packages_data(array('package_id' => $this->input->post('packageId')));
                    $package_title = $packagesArray[0]['package_title'];
                    $package_price = $packagesArray[0]['package_price'];
                    $jsonArray = array('status' => 'success', 'member_package_id' => $customer_profile_id, 'package_title' => $package_title, 'package_price' => $payable_amount, 'package_regular_price' => $package_regular_price, 'first_name' => $first_name, 'last_name' => $last_name, 'address1' => $address1, 'city' => $city, 'state' => $state, 'zip' => $zipcode, 'no_of_uses' => $no_of_uses, 'payment_year_month' => $payment_year_month);
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        $user_profile_arr = $this->UserModel->get_user_packages(array('member_id' => $this->session->userdata('member_id')));


        if ($payable_amount > 0) {

            if ($first_payment) {

                if ($user_profile_arr[0]['coupon_code_used'] == '') {
                    $old_payable_amount = $payable_amount;
                    $payable_amount = $this->payment_model->getDiscountedAmountForFirstPayment($payable_amount, $strCouponCode);

                    $coupon_details = $this->payment_model->getCouponDetails($payable_amount, $strCouponCode);

                    if ($coupon_details['status'] == 'success') {
                        $no_of_uses = $coupon_details['noOfUses'];
                    }

                    if ($coupon_details['noOfUses'] == 'all') {
                        $package_regular_price = $this->payment_model->getDiscountedAmountForFirstPayment($package_regular_price, $strCouponCode);
                    }

                    $package_regular_price = $package_regular_price;

                    $new_payable_amount = $payable_amount;

                    if (($new_payable_amount != $old_payable_amount) && $strCouponCode != '') {
                        $this->UserModel->update_member_package(array('coupon_code_used' => $strCouponCode), array('member_id' => $this->session->userdata('member_id')));                      
                    }
                } else {

                    $member_id = $this->session->userdata('member_id');
                    $coupon_details = $this->payment_model->getPaypalDiscountedAmountForSubsequentPayments($member_id, $payable_amount);

                    if ($coupon_details['monthsCount'] == 'all') {
                        $coupon_second_details = $this->payment_model->getPaypalDiscountedAmountForSubsequentPayments($member_id, $package_regular_price);

                        $no_of_uses = $coupon_details['monthsCount'];
                        $payable_amount = $coupon_details['return_payable_amount'];
                        $package_regular_price = $coupon_second_details['return_payable_amount'];
                    }
                }

                $jsonArray['package_price'] = $payable_amount;
                $jsonArray['no_of_uses'] = $no_of_uses;
                $jsonArray['package_regular_price'] = $package_regular_price;
            } else {

                $payable_amount = $this->payment_model->getDiscountedAmountForSubsequentPayments($this->session->userdata('member_id'), $payable_amount);

                $jsonArray['package_price'] = $payable_amount;

                /*
                  if($package_regular_price > 0){
                  $member_id = $this->session->userdata('member_id');
                  $coupon_details = $this->payment_model->getPaypalDiscountedAmountForSubsequentPayments($member_id, $package_regular_price);
                  $no_of_uses = $coupon_details['monthsCount'];
                  $package_regular_price  = $coupon_details['return_payable_amount'];
                  } */
            }








            //$customerProfileTransaction = $this->BillingModel->createCustomerProfileTransactionRequest($payable_amount, $this->input->post('packageId'), $user_profile_arr[0], $first_payment);
            if ($first_payment) {
                //$update_array = array('customer_profile_id' => 0, 'customer_payment_profile_id' => 0);
                //$this->UserModel->update_member_package($update_array, array('member_id' => $this->session->userdata('member_id')));
            }
            // redirect('upgrade_package_cim/index');
        } else {
            $this->upgradePaypalPackage($user_profile_arr[0], $this->input->post('packageId'), $first_payment);

            if ($package_regular_price > 0) {
                $member_id = $this->session->userdata('member_id');
                $coupon_details = $this->payment_model->getPaypalDiscountedAmountForSubsequentPayments($member_id, $package_regular_price);
                $no_of_uses = $coupon_details['monthsCount'];
                $package_regular_price = $coupon_details['return_payable_amount'];
            }
        }
        $jsonArray['package_regular_price'] = $package_regular_price;

        
        $userTransactionCount = $this->UserModel->get_user_transaction_count(array('user_id' => $this->session->userdata('member_id')));
                    if ($userTransactionCount > 0) {
                        $first_payment = false;
                    } else {
                        $first_payment = true;
                    }
                    

        $user_profile_arr = array(
                            'status' => 'FAILURE',
                            'member_id' => $this->session->userdata('member_id'),
                        );
        $customerProfileTransaction = $this->BillingModel->createCustomerPaypalProfileTransactionRequest($payable_amount, $this->input->post('packageId'), $user_profile_arr, $first_payment);

        
        
        $jsonArray['package_price'] = sprintf("%01.2f",$jsonArray['package_price']);
        $jsonArray['package_regular_price'] = sprintf("%01.2f",$jsonArray['package_regular_price']) ;
        $jsonArray['transaction_id'] = $customerProfileTransaction;
        echo json_encode($jsonArray);
        exit;
        /* End Extra Code */
    }

    function cancelpaypal() {


        $configurationModels = $this->ConfigurationModel->get_site_configuration_data_as_array();
        $to_mails = $configurationModels['admin_notification_email'];
        $from_emails = $configurationModels['admin_email'];
        //exit;
        /* Fetch data from the "red_paypal_member" */
        $paypalPackageArray = $this->UserModel->get_user_packages(array('member_id' => $this->session->userdata('member_id')));

        //send mail to admin and user
        $body = 'Hello Admin,<br/><br/>';
        //Your payment done successfully.
        //your next payment Date: '.$paypalPackageArray[0]['next_payement_date'].'<br/><br/>Thank You<br/>Redcappi Team ';
        $body .= '<b>UserName:</b> ' . $paypalPackageArray[0]['first_name'] . ' ' . $paypalPackageArray[0]['last_name'] . '<br/>';
        $body .= '<b>Email:</b> ' . $this->session->userdata('member_email_address') . '<br/>';
        $body .= '<b>Address:</b> ' . $paypalPackageArray[0]['address'] . '<br/>';
        $body .= '<b>City:</b> ' . $paypalPackageArray[0]['city'] . '<br/>';
        $body .= '<b>State:</b> ' . $paypalPackageArray[0]['state'] . '<br/>';
        $body .= '<b>Zip:</b> ' . $paypalPackageArray[0]['zip'] . '<br/>';
        $body .= '<b>Country:</b> ' . $paypalPackageArray[0]['country'] . '<br/>';
        $body .= '<b>Payment Type:</b> Paypal<br/>';

        //send_mail($to="", $sender="",$sender_name="", $subject="",$message="",$text_message="")
        send_mail($to_mails, $from_emails, 'Redcappi', 'Redcappi Cancel Paypal Payment', $body);


        $this->load->view('header', array('title' => 'Upgrade Package', 'previous_page_url' => $previous_page_url));
        //$this->load->view('user/upgrade_package_cim', array('packages' => $packages, 'user_package' => $user_packages_array[0], 'selected_package' => $selected_package, 'messages' => $messages, 'checked_package_id' => $checked_package_id, 'selected_package_id' => $selected_package_id, 'country_info' => $country_info, 'previous_page_url' => $previous_page_url, 'mode' => $mode));
        $this->load->view('user/cancel_paypal');
        $this->load->view('footer');
    }

    function notify_paypal_url_test() {
        $data2 = 'New<><><><>#####<><><>';
        $myfile = fopen("/var/www/thevaghela.com/public_html/dev/response.txt", "w") or die("Unable to open file!");
        fwrite($myfile, $data2);
        fclose($myfile);
        exit;
    }

    function notify_paypal_url() {
        $data2 = '==========<><><><><>####<><><><><><><>========================';
        $data2 .= file_get_contents('php://input');
        $myfile = fopen("/var/www/html/response.txt", "w") or die("Unable to open file!");
        $last_id = '';
        $data2 .= "TESTMSG<><><><><><><><>======@@@#####============================" . $last_id;
        fwrite($myfile, $data2);
        fclose($myfile);
//		exit;



        $header = '';
        $req = 'cmd=_notify-validate';


        $customArray = explode("|", $_POST['custom']);
        $member_id = $customArray[0];
        $current_package_interval = $customArray[1];

        $current_date = date('Y-m-d h:i:s');
        $start_payment_date = $current_date;
        if ($current_package_interval == 'months') {
            $next_payement_timestamp = strtotime(date("Y-m-d", strtotime($current_date)) . "+1 month");
        } else {
            $next_payement_timestamp = strtotime(date("Y-m-d", strtotime($current_date)) . "+1 year");
        }



        foreach ($_POST as $key => $value) {
            //All PayPal reqs must be URL encoded
            $value = urlencode(stripslashes($value));
            //Append key => value pair to CMD string
            $req .= "&$key=$value";
        }


        //Post info back to paypal for verification
        $header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
        $fp = fsockopen('www.paypal.com', 80, $errno, $errstr, 30);


        $userPackageArray = $this->UserModel->get_user_packages(array('member_id' => $member_id));


        
       // $paypalPackageArray = $this->UserModel->get_user_packages(array('member_id' => $member_id));


        $configurationModels = $this->ConfigurationModel->get_site_configuration_data_as_array();
        $to_mails = $configurationModels['admin_notification_email'];
        $from_emails = $configurationModels['admin_email'];




        /* Insert value of payment Response in table "red_paypal_response" */
        $params = array(
            'red_member_package_id' => $userPackageArray[0]['red_member_package_id'],
            'member_id' => $member_id,
            'package_id' => $userPackageArray[0]['package_id'],
            'paypal_profile_id' => $_POST['subscr_id'],
            'response' => serialize($_POST),
            'createddate' => date('Y-m-d H:i:s'),
        );
        $this->db->insert('red_paypal_response', $params);
        $last_id = $this->db->insert_id();



        if (!$fp) {
            //Process HTTP Error
            $message .= "\n HTTP ERROR. \n";
        } else {
            fputs($fp, $header . $req);
            while (!feof($fp)) {
                $res = fgets($fp, 1024);
                if (!strcmp($res, "VERIFIED")) {
                    //VERIFIED TRANSACTION
                    if ($_POST['payment_status'] == 'Completed' || 1 == 1) {
                        // completed
                        /* Update payment status and txn id in "red_member_packages" */

                        $update_array = array('payment_paypal_status' => 1,
                            'is_payment' => 1,
                            'package_id' => $userPackageArray[0]['package_id'],
                            'start_payment_date' => $start_payment_date,
                            'next_payement_date' => $next_payement_date,
                            'paypal_transaction_id' => $_POST['subscr_id'],
							'paypal_payer_email' => $_POST['payer_email'],
                        );
                        $this->UserModel->update_member_package($update_array, array('member_id' => $member_id));

                        $user_profile_arr = array('response' => serialize($_POST),
                            'paypal_profile_id' => $_POST['subscr_id'],
                            'status' => 'SUCCESS',
                            'member_id' => $member_id,
                        );

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
                        $body .= '<b>Payment Response:</b> ' . json_decode($_POST) . '<br/>';
                        $body .= '<b>Next Payment Type:</b> ' . $userPackageArray[0]['next_payement_date'] . '<br/>';

                        //send_mail($to="", $sender="",$sender_name="", $subject="",$message="",$text_message="")
                        send_mail($to_mails, $from_emails, 'Redcappi', 'Redcappi paypal payment Success', $body);
                    } elseif ($_POST['payment_status'] == 'failed' || $_POST['payment_status'] == 'expired' || $_POST['payment_status'] == 'voided') {

                        $profileId = $userPackageArray[0]['paypal_transaction_id'];
                        /** Stop previous recurring method
                         */
                        $this->paypal_subscription_cancel($profileId, 'Cancel');
                        /*                         * */


                        $update_package_array = array();

                        $update_package_array = array("red_member_packages" => 3);

                        $this->UserModel->update_member_package($update_package_array, array('member_id' => $member_id));




// failed
                        $update_array = array('payment_paypal_status' => 2,
                            'is_payment' => 0,
                            'package_id' => $userPackageArray[0]['package_id'],
                            'start_payment_date' => $start_payment_date,
                            'next_payement_date' => $next_payement_date,
                            'paypal_transaction_id' => $_POST['subscr_id'],
							'paypal_payer_email' => $_POST['payer_email'],
                        );
                        $this->UserModel->update_member_package($update_array, array('member_id' => $member_id));

                        $user_profile_arr = array('response' => serialize($_POST),
                            'paypal_profile_id' => $_POST['subscr_id'],
                            'status' => 'FAILURE',
                            'member_id' => $member_id,
                        );


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
                        $body .= '<b>Payment Response:</b> ' . json_decode($_POST) . '<br/>';
                        $body .= '<b>Next Payment Type:</b> ' . $userPackageArray[0]['next_payement_date'] . '<br/>';

                        //send_mail($to="", $sender="",$sender_name="", $subject="",$message="",$text_message="")
                        send_mail($to_mails, $from_emails, 'Redcappi', 'Redcappi paypal payment Failed', $body);
                    } elseif (!strcmp($res, "INVALID")) {
                        // INvalid

                        $profileId = $userPackageArray[0]['paypal_transaction_id'];
                        /** Stop previous recurring method
                         */
                        $this->paypal_subscription_cancel($profileId, 'Cancel');
                        /*                         * */

                        $update_package_array = array();

                        $update_package_array = array("red_member_packages" => 3);

                        $this->UserModel->update_member_package($update_package_array, array('member_id' => $member_id));


                        $update_array = array('payment_paypal_status' => 3,
                            'is_payment' => 0,
                            'package_id' => $userPackageArray[0]['package_id'],
                            'start_payment_date' => $start_payment_date,
                            'next_payement_date' => $next_payement_date,
                            'paypal_transaction_id' => $_POST['subscr_id'],
							'paypal_payer_email' => $_POST['payer_email'],
                        );
                        $this->UserModel->update_member_package($update_array, array('member_id' => $member_id));

                        $user_profile_arr = array('response' => serialize($_POST),
                            'paypal_profile_id' => $_POST['subscr_id'],
                            'status' => 'FAILURE',
                            'member_id' => $member_id,
                        );


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
                        $body .= '<b>Payment Response:</b> ' . json_decode($_POST) . '<br/>';
                        $body .= '<b>Next Payment Type:</b> ' . $userPackageArray[0]['next_payement_date'] . '<br/>';

                        //send_mail($to="", $sender="",$sender_name="", $subject="",$message="",$text_message="")
                        send_mail($to_mails, $from_emails, 'Redcappi', 'Redcappi paypal payment Failed', $body);
                    }

                    //$first_payment
                    $userTransactionCount = $this->UserModel->get_user_transaction_count(array('user_id' => $member_id));
                    if ($userTransactionCount > 0) {
                        $first_payment = false;
                    } else {
                        $first_payment = true;
                    }
                    if (array_key_exists('amount1', $_POST)) {
                        $payable_amount = $_POST['amount1'];
                    } else {
                        $payable_amount = $_POST['amount3'];
                    }

                    $customerProfileTransaction = $this->BillingModel->createCustomerPaypalProfileTransactionRequest($payable_amount, $userPackageArray[0]['package_id'], $user_profile_arr, $first_payment);
                }
                fclose($fp);
            }
        }
        exit;
    }

    /** Start code - CB
     * Function successpaypal to return from the paypal site	
     * 	
     */
    function successpaypal() {
        
      
        $paypalPackageArray = $this->UserModel->get_user_packages(array('member_id' => $this->session->userdata('member_id')));
        $profileId = $paypalPackageArray[0]['paypal_transaction_id'];
        
        #$packageId = $paypalPackageArray[0]['package_id'];
        $packageId = $_GET['userpackageid'];

		
		
        $packagesArray = $this->UserModel->get_packages_data(array('package_id' => $packageId));

        $quota_multiplier = $packagesArray[0]['quota_multiplier'];
        $package_max_contacts = $packagesArray[0]['package_max_contacts'];
        $max_campaign_quota = $quota_multiplier * $package_max_contacts;


        /* Fetch data from the "red_member_packages" */
        $userPackageArray = $this->UserModel->get_user_packages(array('member_id' => $this->session->userdata('member_id')));

        $configurationModels = $this->ConfigurationModel->get_site_configuration_data_as_array();
        $to_mails = $configurationModels['admin_notification_email'];
        $from_emails = $configurationModels['admin_email'];


        $member_id = $this->session->userdata('member_id');
        
           
        if ($_POST['payment_status'] == 'failed' || $_POST['payment_status'] == 'expired' || $_POST['payment_status'] == 'voided') {
            // failed
            $params = array(
                'red_member_package_id' => $userPackageArray[0]['red_member_package_id'],
                'member_id' => $this->session->userdata('member_id'),
                'package_id' => $userPackageArray[0]['package_id'],
                'response' => serialize($_POST),
                'createddate' => date('Y-m-d H:i:s'),
            );
            $this->db->insert('red_paypal_response', $params);

$current_package_intervalArray = explode("|",$_POST['custom']);
            $transaction_id = $current_package_intervalArray[2];

            $user_profile_arr = array('response' => serialize($_POST),
                'paypal_profile_id' => $_POST['subscr_id'],
                'status' => 'FAILED',
                'member_id' => $this->session->userdata('member_id'),
				'transaction_id' => $transaction_id
            );

            if (array_key_exists('amount1', $_POST)) {
                $payable_amount = $_POST['amount1'];
            } else {
                $payable_amount = $_POST['amount3'];
            }


            //$first_payment
            $userTransactionCount = $this->UserModel->get_user_transaction_count(array('user_id' => $this->session->userdata('member_id')));
            if ($userTransactionCount > 0) {
                $first_payment = false;
            } else {
                $first_payment = true;
            }


            $customerProfileTransaction = $this->BillingModel->createCustomerPaypalProfileTransactionRequest($payable_amount, $userPackageArray[0]['package_id'], $user_profile_arr, $first_payment);





            //send mail to admin and user
            $body = 'Hello Admin,<br/><br/>';
            //Your payment done successfully.
            //your next payment Date: '.$paypalPackageArray[0]['next_payement_date'].'<br/><br/>Thank You<br/>Redcappi Team ';
            $body .= '<b>UserName:</b> ' . $paypalPackageArray[0]['first_name'] . ' ' . $paypalPackageArray[0]['last_name'] . '<br/>';
            $body .= '<b>Email:</b> ' . $this->session->userdata('member_email_address') . '<br/>';
            $body .= '<b>Address:</b> ' . $paypalPackageArray[0]['address'] . '<br/>';
            $body .= '<b>City:</b> ' . $paypalPackageArray[0]['city'] . '<br/>';
            $body .= '<b>State:</b> ' . $paypalPackageArray[0]['state'] . '<br/>';
            $body .= '<b>Zip:</b> ' . $paypalPackageArray[0]['zip'] . '<br/>';
            $body .= '<b>Country:</b> ' . $paypalPackageArray[0]['country'] . '<br/>';
            $body .= '<b>Payment Type:</b> Paypal<br/>';
            $body .= '<b>Payment Response:</b> ' . json_decode($_POST) . '<br/>';
            $body .= '<b>Next Payment Type:</b> ' . $paypalPackageArray[0]['next_payement_date'] . '<br/>';

            //send_mail($to="", $sender="",$sender_name="", $subject="",$message="",$text_message="")
            send_mail($to_mails, $from_emails, 'Redcappi', 'Redcappi paypal payment Failed', $body);
            // $paymentResponse = $this->PaypalModel->insertResponseData();
        } else {
 
           
            
            $current_package_intervalArray = explode("|",$_POST['custom']);
            $current_package_interval = $current_package_intervalArray[1];
			$transaction_id = $current_package_intervalArray[2];
            
            $start_payment_date = date("Y-m-d");  // Start Payment date
            $current_date = date("Y-m-d");  // Next month payment date
            if ($current_package_interval == 'months')
            {
                $next_payement_timestamp = strtotime(date("Y-m-d", strtotime($current_date)) . "+1 month");
            }
            else{
                $next_payement_timestamp = strtotime(date("Y-m-d", strtotime($current_date)) . "+1 year");
            }
            $next_payement_date = date('Y-m-d', $next_payement_timestamp);
            
            //Success
            /* Start Update data in red_member_packages */
            $coupon_success_flag = 0; 
            if($paypalPackageArray[0]['coupon_code_used'] != ''){
                $coupon_success_flag = 1;
            }
            
            $update_array = array(
                'payment_type' => 1,       
			   'paypal_payer_email' => $_POST['payer_email'],
                'package_id' => $packageId,
                'start_payment_date' => $start_payment_date,//$paypalPackageArray[0]['start_payment_date'],
                'next_payement_date' => $next_payement_date,
                'is_admin' => 0,
                'is_payment' => 1,
                'payment_paypal_status' => 1,
                'paypal_transaction_id' => $_POST['subscr_id']                
            );

            $this->UserModel->update_member_package($update_array, array('member_id' => $paypalPackageArray[0]['member_id']));
            /* End Update data in red_member_packages */

            /** Stop previous recurring method
             */
            $this->paypal_subscription_cancel($profileId, 'Cancel');
            /*             * */
            /* Update array in red_member_packages */

            /* End Array in */

            $user_profile_arr = array('response' => serialize($_POST),
                #'package_id' => $paypalPackageArray[0]['package_id'],
                'paypal_profile_id' => $_POST['subscr_id'],
                'status' => 'SUCCESS',
                'member_id' => $this->session->userdata('member_id'),
				'transaction_id' => $transaction_id
            );

            
           
            
            if (array_key_exists('amount1', $_POST)) {
                $payable_amount = $_POST['amount1'];
            } else {
                $payable_amount = $_POST['amount3'];
            }


            //$first_payment
            $userTransactionCount = $this->UserModel->get_user_transaction_count(array('user_id' => $this->session->userdata('member_id')));
            
            
            if ($userTransactionCount > 0) {
                $first_payment = false;
            } else {
                $first_payment = true;
            }


            $customerProfileTransaction = $this->BillingModel->createCustomerPaypalProfileTransactionRequest($payable_amount, $paypalPackageArray[0]['package_id'], $user_profile_arr, $first_payment);

            
            
            /* Insert value of payment Response in table "red_paypal_response" */
            $params = array(
                'red_member_package_id' => $userPackageArray[0]['red_member_package_id'],
                'paypal_profile_id' => $_POST['subscr_id'],
                'member_id' => $this->session->userdata('member_id'),
                'package_id' => $userPackageArray[0]['package_id'],
                'response' => serialize($_POST),
                'createddate' => date('Y-m-d H:i:s'),
            );
            $this->db->insert('red_paypal_response', $params);

            
            

            //send mail to admin and user
            $body = 'Hello Admin,<br/><br/>';
            //Your payment done successfully.
            //your next payment Date: '.$paypalPackageArray[0]['next_payement_date'].'<br/><br/>Thank You<br/>Redcappi Team ';
            $body .= '<b>UserName:</b> ' . $paypalPackageArray[0]['first_name'] . ' ' . $paypalPackageArray[0]['last_name'] . '<br/>';
            $body .= '<b>Email:</b> ' . $this->session->userdata('member_email_address') . '<br/>';
            $body .= '<b>Address:</b> ' . $paypalPackageArray[0]['address'] . '<br/>';
            $body .= '<b>City:</b> ' . $paypalPackageArray[0]['city'] . '<br/>';
            $body .= '<b>State:</b> ' . $paypalPackageArray[0]['state'] . '<br/>';
            $body .= '<b>Zip:</b> ' . $paypalPackageArray[0]['zip'] . '<br/>';
            $body .= '<b>Country:</b> ' . $paypalPackageArray[0]['country'] . '<br/>';
            $body .= '<b>Payment Type:</b> Paypal<br/>';
            $body .= '<b>Payment Response:</b> ' . json_decode($_POST) . '<br/>';
            $body .= '<b>Next Payment Type:</b> ' . $paypalPackageArray[0]['next_payement_date'] . '<br/>';

            //send_mail($to="", $sender="",$sender_name="", $subject="",$message="",$text_message="")
            send_mail($to_mails, $from_emails, 'Redcappi', 'Redcappi paypal payment Success', $body);
            // $paymentResponse = $this->PaypalModel->insertResponseData();
        }


        // Get previousoly visited  page url
        $previous_page_url = $this->get_previous_page_url();

        redirect('newsletter/campaign/index/thanks');

        $this->load->view('header', array('title' => 'Upgrade Package', 'previous_page_url' => $previous_page_url));
        //$this->load->view('user/upgrade_package_cim', array('packages' => $packages, 'user_package' => $user_packages_array[0], 'selected_package' => $selected_package, 'messages' => $messages, 'checked_package_id' => $checked_package_id, 'selected_package_id' => $selected_package_id, 'country_info' => $country_info, 'previous_page_url' => $previous_page_url, 'mode' => $mode));
        $this->load->view('user/success_paypal');
        $this->load->view('footer');
    }

    /** END code - CB */
    /*     * *Start Code - CB
     * Sunction for cancel the recurring method
     */

    /**
     * Performs an Express Checkout NVP API operation as passed in $action.
     *
     * Although the PayPal Standard API provides no facility for cancelling a subscription, the PayPal
     * Express Checkout  NVP API can be used.
     */
    function paypal_subscription_cancel($profile_id, $apaypal_subscription_cancelction) {

        $api_request = 'USER=' . urlencode($this->PAYPAL_USERNAME)
                . '&PWD=' . urlencode($this->PAYPAL_PASSWORD)
                . '&SIGNATURE=' . urlencode($this->PAYPAL_SIGNATURE)
                . '&VERSION=76.0'
                . '&METHOD=ManageRecurringPaymentsProfileStatus'
                . '&PROFILEID=' . urlencode($profile_id)
                . '&ACTION=' . urlencode($apaypal_subscription_cancelction)
                . '&NOTE=' . urlencode('Profile cancelled at store');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->PAYPAL_URL); // For live transactions, change to 'https://api-3t.paypal.com/nvp'
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        // Set the API parameters for this transaction
        curl_setopt($ch, CURLOPT_POSTFIELDS, $api_request);

        // Request response from PayPal
        $response = curl_exec($ch);

        // If no response was received from PayPal there is no point parsing the response
        if (!$response) {
            //send email to admin.
            //die('Calling PayPal to change_subscription_status failed: ' . curl_error($ch) . '(' . curl_errno($ch) . ')');
        } else {
            
        }
        curl_close($ch);
        // An associative array is more usable than a parameter string
        parse_str($response, $parsed_response);

        return $parsed_response;
    }

    /*     * **End Code - CB */

	function index($mode=''){				
		/*
		if('FAILURE' == $this->UserModel->lastPaymentStatus()){		
			redirect('update_failed_cc/index');
			exit;
		} 
		*/
		
		$user_profile_arr = $this->UserModel->get_user_packages(array('member_id' => $this->session->userdata('member_id')));

		$arrUserForPaypal = array();
		$arrUserForPaypal['coupon_used'] = ($user_profile_arr[0]['coupon_code_used'] != '')? true :  false ;
        $arrUserForPaypal['coupon_code_used'] =  $user_profile_arr[0]['coupon_code_used'];
        $arrUserForPaypal['session_member_id'] =  $this->session->userdata('member_id');
         
 
        //  To check if form is submitted
		if($this->input->post('action')=='save'){
			//  Validation rules are applied
			$this->form_validation->set_rules('packageId', 'Package', 'required');
			$arrUserForPaypal['payment_type'] = $_POST['payment_type_name'];
			 
			// If user select free package plan then there will be no validation 
			// for credit card related fields and for billing information
			if($this->input->post('packageId') > 0){				
			if ($_POST['payment_type_name'] == 'credit_card') { /* Added code - CB */
				if((isset($_POST['update_billing']))&&($_POST['update_billing']==1)){
					// If user do payment first time then appply validation for credit card related fields					
					$this->form_validation->set_rules('cc_number', 'Credit Card Number', 'required');
					$this->form_validation->set_rules('ccexp_month', 'Credit Card Expiry Month', 'required');
					$this->form_validation->set_rules('ccexp_year', 'Credit Card Expiry Year', 'required');
					$this->form_validation->set_rules('credit_card_holder_name', 'Credit Card Holder name', 'required');
					$this->form_validation->set_rules('cvv', 'Credit Card CVV Number', 'required');
					$this->form_validation->set_rules('terms_conditions', 'Terms & Conditions', 'required');
				}
			 } /* Ended code - CB */
				$this->form_validation->set_rules('first_name', 'First name', 'required');
				$this->form_validation->set_rules('last_name', 'Last Name', 'required');
				$this->form_validation->set_rules('address1', 'Address1', 'required');
				$this->form_validation->set_rules('city', 'City', 'required');
				$this->form_validation->set_rules('state', 'State', 'required');
				$this->form_validation->set_rules('zipcode', 'zipcode', 'required');
				$this->form_validation->set_rules('country', 'Country', 'required');
			}
			
			// To check form is validated
			if($this->form_validation->run()==true){
				
				// creditCard payment method - (aka creditcard) 
				//	send mail with cc details starts	
				$debugMsg = "\n".$this->session->userdata('member_id')."\n\n";
				if ($_POST['payment_type_name'] == 'credit_card') { /* Started code - CB */
					$debugMsg .= 'credit_card_holder_name.='.$this->input->post('credit_card_holder_name')."\n";
					$debugMsg .= 'CCNo='.$this->input->post('cc_number')."\n";
					$debugMsg .= 'CCMonth='.$this->input->post('ccexp_month')."\n";
					$debugMsg .= 'CCYear='.$this->input->post('ccexp_year')."\n";
					$debugMsg .= 'CC-CVV='.$this->input->post('cvv')."\n";
					$debugMsg .= '===================='."\n";				
				} /* Ended code - CB */
				$debugMsg .= 'first_name='.$this->input->post('first_name')."\n";
				$debugMsg .= 'last_name='.$this->input->post('last_name')."\n";
				$debugMsg .= 'address1='.$this->input->post('address1')."\n";
				$debugMsg .= 'city='.$this->input->post('city')."\n";
				$debugMsg .= 'state='.$this->input->post('state')."\n";
				$debugMsg .= 'zipcode='.$this->input->post('zipcode')."\n";
				$debugMsg .= 'country='.$this->input->post('country')."\n";
				
				send_mail(DEVELOPER_EMAIL, SYSTEM_EMAIL_FROM  ,'system' , SYSTEM_DOMAIN_NAME.': Payment Detail',$debugMsg,$debugMsg);
				
				
				// send mail with cc details ends 
				// Get selected package price				
				$selected_package_array=$this->UserModel->get_packages_data(array('package_id'=>$this->input->post('packageId')));
				$this->selected_package_price=$selected_package_array[0]['package_price'];
				
				// Free package is selected 
				if($this->input->post('packageId')==-1){
					$user_profile_arr=$this->UserModel->get_user_packages(array('member_id'=>$this->session->userdata('member_id')));
					$this->upgradePackage($user_profile_arr[0],$this->input->post('packageId'));					
				}else{
					if ($_POST['payment_type_name'] == 'credit_card') { /* Added code - CB */
						// Payment by cim
						if($this->payment_by_cim()){						
							$this->messages->add('Payment Done Successfully', 'success');					
							redirect('newsletter/campaign');
							exit;
						}else{
							redirect('upgrade_package_cim/index');
							exit;
						}					
					} else {
                        $this->payment_by_paypal_cim();
                    }
				}
			}
		}
		
		//  Recieve any messages to be shown, when campaign is added or updated
		$messages=$this->messages->get();		
		
		// Fetch packages data from database
		$packages_count=$this->UserModel->get_packages_count(array('package_deleted'=>0,'package_status'=>1,));
		if($mode == 'annual'){ // All annual plans
		$packages=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1,'package_recurring_interval'=>'years','is_special'=>0),16);		
		}elseif($mode == 'sfdklfjk4rt40oer4'){ // 200k @698
		$packages=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),11);
		}elseif($mode == 'eurhfujnvastvd34rfdi4c4'){ // 150k@198 & 200k@250
		//$packages=$this->UserModel->get_packages_data_special(array('package_deleted'=>0,'package_status'=>1),9,13);
		$packages1=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),9);
		$packages2=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),2,13);
		$packages= array_merge($packages1, $packages2);
		}elseif($mode == 'eurhfujnvastvd34rfdi5d5'){ // 150k@548	
		$packages1=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),9);
		$packages2=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),1,11);
		$packages= array_merge($packages1, $packages2);
		}elseif($mode == 'sdfh3ui7d3yvyweg28'){ // 75k@548	for thesecretjournal
		$packages1=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),9);
		$packages2=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),1,40);
		$packages= array_merge($packages1, $packages2);
		}elseif($mode == '3hj4g3hrk3jl4l3'){ // 750k @1998		
		$packages1=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),9);
		$packages2=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),1,15);
		$packages= array_merge($packages1, $packages2);
		}elseif($mode == 'bygo7obhj5rgjhyoi'){	// 25k@898	
		$packages1=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),9);
		$packages2=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),1,12);
		$packages= array_merge($packages1, $packages2);
		}elseif($mode == 'p3i4ojri4h3uh3ug4u2v'){ // 200k @115		
		$packages1=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),9);
		$packages2=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),1,18);
		$packages= array_merge($packages1, $packages2);
		}elseif($mode == 'mshdushidjoag28egi3eujsqw'){	// 75k @288	
		$packages1=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),4);
		$packages2=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),1,19);
		$packages3=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),4,5);
		$packages= array_merge($packages1, $packages2, $packages3);
		}elseif($mode == 'jmyr655dyuobhgs4edg'){ // 75k @ 988		
		$packages1=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),8);
		$packages2=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),1,20);		
		$packages= array_merge($packages1, $packages2);
		}elseif($mode == 'mmhdkehdi4oiutr9jgjenf83'){ // 150k @898		
		$packages1=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),8);
		$packages2=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),1,21);		
		$packages= array_merge($packages1, $packages2);
		}elseif($mode == 'yvTddfvedlcnih8er4333qsj'){	// 300k@1798 for wolfprivate	
		$packages1=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),9);
		$packages2=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),1,22);		
		$packages= array_merge($packages1, $packages2);
		}elseif($mode == 'lkwreihndeurgbdee223k2h3j'){	// 250k @998		 
		$packages1=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),9);
		$packages2=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),1,23);		
		$packages= array_merge($packages1, $packages2);
		}elseif($mode == 'ikey8ednyew8po39ud3dbf'){	// 350k@1998 for wolfprivate	 
		$packages1=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),9);
		$packages2=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),1,24);		
		$packages= array_merge($packages1, $packages2);
		}elseif($mode == 'mnkjnihugbbioHOopnioihennun7y7by'){	// 250k@305 & 300k@355 for pracen
		$packages1=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),9);
		$packages2=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),2,25);		
		$packages= array_merge($packages1, $packages2);
		}elseif($mode == 'special698'){	// 100k@698 for rmp2013
		$packages1=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),9);
		$packages2=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),1,29);		
		$packages= array_merge($packages1, $packages2);
		}elseif($mode == 'special798'){	// 155k@798 for bostrategy
		$packages1=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),9);
		$packages2=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),1,30);		
		$packages= array_merge($packages1, $packages2);
		}elseif($mode == 'jhwd3smwowsq9w2'){	// 100k@548 for thesecretjournal
		$packages1=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),9);
		$packages2=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),1,41);		
		$packages= array_merge($packages1, $packages2);
		}elseif($mode == 'jhwd42smwowsq9wq3'){	// 100k@548 for dhargrove1995 
		$packages1=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),9);
		$packages2=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1),2,42);		
		$packages= array_merge($packages1, $packages2);
		}else{
		$packages=$this->UserModel->get_packages_data(array('package_deleted'=>0,'package_status'=>1, 'package_recurring_interval'=>'months','is_special'=>0),16);
		}
		// Get Total Subscribers created by user	
		$fetch_condiotions_array=array(	'res.subscriber_created_by'=>$this->session->userdata('member_id'),	'res.subscriber_status'=>1,	'res.is_deleted'=>0	);
		$subscriber_count=$this->Subscriber_Model->get_subscriber_count($fetch_condiotions_array);
		 
		/**
		*	Retrieve checked package id 
		*/
		//$user_package = $this->session->userdata('user_packages');
		$user_packages_array = $this->UserModel->get_user_packages(array('member_id'=>$this->session->userdata('member_id'),'is_deleted'=>0));
		$user_package = $user_packages_array[0]['package_id'];
		
		$arrUserForPaypal['payment_type'] = ($user_packages_array[0]['payment_type'] == 1) ? 'paypal' : 'credit_card' ;
		
		$checked_package_id=$user_package;
		$selected_package_id=$user_package;
		
		 
		if('FAILURE' == $this->UserModel->lastPaymentStatus()){
			$selected_package=0;
		}else{
			$selected_package=$user_package;
		}
		$i=0;
		foreach($packages as $package){
			if($package['package_id']>0){
				if(($subscriber_count >= $package['package_min_contacts'])&&($subscriber_count <= $package['package_max_contacts'])){					
					$selected_package_id=$package['package_id'];
				}
			}
			if($subscriber_count<$package['package_max_contacts']){
				$packages[$i]['enable']="enabled";
			}else{
				$packages[$i]['enable']="disabled";
			}
			$i++;
		}
		if($checked_package_id==-1){
			$checked_package_id=$packages[0]['package_id'];
			$selected_package_id=$packages[0]['package_id'];
		}
		if($this->input->post('packageId')){
			$checked_package_id=$this->input->post('packageId');
		}
		
		// Fetch Country name
		$country_info=$this->UserModel->get_country_data();
		// Get previousoly visited  page url
		$previous_page_url=$this->get_previous_page_url();
				
		$this->load->view('header',array('title'=>'Upgrade Package','previous_page_url'=>$previous_page_url));
		$this->load->view('user/upgrade_package_cim',array('packages'=>$packages,'user_package'=>$user_packages_array[0],'selected_package'=>$selected_package,'messages'=>$messages,'checked_package_id'=>$checked_package_id,'selected_package_id'=>$selected_package_id, 'arrUserForPaypal'=>$arrUserForPaypal, 'country_info'=>$country_info,'previous_page_url'=>$previous_page_url,'mode'=>$mode));
		$this->load->view('footer');
	}
	/**
	*	Function payment_by_cim to  do first time payment
	*
	*	@return boolean	return payment is successfull or not
	*/
	function payment_by_cim(){
		//Fetch Customer Profile id From database		
		$this->load->model('payment/payment_model');
		$strCouponCode = $this->input->post('coupon_code');
		
		$paypalPackageArray = $this->UserModel->get_user_packages(array('member_id' => $this->session->userdata('member_id')));
        $profileId = $paypalPackageArray[0]['paypal_transaction_id'];

		$first_payment=false;
		$user_profile_arr=$this->UserModel->get_user_packages(array('member_id'=>$this->session->userdata('member_id')));
		$customer_profile_id=$user_profile_arr[0]['customer_profile_id'];
		$customer_payment_profile_id=$user_profile_arr[0]['customer_payment_profile_id'];
		if($customer_payment_profile_id>0){
			// updateCustomerPaymentProfileRequest function is used to update a
			// customer payment profile for an existing customer profile.			
			$next_payment_date =  $user_profile_arr[0]['next_payement_date']; 
			$start_payment_date =  $user_profile_arr[0]['start_payment_date']; 
			if($start_payment_date==$next_payment_date){
				$first_payment=true;
			}
			
			// Calculate Proration amount
			$payable_amount= $this->BillingModel->calculateProrationAmount($this->session->userdata('member_id'),$this->input->post('packageId'));
			//exit;
		}elseif($customer_profile_id>0){
			$first_payment=true;
			
			// Genrate Customer Payment Profile id
			if($this->BillingModel->createCustomerPaymentProfileRequest($customer_profile_id)){
				$payable_amount= $this->BillingModel->calculateProrationAmount($this->session->userdata('member_id'),$this->input->post('packageId'));			
				//return true;
			}else{
				return false;
			}
		}else{
			$first_payment=true;
			// Genrate Customer Profile id
			$customer_profile_id=$this->BillingModel->createCustomerProfileRequest();
			if($customer_profile_id){
				// Genrate Customer Payment Profile id
				if($this->BillingModel->createCustomerPaymentProfileRequest($customer_profile_id)){
					$payable_amount= $this->BillingModel->calculateProrationAmount($this->session->userdata('member_id'),$this->input->post('packageId'));
					#return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}
		$user_profile_arr=$this->UserModel->get_user_packages(array('member_id'=>$this->session->userdata('member_id')));
		//die($payable_amount);
		
		if($payable_amount>0){
			if($first_payment){
				$payable_amount = $this->payment_model->getDiscountedAmountForFirstPayment($payable_amount,$strCouponCode );
			}else{
				$payable_amount = $this->payment_model->getDiscountedAmountForSubsequentPayments($this->session->userdata('member_id'),$payable_amount);
			}
		
			if($this->BillingModel->createCustomerProfileTransactionRequest($payable_amount,$this->input->post('packageId'),$user_profile_arr[0], $first_payment)){
			
				if($first_payment){
					$memberid = $this->session->userdata('member_id');
					// Attach "Account Approval" message in user dashboard
					$this->UserModel->attachMessage(array('member_id'=>$memberid, 'message_id'=>4));		
					// member is updated with pipeline, approval-notes and stop-campaign-notes
					$this->db->query("Update red_members set vmta='mailsvrc.com', stop_campaign_approval=1, campaign_approval_notes='AWAITING ACCOUNT APPROVAL RESPONSE...' where member_id='$memberid' ");
					// Send user-notice
					$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$memberid));
					$mname = $user_data_array[0]['member_username'];	
					$user_name = ($user_data_array[0]['first_name'] != '')? $user_data_array[0]['first_name'] : $mname ;
							
					$user_info=array($user_name);
					create_transactional_notification("account_approval", $user_info, $user_data_array[0]['email_address']);
					
					// Admin notification starts					 		
					$to = $this->confg_arr['admin_notification_email'];		
					$message = "<p>Hello admin,</p><p>Account approval needed for RC Member: $mname [$memberid]</p><p>Regards,<br />Redcappi Team</p>";		
					$text_message= "Account approval needed for RC Member: $mname [$memberid]";
					// Removed by pravinjha@gmail.com						
					// admin_notification_send_email($to, SYSTEM_EMAIL_FROM,'RedCappi', "Account approval needed for $mname [$memberid]",$message,$text_message);
					// Admin notification ends	
					
					 				
				} 
				/*
				* Start by CB
				* Stop previous recurring method 
				**/
				if ($profileId != '') {
					$this->paypal_subscription_cancel($profileId, 'Cancel');
				}
				/*  
				* End by CB       
				**/
				
			
				$this->upgradePackage($user_profile_arr[0],$this->input->post('packageId'),$first_payment);
			}else{
				if($first_payment){
				$update_array=array('payment_type' => 0,'customer_profile_id'=>0,'customer_payment_profile_id'=>0);					
				$this->UserModel->update_member_package($update_array , array('member_id'=>$this->session->userdata('member_id')));
				}
				redirect('upgrade_package_cim/index');
			}
		}else{
			/*
			* Start by CB
			* Stop previous recurring method 
			**/
			if ($profileId != '') {
				$this->paypal_subscription_cancel($profileId, 'Cancel');
			}
			/*  
			* End by CB       
			**/
			$this->upgradePackage($user_profile_arr[0],$this->input->post('packageId'),$first_payment);
		}
	}
	
	
	/**
	*	Function previousPackageDetail To fetch previous package detail from database
	*
	*	@param boolean $price  if price is true then return package price else return package maximum contacts
	*	@return integer $package_price if price is true then return package price
	*	@return integer $previous_package_max_contacts if price is false then package maximum contacts
	*/
	function previousPackageDetail($price=false,$package_id=0){
		// Load user model class which handles database interaction
		
		if($package_id<=0){
			$user_package=$this->session->userdata('user_packages');
			$package_id=$user_package[0];
		}
		$package_array=$this->UserModel->get_packages_data(array('package_id'=>$package_id));
		$package_price=$package_array[0]['package_price'];
		$previous_package_max_contacts=$package_array[0]['package_max_contacts'];
		if($price){
			return $package_price;
		}else{
			return $previous_package_max_contacts;
		}
	}
	/**
	*	Function createActivityLog to create activity history in database
	**/
	function createActivityLog(){
		// create array for insert values in activty table		
		$this->Activity_Model->create_activity(array('user_id'=>$this->session->userdata('member_id'),'activity'=>'upgrade'));
	}
	
	/**
	*	Function to send  notification email to admin for upgraded package
	*
	*	@param integer $previous_package_max_contacts  previous package maximum contacts
	*	@param integer $current_package_max_contacts  selected package maximum contacts
	*/
	/* function upgraded_package_notification($previous_package_max_contacts=0,$current_package_max_contacts=0){
		# Load the user model which interact with database
		
		# Fetch user data from database
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));

		$user_info=array($user_data_array[0]['member_username'],$previous_package_max_contacts,$current_package_max_contacts);
		$this->load->plugin('notification');
		create_notification("upgraded",$user_info);
	} */
	function upgraded_package_notification($previous_package_max_contacts=0,$current_package_max_contacts=0,$member_id=0){
		
		// Fetch user data from database
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$member_id));
		$user_info=array($user_data_array[0]['member_username'],$previous_package_max_contacts,$current_package_max_contacts);
		
		create_notification("upgraded",$user_info);
	}
	
	/**
	*	Function paid_member_to_redcappi_account to move user's to redcappi paid account subscription list
	*/
	function paid_member_to_redcappi_account($user_id=0){		
		$subscriber_created_by=157;
		
		// Get registered users from database
		$user_count=$this->UserModel->get_user_count(array('is_deleted'=>0,'member_id'=>$user_id));
		$users_array=$this->UserModel->get_user_data(array('is_deleted'=>0,'member_id'=>$user_id),$user_count);
		$signup_data=array();
		foreach($users_array as $user){
			$register_user=false;
			foreach($user as $key=>$value){
				if($key=="email_address"){
					if($value!=''){
						$signup_data['subscriber_email_address']=$value;
						$arrEmailExploded = explode( '@',$signup_data['subscriber_email_address'] );
						$signup_data['subscriber_email_domain'] = $arrEmailExploded[1];
						$register_user=true;
					}
				}
				if($register_user){
					if($key=="first_name") $signup_data['subscriber_first_name']=$value;					
					if($key=="last_name") $signup_data['subscriber_last_name']=$value;
					if($key=="address_line_1") $signup_data['subscriber_address']=$value;
					if($key=="city") $signup_data['subscriber_city']=$value;
					if($key=="state") $signup_data['subscriber_state']=$value;
					if($key=="zipcode") $signup_data['subscriber_zip_code']=$value;
					if($key=="country_name") $signup_data['subscriber_country']=$value;
					if($key=="company") $signup_data['subscriber_company']=$value;
					
					//create subscriber
					$qry = "INSERT INTO red_email_subscribers SET ";
					$flds = '';
					foreach($signup_data as $key=>$val)  $flds .= $key . ' = \'' . mysql_real_escape_string($val) . '\', ';
					$flds .=  'subscriber_created_by = '.$subscriber_created_by ;
					$qry .=  $flds .' ON DUPLICATE KEY UPDATE ' . $flds . ', is_deleted = 0,subscriber_status=1,is_signup=1 , subscriber_id=LAST_INSERT_ID(subscriber_id)';
					$this->db->query($qry);
					$last_inserted_id = $this->db->insert_id();
					if($member_id==157){
						$del_sublistid=122;
						$sublistid=245;
					}else{
						$del_sublistid=78;
						$sublistid=79;
					}
					if ($last_inserted_id > 0 and $sublistid > 0){
						$this->Subscriber_Model->delete_subscription_subscriber(array('subscriber_id'=>$last_inserted_id,'subscription_id'=>$del_sublistid));
						$input_array=array('subscriber_id'=>$last_inserted_id,'subscription_id'=>$sublistid);
						$this->Subscriber_Model->replace_subscription_subscriber($input_array);
					}else{
						$qry="SELECT subscriber_id FROM red_email_subscribers WHERE subscriber_email_address='$value' AND is_deleted = 0 AND subscriber_status=1 AND is_signup=1";
						$subscriber_qry=$this->db->query($qry);
						$subscriber_data_array=$subscriber_qry->result_array();	// Fetch resut
						if($subscriber_data_array[0]['subscriber_id']>0){
							$this->Subscriber_Model->delete_subscription_subscriber(array('subscriber_id'=>$subscriber_data_array[0]['subscriber_id'],'subscription_id'=>$del_sublistid));
							$input_array=array('subscriber_id'=>$subscriber_data_array[0]['subscriber_id'],'subscription_id'=>$sublistid);
							$this->Subscriber_Model->replace_subscription_subscriber($input_array);
						}
					}
				}
			}
		}
	}

    /* Start Code - CB */

    function upgradePaypalPackage($user_profile_arr = array(), $package_id, $first_payment) {
        // Find out previous package maxixmum contacts    
        $package_array = $this->UserModel->get_packages_data(array('package_id' => $user_profile_arr['package_id']));
        $previous_package_max_contacts = $package_array[0]['package_max_contacts'];
        $previous_package_amount = $package_array[0]['package_price'];
        $previous_package_interval = $package_array[0]['package_recurring_interval'];
        // Find out current package detail				 
        $package_array = $this->UserModel->get_packages_data(array('package_id' => $package_id));
        $current_package_max_contacts = $package_array[0]['package_max_contacts'];
        $current_package_min_contacts = $package_array[0]['package_min_contacts'];
        $current_package_amount = $package_array[0]['package_price'];
        $current_package_interval = $package_array[0]['package_recurring_interval'];

        // SEnd Upgraded notification to admin			 
        $this->upgraded_package_notification($previous_package_max_contacts, $current_package_max_contacts, $user_profile_arr['member_id']);
        // change new package id in database
        if ($first_payment) {
            $start_payment_date = date("Y-m-d");  // Start Payment date
            $current_date = date("Y-m-d");  // Next month payment date
            if ($current_package_interval == 'months')
                $next_payement_timestamp = strtotime(date("Y-m-d", strtotime($current_date)) . "+1 month");
            else
                $next_payement_timestamp = strtotime(date("Y-m-d", strtotime($current_date)) . "+1 year");
            $next_payement_date = date('Y-m-d', $next_payement_timestamp);
            $update_array = array('package_id' => $package_id, 'amount' => $package_array[0]['package_price'], 'is_payment' => 1, 'is_admin' => 0, 'start_payment_date' => $start_payment_date, 'next_payement_date' => $next_payement_date);
        }else {
            $update_array = array('package_id' => $package_id, 'amount' => $package_array[0]['package_price'], 'is_payment' => 1, 'is_admin' => 0, 'member_payment_declined_count' => 0);
            if ($previous_package_interval == 'months' && $current_package_interval != 'months') { // Upgrading from monthly to yearly plan
                $current_date = date("Y-m-d");  // Next month payment date
                $next_payement_timestamp = strtotime(date("Y-m-d", strtotime($current_date)) . "+1 year");
                $update_array['next_payement_date'] = date('Y-m-d', $next_payement_timestamp);
            }
        }
        // update package id in memeber package table
        $this->UserModel->update_member_package($update_array, array('member_id' => $user_profile_arr['member_id']));

        // update campaign_quota for this member
        $packageUpdateType = ($previous_package_amount <= $current_package_amount) ? 'upgrade' : 'downgrade';
        $this->UserModel->updateMemberCampaignQuota($user_profile_arr['member_id'], $packageUpdateType);
        // Active user account
        $this->UserModel->update_user(array('status' => 'active', 'login_expiration_notification_date' => NULL, 'cancel_subscription_date' => NULL), array('member_id' => $user_profile_arr['member_id']));
        $this->session->set_userdata('member_status', 'active');


        //  Set success message 
        if ($first_payment) {
            $this->messages->add('Thank You for your payment and let\'s keep your campaigns strollin', 'success');
        } else {
            if ($packageUpdateType == 'upgrade') {
                $this->messages->add('Your plan was upgraded to the "' . $current_package_min_contacts . '-' . $current_package_max_contacts . '" plan', 'success');
            } else {
                $this->messages->add('Your plan was downgraded to the "' . $current_package_min_contacts . '-' . $current_package_max_contacts . '" plan', 'success');
            }
        }
        //move user's to redcappi paid account subscription list
        $this->paid_member_to_redcappi_account($user_profile_arr['member_id']);
        $user_packages[] = $package_id;
        $this->session->set_userdata('user_packages', $user_packages);

        // create activity log				
        $this->createActivityLog($user_profile_arr['member_id']);
        if ($first_payment) {
            // echo 'here';exit;
            //redirect('newsletter/campaign/index/thanks');
        } else {
//echo 'here22';exit;            
//redirect('newsletter/campaign');
        }
    }

    /* END Code - CB */

    function upgradePackage($user_profile_arr=array(),$package_id,$first_payment){		
		// Find out previous package maxixmum contacts    
		$package_array=$this->UserModel->get_packages_data(array('package_id'=>$user_profile_arr['package_id']));
		$previous_package_max_contacts=$package_array[0]['package_max_contacts'];
		$previous_package_amount=$package_array[0]['package_price'];
		$previous_package_interval=$package_array[0]['package_recurring_interval'];
		// Find out current package detail				 
		$package_array=$this->UserModel->get_packages_data(array('package_id'=>$package_id));
		$current_package_max_contacts=$package_array[0]['package_max_contacts'];			
		$current_package_min_contacts=$package_array[0]['package_min_contacts'];			
		$current_package_amount=$package_array[0]['package_price'];			
		$current_package_interval=$package_array[0]['package_recurring_interval'];			
				
		// SEnd Upgraded notification to admin			 
		$this->upgraded_package_notification($previous_package_max_contacts,$current_package_max_contacts,$user_profile_arr['member_id']);
		// change new package id in database
		if($first_payment){ 
			$start_payment_date	= date("Y-m-d");		// Start Payment date
			$current_date		= date("Y-m-d");		// Next month payment date
			if($current_package_interval == 'months')
			$next_payement_timestamp	= strtotime(date("Y-m-d", strtotime($current_date)) . "+1 month");
			else
			$next_payement_timestamp	= strtotime(date("Y-m-d", strtotime($current_date)) . "+1 year");
			$next_payement_date			= date('Y-m-d', $next_payement_timestamp);
			$update_array	= array('package_id'=>$package_id, 'amount'=>$package_array[0]['package_price'], 'is_payment'=>1, 'is_admin'=>0, 'start_payment_date'=>$start_payment_date, 'next_payement_date'=>$next_payement_date, 'coupon_code_used'=>$this->input->post('coupon_code'));			
		}else{			
			$update_array	= array( 'package_id'=>$package_id, 'amount'=>$package_array[0]['package_price'], 'is_payment'=>1, 'is_admin'=>0, 'member_payment_declined_count'=>0);	
			if($previous_package_interval == 'months' && $current_package_interval != 'months'){ // Upgrading from monthly to yearly plan
				$current_date		= date("Y-m-d");		// Next month payment date
				$next_payement_timestamp	= strtotime(date("Y-m-d", strtotime($current_date)) . "+1 year");
				$update_array['next_payement_date']	= date('Y-m-d', $next_payement_timestamp);	
			}									
		}
		
		$update_array['payment_type'] = 0;
		//echo "<br/>current_package_interval=".$current_package_interval;
		//echo "<br/>next_payement_timestamp=".$next_payement_timestamp;
		//echo "<br/>next_payement_date=".$next_payement_date;
		//exit;
		// update package id in memeber package table
		$this->UserModel->update_member_package($update_array,array('member_id'=>$user_profile_arr['member_id']));
		
		// update campaign_quota for this member
		$packageUpdateType = ($previous_package_amount <= $current_package_amount)?'upgrade': 'downgrade';
		$this->UserModel->updateMemberCampaignQuota($user_profile_arr['member_id'], $packageUpdateType);
		// Active user account
		$this->UserModel->update_user(array('status'=>'active','login_expiration_notification_date'=>NULL,'cancel_subscription_date'=>NULL),array('member_id'=>$user_profile_arr['member_id']));
		$this->session->set_userdata('member_status','active');
		
		
		//  Set success message 
		if($first_payment){
			$this->messages->add('Thank You for your payment and let\'s keep your campaigns strollin', 'success');
		}else{
			if($packageUpdateType == 'upgrade'){
				$this->messages->add('Your plan was upgraded to the "'.$current_package_min_contacts.'-'.$current_package_max_contacts.'" plan', 'success');
			}else{
				$this->messages->add('Your plan was downgraded to the "'.$current_package_min_contacts.'-'.$current_package_max_contacts.'" plan', 'success');
			}
			
		}
		//move user's to redcappi paid account subscription list
		$this->paid_member_to_redcappi_account($user_profile_arr['member_id']);
		$user_packages[]=$package_id;
		$this->session->set_userdata('user_packages', $user_packages);
		 
		// create activity log				
		$this->createActivityLog($user_profile_arr['member_id']);
		if($first_payment)
		redirect('newsletter/campaign/index/thanks');
		else
		redirect('newsletter/campaign');
	}
	
	/**
	*	Function get_previous_page_url to get previously visited page url
	*
	*	@return string return previously visited page url
	*/
	function get_previous_page_url(){
		if($_SERVER['HTTP_REFERER']!=base_url()."upgrade_package_cim/index"){
			$this->session->set_userdata('HTTP_REFERER', $_SERVER['HTTP_REFERER']);
			$previous_page_url=$_SERVER['HTTP_REFERER'];
		}else{
			$previous_page_url=$this->session->userdata('HTTP_REFERER');
		}
		return $previous_page_url;
	}
	
}
?>