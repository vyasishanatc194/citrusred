<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-1.5.1.min.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.mousewheel-3.0.4.pack.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.pack.js?v=6-20-13"></script>
<link href="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.css?v=6-20-13" rel="stylesheet" type="text/css" />
<SCRIPT type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-ui-1.8.13.custom.min.js?v=6-20-13"></SCRIPT>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css-front/blitzer/jquery-ui-1.8.14.custom.css?v=6-20-13" />



<?php /* Start code by citrusbug*/?>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
 
<div id="dialog" class="dialog" title="Grade Report" style="display:none;width:auto">
</div>

<div id="dialog_batch" class="dialog" title="Batch Report" style="display:none;width:auto">
</div>

<style>
.ui-dialog
{
	width:550px !important;
	height:300px !important;
	
}
.dialog
{
	width:500px !important;
	height:300px !important;
	
}
</style>
<?php /* End code by citrusbug*/?>





  
<script type="text/javascript">
	jQuery('#btnFilter').live('click',function(){
			var hmid = $('#hidMemberId').val();
			var lid = $('#contact_lists').val();
			var adddedBefore = $('#added_on_before').val();
			var is_responsive = $('#is_responsive').val();
			var status = $('#subscriber_status').val();
			var search_key = $('#search_key').val();
			$('#contacts').html("<img src='<?php echo $this->config->item('webappassets');?>images/ajax_loading.gif' width='220' height='19' />");
			  
			  
				jQuery.ajax({
				  url: "<?php echo base_url() ?>webmaster/contacts_segmentation/get_contacts_filter_count/",
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
	function attachContactVmta(){
		var newVMTA = $('#vmta').val();
		var reclimit = $('#txtLimit').val();
		if( confirm(reclimit+" Filtered contacts will be added to selected pipeline. Are you sure for this?")){
			
			$('#contacts').prepend("<img src='<?php echo $this->config->item('webappassets');?>images/ajax_loading.gif' width='220' height='19' />");
			jQuery.ajax({
				  url: "<?php echo base_url() ?>webmaster/contacts_segmentation/attachContactVmta/"+newVMTA,
				  type:"post",
				  data: 'l='+reclimit,
				  success: function(x) {   
					if(x == 'ok')alert("Pipeline attached.");
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

	
	/*Start Code for report of grade  -> CB*/
	function reportgrade(){
		
		
		jQuery.ajax({
			url: "<?php echo base_url() ?>webmaster/contacts_segmentation/report_grade/" ,
			type:"post",
			success: function(x) { 
				$( "#dialog" ).dialog();
				  var response = jQuery.parseJSON(x);
				  google.charts.load("current", {packages:["corechart"]});
				  google.charts.setOnLoadCallback(drawChart);
				  function drawChart() {
					var data = google.visualization.arrayToDataTable(response);
								
								

					/*
					
					
					[
					["Task","Hours per Day"],
					["A","1"],
					["B","1"],
					["D","2"]
					]
					
					
					
					[
					["Task","Hours per Day"],
					["A","1"],
					["B","1"],
					["D","2"]]
					*/
					
					var options = {
					  title: '',
					  is3D: true,
					};
					var chart = new google.visualization.PieChart(document.getElementById('dialog'));
					chart.draw(data, options);
				  }
	  
			/*	var response = jQuery.parseJSON(x);
				var myData = new Array(['2005', 2], ['2006', 1]);
				var myChart = new JSChart('dialog', 'bar');
				myChart.setDataArray(myData);
				myChart.setBarValues(false);
				myChart.draw();
				
				*/
				
			}
		});	 
			
		
		
		
		
		
		//var reclimit = $('#txtLimit').val();
		//if(confirm(reclimit+" Filtered contacts will be marked as unresponsive. Are you sure to do this?")){			
			/*	$('#contacts').prepend("<img src='<?php echo $this->config->item('webappassets');?>images/ajax_loading.gif' width='220' height='19' />");
				
			jQuery.ajax({
				url: "<?php echo base_url() ?>webmaster/contacts_segmentation/mark_unresponsive/" ,
				type:"post",
				data: 'l='+reclimit,
				success: function(x) { 
					if(x == 'ok')alert("Contacts marked unresponsive.");
						$('#contacts img').remove();
					}
			});	 
			*/
		//}	
	}
	
              
	/*END Code for report of grade  -> CB*/
        
        
        
        /*Start Code for Batch of grade  -> CB*/
	function batchgrade(){
		
		jQuery.ajax({
			url: "<?php echo base_url() ?>webmaster/contacts_segmentation/batch_grade/" ,
			type:"post",
			success: function(x) { 
				$( "#dialog" ).dialog();
                                $( ".ui-dialog-title" ).html('Batch Report');
                                
				  var response = jQuery.parseJSON(x);
				  google.charts.load("current", {packages:["corechart"]});
				  google.charts.setOnLoadCallback(drawChart);
				  function drawChart() {
					var data = google.visualization.arrayToDataTable(response);
					
					var options = {
					  title: '',
					  is3D: true,
					};
					var chart = new google.visualization.PieChart(document.getElementById('dialog'));
					chart.draw(data, options);
				  }
	  
				
			}
		});	 
			
		
		
		
		
		
		//var reclimit = $('#txtLimit').val();
		//if(confirm(reclimit+" Filtered contacts will be marked as unresponsive. Are you sure to do this?")){			
			/*	$('#contacts').prepend("<img src='<?php echo $this->config->item('webappassets');?>images/ajax_loading.gif' width='220' height='19' />");
				
			jQuery.ajax({
				url: "<?php echo base_url() ?>webmaster/contacts_segmentation/mark_unresponsive/" ,
				type:"post",
				data: 'l='+reclimit,
				success: function(x) { 
					if(x == 'ok')alert("Contacts marked unresponsive.");
						$('#contacts img').remove();
					}
			});	 
			*/
		//}	
	}
	
        
	/*END Code for Batch of grade  -> CB*/
	
	
	
	
	
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
	 
 
<tr><th>Member Name</th><td colspan='2'><?php echo $users[0]['member_username'].' ['.$users[0]['member_id'].']';  ?>
<input type="hidden" name="hidMemberId" id="hidMemberId" value="<?php echo $users[0]['member_id'];?>" />
</td></tr>
<tr><th>List names</th>
	<td colspan='2'><?php 
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
<tr><th>Email-Id</th>
	<td><select name="email_operator" style="width:80px;"><option value="like">Like</option><option value="not like">Not Like</option></select></td>
	<td><input name="search_key" id="search_key" type="text" maxlength="200" size="38" /></td>
</tr>
<tr><th>Contact Status</th>
	<td><select name="subscriber_status_operator" style="width:80px;"><option value="=">Equal to</option><option value="!=">Not equal to</option></select></td>
	<td><select name="subscriber_status"><option value="1">Active</option><option value="0">Unsubscribed</option><option value="2">Complaints</option><option value="3">Hard Bounce</option><option value="4">Soft Bounce</option><option value="5">Added to DNM</option><option value="-999">Deleted</option><option value="">All</option></select></td>	
</tr>
<tr><th>Contact via signup-form</th>
	<td>&nbsp;</td>
	<td><select name="is_signup"><option value="">All</option><option value="1">True</option><option value="0">False</option></select></td>	
</tr>
<tr><th>Campaign-sent</th>
	<td><select name="sent_operator" style="width:80px;"><option value="">All</option><option value="=">Equal to</option><option value=">">More than</option><option value="<">Less than</option></select></td>
	<td><input name="sent_counter" id="sent_counter" type="text" maxlength="3" size="38" value="0" /></td>	
</tr>
<!--tr><th>Campaign-delivered</th>
	<td><select name="delivered_operator" style="width:80px;"><option value="">All</option><option value="=">Equal to</option><option value=">">More than</option><option value="<">Less than</option></select></td>
	<td><input name="delivered_counter" id="delivered_counter" type="text" maxlength="3" size="38" value="0" /></td>	
</tr-->

<tr><th>Responsive</th><td>&nbsp;</td>
	<td>
		<select name="is_responsive" id="is_responsive">
			<option value="">All</option>
			<option value="y">Yes</option>
			<option value="n">No</option>
		</select>
	</td>
</tr>
<tr><th>Campaign-clicked</th>
	<td><select name="clicked_operator" style="width:80px;"><option value="">All</option><option value="=">Equal to</option><option value=">">More than</option><option value="<">Less than</option></select></td>
	<td><input name="clicked_counter" id="clicked_counter" type="text" maxlength="3" size="38" value="0" /></td>	
</tr>
<tr><th>Contacts added date</th>
<td><select name="subscriber_date_added_operator" style="width:80px;"><option value="">All</option><option value="=">Equal to</option><option value=">">More than</option><option value="<">Less than</option></select></td>
<td><?php echo form_input(array('name'=>'subscriber_date_added','id'=>'subscriber_date_added','maxlength'=>50,'size'=>38,'value'=>date('Y-m-d'))) ; ?>
			<SCRIPT type="text/javascript">
				$(function(){
					$("#subscriber_date_added").datepicker({ dateFormat: 'yy-mm-dd' });
				});
			</SCRIPT></td></tr>

<tr><th>Last sent date</th>
<td><select name="last_sent_date_operator" style="width:80px;"><option value="">All</option><option value="=">Equal to</option><option value=">">More than</option><option value="<">Less than</option></select></td>
<td><?php echo form_input(array('name'=>'last_sent_date','id'=>'last_sent_date','maxlength'=>50,'size'=>38,'value'=>date('Y-m-d'))) ; ?>
			<SCRIPT type="text/javascript">
				$(function(){
					$("#last_sent_date").datepicker({ dateFormat: 'yy-mm-dd' });
				});
			</SCRIPT></td></tr>

<tr><th>Last read date</th>
<td><select name="last_read_date_operator" style="width:80px;"><option value="">All</option><option value="=">Equal to</option><option value=">">More than</option><option value="<">Less than</option></select></td>
<td><?php echo form_input(array('name'=>'last_read_date','id'=>'last_read_date','maxlength'=>50,'size'=>38,'value'=>date('Y-m-d'))) ; ?>
			<SCRIPT type="text/javascript">
				$(function(){
					$("#last_read_date").datepicker({ dateFormat: 'yy-mm-dd' });
				});
			</SCRIPT></td></tr>
 
<tr><th>Status change date</th>
<td><select name="status_change_date_operator" style="width:80px;"><option value="">All</option><option value="=">Equal to</option><option value=">">More than</option><option value="<">Less than</option></select></td>
<td><?php echo form_input(array('name'=>'status_change_date','id'=>'status_change_date','maxlength'=>50,'size'=>38,'value'=>date('Y-m-d'))) ; ?>
			<SCRIPT type="text/javascript">
				$(function(){
					$("#status_change_date").datepicker({ dateFormat: 'yy-mm-dd' });
				});
			</SCRIPT></td></tr>
			
			
			
			
			
			<tr><th>Grade</th>
<td>
<?php 
$i = 0;
foreach(range(A,E) as $key ): ?>
<input type="checkbox" value="<?php echo $key;?>" name="grade[]" id="grade_<?php echo $i;?>" />
	<label for="grade_<?php echo $i;?>"><?php echo $key;?></labe>
<?php 
$i++;
endforeach;?>


</td></tr>
			
			
			
			
			
			

<tr><td colspan="3"><input type="button" name="btnFilter" id="btnFilter" value=" Filter Now "  class="inputbuttons" /></td></tr>
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