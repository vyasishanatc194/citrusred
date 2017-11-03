<!-- TinyMCE -->
<script type="text/javascript" src="<?php echo base_url();?>webappassets/tiny_mce/tiny_mce.js?v=6-20-13"></script>
<script type="text/javascript">
	tinyMCE.init({	mode : "exact",elements : "content",theme : "advanced",width:"800",height:"500" });
</script>
<div class="tblheading">Edit Page</div>
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
echo form_open('webmaster/cms/page_edit/'.$page['id'], array('id' => 'frmPageEdit'));
echo '<table class="tbl_forms"><tr><td >';
echo '<div style="color:#FF0000;">'.validation_errors().'</div>';
echo "</td></td>";
echo "<tr><td>";
echo "Page<br/>";
echo form_input(array('name'=>'page','id'=>'page','value'=>$page['page'])) ."</td>";

echo "<td >Title<br/>";
echo form_textarea(array('name'=>'title','id'=>'title','value'=>$page['title'],'style'=>'height:30px !important;' )) ."</td>";

echo "<td >Keyword<br/>";
echo form_textarea(array('name'=>'keyword','id'=>'keyword','cols'=>20,'rows'=>10,'value'=>$page['keyword'],'style'=>'height:30px !important;')) ."</td></tr>";

echo "<tr><td >Description<br/>";
echo form_textarea(array('name'=>'description','id'=>'description','value'=>$page['description'],'style'=>'height:30px !important;' )) ."</td>";

echo "<td>H1<br/>";
echo form_textarea(array('name'=>'h1','id'=>'h1','value'=>$page['h1'],'style'=>'height:30px !important;')) ."</td>";
echo "</td></tr>";
echo "<tr><td colspan='3'>Content<br/>";
echo form_textarea(array('name'=>'content','id'=>'content','value'=>$page['content'],'style'=>'width:200px !important'));
echo "</td></tr>";
echo '<tr><td><br>';
echo form_submit(array('name' => 'btnEdit', 'id' => 'btnEdit','class'=>'inputbuttons','content' => 'edit'), 'Submit');
echo '&nbsp;';
echo form_button(array('name'=>'btnCancel','class'=>'inputbuttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'webmaster/cms/page_list'."'"));
echo "</td></tr>";
echo form_hidden('action','submit');
echo form_hidden('id',$page['id']);
echo form_close();
echo "</table>";
?>
</div>
