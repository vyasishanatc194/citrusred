<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-1.5.1.min.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.mousewheel-3.0.4.pack.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.pack.js?v=6-20-13"></script>
<link href="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.css?v=6-20-13" rel="stylesheet" type="text/css" />
<script>
	 
	jQuery('.class_verify').live('click',function(){
		var frmel = $(this).attr('id');
		var frmid = frmel.substring(9);
		var is_verified = 0;
		if ($(this).attr('checked'))
			is_verified = 1;
		jQuery.ajax({
				  url: "<?php echo base_url() ?>webmaster/autoresponders/update_aresponder/",
				  type:"POST",
				  data: "is_verified="+is_verified+"&aid="+frmid,
				  success: function(res) { 
					$('#msg_as_'+frmid).text(res);
				  }
				});	 	  
	});
 
</script>
<script type="text/javascript">
    $(document).ready(function(){
		$(".fancybox").fancybox({'centerOnScroll':true,'height':'200','width':'300','scrolling':'auto'});
    });
</script>
<form name="Src_frm" id="Src_frm" method="post" action="<?php echo base_url().'webmaster/autoresponders/index/'.$mode;?>">
	<table border="0" cellspacing="0" cellpadding="0" class="tbl_forms">
		<tr>
			<td width="10%">Username</td>
			<td><?php echo form_input(array('name'=>'username','id'=>'username','maxlength'=>50,'size'=>40 ,'value'=>$contacts_array['username'])) ; ?></td>
			<?php if($mode == 'verified')$reverseMode = 'unverified';else $reverseMode = 'verified'; ?>
			<td align="right" style="text-align:right;"><a href="<?php echo  site_url("webmaster/autoresponders/index/$reverseMode");?>"><?php echo  ucfirst($reverseMode);?> List</a></td>
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
<div class="tblheading"><?php echo ucfirst($mode); ?> Autoresponders <?php //Display paging links
echo $paging_links ?> </div>
<table class="tbl_listing" width="100%">
	<thead>
		<tr>
			<th width="20%"> Username </th>
			<th width="70%"> Autoresponder</th>					
			<th width="10%"> Action </th>						
		</tr>
	</thead>
	<?php
//List all users
if(count($autoresponders)) {
	foreach($autoresponders as $thisAutoresponder){

	?>
	<tr>
		<td width="20%">
			<a href="<?php echo  site_url("webmaster/users_manage/users_list/".$thisAutoresponder['member_username']); ?>"><?php echo $thisAutoresponder['member_username'];  ?></a>
		</td>
		<td width="70%">		 
			<a target="_blank" href="<?php echo  site_url("a/".$thisAutoresponder['campaign_id']); ?>"><?php echo $thisAutoresponder['campaign_title'];?></a>
			<br /><b>Subject:</b> <?php echo $thisAutoresponder['email_subject'];?>
			<br /><b>From:</b> <?php echo $thisAutoresponder['sender_name'];?>  
			<br /><?php echo $thisAutoresponder['sender_email'];?> 
			<br /><b>Scheduled Dt:</b> <?php echo $thisAutoresponder['scheduled_date'];?> 			
		</td>
	 
		<td>	 
		<input type="checkbox" name="verifyit" id="verifyit_<?php echo $thisAutoresponder['campaign_id'];?>" class="class_verify" value="1" <?php if($thisAutoresponder['is_verified'] =='1' )echo "checked='checked'"; ?> /> Verify		
		<br/>
		<span id="msg_as_<?php echo $thisAutoresponder['campaign_id'];?>" style="color:#ff6500"></span>	
		</td>		
	</tr>
<?php }
	}else{
?>
	<tr><td colspan="10" align="center">No Autoresponder Available</td></tr>
<?php } ?>
</table>
