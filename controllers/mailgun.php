<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mailgun extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('EmailSender.php');
    }

    public function index() {

        $this->emailsender->send('peejha@yahoo.com');
        $this->emailsender->send('pravinjha@gmail.com');
        $this->emailsender->send('pravinjha@outlook.com');

    }

}
