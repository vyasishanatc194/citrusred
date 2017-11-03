<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery.uniform.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/payment_cb.js"></script>
<link rel="stylesheet" href="<?php echo $this->config->item('webappassets');?>css/uniform.default.css?v=6-20-13" media="screen" />
<script type='text/javascript'>
  // On load, style typical form elements
  $(document).ready(function() {
    $("select").uniform();
    $("#security_code").hover(function(e){
      $("#large")
        .html("<img src="+ $(this).attr("alt") +" alt='Large Image' /><br/>")
        .fadeIn("slow");
    }, function(){
      $("#large").fadeOut("fast");
    });
    $(".check_package").click(function(){
      $('td.value').removeClass('color-green');
      $('td.value').addClass('color-blue');
      $(this).parent('.value').removeClass('color-blue');
      $(this).parent('.value').addClass('color-green');
    });
  });

  function getCheckedPackageId(){
    var rad_val =0;
    for (var i=0; i < document.getElementsByName('package_id').length; i++) {
      if (document.getElementsByName ('package_id')[i].checked) {
        rad_val = document.getElementsByName ('package_id')[i].value;
      }
    }
    return rad_val;
  }
  function submit_frm(){
    for (var i=0; i < document.getElementsByName('package_id').length; i++) {
      if (document.getElementsByName ('package_id')[i].checked) {
        var rad_val = document.getElementsByName ('package_id')[i].value;
        document.billing_form.packageId.value=rad_val;
      }
    }

	var payment_type = jQuery('.payment_type:checked').val();
    //if((rad_val == '-1')||(rad_val == <?php echo $selected_package; ?>)){
    if(rad_val == <?php echo $selected_package; ?>) {
      fancyAlert('Select a different package to change your plan.');
      return false;
      // Do nothing
    } else {
		if(payment_type == 'paypal'){
            
            
           
            var postdata =  jQuery("#billing_form").serialize();
            jQuery.post('/upgrade_package_cim/payment_by_paypal_cim', postdata, function (result) {
                var resp = jQuery.parseJSON(result);
				
				//console.log(resp);return false;
                if(resp.status == 'success'){
				
				 var custom = jQuery("#custom").val();
                jQuery("#custom").val(custom+"|"+resp.transaction_id); 
				jQuery(".submit_button").html('Loading....');
				jQuery(".submit_button").css('background-color','Gray');
				jQuery(".submit_button").attr('disabled','disabled');
				jQuery(".submit_button").css('font-size','15px');
				jQuery(".submit_button").css('background-image','None');
				
					jQuery("#first_name").val(resp.first_name);
					jQuery("#last_name").val(resp.last_name);
					jQuery("#address1").val(resp.address1);
					jQuery("#city").val(resp.city);
					jQuery("#state").val(resp.state);
					jQuery("#zip").val(resp.zipcode);
					
							
					
                    jQuery("#item_name").val(resp.package_title);
                    
					jQuery("#a1").val(resp.package_price);
					
					//jQuery("#a1").val('2.123');
					
					if(resp.payment_year_month == 'months'){
						jQuery("#t1").val('M');
						jQuery("#t3").val('M');
					}else{
						jQuery("#t1").val('Y');
						jQuery("#t3").val('Y');
					}
					
					if(resp.no_of_uses == 'all'){
						jQuery("#a3").val(resp.package_regular_price);
						
						//jQuery("#a3").val('1.00');
						
					}else{
						if(resp.payment_year_month == 'months'){
							jQuery("#p1").val(resp.no_of_uses);
						}else{
							jQuery("#p1").val(1);
						}
						jQuery("#a3").val(resp.package_regular_price);
					}
					
					
					
					
                    
                    jQuery("#return_url").val('<?php echo $this->PAYPAL_SUCCESS_URL; ?>?userpackageid='+resp.member_package_id);
                    
                    if(resp.package_price == '0' && resp.package_regular_price == '0'){
                        location.reload();
                    }
                    if(resp.package_title == 'Free'){
                        location.reload();
                    }else{
                        jQuery("#billing_form").submit();
                    }
                }else{
					jQuery(".paypal_validation_div").show();
					jQuery(".paypal_validation_div").html(resp.messgae);
				}
				
				
				
				
            });
		}else{
			document.billing_form.submit();
		}
    }
  }
  /*
  fancyAlert to display message
  */
  function fancyAlert(msg) {
    $.fancybox({
      'content' : "<div style='width:400px;'><h5>Alert</h5><p style='text-align:center'>"+msg+"</p><div class='btn-group'><span class='btn cancel' onclick='$.fancybox.close();'>Close</span></div></div>"
    });
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
  <!--[body]-->
    <div class="container">
      <div class="dashboard-home" id="order-page">
        <h1>Plans			
		<?php if($mode =='annual')
			echo"<a href='". base_url()."upgrade_package_cim/index'  class='btn cancel'>See Monthly Plans</a>";
			else
			echo"<a href='". base_url()."upgrade_package_cim/annual'  class='btn cancel'>See Annual Plans</a>";
		?>	
		</h1>
        <?php //if($selected_package == 0)echo $ccFailedMsg = "<div class='error'>Your transaction declined. You need to update your credit-card.</div>";?>
		
		<?php 
		$payment_type = $arrUserForPaypal['payment_type'];
		if($selected_package == 0){
                            if($payment_type == 'paypal'){
                                echo $ccFailedMsg = "<div class='error'>Your transaction declined. You need to update your paypal.</div>";
                            }else{
                                echo $ccFailedMsg = "<div class='error'>Your transaction declined. You need to update your credit-card.</div>";
                            }
                        
                        }
                    ?>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="50%" valign="top">
              <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tbl-pricing">
                <?php
                  $i=1;
                  //for calcuating tr is even or odd
                  //Fetch packages from packages array
                  foreach($packages as $package){
                  if($i%2==0){
                    $class="class='even'";
                  }else{
                    $class="class='odd'";
                  }
                ?>
                  <tr <?php echo $class; ?>>
                    <th width="50%" class="value"><?php echo number_format($package['package_min_contacts']);  ?>-<?php echo number_format($package['package_max_contacts']);  ?> <br>
                      <input type="hidden" name="this_package_<?php echo $package['package_id'];?>" id="this_package_<?php echo $package['package_id'];?>" value="<?php echo number_format($package['package_price'],0);  ?>" />
                      <span class="txt-italic"> Contacts </span>
                    </th>
                    <?php if($checked_package_id==$package['package_id']){ ?>
                    <td width="50%"  class="value color-green">
                      <input class="check_package" name="package_id" id="package_id" type="radio" value="<?php echo $package['package_id'];  ?>" checked="checked" <?php echo $package['enable']; ?> >
                    <?php }else{ ?>
                      <td width="50%" class="value color-blue <?php if($package['enable']=="disabled"){?>inactive<?php } ?>">
                        <input class="check_package" name="package_id" id="package_id" type="radio" value="<?php echo $package['package_id'];  ?>" <?php echo $package['enable']; ?>>
                    <?php } if($package['package_price']==0){ ?>
                      Free&nbsp;&nbsp;
                    <?php }else{ ?>
                      $<?php echo number_format($package['package_price'],0);  ?><span class="txt-italic">/<?php if( $package['package_recurring_interval']=='months') echo 'mo'; else echo 'yr'; ?></span>
                    <?php } if($package['enable']=="disabled"){?>
                      <br/><font class="list_small">List larger than plan</font>
                    <?php } ?>
                      </td>
                  </tr>
                <?php $i++; } ?>
                </table>
              </td>
              <td width="3%"  valign="top" style="padding-top:60px;" class="divider-verticle1"></td>
              <td width="47%" valign="top">
                <?php
                /// display all messages
                if (is_array($messages)):
                  echo "<div class='info'>";
                  foreach ($messages as $type => $msgs):
                    foreach ($msgs as $message):
                      echo ('<span class="' .  $type .' ">' . $message . '</span>');
                    endforeach;
                  endforeach;
                  echo "</div>";
                endif;
                ?>
                <?php
                if(validation_errors()){
                  echo '<div style="color:#FF0000;" class="error info">'.validation_errors().'</div>';
                }
                ?>
				<div style="color:#FF0000;" class="error info paypal_validation_div" style="display:none"></div>
                <form action="" method="post" class="billing-form" name="billing_form" id="billing_form">
                  <h4 class="heading-txt">Billing Information</h4>
                  <table width="98%" border="0" cellpadding="3" cellspacing="0">
                    <tr>
                      <td class="label"><strong>First name</strong></td>
                      <td class="label"><strong>Last name</strong></td>
                    </tr>
                    <tr>
                      <td><?php echo form_input(array('name'=>'first_name','class'=>'clean','id'=>'first_name','maxlength'=>120,'size'=>35,'value'=> $user_package['first_name'] != "" ? $user_package['first_name'] : set_value('first_name') ));   ?>                    </td>
                      <td><?php echo form_input(array('name'=>'last_name','class'=>'clean','id'=>'last_name','maxlength'=>120,'size'=>35,'value'=>$user_package['last_name'] != "" ? $user_package['last_name'] : set_value('last_name'))); ?></td>
                    </tr>
                    <tr>
                      <td colspan="2" class="label"><strong>Street address</strong></td>
                    </tr>
                    <tr>
                      <td colspan="2"><?php echo form_input(array('name'=>'address1','class'=>'clean','id'=>'address1','maxlength'=>60,'size'=>80,'value'=>$user_package['address'] != "" ? $user_package['address'] : set_value('address1') )); ?></td>
                    </tr>
                    <tr>
                      <td class="label"><strong>City</strong></td>
                      <td class="label"><strong>State/Province</strong></td>
                    </tr>
                    <tr>
                      <td><?php echo form_input(array('name'=>'city','class'=>'clean','id'=>'city','maxlength'=>50,'size'=>35,'value'=>$user_package['city'] != "" ? $user_package['city'] : set_value('city') )); ?></td>
                      <td><?php echo form_input(array('name'=>'state','class'=>'clean','id'=>'state','maxlength'=>50,'size'=>35,'value'=>$user_package['state'] != "" ? $user_package['state'] : set_value('state') )); ?></td>
                    </tr>
                    <tr>
                      <td class="label"><strong>Zip/Post code</strong></td>
                      <td class="label" colspan="2"><strong>Country</strong></td>
                    </tr>
                    <tr>
                      <td><?php echo form_input(array('name'=>'zipcode','class'=>'clean','id'=>'zipcode','maxlength'=>50,'size'=>35,'value'=>$user_package['zip'] != "" ? $user_package['zip'] : set_value('zipcode') )) ; ?></td>
                      <td>
                        <?php
                          $country_name= $user_package['country'] != "" ? $user_package['country'] : "United States";;
                        ?>
                        <select name="country" id="country" style="width:200px !important;">
                          <?php
                          foreach($country_info as $country){
                            if($country_name==$country['country_name']){
                              echo "<option value='".$country['country_name']."' selected='selected'>".$country['country_name']."</option>";
                            }else{
                              echo "<option value='".$country['country_name']."'>".$country['country_name']."</option>";
                            }
                          }
                          ?>
                        </select>
                      </td>
                    </tr>
                  </table>
                  <?php /* Start code - CB */?>
				<?php
				$payment_type = $arrUserForPaypal['payment_type'];?>
                  <h4 class="heading-txt">Payment Information</h4>
                  <h4 class="heading-txt"><input type="radio" name="payment_type_name" <?php if($payment_type == 'paypal'): echo 'checked';endif;?> class="payment_type" id="payment_type" value="paypal"/> Paypal
                  <input type="radio" name="payment_type_name" <?php if($payment_type == 'credit_card'): echo 'checked';endif;?> class="payment_type" id="payment_type" value="credit_card"/> Credit Card
                  </h4>
                  
                    <?php /* Start Paypal Hidden Fields */?>
                  
                  
                  <?php  
                    $payment_year_month = ($mode =='annual')?'annual' : 'months';
                  ?>
                    <input type="hidden" name="payment_year_month" value="<?php echo $payment_year_month;?>" />
                    <input type='hidden' name='business' value='<?php echo $this->PAYPAL_EMAIL; ?>' />
                    <input type='hidden' name='cpp-logo-image' value="<?php echo $this->config->item('webappassets');?>images/redesign/logo.png" />                   
                    <input type="hidden" name="custom" id="custom" value="<?php echo $arrUserForPaypal['session_member_id']."|".$payment_year_month;?>" />
                    <input type='hidden' name='cmd' value='_xclick-subscriptions' />
                    <input type="hidden" name="page_style" value="paypal" />
                    <input type="hidden" name="lc" value="" />
                    <input type="hidden" name="no_shipping" value="1" />
                    <input type="hidden" name="currency_code" value="$" />
					<input type="hidden" name="no_note" value="1" />
                    <input type="hidden" name="charset" value="utf-8" />
                    <input type="hidden" name="src" value="1" />
                    <?php 
                        //a1 = Amount;
                        //p1 = trail Cycle;
                        //t1 = trail Period M=montrh,Y=year ,D=Days, W='week';
                    ?>
                    <input type="hidden" name="a1" id="a1" value="10" /> 
                    <input type="hidden" name="p1" id="p1" value="1" />
                    <input type="hidden" name="t1" id="t1" value="D"/>
					
                    <input type="hidden" name="a3" id="a3" value="10" /> 
                    <input type="hidden" name="p3" id="p3" value="1" />
                    <input type="hidden" name="t3" id="t3" value="D"/>
                    
                    
					
					<input type="hidden" name="zip" id="zip" value="" />

                    
                    
                    <input type='hidden' id='item_name' name='item_name' value='Test Product' />
                    <input name="notify_url" value="<?php echo $this->PAYPAL_NOTIFY_URL; ?>" type="hidden" />
                    <!--<input type='hidden' id='amount' name='amount' value='100'>-->
					<input type="hidden" value="2" name="rm" />      
                    <input type='hidden' name='no_shipping' value='1' />
                    <input type='hidden' name='currency_code' value='USD' />
                    <input type='hidden' name='handling' value='0' />
                    <input type='hidden' name='cancel_return' value='<?php echo $this->PAYPAL_CANCEL_URL; ?>' />
                    <input type='hidden' id="return_url" name='return' value='<?php echo $this->PAYPAL_SUCCESS_URL; ?>' />
                    <?php /* End Paypal Hidden Fields */?>
                  
                  
                  <?php /* END Code - CB */?>
                  
                  <div class="card_info_div" <?php if($payment_type == 'paypal'):?> style="display: none" <?php endif;?>>
                  
                  <!-- <table width="98%" border="0" class="<?php echo $user_package['customer_payment_profile_id']>0 ?  "update_credit_card" : "";?>" <?php echo $user_package['customer_payment_profile_id']>0 ? "style=display:none;" : ""; ?>>-->
					<table width="98%" border="0" class="<?php echo $user_package['customer_payment_profile_id']>0 ?  "update_credit_card" : "";?>" >
					<?php 
					// If payment-done & via credit-card then
					//if($user_package['payment_type'] == 0 && $user_package['is_payment'] == 0){ ?>
                    <tr>
                      <td class="label"><strong>Name on card</strong></td>
                      <td class="label"><strong>Credit card number</strong></td>
                    </tr>
                    <tr>
                      <td><?php echo form_input(array('name'=>'credit_card_holder_name','class'=>'clean','id'=>'credit_card_holder_name','maxlength'=>120,'size'=>35,'value'=>set_value('credit_card_holder_name'),'autocomplete'=>'off')); ?></td>
                      <td align="left"><?php echo form_input(array('name'=>'cc_number','class'=>'clean','id'=>'cc_number','maxlength'=>120,'size'=>35,'value'=>set_value('cc_number'),'autocomplete'=>'off' )); ?></td>
                    </tr>
                    <tr>
                      <td class="label"><strong>Expiration date</strong></td>
                      <td class="label">
                        <strong>Security Code</strong>
                        <img id="security_code" src="<?php echo $this->config->item('webappassets');?>images-front/info1.png?v=6-20-13" alt="<?php echo $this->config->item('webappassets');?>images-front/security_code.jpg?v=6-20-13" align="absmiddle"><br/><span id="large"></span>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <?php
                        $months_array=array(1=>'01',2=>'02',3=>'03',4=>'04',5=>'05',6=>'06',7=>'07',8=>'08',9=>'09',10=>'10',11=>'11',12=>'12');
                        $expMo = 'id="expiration-date-month"';
                        echo form_dropdown('ccexp_month',$months_array,$_REQUEST['ccexp_month'], $expMo);

                        for($i=date('Y');$i< date('Y') + 25;$i++){
                          $year_array[$i] = $i;
                        }

                        $expYr = 'id="expiration-date-year"';
                        echo form_dropdown('ccexp_year',$year_array,$_REQUEST['ccexp_year'], $expYr);
                        ?>
                      </td>
                      <td>
                        <?php echo form_input(array('name'=>'cvv','class'=>'clean','id'=>'cvv','maxlength'=>12,'size'=>15,'value'=>set_value('cvv'),'autocomplete'=>'off' )); ;?>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2">                       
                        <img style="float:left;display:block;margin-top:14px;" src="<?php echo base_url();?>webappassets/images-front/credit_card_logos.jpg?v=6-20-13" alt="">
                        <div class="AuthorizeNetSeal" style="display:block;vertical-align:middle;float:right;margin-right:17px"> <script type="text/javascript" language="javascript">var ANS_customer_id="73403142-6ece-4003-8197-ab3a3dfd95ce";</script> <script type="text/javascript" language="javascript" src="//verify.authorize.net/anetseal/seal.js?v=6-20-13" ></script> <a href="http://www.authorize.net/" id="AuthorizeNetText" target="_blank">Payment Gateway</a> </div>
                      </td>
                    </tr>
                    <?php 
					// If payment-done & via credit-card then
					//}else
						if($user_package['payment_type'] == 0 && $user_package['is_payment'] == 1){ ?>
                    <!--tr>
                      <td colspan="2"><div style="margin-bottom:5px;float:left;"> <a href="<?php echo base_url();?>update_billing_cim/index"  title="" class="small orange awesome fr" ><span>Update</span></a> </div> <div style="margin-bottom:5px;float:right;"> <a href="javascript:void(0);" onclick="javascript:$('.credit_card_info').show();$('.update_credit_card').hide();$('#update_billing').val('0');" title="" class="small orange awesome fr" ><span>Cancel</span></a> </div></td>
                    </tr-->
                   
                    <tr>
                      <td colspan="2">
                        <?php if($user_package['customer_payment_profile_id'] && $user_package['credit_card_last_digit'] != ''){ ?>
                        <input type="hidden" name="update_billing" id="update_billing" value="0"  />
                        <?php }else{ ?>
                        <input type="hidden" name="update_billing" id="update_billing" value="1"  />
                        <?php } ?>
                      </td>
                    </tr>
					 <?php } ?>
                  </table>
				  <!-- card details -->
				  <?php if($user_package['payment_type'] == 0 && $user_package['customer_payment_profile_id'] > 0 && $user_package['credit_card_last_digit'] != ''){ ?>
                    <table border="0" cellspacing="0" cellpadding="0" style="border:none;" class="tbl-small credit_card_info card_info_div">
					  <tr>
                        <td width="80%" class="noborder"><strong ><b class="boldblack">CARD</b>  ending in </strong><?php echo $user_package['credit_card_last_digit']; ?> </td>
                      </tr>
                      <tr >
                        <td  class="noborder"><strong class="boldblack">Cardholder name: </strong> <?php echo $user_package['card_holder_name']; ?></td>
                      </tr>
                      <tr>
                        <td  class="noborder"><strong class="boldblack">Expiration </strong><?php echo @substr($user_package['expiration_date'],-7); ?></td>
                      </tr>
                    </table>
                    <div class="black-bar1" style="margin-bottom:15px;">
                      <a href="<?php echo base_url();?>update_billing_cim/index" class="btn cancel fr credit_card_info card_info_div" style="margin:7px 22px 20px 0">
                        Update Credit Card Info
                      </a>
                    </div>
                    <table border="0" align="right" cellpadding="0" cellspacing="0"  class="credit_card_info">
                      <tr>
                        <td colspan="2">
                          <img align="absmiddle" src="<?php echo base_url();?>webappassets/images-front/credit_card_logos.jpg?v=6-20-13" alt="">
                          <div class="AuthorizeNetSeal" style="display:inline-block;vertical-align:middle;"> <script type="text/javascript" language="javascript">var ANS_customer_id="73403142-6ece-4003-8197-ab3a3dfd95ce";</script> <script type="text/javascript" language="javascript" src="//verify.authorize.net/anetseal/seal.js?v=6-20-13" ></script> <a href="http://www.authorize.net/" id="AuthorizeNetText" target="_blank">Payment Gateway</a> </div>
                        </td>
                      </tr>
                    </table>
                  <?php } ?>
				  <!-- card details -->
				  
                  </div>
                  
                  <?php /* Start code - CB */?>
                  <table width="98%" border="0">
				     <tr <?php if($arrUserForPaypal['coupon_used'] !== true && $user_package['is_payment'] == 0):?> style="display:block" <?php else:?> style="display:none" <?php endif;?>>
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
					
                    <tr>
                      <td colspan="2">
                        <div style="padding-bottom: 10px;">
                          <input name="terms_conditions" type="checkbox" value="1" class="clean" />
                          <strong>I Agree To RedCappi <a href="<?php echo base_url().'terms';?>" target="_blank">Terms &amp; Conditions </a>.</strong>
                        </div>
                      </td>
                    </tr>
                  </table>
                  <?php /* END code - CB */?>
                  
                  <?php
                    if($user_package['customer_payment_profile_id']){
                      echo form_hidden('update_subscription','true');
                    }
                    echo form_hidden('action','save');
                    echo form_hidden('packageId','');
                    echo form_close();
                  ?>
				  
                </td>
              </tr>
            </table>
            <div id="order-page-btn-group">
              <a href="<?php echo $previous_page_url; ?>" title="" class="btn cancel inline-block" >Cancel</a>

              <button type="button" class="btn confirm inline-block submit_button" onclick="return submit_frm();">Submit</button>
              <!--
              <a href="javascript:void(0);" title="" class="btn confirm inline-block submit_button" onclick="return submit_frm();">Submit</a>-->
            
            </div>
            </div>
          </div>
        </div>

<!--[/body]-->
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
