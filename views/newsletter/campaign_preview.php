<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.pack.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.mousewheel-3.0.4.pack.js?v=6-20-13"></script>
<link rel="stylesheet" href="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.css?v=6-20-13" type="text/css" media="screen" />
<script type="text/javascript" language="javascript">
var html_code="";
$(document).ready(function(){
if('245' != $('#country').val())
$('span#country_custom_div').hide();
else
$('span#country_custom_div').show();
	
$(".templates").click(function(){
	var thisSkin = $('img',this).attr("id");
		$('input[name=red_theme_name]').val($('.skins',this).attr('id'));
		$('#red_template_name').val(thisSkin);
		$('input[id=theme_'+$('.skins',this).attr('id')+']').attr('checked', true);
	});
	$("a.next_campaign_changes").click(function (e) {
		alert_msg=false;
		window.onbeforeunload = null;
		submit_frm();
	});
	$("a.save_campaign_changes").click(function (e) {
		alert_msg=false;
		window.onbeforeunload = null;
		document.getElementById('action_save').value='save';
		document.campaign_frm.submit();
	});
	$("a.re_generate_text").click(function (e) {
		alert_msg=false;
		window.onbeforeunload = null;
		document.getElementById('action_save').value='save';
		document.getElementById('regenerate_text').value='1';
		document.campaign_frm.submit();
	});
	/**
	Display blank on click of campaign_title input box
	*/
	jQuery("#campaign_title").live('click',function(){
		if(jQuery(this).val().toLowerCase()=="unnamed"){
			$("#current_container_id").val(jQuery(this).val());
			jQuery(this).val("");
		}
	}).live('blur',function(){
		if(jQuery(this).val()==""){
			jQuery(this).val("Unnamed");
		}
		$("#current_container_id").val("");
	});
});
/**
	Onclick of enter button
**/
jQuery('#campaign_title').live("keydown", function(e) {
	var code = e.keyCode || e.which;
	if(code == 13) {
		if(jQuery(this).val()==""){
			jQuery(this).val("Unnamed");
		}
		alert_msg=false;
		window.onbeforeunload = null;
		submit_frm();
	}
});
/**

	page leaving message

**/
var alert_msg=true;
window.onbeforeunload = function ()
{
	if(alert_msg){
		return "Your Campaign has unsaved changes. Any unsaved changes will be lost!\n" +
           "Would you still like to exit without saving??";
	}else {
		return null;
	}
}

//Fucntion to submit a form
 function frmSubmit(frmfld1,val){
	frmfld1.value=val;
	document.form_campaign_theme.submit();
 }
 //Function to change a template
 function changeTemplate(theme_id){
	$('.right-template').css('text-align','center');
	$('.campaign_preview').hide();
	$('.campaign_import_url').hide();
	$('.campaign_zip_file').hide();
	$('.paste_code').hide();
	$('.text_email').hide();
	$('.thumb').show();
	$('.round-box').hide();
	if(!theme_id){
		theme_id="";
	}
		 var block_data="";
		 var url="";
		 <?php if($campaign_data['is_autoresponder']){ ?>
			url="<?php echo base_url() ?>newsletter/autoresponder/get_template_data_for_theme/"+theme_id;
		<?php }else{ ?>
			url="<?php echo base_url() ?>newsletter/campaign/get_template_data_for_theme/"+theme_id;
		<?php } ?>
		  jQuery.ajax({
		  url: url,
		   type:"POST",
			data:block_data,
		  success: function(data) {
			$('.thumb').html(data);
			$('#ul_headers').find('li').find('a').removeClass('highlight');
			$('#ul_more').find('li').find('a').removeClass('highlight');
			$('.li_'+theme_id).find('a').addClass('highlight');
		  }
		});
 }

 // Function to save a template
 function saveTemplate(template_name,template_id,theme_id){
	document.form_campaign_theme.red_theme_name.value=theme_id;
	document.form_campaign_theme.red_template_name.value=template_id;
	document.form_campaign_theme.submit();
 }
 // Function to show  import  URL
 function importUrl(){
	$('.right-template').css('text-align','left');
	$('.thumb').hide();
	$('.campaign_preview').hide();
	$('.campaign_zip_file').hide();
	$('.paste_code').hide();
	$('.text_email').hide();
	$('.campaign_import_url').show();
	$('.round-box').hide();
	$('#ul_more').find('li').find('a').removeClass('highlight');
	$('#ul_headers').find('li').find('a').removeClass('highlight');
	$('.import_url').addClass('highlight');
 }
 // Function to show import from zip file
 function zipFile(){
	$('.right-template').css('text-align','left');
	$('.thumb').hide();
	$('.campaign_preview').hide();
	$('.campaign_import_url').hide();
	$('.paste_code').hide();
	$('.text_email').hide();
	$('.campaign_zip_file').show();
	$('.round-box').hide();
	$('#ul_more').find('li').find('a').removeClass('highlight');
	$('#ul_headers').find('li').find('a').removeClass('highlight');
	$('.import_zip').addClass('highlight');
 }
 // Function to show paste in code
 function paste_code(){
	$('.right-template').css('text-align','left');
	$('.thumb').hide();
	$('.campaign_preview').hide();
	$('.campaign_import_url').hide();
	$('.campaign_zip_file').hide();
	$('.text_email').hide();
	$('.paste_code').show();
	$('.round-box').hide();
	$('#ul_more').find('li').find('a').removeClass('highlight');
	$('#ul_headers').find('li').find('a').removeClass('highlight');
	$('.import_paste_code').addClass('highlight');
 }
 // Function to show text email
 function text_email(){
	$('.right-template').css('text-align','left');
	$('.thumb').hide();
	$('.campaign_preview').hide();
	$('.campaign_import_url').hide();
	$('.campaign_zip_file').hide();
	$('.paste_code').hide();
	$('.text_email').show();
	$('.round-box').hide();
	$('#ul_more').find('li').find('a').removeClass('highlight');
	$('#ul_headers').find('li').find('a').removeClass('highlight');
	$('.import_text_email').addClass('highlight');
 }

 
function submit_frm(){
	<?php if($campaign_data['upgrade_package']==1) {?>
	alert("Unable to send because your are over your contact plan limit. Please Upgrade Now");
	<?php } ?>
	document.getElementById('action_save').value='next';
	document.campaign_frm.submit();
}
function submitPasteHtmlFrm(){
	alert_msg=false;
	window.onbeforeunload = null;
	document.form_campaign_paste_code.action.value='paste_code';
	document.form_campaign_paste_code.submit();
}
function updateFooterAddress(){
setTimeout("$.fancybox($('#user_account_option').html(),{ 'autoDimensions':false,'height':'510','width':'630','centerOnScroll':true,'modal':false});", 1000);
}
function save_user_info(){
  var block_data;
  block_data='company='+encodeURIComponent($('#fancybox-wrap').find('#company_name').val())+'&address_line_1='+encodeURIComponent($('#fancybox-wrap').find('#address').val())+'&city='+encodeURIComponent($('#fancybox-wrap').find('#city').val())+'&state='+encodeURIComponent($('#fancybox-wrap').find('#state').val())+'&zipcode='+encodeURIComponent($('#fancybox-wrap').find('#zip').val())+'&country='+encodeURIComponent($('#fancybox-wrap').find('#country').val())+'&country_custom='+encodeURIComponent($('#fancybox-wrap').find('#country_custom').val());
  jQuery.ajax({
    url: "<?php echo base_url() ?>account/user_info",
    type:"POST",
    data:block_data,
    success: function(data) {
      var data_arr=data.split(':');
      if(data_arr[0]=="error"){
        $('#fancybox-wrap').find('.msg').html(data_arr[1]);
      }else{
         
        $.fancybox.close();
    
      }
    }
  });
}
function showCustom(dpdCountry){
	if('245' == dpdCountry.value){
	$('span#country_custom_div').show();
	}else{
	$('span#country_custom_div').hide();
	}
}
 

 // call functions for change Templates
 changeTemplate();
</script>
  <!--[body]-->
  <div id="body-dashborad">
    <div class="container">
    	<h1>
    		<a href="javascript:void(0)" onclick="return false;" class="btn add inline-block next_campaign_changes">Next Step</a>
		  	<?php if($campaign_data['is_autoresponder']){ ?>
					<a href="javascript:void(0);" class="btn add save_campaign_changes">Save</a>
					<a href="<?php echo CAMPAIGN_DOMAIN.'a/'.$campaign_data['campaign_id']; ?>" target="_blank" class="btn cancel campaign_privew_link">Preview</a>
				<?php }else{ ?>
					<a href="javascript:void(0);" class="btn add save_campaign_changes">Save</a>
					<a href="<?php echo CAMPAIGN_DOMAIN.'c/'.$campaign_data['campaign_id'];?>" target="_blank" class="btn cancel campaign_privew_link">Preview</a>
				<?php } ?>
				<?php if($campaign_data['campaign_template_option']!=5){?><a href="javascript:void(0);" class="re_generate_text btn cancel">Re-Generate Text from HTML</a><?php } ?>
				<?php if($campaign_data['campaign_template_option']==1) { ?>
					Import from URL
				<?php }else if($campaign_data['campaign_template_option']==2){ ?>
					Import from Zip File
				<?php }else if($campaign_data['campaign_template_option']==4){ ?>
					Paste in HTML Code
				<?php }else if($campaign_data['campaign_template_option']==5){ ?>
					Plain-Text Email
				<?php } ?>
			</h1>
		<!--div class="begin_campaign">To Begin, you can use a RedCappi Banner or Do Your Own Thing</div-->
		<?php
		if(validation_errors()){
			echo '<div class="info">'.validation_errors().'</div>';
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
		<div class="campaign_preview campaign-creation-options form-fields">
			<form name="campaign_frm" id="campaign_frm" action="" method="post" class="form-website">
				<strong>Campaign Name</strong><br />
				<input type="text" value="<?php echo $campaign_data['campaign_title']; ?>" id="campaign_title" name="campaign_title"><br />
			  <?php $html=  html_entity_decode($campaign_data['campaign_text_content'], ENT_QUOTES, "utf-8" );?>
				<input type="hidden" name="action_save" id="action_save" value="next" />
			
				<div id="link_send_test" class="clear-both" style="margin:-53px 20px 0 0;padding-bottom:10px;float:right;">
				<a class="btn cancel subscr_list" title="Send Test" style="padding: 8px 75px;margin-right:0;" href="javascript:void(0);" onclick="javascript:updateFooterAddress();"><b>Edit Footer Address</b></a>
				</div>
			 
				<strong class="helper-text" title="This plain-text email message is displayed if recipients have images disabled and won't display your HTML email. Your emails may also get caught in spam filters without a plain-text message.">Plain-Text Message</strong>
				<textarea name="campaign_text_content" id="campaign_text_content"><?php echo $html; ?></textarea>
				<input type="hidden" name="action" value="submit" />
				<input type="hidden" name="regenerate_text" id="regenerate_text" value="0" />
			</form>
		</div>
  </div>
</div>
<!--[/body]-->
 <!--[CAN-SPAM form for Account info]-->
  <div id="user_account_option" style="display:none;">
    <div id="user-contact-info-form-container">
      <h5>Contact Info</h5>
      <div id="user-contact-info-form">
        <p>
          <span style="font-weight: 700;">Add/Modify your physical postal address.</span>
        </p>
        <p>
          <strong>Company Name</strong>
          <input type="text" name="company_name" id="company_name" size="40" value="<?php echo $campaign_data['user_data']['company'];?>" />
        </p>

        <p>
          <strong>Address</strong>
          <input type="text" name="address" id="address" size="40" value="<?php echo $campaign_data['user_data']['address_line_1'];?>" />
        </p>

        <p>
          <strong>City</strong>
          <input type="text" name="city" id="city" size="40" value="<?php echo $campaign_data['user_data']['city'];?>" />
        </p>

        <p>
          <strong>State or Province</strong>
          <input type="text" name="state" id="state" size="40" value="<?php echo $campaign_data['user_data']['state'];?>" />
        </p>

        <p>
          <strong>Zip/Postal Code</strong>
          <input type="text" name="zip" id="zip" size="40" value="<?php echo $campaign_data['user_data']['zipcode'];?>" />
        </p>

        <p>
          <strong>Country</strong>
          <select name="country" id="country" class="country_footer" onchange="javascript: showCustom(this);">
            <?php
              if($campaign_data['user_data']['country_id']){
                $selectd_id=$campaign_data['user_data']['country_id'];
              }else{
                $selectd_id=225;
              }
              foreach($campaign_data['country_info'] as $country){
                if($country['country_id']==$selectd_id){
                  echo "<option value='".$country['country_id']."' selected>".$country['country_name']."</option>";
                }else{
                  echo "<option value='".$country['country_id']."'>".$country['country_name']."</option>";
                }
              }
            ?>
          </select>
		  <div style="height:29px;"><span id="country_custom_div" style="margin-left:155px;"><input type="text" maxlength="50" name="country_custom" id="country_custom" value="<?php echo  $campaign_data['user_data']['country_custom'];?>" /></span></div>
        </p>
      </div>
      <div class="btn-group">
        <a href="javascript:void(0);"  onclick="save_user_info();" class="btn add">Submit</a>
      </div>
    </div>
</div>
