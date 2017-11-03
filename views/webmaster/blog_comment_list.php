
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
 
<div class="tblheading">Manage Comment Listing
<?php //Display paging links
echo $paging_links ?> </div>
<table class="tbl_listing" width="100%" style="border: 1px solid rgb(222, 222, 222);"> 
	<thead> 
		<tr>
			<th>
				Comment
			</th>
			<th>
				Comment From
			</th>
			<th>
				 Added On
			</th>			
			<th>
				Delete
			</th>
		</tr> 
	</thead> 
	<?php 
//List all comments
if(count($comments)) {
foreach($comments as $commnet){
?>
<tr>
	<td>
		<?php echo $commnet['comment'];  ?>
	</td>
	<td>
		<?php echo $commnet['name'];  ?>
	</td>
	<td>
		<?php 
			$date = explode("-",$commnet['added_on']);
			echo $dateNew =  date("M d, Y", mktime(0, 0, 0, $date[1], $date[2], $date[0]));	
		?>
	</td>	
	<td>
		<a onclick="return confirm('Are you sure to delete comment')" href="<?php echo  site_url('webmaster/blog_comments_list/delete/'.$entry_id.'/'.$commnet['id']);?>" >delete</a>
		
	</td>
</tr>
<?php } } else { ?>
<tr><td colspan="8" align="center">No Listing Post Available</td></tr>
<?php } ?>
</table> 


