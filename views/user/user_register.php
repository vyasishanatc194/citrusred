    <?php
    $this->load->helper('url');
    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if (strpos($url, $this->config->item('redirect_url')) > 0) {
        redirect('https://getredcappi.com/sign-up-page/');
    }
    ?>
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
  </head>
  <body>
    <!--[page html]-->
    <script type="text/javascript">
      // Popup login form
      $(document).ready(function(){
        var checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.name = "verifycheckbox";
        checkbox.value= "1";
        var span = document.getElementById("terms_condition");
        span.appendChild(checkbox);
        checkbox.checked = false;
      });
    </script>
    <section role="main" class="main-container content-page">
      <div id="credential-container">
        <img class="logo" src="<?php echo $this->config->item('webappassets');?>/images/home-page-face.png?v=6-20-13" alt="redcappi" />
        <h2>Sign up to RedCappi</h2>
        <div id="credential" class="content key-points">
          <strong class="free-no-hassel">Create your totally free account.<br />No credit card required!</strong>
          <?php if(validation_errors()) echo '<div  class="msg info">'.validation_errors().'</div>'; ?>
          <?php
            // display all messages
            if (is_array($messages)):
              echo '<div id="messages" class="msg info">';
              foreach ($messages as $type => $msgs):
                  foreach ($msgs as $message):
                    echo ('<span class="' .  $type .' error">' . $message . '</span>');
                  endforeach;
              endforeach;
              echo '</div>';
            endif;
          ?>
		  <form action='user/register/' id="signup_form" method="post" onsubmit="">
          <?php //echo form_open('user/register/'); ?>
            <label for="email">Email</label>
            <?php echo form_input(array('name'=>'email','id'=>'email','maxlength'=>50,'size'=>50,'value'=>set_value('email'),'autocorrect'=>'off','autocapitalize'=>'off','title'=>"We'll send you a confirmation")) ; ?>

            <label for="username">Username</label>
            <?php echo form_input(array('name'=>'username','id'=>'username','maxlength'=>50,'size'=>50,'value'=>set_value('username'),'autocorrect'=>'off','autocapitalize'=>'off','title'=>"Pick a unique name on RedCappi.")) ; ?>

            <label for="password">Password</label>
            <input type="password" name="password" value="" maxlength="50" size="50"  title="6 characters or more (be tricky!)" />

            <p class="remember" style="margin: 13px 0 10px;">
              <span class="terms_condition" id="terms_condition"></span> I agree to all RedCappi <a href="<?php echo base_url().'terms'; ?>"  class="terms"> Terms of Service</a>
            </p>

            <?php echo form_submit(array('name' => 'btnRegister', 'id' => 'btnRegister', 'class' => 'btn','content' => 'Create My Account','title'=>'Create My Account'), 'Create My Account'); ?>
          </form>
        </div>
      </div>
    </section>
<script type="text/javascript">  
  _kmq.push(['trackSubmit', 'signup_form', 'Free user registration form submitted']);
</script>	
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
<!-- Google Code for Jigs Signup Goal Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 939207155;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "ZLdiCKHr1WIQ89PsvwM";
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/939207155/?label=ZLdiCKHr1WIQ89PsvwM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
  </body>
</html>
