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
      <input id="search_text" value="Search..." type="text" />
    </div>
  </h2>
  <ul class="classic">
				<?php
					$blog=false;
					foreach($blog_category as $postVal){
						if(!empty($postVal)){
							$blog_count=count($postVal);
							$i=0;
							foreach($postVal as $key=>$value){
								$i++;
								$date = explode("-",$value['added_on']);
								$dateNew =  date("F d, Y", mktime(0, 0, 0, $date[1], $date[2], $date[0]));
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
                </li>';
                ?>
            <?php
              $blog=true;
            }
          }
        } if(!$blog){ ?>
        <?php echo "<li>No Blog Found</li>"; ?>
    <?php }?>
    <li>
      <?php
        //Display paging links
        echo '<div id="tnt_pagination" class="pagination-container">'.$paging_links.'</div>';
      ?>
    </li>
  </ul>

	<?php
  ##### Check for logged in members #######
  if($_SESSION['user_id']!=''){ ?>
    <div class="content">
      <h2>Add Your Posts</h2>
      <div class="shadow-border">
        <div><img src="<?php echo $this->config->item('webappassets');?>images-front/contact-top.png?v=6-20-13" alt=""></div>
          <div class="shadow-line">
            <div class="form">
              <div id="messages" style="color:#FF0000;" class="msg">
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
              if (isset($error)) echo $error;
              echo '<div style="color:#FF0000;" class="msg">'.validation_errors().'</div>';
              echo form_open_multipart('blog_category/addPost/'.$cat_id, array('id' => 'signup'));
              ?>
              <table width="100%" border="0" cellspacing="0" cellpadding="0" id="singup">
                <tr>
                  <td width="36%" class="label">Title</td>
                  <td width="64%">
                    <?php echo form_input(array('name'=>'title','id'=>'title','size'=>50,'class'=>'input' )); ?>
                  </td>
                </tr>
                <tr>
                  <td class="label"> Description</td>
                  <td><?php echo form_textarea(array('name'=>'desc','id'=>'desc','size'=>50,'class'=>'text-area')) ; ?></td>
                </tr>
                <?php if(BLOG_IMAGE_CAN_POST==1){?>
                <tr>
                  <td class="label"> Images</td>
                  <td><input type="file" name="userfile[]" size="20" class="multi" /></td>
                </tr>
                <?php }?>
                <tr>
                  <td>&nbsp;</td>
                  <td>
                    <?php echo form_submit(array('name' => 'submit', 'id' => 'submit', 'class' => 'button-input','content' => 'comment'), ''); ?>
                  </td>
                </tr>
              </table>
              <!-------------hidden field------------>
              <input type="hidden" name="id" value="<?php echo $_REQUEST['id'];  ?>">
              <input type="hidden" name="cat_id" value="<?php echo $cat_id;  ?>">
              <input type="hidden" name="added_on" value="<?php echo date('Y-m-d');  ?>">
              <input type="hidden" name="added_by" value="<?php echo $_SESSION['user_id'];  ?>">
              <input type="hidden" name="status" value="1">
              <input type="hidden" name="post_archives" value="0">
              <input type="hidden" name="action" value="submit">
            </form>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>
  <!--[/content goes here]-->
  <div id="right-column">
    <div class="links-multi">
      <h2><a href="<?php echo base_url().'blog/';?>">Categories</a></h2>
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
