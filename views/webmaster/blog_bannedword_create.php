<div class="tblheading">Create New Listing Category</div>
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

echo form_open('webmaster/blog_banned_words/banned_create/', array('ban_id' => 'frmBannedWordsCreate'));
echo '<table class="tbl_forms"><tr><td >';

echo '<div style="color:#FF0000;">'.validation_errors().'</div>'; 
echo "</td></td>";
echo "<tr><td>";
echo " Banned Word</td><td>"; 
echo form_input(array('name'=>'ban_word','id'=>'ban_word','maxlength'=>50 ,'value'=>$category['ban_word'])) ."</td></tr>";

echo '<tr><td colspan="2"><br>';

echo form_submit(array('name' => 'btnEdit', 'id' => 'btnEdit','class'=>'inputbuttons','content' => 'edit'), 'Submit');
echo '&nbsp;';
echo form_button(array('name'=>'btnCancel','class'=>'inputbuttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'webmaster/blog_banned_words/banned_words_list'."'"));
echo "</td></tr>";
echo form_hidden('action','submit');
echo form_hidden('ban_id',$category['ban_id']);
echo form_close();
echo "</table>";
?>
</div>