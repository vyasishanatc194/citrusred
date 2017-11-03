<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function create_transactional_notification($activity = "", $replace_arr = array(), $to_email = "") {
    $vmta = '';
    switch ($activity) {
        case 'welcome':
            $html_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/welcome.html");
            $txt_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/welcome.txt");
            $subject = 'Welcome to RedCappi';
            $arrSearchStr = array('[xxx_LINK_xxx]', '[USERNAME]', '[FIRSTNAME_OR_USERNAME]', '[PASSWORD]', 'xxxcopyright_yearxxx');
            $new_replace_arr = array('', $replace_arr[0], $replace_arr[0], $replace_arr[1], date('Y'));
            break;
        case 'confirm_user_registration':

            $html_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/welcome_responsive.html");
            $txt_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/welcome_responsive.txt");
            $subject = 'Welcome to RedCappi';
            $arrSearchStr = array('[xxx_LINK_xxx]', '[USERNAME]', '[FIRSTNAME_OR_USERNAME]', '[PASSWORD]', 'xxxcopyright_yearxxx');
            $new_replace_arr = array(site_url("user/confirm_user/" . $replace_arr[0]), $replace_arr[2], $replace_arr[2], $replace_arr[3], date('Y'));


            //$html_content = file_get_contents(config_item('webappassets_path') ."system_emails/html/welcome.html");
            //$txt_content = file_get_contents(config_item('webappassets_path') ."system_emails/html/welcome.txt");
            //	$subject = 'Welcome to RedCappi';
            //	$arrSearchStr = array('[xxx_LINK_xxx]','[USERNAME]','[FIRSTNAME_OR_USERNAME]','[PASSWORD]','xxxcopyright_yearxxx');
            //	$new_replace_arr	= array(site_url("user/confirm_user/".$replace_arr[0]),$replace_arr[2],$replace_arr[2],$replace_arr[3],date('Y'));

            break;
        case 'verify_other_email':
            $html_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/verify_other_email.html");
            $txt_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/verify_other_email.txt");
            $subject = 'Verify your email';
            $arrSearchStr = array('[xxx_LINK_xxx]', '[USERNAME]', '[FIRSTNAME_OR_USERNAME]', 'xxxcopyright_yearxxx');
            $new_replace_arr = array(site_url("user/verify/" . $replace_arr[0]), $replace_arr[2], $replace_arr[2], date('Y'));
            break;
        case 'campaign_send_notification':
            $html_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/campaign_sent.html");
            $txt_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/campaign_sent.txt");
            $subject = 'Your RedCappi campaign has been sent';
            $arrSearchStr = array('[FIRSTNAME_OR_USERNAME]', '[CAMPAIGN_NAME]', '[TOTAL_CONTACTS_SELECTED]', '[CAMPAIGN_VIEW_LINK]', '[STAT_LINK]', 'xxxcopyright_yearxxx');
            $new_replace_arr = array($replace_arr[0], $replace_arr[1], $replace_arr[2], $replace_arr[3], $replace_arr[4], date('Y'));
            break;
        case 'campaign_approved_notification':
            $html_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/campaign_approved.html");
            $txt_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/campaign_approved.txt");
            $subject = 'Your RedCappi campaign has been approved by admin';
            $arrSearchStr = array('[FIRSTNAME_OR_USERNAME]', '[CAMPAIGN_NAME]', '[CAMPAIGN_VIEW_LINK]', 'xxxcopyright_yearxxx');
            $new_replace_arr = array($replace_arr[0], $replace_arr[1], $replace_arr[2], date('Y'));
            break;
        case 'campaign_suspended_notification':
            $html_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/campaign_suspended.html");
            $txt_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/campaign_suspended.txt");
            $subject = 'Your campaign was disallowed';
            $arrSearchStr = array('[FIRSTNAME_OR_USERNAME]', '[CAMPAIGN_NAME]', '[CAMPAIGN_VIEW_LINK]', '[DISALLOW_REASON]', 'xxxcopyright_yearxxx');
            $new_replace_arr = array($replace_arr[0], $replace_arr[1], $replace_arr[2], $replace_arr[3], date('Y'));
            break;
        case 'campaign_not_scheduled_notification':
            $html_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/campaign_not_scheduled.html");
            $txt_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/campaign_not_scheduled.txt");
            $subject = 'Your scheduled campaign could not be sent';
            $arrSearchStr = array('[FIRSTNAME_OR_USERNAME]', '[CAMPAIGN_NAME]', '[xxx_number_of_contacts_xxx]', '[xxx_max_contacts_xxx]', 'CAMPAIGN_VIEW_LINK', 'xxxcopyright_yearxxx');
            $new_replace_arr = array($replace_arr[0], $replace_arr[1], $replace_arr[2], $replace_arr[3], $replace_arr[4], date('Y'));
            break;
        case 'billing_receipt_notification':
            $html_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/invoice.html");
            $txt_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/invoice.txt");
            $subject = 'RedCappi Invoice';
            $arrSearchStr = array('[FIRSTNAME_OR_USERNAME]', '[ORDER_ID]', '[PURCHASE_DATE]', '[CURRENT_PLAN]', '[AMOUNT_PAID]', '[CARD_ENDING_IN]', '[BILLED_TO]', '[COMPANY]', '[PHONE]', '[EMAIL_ADDRESS]', '[BILLING_ADDRESS]', 'xxxcopyright_yearxxx');
            $new_replace_arr = array($replace_arr[0], $replace_arr[1], $replace_arr[2], $replace_arr[3], $replace_arr[4], $replace_arr[5], $replace_arr[6], $replace_arr[7], $replace_arr[8], $replace_arr[9], $replace_arr[10], date('Y'));
            break;
        case 'confirm_subscription':


            if (trim($replace_arr[5]) == "") {
                $html_content = "To activate your subscription, please follow the link below.\r\n
If you can't click it, please copy the entire link and paste it into your browser.\r\n\r\n
xxx_LINK_xxx
							\r\n\r\n
Thank You!
							\r\n";
            } else {
                $html_content = trim($replace_arr[5]) . "\r\n\r\n
xxx_LINK_xxx
						\r\n\r\n	";
            }
            $txt_content = $html_content;
            $html_content = nl2br($html_content);
            $subject = ('' != trim($replace_arr[6])) ? trim($replace_arr[6]) : 'Please Confirm Your Subscription';
            $arrSearchStr = array('xxx_LINK_xxx');
            $encodedURLData = base64url_encode($replace_arr[2] . "-" . $replace_arr[3] . "-" . $replace_arr[4]);
            $link = site_url("newsletter/signup/verify_subscription/" . $encodedURLData);

            $to_email = $replace_arr[3];
            $to = $replace_arr[3];
            $sender = $replace_arr[1];
            $sender_name = $replace_arr[0];
            $vmta = $replace_arr[7];

            $new_replace_arr = array($link);

            break;
        case 'account_approval':
            $html_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/account_approval.html");
            $txt_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/account_approval.txt");
            $subject = 'Thanks for Upgrading - Account Approval';
            $arrSearchStr = array('[FIRSTNAME_OR_USERNAME]', 'xxxcopyright_yearxxx');

            $new_replace_arr = array($replace_arr[0], date('Y'));
            break;
        case 'list_growing':
            $html_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/list_growing.html");
            $txt_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/list_growing.txt");
            $subject = 'Your Email List is Growing';
            $arrSearchStr = array('[FIRSTNAME_OR_USERNAME]', 'xxxcopyright_yearxxx');

            $new_replace_arr = array($replace_arr[0], date('Y'));
            break;
        case 'contact_imported_notification':
            $html_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/contacts_imported.html");
            $txt_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/contacts_imported.txt");
            $subject = 'Your Contacts have been Imported';
            $arrSearchStr = array('[FIRSTNAME_OR_USERNAME]', 'xxxcopyright_yearxxx');

            $new_replace_arr = array($replace_arr[0], date('Y'));
            break;
        case 'contact_imported_upgrade_notification':
            $html_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/contacts_imported_upgrade_plan.html");
            $txt_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/contacts_imported_upgrade_plan.txt");
            $subject = 'Your Contacts have been Imported';
            $arrSearchStr = array('[FIRSTNAME_OR_USERNAME]', 'xxxcopyright_yearxxx');

            $new_replace_arr = array($replace_arr[0], date('Y'));
            break;
        case 'refer_freind':
            $html_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/refer_friend.html");
            $txt_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/refer_friend.txt");
            $sender_name = $replace_arr[0];
            $subject = "$sender_name invites you to RedCappi";
            $message = $replace_arr[1];
            $arrSearchStr = array('[xxx_message_xxx]', 'xxxcopyright_yearxxx');
            $new_replace_arr = array(nl2br($message), date('Y'));
            break;
        case 'user_account_expire':
            $html_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/termination_notice_for_inactivity.html");
            $txt_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/termination_notice_for_inactivity.txt");
            $subject = 'Notice of Account Termination';
            $arrSearchStr = array('{f_name,username}', 'xxxcopyright_yearxxx', '[xxdaysxx]', '[xxnotlogindaysxx]');
            $new_replace_arr = array($replace_arr[0], date('Y'), $replace_arr[2], $replace_arr[3]);
            break;
        case 'confirmation_of_account_termination':
            $html_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/subscription_cancelled.html");
            $txt_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/subscription_cancelled.txt");
            $subject = 'Confirmation of Account Termination';
            $arrSearchStr = array('{f_name,username}', 'xxxcopyright_yearxxx', '[xxdaysxx]');
            $new_replace_arr = array($replace_arr[0], date('Y'), $replace_arr[2]);
            break;
        case 'redcappi_payment_failure':
            $html_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/failed_cc.html");
            $txt_content = file_get_contents(config_item('webappassets_path') . "system_emails/html/failed_cc.txt");
            $subject = 'RedCappi payment failure';
            $arrSearchStr = array('[FIRSTNAME_OR_USERNAME]', 'xxxcopyright_yearxxx');
            $new_replace_arr = array($replace_arr[0], date('Y'));
            break;
    }
    if ($activity != 'confirm_subscription') {
        //$html_content	= getHeader().$html_content.getFooter();
        $to = ($to_email != '') ? $to_email : $replace_arr[1];

        $html_body = str_replace($arrSearchStr, $new_replace_arr, $html_content);
        $txt_body = str_replace($arrSearchStr, $new_replace_arr, $txt_content);
        //$subject	= str_replace($arrSearchStr, $new_replace_arr, $subject);		
        $sender = (trim($sender) != '') ? $sender : SYSTEM_EMAIL_FROM;
        $sender_name = (trim($sender_name) != '') ? $sender_name : 'RedCappi';
        $arrTo = explode(',', $to);
        if (is_array($arrTo)) {
            foreach ($arrTo as $send_to) {
                if ($send_to != '')
                    send_tmail($send_to, $sender, $sender_name, $subject, $html_body, $txt_body, $vmta);
            }
        }else {
            if ($to != '') {
                send_tmail($to, $sender, $sender_name, $subject, $html_body, $txt_body, $vmta);
            }
        }
    } else {
        if ($replace_arr[2] > 0 and $replace_arr[4] > 0 and $replace_arr[3] != '')
            $to = $replace_arr[3];
        else
            $to = '';

        $html_body = str_replace($arrSearchStr, $new_replace_arr, $html_content);
        $txt_body = str_replace($arrSearchStr, $new_replace_arr, $txt_content);
        $sender = (trim($sender) != '') ? $sender : SYSTEM_EMAIL_FROM;
        $sender_name = (trim($sender_name) != '') ? $sender_name : 'RedCappi';
        if ($to != '') {
            send_tmail_plain_text($to, $sender, $sender_name, $subject, $txt_body, $vmta);
        }
    }
}

function send_member_message_email($to, $sender = "", $sender_name = "", $subject = "", $html_content = "", $txt_body = "", $vmta = 'redcappi.com') {
    $html_content = getHeader() . $html_content . getFooter();
    send_tmail($to, $sender, $sender_name, $subject, $html_content, $txt_body, $vmta);
}

function send_tmail($to, $sender = "", $sender_name = "", $subject = "", $message = "", $text_message = "", $vmta = 'redcappi.com') {
    $vmta = 'redcappi.com';
    require_once("phpmailer/class.phpmailer.php");
    $mail = new PHPMailer();
    $mail->IsSMTP(); // set mailer to use SMTP
    $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = false;  // authentication enabled
    $mail->Host = "mail.redcappi.com";
    $mail->Hostname = "mail.redcappi.com";
    $mail->Port = 2525;
    //$mail->AddReplyTo($sender, $sender_name);
    $mail->FromName = $sender_name;
    $mail->From = $sender;
    $mail->Subject = $subject;
    $mail->Sender = "support@redcappi.com";
    $mail->AddCustomHeader('x-virtual-mta: ' . $vmta);
    //$mail->IsHTML(false);	
    //$mail->Body = $text_message;	
    $mail->AltBody = $text_message;
    $mail->MsgHTML($message);
    $mail->AddAddress($to);
    @$mail->Send();
    $mail->SmtpClose();
}

function send_tmail_0($to, $sender = "", $sender_name = "", $subject = "", $message = "", $text_message = "") {
    require_once("phpmailer/class.phpmailer.php");
    $mail = new PHPMailer();
    $mail->IsSMTP(); // set mailer to use SMTP
    $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = false;  // authentication enabled
    $mail->Host = "mail.rcmailcorp.com";
    $mail->Hostname = "mail.rcmailcorp.com";
    $mail->Port = 2525;
    //$mail->AddReplyTo($sender, $sender_name);
    $mail->FromName = $sender_name;
    $mail->From = $sender; // 'support@rcmailcorp.com';
    $mail->Subject = $subject;
    $mail->Sender = "support@bounce.rcmailcorp.com";
    $mail->AddCustomHeader('x-virtual-mta: rcmailcorp.com');
    $mail->IsHTML(false);
    $mail->Body = $text_message;
    $mail->AddAddress($to);
    @$mail->Send();
    $mail->SmtpClose();
}

//function send_tmail_plain_text($to, $sender="",$sender_name="", $subject="",$text_message="", $vmta='rcorp73'){
function send_tmail_plain_text($to, $sender = "", $sender_name = "", $subject = "", $text_message = "", $vmta = 'redrotate3') {
    require_once("phpmailer/class.phpmailer.php");
    $mail = new PHPMailer();
    $mail->IsSMTP(); // set mailer to use SMTP
    $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only	
    if ($vmta == 'rcorp73') {
        $mail->Host = "mail.redcappi.com";
        $mail->Hostname = "mail.redcappi.com";
        $mail->Port = 2525;
        $senderDomain = 'redcappi.com';
    } elseif (in_array($vmta, config_item('lw_vmta'))) {
        $mail->Host = "host.redcappi.net";
        $mail->Hostname = "host.redcappi.net";
        $mail->Port = 2525;
        $senderDomain = 'bounce.redcappi.net';
    } elseif (in_array($vmta, config_item('lw2_vmta'))) {
        $mail->Host = "mail.rcmailsv.com";
        $mail->Hostname = "mail.rcmailsv.com";
        $mail->Port = 2525;
        $senderDomain = 'bounce.rcmailsv.com';
    } elseif (in_array($vmta, config_item('lw3_vmta'))) {
        $mail->Host = "mail.mailsvrc.com";
        $mail->Hostname = "mail.mailsvrc.com";
        $mail->Port = 2525;
        $senderDomain = 'bounce.mailsvrc.com';
    } elseif (in_array($vmta, config_item('ph_vmta'))) {
        $mail->Host = "mta01.redcappi.net";
        $mail->Hostname = "mta01.redcappi.net";
        $mail->Port = 25;
        $senderDomain = 'bounce.redcappi.com';
    } elseif (in_array($vmta, config_item('lw5_vmta'))) {
        $mail->Host = "mail.mailsvrc3.com";
        $mail->Port = 2525;
        $senderDomain = "bounce.mailsvrc3.com";
    } elseif (in_array($vmta, config_item('lw6_vmta'))) {
        $mail->Host = "mail.mailsvrc4.com";
        $mail->Port = 2525;
        $senderDomain = "bounce.mailsvrc4.com";
    } elseif (in_array($vmta, config_item('lw7_vmta'))) {
        $mail->Host = "mail.mailsvrc5.com";
        $mail->Port = 2525;
        $senderDomain = "bounce.mailsvrc5.com";
    } elseif (in_array($vmta, config_item('lw8_vmta'))) {
        $mail->Host = "mail.mailsvrc6.com";
        $mail->Port = 2525;
        $senderDomain = "bounce.mailsvrc6.com";
    }elseif (in_array($vmta, config_item('amazon_vmta'))) {
                                $mail->Host = "smtp.sparkpostmail.com";
                                $mail->Port = 587;
                                $senderDomain = "spark.redcappi.com";
         } else {
        $mail->Host = "mail.rcmailcorp.com";
        $mail->Hostname = "mail.rcmailcorp.com";
        $mail->Port = 2525;
        $senderDomain = 'bounce.rcmailcorp.com';
    }

    $mail->FromName = $sender_name;
    $mail->From = $sender;
    $mail->Subject = $subject;
    $mail->Sender = "support@" . $senderDomain;
    $mail->AddCustomHeader('x-virtual-mta: ' . $vmta);
    $mail->IsHTML(false);
    $mail->Body = $text_message;
    $mail->AddAddress($to);
    @$mail->Send();
    $mail->SmtpClose();
}

function send_tmail_ph($to, $sender = "", $sender_name = "", $subject = "", $text_message = "") {
    require_once("phpmailer/class.phpmailer.php");
    $mail = new PHPMailer();
    $mail->IsSMTP(); // set mailer to use SMTP
    $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only	
    $mail->Host = "mta01.redcappi.net";
    $mail->Port = 25;

    $mail->AddCustomHeader('Sender: support@sender.redcappi.com');
    $mail->FromName = $sender_name;
    $mail->From = $sender;
    $mail->Subject = $subject;
    $mail->Sender = "support@redcappi.com";
    //$mail->AddCustomHeader('x-virtual-mta: rcmailer5');	 
    $mail->IsHTML(false);
    $mail->Body = $text_message;
    $mail->AddAddress($to);
    @$mail->Send();
    $mail->SmtpClose();
}

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function getHeader() {
    return '
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">	  
<head><title>RedCappi Transactional Mail</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" /> 
<link href="http://www.redcappi.com/webappassets/css/email_preview.css?v=6-20-13" rel="stylesheet"></link> 		      
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
</head>
	  
	<body class="body_style" style="background-color: #ffffff !important;">		   	 
	<table width="597" align = "center" cellspacing="0" cellpadding="0" class="container-div preheader" style="  border:none;clear:both; ">	 
	<tr>
		<td align="center" height = "40">&nbsp;</td>									  								
	</tr>	
	</table> 	
	<table cellspacing="0" cellpadding="0" width="605" style="border-width: thick; border-style: solid; border-collapse: separate; border-radius: 10px; overflow: hidden; font-family: Arial,Helvetica,sans-serif; border-color: #E10028 !important;" align="center">
		<tr>
			<td align="left">
				<div style = "margin: 15px 0px 0px 15px;"><img src="http://www.redcappi.com/asset/user_files/4194/image_bank/20160103230627.png" alt="RedCappi_Logo_Icon_Red" border="0"  height="45" width="200" /></div>
				<div style="text-align: left;margin-top:-5px;">
					<span style="margin-left:72px;font-size:15px;font-style:italic;">easy email marketing...</span>
				</div> 																			
				<hr style="width:547px;margin-top:10px;margin-bottom:5px;padding-left:3px;padding-right: 3px;padding-top:0px;padding-bottom:0px;color:#F5F5F5;" />
			</td>									  								
		</tr>
		<tr> 																		
			<td>
				<div style = "margin:5px 25px 5px 25px;line-height:1.5;font-size:13px;">
			  <!-- header ends -->
			  
			  ';
}

function getFooter() {
    return '<!-- footer -->		  
					
				</div>
				<hr style="width:547px;margin-top:10px;margin-bottom:5px;padding-left:3px;padding-right: 3px;padding-top:0px;padding-bottom:0px;color:#F5F5F5;" />
				<div style="text-align: center;margin-bottom:15px;">
					<span style="line-height: 1.2;font-size:10px;color:#333333;">RedCappi LLC. | Chicago, IL| USA |  1-877-722-2774</span><br />					
				</div> 	
			</td>		
		</tr>
	</table>

	<div style="text-align: center;margin: 20px auto;">		
		<div style="line-height: 1.5; font-family:Arial,Helvetica,sans-serif;color:#333333;font-size:12px;padding-bottom:10px;">
		&copy; xxxcopyright_yearxxx RedCappi, All Rights Reserved</div>
		<a href="https://www.redcappi.com/"> <img src="http://www.redcappi.com/webappassets/images/thanks-logo.png?v=6-20-13" alt="logo" title="logo" border="0" /></a>
	</div>

</body>
</html>
		
';
}

?>