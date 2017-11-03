<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery.uniform.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-ui-1.9.2.custom.js"></script>
<link rel="stylesheet" href="<?php echo $this->config->item('webappassets');?>css/uniform.default.css?v=6-20-13" media="screen" />
<link rel="stylesheet" href="<?php echo $this->config->item('webappassets');?>css/jquery-ui-1.9.2.custom.css" media="screen" />
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
      var interval = '<?php echo $current_package_Interval;?>';
		if(interval == 'credit' && $(this).val() != '-1' && $(this).val() != 'credit' ){
			jQuery.fancybox({
		    	'content' : "<div style='width:400px;'><h5>Alert</h5><p style='text-align:center'>Please Downgrade to Free Plan to Purchase Monthly or Yearly Plan.</p><div class='btn-group'><span class='btn confirm' onclick='$.fancybox.close();'>Ok</span></div></div>"
		    });
		}else{
			$('td.value').removeClass('color-green');
			$('td.value').addClass('color-blue');
			$(this).parent('.value').removeClass('color-blue');
			$(this).parent('.value').addClass('color-green');
		}
    });
	
	if($('.payment_type').is(':checked')) { 
       var payment_type = $('.payment_type:checked').val();
       if(payment_type == 'credit_card'){
            jQuery(".card_info_div").show();
            jQuery("#billing_form").attr('action','');
        }else{
            jQuery(".card_info_div").hide();            
            jQuery("#billing_form").attr('action',"<?php echo $this->config->item('PAYPAL_SUBMIT_URL'); ?>");			
        }
   }
   
   jQuery("#package_credit").click(function(){
	   var interval = '<?php echo $current_package_Interval;?>';
		current_package = <?php echo ($user_package['package_id']);?>;
		if(interval == 'credit' || current_package == '-1')
			window.location.href="<?php echo base_url('upgrade_package_cim/credit/');?>"
		else
			jQuery.fancybox({
		    	'content' : "<div style='width:400px;'><h5>Alert</h5><p style='text-align:center'>Please Downgrade to Free Plan to Purchase Email Credits</p><div class='btn-group'><span class='btn confirm' onclick='$.fancybox.close();'>Ok</span></div></div>"
		    });
	});
	
   jQuery(".payment_type").click(function(){
        var payment_type = $(this).val();
        if(payment_type == 'credit_card'){
            jQuery(".card_info_div").show();
            jQuery(".update_credit_card").show();
            jQuery("#billing_form").attr('action','');
        }else{
            jQuery(".card_info_div").hide();
			jQuery("#billing_form").attr('action',"<?php echo $this->config->item('PAYPAL_SUBMIT_URL'); ?>");	
        }
  });
	  
jQuery("#checkAvailCredit").click(function(){
   		var current_package =  <?php echo $user_package['package_id'];?>;
		var href = jQuery(this).data('href');
		var interval = '<?php echo $current_package_Interval;?>';
   		if(current_package == '-1' || interval == 'credit' ){
   			window.location = href;
   		}else{
			jQuery.fancybox({
		    	'content' : "<div style='width:400px;'><h5>Alert</h5><p style='text-align:center'>Please Downgrade to Free Plan to Purchased Email Credits</p><div class='btn-group'><span class='btn confirm' onclick='$.fancybox.close();'>Ok</span></div></div>"
		    });
   		}
   });

    jQuery(".getCreditPlan").live('click',function(){
   		var href = jQuery(this).data('href');
   		$.fancybox.close();
   		window.location = href;
   });
	
	
  /*On click of package id check if the user is downgrading to free plan*/
 jQuery("#package_id").live("click",function(){	
 
	var checked_package_id = jQuery("input[name='package_id']:checked").val();
    var current_package =  <?php echo $user_package['package_id'];?>;
	
	var member_id = <?php echo $arrUserForPaypal['session_member_id']?>;

	if(current_package > 0 && checked_package_id == -1 ){	
	
		jQuery( "#wrapper_survey" ).dialog({
			autoOpen: false,
			width: 425,
			title: 'Downgrade Survey',
			closeOnEscape: false,
			dialogClass: 'myPosition',
		});
		jQuery("#wrapper_survey" ).dialog( "open" );
		jQuery('#survey_form').hide();
		jQuery('#survey_thankyou').hide();
		
		jQuery(".survey_next").live("click",function(){
			jQuery('#survey_form').show();
			jQuery('#welcome').hide();
		});
		
		jQuery(".survey_submit").live("click",function(){
			jQuery('#survey_form').hide();
			jQuery('#welcome').hide();
			var member_id = <?php echo $arrUserForPaypal['session_member_id']?>;
			var survey_radio = jQuery('#survey').is(':checked');			
			var radio_val = jQuery("input[name='survey']:checked").val();
			var survey_ans = jQuery('#survey_ans_textarea').val();
			
			jQuery('.error-message').hide();		
			if(!radio_val){				
				jQuery('.error-message').html('Please select one reason.');	
				jQuery('.error-message').show();
				jQuery('#survey_form').show();				
				return false;
			}else if(!survey_ans){	
				jQuery('.error-message').html('Please answer question (2) below.');			
				jQuery('.error-message').show();
				jQuery('#survey_form').show();
				return false;
				//return false;
			}else{				
				var postdata = {};
				postdata['survey_ans'] = survey_ans;
				postdata['radio_val'] = radio_val;
				postdata['member_id'] = member_id;
				postdata['current_package'] = current_package;
				
				jQuery.post('<?php echo base_url() ?>upgrade_package_cim/survey_form/', postdata, function(result) {
					
					var response = jQuery.parseJSON(result);
					
					if(response.status == 'success'){
						submit_frm();						
					}else{
						jQuery('.error').html('Got failed due to some errors.Please Again fill survey. ');			
						jQuery('.error').show();
					}
                });
			
			}
			jQuery('#survey_thankyou').show();

			
		});
		///Close Dialog Box
		jQuery(".dialog_close").live("click",function(){
		
			jQuery('#wrapper_survey').dialog('close');
			return false;
		});
		
			
			//event.preventDefault();
		
    }
  });
	
 /*** END FOR CANCELLATION SERVEY-CB  ****/
 /**********added By cb**************/
<?php if($mode == 'credit') { ?>
 	/*jQuery("#creditCount").live('input', function(){
    	var getCount = $(this).val();
		jQuery(this).attr("disabled", "disabled");
    	jQuery.ajax({
	    	url: "<?php echo base_url() ?>upgrade_package_cim/fetchPayableAmountForCredit/",
	      	type:"POST",
	      	data:{"creditCount" : getCount},
	      	success: function(data) {
	        	jQuery("#payableAmount").val(data);
				jQuery("#creditCount").attr("disabled", "");
	      	}
	    });
		
    	
	});*/
	jQuery("#creditCount").live('focusout', function() {
		var getCount = $(this).val();
		
		jQuery('.paypal_validation_div').html('');
		var number_pattern = /^[1-9][0-9]*$/;
		if(getCount == ''){
			//alert('null');return false;
			jQuery('.paypal_validation_div').html('<p>Please Enter Minimum 1 Email Credit</p>');
			jQuery("#payableAmount").val('');
			return false;
		}else if(getCount != '' && number_pattern.test(getCount) == false){
			//alert('number');return false;
			jQuery('.paypal_validation_div').html('<p>Please Enter Minimum 1 Email Credit</p>');
			jQuery("#payableAmount").val('');
			return false;
		}else{
			//alert('true');return false;
			jQuery(this).attr("disabled", "disabled");
			var credit = '<?php echo $getCreditValue;?>';
			var amount = credit * getCount;
			jQuery("#payableAmount").val('$ '+amount);
			jQuery("#creditCount").attr("disabled", "");
		}
	});
<?php } ?> /**********Ended By cb**************/
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
    var rad_val = 0;
  	var $mode = '<?php echo $mode; ?>';
  	<?php if($mode == 'credit'){ ?>
  		jQuery('input[name=packageId]').val(<?php echo $getCreditPackage;?>);
  		rad_val = '<?php echo $getCreditPackage;?>';
  	<?php }else{ ?>
    for (var i=0; i < document.getElementsByName('package_id').length; i++) {
      if (document.getElementsByName ('package_id')[i].checked) {
        rad_val = document.getElementsByName ('package_id')[i].value;
        document.billing_form.packageId.value=rad_val;
      }
    }
    <?php } ?>
	//jQuery('.subscriber_msg').hide();
	var checked_package_id = jQuery("input[name='package_id']:checked").val();
	var payment_type = jQuery('.payment_type:checked').val();
	var current_package = <?php echo ($user_package['package_id']);?>;
	if(current_package > 0){
		//alert('yes');return false;
		var next_payment_date ="<?php if($user_package['next_payement_date'] != ''){echo strtotime($user_package['next_payement_date']);}else{echo '0';}; ?>"
	}else{
		
	}
	//if('<?php echo $user_package['next_payement_date']; ?>');return false;
	/* if(<?php echo $user_package['next_payement_date']; ?> != ''){
		var next_payment_date = <?php echo strtotime($user_package['next_payement_date']);?>
	}else{
		alert('here');
	} */
	
	var current_date = <?php echo strtotime(date('Y-m-d'));?>;
	
	var current_paymnet_type = '<?php echo $arrUserForPaypal['payment_type']; ?>';
	//alert("##"+current_date+"#####"+next_payment_date);
    //if((rad_val == '-1')||(rad_val == <?php echo $selected_package; ?>)){
     /* if($mode != 'credit' && current_paymnet_type == payment_type &&  rad_val == <?php echo $selected_package; ?> && ( rad_val != 76 || rad_val != -1  ))  {
      fancyAlert('Please select a different plan to continue.');
      return false;
      // Do nothing
    }else  */if( rad_val == 'credit'){
		fancyAlert('Please select a different plan to continue or go for credit option.');return false;
	}/* else if(next_payment_date > current_date && checked_package_id == current_package && rad_val != 'credit'){
		
		fancyAlert('Please select a different plan to continue.');
      return false;
	} */else { /**Added by cb***/
		<?php if($mode == 'credit'){ ?>
			if (! jQuery('input[name="credit_count"]').length) {
				
				jQuery('<input />').attr('type', 'hidden')
	          		.attr('name', "credit_count")
	          		.attr('value',jQuery("#creditCount").val())
	          		.appendTo('#billing_form');
	        }else{ 
	        	jQuery('input[name="credit_count"]').val(jQuery("#creditCount").val());
	        }
	        if (! jQuery('input[name="payable_amount"]').length) {
		        jQuery('<input />').attr('type', 'hidden')
		          .attr('name', "payable_amount")
		          .attr('value',jQuery("#payableAmount").val())
		          .appendTo('#billing_form');
		    }else{
		    	jQuery('input[name="payable_amount"]').val(jQuery("#payableAmount").val());
		    }
        
        <?php } ?>
        /**Ended by cb**/
		if(payment_type == 'paypal'){
            var postdata =  jQuery("#billing_form").serialize();
            jQuery.post('<?php echo base_url() ?>upgrade_package_cim/payment_by_paypal_cim', postdata, function (result) {
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
					}else if(resp.payment_year_month == 'years'){
						jQuery("#t1").val('Y');
						jQuery("#t3").val('Y');
					}else if(resp.payment_year_month != 'credit'){
			            jQuery("#t1").val('');
			            jQuery("#t3").val('D');
			        }
					
					if(resp.no_of_uses == 'all'){
						jQuery("#a3").val(resp.package_regular_price);
						
						//jQuery("#a3").val('1.00');
						
					}else{
						if(resp.payment_year_month == 'months'){
							jQuery("#p1").val(resp.no_of_uses);
						}else if(resp.payment_year_month == 'years'){
							jQuery("#p1").val(1);
						}else if(resp.payment_year_month == 'credit'){
              				jQuery("#p1").val(0);
              				jQuery("#src").val(0);
							jQuery("#amount").val(resp.package_regular_price);
            			}
						jQuery("#a3").val(resp.package_regular_price);
					}
					
					
					
					jQuery("#return_url").val('<?php echo $this->PAYPAL_SUCCESS_URL; ?>?userpackageid='+resp.member_package_id);
                    
                   // jQuery("#return_url").val("<?php echo $this->config->item('PAYPAL_SUCCESS_URL'); ?>"+resp.transaction_id);
                    
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
	  //$('.expired').hide();
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
        <h1>Pricing Plans & Payments	
		<?php if($mode =='annual'){ /*******Updated By cb********/
				echo"<a href='". base_url()."upgrade_package_cim/index'  class='btn cancel plans_btn'>See Monthly Plans</a>";
				echo"<a href='javascript:void(0)' data-href='". base_url()."upgrade_package_cim/credit' id='checkAvailCredit'  class='btn cancel plans_btn'>Email Credits </a>";
			}elseif($mode == ''){
				echo"<a href='". base_url()."upgrade_package_cim/annual'  class='btn cancel plans_btn'>See Annual Plans</a>";
				echo"<a href='javascript:void(0)' data-href='". base_url()."upgrade_package_cim/credit' id='checkAvailCredit' class='btn cancel plans_btn'>Email Credits </a>";	
			}else{
				echo"<a href='". base_url()."upgrade_package_cim/index'  class='btn cancel plans_btn'>See Monthly Plans</a>";
				echo"<a href='". base_url()."upgrade_package_cim/annual'  class='btn cancel plans_btn'>See Annual Plans</a>";
			}
				
		?>	
		</h1>	
        <?php //if($selected_package == 0)echo $ccFailedMsg = "<div class='error'>Your transaction declined. You need to update your credit-card.</div>";?>
		
		<?php 
		
		$payment_type = $arrUserForPaypal['payment_type'];
		if($selected_package == 0  ){
                            if($payment_type == 'paypal' && $checked_package_id != '-1' && $checked_package_id != '76' ){
                                echo $ccFailedMsg = "<div class='error'>Your transaction was declined. Please update your PayPal info.</div>";
                            }elseif($checked_package_id != '-1' && $checked_package_id != '76' ){
                                echo $ccFailedMsg = "<div class='error'>Your transaction was declined.  Please update your credit-card.</div>";
                            }
                        
                        }
                    ?>
		<table width="60%" border="0" cellspacing="0" cellpadding="0" class="table_contact_pricing">
			<tr>
				<td>
				<?php if($mode != 'credit'){ ?>
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tbl-pricing tbl_cnt_price">
					<tr>
						<th>Select a Plan</th>
						<th>Number of Contacts</th>
					</tr>
                <?php
				
				function build_sorter($key) {
					return function ($a, $b) use ($key) {
						return strnatcmp($a[$key], $b[$key]);
					};
				}
				
				usort($packages, build_sorter('package_price'));
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
				  
					 <?php if($package['is_quote'] == 1){?>
					 <td width="50%" class="value color-blue bg-blue<?php if($package['enable']=="disabled"){?>inactive<?php } ?>">
					  
						<a href="mailto:support@redcappi.com?Subject=Quote Request for package <?php echo $package['package_id'];?>" target="_target">Request A Quote</a>
						</td>					 
					 <?php }else{?>	
                    <?php if($checked_package_id==$package['package_id']){ ?>
                    <td width="50%"  class="value color-green">
                      <input class="check_package" name="package_id" id="package_id" type="radio" value="<?php echo $package['package_id'];  ?>" checked="checked" <?php echo $package['enable']; ?> >
					 
				  <?php }else{ ?>
                      <td width="50%" class="value color-blue <?php if($package['enable']=="disabled"){?>inactive<?php } ?>">
					  
                        <input class="check_package" name="package_id" id="package_id" type="radio" value="<?php echo $package['package_id'];  ?>" <?php echo $package['enable']; ?>>
                    <?php } if($package['package_price']==0){ ?>
					
					  
                      Free&nbsp;&nbsp;
					  </td>
                    <?php }else{ ?>
                      $<?php echo number_format($package['package_price'],0);  ?><span class="txt-italic">/<?php if( $package['package_recurring_interval']=='months') echo 'month'; else echo 'year'; ?></span>
                    <?php } if($package['enable']=="disabled"){?>
                      <br/><font class="list_small">List larger than plan</font>
                    <?php } ?>
                      </td>
					 <?php } ?> 
                    <th width="50%" class="value"><?php echo number_format($package['package_min_contacts']);  ?>-<?php echo number_format($package['package_max_contacts']);  ?> <br>
                      <input type="hidden" name="this_package_<?php echo $package['package_id'];?>" id="this_package_<?php echo $package['package_id'];?>" value="<?php echo number_format($package['package_price'],0);  ?>" />
                      <!--<span class="txt-italic"> Contacts </span>-->
                    </th>
					
                  </tr>
				  
				<?php $i++; }
				
					if($i%2==0){
						$class="class='even'";
					  }else{
						$class="class='odd'";
					  }
					  //echo $user_package['package_recurring_interval'];
					  ?>
					<tr <?php echo $class; ?>>
						<td width="50%"  class="value color-green">
							<input class="check_package" name="package_id" id="package_credit"  type="radio" value="credit" <?php if($current_package_Interval == 'credit') { ?> checked="checked" <?php } ?> <?php echo $package['enable']; ?> >Email Credits
						</td>
						<th width="50%" class="value">
							Unlimited
						</th>
					</tr>
                </table>
                <?php }else{ 
                	/******Added by cb*******/?> 
                	<table>
                		<tr>
                      		<td colspan="2" class="label"><strong>Email Credits </strong></td>
                      		<td colspan="2"><?php echo form_input(array('class'=>'clean','id'=>'creditCount','maxlength'=>120,'min'=>0,'type' => 'number','required' => 'required','value'=>$postData['credit_count']));  ?>
                      		</td>
                    	</tr>
						<?php $amount = ($postData['payable_amount'] > 0) ? $postData['payable_amount'] : '0.00';?>
						<tr>
                      		<td colspan="2" class="label"><strong>Payable Amount</strong></td>
                      		<td colspan="2"><?php echo form_input(array('class'=>'clean total-disable','id'=>'payableAmount','disabled'=>'disabled','maxlength'=>120,'required' => 'required','value'=>'$ '.$amount)); ?></td>
                    	</tr>
                    
						
                    </table>
                <?php	} ?>
				</td>
			</tr>
		</table>
        <table width="93%" border="0" cellspacing="0" cellpadding="0" class="payment_info">
          <tr>
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
				<?php $next_payment_date = strtotime($user_package['next_payement_date']);	
						$current_date = strtotime(date('Y-m-d'));?>
						
				<div style="color:#FF0000;" class="error info paypal_validation_div" style="display:none"></div>
				<?php if($current_date > $next_payment_date && $user_package['package_id'] != -1 && $user_package['is_admin']>0){?> <div class="info"><span>Your payment of $<?php echo $current_price?> is due, please update your billing details, agree to terms and click Submit to be charged.</span></div>
				<?php }?>
				<section>
                <form action="" method="post" class="billing-form" name="billing_form" id="billing_form">
                 <section class="billing">
				 <h4 class="heading-txt">Billing Information</h4>
                  <table width="98%" border="0" cellpadding="3" cellspacing="0">
                    <tr>
                      <td colspan="2" class="label"><strong>First name</strong></td>
                    </tr>
					<tr>
                      <td colspan="2"><?php echo form_input(array('name'=>'first_name','class'=>'clean','id'=>'first_name','maxlength'=>120,'size'=>35,'value'=> $user_package['first_name'] != "" ? $user_package['first_name'] : (($postData['first_name'] != '') ? $postData['first_name'] : set_value('first_name') )));   ?>                    </td>
                     
                    </tr>
					<tr>
                      <td colspan="2" class="label"><strong>Last name</strong></td>
                    </tr>
                    
					<tr>
                       <td colspan="2"><?php echo form_input(array('name'=>'last_name','class'=>'clean','id'=>'last_name','maxlength'=>120,'size'=>35,'value'=>$user_package['last_name'] != "" ? $user_package['last_name'] : (($postData['last_name'] != '') ? $postData['last_name'] : set_value('last_name') ))); ?></td>
                     
                    </tr>
                    <tr>
                      <td colspan="2" class="label"><strong>Street address</strong></td>
                    </tr>
                    <tr>
                      <td colspan="2"><?php echo form_input(array('name'=>'address1','class'=>'clean','id'=>'address1','maxlength'=>60,'size'=>80,'value'=>$user_package['address'] != "" ? $user_package['address'] : (($postData['address1'] != '') ? $postData['address1'] : set_value('address1') ))); ?></td>
                    </tr>
                    <tr>
                      <td colspan="2" class="label"><strong>City</strong></td>
                    </tr>
					
                    <tr>
                      <td colspan="2"><?php echo form_input(array('name'=>'city','class'=>'clean','id'=>'city','maxlength'=>50,'size'=>35,'value'=>$user_package['city'] != "" ? $user_package['city'] : (($postData['city'] != '') ? $postData['city'] : set_value('city') ))); ?></td>
                    </tr>
					<tr>
						<td colspan="2" class="label"><strong>State/Province</strong></td>
                    </tr>
					<tr>
                      <td colspan="2"><?php echo form_input(array('name'=>'state','class'=>'clean','id'=>'state','maxlength'=>50,'size'=>35,'value'=>$user_package['state'] != "" ? $user_package['state'] : (($postData['state'] != '') ? $postData['state'] : set_value('state') ))); ?></td>
                    </tr>
                    <tr>
                      <td class="label"><strong>Zip/Post code</strong></td>
                      <td class="label" colspan="2"><strong>Country</strong></td>
                    </tr>
                    <tr>
                      <td><?php echo form_input(array('name'=>'zipcode','class'=>'clean','id'=>'zipcode','maxlength'=>50,'size'=>35,'value'=>$user_package['zip'] != "" ? $user_package['zip'] : (($postData['zipcode'] != '') ? $postData['zipcode'] : set_value('zipcode') ))); ?></td>
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
				  </section>
                  <?php /* Start code - CB */?>
				<?php
				$payment_type = $arrUserForPaypal['payment_type'];?>
				<section class="payment">
                  <h4 class="heading-txt">Payment Information</h4>
				  <div class="payment_info_pay">
                  <h4 class="heading-txt"><input type="radio" name="payment_type_name" <?php if($payment_type == 'paypal'): echo 'checked';endif;?> class="payment_type" id="payment_type" value="paypal"/> PayPal
                  <input type="radio" name="payment_type_name" <?php if($payment_type == 'credit_card'): echo 'checked';endif;?> class="payment_type" id="payment_type" value="credit_card"/> Credit Card
                  </h4>
                  <hr/>
                    <?php /* Start Paypal Hidden Fields */?>
                  
                  
                  <?php  

                    $payment_year_month = ($mode =='annual')?'annual' : (($mode =='credit')?'credit' :   	(($mode == 'daily') ? 'daily' : 'months'));
                  ?>
                    <input type="hidden" name="payment_year_month" value="<?php echo $payment_year_month;?>" />
                    <input type='hidden' name='business' value="<?php echo $this->config->item('PAYPAL_EMAIL'); ?>" />
                    <input type='hidden' name='cpp-logo-image' value="<?php echo $this->config->item('webappassets');?>images/redesign/logo.png" />                   
                    <input type="hidden" name="custom" id="custom" value="<?php echo $arrUserForPaypal['session_member_id']."|".$payment_year_month;?>" />
                    
                    <input type="hidden" name="page_style" value="paypal" />
                    <input type="hidden" name="lc" value="" />
                    <input type="hidden" name="no_note" value="1" />
                    <input type="hidden" name="charset" value="utf-8" />
                   
                    <?php 
                        //a1 = Amount;
                        //p1 = trail Cycle;
                        //t1 = trail Period M=montrh,Y=year ,D=Days, W='week';
						if($mode == 'credit'){ ?>
					<input type="hidden" id="amount" name="amount" value="<?php echo $row['price']; ?>">
					<input type='hidden' name='cmd' value='_xclick' />
					<?php	}else{
                    ?>
					<input type='hidden' name='cmd' value='_xclick-subscriptions' />
					 <input type="hidden" name="src" value="1" />
                    <input type="hidden" name="a1" id="a1" value="10" /> 
                    <input type="hidden" name="p1" id="p1" value="1" />
                    <input type="hidden" name="t1" id="t1" value="D"/>
					
                    <input type="hidden" name="a3" id="a3" value="10" /> 
                    <input type="hidden" name="p3" id="p3" value="1" />
                    <input type="hidden" name="t3" id="t3" value="D"/>
                    
						<?php } ?>
					
					<input type="hidden" name="zip" id="zip" value="" />

                    
                    
                    <input type='hidden' id='item_name' name='item_name' value='Test Product' />
                    <input name="notify_url" value="<?php echo $this->config->item('PAYPAL_NOTIFY_URL'); ?>" type="hidden" />
                    <!--<input type='hidden' id='amount' name='amount' value='100'>-->
					<input type="hidden" value="2" name="rm" />      
                    <input type='hidden' name='no_shipping' value='1' />
                    <input type='hidden' name='currency_code' value='USD' />
                    <input type='hidden' name='handling' value='0' />
                    <input type='hidden' name='cancel_return' value="<?php echo $this->config->item('PAYPAL_CANCEL_URL'); ?>" />
                    <input type='hidden' id="return_url" name='return' value="<?php echo $this->config->item('PAYPAL_SUCCESS_URL'); ?>" />
                    <?php /* End Paypal Hidden Fields */?>
                  
                  
                  <?php /* END Code - CB */?>
                  
                  <div class="card_info_div" <?php if($payment_type == 'paypal'):?> style="display: none" <?php endif;?>>
                  
                  <table width="98%" border="0" class="<?php echo $user_package['customer_payment_profile_id']>0 ?  "update_credit_card" : "";?>" <?php echo $user_package['customer_payment_profile_id']>0 ? "style=display:none;" : ""; ?>>
					<!-- table width="98%" border="0" class="<?php echo $user_package['customer_payment_profile_id']>0 ?  "update_credit_card" : "";?>"  -->
					<?php 
					// If payment-done & via credit-card then
					//if($user_package['payment_type'] == 0 && $user_package['is_payment'] == 0){ ?>
                    <tr>
                      <td class="label"><strong>Name on card</strong></td>
                      <td class="label"><strong>Credit card number</strong></td>
                    </tr>
                    <tr>
                      <td><?php echo form_input(array('name'=>'credit_card_holder_name','class'=>'clean','id'=>'credit_card_holder_name','maxlength'=>120,'size'=>35,'value'=> ($postData['credit_card_holder_name'] != '')? $postData['credit_card_holder_name'] : set_value('credit_card_holder_name'),'autocomplete'=>'off')); ?></td>
                      <td align="left"><?php echo form_input(array('name'=>'cc_number','class'=>'clean','id'=>'cc_number','maxlength'=>120,'size'=>35,'value'=>($postData['cc_number'] != '')? $postData['cc_number'] : set_value('cc_number'),'autocomplete'=>'off' )); ?></td>
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
                        <?php echo form_input(array('name'=>'cvv','class'=>'clean','id'=>'cvv','maxlength'=>12,'size'=>15,'value'=>($postData['cvv'] != '')? $postData['cvv'] : set_value('cvv'),'autocomplete'=>'off' )); ;?>
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
                  <?php if($mode != 'credit'){?>
                     <tr>
                      <td colspan="2">
                        <div class="discount_coupon_container">
                          <span class="label"><strong>Discount Code</strong></span>
						  <input name="coupon_code" type="text" size="32" value="" id="coupon_code" class="clean" />
                          <a href="javascript:void(0);" onclick="javascript:fetchPayableAmount();" class="btn cancel inline-block" style="margin-left:20px;">
                            Redeem
                          </a>
						  <span class="expired">
							<?php if(count($used_coupon)>0){
							if($used_coupon[0]['status'] == 0 || $used_coupon[0]['valid_untill'] < date('Y-m-d')){
									echo $errormsg = "Your coupon ".$used_coupon['coupon_code']." has been expired";	
								}
							}?>	
							</span>
                          <span id="coupon_code_msg" class="error"></span>
                        </div>
                      </td>
                    </tr>
					<?php } ?>
                    <tr>
                      <td colspan="2">
                        <div style="padding-bottom: 10px;">
                          <input name="terms_conditions" type="checkbox" value="1" class="clean" />
                          <strong>I agree to RedCappi <a href="<?php echo base_url().'terms';?>" target="_blank" class="terms-link">terms &amp; conditions </a>.</strong>
                        </div>
                      </td>
                    </tr>
                  </table>
                  <?php /* END code - CB */?>
				  </div>
                   </section>
                  <?php
                    if($user_package['customer_payment_profile_id']){
                      echo form_hidden('update_subscription','true');
                    }
                    echo form_hidden('action','save');
                    echo form_hidden('packageId','');
                    echo form_close();
                  ?>
				  </section>
                </td>
              </tr>
            </table>
			
            <div id="order-page-btn-group">
             

              <button type="button" class="btn confirm inline-block submit_button" onclick="return submit_frm();">Submit</button>
			   <a href="<?php echo $previous_page_url; ?>" title="" class="btn cancel inline-block" >Cancel</a>
              <!--
              <a href="javascript:void(0);" title="" class="btn confirm inline-block submit_button" onclick="return submit_frm();">Submit</a>-->
            
            </div>
            </div>
          </div>
        </div>
		<div class="wrapper_survey" id="wrapper_survey" style="display:none">
			</br>
			<div style='width:400px;align:center' id="welcome">
			<!--<h5>Survey Form</h5>-->
			<p style='text-align:center;font-size:14px;'>We're sorry you're cancelling your Redcappi paid service.</br> </br> </br> Please answer(2) brief survey questions, then we'll downgrade your account to a free plan.
			</p>
			</br>
			</br>
			<div class='btn-group'>
				<span class='btn survey_next'>Next</span>
			</div>
		</div>
		
		<div class="wrapper_survey" style='width:400px;align:center;font-size:13px;background-color: #ffffff;' id="survey_form">
			<!--<h5>Survey Form</h5>-->
			<p style='text-align:center;background-color: #ffffff'>
				
				<div class="error-message" style="display:none;font-weight:bold;background-color: #ffffff"></div></br>
				<p style="font-weight:bold;background-color:#ffffff">1. Why are you downgrading your plan? </p></br>
				<input type='hidden' value='' id='baseurl'>
				<input type='radio' name='survey' value='Unhappy With Customer Support' id='survey'> Unhappy With Customer Support</br>
				<input type='radio' name='survey' value='Unhappy With Product' id='survey'> Unhappy With Product</br>
				<input type='radio' name='survey' value='Unhappy With Email Delivery %' id='survey'> Unhappy With Email Delivery %</br>
				<input type='radio' name='survey' value='Too Expensive' id='survey'> Too Expensive</br>
				<input type='radio' name='survey' value='Seasonal Emailing Needs Ended' id='survey'> Seasonal Emailing Needs Ended</br>
				<input type='radio' name='survey' value='Moving to Monthly or Annual Plan' id='survey'> Moving to Monthly or Annual Plan</br>
				<input type='radio' name='survey' value='Other' id='survey'> Other</br></br>
				<p style="font-weight:bold">2. Please explain your answer above. </p></br>
				<!--	<input type="text" name="survey_ans_textarea" id='survey_ans_textarea' class="survey_ans_textarea" value=""/>-->
				
				
				</br>
				<textarea name="survey_ans_textarea" id='survey_ans_textarea' class="survey_ans_textarea" rows="4" cols="45"></textarea>
				</br></br>
				<div class='btn-group'>
					<span class='btn survey_submit' id='survey_submit'>Submit</span>
				</div>
				
			</p>
				
		</div>

		<div style='width:400px;align:center;font-size:14px;' id="survey_thankyou"><!--<h5>Survey Form</h5>--><p style='text-align:center;font-size:14px;'>Thanks for taking our survey.</br></br> You have been downgraded to a free plan</p></br><div class='btn-group'><span class='btn dialog_close'>Close</span></div></div>
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
#survey_form div.error-message{
	color:red !important;
	font-weight: bold;
                 background-color: #ffffff;
}

#wrapper_survey > div#welcome {
    height: 151px !important;
    background-color: #ffffff;
}
.ui-dialog .ui-dialog-title {
	float: none !important;
}
.ui-dialog-titlebar-close {
    visibility: hidden;
}
.ui-dialog-titlebar.ui-widget-header.ui-corner-all.ui-helper-clearfix {
    background-color: #fafafa;
    color: black;
    font-size: 21px;
    height: 32px;
    text-align: l;
}


.btn.survey_next,.btn.survey_submit,.btn.dialog_close {
    background-color: #fafafa;
    color: black;
    /*font-weight: bold;*/
}
#welcome > div.btn-group{
	background-color: #fff;
    border: 0px solid #efefef;
}

#survey_form > div.btn-group{
	background-color: #fff;
    border: 0px solid #efefef;
}

#survey_thankyou > div.btn-group{
	background-color: #fff;
    border: 0px solid #efefef;
}
.myPosition {
    position: fixed !important;
	top:100px !important;
}

.ui-widget-header{
	border:0 !important;
	border-bottom: 1px solid #eee !important;
	
}

#survey_form > textarea{
	margin-left: 10px;
	
}


</style>
