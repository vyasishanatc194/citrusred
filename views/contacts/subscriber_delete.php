<!--[/main script] -->
<script type="text/javascript">
function addCustomField(){
	if($('#custome_fld').val()!=""){
		var fld_name=$("#custome_fld").val();
		fld_name=fld_name.replace(/\s/g,'_');
		fld_name=fld_name.replace(/-|\//g, "_");
		fld_name=fld_name.toLowerCase();
		if($('#'+fld_name).length>0){
			$('.contact_frm').show();
			$('.custom_field_frm').hide();
			$('#'+fld_name).focus();
		}else{
			var fld='<tr><th class="contacts_change">'+$('#custome_fld').val()+'</th></tr><tr><td><input type="text" size="40" maxlength="250" name="'+fld_name+'" id="'+fld_name+'" /></td></tr>';
			$('.contact_tbl').append(fld);
			$('.contact_frm').show();
			$('.custom_field_frm').hide();
		}
	}
}
function delete_contacts_form(){
	var block_data="";
	block_data=parent.$('#form1').serialize()+'&';
	block_data+=$('#contact_frm_submit').serialize()+'&submit_action=submit';
	$.blockUI({ message: '<h3 class="please-wait">Please wait...</h3>' });
	$.fancybox.close();
	jQuery.ajax({
		url: "<?php echo base_url() ?>newsletter/subscriber/subscriber_delete/<?php echo $subscription_id; ?>/<?php echo $subscriber_id; ?>",
		type:"POST",
		data:block_data,
		success: function(data) {
			var data_arr=data.split(":", 2);
			if(data_arr[0]=="error"){
				$('#uncheck_list').val('');
				$('#msg').show();
				$('#msg').html(data_arr[1]);
				setTimeout( function(){$('#msg').html('');} , 4000);
				$.fancybox.close();
			}else if(data_arr[0]=="free"){
				$('#uncheck_list').val('');
				$('#msg').show();
				$('#msg').html(data_arr[1]);
				setTimeout( function(){$('#msg').html('');} , 4000);
				$.fancybox.close();
			}else if(data_arr[0]=="success"){
				$('#uncheck_list').val('');
				$('#msg').show();
				$('#msg').html('Contacts deleted successfully.');
				var page_id=0;
				if(($('.check-boxalign').length>1)&&($('.pagination').find('.selected').html()>1)){
					page_id=25*($('.pagination').find('.selected').html()-1);
				}

				setTimeout( function(){$('#msg').html('');} , 4000);
				if($('#action_notmail').val()=="unsubscribe"){
					display_contacts(<?php echo $subscription_id; ?>,'','','',1,'',page_id);
				}else if($('#action_notmail').val()=="complaints"){
					display_contacts(<?php echo $subscription_id; ?>,'','','',2,'',page_id);
				}else if($('#action_notmail').val()=="bounce"){
					display_contacts(<?php echo $subscription_id; ?>,'','','',0,1,page_id);
				}else{
					display_contacts(<?php echo $subscription_id; ?>,'','','','','',page_id);
				}

				display_subscription(<?php echo $subscription_id; ?>);
			}
			$.unblockUI();
		}
	});
}
$(document).ready(function () {
if($('#action').val() == 'page')
$('.total_delete_count').html($("#visible_contacts_count").val());
else
$('.total_delete_count').html($("input[type=checkbox]:checked").length);
});
</script>
<!--[page html]-->
<div class="fancybox-page registration-page_contact_delete font_delete">
	<div class="contact_frm" style="height:auto;">
		<form  method="post" name="contact_frm_submit" id="contact_frm_submit" onsubmit="delete_contacts_form(); return false;">
			<input type="hidden" name="subscription_id" id="subscription_id" value="<?php echo $subscription_id; ?>" />
			<div class="subscriber_msg info"></div>
			<h5>Confirm</h5>
			<?php if($subscription_id>0){?>
				<p>
					Would you like to delete these <b class='total_delete_count'></b> contacts from:<br />
					<input type="radio" name="contact_list" id="all_contact_list" value="1" style="width:10px;" checked />This list only<br />
					<input type="radio" name="contact_list" id="one_contact_list" value="-1" style="width:10px;" />All lists
				</p>
				<div class="btn-group">
					<?php echo form_submit(array('name' => 'subscription_submit', 'id' => 'btnEdit','class'=>'btn danger add_more','content' => 'Submit','style' => 'margin-left:5px; width: 144px; padding: 4px 0'), 'Delete Contact'); ?>
					<?php echo form_button(array('name' => 'subscription_cancel', 'id' => 'subscription_cancel','class'=>'btn cancel','content' => 'Cancel','style' => 'margin-left:5px;','onclick'=>'javascript:$.fancybox.close();'), 'Cancel'); ?>
				</div>
      <?php }else{ ?>
				<p>
					You're about to delete <b class='total_delete_count'></b> contacts from your account.
					Are you sure you want to permanently delete from your account!
				</p>
				<input type="hidden" name="contact_list" id="all_contact_list" value="-1" />
				<div class="btn-group">
					<?php echo form_submit(array('name' => 'subscription_submit', 'id' => 'btnEdit','class'=>'btn danger add_more','content' => 'Submit','style' => 'margin-left:5px; width: 232px; padding: 4px 0'), 'Permanently Delete Contact'); ?>
					<?php echo form_button(array('name' => 'subscription_cancel', 'id' => 'subscription_cancel','class'=>'btn cancel','content' => 'Cancel','style' => 'margin-left:5px;','onclick'=>'javascript:$.fancybox.close();'), 'Cancel'); ?>
				</div>
	    <?php } ?>
	  </form>
  </div>
</div>
