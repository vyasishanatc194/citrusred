<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title; ?></title>
<?php $ci =& get_instance(); ?>
<!-- ------------Load Css----------------------------- -->
<link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico">
<!-- ------------Load Css----------------------------- -->
<link rel="stylesheet" type="text/css" href="<?php echo CAMPAIGN_DOMAIN.'webappassets/css/signup_form.css?v=6-20-13';?>" />
</head>

<body  style="background-color:<?php echo $signup_from['form'][0]['form_background_color'];?>">
	<?php echo $signup_from['copy_code'];?>	 
	 <div class="footlink">Powered by <a href="<?php echo base_url();?>" target="_blank">RedCappi</a><img src="<?php echo CAMPAIGN_DOMAIN.'newsletter/signup/showpblogo/'.$signup_form['form'][0]['id'];?>" alt="" title="" border="0"></div>
</body>
</html>
