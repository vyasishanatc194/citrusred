<!--[body]-->
<div id="body-dashborad">
  <div id="first-time-sender" class="container">
    <h1>Congratulations!</h1>
    <div>
      <!-- <p> Already use Red Cappi on your phone? <a href="#">Finish signup now.</a> </p> -->
  	  <?php
    		echo form_open('refer_friend/index', array('id' => 'frmReferFriend','style'=>'font-size:16px;color:#555;font-weight:700;line-height:28px;'));
    	?>
      <p>
        <img src="<?php echo base_url();?>webappassets/images/home-page-face.png?v=6-20-13" width="150" alt="logo" title="logo"/>
      </p>
      <p>
		You're now sending your first email campaign with RedCappi :-)
      </p>
      <p>
		Since you are new to RedCappi, we will need to make sure that your email content and list are in tip-top shape and that all checks out before we can send.
      </p>
      <p>
		We MAY send you an email requesting more info, before we can release your campaign. Check your inbox, and don't worry, this will usually be quick...
      </p>
      <p>
		If you have any questions or concerns email <a href="mailto:<?php echo SYSTEM_EMAIL_FROM ;?>" style="color:blue"><?php echo SYSTEM_EMAIL_FROM ;?></a> at anytime.
      </p>
      <p style="text-align: center">
        <a class="btn add inline-block" title="" href="<?php echo site_url('newsletter/campaign');?>">Back</a>
      </p>
    </div>
  </div>
</div>
  <!--[/body]-->
