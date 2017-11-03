<div class="tblheading">Create New Support Category</div>
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
echo form_open('webmaster/support_category/support_category_create/', array('id' => 'frmCategoryCreate'));
echo '<table class="tbl_forms"><tr><td >';
echo '<div style="color:#FF0000;">'.validation_errors().'</div>'; 
echo "</td></td>";
echo "<tr><td>";
echo "Support Category Title</td><td>"; 
echo form_input(array('name'=>'category','id'=>'category','maxlength'=>50 ,'value'=>$category['category'])) ."</td></tr>";
echo "<tr><td>Status</td><td>"; 
echo form_dropdown('is_active',array('1'=>'Active','0'=>'Inactive'),$category['is_active']);
echo "</td></tr>";
echo '<tr><td colspan="2"><br>';
echo form_submit(array('name' => 'btnEdit', 'id' => 'btnEdit','class'=>'inputbuttons','content' => 'edit'), 'Submit');
echo '&nbsp;';
echo form_button(array('name'=>'btnCancel','class'=>'inputbuttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'webmaster/support_category/support_category_list'."'"));
echo "</td></tr>";
echo form_hidden('action','submit');
echo form_close();
echo "</table>";
?>
</div>