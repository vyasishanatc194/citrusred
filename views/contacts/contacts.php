<script type="text/javascript" src="<?php  echo $this->config->item('webappassets');?>js/fancybox/jquery.mousewheel-3.0.4.pack.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php  echo $this->config->item('webappassets');?>js/jquery.blockUI.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php  echo $this->config->item('webappassets');?>js/jquery.fastconfirm.js?v=6-20-13"></script>
<link rel="stylesheet" type="text/css" href="<?php  echo $this->config->item('webappassets'); ?>css/jquery.fastconfirm.css?v=6-20-13" media="screen" />
<script type="text/javascript" src="<?php  echo $this->config->item('webappassets');?>js/contacts.js?v=6-20-13"></script>
<script type="text/javascript">
  var base_url="<?php  echo base_url(); ?>";
  var webappassets="<?php  echo $this->config->item('webappassets'); ?>";
  var memid="<?php  echo $extra['member_id']; ?>";
  /****** function to display subscription list using jquery************/
  function display_subscription(subscription_id){
    var block_data="";
    var max_contacts=false;
    <?php  if( $subscriber_count < $package_max_contacts){?>
    max_contacts=true;
    <?php  }?>
    $('.tbl-contacts').html($(".loading").html());
    $.ajax({
      url: base_url+"newsletter/contacts/display_ajax",
      type:"POST",
      data:block_data,
      success: function(data) {
        var data_arr=data.split("/\\");
        $('.subscription_list').html(data_arr[1]);
        $('#'+subscription_id).addClass("main-list")
        var data_select_arr=data_arr[0].split("|");
        var select_box_list="";
        var ul_list="";
        var ul_list_copy="";
        for(var i=0;i<data_select_arr.length;i++){
          var data_val_arr=data_select_arr[i].split("=");
          ul_list+= "<li onclick='submit_frm("+data_val_arr[0]+",\"move\")' name='"+data_val_arr[0]+"' class='move_"+data_val_arr[0]+" list' ><a href='javascript:void(0);'>"+data_val_arr[1]+"</a></li>";
          ul_list_copy+= "<li onclick='submit_frm("+data_val_arr[0]+",\"copy\")' name='"+data_val_arr[0]+"'  class='copy_"+data_val_arr[0]+" list' ><a href='javascript:void(0);'>"+data_val_arr[1]+"</a></li>";
          select_box_list+= "<option value='"+data_val_arr[0]+"'>"+data_val_arr[1]+"</option>";
        }
        
        ul_list+="<li onclick='unsubscribe_list("+document.form1.subscription_selected_id.value+",\"unsubscribe\")' name='"+document.form1.subscription_selected_id.value+"' class='list_do_not_mail' ><a href='javascript:void(0);'> Do Not Mail</a></li>";
        $('.move_list').html(ul_list);
        $('.copy_list').html(ul_list_copy);
        $('#subscription_contact_one').html(select_box_list);
        $('#subscription_select').html(select_box_list);
        $('#subscription_select_copy').html(select_box_list);
        reinit();
        $('.list').show();
        $('.move_-<?php  echo $this->session->userdata('member_id'); ?>').hide();
        $('.copy_-<?php  echo $this->session->userdata('member_id'); ?>').hide();
        $('.move_'+subscription_id).hide();
        $('.copy_'+subscription_id).hide();
        if(subscription_id<0){
          $('.move_list').find('.list').hide();
        }
        var total_contacts=$('#-<?php  echo $this->session->userdata('member_id'); ?>').find('.right-no').html();
        $('.plan').html(total_contacts);
        $('.list_title').find('span').html($('#subscription_title_'+subscription_id).val());
      }
    });
  }
  $('#delete_subscriber').live('click',function(event){
    if($(this).hasClass('disabled_select')){
      return false;
    }
    <?php  if($package_max_contacts==100){ ?>
        var check_subscriber_id=0;
      $('.check-boxalign').each(function () {
        if (this.checked) {
          check_subscriber_id++;
        }
      }
                               );
      if((check_subscriber_id>1)||($('#action').val()=='page')){
        fancyAlert('Free accounts can only delete contacts one at a time. Upgrading your account will allow you to delete contacts in bulk');
      }
      else{
        $this = $(this);
        $.fancybox({ 'href': $this.attr('href'), 'autoDimensions':false, 'centerOnScroll':true, 'scrolling':false, 'width':'420', 'height':'183'});
      }
      <?php  }else {?>
      $this = $(this);
      $.fancybox({'href': $this.attr('href'), 'autoDimensions':false, 'centerOnScroll':true, 'scrolling':true, 'width':'400', 'height':'183'});
      <?php  } ?>
      return false;
  }
                              );
  <?php  if('1'==$extra['contact_import_progress']){ ?>
      setInterval('checkImportStatus()',10000);
    <?php  }else { ?>
      $('.subscriber_msg').hide();
    <?php } ?>

  
</script>
<!--[body]-->
<div id="body-dashborad">
  <div class="container">
    <h1>
      <a class="btn add" href="<?php  echo  site_url("newsletter/contacts_add"); ?>">
        <i class="icon-plus"></i>Add Contacts
      </a>
      <a onclick="openSubscriptionForm();" href="javascript:void(0); "title="Create New List" class="btn cancel list create_newList">
        <i class="icon-plus"></i>Create New List
      </a>
	    <div class="slider_bar">
		<strong class="usage">Usage Plan:</strong>
      <div class="bar-border">
        <div class="progress<?php if(($subscriber_count/$package_max_contacts) > 0.75) {?> alert<?php } ?>">
          <div class="bar" style="width: <?php echo $subscriber_count/$package_max_contacts*100 ?>%"></div>
		  
		  </div> 
		  </div> 
          <?php  if($subscriber_count > $package_max_contacts) { ?>
            <a href="<?php  echo  site_url("upgrade_package_cim/index"); ?>">
				<strong><i class="icon-exclamation-sign"></i> Upgrade Now <i class="icon-exclamation-sign"></i></strong>
            </a>
          <?php  } else { ?>
          <strong> <?php  echo $subscriber_count; ?> / <?php  echo $package_max_contacts; ?> <?php /* if($package_max_contacts==100) echo'Free'; ?> Plan<?php */?></strong>
          <?php } ?>
       
      </div>
    
      <div class="head">Contacts</div>
    </h1>
    <?php  if('1'==$extra['contact_import_progress']){ ?>
    <div class="info">
      While your contacts are uploading, we wanted to share that your contact list will be graded for deliverability before your first campaign is released, helping us maintain sending reputation for you and our other customers. If for some reason your contact list does not pass our deliverability grade, our support team will reach out to you for next steps.  In the meantime, please feel free to create and schedule your campaign(s). Navigating away from this page will not interrupt the upload. After completion of the import process, you will be informed by email.
    </div>
    <?php  } ?>

    <div id="subscription_menu">
      <div id="add-list">
        <form onsubmit="ajaxSubscriptionFrm(this); return(false);" method="post" class="form-website" id="subscriptionfrm"  name="subscriptionfrm">
          <h5>Enter List Name</h5>
          <div class="subscription_msg info"></div>
          <div>
            <?php echo form_input(array('name'=>'subscription_title','id'=>'subscription_title','maxlength'=>250,'size'=>40,'value'=>set_value('subscription_title'),'class'=>'subscription_title')); ?>
          </div>
          <div class="btn-group">
            <?php
              echo form_submit(array('name' =>'subscription_submit', 'id' =>'btnEdit','content' =>'Submit', 'class' => "btn confirm"), 'Save');
              echo form_button(array('name'=>'campaign_cancel', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"$.fancybox.close();", 'class' => "btn cancel"));
            ?>
          </div>
        </form>
      </div>
    </div>

    <form  method="post" name="form1" id="form1" onsubmit="submit_frm(); return false;">
      <input type="hidden" name="contact_list_action" id="contact_list_action" />
      <input type="hidden" name="select_action" id="select_action" />
      <input type="hidden" name="action" id="action" value="" />
      <input type="hidden" name="uncheck_list" id="uncheck_list" value="" />
      <input type="hidden" name="action_notmail" id="action_notmail" value="" />
      <input type="hidden" name="checked" id="checked" value="" />
      <input type="hidden" name="search_key" id="search_key" value="" />
      <input type="hidden" name="visible_contacts_count" id="visible_contacts_count" value="" />
      <input type="hidden" name="subscription_selected_id" id="subscription_selected_id" value="<?php  echo $subscription_first_id; ?>" />

      <div class="subscription_list left-menu contacts">
        <?php
        //Fetch subscriptions from subscriptions array
        if(count($subscriptions)) {
          $i=1;
          foreach($subscriptions as $subscription){

            if($subscription['subscription_id']==$subscription_first_id){
              $selected_subscription_title=$subscription['subscription_title'];
            }
        ?>
        <div class="editing-theme-box<?php if($subscription['subscription_id']<0){?> active <?php } ?>" id="<?php  echo $subscription['subscription_id']; ?>">
          <div class="listname-no"  onclick="display_contacts('<?php  echo $subscription['subscription_id']; ?>')" >
            <span class="right-no"><?php  echo $total["'".$subscription['subscription_id']."'"]; ?></span>
            <strong class="subscription_strong" name="<?php  echo $subscription['subscription_id']; ?>" id="subscription_id_<?php  echo $subscription['subscription_id']; ?>">
              <?php  echo ucfirst(substr ($subscription['subscription_title'],0,15)); ?>
              <input type="hidden" name="subscription_title_<?php  echo $subscription['subscription_id']; ?>" id="subscription_title_<?php  echo $subscription['subscription_id']; ?>" value="<?php  echo $subscription['subscription_title']; ?>" />
            </strong>
            <input type="text" name="subscription_text_<?php  echo $subscription['subscription_id']; ?>" id="subscription_text_<?php  echo $subscription['subscription_id']; ?>"  class="subscription_text" value="<?php  echo $subscription['subscription_title']; ?>" style="display:none;padding:0px; margin:0px;border:none;" maxlength="25"/>
          </div>
          <div class="icon-listing">
            <ul class="list-icons contacts">
              <?php  if($subscription['subscription_id']>0){ ?>
              <li>
                <a id="subscriber_edit" name="<?php  echo $subscription['subscription_id']; ?>" class="subscriber_edit btn cancel delete_contact" href="javascript:void(0);"><img class="campion_send" src="<?php echo $this->config->item('webappassets');?>images/new_png_design/Email-Icon-Edit.png?v=6-20-13" alt="campaigns"></a>
              </li>
              <?php  } ?>
              <?php  if($subscription['subscription_id']>0){ ?>
              <li>
                <a <?php  if($subscription['subscription_id']!="-".$this->session->userdata('member_id')) { ?>class="delete-list fancybox btn cancel delete_contact" href="<?php  echo base_url(); ?>newsletter/contacts/delete/<?php  echo $subscription['subscription_id']; ?>" name="<?php  echo $subscription['subscription_id']; ?>" <?php  } ?>><img class="campion_send" src="<?php echo $this->config->item('webappassets');?>images/new_png_design/Email-Icon-Trash.png?v=6-20-13" alt="campaigns"></i></a>
              </li>
              <?php } ?>
            </ul>
            <ul class="list-icons edit_subscription" style="display:none;">
              <li><a class="btn confirm" onclick="saveSubscriptionTitle('<?php  echo $subscription['subscription_id']; ?>');" href="javascript:void(0);">Save</a></li>
              <li><a class="btn cancel" onclick="javascript:$(this).parents('.editing-theme-box').find('.list-icons').show();$(this).parent().parent().hide();$('#subscription_text_<?php  echo $subscription['subscription_id']; ?>').hide();$('#subscription_id_<?php  echo $subscription['subscription_id']; ?>').show();$('#subscription_text_<?php  echo $subscription['subscription_id']; ?>');$('.right-no').show();" href="javascript:void(0);">Cancel</a></li>
            </ul>
          </div>
        </div>
        <?php $i++; } } else {?>
          <div class="empty" style="height: 400px">
            <p style="padding-top: 100px">No Records Found. To begin adding contacts click on "Add Contacts".</p>
            <a class="btn add" href="<?php  echo  site_url("newsletter/contacts_add"); ?>"><i class="icon-plus"></i>Add Contacts</a>
          </div>
        <?php  } ?>
        <div class="backdrop"></div>
      </div>

      <div class="right-menu contacts">
        <h2 class="list_title autoresponder">
          <a id="move_subscriber" href="javascript:void(0);" class="btn cancel white-drop-button bgdroppable do_not_mail" onclick="javascript:$('.do_not_mail_list').slideToggle();$('.move_list').slideUp();$('.copy_list').slideUp();">
            Do Not Mail List <i class="icon-angle-down"></i>
          </a>
          <span><?php  echo $selected_subscription_title; ?></span>
          <ul class="do_not_mail_list drop-down" >
            <li>
              <a href='javascript:void(0);' onclick="displayRemoved();" id="removed_count">
                Removed (<?php  echo $removed_count; ?>)
              </a>
            </li>
			<li>
              <a href='javascript:void(0);' onclick="displayUnsubscribe();" id="unsubscribe_count">
                Unsubscribed (<?php  echo $unsubscriber_count; ?>)
              </a>
            </li>
            <li>
              <a href='javascript:void(0);' onclick="displayBounces()" id="bounce_count">
                Bounced (<?php  echo $bounce_count; ?>)
              </a>
            </li>
            <li>
              <a href='javascript:void(0);'  onclick="displayComplaints()" id="complaint_count" >
                Complaints (<?php  echo $complaint_count; ?>)
              </a>
            </li>
          </ul>
        </h2>
        <div class="tools">
          <div id="search-container"><input name="email_search" type="text" class="input-radius" id="email_search" />
            <input type="submit" name="btnSearch" id="btnSearch" class="btn confirm" onclick="javascript:search_form();return false;" value="Search" />
            <i class="icon-search"></i>
          </div>
          <ul class="tool-set">
            <li>
              <a href="javascript:void(0);" onclick="updateChecked('list',true);" class="btn cancel select_list" id="select_list">
                Select Page
              </a>
            </li>
            <li>
              <a href="javascript:void(0);" class="btn cancel select_page" onclick="updateChecked('page',true);" id="select_page">
                Select All
              </a>
            </li>
            <li>
              <a href="javascript:void(0);" class="btn cancel white-drop-button fl bgdroppable" onclick="slideMenu('move_list')" ><span class="move_subscriber move_subscriber_list">
                Move To <i class="icon-angle-down"></i>
              </span></a>
              <ul  class="move_list drop-down" >
              <?php
                foreach($select_subscriptions as $subscription){
                  echo "
                  <li  onclick='submit_frm(".$subscription['subscription_id'].",\"move\")' name='".$subscription['subscription_id']."' class='move_".$subscription['subscription_id']." list' >
                  <a href='javascript:void(0);'>
                  ".ucfirst(substr($subscription['subscription_title'],0,25))."
                  </a>
                  </li>
                  ";
                }
                echo "
                <li onclick='unsubscribe_list(".$subscription['subscription_id'].",\"unsubscribe\")' name='".$subscription['subscription_id']."' class='do-not-mail-option' >
                <a href='javascript:void(0);'>
                Do Not Mail
                </a>
                </li>
                ";
              ?>
              </ul>
            </li>
            <li>
              <a href="javascript:void(0);" class="btn cancel white-drop-button fl bgdroppable" onclick="slideMenu('copy_list')" ><span class="move_subscriber copy_subscriber_list">
                Copy To <i class="icon-angle-down"></i>
              </span></a>
              <ul  class="copy_list drop-down">
              <?php
                foreach($select_subscriptions as $subscription){
                  echo "
                  <li onclick='submit_frm(".$subscription['subscription_id'].",\"copy\")' name='".$subscription['subscription_id']."' class='copy_".$subscription['subscription_id']." list' >
                  <a href='javascript:void(0);'>
                  ".ucfirst(substr($subscription['subscription_title'],0,25))."
                  </a>
                  </li>
                  ";
                }
              ?>
              </ul>
            </li>
            <li>
              <a href="<?php  echo base_url(); ?>newsletter/subscriber/subscriber_delete/<?php  echo $subscription_first_id; ?>" class="btn add form_delete delete_subscriber " id="delete_subscriber">
                Delete
              </a>
            </li>
          </ul>
        </div>
        <div class="info" id="msg"></div>
        <?php
          if(validation_errors()){
            echo '
            <div style="color:#FF0000;" class="info">
            '.validation_errors().'
            </div>
            ';
          }
        ?>
        <?php
          // display all messages

          if (is_array($messages)):
          echo '<div class="info" style="border:none;background:none;">';
          foreach ($messages as $type =>
          $msgs):
          foreach ($msgs as $message):
          echo ('
          <span class="' .  $type .'">
          ' . $message . '
          </span>
          ');
          endforeach;
          endforeach;
          echo '
          </div>
          ';
          endif;
        ?>
		
        <div style="width:100%;display:none;" class="disabled" id="DNM_msg"><p><b>Important: </b>The Do Not Mail (DNM) list ensures contacts that have unsubscribed, complained, or bounced do not get re-added to your list. DNM contacts should not be removed unless a contact has specifically requested to be added back to your mailing list.
<br/>
<br/>
		<font color="red">*</font> The contacts in the Do Not Mail (DNM) list DO NOT count towards your total contacts for billing purposes.</p></div>
        <div style="width:100%;" class="tbl-contacts"></div>
        <div class="loading">
          <div class="loader">
            <img src="<?php  echo $this->config->item('webappassets');?>images/loader.gif?v=6-20-13" alt="Loading" />
          </div>
        </div>
        <div class="pagination_div"><ul class="pagination"></ul></div>
        <!--[/navigation]-->
      </div>
    </form>

    <!--Define hidden variables -->
    <div class="unsubscriber_box" style="display:none">
      <div class="fancybox-page registration-page_contact_delete" >
        <div style="width:600px; margin:15px auto;">
          <div class="fancybox-form contact_frm" style="height:120px;width:500px;margin:5px 25px;" >
            <input type="hidden" name="unsubscriber_subscription_id" id="unsubscriber_subscription_id" value="<?php  echo $subscription_id; ?>" />
            <table  width="100%" border="0" cellspacing="0" cellpadding="0"  class="contact_tbl">
              <tr>
                <td class="popup_large">
                  You're about to unsubscribe <b id='total_unsubscribe_count' class="error"></b> contacts from your account and move them to your Do Not Mail List.
                </td>
              </tr>
              <tr>
                <td class="popup_small">
                  Are you sure you want to continue?
                </td>
              </tr>
              <tr>
                <td colspan="2">
                <?php  
				echo form_submit(array('name' =>'subscription_submit','id' =>'btnEdit','class'=>'btn danger','content' =>'Submit','style' =>'margin-left:5px;','onclick'=>'submit_unsubscribe_form()'), 'Add to Do Not Mail List');
                  
				echo form_button(array('name' =>'subscription_cancel','id' =>'subscription_cancel','class'=>'btn cancel','content' =>'Cancel','style' =>'margin-left:5px;','onclick'=>'javascript:$.fancybox.close();'),  'Cancel'); 
				?>
                </td>
              </tr>
            </table>
          </div>
        </div>
        <input type="hidden" name="order_by" class="order_by" value="asc" />
        <input type="hidden" name="order_by_paging" class="order_by_paging"  />
        <input type="hidden" name="order_by_column" class="order_by_column" value="" />
        <!--[/body]-->
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  (function(){
    if(window.location.hash) {
      // Fragment exists
      var hash_url=window.location.hash.substr(1);
      if(hash_url<0){
        hash_url=hash_url.substring(1);
        $('#checked').val(hash_url);
      }
      var url="https://"+window.location.host+"/";
      var length_url=url.length;
      var sub_url=document.URL.substring(length_url);
      var url_arr=sub_url.split('#');
      var url_split=url_arr[0].split('/');
      var srch="";
      var subscription_id="";
      var scroll_top="";
      var action_arr;
      var action_checked;
      var action_notmail;
      var action;
      if(url_split.length>7){
        srch=url_split[7].split('.');
        $('#email_search').val(srch[0]);
        action=url_split[url_split.length-2];
        action_notmail=url_split[url_split.length-3];
        subscription_id=url_split[url_split.length-5];
        scroll_top=url_split[url_split.length-4];
      }
      else{
        action_arr=url_split[url_split.length-1].split('.');
        action_checked=action_arr[0].split('_');
        subscription_id=url_split[url_split.length-4];
        scroll_top=url_split[url_split.length-3];
        action_notmail=url_split[url_split.length-2];
        var action=action_checked[0];
        if(action_checked[1]){
          $('#checked').val(action_checked[1]);
        }
      }
      if(action_notmail!="display_notmail"){
        $('#action_notmail').val(action_notmail);
      }
      var url="<?php  echo base_url(); ?>newsletter/subscriber/subscriber_list/"+subscription_id+"/"+hash_url;
      var block_data="";
      if($('#checked').val()!=""){
        block_data="action="+action+"&checked="+$('#checked').val();
      }
      else{
        block_data="action="+action;
      }
      block_data+="&srch_email="+$('#email_search').val();
      if($('#action_notmail').val()=="removed"){
        //$('.move_subscriber').parent().hide();
        $('.move_subscriber').parent().addClass('disabled');
        $('.delete_subscriber').addClass('disabled_select');
        $('.select_page').addClass('disabled_select');
        $('.select_list').addClass('disabled_select');
        block_data+="&unsubscribe=5";
      }else if($('#action_notmail').val()=="unsubscribe"){
        //$('.move_subscriber').parent().hide();
        $('.move_subscriber').parent().addClass('disabled');
        $('.delete_subscriber').addClass('disabled_select');
        $('.select_page').addClass('disabled_select');
        $('.select_list').addClass('disabled_select');
        block_data+="&unsubscribe=1";
      }else if($('#action_notmail').val()=="complaints"){
        //$('.move_subscriber').parent().hide();
        $('.move_subscriber').parent().addClass('disabled');
        $('.delete_subscriber').addClass('disabled_select');
        $('.select_page').addClass('disabled_select');
        $('.select_list').addClass('disabled_select');
        block_data+="&complaints=2";
      }else if($('#action_notmail').val()=="bounce"){
        //$('.move_subscriber').parent().hide();
        $('.move_subscriber').parent().addClass('disabled');
        $('.delete_subscriber').addClass('disabled_select');
        $('.select_page').addClass('disabled_select');
        $('.select_list').addClass('disabled_select');
        block_data+="&bounce=1";
      }
      $('#action').val(action);
      $('.tbl-contacts').html($(".loading").html());
      $.ajax({
        type: "POST",
        data: block_data,
        url: url,
        success: function(data){
          var data_arr=data.split("|");
          $('.contacts_change').remove();
          $('.tbl-contacts').html(data_arr[1]);
          $('.pagination_div').html(data_arr[0]);
          if(!$('#action_notmail').val()){
            display_subscription(<?php  echo $subscription_first_id;?>);
          }else if($('#action_notmail').val()=='removed'){
            $('.list_title').find('span').html('Removed');
          }else if($('#action_notmail').val()=='unsubscribe'){
            $('.list_title').find('span').html('Unsubscribed');
          }else if($('#action_notmail').val()=='complaints'){
            $('.list_title').find('span').html('Complaints');
          }else if($('#action_notmail').val()=='bounce'){
            $('.list_title').find('span').html('Bounced');
          }
          if(action!="display"){
            $('.select_'+action).addClass("main-list");
            document.getElementById('select_'+action).onclick = function(){
              updateChecked(action,false);
            }
          }
          $('#removed_count').html('Removed ('+data_arr[6]+')');
          $('#unsubscribe_count').html('Unsubscribed ('+data_arr[2]+')');
          $('#bounce_count').html('Bounced ('+data_arr[3]+')');
          $('#complaint_count').html('Complaints ('+data_arr[4]+')');
          $(document).scrollTop(scroll_top);
          reinit();
        }
      });
    }
    else{
      var url_split;
      var url="https://"+window.location.host+"/";
      var length_url=url.length;
      var sub_url=document.URL.substring(length_url);
      url_split=sub_url.split('/');
      if(url_split.length>6){
        var srch=url_split[url_split.length-1].split('.');
        $('#email_search').val(srch[0]);
        var donot_mail=url_split[url_split.length-2];
        $('#action_notmail').val(donot_mail);
      }
      else{
        var donot_mail=url_split[url_split.length-1].split('.');
        $('#action_notmail').val(donot_mail[0]);
      }
      if($('#action_notmail').val()=="unsubscribe"){
        display_contacts(<?php  echo $subscription_first_id;?>,document.getElementById('email_search').value,'','',1);
        $('.tbl-contacts').addClass('donotmaillist');
      }
      else if($('#action_notmail').val()=="complaints"){
        display_contacts(<?php  echo $subscription_first_id;?>,document.getElementById('email_search').value,'','',2);
        $('.tbl-contacts').addClass('donotmaillist');
      }
      else if($('#action_notmail').val()=="bounce"){
        display_contacts(<?php  echo $subscription_first_id;?>,document.getElementById('email_search').value,'','',0,1);
        $('.tbl-contacts').addClass('donotmaillist');
      }
      else{
        display_contacts(<?php  echo $subscription_first_id;?>);
        $('#action_notmail').val('');
      }
      var scroll_top=url_split[url_split.length-2];
      $(document).scrollTop(scroll_top);
    }
  })();
</script>
