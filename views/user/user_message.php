<!--[body]-->
<!--[body]-->
<div id="body-dashborad">
  <div id="first-time-sender" class="container">
    <h1>Notice</h1>
	  <?php
  		echo form_open('refer_friend/index', array('id' => 'frmReferFriend','style'=>'font-size:16px;color:#514646;font-weight:bold;'));
    ?>
    <div>
      <p>
        <img src="<?php echo base_url();?>webappassets/images/home-page-face.png?v=6-20-13" width="150" alt="logo" title="logo"/></td>
      </p>
      <p>
        <?php echo $msg;?>
      </p>
    </div>
  </div>
</div>
<!--[/body]-->
