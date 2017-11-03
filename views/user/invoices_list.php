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
  			<?php foreach($user_transactions as $transaction){ ?>
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
  		  <?php } ?>
  	   </table>
   </div>
  </div>
</div>
