<?php /************For Transaction ************/
if(count($user_transactions) >0){ 
?>
	<div id="body-dashborad">
		<div class="container update-profile">
			<h1>Invoices</h1>
			<div class="update-profile-container invoices">
		
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="list">
					<tr>
					  <th><strong>Invoice Date</strong></th>
					  <th><strong>Charge</strong></th>
					  <th><strong>Invoice</strong></th>
					</tr>
					<?php
					foreach($user_transactions as $transaction){ 
					?>
						<tr>
							<td>
								<a href="<?php echo  site_url("account/billing_detail/".$transaction['transaction_id']);?>" target="_blank">
								<?php
									$datetime = strtotime($transaction['transaction_date']);
									echo $date = date("F d, Y", $datetime);
								?>
								</a>
							</td>
							<td>
								$<?php if($transaction['gateway_response']=='ADMIN')echo '0' ; else echo round($transaction['amount_paid'],2) ; ?>
							</td>
							<td>
								<a href="<?php echo  site_url("account/billing_detail/".$transaction['transaction_id']);?>" target="_blank">#<?php echo $transaction['transaction_id'] ; ?></a>
							</td>
						</tr>
					<?php 
					}
					?>
				</table>
	
			</div>
		</div>
	</div>
<?php 
}elseif(count($user_transactions_credit) >0){  /**************For Credit View**********************/
?>
	<div id="body-dashborad">
		<div class="container update-profile">
			<h1>Email Credit</h1>
			<div class="update-profile-container invoices">
		
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="list">
					<tr>
					  <th><strong>Date</strong></th>
					  <th><strong>Email Credits</strong></th>
					  <th><strong>Amount</strong></th>
					</tr>
					<?php
					foreach($user_transactions_credit as $transaction){ 
					?>
						<tr>
							<td>
								<?php
									$datetime = strtotime($transaction['create_date']);
									echo $date = date("F d, Y", $datetime);
								?>
								
							</td>
							<td>
								<?php echo $transaction['credit_count'];?>
							</td>
							<td>
								$<?php echo round($transaction['total_price'],2) ; ?>
							</td>
						</tr>
					<?php 
					}
					?>
				</table>
	
			</div>
		</div>
	</div>
<?php 
}else{ ?>
	<div id="body-dashborad">
		<div class="container update-profile">
			<h1><?php echo (!isset($user_transactions)) ? 'Email Credit' : 'Invoices' ?></h1>
			<div class="update-profile-container invoices">
		
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="list">
					<tr>
					  <td>No Record Found!</td>
					</tr>
					
				</table>
	
			</div>
		</div>
	</div>
<?php
	}
?>

