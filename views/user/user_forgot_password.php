<center>
<div id="messages">
<?php
/// display all messages

if (is_array($messages)):
    foreach ($messages as $type => $msgs):
        foreach ($msgs as $message):
            echo ('<span class="' .  $type .'">' . $message . '</span>');
        endforeach;
    endforeach;
endif;
?>
</div>  
<br>
<?php
$strloginForm='';
$strloginForm = '<div style="color:#FF0000;">'.validation_errors().'</div>
				<div class="login-pop">
				'; 

$strloginForm .=  form_open('user/forgot_password/', array('id' => 'frmForgotPassword','class' => 'login-form','style' => 'padding-top:65px;'));
$strloginForm .= '<h3>Forgot Password</h3>
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td height="50" ></td>
						<td colspan="2"></td>
					</tr>
					';  

$strloginForm .= "<tr><td width='33%' class='label'>Email-id:<em>*</em></td><td colspan='2'>"; 
$strloginForm .= form_input(array('name'=>'email_address','id'=>'email_address','maxlength'=>50 ,'class'=>'input' ,'value'=>set_value('email_address')));
$strloginForm .= '</td></tr>';
$strloginForm .= "<tr><td valign='top'></td><td colspan='2'>"; 
$strloginForm .= form_submit(array('name' => 'btnForgot', 'id' => 'btnForgot','content' => 'Enter','class'=>'login-button'), 'Login');

$strloginForm .= form_hidden('action','submit');

$strloginForm .= '</td></tr></table>';
$strloginForm .= form_close();
echo $strloginForm;
?>
</center>