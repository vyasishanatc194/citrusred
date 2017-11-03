
<?php if(MAINTENANCE_MODE_FOR_LOGGED_USERS == 'yes'){ redirect('/site_under_maintenance/');exit; } 

if($this->session->userdata('member_staff') > 0){
	if($this->uri->segment(2) == 'campaign' and $this->session->userdata('manage_campaigns') == 0){
		redirect('newsletter/contacts');exit;
	}elseif($this->uri->segment(2) == 'contacts' and $this->session->userdata('manage_contacts') == 0){
		redirect('newsletter/emailreport/display');exit;
	}elseif($this->uri->segment(2) == 'emailreport' and $this->session->userdata('manage_stats') == 0){
		redirect('newsletter/autoresponder/display');exit;
	}elseif($this->uri->segment(2) == 'autoresponder' and $this->session->userdata('manage_autoresponders') == 0){
		redirect('newsletter/signup');exit;
	}elseif($this->uri->segment(2) == 'signup' and $this->session->userdata('manage_signupforms') == 0){
		redirect('dashboard_extra/dashboard_extra_list');exit;
	}elseif($this->uri->segment(1) == 'dashboard_extra' and $this->session->userdata('manage_extra') == 0){
		redirect('user/change_password/');exit;
	}elseif($this->uri->segment(1) == 'account'){
		redirect('user/change_password/');exit;
	}	
	
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=1100"/>
<title><?php echo $title; ?></title>
	<script type="text/javascript">var _kmq = _kmq || [];
	var _kmk = _kmk || '0a3afcfb8bd28bda7d820a02efc3bf70dbd06ea2';
	function _kms(u){
	  setTimeout(function(){
		var d = document, f = d.getElementsByTagName('script')[0],
		s = d.createElement('script');
		s.type = 'text/javascript'; s.async = true; s.src = u;
		f.parentNode.insertBefore(s, f);
	  }, 1);
	}
	_kms('//i.kissmetrics.com/i.js');
	_kms('//doug1izaerwt3.cloudfront.net/' + _kmk + '.1.js');
	</script>
<?php $ci =& get_instance(); ?>
<link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico">
<?php echo link_tag('webappassets/css/inner_red.css?v=20160125'); ?>
<!--[if IE 7]>
  <?php echo link_tag('webappassets/css/font-awesome-ie7.min.css?v=6-20-13'); ?>
<![endif]-->
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-1.4.4.min.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.pack.js?v=6-20-13"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.css?v=6-20-13" media="screen" />
<script type="text/javascript" src ="<?php echo $this->config->item('webappassets');?>js/helper.js?v=6-20-13"></script>
<!--[if lt IE 9]>
<script src="<?php echo $this->config->item('webappassets');?>js/html5shiv-printshiv.js?v=6-20-13"></script>
<![endif]-->
<script type="text/javascript" src="https://s3.amazonaws.com/assets.freshdesk.com/widget/freshwidget.js"></script>
<script type="text/javascript">
	/*FreshWidget.init("", {"queryString": "&widgetType=popup&searchArea=no&helpdesk_ticket[custom_field][member_name]=<?php echo $this->session->userdata("member_username");?>", "utf8": "?", "widgetType": "popup", "buttonType": "text", "buttonText": "Support", "buttonColor": "black", "buttonBg": "#ffffff", "alignment": "4", "offset": "235px", "formHeight": "500px", "url": "https://redcappi.freshdesk.com"} );*/
</script>
<script type="text/javascript">
$(function() {
  $.ajax({
    url: "<?php echo base_url() ?>user/get_message/",
    type:"POST",
    success: function(data) {
      if(data !='') {
        $(data).prependTo("body");
      }
    }
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
});
</script>
<?php if($campaign_data['upgrade_package']){?>
<script  type="text/javascript">
 $(function() {
  $.ajax({
    url: "<?php echo base_url() ?>user/get_message/",
    type:"POST",
    success: function(data) {
      if(data !='') {
        $('<h2 class="new-signup">'+data+'</h2>').prependTo("body");
      }
  });
});
</script>
<?php }?>
<script  type="text/javascript">
_kmq.push(['identify', '<?php echo $this->session->userdata("member_username");?>']);
</script>
	<!-- https://www.inspectlet.com/docs#installinginspectlet -->
	<!-- Begin Inspectlet Embed Code -->
<script type="text/javascript" id="inspectletjs">
window.__insp = window.__insp || [];
__insp.push(['wid', 872425550]);
__insp.push(['identify', '<?php echo $this->session->userdata("member_username");?>']);
(function() {
function __ldinsp(){var insp = document.createElement('script'); insp.type = 'text/javascript'; insp.async = true; insp.id = "inspsync"; insp.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://cdn.inspectlet.com/inspectlet.js'; var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(insp, x); };
document.readyState != "complete" ? (window.attachEvent ? window.attachEvent('onload', __ldinsp) : window.addEventListener('load', __ldinsp, false)) : __ldinsp();

})();
</script>
<!-- End Inspectlet Embed Code -->
</head>
<body>

<!--[page html]-->
<div id="wrapper">
  <!--[header]-->
  <?php if($this->session->userdata('member_status')=='inactive'){?>
    <script type="text/javascript">
      $(function() {
      // function for resend confirmation email
      var $resend = $(".resend_confirmation");
      if($resend) {
        $resend.live('click',function(){
          $.ajax({
            url: "<?php echo base_url() ?>user/user_confirmation_notification/<?php echo $this->session->userdata('member_id'); ?>/confirmation_msg",
            type:"POST",
            success: function(data) {
              //display success message
              if(data=="success"){
                $(".new-signup").html("Please check your email.").delay(3000).slideUp(300);
              }
            }
          });
        });
      }
      });
    </script>
  <?php } ?>

  <!-- div class="feedback"><a href="javascript:void(0);" class="feedback_popup"><img src="<?php echo $this->config->item('webappassets');?>images/feedback.png?v=6-20-13" /></a></div -->
  <div class="upper_header_wrapeer upper_header_login">
	<div class="upper_header">
	<a href="<?php echo site_url("newsletter/campaign");?>" id="logo" class="login_logo" title="RedCappi"></a>
  </div>
  </div>
