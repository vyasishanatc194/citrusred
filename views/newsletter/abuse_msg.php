<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>RedCappi: Report Abuse</title>
<?php echo link_tag('webappassets/css-front/style.css?v=6-20-13'); ?>
<style>
body{background: #f4f4f4}
.unsubscribe_box{width:600px;background:#ffffff; text-align: left !important; margin:40px auto;}
.unsubscribe_box h3{font-size: 16px;
  font-weight: bold;
  color: #000;
  padding: 15px 25px;
  font-family: arial;
  margin: 15px 0px;
  text-align: left !important;
  }
.unsubscribe_box label {
  -webkit-font-smoothing: antialiased;
  display: inline-block;
  font-weight: 300;
  font-size: 14px;
  padding: 0 0 7px;
  margin-left:40px; 
}
.unsubscribe_box .submit_button {
  vertical-align: middle;
  text-align: center;
  width: 30%;
  padding: 7px 0;
  line-height: 20px;
  font-size: 16px;
  display: block;
  cursor: pointer;
  margin: 0 auto 25px 40px;
  border: 1px solid #ccc;
  -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
  -moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
  -webkit-border-radius: 4px;
  -moz-border-radius: 4px;
  border-radius: 4px;
  -webkit-transition: all 0.2s linear;
  -moz-transition: all 0.2s linear;
  -ms-transition: all 0.2s linear;
  -o-transition: all 0.2s linear;
  color: #fff;
  text-shadow: 0 1px 0 rgba(0, 0, 0, 0.25);
  background-color: #ec1e11;
  background-image: -moz-linear-gradient(top, #ff3019, #cf0404);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ff3019), to(#cf0404));
  background-image: -webkit-linear-gradient(top, #ff3019, #cf0404);
  background-image: -o-linear-gradient(top, #ff3019, #cf0404);
  background-image: linear-gradient(to bottom, #ff3019, #cf0404);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffff3019', endColorstr='#ffcf0404', GradientType=0);
  -ms-filter: "progid:DXImageTransform.Microsoft.gradient(enabled=false)";
  background-color: #cf0404;
  border-color: #51a351 #51a351 #387038;
  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
}
</style>
<!--[body]-->
<div style="width:100%;text-align:center;margin:100px auto;">
  <div  class="thanks-box" style="width:100%;text-align:center;">
    <div class="thanks-msg" style="width:350px;background:#fff">
      <?php echo $msg; ?>
	   
    </div>

    <div class="gap"></div>
    <div class="gap"></div>
    <?php if($rc_logo==1){
       echo '<a href="'. site_url("/").'"> <img src="'. $this->config->item('webappassets').'images-front/thanks-logo.png?v=6-20-13" alt="logo" title="logo" border="0"></a>';
    } ?>
  </div>
</div>
</body>
</html>