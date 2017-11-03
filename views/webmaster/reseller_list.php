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
<div style="text-align: right;"><a href="<?php  echo base_url() ?>webmaster/reseller/report"><strong>Report</strong></a> | <a href="<?php  echo base_url() ?>webmaster/reseller/reseller_create"><strong>Create New Reseller</strong></a></div>
<div class="tblheading">Manage Reseller <?php //Display paging links
echo $paging_links ?>

</div>
<table class="tbl_listing" width="100%"> 
	<thead> 

<tr>
<th>ID</th>
<th>Reseller</th>
<th>URL</th>
<th>Comission</th>
<th>No. of months</th>
</tr>
<?php 
//List all packages
if(count($resellers)) {
foreach($resellers as $reseller){
?>
<tr>
	<td><?php echo $reseller['id'];  ?></td>
	<td><?php echo $reseller['referrer_name'] .' ['.$reseller['referrer_string'] . ']';  ?></td>
	<td><?php echo base_url() .'st/'.$reseller['referrer_string'] ;  ?></td>
	<td><?php 
	if($reseller['commission_type'] == 0)
		echo '$'.$reseller['commission'];
	else
		echo $reseller['commission'] . '%';
	?></td>
	<td><?php echo $reseller['commission_months']; if($reseller['commission_months'] > 900)echo' [Assumed lifetime]';  ?></td>
</tr>
<?php } } else { ?>
<tr><td colspan="6" align="center">No Reseller Available</td></tr>
<?php } ?>
</table>

<?php echo form_close(); ?>
