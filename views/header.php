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
<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window,document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
 fbq('init', '635221016664507'); 
fbq('track', 'PageView');
</script>
<noscript>
 <img height="1" width="1" 
src="https://www.facebook.com/tr?id=635221016664507&ev=PageView
&noscript=1"/>
</noscript>
<!-- End Facebook Pixel Code -->
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
  <div class="upper_header_wrapeer">
	<div class="upper_header">
	<a href="<?php echo site_url("newsletter/campaign");?>" id="logo" title="RedCappi"></a>
	<ul class="sub-options">
		<?php if($this->session->userdata('member_staff') == 0){?>      
		  <li><a href="<?php echo  site_url("upgrade_package_cim/index");?>">Upgrade Now</a></li>
		 <?php } ?> 
		  <li><a href="<?php echo site_url("user/logout");?>">Log Out</a></li>
	</ul>	
  </div>
  </div>
  <div id="header-main">
    <div id="header-menu">
      <ul class="main-options">
	  
		<?php 
		if($this->session->userdata('member_staff') == 0 or $this->session->userdata('manage_campaigns') > 0){ ?>
        <li>
			<a href="<?php echo site_url('newsletter/campaign');?>" id="campaigns-header" class="nav_image menu_option_link <?php if($this->uri->segment(2) == 'campaign'){
				echo'active';
				}?>">
				<div class="menu_option_div">
					<img class="image_normal" src="<?php echo $this->config->item('webappassets');?>images/new_png_design/Menu-Icon-Campaigns.png?v=6-20-13" alt="campaigns"/>
					<img class="image_active" src="<?php echo $this->config->item('webappassets');?>images/new_png_design/Menu-Icon-Campaigns-RED.png?v=6-20-13" alt="campaigns"/>
				</div>
				Campaigns
				</a>
		</li>
		<?php 
		} 
		if($this->session->userdata('member_staff') == 0 or $this->session->userdata('manage_contacts') > 0){?>
         <li>
			<a href="<?php echo site_url('newsletter/contacts');?>" id="contacts-header"  class="nav_image menu_option_link <?php if($this->uri->segment(2) == 'contacts')echo'active';?>">
			<div class="menu_option_div">
				<img class="image_normal" src="<?php echo $this->config->item('webappassets');?>images/new_png_design/Menu-Icon-Contacts.png?v=6-20-13" alt="Contacts"/>
				<img class="image_active" src="<?php echo $this->config->item('webappassets');?>images/new_png_design/Menu-Icon-Contacts-RED.png?v=6-20-13" alt="campaigns"/>
			</div>
			Contacts
			</a>
		</li>
		<?php 
		} 
		if($this->session->userdata('member_staff') == 0 or $this->session->userdata('manage_stats') > 0){?>
       <li>
			<a href="<?php echo site_url('newsletter/emailreport/display');?>" id="stats-header"  class="nav_image menu_option_link <?php if($this->uri->segment(2) == 'emailreport')echo"active";?>">
			<div class="menu_option_div">
				<img class="image_normal" src="<?php echo $this->config->item('webappassets');?>images/new_png_design/Menu-Icon-Stats.png?v=6-20-13" alt="Stats"/>
				<img class="image_active" src="<?php echo $this->config->item('webappassets');?>images/new_png_design/Menu-Icon-Stats-RED.png?v=6-20-13" alt="Stats"/>
			</div>
			Stats
			</a>
		</li>
		<?php 
		} 
		if($this->session->userdata('member_staff') == 0 or $this->session->userdata('manage_autoresponders') > 0){?>
        <li>
			<a href="javascript:void(0);" data-href="<?php echo site_url('newsletter/autoresponder/display');?>" id="autoresponders-header" class="nav_image creditmember menu_option_link <?php if($this->uri->segment(2) == 'autoresponder'){echo"active";}?>">
				<div class="menu_option_div">
					<img class="image_normal" src="<?php echo $this->config->item('webappassets');?>images/new_png_design/Menu-Icon-Autoresponders.png?v=6-20-13" alt="Autoresponders"/>
					<img class="image_active" src="<?php echo $this->config->item('webappassets');?>images/new_png_design/Menu-Icon-Autoresponders-RED.png?v=6-20-13" alt="Autoresponders"/>
				</div>
				Autoresponders
				</a>
			</li>
		<?php 
		} 
		
		if($this->session->userdata('member_staff') == 0 or $this->session->userdata('manage_signupforms') > 0){?>
        <li>
			<a href="javascript:void(0);" data-href="<?php echo site_url('newsletter/signup');?>"id="signup-form-header" class="nav_image creditmember menu_option_link <?php if($this->uri->segment(2) == 'signup')echo'active';?>">
				<div class="menu_option_div">	
				<img class="image_normal" src="<?php echo $this->config->item('webappassets');?>images/new_png_design/Menu-Icon-SignUpForms.png?v=6-20-13" alt="Signup Forms"/>
				<img class="image_active" src="<?php echo $this->config->item('webappassets');?>images/new_png_design/Menu-Icon-SignUpForms-RED.png?v=6-20-13" alt="Signup Forms"/>
				</div>
			Signup Forms
			</a>
		</li>
		<?php 
		} 
		
		if($this->session->userdata('member_staff') == 0 or $this->session->userdata('manage_extra') > 0){?>
       <li>
		<a href="<?php echo site_url("dashboard_extra/dashboard_extra_list");?>" id="extras-header" class="nav_image menu_option_link <?php if($this->uri->segment(1) == 'dashboard_extra')
		{echo "active";}?>">
			<div class="menu_option_div">
			<img class="image_normal" src="<?php echo $this->config->item('webappassets');?>images/new_png_design/Menu-Icon-Extras.png?v=6-20-13" alt="Extras"/>
			<img class="image_active" src="<?php echo $this->config->item('webappassets');?>images/new_png_design/Menu-Icon-Extra-RED.png?v=6-20-13" alt="Extras"/>
			</div>
			<span>Extras</span>
		</a>
		</li>
		<?php 
		} 
		?>
         <li>
			<a href="<?php echo site_url("account/index");?>" id="account-header" class="nav_image menu_option_link <?php if($this->uri->segment(1) == 'account' or $this->uri->segment(2) == 'change_password')echo"active";?>">
				<div class="menu_option_div">
				<img class="image_normal" src="<?php echo $this->config->item('webappassets');?>images/new_png_design/Menu-Icon-Account.png?v=6-20-13" alt="Account"/>
				<img class="image_active" src="<?php echo $this->config->item('webappassets');?>images/new_png_design/Menu-Icon-Account-RED.png?v=6-20-13" alt="Account"/>
				</div>
				<span>Account</span>
			</a>
		</li>
        <li>
			<a href="<?php echo site_url("support/index");?>" target="_blank" id="support-header" class="nav_image menu_option_link <?php if($this->uri->segment(1) == 'support')echo'active';?>">
				<div class="menu_option_div">
				<img class="image_normal" src="<?php echo $this->config->item('webappassets');?>images/new_png_design/Menu-Icon-Support.png?v=6-20-13" alt="Support"/>
				<img class="image_active" src="<?php echo $this->config->item('webappassets');?>images/new_png_design/Menu-Icon-Support-RED.png?v=6-20-13" alt="Support"/>
				</div>
				Support
			</a> 
		</li>
      </ul>
    </div>
    
  </div>
