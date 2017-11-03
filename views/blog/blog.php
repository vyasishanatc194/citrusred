    <?php
    $this->load->helper('url');
    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if (strpos($url, $this->config->item('redirect_url')) > 0) {
        redirect('https://www.getredcappi.com/blog');
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
<script type="text/javascript" src="https://ws.sharethis.com/button/buttons.js?v=6-20-13"></script>
 <?php //print_r($blog_category);?>
 <?php $ci =& get_instance();?>
 <section role="main" class="main-container content-page support blog">
  <!--[content goes here]-->
  <h2>
    Blog
    <?php
      if(trim($this->uri->segment(1))=='blog_category'){
        $rss_url=base_url()."feed/index/".$this->uri->segment(4);
      }else{
        $rss_url=base_url()."feed/";
      }
    ?>
    <a href="<?php echo $rss_url ;?>" id="subscribe" class="btn">
      Subscribe to RedCappi Blog <i class="icon-rss"></i>
    </a>
    <div class="search">
      <i class="icon-search"></i>
	  <form  name="search_frm"  method="post" action="<?php echo site_url('blog/result'); ?>">
      <input id="search_text" value="Search..." type="text" />
	  </form>
    </div>
  </h2>
  <ul class="classic">
  <?php
    $blog=false;
    foreach($post as $postVal){
      if(!empty($postVal)){
        $blog_count=count($postVal);
        $i=0;
        foreach($postVal as $key=>$value){
          $i++;
          $date = explode("-",$value['added_on']);
          $dateNew =  date("M d, Y", mktime(0, 0, 0, $date[1], $date[2], $date[0]));
          $title=preg_replace("![^a-z0-9]+!i", "-", trim($value['title']));
          $desc=$value['desc'];
          $desc=str_replace("http://www.".SYSTEM_DOMAIN_NAME."/", base_url(), $desc);
          $desc=str_replace("../../../../", base_url(), $desc);
          $desc=str_replace("../../../", base_url(), $desc);
          $desc=str_replace("text-indent: -24px;",'text-indent: 0px;', $desc);
          $post_comment="";
          if($value['add_comment']==1){
            $post_comment='<a href="' . base_url().'index.php/blog_comment/comment/'.$value['id'].'">Post Comment</a>';
          }else{
            $post_comment='<a href="javascript:void(0);">Comments Closed</a>';
          }
          ?>
            <?php
              $blog_url=base_url().'blog/'.$title.'/'.$value['id'].'';
              $blog_title=$title;
            ?>
            <?php echo '
            <li>
              <div class="content" itemscope itemtype="http://schema.org/Blog">
                <h3><a href="'.base_url().'blog/'.$title.'/'.$value['id'].'" itemprop="headline">' . $value['title'] . '</a></h3>
                <p class="posted">Posted on  <span itemprop="datePublished">     ' . $dateNew . ' </span></p>
                <span class=\'st_twitter\' st_url=\''.$blog_url.'\' st_title=\''.$blog_title.'\' ></span>
                <span class=\'st_facebook\' st_url=\''.$blog_url.'\' st_title=\''.$blog_title.'\'  ></span>
                <span class=\'st_email\' st_url=\''.$blog_url.'\' st_title=\''.$blog_title.'\'  ></span>
                <span class=\'st_sharethis\' st_url=\''.$blog_url.'\' st_title=\''.$blog_title.'\' ></span>
                <div itemprop="text">' . $desc . '</div>
              </div>
            </li>'; ?>
      <?php  } ?>
      <?php $blog=true;
      }
    }
    if(!$blog){ ?>
      <?php echo "<li><div class='content'>No Blog Found</div></li>"; ?>
    <?php } ?>
    <li>
      <?php
        //Display paging links
        echo '<div id="tnt_pagination" class="pagination-container">'.$paging_links.'</div>';
      ?>
    </li>
</ul>
<!--[/content goes here]-->
<div id="right-column">
  <?php  if($blog){ ?>
    <div class="links-multi">
      <h2>
        <a href="<?php echo base_url().'blog/';?>">Categories</a>
      </h2>
      <ul class="clean">
        <?php
        foreach($category as $v1){
          if(!empty($v1)){
            foreach($v1 as $key1=>$v2){
                $category_name=preg_replace("![^a-z0-9]+!i", "-", trim($v2['category_name']));
                echo '<li><a href="' . base_url().'blog/email-marketing-category/'.$category_name.'/'.$v2['id'].'">'.$v2['category_name'].'</a></li>';
            }
          }
        }
        ?>
      </ul>
    </div>
  <?php } ?>
  <?php if($archive==0){}else{?>
    <div class="links-multi">
      <h2>Archives</h2>
      <ul class="clean">
        <?php
          $archiveMonthYear = '';
          foreach($archive as $av1){
            if(!empty($av1)){
              foreach($av1 as $key1=>$av2){
                $dateM = explode('-',$av2['added_on']);
                $dateA =  date("F", mktime(0,0,0, $dateM[1]+1, 0, 0));
                $month_index=date("n", mktime(0,0,0, $dateM[1]+1, 0, 0));
                $year =$dateM[0];
                if($archiveMonthYear != $dateA ." ".$year){
                  $archiveMonthYear = $dateA ." ".$year;
                  echo '<li><a href="' . base_url()."blog/archive/$month_index/$year\">$archiveMonthYear</a></li>";
                }
              }
            }
          }
        ?>
      </ul>
    </div>
  <?php } ?>
</div>
</section>
<!--[/body]-->
