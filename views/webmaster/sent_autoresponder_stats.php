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
			url: "<?php echo base_url() ?>webmaster/dashboard_stat/auto_ipr/",
			type:"POST",
			data:block_data,
			success: function(data) {
				$.fancybox(data);
			}
		});	
	} 
</script>
<?php $ci =& get_instance(); ?>
<form name="Src_frm" id="Src_frm" method="post" action="<?php echo base_url().'webmaster/dashboard_stat/sent_autoresponders';?>">
	<table border="0" cellspacing="0" cellpadding="0" class="tbl_forms">
		<tr>			
			<td width="20%">Username<br/>
			<?php echo form_input(array('name'=>'username','id'=>'username','maxlength'=>50,'size'=>40 ,'value'=>$contacts_array['username'])) ; ?></td>	 
			
			<td><br/>
				<input type="hidden" name="mode" value="search"/>
				<input type="submit" name="btn_search" id="btn_search" value="Search" class="inputbuttons"/>
				<input type="submit" name="btn_cancel" id="btn_cancel" value="Show All" class="inputbuttons"/>
			</td>
			 		
		</tr>
	</table>
</form>
<div class="tblheading">Autoresponder Stats<?php //Display paging links
echo $paging_links ?> </div>

<table class="tbl_listing" width="100%"> 
	<thead>
		<tr>
			<th width="10%">Campaign</th>
			<th width="10%">Activation Date</th>
			<th width="5%">User</th>
			
			<th width="5%">Dt-Range</th>
			<th width="5%">Sent</th>			 
			<th width="5%">Opened</th>
			<th width="5%">Unopened</th>
			<th width="10%">Clicks</th>
			<th width="10%">Forwards</th>
			<th width="10%">Unsubscribes</th>
			<th width="10%">Bounced</th>			
			<th width="10%">Complaints</th>	
			<th width="5%">Action</th>		 	
		</tr> 
	</thead> 
	<?php
	if(count($emailreport_data)>0){
		foreach($emailreport_data as $key=>$email){
		$readPercentage = ($email['total_sent_emails']>0)?round(($email['total_read_emails']/$email['total_sent_emails'])*100,2):0;	
		$unsubscribePercentage = ($email['total_sent_emails']>0)?round(($email['total_unsubscribes']/$email['total_sent_emails'])*100,2):0;	
		$bouncePercentage = ($email['total_sent_emails']>0)?round(($email['email_track_bounce']/$email['total_sent_emails'])*100,2):0;	
		$complaintPercentage = ($email['total_sent_emails']>0)?round(($email['total_complaint_emails']/$email['total_sent_emails'])*100,2):0;
		
		$rowBGColor = ($email['campaign_status'] == 1)?'':'bgcolor="#fce1e1"';
		
	?>
	<tr <?php echo $rowBGColor;?>>
		<td width="10%">		
			<a target="_blank" href="<?php echo CAMPAIGN_DOMAIN.'c/'.$key; ?>" <?php if($email['is_deleted']==1) echo "style='color:#ff0000;'"?>><?php echo $email['campaign_title']; ?></a>
		</td>
		<td width="10%">			
			<?php echo date('l F j, Y \a\t g:i a', strtotime( getGMTToLocalTime($email['email_send_date'], date_default_timezone_get())))?>
		</td>
		<td width="5%">
			<a class="fancybox" href="<?php echo  site_url("webmaster/users_manage/view/".$email['member_id']); ?>"><?php echo $email['member_username'];  ?></a>
		</td>
		<td width="75%" colspan="10">
		<table width="100%">
		<tr>
			<td width="7%">Today:</td>
			<td width="7%"><?php echo $email['today_sent_emails']; ?></td>
			<td width="7%"><?php echo $email['today_read_emails']; ?></td>
			<td width="12%"><?php echo $email['today_unread_emails']; ?></td>
			<td width="12%"><?php echo $email['today_click_emails']; ?></td>
			<td width="12%"><?php echo $email['today_forward_emails']; ?></td>
			<td width="12%"><?php echo $email['today_unsubscribes']; ?></td>	
			<td width="12%"><?php echo $email['today_bounce']; ?></td>
			<td width="12%"><?php echo $email['today_complaint_emails']; ?></td>		
			<td width="7%">&nbsp;</td>
		</tr>
		<tr>
			<td width="5%">Week:</td>
			<td width="5%"><?php echo $email['week_sent_emails']; ?></td>
			<td width="5%"><?php echo $email['week_read_emails']; ?></td>
			<td width="10%"><?php echo $email['week_unread_emails']; ?></td>
			<td width="10%"><?php echo $email['week_click_emails']; ?></td>
			<td width="10%"><?php echo $email['week_forwards']; ?></td>
			<td width="10%"><?php echo $email['week_unsubscribes']; ?></td>	
			<td width="10%"><?php echo $email['week_bounce']; ?></td>
			<td width="10%"><?php echo $email['week_complaints']; ?></td>		
			<td width="25%">&nbsp;</td>
		</tr>
		<tr>
			<td width="5%">Total:</td>
			<td width="5%"><?php echo $email['total_sent_emails']; ?></td>
			<td width="5%"><?php echo $email['total_read_emails'].' ('.$readPercentage.'%)'; ?></td>
			<td width="10%"><?php echo $email['total_unread_emails']; ?></td>
			<td width="10%"><?php echo $email['total_click_emails']; ?></td>
			<td width="10%"><?php echo $email['email_track_forward']; ?></td>
			<td width="10%"><?php echo $email['total_unsubscribes'].' ('.$unsubscribePercentage.'%)'; ?></td>	
			<td width="10%"><?php echo $email['email_track_bounce'].' ('.$bouncePercentage.'%)'; ?></td>
			<td width="10%"><?php echo $email['total_complaint_emails'].' ('.$complaintPercentage.'%)'; ?></td>		
			<td width="25%"><a class="fancybox" href="<?php echo  site_url('webmaster/dashboard_stat/auto_ipr/'.$email['autoresponder_scheduled_id']);?>">IPR</a></td>
		</tr>
		
		</table>
		</td>	
	</tr>
<?php } 
	}else{ 
?>
	<tr><td colspan="12" align="center">No Campaign Available</td></tr>
<?php } ?>
</table>