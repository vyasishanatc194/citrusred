<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
  
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="robots" content="index, follow" />
    <meta name="GOOGLEBOT" content="INDEX, FOLLOW" />
    <meta name="copyright" content="<?php echo SYSTEM_DOMAIN_NAME ;?>. All Rights Reserved." />
    <meta name="publisher" content="<?php echo SYSTEM_DOMAIN_NAME ;?>" />
    <meta name="Author" content="email marketing best email marketing software- <?php echo base_url();?>" />
    <meta name="best email marketing company" content="best email marketing company, we offer email marketing services at very affordable and cheap price." />
   
    <?php $ci =& get_instance();?>
    <link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css/style.css?v=6-20-13"  media="screen" />
    <!--[main script] -->
    <script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-latest.js?v=6-20-13"></script>
    <!--[/main script] -->
    <!--[if lt IE 9]>
    <script src="<?php echo $this->config->item('webappassets');?>js/html5shiv-printshiv.js?v=6-20-13"></script>
    <![endif]-->
  </head>
  <script>
   
  var i = setInterval(function(){
	 var mid = jQuery('.member_id').val();
	  $.ajax({
		url: "<?php echo base_url() ?>user/logcheck/"+mid,
		type:"POST",
		success: function(data) {
		  if(data=="success"){
			window.location.href = "<?php echo base_url() ?>newsletter/campaign";
		  }
		}
	  });
 
	}	,60000); 
	
  </script>
  <body style="background: #fff !important;">
    
    <section role="main" class="main-container content-page login_content">
	<input type="hidden" class="member_id" id="member_id" value="<?php echo $this->session->userdata['member_id'];?>">
      <div class="custom_landing">
		
      
     
        <p class="custom_te"> We are working on something.. </br>
		After the process is completed,you will be automatically redirected to the website</br>
		For More details contact: support@redcappi.com</p>
   
      </div>
    </section>

</body>
</html>
