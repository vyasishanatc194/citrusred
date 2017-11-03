<div id="messages" style="color:#FF0000;">
<?php
/// display all messages

if (is_array($messages)):
    foreach ($messages as $type => $msgs):
        foreach ($msgs as $message):
            echo ('<span class="' .  $type .'">' . $message . '</span>');
        endforeach;
    endforeach;
endif;
?>
</div>  
<div class="tblheading">User Packages </div>
<?php 

echo '<div style="color:#FF0000;">'.validation_errors().'</div>'; 
echo form_open('webmaster/users_manage/user_view_package/'.$member_id, array('id' => 'frmPackages','name' => 'frmPackages'));

?>

<table id="table1" class="tbl_listing" width="100%">
<tr>

<th>
	 Title
</th>
<th>
	Package Type
</th>

<th>
	 Price
</th>

<th>
	 Status
</th>
<th>
	 Delete
</th>
</tr>


<?php 
//Fetch packages from packages array 
if(!count($user_packages)) echo '<tr><td colspan="5" align="center"> No Packages </td></tr>';
foreach($user_packages as $package){
?>
<tr>
	
	<td style="padding:5px;">
	
		<?php echo $package['package_title'];  ?>
	</td>
	<td style="padding:5px;">
	
		<?php if( $package['package_recurring_interval']=='months') echo 'Monthly';else echo 'Yearly'; ?>
	</td>
	<td style="padding:5px;">
		$<?php echo $package['package_price'];  ?>
	</td>
	<td style="padding:5px;">
		<?php if( $package['package_status']==1) echo 'Active';else echo 'InActive'; ?>
	</td>
	<td>	
		<a onclick="return confirm('Are you sure to delete package for this user')" href="<?php echo  site_url('webmaster/users_manage/user_delete_package/'.$package['red_member_package_id'].'/'.$member_id);?>" >delete</a>
	</td>
	
</tr>
<?php } ?>
</table>

<?php
echo form_hidden('action','save');

echo form_hidden('member_id',$member_id);
echo form_close();
?>
<br>
&laquo;<a href="<?php echo base_url() ?>webmaster/users_manage/users_list">Back</a>
</center>