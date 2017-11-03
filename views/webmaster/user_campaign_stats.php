<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link href="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.css" rel="stylesheet" type="text/css" />
<SCRIPT type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-ui-1.8.13.custom.min.js"></SCRIPT>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css-front/blitzer/jquery-ui-1.8.14.custom.css" />
<script type="text/javascript">
	jQuery('.tblheading').find('a').live('click',function(){
		//alert(jQuery(this).attr('href'));
		$("#Src_frm").attr("action", jQuery(this).attr('href'));
		$('#Src_frm').submit();
		return false;
	});
    $(document).ready(function(){
    $(".fancybox").fancybox({'centerOnScroll':true,'height':'200','width':'300','scrolling':'auto'});
    });
	function openIpr(){		
		var block_data="";
		block_data+=$('#Src_frm').serialize();
		jQuery.ajax({
			url: "<?php echo base_url() ?>webmaster/campaign/ipr/",
			type:"POST",
			data:block_data,
			success: function(data) {
				$.fancybox(data);
			}
		});	
	}
</script>
<?php $ci =& get_instance(); ?>
<form name="Src_frm" id="Src_frm" method="post" action="<?php echo base_url().'webmaster/dashboard_stat/user_campaign_stats';?>">
	<table border="0" cellspacing="0" cellpadding="0" class="tbl_forms">
		<tr>			
			<td width="10%">Username<br/>
			<?php echo form_input(array('name'=>'username','id'=>'username','maxlength'=>50,'size'=>40 ,'value'=>$contacts_array['username'])) ; ?></td>	
			<td>Date From<br/>
			
			<?php echo form_input(array('name'=>'date_from','id'=>'date_from','maxlength'=>50,'size'=>40 ,'value'=>$contacts_array['date_from'])) ; ?>
			<SCRIPT type="text/javascript">
				$(function(){$("#date_from").datepicker({ dateFormat: 'yy-mm-dd' });});
			</SCRIPT> </td>
			<td>Date To<br/>
			
			<?php echo form_input(array('name'=>'date_to','id'=>'date_to','maxlength'=>50,'size'=>40 ,'value'=>$contacts_array['date_to'])) ; ?>
			<SCRIPT type="text/javascript">
				$(function(){$("#date_to").datepicker({ dateFormat: 'yy-mm-dd' });	});
			</SCRIPT> </td>
			<td>Sort by<br/>
			<select name="sort_by">
				<option value="email_send_date" selected>Send Date</option>
				<option value="email_track_unsubscribes" <?php echo ($contacts_array['sort_by']=='email_track_unsubscribes')?'selected':'';?> >Unsubscribe</option>
				<option value="email_track_bounce" <?php echo ($contacts_array['sort_by']=='email_track_bounce')?'selected':'';?>>Bounced</option>
				<option value="email_track_spam" <?php echo ($contacts_array['sort_by']=='email_track_spam')?'selected':'';?>>Complaints</option>				
			</select>
			</td>
			
		</tr>
		<tr>			
			<td colspan="1">Keyword</td>
			<td colspan="3" nowrap><?php echo form_input(array('name'=>'keyword','id'=>'keyword','maxlength'=>100,'size'=>120 ,'value'=>$contacts_array['keyword'])) ; ?></td>		
		</tr>
		<tr>			
			<td colspan="1">Show Above Critical Level</td>
			<td colspan="3" nowrap>
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td><input type="radio" style="width:10px;" name="above_critical_level" id="above_critical_level" value="none" checked />None</td>
			<td><input type="radio" style="width:10px;" name="above_critical_level" id="above_critical_level" value="bounces" <?php if($contacts_array['above_critical_level']=='bounces')echo'checked';?> />Bounces</td>
			<td><input type="radio" style="width:10px;" name="above_critical_level" id="above_critical_level" value="complaints" <?php if($contacts_array['above_critical_level']=='complaints')echo'checked';?> />Complaints</td>
			</tr>
			</table>
			</td>		
		</tr>
		<tr>
			<td><input type="hidden" name="mode" value="search"/></td>
			<td>
				<input type="submit" name="btn_search" id="btn_search" value="Search" class="inputbuttons"/>
				<input type="submit" name="btn_cancel" id="btn_cancel" value="Show All" class="inputbuttons"/>
			</td>
			<td>&nbsp;</td>
			<td align="right">			
			<!--a onclick="javascript:void(0);" href="javascript: openIpr();">IPR</a--></td>			
		</tr>
	</table>
</form>
<div class="tblheading">Campaign Stats<?php //Display paging links
echo $paging_links ?> </div>

<table class="tbl_listing" width="100%"> 
	<thead>
		<tr>
			<th width="20%">Campaign Details</th>
			<th width="5%">User</th>
			<th width="5%">Sent</th>			
			<th width="5%">Released</th>			
			<th width="5%">Delivered</th>			
			<th width="5%">Opened</th>
			<th width="5%">Unopened</th>
			<th width="5%">Clicks</th>
			<th width="5%">Forwards</th>
			<th width="5%">Unsubscribes</th>
			<th width="5%">Bounced</th>			
			<th width="5%">Complaints</th>
			<th width="25%">Action</th>		
		</tr> 
	</thead> 
	<?php
	if(count($emailreport_data)>0){
		foreach($emailreport_data as $key=>$email){
		$readPercentage = ($email['total_sent_emails']>0)?round(($email['total_read_emails']/$email['total_sent_emails'])*100,2):0;	
		$unreadPercentage = ($email['total_sent_emails']>0)?round(($email['total_unread_emails']/$email['total_sent_emails'])*100,2):0;	
		$clickPercentage = ($email['total_sent_emails']>0)?round(($email['total_click_emails']/$email['total_sent_emails'])*100,2):0;	
		$forwardPercentage = ($email['total_sent_emails']>0)?round(($email['email_track_forward']/$email['total_sent_emails'])*100,2):0;	
		$unsubscribePercentage = ($email['total_sent_emails']>0)?round(($email['total_unsubscribes']/$email['total_sent_emails'])*100,2):0;	
		$bouncePercentage = ($email['total_sent_emails']>0)?round(($email['email_track_bounce']/$email['total_sent_emails'])*100,2):0;	
		$complaintPercentage = ($email['total_sent_emails']>0)?round(($email['total_complaint_emails']/$email['total_sent_emails'])*100,2):0;
	?>
	<tr>
		<td width="20%">		
		<a target="_blank" href="<?php echo CAMPAIGN_DOMAIN.'c/'.$key; ?>" <?php if($email['is_deleted']==1) echo "style='color:#ff0000;'"?>><?php echo $email['campaign_title']; ?></a>
		<br/>
		<b>From:</b> <?php echo $email['sender_name'] . ' ['.$email['sender_email'] .']';?>	
		<br/>
		<b>Sent at:</b>	<?php echo date('l F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($email['email_send_date'], date_default_timezone_get())))?>			
		</td>
		<td width="5%">
			<a class="fancybox" href="<?php echo  site_url("webmaster/users_manage/view/".$email['member_id']); ?>"><?php echo $email['member_username'];  ?></a>
		</td>
		<td width="5%">
			<?php echo $email['total_sent_emails']; ?>
			<?php if ($email['number_of_contacts'] > 0) echo "<br/>Seg:&nbsp;{".$email['number_of_contacts'].'}';?>
		</td>
		 
		<td width="5%"><?php echo $email['total_released_emails']; ?></td> 
		<td width="5%"><?php echo $email['total_delivered_emails']; ?></td> 
		<td width="5%">			
			<?php echo $email['total_read_emails'].' ('.$readPercentage.'%)'; ?>
		</td>
		<td width="10%">
			<?php echo $email['total_unread_emails'].' ('.$unreadPercentage.'%)'; ?>
		</td>
		<td width="10%">
			<?php echo $email['total_click_emails'].' ('.$clickPercentage.'%)'; ?>
		</td>
		<td width="10%">
			<?php echo $email['email_track_forward'].' ('.$forwardPercentage.'%)'; ?>
		</td>
		<td width="10%">
			<?php echo $email['total_unsubscribes'].' ('.$unsubscribePercentage.'%)'; ?>
		</td>	
		<td width="10%">
			<?php echo $email['email_track_bounce'].' ('.$bouncePercentage.'%)'; ?>
		</td>
		<td width="10%">
			<?php echo $email['total_complaint_emails'].' ('.$complaintPercentage.'%)'; ?>
		</td>
		
		
		<td width="10%">
			<a class="fancybox" href="<?php echo  site_url('webmaster/campaign/ipr/'.$key);?>">IPR</a>
			<?php if($ci->session->userdata('webmaster_id')=='1'){ ?>	
			&nbsp;|&nbsp;
			<a onclick="return confirm('Are you sure to delete campaign')" href="<?php echo  site_url('webmaster/campaign/delete/stat/'.$key);?>">Delete</a>
			<?php }?>
		</td>
	</tr>
<?php } 
	}else{ 
?>
	<tr><td colspan="12" align="center">No Campaign Available</td></tr>
<?php } ?>
</table>