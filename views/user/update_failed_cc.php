  <script type="text/javascript">
   // On load, style typical form elements
   	$(document).ready(function() {
        $("#security_code").hover(function(e){
            $("#large")
                    .html("<img src="+ $(this).attr("alt") +" alt='Large Image' /><br/>")
                    .fadeIn("slow");
        }, function(){
            $("#large").fadeOut("fast");
        });
    });
	function submit_frm(){
		//document.billing_form.packageId.submit();
		for (var i=0; i < document.getElementsByName ('package_id').length; i++)
		{
		   if (document.getElementsByName ('package_id')[i].checked)
			  {
			  var rad_val = document.getElementsByName ('package_id')[i].value;
			  document.billing_form.packageId.value=rad_val;
			  }
		}
		document.billing_form.submit();
	}
	function fetchPayableAmount(){
    if($('#coupon_code').val() ==''){
      $('#coupon_code_msg').html('Enter coupon code');
      return;
    }
    var block_data ="ccode="+$('#coupon_code').val();
    jQuery.ajax({
      url: "<?php echo base_url() ?>ajax/fetchPayableAmount/",
      type:"POST",
      data:block_data,
      success: function(data) {
        if('err'==data){
        $('#coupon_code_msg').html('Invalid coupon code');
		$('#coupon_code').val('');
        }else{
          $('#coupon_code_msg').html(data);
        }
      }
    });
  }
  </script> 
<style>
#security_code {
  cursor: pointer;
}
#large {
  display: none;
  position: absolute;
  color: #FFFFFF;
  background: #ebebeb;
  padding: 1px;
}
</style>
 <!--[body]-->

 <div id="body-dashborad">
  <div class="container update-profile account">
	<h1>Update Billing</h1>


			  <?php
				/// display all messages
				if (is_array($messages)):
					foreach ($messages as $type => $msgs):
						foreach ($msgs as $message):
							echo ('<div class="msg ' .  $type .' info">' . $message . '</div>');
						endforeach;
					endforeach;
				endif;
				?>
			  <?php
				if(validation_errors()){
					echo '<div style="color:#FF0000;" class="msg info">'.validation_errors().'</div>';
				}
				?>

			<form action="" method="post"  name="billing_form">
				<div class="update-profile-container">
				<h2>Billing Information</h2>
				<div class="update-profile-container">
					<strong>First Name</strong>
					<?php echo form_input(array('name'=>'first_name','id'=>'first_name','maxlength'=>120,'size'=>32,'value'=> $user_packages['first_name'] != "" ? $user_packages['first_name'] : set_value('first_name') ));   ?>

					<strong>Last Name</strong>
					<?php echo form_input(array('name'=>'last_name','id'=>'last_name','maxlength'=>120,'size'=>32,'value'=>$user_packages['last_name'] != "" ? $user_packages['last_name'] : set_value('last_name'))); ?>

					<strong>Street Address</strong>
					<?php echo form_input(array('name'=>'address1','id'=>'address1','maxlength'=>60,'size'=>32,'value'=>$user_packages['address'] != "" ? $user_packages['address'] : set_value('address') )); ?>

					<strong>City</strong>
					<?php echo form_input(array('name'=>'city','id'=>'city','maxlength'=>50,'size'=>32,'value'=>$user_packages['city'] != "" ? $user_packages['city'] : set_value('city') )); ?>

					<strong>State/Province</strong>
					<?php echo form_input(array('name'=>'state','id'=>'state','maxlength'=>50,'size'=>32,'value'=>$user_packages['state'] != "" ? $user_packages['state'] : set_value('state') )); ?>

					<strong>Zip/Post code</strong>
					<?php echo form_input(array('name'=>'zipcode','id'=>'zipcode','maxlength'=>50,'size'=>32,'value'=>$user_packages['zip'] != "" ? $user_packages['zip'] : set_value('zip') )); ?>

					<strong>Country</strong>
					<?php $country_name= $user_packages['country'] != "" ? $user_packages['country'] : "United States";?>
					<select name="country" id="country">
						<?php foreach($country_info as $country){
							if($country_name==$country['country_name']){
								echo "<option value='".$country['country_name']."' selected='selected'>".$country['country_name']."</option>";
							}else{
								echo "<option value='".$country['country_name']."'>".$country['country_name']."</option>";
							}
						}?>
					</select>
				</div>
				<h2>Card Information</h2>
				<div class="update-profile-container">
					<strong>Name on card</strong>
					<?php echo form_input(array('name'=>'credit_card_holder_name','id'=>'credit_card_holder_name','maxlength'=>120,'size'=>32,'value'=>set_value('credit_card_holder_name'),'autocomplete'=>'off')); ?>

					<strong>Credit card number</strong>
					<?php echo form_input(array('name'=>'cc_number','id'=>'cc_number','maxlength'=>120,'size'=>32,'value'=>set_value('cc_number'),'autocomplete'=>'off')); ?>

					<strong>Expiration date</strong>
					<?php
					$months_array=array(1=>'January',2=>'Febuary',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
					$expMo = 'id="expiration-date-month"';
          echo form_dropdown('ccexp_month',$months_array,$_REQUEST['ccexp_month'], $expMo);

					for($i=date('Y');$i< date('Y') + 25;$i++){
						$year_array[$i] = $i;
					}

					$expYr = 'id="expiration-date-year"';
          echo form_dropdown('ccexp_year',$year_array,$_REQUEST['ccexp_year'], $expYr);
					?>
					<strong>Security Code <img id="security_code" src="<?php echo $this->config->item('webappassets');?>images-front/info1.png?v=6-20-13" alt="<?php echo $this->config->item('webappassets');?>images-front/security_code.jpg?v=6-20-13" align="absmiddle" /><br/><span id="large"></span></strong>
					<?php echo form_input(array('name'=>'cvv','id'=>'cvv','maxlength'=>12,'size'=>15,'value'=>set_value('cvv'),'autocomplete'=>'off' )); ;?>
					<img align="absmiddle" src="<?php echo base_url();?>webappassets/images-front/credit_card_logos.jpg?v=6-20-13" alt="">
					<div class="AuthorizeNetSeal" style="display:inline-block;vertical-align:middle;">
						<script type="text/javascript" language="javascript">var ANS_customer_id="73403142-6ece-4003-8197-ab3a3dfd95ce";</script>
						<script type="text/javascript" language="javascript" src="//verify.authorize.net/anetseal/seal.js?v=6-20-13" ></script>
						<a href="http://www.authorize.net/" id="AuthorizeNetText" target="_blank">Payment Gateway</a>
					</div>
					<table width="98%" border="0">
				     <tr <?php #if($user_packages['coupon_used'] !== true && $user_packages['is_payment'] == 0):?> style="display:block" <?php #else:?> style="display:none" <?php #endif;?>>
                      <td colspan="2">
                        <div class="discount_coupon_container">
                          <span class="label"><strong>Discount Code</strong></span>
						  <input name="coupon_code" type="text" size="32" value="" id="coupon_code" class="clean" />
                          <a href="javascript:void(0);" onclick="javascript:fetchPayableAmount();" class="btn cancel inline-block" style="margin-left:20px;">
                            Redeem
                          </a>
                          <span id="coupon_code_msg" class="error"></span>
                        </div>
                      </td>
                    </tr>
					</table>
					<div>
						<a href="javascript:void(0);" title="" class="btn confirm" onclick="submit_frm();">Update</a>
						<a href="<?php echo $previous_page_url; ?>" title="" class="btn cancel">Cancel</a>
					</div>


				  <?php
					/* foreach($user_packages as $key=>$user){
						echo form_hidden($key,$user);
					} */
					echo form_hidden('action','save');
					echo form_hidden('packageId','');
					echo form_close();
				?>
            </div>
			</div>
		</form>
	</div>
</div>
