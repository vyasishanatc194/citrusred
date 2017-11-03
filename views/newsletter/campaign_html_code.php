<script type="text/javascript" language="javascript">
// Function to show paste in code
function paste_code(){
  $('.thumb').hide();
  $('.campaign_import_url').hide();
  $('.campaign_zip_file').hide();
  $('.text_email').hide();
  $('.paste_code').show();
  $('#ul_more').find('li').find('a').removeClass('highlight');
  $('#ul_headers').find('li').find('a').removeClass('highlight');
  $('.import_paste_code').addClass('highlight');
  $('.right-template').css('text-align','left');
}
function submitPasteHtmlFrm(){
  document.form_campaign_paste_code.action.value='paste_code';
  document.form_campaign_paste_code.submit();
}
</script>
<!--[body]-->
<div id="body-dashborad">
  <div class="container">
    <h1>Paste in HTML code</h1>
    <div class="inner-container select-campaign">
      <div class="help">To start, paste the HTML code of your email campaign into the editor.</div>
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

          <div class="paste_code">
            <br/>
            <?php
              if($campaign_data['is_autoresponder']){
                echo form_open_multipart('newsletter/campaign_template_options/autoresponder/'.$campaign_data['campaign_id'], array('id' => 'form_campaign_paste_code','name'=>'form_campaign_paste_code','onsubmit'=>"return false;"));
              }else{
                echo form_open_multipart('newsletter/campaign_template_options/index/'.$campaign_data['campaign_id'], array('id' => 'form_campaign_paste_code','name'=>'form_campaign_paste_code','accept-charset'=>'utf-8', 'onsubmit'=>"return false;"));
              }
            ?>
              <?php
                if($campaign_data['campaign_template_option']==4){
                  $html=$campaign_data['html'];
                }
              ?>
              <textarea name="paste_code" id="paste_code" class="paste_text_html_width" style="white-space: pre-wrap;" value="" cols="70"><?php echo $html; ?></textarea>
              <input type="hidden" name="paste_html"  id="paste_html" value="submit" />
              <input type="hidden" name="campaign_id" value="<?php echo $campaign_data['campaign_id']; ?>" />
              <div id="campaign_email_action">
                <?php  echo form_submit(array('name'=>'campaign_paste_code','onclick'=>'submitPasteHtmlFrm();','value'=>'Next','class'=>'btn confirm')); ?>
                <?php
                  if(!$campaign_data['is_autoresponder']){
                    echo form_button(array('name'=>'campaign_cancel','class'=>'btn cancel', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'newsletter/campaign'."'"));
                  }else{
                    echo form_button(array('name'=>'campaign_cancel','class'=>'btn cancel', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'newsletter/autoresponder/display'."'"));
                  }
                ?>
                <div style="float:left;font-size: 15px;padding: 7px 0 0 10px;"><input type="checkbox" name="automatic_css-inliner" id="automatic_css-inliner" value="1" checked /> Inline CSS automatically</div>
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
  $("#paste_code").focus();
</script>
