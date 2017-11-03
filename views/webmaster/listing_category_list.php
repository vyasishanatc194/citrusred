
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
 <div style="text-align: right;"><a href="<?php echo base_url() ?>webmaster/listing_category/category_create"><strong>Create New Listing Category</strong></a></div>
<div class="tblheading">Manage Listing Category 
<?php //Display paging links
echo $paging_links ?> </div>

<table class="tbl_listing" width="100%"> 
	<thead> 
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
				Sub Category
			</th>
			<th>
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
	<td>
		<?php echo $categories['listing_category_id'];  ?>
	</td>
	<td>
		<?php  
		//Check Category is parent or sub category
			if($categories['listing_category_parent']>0){
				echo "<b>->" .$categories['listing_category_title']."</b>"; 
			}else{
				echo $categories['listing_category_title'];
			}		?>
	</td>
	<td>
		<?php if( $categories['listing_category_status']==1) echo 'Active';else echo 'InActive';  ?>
	</td>
	<td>
		<?php echo date('d-M-Y', strtotime( $categories['listing_category_created_on']));  ?>
	</td>
	<td>
	<?php 
		// Check whether create subcategory or not under category
		if($categories['listing_category_parent']>0){
		
	?>
		<a onclick="alert('You can not create subcategory under subcategory')" href="javascript:void(0);" >Create Sub category</a>
	<?php }else{ ?>
		<a href="<?php echo  site_url('webmaster/listing_category/category_create/'.$categories['listing_category_id']);?>" >Create Sub Category</a>
	<?php } ?>
	</td>
	
	<td>
		<a href="<?php echo  site_url('webmaster/listing_category/category_edit/'.$categories['listing_category_id']);?>" >edit</a> /
		<?php 
			if($categories['delete']==1){
		?>
		<a onclick="return confirm('Are you sure to delete category')" href="<?php echo  site_url('webmaster/listing_category/category_delete/'.$categories['listing_category_id']);?>" >delete</a>
		<?php } else{ ?>
		<a onclick="return alert('You can not  delete this category because it contains subcategories ')" href="javascript:void(0);" >delete</a>
		<?php } ?>
	</td>
</tr>
<?php } } else { ?>
<tr><td colspan="8" align="center">No Listing Category Available</td></tr>
<?php } ?>
</table> 


