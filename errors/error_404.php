<?php
header("HTTP/1.1 200 OK");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Page Not Found</title>
<!--------------Load Css------------------------------->
<link rel="shortcut icon" href="<?php echo config_item('base_url');?>favicon.ico">
<!--------------Load Css------------------------------->
<link href="<?php echo config_item('webappassets');?>css/inner_red.css?v=6-20-13" rel="stylesheet" type="text/css" /><!--[if IE 6]>
<script src="<?php echo config_item('webappassets');?>js/DD_belatedPNG_0.0.8a-min.js?v=6-20-13" type="text/javascript"></script>
<script type="text/javascript">
DD_belatedPNG.fix('#outer, #container, #header, a, img');
</script>
<![endif]-->
</head>
<body class="bgr-red-gradient">
  <div id="body-dashborad">
    <div class="container" id="maintenance-container">
      <h1>Page Not Found</h1>
      <div id="maintenance">
        <p>The page you are looking for either doesn't exist or it's been removed from the site.</p>
        <p>Click on any of the links below to continue.</p>
        <ul class="clean">
          <li><a href="<?php echo config_item('base_url');?>">Home</a></li>
          <li><a href="<?php echo config_item('base_url');?>about">About Us</a></li>
          <li><a href="<?php echo config_item('base_url');?>contact">Contact Us</a></li>
          <li><a href="<?php echo config_item('base_url');?>blog">Blog</a></li>
        </ul>
      </div>
    </div>
  </div>
</body>
</html>
