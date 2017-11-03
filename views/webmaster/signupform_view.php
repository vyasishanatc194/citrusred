<div class="tblheading">View Signup-form</div>
<div id="messages">
<?php
// display all messages

if (is_array($messages)):
    foreach ($messages as $type => $msgs):
        foreach ($msgs as $message):
            echo ('<span class="' .  $type .'">' . $message . '</span>');
        endforeach;
    endforeach;
endif;
?>
</div>  
<h5><?php echo $signupform['form_name'];?></h5>
<table class="tbl_forms">
<tr>	
	<td><b>Form Title</b></td><td><?php echo $signupform['form_title'];?></td>
	<td><b>Button text</b></td><td><?php echo $signupform['form_button_text'];?></td>
</tr>
<tr>	
	<td><b>Landing URL</b></td><td><?php echo $signupform['confirmation_thanks_you_message_url'];?></td>
	<td><b>Thankyou URL</b></td><td><?php echo $signupform['singup_thank_you_message_url'];?></td>
</tr>
<tr>
	<td><b>From Email</b></td><td><?php echo $signupform['form_email'];?></td>
	<td><b>From name</b></td><td><?php echo $signupform['form_name'];?></td>
</tr>	
<tr>
	<td><b>Subject</b></td><td colspan=3><?php echo $signupform['subject'];?></td>
</tr>	
<tr>
	<td><b>Message</b></td><td colspan="3" style="width: 200px; word-wrap: break-word;"><?php echo $signupform['confirmation_emai_message'];?></td>
</tr>	
</table>
<p>
&nbsp;
</p>
</div>