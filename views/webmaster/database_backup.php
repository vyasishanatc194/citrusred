<script type="text/javascript">
	function downloadDatabase(){
		var confirm_submit=confirm("Do you want to download Redcappi database");
		if (confirm_submit)
		{
			document.frmDatabaseBackup.submit();
		}
	}
</script>
<div class="tblheading">Database Backup</div>
<?php
echo form_open('webmaster/database_backup/', array('id' => 'frmDatabaseBackup','name'=>'frmDatabaseBackup','onsubmit'=>'downloadDatabase(); return false'));
echo '<table class="tbl_forms">';  

echo "<tr><td colspan='2' valign='top'><div style=\"color:#FF0000;\">".validation_errors()."</div></td></tr>"; 

echo "<tr><td colspan='2' valign='top'><div id=\"messages\" style=\"color:#FF0000;\">";

/// display all messages

if (is_array($messages)):
    foreach ($messages as $type => $msgs):
        foreach ($msgs as $message):
            echo ('<span class="' .  $type .'">' . $message . '</span>');
        endforeach;
    endforeach;
endif;

echo "</div></td></tr>"; 

echo "<tr><td valign='top'></td><td>"; 
echo form_submit(array('name' => 'btnDatabaseBackup', 'id' => 'btnDatabaseBackup','class'=>'inputbuttons','content' => 'Database Backup'), 'Database Backup');

echo form_hidden('action','submit');
echo form_close();

echo '</td></tr></table>';
?>
</center>