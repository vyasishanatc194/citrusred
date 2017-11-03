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
 <div style="text-align: right;"><a href="<?php echo base_url() ?>webmaster/cms/page_create"><strong>Create New Page</strong></a></div>
<div class="tblheading">Manage Pages 
<?php //Display paging links
echo $paging_links ?> </div>

<table class="tbl_listing" width="100%"> 
	<thead> 
		<tr> 
			<th width="5%">
			ID
			</th>
			<th width="15%">
				 Page
			</th>
			<th width="15%">
				 Title
			</th>
			<th width="20%">
				Keyword
			</th>
			<th width="20%">
				Description
			</th>
			<th width="15%">
				h1
			</th>
			<th width="10%">
				Edit/Delete
			</th>
		</tr> 
	</thead> 
	<?php 
//List all pages
if(count($pages_array)) {
foreach($pages_array as $page){
?>
<tr>
	<td width="5%">
		<?php echo $page['id'];  ?>
	</td>
	<td width="15%">
		<?php echo $page['page'];  ?>
	</td>
	<td width="15%">
		<?php echo $page['title'];  ?>
	</td>
	<td width="20%">
		<?php echo $page['keyword'];  ?>
	</td>
	<td width="20%">
		<?php echo $page['description'];  ?>
	</td>
	<td width="20%">
		<?php echo $page['h1'];  ?>
	</td>
	<td width="10%">
		<a href="<?php echo  site_url('webmaster/cms/page_edit/'.$page['id']);?>" >edit</a> /
		<a onclick="return confirm('Are you sure to delete page')" href="<?php echo  site_url('webmaster/cms/page_delete/'.$page['id']);?>" >delete</a>
	</td>
</tr>
<?php } } else { ?>
<tr><td colspan="8" align="center">No Page Available</td></tr>
<?php } ?>
</table> 


