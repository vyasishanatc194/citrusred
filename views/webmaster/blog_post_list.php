<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-1.5.1.min.js?v=6-20-13"></script>
<script type="text/javascript">
	$(document).ready(function() {
		jQuery('.add_comment').live('click',function(){
			if ($(this).attr('checked')) {
				var id=$(this).attr('id');
				var id_arr=id.split('_');
				jQuery.ajax({
				  url: "<?php echo base_url() ?>webmaster/blog_listing_post/mark_comment/"+id_arr[2]+"/1",
				  type:"POST",
				  success: function(data) {
				  }
				});
			}else{
				var id=$(this).attr('id');
				var id_arr=id.split('_');
				jQuery.ajax({
				  url: "<?php echo base_url() ?>webmaster/blog_listing_post/mark_comment/"+id_arr[2]+"/0",
				  type:"POST",
				  success: function(data) {
				  }
				});
			}
		});
	});
</script>
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
 <div style="text-align: right;"><a class="btn" href="<?php echo  site_url('webmaster/blog_listing_post/post_create/'.$cat_id);?>"><strong>Create New Post</strong></a></div>
<div class="tblheading">Manage Listing Post
<?php //Display paging links
echo $paging_links ?> </div>
	<ul class="tabs">
		<li><a href="<?php echo  site_url('webmaster/blog_listing_post/post_list_status/'.$cat_id.'/1');?>" <?php if($status==1 && $archive==0) echo 'class="active"';?>>Active</a></li>
		<li><a href="<?php echo  site_url('webmaster/blog_listing_post/post_list_status/'.$cat_id.'/0');?>" <?php if($status==0 && $archive==0) echo 'class="active"';?>>Inactive</a></li>
	</ul>
<table class="tbl_listing" width="100%" style="border: 1px solid rgb(222, 222, 222);">
	<thead>
		<tr>
			<th width="10%">
				Title
			</th>
			<th width="30%">
				desc
			</th>
			<th width="5%">
				 Status
			</th>
			<th width="30%">
				 Comments
			</th>
			<th width="5%">
				Mark Comments
			</th>

			<th width="10%">
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
	<!--<td>
		<?php //echo $categories['id'];  ?>
	</td>-->

	<td width="10%">
		<?php echo $categories['title'];  ?>
	</td>
	<td width="30%">
		<?php
			$desc=str_replace("../http", "http", $categories['desc']);
			$desc=str_replace("../../../../asset", base_url()."asset", $desc);
			$desc=str_replace("../../../asset", base_url()."asset", $desc);
			echo $desc;
		?>
	</td>
	<td width="5%">
		<?php if($categories['status']==0){
				echo '<a href="'.site_url('webmaster/blog_listing_post/mark_active/'.$cat_id.'/'.$categories['id']).'" >Mark Active</a>' ;
			}else{
				echo '<a href="'.site_url('webmaster/blog_listing_post/mark_inactive/'.$cat_id.'/'.$categories['id']).'" >Mark InActive</a>';
			}	?>
	</td>
	<td width="30%">
		<a href="<?php echo site_url('webmaster/blog_comments_list/display/'.$categories['id']); ?>">Comments</a>
	</td>
	<td width="5%">
		<?php if($categories['add_comment']==1){?>
			<input type="checkbox" name="add_comment_<?php echo $categories['id']; ?>" id="add_comment_<?php echo $categories['id']; ?>" value="1" class="add_comment" checked />
		<?php }else{ ?>
			<input type="checkbox" name="add_comment_<?php echo $categories['id']; ?>" id="add_comment_<?php echo $categories['id']; ?>" value="1" class="add_comment" />
		<?php } ?>
	</td>


	<td width="10%">
		<a href="<?php echo  site_url('webmaster/post_image_upload/listing/'.$categories['id']);?>" >Add Images</a> / <a href="<?php echo  site_url('webmaster/blog_listing_post/post_edit/'.$cat_id.'/'.$categories['id']);?>" >edit</a> /
		<?php

		?>
		<a onclick="return confirm('Are you sure to delete post')" href="<?php echo  site_url('webmaster/blog_listing_post/post_delete/'.$cat_id.'/'.$categories['id']);?>" >delete</a>

	</td>
</tr>
<?php } } else { ?>
<tr><td colspan="8" align="center">No Listing Post Available</td></tr>
<?php } ?>
</table>


