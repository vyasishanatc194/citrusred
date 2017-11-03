<?php

/**
 * 	Model class for subscribers 
 * 	It have  functions for interaction with database.
 */
class Subscriber_Model extends CI_Model {

    //Constructor functon for subscriber with parent constructor
    function Subscriber_Model() {
        parent::__construct();
    }

    /**
     * 	Function create_subscriber
     *
     * 	Function to create new subscriber
     *
     * 	@param (array) (input_array)  values to insert into database
     *
     * 	@return (int) return inserted subscriber id
     */
    function create_subscriber($input_array) {
        # added for the subscription_id for All My Contact in DB_Tbl red_email_subscribers
        $input_array['subscription_id'] = - $input_array['subscriber_created_by'];
        $this->db->insert('red_email_subscribers', $input_array);
        return $this->db->insert_id();
    }

    /**
     * 	Function update_subscriber
     *
     * 	Function to update existing subscriber info
     *
     * 	@param (array) (input_array)  values to update into database
     *
     * 	@param (array) (conditions_array)  conditions to checked with database with conditions
     *
     * 	@return (int)	return updated subscriber id
     */
    function update_subscriber($input_array, $conditions_array, $where_in = false, $where_in_subscriber = array()) {
        if ($where_in) {
            $this->db->where_in('subscriber_id', $this->input->get_post('subscriber_id', true));
        }
        if (count($where_in_subscriber) > 0) {
            $this->db->where_in('subscriber_id', $where_in_subscriber);
        }
        $this->db->update('red_email_subscribers', $input_array, $conditions_array);
        return $this->db->affected_rows();
    }

    /**
     * 	Function delete_subscriber
     *
     * 	Function to delete  subscriber
     *
     * 	@param (array) (conditions_array)  conditions to checked with database with conditions
     *
     * 	@return (int)	return deleted subscriber id
     */
    function delete_subscriber($conditions_array, $action = "") {
        //$this->db->update('red_email_subscribers',array('is_deleted'=>1),$conditions_array);
        if ($action == "in") {
            $this->db->where_in('subscriber_id', $conditions_array);
            $this->db->delete('red_email_subscribers');
        } else {
            $this->db->delete('red_email_subscribers', $conditions_array);
        }
        return $this->db->affected_rows();
    }

    /**
     * 	Function delete_subscriber_from_list
     *
     * 	Function to delete  subscribers from list
     *
     * 	@param (array) (conditions_array)  conditions to checked with database with conditions
     *
     * 	@return (int)	return deleted subscriber id
     */
    function delete_subscriber_from_list($conditions_array, $action = "") {
        //$this->db->update('red_email_subscribers',array('is_deleted'=>1),$conditions_array);
        if ($action == "in") {
            $this->db->where_in('subscriber_id', $conditions_array);
            $this->db->delete('red_email_subscription_subscriber');
        } else {
            $this->db->delete('red_email_subscription_subscriber', $conditions_array);
        }
        return $this->db->affected_rows();
    }

    /**
     * 	Function unsubscribe_subscriber
     *
     * 	Function to unsubscribe  subscriber
     *
     * 	@param (array) (conditions_array)  conditions to checked with database with conditions
     *
     * 	@return (int)	return unsubscribe subscriber id
     */
    function unsubscribe_subscriber($conditions_array) {
        $this->db->update('red_email_subscribers', array('subscriber_status' => 0), $conditions_array);
        return $this->db->affected_rows();
    }

    /**
     * 	Function get_subscriber_data
     *
     * 	Function to fetch subscriber data
     *
     * 	@param (array) (conditions_array)  conditions to checked with database with conditions
     *
     * 	@param (string) (srch) to search records according to search condition submit by user
     *
     * 	@param (string) (order_by)  define order by "Asc" or "Desc"
     *
     * 	@param (string) (order_by_column)  // define order by column name
     *
     * 	@param (int) (rows_per_page)  number of record per page
     *
     * 	@param (int) (start)  These determine which number to start the record
     *
     * 	@return (array)	return fetch records
     */
    function get_subscriber_data($conditions_array = array(), $rows_per_page = 10, $start = 0, $bounce = false) {
        #$soft_bounce_array=$this->get_site_configuration_data(array('config_name'=>'max_soft_bounce'));

        $rows = array();
        $this->db->from('red_email_subscribers as res');
        if ($bounce) {
            $where = "( subscriber_status='3' OR  subscriber_status =4 )";
            $this->db->where($where);
        }
        if ($_POST['unsubscribe'] == '1') {
            $this->db->where("( subscriber_status='0' )");
        } elseif ($_POST['unsubscribe'] == '5') {
            $this->db->where("( subscriber_status='5' )");
        }
        $this->db->where($conditions_array, false);
        if (isset($_POST['srch_email'])) {
            $srch = mysql_real_escape_string($this->input->post('srch_email'));
            $spacefind = preg_match('/\s/',$srch);
            if($spacefind > 0){
                
                $srchnew = (explode(" ",$srch));
                $where = "( res.subscriber_email_address LIKE '%$srch%' OR (res.subscriber_first_name LIKE '%$srchnew[0]%' AND res.subscriber_last_name LIKE '%$srchnew[1]%') OR res.subscriber_extra_fields LIKE '%$srch%')";           
            }
            else{
                $where = "( res.subscriber_email_address LIKE '%$srch%' OR res.subscriber_first_name LIKE '%$srch%' OR res.subscriber_last_name LIKE '%$srch%' OR res.subscriber_extra_fields LIKE '%$srch%')";           
            }
            $this->db->where($where);
        }

        if ($this->input->get_post('uncheck_list', true)) {
            $uncheck_id = explode(',', $this->input->get_post('uncheck_list', true));
            $this->db->where_not_in('res.subscriber_id', $uncheck_id);
        }
        $this->db->limit($rows_per_page, $start);
        if ((isset($_POST['order_by'])) && (isset($_POST['order_by_column']))) {
            $order_by_column = $_POST['order_by_column'];
            $order_by = $_POST['order_by'];
            $this->db->order_by('res.' . $order_by_column, $order_by);
        } else {
            //$this->db->order_by('res.subscriber_id');
        }

        $result = $this->db->get();
        foreach ($result->result_array() as $row) {
            $rows[] = $row;
        }
               

        $result->free_result();
        return $rows;
    }

    /**
     * 	Function get_distinct_subscriber_data
     *
     * 	Function to fetch distinct  subscriber data
     *
     * 	@param (array) (conditions_array)  conditions to checked with database with conditions
     *
     * 	@param (string) (srch) to search records according to search condition submit by user
     *
     * 	@param (string) (order_by)  define order by "Asc" or "Desc"
     *
     * 	@param (string) (order_by_column)  // define order by column name
     *
     * 	@param (int) (rows_per_page)  number of record per page
     *
     * 	@param (int) (start)  These determine which number to start the record
     *
     * 	@return (array)	return fetch distinct records
     */
    function get_distinct_subscriber_data($conditions_array = array(), $srch = "", $order_by = "", $order_by_column = "", $rows_per_page = 10, $start = 0, $bounce = false) {
        $rows = array();
        $this->db->select('res.,resu.*', false);
        $this->db->group_by("subscriber_email_address");
        $this->db->from('red_email_subscribers as res');
        $this->db->join('red_email_subscriptions as resu', 'resu.subscription_id=res.subscription_id');
        if ($this->input->get_post('uncheck_list', true)) {
            $uncheck_id = explode(',', $this->input->get_post('uncheck_list', true));
            $this->db->where_not_in('res.subscriber_id', $uncheck_id);
        }
        if ($bounce) {
            $where = "( subscriber_status='3' OR  subscriber_status =4 )";
            $this->db->where($where);
        }
        $this->db->where($conditions_array);
        if ($srch) {
            $where = "( res.subscriber_email_address LIKE '%$srch%' OR res.subscriber_first_name LIKE '%$srch%' OR res.subscriber_last_name LIKE '%$srch%'  OR res.subscriber_extra_fields LIKE '%$srch%')";
            $this->db->where($where);
        }
        $this->db->limit($rows_per_page, $start);
        if (($order_by == "") && ($order_by_column == "")) {
            $this->db->order_by('res.subscriber_id');
        } else {
            $this->db->order_by('res.' . $order_by_column, $order_by);
        }

        $result = $this->db->get();

        foreach ($result->result_array() as $row) {
            $rows[] = $row;
        }
        $result->free_result();
        return $rows;
    }

    /**
     * 	Function get_subscription_count
     *
     * 	Function to fetch count of subscriber data
     *
     * 	@param (array) (conditions_array)  conditions to checked with database with conditions
     *
     * 	@param (string) (srch) to search records according to search condition submit by user
     *
     * 	@return (int)	return total number of records
     */
    function get_subscriber_count($conditions_array = array(), $bounce = false) {
        #$soft_bounce_array=$this->get_site_configuration_data(array('config_name'=>'max_soft_bounce'));
        $retval = 0;
        $rows = array();
        $this->db->select('count(subscriber_id) as tot_subscriber');
        $this->db->from('red_email_subscribers as res');
        if ($this->input->get_post('uncheck_list', true)) {
            $uncheck_id = explode(',', $this->input->get_post('uncheck_list', true));
            $this->db->where_not_in('res.subscriber_id', $uncheck_id);
        }

        if ($bounce) {
            $where = "( subscriber_status='3' OR  subscriber_status =4 )";
            $this->db->where($where);
        }
        $this->db->where($conditions_array);
        if (isset($_POST['srch_email'])) {
            $srch = mysql_real_escape_string($this->input->post('srch_email'));
            $where = "( res.subscriber_email_address LIKE '%$srch%' OR res.subscriber_first_name LIKE '%$srch%' OR res.subscriber_last_name LIKE '%$srch%'  OR res.subscriber_extra_fields LIKE '%$srch%')";
            $this->db->where($where);
        }
        $result = $this->db->get();

        foreach ($result->result() as $row) {
            $retval = $row->tot_subscriber;
        }
        $result->free_result();
        return $retval;
    }

    function get_unsubscribed_subscriber_count($where) {
        $retval = 0;
        $this->db->select('count(subscriber_id) as tot_unsubscribed');
        $this->db->from('red_email_subscribers');
        $this->db->where($where);

        $result = $this->db->get();

        foreach ($result->result() as $row) {
            $retval = $row->tot_unsubscribed;
        }
        $result->free_result();
        return $retval;
    }

    function get_total_used_subscriber_count($conditions_array = array()) {
        $retval = 0;
        $rows = array();
        $this->db->select('count(subscriber_id) as tot_subscriber');
        $this->db->from('red_email_subscribers as res');
        $this->db->where($conditions_array);

        $result = $this->db->get();

        foreach ($result->result() as $row) {
            $retval = $row->tot_subscriber;
        }
        $result->free_result();
        return $retval;
    }

    function ifAnyContactExists() {
        $retval = false;
        $this->db->select('subscriber_id');
        $this->db->from('red_email_subscribers as res');
        $this->db->where(array('subscriber_created_by' => $this->session->userdata('member_id'), 'subscriber_status' => 1, 'is_deleted' => 0));
        $this->db->limit(1, 0);
        $result = $this->db->get();

        if ($result->num_rows() > 0) {
            $retval = true;
        }
        $result->free_result();
        return $retval;
    }

    /**
     * 	Function get_subscriber_data
     *
     * 	Function to fetch subscriber data
     *
     * 	@param (array) (conditions_array)  conditions to checked with database with conditions
     *
     * 	@param (string) (srch) to search records according to search condition submit by user
     *
     * 	@param (string) (order_by)  define order by "Asc" or "Desc"
     *
     * 	@param (string) (order_by_column)  // define order by column name
     *
     * 	@param (int) (rows_per_page)  number of record per page
     *
     * 	@param (int) (start)  These determine which number to start the record
     *
     * 	@return (array)	return fetch records
     */
    function get_subscriber_info_view($conditions_array = array(), $rows_per_page = 10, $start = 0) {
        $rows = array();
        $this->db->select('*');
        $this->db->from('red_email_subscribers as res');
        $this->db->where($conditions_array);
        if (isset($_POST['srch_email'])) {
            $srch = mysql_real_escape_string($this->input->post('srch_email'));
            $where = "( res.subscriber_email_address LIKE '%$srch%' OR res.subscriber_first_name LIKE '%$srch%' OR res.subscriber_last_name LIKE '%$srch%' OR res.subscriber_extra_fields LIKE '%$srch%')";
            $this->db->where($where);
        }

        if ($this->input->get_post('uncheck_list', true)) {
            $uncheck_id = explode(',', $this->input->get_post('uncheck_list', true));
            $this->db->where_not_in('res.subscriber_id', $uncheck_id);
        }
        $this->db->limit($rows_per_page, $start);
        if ((isset($_POST['order_by'])) && (isset($_POST['order_by_column']))) {
            $order_by_column = $_POST['order_by_column'];
            $order_by = $_POST['order_by'];
            $this->db->order_by('res.' . $order_by_column, $order_by);
        } else {
            $this->db->order_by('res.subscriber_id');
        }
        $result = $this->db->get();
        foreach ($result->result_array() as $row) {
            $rows[] = $row;
        }
        $result->free_result();
        return $rows;
    }

    /**
     * 	Function get_distinct_email
     *
     * 	Function to fetch subscriber data
     *
     * 	@param (array) (conditions_array)  conditions to checked with database with conditions
     *
     * 	@return (array)	return fetch records
     */
    function get_distinct_email($conditions_array = array(), $subscription_id = array()) {
        $rows = array();
        $this->db->select('res.subscriber_id, res.subscriber_email_address, res.subscriber_first_name, res.subscriber_last_name, res.subscriber_state, res.subscriber_zip_code, res.subscriber_country, res.subscriber_company, res.subscriber_city, res.subscriber_dob, res.subscriber_phone, res.subscriber_address', false);
        $this->db->from('red_email_subscription_subscriber as ress');
        $this->db->join('red_email_subscribers as res', 'res.subscriber_id =ress.subscriber_id', 'right');
        $this->db->group_by('res.subscriber_id, res.subscriber_email_address, res.subscriber_first_name, res.subscriber_last_name, res.subscriber_state, res.subscriber_zip_code, res.subscriber_country, res.subscriber_company, res.subscriber_city, res.subscriber_dob, res.subscriber_phone, res.subscriber_address');
        $where = "";
        if (count($subscription_id) > 0) {
            if (!(in_array('-' . $this->session->userdata('member_id'), $subscription_id))) {
                $this->db->or_where_in('ress.subscription_id', $subscription_id);
            }
        }

        $this->db->where($conditions_array);
        #Before the query runs:
        # echo $this->db->_compile_select(); 		 
        #exit;
        $result = $this->db->get();
        foreach ($result->result_array() as $row) {
            $rows[] = $row;
        }
        $result->free_result();
        return $rows;
    }

    function get_contacts_to_queue($conditions_array = array(), $memid, $subscription_id = '') {
        if (trim($subscription_id) != '')
            $arrContacts = @explode('_', $subscription_id);
        if (count($arrContacts) > 0) {
            if (!(in_array('-' . $memid, $arrContacts))) {
                $this->db->query("select res.subscriber_id, res.subscriber_email_address, res.subscriber_first_name, res.subscriber_last_name, res.subscriber_state, res.subscriber_zip_code, res.subscriber_country, res.subscriber_company, res.subscriber_city, res.subscriber_dob, res.subscriber_phone, res.subscriber_address, res.subscriber_extra_fields from red_email_subscribers res inner join red_email_subscription_subscriber ress on res.subscriber_id=ress.subscriber_id where res. subscription_id in ($subscription_id)");
            }
        }


        $rows = array();
        $this->db->select('res.subscriber_id, res.subscriber_email_address, res.subscriber_first_name, res.subscriber_last_name, res.subscriber_state, res.subscriber_zip_code, res.subscriber_country, res.subscriber_company, res.subscriber_city, res.subscriber_dob, res.subscriber_phone, res.subscriber_address, res.subscriber_extra_fields', false);
        $this->db->from('red_email_subscription_subscriber as ress');
        $this->db->join('red_email_subscribers as res', 'res.subscriber_id =ress.subscriber_id', 'right');
        $this->db->group_by('res.subscriber_id, res.subscriber_email_address, res.subscriber_first_name, res.subscriber_last_name, res.subscriber_state, res.subscriber_zip_code, res.subscriber_country, res.subscriber_company, res.subscriber_city, res.subscriber_dob, res.subscriber_phone, res.subscriber_address, res.subscriber_extra_fields');
        $where = "";
        if (trim($subscription_id) != '')
            $arrContacts = @explode('_', $subscription_id);
        if (count($arrContacts) > 0) {
            if (!(in_array('-' . $memid, $arrContacts))) {
                $this->db->or_where_in('ress.subscription_id', $arrContacts);
            }
        }

        $this->db->where($conditions_array);
        #Before the query runs:
        # echo $this->db->_compile_select(); 		 
        #exit;
        $result = $this->db->get();
        foreach ($result->result_array() as $row) {
            $rows[] = $row;
        }
        $result->free_result();
        return $rows;
    }

    function get_distinct_contacts($conditions_array = array(), $memid, $subscription_id = '') {

        $rows = array();
        $this->db->select('res.subscriber_id, res.subscriber_email_address, res.subscriber_first_name, res.subscriber_last_name, res.subscriber_state, res.subscriber_zip_code, res.subscriber_country, res.subscriber_company, res.subscriber_city, res.subscriber_dob, res.subscriber_phone, res.subscriber_address, res.subscriber_extra_fields', false);
        $this->db->from('red_email_subscription_subscriber as ress');
        $this->db->join('red_email_subscribers as res', 'res.subscriber_id =ress.subscriber_id', 'right');
        $this->db->group_by('res.subscriber_id, res.subscriber_email_address, res.subscriber_first_name, res.subscriber_last_name, res.subscriber_state, res.subscriber_zip_code, res.subscriber_country, res.subscriber_company, res.subscriber_city, res.subscriber_dob, res.subscriber_phone, res.subscriber_address, res.subscriber_extra_fields');
        $where = "";
        if (trim($subscription_id) != '')
            $arrContacts = @explode('_', $subscription_id);
        if (count($arrContacts) > 0) {
            if (!(in_array('-' . $memid, $arrContacts))) {
                $this->db->or_where_in('ress.subscription_id', $arrContacts);
            }
        }

        $this->db->where($conditions_array);
        #Before the query runs:
        # echo $this->db->_compile_select(); 		 
        #exit;
        $result = $this->db->get();
        foreach ($result->result_array() as $row) {
            $rows[] = $row;
        }
        $result->free_result();
        return $rows;
    }

    /**
     * 	Function get_distinct_contact_count
     *
     * 	Function to fetch subscriber data
     *
     * 	@param (array) (conditions_array)  conditions to checked with database with conditions
     *
     * 	@return (array)	return fetch records
     */
    function get_distinct_contact_count($conditions_array = array(), $subscription_id = 0) {
        $retval = 0;
        $this->db->select('count(distinct res.subscriber_id) as totContact', false);
        $this->db->from('red_email_subscription_subscriber as ress');
        $this->db->join('red_email_subscribers as res', 'res.subscriber_id =ress.subscriber_id', 'right');

        $this->db->where($conditions_array);
        if ($subscription_id > 0)
            $this->db->where(array('ress.subscription_id' => $subscription_id));
        $result = $this->db->get();

        foreach ($result->result() as $row) {
            $retval = $row->totContact;
        }
        $result->free_result();
        return $retval;
    }

    function get_subscriber($conditions_array = array()) {
        $rows = array();
        $this->db->select('subscriber_id');
        $this->db->from('red_email_subscribers as res');
        if ($this->input->get_post('uncheck_list', true)) {
            $uncheck_id = explode(',', $this->input->get_post('uncheck_list', true));
            $this->db->where_not_in('res.subscriber_id', $uncheck_id);
        }

        if ($bounce) {
            $where = "( subscriber_status='3' OR  subscriber_status =4 )";
            $this->db->where($where);
        }
        $this->db->where($conditions_array);
        if (isset($_POST['email_search'])) {
            $srch = $_POST['email_search'];
            $where = "( res.subscriber_email_address LIKE '%$srch%' OR res.subscriber_first_name LIKE '%$srch%' OR res.subscriber_last_name LIKE '%$srch%'  OR res.subscriber_extra_fields LIKE '%$srch%')";
            $this->db->where($where);
        }
        $result = $this->db->get();

        foreach ($result->result_array() as $row) {
            $rows[] = $row['subscriber_id'];
        }
        $result->free_result();
        return $rows;
    }

    function getSubscriberEmailId($sid) {
        $rsGetEmail = $this->db->query("SELECT `subscriber_email_address` FROM red_email_subscribers where `subscriber_id` = '$sid'");
        return $rsGetEmail->row()->subscriber_email_address;
    }

    /**
     * 	Function replace_subscription_subscriber
     *
     * 	Function to create insert  subscriber_id with subscription_id
     *
     * 	@param (array) (input_array)  values to insert into database
     *
     * 	@return (int) return inserted subscriber id
     */
    function replace_subscription_subscriber($input_array) {
        $this->db->replace_into('red_email_subscription_subscriber', $input_array);
        return $this->db->affected_rows();
    }

    /**
     * 	Function delete_subscription_subscriber
     *
     * 	Function to delete   subscriber_id and  subscription_id from table
     *
     * 	@param (array) (input_array)  values to insert into database
     *
     * 	@return (int) return inserted subscriber id
     */
    function delete_subscription_subscriber($conditions_array = array(), $where_in = false, $where_in_subscription = array(), $where_in_subscriber = array()) {
        if ($where_in) {
            $this->db->where_in('subscriber_id', $this->input->get_post('subscriber_id', true));
        }
        if (count($where_in_subscriber) > 0) {
            $this->db->where_in('subscriber_id', $where_in_subscriber);
        }
        if (count($where_in_subscription) > 0) {
            $this->db->where_in('subscription_id', $where_in_subscription);
        }
        $this->db->delete('red_email_subscription_subscriber', $conditions_array);
        return $this->db->affected_rows();
    }

    /**
     * 	Function get_subscription_subscriber_count
     *
     * 	Function to get  count of contacts according to subscription_id
     *
     * 	@param (array) (input_array)  values to insert into database
     *
     * 	@return (int) return inserted subscriber id
     */
    function get_subscription_subscriber_count($conditions_array = array(), $typ = '') {

        $this->db->select('count(distinct res.subscriber_id) numrows', false);
        $this->db->join('red_email_subscribers as res', 'res.subscriber_id =ress.subscriber_id', 'right');
        if ($typ == 'bounce') {
            $where = "( subscriber_status='3' OR  subscriber_status =4 )";
            $this->db->where($where);
        } elseif ($typ == 'unsubscribe') {
            $where = "( subscriber_status='0' )";
            $this->db->where($where);
        } elseif ($typ == 'removed') {
            $where = "( subscriber_status='5' )";
            $this->db->where($where);
        }
        if (isset($_POST['srch_email'])) {
            $srch = mysql_real_escape_string($this->input->post('srch_email'));
            $where = "( res.subscriber_email_address LIKE '%$srch%' OR res.subscriber_first_name LIKE '%$srch%' OR res.subscriber_last_name LIKE '%$srch%' OR res.subscriber_extra_fields LIKE '%$srch%')";
            $this->db->where($where);
        }
        $this->db->where($conditions_array);
        return $this->db->get('red_email_subscription_subscriber as ress')->row()->numrows;
    }

    /**
     * 	Function get_subscription_subscriber_data
     *
     * 	Function to get  count of contacts according to subscription_id
     *
     * 	@param (array) (input_array)  values to insert into database
     *
     * 	@return (int) return inserted subscriber id
     */
    function get_subscription_subscriber_data($conditions_array = array(), $rows_per_page = 10, $start = 0, $bounce = false) {
        $rows = array();
        $this->db->select('distinct res.*,ress.*', false);
        $this->db->from('red_email_subscription_subscriber as ress');
        $this->db->join('red_email_subscribers as res', 'res.subscriber_id =ress.subscriber_id');
        if ($bounce) {
            $where = "( subscriber_status='3' OR  subscriber_status =4 )";
            $this->db->where($where);
        }
        $this->db->where($conditions_array);
        if (isset($_POST['srch_email'])) {
           $srch = mysql_real_escape_string($this->input->post('srch_email'));
            $spacefind = preg_match('/\s/',$srch);
            if($spacefind > 0){
                
                $srchnew = (explode(" ",$srch));
                $where = "( res.subscriber_email_address LIKE '%$srch%' OR (res.subscriber_first_name LIKE '%$srchnew[0]%' AND res.subscriber_last_name LIKE '%$srchnew[1]%') OR res.subscriber_extra_fields LIKE '%$srch%')";           
            }
            else{
                $where = "( res.subscriber_email_address LIKE '%$srch%' OR res.subscriber_first_name LIKE '%$srch%' OR res.subscriber_last_name LIKE '%$srch%' OR res.subscriber_extra_fields LIKE '%$srch%')";           
            }
            $this->db->where($where);
        }

        if ($this->input->get_post('uncheck_list', true)) {
            $uncheck_id = explode(',', $this->input->get_post('uncheck_list', true));
            $this->db->where_not_in('res.subscriber_id', $uncheck_id);
        }
        $this->db->limit($rows_per_page, $start);
        if ((isset($_POST['order_by'])) && (isset($_POST['order_by_column']))) {
            $order_by_column = $_POST['order_by_column'];
            $order_by = $_POST['order_by'];
            $this->db->order_by('res.' . $order_by_column, $order_by);
        } else {
            //$this->db->order_by('res.subscriber_id');
        }

        $result = $this->db->get();
        foreach ($result->result_array() as $row) {
            $rows[] = $row;
        }
      
        $result->free_result();
        return $rows;
    }

    //Function to fetch site configuration data
    function get_site_configuration_data($conditions_array = array()) {
        $rows = array();
        $result = $this->db->get_where('red_site_configurations as rsc', $conditions_array);

        foreach ($result->result_array() as $row) {
            $rows[] = $row;
        }
        $result->free_result();
        return $rows;
    }

}

?>