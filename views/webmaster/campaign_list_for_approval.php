<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-1.5.1.min.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.mousewheel-3.0.4.pack.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.pack.js?v=6-20-13"></script>
<link href="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.css?v=6-20-13" rel="stylesheet" type="text/css" />
<script type="text/javascript">
	jQuery('.tblheading').find('a').live('click',function(){
		//alert(jQuery(this).attr('href'));
		$("#Src_frm").attr("action", jQuery(this).attr('href'));
		$('#Src_frm').submit();
		return false;
	});
	jQuery('.disallow').live('click',function(){		 
		return (confirm('Are you sure you want to disallow campaign?'))? true : false;		
	});
	jQuery('.draft').live('click',function(){		 
		return (confirm('Are you sure to make this campaign draft again?'))? true : false;		
	});
	jQuery('.archive').live('click',function(){		 
		return (confirm('Are you sure to archive this campaign?'))? true : false;		
	});
    $(document).ready(function(){
		$(".fancybox").fancybox($(".fancybox"),{'centerOnScroll':true,'height':'400','width':'700','scrolling':'auto'});		
    });
	function updateVmta(u, v){
		var vmta =v.value;
		jQuery.ajax({
			url: "<?php echo base_url() ?>webmaster/users_manage/update_vmta/"+u+"/"+vmta,
			type:"POST",
			data: "mode=authentic",
			success: function(data) {
			$('#vmta-'+u).text(data);
			}
		});
	}
	jQuery('.always_slow_release').live('click',function(){
		var id=$(this).attr('id');
		var status =0;
		if ($(this).attr('checked')) status=1;		
		jQuery.ajax({
		  url: "<?php echo base_url() ?>webmaster/users_manage/update_user/"+id+"/"+status,
		  type:"POST",
		  data: "mode=always_slow_release",
		  success: function(data) {
			$('#always_slow_release-'+id).text('updated');
		  }
		});
		
	});

	jQuery('.clAttachMsg').live('click',function(){
		var cid=$(this).attr('id').substring(13);		 
		var message_id = $('#message_id_'+cid+' :selected').val();
		var member_id = $('#hidMid_'+cid).val();
		$("form#frmCampaignSegmentation").attr("action", "/webmaster/campaign/attachMessage/"+cid+'/'+member_id+'/'+message_id);
		$('form#frmCampaignSegmentation').submit();				 	
	});
		
	function attachDisallowedMsg(x){
		if(x != ''){
			jQuery.ajax({
			  url: "<?php echo base_url() ?>webmaster/campaign/ajaxMsg/"+x,
			  type:"POST",
			  success: function(data) { 
				$('#disallow_comment').text(data);
			  }
			});
		}
	}	
</script>
<style type="text/css">
ul.ul_actions{margin:0;padding:0;}
ul.ul_actions li{list-style-type:none;margin-left:0px;padding:5px;width:120px;float:left;}
</style>
<form name="Src_frm" id="Src_frm" method="post" action="<?php echo base_url().'webmaster/campaign/'.$mode;?>">
	<table border="0" cellspacing="0" cellpadding="0" class="tbl_forms">
		<tr>
			<td width="10%">Username</td>
			<td><?php echo form_input(array('name'=>'username','id'=>'username','maxlength'=>50,'size'=>40 ,'value'=>$contacts_array['username'])) ; ?></td>

		</tr>
		<tr>
			<td><input type="hidden" name="mode" value="search"/></td>
			<td>
				<input type="submit" name="btn_search" id="btn_search" value="Search" class="inputbuttons"/>
				<input type="submit" name="btn_cancel" id="btn_cancel" value="Show All" class="inputbuttons"/>
			</td>
		</tr>
	</table>
</form>
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
<div class="tblheading"><?php echo ucfirst($mode); ?> Campaigns <?php //Display paging links
echo $paging_links ?> </div>
<table class="tbl_listing" width="100%">
	<thead>
		<tr>
			<th width="5%">ID</th><th width="15%">Campaign</th><th width="10%">User</th><th width="10%">VMTA</th><th width="10%">Contacts</th><th width="10%">Queued/Scheduled Date</th><th width="40%">Actions</th>		
		</tr>
	</thead>
	<?php
//List all users
if(count($campaigns)){
	foreach($campaigns as $campaign){
	?>
	<tr>
		<td><?php echo $campaign['campaign_id'];  ?></td>
		<td>
			<a target="_blank" href="<?php echo CAMPAIGN_DOMAIN.'c/'.$campaign['campaign_id'];?>" <?php if($campaign['is_deleted']==1) echo "style='color:#ff0000;'"?>><?php echo substr($campaign['campaign_title'],0,20);  ?></a>
			<br/>
			Subject: <?php echo $campaign['email_subject'];  ?>
			<br/>
			From: <?php echo $campaign['sender_name']."<br/>".$campaign['sender_email'];?>
                                                    <br/>
                                                    <a href="<?php echo  site_url("webmaster/users_manage/users_list/".$campaign['member_username']); ?>" <?php if($campaign['campaign_approval_notes'] !='') echo'style="color:#ff0000;"';?>><?php echo $campaign['member_username'];  ?></a>
                                                    <br/>
                                                                            <a target="_blank" href="<?php echo site_url("webmaster/campaign/sendTestViaAdminPanel/".$campaign['campaign_id'] . "/" . $campaign['campaign_created_by']) ?>">Send Mail-Tester Email</a>
                 	</td>
		<td>
			
			
			<?php $check=($campaign['always_slow_release']==1)?"checked='checked'": "";?>
			<input type="checkbox" name="always_slow_release" id="<?php echo $campaign['member_id'];  ?>" class="always_slow_release" value="1" <?php echo $check; ?> />Apply Always Slow Release
			<span id="always_slow_release-<?php echo $campaign['member_id'];?>" style="color:#ff6500"></span>
		</td>
		<td>		
		<select name="vmta"  style="width:150px;" onchange="javascript:updateVmta(<?php echo $campaign['member_id'];?>,this );">
		<?php
			$arrVMTAPool = $this->config->item('pool_and_vmta');
			 
			for($i=0; $i < count($arrVMTAPool); $i++){				 
				for($j=0; $j <  count($arrVMTAPool[$i]); $j++){
					if($j==0)echo "<optgroup label='".$arrVMTAPool[$i][$j]."'></optgroup>";
					if($campaign['vmta'] == $arrVMTAPool[$i][$j])$selIt = 'selected';else $selIt = '';
						echo "<option value='".$arrVMTAPool[$i][$j]."' $selIt>".$arrVMTAPool[$i][$j]."</option>";
				}
			}
		?>
		</select>
		 
<span id="vmta-<?php echo $campaign['member_id'];?>" style="color:#ff6500"></span>
	</td>
		<td>
			<a  href="<?php echo  site_url("webmaster/campaign/get_contacts/".$campaign['campaign_id']."/".$campaign['member_id']); ?>"><?php echo $contacts[$campaign['campaign_id']]['total'];  ?></a>
			<br/>
			<?php echo ltrim($campaign['list_names'],',');?>
			<br/><b>Sent: </b><?php echo $contacts[$campaign['campaign_id']]['sent'];  ?>&nbsp;<?php echo "(". ROUND(($contacts[$campaign['campaign_id']]['sent']) / ($contacts[$campaign['campaign_id']]['sent'] + $contacts[$campaign['campaign_id']]['unsent']),2) * 100 . "%)";?>
			<br/><b>Un-Sent Yet: </b><?php echo $contacts[$campaign['campaign_id']]['unsent'];  ?>&nbsp;<?php echo "(" . ROUND(($contacts[$campaign['campaign_id']]['unsent']) / ($contacts[$campaign['campaign_id']]['sent'] + $contacts[$campaign['campaign_id']]['unsent']),2) * 100 . "%)";?>			
		</td>
		<td>
			<?php if($campaign['campaign_status']!="draft"){
					if(is_null($campaign['campaign_queued'])){
						echo date('F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($campaign['campaign_sheduled'], date_default_timezone_get() )));
					}else{
						echo date('F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($campaign['campaign_queued'], date_default_timezone_get() ) ));
					}
			}else{ ?>
				Not Sent
			<?php } ?>
			<br/>
			<?php if($campaign['campaign_status']!="draft"){ ?>
				<?php echo date('F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($campaign['campaign_sheduled'], date_default_timezone_get() ))); ?>
			<?php }else{ ?>
				Not Scheduled
			<?php } ?>
		</td>		 
		<td>
		<table cellspacing="0" border="0">
		<tr>
			<?php if(($campaign['campaign_status']=="ready")||($campaign['campaign_status']=="active_ready")){  ?>
			<td><ul class="ul_actions">
			<?php if($campaign['stop_campaign_approval']){ ?>
			<li>Approval Stopped - <?php echo $campaign['campaign_approval_notes'] ?></li>
			<?php }else{ ?>
			<li><a id="allow_<?php echo $campaign['campaign_id']; ?>" href="<?php echo  site_url("webmaster/campaign/campaign_segmentation/".$campaign['campaign_id']."/".$campaign['member_id']."/$mode");?>" class="allow">Segmentation </a>- <?php echo $campaign['campaign_approval_notes'] ?></li>
			<?php } ?>
			<li><a target="_blank" href="<?php echo  site_url('webmaster/dashboard_stat/sent_campaign_user_stats/'.$campaign['member_id']);?>">User's Stats</a></li>
			</ul>
			</td>
			<td>
			<ul class="ul_actions">
			<li><a id="draft_<?php echo $campaign['campaign_id']; ?>" href="<?php echo site_url("webmaster/campaign/draftIt/".$campaign['campaign_id']."/$mode/");?>" class="draft">Draft</a></li>
			<li><a id="disallow_<?php echo $campaign['campaign_id']; ?>" href="<?php echo site_url("webmaster/campaign/disallow_confirm/".$campaign['campaign_id']);?>" class="fancybox">Disallow</a></li>
			<li>
			<a onclick="return confirm('Are you sure to delete campaign')" href="<?php echo  site_url('webmaster/campaign/delete/'.$mode.'/'.$campaign['campaign_id']);?>"><font color="red">Delete</font></a>
			</li>
                        <li><a id="messages_<?php echo $campaign['campaign_id'];?>" class="fancybox" href="<?php echo  site_url('webmaster/users_manage/message_list/'.$campaign['member_id']);?>" >Messages</a></li></li>
			</ul>
			
			</td>			
			<?php }elseif(($campaign['is_segmentation']==1)&&($campaign['number_of_contacts']<$contacts[$campaign['campaign_id']])&&($email_not_send_contacts[$campaign['campaign_id']]>0)){ ?>
			<td><ul class="ul_actions">
			<li><a id="allow_<?php echo $campaign['campaign_id']; ?>" href="<?php echo  site_url("webmaster/campaign/campaign_segmentation/".$campaign['campaign_id']."/".$campaign['member_id']."/$mode");?>" class="allow">Segmentation</a></li>			 
			<li><a id="archive_<?php echo $campaign['campaign_id']; ?>" href="<?php echo site_url("webmaster/campaign/archiveIt/".$campaign['campaign_id']."/$mode/");?>" class="archive">Archive</a></li>
			</ul>
			</td>
			<?php }?>
			</tr>
			<tr>
			<td colspan="2"> 
				Assign Message to Memeber:<br/>
				<select name='message_id' id='message_id_<?php echo $campaign['campaign_id'];?>'>
					<option value=''>--select--</option>
					<?php
					foreach($contacts[$campaign['campaign_id']]['arr_message'] as $msg_rec){
						echo "<option value=\"{$msg_rec['message_id']}\">{$msg_rec['message_name']}</option>";
					}
					?>
				</select><span style='margin-left:50px;'> 
				<input type="hidden" name="hidMid" id="hidMid_<?php echo $campaign['campaign_id'];?>" value="<?php echo $campaign['member_id'];?>" />
				<input name='btnAttachMsg' id="btnAttachMsg_<?php echo $campaign['campaign_id'];?>" class="clAttachMsg" type='button' value='Attach message' /></span>			
			</td></tr>
			</table>						 
		</td>
		 
	</tr>
<?php }
	}else{
?>
	<tr><td colspan="10" align="center">No Campaign Available</td></tr>
<?php } ?>
</table>
<form id="frmCampaignSegmentation"></form>
