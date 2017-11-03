    <?php
    $this->load->helper('url');
    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if (strpos($url, $this->config->item('redirect_url')) > 0) {
            redirect('http://getredcappi.com/how-can-we-help-you/');
    }
    ?>
<script type="text/javascript">
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
<section role="main" class="main-container content-page support blog">
  <h2>
    Support
    <a href="<?php echo  base_url()."contact"; ?>" id="subscribe" class="btn">
      Contact Us <i class="icon-envelope"></i>
    </a>
    <div class="search">
      <i class="icon-search"></i>
      <form  name="search_frm"  method="post" action="<?php echo site_url('support/result'); ?>">
        <input name="search_text" id="search_text" type="text"  value="Search..." />
      </form>
    </div>
  </h2>
  <div class="content flip">
    <h2 class="category-title"><?php echo $product_data[0]['category']; ?></h2>
    <ul class="classic">
  		<?php
  			foreach($product_data as $product){
  			$product_name=preg_replace("![^a-z0-9]+!i", "-", trim($product['product']));
  		?>
  			<li style="list-style-type:none">
  				<a href="<?php echo base_url()."support/detail/".$product_name."/". $product['id'].""; ?>">
  					<?php echo substr($product['product'],0,255); ?>
  				</a>
  			</li>
  		<?php
  			}
  		?>
    </ul>
  </div>
  <div id="right-column" class="flip">
    <div class="links-multi">
      <h2>Categories</h2>
      <ul class="clean">
        <?php
        foreach($support_data as $category){
        ?>
        <li>
          <?php if($category['id']==1){?>
            <a href="<?php echo base_url()."support/index";?>" id="categ_<?php echo $category['id']; ?>" <?php if($category['id']==$product_data[0]['category_id']){echo "class='select'";}?>>
          <?php }else{ ?>
            <a href="<?php echo base_url()."support/index/".$category['id'];?>" id="categ_<?php echo $category['id']; ?>" <?php if($category['id']==$product_data[0]['category_id']){echo "class='select'";}?>>
          <?php } ?>
            <?php echo $category['category']; ?>
          </a>
        </li>
        <?php } ?>
      </ul>
    </div>
  </div>
</section>
