<!--[footer]-->
<?php $ci =& get_instance(); ?>
 <?php if($ci->session->userdata('member_id')!='') { ?>
<div class="footer-bar">
  <ul class="footer-links-dashboard">
  <li id="footer-legal">&copy; <?php echo date('Y'); ?> RedCappi LLC, All Rights Reserved.</li>
    <li><a href="<?php echo  site_url("support/index");?>">Support</a></li>
    <li><a href="<?php echo  base_url()."contact";?>">Contact Us</a></li>
    <li><a href="<?php echo base_url()."blog/";?>">Blog</a></li>
    <li><a href="<?php echo  base_url()."terms";?>">T&amp;C</a></li>
    <li><a href="<?php echo  base_url()."privacy";?>">Privacy Statement</a></li>
    <li class="nobackground"><a href="<?php echo  base_url()."anti-spam";?>">Anti-Spam Policy</a></li>
  </ul>
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
