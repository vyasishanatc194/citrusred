<div class="tblheading">Edit Template Category</div>
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


echo form_open('webmaster/template_category/category_edit/'.$category['red_theme_id'], array('id' => 'frmCategoryEdit'));
echo '<table class="tbl_forms"><tr><td >';

echo '<div style="color:#FF0000;">'.validation_errors().'</div>'; 
echo "</td></td>";
echo "<tr><td>";
echo "Listing Category Title</td><td>"; 
echo form_input(array('name'=>'red_theme_name','id'=>'red_theme_name','maxlength'=>50 ,'value'=>$category['red_theme_name'])) ."</td></tr>";

echo "<tr><td>Status</td><td>"; 
echo form_dropdown('red_is_active',array('1'=>'Active','0'=>'Inactive'),$category['red_is_active']);
echo "</td></tr>";

echo '<tr><td><br>';


echo form_submit(array('name' => 'btnEdit', 'id' => 'btnEdit','class'=>'inputbuttons','content' => 'edit'), 'Submit');
echo '&nbsp;';
echo form_button(array('name'=>'btnCancel','class'=>'inputbuttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'webmaster/template_category/template_category_list'."'"));
echo "</td></tr>";
echo form_hidden('action','submit');
echo form_hidden('red_theme_id',$category['red_theme_id']);
echo form_close();
echo "</table>";
?>
</div>