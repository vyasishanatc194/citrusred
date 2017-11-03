<!--<script src="<?php echo $this->config->item('webappassets');?>js/jquery.js?v=6-20-13" type="text/javascript" language="javascript"></script>
<script src="<?php echo $this->config->item('webappassets');?>js/jquery.MultiFile.min.js?v=6-20-13" type="text/javascript" language="javascript"></script> -->
<div class="tblheading">Edit Listing Category</div>
<div id="messages" style="color:#FF0000;">
<?php
// display all messages
if (isset($error)) echo $error;
if (is_array($messages)):
    foreach ($messages as $type => $msgs):
        foreach ($msgs as $message):
            echo ('<span class="' .  $type .'">' . $message . '</span>');
        endforeach;
    endforeach;
endif;
?>
</div>
<?php
print_r($upload_data);
echo form_open_multipart('webmaster/post_image_upload/do_upload/'.$post_id, array('id' => 'frmUpload'));
echo '<table class="tbl_forms"><tr><td >';

echo '<div style="color:#FF0000;">'.validation_errors().'</div>';
echo "</td></tr>";
echo "<tr><td>";
echo "Title</td><td>";
echo '<input type="file" name="userfile" size="20" class="multi" /></td></tr>';
echo "<tr><td>Status</td><td>";
echo form_dropdown('img_status',array('1'=>'Active','0'=>'Inactive'),$category['img_status']);
echo "</td></tr>";
echo '<tr><td><br>';


echo form_submit(array('name' => 'btnEdit', 'id' => 'btnEdit','class'=>'inputbuttons','content' => 'edit'), 'Submit');
echo '&nbsp;';
echo "</td></tr>";
echo form_hidden('action','submit');
echo form_hidden('img_id','');
echo form_hidden('post_id',$post_id);

/* echo form_hidden('added_on',date('Y-m-d')); */
echo form_close();
echo "</table>";
?>
</div>
