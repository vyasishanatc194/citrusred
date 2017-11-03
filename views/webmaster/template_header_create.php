<div class="tblheading">Create New Template Header</div>
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

echo form_open_multipart('webmaster/template_header/header_create/', array('id' => 'frmCategoryCreate'));
echo '<table class="tbl_forms"><tr><td >';

echo '<div style="color:#FF0000;">'.validation_errors().'</div>'; 
echo "</td></td>";
echo "<tr><td>";
echo "Listing Category Title</td><td>"; 
echo form_input(array('name'=>'template_name','id'=>'template_name','maxlength'=>50 ,'value'=>$header['template_name'])) ."</td></tr>";
echo "<tr><td>";
echo "Header Screenshot</td><td>"; 
echo "<input type=\"file\" name=\"screenshot\" size=\"20\" /></td></tr>";
echo "<tr><td>";
echo "Header Category</td><td>"; 
echo "<select name='template_theme_id[]' multiple style='height:127px;'>";
foreach($categories as $category){
	if($category['red_theme_id']==$header['template_theme_id']){
		$select="selected='selected'";
	}else{
		$select="";
	}
	echo "<option value='".$category['red_theme_id']."' ".$select.">".$category['red_theme_name']."</option>";
}
echo "</select>";
echo "</td></tr>";
echo "<tr><td>Status</td><td>"; 
echo form_dropdown('is_active',array('1'=>'Active','0'=>'Inactive'),$header['is_active']);
echo "</td></tr>";

echo '<tr><td colspan="2"><br>';

echo form_submit(array('name' => 'btnEdit', 'id' => 'btnEdit','class'=>'inputbuttons','content' => 'edit'), 'Submit');
echo '&nbsp;';
echo form_button(array('name'=>'btnCancel','class'=>'inputbuttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'webmaster/template_header/template_header_list'."'"));
echo "</td></tr>";
echo form_hidden('action','submit');
echo form_close();
echo "</table>";
?>
</div>