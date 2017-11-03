    <?php
    $this->load->helper('url');
    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if (strpos($url, $this->config->item('redirect_url')) > 0) {
        redirect('https://getredcappi.com/contact-us/');
    }
    ?>
<section role="main" class="main-container content-page">
  <h2>Contact Us</h2>
  <div class="content key-points">
    <div id="contact">
        <!-- www.123contactform.com script begins here -->
<script type="text/javascript" defer src="//www.123contactform.com/embed/2345711.js" data-role="form"></script>
<p>Powered by <a class="footerLink13" title="123ContactForm" href="http://www.123contactform.com">123ContactForm</a> | <a style="font-size:small!important;color:#000000!important; text-decoration:underline!important;" title="Looks like phishing? Report it!" href="http://www.123contactform.com/sfnew.php?s=123contactform-52&control119314=http:///contact-form--2345711.html&control190=Report%20abuse" rel="nofollow">Report abuse</a></p><!-- www.123contactform.com script ends here -->

   <!--
   <?php// echo form_open(base_url().'contact/', array('id' => 'signup'));?>
      <?php// echo validation_errors('<span class="error">', '</span>'); ?>
      <?php //if(isset($msg1)){ ?>
        <?php //echo $msg1; ?>
      <?php //} ?>
     -->
  <!--        <label for="email">Name</label>
          <input type="text" name="name" value="<?php// echo $name;?>" />
          <label for="email">Email Address</label>
          <input type="text" name="email" value="<?php// echo $email;?>" />
          <label for="phone">Phone</label>
          <input type="text" name="phone" value="<?php //echo $phone;?>" />
          
          <label for="message">Message/Comments:</label>
          <textarea name="desc"><?php// echo $desc;?></textarea>
          <input type="hidden" name="word" value="<?php// echo $word;?>" />
          <label for="securityCode">Security Check: <span>Please enter the security code shown in the image.</label>
          <span class="captcha">
            <?php// echo $captcha; ?>
            <input type="text" class="input-captha" name="securityCode" size="33"  >
          </span>
          <input type="submit" class="btn" />
      </form>-->
      <div>
          <h3>Customer Support:</h3>
          <a href="mailto:support@redcappi.com">support@redcappi.com</a>
          <br/>Tel: 1-877-722-2774
          <h3>RedCappi LLC</h3>
          <p>1046 West Kinzie Street<br/> Suite 300 - #370<br/>
Chicago, IL, 60642</p>
          <!-- p>Made in Chicago, IL</p -->
      </div>
    </div>
  </div>
</section>
