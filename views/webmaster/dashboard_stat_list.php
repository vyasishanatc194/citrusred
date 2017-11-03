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
<div class="tblheading">Dashboard Stat</div>
<table class="tbl_listing" width="100%"> 
	<thead> 
		<tr> 
			<td width="15%">Total Users</td><td><a href="<?php echo base_url() ?>webmaster/users_manage/users_list"><?php echo $total_users_count ;?></a></td>
		</tr>
		<tr>	
			<td width="15%">Paid users</td><td><a href="<?php echo base_url() ?>webmaster/users_manage/paid_users"><?php echo $paid_users_count ;?></a></td>		
		</tr>
		<tr>	
			<td width="15%">Admin comped. users</td><td><?php echo $admin_comped_users_count ;?></td>		
		</tr>
		<tr>	
			<td width="15%">Fail payment users</td><td><?php echo $fail_cc_users_count ;?></td>		
		</tr>
		<tr> 
			<td width="15%">Total Free Users</td>
			<td><?php echo $free_users_count ;?></td>		
		</tr> 
		<tr> 
			<td width="15%">Total paid users from beginning</td>
			<td><?php echo $paid_users_count_from_beginning ;?></td>		
		</tr>
		<tr> 
			<td width="15%">Avg. Customer subscription lifetime </td>
			<td><?php echo number_format($avg_subscription_lifetime,3) ;?> months</td>		
		</tr>
		<tr> 
			<td width="15%">Customer Churn (<?php echo $customerChurnMonthStr;?>)</td>
			<td><?php echo $customerChurn .'%' ;?> </td>		
		</tr>
		
		<tr><td width="15%">Users Requiring Upgrades</td>
			<td><a href="<?php echo base_url() ?>webmaster/users_manage/upgrade_users">View<?php echo $user_upgrade_count ;?></a></td>		
		</tr> 
		<tr> 
			<td width="15%">Number of new users registered today</td>
			<td><?php echo $registered_user_count ;?></td>
		</tr> 
		<tr> 
			<td width="15%">Total Campaigns  Approval</td>
			<td><a href="<?php echo base_url() ?>webmaster/campaign/approval"><?php echo $campaign_approval_count ;?></a></td>
		
		</tr> 
		<tr> 
			<td width="15%">
			Total Campaigns  Sent
			</td>
			<td>
				<a href="<?php echo base_url() ?>webmaster/dashboard_stat/sent_campaign"><?php echo $sent_campaign_count ;?></a>
			</td>
		
		</tr> 
		<tr> 
			<td width="15%">
				Report
			</td>
			<td>
				<a href="<?php echo  site_url("webmaster/activity_log");?>">Activity Log</a>
			</td>
		
		</tr>
		<tr> 
			<td width="15%">
				Notification
			</td>
			<td>
				<a href="<?php echo  site_url("webmaster/campaign");?>">Campaign Approval</a>
			</td>
		
		</tr> 
	</thead> 

</table> 


