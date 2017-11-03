<script type="text/javascript">
function saveIt(cid){
	var cval = $('#'+cid).val();
	$.post( 
                  "/webmaster/sitesetting_manage/ajaxSaveSetting",
                  { "cname": cid, "cval" : cval },
                  function(data) {
                     //$('#stage').html(data);
                     alert(data);
                  }
               );	
}
function showQueue(){	
	$.get( "/webmaster/sitesetting_manage/ajaxShowQueue",'',
                  function(data) {
                     $('#div_queue').html(data);                     
                  }
               );	
}
function showRunningCron(){	
	$.get( "/webmaster/sitesetting_manage/showRunningPS",'',
                  function(data) {
                     $('#div_cron').html(data);                     
                  }
               );	
}
</script>
<div class="tblheading">Cron Setting</div>
<?php
echo form_open('webmaster/sitesetting_manage/cron_setting/', array('id' => 'frmCronSetting'));
echo '<table class="tbl_forms">';  
echo "<tr><td colspan='2' valign='top'><div id='messages' style='color:#FF0000;'>";

/// display all messages

if (is_array($messages)):
    foreach ($messages as $type => $msgs):
        foreach ($msgs as $message):
            //echo ('<span class="' .  $type .'">' . $message . '</span>');
        endforeach;
    endforeach;
endif;
echo "</div></td></tr>";

echo "<tr><td valign='top' colspan='2'>
<fieldset style='width:60%'><legend>Maintenance mode:</legend>    
	<label style='display:inline-block;width:40%; border:0px solid #ccc;'>Maintenance mode:</label> ".form_dropdown('maintenance_mode',array('no'=>'NO','yes'=>'YES'),$maintenance_mode, "id='maintenance_mode'")." &nbsp; <a href='javascript:void(0);' onclick='javascript:saveIt(\"maintenance_mode\");' >Update</a>  
</fieldset>
</td></tr>";


echo "<tr><td valign='top' colspan='2'>
<fieldset style='width:60%'><legend>Send Campaign Cron:</legend>
    
	<label style='display:inline-block;width:40%; border:0px solid #ccc;'>Stop/Continue send-campaign-cronjob:</label> ".form_dropdown('continue_campaign_send',array('1'=>'Active','0'=>'Stop'),$continue_campaign_send, "id='continue_campaign_send'")." &nbsp; <a href='javascript:void(0);' onclick='javascript:saveIt(\"continue_campaign_send\");' >Update</a><br/><span style='color:#999; font-size:10px; text-decoration:italics;'> (Default is Active (working). To stop change to \"Stop\")</span>
	
	<hr style='border-top: dotted 1px;' />
	
	<label style='display:inline-block;width:40%; border:0px solid #ccc;'>Send-campaign status:</label> ".form_dropdown('cronjob_status',array('completed'=>'Completed','working'=>'Working'),$cronjob_status, "id='cronjob_status'")." &nbsp; <a href='javascript:void(0);' onclick='javascript:saveIt(\"cronjob_status\");' >Update</a><br/><span style='color:red; font-size:9px;'>".date('F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($campaign_cron_status_change_time, date_default_timezone_get() )))."</span><br/><span style='color:#999; font-size:10px; text-decoration:italics;'>(Default is Active (working). To stop change to \"Stop\")</span>
	
	
  
</fieldset>
</td></tr>";


 
echo "<tr><td valign='top' colspan='2'>

<fieldset style='width:60%;margin-top:40px;'><legend>Import PMTALog Cronjob:</legend>
    
	<label style='display:inline-block;width:40%; border:0px solid #eee;'>Continue pmta-log import:</label> ".form_dropdown('continue_pmtalog_import',array('1'=>'Active','0'=>'Stop'),$continue_pmtalog_import, "id='continue_pmtalog_import'")." &nbsp; <a href='javascript:void(0);' onclick='javascript:saveIt(\"continue_pmtalog_import\");' >Update</a><br/> <span style='color:#999; font-size:10px; text-decoration:italics;'>(Default is Active (working). To stop change to \"Stop\")</span>
	
	<hr style='border-top: dotted 1px;' />
	
	<label style='display:inline-block;width:40%; border:0px solid #eee;'>PMTAlog-import <span style='color:#999; font-size:10px; text-decoration:italics;'>(Even file account/delivered) status</span>:</label> ".form_dropdown('pmtalog_import_acct_even',array('completed'=>'Completed','working'=>'Working'),$pmtalog_import_acct_even, "id='pmtalog_import_acct_even'")." &nbsp; <a href='javascript:void(0);' onclick='javascript:saveIt(\"pmtalog_import_acct_even\");' >Update</a><br/> <span style='color:red; font-size:9px;'>".date('F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($pmtalog_import_acct_even_change_time, date_default_timezone_get() )))."</span><br/>
	<label style='display:inline-block;width:40%; border:0px solid #eee;'>PMTAlog-import<span style='color:#999; font-size:10px; text-decoration:italics;'> (Odd file account/delivered) status</span>:</label> ".form_dropdown('pmtalog_import_acct_odd',array('completed'=>'Completed','working'=>'Working'),$pmtalog_import_acct_odd, "id='pmtalog_import_acct_odd'")." &nbsp; <a href='javascript:void(0);' onclick='javascript:saveIt(\"pmtalog_import_acct_odd\");' >Update</a><br/> <span style='color:red; font-size:9px;'>".date('F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($pmtalog_import_acct_odd_change_time, date_default_timezone_get() )))."</span>

	<hr style='border-top: dotted 1px;' />

	<label style='display:inline-block;width:40%; border:0px solid #eee;'>PMTAlog-import<span style='color:#999; font-size:10px; text-decoration:italics;'> (fbl) status</span>:</label> ".form_dropdown('pmtalog_import_fbl',array('completed'=>'Completed','working'=>'Working'),$pmtalog_import_fbl, "id='pmtalog_import_fbl'")." &nbsp; <a href='javascript:void(0);' onclick='javascript:saveIt(\"pmtalog_import_fbl\");' >Update</a><br/> <span style='color:red; font-size:9px;'>".date('F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($pmtalog_import_fbl_change_time, date_default_timezone_get() )))."</span>
	

	<hr style='border-top: dotted 1px;' />

	<label style='display:inline-block;width:40%; border:0px solid #eee;'>PMTAlog-import<span style='color:#999; font-size:10px; text-decoration:italics;'> (bounced) status</span>:</label> ".form_dropdown('pmtalog_import_bounced',array('completed'=>'Completed','working'=>'Working'),$pmtalog_import_bounced, "id='pmtalog_import_bounced'")." &nbsp; <a href='javascript:void(0);' onclick='javascript:saveIt(\"pmtalog_import_bounced\");' >Update</a><br/> <span style='color:red; font-size:9px;'>".date('F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($pmtalog_import_bounced_change_time, date_default_timezone_get() )))."</span>
	
  
</fieldset>
</td></tr>";

echo "<tr><td valign='top' width='40%'>Continue send-autoresponder<br/>
<span style='color:#999; font-size:10px; text-decoration:italics;'>(Default is Active (working). To stop change to \"Stop\")</span></td><td>"; 
echo form_dropdown('continue_autoresponder_send',array('1'=>'Active','0'=>'Stop'),$continue_autoresponder_send, "id='continue_autoresponder_send'")." &nbsp; <a href='javascript:void(0);' onclick='javascript:saveIt(\"continue_autoresponder_send\");' >Update</a> ";
echo '</td></tr>';

echo "<tr><td valign='top' width='40%'>Continue signu-up form<br/>
<span style='color:#999; font-size:10px; text-decoration:italics;'>(Default is Active (working). To stop change to \"Stop\")</span></td><td>"; 
echo form_dropdown('continue_singup_form',array('1'=>'Active','0'=>'Stop'),$continue_singup_form, "id='continue_singup_form'")." &nbsp; <a href='javascript:void(0);' onclick='javascript:saveIt(\"continue_singup_form\");' >Update</a> ";
echo '</td></tr>';

echo "<tr><td valign='top' width='40%'>Queueing cron status<br/> 
<span style='color:#999; font-size:10px; text-decoration:italics;'>(Default is Active (working). To stop change to \"Stop\")</span></td><td>"; 
echo form_dropdown('queueing_cron',array('1'=>'Working','0'=>'Stopped'),$queueing_cron, "id='queueing_cron'")." &nbsp; <a href='javascript:void(0);' onclick='javascript:saveIt(\"queueing_cron\");' >Update</a><br/> <span style='color:red; font-size:9px;'>".date('F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($queueing_start_date, date_default_timezone_get() ))). '</span></td></tr>';

echo "<tr><td valign='top' width='100%' colspan='2'>  &nbsp; <a href='javascript:void(0);' onclick='javascript:showQueue();' >Show Campaign Queue</a></td></tr>";
echo "<tr><td valign='top' width='100%' colspan='2'><div id='div_queue'></div></td></tr>";


echo "<tr><td valign='top' width='100%' colspan='2'>  &nbsp; <a href='javascript:void(0);' onclick='javascript:showRunningCron();' >Show Running Cronjobs</a></td></tr>";
echo "<tr><td valign='top' width='100%' colspan='2'><div id='div_cron'></div></td></tr>";



echo '</table>';

 
?>
</center>