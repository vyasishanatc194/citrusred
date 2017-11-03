<!--[footer]-->
<div id="footer">
  <div class="container">
    <div class="footerlinks">
      <div class="footerlinks-inner-div">
        <h5>Company</h5>
        <ul class="footerlinks-navi">
          <li><a href="<?php echo  base_url()."about";?>">About Us</a></li>
          <li><a href="<?php echo  base_url()."contact";?>">Contact Us</a></li>          
		  <li><a href="<?php echo  site_url("support/index");?>">Support</a></li>
          <li><a href="<?php echo base_url()."blog/";?>">Blog</a></li>
        </ul>
      </div>
      <div class="footerlinks-inner-div">
        <h5>RedCappi</h5>
        <ul class="footerlinks-navi">
          <li><a href="<?php echo  base_url()."email-marketing-features";?>">Features</a></li>
          <li><a href="<?php echo  base_url()."pricing";?>">Pricing</a></li>
          
          <li><a href="<?php echo  base_url()."rc_api";?>">API</a></li>
        </ul>
      </div>
      <div class="footerlinks-inner-div">
        <h5>Legal</h5>
        <ul class="footerlinks-navi">
          <li><a href="<?php echo  base_url()."terms";?>">Terms and Conditions</a></li>
          <li><a href="<?php echo  base_url()."privacy";?>">Privacy Statement</a></li>
          <li><a href="<?php echo  base_url()."anti-spam";?>">Anti-Spam Policy</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-right-area ">
      <p>&copy; <?php echo date('Y'); ?> RedCappi LLC, All Rights Reserved.</p>
      <a href="http://plus.google.com/104124542722954210936?rel=author" style="display:none">Google</a>
      <ul class="socal-icon">
        <li><a href="https://plus.google.com/104124542722954210936" target="_blank"> <img align="absmiddle" src="<?php echo $this->config->item('webappassets');?>images/google-plus.png?v=6-20-13" alt="Facebook"> </a></li>
        <li><a href="http://www.facebook.com/redcappi" target="_blank"> <img align="absmiddle" src="<?php echo $this->config->item('webappassets');?>images/facebook.png?v=6-20-13" alt="Facebook"> </a></li>
        <li><a href="http://twitter.com/redcappi" target="_blank"> <img align="absmiddle" src="<?php echo $this->config->item('webappassets');?>images/twitter-icon.png?v=6-20-13" alt="twitter"> </a></li>
        <li>
          <?php
            if(trim($this->uri->segment(1))=='blog_category'){
              $rss_url=base_url()."feed/index/".$this->uri->segment(4);
            }else{
              $rss_url=base_url()."feed/";
            }
          ?>
          <a href="<?php echo $rss_url;?>" target="_blank"> <img align="absmiddle" src="<?php echo $this->config->item('webappassets');?>images/rss.png?v=6-20-13" alt="RSS"> </a>
        </li>
      </ul>
    </div>
  </div>
</div>
  <!--[/footer]-->
</div>
<!--[/page html]-->

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
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-25501252-1', 'auto');
  ga('send', 'pageview');

</script>
 <!-- Used to fire the modal -->
    <script>
      // if you want to use the 'fire' or 'disable' fn,
      // you need to save OuiBounce to an object
      $('body').live('click', function() {
        $('#ouibounce-modal').hide();
      });

      $('#ouibounce-modal .modal-footer').live('click', function() {
        $('#ouibounce-modal').hide();
      });

      $('#ouibounce-modal .modal').live('click', function(e) {
        e.stopPropagation();
      });
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
</body>
</html>