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
 <div style="text-align: right;"><a href="<?php echo base_url() ?>webmaster/language_manage/language_create"><strong>Create New Language</strong></a></div>
<div class="tblheading">Manage Languages
<?php //Display paging links
echo $paging_links ?> </div>

<table class="tbl_listing" width="100%"> 
	<thead> 
		<tr> 
			<th width="10%">
			ID
			</th>
			<th width="20%">
				Language
			</th>			
			<th width="10%">
				Code
			</th>
		</tr> 
	</thead> 
	<?php 
//List all color
if(count($languages)) {
foreach($languages as $language){
?>
<tr>
	<td>
		<?php echo $language['id'];  ?>
	</td>
	<td>
		<?php  echo $language['language']; ?>
	</td>
	<td>
		<?php  echo $language['language_code']; ?>
	</td>
	
</tr>
<?php } } else { ?>
<tr><td colspan="8" align="center">No Template Color Available</td></tr>
<?php } ?>
</table> 


