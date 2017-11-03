<script type="text/javascript">
// Popup login form
$(document).ready(function(){
  if('245' != $('#country').val())
    $('div#country_custom_div').hide();
  else
    $('div#country_custom_div').show();
});
function showCustom(dpdCountry){
  if('245' == dpdCountry.value)
    $('div#country_custom_div').show();
  else
    $('div#country_custom_div').hide();
}
</script>
<!--[body]-->
  <div id="body-dashborad">
    <div class="container">
      <h1>
        <a href="<?php echo  site_url("user/change_password");?>" class="btn add">Change Password</a>
        <a href="<?php echo  site_url("upgrade_package_cim/index");?>" class="btn add">My Plan</a>
        Account
      </h1>
      <div class="left-menu account">
        <h2>Profile</h2>
        <?php
        /// display all messages
        if (is_array($messages)):
          echo "<div class='info'>";
          foreach ($messages as $type => $msgs):
            foreach ($msgs as $message):
              echo ($message . '<br />');
            endforeach;
          endforeach;
          echo "</div>";
        endif;
        ?>
        <?php
          if(validation_errors()){
          echo '<div class="info">'.validation_errors().'</div>';
          }
        ?>
        <div class="profile-container">
          <form  method="post" class="form-website" name="account_update" >
            <strong>First Name</strong>
            <input type="text" size="45" name="first_name" value="<?php echo $user_info['first_name']; ?>"/>
            <strong>Last Name</strong>
            <input type="text" size="45" name="last_name" value="<?php echo $user_info['last_name']; ?>"/>
            <strong>Email Address</strong>
            <input type="text" size="45" name="email_address" value="<?php echo $user_info['email_address']; ?>"/>
            <strong>Company/Organization</strong>
            <input type="text" size="45" name="company" value="<?php echo $user_info['company']; ?>"/>
            <strong>Address</strong>
            <input type="text" size="45" name="address_line_1" value="<?php echo $user_info['address_line_1']; ?>"/>
            <strong>City</strong>
            <input type="text" size="45" name="city" value="<?php echo $user_info['city']; ?>"/>
            <strong>State</strong>
            <input type="text" size="45" name="state" value="<?php echo $user_info['state']; ?>"/>
            <strong>Zip Code</strong>
            <input type="text" size="45" name="zipcode" value="<?php echo $user_info['zipcode']; ?>"/> 
            <strong>Country</strong>
            <select name="country" id="country" onchange="javascript: showCustom(this);">
            <?php
              foreach($country_info as $country){
                if($country['country_id']==$user_info['country']){
                  echo "<option value='".$country['country_id']."' selected>".$country['country_name']."</option>";
                }else{
                  echo "<option value='".$country['country_id']."'>".$country['country_name']."</option>";
                }
              }
            ?>
            </select>
            <div id="country_custom_div">
              <strong>Country Name</strong>
              <input type="text" maxlength="50" name="country_custom" id="country_custom" value="<?php echo $user_info['country_custom'];?>" />
            </div>
			<strong>Time zone</strong>
            <select name="member_time_zone">
			<?php		
			$timezones = getTimezones();
			foreach($timezones as $k=>$v){	
			if($v == $user_info['member_time_zone'])$sel = "selected";else $sel = '';
			echo"<option value='$v' {$sel}>$k</option>";
			}			
			?>
			</select>
            <input type="submit" class="btn confirm" name="action" value="Save" title="Submit" alt="Submit" style="margin-top: 30px" />
          </form>
        </div>
      </div>
      <div class="right-menu account">
        <h2>
          My Billing
        <?php if(count($user_transactions)<=0) { ?>
          <a href="<?php echo  site_url("upgrade_package_cim/index"); ?>" class="btn cancel">Upgrade Now</a>
        <?php }else { ?>
          <a href="<?php echo site_url('update_billing_cim/index'); ?>" class="btn cancel">Update Billing</a>
        </h2>
       
           <?php
          if($package_detail['payment_type'] == 1){
				//$payment_type = 'Paypal';
              ?>
                  
                  
                  
          <table border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>
              <strong>Payment Method</strong>
            </td>
            <td>
              Paypal
            </td>
          </tr>
          
          <tr>
            <td>
              <strong>Paypal Email</strong>
            </td>
            <td>
              <?php echo $package_detail['paypal_payer_email']; ?>
            </td>
          </tr>
          
         
         
	
        </table>
          
                  <?php
              
          }else{
          ?>
          
        <table border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>
              <strong>Last 4 Digits on Card:</strong>
            </td>
            <td>
              <?php echo $user_credit_card_info['credit_card_last_digit']; ?>
            </td>
          </tr>
          <tr>
            <td>
              <strong>Cardholder name:</strong>
            </td>
            <td>
              <?php echo $user_credit_card_info['card_holder_name']; ?>
            </td>
          </tr>
          <tr>
            <td>
              <strong>
                Expiration:
              </strong>
            </td>
            <td>
              <?php
                if($user_credit_card_info['expiration_date'][0]==0){
                echo substr($user_credit_card_info['expiration_date'], 1);
              }else{
                echo $user_credit_card_info['expiration_date'];
              }
              ?>
            </td>
          </tr>
		  <?php if($user_info['show_sent_counter']){ ?>
		  <tr><td><strong>Campaign Sent Count:</strong></td>
            <td><?php echo $user_credit_card_info['campaign_sent_counter'];?></td>
          </tr>
		  <?php } ?>
        </table>
		 <?php }?>
        <h2>My Invoices <a href="<?php echo site_url('account/invoices_view_all'); ?>" class="btn cancel">View All</a></h2>
        <table border="0" cellspacing="0" cellpadding="0">
          <tr>
            <th>Date </th>
            <th>Amount</th>
            <th>View</th>
          </tr>
          <?php foreach($user_transactions as $transaction){ ?>
          <tr>
            <td>
              <?php
                $datetime = strtotime($transaction['transaction_date']);
                 $date = date("M d, Y", $datetime);
                 echo '<strong>'.$date.'</strong>';
              ?>
            </td>
            <td>
              $<?php if($transaction['gateway_response']=='ADMIN')echo '0' ; else echo round($transaction['amount_paid'],2) ; ?>
            </td>
            <td>
              <a target="_blank" href="<?php echo  site_url("account/billing_detail/".$transaction['transaction_id']);?>" >#<?php echo $transaction['transaction_id'] ; ?></a>
            </td>
          </tr>
          <?php } ?>
        </table>
	  <?php } ?>
	  <?php if($package_detail['package_recurring_interval'] == 'credit'){  ?>
		<h2>My Email Credits</h2>
        <table border="0" cellspacing="0" cellpadding="0">
          <tr>
            <th>Credits Purchased</th>
            <td><?php echo $max_Email = ($package_detail['max_campaign_quota'] >  0)? $package_detail['max_campaign_quota'] : $userCredit['max_email'];?></td>
          </tr>
		  <tr>
            <th>Credits Used</th>
            <td><?php echo $package_detail['campaign_sent_counter'];?></td>
          </tr>
          <tr>
            <th>Credits Available</th>
            <td><?php echo $max_Email - $package_detail['campaign_sent_counter'];?></td>
          </tr>
		  
          
        </table>
      <h2>Purchased Credits <a href="<?php echo site_url('account/credit_view_all'); ?>" class="btn cancel">View All</a></h2>
        <table border="0" cellspacing="0" cellpadding="0">
          <tr>
            <th>Date</th>
            <th>Email Credits</th>
            <th>Amount</th>
          </tr>
          <?php 
          if(count($getCreditList) > 0 ){ 
            foreach($getCreditList as $creditList){ ?>
          <tr>
            <td>
              <?php 
                $datetime = strtotime($creditList['create_date']);
                $date = date("M d, Y", $datetime);
                echo '<strong>'.$date.'</strong>';
              ?>
            </td>
            <td><?php echo $creditList['credit_count'];?></td>
            <td><?php echo '$'.round($creditList['total_price'],2);?></td>
          </tr>
          
          <?php } } ?>
          
          
        </table>
	  <?php } ?>
    </div>
  </div>
</div>
<?php
function getTimezones(){
return
array (
  '(GMT-12:00) International Date Line West' => 'Pacific/Wake',
  '(GMT-11:00) Midway Islands Time' => 'Pacific/Apia',
  '(GMT-10:00) Hawaii Standard Time' => 'Pacific/Honolulu',
  '(GMT-09:00) Alaska Standard Time' => 'America/Anchorage',  
  '(GMT-08:00) Pacific Standard Time' => 'America/Los_Angeles',
  '(GMT-07:00) Mountain/Phoenix Standard Time' => 'America/Phoenix',   
  '(GMT-06:00) Central Standard Time' => 'America/Chicago',  
  '(GMT-05:00) Eastern Standard Time' => 'America/New_York',
  '(GMT-05:00) Indiana Eastern Standard Time' => 'America/Indiana/Indianapolis',
  '(GMT-04:00) Puerto Rico and US Virgin Islands Time' => 'America/Halifax',
  '(GMT-03:30) Canada Newfoundland Time' => 'America/St_Johns',
  '(GMT-03:00) Brazil-Eastern/Argentina Standard Time' => 'America/Sao_Paulo',  
  '(GMT-02:00) Mid-Atlantic' => 'America/Noronha',
  '(GMT-01:00) Central African Time' => 'Atlantic/Azores',
  '(GMT-01:00) Cape Verde Is.' => 'Atlantic/Cape_Verde', 
  '(GMT) Greenwich Mean Time : Dublin' => 'Europe/London',  
  '(GMT+01:00) European Central Time' => 'Europe/Berlin', 
  '(GMT+02:00) Eastern European Time' => 'Europe/Istanbul', 
  '(GMT+02:00) (Arabic) Egypt Standard Time' => 'Asia/Jerusalem',
  '(GMT+03:00) Eastern African Time' => 'Africa/Nairobi',
  '(GMT+03:30) Middle East Time' => 'Asia/Tehran',
  '(GMT+04:00) Near East Time' => 'Asia/Muscat',
  '(GMT+04:30) Kabul' => 'Asia/Kabul',
  '(GMT+05:00) Pakistan Lahore Time' => 'Asia/Karachi',
  '(GMT+05:30) India Standard Time' => 'Asia/Calcutta',
  '(GMT+05:45) Kathmandu' => 'Asia/Katmandu',
  '(GMT+06:00) Bangladesh Standard Time' => 'Asia/Dhaka',
  '(GMT+06:00) Sri Jayawardenepura' => 'Asia/Colombo',
  '(GMT+06:30) Rangoon' => 'Asia/Rangoon',
  '(GMT+07:00) Vietnam Standard Time' => 'Asia/Bangkok',
  '(GMT+07:00) Jakarta' => 'Asia/Bangkok',
  '(GMT+08:00) China Taiwan Time' => 'Asia/Hong_Kong',
  '(GMT+09:00) Japan Standard Time' => 'Asia/Tokyo',
  '(GMT+09:30) Australia Central Time' => 'Australia/Adelaide',
  '(GMT+10:00) Australia Eastern Time' => 'Australia/Sydney',
  '(GMT+11:00) Solomon Standard Time' => 'Asia/Magadan',
  '(GMT+12:00) New Zealand Standard Time' => 'Pacific/Auckland', 
  '(GMT+13:00) Nuku\'alofa' => 'Pacific/Tongatapu',
);
}
?>