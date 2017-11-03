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
<div style="text-align: right;"><a href="<?php  echo base_url() ?>webmaster/coupons/coupon_create"><strong>Create New Coupon</strong></a></div>
<div class="tblheading">Manage Coupons <?php //Display paging links
echo $paging_links ?>

</div>
<table class="tbl_listing" width="100%"> 
	<thead> 

<tr>
<th>ID</th>
<th>Coupon code</th>
<th>Coupon Value</th>
<th>No. of members</th>
<th>No. of times/months</th>
<th>Last application date</th>
 
</tr>
<?php 
//List all packages
if(count($coupons)) {
foreach($coupons as $coupon){
?>
<tr>
	<td><?php echo $coupon['coupon_id'];  ?></td>
	<td><?php echo $coupon['coupon_code'];  ?></td>
	<td><?php 
	if($coupon['coupon_value'] == 0)
		echo '$'.$coupon['coupon_value'];
	else
		echo $coupon['coupon_value'] . '%';
	?></td>
	<td><?php echo $coupon['max_number_of_members']; if($coupon['max_number_of_members'] > 900)echo' [Assumed unlimited]'; ?></td>
	<td><?php echo $coupon['usable_number_of_times']; if($coupon['usable_number_of_times'] > 900)echo' [Assumed lifetime]';  ?></td>
	<td><?php echo $coupon['valid_untill'];  ?></td>	 
</tr>
<?php } } else { ?>
<tr><td colspan="6" align="center">No Coupon Available</td></tr>
<?php } ?>
</table>

<?php echo form_close(); ?>
