
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
 <div style="text-align: right;"><a class="btn" href="<?php echo  site_url('webmaster/blog_listing_category/category_create/');?>"><strong>Create New Listing Category</strong></a></div>
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
		<?php echo $categories['id'];  ?>
	</td>
	<td>
		<?php  
			echo $categories['category_name'];
		?>
	</td>
	<td>
		<?php if($categories['category_status']==0){
				echo "Inactive" ; 
			}else{
				echo 'Active';
			}	?>
	</td>

	
	<td>
		<a href="<?php echo  site_url('webmaster/blog_listing_post/post_list/'.$categories['id']);?>" >Posts</a> / <a href="<?php echo  site_url('webmaster/blog_listing_category/category_edit/'.$categories['id']);?>" >edit</a> /
		<?php 

		?>
		<a onclick="return confirm('Are you sure to delete category')" href="<?php echo  site_url('webmaster/blog_listing_category/category_delete/'.$categories['id']);?>" >delete</a>
		
	</td>
</tr>
<?php } } else { ?>
<tr><td colspan="8" align="center">No Listing Category Available</td></tr>
<?php } ?>
</table> 


