<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-1.5.1.min.js"></script>
<SCRIPT type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-ui-1.8.13.custom.min.js"></SCRIPT>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css-front/blitzer/jquery-ui-1.8.14.custom.css" />

<div class="tblheading">Edit User
<?php
	echo "<a  onclick=\"return confirm('Are you sure to delete user ')\" href='".site_url('webmaster/users_manage/user_delete/'.$user['member_id'])."' style='padding:0 50px;float:right'>Delete User</a>";
	if(($transaction_count>0)&&(is_null($user['cancel_subscription_date']))) {
		echo "<a  onclick=\"return confirm('Are you sure to cancel subscription ')\" href='".site_url('webmaster/users_manage/cancel_subscription/'.$user['member_id'])."' style='float:right'>Cancel Subscription</a>";
	}
?>
</div>
<div id="messages">
<?php
// display all messages
if (is_array($messages)):
    foreach ($messages as $type => $msgs):
        foreach ($msgs as $message):
            echo ('<span class="' .  $type .'">' . $message . '</span>');
        endforeach;
    endforeach;
endif;
?>
</div>  
<?php 
echo form_open('webmaster/users_manage/user_edit/'.$user['member_id'], array('id' => 'frmUserEdit'));
echo '<table class="tbl_forms"><tr><td >';

echo '<div style="color:#FF0000;">'.validation_errors().'</div>'; 
echo "</td></td>";
echo "<tr><td>";
echo "Username<br/>"; 
echo form_input(array('name'=>'username','id'=>'username','maxlength'=>50 ,'value'=>$user['member_username'])) ."</td>";

echo "<td >Email<br/>"; 
echo form_input(array('name'=>'email','id'=>'email','maxlength'=>50,'value'=>$user['email_address'] )) ."</td>";

echo "<td >Phone<br/>"; 
echo form_input(array('name'=>'phone','id'=>'phone','maxlength'=>50,'value'=>$user['phone_number'] )) ."</td></tr>";

echo "<tr><td >First Name<br/>"; 
echo form_input(array('name'=>'first_name','id'=>'first_name','maxlength'=>20,'value'=>$user['first_name'] )) ."</td>";
 
echo "<td>Last Name<br/>"; 
echo form_input(array('name'=>'last_name','id'=>'last_name','maxlength'=>20,'value'=>$user['last_name'])) ."</td>";
 

echo "<td>Address Line1<br/>"; 
echo form_input(array('name'=>'address1','id'=>'address1','maxlength'=>50,'value'=>$user['address_line_1'] )) ."</td></tr>";
echo '<tr><td>';
echo "Address Line2<br/>"; 
echo form_input(array('name'=>'address2','id'=>'address2','maxlength'=>50 ,'value'=>$user['address_line_2'])) ."</td>";

echo "<td>City<br/>"; 
echo form_input(array('name'=>'city','id'=>'city','maxlength'=>50,'value'=>$user['city'] )) ."</td>";

echo "<td>State<br/>"; 
echo form_input(array('name'=>'state','id'=>'state','maxlength'=>50,'value'=>$user['state'] )) ."</td></tr>";

echo "<tr><td>Zip Code<br/>"; 
echo form_input(array('name'=>'zipcode','id'=>'zipcode','maxlength'=>50,'value'=>$user['zipcode'] )) ."</td>";
echo "<td>Status<br/>"; 
echo form_dropdown('status',array('active'=>'Active','inactive'=>'Inactive','unconfirmed'=>'Unconfirmed','failed-cc'=>'Failed-cc'),$user['status']);
echo "</td>";
echo "<td>Branded<br/>"; 
echo form_dropdown('rc_logo',array('1'=>'Active','0'=>'Inactive'),$user['rc_logo']);
echo "</td></tr>";

echo "<tr><td>Max Campaign Quota<br/>"; 
echo form_input(array('name'=>'max_campaign_quota','id'=>'max_campaign_quota','value'=>$user_package['max_campaign_quota'] )) ."</td>";
echo "<td>Campaign Sent Counter<br/>"; 
echo form_input(array('name'=>'campaign_sent_counter','id'=>'campaign_sent_counter','value'=>$user_package['campaign_sent_counter'] )) ."</td>";
echo "<td rowspan='2' style='border-left:3px solid #00ff00;'>
<ul style='color:#ff0000;font-size:9px;'>
<li> - \"Max Campaign Quota\" is calculated as \"Package-Max-Contact\" * \"Package-Quota-Multiplier\" *  \"User-Specific-Quota-Multiplier\". </li>
<li> - For permanent quota increase update \"User specific quota multiplier\". This will be effective even after package change by user.</li>
<li> - For permanent quota increase update \"Max Campaign Quota\". This will be reset after package change by user.</li>
<li> - For one time quota increase reset/reduce \"Campaign Sent Counter\"</li></ul>

</td>";
echo "</tr>";

echo "<tr><td colspan='2'>User specific quota multiplier<br/>"; 
echo form_input(array('name'=>'user_quota_multiplier','id'=>'user_quota_multiplier','value'=>($user_package['user_quota_multiplier'] * $user_package['quota_multiplier']) ,'style'=>"width:50px")) ."

<input type='button' name='btnUpdateQuota' value='Update' onclick=\"javascript:updateCampaignQuota();\" /><br/>
Click to update \"Max Campaign Quota\" in text box and press submit to save in database. 
</td></tr>";

echo "<tr><td colspan='2'>Member DNM List<br/>"; 
echo form_input(array('name'=>'member_dnm','id'=>'member_dnm','value'=>$user['member_dnm'], 'style'=>'width:550px;'));
echo "</td>
<td style='border-left:3px solid #0000ff;'>
<ul style='color:#ff0000;font-size:9px;'>
<li> - This comma separated filter will be added to the Global DNM filter and then CAMPAIGN will not be sent to the matching contacts. </li>
</ul>
</td></tr>";

/* echo "<tr><td colspan='2'>Member Unresponsive List<br/>"; 
echo form_input(array('name'=>'member_unresponsive','id'=>'member_unresponsive','value'=>$user['member_unresponsive'], 'style'=>'width:550px;'));
echo "</td>
<td style='border-left:3px solid #ff6500;'>
<ul style='color:#ff0000;font-size:9px;'>
<li> - This field and GLOBAL UNRESPONSIVE field are exclusive to each other. </li>
<li> - If this comma separated list has any entry, this will be used for UNRESPONSIVE filter. </li>
<li> - If this comma separated list is blank, GLOBAL UNRESPONSIVE filter will be used. </li>
</ul>
</td></tr>"; */

echo "<tr><td colspan='2'>Unresponsive Release Count<br/>"; 
echo form_input(array('name'=>'unresponsive_release_count','id'=>'unresponsive_release_count','value'=>$user['unresponsive_release_count'], 'style'=>'width:550px;'));
echo "</td>
<td style='border-left:3px solid #33aa00;'>
<ul style='color:#ff0000;font-size:9px;'>
<li> - This much UNRESPONSIVE contacts will get Campaign for major webmails (GMail, Yahoo, Hotmail, AOL). </li>
</ul>
</td></tr>";
 

echo "<tr><td>Coupon-code<br/>"; 
echo form_input(array('name'=>'coupon_code','id'=>'coupon_code','maxlength'=>50,'size'=>40 ,'value'=>$user_package['coupon_code_used'])) ;
echo "</td>";
echo "<td>Coupon attached on Date<br/>"; 
echo form_input(array('name'=>'coupon_attached_on','id'=>'coupon_attached_on','maxlength'=>50,'size'=>40 ,'value'=>$user_package['coupon_attached_on'])) ;
echo '<SCRIPT type="text/javascript"> $(function(){$("#coupon_attached_on").datepicker({ dateFormat: "yy-mm-dd" });});	</SCRIPT>';
echo "</td>";
echo "<td rowspan='2'>"; 
$is_checked = ($user['stop_campaign_approval'])? 'checked' : ''; 
echo "Stop Campaign Approval <input type='checkbox' name='stop_campaign_approval' id='stop_campaign_approval' value='yes' $is_checked />";
$is_checked = ($user['show_sent_counter'])? 'checked' : ''; 
echo "<br/> Show Sent Counter <input type='checkbox' name='show_sent_counter' id='show_sent_counter' value='yes' $is_checked />";
$is_checked = ($user['is_pausable'])? 'checked' : ''; 
echo "<br/> Campaign can be paused by system <input type='checkbox' name='is_pausable' id='is_pausable' value='yes' $is_checked />";
$is_checked = ($user['apply_unauthentication_message'])? 'checked' : ''; 
echo "<br/> List-growing dashboard msg. can be attached <input type='checkbox' name='apply_unauthentication_message' id='apply_unauthentication_message' value='yes' $is_checked />";
$is_checked = ($user['reply_to_enabled'])? 'checked' : ''; 
echo "<br/> \"Reply-to\" Enabled <input type='checkbox' name='reply_to_enabled' id='reply_to_enabled' value='yes' $is_checked />";
echo "</td></tr>";
  
echo "<tr><td>Attach Referrer<br/>"; 
echo form_dropdown('referrer',$referrer,$user['ls_site_id']);
echo "</td>";
 echo "<td>Next Payment Date<br/>"; 
echo form_input(array('name'=>'next_payement_date','id'=>'next_payement_date','maxlength'=>50,'size'=>40 ,'value'=>$user_package['next_payement_date'])) ;
echo '<SCRIPT type="text/javascript"> $(function(){$("#next_payement_date").datepicker({ dateFormat: "yy-mm-dd" });});	</SCRIPT>';
echo "</td></tr>";
 
echo "<tr><td>Cancellation Reason<br/>"; 
echo form_input(array('name'=>'cancel_type','id'=>'cancel_type','maxlength'=>50,'size'=>40 ,'value'=>$user_package['cancel_type'])) ;
echo "</td><td>Cancellation Note<br/>"; 
echo form_input(array('name'=>'cancel_reason','id'=>'cancel_reason','maxlength'=>50,'size'=>40 ,'value'=>$user_package['cancel_reason']));
echo "</td></tr>"; 
  
  
echo '<tr><td><br>';
echo form_submit(array('name' => 'btnEdit', 'id' => 'btnEdit','class'=>'inputbuttons','content' => 'edit'), 'Submit');
echo '&nbsp;';
echo form_button(array('name'=>'btnCancel','class'=>'inputbuttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'webmaster/users_manage/users_list'."'"));
echo "</td></tr>";
echo form_hidden('member_unresponsive','');
echo form_hidden('action','submit');
#echo form_hidden('package_max_contacts',$user_package['package_max_contacts']);
#echo form_hidden('quota_multiplier',$user_package['quota_multiplier']);
echo form_input(array('name' => 'package_max_contacts', 'type'=>'hidden', 'id' =>'package_max_contacts','value'=>$user_package['package_max_contacts']));
echo form_input(array('name' => 'quota_multiplier', 'type'=>'hidden', 'id' =>'quota_multiplier','value'=>$user_package['quota_multiplier']));
echo form_hidden('member_id',$user['member_id']);
echo form_close();
echo "</table>";
?>
</div>
<script language="javascript" type="text/javascript">
function updateCampaignQuota(){
var package_max_contacts = $('#package_max_contacts').val();
var quota_multiplier = $('#quota_multiplier').val();
var user_quota_multiplier = $('#user_quota_multiplier').val();
$('#max_campaign_quota').val(package_max_contacts * user_quota_multiplier);

}
</script>
