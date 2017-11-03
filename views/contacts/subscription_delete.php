<script type="text/javascript">
<?php if($autoresponder_subscription==1){?>
<?php }else{ ?>
function delete_list_frm(){
var block_data="";
block_data+='submit_action=submit';

	jQuery.ajax({
		url: "<?php echo base_url() ?>newsletter/contacts/delete/<?php echo $subscription_id; ?>",
		type:"POST",
		data:block_data,
		success: function(data) {
			parent.display_subscription(<?php echo $subscription_id ?>);
			parent.display_contacts(<?php echo '-'.$this->session->userdata('member_id'); ?>);
			$.fancybox.close();
		}
	});
}
<?php } ?>
</script>
<!--[page html]-->
<div class="fancybox-page registration-page_contact_delete">
  <div class="fancybox-form contact_frm">
    <?php if($autoresponder_subscription==1){?>
    <h5>Alert</h5>
    <p>Lists associated with autoresponders cannot be deleted. Please update the autoresponder associated with this list first before attempting to delete the list.</p>
    <?php } else { ?>
      <h5>Confirm</h5>
      <form method="post" name="contact_frm_submit" id="contact_frm_submit" onsubmit="delete_list_frm(); return false;">
        <input type="hidden" name="subscription_id" id="subscription_id" value="<?php echo $subscription_id; ?>" />
        <div class="subscriber_msg info"></div>
        <p>
          Are you sure you want to delete this list? Only the list will be deleted and the contacts  in the list will remain in your account.
        </p>
        <div class="btn-group">
          <input type="hidden" name="contact_list" id="all_contact_list" value="-1" style="width:10px;" checked />
          <?php echo form_submit(array('name' => 'subscription_submit', 'id' => 'btnEdit','class'=>'add_more btn danger','content' => 'Submit'), 'Delete'); ?>
          <?php echo form_button(array('name' => 'subscription_cancel', 'id' => 'subscription_cancel','class'=>'btn cancel fast_confirm_cancel','content' => 'Cancel','style' => 'margin-left:5px;','onclick'=>'javascript:$.fancybox.close();'), 'Cancel'); ?>
        </div>
      </form>
    <?php } ?>
  </div>
</div>
