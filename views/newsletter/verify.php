<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=1100"/>
<title>Add new sending email</title>
<?php echo link_tag('webappassets/css-front/style.css?v=6-20-13'); ?>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-1.4.4.min.js?v=6-20-13"></script>
<style>
body{background: #f4f4f4}
h1{font-size:16px;font-weight:bold;}
ul{padding:20px;}
li{padding:10px; list-style-type:none;}
</style>
<script type="text/javascript">
$(document).ready(function(){
	$('#frmNewEml input').change( function() {
	   var x =($('input[name=domain_reason]:checked', '#frmNewEml').val()); 
		if(x == 1){
			$('#domain_reason_other').text('Changed Domains for my Business');
			$('#domain_reason_other').hide();
		}else if(x == 2){						
			$('#domain_reason_other').text('User is no longer employed with organization');
			$('#domain_reason_other').hide();
		}else if(x == 3){
			$('#domain_reason_other').text('');
			$('#domain_reason_other').show(); 
		}
	});
});

function checkAns(){
	if($('#domain_reason_other').val() != ''){
		return true;
	}else{		
		alert("Please select/enter a reason!");
		return false;
	}
}
</script>
</head>
<body>
<!--[body]-->
<div style="width:100%;text-align:center;margin:100px auto;">
  <div  class="thanks-box" style="width:100%;text-align:left;">
    <div class="thanks-msg" style="width:750px;background:#ffffff; padding:20px;">
      <h1>Thanks for verifying your new sending email. Changing your sending email can harm your reputation and ours.  Please choose one of the reasons below for changing your sending email:</h1>
      <br/>
	  <form name="frmNewEml" id="frmNewEml" action="<?php echo base_url();?>user/domain_reason/" method="post" accept-charset="utf-8" onsubmit="javascript:return checkAns();">
		<ul>
			<li> <input type="radio" class="rdoReason" name="domain_reason" value="1" /> Changed Domains for my Business</li>
			<li> <input type="radio" class="rdoReason" name="domain_reason" value="2" /> User is no longer employed with organization</li>
			<li> <input type="radio" class="rdoReason" name="domain_reason" value="3" /> Other - Fill in Reason
			<br/>
			<textarea id="domain_reason_other" name="domain_reason_other" style="display:none; width:400px; height:130px;"></textarea>
			
			</li>
        </ul>
		<input type="hidden" name="hidString" value="<?php echo $str;?>" />
		<input type="submit" name="btnSubmit" value=" Submit " />
		</form>
        <br/>
      
    </div>
    <div class="gap"></div>
    <div class="gap"></div>
  </div>
<?php    
    echo '<a href="'.  site_url("/").'"> <img src="'. $this->config->item('webappassets').'images-front/thanks-logo.png?v=6-20-13" alt="logo" title="logo" border="0"></a>';
   //echo '<div class="footlink">Powered by <a href="http://www.'.SYSTEM_DOMAIN_NAME.'" target="_blank">RedCappi</a></div>';	
?>   
</div>
</body>
</html>