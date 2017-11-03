<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/jquery-1.5.1.min.js?v=6-20-13"></script>
<script type="text/javascript" src="<?php echo $this->config->item('webappassets');?>js/colorpicker.js?v=6-20-13"></script>
<link href="<?php echo $this->config->item('webappassets');?>css-front/colorpicker.css?v=6-20-13" rel="stylesheet" type="text/css" />
<script type="text/javascript">
	$(document).ready(function() {
		$('#outer_bg').ColorPicker({
			color: '#'+$('#outer_bg').val(),
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				//return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				//return false;
			},
			onSubmit: function (hsb, hex, rgb, el) {
				$('#outer_bg').css('backgroundColor', '#' + hex);
				$('#outer_bg').val(hex);
				$('#outer_bg').css('color','#'+hex);
				$(el).ColorPickerHide();

			}
		});
		$('#body_bg').ColorPicker({
			color: '#'+$('#body_bg').val(),
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
			},
			onSubmit: function (hsb, hex, rgb, el) {
				$('#body_bg').css('backgroundColor', '#' + hex);
				$('#body_bg').val(hex);
				$('#body_bg').css('color','#'+hex);
				$(el).ColorPickerHide();

			}
		});
		$('#footer_bg').ColorPicker({
			color: '#'+$('#footer_bg').val(),
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
			},
			onSubmit: function (hsb, hex, rgb, el) {
				$('#footer_bg').css('backgroundColor', '#' + hex);
				$('#footer_bg').val(hex);
				$('#footer_bg').css('color','#'+hex);
				$(el).ColorPickerHide();

			}
		});
		$('#border_color').ColorPicker({
			color: '#'+$('#border_color').val(),
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
			},
			onSubmit: function (hsb, hex, rgb, el) {
				$('#border_color').css('backgroundColor', '#' + hex);
				$('#border_color').val(hex);
				$('#border_color').css('color','#'+hex);
				$(el).ColorPickerHide();

			}
		});
		$('#footer_font_color').ColorPicker({
			color: '#'+$('#footer_font_color').val(),
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
			},
			onSubmit: function (hsb, hex, rgb, el) {
				$('#footer_font_color').css('backgroundColor', '#' + hex);
				$('#footer_font_color').val(hex);
				$('#footer_font_color').css('color','#'+hex);
				$(el).ColorPickerHide();

			}
		});
	});
	$('#btnEdit').live('click',function(){
		$('#frmCategoryEdit').submit();
	});
</script>
<div class="tblheading">Edit Template Color</div>
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


echo form_open('webmaster/template_color/color_edit/'.$color['id'], array('id' => 'frmCategoryEdit'));
echo '<table class="tbl_forms"><tr><td >';
echo '<div style="color:#FF0000;">'.validation_errors().'</div>';
echo "</td></td>";
echo "<tr><td>";
echo "Color Template Title</td><td>";
echo form_input(array('name'=>'theme_name','id'=>'theme_name','maxlength'=>50 ,'value'=>$color['theme_name'])) ."</td></tr>";
echo "<tr><td>";
echo "Outer BG Color</td><td>";
echo '<input id="outer_bg" name="outer_bg" class="color" value="'.substr(trim($color['outer_bg']),1).'" style="background-color:'. $color['outer_bg'].';color:'. $color['outer_bg'].'" /></td></tr>';
echo "<tr><td>";
echo "Body BG Color</td><td>";
echo '<input id="body_bg" name="body_bg" class="color" value="'.substr(trim($color['body_bg']),1).'" style="background-color:'. $color['body_bg'].';color:'. $color['body_bg'].'" /></td></tr>';
echo "<tr><td>";
echo "Footer BG Color</td><td>";
echo '<input id="footer_bg" name="footer_bg" class="color" value="'.substr(trim($color['footer_bg']),1).'" style="background-color:'. $color['footer_bg'].';color:'. $color['footer_bg'].'" /></td></tr>';
echo "<tr><td>";
echo "Border Color</td><td>";
echo '<input id="border_color" name="border_color" class="color" value="'.substr(trim($color['border_color']),1).'" style="background-color:'. $color['border_color'].';color:'. $color['border_color'].'" /></td></tr>';
echo "<tr><td>";
echo "Footer Font Color</td><td>";
echo '<input id="footer_font_color" name="footer_font_color" class="color" value="'.substr(trim($color['footer_font_color']),1).'" style="background-color:'. $color['footer_font_color'].';color:'. $color['footer_font_color'].'" /></td></tr>';
echo "<tr><td>Status</td><td>";
echo form_dropdown('is_active',array('1'=>'Active','0'=>'Inactive'),$header['is_active']);
echo "</td></tr>";

echo '<tr><td><br>';


echo form_submit(array('name' => 'btnEdit', 'id' => 'btnEdit','class'=>'inputbuttons','content' => 'edit'), 'Submit');
echo '&nbsp;';
echo form_button(array('name'=>'btnCancel','class'=>'inputbuttons', 'value'=>'Cancel','content'=>'Cancel','onclick'=>"window.location.href='".base_url().'webmaster/template_color/template_color_list'."'"));
echo "</td></tr>";
echo form_hidden('action','submit');
echo form_hidden('id',$color['id']);
echo form_close();
echo "</table>";
?>
</div>
