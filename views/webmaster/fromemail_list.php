<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-1.5.1.min.js?v=6-20-13"></script> 
 
<form name="Src_frm" id="Src_frm" method="post" action="<?php echo base_url().'webmaster/fromemail/index/';?>">
	<table border="0" cellspacing="0" cellpadding="0" class="tbl_forms">
		<tr>
			<td width="10%">Username</td>
			<td><?php echo form_input(array('name'=>'username','id'=>'username','maxlength'=>50,'size'=>40 ,'value'=>$contacts_array['username'])) ; ?></td>
			
		</tr>
		<tr>
			<td><input type="hidden" name="mode" value="search"/></td>
			<td colspan="1">
				<input type="submit" name="btn_search" id="btn_search" value="Search" class="inputbuttons"/>
				<input type="submit" name="btn_cancel" id="btn_cancel" value="Show All" class="inputbuttons"/>
			</td>
		</tr>
	</table>
</form>
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
<div class="tblheading">Unverified From Emails <?php //Display paging links
echo $paging_links ?> </div>
<table class="tbl_listing" width="100%">
	<thead>
		<tr>
			<th width="20%"> Username </th>
			<th width="20%"> New From Email</th>					
			<th width="50%"> Reason to add</th>					
			<th width="10%"> Action </th>						
		</tr>
	</thead>
	<?php
//List all users
if(count($fromemails)) {
	foreach($fromemails as $thisFromemail){

	?>
	<tr>
		<td width="20%">
			<a href="<?php echo  site_url("webmaster/users_manage/users_list/".$thisFromemail['member_username']); ?>"><?php echo $thisFromemail['member_username'];  ?></a>
		</td>
		<td width="20%"> <?php echo $thisFromemail['email_address'];?> </td>
		<td width="20%"> <?php echo $thisFromemail['domain_reason'];?> </td>
	 
		<td>
		<a onclick="return confirm('Are you sure to verify this email')" href="<?php echo base_url() ?>webmaster/fromemail/verifyit/<?php echo $thisFromemail['unique_string'];?>" > Verify	</a>
		&nbsp; | &nbsp;
		<a onclick="return confirm('Are you sure to disallow this email')" href="<?php echo base_url() ?>webmaster/fromemail/disallow/<?php echo $thisFromemail['unique_string'];?>" > Disallow	</a>
		&nbsp; | &nbsp;		
		<a onclick="return confirm('Are you sure to remove this email')" href="<?php echo base_url() ?>webmaster/fromemail/remove/<?php echo $thisFromemail['unique_string'];?>" > Remove	</a>
		</td>		
	</tr>
<?php }
	}else{
?>
	<tr><td colspan="10" align="center">No From Email For Verification Available</td></tr>
<?php } ?>
</table>
