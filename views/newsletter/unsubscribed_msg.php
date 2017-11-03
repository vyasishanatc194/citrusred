<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>RedCappi: Unsubscribe</title>
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
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-1.4.4.min.js?v=6-20-13"></script>
<script type="text/javascript" language="javascript">
jQuery('.rd_feedback').live('click',function(e){
	var opt = jQuery(this).val();
	if(opt == 6){
		jQuery('#unsubscribed_feedback_text').attr('readonly',false);
	}else{	
		jQuery('#unsubscribed_feedback_text').attr('readonly',true);
		jQuery('#unsubscribed_feedback_text').val('');
	}	
});
jQuery('#btnSubmit').live('click', function(e){
	if(jQuery($('input[name=unsubscribed_feedback]:radio')).is(':checked')){
		var opt = jQuery($('input[name=unsubscribed_feedback]:radio:checked')).val();
	 	var opt_txt = jQuery('#unsubscribed_feedback_text').val();
		var cid_sid = jQuery('#cid_sid').val();
		jQuery.ajax({
			url: "/newsletter/unsubscribe_mail/unsubscribe_feedback/",
			type:"POST",
			data : 'opt='+opt+'&cid_sid='+cid_sid+'&opt_txt='+opt_txt,
			success: function(msg) {
				jQuery('.unsubscribe_box').html('<h3>'+msg+'</h3>');
			}
		});	
	}else{
		alert("Please select a reason");
	}	
});
</script>
<!--[body]-->
<div style="width:100%;text-align:center;margin:100px auto;">
  <div  class="thanks-box" style="width:100%;text-align:center;">
    <div class="thanks-msg" style="width:350px;background:#fff">
      <?php echo $msg; ?>
	  <br/>
      <?php if($cid_sid != ''){?>
        <h2>Unsubscribed accidentally? <a href="<?php echo base_url().'newsletter/unsubscribe_mail/resubscribe/'.$rc_logo.'/'.$sid;?>">Click here to re-subscribe</a></h2>
        <br/>
      <?php }?>
    </div>
	<?php if($isFeedback == 0){?>
	<div class="thanks-msg unsubscribe_box">
		<h3>We'd love to know why you unsubscribed:</h3>
		<input type="hidden" name="cid_sid" id="cid_sid" value="<?php echo $cid_sid;?>" />
		<?php $arrUnsubscribeFeedbackTxt = config_item('unsubscribe_feedback');
		$i=1;
		foreach($arrUnsubscribeFeedbackTxt as $unsub_txt){
			echo "<label><input type='radio' name='unsubscribed_feedback' class='rd_feedback' value='$i'/> ". $unsub_txt."</label><br />";		
			$i++;
		}
		?>		
		<label><textarea name="unsubscribed_feedback_text" id="unsubscribed_feedback_text" readonly style="width:500px;height:200px;"></textarea></label>
		<input type="button" name="btnSubmit" id="btnSubmit" class="submit_button"  value="Submit" />
	</div>
	<?php } ?>
    <div class="gap"></div>
    <div class="gap"></div>
    <?php if($rc_logo==1){
       echo '<a href="'. site_url("/").'"> <img src="'. $this->config->item('webappassets').'images-front/thanks-logo.png?v=6-20-13" alt="logo" title="logo" border="0"></a>';
	 // echo '<div class="footlink">Powered by <a href="http://www.'.SYSTEM_DOMAIN_NAME.'" target="_blank">RedCappi</a></div>';	
    } ?>
  </div>
</div>
</body>
</html>