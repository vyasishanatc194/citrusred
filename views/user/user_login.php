<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <?php
    if(isset($seo_array)){
      if(count($seo_array)>0){
        echo '<meta name="description" content="'.$seo_array[0]['description'].'" />';
        echo '<meta name="keywords" content="'.$seo_array[0]['keyword'].'" />';
      }
    }
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="robots" content="index, follow" />
    <meta name="GOOGLEBOT" content="INDEX, FOLLOW" />
    <meta name="copyright" content="<?php echo SYSTEM_DOMAIN_NAME ;?>. All Rights Reserved." />
    <meta name="publisher" content="<?php echo SYSTEM_DOMAIN_NAME ;?>" />
    <meta name="Author" content="email marketing best email marketing software- <?php echo base_url();?>" />
    <meta name="best email marketing company" content="best email marketing company, we offer email marketing services at very affordable and cheap price." />
    <title>
      <?php
        $title_text=true;
        if(isset($seo_array)){
          if(count($seo_array)>0){
            if(isset($seo_array[0]['title'])){
              echo $seo_array[0]['title'];
              $title_text=false;
            }
          }
        }
        if($title_text){
          echo $title;
        }
       ?>
    </title>
    <?php $ci =& get_instance();?>
    <link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css/style.css?v=6-20-13"  media="screen" />
    <!--[main script] -->
    <script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-latest.js?v=6-20-13"></script>
    <!--[/main script] -->
    <!--[if lt IE 9]>
    <script src="<?php echo $this->config->item('webappassets');?>js/html5shiv-printshiv.js?v=6-20-13"></script>
    <![endif]-->
  </head>
  <body style="background: #fff !important;">
    <script type="text/javascript">
    function ajaxLogin(frm){
       
	  jQuery('.msg').html("<img border='0'  style='margin:0;' src='<?php echo $this->config->item('webappassets');?>images/ajax_loading.gif' />");
	  // Collect login form variables
      // var block_data+="action=save&"+'&member_username='+frm.member_username.value+'&member_password='+frm.member_password.value;
      
      jQuery.ajax({
        url: "<?php echo base_url() ?>user/login/",
        type:"POST",
		//dataType   : 'json',
		//contentType: 'application/json; charset=UTF-8',
        data:jQuery("form[name=signin]").serialize(),
        success: function(data) { 
          // if get error in login then display error
			if(data=="error"){
				jQuery('#pwd').val('');
				jQuery('.msg').html("Incorrect Username or Password.");
				jQuery('.msg').show();
			}else if(data=="locked"){
				jQuery('#pwd').val('');
				jQuery('.msg').html("Your account has been temporarily blocked for too many failed login attempts. Please try again later.");
				jQuery('.msg').show();
			}else if(data=="inactive"){
				jQuery('.msg').html("Login success. Redirecting...");
				jQuery('.msg').show();
				document.location="<?php echo base_url() ?>user/user_account_inactive_message";
			}else if(data=="success"){
				jQuery('.msg').html("Login success. Redirecting...");
				jQuery('.msg').show();
				parent.document.location="<?php echo base_url() ?>newsletter/campaign";
			}
        } 
      });
      return false;
    }
    /**
    *  function for forgot password using ajax
    */
    function ajaxForgotPassword(frm){
      var block_data="";
      // Collect forgot password form variables
      block_data+="action=submit&"+'&email_address='+frm.email_address.value;
      jQuery.ajax({
        url: "<?php echo base_url() ?>user/forgot_password/",
        type:"POST",
        data:block_data,
        success: function(data) {
          // if get error in forgot password then display error
          if(data=="error"){
            jQuery(".content").css("min-height","20px");
            if(frm.email_address.value==""){
              jQuery('.fr_msg').html('The Email field is required.');
            }else{
              jQuery('.fr_msg').html('Your email is not registered.');
            }
            jQuery('.fr_msg').show();
          }else if(data=="success"){
            frm.email_address.value ='';
            // if success in forgot password then send mail
            jQuery(".content").css("min-height","20px");
            jQuery('.fr_msg').html('An email has been sent to you containing a link to reset your password.');
            jQuery('.fr_msg').show();
          }
        }
      });
      return false;
    }

    /**
    *  function for show login popup window
    */
    function showForgotPwd(){
      $('#signin').hide();
      $('#forgotPwdBlock').show();
      $('.heading').html("Forgot Password");
      jQuery('.msg').hide();
      jQuery('.fr_msg').hide();
    }
    /**
    *  function for show forgot password popup window
    */
    function showLoginBlock(){
      $('#forgotPwdBlock').hide();
      $('#signin').show();
      $('.heading').html("Login to RedCappi");
      jQuery('.msg').hide();
      jQuery('.fr_msg').hide();
    }
    </script>
    <section role="main" class="main-container content-page login_content">
      <div id="credential-container">
     <!--   <img class="logo" src="<?php echo $this->config->item('webappassets');?>/images/home-page-face.png?v=6-20-13" alt="redcappi" /> -->
        <h2 class="heading login_heading">Log into RedCappi</h2>
        <div id="credential" class="content key-points">
          <form name="signin" id="signin" method="post" onsubmit="ajaxLogin(this); return(false);">
            <p class="msg" style="color:red;font-weight:bold;display:inline-block;"></p>
            <!--<label for="username"><strong>Username Or Email</strong> </label>-->
            <input type="text" tabindex="4" title="username" id="log" name="member_username" autocorrect="off" autocapitalize="off" placeholder="Username Or Email"/>
            <!--<label for="password"><strong>Password</strong></label>-->
            <input type="password" tabindex="5" title="password" value="" id="pwd" name="member_password" autocorrect="off" autocapitalize="off" placeholder="Password"/>
            <a onclick="javascript: showForgotPwd();" href="javascript:void(0);" class="forgot">Forgot password?</a>
            <p>
              <input type="checkbox" tabindex="9" value="1" name="remember_login" id="remember" class="checkbox">
               <span style="vertical-align: middle; font-size: 15px; padding-left: 9%;">Remember me </span>
            </p>
            <input type="submit" name="btnLogin"  tabindex="10" value="Login" id="signin_submit" class="btn submit-input">
            <input type="hidden" value="save" name="action">
            <p class="new-account">
               Don't have an account? <a href="javascript:void(0);" onclick="javascript:parent.document.location='<?php echo base_url().'signup';?>'">Sign up now</a>.
            </p>
          </form>
          <form onsubmit="ajaxForgotPassword(this); return(false);" method="post" class="login-form" id="forgotPwdBlock" name="forgot" style="display: none">
            <p class="fr_msg" style="color:red;font-weight:bold;"></p>
            <label for="username"><strong>Registered Email Address</strong></label>
            <?php echo form_input(array('name'=>'email_address','id'=>'log','maxlength'=>50, 'autocorrect'=>'off','autocapitalize'=>'off','value'=>set_value('email_address'))); ?>
              <p class="remember">
                <?php
                  echo form_submit(array('name' => 'btnForgot', 'id' => 'signin_submit', 'class' => 'btn submit-input','content' => 'Login'), 'Reset Password');
                  echo form_hidden('action','submit');
                ?>
                <a href="javascript:void(0);" id="show_login_block" class="btn" onclick="javascript: showLoginBlock();">Cancel</a>
                <input name="amember_redirect_url" value="/pricing/" type="hidden">
              </p>
          </form>
        </div>
      </div>
    </section>
<script type="text/javascript">
adroll_adv_id = "TDYV2PQUMFCC5OH6LNPYFT";
adroll_pix_id = "6PLPRQD4P5EMVFHJG3UFLE";
(function () {
var oldonload = window.onload;
window.onload = function(){
   __adroll_loaded=true;
   var scr = document.createElement("script");
   var host = (("https:" == document.location.protocol) ? "https://s.adroll.com" : "http://a.adroll.com");
   scr.setAttribute('async', 'true');
   scr.type = "text/javascript";
   scr.src = host + "/j/roundtrip.js";
   ((document.getElementsByTagName('head') || [null])[0] ||
    document.getElementsByTagName('script')[0].parentNode).appendChild(scr);
   if(oldonload){oldonload()}};
}());
</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-25501252-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js?v=6-20-13';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
</html>
