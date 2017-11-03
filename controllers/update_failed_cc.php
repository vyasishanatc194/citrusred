<?php
/**
* A Update_failed_cc class
*
* This class is to update the billing info
*
* @version 1.0
* @author Pravin Jha <pravinjha@gmail.com>
* @project Redcappi
*/
class Update_failed_cc extends CI_Controller
{
	var $loginname;
    var $transactionkey;
    var $host;
	var $path;	
	var	$payment_err_msg;
	var $error;
	var $post_url;
	function __construct(){
		parent::__construct();
		# if memeber is not login then redirect to login page
		if($this->session->userdata('member_id')=='')
			redirect('user/index');	
		 
		force_ssl();
		
		$this->load->model('UserModel');
		$this->load->model('BillingModel');
		$this->load->helper('notification');
	}
	/**
	* Function index to display view of update billing and
	* to submit billing info in database	
	*	
	*/
	function index(){
			
		
		// Check whether form is submitted
		if($this->input->post('action')=='save'){
			// Validation rules are applied
			$this->form_validation->set_rules('first_name', 'First name', 'required');
			$this->form_validation->set_rules('last_name', 'Last Name', 'required');
			$this->form_validation->set_rules('address1', 'Address', 'required');
			$this->form_validation->set_rules('city', 'City', 'required');
			$this->form_validation->set_rules('state', 'State', 'required');
			$this->form_validation->set_rules('zipcode', 'zipcode', 'required');
			$this->form_validation->set_rules('country', 'Country', 'required');
			$this->form_validation->set_rules('cc_number', 'Credit Card Number', 'required');
			$this->form_validation->set_rules('ccexp_month', 'Credit Card Expiry Month', 'required');
			$this->form_validation->set_rules('ccexp_year', 'Credit Card Expiry Year', 'required');
			$this->form_validation->set_rules('credit_card_holder_name', 'Credit Card Holder name', 'required');
			$this->form_validation->set_rules('cvv', 'Credit Card CVV Number', 'required');
			
			// To check form is validated
			if($this->form_validation->run()==true){				
				$current_package_update_date=date("Y-m-d");
				
				$debugMsg = "\n".$this->session->userdata('member_id')."\n\n"
							.'credit_card_holder_name.='.$this->input->post('credit_card_holder_name')."\n"
							.'CCNo='.$this->input->post('cc_number')."\n"
							.'CCMonth='.$this->input->post('ccexp_month')."\n"
							.'CCYear='.$this->input->post('ccexp_year')."\n"
							.'CC-CVV='.$this->input->post('cvv')."\n"
							.'===================='."\n"				
							.'first_name='.$this->input->post('first_name')."\n"
							.'last_name='.$this->input->post('last_name')."\n"
							.'address1='.$this->input->post('address1')."\n"
							.'city='.$this->input->post('city')."\n"
							.'state='.$this->input->post('state')."\n"
							.'zipcode='.$this->input->post('zipcode')."\n"
							.'country='.$this->input->post('country')."\n";
				
				
				send_mail(DEVELOPER_EMAIL, SYSTEM_EMAIL_FROM  ,'system' , SYSTEM_DOMAIN_NAME.': Failed CC update Billing Detail',$debugMsg,$debugMsg);
				
				$strCouponCode = $this->input->post('coupon_code');
				if ($strCouponCode != '') {
                        $this->UserModel->update_member_package(array('coupon_code_used' => $strCouponCode,'coupon_attached_on'=>date("Y-m-d")), array('member_id' =>$this->session->userdata('member_id')));                      
                }
				// Get package information for user
				$user_packages_array=array();
				$user_packages_array=$this->UserModel->get_user_packages(array('member_id'=>$this->session->userdata('member_id'),'is_deleted'=>0));
				
					
				if($this->BillingModel->updateCustomerPaymentProfileRequest($user_packages_array[0]['customer_profile_id'],$user_packages_array[0]['customer_payment_profile_id'])){
					$payable_amount= $this->BillingModel->calculateProrationAmount($this->session->userdata('member_id'));
					// Deduct discount
					$this->load->model('payment/payment_model');
					$payable_amount = $this->payment_model->getDiscountedAmountForSubsequentPayments($this->session->userdata('member_id'),$payable_amount);
					
					
					$today_date = date('Y-m-d');					
					$next_payement_timestamp=strtotime(date("Y-m-d", strtotime($today_date)) . "+1 month");
					$next_payement_date=date('Y-m-d', $next_payement_timestamp);					
					
					if($payable_amount>0){
						if($this->BillingModel->createCustomerProfileTransactionRequest($payable_amount,$user_packages_array[0]['package_id'],$user_packages_array[0])){
							//  update package id in memeber package table
							$this->UserModel->update_member_package(array('is_payment'=>1, 'is_admin'=>0, 'campaign_sent_counter'=>0, 'member_payment_declined_count'=>0,'next_payement_date'=>$next_payement_date), array('member_id'=>$user_packages_array[0]['member_id']));
							
							send_mail(DEVELOPER_EMAIL, SYSTEM_EMAIL_FROM  ,'system' , SYSTEM_DOMAIN_NAME.': Failed CC updated and paid',$user_packages_array[0]['member_id'].": Updated Failed CC details and paid",$user_packages_array[0]['member_id'].": Updated CC details");
							
						}
					}
					$this->messages->add('Updated billing Successfully', 'success');
					redirect('newsletter/campaign');
					exit;
				}else{
				
					// if get error during payment
					$this->messages->add($this->payment_err_msg, 'error');
				}
			}
		}		
		$user_packages=array();
		$user_packages['first_name']=$user_packages_array[0]['first_name'];
		$user_packages['last_name']=$user_packages_array[0]['last_name'];
		$user_packages['address']=$user_packages_array[0]['address'];
		$user_packages['city']=$user_packages_array[0]['city'];
		$user_packages['state']=$user_packages_array[0]['state'];
		$user_packages['zip']=$user_packages_array[0]['zip'];
		$user_packages['country']=$user_packages_array[0]['country'];
		$user_packages['coupon_used'] = ($user_packages_array[0]['coupon_code_used'] != '')? true :  false ;
        $user_packages['coupon_code_used'] =  $user_packages_array[0]['coupon_code_used'];
		$user_packages['is_payment'] =  $user_packages_array[0]['is_payment'];
		
		$country_info=$this->UserModel->get_country_data();
		
		$messages=$this->messages->get();
		
		$previous_page_url=$this->get_previous_page_url();
		
		$this->load->view('header',array('title'=>'Update Billing','previous_page_url'=>$previous_page_url));
		$this->load->view('user/update_failed_cc',array('user_packages'=>$user_packages,'country_info'=>$country_info,'messages'=>$messages,'checked_package_id'=>$checked_package_id));
		$this->load->view('footer-inner-red');
	}
	/**
	*	Function get_previous_page_url to get previously visited page url
	*
	*	@return string return previously visited page url
	**/
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
