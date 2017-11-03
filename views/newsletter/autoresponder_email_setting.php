<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.mousewheel-3.0.4.pack.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.pack.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery.blockUI.js?v=6-20-13"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.css?v=6-20-13" media="screen" />
<script type="text/javascript">
  // Popup login form
  $(document).ready(function(){
	if('245' != $('#country').val())
	$('span#country_custom_div').hide();
	else
	$('span#country_custom_div').show();

    $(".fancybox").fancybox({  'width': '390', 'height': '230' });
  });
function showCustom(dpdCountry){
	if('245' == dpdCountry.value){
	$('span#country_custom_div').show();
	}else{
	$('span#country_custom_div').hide();
	}
}
</script>
<script type="text/javascript">
  /*
    Function to send a test email
  */
  function send_test_email(){
	var is_ga = $('#is_ga_enabled').is(':checked');
	var is_ctrack = $('#is_clicktracking').is(':checked');
	is_ctrack = (is_ctrack)?'yes':'no';
	
	var c_sender_name = $('#email_from').val() ;
	var c_sender_email =  $('#email_id').val() ;		 
	var c_subject = $('#email_subject').val();
	if(c_subject ==''){
		$.fancybox(('<h5>Add "Email Subject"</h5><p>An "Email Subject" is required.</p>'),{ 'autoDimensions':false,'height':'150','width':'300','centerOnScroll':true,'modal':false});
		exit;
	}else if(c_sender_name ==''){
		$.fancybox(('<h5>Add "From Name"</h5><p>A "From Name" is required.</p>'),{ 'autoDimensions':false,'height':'150','width':'300','centerOnScroll':true,'modal':false});
		exit;
	}else if(c_sender_email ==''){
		$.fancybox(('<h5>Add "From Email"</h5><p>A "From Email" is required.</p>'),{ 'autoDimensions':false,'height':'150','width':'300','centerOnScroll':true,'modal':false});
		exit;
	}else{
		var c_sender_email_domain = c_sender_email.split('@')[1].toLowerCase();
		if(c_sender_email_domain.substring(0,3) === 'aol' || c_sender_email_domain.substring(0,3) === 'gmx' || c_sender_email_domain.substring(0,5) === 'yahoo'){
			$.fancybox(('<h5>From Email Issue</h5><p>Due to recent changes at  Yahoo, GMX and AOL, campaigns sent from a <b>yahoo</b> or <b>gmx</b> or <b>aol</b> email addresses will not be delivered. Please use a different "From Email" for your campaigns.</p>'),{ 'autoDimensions':false,'height':'150','width':'600','centerOnScroll':true,'modal':false});
			exit();
		}
	}
	
    var block_data="";
    block_data+="is_ga="+is_ga+"&is_ctrack="+is_ctrack+"&email_address="+escape($('#email_address').val())+"&email_subject="+encodeURIComponent($('#email_subject').val())+"&email_id="+escape($('#email_id').val())+"&email_from="+encodeURIComponent($('#email_from').val());
	jQuery.ajax({
      url: "<?php echo base_url() ?>newsletter/campaign_email_test/autoresponder/<?php echo $autoresponder_data['campaign_id']; ?>",
      type:"POST",
      data:block_data,
      contentType: "application/x-www-form-urlencoded;charset=utf-8",
      success: function(data) {
        var data_arr=data.split(":", 3);
        if(data_arr[0]=="error"){
          $('.email_msg').html(data_arr[1]);
          $('.email_msg').addClass('info');
          $('.email_msg').fadeIn();
          if(data_arr[2]>25){
            if($('.test_email_count').length<=0){
              var html='<div class="test_email_count" style="display:block;"><div class="email_msg info" style="margin-top:30px;">You have reached the maximum allowed tests for this campaign.</div></td></tr><tr class="test_email_count" style="display:block;"><td colspan="2" style="float:right;"><a style="margin:10px 10px 0 0px; padding-right:1px;" class="button-red fr subscr_list " title="Cancel" href="javascript:void(0); " onclick="javascript:$(\'.test_email_count\').hide();$(\'#link_send_test\').show();"><span>Cancel</span></a></div>';
              $('#link_send_test').after(html);
              $('.email_address_tr').remove();
            }else{
              $('.test_email_count').show();
              $('.email_msg').html('You have reached the maximum allowed tests for this campaign.');
            }
          }
        }else if(data_arr[0]=="Success"){
          $('.email_msg').html('A test email was sent');
          $('.email_msg').addClass('info');
          $('.email_msg').fadeIn();
          setTimeout( function(){$('.email_msg').fadeOut();} , 4000);
          setTimeout( function(){$('.email_address_tr').hide();} , 4000);
          setTimeout( function(){$('#link_send_test').show();} , 4000);
          if(data_arr[1]>25){
            $('.email_address_tr').remove();
          }
        }
      }
    });
  }
</script>
<link href="<?php echo $this->config->item('webappassets');?>js/fancybox/fancy.css?v=6-20-13" rel="stylesheet" type="text/css" />
<script type="text/javascript">
  <?php if(!$autoresponder_data['user_info']){ ?>
    var user_info=false;
  <?php }else{ ?>
    var user_info=true;
  <?php } ?>
  function submitFrm(){
    if(user_info){
		var c_sender_name = $('#email_from').val() ;
		var c_sender_email =  $('#email_id').val() ;		 
		var c_subject = $('#email_subject').val();
		if(c_subject ==''){
			$.fancybox(('<h5>Add "Email Subject"</h5><p>An "Email Subject" is required.</p>'),{ 'autoDimensions':false,'height':'150','width':'300','centerOnScroll':true,'modal':false});
			exit;
		}else if(c_sender_name ==''){
			$.fancybox(('<h5>Add "From Name"</h5><p>A "From Name" is required.</p>'),{ 'autoDimensions':false,'height':'150','width':'300','centerOnScroll':true,'modal':false});
			exit;
		}else if(c_sender_email ==''){
			$.fancybox(('<h5>Add "From Email"</h5><p>A "From Email" is required.</p>'),{ 'autoDimensions':false,'height':'150','width':'300','centerOnScroll':true,'modal':false});
			exit;
		}else{
			var c_sender_email_domain = c_sender_email.split('@')[1].toLowerCase();
			if(c_sender_email_domain.substring(0,3) === 'aol' || c_sender_email_domain.substring(0,3) === 'gmx' || c_sender_email_domain.substring(0,5) === 'yahoo'){
				$.fancybox(('<h5>From Email Issue</h5><p>Due to recent changes at  Yahoo, GMX and AOL, campaigns sent from a <b>yahoo</b> or <b>gmx</b> or <b>aol</b> email addresses will not be delivered. Please use a different "From Email" for your campaigns.</p>'),{ 'autoDimensions':false,'height':'150','width':'600','centerOnScroll':true,'modal':false});
				exit();
			}
			$('#save_email').val('0');
			document.form_autoresponder_send.submit();
		}
    }else{
      setTimeout("$.fancybox($('#user_account_option').html(),{ 'autoDimensions':false,'height':'510','width':'630','centerOnScroll':true,'modal':false});", 1000);
    }
  }
    /***********Function to add user info*******************/
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
        $('.company_name').html($('#fancybox-wrap').find("#company_name").val());
        $('.address').html($('#fancybox-wrap').find("#address").val());
        $('.city').html(" | "+$('#fancybox-wrap').find("#city").val());
        $('.state').html(", "+$('#fancybox-wrap').find("#state").val());
        $('.zip').html($('#fancybox-wrap').find("#zip").val());
        var country=$('#fancybox-wrap').find("#country :selected").text();
        if(country=="United States"){
          country="USA";
        }
        $('.country').html(" | "+country);
        user_info=true;
        $('#save_email').val('0');
        document.form_autoresponder_send.submit();
        $.fancybox.close();
        /*
        *  Update company info on campaing footer
        */
        jQuery.ajax({
          url: "<?php echo base_url() ?>newsletter/autoresponder/update_company_info_on_campaign/<?php echo $autoresponder_data['campaign_id']; ?>",
          type:"POST",
          data:block_data,
          success: function(data) {

          }
        });
      }
    }
  });
}
jQuery("#save_exit").live('click',function(){
  $('#save_email').val('0');
  document.form_autoresponder_send.submit();
});
jQuery(".share_facebook").live('click',function(){
  var block_data="";
  jQuery.ajax({
    url: "<?php echo  site_url('newsletter/autoresponder/share_link/'.$autoresponder_data['campaign_id']); ?>",
    type:"POST",
    data:block_data,
    success: function(data){
      $.fancybox({
        'content' : data,
        'width': '390',
        'height': '230'
      });
    }
  });
});

$('#btn_add_other_eml').live('click', function(){
	$.fancybox($('#add_other_from_emails').html(),{ 'autoDimensions':false,'height':'190','width':'530','centerOnScroll':true,'modal':false});
	}
);
function save_another_eml(){
	var newEml = encodeURIComponent($('#fancybox-wrap').find('#another_emailid').val());

	jQuery.ajax({
		url: "<?php echo base_url() ?>newsletter/campaign_email_setting/add_another_emailid/",
		type:"POST",
		data:'newEml='+newEml,
		success: function(data) {

			if(data =='err'){
				$('#fancybox-wrap').find('#errInvalid').text('* Invalid Email');
			}else{
				setTimeout("$.fancybox($('#verify_eml').html(),{ 'autoDimensions':false,'height':'210','width':'430','centerOnScroll':true,'modal':false});", 1000);
				//$.fancybox.close();
			}
		}
	});
}
function updateFromEmailDpd() {
$.blockUI({ message: '<h3 class="please-wait">Please wait...</h3>' });
    jQuery.ajax({
		url: "<?php echo base_url() ?>newsletter/campaign_email_setting/ajaxFromEmlArray/",
		type:"POST",
		success: function(data) {
			var arrData = data.split(',');
			$('#email_id option').remove();
			$.each(arrData, function (index, value) {
				$('#email_id').append($('<option>', { value: value, text : value }));
			});

		}
	});
$.unblockUI();
}
function addToken(txtToAdd){
  var caretPos = document.getElementById("email_subject").selectionStart;
  var textAreaTxt = (jQuery("#email_subject").val().toLowerCase()=="unnamed")? "" : jQuery("#email_subject").val();
  if (caretPos === 0) {
    jQuery("#email_subject").val(textAreaTxt.substring(0, caretPos) + txtToAdd + " " + textAreaTxt.substring(caretPos) );
  } else {
    jQuery("#email_subject").val(textAreaTxt.substring(0, caretPos) + " " + txtToAdd + " " + textAreaTxt.substring(caretPos) );
  }
}
/*
jQuery("#email_subject").live('click',function(){
	if(jQuery(this).val().toLowerCase()=="unnamed"){		
		jQuery(this).val("");
	}
}).live('blur',function(){
	if(jQuery(this).val()==""){
		jQuery(this).val("Unnamed");
	}
});*/
</script>
<!--[body]-->
  <div id="body-dashborad">
    <div class="container">
      <h1>
        Autoresponder Options
        <a class="btn add subscr_list" title="Save & Exit" id="save_exit" href="javascript:void(0);">Save & Exit</a>
        <a class="btn cancel" title="Edit Campaign"  href="<?php echo ($autoresponder_data['autoresponder']['campaign_template_option']==3) ? site_url('newsletter/autoresponder/autoresponder_editor/'.$autoresponder_data['campaign_id']) : site_url('newsletter/campaign_template_options/autoresponder/'.$autoresponder_data['campaign_id']) ;?>">Edit Campaign</a>
        <a class="btn cancel preview_email_setting" title="Preview" target="_blank" href="<?php echo CAMPAIGN_DOMAIN.'a/'.$autoresponder_data['campaign_id']; ?>">Preview</a>
      </h1>

      <?php
        // display all messages
        if (is_array($messages)):
          echo '<div class="info">';
          foreach ($messages as $type => $msgs):
            foreach ($msgs as $message):
              echo ('"' .  $type .'">' . $message . '. ');
            endforeach;
          endforeach;
          echo '</div>';
        endif;
      ?>
      <?php
        if(validation_errors()){
          echo '<div class="info">'.validation_errors().'</div>';
      }
      ?>
      <?php
        echo form_open('newsletter/campaign_email_setting/autoresponder/'.$autoresponder_data['campaign_id'], array('id' => 'form_autoresponder_send','name'=>'form_autoresponder_send','class'=>"form-website"));
      ?>

      <input type="hidden" value="0" name="send_now" id="send_now" />
      <input type="hidden" value="0" name="save_email" id="save_email" />
      <div class="left-menu account campaign-options">
        <div class="profile-container personalization-subject form-fields">
          <strong>Email Subject:</strong>
          <?php
            if($autoresponder_data['autoresponder']['email_subject']==""){
            //  $subject=$autoresponder_data['autoresponder']['campaign_title'];
            }else{
              $subject=$autoresponder_data['autoresponder']['email_subject'];
            }

            echo form_input(array('name'=>'email_subject','id'=>'email_subject','placeholder'=>'Enter your Subject Line here...','maxlength'=>250,'size'=>40,'style'=>'width:313px','value'=>$subject != "" ? $subject : set_value('email_subject') ));
          ?>
		  <div class="personalization-dropdown">
            <i class="icon-user"></i> Personalize <i class="icon-chevron-down"></i>
            <select name="personalization" id="personalization" onchange="javascript:addToken(this.value); this.value = '';">
              <option value="">Select Personalization</option>
              <option value="{name}">Name</option>
              <option value="{email}">Email</option>
              <option value="{first_name}">First name</option>
              <option value="{last_name}">Last name</option>
              <option value="{address}">Address</option>
              <option value="{city}">City</option>
              <option value="{state}">State</option>
              <option value="{zip}">Zip Code</option>
              <option value="{country}">Country</option>
            </select>
          </div>
		  <strong>From Name:</strong>
          <?php echo form_input(array('name'=>'email_from','id'=>'email_from','maxlength'=>250,'size'=>40,'value'=>$autoresponder_data['autoresponder']['sender_name'] != "" ? $autoresponder_data['autoresponder']['sender_name'] : $autoresponder_data['email_from'] )); ?>

          <strong>From Email:</strong>
          <?php //echo form_input(array('name'=>'email_id','id'=>'email_id','maxlength'=>250,'size'=>40,'value'=>$autoresponder_data['autoresponder']['sender_email'] != "" ? $autoresponder_data['autoresponder']['sender_email'] : $autoresponder_data['email_id'])); ?>
		  <select name="email_id" id="email_id" style="width:368px;">
			<?php
				foreach($autoresponder_data['email_id'] as $fromEml){
					if($autoresponder_data['autoresponder']['sender_email'] == $fromEml)
					echo "<option value='$fromEml' selected>{$fromEml}</option>";
					else
					echo "<option value='$fromEml'>{$fromEml}</option>";
				}
			?>
		</select>
		<span class="autotresponder_list_div" style="margin-left:5px;"><a href="javascript:void(0);" id="btn_add_other_eml" class="edit-interval">Add New</a></span>
		<span><a href="javascript:void(0);" onclick="javascript: updateFromEmailDpd();" id="btn_refresh" style="margin-left:5px;"><img src="<?php echo $this->config->item('webappassets');?>images/reload2.png" height="14" alt="Refresh" align="absmiddle" /></a></span>

		<!-- GA & clicktracking -->
		<div style="display:inline-block;border:0px solid #ff0000;">
		<?php if($autoresponder_data['is_ga_enabled']){?>
			<span style="display:block;float:left;width:250px;"><strong><input type="checkbox" name="is_ga_enabled" id="is_ga_enabled" value="1" checked style="margin-right:12px;" />Track using Google Analytics</strong></span>
		<?php }?>
		<?php if($autoresponder_data['is_clicktracking']){?>
			<span style="display:block;float:left;width:200px;"><strong><input type="checkbox" name="is_clicktracking" id="is_clicktracking" value="1" checked style="margin-right:12px" />Track Clicks</strong></span>
			 
		<?php }?>
		</div>
		<!-- GA & clicktracking -->

          <strong>Number Of Days:</strong>
          <?php echo form_input(array('name'=>'autoresponder_schedule_interval','id'=>'autoresponder_schedule_interval','maxlength'=>3,'size'=>40,'style'=>'width:35px;height:15px;margin-right:15px;','value'=>$autoresponder_data['autoresponder']['autoresponder_scheduled_interval'] != "" ? $autoresponder_data['autoresponder']['autoresponder_scheduled_interval'] : set_value('autoresponder_schedule_interval') )) ; ?> </td>
          <b>Enter a "0" to have the email sent within a couple hours.</b>
          <a class="btn add" href="javascript:void(0);" onclick="submitFrm();">Start Autoresponder</a>
        </div>
      </div>
      <div class="right-menu account campaign-options">
        <div class="profile-container">
          <ul class="social-icon autoresponder">
            <li>
				<?php $encoded_url = 'https://www.facebook.com/sharer/sharer.php?u='.rawurlencode(CAMPAIGN_DOMAIN.'a/'.$autoresponder_data['campaign_id']).'';?>
              <a href="<?php echo $encoded_url;?>"  title="Click to share this post on Facebook" target="_blank"><img src="<?php echo $this->config->item('webappassets');?>images/facebook-share.png?v=6-20-13" alt="">
                <span>Share on Facebook</span>
              </a>
            </li>
            <li>
              <a href="http://twitter.com?status=Here is our newest campaign :<?php echo CAMPAIGN_DOMAIN.'a/'.$autoresponder_data['campaign_id']; ?>via RedCappi" title="Click to share this post on Twitter" target="_blank"><img src="<?php echo $this->config->item('webappassets');?>images/twitter-share.png?v=6-20-13" alt="">
                <span>Share on Twitter</span>
              </a>
            </li>
          </ul>
        </div>
        <div class="profile-container">
          <div id="link_send_test" class="clear-both">
            <a class="btn confirm subscr_list" title="Send Test" href="javascript:void(0); " onclick="javascript:$('.email_address_tr').show();$('.test_email_count').show();$('#link_send_test').hide();">
              Send a Test Email
            </a>
          </div>
          <?php if($autoresponder_data['camapign']['test_email_count']>=25){ ?>
            <div class="test_email_count">
              <div class="info email_msg" style="margin-top:30px;">You have reached the maximum allowed tests for this campaign.</div>
              <a style="margin:10px 10px 0 0px; padding-right:1px;" class="button-red fr subscr_list " title="Cancel" href="javascript:void(0); " onclick="javascript:$('.test_email_count').hide();$('#link_send_test').show();">
                Cancel
              </a>
            </div>
          <?php }else{ ?>
            <div class="email_address_tr">
              <div class="info email_msg">Separate multiple email addresses with a comma, up to 5 email addresses at a time (max. 25).</div>
              <textarea name="email_address" id="email_address" style="width:335px;"></textarea>
			  <div class="btn-group" style="padding: 8px 10px; text-align: right">
              <a class="btn confirm  inline-block" title="Send Test" href="javascript:void(0); " onclick="send_test_email();">
                Send
              </a>
              <a class="btn cancel inline-block" title="Cancel" href="javascript:void(0);" onclick="javascript:$('.email_address_tr').hide();$('#link_send_test').show();">
                Cancel
              </a>
			  </div>
            </div>
          <?php } ?>
        </div>
      </div>
      <?php
        echo form_hidden('subscription_ids_str',$autoresponder_data['subscription_ids_str']);
        echo form_hidden('action','send_autoresponder');
        echo form_close();
      ?>
    </div>
  </div>
<!--[/body]-->
<!--[CAN-SPAM form for Account info]-->
<div id="user_account_option" style="display:none;">
  <div id="user-contact-info-form-container">
    <h5>Contact Info</h5>
    <div id="user-contact-info-form">
      <p>
        <span style="font-weight: 700;">Your email must include your valid physical postal address.</span>
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
<!-- Add Other From Emails -->
<div style="display:none;" id="add_other_from_emails">
	<div id="add_other_from_emails_form">
        <h5>Add new email address</h5>
        <p>
          <strong>Enter the email address you'll like to verify to use in your emails</strong><br/>
          <input type="text" name="another_emailid" id="another_emailid" size="40" style="width:325px; margin:10px 0px;" /><span id='errInvalid' style="font-weight:bold; color:#ff0000 !important;padding-left:15px"></span>
        </p>
		<div class="btn-group">
			<a href="javascript:void(0);"  onclick="save_another_eml();" class="btn add">Submit</a>
		</div>
	</div>
</div>
<div style="display:none;" id="verify_eml">

        <h5>Verify your email</h5>
        <p>A verification email was sent. Check your email and verify to be able to see it as an option.</p>

</div>
<!-- Add Other From Emails -->
