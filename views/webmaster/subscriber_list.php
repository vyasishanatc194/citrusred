<form name="Src_frm" id="Src_frm" method="post" action="<?php echo ($user == 1) ? base_url().'webmaster/users_manage/get_contacts/'.$user_id : base_url().'webmaster/campaign/get_contacts/'.$id."/".$user_id;?>">
	<table border="0" cellspacing="0" cellpadding="0" class="tbl_forms">
		<tr>
			<td>Keyword</td>
			<td><?php echo form_input(array('name'=>'keyword','id'=>'keyword','maxlength'=>50,'size'=>40 ,'value'=>$contacts['keyword'])) ; ?></td>
			<td>Email Address</td>
			<td><?php echo form_input(array('name'=>'subscriber_email_address','id'=>'subscriber_email_address','maxlength'=>50,'size'=>40 ,'value'=>$contacts['subscriber_email_address'])) ; ?></td>
			<?php if($user == 1) {?>
				<td>name</td>
				<td><?php echo form_input(array('name'=>'subscriber_name','id'=>'subscriber_name','maxlength'=>50,'size'=>40 ,'value'=>$contacts['subscriber_name'])) ; ?></td>
			<?php } ?>
		</tr>
		<tr>
			<td><input type="hidden" name="mode" value="search"/></td>
			<td><input type="submit" name="btn_search" id="btn_search" value="Search" class="inputbuttons"/>
			<input type="submit" name="btn_cancel" id="btn_cancel" value="Show All" class="inputbuttons"/></td>
		</tr>
	</table>
</form>
<div class="tblheading">Contact List Campaigns <?php //Display paging links
echo $paging_links ?></div>
<table class="tbl_listing" width="100%">
	<thead>
		<tr>
			<th width="50%">
				 Email Address
			</th>
			<th width="25%">
				 First Name
			</th>
			<th width="25%">
				 Last Name
			</th>
		</tr>
	</thead>
	<?php
//List all users
if(count($email_subscribers)) {
foreach($email_subscribers as $email_subscriber){
?>
<tr>
	<td width="40%">
		<?php echo $email_subscriber['subscriber_email_address'];  ?>
	</td>
	<td width="25%">
		<?php echo $email_subscriber['subscriber_first_name'];  ?>
	</td>
	<td width="25%">
		<?php echo $email_subscriber['subscriber_last_name'];  ?>
	</td>
</tr>
<?php
 } } else { ?>
<tr><td colspan="4" align="center">No Contact Available</td></tr>
<?php } ?>
</table>
