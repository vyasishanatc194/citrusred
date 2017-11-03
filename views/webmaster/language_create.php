<div class="tblheading">Create New Language</div>
 <div id="messages" style="color:#FF0000;">
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

echo form_open('webmaster/language_manage/language_create/', array('id' => 'frmCategoryCreate'));
echo '<table class="tbl_forms"><tr><td >';

echo '<div style="color:#FF0000;">'.validation_errors().'</div>'; 
echo "</td></td>";
echo "<tr><td>";
echo "Language</td><td>"; 
echo form_input(array('name'=>'language','id'=>'language','maxlength'=>50 ,'value'=>$language['language'])) ."</td></tr>";
echo "<tr><td>";
echo "Language Code</td><td>"; 
echo form_input(array('name'=>'language','id'=>'language','maxlength'=>50 ,'value'=>$language['language_code'])) ."</td></tr>";
echo "<tr><td>";
echo form_submit(array('name' => 'btnEdit', 'id' => 'btnEdit','class'=>'inputbuttons','content' => 'submit'), 'Submit');
echo '&nbsp;';
echo form_button(array('name'=>'btnCancel','class'=>'inputbuttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'webmaster/language_manage/language_list'."'"));
echo "</td></tr>";
echo form_hidden('action','submit');
echo form_close();
echo "</table>";
?>
</div>