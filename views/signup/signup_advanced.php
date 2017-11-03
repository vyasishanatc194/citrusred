<div class="signup-tbl custom_url content-random">
            <h4>Advanced</h4>
            <div class="signup-tbl">
              <h3 class="signup-tbl-title first">Custom Confirmation Message</h3>

              <div class="signup-tbl-inline">
                <label class="label">From Name</label>
                <input type="text" size="40"  id="from_name" maxlength="100" value="<?php echo $signup_from['form'][0]['from_name'];?>" name="from_name">
              </div>

              <div class="signup-tbl-inline">
                <!--label class="label">From Email</label>
                <input type="text" id="from_email" maxlength="100" value="" name="from_email"-->
                <label class="label">From Email:
                  <span class="autotresponder_list_div" style="margin-left:5px;"><a href="javascript:void(0);" id="btn_add_other_eml" class="edit-interval">Add New</a></span>
                  <span><a href="javascript:void(0);" onclick="javascript: updateFromEmailDpd();" id="btn_refresh" style="margin-left:5px;"><img src="<?php echo $this->config->item('webappassets');?>images/reload2.png" height="14" alt="Refresh" align="absmiddle" /></a></span>
                </label>
                <select name="from_email" id="from_email"  class="select_language ui-widget ui-state-default ui-corner-all">
                  <?php
                    foreach($signup_from['email_id'] as $fromEml){
                      if($signup_from['form'][0]['from_email'] == $fromEml)
                      echo "<option value='$fromEml' selected>{$fromEml}</option>";
                      else
                      echo "<option value='$fromEml'>{$fromEml}</option>";
                    }
                  ?>
                </select>
              </div>

              <div class="signup-tbl-inline block">
                <label class="label">Subject</label>
                <input type="text" id="subject" maxlength="100" value="<?php echo $signup_from['form'][0]['subject'];?>" name="subject">
              </div>

              <div class="signup-tbl-inline block">

                <label class="label">Confirmation Email Message</label>
                <div class="signup-tbl-confirm-msg">
                  <textarea class="confirmation_emai_message" name="confirmation_emai_message" id="confirmation_emai_message" ><?php echo $signup_from['form'][0]['confirmation_emai_message'];?></textarea>
                  <?php echo base_url();?>{confirmation url}
                </div>

              </div>

              <div class="clear-both"></div>

              <h3 class="signup-tbl-title">Landing Page</h3>
              <div class="signup-tbl-inline">
                <label class="label">Signup "Thank You" Page</label>
                <input type="text" size="40" id="singup_thank_you_message_url" value="<?php echo $signup_from['form'][0]['singup_thank_you_message_url'];?>" name="singup_thank_you_message_url">
              </div>

              <div class="signup-tbl-inline">
                <label class="label">Confirmation Landing Page</label>
                <input type="text" size="40"  id="confirmation_thanks_you_message_url" value="<?php echo $signup_from['form'][0]['confirmation_thanks_you_message_url'];?>" name="confirmation_thanks_you_message_url">
              </div>

              <div class="clear-both"></div>

              <h3 class="signup-tbl-title">Internationalization</h3>
              <div class="signup-tbl-inline block">
                <label class="label">Language</label>
                <select name="form_language" id="form_language" class="select_language ui-widget ui-state-default ui-corner-all" onchange="updateLanguage(this.value)">
                  <?php
          $selLang = (trim($signup_from['form'][0]['form_language']) != '')?$signup_from['form'][0]['form_language']:'en';
                    foreach($signup_froms_language as $c => $lang){
                      if($c == $selLang)
                      echo "<option value='$c' selected>$lang</option>";
                      else
                      echo "<option value='$c'>$lang</option>";
                    }
                  ?>
                </select>
              </div>
            </div>

            <div class="clear-both"></div>

            <input type="hidden" name="custom_frm_action" id="custom_frm_action" value="submit" />

            <div style="margin: 0 10px 10px">
              <button id="save-advanced-section" class="btn confirm inline-block"  type="button" name="listing_submit">Save</button>
              <button id="cancel-advanced-section" class="btn cancel inline-block" type="button" name="listing_submit">Cancel</button>
            </div>

            <div class="signupform_code_td"  style="background-color:<?php echo $signup_froms['form_background_color'];?>; border: 0;background-image:url(<?php echo $signup_from['form'][0]['background_background_image']; ?>);background-repeat:<?php echo ($signup_from['form'][0]['background_background_tile_image'])?'repeat':'no-repeat';?>"></div>

            <div class="signupform_code_td"  style="background-color:<?php echo $signup_froms['form_background_color'];?>; border: 0;background-image:url(<?php echo $signup_from['form'][0]['background_background_image']; ?>);background-repeat:<?php echo ($signup_from['form'][0]['background_background_tile_image'])?'repeat':'no-repeat';?>"></div>

          </div>