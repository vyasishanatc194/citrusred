<?php include_once("seo_header.php");?>
<table width="100%" border="0" cellspacing="0" cellpadding="0"> 
        <tr> 
          
          <td id="rightside">
<div class="tblheading">Create seo</div>
<div id="messages">
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
echo form_open('seo/create_seo/', array('id' => 'frmSeoCreate'));
echo '<table class="tbl_forms"><tr><td>';
echo '<div style="color:#FF0000;">'.validation_errors().'</div>'; 
echo "</td></td>";
echo "<tr><td>";
echo "Page<br/>"; 
echo form_input(array('name'=>'page','id'=>'page','value'=>$seo['page'])) ."</td>";
echo "<td >Title<br/>"; 
echo form_textarea(array('name'=>'title','id'=>'title','value'=>$seo['title'],'style'=>'height:30px !important;' )) ."</td>";
echo "<td >Keyword<br/>"; 
echo form_textarea(array('name'=>'keyword','id'=>'keyword','cols'=>20,'rows'=>10,'value'=>$seo['keyword'],'style'=>'height:30px !important;')) ."</td></tr>";
echo "<tr><td >Description<br/>"; 
echo form_textarea(array('name'=>'description','id'=>'description','value'=>$seo['description'],'style'=>'height:30px !important;' )) ."</td>"; 
echo "<td>H1<br/>"; 
echo form_textarea(array('name'=>'h1','id'=>'h1','value'=>$seo['h1'],'style'=>'height:30px !important;')) ."</td>";
echo "</td></tr>";
echo '<tr><td><br>';
echo form_submit(array('name' => 'btnCreate', 'id' => 'btnCreate','class'=>'inputbuttons','content' => 'Create'), 'Submit');
echo '&nbsp;';
echo form_button(array('name'=>'btnCancel','class'=>'inputbuttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'seo/seo_list'."'"));
echo "</td></tr>";
echo form_hidden('action','submit');
echo form_close();
echo "</table>";
?>
</div>
</td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td id="footer"><table width="100%" >
        <tr>
          <td class="txt_copyright"> &copy; 2011 . All rights reserved.</td>
          <td>&nbsp;</td>
        </tr>
      </table></td>
  </tr>
</table>
</body>
</html>