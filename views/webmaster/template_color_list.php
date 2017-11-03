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
 <div style="text-align: right;"><a href="<?php echo base_url() ?>webmaster/template_color/color_create"><strong>Create New Template Color</strong></a></div>
<div class="tblheading">Manage Template Color 
<?php //Display paging links
echo $paging_links ?> </div>

<table class="tbl_listing" width="100%"> 
	<thead> 
		<tr> 
			<th width="10%">
			ID
			</th>
			<th width="20%">
				Title
			</th>			
			<th width="10%">
				Outer BG Color
			</th>
			<th width="10%">
				 Body BG Color
			</th>
			<th width="10%">
				 Footer BG Color
			</th>
			<th width="10%">
				 Border BG Color
			</th>			
			<th width="10%">
				Footer Font
			</th>
			<th width="15%">
				Status
			</th>			
			<th width="15%">
				Edit/Delete
			</th>
		</tr> 
	</thead> 
	<?php 
//List all color
if(count($colors)) {
foreach($colors as $color){
?>
<tr>
	<td width="10%">
		<?php echo $color['id'];  ?>
	</td>
	<td width="20%">
		<?php  echo $color['theme_name']; ?>
	</td>
	<td width="10%" style="background-color:<?php echo $color['outer_bg']; ?>">
		<?php  echo $color['outer_bg']; ?>
	</td>
	<td width="10%" style="background-color:<?php echo $color['body_bg']; ?>">
		<?php  echo $color['body_bg']; ?>
	</td>
	<td width="10%" style="background-color:<?php echo $color['footer_bg']; ?>">
		<?php  echo $color['footer_bg']; ?>
	</td>
	<td width="10%" style="background-color:<?php echo $color['border_color']; ?>">
		<?php  echo $color['border_color']; ?>
	</td>
	<td width="10%" style="background-color:<?php echo $color['footer_font_color']; ?>">
		<?php  echo $color['footer_font_color']; ?>
	</td>	
	<td width="15%">
		<?php if( $color['is_active']==1) echo 'Active';else echo 'InActive';  ?>
	</td>
	<td width="15%">
		<a href="<?php echo  site_url('webmaster/template_color/color_edit/'.$color['id']);?>" >edit</a> /	
		<a onclick="return confirm('Are you sure to delete template color')" href="<?php echo  site_url('webmaster/template_color/color_delete/'.$color['id']);?>" >delete</a>	
	</td>
</tr>
<?php } } else { ?>
<tr><td colspan="8" align="center">No Template Color Available</td></tr>
<?php } ?>
</table> 


