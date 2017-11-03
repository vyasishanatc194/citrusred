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
 <div style="text-align: right;"><a href="<?php echo base_url() ?>webmaster/support_content/support_content_create"><strong>Create New Support Content</strong></a></div>
<div class="tblheading">Manage Support Content 
<?php //Display paging links
echo $paging_links ?> </div>

<table class="tbl_listing" width="100%"> 
	<thead> 
		<tr> 
			<th width="10%">
			ID
			</th>
			<th width="20%">
				Category
			</th>
			<th width="40%">
				Product
			</th>			
			<th width="15%">
				 Status
			</th>			
			<th width="15%">
				Edit/Delete
			</th>
		</tr> 
	</thead> 
	<?php 
//List all contents
if(count($contents)) {
foreach($contents as $content){
?>
<tr>
	<td width="10%">
		<?php echo $content['id'];  ?>
	</td>
	<td width="20%">
		<?php  echo $content['category']; ?>
	</td>
	<td width="40%">
		<?php  echo $content['product']; ?>
	</td>
	<td width="15%">
		<?php if( $content['is_active']==1) echo 'Active';else echo 'InActive';  ?>
	</td>
	<td width="15%">
		<a href="<?php echo  site_url('webmaster/support_content/support_content_edit/'.$content['id']);?>" >edit</a> /	
		<a onclick="return confirm('Are you sure to delete product')" href="<?php echo  site_url('webmaster/support_content/support_content_delete/'.$content['id']);?>" >delete</a>	
	</td>
</tr>
<?php } } else { ?>
<tr><td colspan="5" align="center">No Support Content Available</td></tr>
<?php } ?>
</table> 


