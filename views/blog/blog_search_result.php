<?php echo link_tag('webappassets/css/support_style.css?v=6-20-13'); ?>
<script type="text/javascript">
	$(".pagination a").live('click',function(event){
		$("#hidden_frm").attr("action",$(this).attr('href'));
		$("#hidden_frm").submit();
		return false;
	});
  $("#search_text").live('focus',function(event){
    if($(this).val()=="Search..."){
      $(this).val('');
    }
  });
  $("#search_text").live('blur',function(event){
    if($(this).val()==""){
      $(this).val('Search...');
    }
  });
</script>
<section role="main" class="main-container content-page support search">
  <h2>
    Search Blog
    <a href="<?php echo  base_url()."contact"; ?>" id="subscribe" class="btn">
      Contact Us <i class="icon-envelope"></i>
    </a>
    <div class="search">
      <i class="icon-search"></i>
      <form  name="search_frm"  method="post" action="<?php echo site_url('blog/result'); ?>">
        <input name="search_text" id="search_text" type="text"  value="Search..." />
      </form>
    </div>
  </h2>
  <div class="content">
    <ul class="classic">
    	<?php if(count($product_data)>0){ ?>
    		<?php
    			foreach($product_data as $product){
    			$product_name=preg_replace("![^a-z0-9]+!i", "-", trim($product['title']));
    		?>
    			<li>
    				<a href="<?php echo base_url()."blog/".$product_name."/". $product['id'].""; ?>"><?php echo $product['title']; ?></a><br/>
    			   <?php echo substr(strip_tags($product['desc']),0,150)." ..."; ?>
    			</li>
    		<?php } ?>
    	<?php }else{ ?>
    			<li style="list-style-type:none">Your search - - did not match any documents.</li>
    	<?php } ?>
    </ul>
  </div>
  <form name="hidden_frm" method="post" id="hidden_frm">
	<input type="hidden" name="search_text" value="<?php echo $search_text; ?>"/>
  </form>
  <?php
    //Display paging links
    echo '<div id="tnt_pagination" class="pagination-container">'.$paging_links.'</div>';
  ?>
</section>
