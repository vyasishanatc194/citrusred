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
<div style="text-align: right;"></div> 
<table class="tbl_listing" width="100%"> 
	<thead> 
		<tr> 
			<th colspan="3">User Stats</th>
		</tr>
	</thead>
	<tbody>
		<tr> 
			<td><b>Total Users:</b> <a href="<?php echo base_url() ?>webmaster/users_manage/users_list"><?php echo $stats['total_users_count'] ;?></a></td>
			<td><b>Total Paid Users:</b> <a href="<?php echo base_url() ?>webmaster/users_manage/paid_users"><?php echo $stats['paid_users_count'] ;?></a></td>
			<td><b>Admin comped. users: </b><?php echo $stats['admin_comped_users_count'] ;?></td>
		</tr>
		<tr>
			<td><b>Total Engaged Users: </b><?php echo "Coming Soon";?></td>		 	
			<td><b>Total Free Users: </b><?php echo $stats['free_users_count'] ;?></td>
			<td><b>Users Requiring Upgrades: </b><a href="<?php echo base_url() ?>webmaster/users_manage/upgrade_users">View<?php echo $stats['user_upgrade_count'] ;?></a></td>
		</tr>
		<tr>
			<td><b>Free To Paid (Today): </b><?php echo $stats['free_to_paid_today'];?></td>
			<td><b>Free To Paid (Last 7 Days): </b><?php echo $stats['free_to_paid_last7days'];?></td>
			<td><b>Free To Paid (Last 30 Days): </b><?php echo $stats['free_to_paid_last30days'];?></td>
		</tr> 
		<tr>
			<td><b>Fail To Paid (Today): </b><?php echo $stats['fail_to_paid_today'];?></td>
			<td><b>Fail To Paid (Last 7 Days): </b><?php echo $stats['fail_to_paid_last7days'];?></td>
			<td><b>Fail To Paid (Last 30 Days): </b><?php echo $stats['fail_to_paid_last30days'];?></td>
		</tr> 
		<tr>	
			<td><b>New Users Registered Today: </b><?php echo $stats['registered_user_count'] ;?></td>
			<td><b>New Users This Month: </b><?php echo $stats['this_month_users'] ;?></td>		  
			<td><b>New Users Last Month: </b><?php echo $stats['last_month_users'] ;?></td>
		</tr> 
		<tr>
			<td><b>New Users Registered (Last 7 Days): </b><?php echo $stats['last_7days_users'] ;?></td>
			<td><b>New Users Registered (Last 30 Days): </b><?php echo $stats['last_30days_users'] ;?></td>
			<td>&nbsp;</td>
		</tr>
	 </tbody>
	 <thead> 
		<tr> 
			<th colspan="3">SaaS Metrics</th>
		</tr>
	</thead>
	 <tbody>
	 	<tr>	
			<td><b>Total Paid Users All Time: </b><?php echo $stats['paid_users_count_from_beginning'] ;?></td>
			<td><b>Avg. Customer Subscription Lifetime: </b><?php echo number_format($stats['avg_subscription_lifetime'],3) ;?></td>
			<td><b>Customer Churn: <?php echo $stats['sqlchurn'] ;?></td>
		</tr>
		<tr>	
			<td><b>MRR Expected: </b>$<?php echo $stats['mrr_expected'] ;?></td>
			<td><b>Avg. Revenue Per Customer (Monthly): </b>$<?php echo number_format($stats['monthly_avg_revenue_per_customer'],3) ;?></td>
			<td><b>Lifetime Value: </b>$<?php echo $stats['lifetime_value'] ;?></td>
		</tr>
		<tr>	
                    <td><b>MRR Expected Today: </b>$<?php echo $stats['mrr_expected_today'] ;?>&nbsp;<b>Credit Card</b>$<?php echo $stats['mrr_expected_today_credit_card'];?>&nbsp;<b>PayPal:</b>$<?php echo $stats['mrr_expected_today_paypal'];?></td>
			<td><b>MRR Expected This Week(Sun-Sat): </b>$<?php echo number_format($stats['mrr_expected_week_sun_sat'],3) ;?></td>
			<td>&nbsp;</td>
		</tr>
	</tbody>		
	<thead> 
		<tr> 
			<th colspan="3">Admin Tasks</th>
		</tr>
	</thead>
	 <tbody>
		<tr> 
			<td><b>Campaign Needing Approval: </b><a href="<?php echo base_url() ?>webmaster/campaign/approval"><?php echo $stats['campaign_approval_count'] ;?></a> &nbsp;  &nbsp;  &nbsp; <font color="red"><b>Delayed: </b><?php echo $stats['campaign_approval_delayed'] ;?></font></td>
			<td><b>Signup-Forms Needing Approval: </b><a href="<?php echo base_url() ?>webmaster/signupforms/index/unverified"><?php echo $stats['signupform_unverified'];?></a></td>
			<td><b>Auto-Responders Needing Approval: </b><a href="<?php echo base_url() ?>webmaster/autoresponders/index/unverified"><?php echo $stats['autoresponders_unverified'];?></a></td>
		</tr> 
		<tr> 
			<td><b>High Unsubscribes (Today & Yesterday): </b><?php echo ($stats['high_unsubscribes'] > 0)? "<a href='/webmaster/dashboard_stat/bad_campaign_report/unsubscribe'>".$stats['high_unsubscribes']."</a>" :  '0'; ?></td>
			<td><b>High Complaints (Today & Yesterday): </b><?php echo ($stats['high_spams'] > 0)? "<a href='/webmaster/dashboard_stat/bad_campaign_report/spam'>".$stats['high_spams']."</a>" :  '0'; ?></td>
			<td><b>High bounces (Today & Yesterday): </b><?php echo ($stats['high_bounces'] > 0)? "<a href='/webmaster/dashboard_stat/bad_campaign_report/bounce'>".$stats['high_bounces']."</a>" :  '0'; ?></td>
		</tr>
		<tr> 
			<td><b>Blocked Campaigns (Today & Yesterday): </b><a href='/webmaster/dashboard_stat/blocked_contacts'>Blocked campaigns/contacts</a></td>
                        <td><b>From Emails Needing Approval:</b><a href="<?php echo base_url() ?>webmaster/fromemail/index"><?php echo $stats['fromemail_unverified'];?></a></td>
			<td><b></b></td>
		</tr>
	  </tbody>	
	  <thead> 
		<tr> 
			<th colspan="3">System Performance/Metrics</th>
		</tr>		
		</thead>
	 	<tbody>
	 	
		
		<tr>
			<td><b>Payment Fail (Today): </b><?php echo $stats['fail_cc_users_today'];?></td>
			<td><b>Payment Fail (Last 7 Days): </b><?php echo $stats['fail_cc_users_last7days'];?></td>
			<td><b>Payment Fail (Last 30 Days): </b><?php echo $stats['fail_cc_users_last30days'];?></td>
		</tr>
	 	<tr> 
			<td><b>Total Campaigns Sent (Today): </b><a href="<?php echo base_url() ?>webmaster/dashboard_stat/sent_campaign"><?php echo $stats['sent_campaign_today'] ;?></a></td>
			<td><b>Total Campaigns Sent (Last 7 Days): </b><a href="<?php echo base_url() ?>webmaster/dashboard_stat/sent_campaign"><?php echo $stats['sent_campaign_last7days'] ;?></a></td>	
			<td><b>Total Campaigns Sent (All Time): </b><a href="<?php echo base_url() ?>webmaster/dashboard_stat/sent_campaign"><?php echo $stats['sent_campaign_count'] ;?></a></td>
		</tr> 
		<tr>
			<td><b>Total Email Sent (Today): </b><?php echo number_format($stats['sent_email_today']);?></td>
			<td><b>Total Email Sent (Last 7 Days): </b><?php echo number_format($stats['sent_email_last7days']);?></td>
			<td><b>Total Email Sent (All Time): </b><?php echo number_format($stats['sent_email_count']);?></td>
		</tr> 
		
	</tbody> 

</table> 

<script type="text/javascript">
     var time = new Date().getTime();
     $(document.body).bind("mousemove keypress", function(e) {
         time = new Date().getTime();
     });

     function refresh() {
         if(new Date().getTime() - time >= 600000) 
             window.location.reload(true);
         else 
             setTimeout(refresh, 100000);
     }

     setTimeout(refresh, 100000);
</script>
