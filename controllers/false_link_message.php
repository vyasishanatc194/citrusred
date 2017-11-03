<?php
/**
* A Unsubscribe_mail class
*
* This class is for unsubscriber mail
*
* @version 1.0
* @author Pravin Jha <pravinjha@gmail.com>
* @project Redcappi
*/
class False_link_message extends CI_Controller
{
	/**
	*	Contructor for controller.
	*	It checks user session and redirects user if not logged in
	*/
	function __construct(){
        parent::__construct(); 
    }
		

	
	function index(){
		$msg= '<h3>This email was sent by someone or company *not* associated with our system and they have put our link in the spam messages they are sending (spoofing our system), to help their emails get through spam filters.</h3>';
		$msg .= '<h3>We apologize for any inconvenience, and because this email did NOT actually come from our system, there is nothing much we can do from our end.</h3>';
		$msg .= '<h3>Feel free to forward us <a href="mailto:support@redcappi.com">support@redcappi.com</a> the unwanted message you have received (with all header info) so that we can further look into where the emails are being sent from.</h3>';
		$msg .= '<h3>Thanks you!</h3>';
		# Load Thanks Message view
		$strMsg =  $this->load->view('newsletter/thanks_msg',array('msg'=>$msg),true);
		echo str_replace('350px','700px',$strMsg);
	}
}
?>