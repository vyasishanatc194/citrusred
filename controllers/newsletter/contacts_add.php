<?php
/**
  *	Controller class for contacts_add 
  *	It have controller functions for contacts import/add.
 */
class Contacts_add extends CI_Controller
{
	/**
	  *	Contructor for controller.
	  *	It checks user session and redirects user if not logged in
	 */
	function __construct()
    {
        parent::__construct();
		# check via common model
		if(!$this->is_authorized->check_user())		 
			redirect('user/index');
			
		# Create user's folders
		$this->is_authorized->createUserFiles();
		
		$this->load->model('newsletter/subscription_Model');		
		$this->load->model('UserModel');		
    }
	
	 
	/**
	 * Function index
	 *
	 * 'Index' controller function for listing of subscriptions.
	 *
	 * @param (int) (subscription_id)  for displaying subscription selectable(blue color)  in view of subscription list
	 */
	
	#function index(){
	#	$this->display();
	#}
	function index($subscription_id=0,$scroll=0,$action="")
	{
		
		//	Collect subscription id
		//Protecting MySQL from query string sql injection Attacks
		if(is_numeric($subscription_id)){
			$subscription_id = $subscription_id;
		}else{
			$subscription_id=0;
		}
		$subscription_data['subscription_first_id']=$subscription_id;
		// Recieve any messages to be shown, when subscription is added or updated
		$messages=$this->messages->get();
		// Get Maximum Contacts according to session package id	
		
	 
	 
		$fetch_condiotions_array=array('subscription_created_by'=>$this->session->userdata('member_id'),'is_deleted'=>0	);
		 
		// Fetches subscription data from database		
		$subscription_data['select_subscriptions']=$this->subscription_Model->get_subscription_data($fetch_condiotions_array);		 
		/**
		 * Fetch user data 
		 */
		$user_data_array=$this->UserModel->get_user_data(array('member_id'=>$this->session->userdata('member_id')));
		$subscription_data['extra']=$user_data_array[0];
		//Loads header, subscription and footer view.
		$this->load->view('header',array('title'=>'Add/Import Contacts'));
		$this->load->view('contacts/contacts_add',$subscription_data);
		$this->load->view('footer');
	}
	 
 
}
?>