<div id="inner_div">

<div id="messages">
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
<?php echo form_open('webmaster/images_manage/images_list', array('id' =>'form_images_list','name'=>'form_images_list','method'=>'post')); ?>

 <br>
 <h3>Manage Images</h3>
 <a href="<?php echo base_url() ?>webmaster/images_manage/image_upload"><strong>Upload Image</strong></a>
 <br><br>
<table id="table1" cellpadding="0" cellspacing="0">
<tr>
<th>
	 SNo
</th>
<th>
	 Title
</th>
<th>
	Thumb
</th>
<th>
	View
</th>
<th>
	Delete
</th>


</tr>
<?php 
//List all packages
if(count($images)) {
for($i=0;$i<count($images) ;$i++){
?>
<tr>
	<td>
		<?php echo $i+1;  ?>
	</td>
	<td>
		<?php echo $images[$i];  ?>
	</td>
	<td>
		<img src="<?php echo base_url().'webappassets/image_library/thumbs/'.$images[$i] ?>">
	</td>
	
	<td>
		<a href="<?php echo  site_url('webmaster/images_manage/image_view/'.$images[$i]);?>">View</a>	
	</td>

	
	
	<td>
		
		<a onclick="return confirm('Are you sure to delete image')" href="<?php echo  site_url('webmaster/images_manage/image_delete/'.$images[$i]);?>" >delete</a>
	</td>
</tr>
<?php } } else { ?>
<tr><td colspan="8" align="center">No Images  Available</td></tr>
<?php } ?>
</table>
<div style="padding:10px;">
<?php //Display paging links
echo $paging_links ?>
</div>
<?php echo form_close(); ?>
</div>