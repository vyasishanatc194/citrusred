<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-1.5.1.min.js?v=6-20-13"></script>
<script type="text/javascript">
	$(document).ready(function() {
		jQuery('#add_number_of_contacts').live('click',function(){
			if ($(this).attr('checked')) {
				$('#all').attr('checked', false);
				$('#number_of_contacts').show();
			}else{
				$('#all').attr('checked', true);
				$('#number_of_contacts').hide();
			}
		});
		
		jQuery('#automate').live('click',function(){
			if ($(this).attr('checked')) {				
				$('#segment_interval').show();
			}else{				
				$('#segment_interval').hide();
			}
		});
		jQuery('#all').live('click',function(){
			if ($(this).attr('checked')) {
				$('#add_number_of_contacts').attr('checked', false);
				$('#number_of_contacts').hide();
				$('#segment_interval').hide();
			}else{
				$('#add_number_of_contacts').attr('checked', true);
				$('#number_of_contacts').show();
				$('#segment_interval').show();
			}
		});
		jQuery('#btnAddNote').live('click',function(){
			var approval_notes = $('#approval_notes').val();
			var member_id = $('#member_id').val();
			jQuery.ajax({
			  url: "<?php echo base_url() ?>webmaster/campaign/saveNoteOnly/",
			  type:"POST",
			  data: 'approval_notes='+approval_notes+'&member_id='+member_id,
			  success: function(data) {
				alert(data);
			  }
			});				 
		});
		jQuery('#btnAttachMsg').live('click',function(){
			var message_id = $('#message_id :selected').val();
			var member_id = $('#member_id').val();
			var cid = $('#campaign_id').val();			
			$("form#frmCampaignSegmentation").attr("action", "/webmaster/campaign/attachMessage/"+cid+'/'+member_id+'/'+message_id);
			$('form#frmCampaignSegmentation').submit();			
		});
		
	});
	
</script>
<div class="tblheading">Campaign Segmentation</div>
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
<?php
echo '<div style="color:#FF0000;">'.validation_errors().'</div>';
echo form_open('webmaster/campaign/campaign_segmentation/'.$campaign_id.'/'.$member_id.'/'.$mode, array('id' => 'frmCampaignSegmentation'));
echo '<table class="tbl_forms">';

 
echo "<tr><td>Approval Note:<br/> ".form_textarea(array('name'=>'approval_notes','id'=>'approval_notes' ,'value'=>trim($approval_notes),'rows'=>3, 'style'=>'width:500px; height:20px;'));
echo "<br/><span style='margin-left:50px;'> <input name='btnAddNote' id='btnAddNote' type='button' value='Add/Update note only' /></span>";
echo  "</td>";
echo "<td>Assign Message to Memeber:<br/> <select name='message_id' id='message_id'><option value=''>--select--</option>";

foreach($message_list as $msg_rec){
echo "<option value=\"{$msg_rec['message_id']}\">{$msg_rec['message_name']}</option>";

}


echo "</select>
	<br/><span style='margin-left:50px;'> <input name='btnAttachMsg' id='btnAttachMsg' type='button' value='Attach message' /></span>";
echo  "</td></tr>";



$arrSendToChk = array('name'=>'all','id'=>'all' ,'value'=>'All','style'=>'width:50px;');

$arrSegmentSizeChk = array('name'=>'add_number_of_contacts','id'=>'add_number_of_contacts','value'=>'1','style'=>'width:50px;');
$arrSegmentSizeText = array('name'=>'number_of_contacts','id'=>'number_of_contacts');

$arrAutomateItChk = array('name'=>'automate','id'=>'automate' ,'value'=>'automate','style'=>'width:50px;');
$arrAutomateItText = array('name'=>'segment_interval','id'=>'segment_interval');

if(!$is_segmented){
	$arrSendToChk['checked']= 'checked';
	$arrSegmentSizeText['style']= 'width:100px;display:none';
	$arrAutomateItText['value'] = 30;
	$arrAutomateItText['style'] = 'width:100px; display:none';
}else{
	$arrSegmentSizeChk['checked']= 'checked';
	$arrSegmentSizeText['style']= 'width:100px;';
	$arrSegmentSizeText['value']= $segment_size;
	$arrAutomateItChk['checked']= 'checked';
	$arrAutomateItText['value'] = $segment_interval;
	$arrAutomateItText['style'] = 'width:100px;';
}	
echo "<tr><td style='width:50%;'>";
echo '<table class="tbl_forms">';
echo "<tr><td>Send Campaign To:<br/> ".form_checkbox($arrSendToChk) ." All (Total $subscriber_count Contacts)</td></tr>";
echo "<tr><td>OR<br/></td></tr>";
echo "<tr><td>Add Number Of Contacts (Total $subscriber_count Contacts): ";
echo form_checkbox($arrSegmentSizeChk);
echo form_input($arrSegmentSizeText);
echo "</td></tr>";
echo "<tr><td>Automate It: ".form_checkbox($arrAutomateItChk) ;
echo form_input($arrAutomateItText)."<span style='display:inline;font-size:9px;'>Put segment-interval in minutes.</span></td></tr>";
echo "<tr><td><hr /></td></tr>";
echo "<tr><td>Add initial delay: ".form_input( array('name'=>'add_delay','id'=>'add_delay','value'=>$campaign_data['campaign_delay_minute'],'style'=>'width:50px;') ) ." minutes</td></tr>";
echo "</table></td>";

 echo "<td style='width:50%;'>";
echo '<table class="tbl_forms">';
echo "<tr><td><b>Subject: </b>".$campaign_data['email_subject']."</td></tr>";
echo "<tr><td><b>From Name: </b>".$campaign_data['sender_name']."</td></tr>";
echo "<tr><td><b>From Email: </b>".$campaign_data['sender_email']."</td></tr>";
echo "<tr><td>&nbsp;</td></tr>";
echo "<tr><td><b>Sent: </b>".$campaign_data['sent']."</td></tr>";
echo "<tr><td><b>Unsent-yet: </b>".$campaign_data['unsent']."</td></tr>";
echo "</table></td>";
echo"</tr>";

echo "<tr><td colspan='2'><hr/><br/>";
echo form_submit(array('name' => 'btnEdit', 'id' => 'btnEdit','class'=>'inputbuttons1','content' => 'edit'), 'Submit');
echo '&nbsp;';
echo " <input name='btnCancel' id='btnCancel' type='button' value='Cancel' content='Cancel' onclick='window.location.href=\"".base_url()."webmaster/campaign/ongoing/\"'  />";
echo "</td></tr><tr><td colspan='2'>";
echo "<input name='action' id='action' type='hidden' value='submit' />"; 
echo "<input name='member_id' id='member_id' type='hidden' value='$member_id' />"; 
echo "<input name='campaign_id' id='campaign_id' type='hidden' value='$campaign_id' />";  
echo "</td></tr>";
echo "</table>";
echo form_close();
?>
<table class="tbl_forms">
<tr><td>
<div id="pmSpam">
<b>Score:</b><?php echo $campaign_data['spamscore'];?>
<br/>
<br/>
<b>Report:</b><br/>
<?php echo $campaign_data['spamreport'];?>
</div>
 
</td><td>
<P ALIGN='center'><IFRAME SRC='<?php echo base_url();?>newsletter/campaign_preview/campaign_view/<?php echo $campaign_id;?>'   title="<?php echo $campaign_data['email_subject'];?>" WIDTH='700' HEIGHT='600' FRAMEBORDER='0' SCROLLING='auto'></IFRAME></P></td></tr>
</table>
</div>
