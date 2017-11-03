<!--[body]-->
<div id="body-dashborad">
  <div class="container">
    <h1>Refer a Friend</h1>
    <?php
    if(validation_errors()){
        echo '<div class="msg info">'.validation_errors().'</div>';
    }
     
    if ($this->session->flashdata('message')!=''){
        echo '<div class="msg info">'. $this->session->flashdata('message').'</div>';
    }
    ?>
 
    <div class="update-profile refer">
      <?php
        echo form_open('refer_friend/index', array('id' => 'frmReferFriend'));
      ?>
      <label for="name">Your Name<em>*</em></label>
      <?php echo  form_input(array('name'=>'your_name','id'=>'your_name','maxlength'=>50,'size'=>40  ,'value'=>set_value('your_name'),'title'=>'Your Name'));?>
      <label for="to">To:<em>*</em> <small>(use commas to separate emails)</small></label>
      <?php echo  form_textarea(array('name'=>'to','id'=>'to' ,'value'=>set_value('to'),'title'=>'To'));?>
      <label for="message">Message*</label>
      <?php echo  form_textarea(array('name'=>'message','id'=>'message' , 'value'=>$send_message,'title'=>'Message'));?>
      <p>
        This is one-time email. <a class="refer_privacy" href="<?php echo  base_url()."privacy";?>">Privacy Statement.</a>
      </p>
      <?php echo form_submit(array('name' => 'submit', 'id' => 'submit','content' => 'Send','class'=>'btn confirm'), 'Send'); ?>
    </div>
  </div>
</div>
<!--[/body]-->
