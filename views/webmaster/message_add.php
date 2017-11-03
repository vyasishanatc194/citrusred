<div class="tblheading">Create New Message</div>
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
<div class="form">
<?php 
	


echo form_open('webmaster/manage_messages/message_create/', array('id' => 'frmUserCreate'));
echo '<table class="tbl_forms" border="0" style="width:60%"><tr><td colspan="2">';

echo '<div style="color:#FF0000;">'.validation_errors().'</div>'; 
echo "</td></td>";
echo "<tr><td>";
echo "Heading<br/>"; 
echo form_input(array('name'=>'message_name','id'=>'message_name','maxlength'=>250 ,'value'=>$message_detail['message_name'])) ."</td>";
echo "<td>Email Subject<br/>"; 
   echo form_textarea(array('name'=>'email_subject','id'=>'email_subject','rows'=>1,'cols'=>90,'value'=>$message_detail['email_subject']));
echo "</td></tr>"; 

echo "<tr><td>Body Text<br/>"; 
   echo form_textarea(array('name'=>'message_body','id'=>'message_body','rows'=>7,'cols'=>60,'value'=>$message_detail['message_body']));
echo "</td>";  
echo "<td>Email Body<br/>"; 
   echo form_textarea(array('name'=>'email_body','id'=>'email_body','rows'=>7,'cols'=>90,'value'=>$message_detail['email_body']));
echo "</td></tr>"; 

/*
echo "<td>Message Type<br/>"; 
echo form_dropdown('message_type',array('0'=>'System','1'=>'Admin'),$message_detail['message_type']) ."</td></tr>";

echo "<tr><td>User Type<br/>"; 
echo form_dropdown('user_type',array('0'=>'Bulk','1'=>'Individual'),$message_detail['user_type']) ."</td></tr>";
  
echo "<tr><td>Body Type<br/>"; 
echo form_dropdown('message_body_type',array('0'=>'Text','1'=>'URL'),$message_detail['message_body_type']) ."</td></tr>";
   
*/


   
echo "<tr><td>Mail Notification<br/>"; 
echo form_dropdown('is_mail_notification',array('0'=>'No','1'=>'Yes'),$message_detail['is_mail_notification']) ."</td>";
  
echo "<td>Status<br/>"; 
echo form_dropdown('message_status',array('1'=>'Active','0'=>'Inactive'),$message_detail['message_status']);
echo "</td></tr>";
  

echo '<tr><td colspan="2"><br>';
echo form_submit(array('name' => 'btnEdit', 'id' => 'btnEdit','class'=>'inputbuttons','content' => 'edit'), 'Submit');
echo '&nbsp;';
echo form_button(array('name'=>'btnCancel','class'=>'inputbuttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'webmaster/manage_messages/'."'"));
echo "</td></tr>";
echo form_hidden('action','submit');
 
echo form_close();
echo "</table>";
?>
</div>