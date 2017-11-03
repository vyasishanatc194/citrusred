  
  <div id="body-dashborad" class="nobackground">
    <div class="container">
      <div class="dashboard-home" >
          

        <div>
          <div class="body-portion">
		  <!-- left side -->
		
            <div class="left-side">			
              <!--[navigation]-->
              <div id="container-1">
                <div class="gray-box tabs-container1 ">
                <div class="gray-box_contact">
				 			
				
				<ul class="contact_upload_tabs" style="display:none;">
						<li><a href="javascript:void(0);" onclick="contact_add_dropdown('contact_frm')" id="contact_frm" class="contact_type" >One at a time</a></li>
						<li><a href="javascript:void(0);" onclick="contact_add_dropdown('import_contact')" id="import_contact" class="contact_type" >Upload From a File</a></li>
						<li class="noborder1"><a href="javascript:void(0);" onclick="contact_add_dropdown('paste_contact')" id="paste_contact" class="contact_type noborder1" >Copy & Paste Contacts</a></li>				
					</ul>
				
				
				
		  	<div class="contact_frm subscriber_menus" style="display:none;">			 	
		   <div class="subscriber_msg" style="display:block ;">&nbsp;</div>
		<form onsubmit="return(false);" method="post" class="form-website" id = "subscriberfrm"  name="subscriberfrm" enctype = "multipart/form-data">
		<table width="50%" border="0" cellspacing="2" cellpadding="0" id="tbl_subscriber_frm">		
			<tr>
				<td class="label1" width="30%"><b>First Name</b></td>
				<td class="label1" width="30%"><b>Last Name</b></td>
				<td class="label1" width="30%"><b>Email Address</b></td>
			</tr>
			<tr>
			<td  width="30%">
			<?php
					echo form_input(array('name'=>'subscriber_first_name[]','maxlength'=>250,'size'=>40,'value'=>set_value('subscriber_first_name') ));
			?>
			</td>
			<td width="30%">
			<?php
				echo form_input(array('name'=>'subscriber_last_name[]','maxlength'=>250,'size'=>40,'value'=>set_value('subscriber_last_name'))); 
			?>
			</td>
			<td  width="30%">
			 <?php 
				echo form_input(array('name'=>'subscriber_email_address[]','maxlength'=>250,'size'=>40,'value'=>set_value('subscriber_email_address') ));
				?>
			</td>
			</tr>			
           </table>
		   <table width="100%" border="0" cellspacing="2" cellpadding="0">
		   <tr>
				<td colspan="2" class="label">
					Select List:
				</td>
			</tr>
		  <tr>
              <td width="100%" align="right" colspan="3">
			   <select name="subscription_contact_one" id="subscription_contact_one" class="select-normal" >
			   <?php
				foreach($select_subscriptions as $subscription){
				 echo "<option value='".$subscription['subscription_id']."'>".ucfirst($subscription['subscription_title'])."</option>";
				}
				?>
				</select>
			<?php
				echo form_button(array('name' => 'add_row', 'id' => 'add_row','class'=>'button-input','content' => 'Add New Row','onclick'=>"add_table_row();"), 'Add New Row');
			?>
          </td>
		 </tr>
		 <tr>
			<td colspan="2">&nbsp;
			<input type="hidden" name="terms" id="terms" value="true" />
			</td>
		</tr>
	  <tr class="terms_condition"><td colspan="2"><input type="checkbox" name="terms_condition_save" id="terms_condition_save" value="1" style="width:10px;" />			
           I agree to all RedCappi <a target="_blank" href="<?php echo  base_url().'terms';?>">Terms & Conditions</a>. I agree not to access or otherwise use third party mailing lists or otherwise prepare or send unsolicited email.</td></tr>
		 <tr>
              <td width="100%">
			<?php
				echo form_submit(array('name' => 'save', 'id' => 'save','class'=>'button-input','content' => 'Submit','onclick'=>"ajaxSubscriberFrm('subscribercopyfrm','save')"), 'Save');
				echo '&nbsp;';
				echo form_submit(array('name' => 'save_add_more', 'id' => 'save_add_more','class'=>'button-input add_more','content' => 'Submit','onclick'=>"ajaxSubscriberFrm('subscribercopyfrm','save_add_more')"), 'Save & Add More');
				echo '&nbsp;';
				echo form_button(array('name'=>'campaign_cancel','class'=>'buttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"closeSubscriberForm('contact_frm');"));
			?>	
          </td>
		 </tr>
		 </table>
		  </form>
		  </div>
		  
		  
		  <div  class="import_contact subscriber_menus">
		   	
		  <div class="subscriber_msg" style="display:block ; ">&nbsp;</div>
		   <?php
			echo form_open_multipart('newsletter/subscriber/create/'.$subscriptions[0]['subscription_id'], array('id' => 'form_subscriber_import','name'=>'form_subscriber_import','class'=>"form-website"));
			?>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>       
		<td colspan="2">	  
         <?php echo form_upload(array('id'=>'subscriber_csv_file','name'=>'subscriber_csv_file','value'=>set_value('subscriber_csv_file') )); ?>
		 <span class="small">Upload file with extension csv or xls</span>
		 </td>
	</tr>
	<tr>
				<td colspan="2" class="label">Select List:</td>
			</tr>
            <tr>
              <td width="32%" class="label" >
			  <select name="subscription_select" id="subscription_select" class="select-normal" >
			   <?php				foreach($select_subscriptions as $subscription){
				 echo "<option value='".$subscription['subscription_id']."'>".ucfirst($subscription['subscription_title'])."</option>";
				}
				?>
				</select>
			  </td>
			  <td width="68%">
			 
			</td>
		</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	  <tr class="terms_condition"><td colspan="2"><input type="checkbox" name="terms_condition" id="terms_condition" value="1" style="width:10px;" />
           I agree to all RedCappi <a target="_blank" href="<?php echo base_url().'terms';?>">Terms & Conditions</a>. I agree not to access or otherwise use third party mailing lists or otherwise prepare or send unsolicited email.</td></tr>
	<tr>
		<td colspan="2">
		<?php
		echo form_button(array('name'=>'subscriber_submit','id'=>'subscriber_submit','class'=>'button-input','value'=>'Import','content'=>'Save','onclick'=>"return ajaxFileUpload();"));
		echo '&nbsp;';
		echo form_button(array('name'=>'campaign_cancel','class'=>'buttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"closeSubscriberForm('import_contact');"));
	?>
		</td>
	</tr>
	</table>
	</form>	
	</div>
	
	
	<div class="paste_contact subscriber_menus">
		
	<div class="subscriber_msg" style="display:block ; ">&nbsp;</div>
	<form onsubmit="ajaxSubscriberFrm(this,'copycsv'); return(false);" method="post" class="form-website" id = "subscribercopyfrm"  name="subscribercopyfrm">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	 
		<tr>
		  <td colspan="2">
			<textarea  name="copy_csv" id="copy_csv"></textarea>
			</td>
		</tr>
      <tr>
          <td width="32%" class="label" >Select List: </td>
		  <td>
		  </td>
	</tr>
	<tr>
		  <td colspan="2">
			 <select name="subscription_select_copy" id="subscription_select_copy" class="select-normal" >
			   <?php				foreach($select_subscriptions as $subscription){
				 echo "<option value='".$subscription['subscription_id']."'>".ucfirst($subscription['subscription_title'])."</option>";
				}
				?>
				</select>
			</td>
		</tr> 
		
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
	  <tr class="terms_condition"><td colspan="2"><input type="checkbox" name="terms_condition_copy" id="terms_condition_copy" value="1" style="width:10px;" />
           I agree to all RedCappi <a target="_blank" href="<?php echo base_url().'terms';?>">Terms & Conditions</a>. I agree not to access or otherwise use third party mailing lists or otherwise prepare or send unsolicited email.</td></tr>
	<tr>
		<tr>
			<td colspan="2">
		<?php
			echo form_submit(array('name' => 'subscription_submit', 'id' => 'btnEdit','class'=>'button-input','content' => 'Submit'), 'Save');
			echo '&nbsp;';
			echo form_button(array('name'=>'campaign_cancel','class'=>'buttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"closeSubscriberForm('paste_contact');"));
		?>
			</td>
		</tr>
	</table>
		</form>  
		</div>
	










	
		    
                 
                  
                </div>
				
        
              </div>
              <!--[/navigation]-->
            </div>
            </div>
			 
            <!--/second part -->
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--Define hidden variables -->
  
 
  