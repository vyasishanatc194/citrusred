<script type="text/javascript">
   $(document).ready(function() {
	  $('#subscription_cancel').click(
	      function() {
	          return false;
	  });
  });
  function submit_frm(){
    var block_data="";
    block_data+="action=save&"+$('#interval_time_frm_submit').serialize();
    jQuery.ajax({
      url: "<?php echo base_url() ?>newsletter/autoresponder/edit_interval_time/<?php echo $autoresponder_id; ?>",
      type:"POST",
      data:block_data,
      success: function(data) {
        var data_arr=data.split(":", 2);
        if(data_arr[0]=="error"){
          $('.msg').html(data_arr[1]);
        $('.msg').addClass('info');
          $('.msg').fadeIn();
        }else if(data_arr[0]=="success"){
          $('.msg').html('Interval updated successfully.');
          $('.msg').addClass('info');
          $('.msg').fadeIn();
          if(data_arr[1]==0){
            parent.$('.campaign_<?php echo $autoresponder_id; ?>').find('.day').html("within a couple hours after");
          }else if(data_arr[1]==1){
            parent.$('.campaign_<?php echo $autoresponder_id; ?>').find('.day').html(data_arr[1]+" day after");
          }else{
            parent.$('.campaign_<?php echo $autoresponder_id; ?>').find('.day').html(data_arr[1]+" days after");
          }
          setTimeout( function(){$('.msg').fadeOut();} , 4000);
          $.fancybox.close();
        }
      }
    });
  }
</script>
<div class="contact_frm">
  <form  method="post" name="interval_time_frm_submit" id="interval_time_frm_submit" onsubmit="submit_frm(); return false;">
  <div class="msg" style="display:none;"></div>
  <h5>Update Interval</h5>
    <?php
      echo '<p style="padding: 0 15px"><strong>Number Of Days: *</strong>
          '. form_input(array('name'=>'number_of_days','id'=>'number_of_days','maxlength'=>3,'style'=>'width:50px; display:inline','size'=>40,'value'=>set_value('number_of_days') )).'
          </p><p class="helper">Enter a "0" to have the email sent within a couple hours.</p>';
    ?>
  </div>
	<div class="btn-group">
	  <?php echo form_submit(array('name' => 'submit', 'id' => 'btnEdit','class'=>'btn confirm add_more','content' => 'Submit', 'onclick'=>'javascript:submit_frm();'), 'Save'); ?>
	  <?php echo form_button(array('name' => 'subscription_cancel', 'id' => 'subscription_cancel','class'=>'btn cancel','onclick'=>'javascript:$.fancybox.close();','content' => 'Cancel'
	), 'Cancel'); ?>
	</div>
 <input type="hidden" name="autoresponder_id" id="autoresponder_id" value="<?php echo $autoresponder_id; ?>" />
 </form>
</div>
