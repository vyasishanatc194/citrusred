<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link href="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.css" rel="stylesheet" type="text/css" />
<SCRIPT type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-ui-1.8.13.custom.min.js"></SCRIPT>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css-front/blitzer/jquery-ui-1.8.14.custom.css" />

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

<div class="tblheading">Reseller Report<?php //Display paging links
echo $paging_links ?>

</div>

<form name="Src_frm" id="Src_frm" method="post" action="">
	<table border="0" cellspacing="0" cellpadding="0" class="tbl_forms">
		<tr>
			<td>Attach Referrer<br/><?php echo form_dropdown('referrer',$arrReferrerName,$referrer);?></td>
			<td>
				From Date<br/>
<?php 				
echo form_input(array('name'=>'from_date','id'=>'from_date','maxlength'=>50,'size'=>40 ,'value'=>$fdate)) ;
echo '<SCRIPT type="text/javascript"> $(function(){$("#from_date").datepicker({ dateFormat: "yy-mm-dd" });});	</SCRIPT>';
?>
			</td>
			<td>
				To Date<br/>
<?php 				
echo form_input(array('name'=>'to_date','id'=>'to_date','maxlength'=>50,'size'=>40 ,'value'=>$tdate)) ;
echo '<SCRIPT type="text/javascript"> $(function(){$("#to_date").datepicker({ dateFormat: "yy-mm-dd" });});	</SCRIPT>';
?>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<input type="submit" name="btn_search" id="btn_search" value="Search" class="inputbuttons"/>
				<input type="submit" name="btn_cancel" id="btn_cancel" value="Show All" class="inputbuttons"/>
			</td>
		</tr>
	</table>
</form>

<?php
echo "<pre>";
//print_r($arrReseller);
echo "</pre>";
?>
<table class="tbl_listing" width="100%"> 
	<thead> 

<tr>
<th>Sl.No.</th>
<th>Member</th>
<th>Tr. Date</th>
<th>Amount</th>
<th>Comission</th>
<th>No. of months</th>
</tr>
<?php 
//List all packages
if(count($arrReseller)) {
$i=1;
foreach($arrReseller as $reseller){
?>
<tr>
	<td><?php echo $i++;  ?></td>	
	<td><?php echo $reseller['member_username']. ' ['.$reseller['member_id'].']' ;  ?></td>
	<td><?php echo $reseller['transaction_date'] ;  ?></td>	
	<td><?php echo $reseller['transaction_amount'] ;  ?></td>
	<td><?php 
	if($reseller['commission_type'] == 0)
		echo '$'.$reseller['commission'];
	else
		echo '$'.($reseller['commission'] * $reseller['transaction_amount']) /100;
	?></td>
	<td><?php echo $reseller['commission_months']; if($reseller['commission_months'] > 900)echo' [Assumed lifetime]';  ?></td>
</tr>
<?php 
} //end for
?>
<tr><td colspan='3' align='right'><b>Total:</b></td><td><?php echo $totAmount ;  ?></td>
	<td colspan="2"><?php 
	if($reseller['commission_type'] == 0)
		echo '$'.$totAmount;
	else
		echo '$'.($reseller['commission'] * $totAmount) /100;
	?></td>
<?php
} else { ?>
<tr><td colspan="6" align="center">No Reseller Available</td></tr>
<?php } ?>
</table>

<?php echo form_close(); ?>
