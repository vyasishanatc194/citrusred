 <script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery.blockUI.js?v=6-20-13"></script>
 <script type="text/javascript">
  /**
    Update Autoresponder and signup form status: active,inactive
  */
  function changeClickTracking(status){
	var block_data='clicktracking_status='+status;
    jQuery.ajax({
      url: "<?php echo base_url() ?>dashboard_extra/update/",
      data:block_data,
      type:"POST",
      success: function(data) {
      if(data==1){
        $('#clicktracking_status').html("<i class='icon-check'></i>");
        $('#clicktracking_status').addClass('active'); 
		document.getElementById('clicktracking_status').onclick = function(){changeClickTracking(data);}		
      }else{
        $('#clicktracking_status').html("<i class='icon-check-empty'></i>");
        $('#clicktracking_status').removeClass('active');
		document.getElementById('clicktracking_status').onclick = function(){changeClickTracking(data);}		
      }
      }
    });
  }
  function changeStatus(fld_id,status){
	if(fld_id=='google_analytics_status'){
		if(status=="Inactive" || status==0){
			$('#div_ticked').show();
			$('#div_unticked').hide();
		}else{
			$('#div_ticked').hide();
			$('#div_unticked').show();
		}
	}
	if(status=="Active"){
      status=1;
    }
    var block_data=fld_id+'='+status;
    jQuery.ajax({
      url: "<?php echo base_url() ?>dashboard_extra/update/",
      data:block_data,
      type:"POST",
      success: function(data) {
      if(data==1){
        $('#'+fld_id).html("<i class='icon-check'></i>");
        $('#'+fld_id).addClass('active');
        document.getElementById(fld_id).onclick = function(){changeStatus(fld_id,data);}
      }else{
        $('#'+fld_id).html("<i class='icon-check-empty'></i>");
        $('#'+fld_id).removeClass('active');
        document.getElementById(fld_id).onclick = function(){changeStatus(fld_id,data);}
      }
      }
    });
  }

  jQuery("#api_status").live('click',function(event) {
  if($('#api_status').html() == 'Re-generate Key'){
  var msg='<h5>Confirm</h5><p>Are you sure you want to re-generate your API-keys?  Changing the key will cause all API requests for the previous key to fail.</p><div class="btn-group"><button class="btn confirm fast_confirm_proceed" onclick="generateAPI()">Yes</button><button class="btn cancel fast_confirm_cancel" onclick="$.fancybox.close()">No</button></div>';
  $.fancybox({'content' : "<div style=\"width:400px;\">"+msg+"</div>"});
  }else{
	generateAPI();
  }
  });
  function generateAPI(){
    jQuery.ajax({
      url: "<?php echo base_url() ?>dashboard_extra/generate_api/",
      type:"POST",
      success: function(data) {
        $('#div_api').html(data);
        $('#api_status').html('Re-generate Key');
      }
    });
	$.fancybox.close();
  }

  /**
    Update user selected language
  */
  $('.select_language').live('change',function(){
    $.blockUI({ message: '<h3 class="please-wait">Please wait...</h3>' });
    jQuery.ajax({
      url: "<?php echo base_url() ?>dashboard_extra/update_language",
      type:"POST",
      data:'language='+$(this).val(),
      success: function(data) {
        if(data=="updated"){
          var msg="<h5>Confirm</h5><p>Your language has been changed</p><div class='btn-group'><button class='btn confirm fast_confirm_cancel' onclick='$.fancybox.close()'>Ok</button></div>"
          $.fancybox({
            'content' : "<div style=\"width:400px;\">"+msg+"</div>"
          });
        }
        $.unblockUI();
      }
    });
  });
  $('a.boxclose').live('click',function(){
  var domain = $(this).parent().html();
  jQuery.ajax({
      url: "<?php echo base_url() ?>dashboard_extra/remove_domain",
      type:"POST",
      data:'strDomain='+ domain,
      success: function(data) {

    $('#domain_list').html(data);

      }
    });
  });
  function addDomain(){
    $el = $("#google_analytics_domain");
    var domain = $el.val();
    jQuery.ajax({
      url: "<?php echo base_url() ?>dashboard_extra/add_domain",
      type:"POST",
      data:'strDomain='+ domain,
      success: function(data) {
        $el.val("");
        $('#domain_list').html(data);
      }
    });
  }
  $("#google_analytics_domain").live("keydown",function(e) {
    if(e.keyCode == 13) {
      addDomain();
    }
  });
 </script>
 <!--[body]-->
  <div id="body-dashborad">
    <div class="container">
      <h1>Extras</h1>
        <!--second part -->
        <div class="body-portion">
		
        <div class="content-random">
          <div>
            <a class="<?php echo $google_analytics_class; ?> check-box" id="google_analytics_status" href="javascript:void(0);" onclick="changeStatus('google_analytics_status','<?php echo $google_analytics_status; ?>');"><i class='icon-check'></i><i class='icon-check-empty'></i></a>
          </div>
          <div>
            <h4>Google Analytics </h4>
			<div id="div_unticked" <?php if('Active'==$google_analytics_status)echo'style="display:none;"'; ?>>
		   <p>
              With Google Analytics integration, RedCappi will automatically add tracking codes in your email campaign links, so that you can track the clicks from your subscribers and find out exactly how many visits, conversions and sales came in from your email campaigns. To get the stats, you will need to enter the domain or domains you wish to track in your Google Analytics account, and we'll take care of the rest...
            </p>
			</div>
			<div id="div_ticked" <?php if('Active'!=$google_analytics_status)echo'style="display:none;"'; ?>>
            <p>
        			<strong>Note:</strong> You need to have Google Analytics account enabled to the domains for which you want to track. You can get additional information and sign up for a free account at: <a href="http://www.google.com/analytics" style="text-decoration:underline;" target="_blank">http://www.google.com/analytics</a>
            </p>
            <p>
              <strong style="display:block">Add Domain:</strong>
              <input type="text" name="google_analytics_domain" id="google_analytics_domain" class="clean" style="width: 300px" />
              <a href="javascript:addDomain();" class="btn add inline-block" style="position:relative;font-weight:700;top:-2px;right:-3px"><i class="icon-plus"></i>Add Domain</a>
            </p>
            <p id="domain_list"><?php echo $allDomains;?></p>
			</div>
          </div>
        </div>
      

        <div class="content-random">
          <div>
            <a id="language" href="javascript:void(0);" onclick="changeStatus('language','<?php echo $language; ?>');"><?php echo $language; ?></a>
          </div>
          <div>
            <ul class="status-one">
              <li>
                <select class="select_language">
                  <option value=sq <?php echo ($selected_user_language=="sq") ?"selected='selected'":'';?>>Albanian</option>
                  <option value=hy <?php echo ($selected_user_language=="hy") ?"selected='selected'":'';?>>Armenian</option>
                  <option value=ar <?php echo ($selected_user_language=="ar") ?"selected='selected'":'';?>>Arabic</option>
                  <option value=az <?php echo ($selected_user_language=="az") ?"selected='selected'":'';?>>Azerbaijani</option>
                  <option value=eu <?php echo ($selected_user_language=="eu") ?"selected='selected'":'';?>>Basque</option>
                  <option value=be <?php echo ($selected_user_language=="be") ?"selected='selected'":'';?>>Belarusian</option>
                  <option value=bg <?php echo ($selected_user_language=="bg") ?"selected='selected'":'';?>>Bulgarian</option>
                  <option value=ca <?php echo ($selected_user_language=="ca") ?"selected='selected'":'';?>>Catalan</option>
                  <option value=zh-CN <?php echo ($selected_user_language=="zh-CN") ?"selected='selected'":'';?>>Chinese (Simplified)</option>
                  <option value=zh-TW <?php echo ($selected_user_language=="zh-TW") ?"selected='selected'":'';?>>Chinese (Traditional)</option>
                  <option value=hr <?php echo ($selected_user_language=="hr") ?"selected='selected'":'';?>>Croatian</option>
                  <option value=cs <?php echo ($selected_user_language=="cs") ?"selected='selected'":'';?>>Czech</option>
                  <option value=da <?php echo ($selected_user_language=="da") ?"selected='selected'":'';?>>Danish</option>
                  <option value=nl <?php echo ($selected_user_language=="nl") ?"selected='selected'":'';?>>Dutch</option>
                  <option value=en <?php echo ($selected_user_language=="en") ?"selected='selected'":'';?>>English</option>
                  <option value=et <?php echo ($selected_user_language=="et") ?"selected='selected'":'';?>>Estonian</option>
                  <option value=tl <?php echo ($selected_user_language=="tl") ?"selected='selected'":'';?>>Filipino</option>
                  <option value=fi <?php echo ($selected_user_language=="fi") ?"selected='selected'":'';?>>Finnish</option>
                  <option value=fr <?php echo ($selected_user_language=="fr") ?"selected='selected'":'';?>>French</option>
                  <option value=gl <?php echo ($selected_user_language=="gl") ?"selected='selected'":'';?>>Galician</option>
                  <option value=ka <?php echo ($selected_user_language=="ka") ?"selected='selected'":'';?>>Georgian</option>
                  <option value=de <?php echo ($selected_user_language=="de") ?"selected='selected'":'';?>>German</option>
                  <option value=el <?php echo ($selected_user_language=="el") ?"selected='selected'":'';?>>Greek</option>
                  <option value=iw <?php echo ($selected_user_language=="iw") ?"selected='selected'":'';?>>Hebrew</option>
                  <option value=hi <?php echo ($selected_user_language=="hi") ?"selected='selected'":'';?>>Hindi</option>
                  <option value=hu <?php echo ($selected_user_language=="hu") ?"selected='selected'":'';?>>Hungarian</option>
                  <option value=is <?php echo ($selected_user_language=="is") ?"selected='selected'":'';?>>Icelandic</option>
                  <option value=id <?php echo ($selected_user_language=="id") ?"selected='selected'":'';?>>Indonesian</option>
                  <option value=ga <?php echo ($selected_user_language=="ga") ?"selected='selected'":'';?>>Irish</option>
                  <option value=it <?php echo ($selected_user_language=="it") ?"selected='selected'":'';?>>Italian</option>
                  <option value=ja <?php echo ($selected_user_language=="ja") ?"selected='selected'":'';?>>Japanese</option>
                  <option value=ko <?php echo ($selected_user_language=="ko") ?"selected='selected'":'';?>>Korean</option>
                  <option value=la <?php echo ($selected_user_language=="la") ?"selected='selected'":'';?>>Latin</option>
                  <option value=lv <?php echo ($selected_user_language=="lv") ?"selected='selected'":'';?>>Latvian</option>
                  <option value=lt <?php echo ($selected_user_language=="lt") ?"selected='selected'":'';?>>Lithuanian</option>
                  <option value=mt <?php echo ($selected_user_language=="mt") ?"selected='selected'":'';?>>Maltese</option>
                  <option value=mk <?php echo ($selected_user_language=="mk") ?"selected='selected'":'';?>>Macedonian</option>
                  <option value=no <?php echo ($selected_user_language=="no") ?"selected='selected'":'';?>>Norwegian</option>
                  <option value=fa <?php echo ($selected_user_language=="fa") ?"selected='selected'":'';?>>Persian</option>
                  <option value=pl <?php echo ($selected_user_language=="pl") ?"selected='selected'":'';?>>Polish</option>
                  <option value=pt <?php echo ($selected_user_language=="pt") ?"selected='selected'":'';?>>Portuguese</option>
                  <option value=ro <?php echo ($selected_user_language=="ro") ?"selected='selected'":'';?>>Romanian</option>
                  <option value=ru <?php echo ($selected_user_language=="ru") ?"selected='selected'":'';?>>Russian</option>
                  <option value=sr <?php echo ($selected_user_language=="sr") ?"selected='selected'":'';?>>Serbian</option>
                  <option value=sk <?php echo ($selected_user_language=="sk") ?"selected='selected'":'';?>>Slovak</option>
                  <option value=sl <?php echo ($selected_user_language=="sl") ?"selected='selected'":'';?>>Slovenian</option>
                  <option value=es <?php echo ($selected_user_language=="es") ?"selected='selected'":'';?>>Spanish</option>
                  <option value=sw <?php echo ($selected_user_language=="sw") ?"selected='selected'":'';?>>Swahili</option>
                  <option value=sv <?php echo ($selected_user_language=="sv") ?"selected='selected'":'';?>>Swedish</option>
                  <option value=th <?php echo ($selected_user_language=="th") ?"selected='selected'":'';?>>Thai</option>
                  <option value=tr <?php echo ($selected_user_language=="tr") ?"selected='selected'":'';?>>Turkish</option>
                  <option value=uk <?php echo ($selected_user_language=="uk") ?"selected='selected'":'';?>>Ukrainian</option>
                  <option value=ur <?php echo ($selected_user_language=="ur") ?"selected='selected'":'';?>>Urdu</option>
                  <option value=vi <?php echo ($selected_user_language=="vi") ?"selected='selected'":'';?>>Vietnamese</option>
                  <option value=cy <?php echo ($selected_user_language=="cy") ?"selected='selected'":'';?>>Welsh</option>
                  <option value=yi <?php echo ($selected_user_language=="yi") ?"selected='selected'":'';?>>Yiddish</option>
                </select>
              </li>
            </ul>
          </div>
          <div>
            <h4>Subscriber Language</h4>
            <p>
              RedCappi's footer content is set to "English" by default. If you are sending email campaigns in a different language, you will need to select the preferred language setting to match.
            </p>
          </div>
        </div>
        <div class="content-random">
          <h4>
            <a href="<?php echo  base_url().'contact';?>">Custom Services </a>
            <a href="<?php echo base_url().'contact';?>" class="btn add"><i class="icon-envelope"></i>Email Us</a>
          </h4>
          <div>
            <p>Don't see what you're looking for? We can custom design your email banners, logos, and much more. Contact us for specific quotes on any custom job.   </p>
          </div>
        </div>

		
		
        <div class="content-random">
          <div>
            <a class="<?php echo $clicktracking_class; ?> check-box" id="clicktracking_status" href="javascript:void(0);" onclick="changeClickTracking('<?php echo $clicktracking_status; ?>');"><i class='icon-check'></i><i class='icon-check-empty'></i></a>
          </div>
          <div>
            <h4>Manage Click Tracking</h4>			 
		   <p>
              Manage your click throughs (who clicked, how many times they clicked, etc.) by activating this feature. You will then be able to turn ON or OFF the click tracking capabilities per individual email campaign.
            </p>		
          </div>
        </div>
      
		
		
		
        <div class="content-random">
          <h4>
            RedCappi API
            <span class="<?php echo $api_class; ?>"><a id="api_status" href="javascript:void(0);" class="btn add"><?php echo $api_status; ?> Key</a></span>
          </h4>
          <div id="div_api">
            <?php if($api_status =='Generate'){?>
              <p>
				RedCappi's user-friendly and powerful API, allows you to easily integrate and sync your database with RedCappi to create and manage your email lists and contacts. Simply click the "Generate Key" and you're ready to integrate... For more help check our <a href='#'>API documentation.</a>
              </p>
            <?php }else{?>
              <p>
                <strong>Your API Keys:</strong><br/>
                Public Key: <?php echo $public_api_key;?><br/>
                Private Key: <?php echo $private_api_key;?><br/>
              </p>
            <?php }?>
          </div>
        </div>

        <!--/second part -->
    </div>
  </div>
