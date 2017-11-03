<form name="Src_frm" id="Src_frm" method="post" action="<?php echo site_url("webmaster/campaign/edit/approval/$cid");?>">
	<table border="0" cellspacing="0" cellpadding="0" class="tbl_forms" width="100%">
		<tr>
			<td width="10%">Select a message</td>
			<td><select name="disallow_msg" id="disallow_msg" onchange="javascript:attachDisallowedMsg(this.value)">
				<option value=''>--select--</option>
				<?php
				foreach($disallowedmsg as $k=>$v){
					echo "<option value='$k'>$v</option>";					
				}
				?>
			</select></td>
		</tr>
		<tr>
			<td width="10%">Comment for user</td>
			<td><textarea name="disallow_comment" id="disallow_comment" style="width:300px;height:150px;" cols="220" rows="12"></textarea></td>

		</tr>
		<tr>
			
			<td colspan="2">
				<input type="submit" name="btn_search" id="btn_search" value="Disallow Now" class="inputbuttons"/>		
				<input type="button" name="btnClose" id="btnClose" value="Cancel" onclick="javascript: parent.$.fancybox.close();" />				
			</td>
		</tr>
	</table>
</form>
