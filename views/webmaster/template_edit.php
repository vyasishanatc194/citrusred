<div class="tblheading">Edit Template</div>
<div class="form">
<?php 
//Display validation errors
//Create form 
echo form_open('webmaster/templates_manage/template_edit/'.$template_data['template_id'], array('id' => 'form_template_create'));
echo '<table class="tbl_forms">';
echo "<tr><td>";
echo validation_errors('<div class="validation_error" style="color:#FF0000;">', '</div>'); 
echo "</td></td>";

//Create textbox for template title
echo '<tr><td>Template Title</td><td>'; 
echo form_input(array('name'=>'template_title','id'=>'template_title','maxlength'=>250,'size'=>65,'value'=>$template_data['template_title'] ));
echo '</td></tr>';

//Create FCK editor for template content
echo '<tr><td valign="top">Template Content</td><td >'; 
//echo form_textarea(array('name'=>'template_content','id'=>'template_content','rows'=>10,'cols'=>50,'value'=>$template_data['template_content'] ));

$this->fckeditor->BasePath = base_url() . 'ci_system/plugins/fckeditor/';
$this->fckeditor->Value =$template_data['template_content'];
$this->fckeditor->Height=400 ;
$this->fckeditor->Width=800 ;
$this->fckeditor->Create() ;
echo '</td></tr>';

//Create dropdown for template status
echo '<tr><td valign="top">Template Status</td><td >'; 
echo form_dropdown('template_status',array(''=>'select one','1'=>'Active','0'=>'Inactive'),$template_data['template_status']);
echo '</td></tr>';

echo '<tr><td valign="top"></td><td ></td></tr>'; 

echo '<tr><td valign="top"></td><td >'; 
//Create submit button to save template
echo form_submit(array('name'=>'template_submit','value'=>'Submit','class'=>'inputbuttons'));
echo '&nbsp';
//Create cancel button
echo form_button(array('name'=>'template_cancel','class'=>'inputbuttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'webmaster/templates_manage/templates_list'."'"));
echo '</td></tr>';

//Create hidden field with name 'action' and template ID
echo form_hidden('action','save');
echo form_hidden('template_id',$template_data['template_id']);

//Close the form
echo form_close();

?>
</div>