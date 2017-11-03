<!--[body]-->
    <?php
    $this->load->helper('url');
    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if (strpos($url, $this->config->item('redirect_url')) > 0) {
        redirect('https://www.getredcappi.com/api');
    }
    ?>
<section role="main" class="main-container content-page">
  <h2 itemprop="headline">RedCappi API</h2>
  <div class="content key-points" itemscope itemprop="about">
    <div class="api-resources">
      <h3>
        With the RedCappi API, you can integrate your existing website with RedCappi easier than ever before.
      </h3>
      <p>
        We are excited to provide our customers with access to RedCappi web API, which will allow your apps to collaborate with RedCappi for better and easier contacts management.
      </p>
      <h3>Getting Started</h3>
      <p>
        There is an <a href="http://api.<?php echo SYSTEM_DOMAIN_NAME;?>/v1/explorer/index.php">Interactive API Documentation and Sandbox</a> for developers which allow interacting with RedCappi API and seeing how the API responds to different commands and parameters.
      </p>
      <p>
        This requires authentication using public and private keys from your RedCappi account. It allows you to make real calls on your account.
      </p>
      <p>
        For more in depth information regarding the API, visit the <a href='<?php echo base_url()."api_information";?>'>RedCappi API Information</a> page.
      </p>
      <h3>Authentication</h3>
      <p>
        Authentication using your key is required for every API call. We are supporting HTTP Authentication using API Keys.
      </p>
      <p>
        You can generate/re-generate your API public and private keys from the "Extra" Menu when logged into your RedCappi account. These keys should be used for authenticating your API calls.
      </p>
      <h3>API Resources</h3>
      <strong>List Management</strong>
      <ul class="classic">
        <li>Get List(s)</li>
        <li>Add List</li>
        <li>Modify List</li>
        <li>Delete List</li>
      </ul>
      <strong>Contact Management</strong>
      <ul class="classic">
        <li>Get Contact(s)</li>
        <li>Add Contact</li>
        <li>Modify Contact</li>
        <li>Delete Contact</li>
      </ul>
    </div>
  </div>
</section>
<!--[/body]-->
