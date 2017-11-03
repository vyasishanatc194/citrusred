<?php

class PaypalModel extends CI_Model {

    //Constructor class with parent constructor
    function PaypalModel() {
        parent::__construct();
        $this->load->helper('cookie');
        $this->load->library('encrypt');
    }

    function insertResponseData($params) {
        echo '<pre>';
        print_R($params);exit;
        $this->db->insert('red_paypal_response', $params);
        return $this->db->insert_id();
    }

}

?>