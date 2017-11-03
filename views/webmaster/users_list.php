    <script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link href="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.css" rel="stylesheet" type="text/css" />
<SCRIPT type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-ui-1.8.13.custom.min.js"></SCRIPT>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css-front/blitzer/jquery-ui-1.8.14.custom.css" />
<script type="text/javascript">
	$(document).ready(function(){
		$(".fancybox").fancybox({'centerOnScroll':true,'height':'200','width':'300','scrolling':'auto'});
    });
	jQuery('.is_authentic').live('click',function(){
		var id=$(this).attr('id');
		var status =0;
		if ($(this).attr('checked')){
			status =1;	
		}else{
			if($('#automatic_segmentation_'+id).attr('checked'))	
			$('#automatic_segmentation_'+id)[0].click();
		}	
		jQuery.ajax({
		  url: "<?php echo base_url() ?>webmaster/users_manage/update_user/"+id+"/"+status,
		  type:"POST",
		  data: "mode=authentic",
		  success: function(data) {
			$('#auth-'+id).text('updated');
		  }
		});
	});
	jQuery('.is_disclaimer').live('click',function(){
		var id=$(this).attr('id');
		var mid = id.substr(5);
		var status =0;
		if ($(this).attr('checked'))status =1;		
		jQuery.ajax({
		  url: "<?php echo base_url() ?>webmaster/users_manage/update_user/"+mid+"/"+status,
		  type:"POST",
		  data: "mode=disclaimer",
		  success: function(data) { 
			$('#disclaimer-'+mid).text('updated');
		  }
		});
	});
	jQuery('.apply_unresponsive_filter').live('click',function(){
		var id=$(this).attr('id');
		var status =0;
		if ($(this).attr('checked')) status=1;		
		jQuery.ajax({
		  url: "<?php echo base_url() ?>webmaster/users_manage/update_user/"+id+"/"+status,
		  type:"POST",
		  data: "mode=apply_unresponsive_filter",
		  success: function(data) {
			$('#apply_unresponsive_filter-'+id).text('updated');
		  }
		});
		
	});		
	jQuery('.update_urc').live('click',function(){
			var id=$(this).attr('id');
			var mid = id.substr(8);
			var urc_val = $('#urc_'+mid).val();
			jQuery.ajax({
			  url: "<?php echo base_url() ?>webmaster/users_manage/update_user/"+mid+"/"+urc_val,
			  type:"POST",
			  data: "mode=unresponsive_release_count",
			  success: function(data) { 
				$('#urcmsg_'+mid).text('updated');
			  }
			});			
		});
	jQuery('.update_user_note').live('click',function(){
			var id=$(this).attr('id');
			var mid = id.substr(14); 
			var user_note_val = $('#user_note_'+mid).val(); 
			jQuery.ajax({
			  url: "<?php echo base_url() ?>webmaster/users_manage/update_user/"+mid+"/",
			  type:"POST",
			  data: "mode=user_note&user_note_val="+user_note_val,
			  success: function(data) { 
				$('#user_note_msg_'+mid).text('updated');
			  }
			});			
		});	
 	 
	jQuery('.apply_automatic_segmentation').live('click',function(){
		var id=$(this).attr('id');
		var mid = id.substr(23);
		var status =0;
		if ($(this).attr('checked')) status=1;	
		jQuery.ajax({
		  url: "<?php echo base_url() ?>webmaster/users_manage/update_user/"+mid+"/"+status,
		  type:"POST",
		  data: "mode=apply_automatic_segmentation",
		  success: function(data) {
			$('#apply_automatic_segmentation-'+mid).text('updated');
		  }
		});
		
	});	
	jQuery('.update_segment_size').live('click',function(){
			var id=$(this).attr('id');
			var mid = id.substr(17);
			var segment_size_val = $('#segment_size_'+mid).val();
			
			jQuery.ajax({
			  url: "<?php echo base_url() ?>webmaster/users_manage/update_user/"+mid+"/"+segment_size_val,
			  type:"POST",
			  data: "mode=segment_size",
			  success: function(data) { 
				$('#segment_size_msg_'+mid).text('updated');
			  }
			});			
		});		
		
	jQuery('.attach_seedlist').live('click',function(){
		var id=$(this).attr('id');
		var status = 0;
		if ($(this).attr('checked')) status = 1;			
		jQuery.ajax({
		  url: "<?php echo base_url() ?>webmaster/users_manage/attach_seedlist/"+id+"/"+status ,
		  type:"POST",
		  success: function(data) {
			$('#seedlist-'+id).text('updated');
		  }
		});			 
	});
	jQuery('.is_risky').live('click',function(){
			var id=$(this).attr('id');
				id = id.substr(6);
			var status=0;	
			if ($(this).attr('checked')) status=1;			
			jQuery.ajax({
			  url: "<?php echo base_url() ?>webmaster/users_manage/update_risky/"+id+"/"+status,
			  type:"POST",
			  success: function(data) {
				$('#risky-'+id).text('updated');
			  }
			});			 
		});
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
	function fnReanalyse(mid){
	jQuery.ajax({
			url: "<?php echo base_url() ?>webmaster/users_manage/reanalysis/"+mid,
			type:"POST",
			data: "mode=authentic",
			success: function(data) {			
				$.fancybox(data);
			}
		});	
	}
	function fnAnalyseNew(mid){	
	jQuery.ajax({
			url: "<?php echo base_url() ?>webmaster/users_manage/analyseNewContacts/"+mid,
			type:"POST",
			data: "mode=authentic",
			success: function(data) {			
				$.fancybox(data);
			}
		});	
	}
	jQuery('a.exp').live('click',function(){	
		var thisID = $(this).attr("id");
		if(confirm(" Filtered contacts will be exported to csv. Are you sure to export contacts?")){		
			$.get("<?php echo base_url() ?>webmaster/users_manage/export_analysed_contacts/"+thisID,function(filedata){ // AJAX call returns with CSV file data
				  $("#filedata").val(filedata);      				  
				  $("#hiddenform").submit();         
			});					 
		}
	});
	jQuery('a.del').live('click',function(){	
		var thisID = $(this).attr("id");
		if(confirm(" Filtered contacts will be deleted. Are you sure to delete contacts?")){		
			$.get("<?php echo base_url() ?>webmaster/users_manage/delete_analysed_contacts/"+thisID,function(filedata){ // AJAX call returns with CSV file data
				 alert(filedata);       
			});					 
		}
	});	
	jQuery('a.sup').live('click',function(){	
		var thisID = $(this).attr("id");
		if(confirm(" Filtered contacts will be suppressed. Are you sure to suppress contacts?")){		
			$.get("<?php echo base_url() ?>webmaster/users_manage/suppress_analysed_contacts/"+thisID,function(filedata){ // AJAX call returns with CSV file data
				 alert(filedata);       
			});					 
		}
	});	
	jQuery('.clAttachMsg').live('click',function(){
		var member_id=$(this).attr('id').substring(13);		 
		var message_id = $('#message_id_'+member_id+' :selected').val();
		
		$("form#hiddenform").attr("action", "/webmaster/campaign/attachMessage/0/"+member_id+'/'+message_id);
		$('form#hiddenform').submit();					
	});
</script>
<?php if($mode!="paid_users"){?>
<script type="text/javascript">
	jQuery('.tblheading').find('a').live('click',function(){
		//alert(jQuery(this).attr('href'));
		$("#Src_frm").attr("action", jQuery(this).attr('href'));
		$('#Src_frm').submit();
		return false;
	});
	jQuery('#field_name').live('change',function(){
		if($(this).val()=="package_id"){
			$('#field_value').hide();
			$('#select_status').hide();
			$('#select_package').show();
		}else if($(this).val()=="status"){
			$('#field_value').hide();
			$('#select_package').hide();
			$('#select_status').show();
		}else{
			$('#field_value').show();
			$('#select_package').hide();
			$('#select_status').hide();
		}
	});
	function remMsg(mid,msgid){	
	jQuery.ajax({
			url: "<?php echo base_url(); ?>webmaster/users_manage/del_msg/"+mid+'/'+msgid,
			type:"POST",
			data: "mode=del",
			success: function(data) {			
				$.fancybox(data);
			}
		});	
	
	}
</script>

<form name="Src_frm" id="Src_frm" method="post" action="<?php echo ($mode == "upgrade_users") ?  base_url().'webmaster/users_manage/upgrade_users' : base_url().'webmaster/users_manage/users_list';?>">
	<table border="0" cellspacing="0" cellpadding="0" class="tbl_forms">
		<tr>
			<td>Field</td>
			<td>
				<select name="field_name" id="field_name">
					<option value="member_username" <?php echo ($search['field_name'] == "member_username") ? 'selected="selected"' : '';?>>Username</option>
					<option value="member_id" <?php echo ($search['field_name'] == "member_id") ? 'selected="selected"' : '';?>>User-ID</option>
					<option value="email_address" <?php echo ($search['field_name'] == "email_address") ? 'selected="selected"' : '';?>>Email-address</option>
					<option value="first_name"<?php echo ($search['field_name'] == "first_name") ? 'selected="selected"' : '';?>>First Name</option>
					<option value="last_name" <?php echo ($search['field_name'] == "last_name") ? 'selected="selected"' : '';?>>Last Name</option>
					<option value="package_id" <?php echo ($search['field_name'] == "package_id") ? 'selected="selected"' : '';?>>Package</option>
					<option value="status" <?php echo ($search['field_name'] == "status") ? 'selected="selected"' : '';?>>Status</option>
				</select>
			</td>
			<td>
				<input type="text" name="field_value" id="field_value" value="<?php echo $search['field_value']; ?>"  <?php echo (($search['field_name'] == "package_id")OR($search['field_name'] == "status")) ? 'style="display:none;"' : 'style="display:block;"';?>/>
				<select name="select_package" id="select_package"  <?php echo ($search['field_name'] == "package_id") ? 'style="display:block;"' : 'style="display:none;"';?>>
					<?php foreach($packages as $package){ ?>
						<option value="<?php echo $package['package_id'];?>" <?php echo ($search['select_package'] == $package['package_id']) ? 'selected="selected"' : '';?>><?php echo $package['package_min_contacts']."-".$package['package_max_contacts']; ?></option>
					<?php } ?>
				</select>
				<select name="select_status" id="select_status"  <?php echo ($search['field_name'] == "status") ? 'style="display:block;"' : 'style="display:none;"';?>>
					<option value="Active-Paid" <?php echo ($search['select_status'] == "Active-Paid") ? 'selected="selected"' : '';?>>Active-Paid</option>
					<option value="Admin-comped" <?php echo ($search['select_status'] == "Admin-comped") ? 'selected="selected"' : '';?>>Admin-comped</option>
					<option value="Active-Free"  <?php echo ($search['select_status'] == "Active-Free") ? 'selected="selected"' : '';?>>Active-Free</option>
					<option value="Inactive-Policy related" <?php echo ($search['select_status'] == "Inactive-Policy related") ? 'selected="selected"' : '';?>>Inactive-Policy related</option>
					<option value="Inactive- Failed CC" <?php echo ($search['select_status'] == "Inactive- Failed CC") ? 'selected="selected"' : '';?>>Inactive- Failed CC</option>
					<option value="Inactive- Unconfirmed" <?php echo ($search['select_status'] == "Inactive- Unconfirmed") ? 'selected="selected"' : '';?>>Inactive- Unconfirmed</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><input type="hidden" name="mode" value="search"/></td>
			<td colspan="2">
				<input type="submit" name="btn_search" id="btn_search" value="Search" class="inputbuttons"/>
				<input type="submit" name="btn_cancel" id="btn_cancel" value="Show All" class="inputbuttons"/>
			</td>
		</tr>
	</table>
</form>
<?php } ?>
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
 <span style="text-align: left;"><b>Total Paid Users:</b> <?php echo $totusercount;?></span>
 <div style="text-align: right;"><a href="<?php echo base_url() ?>webmaster/users_manage/user_create"><strong>Create New User</strong></a></div>
<div class="tblheading">Manage Users <?php //Display paging links
echo $paging_links ?> </div>

<table class="tbl_listing" width="100%">
	<thead>
		<tr>
			<th width="5%">ID</th>
			<th width="30%" colspan="3">User Details</th>		
			<th width="37%">Actions</th>			
			<th width="28%">More Actions</th>			 
		</tr>
	</thead>
	<?php
//List all users
if(count($users)) {
foreach($users as $user){
?>
<tr>
	<td>
		<?php echo $user['member_id'];  ?>
	</td>
	<td colspan="3">
	<table width="100%" class="tbl_listing" <?php if( $user['campaign_approval_notes'] != '') echo "style='background-color:#FBEBE9'"	;?>>
	<tr><td style="background-color:#ebebeb;padding:2px;"><b>User & Status</b></td>
	<td style="background-color:#ebebeb;padding:2px;"><b>Contacts</b></td>
	<td style="background-color:#ebebeb;padding:2px;"><b>Pipeline</b></td></tr>
	<tr>
	<td>
	<?php 
		if($user['affiliate_status']){
		$color = '#ff6500';
		}elseif($user['is_via_adwords'] !=''){
		$color = '#00ff00';
		}else{
		$color = '#000000';		
		}
			?>
			
			<?php
			if($user['payment_type'] == 1){
				$payment_type = 'Paypal';
				$email_text = 'PayPal Email:';
			}else{
				$payment_type = 'Credit Card';
				$email_text = 'Email:';
			}
			?>
		<label style="color:<?php echo $color;?>;font-weight:bold;">
		<?php echo $user['member_username']; ?><?php echo (trim($user['ls_site_id'])!='')? ' ['.$user['ls_site_id'].']' : '';?></label>
		<br/>
		Payment Type: <?php echo $payment_type;?>
		<br/>
		<?php if($user['payment_type'] == 1){ ?>
		Paypal Subscription Id: <?php echo ($user['paypal_transaction_id'] != '') ? $user['paypal_transaction_id']: 'Not Available' ;?>
		<br/>
		<?php } ?>
		<?php echo $email_text;?> <?php echo $user['email_address'];  ?>
		<br/>Added On: <?php //echo date('d-M-Y H:i', strtotime( $user['created_on'])); 
				echo date('F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($user['created_on'], date_default_timezone_get() )));
		?>
		<hr/>
		<?php echo $user['status_description']; ?><b> (<?php echo ($user['package']=="0-100")?"Free":$user['package']; ?>)</b>
		<b><?php echo ($user['package']!="0-100")?"&nbsp; - &nbsp;[".'Payment Dt:'.$user['next_payement_date']."]":''; ?></b>
	</td>
	<td>		
		<a class="fancybox" href="<?php echo  site_url("webmaster/users_manage/contact_details/".$user['member_id']); ?>"><?php echo $user['contacts']; ?></a>
		<br/> (Unsent Yet: <?php echo $user['fresh_contacts']; ?>)
		<br/> (Used Yet: <?php echo $user['total_used_contacts']; ?>)
		<hr/>		
		<br/> (Validated Contacts: <?php echo $user['validate_contacts']; ?>)
		<br/><a class="data_validation" is_paid="1" user_id="<?php echo $user['member_id'];?>" href="javascript:void(0)">DV - Suppress Contacts</a>
		<br/><a class="data_validation" is_paid="0" user_id="<?php echo $user['member_id'];?>" href="javascript:void(0)">DV - Ignore Contacts</a>
		<br/><?php if($user['is_analyzed'])echo "<a class='fancybox' href='".site_url("webmaster/users_manage/view_contact_analysis/".$user['member_id'])."'>View analysis</a>";?>
	</td>
	 

	<td>
		<select name="vmta"  style="width:150px;" onchange="javascript:updateVmta(<?php echo $user['member_id'];?>,this );">
		<?php
			$arrVMTAPool = $this->config->item('pool_and_vmta');
			 
			for($i=0; $i < count($arrVMTAPool); $i++){				 
				for($j=0; $j <  count($arrVMTAPool[$i]); $j++){
					if($j==0)echo "<optgroup label='".$arrVMTAPool[$i][$j]."'></optgroup>";
					if($user['vmta'] == $arrVMTAPool[$i][$j])$selIt = 'selected';else $selIt = '';
						echo "<option value='".$arrVMTAPool[$i][$j]."' $selIt>".$arrVMTAPool[$i][$j]."</option>";
				}
			}
		?>
		</select>
		 
<span id="vmta-<?php echo $user['member_id'];?>" style="color:#ff6500"></span>
	</td>
</tr>
<tr><td colspan="2"><textarea style="width:450px;color:#ff0000;" name="user_note" id="user_note_<?php echo $user['member_id'];?>"><?php echo $user['campaign_approval_notes'];	?></textarea>
</td><td><a name="btn_user_note" id="btn_user_note_<?php echo $user['member_id'];  ?>" class="update_user_note"  style="border:1px solid #ccc;background:#ebebeb; color:#333;padding:2px 5px;" href="javascript:void(0);">Update message</a>		
		<span id="user_note_msg_<?php echo $user['member_id'];?>" style="color:#ff6500"></span></td>
</tr>
</table>
</td>
	<td>
	<table width="100%">
	<tr><td>
	<ul style="line-height:20px;margin:0 0 0 10px;padding:0;">
		<li style="float:left;width:150px;">
		<?php $check=($user['is_risky']==1)?"checked='checked'": "";?>
		<input type="checkbox" name="is_risky" id="risky_<?php echo $user['member_id'];  ?>" class="is_risky" value="1" <?php echo $check; ?> /> Is Risky
		<span id="risky-<?php echo $user['member_id'];?>" style="color:#ff6500"></span>
		</li>
		<li style="float:left;width:150px;">
		<?php $check=($user['attach_seedlist']==1)?"checked='checked'": "";?>
		<input type="checkbox" name="attach_seedlist" id="<?php echo $user['member_id'];  ?>" class="attach_seedlist" value="1" <?php echo $check; ?> /> Attach Seedlist
		<span id="seedlist-<?php echo $user['member_id'];?>" style="color:#ff6500"></span>
		</li>
		<li style="float:left;width:150px;">
		<?php $check=($user['is_authentic']==1)?"checked='checked'": "";?>
		<input type="checkbox" name="is_authentic" id="<?php echo $user['member_id'];  ?>" class="is_authentic" value="1" <?php if($user['fresh_contacts'] > 2000)echo"DISABLED"; ?>  <?php echo $check; ?> /> Is Authentic
		<span id="auth-<?php echo $user['member_id'];?>" style="color:#ff6500"></span>
		</li>
		<li style="float:left;width:150px;">
		<?php $check=($user['is_disclaimer']==1)?"checked='checked'": "";?>
		<input type="checkbox" name="is_disclaimer" id="disc-<?php echo $user['member_id'];  ?>" class="is_disclaimer" value="1" <?php echo $check; ?> /> Add disclaimer
		<span id="disclaimer-<?php echo $user['member_id'];?>" style="color:#ff6500"></span>
		</li>
		<li style="float:left;width:400px;margin:0padding:0;">
			<?php $check=($user['always_slow_release']==1)?"checked='checked'": "";?>
			<input type="checkbox" name="always_slow_release" id="<?php echo $user['member_id'];  ?>" class="always_slow_release" value="1" <?php echo $check; ?> /> Always Slow Release
			<span id="always_slow_release-<?php echo $user['member_id'];?>" style="color:#ff6500"></span>
		</li>	
		<li style="float:left;width:400px;margin:0padding:0;">
		<?php $check=($user['apply_unresponsive_filter']==1)?"checked='checked'": "";?>
		<input type="checkbox" name="apply_unresponsive_filter" id="<?php echo $user['member_id'];  ?>" class="apply_unresponsive_filter" value="1" <?php echo $check; ?> />Apply Unresponsive Filter
		<span id="apply_unresponsive_filter-<?php echo $user['member_id'];?>" style="color:#ff6500"></span>		 	
		:<input type="input" style="width:60px;margin-left:30px;" name="unresponsive_release_count" id="urc_<?php echo $user['member_id'];?>" value="<?php echo $user['unresponsive_release_count']; ?>" />
		<a name="btn_urc" id="btn_urc_<?php echo $user['member_id'];  ?>" class="update_urc" href="javascript:void(0);" style="border:1px solid #ccc;background:#ebebeb; color:#333;padding:2px 5px;">Update release count</a>		
		<span id="urcmsg_<?php echo $user['member_id'];?>" style="color:#ff6500"></span>		
		</li>
		<li style="float:left;width:400px;border-left:0px solid #ccc;">	
		<?php $check=($user['is_automatic_segmentation']==1)?"checked='checked'": "";?>
		<input type="checkbox" name="apply_automatic_segmentation" id="automatic_segmentation_<?php echo $user['member_id'];  ?>" class="apply_automatic_segmentation" value="1" <?php echo $check; ?> />Apply automatic segmentation
		<span id="apply_automatic_segmentation-<?php echo $user['member_id'];?>" style="color:#ff6500"></span> 
		:<input type="input" style="width:60px;margin-left:9px;" name="segment_size" id="segment_size_<?php echo $user['member_id'];?>" value="<?php echo $user['segment_size']; ?>" />
		<a name="btn_segment_size" id="btn_segment_size_<?php echo $user['member_id'];  ?>" class="update_segment_size" href="javascript:void(0);"  style="border:1px solid #ccc;background:#ebebeb; color:#333;padding:2px 5px;">Update count</a>		
		<span id="segment_size_msg_<?php echo $user['member_id'];?>" style="color:#ff6500"></span>		
		</li>
	</ul>
	</td>
	</tr>
	<tr><td valign="top"> Message:	
		<select name='message_id' id='message_id_<?php echo $user['member_id'];?>'>
			<option value=''>--select--</option>
			<?php
			if(count($user['arr_message']) > 0){
				foreach($user['arr_message'] as $msg_rec){
					echo "<option value=\"{$msg_rec['message_id']}\">{$msg_rec['message_name']}</option>";
				}
			}?>
		</select> 
		<input name='btnAttachMsg' id="btnAttachMsg_<?php echo $user['member_id'];?>" class="clAttachMsg" type='button' value=' Attach ' />		
	</td></tr>
	</table>
	</td>
	<td>
	<ul style="line-height:20px;margin:0 0 0 10px;padding:0;">
		<li style="float:left;width:100px;margin-top:1px;"><a style="border:1px solid #ccc;background:#ebebeb; color:#333;padding:2px 5px;" href="<?php echo  site_url("webmaster/users_manage/check_user_for_admin/".$user['member_id']); ?>" target="_blank">View Dashboard</a></li>
		<li style="float:left;width:100px;margin-top:1px;"><a class='fancybox' style="border:1px solid #ccc;background:#ebebeb; color:#333;padding:2px 5px;" href="<?php echo  site_url('webmaster/users_manage/message_list/'.$user['member_id']);?>" >Messages</a></li>
		<li style="float:left;width:100px;margin-top:1px;"><a style="border:1px solid #ccc;background:#ebebeb; color:#333;padding:2px 5px;" href="<?php echo  site_url('webmaster/users_manage/user_add_package/'.$user['member_id']);?>" >Add invoice</a></li>
		<li style="float:left;width:100px;margin-top:1px;"><a style="border:1px solid #ccc;background:#ebebeb; color:#333;padding:2px 5px;" href="<?php echo  site_url('webmaster/users_manage/invoice_list/'.$user['member_id']);?>" >View invoice</a></li>
		<li style="float:left;width:100px;margin-top:1px;"><a style="border:1px solid #ccc;background:#ebebeb; color:#333;padding:2px 5px;" href="<?php echo  site_url('webmaster/contacts_segmentation/index/'.$user['member_id']);?>" >Segmentation</a></li>
		<li style="float:left;width:100px;margin-top:1px;"><a style="border:1px solid #ccc;background:#ebebeb; color:#333;padding:2px 5px;" href="<?php echo  site_url('webmaster/contacts_segmentation/filter/'.$user['member_id']);?>" >Filter</a></li>
		<li style="float:left;width:100px;margin-top:1px;"><a style="border:1px solid #ccc;background:#ebebeb; color:#333;padding:2px 5px;" href="<?php echo  site_url('webmaster/contacts_segmentation/importContacts/'.$user['member_id']);?>" >Add contact</a></li>
		<li style="float:left;width:100px;margin-top:1px;"><a style="border:1px solid #ccc;background:#ebebeb; color:#333;padding:2px 5px;" href="<?php echo  site_url('webmaster/contacts_segmentation/addDNM/'.$user['member_id']);?>" >Add DNM</a></li>
		<li style="float:left;width:100px;margin-top:1px;"><a style="border:1px solid #ccc;background:#ebebeb; color:#333;padding:2px 5px;" href="<?php echo  site_url('webmaster/users_manage/user_edit/'.$user['member_id']);?>" style="color:red;">Edit</a></li>	
		<li style="float:left;width:100px;margin-top:1px;"><a style="border:1px solid #ccc;background:#ebebeb; color:#333;padding:2px 5px;" href="<?php echo  site_url('webmaster/users_manage/user_batch/'.$user['member_id']);?>" style="color:red;">Batch Scheduled</a></li>			
		<li style="float:left;width:100px;margin-top:1px;"><a style="border:1px solid #ccc;background:#ebebeb; color:#333;padding:2px 5px;" href="<?php echo  site_url('webmaster/users_manage/user_supress/'.$user['member_id']);?>" style="color:red;">Supress Contacts</a></li>	
		</ul></td>	
</tr>
<?php } } else { ?>
<tr><td colspan="11" align="center">No User Available</td></tr>
<?php } ?>
</table>
<form id="hiddenform" method="POST" action="<?php echo base_url() ?>webmaster/users_manage/download">
    <input type="hidden" id="filedata" name="data" value="">
</form>
 
 
 
 
 <script>
 $(".data_validation").click(function(){
    var user_id = $(this).attr('user_id');
    var is_paid = $(this).attr('is_paid');
    $.fancybox({'content' : "<div style=\"width:400px;\"><h5 style=\"margin-top:0px;\">Validate Contacts</h5><div class=\"subscription_msg info\" style=\"padding-left:10px\">Are you sure to validate contacts/list?</div><div class=\"btn-group\"><input type=\"submit\" class=\"btn confirm\" content=\"Submit\"  value=\"Save\" id=\"email_validation\"  onclick=\"email_validation("+user_id+","+is_paid+");\"><button class=\"btn cancel\" onclick=\"$.fancybox.close();\" value=\"Cancel\" type=\"button\">Cancel</button></div></div>"});
 });
 
 
 function email_validation(user_id, is_paid=0){
    var block_data="user_id="+user_id+"&is_paid="+is_paid;
    $.ajax({
        url: "<?php echo base_url() ?>webmaster/users_manage/email_validation/",
        type:"POST",
        data:block_data,
        success: function(data){
            var result = jQuery.parseJSON(data);
            if(result.resp == '1'){
                $.fancybox({'content' : "<div style=\"width:400px;\"><h5 style=\"margin-top:0px;\">"+result.status+"</h5><div class=\"subscription_msg info\" style=\"padding:10px\">"+result.message+"</div><button class=\"btn cancel\" onclick=\"$.fancybox.close();\" value=\"Cancel\" type=\"button\">Close</button></div></div>"});
            
            }else{
                $.fancybox({'content' : "<div style=\"width:400px;\"><h5 style=\"margin-top:0px;\">"+result.status+"</h5><div class=\"subscription_msg info\" style=\"padding:10px\">"+result.message+"</div><button class=\"btn cancel\" onclick=\"$.fancybox.close();\" value=\"Cancel\" type=\"button\">Close</button></div></div>"});
            }
        }
      });
 }
 
 </script> 
