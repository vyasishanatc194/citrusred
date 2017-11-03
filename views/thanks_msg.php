<?php echo link_tag('webappassets/css-front/style.css?v=6-20-13'); ?>
<style>body{background: #f4f4f4}</style>
<!--[body]-->
<div style="width:100%;text-align:center;margin:100px auto;">
  <div  class="thanks-box" style="width:100%;text-align:center;">
    <div class="thanks-msg" style="width:350px;background:#fff">
      <?php echo $msg; ?>
      <br/>
      <?php if($showlink !='no' and $sid != ''){?>
        <h2>Unsubscribed accidentally? <a href="<?php echo base_url().'newsletter/unsubscribe_mail/resubscribe/'.$rc_logo.'/'.$sid;?>">Click here to re-subscribe</a></h2>
        <br/>
      <?php }?>
    </div>
    <div class="gap"></div>
    <div class="gap"></div>
    <?php if($rc_logo==1){
    echo '<a href="'.site_url("/").'"> <img src="'. $this->config->item('webappassets').'images-front/thanks-logo.png?v=6-20-13" alt="logo" title="logo" border="0"></a>';
	//echo '<div class="footlink">Powered by <a href="http://www.'.SYSTEM_DOMAIN_NAME.'" target="_blank">RedCappi</a></div>';	
    } ?>
  </div>
</div>
