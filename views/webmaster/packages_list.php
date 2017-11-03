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
<div style="text-align: right;"><a href="<?php echo base_url() ?>webmaster/packages_manage/package_create"><strong>Create New Package</strong></a></div>
<div class="tblheading">Manage Packages <?php //Display paging links
echo $paging_links ?>

</div>
<table class="tbl_listing" width="100%"> 
	<thead> 

<tr><th>ID</th><th>Package Type</th><th>Package Title</th><th>Package Price</th><th>Recurring Interval</th><th>Package Status</th><th>Edit/Delete</th></tr>
<?php 
//List all packages
if(count($packages)) {
foreach($packages as $package){
?>
<tr>
	<td><?php echo $package['package_id'];  ?></td>
	<td><?php echo $package['package_type'];  ?></td>
	<td><?php echo $package['package_title'];  ?></td>
	<td>$<?php echo $package['package_price'];  ?></td>
	<td><?php echo $package['package_recurring_interval'];  ?></td>
	<td><?php if($package['package_status']) echo 'Active';else echo 'Inactive';  ?></td>
	<td><a href="<?php echo  site_url('webmaster/packages_manage/package_edit/'.$package['package_id']);?>" >edit</a> /
		<a onclick="return confirm('Are you sure to delete package')" href="<?php echo  site_url('webmaster/packages_manage/package_delete/'.$package['package_id']);?>" >delete</a>
	</td>
</tr>
<?php } } else { ?>
<tr><td colspan="8" align="center">No Packages Available</td></tr>
<?php } ?>
</table>

<?php echo form_close(); ?>
