<div id="messages" style="color:#FF0000;">
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
 
<div class="tblheading">Users Notifications <?php //Display paging links
echo $paging_links ?> </div>
<table class="tbl_listing" width="100%"> 
	<thead> 
		<tr> 
			<th>
			ID
			</th>
			
			<th>
				Email Address
			</th>
			<th>
				Username
			</th>
			<th>
				Notification
			</th>			
		</tr> 
	</thead> 
	<?php 
//List all users
if(count($users)) {
$notification=false;
foreach($users as $user){
	if($user['upgrade_package']){
		$notification=true;
?>
<tr>
	<td>
		<?php echo $user['member_id'];  ?>
	</td>	
	<td>
		<?php echo $user['email_address'];  ?>
	</td>
	<td>
		<?php echo $user['member_username'];  ?>
	</td>
	<td>
		<?php echo "User Requires upgradation";  ?>
	</td>
	
</tr>
<?php } } } else { ?>
<tr><td colspan="8" align="center">No User Available</td></tr>
<?php } ?>
<?php if(!$notification){?>
<tr><td colspan="8" align="center">No Notification</td></tr>
<?php } ?>
</table> 


