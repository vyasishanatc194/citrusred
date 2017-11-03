<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery.fastconfirm.js?v=6-20-13"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->config->item('webappassets');?>css/jquery.fastconfirm.css?v=6-20-13" media="screen" />
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.mousewheel-3.0.4.pack.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.pack.js?v=6-20-13"></script>
<link href="<?php echo $this->config->item('webappassets');?>js/fancybox/jquery.fancybox-1.3.4.css?v=6-20-13" rel="stylesheet" type="text/css" />
<script type="text/javascript">
  function openAutoresponderForm(action){
    $('#autoresponder_action').val(action);
    var msg=$('#autoresponder_menu').html();
    $.fancybox({'content' : "<div style='width:400px;' id='edit-autoresponder-info'>"+msg+"</div>"});
    bindAutoresponderForm();
    $('#autoresponder_title').val('');
    $('select#autoresponder_subscription_id option:first-child').attr("selected", "selected");
    $('.drop_img').attr('src',"<?php echo $this->config->item('webappassets');?>images/down.png?v=6-20-13");
  }
  var bindForm = true;
  var bindAutoresponderForm = function() {
    if(bindForm) {
      bindForm = false;
      $("#autoresponderfrm").live("submit", function(e) {
        e.preventDefault();
        e.stopPropagation();
        var block_data="action=submit&" + $('#autoresponderfrm').serialize();
        var url="";
		var autoresponder_group_id = $('#autoresponder_grp_id').val();
        if($('#autoresponder_action').val()=="create"){
          url="<?php echo base_url() ?>newsletter/autoresponder/create_group/";
        }else{
          url="<?php echo base_url() ?>newsletter/autoresponder/update_group/"+autoresponder_group_id;
        }
        jQuery.ajax({
          url: url,
          type:"POST",
          data:block_data,
          success: function(data){
            var data_arr=data.split(":", 2);
            if(data_arr[0]=="error"){
              $('.autoresponder_msg').html(data_arr[1]);
            }else if(data_arr[0]=="success"){
              if($('#autoresponder_action').val()=="create"){
                $('.autoresponder_msg').html("Autoresponder created successfully.");
                setTimeout(function(){$('#autoresponder_menu').hide();} , 4000);
                location.reload();
              }else{
                $('.autoresponder_msg').html("Autoresponder updated successfully.");
                $('.autoresponder_msg').show();
                setTimeout(function(){$.fancybox.close();} , 2000);
                setTimeout(function(){$('.autoresponder_group').show();} , 2000);
                $('#'+autoresponder_group_id).find(".subscription_strong").html($('#autoresponder_title').val());
                $('#autoresponder_list_id_'+autoresponder_group_id).val($('#autoresponder_subscription_id').val());
              }
            }
          }
        });
      });
    }
  };
  function displayAutoresponder(grp_id,grp_status,display_delete_hover){
    if(grp_id){
      $('.editing-theme-box').removeClass('active');
      $('#'+grp_id).addClass('active');
       if(!display_delete_hover){
        $('.delete_hover').hide();
      }
    }else{
      grp_id=$('.select').attr('id');
    }
    var block_data='status='+grp_status;
    jQuery.ajax({
      url: "<?php echo base_url() ?>newsletter/autoresponder/display_autoresponder/"+grp_id,
      data:block_data,
      type:"POST",
      success: function(data) {
        $('#autoresponder_list').html(data);
        reinit();
      }
    });
  }
  function inactiveAutoresponder(grp_id,grp_status){
    if(grp_status==1){
      grp_status=0;
    }else{
      grp_status=1;
    }
    var block_data='status='+grp_status;
     jQuery.ajax({
        url: "<?php echo base_url() ?>newsletter/autoresponder/update_autoresponder_grp/"+grp_id,
        data:block_data,
        type:"POST",
        success: function(data) {
        document.getElementById(grp_id).onclick = function(){displayAutoresponder(grp_id,grp_status);}
        displayAutoresponder(grp_id,grp_status);
        }
      });
  }
  function update_status(autoresponder_id){
     jQuery.ajax({
        url: "<?php echo base_url() ?>newsletter/autoresponder/autoresponder_status/"+autoresponder_id,
        type:"POST",
        success: function(data) {
          var msg_status;
          $el = $('#status_'+autoresponder_id);
          if(data=="Inactive"){
            $el.find("img.auto_play").show();
            $el.find("img.auto_pause").hide();
            $el.parents("tr.autoresponder_list").addClass("campaign-inactive");
          }else{
            $el.find("img.auto_pause").show();
            $el.find("img.auto_play").hide();
            $el.parents("tr.autoresponder_list").removeClass("campaign-inactive");
          }
          $('#status_'+autoresponder_id).html(msg_status);
        }
      });
  }
  function reinit() {
    $(".fancybox").fancybox({'autoDimensions':false,'height':'auto','width':'400'});
  }
/*
    ajax call to delete contact
*/
jQuery(".delete-row").live('click',function(event)
{
  var thisID = jQuery(this).attr('name');
  jQuery(this).parents("tr").fastConfirm({
    position: "top",
    questionText: "Are you sure you want to delete this autoresponder campaign?",
    onProceed: function(trigger) { 
      var autoresponder_id=thisID;	  
       jQuery.ajax({
        url: "<?php echo base_url() ?>newsletter/autoresponder/delete/"+autoresponder_id,
        type:"POST",
        success: function(data){		
          jQuery(trigger).parents('.autoresponder_list').remove();
          var data_arr=data.split("|");
          displayAutoresponder(data_arr[0],data_arr[1]);
        }
      });
    },
    onCancel: function(trigger) {
    }
  });

});
/*
    ajax call to delete autoresponder group
*/
jQuery(".delete_group").live('click',function(event)
{
  jQuery(this).fastConfirm({
    position: "top",
    questionText: "Are you sure you want <br/>to delete this autoresponder?",
    onProceed: function(trigger) {
      var autoresponder_id=jQuery(trigger).attr('name');
       jQuery.ajax({
        url: "<?php echo base_url() ?>newsletter/autoresponder/delete_group/"+autoresponder_id,
        type:"POST",
        success: function(data){
          if($('.editing-theme-box').length>1){
            jQuery(trigger).parents(".editing-theme-box").remove();
            jQuery('.'+autoresponder_id).remove();
            $(".editing-theme-box:first").addClass("select");
            displayAutoresponder($(".autoresponder_group li:first").find('a').attr('id'),$(".autoresponder_group li:first").find('a').attr('name'));
          }else{
            jQuery(trigger).parents(".editing-theme-box").remove();
            jQuery('.'+autoresponder_id).remove();
            $('.tbl-listing').html('<tr><td>No Autoresponders created.<br/><br/><a  onclick="openAutoresponderForm(\'create\');" href="javascript:void(0);" class="links-bg">New Autoresponder</a></td></tr></table>');
          }
        }
      });
    },
    onCancel: function(trigger) {
    }
  });

});
/*
    ajax call to edit autoresponder group
*/
jQuery(".edit_group").live('click',function(event)
{
  var msg=$('#autoresponder_menu').html();
  $.fancybox({'content' : "<div style='width:400px;' id='edit-autoresponder-info'>"+msg+"</div>"});
  bindAutoresponderForm();
  var $el = $("#edit-autoresponder-info");
  var title=$(this).parents(".editing-theme-box").find('strong.subscription_strong').html();
  $el.find('#autoresponder_action').val('edit');
  $el.find("h5").html("Edit Autoresponder");
  $el.find('#autoresponder_grp_id').val($(this).parents(".editing-theme-box").attr('id'));
  $el.find('#autoresponder_title').val(LTrim(RTrim(title)));
  var subscription_id=parseInt($(this).find('.autoresponder_list_id').val());
  $el.find("#autoresponder_subscription_id").val(subscription_id);
});

jQuery(".fancybox_edit_inerval").live('click',function(){
  var block_data="";
  var autoresponder_id=jQuery(this).parent().find('.hide_autoresponder_id').html();
  jQuery.ajax({
    url: "<?php echo base_url()."newsletter/autoresponder/edit_interval_time/"?>"+autoresponder_id,
    type:"POST",
    data:block_data,
    success: function(data){
      $.fancybox({
        'content' : data
      });
    }
  });
});
function noStats(){
	var msg='<h5>Confirm</h5><p>Nothing to track. Campaign has not been sent.</p><div class="btn-group"><button class="btn confirm fast_confirm_cancel" onclick="$.fancybox.close();">Ok</button></div>';
	$.fancybox({'content' : "<div style=\"width:400px;\">"+msg+"</div>"});
}
// Removes leading whitespaces
function LTrim( value ) {
  var re = /\s*((\S+\s*)*)/;
  return value.replace(re, "$1");
}
// Removes ending whitespaces
function RTrim( value ) {
  var re = /((\s*\S+)*)\s*/;
  return value.replace(re, "$1");
}

</script>
<script type="text/template" id="autoresponder_menu">
  <div id="add-list">
    <h5>Add Autoresponder</h5>
    <div class="autoresponder_msg info"></div>
    <form method="post" class="form-website" id="autoresponderfrm">
      <p style="padding-bottom: 0">
        <strong>Autoresponder Name</strong>
      </p>
      <?php echo form_input(array('name'=>'autoresponder_title','id'=>'autoresponder_title','maxlength'=>250,'size'=>40,'value'=>set_value('autoresponder_title')));?>
      <p>
        <strong>Select List: </strong>
        <select style="height:20px" name="autoresponder_subscription_id" id="autoresponder_subscription_id">
          <?php
            foreach($autoresponder_data['select_subscriptions'] as $subscription){
              echo "<option value='".$subscription['subscription_id']."'>".ucfirst(substr($subscription['subscription_title'],0,25))."</option>";
            }
          ?>
        </select>
      </p>
      <div class="btn-group">
        <input type="hidden" name="autoresponder_action" id="autoresponder_action" value="create" />
        <input type="hidden" name="autoresponder_grp_id" id="autoresponder_grp_id" value="" />
        <?php
          echo form_submit(array('name' => 'autoresponder_submit', 'id' => 'btnEdit','class'=>'btn confirm','content' => 'Submit'), 'Save');
          echo form_button(array('name'=>'autoresponder_cancel','class'=>'btn cancel', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"$.fancybox.close();"));
        ?>
      </div>
    </form>
  </div>
</script>
<!--[body]-->
  <div id="body-dashborad">
    <div class="container">
      <h1>
        <a onclick="openAutoresponderForm('create');" href="javascript:void(0);" class="btn add"><i class="icon-plus"></i>Add Autoresponder</a>
        Autoresponders
      </h1>

      <div class="subscription_list left-menu contacts autoresponder">
        <?php
          $i=1;
          foreach($autoresponder_data['autoresponder_group'] as $autoresponder_group){
            if($i==1){
              $autoresponder_grp_id=$autoresponder_group['id'];
              $autoresponder_grp_status=$autoresponder_group['status'];
            }
        ?>
          <div class="editing-theme-box" id="<?php echo $autoresponder_group['id'] ?>">
            <a <?php echo $style ; ?> name="<?php echo $autoresponder_group['status'] ?>" href="javascript:void(0);" onclick="displayAutoresponder(<?php echo $autoresponder_group['id'] ?>,<?php echo $autoresponder_group['status'] ?>);">
              <strong class="subscription_strong">
                <?php echo $autoresponder_group['group_name'];?>
              </strong>
            </a>
            <div class="icon-listing">
              <ul class="list-icons contacts">
                <li>
                  <a href="javascript:void();" id="edit_<?php echo $autoresponder_group['id'] ?>" class="edit_group btn cancel delete_contact" name="<?php echo $autoresponder_group['id'] ?>">
                    <img class="campion_send" src="<?php echo $this->config->item('webappassets');?>images/new_png_design/Email-Icon-Edit.png?v=6-20-13" alt="campaigns"/>
                    <input type="hidden" class="autoresponder_list_id" id="autoresponder_list_id_<?php echo $autoresponder_group['id'] ?>" value="<?php echo $autoresponder_group['autoresponder_subscription_id'];?>"/>
                  </a>
                </li>
                <li>
                  <a href="javascript:void();" class="delete_group delete-list btn cancel delete_contact" name="<?php echo $autoresponder_group['id'] ?>">
                    <img class="campion_send" src="<?php echo $this->config->item('webappassets');?>images/new_png_design/Email-Icon-Trash.png?v=6-20-13" alt="campaigns"/>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        <?php
          $i++;
          }
        ?>
        <div style="padding-bottom: 1px"></div>
        <div class="backdrop"></div>
      </div>
      <div class="right-menu contacts">
        <!--[navigation]-->
        <div class="autotresponder_list_div">
          <?php if(count($autoresponder_data['autoresponder_group'])>0){ ?>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tbl-listing list" id="autoresponder_list" >
              <tr>
                <td align="left">
                  <img src="<?php echo $this->config->item('webappassets');?>images/ajax_loading.gif?v=6-20-13" />
                </td>
              </tr>
            </table>
            <script type="text/javascript">
              displayAutoresponder(<?php echo $autoresponder_grp_id; ?>,<?php echo $autoresponder_grp_status; ?>,'1');
            </script>
          <?php }else{ ?>
            <div class="empty" style="height: 400px">
              <p style="padding-top: 100px">No records found. Click on “Add Autoresponder” to get started.</p>
              <a class="btn add" onclick="openAutoresponderForm('create');" href="javascript:void(0);" ><i class="icon-plus"></i>Add Autoresponder</a>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>
<!--[/body]-->
