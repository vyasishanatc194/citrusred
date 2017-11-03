<div class="tblheading">Approve Campaign</div>
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


echo form_open('webmaster/campaign/edit/'.$campaign['campaign_id'], array('id' => 'frmCampaignEdit'));
echo '<table class="tbl_forms"><tr><td >';
echo '<div style="color:#FF0000;">'.validation_errors().'</div>'; 
echo "</td></td>";
echo "<tr><td>";
echo "Username<br/>"; 
echo form_input(array('name'=>'username','id'=>'username','maxlength'=>50 ,'value'=>$user['member_username'])) ."</td>";

echo "<td >Email<br/>"; 
echo form_input(array('name'=>'email','id'=>'email','maxlength'=>50,'value'=>$user['email_address'] )) ."</td>";

echo "<td >Phone<br/>"; 
echo form_input(array('name'=>'phone','id'=>'phone','maxlength'=>50,'value'=>$user['phone_number'] )) ."</td></tr>";

echo "<tr><td >First Name<br/>"; 
echo form_input(array('name'=>'first_name','id'=>'first_name','maxlength'=>20,'value'=>$user['first_name'] )) ."</td>";
 
echo "<td>Last Name<br/>"; 
echo form_input(array('name'=>'last_name','id'=>'last_name','maxlength'=>20,'value'=>$user['last_name'])) ."</td>";
 

echo "<td>Address Line1<br/>"; 
echo form_input(array('name'=>'address1','id'=>'address1','maxlength'=>50,'value'=>$user['address_line_1'] )) ."</td></tr>";
echo '<tr><td>';
echo "Address Line2<br/>"; 
echo form_input(array('name'=>'address2','id'=>'address2','maxlength'=>50 ,'value'=>$user['address_line_2'])) ."</td>";

echo "<td>City<br/>"; 
echo form_input(array('name'=>'city','id'=>'city','maxlength'=>50,'value'=>$user['city'] )) ."</td>";

echo "<td>State<br/>"; 
echo form_input(array('name'=>'state','id'=>'state','maxlength'=>50,'value'=>$user['state'] )) ."</td></tr>";

echo "<tr><td>Zip Code<br/>"; 
echo form_input(array('name'=>'zipcode','id'=>'zipcode','maxlength'=>50,'value'=>$user['zipcode'] )) ."</td>";

echo "<td>Country<br/>"; 
echo form_input(array('name'=>'country','id'=>'country','maxlength'=>50,'value'=>$user['country'] )) ."</td>";

echo "<td>Status<br/>"; 
echo form_dropdown('status',array('1'=>'Active','2'=>'Inactive'),$user['status']);
echo "</td></tr>";

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