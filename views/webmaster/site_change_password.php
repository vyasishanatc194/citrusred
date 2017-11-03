<div class="tblheading">Change Password</div>


<?php 

  


echo form_open('webmaster/sitesetting_manage/change_password/', array('id' => 'frmChangePassword'));
echo '<table class="tbl_forms">';  

echo "<tr><td colspan='2' valign='top'><div style=\"color:#FF0000;\">".validation_errors()."</div></td></tr>"; 

echo "<tr><td colspan='2' valign='top'><div id=\"messages\" style=\"color:#FF0000;\">";

/// display all messages

if (is_array($messages)):
    foreach ($messages as $type => $msgs):
        foreach ($msgs as $message):
            echo ('<span class="' .  $type .'">' . $message . '</span>');
        endforeach;
    endforeach;
endif;

echo "</div></td></tr>"; 

echo "<tr><td valign='top'>Current Password</td><td>"; 
echo form_password(array('name'=>'webmaster_password','id'=>'webmaster_password','maxlength'=>50 ,'value'=>set_value('webmaster_password')));
echo '</td></tr>';



echo "<tr><td valign='top'>New Password</td><td>"; 
echo form_password(array('name'=>'webmaster_new_password','id'=>'webmaster_new_password','maxlength'=>50 ,'value'=>set_value('webmaster_new_password')));
echo '</td></tr>';

echo "<tr><td valign='top'>Confirm New Password</td><td>"; 
echo form_password(array('name'=>'webmaster_confirm_password','id'=>'webmaster_confirm_password','maxlength'=>50 ,'value'=>set_value('webmaster_confirm_password')));
echo '</td></tr>';


echo "<tr><td valign='top'></td><td>"; 
echo form_submit(array('name' => 'btnChangePasssword', 'id' => 'btnChangePasssword','class'=>'inputbuttons','content' => 'Change Passsword'), 'Change Passsword');


echo form_hidden('action','submit');
echo form_close();

echo '</td></tr></table>';

?>
</center>