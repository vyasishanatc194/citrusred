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
<div class="tblheading">Invoice List <?php //Display paging links
echo $paging_links ?>
<?php
	if(count($user_transactions)&&(is_null($user['cancel_subscription_date']))) {
		echo "<a href='".site_url('webmaster/users_manage/cancel_subscription/'.$member_id)."' style='float:right'>Cancel Subscription</a>";
	}
?>
</div>

<table class="tbl_listing" width="100%"> 
	<thead> 
		<tr> 
			<th>
				Date 
			</th>
			<th>
				 Amount
			</th>
			<th>
				 View
			</th>
		</tr> 
	</thead> 
	<?php 
//List all user_transactions
if(count($user_transactions)) {
foreach($user_transactions as $transaction){
?>
<tr>
	<td>
		<?php
			$datetime = strtotime($transaction['transaction_date']);
			$date = date("M d, Y", $datetime);
			echo '<b class="boldblack">'.$date.'</b>';
		?>
	</td>
	<td>
		$<?php echo round($transaction['amount_paid'],2) ; ?>
	</td>
	<td>
		<a href="<?php echo site_url("webmaster/users_manage/billing_detail/".$transaction['transaction_id']);?>" target="_blank">#<?php echo $transaction['transaction_id'] ; ?></a>
	</td>
</tr>
<?php } } else { ?>
<tr><td colspan="3" align="center">No Invoice List Available</td></tr>
<?php } ?>
</table> 


