<script type="text/javascript">
function resetForm(){
	document.searchFrm.type.value='';
	document.searchFrm.envId.value='';
	document.searchFrm.jobId.value='';
	document.searchFrm.resetThisForm.value='ok';
	 
	document.searchFrm.submit();

}
</script>
<div class="search">
	<fieldset>
		<legend>Search</legend>
		<form name='searchFrm' action=<?php echo site_url('webmaster/pmta_log/index/');?> method='post'>
		<input type="hidden" name="resetThisForm" value="notok" />
		<table width="100%" class="tbl_forms">
			<tr>
				<td>Log Type</td>
				<td>Email-ID</td>					
				<td>Campaign ID</td>					
				<td>Contact ID</td>
				<td>DSN Msg.</td>
			</tr>
			<tr>
				<td><select name="type" style="width:130px;">
					<option value=''>All</option>
					<option value='d' <?php echo ($type == 'd')?'selected':'' ; ?>>Delivered</option>
					<option value='b' <?php echo ($type == 'b')?'selected':'' ; ?>>Bounced</option>
					<option value='rb' <?php echo ($type == 'rb')?'selected':'' ; ?>>Async Bounced</option>
					<option value='f' <?php echo ($type == 'f')?'selected':'' ; ?>>Complaints</option>
					
					</select></td>					
				<td><input name="rcpt" style="width:150px;" type='text' value='<?php echo $rcpt; ?>'></td>					
				<td><input name="envId" style="width:100px;" type='text' value='<?php echo $envId; ?>'></td>					
				<td><input name="jobId" style="width:100px;" type='text' value='<?php echo $jobId; ?>'></td>				
				<td><input name="dsnDiag" style="width:250px;" type='text' value='<?php echo $dsnDiag; ?>'></td>				
			</tr>
			<tr><td colspan="5" align="right"><input type='submit' name='search' value='Search' style="width:150px">
				<input type='button' onclick="javascript:resetForm();" name='resetbtn' value='Reset'  style="width:150px">				
				</td>
			</tr>	
		</table>
		</form>
	</fieldset>
</div>
<div class="content">
	<h3>PMTA Log</h3>
	<span style="font-size:12px;">(Total Records:<?php echo $total_rows;?>)</font>
	<br />				
	<br />				
	<div class="paging"><?php echo $pagination; ?></div>
	<div class="data"><?php echo $table; ?></div>
	<div class="paging"><?php echo $pagination; ?></div>
</div>