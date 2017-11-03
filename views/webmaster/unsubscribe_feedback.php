<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link href="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.css" rel="stylesheet" type="text/css" />
<SCRIPT type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-ui-1.8.13.custom.min.js"></SCRIPT>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css-front/blitzer/jquery-ui-1.8.14.custom.css" />
<script type="text/javascript">
	$(document).ready(function(){
		$(".fancybox").fancybox({'centerOnScroll':true,'height':'200','width':'300','scrolling':'auto'});
    });
 
 
	function showFeedback(mid,fid){
	jQuery.ajax({
			url: "<?php echo base_url() ?>webmaster/contacts_segmentation/showFeedback/"+mid+"/"+fid,
			type:"POST",
			data: "mode=authentic",
			success: function(data) {			
				$.fancybox(data);
			}
		});	
	}
</script>
<form name="Src_frm" id="Src_frm" method="post" action="<?php echo base_url().'webmaster/contacts_segmentation/unsubscribe_feedback';?>">
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
			<td>Pipeline<br/>
			<select name="pipeline">
				<option value="" selected>ALL</option>
				<option value="redrotate" <?php echo ($contacts_array['pipeline']=='redrotate')?'selected':'';?> >redrotate</option>
				<option value="rcmailsv.com" <?php echo ($contacts_array['pipeline']=='rcmailsv.com')?'selected':'';?>>rcmailsv</option>
				<option value="mailsvrc.com" <?php echo ($contacts_array['pipeline']=='mailsvrc.com')?'selected':'';?>>mailsvrc</option>				
			</select>
			</td>
			
		</tr>
		<tr>			
			<td colspan="4">Feedback-ID:
			<select name="feedback_id">
				<option value="" selected>ALL</option>
				<option value="1" <?php echo ($contacts_array['feedback_id']=='1')?'selected':'';?> >1</option>
				<option value="2" <?php echo ($contacts_array['feedback_id']=='2')?'selected':'';?> >2</option>
				<option value="3" <?php echo ($contacts_array['feedback_id']=='3')?'selected':'';?> >3</option>
				<option value="4" <?php echo ($contacts_array['feedback_id']=='4')?'selected':'';?> >4</option>
				<option value="5" <?php echo ($contacts_array['feedback_id']=='5')?'selected':'';?> >5</option>
				<option value="6" <?php echo ($contacts_array['feedback_id']=='6')?'selected':'';?> >6</option>
			</select>
			 <b>more than</b>
			 <input type="text" name="feedback_id_morethan" id="feedback_id_morethan" value="0" />
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
<div id="feedback_detail"> 
<div class="tblheading">Unsubscribe Feedback <?php //Display paging links
echo $paging_links ?> </div>

<?php

echo $tbl;

?>
</div>