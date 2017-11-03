<?php
/**
* A Feed class
*
* This class is to display rss feeds for blog
*
* @version 1.0
* @author Pravin Jha <pravinjha@gmail.com>
* @project Redcappi
*/
class Feed extends CI_Controller 
{
    function Feed()
    {
		parent::__construct();
		$this->load->model('blogfeed_model', '', TRUE);	//Load blog model
		$this->load->helper('xml');	//Load xml helper		force_ssl();	
    }
   /**
	* Function index to display to display rss feeds for blog
	* 
	*	@param integer $category_id to display rss feeds according to category
	*/
    function index($category_id=0)
    {
		############################################
		# Collect data for rss feed in  and array  #
		############################################
		$data['encoding'] = 'utf-8';
		$data['feed_name'] = SYSTEM_DOMAIN_NAME;
		$data['feed_url'] = base_url();		
		$data['page_language'] = 'en-us';
		$data['image_title'] = 'Redcappi';
		$data['image_width'] = '203';
		$data['image_height'] = '70';
		$data['site_url'] = base_url();
		$data['image_url'] = base_url().'webappassets/images/logo.png';
		#################################
		# Fetch blog info from database #
		#################################
		if($category_id>0){
			$this->load->model('blog/category_model', '', TRUE);	# Load category model
			$data['posts'] = $this->blogfeed_model->getRecentPosts($category_id);
			$category_array = $this->category_model->get_category_name(array('id'=>$category_id));
			$data['category_name']=$category_array[0]['category_name'];
			$data['category_id']=$category_id;
			$data['page_description'] = ucfirst($category_array[0]['category_name'])." News";
		}else{
			$data['posts'] = $this->blogfeed_model->getRecentPosts();
			$data['page_description'] = 'Easy to use drag-and-drop Email Builder, Email Marketing Software, Email Marketing Tool';
		}
		header("Content-Type: application/xml");
		# Load rss feed view
		$this->load->view('blog/rss_feed', $data);
    }
}
?>