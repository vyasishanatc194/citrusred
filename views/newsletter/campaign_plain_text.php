<script type="text/javascript" language="javascript">

// Function to show text email
function text_email(){
  $('.thumb').hide();
  $('.campaign_import_url').hide();
  $('.campaign_zip_file').hide();
  $('.paste_code').hide();
  $('.text_email').show();
  $('#ul_more').find('li').find('a').removeClass('highlight');
  $('#ul_headers').find('li').find('a').removeClass('highlight');
  $('.import_text_email').addClass('highlight');
  $('.right-template').css('text-align','left');
}
</script>
<!--[body]-->
<div id="body-dashborad">
  <div class="container">
    <h1>Plain-Text Email</h1>
    <div class="inner-container select-campaign">
      <div class="help">To start, enter the content of your email campaign.</div>
      <?php if(validation_errors()){
        echo '<div style="color:#FF0000;" class="info">'.validation_errors().'</div>';
      }?>
      <?php
      // display all messages
      if (is_array($messages)):
        echo '<div class="info">';
        foreach ($messages as $type => $msgs):
          foreach ($msgs as $message):
            echo ('<span class="' .  $type .'">' . $message . '</span>');
          endforeach;
        endforeach;
        echo '</div>';
      endif;
      ?>

        <div class="right-menu1 campaign form-fields">
          <ul  class="thumb" <?php if($campaign_data['campaign_template_option']!=3) { ?> style="display:none;" <?php } ?>></ul>


          <div   class="text_email">

              <?php
                if($campaign_data['is_autoresponder']){
                  echo form_open_multipart('newsletter/campaign_template_options/autoresponder/'.$campaign_data['campaign_id'].'/text_email', array('id' => 'form_campaign_text_email','name'=>'form_campaign_text_email'));
                }else{
                  echo form_open_multipart('newsletter/campaign_template_options/index/'.$campaign_data['campaign_id'].'/text_email', array('id' => 'form_campaign_text_email','name'=>'form_campaign_text_email'));
                }
              ?>
              <?php
                if($campaign_data['campaign_template_option']==5){
                  $html=$campaign_data['html'];
                }
              ?>
            <textarea name="campaign_text_email" id="campaign_text_email" class="paste_text_html_width" value="" cols="70"><?php echo $html; ?></textarea>
            <input type="hidden" name="campaign_id" value="<?php echo $campaign_data['campaign_id']; ?>" />
            <div id="campaign_email_action">
              <?php  echo form_submit(array('name'=>'text_email','value'=>'Next', 'class'=>'btn confirm')); ?>
              <?php
                if($campaign_data['is_autoresponder']){
                  echo form_button(array('name'=>'campaign_cancel','class'=>'btn cancel', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'newsletter/autoresponder/display'."'"));
                }else{
                    echo form_button(array('name'=>'campaign_cancel','class'=>'btn cancel', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'newsletter/campaign'."'"));
                }
              ?>
            </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--[/body]-->
<script type="text/javascript">
  $("#campaign_text_email").focus();
</script>
