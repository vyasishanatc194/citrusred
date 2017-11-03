<div class="tblheading">Edit Banned Words</div>
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
<?php 


echo form_open('webmaster/blog_banned_words/banned_edit/'.$category['ban_id'], array('ban_id' => 'frmBannedWordsEdit'));
echo '<table class="tbl_forms"><tr><td >';

echo '<div style="color:#FF0000;">'.validation_errors().'</div>'; 
echo "</td></tr>";
echo "<tr><td>";
echo "Banned Name</td><td>"; 
echo form_input(array('name'=>'ban_word','id'=>'ban_word','maxlength'=>50 ,'value'=>$category['ban_word'])) ."</td></tr>";



echo '<tr><td><br>';


echo form_submit(array('name' => 'btnEdit', 'id' => 'btnEdit','class'=>'inputbuttons','content' => 'edit'), 'Submit');
echo '&nbsp;';
echo form_button(array('name'=>'btnCancel','class'=>'inputbuttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'index.php/webmaster/blog_banned_words/banned_words_list'."'"));
echo "</td></tr>";
echo form_hidden('action','submit');
echo form_hidden('ban_id',$category['ban_id']);
echo form_close();
echo "</table>";
?>
</div>