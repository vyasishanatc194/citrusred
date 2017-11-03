<div class="tblheading">Create New User</div>
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
$user = $user[0];	

echo form_open('webmaster/users_manage/subuser_create/', array('id' => 'frmUserCreate'));
echo '<table class="tbl_forms"><tr><td >';

echo '<div style="color:#FF0000;">'.validation_errors().'</div>'; 
echo "</td></td>";
echo "<tr><td>Parent Member<em>*</em><br/>". form_dropdown('parent_id',$members_list,$user['parent_id'])."</td><td>";
echo "Username<em>*</em><br/>"; 
echo form_input(array('name'=>'username','id'=>'username','maxlength'=>50 ,'value'=>$user['member_username'])) ."</td>";

echo "<td >Email<em>*</em><br/>"; 
echo form_input(array('name'=>'email','id'=>'email','maxlength'=>50,'value'=>$user['email_address'] )) ."</td>";

echo "<td >Password<em>*</em><br/>"; 
echo form_input(array('name'=>'member_password','id'=>'member_password','maxlength'=>50 )) ."</td>";
  
echo "</tr>";

echo"<tr><td colspan='4'>Manage permissions(This will only work for staff-members)</td></tr>";
echo"<tr>";
echo"<td>".form_checkbox(array('name'=>'manage_campaigns','id'=>'manage_campaigns','value'=>1,'checked'=>($user['manage_campaigns'])?'checked':'', 'style'=>'width:10px;'))."Manage Campaign</td>";
echo"<td>".form_checkbox(array('name'=>'manage_contacts','id'=>'manage_contacts','value'=>1,'checked'=>($user['manage_contacts'])?'checked':'', 'style'=>'width:10px;'))."Manage Contacts</td>";
echo"<td>".form_checkbox(array('name'=>'manage_stats','id'=>'manage_stats','value'=>1,'checked'=>($user['manage_stats'])?'checked':'', 'style'=>'width:10px;'))."Manage Stats</td>";
echo"<td>".form_checkbox(array('name'=>'manage_autoresponders','id'=>'manage_autoresponders','value'=>1,'checked'=>($user['manage_autoresponders'])?'checked':'', 'style'=>'width:10px;'))."Manage Autoresponders</td>";
echo "</tr>";

echo "<tr>";
echo"<td>".form_checkbox(array('name'=>'manage_signupforms','id'=>'manage_signupforms','value'=>1,'checked'=>($user['manage_signupforms'])?'checked':'', 'style'=>'width:10px;'))."Manage Signup-forms</td>";
echo"<td>".form_checkbox(array('name'=>'manage_extra','id'=>'manage_extra','value'=>1,'checked'=>($user['manage_extra'])?'checked':'', 'style'=>'width:10px;'))."Manage Extras</td>";
echo "<td>&nbsp;</td><td>&nbsp;</td></tr>";
echo '<tr><td><br>';

echo form_submit(array('name' => 'btnEdit', 'id' => 'btnEdit','class'=>'inputbuttons','content' => 'edit'), 'Submit');
echo '&nbsp;';
echo form_button(array('name'=>'btnCancel','class'=>'inputbuttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'webmaster/users_manage/users_list'."'"));
echo "</td></tr>";
echo form_hidden('action','submit');
echo form_hidden('member_id',$user['member_id']);
echo form_close();
echo "</table>";
?>
</div>