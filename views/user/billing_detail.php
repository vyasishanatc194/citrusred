<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Billing Detail</title>
<?php echo link_tag('webappassets/css/invoice-style.css?v=6-20-13'); ?>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-1.5.1.min.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.0.0.js?v=6-20-13"></script>
<script type="text/javascript">
    $(document).ready(function(){
		$(".fancybox").fancybox();
		jQuery('#email_receipt').live('click',function(){
			jQuery.ajax({
				url: "<?php echo base_url() ?>account/email_billing_receipt/<?php echo $user_transactions['member_id'];?>/<?php echo $user_transactions['transaction_id'];?>",
				type:"POST",
				success: function(data) {
					$('.info').show();
					$('.info').html("Email was Sent");
					$('.info').hide(10000);
				}
			});
		});
    });

</script>
<link href="<?php echo $this->config->item('webappassets');?>js/fancybox/fancy.css?v=6-20-13" rel="stylesheet" type="text/css" />
</head>
<body>

	<div class="wraper ">
    	<div class="container">
        	<div class="main-div">
            <!-- header -->
            <div class="header">
      <div class="header-div">
            	<a href="<?php echo base_url(); ?>"><img src="<?php echo $this->config->item('webappassets');?>images-front/logo.png?v=6-20-13" class="logo" alt="RedCappi - Email Marketing System" /></a>

              </div>
                    <div class="billing-div">Billing receipt</div>
                    	<div class="print-div">

                          <div class="print-link">
                          <ul>
                          <li><a title="print receipt" target="_blank" href="javascript:window.print()">Print receipt</a></li>
                          <li><a  class="subscr_list "  title="email this receipt" id="email_receipt" href="javascript:void(0); ">Email this receipt</a></li>
                          </ul>
                          </div>
                        </div>
            </div>
            <!-- end header -->
            <!-- body -->
            <div class="order-div">
			<div class="info" style="display:none;color:red;font-weight:bold;"></div>
            <table width="90" border="0" cellspacing="1" cellpadding="0" class="table-div">
  <tr>
    <td colspan="2" class="tbl-heading">Payment Details</td>
    </tr>
  <tr>
    <th width="471">Invoice ID</th>
    <td width="462"><?php echo $user_transactions['transaction_id']; ?></td>
  </tr>
  <tr>
    <th>Payment Date</th>
    <td  class="tb2-white">
		<?php
			$datetime = strtotime($user_transactions['transaction_date']);
			$date = date("F d, Y", $datetime);
			echo $date;
		?>

	</td>
  </tr>
  <tr>
    <th>Amount Paid</th>
    <td>$<?php if($user_transactions['gateway_response']=="ADMIN")echo "0"; else  echo  round($user_transactions['amount_paid'], 2); ?></td>
  </tr>
  <tr>
    <th>Details </th>
    <td class="tb2-white">
	<?php 
	//echo '<pre style="display:none">';print_r($user_transactions);echo '</pre>';
	if(in_array($user_transactions['user_id'],array(3215, 3333, 3388, 3489, 4252))){?>
	Monthly charge for newsletter editor and service
	<?php }elseif(in_array($user_transactions['transaction_id'],array(13450, 13328, 13307, 13321, 13262, 13195, 12993,12766,12711,2759,8879,8684, 4445, 5531))){?>
	6 Months charge for list size <?php echo $user_transactions['package_min_contacts']." to ".$user_transactions['package_max_contacts']; ?>
	<?php }elseif(in_array($user_transactions['transaction_id'],array(7001))){?>
	8 Months charge for list size <?php echo $user_transactions['package_min_contacts']." to ".$user_transactions['package_max_contacts']; ?>
	<?php }elseif(in_array($user_transactions['transaction_id'],array(13449, 13385, 13134, 13108, 12788, 12477, 12469, 11140, 11866, 9770, 9058, 8754,8420,4446, 7199))){?>
	3 Months charge for list size <?php echo $user_transactions['package_min_contacts']." to ".$user_transactions['package_max_contacts']; ?>
	<?php }elseif(in_array($user_transactions['transaction_id'],array(11570, 12438, 12159, 9079,8877))){?>
	2 Months charge for list size <?php echo $user_transactions['package_min_contacts']." to ".$user_transactions['package_max_contacts']; ?>
	<?php }elseif(in_array($user_transactions['transaction_id'],array(13380, 13194, 13261, 13210, 10756,10401,9962,3428,10132))){?>
	Annual charge for list size <?php echo $user_transactions['package_min_contacts']." to ".$user_transactions['package_max_contacts']; ?>
	<?php }elseif(in_array($user_transactions['transaction_id'],array(12841, 3991, 6404, 7154))){?>
	Misc. charge
	<?php }elseif(in_array($user_transactions['transaction_id'],array(4761, 5006))){?>
	Misc. overage charge
	<?php }elseif(in_array($user_transactions['transaction_id'],array(13523, 4775))){?>	
	Misc. send overage charge
	<?php }elseif(in_array($user_transactions['transaction_id'],array(4497))){?>
	Misc. charge for additional contacts
	<?php }elseif('credit'==$user_transactions['package_recurring_interval']){ ?>
		Purchased <?php echo $user_transactions['credit_count'];?> Email Credits
	<?php }
	else{?>
	<?php if('years'==$user_transactions['package_recurring_interval'])echo 'Annual'; else echo 'Monthly';?> charge for list size <?php echo $user_transactions['package_min_contacts']." to ".$user_transactions['package_max_contacts']; ?>
	<?php }?>
	<?php if($user_transactions['gateway_response']=="ADMIN") echo "<br/>(Comped by RedCappi)"; ?></td>
  </tr>
 

</table>


<table width="90" border="0" cellspacing="1" cellpadding="0" class="table-div">
  <tr>
    <td colspan="2" class="tbl-heading">Billing Details</td>
    </tr>
  <tr>
    <th width="471">Billed to</th>
    <td width="462">
    <?php 
    if($user_transactions['user_id'] == 10034){
    	echo "Christopher Messner";
    }else{
    	echo $user_transactions['first_name']." ".$user_transactions['last_name']; 
    }    
    ?></td>
  </tr>
  <tr>
    <th>Company</th>
    <td  class="tb2-white"><?php echo $user_transactions['company']; ?></td>
  </tr>
  <tr>
    <th>Phone</th>
    <td><?php echo $user_transactions['phone_number']; ?></td>
  </tr>
  <tr>
    <th>Email Address </th>
    <td class="tb2-white"><a href="#"><?php echo  $user_transactions['email_address']; ?></a></td>
  </tr>
  <tr>
    <th>Billing Address</th>
    <td>
    <?php 
    if($user_transactions['user_id'] == 10034){
    	echo "Fridtjof Nansen Straße 36<br/>";
    	echo "AT- 9800 Spittal an der Drau";
    }else{
    	echo  $user_transactions['address'];
    } 
    ?></td>
  </tr>
  <?php if($user_transactions['transaction_id'] != 10401) { ?>
   <tr>
    <th>Paid with</th>
    <td class="tb2-white">
	<?php if('Paypal' == $user_transactions['gateway']){
		echo 'PayPal';
	}else{
	?>
		<?php if(3428 == $user_transactions['transaction_id']){echo 'cash';}else{?>card ending in <?php echo $user_transactions['credit_card_last_digit']; }?>
	<?php } ?>
	</td>
  </tr>
<?php } ?>
</table>
            </div>
            <!-- end body -->
            <div class="bottom-line"></div>
            <!-- footer -->
            <div class="footer">
            	<div class="footer-div">
                	<h2>RedCappi LLC</h2>
                    <p>1046 West Kinzie Street<br />
Suite 300 - #370<br />
Chicago, IL<br />
<?php echo SYSTEM_EMAIL_FROM ;?> </p>
                </div>
            </div>
          <!-- end footer -->
            </div>
            <div class="main-bottom"><img src="<?php echo $this->config->item('webappassets');?>images-front/bottobg.png?v=6-20-13"  alt=""/></div>
        </div>
    </div>
</body>
</html>