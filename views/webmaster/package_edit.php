<div class="tblheading">Edit Package</div>
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

  
echo form_open('webmaster/packages_manage/package_edit/'.$package_id, array('id' => 'frmPackageCreate'));
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
			<?php echo form_input(array('name'=>'package_title','id'=>'package_title','maxlength'=>150 ,'size'=>50,'value'=>$package_data['package_title'])); ?>
		</td>
		<td>
			Package Price
		<br/>
			<?php echo form_input(array('name'=>'package_price','id'=>'package_price','maxlength'=>50 ,'size'=>30,'value'=>$package_data['package_price'])); ?>
		</td>
	
		<td>
			Package Summary
		<br/>
			<?php echo form_textarea(array('name'=>'package_summary','id'=>'package_summary','rows'=>2,'cols'=>38,'value'=>$package_data['package_summary'])); ?>
		</td>
	</tr> 
	 
	<tr>
		<td>
			Mix Contacts
		<br/>
			<?php echo form_input(array('name'=>'package_min_contacts','id'=>'package_min_contacts','maxlength'=>50 ,'size'=>30,'value'=>$package_data['package_min_contacts'])); ?>
		</td>
	
		<td>
			Max Contacts
		<br/>
			<?php echo form_input(array('name'=>'package_max_contacts','id'=>'package_max_contacts','maxlength'=>50 ,'size'=>30,'value'=>$package_data['package_max_contacts'])); ?>
		</td>
		
		<td rowspan="2">
			Package Price Summary
		<br/>
			<?php echo form_textarea(array('name'=>'package_price_summary','id'=>'package_price_summary','rows'=>5,'cols'=>38,'value'=>$package_data['package_price_summary'])); ?>
		</td>
	</tr>
	<tr>
		<td>
			Recurring Interval
		<br/>
			<?php echo form_dropdown('package_recurring_interval',array(''=>'select one','months'=>'Months','years'=>'Years'),$package_data['package_recurring_interval']); ?>
		</td>
	 
		
		<td colspan="1">
			Package Status
		<br/>
			<?php echo form_dropdown('package_status',array(''=>'select one',1=>'Active',0=>'InActive'),$package_data['package_status']); ?>
		</td>
	</tr> 
	<tr>	
		<td>
			New/Old
		<br/>
			<?php echo form_dropdown('is_new',array(''=>'select one',1=>'New',0=>'Old'),$package_data['is_new']); ?>
		</td>
		<td>
			<?php $check=($package_data['is_quote']==1)?"checked='checked'": "";?>
		<input type="checkbox" name="is_quote" id="is_quote" class="is_quote" value="" <?php echo $check; ?> /> Is Quote
		</td>
	</tr>
	
	<tr>
		
		<td >
			<?php
			echo form_hidden('package_type','newsletter');
			echo form_hidden('quota_multiplier',$package_data['quota_multiplier']);
			echo form_hidden('action','submit');
			echo form_submit(array('name' => 'btnSubmit', 'id' => 'btnSubmit','class'=>'inputbuttons','content' => 'Submit'), 'Submit');
			echo '&nbsp;';
			echo form_button(array('name'=>'btnCancel','class'=>'inputbuttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'webmaster/packages_manage/packages_list'."'"));
			?>
		</td>
		
	</tr> 
	
	 
</table>
<?php

echo form_hidden('package_id',$package_id);
echo form_close();
?>
</div>