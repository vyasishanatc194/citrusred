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
<section role="main" class="main-container content-page support detail">
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
  <div class="content">
    <h2 class="category-title"><?php echo substr($product_data['product'],0,255); ?></h2>
    <?php
      $description=str_replace("https", "http", $product_data['description']);
      $description=str_replace("../http", "http", $description);
      $description=str_replace("../../../../asset", base_url()."asset", $description);
      $description=str_replace("../../../asset", base_url()."asset", $description);
      $description=str_replace("../../asset", base_url()."asset", $description);
    ?>
    <?php echo $description; ?>
  </div>
</section>
