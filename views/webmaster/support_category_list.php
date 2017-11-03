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
 <div style="text-align: right;"><a href="<?php echo base_url() ?>webmaster/support_category/support_category_create"><strong>Create New Support Category</strong></a></div>
<div class="tblheading">Manage Support Category 
<?php //Display paging links
echo $paging_links ?> </div>

<table class="tbl_listing" width="100%"> 
	<thead> 
		<tr> 
			<th width="20%">
			ID
			</th>
			<th width="40%">
				Category
			</th>			
			<th width="25%">
				 Status
			</th>			
			<th width="25%">
				Edit/Delete
			</th>
		</tr> 
	</thead> 
	<?php 
//List all category
if(count($categories)) {
foreach($categories as $category){
?>
<tr>
	<td width="20%">
		<?php echo $category['id'];  ?>
	</td>
	<td width="40%">
		<?php  echo $category['category']; ?>
	</td>
	<td width="25%">
		<?php if( $category['is_active']==1) echo 'Active';else echo 'InActive';  ?>
	</td>
	<td width="25%">
		<a href="<?php echo  site_url('webmaster/support_category/support_category_edit/'.$category['id']);?>" >edit</a> /	
		<a onclick="return confirm('Are you sure to delete category')" href="<?php echo  site_url('webmaster/support_category/support_category_delete/'.$category['id']);?>" >delete</a>	
	</td>
</tr>
<?php } } else { ?>
<tr><td colspan="4" align="center">No Support Category Available</td></tr>
<?php } ?>
</table> 


