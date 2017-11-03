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

    $(".fancybox").fancybox({ 'width': '390', 'height': '330' });
});
function showCustom(dpdCountry){
	if('245' == dpdCountry.value){
	$('span#country_custom_div').show();
	}else{
	$('span#country_custom_div').hide();
	}
}
</script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-ui-1.8.13.custom.min.js?v=6-20-13"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css/blitzer/jquery-ui-1.8.14.custom.css?v=6-20-13" />
<script type="text/javascript">

  <?php if(!$campaign_data['user_info']){ ?>
    var user_info=false;
  <?php }else{ ?>
    var user_info=true;
  <?php } ?>
  var send_now_email=true;
  jQuery('.send_now').live('click',function(){
    send_now_email=true;
	$('#send_now').val('1');

    var c_sender_name = $('#email_from').val() ;
    var c_sender_email =  $('#email_id').val() ;
    $('.campaign_sender_name').html(c_sender_name);
    $('.campaign_sender_email').html(c_sender_email);
    var c_subject = $('#email_subject').val();
    $('.campaign_subject').html(c_subject);
	if(c_subject ==''){
		$.fancybox(('<h5>Add "Email Subject"</h5><p>An "Email Subject" is required.</p>'),{ 'autoDimensions':false,'height':'150','width':'300','centerOnScroll':true,'modal':false});
		return false;
	}else if(c_sender_name ==''){
		$.fancybox(('<h5>Add "From Name"</h5><p>A "From Name" is required.</p>'),{ 'autoDimensions':false,'height':'150','width':'300','centerOnScroll':true,'modal':false});return false;
	}else if(c_sender_email ==''){
		$.fancybox(('<h5>Add "From Email"</h5><p>A "From Email" is required.</p>'),{ 'autoDimensions':false,'height':'150','width':'300','centerOnScroll':true,'modal':false});
		return false;
	}else if(!($('[name="subscriptions[]"]:checked').length > 0)){
    
    $.fancybox(('<h5>"Select Contact List"</h5><p>Select minimum one list to sent campaign.</p>'),{ 'autoDimensions':false,'height':'150','width':'300','centerOnScroll':true,'modal':false});
    return false;
  }else{
		var c_sender_email_domain = c_sender_email.split('@')[1].toLowerCase();
		if(c_sender_email_domain.substring(0,3) === 'aol' || c_sender_email_domain.substring(0,3) === 'gmx' || c_sender_email_domain.substring(0,5) === 'yahoo'){
			$.fancybox(('<h5>From Email Issue</h5><p>Due to recent changes at  Yahoo, GMX and AOL, campaigns sent from a <b>yahoo</b> or <b>gmx</b> or <b>aol</b> email addresses will not be delivered. Please use a different "From Email" for your campaigns.</p>'),{ 'autoDimensions':false,'height':'150','width':'600','centerOnScroll':true,'modal':false});
			exit();
		}
	}

	<?php if(count($campaign_data['campaigns_list']) >0 ){?>
    var is_abtesting_select = $('#ab_test_campaign').val();
    if($('#is_abtesting').is(":checked")){
      if(is_abtesting_select == 0){
    
        $.fancybox(('<h5>Add "Campaign a/b testing"</h5><p>A "Select campaign for a/b testing" is required.</p>'),{ 'autoDimensions':false,'height':'150','width':'300','centerOnScroll':true,'modal':false});
        return false;
      }
    }
    
  <?php } ?>
    var selectedContactsCount =0;
    var block_data;
    block_data=$('#form_campaign_send').serialize();
    jQuery.ajax({
      url: "<?php echo base_url(); ?>newsletter/campaign/selected_subscribers",
      type:"POST",
      data:block_data,
      success: function(data){
        $('.number_of_contact').html(data);
        $('.remaining_quota').html($('#quota_remaining').val());
    <?php if(isset($remaining) && $mode != ''){ ?>
            var countOfSubscriber = data;
            var remaining = '<?php echo $remaining;?>';
			if($('#quota_remaining').val() < (1 * data)){
			$.fancybox(('<h5>Your trying to send an email to '+data+' People, but you only have '+$('#quota_remaining').val()+' credits available, purchase more credits <a href="<?php echo base_url('upgrade_package_cim/credit/');?>">here</a></h5>'),{ 'autoDimensions':false,'height':'150','width':'500','centerOnScroll':true,'modal':false});
			return false;
		}
            
          <?php }else{ ?>
        if($('#quota_remaining').val() < (1 * data)){
            $.fancybox($('#quota_exceeded_msg').html(),{ 'autoDimensions':false,'height':'250','width':'600','centerOnScroll':true,'modal':false});
            return false;
        }
		  <?php } ?>
        if(user_info){
      
          $.fancybox($('#send_now_msg').html(),{'autoDimensions':false,'height':'232','width':'430','centerOnScroll':true});
        }else{
      
          setTimeout("$.fancybox($('#user_account_option').html(),{ 'autoDimensions':false,'height':'510','width':'630','centerOnScroll':true,'modal':false});", 1000);
        }
      }
    });
  });
  $('#fancybox-wrap').find('.send_mail').live('click',function(){
    parent.submitFrm();
    $.fancybox.close();
  });

  $('#fancybox-wrap').find('.cancel_mail').live('click',function(){
    $.fancybox.close();
  });
  jQuery('.subscriptions_check').live('click',function(){

    var c_sender_name = $('#email_from').val() ;
    var c_sender_email =  $('#email_id').val() ;
    $('.campaign_sender_name').html(c_sender_name);
    $('.campaign_sender_email').html(c_sender_email);
    var c_subject = $('#email_subject').val();
    $('#campaign_subject').html(c_subject);

    var block_data;
    block_data=$('#form_campaign_send').serialize();
    jQuery.ajax({
      url: "<?php echo base_url(); ?>newsletter/campaign/selected_subscribers",
      type:"POST",
      data:block_data,
      success: function(data){
        $('.number_of_contact').html(data);
      }
    });
  });
  function fancyAlert(msg){
    $.fancybox({
      'content' : "<div style=\"margin:20px;width:240px;\">"+msg+"</div>"
    });
  }
  jQuery('#is_abtesting').live('click',function(){
    if (jQuery('#is_abtesting').is(":checked"))
    {
      jQuery('#ab-testing').css("display", "block");
    }else{
      jQuery('#ab-testing').css("display", "none");
    }
    
  });
  function submitFrm(){

    $('save_email').val('1');
    $.blockUI({ message: '<h3 class="please-wait">Please wait...</h3>' });

    document.form_campaign_send.submit();
  }
  function scheduleFrm(){
    $('save_email').val('1');
	$('#send_now').val('0');
   <?php if(isset($remaining) && $remaining > 0){ ?>
  //var countOfSubscriber = $('.number_of_contact').html();
  var remaining = '<?php echo $remaining;?>';
  /*alert(remaining);
  alert($('.number_of_contact').val());
  alert(remaining-countOfSubscriber);*/
  var block_data;
    block_data=$('#form_campaign_send').serialize();
    jQuery.ajax({
      url: "<?php echo base_url(); ?>newsletter/campaign/selected_subscribers",
      type:"POST",
      data:block_data,
      success: function(data){
        //$('.number_of_contact').html(data);
		if((remaining-data) < 0 ){
			$.fancybox(('<h5>Your trying to send an email to '+data+' People, but you only have '+remaining+' credits available, purchase more credits <a href="<?php echo base_url('upgrade_package_cim/credit/');?>">here</a></h5>'),{ 'autoDimensions':false,'height':'150','width':'500','centerOnScroll':true,'modal':false});
			return false;
		}else{
			$('.schedule_delivery').toggle();
		}
      }
    });
  
	
  /* if(countOfSubscriber > remaining ){
    
    $.fancybox(('<h5>You have extend limit of sending mail.Please Purchase Credit.</h5>'),{ 'autoDimensions':false,'height':'150','width':'300','centerOnScroll':true,'modal':false});
    return false;
  } */
  
  <?php }else{ ?>
    $('.schedule_delivery').toggle();
  <?php } ?>
  }
  
    
    function send_test_email_mail_tester(){
	$.blockUI({ message: '<h3 class="please-wait">Please wait...</h3>' });
	var is_ga = $('#is_ga_enabled').is(':checked');
	var is_ctrack = $('#is_clicktracking').is(':checked');
	is_ctrack = (is_ctrack)?'yes':'no';
	//is_ctrack = $('#is_clicktracking').length ? $('#is_clicktracking').is(':checked') : is_ctrack;
	var c_sender_name = $('#email_from').val() ;
    var c_subject = $('#email_subject').val();
	var c_sender_email =  $('#email_id').val() ;
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
    //$.blockUI({ message: '<h3 class="please-wait">Please wait...</h3>' });

	block_data+="is_ga="+is_ga+"&is_ctrack="+is_ctrack+"&email_address="+escape($('#email_address').val())+"&email_subject="+encodeURIComponent($('#email_subject').val())+"&email_id="+escape($('#email_id').val())+"&email_from="+encodeURIComponent($('#email_from').val());
	if($('#reply_to_email').val() != '')	{
		block_data +="&reply_to_email="+escape($('#reply_to_email').val());
	}
    jQuery.ajax({
      url: "<?php echo base_url() ?>newsletter/campaign_email_test_mail_tester/index/<?php echo $campaign_data['campaign_id']; ?>",
      type:"POST",
      data:block_data,
      contentType: "application/x-www-form-urlencoded;charset=utf-8",
      success: function(data) {
        $.unblockUI();
        var data_arr=data.split(":", 3);
        if(data_arr[0]=="error"){
          $('.email_msg').html('');
          $('.email_msg').html(data_arr[1]);
          $('.email_msg').addClass('info');
          $('.email_msg').fadeIn();
          if(data_arr[2]>25){
            if($('.test_email_count').length<=0){
              var html='<tr class="test_email_count" style="display:block;"><td colspan="2"><div class="email_msg info" style="margin-top:30px;">You have reached the maximum allowed tests for this campaign.</div></td></tr><tr class="test_email_count" style="display:block;"><td colspan="2" style="float:right;"><a style="margin:10px 10px 0 0px; padding-right:1px;" class="button-red fr subscr_list " title="Cancel" href="javascript:void(0); " onclick="javascript:$(\'.test_email_count\').hide();$(\'#link_send_test\').show();"><span>Cancel</span></a></td></tr>';
              $('#link_send_test').after(html);
              $('.email_address_tr').remove();
            }else{
              $('.email_address_tr').remove();
              $('.test_email_count').show();
              $('.email_msg').html('You have reached the maximum allowed tests for this campaign.');
              $('.email_msg').show();
            }
          }
        }else if(data_arr[0]=="Success"){
          $('#email_address').val('');
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
  /*
    Function to send a test email
  */
  function send_test_email(){
	$.blockUI({ message: '<h3 class="please-wait">Please wait...</h3>' });
	var is_ga = $('#is_ga_enabled').is(':checked');
	var is_ctrack = $('#is_clicktracking').is(':checked');
	is_ctrack = (is_ctrack)?'yes':'no';
	//is_ctrack = $('#is_clicktracking').length ? $('#is_clicktracking').is(':checked') : is_ctrack;
	var c_sender_name = $('#email_from').val() ;
    var c_subject = $('#email_subject').val();
	var c_sender_email =  $('#email_id').val() ;
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
    //$.blockUI({ message: '<h3 class="please-wait">Please wait...</h3>' });

	block_data+="is_ga="+is_ga+"&is_ctrack="+is_ctrack+"&email_address="+escape($('#email_address').val())+"&email_subject="+encodeURIComponent($('#email_subject').val())+"&email_id="+escape($('#email_id').val())+"&email_from="+encodeURIComponent($('#email_from').val());
	if($('#reply_to_email').val() != '')	{
		block_data +="&reply_to_email="+escape($('#reply_to_email').val());
	}
    jQuery.ajax({
      url: "<?php echo base_url() ?>newsletter/campaign_email_test/index/<?php echo $campaign_data['campaign_id']; ?>",
      type:"POST",
      data:block_data,
      contentType: "application/x-www-form-urlencoded;charset=utf-8",
      success: function(data) {
        $.unblockUI();
        var data_arr=data.split(":", 3);
        if(data_arr[0]=="error"){
          $('.email_msg').html('');
          $('.email_msg').html(data_arr[1]);
          $('.email_msg').addClass('info');
          $('.email_msg').fadeIn();
          if(data_arr[2]>25){
            if($('.test_email_count').length<=0){
              var html='<tr class="test_email_count" style="display:block;"><td colspan="2"><div class="email_msg info" style="margin-top:30px;">You have reached the maximum allowed tests for this campaign.</div></td></tr><tr class="test_email_count" style="display:block;"><td colspan="2" style="float:right;"><a style="margin:10px 10px 0 0px; padding-right:1px;" class="button-red fr subscr_list " title="Cancel" href="javascript:void(0); " onclick="javascript:$(\'.test_email_count\').hide();$(\'#link_send_test\').show();"><span>Cancel</span></a></td></tr>';
              $('#link_send_test').after(html);
              $('.email_address_tr').remove();
            }else{
              $('.email_address_tr').remove();
              $('.test_email_count').show();
              $('.email_msg').html('You have reached the maximum allowed tests for this campaign.');
              $('.email_msg').show();
            }
          }
        }else if(data_arr[0]=="Success"){
          $('#email_address').val('');
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
  jQuery("#save_exit").live('click',function(){
    $('#save_email').val('1');
    document.form_campaign_send.submit();
  });
  jQuery(".schedule_email").live('click',function(){
    send_now_email=false;
	var c_sender_name = $('#email_from').val() ;
    var c_sender_email =  $('#email_id').val() ;
    $('.campaign_sender_name').html(c_sender_name);
    $('.campaign_sender_email').html(c_sender_email);

    var c_subject = $('#email_subject').val();
    $('.campaign_subject').html(c_subject);


	if(c_subject ==''){
		$.fancybox(('<h5>Add "Email Subject"</h5><p>An "Email Subject" is required.</p>'),{ 'autoDimensions':false,'height':'150','width':'300','centerOnScroll':true,'modal':false});
		return false
	}else if(c_sender_name ==''){
		$.fancybox(('<h5>Add "From Name"</h5><p>A "From Name" is required.</p>'),{ 'autoDimensions':false,'height':'150','width':'300','centerOnScroll':true,'modal':false});
		return false
	}else if(c_sender_email ==''){
		$.fancybox(('<h5>Add "From Email"</h5><p>A "From Email" is required.</p>'),{ 'autoDimensions':false,'height':'150','width':'300','centerOnScroll':true,'modal':false});
		return false
	}else if(!($('[name="subscriptions[]"]:checked').length > 0)){
    
		$.fancybox(('<h5>"Select Contact List"</h5><p>Select minimum one list to sent campaign.</p>'),{ 'autoDimensions':false,'height':'150','width':'300','centerOnScroll':true,'modal':false});
    return false;
	}else{
		var c_sender_email_domain = c_sender_email.split('@')[1].toLowerCase();
		if(c_sender_email_domain.substring(0,3) === 'aol' || c_sender_email_domain.substring(0,3) === 'gmx' || c_sender_email_domain.substring(0,5) === 'yahoo'){
			$.fancybox(('<h5>From Email Issue</h5><p>Due to recent changes at  Yahoo, GMX and AOL, campaigns sent from a <b>yahoo</b> or <b>gmx</b> or <b>aol</b> email addresses will not be delivered. Please use a different "From Email" for your campaigns.</p>'),{ 'autoDimensions':false,'height':'150','width':'600','centerOnScroll':true,'modal':false});
			return false;
		}
	}

	<?php if(count($campaign_data['campaigns_list']) >0 ){?>
    var is_abtesting_select = $('#ab_test_campaign').val();
    if($('#is_abtesting').is(":checked")){
      if(is_abtesting_select == 0){
    
        $.fancybox(('<h5>Add "Campaign a/b testing"</h5><p>A "Select campaign for a/b testing" is required.</p>'),{ 'autoDimensions':false,'height':'150','width':'300','centerOnScroll':true,'modal':false});
        return false;
      }
    }
    
  <?php } ?>
  
    var block_data;
    block_data=$('#form_campaign_send').serialize();
    jQuery.ajax({
      url: "<?php echo base_url(); ?>newsletter/campaign/selected_subscribers",
      type:"POST",
      data:block_data,
      success: function(data){
		$('.number_of_contact').html(data);
		<?php if(isset($remaining) && $remaining > 0){ ?>
            var countOfSubscriber = data;
            var remaining = '<?php echo $remaining;?>';
			if($('#quota_remaining').val() < (1 * data)){
				$.fancybox(('<h5>Your trying to send an email to '+data+' People, but you only have '+$('#quota_remaining').val()+' credits available, purchase more credits <a href="<?php echo base_url('upgrade_package_cim/credit/');?>">here</a></h5>'),{ 'autoDimensions':false,'height':'150','width':'500','centerOnScroll':true,'modal':false});
				return false;
			}else{
				$('.number_of_contact').html(data);
			  if(user_info){
				$('#save_email').val('0');
				//$.fancybox($('#schedule_msg').html(),{'autoDimensions':false,'height':'212','width':'400','centerOnScroll':true});
				$.fancybox($('#send_now_msg').html(),{'autoDimensions':false,'height':'212','width':'400','centerOnScroll':true});
			  }else{
				$('#save_email').val('0');
				setTimeout("$.fancybox($('#user_account_option').html(),{ 'autoDimensions':false,'height':'510','width':'630','centerOnScroll':true,'modal':false});", 1000);
			  }
			}
            
          <?php }else{ ?>
			if($('#quota_remaining').val() < (1 * data)){
				if($('#quota_remaining').val() < 0)
					$('.remaining_quota').html('0');
				else
					$('.remaining_quota').html($('#quota_remaining').val());
				$.fancybox($('#quota_exceeded_msg').html(),{ 'autoDimensions':false,'height':'200','width':'600','centerOnScroll':true,'modal':false});
				exit;
			}else{
			  $('.number_of_contact').html(data);
			  if(user_info){
				$('#save_email').val('0');
				//$.fancybox($('#schedule_msg').html(),{'autoDimensions':false,'height':'212','width':'400','centerOnScroll':true});
				$.fancybox($('#send_now_msg').html(),{'autoDimensions':false,'height':'212','width':'400','centerOnScroll':true});
			  }else{
				$('#save_email').val('0');
				setTimeout("$.fancybox($('#user_account_option').html(),{ 'autoDimensions':false,'height':'510','width':'630','centerOnScroll':true,'modal':false});", 1000);
			  }
			}
		  <?php } ?>
			}
		});

	});
  jQuery(".share_facebook").live('click',function(){
    var block_data="";
    jQuery.ajax({
      url: "<?php echo base_url()."newsletter/campaign/share_link/".$campaign_data['campaign_id'] ?>",
      type:"POST",
      data:block_data,
      success: function(data){
        $.fancybox({
          'content' : data
        });
      }
    });
  });


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
        if(send_now_email){
          $.fancybox($('#send_now_msg').html(),{'autoDimensions':false,'height':'212','width':'400','centerOnScroll':true});
        }else{
          $.fancybox($('#send_now_msg').html(),{'autoDimensions':false,'height':'212','width':'400','centerOnScroll':true});
         // $.fancybox($('#schedule_msg').html(),{'autoDimensions':false,'height':'212','width':'400','centerOnScroll':true});
        }
        $.fancybox.close();
        /*
        *  Update company info on campaing footer
        */

        jQuery.ajax({
          url: "<?php echo base_url() ?>newsletter/campaign/update_company_info_on_campaign/<?php echo $campaign_data['campaign_id']; ?>",
          type:"POST",
          data:block_data,
          success: function(data) {

          }
        });
      }
    }
  });
}
$('#btn_add_other_eml').live('click', function(){
	$.fancybox($('#add_other_from_emails').html(),{ 'autoDimensions':false,'height':'190','width':'530','centerOnScroll':true,'modal':false});
	}
);
function save_another_eml(){
	var em = $('#fancybox-wrap').find('#another_emailid').val();
	var newEml = encodeURIComponent(em);
	$.fancybox.close();
	$.blockUI({ message: '<h3 class="please-wait">Please wait...</h3>' });
	jQuery.ajax({
		url: "<?php echo base_url() ?>newsletter/campaign_email_setting/add_another_emailid/",
		type:"POST",
		data:'newEml='+newEml,
		success: function(data) {
			if(data =='InvalidDomain'){
				setTimeout("$.fancybox($('#InvalidDomain').html(),{ 'autoDimensions':false,'height':'490','width':'430','centerOnScroll':true,'modal':false});", 300);
			}else if(data =='err'){				
				$('#verify_eml').html('<h5>Add new email address</h5><p><font color="#ff0000">* Invalid Email address.</font></p>');
				setTimeout("$.fancybox($('#verify_eml').html(),{ 'autoDimensions':false,'height':'110','width':'430','centerOnScroll':true,'modal':false});", 300);
			}else if(data =='dup'){								
				$('#verify_eml').html('<h5>Add new email address</h5><p><font color="#ff0000">* You have already added this Email address.</font></p>');
				setTimeout("$.fancybox($('#verify_eml').html(),{ 'autoDimensions':false,'height':'110','width':'430','centerOnScroll':true,'modal':false});", 300);
			}else if(data =='temp'){								
				$('#verify_eml').html('<h5>Add new email address</h5><p><font color="#ff0000">* Please enter your permanent Email address.</font></p>');
				setTimeout("$.fancybox($('#verify_eml').html(),{ 'autoDimensions':false,'height':'110','width':'430','centerOnScroll':true,'modal':false});", 300);
			}else{
				$('#verify_eml').html('<h5>Verify your email</h5><p>A verification email was sent to '+em+', please click on the verification link and select the appropriate reason for changing your email address.</p>');
				setTimeout("$.fancybox($('#verify_eml').html(),{ 'autoDimensions':false,'height':'210','width':'430','centerOnScroll':true,'modal':false});", 300);
			}
			$.unblockUI();
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

//setInterval(function(){ updateFromEmailDpd() }, 10000);

function openpinterest(){
	var s= encodeURIComponent($('#email_subject').val());
	var u = "http://pinterest.com/pin/create/button/?url=<?php echo urlencode(CAMPAIGN_DOMAIN.'c/'.$campaign_data['campaign_id']);?>&description="+s;

	var win=window.open(u, '_blank');
	win.focus();
}
function addToken(txtToAdd){
	var caretPos = document.getElementById("email_subject").selectionStart;    
	var textAreaTxt = (jQuery("#email_subject").val().toLowerCase()=="unnamed")? "" : jQuery("#email_subject").val();
	txtToAdd = (caretPos === 0)? txtToAdd + " " : " " + txtToAdd + " ";
    jQuery("#email_subject").val(textAreaTxt.substring(0, caretPos) + txtToAdd + textAreaTxt.substring(caretPos) );
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
});
*/
</script>
<style>
  #ui-datepicker-div{display:none;}
</style>
<!--[body]-->
  <div id="body-dashborad">
    <div class="container">
      <h1>
       <a class="btn add subscr_list " title="Save & Exit" id="save_exit" href="javascript:void(0);">Save & Exit</a>
        <?php  if($campaign_data['camapign']['is_responsive'] == 1){ ?>
         <a class="btn cancel" title="Edit Campaign"  href="<?php echo base_url()?>newsletter/campaign/responsivetemplate/<?php echo $campaign_data['campaign_id']?>">Edit Campaign</a>
         <?php }else{?>
         <a class="btn cancel" title="Edit Campaign"  href="<?php echo ($campaign_data['campaign_template_option']==3) ? site_url('newsletter/campaign/campaign_editor/'.$campaign_data['campaign_id']) : site_url('newsletter/campaign_template_options/index/'.$campaign_data['campaign_id']) ;?>">Edit Campaign</a>
       
         <?php }?>  <?php 
       // echo "<pre>";print_r($campaign_data['camapign']);exit;
        if($campaign_data['camapign']['is_responsive'] == 1){
            $preview_url = base_url().'newsletter/campaign/responsiveview/'.$campaign_data['campaign_id'];
        }else{
            $preview_url = CAMPAIGN_DOMAIN.'c/'.$campaign_data['campaign_id'];
        } ?>
        <a class="btn cancel preview_email_setting" title="Preview" target="_blank" href="<?php echo CAMPAIGN_DOMAIN.'c/'.$campaign_data['campaign_id'];?>">Preview</a>
        Campaign Options
      </h1>
        <div class="dashboard-home dashboard-home_email_setting">
  <?php
    // display all messages
    if (is_array($messages)):
      echo '<div class="info" style="border:none;background:none;">';
      foreach ($messages as $type => $msgs):
        foreach ($msgs as $message):
          echo ('<span class="' .  $type .'">' . $message . '</span>');
        endforeach;
      endforeach;
      echo '</div>';
    endif;
  ?>
  <?php
    if(validation_errors()){
      echo '<div  class="info">'.validation_errors().'</div>';
    }
  ?>
  <?php
    echo form_open('newsletter/campaign_email_setting/index/'.$campaign_data['campaign_id'], array('id' => 'form_campaign_send','name'=>'form_campaign_send','class'=>"form-website"));
   ?>
    <input type="hidden" value="0" name="send_now" id="send_now" />
    <input type="hidden" value="0" name="save_email" id="save_email" />
    <input type="hidden" value="<?php echo $quota_remaining;?>" name="quota_remaining" id="quota_remaining" />

    <div class="left-menu account campaign-options" style="width:500px;">
      <div class="profile-container form-fields personalization-subject">
        <strong>Email Subject:</strong>
        <?php
          if($campaign_data['camapign']['email_subject']==""){
         //   $subject=$campaign_data['camapign']['campaign_title'];
          }else{
            $subject=$campaign_data['camapign']['email_subject'];
          }
          echo form_input(array('name'=>'email_subject','id'=>'email_subject','placeholder'=>'Enter your Subject Line here...','maxlength'=>250, 'style'=>'width:308px','size'=>40,'value'=>$subject != "" ? $subject : set_value('email_subject') ));
        ?>
        <!-- Personalization dropdown -->
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
				<?php/*  foreach($global as $key => $val){ ?>
                            <option value="{<?php echo $val ?>}"><?php echo $val ?></option>
                            
                        <?php } */?>
        	</select>
        </div>

        <strong>From Name:</strong>
        <?php echo form_input(array('name'=>'email_from','id'=>'email_from','maxlength'=>250,'size'=>40, 'style'=>'width:443px','value'=>$campaign_data['camapign']['sender_name'] != "" ? $campaign_data['camapign']['sender_name'] : $campaign_data['email_from'] )); ?>

		<strong>From Email:</strong>
		<select name="email_id" id="email_id" style="width:368px;">
			<?php
				foreach($campaign_data['email_id'] as $fromEml){
					if($campaign_data['camapign']['sender_email'] == $fromEml)
					echo "<option value='$fromEml' selected>{$fromEml}</option>";
					elseif($campaign_data['last_campaign_from_email'] == $fromEml)
					echo "<option value='$fromEml' selected>{$fromEml}</option>";
					else
					echo "<option value='$fromEml'>{$fromEml}</option>";
				}
			?>
		</select>
		<span class="autotresponder_list_div" style="margin-left:5px;"><a href="javascript:void(0);" id="btn_add_other_eml" class="edit-interval">Add New</a></span>
		<span><a href="javascript:void(0);" onclick="javascript: updateFromEmailDpd();" id="btn_refresh" style="margin-left:5px;"><img src="<?php echo $this->config->item('webappassets');?>images/reload2.png" height="14" alt="Refresh" align="absmiddle" /></a></span>

		<?php if($campaign_data['reply_to_enabled']){ ?>
			<strong>Reply-to Email:</strong>
			<select name="reply_to_email" id="reply_to_email" style="width:368px;">
			<?php
				foreach($campaign_data['email_id'] as $fromEml){
					if($campaign_data['camapign']['reply_to_email'] == $fromEml)
					echo "<option value='$fromEml' selected>{$fromEml}</option>";					
					else
					echo "<option value='$fromEml'>{$fromEml}</option>";
				}
			?>
		</select>	
		<?php }?>
		<?php if($campaign_data['is_ga_enabled']){?>
			<strong><input type="checkbox" name="is_ga_enabled" id="is_ga_enabled" value="1" checked style="margin-right:12px;" />Track using Google Analytics</strong>
		<?php }?>
		<?php if($campaign_data['is_clicktracking']){?>
			<strong><input type="checkbox" name="is_clicktracking" id="is_clicktracking" value="1" checked style="margin-right:12px" />Track Clicks</strong>
		<?php }?>
		<strong><input type="checkbox" name="is_abtesting" <?php if ($campaign_data['camapign']['is_ab'] == '1') {echo 'checked="checked"';} ?> id="is_abtesting" value="1" style="margin-right:12px" />Is A/B Testing enabled</strong> 
    <label id="ab-testing" <?php if($campaign_data['camapign']['is_ab'] == '0'){ echo 'style="display:none"'; } ?> >
          <?php 
           if(count($campaign_data['campaigns_list']) == 0 ) {
            echo "<strong>You'll be able to pick a campaign to test against when you setup your next campaign.</strong>";
          }else{?>
          <strong>Select Campaign</strong>
          <select name="ab_test_campaign"  id="ab_test_campaign" style="width:368px;">
              <option value='0'>Select</option>
              <?php 
                foreach($campaign_data['campaigns_list'] as $campaigns_list){
          if ($campaign_data['camapign']['campaign_abtesting'] == $campaigns_list['campaign_id']) 
            echo "<option value='". $campaigns_list['campaign_id']."' selected>".$campaigns_list['campaign_title']."</option>";
          else
            echo "<option value='" . $campaigns_list['campaign_id'] . "' >" . $campaigns_list['campaign_title'] . "</option>";
                  
                }
                
              ?>
          </select> 
          <?php } ?>
      </label>
        <strong>Select List:</strong>
        <?php
          $i=0;
          echo '<div style="height:150px; padding:10px; border:1px solid #ddd; border-radius:5px 5px 5px 5px;background:none repeat scroll 0 0 #fbfbfb; overflow:auto;"><table style="border:none">';
          foreach($subscription_data['subscriptions'] as $subscription){
            if(isset($campaign_data['camapign']['subscription_list']) && in_array($subscription['subscription_id'],$campaign_data['camapign']['subscription_list']))
              $checked=true;
            else
              $checked=false;
            echo '<tr style="border:1px solid #000"><td style="padding:3px 3px;">';
            echo form_checkbox(array('name'=>'subscriptions[]','id'=>'subscriptions','class'=>'subscriptions_check','value'=>$subscription['subscription_id'],'checked'=>$checked ,'style'=>'display:inline;margin-right:15px;')).'</td><td >'.ucwords(substr($subscription['subscription_title'],0,25))." (".$subscription['number_of_contacts'].")";
            echo '</td></tr>';
            $i++;
          }
          if($i<=0) {
            echo "Please Create Subscriptions";
          }
          echo '</table></div>';
        ?>

        <div class="schedule_delivery">
          <a href="javascript:void(0);" class="btn add send_now inline-block">Send Now</a>
          <a href="javascript:void(0);" class="btn add inline-block" onclick="scheduleFrm();">Schedule Delivery</a>
        </div>
        <div style="display:none;" id="send_now_msg">
          <h5>Campaign Overview:</h5>
          <p>
            <strong>Total Contacts:</strong> <span class="number_of_contact"></span><br />
            <strong>Subject:</strong> <span class="campaign_subject"></span><br />
            <strong>From Name:</strong> <span class="campaign_sender_name"></span><br />
            <strong>From Email:</strong> <span class="campaign_sender_email"></span><br />
          </p>
          <div class="btn-group">
            <a class="fast_confirm_proceed send_mail btn confirm">Yes</a>
            <a class="fast_confirm_cancel cancel_mail btn cancel">No</a>
          </div>
        </div>



        <div class="schedule_delivery" <?php if(isset($_POST['send_now'])&&($_POST['send_now']!=0)||(!isset($_POST['send_now']))){?>style="display:none;" <?php } ?>>
          <strong>Delivery Date</strong>
          <?php echo '<input value="'.$campaign_data['camapign']['delivery_date'].'" id="scheduled_date" name="scheduled_date" type="text" size="40" style="width:160px; height:22px;"  readonly>'; ?>
          <script type="text/javascript">
          $(function() { $( "#scheduled_date" ).datepicker();});
          </script>
          <strong>Start Sending at</strong>
          <select class="select" style="margin:0 5px 0 0;" name="sch_hours">
          <?php
            for($i=1;$i<=12;$i++){
              if($campaign_data['camapign']['send_time'][0]==$i){
                echo "<option value='$i' selected='selected'>".$i."</option>";
              }else{
                echo "<option value='$i'>".$i."</option>";
              }
            }
          ?>
          </select>
          <select  class="select" style="margin:0 5px 0 0;" name="sch_min">
            <?php
            for($i=0;$i<=59;$i++){
              if(strlen($i)==1){
                if($campaign_data['camapign']['send_time'][1]=="0".$i){
                  echo "<option value='$i' selected='selected'>0".$i."</option>";
                }else{
                  echo "<option value='$i'>0".$i."</option>";
                }
              }else{
                if($campaign_data['camapign']['send_time'][1]==$i){
                  echo "<option value='$i' selected='selected'>".$i."</option>";
                }else{
                  echo "<option value='$i'>".$i."</option>";
                }
              }
            }
            ?>
          </select>
          <select  class="select" style="margin:0 0px 0 0;" name="sch_time">
            <?php if($campaign_data['camapign']['send_time'][2]=="am"){ ?>
              <option value="am" selected="selected">AM</option>
            <?php }else{ ?>
              <option value="am">AM</option>
            <?php } ?>
            <?php if($campaign_data['camapign']['send_time'][2]=="pm"){ ?>
              <option value="pm" selected="selected">PM</option>
            <?php }else{ ?>
              <option value="pm">PM</option>
            <?php } ?>
          </select>
		  <?php $member_time_zone = array_search($this->session->userdata('member_time_zone'),getTimezones() );?>
          <p style="margin: 5px 0"><small><b><?php echo $member_time_zone;// US Pacific Time (Los Angeles)?>. </b> To change your timezone, go to <a href='<?php echo site_url("account/index");?>' style="text-decoration:underline;">Account</a> section.</small><p>
          <div>
            <?php
              echo form_button(array('name' => 'campaign_submit', 'id' => 'btnEdit','class'=>'btn add inline-block schedule_email','content' => 'Schedule'), 'Schedule');
              echo form_button(array('name'=>'campaign_cancel','class'=>'btn cancel inline-block', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"$('.schedule_delivery').toggle();"));
            ?>
          </div>

        </div>
      </div>
    </div>

    <div class="right-menu account campaign-options">
      <div class="profile-container">
        <ul class="social-icon autoresponder">
          <li><?php $encoded_url = 'https://www.facebook.com/sharer/sharer.php?u='.rawurlencode($preview_url).'&scrape=true';?>
      <a href="<?php echo $encoded_url?>"  title="Click to share this campaign on Facebook" target="_blank"><img src="<?php echo $this->config->item('webappassets');?>images/facebook-share.png?v=6-20-13" alt=""><span>Share on Facebook</span></a></li>
          <li><a href="http://twitter.com?status=Here is our newest campaign : <?php echo $preview_url;?> via RedCappi" title="Click to share this post on Twitter" target="_blank"><img src="<?php echo $this->config->item('webappassets');?>images/twitter-share.png?v=6-20-13" alt=""><span>Share on Twitter</span></a></li>
          <li style="display:none"><a href="javascript:void(0)" onclick="javascript:openpinterest();"><img border="0" src="//assets.pinterest.com/images/PinExt.png?v=6-20-13" title="Pin It" /><span>Share on Pinterest</span></a> </li>
          <li style="display:none"><a href="https://plus.google.com/share?url=<?php echo urlencode($preview_url);?>" title="Click to share this post on Google+" target="_blank"><img src="<?php echo $this->config->item('webappassets');?>images/google-plus-share.png?v=6-20-13" alt=""><span>Google+</span></a></li>
        </ul>
      </div>
      <div class="profile-container">
          <div id="link_send_test" class="clear-both">
            <a class="btn confirm subscr_list" title="Send Test" href="javascript:void(0); " onclick="javascript:$('.email_address_tr').show();$('.test_email_count').show();$('#link_send_test').hide();">
                Send a Test Email
            </a>
          </div>

          <br/>
          <div id="link_send_mt_test" class="clear-both">
            <a class="btn confirm subscr_list" target="_blank" title="Check Your Email Score Prior to Scheduling" href='<?php echo site_url("newsletter/campaign_email_setting/sendTestViaFrontEnd/".$campaign_data['camapign']['campaign_id'] . "/" . $campaign_data['camapign']['campaign_created_by']) ?>'>Check Your Email Score Prior to Scheduling</a>
          </div>
   

          <?php if($campaign_data['camapign']['test_email_count']>=25){ ?>
            <div class="test_email_count">
              <div class="info email_msg" style="margin-top:30px;">
			  You have reached the maximum allowed tests for this campaign.</div>
              <a style="margin:10px 10px 0 0px; padding-right:1px;" class="button-red fr subscr_list " title="Cancel" href="javascript:void(0); " onclick="javascript:$('.test_email_count').hide();$('#link_send_test').show();">
                Cancel
              </a>
            </div>
          <?php }else{ ?>
            <div class="email_address_tr">
              <div class="info email_msg">Separate multiple email addresses with a comma, up to 5 email addresses at a time (max. 25).</div>
              <textarea name="email_address" id="email_address" style="width:336px;"></textarea>
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
        echo form_hidden('subscription_ids_str',$campaign_data['subscription_ids_str']);
        echo form_hidden('action','send_campaign');
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

<div style="display:none;" id="quota_exceeded_msg">
  <center>
  <p align="center"><h3 style="margin:20px 0 10px 0;font-size:20px;color:#000;">Quota exceeded:</h3>
	<p align="left" style="font-size:14px;">
	You are trying to send a new campaign to <span class="number_of_contact" style="font-size:14px;color:#ac0203;font-weight:bold;"></span> number of contacts. This will exceed your allowed limit for this billing period.
<br/><br/>
Currently, you can only send to <span class="remaining_quota" style="font-size:14px;color:#ac0203;font-weight:bold;"></span> contacts.

  </p>
    <div class="diy_message" style="float:left;width:100%;">
    <button class="btn cancel cancel_mail">Close</button>
  </div>
  </center>
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
        <p>A verification email was sent , please click on the verification link and select the appropriate reason for changing your email address.</p>
</div>
<div style="display:none; " id="InvalidDomain">
	<div style=" border:2px solid #eb242b; margin:20px;padding:0px 10px;font-size:13px;font-family:calibri ">
			<p><b>Ding!</b> Our stats team has been hard at work and they have figured out that FREE web-mail domains are the lowest domains in our deliverability category. Due to this new find, we are preventing users from using domains such as Yahoo, GMAIL, AOL, Hotmail, etc as sending (FROM EMAIL) domains.</p>

			<p>To prevent the risk of delivery issues, use a FROM EMAIL address at your own custom domain.</p>

			<p>If you need to register a domain please click <a href="http://store.redcappi.com/">http://store.redcappi.com/</a> to purchase a domain, alternatively, you can email support@redcappi.com with your domain choice and we will register your domain for you.</p>

			<p>*You can always register your domain by yourself, without using our services.</p>			
	</div>
</div>
<!-- Add Other From Emails -->
<?php

function getTimezones(){
return
array (
  '(GMT-12:00) International Date Line West' => 'Pacific/Wake',
  '(GMT-11:00) Midway Islands Time' => 'Pacific/Apia',
  '(GMT-10:00) Hawaii Standard Time' => 'Pacific/Honolulu',
  '(GMT-09:00) Alaska Standard Time' => 'America/Anchorage',
  '(GMT-08:00) Pacific Standard Time' => 'America/Los_Angeles',
  '(GMT-07:00) Mountain/Phoenix Standard Time' => 'America/Phoenix',
  '(GMT-06:00) Central Standard Time' => 'America/Chicago',
  '(GMT-05:00) Eastern Standard Time' => 'America/New_York',
  '(GMT-05:00) Indiana Eastern Standard Time' => 'America/Indiana/Indianapolis',
  '(GMT-04:00) Puerto Rico and US Virgin Islands Time' => 'America/Halifax',
  '(GMT-03:30) Canada Newfoundland Time' => 'America/St_Johns',
  '(GMT-03:00) Brazil-Eastern/Argentina Standard Time' => 'America/Sao_Paulo',
  '(GMT-02:00) Mid-Atlantic' => 'America/Noronha',
  '(GMT-01:00) Central African Time' => 'Atlantic/Azores',
  '(GMT-01:00) Cape Verde Is.' => 'Atlantic/Cape_Verde',
  '(GMT) Greenwich Mean Time : Dublin' => 'Europe/London',
  '(GMT+01:00) European Central Time' => 'Europe/Berlin',
  '(GMT+02:00) Eastern European Time' => 'Europe/Istanbul',
  '(GMT+02:00) (Arabic) Egypt Standard Time' => 'Asia/Jerusalem',
  '(GMT+03:00) Eastern African Time' => 'Africa/Nairobi',
  '(GMT+03:30) Middle East Time' => 'Asia/Tehran',
  '(GMT+04:00) Near East Time' => 'Asia/Muscat',
  '(GMT+04:30) Kabul' => 'Asia/Kabul',
  '(GMT+05:00) Pakistan Lahore Time' => 'Asia/Karachi',
  '(GMT+05:30) India Standard Time' => 'Asia/Calcutta',
  '(GMT+05:45) Kathmandu' => 'Asia/Katmandu',
  '(GMT+06:00) Bangladesh Standard Time' => 'Asia/Dhaka',
  '(GMT+06:00) Sri Jayawardenepura' => 'Asia/Colombo',
  '(GMT+06:30) Rangoon' => 'Asia/Rangoon',
  '(GMT+07:00) Vietnam Standard Time' => 'Asia/Bangkok',
  '(GMT+07:00) Jakarta' => 'Asia/Bangkok',
  '(GMT+08:00) China Taiwan Time' => 'Asia/Hong_Kong',
  '(GMT+09:00) Japan Standard Time' => 'Asia/Tokyo',
  '(GMT+09:30) Australia Central Time' => 'Australia/Adelaide',
  '(GMT+10:00) Australia Eastern Time' => 'Australia/Sydney',
  '(GMT+11:00) Solomon Standard Time' => 'Asia/Magadan',
  '(GMT+12:00) New Zealand Standard Time' => 'Pacific/Auckland',
  '(GMT+13:00) Nuku\'alofa' => 'Pacific/Tongatapu',
);
}

function getTimezones_0(){
return
array (
  '(GMT-12:00) International Date Line West' => 'Pacific/Wake',
  '(GMT-11:00) Midway Island/Samoa' => 'Pacific/Apia',

  '(GMT-10:00) Hawaii' => 'Pacific/Honolulu',
  '(GMT-09:00) Alaska' => 'America/Anchorage',
  '(GMT-08:00) Pacific Time (US &amp; Canada)' => 'America/Los_Angeles',
  '(GMT-07:00) Arizona/Mountain Time (US &amp; Canada)' => 'America/Phoenix',
  '(GMT-06:00) Central Time (US &amp; Canada)' => 'America/Chicago',
  '(GMT-05:00) Eastern Time (US &amp; Canada)' => 'America/New_York',
  '(GMT-05:00) Indiana (East)' => 'America/Indiana/Indianapolis',

  '(GMT-04:00) Atlantic Time (Canada)' => 'America/Halifax',

  '(GMT-03:30) Newfoundland' => 'America/St_Johns',
  '(GMT-03:00) Brasilia' => 'America/Sao_Paulo',

  '(GMT-02:00) Mid-Atlantic' => 'America/Noronha',
  '(GMT-01:00) Azores' => 'Atlantic/Azores',
  '(GMT-01:00) Cape Verde Is.' => 'Atlantic/Cape_Verde',
  '(GMT) Casablanca' => 'Africa/Casablanca',
  '(GMT) Edinburgh' => 'Europe/London',
  '(GMT) Greenwich Mean Time : Dublin' => 'Europe/London',
  '(GMT) Lisbon' => 'Europe/London',
  '(GMT) London' => 'Europe/London',
  '(GMT) Monrovia' => 'Africa/Casablanca',
  '(GMT+01:00) Amsterdam' => 'Europe/Berlin',
  '(GMT+01:00) Belgrade' => 'Europe/Belgrade',
  '(GMT+01:00) Berlin' => 'Europe/Berlin',
  '(GMT+01:00) Bern' => 'Europe/Berlin',
  '(GMT+01:00) Bratislava' => 'Europe/Belgrade',
  '(GMT+01:00) Brussels' => 'Europe/Paris',
  '(GMT+01:00) Budapest' => 'Europe/Belgrade',
  '(GMT+01:00) Copenhagen' => 'Europe/Paris',
  '(GMT+01:00) Ljubljana' => 'Europe/Belgrade',
  '(GMT+01:00) Madrid' => 'Europe/Paris',
  '(GMT+01:00) Paris' => 'Europe/Paris',
  '(GMT+01:00) Prague' => 'Europe/Belgrade',
  '(GMT+01:00) Rome' => 'Europe/Berlin',
  '(GMT+01:00) Sarajevo' => 'Europe/Sarajevo',
  '(GMT+01:00) Skopje' => 'Europe/Sarajevo',
  '(GMT+01:00) Stockholm' => 'Europe/Berlin',
  '(GMT+01:00) Vienna' => 'Europe/Berlin',
  '(GMT+01:00) Warsaw' => 'Europe/Sarajevo',
  '(GMT+01:00) West Central Africa' => 'Africa/Lagos',
  '(GMT+01:00) Zagreb' => 'Europe/Sarajevo',
  '(GMT+02:00) Athens' => 'Europe/Istanbul',
  '(GMT+02:00) Bucharest' => 'Europe/Bucharest',
  '(GMT+02:00) Cairo' => 'Africa/Cairo',
  '(GMT+02:00) Harare' => 'Africa/Johannesburg',
  '(GMT+02:00) Helsinki' => 'Europe/Helsinki',
  '(GMT+02:00) Istanbul' => 'Europe/Istanbul',
  '(GMT+02:00) Jerusalem' => 'Asia/Jerusalem',
  '(GMT+02:00) Kyiv' => 'Europe/Helsinki',
  '(GMT+02:00) Minsk' => 'Europe/Istanbul',
  '(GMT+02:00) Pretoria' => 'Africa/Johannesburg',
  '(GMT+02:00) Riga' => 'Europe/Helsinki',
  '(GMT+02:00) Sofia' => 'Europe/Helsinki',
  '(GMT+02:00) Tallinn' => 'Europe/Helsinki',
  '(GMT+02:00) Vilnius' => 'Europe/Helsinki',
  '(GMT+03:00) Baghdad' => 'Asia/Baghdad',
  '(GMT+03:00) Kuwait' => 'Asia/Riyadh',
  '(GMT+03:00) Moscow' => 'Europe/Moscow',
  '(GMT+03:00) Nairobi' => 'Africa/Nairobi',
  '(GMT+03:00) Riyadh' => 'Asia/Riyadh',
  '(GMT+03:00) St. Petersburg' => 'Europe/Moscow',
  '(GMT+03:00) Volgograd' => 'Europe/Moscow',
  '(GMT+03:30) Tehran' => 'Asia/Tehran',
  '(GMT+04:00) Abu Dhabi' => 'Asia/Muscat',
  '(GMT+04:00) Baku' => 'Asia/Tbilisi',
  '(GMT+04:00) Muscat' => 'Asia/Muscat',
  '(GMT+04:00) Tbilisi' => 'Asia/Tbilisi',
  '(GMT+04:00) Yerevan' => 'Asia/Tbilisi',
  '(GMT+04:30) Kabul' => 'Asia/Kabul',
  '(GMT+05:00) Ekaterinburg' => 'Asia/Yekaterinburg',
  '(GMT+05:00) Islamabad' => 'Asia/Karachi',
  '(GMT+05:00) Karachi' => 'Asia/Karachi',
  '(GMT+05:00) Tashkent' => 'Asia/Karachi',
  '(GMT+05:30) Mumbai/New Delhi/Kolkata/Chennai' => 'Asia/Calcutta',

  '(GMT+05:45) Kathmandu' => 'Asia/Katmandu',
  '(GMT+06:00) Almaty' => 'Asia/Novosibirsk',
  '(GMT+06:00) Astana' => 'Asia/Dhaka',
  '(GMT+06:00) Dhaka' => 'Asia/Dhaka',
  '(GMT+06:00) Novosibirsk' => 'Asia/Novosibirsk',
  '(GMT+06:00) Sri Jayawardenepura' => 'Asia/Colombo',
  '(GMT+06:30) Rangoon' => 'Asia/Rangoon',
  '(GMT+07:00) Bangkok' => 'Asia/Bangkok',
  '(GMT+07:00) Hanoi' => 'Asia/Bangkok',
  '(GMT+07:00) Jakarta' => 'Asia/Bangkok',
  '(GMT+07:00) Krasnoyarsk' => 'Asia/Krasnoyarsk',
  '(GMT+08:00) Beijing' => 'Asia/Hong_Kong',
  '(GMT+08:00) Chongqing' => 'Asia/Hong_Kong',
  '(GMT+08:00) Hong Kong' => 'Asia/Hong_Kong',
  '(GMT+08:00) Irkutsk' => 'Asia/Irkutsk',
  '(GMT+08:00) Kuala Lumpur' => 'Asia/Singapore',
  '(GMT+08:00) Perth' => 'Australia/Perth',
  '(GMT+08:00) Singapore' => 'Asia/Singapore',
  '(GMT+08:00) Taipei' => 'Asia/Taipei',
  '(GMT+08:00) Ulaan Bataar' => 'Asia/Irkutsk',
  '(GMT+08:00) Urumqi' => 'Asia/Hong_Kong',
  '(GMT+09:00) Osaka' => 'Asia/Tokyo',
  '(GMT+09:00) Sapporo' => 'Asia/Tokyo',
  '(GMT+09:00) Seoul' => 'Asia/Seoul',
  '(GMT+09:00) Tokyo' => 'Asia/Tokyo',
  '(GMT+09:00) Yakutsk' => 'Asia/Yakutsk',
  '(GMT+09:30) Adelaide' => 'Australia/Adelaide',
  '(GMT+09:30) Darwin' => 'Australia/Darwin',
  '(GMT+10:00) Brisbane' => 'Australia/Brisbane',
  '(GMT+10:00) Canberra' => 'Australia/Sydney',
  '(GMT+10:00) Guam' => 'Pacific/Guam',
  '(GMT+10:00) Hobart' => 'Australia/Hobart',
  '(GMT+10:00) Melbourne' => 'Australia/Sydney',
  '(GMT+10:00) Port Moresby' => 'Pacific/Guam',
  '(GMT+10:00) Sydney' => 'Australia/Sydney',
  '(GMT+10:00) Vladivostok' => 'Asia/Vladivostok',
  '(GMT+11:00) Magadan' => 'Asia/Magadan',
  '(GMT+11:00) New Caledonia' => 'Asia/Magadan',
  '(GMT+11:00) Solomon Is.' => 'Asia/Magadan',
  '(GMT+12:00) Auckland' => 'Pacific/Auckland',
  '(GMT+12:00) Fiji' => 'Pacific/Fiji',
  '(GMT+12:00) Kamchatka' => 'Pacific/Fiji',
  '(GMT+12:00) Marshall Is.' => 'Pacific/Fiji',
  '(GMT+12:00) Wellington' => 'Pacific/Auckland',
  '(GMT+13:00) Nuku\'alofa' => 'Pacific/Tongatapu',
);
}
?>
