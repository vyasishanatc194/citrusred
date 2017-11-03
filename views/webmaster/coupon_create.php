<SCRIPT type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-ui-1.8.13.custom.min.js"></SCRIPT>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css-front/blitzer/jquery-ui-1.8.14.custom.css" />

<div class="tblheading">Create New Coupon</div>
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
<div class="form">
<?php 

  
echo form_open('webmaster/coupons/coupon_create/', array('id' => 'frmCouponCreate'));
?>
<table class="tbl_forms">
	<tr>
		<td colspan="2">
		<?php echo '<div style="color:#FF0000;">'.validation_errors().'</div>';  ?>
		</td>
	</tr>
	<tr><td><b>Coupon Code:</b></td><td><?php echo form_input(array('name'=>'coupon_code','id'=>'coupon_code','maxlength'=>150 ,'size'=>50,'value'=>set_value('coupon_code'))); ?></td></tr>
	<tr><td><b>Coupon Value:</b></td><td><?php echo form_input(array('name'=>'coupon_value','id'=>'coupon_value','maxlength'=>50 ,'size'=>30,'value'=>0)); ?>
	<?php
		echo form_radio('coupon_type', '1',true, 'id=coupon_type, style="width:40px;"') . '% (percentage)';		
		echo form_radio('coupon_type', '0',false, 'id=coupon_type, style="width:40px;"') . '$ (absolute)';
	?>
	</td></tr>
	<!--tr><td><b>Coupon Type:</b></td><td><?php //echo form_dropdown('coupon_type',array('1'=>'Percentage','0'=>'Absoulte'),$_REQUEST['coupon_type']); ?></td></tr-->
	<tr><td><b>No. of members allowed:</b></td><td><?php echo form_input(array('name'=>'max_number_of_members','id'=>'max_number_of_members','maxlength'=>50 ,'size'=>30,'value'=>1)); ?><span style="font-size:9px;">999 for unlimited</td></tr>
	<tr><td><b>No. of months:</b></td><td><?php echo form_input(array('name'=>'usable_number_of_times','id'=>'usable_number_of_times','maxlength'=>50 ,'size'=>30,'value'=>1)); ?><span style="font-size:9px;">999 for unlimited</td></tr>
	<tr><td><b>Expiration date:</b></td><td><?php echo form_input(array('name'=>'valid_untill','id'=>'valid_untill','maxlength'=>10 ,'size'=>30,'value'=>date('Y-m-d'))); ?>
	<SCRIPT type="text/javascript"> $(function(){$("#valid_untill").datepicker({ dateFormat: "yy-mm-dd" });});	</SCRIPT>
	</td></tr>
	
 
	
	<tr>		
		<td colspan="2">
			<?php
			echo form_submit(array('name' => 'btnSubmit', 'id' => 'btnSubmit','class'=>'inputbuttons','content' => 'Submit'), 'Submit');
			echo '&nbsp;';
			echo form_button(array('name'=>'btnCancel','class'=>'inputbuttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'webmaster/coupons/'."'"));
			?>
		</td>		
	</tr> 	 
</table>
<?php
echo form_hidden('action','submit');

echo form_close();
?>
</div>