<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-1.5.1.min.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.mousewheel-3.0.4.pack.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.pack.js?v=6-20-13"></script>
<link href="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.css?v=6-20-13" rel="stylesheet" type="text/css" />
<SCRIPT type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-ui-1.8.13.custom.min.js?v=6-20-13"></SCRIPT>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css-front/blitzer/jquery-ui-1.8.14.custom.css?v=6-20-13" />
<script type="text/javascript">
	jQuery('#btnFilter').live('click',function(){
			var hmid = $('#hidMemberId').val();
			var lid = $('#contact_lists').val();
				var adddedBefore = $('#added_on_before').val();
				var adddedAfter = $('#added_on_after').val();
			var is_responsive = $('#is_responsive').val();
			var status = $('#subscriber_status').val();
			var search_key = $('#search_key').val();
			$('#contacts').html("<img src='<?php echo $this->config->item('webappassets');?>images/ajax_loading.gif' width='220' height='19' />");
			  
			  
				jQuery.ajax({
				  url: "<?php echo base_url() ?>webmaster/contacts_segmentation/get_contacts_count/",
				  type:"post",
				  data: $('#segmentation').serialize(),
				  success: function(x) { 
					$('#contacts').html(x);
					$('#segmentation').hide();
				  }
				});
			 
		});
	function disableForm(){
		var limit = document.forms[0].elements.length;
		for (i=0;i<limit;i++) {
		  //document.forms[0].elements[i].disabled = true;
		}
	}
	function enableForm(){
		var limit = document.forms[0].elements.length;
		for (i=0;i<limit;i++) {
		  document.forms[0].elements[i].disabled = false;
		}
	}	
	function movelist(){
		var newList = $('#dpdMoveTo').val();
		var reclimit = $('#txtLimit').val();
		if(parseInt(newList) > 0 && confirm(reclimit+" Filtered contacts will be moved. Are you sure to move contacts?")){
			var fromList =  $('#contact_lists').val();
			$('#contacts').prepend("<img src='<?php echo $this->config->item('webappassets');?>images/ajax_loading.gif' width='220' height='19' />");
			jQuery.ajax({
				  url: "<?php echo base_url() ?>webmaster/contacts_segmentation/move_contacts/"+fromList+'/'+newList,
				  type:"post",
				  data: 'l='+reclimit,
				  success: function(x) {
					if(x == 'ok')alert("Contacts moved.");	
					$('#contacts img').remove();	
				  }
			});	
		}else if(newList =='dnm' &&  confirm(reclimit+" Filtered contacts will be suppressed. Are you sure to move contacts to DNM?")){
			var fromList =  $('#contact_lists').val();
			$('#contacts').prepend("<img src='<?php echo $this->config->item('webappassets');?>images/ajax_loading.gif' width='220' height='19' />");
			jQuery.ajax({
				  url: "<?php echo base_url() ?>webmaster/contacts_segmentation/move_to_dnm/"+fromList,
				  type:"post",
				  data: 'l='+reclimit,
				  success: function(x) {  alert(x);
					if(x == 'ok')alert("Contacts suppressed.");	
					$('#contacts img').remove();					
				  }
			});	
		}	
	}
	function copylist(){
		var newList = $('#dpdCopyTo').val();
		var reclimit = $('#txtLimit').val();
		if(parseInt(newList) > 0 && confirm(reclimit+" Filtered contacts will be copied. Are you sure to copy contacts?")){
			var fromList =  $('#contact_lists').val();
			$('#contacts').prepend("<img src='<?php echo $this->config->item('webappassets');?>images/ajax_loading.gif' width='220' height='19' />");
			jQuery.ajax({
				  url: "<?php echo base_url() ?>webmaster/contacts_segmentation/copy_contacts/"+fromList+'/'+newList,
				  type:"post",
				  data: 'l='+reclimit,
				  success: function(x) {  
					if(x == 'ok')alert("Contacts copied.");
					$('#contacts img').remove();
				  }
			});	
		}	
	}
	function exportit(){
		var reclimit = $('#txtLimit').val();
		if(confirm(reclimit+ " Filtered contacts will be exported to csv. Are you sure to export contacts?")){			
			window.location.href="<?php echo base_url() ?>webmaster/contacts_segmentation/export_contacts/"+reclimit;			 
		}	
	}
	function deletecontacts(){
		var reclimit = $('#txtLimit').val();
		if(confirm(reclimit+" Filtered contacts will be deleted from your account. Are you sure to delete contacts?")){			
			$('#contacts').prepend("<img src='<?php echo $this->config->item('webappassets');?>images/ajax_loading.gif' width='220' height='19' />");			 
			jQuery.ajax({
				url: "<?php echo base_url() ?>webmaster/contacts_segmentation/delete_contacts/",
				type:"post",
				data: 'l='+reclimit,
				success: function(x) { 
					if(x == 'ok')alert("Contacts removed.");
						$('#contacts img').remove();
					}
			});	 
		}	
	}
	function deletefromlist(){
		var reclimit = $('#txtLimit').val();
		if(confirm(reclimit+" Filtered contacts will be deleted from this list only. Are you sure to delete contacts?")){			
			$('#contacts').prepend("<img src='<?php echo $this->config->item('webappassets');?>images/ajax_loading.gif' width='220' height='19' />");
			var fromList =  $('#contact_lists').val();				
			jQuery.ajax({
				url: "<?php echo base_url() ?>webmaster/contacts_segmentation/delete_from_list/"+fromList ,
				type:"post",
				data: 'l='+reclimit,
				success: function(x) { 
					if(x == 'ok')alert("Contacts removed.");
						$('#contacts img').remove();
					}
			});	 
		}	
	}
	function fn_mark_unresponsive(){
		var reclimit = $('#txtLimit').val();
		if(confirm(reclimit+" Filtered contacts will be marked as unresponsive. Are you sure to do this?")){			
			$('#contacts').prepend("<img src='<?php echo $this->config->item('webappassets');?>images/ajax_loading.gif' width='220' height='19' />");
				
			jQuery.ajax({
				url: "<?php echo base_url() ?>webmaster/contacts_segmentation/mark_unresponsive/" ,
				type:"post",
				data: 'l='+reclimit,
				success: function(x) { 
					if(x == 'ok')alert("Contacts marked unresponsive.");
						$('#contacts img').remove();
					}
			});	 
		}	
	}
	function openSubscriptionForm(){
	if( $('#subscription_menu').is(':hidden') ){
		$('.contact_frm').hide();
		$('.import_contact').hide();
		$('.paste_contact').hide();
		$('.import_conact_mailserver').hide();
		var msg=$("#add-list").html();
  	$.fancybox({'content' : "<div style=\"width:400px;\">"+msg+"</div>"});
		$('.subscription_msg').html("");
		$('.drop_img').attr('src',"<?php echo $this->config->item('webappassets');?>images/down.png?v=6-20-13");
	}else if($('#subscription_menu').is(':visible')){
		$('.contact_frm').hide();
		$('.import_contact').hide();
		$('.paste_contact').hide();
		$('.import_conact_mailserver').hide();
		$('#subscription_menu').hide();
		$('.drop_img').attr('src',"<?php echo $this->config->item('webappassets');?>images/login-up.png?v=6-20-13");
	}
	//closeSubscriberForm('contact_frm');
}

function ajaxSubscriptionFrm(frm){
		var mid = $('#hidMemberId').val();
		 var block_data="";
		block_data+="action=submit&"+'&mid='+mid+'&subscription_title='+escape(frm.subscription_title.value);
		   $.ajax({
		  url: "<?php echo base_url() ?>webmaster/contacts_segmentation/create/",
		   type:"POST",
			data:block_data,
		  success: function(data){
		  var data_arr=data.split(":", 2);
		  if(data_arr[0]=="error"){
			$('.subscription_msg').html(data_arr[1]);
			$('.subscription_msg').addClass('info');
		  }else if(data_arr[0]=="success"){			 
			frm.subscription_title.value="";
			$('.subscription_msg').html("List created successfully.");
			setTimeout(function(){window.location.reload();} , 2000);
		  }
		  }
		});
		
		return false;
}
</script>
 
  
<div class="tblheading">Contacts Segmentation</div>


<form name="segmentation" id="segmentation"> 
<table class="tbl_listing" width="100%">
	 
 
<tr><th>Member Name</th><td><?php echo $users[0]['member_username'].' ['.$users[0]['member_id'].']';  ?>
<input type="hidden" name="hidMemberId" id="hidMemberId" value="<?php echo $users[0]['member_id'];?>" />
</td></tr>
<tr><th>List names</th>
	<td><?php 
	$strOptions ='<select name="contact_lists" id="contact_lists">';
	for($i=0;$i<count($contact_list);$i++){
	$strOptions .= "<option value='{$contact_list[$i]['subscription_id']}'>{$contact_list[$i]['subscription_title']}->[{$contact_list[$i]['total_contact_in_list']}]</option>";
	}
	$strOptions .='</select>';
	
	echo $strOptions;
	?>
<a onclick="openSubscriptionForm();" href="javascript:void(0);" title="Create New List" class="btn cancel list">
        <i class="icon-plus"></i>Create New List
</a>	
	</td>
</tr>
<tr><th>Contacts added after(including this date)</th>
<td><?php echo form_input(array('name'=>'added_on_after','id'=>'added_on_after','maxlength'=>50,'size'=>38,'value'=>'')) ; ?>
			<SCRIPT type="text/javascript">
				$(function(){
					$("#added_on_after").datepicker({ dateFormat: 'yy-mm-dd' });
				});
			</SCRIPT></td></tr>
<tr><th>Contacts added before(including this date)</th>
<td><?php echo form_input(array('name'=>'added_on_before','id'=>'added_on_before','maxlength'=>50,'size'=>38,'value'=>'')) ; ?>
			<SCRIPT type="text/javascript">
				$(function(){
					$("#added_on_before").datepicker({ dateFormat: 'yy-mm-dd' });
				});
			</SCRIPT></td></tr>
<tr><th>Campaign sent more than </th>
	<td><input name="sent_counter" id="sent_counter" type="text" maxlength="3" size="38" value="0" /></td>
</tr>
<tr><th>Responsive</th>
	<td>
		<select name="is_responsive" id="is_responsive">
			<option value="a">All</option>
			<option value="y">Yes</option>
			<option value="n">No</option>
		</select>
	</td>
</tr>
<tr><th>Status</th>
	<td>
		<select name="subscriber_status" id="subscriber_status">
			<option value="1">Active</option>
			<option value="0">DNM</option>
		</select>
	</td>
</tr>
<tr><th>Email-Id like</th>
	<td><input name="search_key" id="search_key" type="text" maxlength="50" size="38" /></td>
</tr>
<tr><td colspan="2"><input type="button" name="btnFilter" id="btnFilter" value=" Filter Now "  class="inputbuttons" /></td></tr>
</table>
</form>

<div id="contacts"></div>
<div class="tblheading"> &nbsp; </div>
<div id="subscription_menu" style="display: none;">
	<div id="add-list">
		<form onsubmit="ajaxSubscriptionFrm(this); return(false);" method="post" class="form-website" id="subscriptionfrm"  name="subscriptionfrm">
			<h5 style="margin-top:0px;">Enter List Name</h5>
			<div class="subscription_msg info"></div>
			<div>
			<input type="text" name="subscription_title" value="" id="subscription_title" maxlength="250" size="40" class="subscription_title"  />          </div>
			<div class="btn-group">
			<input type="submit" name="subscription_submit" value="Save" id="btnEdit" content="Submit" class="btn confirm"  /><button name="campaign_cancel" type="button" value="Cancel" onclick="$.fancybox.close();" class="btn cancel" >Cancel</button>          
			</div>
		</form>
	</div>
</div>