<div class="tblheading">General Setting</div>
<?php
echo form_open('webmaster/sitesetting_manage/general_setting/', array('id' => 'frmGeneralSetting'));
echo '<table class="tbl_forms">';  

echo "<tr><td colspan='2' valign='top'><div style=\"color:#FF0000;\">".validation_errors()."</div></td></tr>"; 

echo "<tr><td colspan='2' valign='top'><div id=\"messages\" style=\"color:#FF0000;\">";

/// display all messages

if (is_array($messages)):
    foreach ($messages as $type => $msgs):
        foreach ($msgs as $message):
            echo ('<span class="' .  $type .'">' . $message . '</span>');
        endforeach;
    endforeach;
endif;
echo "</div></td></tr>";
echo "<tr><td valign='top' width='40%'>Maximum Payment Declined<br/>
(Maximum Payment Declined Transaction Counter)</td><td>"; 
echo form_input(array('name'=>'maximum_payment_declined','id'=>'maximum_payment_declined','maxlength'=>50 ,'value'=>$maximum_payment_declined));
echo '</td></tr>';echo "<tr><td valign='top' width='40%'>Maximum Add Contact<br/>
(Send notification to admin if member adds more than X contacts)</td><td>"; 
echo form_input(array('name'=>'maximum_add_contact','id'=>'maximum_add_contact','maxlength'=>50 ,'value'=>$maximum_add_contact));
echo '</td></tr>';
echo "<tr><td valign='top'>Maximum Delete Contact<br/>
(Send notification to admin if member deletes more than X contacts)
</td><td>"; 
echo form_input(array('name'=>'maximum_delete_contact','id'=>'maximum_delete_contact','maxlength'=>50 ,'value'=>$maximum_delete_contact));
echo '</td></tr>';

echo "<tr><td colspan='2'><hr /> </td></tr>";
echo "<tr><td valign='top'>Bounce Critical Level (Percentage)<br/>
(Notify admin if Bounce for a compaign is more than X%)
</td><td>"; 
echo form_input(array('name'=>'maximum_bounce_contact','id'=>'maximum_bounce_contact','maxlength'=>50 ,'value'=>$maximum_bounce_contact));
echo '</td></tr>';
echo "<tr><td valign='top'>FBL Critical Level (Percentage)<br/>(Notify admin if Complaint for a campaign is more than X%)</td><td>"; 
echo form_input(array('name'=>'fbl_critical_limit','id'=>'fbl_critical_limit','maxlength'=>50 ,'value'=>$fbl_critical_limit));
echo '</td></tr>';
echo "<tr><td valign='top'>Unsubscribe Critical Level (Percentage)<br/>(Notify admin if Unsubscribe for a campaign is more than X%)</td><td>"; 
echo form_input(array('name'=>'unsubscribe_critical_level_to_alert','id'=>'unsubscribe_critical_level_to_alert','maxlength'=>50 ,'value'=>$unsubscribe_critical_level_to_alert));
echo '</td></tr>';

echo "<tr><td valign='top'>Unsubscribe Critical Level to Pause(Percentage)<br/>(Pause campaign-sending if Unsubscribe for a campaign is more than X%)</td><td>"; 
echo form_input(array('name'=>'unsubscribe_critical_level_to_pause','id'=>'unsubscribe_critical_level_to_pause','maxlength'=>50 ,'value'=>$unsubscribe_critical_level_to_pause));
echo '</td></tr>';
echo "<tr><td valign='top'>FBL Critical Level to Pause(Percentage)<br/>(Pause campaign-sending if Complaints for a campaign is more than X%)</td><td>"; 
echo form_input(array('name'=>'fbl_critical_level_to_pause','id'=>'fbl_critical_level_to_pause','maxlength'=>50 ,'value'=>$fbl_critical_level_to_pause));
echo '</td></tr>';
echo "<tr><td valign='top'>Bounce Critical Level to Pause(Percentage)<br/>(Pause campaign-sending if Bounce for a campaign is more than X%)</td><td>"; 
echo form_input(array('name'=>'bounce_critical_level_to_pause','id'=>'bounce_critical_level_to_pause','maxlength'=>50 ,'value'=>$bounce_critical_level_to_pause));
echo '</td></tr>';


echo "<tr><td colspan='2'><hr /> </td></tr>";
/**********Added By cb***********/

echo "<tr><td valign='top'>Per Email Price for Credit Plan<br/>
(Per Email price can be calculated with the amount of credit)
</td><td>"; 
echo form_input(array('name'=>'per_mail_price_for_credit','id'=>'per_mail_price_for_credit','maxlength'=>50 ,'value'=>$per_mail_price_for_credit));
echo '</td></tr>';

echo "<tr><td valign='top'>Maximum Add Contacts By user With Credit Plan<br/>
(When User have credit Plan then they can add max contact according this)
</td><td>"; 
echo form_input(array('name'=>'max_contact_add_by_credit','id'=>'max_contact_add_by_credit','maxlength'=>50 ,'value'=>$max_contact_add_by_credit));
echo '</td></tr>';

/**********Ended By cb***********/

echo "<tr><td valign='top'>Maximum Soft Bounce Contact<br/>
(Contact inactivated after X soft bounces)
</td><td>"; 
echo form_input(array('name'=>'max_soft_bounce','id'=>'max_soft_bounce','maxlength'=>50 ,'value'=>$max_soft_bounce));
echo '</td></tr>';


echo "<tr><td valign='top'>Ignore Soft Bounce For X Days<br/>
(Dont send campaign to soft bounced contacts for X days)
</td><td>"; 
echo form_input(array('name'=>'ignore_softbounced_for_x_days','id'=>'ignore_softbounced_for_x_days','maxlength'=>50 ,'value'=>$ignore_softbounced_for_x_days));
echo '</td></tr>';


echo "<tr><td valign='top'>Comment Closed after (days)<br/>
(Blog comment will close after xxx days)
</td><td>"; 
echo form_input(array('name'=>'comment_close_days','id'=>'comment_close_days','maxlength'=>50 ,'value'=>$comment_close_days));
echo '</td></tr>';
echo "<tr><td valign='top'>Default Limit For Schedule Email<br/>
(Admin approval not required for campaign with less than X contacts)
</td><td>"; 
echo form_input(array('name'=>'default_allowed_limit_for_send_email','id'=>'default_allowed_limit_for_send_email','maxlength'=>50 ,'value'=>$default_allowed_limit_for_send_email));
echo '</td></tr>';
echo "<tr><td valign='top'>Send notification to user after (days)<br/>
(Notify user if not logged-in for X days )
</td><td>"; 
echo form_input(array('name'=>'send_notification_to_user_after_xx_days','id'=>'send_notification_to_user_after_xx_days','maxlength'=>50 ,'value'=>$send_notification_to_user_after_xx_days));
echo '</td></tr>';
echo '</td></tr>';
echo "<tr><td valign='top'>User Account will inactive after (days)<br/>
(Account gets deleted after X days, if he does not logs-in even after notification.)
</td><td>"; 
echo form_input(array('name'=>'user_account_inactive_after_xx_days','id'=>'user_account_inactive_after_xx_days','maxlength'=>50 ,'value'=>$user_account_inactive_after_xx_days));
echo '</td></tr>';
echo "<tr><td valign='top'>Delete unconfirmed user after (days)<br/>
(Un-confirmed users will be deleted after X days from registration)
</td><td>"; 
echo form_input(array('name'=>'delete_unconfirmed_users_after_xx_days','id'=>'delete_unconfirmed_users_after_xx_days','maxlength'=>50 ,'value'=>$delete_unconfirmed_users_after_xx_days));
echo '</td></tr>';
echo '</td></tr>';
echo "<tr><td valign='top'>Delete user detail after cancel subscription (days)
</td><td>"; 
echo form_input(array('name'=>'cancel_subscription_after_xx_days','id'=>'cancel_subscription_after_xx_days','maxlength'=>50 ,'value'=>$cancel_subscription_after_xx_days));
echo '</td></tr>';
echo "<tr><td valign='top'>Campaign Stat will move to archive after (days)</td><td>"; 
echo form_input(array('name'=>'campaign_stat_archive_after_xx_days','id'=>'campaign_stat_archive_after_xx_days','maxlength'=>50 ,'value'=>$campaign_stat_archive_after_xx_days));
echo '</td></tr>';

echo "<tr><td valign='top'>Unauthenticate account after adding contacts more than </td><td>"; 
echo form_input(array('name'=>'max_contacts_to_unauthenticate','id'=>'max_contacts_to_unauthenticate','maxlength'=>50 ,'value'=>$max_contacts_to_unauthenticate));
echo '</td></tr>';

echo "<tr><td valign='top'>Apply list-growing message after adding contacts more than </td><td>"; 
echo form_input(array('name'=>'max_contacts_for_list_growing_alert','id'=>'max_contacts_for_list_growing_alert','maxlength'=>50 ,'value'=>$max_contacts_for_list_growing_alert));
echo '</td></tr>';

echo "<tr><td valign='top'>Maximum friends to Forward a campaign</td><td>"; 
echo form_input(array('name'=>'campaign_forward_limit','id'=>'campaign_forward_limit','maxlength'=>50 ,'value'=>$campaign_forward_limit));
echo '</td></tr>';


echo "<tr><td valign='top'>Admin Notification Email(Comma Separated Email)</td><td>"; 
echo form_input(array('name'=>'admin_notification_email','id'=>'admin_notification_email','maxlength'=>200 ,'value'=>$admin_notification_email));
echo '</td></tr>';

echo "<tr><td valign='top'>Do Not Mail List Strings(Comma Separated values)</td><td>"; 
echo form_textarea(array('name'=>'do_not_mail_list','id'=>'do_not_mail_list','value'=>$do_not_mail_list, 'cols'=>50, 'style'=>'width:755px;'));
echo '</td></tr>';

echo "<tr><td valign='top'>Unresponsive ignored contacts(Comma Separated values)</td><td>"; 
echo form_textarea(array('name'=>'unresponsive_ignored','id'=>'unresponsive_ignored','value'=>$unresponsive_ignored, 'style'=>'width:755px;'));
echo '</td></tr>';


echo "<tr><td valign='top'>Seed-list</td><td>"; 
echo form_textarea(array('name'=>'seedlist','id'=>'seedlist' ,'value'=>$seedlist, 'style'=>'width:755px;'));
echo '</td></tr>';


echo "<tr><td valign='top'></td><td>";
echo form_submit(array('name' => 'btnChangeSetting', 'id' => 'btnChangeSetting','class'=>'inputbuttons','content' => 'Change Setting'), 'Change Setting');
echo form_hidden('action','submit');
echo form_close();
echo '</td></tr></table>';
?>
</center>