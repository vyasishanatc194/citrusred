<?php
/**
Admin Notification send mail helper
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');


function admin_notification_send_email($recipient, $sender,$sender_name, $subject, $message,$text_message)
{
require_once("phpmailer/class.phpmailer.php");		
		$CI =& get_instance();
		$sess_user = $CI->session->userdata('member_id');
		if (!isset($sess_user)) exit;	
		
		$mail = new PHPMailer(); 
		
		//SMTP begin
		$mail->IsSMTP();// set mailer to use SMTP
		$mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
		$mail->SMTPAuth = false;  // authentication enabled
		//$mail->Host = "host.redcappi.net";
		$mail->Host = "mail.redcappi.com";
		$mail->Port = 2525; 
		//SMTP end
		
		//$mail->AddCustomHeader('Sender: support@redcappi.com');
		$mail->AddCustomHeader('Sender: support@bounce.rcmailsv.com');
		//mail begin
		
		$emailz = explode(",",$recipient);
			$mailCt = count($emailz);
				
			for($i=0; $i<$mailCt; $i++){			
				$mail->AddAddress($emailz[$i]);
			}
		$mail->FromName = $sender_name;
		$mail->From = $sender;		
		//$mail->Sender="support@redcappi.com";			
		$mail->Sender="support@bounce.rcmailsv.com";			
		$mail->AddCustomHeader('x-envid: notification');
		$mail->AddCustomHeader('x-fblid: admin-notification');			
		$mail->AddCustomHeader('x-job: notification');			
		
		
			// the HTML to the plain text. Store it into the variable. 
		$mail->Subject = $subject;
		//$mail->AddCustomHeader('x-virtual-mta: rcmailer8');	 		
		$mail->AddCustomHeader('x-virtual-mta: rcmailsv.com');	 		
		$mail->AltBody = $text_message;
		$mail->MsgHTML($message);
		@$mail->Send();
	   $mail->SmtpClose();
	//mail end
}
?>