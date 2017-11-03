  <!--[body]-->
  <script>
	function checkField(fldobj){
		fldobj.checked=true;
	}
  </script>
  <div id="body-dashborad">
    <div class="container">
      <div class="dashboard-home">
        <h1>Create Subscription</h1>
		<?php 
		if(validation_errors()){
			echo '<div style="color:#FF0000;" class="info">'.validation_errors().'</div>';
		}
		?>
		 <?php
				// display all messages

				if (is_array($messages)):
					echo '<div class="info">';
					foreach ($messages as $type => $msgs):
						foreach ($msgs as $message):
							echo ('<span class="' .  $type .'">' . $message . '</span>');
						endforeach;
					endforeach;
					echo '</div>';
				endif;

				?>
       	<?php 
			echo form_open('newsletter/contacts/create', array('id' => 'form_subscription_create','name'=>'form_subscription_create','class'=>"form-website"));
		?>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="32%" class="label" style="width:220px;">Subscription Title</td>
              <td width="68%">
			  <?php echo form_input(array('name'=>'subscription_title','id'=>'subscription_title','maxlength'=>250,'size'=>40,'value'=>set_value('subscription_title') )); ?>
			 </td>
            </tr>
              <tr>
              <td class="label">Subscription Status</td>
              <td>
			<?php echo form_dropdown('subscription_status',array('1'=>'Active','0'=>'Inactive'),$_REQUEST['subscription_status'],"class='select' style='width:250px;'"); ?>
				</td>
            </tr>
			<tr>
				<td colspan="2">
			  <fieldset style="display:block;" >
				<legend>Subscriber Form Fields:</legend>
				<table style="width:500px;"  width="100%" border="0" cellspacing="0" cellpadding="0">
				 <tr>
              <td style="width:120px;" height="25" class="label">Email Address </td>
              <td style="width:120px;" ><input type="checkbox" name="subscription_is_email" value="1" id="subscription_is_email" maxlength="50" size="40" checked DISABLED /></td>
			  <td style="width:120px;"  ><?php echo form_button(array('name'=>'campaign_check','class'=>'buttons', 'value'=>'checked','content'=>'checked','onclick'=>"window.location.href='".base_url().'newsletter/contacts'."'","disabled"=>"disabled")); ?></td>
            </tr>
            <tr>
              <td height="25" class="label" style="width:120px;" >Name</td>
              <td style="width:120px;" ><input type="checkbox" name="subscription_is_name" value="1" id="subscription_is_name" maxlength="50" size="40" /></td>
			  <td style="width:120px;" ><?php echo form_button(array('name'=>'campaign_check','class'=>'buttons', 'value'=>'checked','content'=>'checked','onclick'=>"checkField(document.form_subscription_create.subscription_is_name);")); ?></td>
            </tr>
			<tr>
              <td style="width:120px;"  height="25" class="label">Phone</td>
              <td style="width:120px;" ><input type="checkbox" name="subscription_is_phone" value="1" id="subscription_is_phone" maxlength="50" size="40" /></td>
			  <td style="width:120px;" ><?php echo form_button(array('name'=>'campaign_check','class'=>'buttons', 'value'=>'checked','content'=>'checked','onclick'=>"checkField(document.form_subscription_create.subscription_is_phone);")); ?></td>
            </tr>
			<tr>
              <td style="width:120px;"  height="25" class="label">Address</td>
              <td style="width:120px;" ><input type="checkbox" name="subscription_is_address" value="1" id="subscription_is_address" maxlength="50" size="40" /></td>
			  <td style="width:120px;" ><?php echo form_button(array('name'=>'campaign_check','class'=>'buttons', 'value'=>'checked','content'=>'checked','onclick'=>"checkField(document.form_subscription_create.subscription_is_address);")); ?></td>
            </tr>
			<tr>
              <td style="width:120px;"  height="25" class="label">Date Of Birth</td>
              <td style="width:120px;" ><input type="checkbox" name="subscription_is_dob" value="1" id="subscription_is_dob" maxlength="50" size="40" /></td>
			  <td style="width:120px;" ><?php echo form_button(array('name'=>'campaign_check','class'=>'buttons', 'value'=>'checked','content'=>'checked','onclick'=>"checkField(document.form_subscription_create.subscription_is_dob);")); ?></td>
            </tr>
			</table>
			  </fieldset>
				</td>
			</tr>
           
          
            <tr>
              <td class="label">&nbsp;</td>
             <td>
			<?php
				echo form_submit(array('name' => 'subscription_submit', 'id' => 'btnEdit','class'=>'button-input','content' => 'Submit'), 'Submit');
				echo '&nbsp;';
				echo form_button(array('name'=>'campaign_cancel','class'=>'buttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'newsletter/contacts'."'"));
			?>	
			</td>
            </tr> <tr>
              <td class="label">&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td colspan="2"  >
              <div class="seprator-hor">&nbsp;</div>              </td>
            </tr>
          </table>
        <?php
			echo form_hidden('action','submit');
			echo form_close();
		?>
      </div>
    </div>
  </div>
  <!--[/body]-->