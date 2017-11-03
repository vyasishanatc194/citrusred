<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=1100"/>
<title>Verification</title>
<link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico">
<?php echo link_tag('webappassets/css/inner_red.css?v=7-9-13'); ?>
<!--[if IE 7]>
  <?php echo link_tag('webappassets/css/font-awesome-ie7.min.css?v=6-20-13'); ?>
<![endif]-->
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-1.4.4.min.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.pack.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery.blockUI.js?v=6-20-13"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.css?v=6-20-13" media="screen" />
<!--[if lt IE 9]>
<script src="<?php echo $this->config->item('webappassets');?>js/html5shiv-printshiv.js?v=6-20-13"></script>
<![endif]-->

</head>
<body>

<!--[page html]-->
<div id="wrapper">
  <!--[header]-->
  <?php if($this->session->userdata('member_status')=='inactive'){?>
    <script type="text/javascript">

        $(".resend_confirmation").live('click',function(){
		$.blockUI({ message: '<h3 class="please-wait">Please wait...</h3>' });
          $.ajax({
            url: "<?php echo base_url() ?>user/user_confirmation_notification/<?php echo $this->session->userdata('member_id'); ?>/confirmation_msg",
            type:"POST",
            success: function(data) {
              if(data=="success"){
				$.fancybox('<p style=\"font-weight: 700;margin:25px auto;\">Please check your email.</p>',{ 'autoDimensions':false,'height':'100','width':'300', 'centerOnScroll':true,'modal':false});
				 $.unblockUI();
              }
            }
          });
        });
		 $(".feedback_popup").live('click',function(){		 
			$.ajax({
			  type  : "POST",
			  cache : false,
			  url   : '<?php echo base_url() ?>feedback/create',
			  data  : $(this).serializeArray(),
			  success: function(data) { 
				if(data) {
				  $.fancybox(data,{'autoDimensions':true,'height':'auto','width':'560','centerOnScroll':true});
				}
			  }
			});
			return false;
		  });
    </script>
  <?php } ?>


  <div id="header-main">
    <div id="header-menu">
      <a href="<?php echo site_url("newsletter/campaign");?>" id="logo" title="RedCappi"></a>
    </div>
  </div>
  <!--[header]-->

  <!--[body]-->
<div id="body-dashborad">
  <div id="first-time-sender" class="container">
  <h1>Check your email to verify your FREE account!</h1>
    <div>

      <p>
        <img src="<?php echo base_url();?>webappassets/images/home-page-face.png?v=6-20-13" width="150" alt="logo" title="logo"/>
      </p>
		<p style="font-size:20px;">Thanks for signing up for a RedCappi account :-)<br/>
		In order to complete the sign up process and create your first email you need to complete 2 really simple steps:<br/>
		<b>Step 1</b> - You need to check your email (<?php echo $this->session->userdata('member_email_address');?>) for a message from RedCappi
<br/>
          <b>Step 2</b> - Click the "Click to Activate" button in that email
					<br/>
					<a style="width:325px;margin:45px auto 0px auto" href="javascript:void(0);" class="btn cancel list resend_confirmation">Click here to resend confirmation</a>
		<span style="border:0px solid #ff0000;display:block;width:200px;margin:0px auto 0;font-size:12px;"> Having trouble? <a href="javascript:void(0);"  style="color:blue;text-decoration:underline;font-size:12px;" class="feedback_popup">Contact Support</a> </span>
		
		<span style="border:0px solid #ff0000;display:block;width:200px;margin:40px auto 0;font-size:12px;"> <a href="<?php echo base_url();?>user/logout"  style="color:blue;text-decoration:underline;font-size:12px;">Sign in as a different user</a> </span>
		</p>
		</div>
  </div>
</div>
<p>&nbsp;</p>
<p>&nbsp;</p>
  <!--[/body]-->
 
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-25501252-1', 'auto');
  ga('send', 'pageview');

</script> 
<script type="text/javascript">
      var capterra_vkey = '260b8ac9c4d30e276a9f54e0c065a676',
      capterra_vid = '2104581',
      capterra_prefix = (('https:' == document.location.protocol) ? 'https://ct.capterra.com' : 'http://ct.capterra.com');

      (function() {
        var ct = document.createElement('script'); ct.type = 'text/javascript'; ct.async = true;
        ct.src = capterra_prefix + '/capterra_tracker.js?vid=' + capterra_vid + '&vkey=' + capterra_vkey;
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ct, s);
      })();
</script>
<script type="text/javascript">(function(){
  var r = document.referrer;
  var h = window.location.href;
  var p = '100';
  var e = ''; // External ID (optional)
  var listing_id = '103243';
  var a = document.createElement('script');
  a.type = 'text/javascript';
  a.async = true;
  a.src = 'https://www.getapp.com/conversion/' + encodeURIComponent(listing_id) +
    '/r.js?p=' + encodeURIComponent(p) + '&h=' + encodeURIComponent(h) +
    '&r=' + encodeURIComponent(r) + '&e=' + encodeURIComponent(e);
  var s = document.getElementsByTagName('script')[0];
  s.parentNode.insertBefore(a, s);
})();</script>
<!-- Google Code for Lead Form Completed Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 939207155;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "oo2HCJy-7WUQ89PsvwM";
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/939207155/?label=oo2HCJy-7WUQ89PsvwM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
</body>
</html>