<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.mousewheel-3.0.4.pack.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery.blockUI.js?v=6-20-13"></script>
<script type="text/javascript">
    $(document).ready(function(){
    $(".fancybox").fancybox({'autoDimensions':false,'centerOnScroll':true,'scrolling':true,'width':'400','height':'auto'});

    <?php if('1'==$extra['contact_import_progress']){?>
      $('.subscriber_msg').show();
      setInterval('checkImportStatus()',10000);
    <?php }else{?>
      //$('.subscriber_msg').removeClass('info');
      // $('.subscriber_msg').hide();
      //$('.subscriber_msg').css('display','none');
      //$('.subscriber_msg').html('');
    <?php }?>
    });
  $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
</script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets'); ?>js/jquery.fastconfirm.js?v=6-20-13"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets'); ?>css/jquery.fastconfirm.css?v=6-20-13" media="screen" />
<script type="text/javascript" src="<?php echo $this->config->item('webappassets'); ?>js/ajaxfileupload.js?v=6-20-13"></script>
<script type="text/javascript">
var base_url="<?php echo base_url();?>";
var webappassets="<?php echo $this->config->item('webappassets');?>";
var memid="<?php echo $extra['member_id'];?>";
</script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets'); ?>js/contacts_add.js?v=6-20-13"></script>
<!--[body]-->
  <div id="body-dashborad">
    <div class="container">
      <ul class="contact_upload_tabs">
        <li><a href="javascript:void(0);" onclick="contact_add_dropdown('import_contact')" id="import_contact" class="contact_type active btn add">Upload From a File</a></li>
        <li><a href="javascript:void(0);" onclick="contact_add_dropdown('contact_frm')" id="contact_frm" class="contact_type btn add">One at a time</a></li>
        <li class="noborder1"><a href="javascript:void(0);" onclick="contact_add_dropdown('paste_contact')" id="paste_contact" class="contact_type noborder1 btn add">Copy & Paste Contacts</a></li>
      </ul>
      <div class="form-fields">
        <div class="subscriber_msg info"><?php if('1'==$extra['contact_import_progress']){?>Your list import is under process. Larger lists will take longer. However, navigating away from this page will not interrupt the upload. After completion of process, you will be informed by email.<?php }?></div>
        <div class="contact_frm subscriber_menus" style="display:none;">
          <div class="subscriber_msg_remove" style="display:block;"></div>
          <form onsubmit="return(false);" method="post" class="form-website" id="subscriberfrm"  name="subscriberfrm" enctype="multipart/form-data">
            <table width="50%" border="0" cellspacing="2" cellpadding="0" id="tbl_subscriber_frm">
              <tr>
                <td class="label1" width="30%"><strong>First Name</strong></td>
                <td class="label1" width="30%"><strong>Last Name</strong></td>
                <td class="label1" width="30%"><strong>Email Address</strong></td>
              </tr>
              <tr>
                <td width="30%">
                  <?php echo form_input(array('name'=>'subscriber_first_name[]','maxlength'=>250,'size'=>40,'value'=>set_value('subscriber_first_name') )); ?>
                </td>
                <td width="30%">
                  <?php echo form_input(array('name'=>'subscriber_last_name[]','maxlength'=>250,'size'=>40,'value'=>set_value('subscriber_last_name'))); ?>
                </td>
                <td width="30%">
                  <?php echo form_input(array('name'=>'subscriber_email_address[]','maxlength'=>250,'size'=>40,'value'=>set_value('subscriber_email_address') )); ?>
                </td>
              </tr>
			  <tr>
                <td width="30%">
                  <?php echo form_input(array('name'=>'subscriber_first_name[]','maxlength'=>250,'size'=>40,'value'=>set_value('subscriber_first_name') )); ?>
                </td>
                <td width="30%">
                  <?php echo form_input(array('name'=>'subscriber_last_name[]','maxlength'=>250,'size'=>40,'value'=>set_value('subscriber_last_name'))); ?>
                </td>
                <td width="30%">
                  <?php echo form_input(array('name'=>'subscriber_email_address[]','maxlength'=>250,'size'=>40,'value'=>set_value('subscriber_email_address') )); ?>
                </td>
              </tr>
              <tr>
                <td width="30%">
                  <?php echo form_input(array('name'=>'subscriber_first_name[]','maxlength'=>250,'size'=>40,'value'=>set_value('subscriber_first_name') )); ?>
                </td>
                <td width="30%">
                  <?php echo form_input(array('name'=>'subscriber_last_name[]','maxlength'=>250,'size'=>40,'value'=>set_value('subscriber_last_name'))); ?>
                </td>
                <td width="30%">
                  <?php echo form_input(array('name'=>'subscriber_email_address[]','maxlength'=>250,'size'=>40,'value'=>set_value('subscriber_email_address') )); ?>
                </td>
              </tr>
            </table>

            <?php echo form_button(array('name' => 'add_row', 'id' => 'add_row','class'=>'btn cancel','content' => 'Add New Row','onclick' => 'add_table_row();', 'style' => 'margin: 0 0 15px 14px'), 'Add New Row'); ?>

            <div class="add-contacts-actions">
              <strong>Select List:</strong><br />
              <select name="subscription_contact_one" id="subscription_contact_one">
                <?php
                foreach($select_subscriptions as $subscription){
                  if($subscription_first_id == $subscription['subscription_id'])
                    echo "<option value='".$subscription['subscription_id']."' selected>".ucfirst($subscription['subscription_title'])."</option>";
                  else
                    echo "<option value='".$subscription['subscription_id']."'>".ucfirst($subscription['subscription_title'])."</option>";
                }
                ?>
              </select>
              <input type="hidden" name="terms" id="terms" value="true" />

              <div class="terms_condition">
                <input type="checkbox" name="terms_condition_save" id="terms_condition_save" value="1" />
                I agree to all RedCappi <a target="_blank" href="<?php echo  base_url().'terms';?>">Terms & Conditions</a>. I agree not to access or otherwise use third party mailing lists or otherwise prepare or send unsolicited email.
              </div>

              <?php
                echo form_submit(array('name' => 'save', 'id' => 'save','class'=>'btn confirm','content' => 'Submit','onclick'=>"ajaxSubscriberFrm('subscribercopyfrm','save')"), 'Save');
                echo form_submit(array('name' => 'save_add_more', 'id' => 'save_add_more','class'=>'btn confirm add_more','content' => 'Submit','onclick'=>"ajaxSubscriberFrm('subscribercopyfrm','save_add_more')"), 'Save & Add More');
                echo form_button(array('name'=>'campaign_cancel','class'=>'btn cancel', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".  site_url("newsletter/contacts")."';"));
              ?>
            </div>
          </form>
        </div>
        <div class="import_contact subscriber_menus">
          <div class="subscriber_msg_remove" style="display:block;"></div>
          <?php echo form_open_multipart('newsletter/subscriber/create/'.$subscriptions[0]['subscription_id'], array('id' => 'form_subscriber_import','name'=>'form_subscriber_import','class'=>"form-website")); ?>
            <div class="file-upload">
              <strong>Upload file with extension CSV or XLS:</strong>
              <?php echo form_upload(array('id'=>'subscriber_csv_file','name'=>'subscriber_csv_file','value'=>set_value('subscriber_csv_file') )); ?>
            </div>
            <div class="add-contacts-actions">
              <strong>Select List:</strong><br />
              <select name="subscription_select" id="subscription_select">
              <?php
               foreach($select_subscriptions as $subscription){
                if($subscription_first_id == $subscription['subscription_id'])
                  echo "<option value='".$subscription['subscription_id']."' selected>".ucfirst($subscription['subscription_title'])."</option>";
                else
                  echo "<option value='".$subscription['subscription_id']."'>".ucfirst($subscription['subscription_title'])."</option>";
              }
              ?>
              </select>
              <div class="terms_condition">
                <input type="checkbox" name="terms_condition" id="terms_condition" value="1" />
                I agree to all RedCappi <a target="_blank" href="<?php echo base_url().'terms';?>">Terms & Conditions</a>. I agree not to access or otherwise use third party mailing lists or otherwise prepare or send unsolicited email.
              </div>
			  <div id="test1"></div>
              <?php
                echo form_button(array('name'=>'subscriber_submit','id'=>'subscriber_submit','class'=>'btn confirm','value'=>'Import','content'=>'Save','onclick'=>"return ajaxFileUpload();"));
                echo form_button(array('name'=>'campaign_cancel','class'=>'btn cancel', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".  site_url("newsletter/contacts/")."';"));
              ?>
            </div>
          </form>
        </div>
        <div class="paste_contact subscriber_menus"  style="display:none;">
          <div class="subscriber_msg_remove" style="display:block;"></div>
          <form onsubmit="ajaxSubscriberFrm(this,'copycsv'); return(false);" method="post" class="form-website" id="subscribercopyfrm"  name="subscribercopyfrm">
            <div class="add-contacts-actions">
              <strong>Copy and Paste your Contacts here:</strong>
              <textarea  name="copy_csv" id="copy_csv"></textarea>
              <strong>Select List:</strong><br />
              <select name="subscription_select_copy" id="subscription_select_copy">
              <?php
               foreach($select_subscriptions as $subscription){
                if($subscription_first_id == $subscription['subscription_id'])
                  echo "<option value='".$subscription['subscription_id']."' selected>".ucfirst($subscription['subscription_title'])."</option>";
                else
                  echo "<option value='".$subscription['subscription_id']."'>".ucfirst($subscription['subscription_title'])."</option>";
              }
              ?>
              </select>
              <div class="terms_condition">
                <input type="checkbox" name="terms_condition_copy" id="terms_condition_copy" value="1" />
                I agree to all RedCappi <a target="_blank" href="<?php echo base_url().'terms';?>">Terms & Conditions</a>. I agree not to access or otherwise use third party mailing lists or otherwise prepare or send unsolicited email.
              </div>
              <?php
                echo form_submit(array('name' => 'subscription_submit', 'id' => 'btnEdit','class'=>'btn confirm','content' => 'Submit'), 'Save');
                echo form_button(array('name'=>'campaign_cancel','class'=>'btn cancel', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".  site_url("newsletter/contacts")."';"));
              ?>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
