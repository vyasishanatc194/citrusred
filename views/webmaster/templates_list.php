<div id="messages" style="color:#FF0000;">
<?php
// display all messages from template create, edit and listing pages

if (is_array($messages)):
    foreach ($messages as $type => $msgs):
        foreach ($msgs as $message):
            echo ('<span class="' .  $type .'">' . $message . '</span>');
        endforeach;
    endforeach;
endif;
?>
</div>  
 <div style="text-align: right;"><a href="<?php echo  site_url("webmaster/templates_manage/template_create");?>">Create Template</a></div>
<div class="tblheading">Templates <?php //display paging links
echo $links;?></div>
<!-- Create table for listing of templates --->
<table id="table1" class="tbl_listing" width="100%">
<tr>
<th>
	 ID
</th>
<th>
	 Title
</th>
<th>
	 Status
</th>
<th>
	Date Created
</th>
<th>
	Edit/Delete
</th>
</tr>
<?php 
//Fetch campaings from templates array 
if(count($templates)) {
foreach($templates as $template){
?>
<tr>
	<td>
		<?php echo $template['template_id'];  ?>
	</td>
	<td>
		<?php echo $template['template_title'];  ?>
	</td>
	<td>
		<?php if( $template['template_status']) echo 'Active';else echo 'InActive';  ?>
	</td>
	<td>
		<?php echo date('d-M-Y', strtotime( $template['template_date_added']));  ?>
	</td>
	<td>
		<a href="<?php echo  site_url('webmaster/templates_manage/template_edit/'.$template['template_id']);?>" >edit</a> /
		<a onclick="return confirm('Are you sure to delete template')" href="<?php echo  site_url('webmaster/templates_manage/template_delete/'.$template['template_id']);?>" >delete</a>
	</td>
</tr>
<?php }
} else { ?>
<tr><td colspan="6" align="center">No Templates Available</td></tr>
<?php } ?>
</table>
