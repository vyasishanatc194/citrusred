<center>
<div id="messages">
<?php
/// display all messages

if (is_array($messages)):
    foreach ($messages as $type => $msgs):
        foreach ($msgs as $message):
            echo ('<span class="' .  $type .'" style="color:#FF0000;">' . $message . '</span>');
        endforeach;
    endforeach;
endif;
?>
</div>  
<br>
<?php 
echo '<div style="color:#FF0000;">'.validation_errors().'</div>';
echo '<table class="login_table">';  
echo form_open('webmaster/account/login/', array('id' => 'frmLogin'));
echo "<tr><th colspan='2' valign='top'>Webmaster Login</th></tr>";
echo "<tr><td valign='top'>Username</td><td>"; 
echo form_input(array('name'=>'webmaster_username','id'=>'webmaster_username','maxlength'=>50 ,'value'=>set_value('webmaster_username')));
echo '</td></tr>';
echo "<tr><td valign='top'>Password</td><td>"; 
echo form_password(array('name'=>'webmaster_password','id'=>'webmaster_password','maxlength'=>50 ,'value'=>set_value('webmaster_password')));
echo '</td></tr>';
echo "<tr><td valign='top'></td><td>"; 
echo form_submit(array('name' => 'btnLogin','class'=>'inputbuttons', 'id' => 'btnLogin','content' => 'Login'), 'Login');
echo form_hidden('action','submit');
echo form_close();
echo '</td></tr></table>';
?>
</center>