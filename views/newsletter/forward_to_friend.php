<html>
<head>
  <title>Forward to Friend</title>
  <?php echo link_tag('webappassets/css/style.css?v=6-20-13'); ?>
  <script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-1.5.1.min.js?v=6-20-13"></script>
</head>
<body>
<!--[main script] -->
<!--[/main script] -->
<script type="text/javascript">
  jQuery(".remove_tr").live('click',function(event){
    $(this).parent().remove();
  });
  function add_table_row(){
    var x=0;
    for (i=0; i< document.frmForwardToFriend.elements.length; i++){
      if('to[]'==document.frmForwardToFriend.elements[i].name)x++;
    }
    if(x <5){
      var tr_row='<div><input type="text" title="To" class="west" id="to[]" value="" name="to[]"><a href="javascript:void(0);" class="remove_tr"><i class="icon-remove-sign"></i></a></div>';
      $('#input-container').append(tr_row);
    }else{
      $('.msg').show();
      $('.msg').html('<p>You can not forward campaign to more than 5 friends.</p>');
      setTimeout( function(){$('.msg').hide();} , 4000);
    }
  }
  function submitFrm(){
    var block_data=$('#frmForwardToFriend').serialize()+'&submit=Submit';
    var url="";
    <?php if($is_autoresponder==1){ ?>
      url="<?php echo base_url() ?>newsletter/forward_to_friend/autoresponder/<?php echo $campaign_id; ?>/<?php echo $scheduled_id; ?>/<?php echo $subscriber_id; ?>/<?php echo $forward_friend_id; ?>";
    <?php }else{ ?>
      url="<?php echo base_url() ?>newsletter/forward_to_friend/index/<?php echo $campaign_id; ?>/<?php echo $subscriber_id; ?>/<?php echo $forward_friend_id; ?>";
    <?php } ?>
    jQuery.ajax({
      url:url,
      type:"POST",
      data:block_data,
      success: function(data) {
        var data_array=data.split(":");
        if(data_array[0]=="error"){
          $('.msg').show();
          $('.msg').html(data_array[1]);
        }else{
          $('.info').show();
          $('.info').html("Campaign has been forwrd to your friends.");
          setTimeout( function(){$('.info').hide();} , 4000);
        }
      }
    });
  }
</script>
<!--[body]-->
  <section id="send-to-friend-main-container" role="main" class="main-container content-page">
    <h2>Forward to Friend</h2>
    <div class="content key-points send-to-friend-container">
      <div id="send-to-friend">
        <div class="msg info"><?php if(validation_errors()) echo validation_errors(); ?></div>
        <!-- <p> Already use Red Cappi on your phone? <a href="#">Finish signup now.</a> </p> -->
        <?php
          if($is_autoresponder==1){
            echo "<form action='".base_url()."newsletter/forward_to_friend/autoforwardnow/{$campaign_id}/{$scheduled_id}/{$subscriber_id}/{$forward_friend_id}' id='frmForwardToFriend' name= 'frmForwardToFriend' method='post' >";
          }else{
            echo "<form action='".base_url()."newsletter/forward_to_friend/forwardnow/{$campaign_id}/{$subscriber_id}/{$forward_friend_id}' id='frmForwardToFriend' name= 'frmForwardToFriend'  method='post' >";
          }
        ?>
          <label for="to[]">To:</label>
          <div id="input-container">
            <?php echo  form_input(array('name'=>'to[]','id'=>'to[]' ,'class'=>'west' ,'value'=>set_value('to'),'title'=>'To')); ?>
          </div>
          <div class="send-to-friend-container-button">
            <a href="javascript:void(0);" class="btn cancel" onclick="add_table_row();"><i class="icon-plus"></i> Add More Fields</a> <?php echo form_submit(array('name' => 'submit', 'id' => 'submit','content' => 'submit','class'=>'btn confirm'), 'Submit'); ?>
          </div>
        </form>
      </div>
    </section>
    <?php
	echo '<a href="'.site_url("/").'" class="send-to-friend-logo"> <img src="'. $this->config->item('webappassets').'images-front/thanks-logo.png?v=6-20-13" alt="logo" title="logo" border="0"></a>';
	
	
	// echo '<div class="footlink">Powered by <a href="http://www.'.SYSTEM_DOMAIN_NAME.'" target="_blank">RedCappi</a></div>';	
	 
	?>
	
  <!--[/body]-->
</body>
</html>
