<!--[body]-->

<div id="body-dashborad">
  <div class="container update-profile">
    <h1>Change Password</h1>
    <div class="update-profile-container">
      <?php
        if(validation_errors()){
          echo '<div class="info">'.validation_errors().'</div>';
        }
      ?>
      <?php
        echo form_open('user/change_password/', array('id' => 'frmChangePassword'));
      ?>
      <?php
      // display all messages

      if (is_array($messages)):
        echo '<div class="info" style="width: auto; display: inline-block; margin-bottom: 20px">';
        foreach ($messages as $type => $msgs):
          foreach ($msgs as $message):
            echo ('<span class="' .  $type .'">' . $message . '</span>');
          endforeach;
        endforeach;
        echo '</div>';
      endif;

      ?>
      <strong>Current Password<em>*</em></strong>
      <?php echo form_password(array('name'=>'member_password','id'=>'member_password','maxlength'=>50,'size'=>50 ,'value'=>set_value('member_password'),'class'=>'input')) ; ?>

      <strong>New Password<em>*</em></strong>
      <?php echo form_password(array('name'=>'member_new_password','id'=>'member_new_password','maxlength'=>50,'size'=>50 ,'value'=>set_value('member_new_password'),'class'=>'input')); ?>

      <strong>Confirm New Password<em>*</em></strong>
      <?php echo form_password(array('name'=>'member_confirm_password','id'=>'member_confirm_password','maxlength'=>50,'size'=>50 ,'value'=>set_value('member_confirm_password'),'class'=>'input')); ?>

      <?php echo form_submit(array('name' => 'btnChangePasssword', 'id' => 'btnChangePasssword','class'=>'btn confirm','content' => 'Change Password'), 'Change Password'); ?>

      <?php
        echo form_hidden('action','submit');
        echo form_close();
      ?>
    </div>
  </div>
</div>
</body>
</html>
