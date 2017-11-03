<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-ui-1.8.13.custom.min.js?v=6-20-13"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css/blitzer/jquery-ui-1.8.14.custom.css?v=6-20-13" />

<script type="text/javascript">
  function addCustomField(){
    if($('.custome_fld').val()!=""){
      var fld_name=$(".custome_fld").val();
      fld_name=fld_name.replace(/\s/g,'_');
      fld_name=fld_name.replace(/-|\//g, "_");
      //fld_name=fld_name.toLowerCase();
      if($('[id='+fld_name+']').length>0){
        $('.contact_frm').show();
        $('.custom_field_frm').hide();
        $('[id='+fld_name+']').focus();
      }else{
        var fld='<div class="new-custom-field"><strong class="contacts_change">'+$('.custome_fld').val()+'</strong><input type="hidden" name="custom_'+fld_name+'" value="'+$('.custome_fld').val()+'" /><input type="text" name="'+fld_name+'" id="'+fld_name+'"  size="40" maxlength="250" class="custom_text" onclick="javascript:$(\'.custom_list\').hide();" autocomplete="off" /><a href="javascript:void(0);" class="btn danger delete_custom_field inline-block">Delete</a></div>';
        $('.div_contact_prfile').append(fld);
        $('.contact_frm').show();
        $('.custom_field_frm').hide();
      }
    }
  }
  function addExtraField(fld_name){
    fld_text=fld_name.toLowerCase();
    fld_name=fld_text.replace(/\s/g,'_');
    if($('#'+fld_name).length>0){
      $('.contact_frm').show();
      $('.custom_field_frm').hide();
      $('#'+fld_name).focus();
    }else{
        var fld='<div class="new-custom-field"><strong class="contacts_change">'+ucfirst(fld_text)+'</strong><input type="text" name="'+fld_name+'" id="'+fld_name+'"  size="40" maxlength="250"  class="custom_text" onclick="javascript:$(\'.custom_list\').hide();"/><a href="javascript:void(0);" class="btn danger delete_custom_field inline-block">Delete</a></div>';
      $('.div_contact_prfile').append(fld);
      if(fld_name=="birthday"){
        $("#"+fld_name).datepicker({changeMonth: true,
        changeYear: true, yearRange: '1950:2012' });
      }
    }
    $('.custom_list').slideUp();
    $(".fancybox").fancybox.resize();
  }
  function ucfirst(str) {
    var firstLetter = str.substr(0, 1);
    return firstLetter.toUpperCase() + str.substr(1);
  }
  $('.delete_custom_field').live('click',function (){
    var parent_ob=$(this).parents(".new-custom-field");
    parent_ob.remove();
  });
  $('.custom_text').live('keyup',function (){
    var parent_ob=$(this).parent();
    parent_ob.find('a').remove();
	parent_ob.removeClass('new-custom-field');
    var val=$(this).val().replace(/^\s+|\s+$/g,"");
    if(val==""){
    parent_ob.append('<a href="javascript:void(0);" class="btn danger delete_custom_field inline-block">Delete</a>');
	parent_ob.addClass('new-custom-field');
	}
  });
  function submit_frm(){
    var block_data="";
    block_data+=$('#contact_frm_submit').serialize();
    console.log(block_data);
    jQuery.ajax({
      url: "<?php echo base_url() ?>newsletter/subscriber/edit/<?php echo $subscriptions[0]['subscriber_id']; ?>/<?php echo $contact_soft_bounce;?>/<?php echo $contact_bounce_status;?>",
      type:"POST",
      data:block_data,
      success: function(data) {
        var data_arr=data.split(":", 2);
        if(data_arr[0]=="error"){
          $('.subscriber_msg').html('');
          $('.subscriber_msg').html(data_arr[1]);
          $('.subscriber_msg').addClass('info');
          $('.subscriber_msg').fadeIn();
        }else if(data_arr[0]=="success"){
          $('.subscriber_msg').html('Contact Updated Successfully');
          $('.subscriber_msg').addClass('info');
          $('.subscriber_msg').fadeIn();
          var elements=$('.custom_text');
          elements.each(function() {
            var val;
            val=$(this).val();
            val= val.replace(/^\s+|\s+$/g,"");
            if(val==""){
              var parent_ob=$(this).parent().parent();
              parent_ob.prev().remove();
              parent_ob.remove();
            }
          });
          /* parent.$('#subscriber_tr_<?php echo $subscriptions[0]['subscriber_id']; ?>').find('.subscriber_firstname').html($("input#first_name").val());
          parent.$('#subscriber_tr_<?php echo $subscriptions[0]['subscriber_id']; ?>').find('.subscriber_lastname').html($("input#last_name").val());
          parent.$('#subscriber_tr_<?php echo $subscriptions[0]['subscriber_id']; ?>').find('.subscriber_email').html($("input#email_address").val());
          setTimeout(function(){$('.subscriber_menus').fadeOut();} , 4000); */
        }
      }
    });
  }
  function moreHeight(){
    var space = document.createElement('div');
    space.setAttribute('id', 'dummy');
    space.style.height = "450px";
    space.style.clear = "both";
    document.getElementsByTagName("body")[0].appendChild(space);
    window.scrollBy(100,350);
    setTimeout('window.scrollBy(0,250)',50000);
  }
  function reduceHeight(){
    var rem = document.getElementById('dummy');
    document.getElementsByTagName("body")[0].removeChild(rem);
  }


function onScrollPaging(){
  var psize = $('#page_counter').val();
    jQuery.ajax({
      url: "<?php echo base_url() ?>newsletter/subscriber/ajaxHistory/<?php echo $subscriptions[0]['subscriber_id'] ; ?>/<?php echo $subscriptions[0]['subscriber_id'] ; ?>/<?php echo $subscriptions[0]['subscriber_id'] ; ?>/"+psize+"/",
      type:"POST",

      success: function(data) {
        $(".history_contact_rec").append(data);
        $('div#last_msg_loader').html('');
        if(data.length == 0) {
          $(window).unbind("scroll");
          var $el = $("#last_msg_loader");
          if($el.children().length == 0) {
            $el.html("<h2>No Records Found</h2>");
          } else {
            $el.append("<h2>All Records have been loaded.</h2>");
          }
        }
      }
    });
  };
  $(document).ready(function(){
    $(window).scroll(function(){
      if($(window).scrollTop() == $(document).height() - $(window).height()){
        $('div#last_msg_loader').html('<img src="<?php echo $this->config->item('webappassets');?>images/loader.gif?v=6-20-13">');
        var psize = parseInt($('#page_counter').val()) + 1;
        $('#page_counter').val(psize)
        setTimeout("onScrollPaging()",3000);
      }
    });

  });
</script>
<!--[/main script] -->


<!--[body]-->

<div id="body-dashborad">
  <div class="container">
    <h1>User Profile</h1>
    <div class="left-menu account">
      <h2 class="h2_contact_profile">Edit Profile</h2>
      <div class="profile-container" style="overflow: visible">
        <form  method="post" name="contact_frm_submit" id="contact_frm_submit" class="contact_frm_edit" onsubmit="submit_frm(); return false;" >
          <div class="subscriber_msg"></div>
          <div class="div_contact_prfile">
            <?php
              if(($subscriptions[0]['subscriber_status']!=1)&&($subscriptions[0]['subscriber_status']!=3)&&($subscriptions[0]['subscriber_status']!=4)){
                $readonly="readonly";
              }else{
                $readonly="";
              }
            ?>
            <?php
              echo '<strong>First Name</strong>'. form_input(array('name'=>'subscriber_first_name','id'=>'first_name','maxlength'=>250,'size'=>40,'value'=>stripslashes($subscriptions[0]['subscriber_first_name']),'onclick'=>"javascript:$('.custom_list').hide();",$readonly=>$readonly )).'';
              echo '<strong>Last Name</strong>'.  form_input(array('name'=>'subscriber_last_name','id'=>'last_name','maxlength'=>250,'size'=>40,'value'=>stripslashes($subscriptions[0]['subscriber_last_name']),'onclick'=>"javascript:$('.custom_list').hide();",$readonly=>$readonly)).'';
              echo '<strong>Email Address</strong>'. form_input(array('name'=>'subscriber_email_address','id'=>'email_address','maxlength'=>250,'size'=>40,'value'=>$subscriptions[0]['subscriber_email_address'],'onclick'=>"javascript:$('.custom_list').hide();",$readonly=>$readonly )).'';
              if(trim($subscriptions[0]['subscriber_address'])!=""){
                echo '<strong>Address</strong>'. form_input(array('name'=>'address','id'=>'address','maxlength'=>250,'size'=>40,'value'=>$subscriptions[0]['subscriber_address'],'onclick'=>"javascript:$('.custom_list').hide();",$readonly=>$readonly,'class'=>'custom_text' )).'';
              }
              if(trim($subscriptions[0]['subscriber_dob'])!=""){
                echo '<strong>Birthday</strong>'. form_input(array('name'=>'birthday','id'=>'birthday','maxlength'=>250,'size'=>40,'value'=>$subscriptions[0]['subscriber_dob'],'onclick'=>"javascript:$('.custom_list').hide();",$readonly=>$readonly,'class'=>'custom_text' )).'';
                ?>
                <?php
                  if(($subscriptions[0]['subscriber_status']==1)||($subscriptions[0]['subscriber_status']==3)){
                ?>
                  <script type="text/javascript">
                    $(function() {
                      $("#birthday").datepicker({changeMonth: true,
              changeYear: true, yearRange: '1950:2012' });
                    });
                  </script>
                <?php } ?>
              <?php }
              if(trim($subscriptions[0]['subscriber_city'])!=""){
                echo '<strong>City</strong>'. form_input(array('name'=>'city','id'=>'city','maxlength'=>250,'size'=>40,'value'=>$subscriptions[0]['subscriber_city'],'onclick'=>"javascript:$('.custom_list').hide();",$readonly=>$readonly,'class'=>'custom_text' )).'';
              }
              if(trim($subscriptions[0]['subscriber_company'])!=""){
                echo '<strong>Company</strong>'. form_input(array('name'=>'company','id'=>'company','maxlength'=>250,'size'=>40,'value'=>stripslashes($subscriptions[0]['subscriber_company']),'onclick'=>"javascript:$('.custom_list').hide();",$readonly=>$readonly,'class'=>'custom_text')).'';
              }
              if(trim($subscriptions[0]['subscriber_country'])!=""){
                echo '<strong>Country</strong>'. form_input(array('name'=>'country','id'=>'country','maxlength'=>250,'size'=>40,'value'=>$subscriptions[0]['subscriber_country'],'onclick'=>"javascript:$('.custom_list').hide();",$readonly=>$readonly,'class'=>'custom_text')).'';
              }
              if(trim($subscriptions[0]['subscriber_phone'])!=""){
                echo '<strong>Phone</strong>'. form_input(array('name'=>'phone','id'=>'phone','maxlength'=>250,'size'=>40,'value'=>$subscriptions[0]['subscriber_phone'],'onclick'=>"javascript:$('.custom_list').hide();",$readonly=>$readonly,'class'=>'custom_text' )).'';
              }
              if(trim($subscriptions[0]['subscriber_state'])!=""){
                echo '<strong>State</strong>'. form_input(array('name'=>'state','id'=>'state','maxlength'=>250,'size'=>40,'value'=>$subscriptions[0]['subscriber_state'],'onclick'=>"javascript:$('.custom_list').hide();",$readonly=>$readonly, 'class'=>'custom_text')).'';
              }
              if(trim($subscriptions[0]['subscriber_zip_code'])!=""){
                echo '<strong>Zip Code</strong>'. form_input(array('name'=>'zip_code','id'=>'zip_code','maxlength'=>250,'size'=>40,'value'=>$subscriptions[0]['subscriber_zip_code'],'onclick'=>"javascript:$('.custom_list').hide();",$readonly=>$readonly,'class'=>'custom_text' )).'';
              }
              if($subscriptions[0]['subscriber_extra_fields'] !=''){
                foreach(unserialize($subscriptions[0]['subscriber_extra_fields']) as $col=>$val){
                  if((strpos($col,'first') === false) and (strpos($col,'last') === false) and (strpos($col,'email') === false)){
                    if((trim($col)!="")&&(trim($val) !="")){
                      echo '<div class="new-custom-field"><strong>'.ucwords(str_replace('_',' ',urldecode($col))).'<input type="hidden" name="custom_'.str_replace(" ","_",$col).'" value="'.$col.'" /></strong>
                          '.form_input(array('name'=>str_replace(" ","_",$col),'id'=>str_replace(" ","_",$col),'maxlength'=>250,'size'=>40,'value'=>$val,'onclick'=>"javascript:$('.custom_list').hide();",$readonly=>$readonly,'class'=>'custom_text' )) .'</div>';
                    }
                  }
                }
              }
              ?>
          </div>
          <?php if(($subscriptions[0]['subscriber_status']==1)||($subscriptions[0]['subscriber_status']==3)||($subscriptions[0]['subscriber_status']==4)){ ?>
          <div class="button_div">
            <div  id="button" class="div_add_newrow">
              <a class="contact_frm btn cancel inline-block" style="font-weight:700;padding:6px 12px;font-size: 14px;"  onclick="javascript:$('.custom_list').slideToggle();$('.button_div').append('<div class=\'ss\'>&nbsp;</div>');setTimeout( function(){$('.ss').remove()} , 2000); " href="javascript:void(0);">Add New Field <i class="icon-chevron-down"></i></a>
              <ul class="custom_list">
                <li><a href="javascript:void(0);" class="add-sign-up-field" onclick="addExtraField('address')">Address</a></li>
                <li><a href="javascript:void(0);" class="add-sign-up-field" onclick="addExtraField('birthday')">Birthday</a></li>
                <li><a href="javascript:void(0);" class="add-sign-up-field" onclick="addExtraField('city')">City</a></li>
                <li><a href="javascript:void(0);" class="add-sign-up-field" onclick="addExtraField('company')">Company</a></li>
                <li><a href="javascript:void(0);" class="add-sign-up-field" onclick="addExtraField('country')">Country</a></li>
                <li><a href="javascript:void(0);" class="add-sign-up-field" onclick="addExtraField('phone')">Phone</a></li>
                <li><a href="javascript:void(0);" class="add-sign-up-field" onclick="addExtraField('state')">State</a></li>
                <li><a href="javascript:void(0);" class="add-sign-up-field" onclick="addExtraField('zip code')">Zip Code</a></li>
                <li><a href="javascript:void(0);" class="add-sign-up-field" onclick="javascript:$('.contact_frm').hide();$('.custom_field_frm').show();$('.custome_fld').val('');$('.custom_list').slideUp();">Custom</a></li>
              </ul>
              <div class="custom_field_frm">
                <strong>Type a name for your custom field</strong>
                <input type="text"  class="custome_fld"  maxlength=100 />
                <?php
                  echo form_button(array('name' => 'subscription_submit', 'id' => 'btnEdit','class'=>'btn add inline-block add_more','content' => 'Submit','onclick' => 'addCustomField();'), 'Save');
                  echo form_button(array('name'=>'campaign_cancel','class'=>'btn cancel inline-block', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"javascript:$('.contact_frm').show();$('.custom_field_frm').hide();"));
                  ?>
              </div>
            </div>
          </div>
         <?php  
          if(($subscriptions[0]['subscriber_status']==3)||($subscriptions[0]['subscriber_status']==4)){
            echo form_submit(array('name' => 'subscription_submit', 'id' => 'btnEdit','class'=>'btn add add_more contact_frm','content' => 'Submit'), 'Save & Add Back to My Contacts');
          }else{
            echo form_submit(array('name' => 'subscription_submit', 'id' => 'btnEdit','class'=>'btn add add_more contact_frm','content' => 'Submit'), 'Save');
          }
        ?>
         <input type="hidden" name="subscriber_id" id="subscriber_id" value="<?php echo $subscriptions[0]['subscriber_id'] ; ?>" />
         <input type="hidden" name="subscription_id" id="subscription_id" value="<?php echo $subscriptions[0]['subscription_id'] ; ?>" />
         <?php } ?>
       </form>
      </div>
    </div>
    <div class="right-menu account profile">
      <h2>User Info</h2>
      <div class="profile-container">
        <strong>Status:</strong>
        <span class="span_status"><?php echo ($subscriptions[0]['subscriber_status']==1)? 'Active':'Inactive'; ?></span>
        <?php if($subscriptions[0]['subscriber_status']!=6){ ?>
          <strong>List:</strong>
        <?php } ?>
        <?php $i=1; ?>
          <span class="span_status">
            <?php if($subscriptions[0]['subscriber_status']==6){ ?>
              Contact not exist
            <?php }else{ ?>
            <?php foreach($subscription_title As $subscription){?>
              <?php echo  $subscription; ?>
              <?php if($i!=count($subscription_title)) echo ","; ?>
              <?php $i++; } ?>
            <?php } ?>
          </span>
        <?php if($subscriptions[0]['subscriber_status']!=6){ ?>
          <strong>Date Added:</strong>
          <span class="span_status">
            <?php              
              echo $date = date('F j, Y \a\t g:i a', strtotime(getGMTToLocalTime($subscriptions[0]['subscriber_date_added'], $this->session->userdata('member_time_zone'))));
            ?>
          </span>
          <span class="span_status">(Added By <?php if($subscriptions[0]['is_signup']==1){ echo "Signup" ; } else { echo "You"; } ?>)</span>
        <?php } ?>
      </div>
    </div>
    <div class="right-menu account profile dual">
      <h2>History</h2>
        <div id="content">
        <?php if($contact_history !=''){?>
          <input type="hidden" name="page_counter" id="page_counter" value="0" />
          <input type="hidden" name="contact_soft_bounce" id="contact_soft_bounce" value="<?php echo $contact_soft_bounce;?>" />
          <input type="hidden" name="contact_bounce_status" id="contact_bounce_status" value="<?php echo $contact_bounce_status;?>" />
          <table  width="100%" class="tbl-small" id="results">
            <tbody class="history_contact_rec">
              <tr>
                <th class="contacts_change" style="width:200px;">Date</th>
                <th class="contacts_change" style="width:370px;">Campaign Name</th>
                <th class="contacts_change" style="width:100px;">Activity</th>
              </tr>
              <?php echo $contact_history;?>
            </tbody>
         </table>
        <div class="loading-table"><div id='last_msg_loader' class="loader"></div></div>
        <?php } else { ?>
        <div class="loading-table"><div id='last_msg_loader' class="loader"><h2>No Records Found</h2></div></div>
        <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>
