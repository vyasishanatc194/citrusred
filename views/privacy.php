    <?php
    $this->load->helper('url');
    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if (strpos($url, $this->config->item('redirect_url')) > 0) {
        redirect('https://getredcappi.com/privacy-statement/');
    }
    ?>
<!--[body]-->
<section role="main" class="main-container content-page">
  <h2>Privacy Statement</h2>
  <div class="content key-points" itemscope itemprop="about">
  <?php
  if(isset($seo_array)){
    if(count($seo_array)>0){
      $seo_array[0]['content']=str_replace('href="www.'.SYSTEM_DOMAIN_NAME.'"','href="http://www.'.SYSTEM_DOMAIN_NAME.'"',$seo_array[0]['content']);
      echo $seo_array[0]['content'];
    }
  }
  ?>
  </div>
  <div class="sign-up-bar content">
    <?php echo form_open('user/register/'); ?>
      <input placeholder="Email..." type="text" class="text" name="email" autocorrect="off" autocapitalize="off" /><input placeholder="Username..." type="text" class="text" name="username" autocorrect="off" autocapitalize="off" /><input placeholder="Password..." type="password" class="text" name="password" /><input value="Get Started" name="btnRegister" class="btn" type="submit" />
    </form>
  </div>
</section>
<!--[/body]-->
