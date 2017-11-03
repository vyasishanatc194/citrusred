<script type="text/javascript" language="javascript">
var html_code="";
$(document).ready(function(){
<?php if(!$campaign_data['user_info']){ ?>
  //setTimeout("$.fancybox($('#user_account_option').html(),{ 'autoDimensions':false,'height':'500','width':'650','centerOnScroll':true,'modal':true});", 1000);
<?php } ?>
  $(".templates").click(function(){
    var thisSkin = $('img',this).attr("id");
    $('input[name=red_theme_name]').val($('.skins',this).attr('id'));
    $('#red_template_name').val(thisSkin);
    $('input[id=theme_'+$('.skins',this).attr('id')+']').attr('checked', true);
  });
});
/**
  Display blank on click of campaign_title input box
*/
jQuery("#campaign_title").live('click',function(){
  if(jQuery(this).val().toLowerCase()=="unnamed"){
    $("#current_container_id").val(jQuery(this).val());
    jQuery(this).val("");
  }
}).live('blur',function(){
  if(jQuery(this).val()==""){
    jQuery(this).val("Unnamed");
  }
  $("#current_container_id").val("");
});
//Fucntion to submit a form
function frmSubmit(frmfld1,val){
  frmfld1.value=val;
  document.form_campaign_theme.submit();
}
//Function to change a template
function changeTemplate(theme_id){
  $('.right-template').css('text-align','center');
  $('.campaign_import_url').hide();
  $('.campaign_zip_file').hide();
  $('.paste_code').hide();
  $('.text_email').hide();
  $('.thumb').show();
  if(!theme_id){
    theme_id="";
  }
  var block_data="";
  var url="";
  <?php if($campaign_data['is_autoresponder']){ ?>
    url="<?php echo base_url() ?>newsletter/autoresponder/get_template_data_for_theme/"+theme_id;
  <?php }else{ ?>
    url="<?php echo base_url() ?>newsletter/campaign/get_template_data_for_theme/"+theme_id;
  <?php } ?>
  jQuery.ajax({
    url: url,
    type:"POST",
    data:block_data,
    success: function(data) {
      $('.thumb').html(data);
      $('#ul_headers').find('li').find('a').removeClass('highlight');
      $('#ul_more').find('li').find('a').removeClass('highlight');
      $('.li_'+theme_id).find('a').addClass('highlight');
    }
  });
}

// Function to save a template
function saveTemplate(template_name,template_id,theme_id){
  document.form_campaign_theme.red_theme_name.value=theme_id;
  document.form_campaign_theme.red_template_name.value=template_id;
  document.form_campaign_theme.submit();
}
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
function submitPasteHtmlFrm(){
  document.form_campaign_paste_code.action.value='paste_code';
  document.form_campaign_paste_code.submit();
}
/***********Function to add user info*******************/
function save_user_info(){
  var block_data;
  block_data='company='+escape($('#fancybox-wrap').find('#company_name').val())+'&address_line_1='+escape($('#fancybox-wrap').find('#address').val())+'&city='+escape($('#fancybox-wrap').find('#city').val())+'&state='+escape($('#fancybox-wrap').find('#state').val())+'&zipcode='+escape($('#fancybox-wrap').find('#zip').val())+'&country='+escape($('#fancybox-wrap').find('#country').val());
  jQuery.ajax({
    url: "<?php echo base_url() ?>account/user_info",
    type:"POST",
    data:block_data,
    success: function(data) {
      var data_arr=data.split(':');
      if(data_arr[0]=="error"){
        $('#fancybox-wrap').find('.msg').html(data_arr[1]);
      }else{
        $('.company_name').html($('#fancybox-wrap').find("#company_name").val());
        $('.address').html($('#fancybox-wrap').find("#address").val());
        $('.city').html(" | "+$('#fancybox-wrap').find("#city").val());
        $('.state').html(", "+$('#fancybox-wrap').find("#state").val());
        $('.zip').html($('#fancybox-wrap').find("#zip").val());
        var country=$('#fancybox-wrap').find("#country :selected").text();
        if(country=="United States"){
          country="USA";
        }
        $('.country').html(" | "+country);
        $.fancybox.close();
      }
    }
  });
}
// call functions for change Templates
changeTemplate();

$(function(){
  var followScroll = function() {
    if($(window).height() > $(".left-menu.campaign").height()) {
      var $leftMenu = $(".left-menu.campaign"),
          top = $leftMenu.offset().top - parseFloat($leftMenu.css('marginTop').replace(/auto/,0));

      $(window).scroll(function() {
        var y = $(this).scrollTop();

        if (y >= top) {
          $leftMenu.addClass('fixed');
        } else {
          $leftMenu.removeClass('fixed');
        }
      });
    } else {
      $(window).unbind("scroll");
      $(".left-menu.campaign").removeClass("fixed").removeAttr("style");
    }
  };

  followScroll();

  $(window).resize(function() {
    followScroll();
  });
});
<?php if($ctyp=='import_url'){?>
$(function(){
importUrl();
});
<?php } ?>
</script>
<!--[body]-->
<div id="body-dashborad">
  <div class="container">
    <h1>
      <a href="javascript:void(0);" onclick="frmSubmit(document.form_campaign_theme.red_theme_name,-1);" class="btn add">Start with a Blank Banner</a>
      Campaign Type
    </h1>
    <div class="inner-container select-campaign">
      <div class="help">To begin, select a RedCappi designed banner or use your own code</div>
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
          <div>
            <a href="javascript:void(0);" onclick="changeTemplate(); javascript:$('#ul_headers').slideToggle();" class="select_banners">
              <i class="icon-chevron-down"></i>
              Categories
            </a>
            <ul style="display:block;" id="ul_headers">
             <?php
              //Fetch campaings from campaigns array
              if(count($campaign_data['theme_data'])){
                $i=1;
                foreach($campaign_data['theme_data'] as $theme_info){
                  $theme[$theme_info['red_theme_id']]=$theme_info['red_theme_name'];
                  if($theme_info['red_theme_id']!=-1){
                    echo "<div class=\"templates\" style=\"border:none;\" ><span class=\"skins\" id=\"".$theme_info['red_theme_id']."\">";
                  }
                  //
                  foreach($campaign_data['template_data'][$theme_info['red_theme_id']] as $temlate_info){
                    $template_img_path = $this->config->item('webappassets').'email_templates/'.$temlate_info['template_name'].'/'.$temlate_info['screenshot'];
                  }
                  //template screenshot path
                  $data = array(
                    'name'        => 'red_theme_name',
                    'id'          => 'theme_'.$theme_info['red_theme_id'],
                    'value'       => $theme_info['red_theme_id'],
                    'checked'       => True
                  );
                  if($theme_info['red_theme_id']!=-1){
                    //echo '<div align="center">'.form_radio($data)."</div>";
                    echo "</div>";
                    echo '<input type="hidden" name="theme_'.$theme_info['red_theme_id'].'" id="theme_'.$theme_info['red_theme_id'].'" value="'.$theme_info['red_theme_id'].'" />';
                    echo '<li class="li_'.$theme_info['red_theme_id'].'"><a href="javascript:void(0);" onclick="changeTemplate('.$theme_info['red_theme_id'].')" >'.$theme_info['red_theme_name'].'</a></li>';
                  }
                }
              }?>
            </ul>
          </div>
          <div>
            <a href="javascript:void(0);" onclick="javascript:$('#ul_more').slideToggle();" class="select_banners">
              <i class="icon-chevron-down"></i>
              Use Your Own Code
            </a>
            <ul id="ul_more">
              <li><a href="javascript:void(0);" onclick='importUrl();' class="import_url">Import From URL</a></li>
              <li><a href="#body-dashborad" onclick='zipFile();' class="import_zip">Import From ZIP file</a></li>
              <li><a href="#body-dashborad" onclick="paste_code();" class="import_paste_code">Paste in HTML code</a></li>
              <li><a href="#body-dashborad" onclick="text_email();" class="import_text_email">Plain-Text Email</a></li>
            </ul>
          </div>
        </div>
        <div class="right-menu campaign form-fields">
          <ul  class="thumb" <?php if($campaign_data['campaign_template_option']!=3) { ?> style="display:none;" <?php } ?>></ul>
          <div <?php if($campaign_data['campaign_template_option']!=1) { ?> style="display:none;" <?php } ?> class="campaign_import_url">
            <h2>Import from URL</h2>
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
          <div <?php if($campaign_data['campaign_template_option']!=2) { ?> style="display:none;" <?php } ?> class="campaign_zip_file">
            <h2>Import from zip file</h2>
            <?php
              if($campaign_data['is_autoresponder']){
                echo form_open_multipart('newsletter/campaign_template_options/autoresponder/'.$campaign_data['campaign_id'].'/import_zip', array('id' => 'form_campaign_zip','name'=>'form_campaign_zip'));
              }else{
                echo form_open_multipart('newsletter/campaign_template_options/index/'.$campaign_data['campaign_id'].'/import_zip', array('id' => 'form_campaign_zip','name'=>'form_campaign_zip'));
              }
            ?>
            <?php echo form_upload(array('name'=>'campaign_import_zip_file','id'=>'campaign_import_zip_file','value'=>set_value('campaign_import_zip_file') ));?>
			<input type="hidden" name="campaign_import_zip_file_submit" value="Import" />
            <input type="hidden" name="campaign_id" value="<?php echo $campaign_data['campaign_id']; ?>" />
            <?php  //echo form_submit(array('name'=>'btnZipImport','value'=>'Select Zip File to Upload', 'class'=>'btn confirm')); ?>
			<button class="btn confirm">Select Zip File to Upload</button>
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
          <div <?php if($campaign_data['campaign_template_option']!=4) {  ?>  style="display:none;" <?php } ?> class="paste_code">
            <h2>Paste in HTML code</h2><br/>
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
            <textarea name="paste_code" id="paste_code" value="" cols="70" style="white-space: pre-wrap;"><?php echo $html; ?></textarea>
            <input type="hidden" name="paste_html"  id="paste_html" value="submit" />
            <input type="hidden" name="campaign_id" value="<?php echo $campaign_data['campaign_id']; ?>" />
            <div><input type="checkbox" name="automatic_css-inliner" id="automatic_css-inliner" value="1" checked /> Inline CSS automatically</div><br /><br />
            <?php  echo form_submit(array('name'=>'campaign_paste_code','onclick'=>'submitPasteHtmlFrm();','value'=>'Submit','class'=>'btn confirm')); ?>
            <?php
              if(!$campaign_data['is_autoresponder']){
                echo form_button(array('name'=>'campaign_cancel','class'=>'btn confirm', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'newsletter/campaign'."'"));
              }else{
                echo form_button(array('name'=>'campaign_cancel','class'=>'btn confirm', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'newsletter/autoresponder/display'."'"));
              }
            ?>
            </form>
          </div>
          <div <?php if($campaign_data['campaign_template_option']!=5) { ?> style="display:none;" <?php } ?> class="text_email">
            <h2>Plain-Text Email</h2><br/>
              <?php
                if($campaign_data['is_autoresponder']){
                  echo form_open_multipart('newsletter/campaign_template_options/autoresponder/'.$campaign_data['campaign_id'], array('id' => 'form_campaign_text_email','name'=>'form_campaign_text_email'));
                }else{
                  echo form_open_multipart('newsletter/campaign_template_options/index/'.$campaign_data['campaign_id'], array('id' => 'form_campaign_text_email','name'=>'form_campaign_text_email'));
                }
              ?>
              <?php
                if($campaign_data['campaign_template_option']==5){
                  $html=$campaign_data['html'];
                }
              ?>
            <textarea name="campaign_text_email" id="campaign_text_email" value="" style="height:400px;resize:vertical;" cols="70"><?php echo $html; ?></textarea>
            <input type="hidden" name="campaign_id" value="<?php echo $campaign_data['campaign_id']; ?>" />
            <?php  echo form_submit(array('name'=>'text_email','value'=>'Submit','onclick'=>'document.form_campaign_text_email.action.value=\'text_email\'','class'=>'btn confirm')); ?>
            <?php
              if($campaign_data['is_autoresponder']){
                echo form_button(array('name'=>'campaign_cancel','class'=>'btn confirm', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'newsletter/autoresponder/display'."'"));
              }else{
                  echo form_button(array('name'=>'campaign_cancel','class'=>'btn confirm', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'newsletter/campaign'."'"));
              }
            ?>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--[/body]-->
