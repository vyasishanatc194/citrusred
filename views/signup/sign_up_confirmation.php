<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php echo link_tag('webappassets/css-front/style.css?v=6-20-13'); ?>
<style>body{background: #f4f4f4}</style>
</head>
<body>
<!--[body]-->
<div style="width:100%;text-align:center;margin:100px auto;margin-bottom:0px;">
  <div  class="thanks-box" style="width:100%;text-align:center;">
    <div class="thanks-msg" style="width:370px;background:#fff">
      <?php echo ($msg); ?>
    </div>
    <div class="gap"></div>
    <div class="gap"></div>
    <?php if($rc_logo==1){?> 
		<div class="footlink">Powered by <a href="<?php echo base_url();?>" target="_blank">RedCappi</a></div>	   
    <?php } ?>
  </div>
</div>
</body>
</html>