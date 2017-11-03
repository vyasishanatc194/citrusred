<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <?php
    if(isset($seo_array)){
      if(count($seo_array)>0){
        echo '<meta name="description" content="'.$seo_array[0]['description'].'" />';
        echo '<meta name="keywords" content="'.$seo_array[0]['keyword'].'" />';
      }
    }
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="robots" content="index, follow" />
    <meta name="GOOGLEBOT" content="INDEX, FOLLOW" />
    <meta name="copyright" content="<?php echo SYSTEM_DOMAIN_NAME ;?>. All Rights Reserved." />
    <meta name="publisher" content="<?php echo SYSTEM_DOMAIN_NAME ;?>" />
    <meta name="Author" content="email marketing best email marketing software- <?php echo base_url();?>" />
    <meta name="best email marketing company" content="best email marketing company, we offer email marketing services at very affordable and cheap price." />
    <title>
      <?php
        $title_text=true;
        if(isset($seo_array)){
          if(count($seo_array)>0){
            if(isset($seo_array[0]['title'])){
              echo $seo_array[0]['title'];
              $title_text=false;
            }
          }
        }
        if($title_text){
          echo $title;
        }
       ?>
    </title>
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
  <body>
    <!--[page html]-->
 
    <section role="main" class="main-container content-page">
      <div id="credential-container">
        <img class="logo" src="<?php echo $this->config->item('webappassets');?>/images/home-page-face.png?v=6-20-13" alt="redcappi" />
<h2>Reset Password</h2>	
<div id="credential" class="content key-points">
  <?php			
		echo form_open('user/reset_password/'.$token, array('id' => 'frmResetPassword'));
	?>
    
	<div class="clear"></div>
	<?php 
		if(validation_errors()){
			echo '<div style="color:#FF0000;" class="info">'.validation_errors().'</div>';
		}
		?>
		 <?php
				// display all messages

				if (is_array($messages)):
					echo '<div class="info" style="background:none; border:none;">';
					foreach ($messages as $type => $msgs):
						foreach ($msgs as $message):
							echo ('<span class="' .  $type .'">' . $message . '</span>');
						endforeach;
					endforeach;
					echo '</div>';
				endif;

		?>
		<label for="new_pwd">New Password</label>        
		<?php echo  form_password(array('name'=>'password','id'=>'password','maxlength'=>50,'size'=>40 ,'class'=>'west' ,'value'=>set_value('password'),'title'=>'New Password'));?>
		
		<label for="cnf_pwd">Confirm Password</label>        	
       
		<?php echo  form_password(array('name'=>'confirm_password','id'=>'confirm_password','maxlength'=>50,'size'=>40 ,'class'=>'west' ,'value'=>set_value('confirm_password'),'title'=>'Confirm Password'));?>
		 
		<?php echo form_submit(array('name' => 'submit', 'id' => 'submit','content' => 'submit','class'=>'button-input'), 'Submit'); ?>
		 
	<?php 
		echo form_close();
	?> 
 </div>
 </div>
    </section>
 	
  </body>
</html>

