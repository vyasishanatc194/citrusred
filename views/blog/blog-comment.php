 <script type="text/javascript">
	$(document).ready(function () {
		$('.reply').click(function () {
			$('.post_comment').hide();
			$('.post_reply').show();
			$('#postId').val($(this).attr('id'));
		});
	});
 </script>
 <?php $ci =& get_instance();?>
 <!--[body]-->
  <div id="body">
    <div class="container">
      	<?php if($ci->session->userdata('member_id')=='') { ?>
			<div class="get-started"> <a href="<?php echo  base_url()."signup"; ?>"><img src="<?php echo $this->config->item('webappassets');?>images/spacer.png?v=6-20-13" alt="" width="130" height="85"></a></div>
		<?php } ?>
      <!--[content goes here]-->
      <div id="content-area">
        <h1>Blog</h1>
		<?php
			$blog=false;
			$add_comment=true;
			foreach($blog_article as $postVal){
				if(!empty($postVal)){
					foreach($postVal as $key=>$value){
						$date = explode("-",$value['added_on']);
						$dateNew =  date("F d, Y", mktime(0, 0, 0, $date[1], $date[2], $date[0]));
						$title=preg_replace("![^a-z0-9]+!i", "-", trim($value['title']));
						$desc=$value['desc'];
						$desc=str_replace("../../../../", base_url(), $desc);
						$desc=str_replace("../../../", base_url(), $desc);
						$desc=str_replace("text-indent: -24px;",'text-indent: 0px;', $desc);
						$post_comment="";
						$add_comment=false;
						if($value['add_comment']==1){
							$post_comment='<a href="' . base_url().'index.php/blog_comment/comment/'.$value['id'].'">Post Comment</a>';
							$add_comment=true;
						}else{
							$post_comment='<a href="javascript:void(0);">Comments Closed</a>';
						}
		?>
						<div class="content-box">
						  <div class="content">
							<ul class="blog-style">
							<?php
								$blog_url=base_url().'blog/'.$title.'/'.$value['id'].'';
								$blog_title=$title;
							?>
							<?php	echo '<li>
								<h3><a href="' . base_url().'blog/'.$title.'/'.$value['id'].'">' . $value['title'] . '</a></h3>
								<p class="posted">Posted  on <span>     ' . $dateNew . ' </span></p>
								<span class=\'st_twitter\' st_url=\''.$blog_url.'\' st_title=\''.$blog_title.'\' ></span>
								<span class=\'st_facebook\' st_url=\''.$blog_url.'\' st_title=\''.$blog_title.'\'  ></span>
								<span class=\'st_email\' st_url=\''.$blog_url.'\' st_title=\''.$blog_title.'\'  ></span>
								<span class=\'st_sharethis\' st_url=\''.$blog_url.'\' st_title=\''.$blog_title.'\' ></span>
								' . $desc . '<br/>
								<div style="float:right; width:100%;margin:20px 0 0px 0;"><a href="' . base_url().'index.php/blog_comment/comment/'.$value['id'].'" class="rightlinks">'.$value['post_comment'].' Comments  </a>'.$post_comment.'</div><br/><br/>
								</li>';	?>
							</ul>

									<?php if(!empty($comment)){ ?>
									<h2>Comments</h2>
												<ul class="blog-style">
												<?php
													foreach($comment as $postVal){
														$date = explode("-",$postVal['added_on']);
														$dateNew =  date("F d, Y", mktime(0, 0, 0, $date[1], $date[2], $date[0]));

														$reply_comment="";
														if(!empty($postVal['reply'])){
															$reply_comment.="<ul>";
															foreach($postVal['reply'] as $reply){

																if($reply['comment']!=""){
																	$date = explode("-",$reply['added_on']);
																	$dateNew =  date("F d, Y", mktime(0, 0, 0, $date[1], $date[2], $date[0]));
																	$reply_comment.='<li>
																<p class="posted" style="margin-left:0px !important;">Posted by <span>' . $reply['name'] . '</span> on <span>     ' . $dateNew . ' </span></p>
																<p style="margin-left:0px !important;">' . $reply['comment'] . '</p>
																</li>';
																}
															}
															$reply_comment.="</ul>";
														}
														echo '<li>
																<p class="posted" style="margin-left:0px !important;">Posted by <span>' . $postVal['name'] . '</span> on <span>     ' . $dateNew . ' </span><span style="float:right;"><a href="#post_reply" class="reply" id="'.$postVal['id'].'">reply</a></span></p>
																<p style="margin-left:0px !important;">' . $postVal['comment'] . '</p>'.$reply_comment.'
																</li>';
													}
												?>

												</ul>
												<?php } ?>
											  </div>
											  <div class="bottom"><img src="<?php echo $this->config->item('webappassets');?>images/bottom.png?v=6-20-13" alt=""></div>
											</div>
				<?php	}

			$blog=true;
						}
					}	if(!$blog){ ?>
				<div class="content-box">
							<div class="top"><img src="<?php echo $this->config->item('webappassets');?>images/top.png?v=6-20-13" alt="" /></div>
							  <div class="content">
								<div class="blog"> </div>
								<ul class="blog-style">
				<?php echo "<li>No Blog Found</li>"; ?>
				</ul>
								 </div>
					  <div class="bottom"><img src="<?php echo $this->config->item('webappassets');?>images/bottom.png?v=6-20-13" alt=""></div>
					</div>
				<?php 	}
				?>

           <?php if($add_comment){ ?>
			<div class="top"><img src="<?php echo $this->config->item('webappassets');?>images/top.png?v=6-20-13" alt=""></div>
			<div class="content post_comment" id="post_comment">
            <h2>Post Your Comment</h2>
            <div class="shadow-border">
              <div><img src="<?php echo $this->config->item('webappassets');?>images/contact-top.png?v=6-20-13" alt=""></div>
              <div class="shadow-line">
                <div class="form">
					<div id="messages" style="color:#FF0000;" class="msg">
						<?php
						// display all messages

						if (is_array($messages)):
							foreach ($messages as $type => $msgs):
								foreach ($msgs as $message):
									echo ('<div class="info" style="margin-left:10px;"><span class="' .  $type .'" >' . $message . '</span></div>');
								endforeach;
							endforeach;
						endif;
						?>
					</div>

					<?php
						echo '<div style="color:#FF0000;" class="msg">'.validation_errors().'</div>';
						echo form_open('blog_comment/commentPost/'.$post_id, array('id' => 'signup'));
					?>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" id="singup">
						<tr>
							<td width="36%" class="label"> Name*</td>
							<td width="64%"><?php echo form_input(array('name'=>'name','id'=>'name','size'=>50,'class'=>'input')) ; ?></td>
						</tr>
						<tr>
							<td width="36%" class="label"> Email* (Not Published)</td>
							<td width="64%"><?php echo form_input(array('name'=>'email','id'=>'email','size'=>50,'class'=>'input')) ; ?> </td>
						</tr>
						<tr>
							<td width="36%" class="label">Comment*</td>
							<td width="64%">
							<?php echo form_textarea(array('name'=>'comment','id'=>'comment','size'=>50,'class'=>'text-area' )); ?>
							</td>
						</tr>
						<tr>
							<td class="label" width="36%"></td>
							<td width="64%">
								<input type="hidden" name="word" value="<?=$data['word'];?>" />
								<span  class="captcha"><?php echo $data['captcha']; ?></span>
								<input type="text" class="input-captha" name="securityCode" size="33" value="<?=$data['securityCode'];?>" >
							</td>
                      </tr>
                      <tr>
                        <td width="36%">&nbsp;</td>
                        <td width="64%">Please enter the security code shown in the image - this is required to prevent automated submissions</td>
                      </tr>
                      <tr>
                        <td >&nbsp;</td>
                        <td>
						<?php echo form_submit(array('name' => 'submit', 'id' => 'submit', 'class' => 'button-input','content' => 'comment'), ''); ?>
					</td>
                      </tr>
                    </table>
					<!-------------hidden field------------>
					 <input type="hidden" name="id" value="<?php echo $_REQUEST['id'];  ?>">
					 <input type="hidden" name="entry_id" value="<?php echo $post_id;  ?>">
					  <input type="hidden" name="added_on" value="<?php echo date('Y-m-d');  ?>">
					  <input type="hidden" name="action" value="submit">
                  </form>
                </div>
              </div>
              <div><img src="<?php echo $this->config->item('webappassets');?>images/contact-bottom.png?v=6-20-13" alt=""></div>
            </div>
          </div>
		  <div class="content post_reply" id="post_reply" style="display:none">
            <h2>Post Your Reply</h2>
            <div class="shadow-border">
              <div><img src="<?php echo $this->config->item('webappassets');?>images/contact-top.png?v=6-20-13" alt=""></div>
              <div class="shadow-line">
                <div class="form">
					<div id="messages" style="color:#FF0000;" class="msg">
					<?php
					// display all messages
					if (is_array($messages)):
						foreach ($messages as $type => $msgs):
							foreach ($msgs as $message):
								echo ('<div class="info" style="margin-left:10px;"><span class="' .  $type .'" >' . $message . '</span></div>');
							endforeach;
						endforeach;
					endif;
					?>
				</div>

                  <?php
						echo '<div style="color:#FF0000;" class="msg">'.validation_errors().'</div>';
						echo form_open('blog_comment/commentReply/'.$post_id, array('id' => 'signup'));
					?>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" id="singup">
						<tr>
							<td width="36%" class="label"> Name*</td>
							<td width="64%"><?php echo form_input(array('name'=>'name','id'=>'name','size'=>50,'class'=>'input')) ; ?></td>
						</tr>
						<tr>
							<td width="36%" class="label"> Email* (Not Published)</td>
							<td width="64%"><?php echo form_input(array('name'=>'email','id'=>'email','size'=>50,'class'=>'input')) ; ?> </td>
						</tr>
						<tr>
							<td width="36%" class="label">Comment*</td>
							<td width="64%">
							<?php echo form_textarea(array('name'=>'comment','id'=>'comment','size'=>50,'class'=>'text-area' )); ?>
							</td>
						</tr>
						<tr>
							<td class="label" width="36%"></td>
							<td width="64%">
								<input type="hidden" name="word" value="<?=$data['word'];?>" />
								<span  class="captcha"><?php echo $data['captcha']; ?></span>
								<input type="text" class="input-captha" name="securityCode" size="33" value="<?=$data['securityCode'];?>" >
							</td>
                      </tr>
                      <tr>
                        <td >&nbsp;</td>
                        <td>
						<?php echo form_submit(array('name' => 'submit', 'id' => 'submit', 'class' => 'button-input','content' => 'comment'), ''); ?>
					</td>
                      </tr>
                    </table>
					<!-------------hidden field------------>
					<input type="hidden" name="postId" id="postId" value="" />
					<input type="hidden" name="added_on" value="<?php echo date('Y-m-d');  ?>">
					<input type="hidden" name="action" value="submit" />
                  </form>
                </div>
              </div>
              <div><img src="<?php echo $this->config->item('webappassets');?>images/contact-bottom.png?v=6-20-13" alt=""></div>
            </div>
          </div>
          <div class="bottom"><img src="<?php echo $this->config->item('webappassets');?>images/bottom.png?v=6-20-13" alt=""></div>
		   <?php } ?>
        </div>

      <!--[/content goes here]-->
		<div class="rss_blog_link">
			<?php
				if(trim($this->uri->segment(1))=='blog_category'){
					$rss_url=base_url()."feed/index/".$this->uri->segment(4);
				}else{
					$rss_url=base_url()."feed/";
				}
			?>
			<a href="<?php echo $rss_url ;?>"><img src="<?php echo $this->config->item('webappassets');?>images/RSS_blog.png?v=6-20-13" /></a>
		</div>
      <div id="right-portion">
       	<?php if($archive==0){}else{?>
        <div class="links-multi"><?php print_r($archive);?>
          <h2>Archives</h2>
          <ul class="links-list">
		   <?php
			foreach($archive as $av1){
				if(!empty($av1)){
					foreach($av1 as $key1=>$av2){
						$dateA = explode('-',$av2['added_on']);
						$dateA = date("F", mktime(0, 0, 0, $dateA[1]+1,0,0));
						echo '<li><a href="' . base_url().'index.php/blog_category/blog_category_listing/'.$av2['added_on'].'">' . $dateA . ' ( ' . ' )</a></li>';
					}
				}
			}
			?>
          </ul>
        </div>
		  <?php } ?>
        <div class="links-multi">
          <h2><a href="<?php echo base_url().'blog/';?>">Categories</a></h2>
          <ul class="links-list">
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
      </div>
    </div>
  </div>
  <!--[/body]-->
