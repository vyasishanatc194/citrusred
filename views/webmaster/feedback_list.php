<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-1.5.1.min.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.mousewheel-3.0.4.pack.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.pack.js?v=6-20-13"></script>
<link href="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.css?v=6-20-13" rel="stylesheet" type="text/css" />
<script type="text/javascript">
    $(document).ready(function(){
    $(".fancybox").fancybox({'autoDimensions':false,'centerOnScroll':true,'height':'200','width':'300','scrolling':'auto'});
    });
</script>
<div id="messages" style="color:#FF0000;">
<?php
// display all messages from template create, edit and listing pages

if (is_array($messages)):
    foreach ($messages as $type => $msgs):
        foreach ($msgs as $message):
            echo ('<span class="' .  $type .'">' . $message . '</span>');
        endforeach;
    endforeach;
endif;
?>
</div>
<div class="tblheading">Feedback <?php //display paging links
echo $paging_links;?></div>
<!-- Create table for listing of templates --->
<table id="table1" class="tbl_listing" width="100%">
<tr>
<th width="10%">
	 ID
</th>
<th width="20%">
	Email Address
</th>
<th width="20%">
	 Subject
</th>
<th width="20%">
	 Message
</th>
<th width="10%">
	Date Created
</th>
<th width="20%">
	Read more/Delete
</th>
</tr>
<?php
//Fetch campaings from templates array
if(count($feedbacks)) {
foreach($feedbacks as $feedback){
?>
<tr>
	<td width="10%">
		<?php echo $feedback['id'];  ?>
	</td>
	<td width="20%">
		<?php echo $feedback['email_address'];  ?>
	</td>
	<td width="20%">
		<?php echo $feedback['subject'];  ?>
	</td>
	<td width="20%">
		<?php echo substr($feedback['message'],0,30);  ?>
	</td>
	<td width="10%">
		<?php echo date('d-M-Y', strtotime( $feedback['timestamp']));  ?>
	</td>
	<td width="20%">
		<a href="<?php echo  site_url('webmaster/feedback/message/'.$feedback['id']);?>" class="fancybox">Read more</a> / <a onclick="return confirm('Are you sure to delete feedback')" href="<?php echo  site_url('webmaster/feedback/delete/'.$feedback['id']);?>">Delete</a>
	</td>
</tr>
<?php }
} else { ?>
<tr><td colspan="6" align="center">No Feedback Available</td></tr>
<?php } ?>
</table>
