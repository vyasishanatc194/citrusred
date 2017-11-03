
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
 <div style="text-align: right;"><a class="btn" href="<?php echo  site_url('webmaster/post_image_upload/image_create/'.$post_id);?>"><strong>Add New Image</strong></a></div>
<div class="tblheading">Manage  Post Images 
<?php //Display paging links
echo $paging_links ?> </div>

<table class="tbl_listing" width="100%"> 
	<thead> 
		<tr> 
			<th>
	
			</th>

			<th>
				Image Name
			</th>
					<!--	<th>
				 Default
			</th>-->
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
		<?php echo '<img src="'.$this->config->item('blog_files').$categories['img_name'].'" width="50" height="50">';  ?>
	</td>

	<td>
		<?php echo $categories['img_name'];  ?>
	</td>
	<!--<td>
		<?php //echo $categories['img_default'];  ?>
	</td>-->
	<td>
		<?php if($categories['img_status']==0){
				echo "Inactive" ; 
			}else{
				echo 'Active';
			}	?>
	</td>

	
	<td>

		<?php 

		?>
		<a onclick="return confirm('Are you sure to delete image')" href="<?php echo  site_url('webmaster/post_image_upload/image_delete/'.$post_id.'/'.$categories['img_id']);?>" >delete</a>
		
	</td>
</tr>
<?php } } else { ?>
<tr><td colspan="8" align="center">No Listing Image Available</td></tr>
<?php } ?>
</table> 


