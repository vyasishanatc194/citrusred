<?php
class Emailmarketing extends CI_Controller
{

	
	function index()
	{
	//Loads header, emailmarketing and footer view.
		$this->load->view('header',array('title'=>'Email Marketing'));
		$this->load->view('newsletter/emailmarketing',array('messages' =>$messages));
		$this->load->view('footer');
		
	}
	

	
} 
?>