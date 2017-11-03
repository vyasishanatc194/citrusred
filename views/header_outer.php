<?php if(MAINTENANCE_MODE_FOR_ALL_USERS == 'yes'){ redirect('/site_under_maintenance/');exit;} ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
  

    <?php
	if(isset($blog_description)){
		echo "<meta name=\"description\" content=\"{$blog_description}\" />\n";
		echo "<meta name=\"keywords\" content=\"{$meta_keywords}\" />\n";
	}elseif(isset($seo_array)){
      if(count($seo_array)>0){
        echo '<meta name="description" content="'.$seo_array[0]['description'].'" />';
        echo '<meta name="keywords" content="'.$seo_array[0]['keyword'].'" />';
      }
    }
	?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Language" content="en-US" />
	<meta name="msvalidate.01" content="F20291881CA4263B43E45923C943F3C0" />
    <meta name="Robots" content="index, follow" />
    <meta name="GoogleBot" content="index, follow" />
    <meta name="Publisher" content="RedCappi" />
    <meta name="Author" content="RedCappi" />
    <meta name="Copyright" content="RedCappi" />
    <meta name="viewport" content="width=1153"/>
	<?php if(isset($og_meta_tags)){ echo $og_meta_tags; }else{ ?>
    <meta property="og:title" content="RedCappi Email Marketing" />
    <meta property="og:type" content="website" />
    <meta property="og:description" content="RedCappi makes it easy to send email newsletters, grow your subscribers and manage your email list. Sign up for a completely free email marketing account!"/>
    <meta property="og:url" content="http://redcappi.com" />
    <meta property="og:image" content="http://www.redcappi.com/webappassets/images/redesign/logo.png?v=9-30-13"/>
	<?php }?>
    <meta property="og:site_name" content="RedCappi" />
    <meta property="fb:page_id" content="134820093260718" />
    <meta property="fb:admin" content="134820093260718" />
    <link href="https://plus.google.com/104124542722954210936" rel="publisher" />
    <link rel="alternate" type="application/atom+xml" title="RedCappi Blog" href="/feed/" />
    <link rel="search" type="application/opensearchdescription+xml" title="RedCappi Blog Search" href="/blog/result" />
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
    <?php $ci =& get_instance();?>
    <link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css/style.css?v=11-10-13"  media="screen" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css/modal.css?v=11-10-13"  media="screen" />
    <!--[if IE 7]>
      <?php echo link_tag('webappassets/css/font-awesome-ie7.min.css?v=6-20-13'); ?>
    <![endif]-->
    <!--[main script] -->
    <script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-latest.js?v=6-20-13"></script>
    <script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/outer-helper.js"></script>
    <script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/ouibounce.min.js"></script>
    <!--[/main script] -->
    <!--[if lt IE 9]>
    <script src="<?php echo $this->config->item('webappassets');?>js/html5shiv-printshiv.js?v=6-20-13"></script>
    <script type="text/javascript">
      $(function() {
        $(".sign-up-bar input[type='text'],.sign-up-bar input[type='password']").each(function() {
          $(this).val($(this).attr("placeholder")).addClass("placeholder");
          $(this).bind('focus',function(){
            if($(this).val()==$(this).attr("placeholder")){
              $(this).val('').removeClass("placeholder");
            }
          });
          $(this).bind('blur',function(){
            if($(this).val()==""){
              var $this = $(this);
              $(this).val($this.attr("placeholder")).addClass("placeholder");
            }
          });
        });
      });
    <![endif]-->
    </script> 
	<!-- Hotjar Tracking Code for www.redcappi.com -->
	<script>
		(function(f,b){
			var c;
			f.hj=f.hj||function(){(f.hj.q=f.hj.q||[]).push(arguments)};
			f._hjSettings={hjid:31343, hjsv:4};
			c=b.createElement("script");c.async=1;
			c.src="//static.hotjar.com/c/hotjar-"+f._hjSettings.hjid+".js?sv="+f._hjSettings.hjsv;
			b.getElementsByTagName("head")[0].appendChild(c); 
		})(window,document);
	</script>
	<!-- https://www.inspectlet.com/docs#installinginspectlet -->
	<!-- Begin Inspectlet Embed Code -->
<script type="text/javascript" id="inspectletjs">
window.__insp = window.__insp || [];
__insp.push(['wid', 872425550]);
(function() {
function __ldinsp(){var insp = document.createElement('script'); insp.type = 'text/javascript'; insp.async = true; insp.id = "inspsync"; insp.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://cdn.inspectlet.com/inspectlet.js'; var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(insp, x); };
document.readyState != "complete" ? (window.attachEvent ? window.attachEvent('onload', __ldinsp) : window.addEventListener('load', __ldinsp, false)) : __ldinsp();

})();
</script>
<!-- End Inspectlet Embed Code -->
  </head>
  <body class="<?php echo $this->uri->segment(1) ?>" itemscope itemtype="http://schema.org/WebApplication">
<!-- Qualaroo for redcappi.com -->
<!-- Paste this code right after the <body> tag on every page of your site. -->
<script type="text/javascript">
  var _kiq = _kiq || [];
  (function(){
    setTimeout(function(){
    var d = document, f = d.getElementsByTagName('script')[0], s = d.createElement('script'); s.type = 'text/javascript';
    s.async = true; s.src = '//s3.amazonaws.com/ki.js/57996/cUO.js'; f.parentNode.insertBefore(s, f);
    }, 1);
  })();
</script>  
  <!--[page html]-->
    <header>
      <div id="header-content">
        <a href="<?php echo  base_url();?>" title="redcappi" id="logo"><img src="<?php echo $this->config->item('webappassets');?>images/redesign/logo.png?v=6-20-13" alt="RedCappi" <?php if($this->uri->segment(1) == "") {?>class="animated bounceInDown"<?php } ?> /><span itemprop="author">RedCappi</span></a>
        <nav>
          <ul>
            <li><a href="<?php echo  base_url()."email-marketing-features";?>" <?php if($this->uri->segment(1)=='email-marketing-features') {?> class="active" <?php } ?>>Features</a></li>
            <li><a href="<?php echo  base_url()."pricing";?>" <?php if($this->uri->segment(1)=='pricing') {?> class="active" <?php } ?>>Pricing</a></li>
            <li><a href="<?php echo base_url()."blog/";?>" <?php if($this->uri->segment(1)=='blog') {?> class="active" <?php } ?>>Blog</a></li>
            <li style="float:right"><a href="/signup" id="sign-up" class="btn">Get Started Free <i class="icon-circle-arrow-right"></i><span>No credit card required!</span></a></li>
            <li style="float:right"><a href="/user/login" id="login" <?php if($this->uri->segment(2)=='login') {?> class="active" <?php } ?>>Login</a></li>
          </ul>
        </nav>
      </div>
    </header>
  <!--[/header]-->
    <!-- Ouibounce Modal -->    
    <div id="ouibounce-modal">
      <div class="underlay"></div>
      <div class="modal">   

        <div class="modal-body">
            <?php $wufoo_url = 'zj94f0r0wc297a'; ?>
         <iframe height="260" allowTransparency="true" frameborder="0" scrolling="no" style="width:100%;border:none"  src="https://redcappi.wufoo.com/embed/<?php echo $wufoo_url?>/"><a href="https://redcappi.wufoo.com/forms/<?php echo $wufoo_url?>/">Fill out my form!</a></iframe> 
        </div>

        <div class="modal-footer">
          <p>no thanks</p>
        </div>
      </div>
    </div>
	<!-- Ouibounce Modal Ends -->