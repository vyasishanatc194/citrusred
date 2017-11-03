<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-1.5.1.min.js?v=6-20-13"></script>


<div id="messages" style="color:#FF0000;">
<?php
if (is_array($messages)):
    foreach ($messages as $type => $msgs):
        foreach ($msgs as $message):
            echo ('<span class="' .  $type .'">' . $message . '</span>');
        endforeach;
    endforeach;
endif;

?>
</div>
 <div style="text-align: right;"><a href="javascript:void(0);" onclick="javascript:$('#divAssignMessage').show();"><strong>Assign Message to Member</strong></a></div>
<div id="divAssignMessage" style="margin:20px 0;display:none;">
<div class="tblheading">Assign Message to Memeber</div>
<form name="frmAssignMessage" id="frmAssignMessage" method="post" action="<?php echo base_url() ?>webmaster/manage_messages/assign_message/" >
<table class="tbl_listing" width="60%">
<tr><td><b>Member: </b></td><td><input type="text" name="member_id" /> </td>
<td><b>Message: </b></td><td><select name="message_id">
<option value="">--select--</option>
<?php
foreach($message_list as $msg_rec){
echo "<option value=\"{$msg_rec['message_id']}\">{$msg_rec['message_name']}</option>";

}
?>
</select></td>
<td>
<input type="submit" name="action" value="Submit" />
<input type="reset" name="btnCancel" value="Cancel" onclick="javascript:$('#divAssignMessage').hide();" />
</td></tr>
</table>
</form>
</div>
<div class="tblheading">Filter Message</div>

<table class="tbl_listing" width="60%">
 
<td><b>Message: </b></td><td><select name="mid" onchange="javascript:window.location.href='<?php echo base_url().'webmaster/manage_messages/member_message/';?>'+this.value">
<?php
foreach($message_list as $msg_rec){
if($mid == $msg_rec['message_id'])$strSelected = 'selected';else $strSelected = '';
echo "<option value=\"{$msg_rec['message_id']}\" {$strSelected}>{$msg_rec['message_name']}</option>";
}
?>
</select></td>
</tr>
</table>
</div>
<div class="tblheading">Manage Memeber and Messages </div>
<table class="tbl_listing" width="60%">
	<thead>
		<tr>
			<th width="20%">Member</th>
			<th width="50%">Message</th>
			<th width="20%">AddedDate</th>
			<th width="10%">Remove</th>
		</tr>
	</thead>
	<?php
//List all users
if(count($member_message_list)) {
foreach($member_message_list as $member_message){
?>
<tr>
	<td><?php echo $member_message['member_username'];  ?></td>
	<td><?php echo $member_message['message_name'];  ?></td>
	<td><?php echo $member_message['assigned_date'];  ?></td>
	<td>
		<a href="<?php echo  site_url('webmaster/manage_messages/remove_member_message/'.$member_message['member_id'].'/'.$member_message['message_id']);?>" >Remove</a>
	</td>
</tr>
<?php } } else { ?>
<tr><td colspan="11" align="center">No Member Message Available</td></tr>
<?php } ?>
</table>
