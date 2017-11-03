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
echo form_open('webmaster/campaign/campaign_segmentation/'.$campaign_id.'/'.$member_id.'/'.$mode, array('id' => 'frmCampaignSegmentation'));
echo '<table class="tbl_forms"><tr><td colspan="2">';
echo '<div style="color:#FF0000;">'.validation_errors().'</div>';
echo "</td></tr>";

echo "<tr><td>";
echo '<table class="tbl_forms">';
if(!$is_segmented){
echo "<tr><td>Send Campaign To: ".form_checkbox(array('name'=>'all','id'=>'all' ,'value'=>'All','style'=>'width:50px;','checked'=>'checked')) ." All</td></tr>";
echo "<tr><td>OR<br/></td></tr>";

echo "<tr><td>Add Number Of Contacts (Total $subscriber_count Contacts): ";
echo form_checkbox(array('name'=>'add_number_of_contacts','id'=>'add_number_of_contacts','value'=>'1','style'=>'width:50px;')) .
form_input(array('name'=>'number_of_contacts','id'=>'number_of_contacts','style'=>'display:none'));
echo "</td></tr>";

echo "<tr><td>Automate It: ".form_checkbox(array('name'=>'automate','id'=>'automate' ,'value'=>'automate','style'=>'width:50px;')) .
form_input(array('name'=>'segment_interval','id'=>'segment_interval','value'=>30, 'style'=>'width:100px; display:none')).
"</td></tr>";
echo "<tr><td>Put segment-interval in minutes.<br/></td></tr>";
echo "<tr><td>Add initial delay:".form_checkbox(array('name'=>'add_delay','id'=>'add_delay' ,'value'=>'1','style'=>'width:50px;')) ."<br/></td></tr>";
}else{
echo "<tr><td>Send Campaign To:<br/> ".form_checkbox(array('name'=>'all','id'=>'all' ,'value'=>'All','style'=>'width:50px;')) ." All</td></tr>";
echo "<tr><td>OR<br/></td></tr>";

echo "<tr><td>Add Number Of Contacts (Total $subscriber_count Contacts): ";
echo form_checkbox(array('name'=>'add_number_of_contacts','id'=>'add_number_of_contacts','value'=>'1','style'=>'width:50px;','checked'=>'checked')) .
form_input(array('name'=>'number_of_contacts','id'=>'number_of_contacts','value'=>$segment_size));
echo "</td></tr>";

echo "<tr><td>Automate It: ".form_checkbox(array('name'=>'automate','id'=>'automate' ,'value'=>'automate','style'=>'width:50px;','checked'=>'checked')) .
form_input(array('name'=>'segment_interval','id'=>'segment_interval','value'=>$segment_interval, 'style'=>'width:100px;')).
"</td></tr>";
echo "<tr><td>Put segment-interval in minutes.<br/></td></tr>";
echo "<tr><td>Add initial delay:".form_checkbox(array('name'=>'add_delay','id'=>'add_delay' ,'value'=>'1','style'=>'width:50px;')) ."<br/></td></tr>";
}
echo "<tr><td><hr/></td><tr>";
echo "<tr><td><b>Subject: </b>".$campaign_data['email_subject']."</td></tr>";
echo "<tr><td><b>From Name: </b>".$campaign_data['sender_name']."</td></tr>";
echo "<tr><td><b>From Email: </b>".$campaign_data['sender_email']."</td></tr>";
echo "<tr><td>&nbsp;</td></tr>";
echo "</table></td>";

echo "<td>";
echo '<table class="tbl_forms">';
echo "<tr><td>Approval Note:<br/> ".form_textarea(array('name'=>'approval_notes','id'=>'approval_notes' ,'value'=>trim($approval_notes),'style'=>'width:400px; height:100px;'));
echo "<br/><br/> <input name='btnAddNote' id='btnAddNote' type='button' value='Add/Update note only' class='inputbuttons' />";
echo  "</td></tr>";

echo "</table></td></tr>";

echo '<tr><td colspan="2"><br>';
echo form_submit(array('name' => 'btnEdit', 'id' => 'btnEdit','class'=>'inputbuttons','content' => 'edit'), 'Submit');
echo '&nbsp;';
echo form_button(array('name'=>'btnCancel','class'=>'inputbuttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'webmaster/campaign/ongoing/'."'"));
echo "</td></tr><tr><td colspan='2'>";
echo "<input name='action' id='action' type='hidden' value='submit' />"; 
echo "<input name='member_id' id='member_id' type='hidden' value='$member_id' />"; 
echo "<input name='campaign_id' id='campaign_id' type='hidden' value='$campaign_id' />";  
echo "</td></tr>";
echo "</table>";
echo form_close();
?>
<table class="tbl_forms">
<tr><td><?php echo $campaign_urls;?> </td><td>
<P ALIGN='center'><IFRAME SRC='<?php echo base_url();?>newsletter/campaign_preview/campaign_view/<?php echo $campaign_id;?>'   title="<?php echo $campaign_data['email_subject'];?>" WIDTH='700' HEIGHT='600' FRAMEBORDER='0' SCROLLING='auto'></IFRAME></P></td></tr>
</table>
</div>
