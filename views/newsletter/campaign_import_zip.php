<script type="text/javascript" language="javascript">
// Function to show import from zip file
function zipFile(){
  $('.thumb').hide();
  $('.campaign_import_url').hide();
  $('.paste_code').hide();
  $('.text_email').hide();
  $('.campaign_zip_file').show();
  $('#ul_more').find('li').find('a').removeClass('highlight');
  $('#ul_headers').find('li').find('a').removeClass('highlight');
  $('.import_zip').addClass('highlight');
  $('.right-template').css('text-align','left');
}
</script>
<!--[body]-->
<div id="body-dashborad">
  <div class="container">
    <h1>Import from Zip File</h1>
    <div class="inner-container select-campaign">
      <div class="help">To start, upload the Zip File containing the contents of your email campaign.</div>
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

        <div class="right-menu campaign form-fields imort-zip">
          <ul  class="thumb" <?php if($campaign_data['campaign_template_option']!=3) { ?> style="display:none;" <?php } ?>></ul>

          <div  class="campaign_zip_file">

            <?php
              if($campaign_data['is_autoresponder']){
                echo form_open_multipart('newsletter/campaign_template_options/autoresponder/'.$campaign_data['campaign_id'].'/import_zip', array('id' => 'form_campaign_zip','name'=>'form_campaign_zip'));
              }else{
                echo form_open_multipart('newsletter/campaign_template_options/index/'.$campaign_data['campaign_id'].'/import_zip', array('id' => 'form_campaign_zip','name'=>'form_campaign_zip'));
              }
            ?>
            <?php echo form_upload(array('name'=>'campaign_import_zip_file','id'=>'campaign_import_zip_file','value'=>set_value('campaign_import_zip_file') ));?>
			 
		   <button class="btn confirm">Select Zip File to Upload</button>
            <input type="hidden" name="campaign_import_zip_file_submit" value="Import" />
            <input type="hidden" name="campaign_id" value="<?php echo $campaign_data['campaign_id']; ?>" />

            <script type="text/javascript">
              $("#campaign_import_zip_file").bind("change",function() {
                    $("#form_campaign_zip").submit();
              });
            </script>
            </form>


			<p>
			<ul style="list-style:disc;font-size:14px; margin:50px 20px;">How do I format my Zip File?
			<li style="font-size:12px;margin:10px 10px 10px 20px;">Include one index.html file.</li>
			<li style="font-size:12px; margin:10px 10px 10px 20px;">All your web friendly images (PNG, GIF, JPG, JPEG).</li>
			<li style="font-size:12px; margin:10px 10px 10px 20px;">Your CSS file(s) or inline CSS in your HTML.</li>
			<li style="font-size:12px; margin:10px 10px 10px 20px;">Make sure your file size less than 2MB.</li>
			</p>
</div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--[/body]-->
