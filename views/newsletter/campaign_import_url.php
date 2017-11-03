<script type="text/javascript" language="javascript">
// Function to show  import  URL
function importUrl(){
  $('.thumb').hide();
  $('.campaign_zip_file').hide();
  $('.paste_code').hide();
  $('.text_email').hide();
  $('.campaign_import_url').show();
  $('#ul_more').find('li').find('a').removeClass('highlight');
  $('#ul_headers').find('li').find('a').removeClass('highlight');
  $('.import_url').addClass('highlight');
  $('.right-template').css('text-align','left');
}
</script>
<!--[body]-->
<div id="body-dashborad">
  <div class="container">
    <h1>Import from URL</h1>
    <div class="inner-container select-campaign">
      <div class="help">To start, enter the link containing the contents of your email campaign.</div>
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
        <div class="left-menu campaign">
          <div>
            <?php
             //Hidden form for start from scratch
            if($campaign_data['is_autoresponder']){
              echo form_open_multipart('newsletter/autoresponder/theme', array('id' => 'form_campaign_theme','name'=>'form_campaign_theme'));
            }else{
              echo form_open_multipart('newsletter/campaign/theme', array('id' => 'form_campaign_theme','name'=>'form_campaign_theme'));
            }?>
            <input type="hidden" name="theme_-1" id="theme_-1" value="-1" />
            <?php
              echo form_hidden('action','save');
              echo form_hidden('campaign_id',$campaign_data['campaign_id']);
              echo "<input type='hidden' name='red_template_name' id='red_template_name' value='-1' />";
              echo "<input type='hidden' name='red_theme_name' id='red_theme_name' value='-1' />";
              echo form_close();
            ?>
          </div>


        </div>
        <div class="right-menu campaign form-fields import-url">
          <ul  class="thumb" <?php if($campaign_data['campaign_template_option']!=3) { ?> style="display:none;" <?php } ?>></ul>
          <div  class="campaign_import_url">
            <?php
              if($campaign_data['is_autoresponder']){
                echo form_open_multipart('newsletter/campaign_template_options/autoresponder/'.$campaign_data['campaign_id'], array('id' => 'form_campaign_import','name'=>'form_campaign_import'));
              }else{
                echo form_open_multipart('newsletter/campaign_template_options/index/'.$campaign_data['campaign_id'], array('id' => 'form_campaign_import','name'=>'form_campaign_import'));
              }
              echo form_input(array('name'=>'campaign_import_url','id'=>'campaign_import_url','size'=>60,'maxlength'=>250,'value'=>$campaign_data['import_campaign_url'] ));
            ?>
            <input type="hidden" name="campaign_id" value="<?php echo $campaign_data['campaign_id']; ?>" />
            <?php echo form_submit(array('name'=>'campaign_import_url_submit','value'=>'Import','onclick'=>'document.form_campaign_import.action.value=\'import_url\'','class'=>'btn confirm'))."<p>(eg. http://www.domain.com/page.html)</p>"; ?>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $("#campaign_import_url").focus();
</script>
<!--[/body]-->
