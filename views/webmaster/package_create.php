<div class="tblheading">Create New Package</div>
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

  
echo form_open('webmaster/packages_manage/package_create/', array('id' => 'frmPackageCreate'));
?>
<table class="tbl_forms">
	<tr>
		<td>
		<?php echo '<div style="color:#FF0000;">'.validation_errors().'</div>';  ?>
		</td>
	</tr>
	<tr>	
		<td>
			Package Title
		<br/>
			<?php echo form_input(array('name'=>'package_title','id'=>'package_title','maxlength'=>150 ,'size'=>50,'value'=>set_value('package_title'))); ?>
		</td>
		
		<td>
			Package Price
		<br/>
			<?php echo form_input(array('name'=>'package_price','id'=>'package_price','maxlength'=>50 ,'size'=>30,'value'=>set_value('package_price'))); ?>
		</td>
		
		<td rowspan="2">
			Package Summary
		<br/>
			<?php echo form_textarea(array('name'=>'package_summary','id'=>'package_summary','rows'=>3,'cols'=>38,'value'=>set_value('package_summary'))); ?>
		</td>
	</tr> 
	  
	<tr>
		<td>
			Min Contacts
		<br/>
			<?php echo form_input(array('name'=>'package_min_contacts','id'=>'package_min_contacts','maxlength'=>50 ,'size'=>30,'value'=>set_value('package_min_contacts'))); ?>
		</td>
	
		<td>
			Max Contacts
		<br/>
			<?php echo form_input(array('name'=>'package_max_contacts','id'=>'package_max_contacts','maxlength'=>50 ,'size'=>30,'value'=>set_value('package_max_contacts'))); ?>
		</td>
		
	
		
	</tr>
	<tr>		
		<td>
			Recurring Interval
		<br/>
			<?php echo form_dropdown('package_recurring_interval',array(''=>'select one','months'=>'Months','years'=>'Years'),$_REQUEST['package_recurring_interval']); ?>
		</td>
		<td>
			Package Status
		<br/>
			<?php echo form_dropdown('package_status',array(''=>'select one',1=>'Active',0=>'InActive'),$_REQUEST['package_status']); ?>
		</td>
		<td valign="top">
			Package Price Summary
		<br/>
			<?php echo form_textarea(array('name'=>'package_price_summary','id'=>'package_price_summary','rows'=>3,'cols'=>38,'value'=>set_value('package_price_summary'))); ?>
		</td>
	</tr> 
	<tr>	
		<td>
			New/Old
		<br/>
			<?php echo form_dropdown('is_new',array(''=>'select one',1=>'New',0=>'Old'),$_REQUEST['is_new']); ?>
		</td><td>
			
		<input type="checkbox" name="is_quote" id="is_quote" class="is_quote" value="1" <?php echo $check; ?> /> Is Quote
		</td>
	</tr>
	
	<tr>
		
		<td >
			<?php
			echo form_hidden('package_type','newsletter');
			echo form_hidden('quota_multiplier','1000');
			echo form_hidden('action','submit');
			echo form_submit(array('name' => 'btnSubmit', 'id' => 'btnSubmit','class'=>'inputbuttons','content' => 'Submit'), 'Submit');
			echo '&nbsp;';
			echo form_button(array('name'=>'btnCancel','class'=>'inputbuttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'webmaster/packages_manage/packages_list'."'"));
			?>
		</td>
		
	</tr> 
	
	 
</table>
<?php
 
echo form_close();
?>
</div>