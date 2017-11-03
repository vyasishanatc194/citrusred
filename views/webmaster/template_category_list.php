
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
 <div style="text-align: right;"><a href="<?php echo base_url() ?>webmaster/template_category/category_create"><strong>Create New Template Category</strong></a></div>
<div class="tblheading">Manage Template Category 
<?php //Display paging links
echo $paging_links ?> </div>

<table class="tbl_listing" width="100%"> 
	<thead> 
		<tr> 
			<th width="10%">
			ID
			</th>
			<th width="40%">
				Title
			</th>
			<th width="20%">
				 Status
			</th>			
			<th width="30%">
				Edit/Delete
			</th>
		</tr> 
	</thead> 
	<?php 
//List all category
if(count($category)) {
foreach($category as $categories){
?>
<tr>
	<td width="10%">
		<?php echo $categories['red_theme_id'];  ?>
	</td>
	<td width="40%">
		<?php  echo $categories['red_theme_name']; ?>
	</td>
	<td width="20%">
		<?php if( $categories['red_is_active']==1) echo 'Active';else echo 'InActive';  ?>
	</td>
	<td width="30%">
		<a href="<?php echo  site_url('webmaster/template_category/category_edit/'.$categories['red_theme_id']);?>" >edit</a> /	
		<a onclick="return confirm('Are you sure to delete category')" href="<?php echo  site_url('webmaster/template_category/category_delete/'.$categories['red_theme_id']);?>" >delete</a>	
	</td>
</tr>
<?php } } else { ?>
<tr><td colspan="8" align="center">No Listing Category Available</td></tr>
<?php } ?>
</table> 


