<div class="tblheading">View User</div>
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
<?php 
echo form_open('webmaster/users_manage/user_edit/'.$user['member_id'], array('id' => 'frmUserEdit'));
echo '<table class="tbl_forms"><tr><td >';

echo '<div style="color:#FF0000;">'.validation_errors().'</div>'; 
echo "</td></td>";
echo "<tr><td>";
echo "Username<br/>"; 
echo form_input(array('name'=>'username','id'=>'username','maxlength'=>50 ,'value'=>$user['member_username'],'readonly'=>'readonly')) ."</td>";

echo "<td >Email<br/>"; 
echo form_input(array('name'=>'email','id'=>'email','maxlength'=>50,'value'=>$user['email_address'],'readonly'=>'readonly' )) ."</td>";

echo "<td >Phone<br/>"; 
echo form_input(array('name'=>'phone','id'=>'phone','maxlength'=>50,'value'=>$user['phone_number'],'readonly'=>'readonly' )) ."</td></tr>";

echo "<tr><td >First Name<br/>"; 
echo form_input(array('name'=>'first_name','id'=>'first_name','maxlength'=>20,'value'=>$user['first_name'],'readonly'=>'readonly' )) ."</td>";
 
echo "<td>Last Name<br/>"; 
echo form_input(array('name'=>'last_name','id'=>'last_name','maxlength'=>20,'value'=>$user['last_name'],'readonly'=>'readonly')) ."</td>";
 

echo "<td>Address Line1<br/>"; 
echo form_input(array('name'=>'address1','id'=>'address1','maxlength'=>50,'value'=>$user['address_line_1'],'readonly'=>'readonly' )) ."</td></tr>";
echo '<tr><td>';
echo "Address Line2<br/>"; 
echo form_input(array('name'=>'address2','id'=>'address2','maxlength'=>50 ,'value'=>$user['address_line_2'],'readonly'=>'readonly')) ."</td>";

echo "<td>City<br/>"; 
echo form_input(array('name'=>'city','id'=>'city','maxlength'=>50,'value'=>$user['city'],'readonly'=>'readonly' )) ."</td>";

echo "<td>State<br/>"; 
echo form_input(array('name'=>'state','id'=>'state','maxlength'=>50,'value'=>$user['state'],'readonly'=>'readonly' )) ."</td></tr>";

echo "<tr><td>Zip Code<br/>"; 
echo form_input(array('name'=>'zipcode','id'=>'zipcode','maxlength'=>50,'value'=>$user['zipcode'],'readonly'=>'readonly' )) ."</td>";

echo "<td>Country<br/>"; 
echo form_input(array('name'=>'country','id'=>'country','maxlength'=>50,'value'=>$user['country_name'],'readonly'=>'readonly' )) ."</td></tr>";


echo '<tr><td><br>';
echo form_close();
echo "</table>";
?>
</div>