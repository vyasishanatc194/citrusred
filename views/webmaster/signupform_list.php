<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-1.5.1.min.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.mousewheel-3.0.4.pack.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.pack.js?v=6-20-13"></script>
<link href="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.css?v=6-20-13" rel="stylesheet" type="text/css" />
<script>
	jQuery('.tblheading').find('a').live('click',function(){
		//alert(jQuery(this).attr('href'));
		$("#Src_frm").attr("action", jQuery(this).attr('href'));
		$('#Src_frm').submit();
		return false;
	});
	jQuery('.class_verify').live('click',function(){
		var frmel = $(this).attr('id');
		var frmid = frmel.substring(9);
		var is_verified = 0;
		if ($(this).attr('checked'))
			is_verified = 1;
		jQuery.ajax({
				  url: "<?php echo base_url() ?>webmaster/signupforms/update_signupform/verify/",
				  type:"POST",
				  data: "is_verified="+is_verified+"&sid="+frmid,
				  success: function(res) { 
					$('#msg_signgupform_'+frmid).text(res);
				  }
				});	 	  
	});
	jQuery('.class_single_opt_in').live('click',function(){
		var frmel = $(this).attr('id');
		var frmid = frmel.substring(14);		
		var singleoptin = 0;
		if ($(this).attr('checked'))
			singleoptin = 1;
		jQuery.ajax({
				  url: "<?php echo base_url() ?>webmaster/signupforms/update_signupform/singleoptin/",
				  type:"POST",
				  data: "singleoptin="+singleoptin+"&sid="+frmid,
				  success: function(res) { 
					$('#msg_signgupform_'+frmid).text(res);
				  }
				});	 	  
	});
</script>
<script type="text/javascript">
    $(document).ready(function(){
		$(".fancybox").fancybox({'centerOnScroll':true,'height':'200','width':'300','scrolling':'auto'});
    });
</script>
<form name="Src_frm" id="Src_frm" method="post" action="<?php echo base_url().'webmaster/signupforms/index/'.$mode;?>">
	<table border="0" cellspacing="0" cellpadding="0" class="tbl_forms">
		<tr>
			<td width="10%">Username</td>
			<td><?php echo form_input(array('name'=>'username','id'=>'username','maxlength'=>50,'size'=>40 ,'value'=>$contacts_array['username'])) ; ?></td>
			<?php if($mode == 'verified')$reverseMode = 'unverified';else $reverseMode = 'verified'; ?>
			<td align="right" style="text-align:right;"><a href="<?php echo  site_url("webmaster/signupforms/index/$reverseMode");?>"><?php echo  ucfirst($reverseMode);?> List</a></td>
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
<div class="tblheading"><?php echo ucfirst($mode); ?> Signup-forms <?php //Display paging links
echo $paging_links ?> </div>
<table class="tbl_listing" width="100%">
	<thead>
		<tr>
			<th width="5%"> ID </th>
                        <th width=10%">Date Added</th>
			<th width="10%"> Form Name </th>
			<th width="10%"> User </th>			
			<th width="10%"> Signle-opt-in </th>			
			<th width="10%"> Total Confirmation-mail </th>			
			<th width="10%"> Clicked Confirmation-mail </th>			
			<th width="25%"> Action </th>			
			<th width="5%"> Delete </th>
		</tr>
	</thead>
	<?php
//List all users
if(count($signupforms)) {
	foreach($signupforms as $signupform){

	?>
	<tr>
		<td>
			<?php echo $signupform['id'];  ?>
		</td>
                <td>
                   <?php echo $signupform['date_added']; ?>
                </td>
		<td>		 
			<a class="fancybox" href="<?php echo  site_url("webmaster/signupforms/view/".$signupform['id']); ?>"><?php echo $signupform['form_name'];?></a>
		</td>
		<td>
			<a href="<?php echo  site_url("webmaster/users_manage/users_list/".$signupform['member_username']); ?>"><?php echo $signupform['member_username'];  ?></a>
		</td>
		<td><?php echo $signupform['signle_optin_count']; ?></td>
		<td><?php if($signupform['single_opt_in'] =='1')echo'-';else echo $signupform['sent_confirmation_email']; ?></td>
		<td><?php if($signupform['single_opt_in'] =='1')echo'-';else echo $signupform['clicked_confirmation_email']; ?></td>
		<td>	<?php // echo  site_url('webmaster/signupforms/verifyit/'..'/'.$signupform['id']);?>	
		 
		<input type="checkbox" name="verifyit" id="verifyit_<?php echo $signupform['id'];?>" class="class_verify" value="1" <?php if($signupform['is_verified'] =='1' )echo "checked='checked'"; ?> /> Verify
		
		&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="checkbox" name="single_opt_in" id="single_opt_in_<?php echo $signupform['id'];?>" class="class_single_opt_in" value="1" <?php if($signupform['single_opt_in'] =='1')echo "checked='checked'"; ?> /> Single Opt-In
		<br/>
		<span id="msg_signgupform_<?php echo $signupform['id'];?>" style="color:#ff6500"></span>	
		</td>
		<td>
			<a onclick="return confirm('Are you sure to delete this signup-form')" href="<?php echo  site_url('webmaster/signupforms/delete/'.$mode.'/'.$signupform['id']);?>">Delete</a>
		</td>
	</tr>
<?php }
	}else{
?>
	<tr><td colspan="10" align="center">No Signup-form Available</td></tr>
<?php } ?>
</table>
