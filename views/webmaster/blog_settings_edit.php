<div class="tblheading">Edit Listing Category</div>
<div id="messages" style="color:#FF0000;">
<?php
// display all messages

if (is_array($messages)):
    foreach ($messages as $type => $msgs):
        foreach ($msgs as $message):
            echo ('<span class="' .  $type .'">' . $message . '</span>');
        endforeach;
    endforeach;
endif;
?>
</div>  
<?php 
echo form_open('webmaster/blog_settings/blog_settings_edit/', array('id' => 'frmBlogSetting'));
echo '<table class="tbl_forms"><tr><td >';

echo '<div style="color:#FF0000;">'.validation_errors().'</div>'; 
echo "</td></tr>";
echo "<tr><td>";
echo "Gallery Type</td><td>"; 
echo form_dropdown('blog_gallery_type',array('1'=>'Lightbox-grouped(next-previous)','2'=>'Gallery with Cycle Plugin'),BLOG_GALLERY_TYPE);
echo "</td></tr>";

echo "<tr><td>Front Page Post Display</td><td>"; 
echo form_input(array('name'=>'blog_front_page_post_display','id'=>'blog_front_page_post_display','maxlength'=>25 ,'value'=>BLOG_FRONT_PAGE_POST_DISPLAY)) ."</td></tr>";

echo "<tr><td>Can Post Image</td><td>"; 
echo form_dropdown('blog_image_can_post',array('1'=>'Yes','0'=>'No'),BLOG_IMAGE_CAN_POST);
echo "</td></tr>";

echo "<tr><td>Can Visitor Post Comments</td><td>"; 
echo form_dropdown('blog_visitors_can_comment_post',array('1'=>'Yes','0'=>'No'),BLOG_VISITORS_CAN_COMMENT_POST);
echo "</td></tr>";

echo "<tr><td>Display Post As Summary</td><td>"; 
echo form_dropdown('blog_summary_post',array('1'=>'Yes','0'=>'No'),BLOG_SUMMARY_POST);
echo "</td></tr>";

echo "<tr><td>Image Dimesions</td><td>"; 
echo form_input(array('name'=>'blog_image_width','id'=>'blog_image_width','maxlength'=>5,'value'=>BLOG_IMAGE_WIDTH,'style'=>'width:100px;')) ."X".form_input(array('name'=>'blog_image_height','id'=>'blog_image_height','maxlength'=>5 ,'value'=>BLOG_IMAGE_HEIGHT,'style'=>'width:100px;'))."</td></tr>";
echo '<tr><td><br>';


echo form_submit(array('name' => 'btnEdit', 'id' => 'btnEdit','class'=>'inputbuttons','content' => 'edit'), 'Submit');
echo '&nbsp;';
echo form_button(array('name'=>'btnCancel','class'=>'inputbuttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'index.php/webmaster/blog_listing_category/category_list'."'"));
echo "</td></tr>";
echo form_hidden('action','submit');

echo form_close();
echo "</table>";
?>
</div>