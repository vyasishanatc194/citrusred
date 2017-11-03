<!--[footer]-->
<?php $ci =& get_instance(); ?>
 <?php if($ci->session->userdata('member_id')!='') { ?>
 <div class="footer_wrapper">
<div class="footer-bar">
<div class="links_footer">
  <ul class="footer-links-dashboard">
	
    <li><a href="<?php echo  site_url("support/index");?>">Support</a></li>
    <li><a href="<?php echo  base_url()."contact";?>">Contact Us</a></li>
    <li><a href="<?php echo base_url()."blog/";?>">Blog</a></li>
    <li><a href="<?php echo  base_url()."terms";?>">T&amp;C</a></li>
    <li><a href="<?php echo  base_url()."privacy";?>">Privacy Statement</a></li>
    <li class="nobackground"><a href="<?php echo  base_url()."anti-spam";?>">Anti-Spam Policy</a></li>
  </ul>
  </div>
  <div class="copy_right">
	<p id="footer-legal">&copy; <?php echo date('Y'); ?> RedCappi LLC, All Rights Reserved.</p>
  </div>
  <div class="social_links_footer">
  <ul class="socal-icon-dashboard">
    <li><a href="http://www.facebook.com/redcappi" target="_blank"> <img align="absmiddle" src="<?php echo $this->config->item('webappassets');?>images/facebook.png?v=6-20-13" alt=""> </a></li>
    <li><a href="http://twitter.com/redcappi" target="_blank"> <img align="absmiddle" src="<?php echo $this->config->item('webappassets');?>images/twitter-icon.png?v=6-20-13" alt=""> </a></li>
    <li>
      <?php
        if(trim($this->uri->segment(1))=='blog_category'){
          $rss_url=base_url()."feed/index/".$this->uri->segment(4);
        }else{
          $rss_url=base_url()."feed/";
        }
      ?>
      <a href="<?php echo $rss_url ;?>" target="_blank"> <img align="absmiddle" src="<?php echo $this->config->item('webappassets');?>images/rss.png?v=6-20-13" alt=""> </a>
    </li>
  </ul>
  </div>

</div>
</div>
 <?php } else{ ?>
<div id="footer">
  <div class="footer-bar">
    <div class="footerlinks">
      <div class="footerlinks-inner-div">
        <h5>Company</h5>
        <ul class="footerlinks-navi">
          <li><a href="<?php echo  site_url("about_us/index");?>">About Us</a></li>
          <li><a href="<?php echo  site_url("contactus/index");?>">Contact Us</a></li>
          <li><a href="<?php echo base_url()."blog/";?>">Blog</a></li>
		  
        </ul>
      </div>
      <div class="footerlinks-inner-div">
        <h5>RedCappi</h5>
        <ul class="footerlinks-navi">
          <li><a href="<?php echo  site_url("feature/index");?>">Features</a></li>
          <li><a href="<?php echo  site_url("pricing/index");?>">Pricing</a></li>
        </ul>
      </div>
      <div class="footerlinks-inner-div">
        <h5>Legal</h5>
        <ul class="footerlinks-navi">
          <li><a href="<?php echo base_url().'terms';?>">Terms and Conditions</a></li>
          <li><a href="<?php echo base_url().'privacy';?>">Privacy Statement</a></li>
          <li><a href="<?php echo base_url().'anti_policy';?>">Anti-Spam Policy</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-right-area ">
      <p>© <?php echo date('Y'); ?> RedCappi LLC, All Rights Reserved.</p>
      <ul class="socal-icon">
        <li>Follow Us</li>
        <li><a href="http://www.facebook.com/redcappi"> <img align="absmiddle" src="<?php echo $this->config->item('webappassets');?>images/facebook.png?v=6-20-13" alt=""> </a></li>
        <li><a href="http://www.facebook.com/redcappi"> <img align="absmiddle" src="<?php echo $this->config->item('webappassets');?>images/twitter-icon.png?v=6-20-13" alt=""> </a></li>
        <li><a href="http://www.facebook.com/redcappi"> <img align="absmiddle" src="<?php echo $this->config->item('webappassets');?>images/rss.png?v=6-20-13" alt=""> </a></li>
      </ul>
    </div>
  </div>
</div>
<?php } ?>
  <!--[/footer]-->
</div>
<!--[/page html]-->
<?php
if($isFirstTimeUser != ''){
echo $isFirstTimeUser;
?>
<!-- Google Code for Free Account Signup Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 1014822107;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "6nsaCIuD6FYQ2-nz4wM";
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/1014822107/?label=6nsaCIuD6FYQ2-nz4wM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
<?php
}
?>
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
<!-- Start of Async HubSpot Analytics Code -->
  <script type="text/javascript">
    (function(d,s,i,r) {
      if (d.getElementById(i)){return;}
      var n=d.createElement(s),e=d.getElementsByTagName(s)[0];
      n.id=i;n.src='//js.hs-analytics.net/analytics/'+(Math.ceil(new Date()/r)*r)+'/2385837.js';
      e.parentNode.insertBefore(n, e);
    })(document,"script","hs-analytics",300000);
  </script>
<!-- End of Async HubSpot Analytics Code -->
<script type="text/javascript"> 
jQuery(".creditmember").live('click',function(){
	var href = jQuery(this).data('href');
	jQuery.ajax({
		url: "<?php echo base_url() ?>ajax/checkMamberPackage/<?php echo $this->session->userdata('member_id'); ?>",
		type:"POST",
		success: function(data) {
			
			var contact = JSON.parse(data);
			if(contact.STATUS == 'Success'){
				//alert('here');
				jQuery.fancybox({
					'content' : "<div style='width:400px;height:160px;'><p>This feature is only available for monthly or annual subscribers. To use these features, please select a monthly or yearly plan under Account -> My Plan.</p><div class='btn-group'><span class='btn confirm' onclick='$.fancybox.close();'>Close</span></div></div>"
				}); 
			}else{
				//alert('here12');
				window.location = href;
				
			}
      
		}
	});
  
});
</script>
</body>
</html>
