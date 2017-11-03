
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
 <div style="text-align: right;"><a class="btn" href="<?php echo  site_url('webmaster/blog_banned_words/banned_create/');?>"><strong>Add New Banned Word</strong></a></div>
<div class="tblheading">Manage Banned Word
<?php //Display paging links
echo $paging_links ?> </div>

<table class="tbl_listing" width="100%"> 
	<thead> 
		<tr> 
			<th>
			ID
			</th>
			<th>
				Banned Word
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
		<?php echo $categories['ban_id'];  ?>
	</td>
	<td>
		<?php  
			echo $categories['ban_word'];
		?>
	</td>


	
	<td>
		<a href="<?php echo  site_url('webmaster/blog_banned_words/banned_edit/'.$categories['ban_id']);?>" >edit</a> /
		<?php 

		?>
		<a onclick="return confirm('Are you sure to delete category')" href="<?php echo  site_url('webmaster/blog_banned_words/banned_delete/'.$categories['ban_id']);?>" >delete</a>
		
	</td>
</tr>
<?php } } else { ?>
<tr><td colspan="8" align="center">No Listing Banned Words Available</td></tr>
<?php } ?>
</table> 


