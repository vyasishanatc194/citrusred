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
	
	function showIPR(){
		var fromdt = $('#date_from').val();
		var todt = $('#date_to').val();
		var pipeline = $('#pipeline').val();
		var rcuser = $('#rcusers').val();
		
		//var prevdt = new Date(fromdt);		
		//prevdt.setDate(prevdt.getDate()-1);		
		//var x = new Date(prevdt).toISOString().substring(0, 10);
		 
		//$('#prev_dt').html(x);
		
		$('#fdt').html(fromdt);
		$('#tdt').html(todt);
		
		
		jQuery.ajax({
		  url: "<?php echo base_url() ?>webmaster/report/showIPR/ajax",
		  type:"POST",
		  data: 'btn_search=Search&pipeline='+pipeline+'&rcuser='+rcuser+'&date_from='+fromdt+'&date_to='+todt,
		  success: function(data) {
			$('#gipr').html(data);
		  }
		});		
	}
	function showDailyIPR(date_dir){		
		var dtto = (date_dir == 'prev')?$('#prev_dt').html() : $('#next_dt').html();
		var dtfrom = new Date(dtto);		
		dtfrom.setDate(dtfrom.getDate()-1);
		dtfrom = new Date(dtfrom).toISOString().substring(0, 10);
		
		var nextdt = new Date(dtto);		
		nextdt.setDate(nextdt.getDate()+1);
		nextdt = new Date(nextdt).toISOString().substring(0, 10);		
		$('#prev_dt').html(dtfrom);
		$('#next_dt').html(nextdt);		
		$('#fdt').html(dtfrom);
		$('#tdt').html(dtto);
		$('#date_from').val(dtfrom);
		$('#date_to').val(dtto);
		
		var pipeline = $('#pipeline').val();
		var rcuser = $('#rcusers').val();
		jQuery.ajax({
		  url: "<?php echo base_url() ?>webmaster/report/showIPR/ajax",
		  type:"POST",
		  data: 'btn_search=Search&pipeline='+pipeline+'&rcuser='+rcuser+'&date_from='+dtfrom+'&date_to='+dtto,
		  success: function(data) {
			$('#gipr').html(data);
		  }
		});	
	}
</script>

 
<form name="Src_frm" id="Src_frm" method="post" action="<?php echo base_url().'webmaster/report/global_ipr';?>"> 
<input type="hidden" name="interval" id="interval" value="0" />
	<table border="1" cellspacing="0" cellpadding="0" class="tbl_forms">
		<tr><td><a href="javascript:void(0);" onclick="javascript:showDailyIPR('prev')">Show for <span id='prev_dt'><?php echo $previousDay;?></span></a></td>				
			<td align="right"><?php	echo "<b>Global IPR from UTC Date:</b> After <span id='fdt'>$date_from</span> to <span id='tdt'>$date_to</span>";?></td>
			<td><a href="javascript:void(0);" onclick="javascript:showDailyIPR('next')">Show for <span id='next_dt'><?php echo $nextDay;?></span></a></td>			 
		</tr>
		<tr>			
			<td width="10%">Pipeline<br/>			 
			<select name="pipeline" id="pipeline"  style="width:150px;"  onchange="javascript:showIPR()">
			<option value=''>ALL</option>		 
			<?php
				$selIt ='';
				foreach($arrPools as $this_vmta){
					if($pipeline == $this_vmta)$selIt = 'selected';else $selIt = '';
					echo "<option value='$this_vmta' $selIt>$this_vmta</option>";
				}
			?>		  
			</select>			
			</td>	
			<td align="right" colspan="2">RC Users<br/>							 
			<select name="rcusers" id="rcusers"  style="width:150px;"  onchange="javascript:showIPR()">
			<option value=''>ALL</option>		 
			<?php
				$selIt ='';
				foreach($arrRcusers as $this_userid=>$this_user){
					if($rcusers == $this_userid)$selIt = 'selected';else $selIt = '';
					echo "<option value='$this_userid' $selIt>$this_user</option>";
				}
			?>		  
			</select>
			</td>
			 
		</tr>
		<tr>
			<td>Date From<br/>
			
			<?php echo form_input(array('name'=>'date_from','id'=>'date_from','maxlength'=>50,'size'=>40 ,'value'=>$date_from)) ; ?>
			<SCRIPT type="text/javascript">
				$(function(){$("#date_from").datepicker({ dateFormat: 'yy-mm-dd' });});
			</SCRIPT> </td>
			<td colspan="2">Date To<br/>
			
			<?php echo form_input(array('name'=>'date_to','id'=>'date_to','maxlength'=>50,'size'=>40 ,'value'=>$date_to)) ; ?>
			<SCRIPT type="text/javascript">
				$(function(){$("#date_to").datepicker({ dateFormat: 'yy-mm-dd' });	});
			</SCRIPT> 
			
			<input type="button" name="btnSubmit" id="btnSubmit" value="Show" onclick="javascript:showIPR();" />
			</td>
		</tr>	
		 
	</table>
</form>

<div id="gipr">
<?php echo $strGlobalIPR;?>
</div>