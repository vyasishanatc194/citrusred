<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-1.5.1.min.js?v=6-20-13"></script>
<script type="text/javascript">
	$(document).ready(function() {
		jQuery('.show_on_dashboard').live('click',function(){
			if ($(this).attr('checked')) {
				var id=$(this).attr('id');
				jQuery.ajax({
				  url: "<?php echo base_url() ?>webmaster/template_header/display_header_on_dashboard/"+id+"/1",
				  type:"POST",
				  success: function(data) {
				  }
				});
			}else{
				var id=$(this).attr('id');
				jQuery.ajax({
				  url: "<?php echo base_url() ?>webmaster/template_header/display_header_on_dashboard/"+id+"/0",
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
 <div style="text-align: right;"><a href="<?php echo base_url() ?>webmaster/template_header/header_create"><strong>Create New Template header</strong></a></div>
<div class="tblheading">Manage Template Header
<?php //Display paging links
echo $paging_links ?> </div>

<table class="tbl_listing" width="100%">
	<thead>
		<tr>
			<th width="10%">
			ID
			</th>
			<th width="15%">
				Title
			</th>
			<th width="20%">
				Screenshot
			</th>
			<th width="15%">
				Category
			</th>
			<th width="15%">
				 Show on Header Dashboard
			</th>
			<th width="10%">
				 Status
			</th>
			<th width="15%">
				Edit/Delete
			</th>
		</tr>
	</thead>
	<?php
//List all category
if(count($headers)) {
foreach($headers as $header){
?>
<tr>
	<td width="10%">
		<?php echo $header['template_id'];  ?>
	</td>
	<td width="15%">
		<?php  echo $header['template_name']; ?>
	</td>
	<td width="20%">
		<img src="<?php  echo base_url()."webappassets/header-images/header-".$header['template_id'].".jpg"; ?>" alt="screenshot" />
	</td>
	<td width="15%">
		<?php  echo $header['red_theme_name']; ?>
	</td>
	<td width="15%">
		<?php
			if($header['show_on_dashboard']==1)
			{
				$check="checked='checked'";
			}else{
				$check="";
			}
		?>
		<input type="checkbox" name="show_on_dashboard" id="<?php echo $header['template_id'];  ?>" class="show_on_dashboard" value="1" <?php echo $check; ?> />
	</td>
	<td width="10%">
		<?php if( $header['is_active']==1) echo 'Active';else echo 'InActive';  ?>
	</td>
	<td width="30%">
		<a href="<?php echo  site_url('webmaster/template_header/header_edit/'.$header['template_id']);?>" >edit</a> /
		<a onclick="return confirm('Are you sure to delete category')" href="<?php echo  site_url('webmaster/template_header/header_delete/'.$header['template_id']);?>" >delete</a>
	</td>
</tr>
<?php } } else { ?>
<tr><td colspan="8" align="center">No Template Header Available</td></tr>
<?php } ?>
</table>


