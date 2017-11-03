<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title; ?></title>
<?php $ci =& get_instance(); ?>
<!--------------Load Css------------------------------->
<link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico">
<!--------------Load Css------------------------------->
<?php echo link_tag('webappassets/css/inner_red.css?v=6-20-13'); ?>
<!--[if IE 6]>
<script src="<?php echo $this->config->item('webappassets');?>js/DD_belatedPNG_0.0.8a-min.js?v=6-20-13" type="text/javascript"></script>
<script type="text/javascript">
DD_belatedPNG.fix('#outer, #container, #header, a, img');
</script>
<![endif]-->

<!--[main script] -->
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-latest.js?v=6-20-13"></script>
<!--[/main script] -->

<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.pack.js?v=6-20-13"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.css?v=6-20-13" media="screen" />
<?php
	if($this->session->userdata('member_status')=='inactive'){
?>
<script type="text/javascript">
	// Send confirmation email
	jQuery(".resend_confirmation")
	.live('click',function(){
		jQuery.ajax({
			url: "<?php echo base_url() ?>user/user_confirmation_notification/<?php echo $this->session->userdata('member_id'); ?>/confirmation_msg",
			type:"POST",
			success: function(data) {
				//display success message
				if(data=="success"){
					$.fancybox({
						'content' : "<div style=\"margin:20px;width:240px;\">Your confirmation mail has been sent</div>"
					});
				}
			}
		});
	});
</script>
<?php } ?>
<script type="text/javascript">
	$(document).ready(function(){
		$(".fancybox").fancybox();
    });
	jQuery(".feedback_popup").live('click',function(){
		$.ajax({
			type        : "POST",
			cache       : false,
			url         : '<?php echo base_url() ?>feedback/create',
			data        : $(this).serializeArray(),
			success: function(data) {
				if(data){
					$.fancybox(data,{'autoDimensions':false,'height':'auto','width':'680','centerOnScroll':true});
				}
			}
		});
		return false;
	});
</script>
</head>
<body class="bgr-red-gradient">
<!--[page html]-->
<div id="wrapper">

  <!--[header]-->
  <div id="header_main" class="nobackground">
  <div class="feedback"><a href="javascript:void(0);" class="feedback_popup"><img src="<?php echo $this->config->item('webappassets');?>images/feedback.png?v=6-20-13" /></a></div>
   <h2>
		<?php

			if($this->session->userdata('member_status')=='inactive'){
				echo 'A confirmation message was sent to '.$this->session->userdata('member_email_address').'. Please activate your account by clicking the link in the email sent to you. <a style="color:#FF0000 !important; text-decoration:underline;" href="javascript:void(0);" class="resend_confirmation">Click here to resend confirmation</a>';
			}
		?>
	</h2>
    <div class="container">
      <!--[logo]-->
      <div id="logo-dashboard">
        <h1><a href="<?php echo  site_url("newsletter/campaign");?>"><span>RED Cappi</span></a></h1>
      </div>
      <!--[/logo]-->
	   <?php if(($this->uri->segment(3)!='first_time_user_notification')&&($this->uri->segment(2)!='user_account_inactive_message')){ ?>
       <div class="right-info">
        <div class="clear">
			<?php if($previous_page_url==""){ ?>
			<a href="javascript: self.history.back();" title="" class="fr go_back_link" ><span>Go&nbsp;Back</span></a>
		<?php }else{ ?>
			<a href="<?php echo $previous_page_url; ?>" title="" class="fr go_back_link" ><span>Go&nbsp;Back</span></a>
		<?php } ?>
		</div>
      </div>
	  <?php } ?>

    </div>
  </div>
  <!--[/header]-->
