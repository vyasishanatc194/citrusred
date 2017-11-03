<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Email Sender
 */
class EmailSender
{
    private $ci; // CI Instance

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->library('email');

        $this->ci->load->config('email');
        $this->ci->email->initialize($this->ci->config->item('email'));
    }

    public function send($destinatario = '')
    {
        $this->ci->email->from('somu@gmail.com', 'Your Name');
        $this->ci->email->to($destinatario);
        // $this->ci->email->cc('another@another-example.com');
        // $this->ci->email->bcc('them@their-example.com');

        $this->ci->email->subject('Email for pravin');
        $this->ci->email->message('<b>Pravin: </b><font color="red"> the email class from CodeIgniter using EmailSender.</font> ');

        $this->ci->email->send();
    }
}
