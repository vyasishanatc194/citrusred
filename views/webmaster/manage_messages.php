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
 <div style="text-align: right;"><a href="<?php echo base_url() ?>webmaster/manage_messages/message_create"><strong>Create New Message</strong></a></div>
<div class="tblheading">Manage Messages </div>

<table class="tbl_listing" width="100%">
	<thead>
		<tr>
			<th width="5%">ID</th>
			<th width="20%">Message Head</th>
			<!--th width="10%">Message Type</th>
			<th width="10%">User Type</th>
			<th width="10%">Body Type</th-->
			<th width="30%">Message Body</th>
			<th width="5%">Is Mail</th>
			<th width="10%">Email Subject</th>
			<th width="10%">Email Body</th>
			<th width="5%">Status</th>
			<th width="5%">Edit</th>
		</tr>
	</thead>
	<?php
//List all users
if(count($message_list)) {
foreach($message_list as $messages){
?>
<tr>
	<td><?php echo $messages['message_id'];  ?></td>
	<td><?php echo $messages['message_name'];  ?></td>
	<td><?php echo $messages['message_body'];  ?></td>
	<td><?php echo ('0' == $messages['is_mail_notification'])?'No':'Yes';  ?></td>
	<td><?php 
		//echo ('0' == $messages['message_type'])?'System':'Admin';  
		echo $messages['email_subject'];
	?></td>
	<td><?php
		//echo ('0' == $messages['user_type'])?'Bulk':'Individual';  
		echo $messages['email_body'];	
	?></td>
	<!-- td>< ? php switch($messages['message_body_type']){
		case 0: echo 'On Dashboard';break;
		case 1: echo 'Global on header';break;
		case 2: echo 'On a Page';break;
		}  ? ></td-->


	<td><?php echo ('0' == $messages['message_status'])?'In-Active':'Active';  ?></td>

	<td>
		<a href="<?php echo  site_url('webmaster/manage_messages/message_edit/'.$messages['message_id']);?>" >edit</a>
	</td>
</tr>
<?php } } else { ?>
<tr><td colspan="11" align="center">No Message Available</td></tr>
<?php } ?>
</table>
