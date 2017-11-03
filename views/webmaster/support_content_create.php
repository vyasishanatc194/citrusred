<!-- TinyMCE -->
<script type="text/javascript" src="<?php echo base_url();?>webappassets/tiny_mce/tiny_mce.js?v=6-20-13"></script>
<script type="text/javascript">


	tinyMCE.init({	mode : "textareas",	theme : "advanced",elements : "ajaxfilemanager",
			 file_browser_callback : "ajaxfilemanager",
			plugins : "advimage,advlink,media,contextmenu",
			relative_urls : false,
			remove_script_host : false,
			convert_urls : true });

	function ajaxfilemanager(field_name, url, type, win) {
			var ajaxfilemanagerurl = "<?php echo base_url();?>webappassets/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php";
			var view = 'detail';
			switch (type) {
				case "image":
				view = 'thumbnail';
					break;
				case "media":
					break;
				case "flash":
					break;
				case "file":
					break;
				default:
					return false;
			}
            tinyMCE.activeEditor.windowManager.open({
                url: "<?php echo base_url();?>webappassets/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php?view=" + view,
                width: 782,
                height: 440,
                inline : "yes",
                close_previous : "no"
            },{
                window : win,
                input : field_name
            });
		}
</script>
<!-- /TinyMCE -->
<div class="tblheading">Create New Support Content</div>
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

echo form_open_multipart('webmaster/support_content/support_content_create/', array('id' => 'frmContentCreate'));
echo '<table class="tbl_forms"><tr><td >';

echo '<div style="color:#FF0000;">'.validation_errors().'</div>';
echo "</td></td>";
echo "<tr><td>";
echo "Product</td><td>";
echo form_input(array('name'=>'product','id'=>'product','maxlength'=>255 ,'value'=>$content['product'])) ."</td></tr>";
echo "<tr><td>";
echo "Contnet</td><td>";
echo form_textarea(array('name'=>'description','id'=>'description','style' =>'width:800px;height:400px;', 'value'=>$content['description']));
echo "</td></tr>";
echo "<tr><td>";
echo "Support Category</td><td>";
echo "<select name='category_id'>";
foreach($categories as $category){
	if($category['id']==$content['category_id']){
		$select="selected='selected'";
	}else{
		$select="";
	}
	echo "<option value='".$category['id']."' ".$select.">".$category['category']."</option>";
}
echo "</select>";
echo "</td></tr>";
echo "<tr><td>Status</td><td>";
echo form_dropdown('is_active',array('1'=>'Active','0'=>'Inactive'),$content['is_active']);
echo "</td></tr>";
echo '<tr><td><br>';
echo form_submit(array('name' => 'btnEdit', 'id' => 'btnEdit','class'=>'inputbuttons','content' => 'edit'), 'Submit');
echo '&nbsp;';
echo form_button(array('name'=>'btnCancel','class'=>'inputbuttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'webmaster/support_content/support_content_list'."'"));
echo "</td></tr>";
echo form_hidden('action','submit');
echo form_close();
echo "</table>";
?>
</div>
