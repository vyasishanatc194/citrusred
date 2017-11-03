<div class="tblheading">Email Personalize</div>
<?php
echo form_open('webmaster/sitesetting_manage/email_personalize/', array('id' => 'frmEmailPersonalize'));
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
echo "<tr><td valign='top'>First Name</td><td>"; 
echo form_input(array('name'=>'subscriber_first_name','id'=>'subscriber_first_name','maxlength'=>50 ,'value'=>$subscriber_first_name));
echo '</td><td>';
echo form_input(array('name'=>'subscriber_first_name_default_value','id'=>'subscriber_first_name_default_value','maxlength'=>50 ,'value'=>$default_value_arr[subscriber_first_name]));
echo '</td></tr>';
echo "<tr><td valign='top'>Last Name</td><td>"; 
echo form_input(array('name'=>'subscriber_last_name','id'=>'subscriber_last_name','maxlength'=>50 ,'value'=>$subscriber_last_name));
echo '</td><td>';
echo form_input(array('name'=>'subscriber_last_name_default_value','id'=>'subscriber_last_name_default_value','maxlength'=>50 ,'value'=>$default_value_arr[subscriber_last_name]));
echo '</td></tr>';
echo "<tr><td valign='top'>Email Address</td><td>"; 
echo form_input(array('name'=>'subscriber_email_address','id'=>'subscriber_email_address','maxlength'=>50 ,'value'=>$subscriber_email_address));
echo '</td><td>';
echo form_input(array('name'=>'subscriber_email_address_default_value','id'=>'subscriber_email_address_default_value','maxlength'=>50 ,'value'=>$default_value_arr[subscriber_email_address]));
echo '</td></tr>';
echo "<tr><td valign='top'>State</td><td>"; 
echo form_input(array('name'=>'subscriber_state','id'=>'subscriber_state','maxlength'=>50 ,'value'=>$subscriber_state));
echo '</td><td>';
echo form_input(array('name'=>'subscriber_state_default_value','id'=>'subscriber_state_default_value','maxlength'=>50 ,'value'=>$default_value_arr[subscriber_state]));
echo '</td></tr>';
echo "<tr><td valign='top'>Zip Code</td><td>"; 
echo form_input(array('name'=>'subscriber_zip_code','id'=>'subscriber_zip_code','maxlength'=>50 ,'value'=>$subscriber_zip_code));
echo '</td><td>';
echo form_input(array('name'=>'subscriber_zip_code_default_value','id'=>'subscriber_zip_code_default_value','maxlength'=>50 ,'value'=>$default_value_arr[subscriber_zip_code]));
echo '</td></tr>';
echo "<tr><td valign='top'>Country</td><td>"; 
echo form_input(array('name'=>'subscriber_country','id'=>'subscriber_country','maxlength'=>50 ,'value'=>$subscriber_country));
echo '</td><td>';
echo form_input(array('name'=>'subscriber_country_default_value','id'=>'subscriber_country_default_value','maxlength'=>50 ,'value'=>$default_value_arr[subscriber_country]));
echo '</td></tr>';
echo "<tr><td valign='top'>City</td><td>"; 
echo form_input(array('name'=>'subscriber_city','id'=>'subscriber_city','maxlength'=>50 ,'value'=>$subscriber_city));
echo '</td><td>';
echo form_input(array('name'=>'subscriber_city_default_value','id'=>'subscriber_city_default_value','maxlength'=>50 ,'value'=>$default_value_arr[subscriber_city]));
echo '</td></tr>';
echo "<tr><td valign='top'>Company</td><td>"; 
echo form_input(array('name'=>'subscriber_company','id'=>'subscriber_company','maxlength'=>50 ,'value'=>$subscriber_company));
echo '</td><td>';
echo form_input(array('name'=>'subscriber_company_default_value','id'=>'subscriber_company_default_value','maxlength'=>50 ,'value'=>$default_value_arr[subscriber_company]));
echo '</td></tr>';
echo "<tr><td valign='top'>DOB</td><td>"; 
echo form_input(array('name'=>'subscriber_dob','id'=>'subscriber_dob','maxlength'=>50 ,'value'=>$subscriber_dob));
echo '</td><td>';
echo form_input(array('name'=>'subscriber_dob_default_value','id'=>'subscriber_dob_default_value','maxlength'=>50 ,'value'=>$default_value_arr[subscriber_dob]));
echo '</td></tr>';
echo "<tr><td valign='top'>Phone</td><td>"; 
echo form_input(array('name'=>'subscriber_phone','id'=>'subscriber_phone','maxlength'=>50 ,'value'=>$subscriber_phone));
echo '</td><td>';
echo form_input(array('name'=>'subscriber_phone_default_value','id'=>'subscriber_phone_default_value','maxlength'=>50 ,'value'=>$default_value_arr[subscriber_phone]));
echo '</td></tr>';
echo "<tr><td valign='top'>Address</td><td>"; 
echo form_input(array('name'=>'subscriber_address','id'=>'subscriber_address','maxlength'=>50 ,'value'=>$subscriber_address));
echo '</td><td>';
echo form_input(array('name'=>'subscriber_address_default_value','id'=>'subscriber_address_default_value','maxlength'=>50 ,'value'=>$default_value_arr[subscriber_address]));
echo '</td></tr>';
echo "<tr><td valign='top'></td><td>";
echo form_submit(array('name' => 'btnChangeSetting', 'id' => 'btnChangeSetting','class'=>'inputbuttons','content' => 'Change Setting'), 'Change Setting');
echo form_hidden('action','submit');
echo form_close();
echo '</td></tr></table>';
?>
</center>