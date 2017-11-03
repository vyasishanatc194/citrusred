<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<html>
<head>
<title>Upload Form</title>
<script src="<?php echo $this->config->item('webappassets');?>js/jquery.js?v=6-20-13" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->config->item('webappassets');?>js/jquery.MultiFile.min.js?v=6-20-13" type="text/javascript" language="javascript"></script>
</head>
<body>

<?php if (isset($error)) echo $error;?>
<?php echo form_open_multipart('upload/do_upload');?>
<input type="file" name="userfile[]" size="20" class="multi" />
<br /><br />

<input type="submit" value="upload" />

</form>

</body>
</html>

