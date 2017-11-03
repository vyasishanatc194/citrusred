<script type="text/javascript" src="<?php echo $this->config->item('webappassets'); ?>js/jquery-ui-1.8.13.custom.min.js?v=6-20-13"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets'); ?>css/blitzer/jquery-ui-1.8.14.custom.css?v=6-20-13" />
<style>
	.tbl-small a:hover { color: #194375; }
</style>
<script type="text/javascript">
function addCustomField(){
	if($('.custome_fld').val()!=""){
		var fld_name=$(".custome_fld").val();
		fld_name=fld_name.replace(/\s/g,'_');
		fld_name=fld_name.replace(/-|\//g, "_");
		//fld_name=fld_name.toLowerCase();
		if($('[id='+fld_name+']').length>0){
			$('.contact_frm').show();
			$('.custom_field_frm').hide();
			$('[id='+fld_name+']').focus();
		}else{
			var fld='<tr><th class="contacts_change">'+$('.custome_fld').val()+'</th></tr><tr><td><input type="text" name="'+fld_name+'" id="'+fld_name+'"  size="40" maxlength="250" class="custom_text" onclick="javascript:$(\'.custom_list\').hide();" autocomplete="off"/><a href="javascript:void(0);" class="delete_custom_field">Delete</a></td></tr>';
			$('.terms_condition').before(fld);
			$('.contact_frm').show();
			$('.custom_field_frm').hide();
		}
	}
}
function addExtraField(fld_name){
	fld_text=fld_name.toLowerCase();
	fld_name=fld_text.replace(/\s/g,'_');
	if($('#'+fld_name).length>0){
		$('.contact_frm').show();
		$('.custom_field_frm').hide();
		$('#'+fld_name).focus();
	}else{
		var fld='<tr><th class="contacts_change">'+ucfirst(fld_text)+'</th></tr><tr><td><input type="text" name="'+fld_name+'" id="'+fld_name+'"  size="40" maxlength="250" class="custom_text" onclick="javascript:$(\'.custom_list\').hide();"/><a href="javascript:void(0);" class="delete_custom_field">Delete</a></td></tr>';
		$('.terms_condition').before(fld);
		if(fld_name=="birthday"){
			$("#"+fld_name).datepicker({changeMonth: true,
				changeYear: true, yearRange: '1950:2012' });
		}
	}
	$('.custom_list').slideUp();
}
function ucfirst(str) {
    var firstLetter = str.substr(0, 1);
    return firstLetter.toUpperCase() + str.substr(1);
}

$('.delete_custom_field').live('click',function (){
	var parent_ob=$(this).parent().parent();
	parent_ob.prev().remove();
	parent_ob.remove();
});
$('.custom_text').live('keyup',function (){
	var parent_ob=$(this).parent();
	parent_ob.find('a').remove();
	var val=$(this).val().replace(/^\s+|\s+$/g,"");
	if(val=="")
	parent_ob.append('<a href="javascript:void(0);" class="delete_custom_field">Delete</a>');
});
function submit_frm(){
var block_data="";
block_data+=$('#contact_frm_submit').serialize();
	jQuery.ajax({
		url: "<?php echo base_url() ?>newsletter/subscriber/subscriber_create/<?php echo $subscription_id ?>",
		type:"POST",
		data:block_data,
		success: function(data) {
			var data_arr=data.split(":", 2);
			if(data_arr[0]=="error"){
				$('.subscriber_msg').html('');
				$('.subscriber_msg').html(data_arr[1]);
				$('.subscriber_msg').addClass('info');
				$('.subscriber_msg').fadeIn();
			}else if(data_arr[0]=="success"){
				var elements=$('.custom_text');
				elements.each(function() {
					var val=$(this).val().trim();
					if(val==""){
						var parent_ob=$(this).parent().parent();
						parent_ob.prev().remove();
						parent_ob.remove();
					}
				});
				$('.subscriber_msg').html('Contact added successfully.');
				$('#contact_frm_submit')[0].reset();
				$('.subscriber_msg').addClass('info');
				$('.subscriber_msg').fadeIn();
			}
		}
	});
}
function cancelSubscriber(){
	window.location="<?php echo $previous_page_url; ?>";
}
</script>
<style>
	#ui-datepicker-div{display:none;}
</style>
<!--[body]-->

<div class="register-form">
    <div class="registration-page">
        <h2>Add a Contact</h2>
			<form  method="post" name="contact_frm_submit" id="contact_frm_submit" class="contact_frm_create"  onsubmit="submit_frm(); return false;">
			<input type="hidden" name="subscription_id" id="subscription_id" value="<?php echo $subscription_id ?>" />
			<div class="subscriber_msg" >&nbsp;</div>
          <table  width="100%" border="0" cellspacing="0" cellpadding="0"  class="contact_tbl">
		<?php
			echo '<tr> <th class="contacts_change">First Name</th> </tr>
					<tr><td>'. form_input(array('name'=>'subscriber_first_name','id'=>'first_name','maxlength'=>250,'size'=>40,'value'=>'' )).'</td>
				</tr>';
			echo '<tr> <th class="contacts_change">Last Name</th></tr>
					<tr><td>'.  form_input(array('name'=>'subscriber_last_name','id'=>'last_name','maxlength'=>250,'size'=>40,'value'=>'')).'</td>
				</tr>';
			echo '<tr> <th class="contacts_change">Email Address</th> </tr>
					<tr><td>'. form_input(array('name'=>'subscriber_email_address','id'=>'email_address','maxlength'=>250,'size'=>40,'value'=>'' )).'</td>
				</tr>';
		?>
        <tr class="terms_condition"><td><input type="checkbox" name="terms_condition" id="terms_condition" value="1" style="width:10px;" />
           I agree to all RedCappi <a target="_blank" href="<?php echo  site_url("terms");?>">Terms & Conditions</a>. I agree not to access or otherwise use third party<br/> mailing lists or otherwise prepare or send unsolicited email.</td></tr>
          </table>

      <div class="button_div">
	  <div class="div_add_newrow" id="button">
		<a class="white-drop-button fl contact_frm" onclick="javascript:$('.custom_list').slideToggle();$('.button_div').append('<div class=\'dummy_content\'>&nbsp;</div>');setTimeout( function(){$('.dummy_content').remove()} , 2000);" href="javascript:void(0);"><span >Add New Field <img class="dropsub_img" src="<?php echo $this->config->item('webappassets');?>images/tab_arrow.png?v=6-20-13" alt="" align="absmiddle" >  </span></a>
			<ul class="custom_list" >
                <li><a href="javascript:void(0);" onclick="addExtraField('address')">Address</a></li>
                <li><a href="javascript:void(0);" onclick="addExtraField('birthday')">Birthday</a></li>
                <li><a href="javascript:void(0);" onclick="addExtraField('city')">City</a></li>
                <li><a href="javascript:void(0);" onclick="addExtraField('company')">Company</a></li>
                <li><a href="javascript:void(0);" onclick="addExtraField('country')">Country</a></li>
                <li><a href="javascript:void(0);" onclick="addExtraField('phone')">Phone</a></li>
                <li><a href="javascript:void(0);" onclick="addExtraField('state')">State</a></li>
                <li><a href="javascript:void(0);" onclick="addExtraField('zip code')">Zip Code</a></li>                <li><a href="javascript:void(0);" onclick="javascript:$('.contact_frm').hide();$('.custom_field_frm').show();$('.custome_fld').val('');$('.custom_list').slideUp();">Custom</a></li>
            </ul>
			<table width="50%" border="0" cellspacing="0" cellpadding="0"  class="tbl-small custom_field_frm" style="display:none;">
				<tr><th class="contacts_change">Type a name for your custom field</th></tr>
				<tr><td style="border:none;"><input type="text"  class="custome_fld" maxlength="100" /></td></tr>
				<tr>
					<td colspan="2" style="border:none;">
						<?php
							echo form_button(array('name' => 'subscription_submit', 'id' => 'btnEdit','class'=>'buttons add_more','content' => 'Add','onclick' => 'addCustomField();'), 'Save');
							echo '&nbsp;';
							echo form_button(array('name'=>'campaign_cancel','class'=>'buttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"javascript:$('.contact_frm').show();$('.custom_field_frm').hide();"));
						?>
					</td>
				</tr>
			</table>
		</div>
	  </div>
	  <div class="gap" style="clear:both;"></div>
	   <input type="hidden" name="action" id="action" value="save" />
	    <?php echo form_submit(array('name' => 'subscription_submit', 'id' => 'btnEdit','class'=>'button-input add_more contact_frm','content' => 'Submit','style' => 'margin-left:5px;'), 'Save'); ?>
		<?php echo form_button(array('name' => 'subscription_cancel', 'id' => 'subscription_cancel','class'=>'button-input contact_frm','content' => 'Cancel','style' => 'margin-left:5px;','onclick'=>'cancelSubscriber()'), 'Cancel'); ?>
	   </form>
     </div>
 <div class="clear"></div>
  </div>

