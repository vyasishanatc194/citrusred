<SCRIPT type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-ui-1.8.13.custom.min.js"></SCRIPT>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css-front/blitzer/jquery-ui-1.8.14.custom.css" />

<div class="tblheading">Create New Reseller</div>
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

  
echo form_open('webmaster/reseller/reseller_create/', array('id' => 'frmResellerCreate'));
?>
<table class="tbl_forms">
	<tr>
		<td colspan="2">
		<?php echo '<div style="color:#FF0000;">'.validation_errors().'</div>';  ?>
		</td>
	</tr>
	<tr><td><b>Reseller Name:</b></td><td><?php echo form_input(array('name'=>'referrer_name','id'=>'referrer_name','maxlength'=>150 ,'size'=>50,'value'=>set_value('referrer_name'))); ?></td></tr>
	<tr><td><b>Reseller Detail:</b></td><td><?php echo form_input(array('name'=>'referrer_email','id'=>'referrer_email','maxlength'=>150 ,'size'=>50,'value'=>set_value('referrer_email'))); ?></td></tr>
	<tr><td><b>String/code:</b></td><td><?php echo form_input(array('name'=>'referrer_string','id'=>'referrer_string','maxlength'=>150 ,'size'=>50,'value'=>set_value('referrer_string'))); ?></td></tr>
	<tr><td><b>Commission:</b></td><td><?php echo form_input(array('name'=>'commission','id'=>'commission','maxlength'=>50 ,'size'=>30,'value'=>0)); ?>
	<?php
		echo form_radio('commission_type', '1',true, 'id=commission_type, style="width:40px;"') . '% (percentage)';		
		echo form_radio('commission_type', '0',false, 'id=commission_type, style="width:40px;"') . '$ (absolute)';
	?>
	</td></tr>
	
	
	<tr><td><b>Commission months:</b></td><td><?php echo form_input(array('name'=>'commission_months','id'=>'commission_months','maxlength'=>50 ,'size'=>30,'value'=>1)); ?><span style="font-size:9px;">999 for unlimited</td></tr>
		
	<tr>		
		<td colspan="2">
			<?php
			echo form_submit(array('name' => 'btnSubmit', 'id' => 'btnSubmit','class'=>'inputbuttons','content' => 'Submit'), 'Submit');
			echo '&nbsp;';
			echo form_button(array('name'=>'btnCancel','class'=>'inputbuttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'webmaster/reseller/'."'"));
			?>
		</td>		
	</tr> 	 
</table>
<?php
echo form_hidden('action','submit');

echo form_close();
?>
</div>