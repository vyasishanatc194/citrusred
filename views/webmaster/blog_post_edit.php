<!-- TinyMCE -->
<script type="text/javascript" src="<?php echo base_url();?>webappassets/tiny_mce/tiny_mce.js?v=6-20-13"></script>
<script type="text/javascript">
	tinyMCE.init({	mode : "exact",	theme : "advanced",elements : "desc, ajaxfilemanager",
			 file_browser_callback : "ajaxfilemanager",
			plugins : "advimage,advlink,media,contextmenu" ,
			relative_urls : false,
			remove_script_host : false,
			convert_urls : true			
			//document_base_url : 'c:/xampp/http://localhost/asset/uploads/'
			});


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
<div class="tblheading">Create/Modify Article</div>
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
$desc=str_replace("../http", "http", $category['desc']);
$desc=str_replace("../../../../asset", base_url()."asset", $desc);
$desc=str_replace("../../../asset", base_url()."asset", $desc);


echo form_open('webmaster/blog_listing_post/post_edit/'.$cat_id.'/'.$category['id'], array('id' => 'frmCategoryEdit'));
echo '<table class="tbl_forms"><tr><td >';
echo '<div style="color:#FF0000;">'.validation_errors().'</div>';
echo "</td></tr>";
echo "<tr><td>";
echo "Title</td><td>";
echo form_input(array('name'=>'title','id'=>'title','maxlength'=>250 ,'value'=>$category['title'], 'style' =>'width:800px;')) ."</td></tr>";
echo "<tr><td>Description</td><td>";
echo form_textarea(array('name'=>'desc','id'=>'desc','style' =>'width:800px;height:400px;', 'value'=>$desc)) ."</td></tr>";

echo "<tr><td>Meta Keywords</td><td>";
echo form_textarea(array('name'=>'meta_keywords','id'=>'meta_keywords','style' =>'width:300px;height:100px;', 'value'=>$category['meta_keywords'])) ."</td></tr>";

echo "<tr><td>Meta Description</td><td>";
echo form_textarea(array('name'=>'meta_description','id'=>'meta_description','style' =>'width:300px;height:100px;', 'value'=>$category['meta_description'])) ."</td></tr>";

echo "<tr><td>Status</td><td>";
echo form_dropdown('status',array('1'=>'Active','0'=>'Inactive'),$category['status']);
echo "</td></tr>";
echo '<tr><td><br>';
echo form_submit(array('name' => 'btnEdit', 'id' => 'btnEdit','class'=>'inputbuttons','content' => 'edit'), 'Submit');
echo '&nbsp;';
echo form_button(array('name'=>'btnCancel','class'=>'inputbuttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'index.php/webmaster/blog_listing_post/post_list/'.$cat_id."'"));
echo "</td></tr>";
echo form_hidden('action','submit');
echo form_hidden('id',$category['id']);
echo form_hidden('cat_id',$cat_id);
echo form_hidden('added_by',$_SESSION['admin_id']);
/* echo form_hidden('added_on',date('Y-m-d')); */
echo form_close();
echo "</table>";
?>
</div>
