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
<div class="tblheading">Campaign Stats </div>

<table class="tbl_listing" width="100%"> 
	<thead>
		<tr>
			<th width="25%">Campaign</th>
			<th width="25%">User</th>
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
			<th width="5%">Action</th>			
		</tr> 
	</thead> 
	<?php
	if(count($emailreport_data)>0){
		foreach($emailreport_data as $key=>$email){
		$readPercentage = ($email['total_delivered_emails']>0)?round(($email['total_read_emails']/$email['total_delivered_emails'])*100,2):0;	
		$unreadPercentage = ($email['total_delivered_emails']>0)?round(($email['total_unread_emails']/$email['total_delivered_emails'])*100,2):0;	
		$clickPercentage = ($email['total_delivered_emails']>0)?round(($email['total_click_emails']/$email['total_delivered_emails'])*100,2):0;	
		$forwardPercentage = ($email['total_delivered_emails']>0)?round(($email['email_track_forward']/$email['total_delivered_emails'])*100,2):0;	
		$unsubscribePercentage = ($email['total_delivered_emails']>0)?round(($email['total_unsubscribes']/$email['total_delivered_emails'])*100,2):0;	
		$bouncePercentage = ($email['total_delivered_emails']>0)?round(($email['email_track_bounce']/$email['total_delivered_emails'])*100,2):0;	
		$complaintPercentage = ($email['total_delivered_emails']>0)?round(($email['total_complaint_emails']/$email['total_delivered_emails'])*100,2):0;
	?>
	<tr>
		<td width="25%">		
			<a target="_blank" href="<?php echo CAMPAIGN_DOMAIN.'c/'.$key; ?>" <?php if($email['is_deleted']==1) echo "style='color:#ff0000;'"?>><?php echo $email['campaign_title']; ?></a>
			<br/><b>Subject: </b><?php echo $email['email_subject'];  ?>
			<br/><b>From-Email: </b><?php echo $email['sender_email'];  ?>
			<br/><b>From-name: </b><?php echo $email['sender_name'];  ?>
		</td>
		<td width="25%" style="word-wrap: break-word;word-break: break-all;">
			<a href="<?php echo  site_url("webmaster/users_manage/users_list/".$email['member_username']); ?>"><?php echo $email['member_username'];  ?></a>
			<br/><b>Sent at: </b><?php echo date('Y-m-d g:i a', strtotime( getGMTToLocalTime($email['email_send_date'], date_default_timezone_get())));?>			
			<br/><b>Sent to: </b><?php echo $email['subscription_list'];?>			
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
		<td width="5%">
			<?php echo $email['total_unread_emails'].' ('.$unreadPercentage.'%)'; ?>
		</td>
		<td width="5%">
			<?php echo $email['total_click_emails'].' ('.$clickPercentage.'%)'; ?>
		</td>
		<td width="5%">
			<?php echo $email['email_track_forward'].' ('.$forwardPercentage.'%)'; ?>
		</td>
		<td width="5%">
			<?php echo $email['total_unsubscribes'].' ('.$unsubscribePercentage.'%)'; ?>
		</td>	
		<td width="5%">
			<?php echo $email['email_track_bounce'].' ('.$bouncePercentage.'%)'; ?>
		</td>
		<td width="5%">
			<?php echo $email['total_complaint_emails'].' ('.$complaintPercentage.'%)'; ?>
		</td>
		
		
		<td width="5%">
			<a class="fancybox" href="<?php echo  site_url('webmaster/campaign/ipr/'.$key);?>">IPR</a>
		</td>
	</tr>
<?php } 
	}else{ 
?>
	<tr><td colspan="12" align="center">No Campaign Available</td></tr>
<?php } ?>
</table>