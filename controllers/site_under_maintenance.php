<?php
/************About Us class********************/
class Site_under_maintenance extends CI_Controller
{
	function __construct(){
        parent::__construct();
		if(($_SERVER["SERVER_NAME"]=="red7.me")||($_SERVER["SERVER_NAME"]=="www.red7.me")){
			redirect(base_url());
		}		
	}
	function index()
	{
		 
		$this->load->view('site_under_maintenance');
		
	}
}
/* End of file */
?>
