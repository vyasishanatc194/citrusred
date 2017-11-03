<?php include_once("seo_header.php");?>
<table width="100%" border="0" cellspacing="0" cellpadding="0"> 
        <tr> 
          
          <td id="rightside">
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
 <div style="text-align: right;"><a href="<?php echo base_url() ?>seo/create_seo"><strong>Submit Seo </strong></a></div>
<div class="tblheading">Manage Seo <?php //Display paging links
echo $paging_links ?> </div>

<table class="tbl_listing" width="100%"> 
	<thead> 
		<tr> 
			<th width="5%">
			ID
			</th>
			<th width="15%">
				 Page
			</th>
			<th width="15%">
				 Title
			</th>
			<th width="20%">
				Keyword
			</th>
			<th width="20%">
				Description
			</th>
			<th width="15%">
				h1
			</th>
			<th width="10%">
				Edit/Delete
			</th>
		</tr> 
	</thead> 
	<?php 
//List all users
if(count($seo_array)) {
foreach($seo_array as $seo){
?>
<tr>
	<td width="5%">
		<?php echo $seo['id'];  ?>
	</td>
	<td width="15%">
		<?php echo $seo['page'];  ?>
	</td>
	<td width="15%">
		<?php echo $seo['title'];  ?>
	</td>
	<td width="20%">
		<?php echo $seo['keyword'];  ?>
	</td>
	<td width="20%">
		<?php echo $seo['description'];  ?>
	</td>
	<td width="20%">
		<?php echo $seo['h1'];  ?>
	</td>
	<td width="10%">
		<a href="<?php echo  site_url('seo/seo_edit/'.$seo['id']);?>" >edit</a> /
		<a onclick="return confirm('Are you sure to delete page')" href="<?php echo  site_url('seo/seo_delete/'.$seo['id']);?>" >delete</a>
	</td>
</tr>
<?php } } else { ?>
<tr><td colspan="6" align="center">No Record Found</td></tr>
<?php } ?>
</table> </td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td id="footer"><table width="100%" >
        <tr>
          <td class="txt_copyright"> &copy; 2011 . All rights reserved.</td>
          <td>&nbsp;</td>
        </tr>
      </table></td>
  </tr>
</table>
</body>
</html>