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
<script src="js/DD_belatedPNG_0.0.8a-min.js?v=6-20-13" type="text/javascript"></script>
<script type="text/javascript">
DD_belatedPNG.fix('#outer, #container, #header, a, img');
</script>
<![endif]-->
<!--[main script] -->
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-latest.js?v=6-20-13"></script>
<!--[/main script] -->
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.0.0.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.pack.js?v=6-20-13"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.css?v=6-20-13" media="screen" />
<script type="text/javascript">
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
</script>
<style>
	#ui-datepicker-div{display:none;}
	.bg-white{background:#fff;}
</style>
</head>
<body class="bgr-red-gradient">
<!--[page html]-->
<div id="wrapper">
<div class="feedback"><a href="<?php echo  site_url("feedback/create");?>" class="feedback_popup"><img src="<?php echo $this->config->item('webappassets');?>images/feedback.png?v=6-20-13" /></a></div>
 <h2 class="wrapper">
		<?php
			if($this->session->userdata('member_status')=='inactive'){
				echo 'A confirmation message was sent to '.$this->session->userdata('member_email_address').'. Please activate your account by clicking the link in the email sent to you. <a style="color:#FF0000 !important; text-decoration:underline;" href="javascript:void(0);" class="resend_confirmation">Click here to resend confirmation</a>';

			}
		?>
	</h2>
  <!--[header]-->

	<table width="50%" align="center" border="0" cellspacing="0" cellpadding="0">
	<tr><td colspan="2" align="left">
	<div id="header_main" class="nobackground">

    <div class="container_red">
      <!--[logo]-->
      <div id="logo-dashboard">
        <h1><a href="<?php echo  site_url("newsletter/campaign");?>"><span>RED Cappi</span></a></h1>

      </div>
	  <!--[/logo]-->

    </div>
  </div>
	  </td>
	  </tr>



	  <tr><td valign="top"  width="120">
	  <?php if($previous_page_url==""){ ?>
			<a href="javascript: self.history.back();" title="" class="fl go_back_link" ><span>Go&nbsp;Back</span></a>
		<?php }else{ ?>
			<a href="<?php echo $previous_page_url; ?>" title="" class="fl go_back_link" ><span>Go&nbsp;Back</span></a>
		<?php } ?>
		&nbsp;</td>
		<td align="left">

		<table width="720" border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td align="left" valign="top"><img src="<?php echo base_url();?>webappassets/images/tl.png?v=6-20-13" width="6" height="6" /></td>
		<td width="708" class="bg-white"></td>
		<td align="right" valign="top"><img src="<?php echo base_url();?>webappassets/images/tr.png?v=6-20-13" width="6" height="6" /></td>
		</tr>
		 <tr>
		<td colspan="3" class="bg-white">

  <!--[/header]-->
