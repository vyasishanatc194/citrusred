<?php
/************About Us class********************/
class About_us extends CI_Controller
{
	function index()
	{
		//Loads header, About us and footer view.
		$this->load->view('header',array('title'=>'About Us'));
		$this->load->view('about-us');
		$this->load->view('footer');		
	}
}
?>
