<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-1.5.1.min.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.mousewheel-3.0.4.pack.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.pack.js?v=6-20-13"></script>
<link href="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.css?v=6-20-13" rel="stylesheet" type="text/css" />
<SCRIPT type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-ui-1.8.13.custom.min.js?v=6-20-13"></SCRIPT>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css-front/blitzer/jquery-ui-1.8.14.custom.css?v=6-20-13" />
<script>
	jQuery('.allow').live('click',function(){
		var confirm_msg=confirm('Are you sure you want to approve campaign');
		if(confirm_msg){
			return true;
		}else{
			return false;
		}
		return false;
	});
	jQuery('.disallow').live('click',function(){
		var confirm_msg=confirm('Are you sure you want to disallow campaign');
		if(confirm_msg){
			return true;
		}else{
			return false;
		}
		return false;
	});
	jQuery('.tblheading').find('a').live('click',function(){
		//alert(jQuery(this).attr('href'));
		$("#Src_frm").attr("action", jQuery(this).attr('href'));
		$('#Src_frm').submit();
		return false;
	});

	function revertImport(mid,importId){
		var x = $('#'+importId).html();
		var ajaximg = '<img border="0" src="<?php echo $this->config->item('webappassets');?>images-front/ajax_loading.gif?v=6-20-13"/>';
		$('#'+importId).html(x+ajaximg);
		jQuery.ajax({
				  url: "<?php echo base_url() ?>webmaster/activity_log/undo_import/"+mid+"/"+importId,
				  type:"POST",
				  success: function(data) {
					if('success' == data)
					$('#'+importId).html(' | To re-import use front end');
				  }
				});
	}
</script>
<script type="text/javascript">
    $(document).ready(function(){
    $(".fancybox").fancybox({'centerOnScroll':true,'height':'200','width':'300','scrolling':'auto'});
    });
</script>
<form name="Src_frm" id="Src_frm" method="post" action="<?php echo base_url().'webmaster/activity_log/display';?>">
	<table border="0" cellspacing="0" cellpadding="0" class="tbl_forms">
		<tr>
			<td>Date</td>
			<td><?php echo form_input(array('name'=>'date','id'=>'date','maxlength'=>50,'size'=>40 ,'value'=>$contacts['date'])) ; ?>
			<SCRIPT type="text/javascript">
				$(function(){
					$("#date").datepicker();
				});
			</SCRIPT>
			</td>
			<td>Username</td>
			<td><?php echo form_input(array('name'=>'username','id'=>'username','maxlength'=>50,'size'=>40 ,'value'=>$contacts['username'])) ; ?></td>
			<td>Campaign name</td>
			<td><?php echo form_input(array('name'=>'campaign_name','id'=>'campaign_name','maxlength'=>50,'size'=>40 ,'value'=>$contacts['campaign_name'])) ; ?></td>
		</tr>
		<tr>
			<td><input type="hidden" name="mode" value="search"/></td>
			<td colspan="5">
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
echo $this->session->flashdata('messages');
?>
</div>
<div class="tblheading">Activity Logs <?php //Display paging links
echo $paging_links ?> </div>
<table class="tbl_listing" width="100%">
	<thead>
		<tr>
			<th width="20%">
				 Activity Date
			</th>
			<th width="80%">
				 Activty
			</th>
		</tr>
	</thead>			 
<?php
//List all users
if(count($activity_logs)) {
	foreach($activity_logs as $activity_log){
	?>
	<tr>
		<td width="20%">
			<?php echo date('F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($activity_log['timestamp'],date_default_timezone_get())));  ?>
		</td>
		<td width="80%">
			<?php $activity=explode(":",$activity_log['activity']); ?>
			<?php if($activity[0]=="login") {
					echo "<a href='". site_url("webmaster/users_manage/users_list/".$activity_log['member_username'])."'>".$activity_log['member_username']."</a> Logged-in (".$activity[1].")";
				}else if($activity_log['activity']=="logout") {
					echo "<a href='". site_url("webmaster/users_manage/users_list/".$activity_log['member_username'])."'>".$activity_log['member_username']."</a> Logged-out";
				}else if((strtolower ($activity_log['activity'])=="contact_imported")||(strtolower ($activity_log['activity'])=="contact_add")||(strtolower ($activity_log['activity'])=="contact_deleted")||(strtolower ($activity_log['activity'])=="contact_added_to_do_not_mail")) {
					echo "<a href='". site_url("webmaster/users_manage/users_list/".$activity_log['member_username'])."'>".$activity_log['member_username']."</a> ".ucfirst($activity_log['activity'])." (".$activity_log['number_of_contacts'].")";
				}else if(strtolower ($activity_log['activity'])=="contact_import_starts") {
					echo "<a href='". site_url("webmaster/users_manage/users_list/".$activity_log['member_username'])."'>".$activity_log['member_username']."</a> ".ucfirst($activity_log['activity'])." (".$activity_log['number_of_contacts'].")";
				}else if(strpos($activity_log['activity'],"campaign_")!==False) {
					if(strpos($activity_log['activity'],"delete")!==False){
						echo ucfirst($activity_log['activity'])."  by "."<a href='". site_url("webmaster/users_manage/users_list/".$activity_log['member_username'])."'>".$activity_log['member_username']."</a> ";
					}else{
						$campaign_link= CAMPAIGN_DOMAIN.'c/'.$activity_log['campaign_id'];
						echo ucfirst($activity_log['activity'])." (<a  href='". $campaign_link."' target='_blank'>".$activity_log['campaign_title']."</a>) by "."<a class='fancybox' href='". site_url("webmaster/users_manage/view/".$activity_log['member_id'])."'>".$activity_log['member_username']."</a> ";
					}
				}else if(strpos($activity_log['activity'],"autoresponder_")!==False) {
					$autoresponder_link= CAMPAIGN_DOMAIN.'a/'.$activity_log['campaign_id'];
					echo ucfirst($activity_log['activity'])." (<a  href='". $autoresponder_link."' target='_blank'>".$activity_log['campaign_title']."</a>) by "."<a class='fancybox' href='". site_url("webmaster/users_manage/view/".$activity_log['member_id'])."'>".$activity_log['member_username']."</a> ";
				}else{
					echo "<a class='fancybox' href='". site_url("webmaster/users_manage/users_list/".$activity_log['member_username'])."'>".$activity_log['member_username']."</a> ".ucfirst($activity_log['activity']);
				}
				if($activity_log['contact_list_type']==1){
					echo " List Type: Individual";
				}else if($activity_log['contact_list_type']==2){
					echo " List Type: Copy&Paste";
					if($activity_log['file_name']!=""){
						$importBatchID = $activity_log['campaign_id'];
						echo " <a href='". site_url("webmaster/activity_log/export_file/".$activity_log['member_id']."/".$activity_log['file_name'])."'>Download File</a>";
						if($activity_log['is_undone'] == 0){
						//echo "<span id='{$importBatchID}'> | <a href='". site_url("webmaster/activity_log/undo_import/{$importBatchID}'>Undo Import</a></span>";
						echo "<span id='{$importBatchID}'> | <a href='javascript:void(0);' onclick='javascript:revertImport({$activity_log['member_id']},{$importBatchID})'>Undo Import</a></span>";
						}else{
						echo " | To re-import use front end";
						}
					}
				}else if($activity_log['contact_list_type']==3 and $activity_log['activity']!='contact_import_starts'){
					echo " List Type: Imported from file";
					if($activity_log['file_name']!=""){
						$importBatchID = $activity_log['campaign_id'];
						echo " <a href='". site_url("webmaster/activity_log/export_file/".$activity_log['member_id']."/".$activity_log['file_name'])."'>Download File</a>";
						if($activity_log['is_undone'] == 0){
						//echo "<span id='{$importBatchID}'> | <a href='". site_url("webmaster/activity_log/undo_import/{$importBatchID}'>Undo Import</a></span>";
						echo "<span id='{$importBatchID}'> | <a href='javascript:void(0);' onclick='javascript:revertImport({$activity_log['member_id']},{$importBatchID})'>Undo Import</a></span>";
						}else{
						echo " | To re-import use front end";
						}
					}
				}else if($activity_log['contact_list_type']==4){
					echo " List Type: Signup Form";
				}else if($activity_log['contact_list_type']==5){
					echo " List Type: API";
				}
			?>
		</td>
	</tr>
<?php }
}else{
?>
	<tr><td colspan="2" align="center">No Acitivity Log</td></tr>
<?php } ?>
</table>
