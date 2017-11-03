<?php

/*
  Plugin send campaign email for sending mail & autoresponders
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once("phpmailer/class.phpmailer.php");

function send_campaign_batch($campaign_file, $process_id) {
    #set execution time
    set_time_limit(0);
    $CI = & get_instance();
    $CI->load->model('is_authorized');
    $mail = new PHPMailer();
    $arrCampaignBag = unserialize(file_get_contents(config_item('campaign_files') . $campaign_file));

    $arrCampaignBatch = array_chunk($arrCampaignBag, 100);
    foreach ($arrCampaignBatch as $eachBatch) {
        //SMTP begin
        $mail->IsSMTP(); // set mailer to use SMTP
        $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth = false;  // authentication enabled
        $mail->IsHTML(false);
        $mail->SMTPKeepAlive = true; // SMTP connection will not close after each email sent


        foreach ($eachBatch as $eachRecipient) {
            //mail begin
            $subscriber_id = $eachRecipient['subscriber_info']['subscriber_id'];
            $thisEmailId = $eachRecipient['subscriber_info']['subscriber_email_address'];
            $thisCampaignId = $eachRecipient['campaign_id'];
            $rsIsThisSent = $CI->db->query("select email_sent from red_email_queue where campaign_id='$thisCampaignId' and subscriber_id='$subscriber_id'");
            $isThisSent = $rsIsThisSent->row()->email_sent;
            $rsIsThisSent->free_result();
            if ($isThisSent == 0) {
                $encodedSubscriber = $CI->is_authorized->encodeSubscriber($subscriber_id, $thisEmailId);

                $mail->FromName = $eachRecipient['sender_name'];
                $mail->From = $eachRecipient['sender'];
                $mail->Subject = $eachRecipient['subject'];
                if (trim($eachRecipient['reply_to_email']) != '') {
                    $mail->AddReplyTo($eachRecipient['reply_to_email'], $eachRecipient['sender_name']);
                }
                $sender_id = str_replace('@', '=', strtolower($eachRecipient['sender']));
                $mailtype = "newsletter";
                $mail->AddCustomHeader('Feedback-ID:' . ' CampaignID' . $thisCampaignId . ':CustomerID' . $eachRecipient['campaign_created_by'] . ':MailTypeID' . $mailtype . ':redcappi');
                if (in_array($eachRecipient['vmta'], config_item('lw_vmta'))) {
                    $mail->Host = "host.redcappi.net";
                    $mail->Hostname = "host.redcappi.net";
                    $mail->Port = 2525;
                    $mail->AddCustomHeader('Sender: ' . $sender_id . '@bounce.redcappi.net');
                    $mail->Sender = base64_encode($subscriber_id) . "@bounce.redcappi.net";
                    $ListUnsubscribe = 'List-Unsubscribe: <mailto:unsubscribe@redcappi.net?subject=Unsubscribe ' . $encodedSubscriber . '>, <http://www.' . SYSTEM_DOMAIN_NAME . '/newsletter/unsubscribe_mail/unsubscribe/' . $thisCampaignId . '/' . $encodedSubscriber . '>';
                } elseif (in_array($eachRecipient['vmta'], config_item('lw2_vmta'))) {
                    $mail->Host = "mail.rcmailsv.com";
                    $mail->Hostname = "mail.rcmailsv.com";
                    $mail->Port = 2525;
                    $mail->AddCustomHeader('Sender: ' . $sender_id . '@bounce.rcmailsv.com');
                    $mail->Sender = base64_encode($subscriber_id) . "@bounce.rcmailsv.com";
                    $ListUnsubscribe = 'List-Unsubscribe: <mailto:unsubscribe@rcmailsv.com?subject=Unsubscribe ' . $encodedSubscriber . '>, <http://www.' . SYSTEM_DOMAIN_NAME . '/newsletter/unsubscribe_mail/unsubscribe/' . $thisCampaignId . '/' . $encodedSubscriber . '>';
                } elseif (in_array($eachRecipient['vmta'], config_item('lw3_vmta'))) {
                    $mail->Host = "mail.mailsvrc.com";
                    $mail->Hostname = "mail.mailsvrc.com";
                    $mail->Port = 2525;
                    $mail->AddCustomHeader('Sender: ' . $sender_id . '@bounce.mailsvrc.com');
                    $mail->Sender = base64_encode($subscriber_id) . "@bounce.mailsvrc.com";
                    $ListUnsubscribe = 'List-Unsubscribe: <mailto:unsubscribe@mailsvrc.com?subject=Unsubscribe ' . $encodedSubscriber . '>, <http://www.' . SYSTEM_DOMAIN_NAME . '/newsletter/unsubscribe_mail/unsubscribe/' . $thisCampaignId . '/' . $encodedSubscriber . '>';
                } elseif (in_array($eachRecipient['vmta'], config_item('lw4_vmta'))) {
                    $mail->Host = "mail.mailsvrc2.com";
                    $mail->Hostname = "mail.mailsvrc2.com";
                    $mail->Port = 2525;
                    $mail->AddCustomHeader('Sender: ' . $sender_id . '@bounce.mailsvrc2.com');
                    $mail->Sender = base64_encode($subscriber_id) . "@bounce.mailsvrc2.com";
                    $ListUnsubscribe = 'List-Unsubscribe: <mailto:unsubscribe@mailsvrc2.com?subject=Unsubscribe ' . $encodedSubscriber . '>, <http://www.' . SYSTEM_DOMAIN_NAME . '/newsletter/unsubscribe_mail/unsubscribe/' . $thisCampaignId . '/' . $encodedSubscriber . '>';
                } elseif (in_array($eachRecipient['vmta'], config_item('lw5_vmta'))) {
                    $mail->Host = "mail.mailsvrc3.com";
                    $mail->Hostname = "mail.mailsvrc3.com";
                    $mail->Port = 2525;
                    $mail->AddCustomHeader('Sender: ' . $sender_id . '@bounce.mailsvrc3.com');
                    $mail->Sender = base64_encode($subscriber_id) . "@bounce.mailsvrc3.com";
                    $ListUnsubscribe = 'List-Unsubscribe: <mailto:unsubscribe@mailsvrc3.com?subject=Unsubscribe ' . $encodedSubscriber . '>, <http://www.' . SYSTEM_DOMAIN_NAME . '/newsletter/unsubscribe_mail/unsubscribe/' . $thisCampaignId . '/' . $encodedSubscriber . '>';
                } elseif (in_array($eachRecipient['vmta'], config_item('lw6_vmta'))) {
                    $mail->Host = "mail.mailsvrc4.com";
                    $mail->Hostname = "mail.mailsvrc4.com";
                    $mail->Port = 2525;
                    $mail->AddCustomHeader('Sender: ' . $sender_id . '@bounce.mailsvrc4.com');
                    $mail->Sender = base64_encode($subscriber_id) . "@bounce.mailsvrc4.com";
                    $ListUnsubscribe = 'List-Unsubscribe: <mailto:unsubscribe@mailsvrc4.com?subject=Unsubscribe ' . $encodedSubscriber . '>, <http://www.' . SYSTEM_DOMAIN_NAME . '/newsletter/unsubscribe_mail/unsubscribe/' . $thisCampaignId . '/' . $encodedSubscriber . '>';
                } elseif (in_array($eachRecipient['vmta'], config_item('lw7_vmta'))) {
                    $mail->Host = "mail.mailsvrc5.com";
                    $mail->Hostname = "mail.mailsvrc5.com";
                    $mail->Port = 2525;
                    $mail->AddCustomHeader('Sender: ' . $sender_id . '@bounce.mailsvrc5.com');
                    $mail->Sender = base64_encode($subscriber_id) . "@bounce.mailsvrc5.com";
                    $ListUnsubscribe = 'List-Unsubscribe: <mailto:unsubscribe@mailsvrc5.com?subject=Unsubscribe ' . $encodedSubscriber . '>, <http://www.' . SYSTEM_DOMAIN_NAME . '/newsletter/unsubscribe_mail/unsubscribe/' . $thisCampaignId . '/' . $encodedSubscriber . '>';
                } elseif (in_array($eachRecipient['vmta'], config_item('lw8_vmta'))) {
                    $mail->Host = "mail.mailsvrc6.com";
                    $mail->Hostname = "mail.mailsvrc6.com";
                    $mail->Port = 2525;
                    $mail->AddCustomHeader('Sender: ' . $sender_id . '@bounce.mailsvrc6.com');
                    $mail->Sender = base64_encode($subscriber_id) . "@bounce.mailsvrc6.com";
                    $ListUnsubscribe = 'List-Unsubscribe: <mailto:unsubscribe@mailsvrc6.com?subject=Unsubscribe ' . $encodedSubscriber . '>, <http://www.' . SYSTEM_DOMAIN_NAME . '/newsletter/unsubscribe_mail/unsubscribe/' . $thisCampaignId . '/' . $encodedSubscriber . '>';
                } elseif (in_array($eachRecipient['vmta'], config_item('mailgun'))) {
                    $mail->SMTPAuth = TRUE;
                    $mail->Username = 'postmaster@m.massmailexpert.com';
                    $mail->Password = '0fadc1fc90336ea9fc811ad7d3fa1da0';

                    $mail->Host = "smtp.mailgun.org";
                    $mail->Hostname = "smtp.mailgun.org";
                    $mail->Port = 587;
                    $mail->AddCustomHeader('Sender: ' . $sender_id . '@bounce.redcappi.net');
                    $mail->Sender = base64_encode($subscriber_id) . "@bounce.redcappi.net";
                    $ListUnsubscribe = 'List-Unsubscribe: <mailto:unsubscribe@redcappi.net?subject=Unsubscribe ' . $encodedSubscriber . '>, <http://www.' . SYSTEM_DOMAIN_NAME . '/newsletter/unsubscribe_mail/unsubscribe/' . $thisCampaignId . '/' . $encodedSubscriber . '>';
                } elseif (in_array($eachRecipient['vmta'], config_item('ph_vmta'))) {
                    $mail->Host = "mta01.redcappi.net";
                    $mail->Hostname = "mta01.redcappi.net";
                    $mail->Port = 25;
                    $mail->AddCustomHeader('Sender: ' . $sender_id . '@sender.redcappi.com');
                    $mail->Sender = base64_encode($subscriber_id) . "@bounce.redcappi.com";
                    $ListUnsubscribe = 'List-Unsubscribe: <mailto:unsubscribe@bounce.redcappi.com?subject=Unsubscribe ' . $encodedSubscriber . '>, <http://www.' . SYSTEM_DOMAIN_NAME . '/newsletter/unsubscribe_mail/unsubscribe/' . $thisCampaignId . '/' . $encodedSubscriber . '>';
                } elseif (in_array($eachRecipient['vmta'], config_item('spark_vmta'))) {
                    $mail->SMTPAuth = TRUE;
                    $mail->Username = 'SMTP_Injection';
                    $mail->Password = '5708441661a8cbbfd85dd477f4c4bc14452da910';
                    $mail->Host = "smtp.sparkpostmail.com";
                    $mail->Hostname = "smtp.sparkpostmail.com";
                    $mail->Port = 587;
                    $mail->AddCustomHeader('Sender: ' . $sender_id . '@spark.redcappi.com');
                    $mail->Sender = base64_encode($subscriber_id) . "@spark.redcappi.com";
                    $ListUnsubscribe = 'List-Unsubscribe: <mailto:unsubscribe@spark.redcappi.com?subject=Unsubscribe ' . $encodedSubscriber . '>, <http://www.' . SYSTEM_DOMAIN_NAME . '/newsletter/unsubscribe_mail/unsubscribe/' . $thisCampaignId . '/' . $encodedSubscriber . '>';
                }else {
                    $mail->Host = "mail.rcmailcorp.com";
                    $mail->Hostname = "mail.rcmailcorp.com";
                    $mail->Port = 2525;
                    $mail->AddCustomHeader('Sender: ' . $sender_id . '@bounce.rcmailcorp.com');
                    $mail->Sender = base64_encode($subscriber_id) . "@bounce.rcmailcorp.com";
                    $ListUnsubscribe = 'List-Unsubscribe: <mailto:unsubscribe@rcmailcorp.com?subject=Unsubscribe ' . $encodedSubscriber . '>, <http://www.' . SYSTEM_DOMAIN_NAME . '/newsletter/unsubscribe_mail/unsubscribe/' . $thisCampaignId . '/' . $encodedSubscriber . '>';
                }


                $campaign_type = $eachRecipient['campaign_type'];

                if ($campaign_type == 'text') {
                    $mail->Body = $eachRecipient['text_message'];
                } else {
                    $mail->AltBody = $eachRecipient['text_message'];
                    $mail->MsgHTML($eachRecipient['message']);
                }
                if (substr(strtolower($thisEmailId), -9) == 'gmail.com')
                    $mail->Precedence = 'bulk';
                else
                    $mail->Precedence = '';

                $subscriber_name = $eachRecipient['subscriber_info']['subscriber_first_name'] . " " . $eachRecipient['subscriber_info']['subscriber_last_name'];



                $mail->AddCustomHeader('x-envid:' . $thisCampaignId);
                $mail->AddCustomHeader('x-fblid:' . $subscriber_id . '-' . $thisCampaignId . '-' . CAMPAIGN_HEADER_SUFFIX);
                $mail->AddCustomHeader('x-job:' . $subscriber_id);
                $mail->AddCustomHeader('x-virtual-mta: ' . $eachRecipient['vmta']);



                $mail->AddAddress($thisEmailId, $subscriber_name); // recipient email address	

                $mail->AddCustomHeader($ListUnsubscribe);

                if (!$mail->Send()) {//echo 'Failed to Send to-'.$thisEmailId;
                    // IF ANY ERROR COMES CLOSE THE SMTP Connection, reset the thread and delete the file, without any contact's record marked as SENT.				
                    $mail->SmtpClose();
                    $CI->db->query("update `red_campaign_thread` set `thread_status` = '0' where `thread_id` = '$process_id'");
                    sleep(1);
                    if (file_exists(config_item('campaign_files') . $campaign_file)) {
                        @unlink(config_item('campaign_files') . $campaign_file);
                    }
                    exit;
                }
                $mail->ClearAddresses();
                $mail->ClearAttachments();
                $mail->ClearCustomHeaders();
                $mail->ClearAllRecipients(); // reset the `To:` list to empty
                // Update Daily-global-IPR			
                $arrEml = explode('@', $thisEmailId);
                $emlDomain = $arrEml[1];
                if (in_array($emlDomain, config_item('major_domains'))) {
                    $CI->db->query("insert into red_global_ipr_daily set `mail_domain` = '$emlDomain' ,  `log_date`=CURDATE() ,  `pipeline`='" . $eachRecipient['vmta'] . "', `user_id`='" . $eachRecipient['campaign_created_by'] . "', total_sent= total_sent + 1, total_released= total_released + 1 ON DUPLICATE  KEY UPDATE  total_sent= total_sent + 1, total_released = total_released + 1");
                }
                // Update email_sent sent status & increment total-campaign-sent-counter
                //$CI->db->query("update red_email_queue set email_sent=1,email_sent_date=now() where campaign_id ='$thisCampaignId' AND `subscriber_id`='$subscriber_id'");
                // Start: added on 19 May2016
                // Add record in Stats table and delete from queue table
                $CI->db->trans_start();
                $CI->db->query("INSERT INTO `red_email_track` set `campaign_id`='$thisCampaignId', `user_id`='" . $eachRecipient['campaign_created_by'] . "', `subscriber_id`='$subscriber_id', `subscriber_email_address`='$thisEmailId', `subscriber_email_domain`='$emlDomain', `email_sent`=1, `email_sent_date`=now(), `not_sent_reason`=0");
                $CI->db->query("delete from red_email_queue where campaign_id ='$thisCampaignId' AND `subscriber_id`='$subscriber_id'");
                $CI->db->trans_complete();
                // END			
                $CI->db->query("update red_email_subscribers set `release_count`= `release_count` + 1,`last_release_date`=current_timestamp()  where `subscriber_id`='$subscriber_id'");
                $CI->db->query("update red_email_campaigns_scheduled set `email_track_released`= `email_track_released` + 1 where `campaign_id`='$thisCampaignId'");
                $CI->db->query("update `red_member_packages` set `campaign_sent_counter`=(`campaign_sent_counter` + 1) where `member_id`='" . $eachRecipient['campaign_created_by'] . "'");
            }
        }
        $mail->SmtpClose();
    }
    # update Db that this process is free now
    $CI->db->query("update `red_campaign_thread` set `thread_status` = '0' where `thread_id` = '$process_id'");
    if (file_exists(config_item('campaign_files') . $campaign_file)) {
        @unlink(config_item('campaign_files') . $campaign_file);
    }
}

function send_autoresponder_batch($message, $text_message, $subject, $sender_name, $sender, $campaign_id, $subscriber_info = array(), $campaign_type = 'html', $vmta = 'redrotate3') {

    #set execution time
    set_time_limit(0);
    require_once("phpmailer/class.phpmailer.php");
    $mail = new PHPMailer();
    //SMTP begin
    $mail->IsSMTP(); // set mailer to use SMTP
    $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = false;  // authentication enabled
    $key = $subscriber_info['schedule_id'] . "_" . $subscriber_info['subscriber_id'];
    if (in_array($vmta, config_item('lw_vmta'))) {
        $mail->Host = "host.redcappi.net";
        $mail->Port = 2525;
        $mail->Sender = base64_encode($key) . "@bounce.redcappi.net";
    } elseif (in_array($vmta, config_item('lw2_vmta'))) {
        $mail->Host = "mail.rcmailsv.com";
        $mail->Port = 2525;
        $mail->Sender = base64_encode($key) . "@bounce.rcmailsv.com";
    } elseif (in_array($vmta, config_item('lw3_vmta'))) {
        $mail->Host = "mail.mailsvrc.com";
        $mail->Port = 2525;
        $mail->Sender = base64_encode($key) . "@bounce.mailsvrc.com";
    } elseif (in_array($vmta, config_item('lw4_vmta'))) {
        $mail->Host = "mail.mailsvrc2.com";
        $mail->Hostname = "mail.mailsvrc2.com";
        $mail->Port = 2525;
        $mail->Sender = base64_encode($key) . "@bounce.mailsvrc2.com";
    } elseif (in_array($vmta, config_item('lw5_vmta'))) {
        $mail->Host = "mail.mailsvrc3.com";
        $mail->Hostname = "mail.mailsvrc3.com";
        $mail->Port = 2525;
        $mail->Sender = base64_encode($key) . "@bounce.mailsvrc3.com";
    } elseif (in_array($vmta, config_item('lw6_vmta'))) {
        $mail->Host = "mail.mailsvrc4.com";
        $mail->Hostname = "mail.mailsvrc4.com";
        $mail->Port = 2525;
        $mail->Sender = base64_encode($key) . "@bounce.mailsvrc4.com";
    } elseif (in_array($vmta, config_item('lw7_vmta'))) {
        $mail->Host = "mail.mailsvrc5.com";
        $mail->Hostname = "mail.mailsvrc5.com";
        $mail->Port = 2525;
        $mail->Sender = base64_encode($key) . "@bounce.mailsvrc5.com";
    } elseif (in_array($vmta, config_item('lw8_vmta'))) {
        $mail->Host = "mail.mailsvrc6.com";
        $mail->Hostname = "mail.mailsvrc6.com";
        $mail->Port = 2525;
        $mail->Sender = base64_encode($key) . "@bounce.mailsvrc6.com";
    }
    //mail begin
    //$sender_id = str_replace('@','=',strtolower($sender));	
    //$mail->AddCustomHeader('Sender: '.$sender_id.'@bounce.redcappi.net');	
    $mail->FromName = $sender_name;
    //$mail->SetFrom($from, $from_name);
    $mail->From = $sender;
    // prepare custom header
    $mail->ClearAddresses();
    $mail->ClearAttachments();
    $mail->ClearCustomHeaders();
    $mail->IsHTML(false);


    $mail->AddCustomHeader('x-envid:' . $campaign_id);
    $mail->AddCustomHeader('x-fblid:auto_' . $key . '-' . $campaign_id . '-' . CAMPAIGN_HEADER_SUFFIX);
    $mail->AddCustomHeader('x-job:auto_' . $key);
    $mail->AddCustomHeader('x-virtual-mta: ' . $vmta);
    $mail->AddCustomHeader('List-Unsubscribe: <mailto:unsubscribe@redcappi.net?subject=Unsubscribe ' . $subscriber_info['subscriber_id'] . '>, <http://www.' . SYSTEM_DOMAIN_NAME . '/newsletter/autoresponder_email/unsubscribe/' . $campaign_id . '/' . $subscriber_info['schedule_id'] . '/' . $subscriber_info['subscriber_id'] . '>');
    $subscriber_name = $subscriber_info['subscriber_first_name'] . " " . $subscriber_info['subscriber_last_name'];
    $mail->AddAddress($subscriber_info['subscriber_email_address'], $subscriber_name);
    $mail->Subject = $subject;
    if ($campaign_type == 'text') {
        $mail->Body = $text_message;
    } else {
        $mail->AltBody = $text_message;
        $mail->MsgHTML($message);
    }

    print_r("before mail send");
    if (!$mail->Send()) {


        $isMailSent = False;
        echo 'Failed to Send to-' . $subscriber_info['subscriber_id'];
    } else {

        $isMailSent = true;
        echo 'Mail Sent';
    }
    $mail->SmtpClose();
    return $isMailSent;
}

?>