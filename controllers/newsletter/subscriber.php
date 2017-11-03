<?php

/**
 * 	Controller class for subscribers
 * 	It have controller functions for subscribers management.
 */
class Subscriber extends CI_Controller {

    /**
     * 	Contructor for controller.
     * 	It checks user session and redirects user if not logged in
     */
    function __construct() {
        parent::__construct();
        $this->load->helper('cookie');

        # check via common model
        if (!$this->is_authorized->check_user())
            redirect('user/index');

        # Create user's folders
        $this->is_authorized->createUserFiles();

        $this->load->model('ConfigurationModel');
        $this->load->model('newsletter/Subscription_Model');
        $this->load->model('newsletter/Subscriber_Model');
        $this->load->model('newsletter/Emailreport_Model');
        $this->load->model('newsletter/contact_model');
        $this->load->model('Activity_Model');

        $this->load->library('upload'); // Load upload library class for uploading files
        $this->load->helper('notification');
        #Check if folder with modulo of User ID exists on server
        $user_dir = $this->session->userdata('member_id') % 1000;

        #Get absolute path for uploading
        $this->upload_path = $this->config->item('user_private') . $user_dir . '/' . $this->session->userdata('member_id');
        $this->output->enable_profiler(false);
        // Force SSL
        force_ssl();
    }

    /**
     * Function Index
     *
     * By default it calls display function.
     */
    function index() {
        $this->display();
    }

    /**
     * 	Function Create
     *
     * 	'Create' controller function to create new subscriber.
     * 	@param (int) (subscription_id)  for creating subscriber in subscription list according to subscription_id
     */
    function isImportInProgress() {
        $mid = $this->session->userdata('member_id');
        return $this->db->query("select `contact_import_progress` from `red_members` where `member_id` = '$mid'")->row()->contact_import_progress;
    }

    function create($subscription_id = 0) {
        //Check if user is not login then redirect to index page
        if ($this->session->userdata('member_id') == '')
            redirect('user/index');

        if (!is_numeric($subscription_id)) {
            $subscription_id = 0;
            echo "error:subscription id not exist";
            exit;
        }

        if ($this->isImportInProgress()) {
            echo "error: There is already a list import in progress. Please wait untill the import is fully completed, before uploading another list.";
            exit;
        }

        // To check form is submittted for copycsv
        if ($this->input->post('action') == 'copycsv') {
            // Validation rules are applied
            $this->form_validation->set_rules('copy_csv', 'Copy and Paste CSV', 'required');
            $this->form_validation->set_rules('terms_condition', 'Terms & Conditions', 'required');
            // To check form is validated
            if ($this->form_validation->run() == true) {
                $copy_csv = $this->input->post('copy_csv'); //csv submitted by user
                //$data=$copy_csv;
                $data = preg_split('/[\n\r]+/', $copy_csv); //Convert in to an array from comma seprated values
                $data_new = array(0 => "\n");
                $data_merge = array_merge($data_new, $data);
                $data = array();
                $data = $data_merge;
                //Check if folder with name of 'csv_files' exists on server
                if (!file_exists($this->upload_path . '/csv_files')) {
                    mkdir($this->upload_path . '/csv_files/', 0777);
                    chmod($this->upload_path . '/csv_files/', 0777);
                }
                $csv_file = $this->session->userdata('member_id') . '_' . date('YmdHis');
                $csvFilePath = $this->upload_path . '/csv_files/' . $csv_file;
                $fh = fopen($csvFilePath . ".csv", 'w') or die("can't open file");
                foreach ($data as $line) {
                    fputcsv($fh, explode(',', $line));
                }
                $memberid = $this->session->userdata('member_id');
                $sublistid = $subscription_id;
                $file_name = $csv_file;
                $list_type = 2;
                $command = config_item('php_path') . " " . FCFOLDER . "/index.php  newsletter/cronjob bgprocess $memberid $sublistid $file_name $list_type";
                $this->db->query("INSERT INTO red_activity_log (user_id, activity) VALUES (157,'" . $command . "')");
                if (base_url() != 'https://www.redcappi.dev/' and base_url() != 'http://www.redcappi.dev/') {
                    exec("$command > /dev/null &", $arrOutput);
                    $this->db->query("INSERT INTO red_activity_log (user_id, activity) VALUES (157,'" . $command . "')");
                } else {
                    exec("$command > null", $arrOutput);
                }

                //Display Success Message
                echo "copy_success:" . $subscription_id;
            }
        }
        // To check form is submittted for export csv
        if ($this->input->post('subscriber_csv_export_submit') == 'Export CSV') {
            $this->form_validation->set_rules('subscription_list', 'Subscription List', 'is_natural_no_zero');
            $this->form_validation->set_message('is_natural_no_zero', 'Subscription is Required Field');

            if ($this->form_validation->run() == true) {
                $this->exportcsv();
            }
        }

        // To check form is submittted for import
        if ($method == 'Import') {
            $this->form_validation->set_rules('subscriber_csv_file', 'Subscriber CSV Import', 'callback_validate_csv_upload');

            if ($this->form_validation->run() == true) {
                $subscriber_data['imported_subscribers'] = $this->imported_subscribers;
            }
        }
        // To check form is submittted for "One at time subscriber"
        if ($this->input->post('action') == 'submit') {
            if ($this->input->post('terms') == "true") {
                $this->form_validation->set_rules('terms_condition_save', 'Terms & Conditions', 'required');
            }
            //Apply validations if submitted subscribers are more than one
            $validation = false;
            for ($i = 0; $i < count($_POST[subscriber_email_address]); $i++) {
                $j = $i + 1; //Counter for submitted subscriber row
                if ($_POST[subscriber_email_address][$i] != '') {
                    //Apply validation rules
                    $validation = true;
                    $this->form_validation->set_rules('subscriber_email_address[' . $i . ']', 'Contact in row' . $j, 'required|valid_email|callback_email_check|trim');
                }
            }
            //Apply validations if submitted subscriber is only one
            if (!$validation) {
                //Apply validation rules
                $this->form_validation->set_rules('subscriber_email_address[0]', 'Subscriber Email Address ', 'required|valid_email|callback_email_check|trim');
            }
            // To check form is validated
            if ($this->form_validation->run() == true) {
                //Insert subscriber to subscriptions submitted by user.
                for ($i = 0; $i < count($_POST[subscriber_email_address]); $i++) {
                    $j = $i + 1;
                    // Retrieve data posted in form posted by user using input class
                    if ($_POST[subscriber_email_address][$i] != '') {
                        //To check email address exist in dtabase or not
                        if ($this->email_check($_POST[subscriber_email_address][$i])) {
                            //Collect submitted subscriber values by user in an array
                            $input_array['subscriber_email_address'] = mysql_real_escape_string($_POST[subscriber_email_address][$i]);
                            $arrEmailExploded = explode('@', $input_array['subscriber_email_address']);
                            $input_array['subscriber_email_domain'] = $arrEmailExploded[1];

                            if (trim($_POST['subscriber_first_name'][$i]) != '')
                                $input_array['subscriber_first_name'] = mysql_real_escape_string($_POST[subscriber_first_name][$i]);

                            if (trim($_POST['subscriber_last_name'][$i]) != '')
                                $input_array['subscriber_last_name'] = mysql_real_escape_string($_POST[subscriber_last_name][$i]);

                            //Collect Subscription id submitted by user
                            if (isset($_POST['subscription_contact_one'])) {
                                $subscription_id = $_POST['subscription_contact_one'];
                            }
                            //Collect subscription id in array
                            //$input_array['subscription_id']=$subscription_id;
                            //Create Subscriber
                            $qry = "INSERT INTO red_email_subscribers SET ";
                            $flds = '';
                            foreach ($input_array as $key => $val)
                                $flds .= $key . ' = \'' . mysql_real_escape_string($this->fixEncoding(trim($val))) . '\', ';
                            $flds .= 'subscriber_created_by = ' . $this->session->userdata('member_id');
                            $qry .= $flds . ' ON DUPLICATE KEY UPDATE ' . $flds . ', is_deleted = 0,subscriber_status=1 , subscriber_id=LAST_INSERT_ID(subscriber_id);';

                            $this->db->query($qry);
                            unset($input_array);
                            $last_inserted_id = $this->db->insert_id();
                            //Create subscriber for "All Contact List"
                            if ('-' . $this->session->userdata('member_id') != $subscription_id) {
                                $input_array = array('subscriber_id' => $last_inserted_id, 'subscription_id' => $subscription_id);
                                $this->Subscriber_Model->replace_subscription_subscriber($input_array);
                                unset($input_array);
                            }
                        } else {
                            //If email address already in database then print error message
                            echo 'error:Email Address in row' . $j . ' already exists';
                            die;
                        }
                    }
                }
                // Load log configuration model class which handles database interaction
                $config_arr = $this->ConfigurationModel->get_site_configuration_data(array('config_name' => 'max_contacts_to_unauthenticate'));
                $max_contacts_to_unauthenticate = $config_arr[0]['config_value'];
                $this->load->model('UserModel');
                //Fetch User Data from database
                $user_data = $this->UserModel->get_user_data(array('member_id' => $this->session->userdata('member_id')));
                $unauthentic_contacts = $user_data[0]['unauthentic_contacts'];
                $total_unauthentic_contacts = $unauthentic_contacts + $j;
                if (($total_unauthentic_contacts) > $max_contacts_to_unauthenticate) {
                    // update user info
                    $this->UserModel->update_user(array('unauthentic_contacts' => $total_unauthentic_contacts, 'is_authentic' => 0, 'is_automatic_segmentation' => 0), array('member_id' => $this->session->userdata('member_id')));
                    // Admin notification starts
                    $this->load->helper('admin_notification');
                    $mid = $this->session->userdata('member_id');
                    $mname = $this->session->userdata('member_username');
                    $message = "<p>Hello admin,</p><p>RC Member :$mname [$mid] is unauthenticated after contacts import</p><p>Regards,<br />Redcappi Team</p>";
                    $text_message = "RC Member :$mname [$mid] is unauthenticated after contacts import";

                    $to = $this->db->query('SELECT config_value FROM `red_site_configurations` where `config_name` = "admin_notification_email"')->row()->config_value;
                    admin_notification_send_email($to, SYSTEM_EMAIL_FROM, 'RedCappi', 'User unauthenticated', $message, $text_message);
                    // Admin notification ends
                } else {
                    // update user info
                    $this->UserModel->update_user(array('unauthentic_contacts' => $total_unauthentic_contacts), array('member_id' => $this->session->userdata('member_id')));
                }
                // create array for insert values in activty table
                $values = array('user_id' => $this->session->userdata('member_id'), 'activity' => 'contact_add', 'number_of_contacts' => $j, 'contact_list_type' => 1);
                $this->Activity_Model->create_activity($values);
                // Load log configuration model class which handles database interaction
                $config_arr = $this->ConfigurationModel->get_site_configuration_data(array('config_name' => 'maximum_add_contact'));
                $maximum_add_contact = $config_arr[0]['config_value'];
                if ($j > $maximum_add_contact) {
                    $this->contact_notification($j, $maximum_add_contact, 'added');
                }
                // Assign success message by message class
                $this->messages->add('Subscriber created successfully', 'success');
                echo "success:" . $subscription_id;
            }
        }
        // If validation errors in submitted values by user then print validation errors
        if (validation_errors()) {
            echo 'error:' . validation_errors();
        }
    }

    function fixEncoding($in_str) {
        $cur_encoding = mb_detect_encoding($in_str);
        if ($cur_encoding == "UTF-8" && mb_check_encoding($in_str, "UTF-8")) {
            return $in_str;
        } else {
            return utf8_encode($in_str);
        }
    }

    /**
     * 	Function Subscriber_create
     *
     * 	'subscriber_create' controller function to create new subscriber using popup window
     * 	@param (int) (subscription_id)  for creating subscriber in subscription list according to subscription_id
     */
    function subscriber_create($subscription_id = 0, $scroll = 0, $selected_subscription_id = 0, $page_link = 0, $action = "", $action_notmail = "", $search = "") {
        //Check if user is not login then redirect to index page
        if ($this->session->userdata('member_id') == '')
            redirect('user/index');

        //	Collect subscription id
        //Protecting MySQL from query string sql injection Attacks
        if (is_numeric($subscription_id)) {
            $subscription_id = $subscription_id;
        } else {
            $subscription_id = 0;
            echo "error:subscription id not exist";
            exit;
        }
        //Check form submitted or not
        if ($this->input->post('action') == 'save') {
            // Validation rules are applied
            $this->form_validation->set_rules('subscriber_email_address', 'Email', 'required|valid_email|callback_email_check|trim');
            $this->form_validation->set_rules('terms_condition', 'Terms & Conditions', 'required');
            // To check form is validated
            if ($this->form_validation->run()) {
                //create array for submit subscriber into database
                $signup_data = array();
                // create array for collecting extra fields name and value
                $serialize_data = array();
                foreach ($_POST as $k => $v) {
                    if (($k != 'action') && ($k != 'terms_condition') && ($k != 'subscription_id') && ($k != 'custome_fld')) {
                        //if submit value contain email address of subscriber
                        if ($k == 'subscriber_email_address') {
                            $signup_data['subscriber_email_address'] = $this->input->post('subscriber_email_address', true);
                            $arrEmailExploded = explode('@', $signup_data['subscriber_email_address']);
                            $signup_data['subscriber_email_domain'] = $arrEmailExploded[1];
                        } else if ($k == 'subscriber_first_name') {
                            //if submit value contain first name of subscriber
                            $signup_data['subscriber_first_name'] = $this->input->post('subscriber_first_name', true);
                        } else if ($k == 'subscriber_last_name') {
                            //if submit value contain last name of subscriber
                            $signup_data['subscriber_last_name'] = $this->input->post('subscriber_last_name', true);
                        } else if ($k == 'address') {
                            //If submit value is last name then collect last name in array
                            $signup_data['subscriber_address'] = $this->input->post('address', true);
                        } else if ($k == 'birthday') {
                            //If submit value is last name then collect last name in array
                            $signup_data['subscriber_dob'] = $this->input->post('birthday', true);
                        } else if ($k == 'city') {
                            //If submit value is last name then collect last name in array
                            $signup_data['subscriber_city'] = $this->input->post('city', true);
                        } else if ($k == 'company') {
                            //If submit value is last name then collect last name in array
                            $signup_data['subscriber_company'] = $this->input->post('company', true);
                        } else if ($k == 'country') {
                            //If submit value is last name then collect last name in array
                            $signup_data['subscriber_country'] = $this->input->post('country', true);
                        } else if ($k == 'phone') {
                            //If submit value is last name then collect last name in array
                            $signup_data['subscriber_phone'] = $this->input->post('phone', true);
                        } else if ($k == 'state') {
                            //If submit value is last name then collect last name in array
                            $signup_data['subscriber_state'] = $this->input->post('state', true);
                        } else if ($k == 'zip_code') {
                            //If submit value is last name then collect last name in array
                            $signup_data['subscriber_zip_code'] = $this->input->post('zip_code', true);
                        } else {
                            //else collect extra fields of subscriber
                            $v = trim($v);
                            if ($v != "")
                                $serialize_data[$k] = $v;
                        }
                    }
                }
                if (count($serialize_data) > 0) {
                    $signup_data['subscriber_extra_fields'] = serialize($serialize_data); //serialize array of extra fields
                }


                //create subscriber
                $qry = "INSERT INTO red_email_subscribers SET ";
                $flds = '';
                foreach ($signup_data as $key => $val)
                    $flds .= $key . ' = \'' . mysql_real_escape_string($val) . '\', ';
                $flds .= 'subscriber_created_by = ' . $this->session->userdata('member_id');
                $qry .= $flds . ' ON DUPLICATE KEY UPDATE ' . $flds . ', is_deleted = 0,subscriber_status=1 , subscriber_id=LAST_INSERT_ID(subscriber_id)';

                $this->db->query($qry);
                $last_inserted_id = $this->db->insert_id();
                if ($subscription_id > 0) {
                    $input_array = array('subscriber_id' => $last_inserted_id, 'subscription_id' => $subscription_id);
                    $this->Subscriber_Model->replace_subscription_subscriber($input_array);
                }
                #############################
                # create activity log		#
                #############################
                # create array for insert values in activty table
                $values = array('user_id' => $this->session->userdata('member_id'),
                    'activity' => 'contact_add',
                    'number_of_contacts' => 1,
                    'contact_list_type' => 1
                );
                $this->Activity_Model->create_activity($values);
                //	print success message
                echo "success:Subscriber created successfully";
            } else {
                // print validation errors
                echo "error:" . validation_errors();
            }
        } else {
            $previous_page_url = base_url() . "newsletter/contacts/index/" . $selected_subscription_id . "/" . $scroll . "/" . $action_notmail . "/" . $action;
            if ($search != "")
                $previous_page_url .= "/" . $search;
            if ($page_link != 0)
                $previous_page_url .= "#" . $page_link;

            //Loads  subscriber  view.
            $this->load->view('header', array('title' => 'Create Subscriber', 'previous_page_url' => $previous_page_url));
            $this->load->view('contacts/subscriber_create', array('subscription_id' => $subscription_id, 'previous_page_url' => $previous_page_url));
            $this->load->view('footer-inner-red');
        }
    }

    /**
     * 	Function Email_check
     *
     * 	'email_check' controller function to check if email already exists in database before updating database
     * 	by input from user.
     * 	@param (string) (email)  contains email address of subscriber submitted by user
     */
    function email_check($email = "") {

        /*
          Creating array of conditions to checked with database with conditions.
          To check if Email entered by user already exists in database.
          To check if ID is not as current subscriber in case of update

         */

        $conditions_array['subscriber_email_address'] = $email;
        $conditions_array['subscriber_created_by'] = $this->session->userdata('member_id');
        //$conditions_array['res.subscription_id']=$this->input->get_post('subscription_id', TRUE);
        if (isset($_POST['subscriber_id'])) {
            $conditions_array['subscriber_id !='] = $_POST['subscriber_id'];
        }
        if (isset($_POST['subscription_contact_one'])) {
            //$subscription_id=$_POST['subscription_contact_one'];
            $conditions_array['subscription_id'] = $_POST['subscription_id'];
        }
        $conditions_array['res.is_deleted'] = 0;
        $subscriber_array = $this->Subscriber_Model->get_subscriber_data($conditions_array);

        // returns true if email exits and false if not exits.
        if (count($subscriber_array) > 0) {
            //	set validation message
            if ($subscriber_array[0]['subscriber_status'] == 1) {
                $this->form_validation->set_message('email_check', '%s already exists in your account.');
            } else {
                $this->form_validation->set_message('email_check', '%s already exists in the Do Not Mail List of your account.');
            }
            return FALSE;
        } else {
            // check bounce email
            $subscriber_bounce_array = $this->Subscriber_Model->get_subscriber_data($conditions_array, 10, 0, true);
            if (count($subscriber_bounce_array) > 0) {
                $this->form_validation->set_message('email_check', '%s already exists in the Bounce List of your account.');
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }

    /**
     * 	Function Edit
     *
     * 	'Edit' controller function to edit existing subscriber.
     *
     * 	@param (int) (subscriber_id)  contains subcriber id which is used for edit the subscriber data
     */
    function edit($subscriber_id = 0, $subscription_id = 0) {
        //Check if user is not login then redirect to index page
        if ($this->session->userdata('member_id') == '')
            redirect('user/index');

        //	Collect subscriber id
        //Protecting MySQL from query string sql injection Attacks
        if (is_numeric($subscriber_id)) {
            $id = $subscriber_id;
        } else {
            $id = 0;
            echo "error:subscriber id not exist";
            exit;
        }

        // Validation rules are applied
        $this->form_validation->set_rules('subscriber_email_address', 'Email', 'required|valid_email|callback_email_check|trim');
        // To check form is validated
        if ($this->form_validation->run()) {
            //create array for update subscriber into database
            $signup_data = array();
            $signup_data['subscriber_address'] = "";
            $signup_data['subscriber_dob'] = "";
            $signup_data['subscriber_city'] = "";
            $signup_data['subscriber_company'] = "";
            $signup_data['subscriber_country'] = "";
            $signup_data['subscriber_phone'] = "";
            $signup_data['subscriber_state'] = "";
            $signup_data['subscriber_zip_code'] = "";
            // create array for collecting extra fields name and value
            foreach ($_POST as $k => $v) {
                if (($k != 'subscriber_id') && ($k != 'subscription_id') && ($k != 'custome_fld')) {
                    if ($k == 'subscriber_email_address') {
                        //If submit value is email address then collect email address in array
                        $signup_data['subscriber_email_address'] = $this->input->post('subscriber_email_address', true);
                    } else if ($k == 'subscriber_first_name') {
                        //If submit value is first name then collect first name in array
                        $signup_data['subscriber_first_name'] = $this->input->post('subscriber_first_name', true);
                    } else if ($k == 'subscriber_last_name') {
                        //If submit value is last name then collect last name in array
                        $signup_data['subscriber_last_name'] = $this->input->post('subscriber_last_name', true);
                    } else if ($k == 'name') {
                        //If submit value is  name then collect  name in array
                        $signup_data['subscriber_name'] = $this->input->post('name', true);
                    } else if ($k == 'address') {
                        //If submit value is last name then collect last name in array
                        $signup_data['subscriber_address'] = $this->input->post('address', true);
                    } else if ($k == 'birthday') {
                        //If submit value is last name then collect last name in array
                        $signup_data['subscriber_dob'] = $this->input->post('birthday', true);
                    } else if ($k == 'city') {
                        //If submit value is last name then collect last name in array
                        $signup_data['subscriber_city'] = $this->input->post('city', true);
                    } else if ($k == 'company') {
                        //If submit value is last name then collect last name in array
                        $signup_data['subscriber_company'] = $this->input->post('company', true);
                    } else if ($k == 'country') {
                        //If submit value is last name then collect last name in array
                        $signup_data['subscriber_country'] = $this->input->post('country', true);
                    } else if ($k == 'phone') {
                        //If submit value is last name then collect last name in array
                        $signup_data['subscriber_phone'] = $this->input->post('phone', true);
                    } else if ($k == 'state') {
                        //If submit value is last name then collect last name in array
                        $signup_data['subscriber_state'] = $this->input->post('state', true);
                    } else if ($k == 'zip_code') {
                        //If submit value is last name then collect last name in array
                        $signup_data['subscriber_zip_code'] = $this->input->post('zip_code', true);
                    } else {
                        //else collect extra fields of contact
                        $k = trim($k);
                        if (($k != "") && (isset($_POST['custom_' . $k]))) {
                            $custom = $this->input->post('custom_' . $k, true);
                            if ($this->input->post('custom_' . $k, true) != "")
                                $serialize_data[$custom] = $v;
                        }
                    }
                }
            }
            if (count($serialize_data) > 0) {
                $signup_data['subscriber_extra_fields'] = serialize($serialize_data); //serialize array of extra fields
            } else {
                $signup_data['subscriber_extra_fields'] = "";
            }
            $signup_data['subscrber_bounce'] = 0;
            $signup_data['soft_bounce'] = 0;
            $signup_data['subscriber_status'] = 1;


            // Update subscriber
            $this->Subscriber_Model->update_subscriber($signup_data, array('subscriber_id' => $id, 'subscriber_created_by' => $this->session->userdata('member_id')));
            // print success message
            echo "success:Contact updated successfully";
        } else {
            // print validation errors
            echo "error:" . validation_errors();
        }
    }

    /**
     * 	Function View
     *
     * 	'View' controller function to view existing subscriber.
     *
     * 	@param (int) (subscriber_id)  contains subcriber id which is used for view the subscriber data
     */
    function ajaxHistory($sid, $contact_soft_bounce, $contact_bounce_status, $p = 0) {
        $site_configuration_array = $this->ConfigurationModel->get_site_configuration_data(array('config_name' => 'max_soft_bounce'));
        $max_soft_bounce = $site_configuration_array[0]['config_value'];
        // Fetch Email Report
        $psize = 5;
        if ($p < 1)
            $startfrom = 0;
        else
            $startfrom = ($p) * $psize;

        $email_report = $this->Emailreport_Model->get_emailreport_campaign_data(array('ret.subscriber_id' => $sid, 'campaign_created_by' => $this->session->userdata('member_id'), 'ret.email_sent' => 1), $psize, $startfrom);
        $soft_bounce = $contact_soft_bounce;
        if (count($email_report) > 0) {
            foreach ($email_report as $key => $campaign_report) {
                $fetch_condiotions_array = array('campaign_id' => $campaign_report['campaign_id'], 'counter >' => 0, 'subscriber_id' => $sid);

                // Fetches subscriber data from database
                $emailclickreport_data[$campaign_report['campaign_id']] = $this->Emailreport_Model->get_emailreport_click($fetch_condiotions_array);
                # Count clicks of all url
                $counter = 0;
                foreach ($emailclickreport_data[$campaign_report['campaign_id']] as $click) {
                    $counter+=$click['cnt'];
                }
                $email_report[$key]['clicks'] = $counter;
                if (($contact_bounce_status == 1) && ($contact_soft_bounce > $max_soft_bounce)) {
                    $email_report[$key]['soft_bounce_status'] = $soft_bounce;
                    $soft_bounce = $contact_soft_bounce--;
                }
            }
            $subscriptions = $this->Subscriber_Model->get_subscriber_info_view(array('res.subscriber_id' => $id, 'res.subscriber_created_by' => $this->session->userdata('member_id'), 'res.is_deleted' => 0));
            $shorten_url = get_shorten_url();

            $result = $this->load->view('contacts/contact_history', array('subscriptions' => $subscriptions, 'email_report' => $email_report, 'shorten_url' => $shorten_url, 'max_soft_bounce' => $max_soft_bounce, 'email_report_click' => $emailclickreport_data, 'max_soft_bounce' => $max_soft_bounce), true);
        } else {
            $result = '';
        }
        if ($p > 0) {
            echo $result;
        } else {
            return $result;
        }
    }

    function view($subscriber_id, $scroll = 0, $selected_subscription_id = 0, $page_link = 0, $action = "", $action_notmail = "", $search = "") {
        //Check if user is not login then redirect to index page
        if ($this->session->userdata('member_id') == '')
            redirect('user/index');



        $site_configuration_array = $this->ConfigurationModel->get_site_configuration_data(array('config_name' => 'max_soft_bounce'));
        $max_soft_bounce = $site_configuration_array[0]['config_value'];


        //	Collect subscriber id
        //Protecting MySQL from query string sql injection Attacks
        if (is_numeric($subscriber_id)) {
            $id = $subscriber_id;
        } else {
            echo "error:subscriber does not exist";
            exit;
        }
        //Fetch subscriber data from database by subscriber ID
        $subscriptions = $this->Subscriber_Model->get_subscriber_info_view(array('res.subscriber_id' => $id, 'res.subscriber_created_by' => $this->session->userdata('member_id'), 'res.is_deleted' => 0));

        if (count($subscriptions) > 0) {
            if ($subscriptions[0]['subscriber_name'] != "") {
                $name_arr = explode(" ", $subscriptions[0]['subscriber_name']);
                $subscriptions[0]['subscriber_first_name'] = ($subscriptions[0]['subscriber_first_name'] != "") ? $subscriptions[0]['subscriber_first_name'] : $name_arr[0];
                $subscriptions[0]['subscriber_last_name'] = ($subscriptions[0]['subscriber_last_name'] != "") ? $subscriptions[0]['subscriber_last_name'] : $name_arr[1];
                unset($subscriptions[0]['subscriber_name']);
            }
            // Fetch Subscription list
            $subscriptions['list'] = $this->Subscription_Model->get_subscription_list(array('ress.subscriber_id' => $id, 'res.is_deleted' => 0));
            $subscription_title = array();
            $i = 0;
            foreach ($subscriptions['list'] as $subscription) {
                if ($subscription['subscription_title'] == "All My Contacts") {
                    $subscription_title[0] = $subscription['subscription_title'];
                } else {
                    if ($i == 0) {
                        $j = $i + 1;
                        $i++;
                    } else {
                        $j = $i;
                    }
                    $subscription_title[$j] = $subscription['subscription_title'];
                }

                $i++;
            }

            $subscription_title[0] = "All My Contacts";
            ksort($subscription_title);
            $result = array_unique($subscription_title);
            $subscription_title = array();
            $subscription_title = $result;


            // get Email Report/History
            $strHistory = $this->ajaxHistory($subscriptions[0]['subscriber_id'], $subscriptions[0]['soft_bounce'], $subscriptions[0]['subscrber_bounce']);
        } else {
            $previous_page_url = base_url() . "newsletter/contacts/index/" . $selected_subscription_id . "/" . $scroll . "/" . $action_notmail . "/";
            if ($page_link != 0)
                $previous_page_url .= $action;
            if ($search != "")
                $previous_page_url .= "/" . $search;
            redirect($previous_page_url);
        }
        #Get shoreten url
        $shorten_url = get_shorten_url();

        //Loads  subscriber  view.
        $this->load->view('header', array('title' => 'Subscriber View', 'previous_page_url' => $previous_page_url));
        $this->load->view('contacts/subscriber_view', array('subscriptions' => $subscriptions, 'contact_soft_bounce' => $subscriptions[0]['soft_bounce'], 'contact_bounce_status' => $subscriptions[0]['subscrber_bounce'], 'subscription_title' => $subscription_title, 'shorten_url' => $shorten_url, 'max_soft_bounce' => $max_soft_bounce, 'contact_history' => $strHistory));
        $this->load->view('footer-inner-red');
    }

    /**
     * 	Function Subscriber_list
     *
     * 	'Subscriber_list' controller function for listing of subscribers according to subscription id.
     *
     * 	@param (int) (id)  contains subscription id which is used for listing of subscribers data according
     * 	to subscription id
     *
     * 	@param (int) (start)  Calculate the start numbers. These determine which number to start the pagination links
     *
     * 	@param (string) (type)  Calaculate Subscriber list will be according to "Select List" link or to "Select Page" link
     */
    function subscriber_list($subscription_id = 0, $start = 0, $type = "") {

        $id = (is_numeric($subscription_id)) ? $subscription_id : 0;

        if ($id == 0) {
            echo "error:subscription id not exist";
            exit;
        }

        // Recieve any messages to be shown, when subscriber is added or updated
        $messages = $this->messages->get();

        //$subscriptions=$this->Subscription_Model->get_subscription_data(array('subscription_id'=>$id,'is_deleted'=>0,'subscription_created_by'=>$this->session->userdata('member_id')));
        $subscriptions = $id;
        if ($this->input->get_post('unsubscribe', true) == 5) {
            $fetch_condiotions_array = array('res.is_deleted' => 0, 'res.subscriber_created_by' => $this->session->userdata('member_id'));
            $id = '-' . $this->session->userdata('member_id');
        } elseif ($this->input->get_post('unsubscribe', true) == 1) {
            // Collect condition array for fetch subscribers from database
            $fetch_condiotions_array = array('res.is_deleted' => 0, 'res.subscriber_created_by' => $this->session->userdata('member_id'));
            //'(res.subscriber_status' => '1 or res.subscriber_status =5)',
            $id = '-' . $this->session->userdata('member_id');
        } elseif ($this->input->get_post('complaints', true) == 2) {
            $fetch_condiotions_array = array('res.subscriber_created_by' => $this->session->userdata('member_id'), 'res.subscriber_status' => 2, 'res.is_deleted' => 0);
            $id = '-' . $this->session->userdata('member_id');
        } elseif ($this->input->get_post('bounce', true) == 1) {
            $fetch_condiotions_array = array('res.subscriber_created_by' => $this->session->userdata('member_id'), 'res.is_deleted' => 0);
        } else {
            $fetch_condiotions_array = array('res.subscriber_created_by' => $this->session->userdata('member_id'), 'res.subscriber_status' => 1, 'res.is_deleted' => 0);

            if ($this->input->get_post('subscription_list', true) != '' && $this->input->get_post('subscription_list', true) > 0) {
                $fetch_condiotions_array['res.subscription_id'] = $this->input->get_post('subscription_list', true);
            }
            // Collect condition array for subscription "All Contact List"
            if (($id > 0) && (!($this->input->get_post('bounce'))) && (!($this->input->get_post('unsubscribe'))) && (!($this->input->get_post('complaints')))) {
                $fetch_condiotions_array['ress.subscription_id'] = $id;
            }
        }
        // Define config parameters for paging like base url, total rows and record per page.
        $config['base_url'] = base_url() . 'newsletter/subscriber/subscriber_list/' . $id; // The page we are linking to

        if ($id > 0) {
            if ($this->input->get_post('bounce', true) == 1) {
                $config['total_rows'] = $this->Subscriber_Model->get_subscriber_count($fetch_condiotions_array, true);     // Total number of items (database results)
            } else {
                $config['total_rows'] = $this->Subscriber_Model->get_subscription_subscriber_count($fetch_condiotions_array); // Total number of items (database results)				
                //$config['total_rows']=$this->contact_model->get_contacts_count_in_list($fetch_condiotions_array,$id);	// Total number of items (database results)				
            }
        } else {
            if ($this->input->get_post('bounce', true) == 1) {
                $config['total_rows'] = $this->Subscriber_Model->get_subscriber_count($fetch_condiotions_array, true); // Total number of items (database results)
            } elseif ($this->input->get_post('unsubscribe', true) == 5) {
                $unsubscribe_where = "subscriber_created_by ='" . $this->session->userdata('member_id') . "' and is_deleted=0 and subscriber_status=5";
                if (trim($this->input->post('srch_email', true)) != '')
                    $unsubscribe_where .= " and subscriber_email_address like'%" . trim($this->input->post('srch_email', true)) . "%'";
                $config['total_rows'] = $this->Subscriber_Model->get_unsubscribed_subscriber_count($unsubscribe_where);
            }elseif ($this->input->get_post('unsubscribe', true) == 1) {
                $unsubscribe_where = "subscriber_created_by ='" . $this->session->userdata('member_id') . "' and is_deleted=0 and subscriber_status=0";
                if (trim($this->input->post('srch_email', true)) != '')
                    $unsubscribe_where .= " and subscriber_email_address like'%" . trim($this->input->post('srch_email', true)) . "%'";
                $config['total_rows'] = $this->Subscriber_Model->get_unsubscribed_subscriber_count($unsubscribe_where);
            }else {
                if (count($subscriptions) > 0) {
                    $config['total_rows'] = $this->Subscriber_Model->get_subscriber_count($fetch_condiotions_array); // Total number of items (database results)
                }
            }
        }

        // if user fetch data according to "Select Page" link otherwise according to "Select List" link
        if ($type) {
            $config['per_page'] = $config['total_rows']; // Max number of items you want shown per page
        } else {
            if ($this->session->userdata('ps') > 0)
                $config['per_page'] = $this->session->userdata('ps');
            else
                $config['per_page'] = 25; //Default 25 recods per page
        }

        $config['uri_segment'] = 5;
        $config['num_links'] = 4; // Number of "digit" links to show before/after the currently viewed page
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['cur_tag_open'] = '<li><a class="selected">';
        $config['cur_tag_close'] = '</a></li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';

        // Initialize paging with above config parameters
        $this->pagination->initialize($config);
        if ($start == 1) {
            $start = 0;
        }

        //Create paging inks
        $subscriber_data['links'] = $this->pagination->create_links();


        /*
         * 	check subscription id is equal to login memeber id  if true then Fetch data for "All Contact" subscription list
         */


        if ($id == "-" . $this->session->userdata('member_id')) {
            if ($this->input->get_post('bounce', true) == 1) {
                $subscriber_data['subscribers'] = $this->Subscriber_Model->get_subscriber_data($fetch_condiotions_array, $config['per_page'], $start, true);
            } else {
                $subscriber_data['subscribers'] = $this->Subscriber_Model->get_subscriber_data($fetch_condiotions_array, $config['per_page'], $start);
            }
        } else {
            if ($this->input->get_post('bounce', true) == 1) {
                $subscriber_data['subscribers'] = $this->Subscriber_Model->get_subscriber_data($fetch_condiotions_array, $config['per_page'], $start, true);
            } elseif ($this->input->get_post('unsubscribe', true) == 5) {
                $subscriber_data['subscribers'] = $this->Subscriber_Model->get_subscriber_data($fetch_condiotions_array, $config['per_page'], $start);
            } elseif ($this->input->get_post('unsubscribe', true) == 1) {
                $subscriber_data['subscribers'] = $this->Subscriber_Model->get_subscriber_data($fetch_condiotions_array, $config['per_page'], $start);
            } elseif ($this->input->get_post('complaints', true) == 2) {
                $subscriber_data['subscribers'] = $this->Subscriber_Model->get_subscriber_data($fetch_condiotions_array, $config['per_page'], $start);
            } else {
                if ($subscriptions > 0) {
                    $subscriber_data['subscribers'] = $this->Subscriber_Model->get_subscription_subscriber_data($fetch_condiotions_array, $config['per_page'], $start);
                }
            }
        }

        //Assign messages to array to be send to view.
        $subscriber_data['messages'] = $messages;

        // Fetch total number of removed contacts
        $removed_where = "subscriber_created_by ='" . $this->session->userdata('member_id') . "' and is_deleted=0 and subscriber_status=5 ";
        if (trim($this->input->post('srch_email', true)) != '')
            $removed_where .= " and subscriber_email_address like'%" . trim($this->input->post('srch_email', true)) . "%'";
        $removed_count = $this->Subscriber_Model->get_unsubscribed_subscriber_count($removed_where);

        // Fetch total number of unsubscribe contacts
        $unsubscribe_where = "subscriber_created_by ='" . $this->session->userdata('member_id') . "' and is_deleted=0 and subscriber_status=0 ";
        if (trim($this->input->post('srch_email', true)) != '')
            $unsubscribe_where .= " and subscriber_email_address like'%" . trim($this->input->post('srch_email', true)) . "%'";
        $unsubscriber_count = $this->Subscriber_Model->get_unsubscribed_subscriber_count($unsubscribe_where);

        // Fetch total number of compliant contacts
        $fetch_condiotions_array = array('res.subscriber_created_by' => $this->session->userdata('member_id'), 'res.subscriber_status' => 2, 'res.is_deleted' => 0);
        $complaint_count = $this->Subscriber_Model->get_subscriber_count($fetch_condiotions_array);

        // Fetch total number of Bounce contacts
        $fetch_condiotions_array = array('res.subscriber_created_by' => $this->session->userdata('member_id'), 'res.is_deleted' => 0);
        $bounce_count = $this->Subscriber_Model->get_subscriber_count($fetch_condiotions_array, true);
        /*
          check subscriber exist in database or not If subscribers exist then Collect subscriber list for displaying in subscriber view using ajax
         */

        if (count($subscriber_data['subscribers'])) {
            $contact_list = "
			<tr class='contacts_change'>
							<th width=\"4%\"></th>
							<th width=\"30%\" class='contact_change_td' ><a href='javascript:void(0);' onclick='order_by(\"subscriber_email_address\")'>Email Address</a></th>
                                                                                                                        <th width=\"22%\" class='contact_change_td' ><a href='javascript:void(0);' onclick='order_by(\"last_read_date\")'>Last Read (GMT)</a></th>
							<th width=\"29%\" class='contact_change_td'><a href='javascript:void(0);' onclick='order_by(\"subscriber_first_name\")'>Name<a/></th>
							<th width=\"15%\"></th>
						  </tr>";

            // Check if empty contacts
            $emtpy = "";
            //	Collect each Subscriber info
            foreach ($subscriber_data['subscribers'] as $subscriber) {
                if ($subscriber['subscriber_name'] != "") {
                    $name_arr = explode(" ", stripslashes($subscriber['subscriber_name']));
                    if ($subscriber['subscriber_first_name'] == "") {
                        $subscriber['subscriber_first_name'] = $name_arr[0];
                    }
                    if ($subscriber['subscriber_last_name'] == "") {
                        $subscriber['subscriber_last_name'] = $name_arr[1];
                    }
                }
                if (!$subscriber['subscription_id']) {
                    $subscriber['subscription_id'] = "-" . $this->session->userdata('member_id');
                }
                //Check status of subscriber
                if ($subscriber['subscriber_status'] == 1) {
                    $status = "Subscribed";
                } else {
                    $status = "Unsubscribed";
                }
                if ($start == 0) {
                    $start = 1;
                }
                if (($_POST['action'] == "page") || ($_POST['action'] == "list")) {
                    $checked = "checked";
                }
                if ($_POST['action'] == "page") {
                    $disabeld = " disabled true";
                }
                $contact_list.='<tr class="contacts_change " id="subscriber_tr_' . $subscriber['subscriber_id'] . '">
								<td><input  class="check-boxalign" type="checkbox"  value="' . $subscriber['subscriber_id'] . '" name="subscriber_id[]" id="subscriber_id" onclick="change_color_td(this);" ' . $checked . $disabeld . '></td>
								<td class="contact_change_td"><h4><a class="subscriber_email update_subscriber" title="' . $subscriber['subscriber_email_address'] . '" href="' . site_url('newsletter/subscriber/view/' . $subscriber['subscriber_id']) . '">' . substr($subscriber['subscriber_email_address'], 0, 50) . '</a></h4></td>
<td class="contact_change_td"><h4><a class="subscriber_email update_subscriber" title="' . $subscriber['last_read_date'] . '" href="' . site_url('newsletter/subscriber/view/' . $subscriber['subscriber_id']) . '">' . $subscriber['last_read_date']. '</a></h4></td>								
<td class="subscriber_firstname"  title="' . stripslashes($subscriber['subscriber_first_name']) . '">' . substr(stripslashes($subscriber['subscriber_first_name']), 0, 25) . ' ' . substr(stripslashes($subscriber['subscriber_last_name']), 0, 25) . '</td>
                                                                                                                                         
								<td class="contact_change_td"><ul class="list-icons contacts mini"><li><a href="' . site_url('newsletter/subscriber/subscriber_delete/' . $subscriber['subscription_id'] . '/' . $subscriber['subscriber_id']) . '" class="fancybox_delete btn cancel delete_contact" name="' . $subscriber['subscriber_id'] . '"><img class="campion_send" src="'.$this->config->item('webappassets').'images/new_png_design/Email-Icon-Trash.png?v=6-20-13" alt="campaigns"/></a></li></ul></td>
							</tr>'; //Collect html for each subscriber
            }
            //$contact_list.="<tr class='contacts_change'><th colspan=\"4\" ><a href=\"javascript:void(0);\" class=\"export_csv btn cancel\"><img src=\"".base_url()."webappassets/images/table-export.png?v=6-20-13\" alt=\"\" align=\"absbottom\"> Export</a></th></tr>";
            for ($i = 25; $i <= 100; $i = $i * 2) {
                if ($config['per_page'] == $i)
                    $opt .= "<option value='$i' selected>$i</option>";
                else
                    $opt .= "<option value='$i'>$i</option>";
            }
            $contact_list.="<tr class='contacts_change'>
												<th colspan=\"4\" >
													<a href=\"javascript:void(0);\" class=\"export_csv btn cancel\">
														<img class='campion_send' src=\"" . base_url() . "webappassets/images/new_png_design/Export-Icon.png?v=6-20-13\" alt=\"\" align=\"absbottom\"> Export</a>
													<div class='view-pagesize'>
														<strong>View:</strong>
														<select id='psize' onchange=\"javascript:newPageSize(this);\">
															{$opt}
														</select>
													</div>
												</th>
											</tr>";
        }else {
            // if subscribers list is empty then display "No record found" message
            $emtpy = "empty";

            $totContactInAccount = $this->contact_model->get_contacts_count_in_list(array('subscriber_created_by' => $this->session->userdata('member_id'), 'subscriber_status' => 1, 'is_deleted' => 0), $subscription_id);
            if ($totContactInAccount > 0) {
                $contact_list.='<tr class="contacts_change">
			<td colspan="4"><div class="empty" style="height: 400px">
            <p style="padding-top: 100px">No Records Found.</p>            
          </div></td></tr>';
            } else {
                $contact_list.='<tr class="contacts_change">
			<td colspan="4"><div class="empty" style="height: 400px">
            <p style="padding-top: 100px">No Records Found. <span class="empty-add">To begin adding contacts click on "Add Contacts".</span></p>
            <a class="btn add" href="/newsletter/contacts_add"><i class="icon-plus"></i>Add Contacts</a>
          </div></td></tr>';
            }
        }
        $contact_list = '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="list mini ' . $emtpy . '">' . $contact_list . '</table> ';

        echo $subscriber_data['links'] . "|" . $contact_list . '|' . $unsubscriber_count . "|" . $bounce_count . "|" . $complaint_count . "|" . $config['total_rows'] . "|" . $removed_count;
    }

    /**
     * Function Delete
     *
     * 'Delete' controller function to Delete existing subscriber.
     *
     * @param (int) (id)  contains subscriber_id which is used for delete the subscriber from database
     */
    function delete($subscriber_id = 0) {
        //Check if user is not login then redirect to index page
        if ($this->session->userdata('member_id') == '')
            redirect('user/index');

        //	Collect subscriber id
        //Protecting MySQL from query string sql injection Attacks
        if (is_numeric($subscriber_id)) {
            $id = $subscriber_id;
        } else {
            $id = 0;
            echo "error:subscriber id not exist";
            exit;
        }


        //	if deleted subscriber id is only one
        if ($id != 0) {
            // Fetch Email id of subscriber for removing from All contacts list
            $fetch_conditions_array = array('subscriber_id' => $id, 'subscriber_status' => 1, 'is_deleted' => 0, 'resu.subscription_created_by' => $this->session->userdata('member_id'));
            //	Fetch subscriber email address from database
            $subscriber = $this->Subscriber_Model->get_subscriber_data($fetch_conditions_array);
            //check subscriber id for login user
            if (count($subscriber[0]) > 0) {
                // create activity log					
                $this->Activity_Model->create_activity(array('user_id' => $this->session->userdata('member_id'), 'activity' => 'contact_deleted', 'number_of_contacts' => count($subscriber[0])));
                // Load log configuration model class which handles database interaction
                $config_arr = $this->ConfigurationModel->get_site_configuration_data(array('config_name' => 'maximum_delete_contact'));
                $maximum_delete_contact = $config_arr[0]['config_value'];
                $delete_contacts = count($subscriber[0]);
                if ($delete_contacts > $maximum_delete_contact) {
                    $this->contact_notification($delete_contacts, $maximum_delete_contact, 'deleted');
                }
                // Deletes subscriber according to subscriber ID
                $this->Subscriber_Model->delete_subscriber(array('subscriber_id' => $id));
            }
        } else {
            //	if deleted subscriber id are multiple
            $delete_ids = ""; // collect  delted contact id in delete_ids variable
            //	Delete each subscriber record
            $i = 0;
            foreach ($_POST['subscriber_id'] as $subscriber_id) {

                //Protecting MySQL from query string sql injection Attacks
                if (is_numeric($subscriber_id)) {
                    $id = $subscriber_id;
                } else {
                    $id = 0;
                    echo "error:subscriber id not exist";
                    exit;
                }
                // Fetch Email id of subscriber for removing from All contacts list
                $fetch_conditions_array = array(
                    'subscriber_id' => $id,
                    'resu.subscription_created_by' => $this->session->userdata('member_id')
                );
                $subscriptions = $this->Subscriber_Model->get_subscriber_data($fetch_conditions_array);
                // Deletes subscriber according to subscriber ID
                //check subscriber id for login user
                if (count($subscriptions[0]) > 0) {
                    $this->Subscriber_Model->delete_subscriber(array('subscriber_id' => $id));
                    // Deletes subscriber from "All Contact List"
                    $this->Subscriber_Model->delete_subscriber(array('subscriber_email_address' => $subscriptions[0]['subscriber_email_address'], 'subscription_id' => '-' . $this->session->userdata('member_id')));
                    $delete_ids.=$id . ",";
                    $i++;
                }
            }
            if ($i > 0) {
                // create activity log		
                $this->Activity_Model->create_activity(array('user_id' => $this->session->userdata('member_id'), 'activity' => 'contact_deleted', 'number_of_contacts' => $i));
                // Load log configuration model class which handles database interaction
                $config_arr = $this->ConfigurationModel->get_site_configuration_data(array('config_name' => 'maximum_delete_contact'));
                $maximum_delete_contact = $config_arr[0]['config_value'];
                $delete_contacts = $i;
                if ($delete_contacts > $maximum_delete_contact) {
                    $this->contact_notification($delete_contacts, $maximum_delete_contact, 'deleted');
                }
            }
            $delete_ids = substr($delete_ids, 0, -1);
            echo $delete_ids; //	print deleted subscriber ids
        }
    }

    /**
     * Function Subscriber_delete
     *
     * 'subscriber_delete' controller function to confirm Delete existing subscriber.
     *
     * @param (int) (subscription_id)  contains subscription_id which is used for delete the subscriber from subscription list
     * @param (int) (subscriber_id)  contains subscriber_id which is used for delete the subscriber from database
     */
    function subscriber_delete($subscription_id = 0, $subscriber_id = 0) {
        if ($this->input->post('submit_action') == 'submit') {
            // Validation rules are applied
            $this->form_validation->set_rules('contact_list', 'Confirm', 'required');
            // To check form is validated
            if ($this->form_validation->run()) {
                $user_packages_array = $this->UserModel->get_user_packages(array('member_id' => $this->session->userdata('member_id'), 'is_deleted' => 0));
                $package_id = $user_packages_array[0]['package_id'];
                if (($package_id < 0) && (($this->input->get_post('action', true) == "page") || ($this->input->get_post('action', true) == "list"))) {
                    echo "free:Free accounts can only delete contacts one at a time. Upgrading your account will allow you to delete contacts in bulk";
                    exit;
                }
                if ($subscription_id == 0)
                    die("error: List is not selected");
                $contact_list = $this->input->get_post('contact_list', true);
                if ($contact_list == 1) {
                    if ((count($_POST['subscriber_id']) > 0) || ($this->input->get_post('action', true) == "page")) {
                        if ($this->input->get_post('action', true) == "page") {
                            // Delete All/Searched contacts from List only
                            $delete_contacts = $this->contact_model->remove_contacts_from_list($this->session->userdata('member_id'), $subscription_id);
                        } else {
                            $subscription_array[] = $subscription_id;
                            $this->Subscriber_Model->delete_subscription_subscriber(array(), true, $subscription_array);
                            $delete_contacts = count($this->input->get_post('subscriber_id', true));
                        }

                        if ($delete_contacts > 0) {
                            /* # create array for insert values in activty table
                              $values=array('user_id'=>$this->session->userdata('member_id'), 'activity'=>'contact_deleted', 'number_of_contacts'=>$delete_contacts);
                              $this->Activity_Model->create_activity($values);
                              // Load log configuration model class which handles database interaction
                              $config_arr=$this->ConfigurationModel->get_site_configuration_data(array('config_name'=>'maximum_delete_contact'));
                              $maximum_delete_contact=$config_arr[0]['config_value'];

                              if($delete_contacts>$maximum_delete_contact){
                              $this->contact_notification($delete_contacts,$maximum_delete_contact,'deleted');
                              } */

                            // print success message
                            echo "success:Contacts deleted successfully";
                            exit;
                        } else {
                            echo "error:No Record Selected";
                            exit;
                        }
                    } else {
                        echo "error:No Record Selected";
                        exit;
                    }
                } elseif ($contact_list == -1) {

                    if ((count($_POST['subscriber_id']) > 0) || ($this->input->get_post('action', true) == "page")) {
                        $delete_contacts = 0;

                        if ($this->input->get_post('action', true) == "page") {
                            // Delete All/Searched contacts from member's account or from "ALL Lists"
                            $delete_contacts = $this->contact_model->remove_contacts_from_all_lists($this->session->userdata('member_id'), $subscription_id);
                        } else {
                            $this->Subscriber_Model->delete_subscription_subscriber(array(), true);
                            $where_condition = array('subscriber_created_by' => $this->session->userdata('member_id'));
                            $today = date('Y-m-d');
                            //$this->Subscriber_Model->update_subscriber(array('is_deleted'=>1,'subscriber_status'=>1,'status_change_date'=>$today),$where_condition,true);
                            $this->Subscriber_Model->update_subscriber(array('is_deleted' => 1, 'status_change_date' => $today), $where_condition, true);

                            $contact_id = $this->input->get_post('subscriber_id', true);

                            $rsContactStatus = $this->db->query("select subscriber_status st ,subscriber_email_address eml from red_email_subscribers where subscriber_id in (" . implode($contact_id) . ")");
                            $contactStatus = $rsContactStatus->row()->st;
                            $contactEmail = $rsContactStatus->row()->eml;
                            $rsContactStatus->free_result();
                            if (trim($contactStatus) != '1' and trim($contactEmail) != '') {
                                $this->load->helper('admin_notification');
                                $mid = $this->session->userdata('member_id');
                                $mname = $this->session->userdata('member_username');
                                $message = "<p>Hello admin,</p><p>RC Member :$mname [$mid] has deleted contact [ $contactEmail ] from DNM.</p><p>Regards,<br />Redcappi Team</p>";
                                $text_message = "RC Member :$mname [$mid] has deleted contact [ $contactEmail ] from DNM.";
                                $to = $this->db->query('SELECT config_value FROM `red_site_configurations` where `config_name` = "admin_notification_email"')->row()->config_value;
                                admin_notification_send_email($to, SYSTEM_EMAIL_FROM, 'RedCappi', "DNM deleted by $mname [$mid]", $message, $text_message);
                            }
                            $delete_contacts = count($this->input->get_post('subscriber_id', true));
                        }

                        if ($delete_contacts > 0) {
                            // create activity log								
                            $this->Activity_Model->create_activity(array('user_id' => $this->session->userdata('member_id'), 'activity' => 'contact_deleted', 'number_of_contacts' => $delete_contacts));
                            // Load log configuration model class which handles database interaction
                            $config_arr = $this->ConfigurationModel->get_site_configuration_data(array('config_name' => 'maximum_delete_contact'));
                            $maximum_delete_contact = $config_arr[0]['config_value'];

                            if ($delete_contacts > $maximum_delete_contact) {
                                $this->contact_notification($delete_contacts, $maximum_delete_contact, 'deleted');
                            }
                        }
                        if ($delete_contacts > 0) {
                            // print success message
                            echo "success:Contacts deleted successfully";
                            exit;
                        } else {
                            echo "error:No Record Selected";
                            exit;
                        }
                    } else {
                        echo "error:No Record Selected";
                        exit;
                    }
                }
            } else {
                // print validation errors
                echo "error:" . validation_errors();
                exit;
            }
        }
        $this->load->view('contacts/subscriber_delete', array('subscription_id' => $subscription_id, 'subscriber_id' => $subscriber_id));
    }

    /**
     * Function suppress
     *
     * 'suppress' controller function for suppression of subscriber.
     *
     * @param (int) (id)  contains subscriber_id which is used for suppression the subscriber from database
     */
    function suppress($subscriber_id = 0) {
        //Check if user is not login then redirect to index page
        if ($this->session->userdata('member_id') == '')
            redirect('user/index');

        //	Collect subscriber id
        //Protecting MySQL from query string sql injection Attacks
        if (is_numeric($subscriber_id)) {
            $id = $subscriber_id;
        } else {
            $id = 0;
            echo "error:subscriber id not exist";
            exit;
        }

        // Load camapign model class which handles database interaction
        $this->load->model('newsletter/Campaign_Model');
        // Load camapign model class which handles database interaction

        if (count($id) > 0) {
            // Unsubscribe subscriber according to subscriber ID
            $todayDT = date('Y-m-d');
            $this->Subscriber_Model->update_subscriber(array('subscriber_status' => 5, 'status_change_date' => $todayDT), array('subscriber_id' => $id, 'subscriber_created_by' => $this->session->userdata('member_id')));
            $this->Emailreport_Model->delete_emailqueue(array('subscriber_id' => $id));
        }
    }

    /**
     * Function Unsubscribe
     *
     * 'Unsubscribe' controller function for unsubscribe of subscriber.
     *
     * @param (int) (id)  contains subscriber_id which is used for unsubscribe the subscriber from database
     */
    function subscribe_list($subscriber_id = 0) {
        //Check if user is not login then redirect to index page
        if ($this->session->userdata('member_id') == '')
            redirect('user/index');

        //	Collect subscriber id
        //Protecting MySQL from query string sql injection Attacks
        if (is_numeric($subscriber_id)) {
            $id = $subscriber_id;
        } else {
            $id = 0;
            echo "error:subscriber id not exist";
            exit;
        }

        $subscriptions_to_copy_in = $this->input->get_post('select_subscription', true);

        $fetch_condition = array('subscriber_id' => $id, 'res.subscription_id' => $subscriptions_to_copy_in, 'subscription_created_by' => $this->session->userdata('member_id'));

        $subscriber_arr = $this->Subscriber_Model->get_subscriber_data($fetch_condition);
        if (count($subscriber_arr) > 0) {
            // Unsubscribe subscriber according to subscriber ID
            $this->Subscriber_Model->update_subscriber(array('subscriber_status' => 1), array('subscriber_email_address' => $subscriber_arr[0]['subscriber_email_address'], 'subscriber_created_by' => $this->session->userdata('member_id')));
        } else {
            $fetch_condition = array('subscriber_id' => $id, 'subscription_created_by' => $this->session->userdata('member_id'));
            $subscriber_arr = $this->Subscriber_Model->get_subscriber_data($fetch_condition);
            $subscriptions_to_copy_in = $this->input->get_post('select_subscription', true);
            $input_array['subscriber_first_name'] = $subscriber_arr[0]['subscriber_first_name'];
            $input_array['subscriber_last_name'] = $subscriber_arr[0]['subscriber_last_name'];
            $input_array['subscriber_email_address'] = $subscriber_arr[0]['subscriber_email_address'];
            $input_array['subscriber_phone'] = $subscriber_arr[0]['subscriber_phone'];
            $input_array['subscriber_address'] = $subscriber_arr[0]['subscriber_address'];
            $input_array['subscriber_dob'] = $subscriber_arr[0]['subscriber_dob'];
            $input_array['subscriber_extra_fields'] = $subscriber_arr[0]['subscriber_extra_fields'];
            $input_array['subscription_id'] = $subscriptions_to_copy_in;
            $input_array['subscriber_created_by'] = $this->session->userdata('member_id');
            $this->Subscriber_Model->create_subscriber($input_array);
            // Unsubscribe subscriber according to subscriber ID
            $this->Subscriber_Model->update_subscriber(array('subscriber_status' => 1), array('subscriber_email_address' => $subscriber_arr[0]['subscriber_email_address'], 'subscriber_created_by' => $this->session->userdata('member_id')));

            // Assign  success message by message class
            $this->messages->add('Subscriber subscribe successfully', 'success');
        }
    }

    /**
     * Function Importcsv
     *
     * 'Importcsv' controller function for importing subscribers from csv file.
     *
     * @param (int) (subscription_id)  contains subscription_id which is used for importing subscribers into subscription list
     */
    function importcsv($subscription_id, $terms_condition = 0) {
        //Check if user is not login then redirect to index page
        if ($this->session->userdata('member_id') == '')
            redirect('user/index');

        if (is_numeric($subscription_id)) {
            $id = $subscription_id;
        } else {
            $id = 0;
            echo "error:subscription id not exist";
            exit;
        }
        if ($this->isImportInProgress()) {
            $data1 = array('error' => "There is already a list import in progress. Please wait untill the import is fully completed, before uploading another list.");
            echo json_encode($data1);
            exit;
        }
        if ($terms_condition != 0) {
            //	Call validate_csv_upload function for validate and upload the csv file
            $this->validate_csv_upload($id);
        } else {
            $data1 = array('error' => "The Terms & Conditions field is required.");
            echo json_encode($data1);
        }
    }

    /**
     * Function Importcsv
     *
     * 'Importcsv' controller function for validating and upload csv
     *
     * @param (int) (subscription_id)  contains subscription_id which is used for importing subscribers into subscription list
     */
    function validate_csv_upload($subscription_id) {

		ini_set('upload_max_filesize', '12M');
        ini_set('post_max_size', '12M');
        ini_set('max_input_time', 500);
        ini_set('max_execution_time', 500);
        ini_set('memory_limit', '-1');


        if (is_numeric($subscription_id)) {
            $subsc_id = $subscription_id;
        } else {
            $subsc_id = 0;
            echo "error:subscription id not exist";
            exit;
        }


        //Check if folder with name of 'csv_files' exists on server
        if (!file_exists($this->upload_path . '/csv_files')) {
            mkdir($this->upload_path . '/csv_files', 0777);
            chmod($this->upload_path . '/csv_files', 0777);
        }

        // Initialization upload configuration
        $upload_config = array();

        $upload_config['upload_path'] = $this->upload_path . '/csv_files/';
        $upload_config['allowed_types'] = 'csv|xls|xlsx|zip|rar';
        $upload_config['max_size'] = 1024*10;
        $this->upload->initialize($upload_config);

        # New file name of csv file
        $new_file_name = $this->session->userdata('member_id') . '_' . date('YmdHis');

        #check if file is uploaded successfully
        if (!$this->upload->do_upload('subscriber_csv_file')) {
            $this->form_validation->set_message('validate_csv_upload', $this->upload->display_errors());
            echo json_encode(array('error' => urlencode($this->upload->display_errors())));
            exit;
        } else {
            $uploaded_file_array = $this->upload->data();
            // check for php script in file. If found then delete the file.
            $f_content = @file_get_contents($uploaded_file_array['full_path']);
            if (stripos($f_content, "<?php") !== false) {
                @unlink($uploaded_file_array['full_path']);
                send_mail(SYSTEM_NOTICE_EMAIL_TO, SYSTEM_EMAIL_FROM, 'system', SYSTEM_DOMAIN_NAME . ': Hacking Attempt', "Contact file(" . $uploaded_file_array['full_path'] . ") having PHP into it tried to upload", "Contact file(" . $uploaded_file_array['full_path'] . ") having PHP into it tried to upload");
                $data1 = array('error' => 'Your file could not be imported');
                echo json_encode($data1);
                exit;
            }
            // continue
            // Rename uploaded file with new name
            rename($uploaded_file_array['full_path'], $uploaded_file_array['file_path'] . $new_file_name . $uploaded_file_array['file_ext']);
            $file_extension = $uploaded_file_array['file_ext'];
            if ($uploaded_file_array['file_ext'] == ".zip") {
                #upload Zip file
                $file_extension = $this->upload_zip_file($upload_config['upload_path'], $new_file_name, $uploaded_file_array['file_ext']);
            } elseif ($file_extension == ".xls") {
                #Load Excel_reader plugin for converting xls file to csv file
                $this->load->helper('excel_reader');
                convert_xls_to_csv($uploaded_file_array['file_path'] . $new_file_name . $file_extension, $uploaded_file_array['file_path'] . $new_file_name . '.csv');
            } else if ($file_extension == ".xlsx") {
                $this->load->helper('phpexcel'); #Load phpexcel plugin
                $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                # The Excel file that you want to convert to CSV
                $input_xlsx = $uploaded_file_array['file_path'] . $new_file_name . $file_extension;
                # The file to which you want to export the CSV data
                $output_csv = $uploaded_file_array['file_path'] . $new_file_name . '.csv';
                # This opens the Excel file with PHPExcel and then exports it to CSV
                $objPHPExcel = PHPExcel_IOFactory::load($input_xlsx);
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
                $objWriter->save($output_csv);
            }

            $memberid = $this->session->userdata('member_id');
            $sublistid = $subscription_id;
            $file_name = $new_file_name;
            $contact_import_log = config_item('campaign_files') . 'contact_import_log_' . date('Ymdhis');
            $list_type = 3;
            $do_notify = ($this->session->userdata('webmaster_id') != '') ? 0 : 1;

            $command = config_item('php_path') . " " . FCFOLDER . "/index.php  newsletter/cronjob bgprocess $memberid $sublistid $file_name $list_type $do_notify";
            //exec( "$command >> $contact_import_log  2>&1 &", $arrOutput );
            $do_notify = 1;
            $sqlbgprocess = "INSERT INTO red_bg_process_import_batch (member_id, sub_list_id, file_name, list_type, do_notify, job_status) VALUES (" . $memberid . "," . $sublistid . ",'" . $file_name . "', " . $list_type . ", " . $do_notify . ",0)";
            $this->db->query($sqlbgprocess);
            $this->db->query("UPDATE red_members set contact_import_progress=1 where member_id='" . $memberid . "'");
            if (base_url() != 'https://www.redcappi.dev/') {
                //exec( "$command > /dev/null &", $arrOutput );
            } else {
                //exec( "$command > null", $arrOutput );				
            }
            #Display success message
            $msg = "File Imported Successfully:" . $sublistid;
            $data1 = array('error' => '', 'msg' => $msg);
            echo json_encode($data1);
        }
    }

    /**
     * 	Function Exportcsv
     *
     * 	'Exportcsv' controller function for exporting csv file according to subscription id
     *
     * 	@param (int) (subscription_id)  contains subscription id for which csv will be export
     */
    function exportcsv($subscription_id = 0, $search_key = '') {
        //Check if user is not login then redirect to index page
        if ($this->session->userdata('member_id') == '')
            redirect('user/index');

        $id = (is_numeric($subscription_id)) ? $subscription_id : 0;
        if ($id == 0) {
            echo'';
            exit;
        }

        $condiotions_string = " `res`.`subscriber_created_by` = '" . $this->session->userdata('member_id') . "' and `res`.`is_deleted` = 0 and `res`.`subscriber_status` = 1";
        if (isset($_POST['action']) and 'page' != trim($_POST['action'])) {

            if (count($_POST['subscriber_id']) > 0) {
                foreach ($_POST['subscriber_id'] AS $sid) {
                    $strSID .= $sid . ', ';
                }

                $condiotions_string .= ' and res.subscriber_id in (' . rtrim($strSID, ', ') . ')';
            }
        }
        if (trim($search_key) != '')
            $condiotions_string .= " and res.subscriber_email_address like '%" . trim($search_key) . "%'";
        //Create output string  with heading
        $csv_output_header = "First Name,Last Name,Email Address,Address,Birthday,City,Company,Country,Phone,State,Zip Code,Optin Time,Optin IP";
        //$csv_output_header.="\n";
        $csv_output = "\n";
        //Fetch subscriber data according to list
        // check if subscription id is less than 0 then collect data for "All contact" subscription list
        if ($id < 0) {
            $subscriber_data = $this->contact_model->get_contacts_detail_in_selected_lists($condiotions_string);
        } elseif ($id > 0) {
            $subscriber_data = $this->contact_model->get_contacts_detail_in_selected_lists($condiotions_string, array($id));
        } else {
            $subscriber_data = array();
        }
        $i = 0;
        $header = array();
        foreach ($subscriber_data as $subscriber) {
            if (($subscriber['subscriber_extra_fields'] != "") && ($subscriber['subscriber_extra_fields'] != "b:0;")) {
                $arrSubscriberExtraField = unserialize($subscriber['subscriber_extra_fields']);
                if (is_array($arrSubscriberExtraField)) {
                    foreach ($arrSubscriberExtraField as $col => $val) {
                        if (!in_array($col, $header)) {
                            $csv_output_header.="," . $col;
                            $header[] = $col;
                            $i++;
                        }
                    }
                }
            }
        }

        //Append subscribers to csv output
        foreach ($subscriber_data as $subscriber) {
            $csv_output.=$subscriber['subscriber_first_name'] . ",";
            $csv_output.=$subscriber['subscriber_last_name'] . ",";
            $csv_output.=$subscriber['subscriber_email_address'] . ",";
            $csv_output.=$subscriber['subscriber_address'] . ",";
            $csv_output.=$subscriber['subscriber_dob'] . ",";
            $csv_output.=$subscriber['subscriber_city'] . ",";
            $csv_output.=$subscriber['subscriber_company'] . ",";
            $csv_output.=$subscriber['subscriber_country'] . ",";
            $csv_output.=$subscriber['subscriber_phone'] . ",";
            $csv_output.=$subscriber['subscriber_state'] . ",";
            $csv_output.=$subscriber['subscriber_zip_code'] . ",";
            $csv_output.=$subscriber['subscriber_date_added'] . ",";
            $csv_output.=$subscriber['subscriber_ip'] . "";
            $extra_field = ($subscriber['subscriber_extra_fields'] != "") ? unserialize($subscriber['subscriber_extra_fields']) : array();

            foreach ($header as $value) {
                $csv_output.= (is_array($extra_field) and array_key_exists($value, $extra_field) ) ? "," . $extra_field[$value] : ",";
            }
            $csv_output.="\n";
        }
        $csv_output = $csv_output_header . $csv_output;
        //Create filename and send output headers
        $filename = "Contacts_" . date("Y-m-d_H-i", time());
        header("Content-type: application/vnd.ms-excel");
        header("Content-disposition: csv" . date("Y-m-d") . ".csv");
        header("Content-disposition: filename=" . $filename . ".csv");
        //print csv output
        print $csv_output;
        exit;
    }

    /**
     * 	Function exportcsv_do_not_mail_list to export csv of do not mail list:
     * 	Unsubscribe list, bounced list, complaint list
     * */
    function exportcsv_do_not_mail_list($type = "") {
        //Check if user is not login then redirect to index page
        if ($this->session->userdata('member_id') == '')
            redirect('user/index');

        //Create output string  with heading
        $csv_output_header = "First Name,Last Name,Email Address,Address,Birthday,City,Company,Country,Phone,State,Zip Code";
        //$csv_output_header.="\n";
        $csv_output = "\n";
        if ($type == "removed") {
            // Collect condition array for fetch subscribers from database
            $fetch_condiotions_array = array('res.is_deleted' => 0, 'res.subscriber_created_by' => $this->session->userdata('member_id'));
            $id = '-' . $this->session->userdata('member_id');
            $total_rows = $this->Subscriber_Model->get_subscription_subscriber_count($fetch_condiotions_array, $type);
            $subscriber_data = $this->Subscriber_Model->get_subscriber_data(array('res.is_deleted' => 0, '(res.subscriber_status=5)' => NULL, 'res.subscriber_created_by' => $this->session->userdata('member_id')), $total_rows);
        } elseif ($type == "unsubscribe") {
            // Collect condition array for fetch subscribers from database
            $fetch_condiotions_array = array('res.is_deleted' => 0, 'res.subscriber_created_by' => $this->session->userdata('member_id'));
            $id = '-' . $this->session->userdata('member_id');
            $total_rows = $this->Subscriber_Model->get_subscription_subscriber_count($fetch_condiotions_array, $type);
            $subscriber_data = $this->Subscriber_Model->get_subscriber_data(array('res.is_deleted' => 0, '(res.subscriber_status =0)' => NULL, 'res.subscriber_created_by' => $this->session->userdata('member_id')), $total_rows);
        } elseif ($type == "complaints") {
            // Collect condition array for fetch subscribers from database
            $fetch_condiotions_array = array('res.is_deleted' => 0, 'res.subscriber_status' => 2, 'res.subscriber_created_by' => $this->session->userdata('member_id'));
            $id = '-' . $this->session->userdata('member_id');
            $total_rows = $this->Subscriber_Model->get_subscription_subscriber_count($fetch_condiotions_array);
            $subscriber_data = $this->Subscriber_Model->get_subscriber_data($fetch_condiotions_array, $total_rows);
        } elseif ($type == "bounce") {
            // Collect condition array for fetch subscribers from database
            $fetch_condiotions_array = array('res.is_deleted' => 0, 'res.subscriber_created_by' => $this->session->userdata('member_id'));
            $total_rows = $this->Subscriber_Model->get_subscriber_count($fetch_condiotions_array, true);
            $subscriber_data = $this->Subscriber_Model->get_subscriber_data($fetch_condiotions_array, $total_rows, 0, true);
        }

        $i = 0;
        $header = array();
        foreach ($subscriber_data as $subscriber) {
            if (($subscriber['subscriber_extra_fields'] != "") && ($subscriber['subscriber_extra_fields'] != "b:0;")) {
                foreach (unserialize($subscriber['subscriber_extra_fields']) as $col => $val) {
                    if (!in_array($col, $header)) {
                        $csv_output_header.="," . $col;
                        $header[] = $col;
                        $i++;
                    }
                }
            }
        }
        //Append subscribers to csv output
        foreach ($subscriber_data as $subscriber) {
            $csv_output.=$subscriber['subscriber_first_name'] . ",";
            $csv_output.=$subscriber['subscriber_last_name'] . ",";
            $csv_output.=$subscriber['subscriber_email_address'] . ",";
            $csv_output.=$subscriber['subscriber_address'] . ",";
            $csv_output.=$subscriber['subscriber_dob'] . ",";
            $csv_output.=$subscriber['subscriber_city'] . ",";
            $csv_output.=$subscriber['subscriber_company'] . ",";
            $csv_output.=$subscriber['subscriber_country'] . ",";
            $csv_output.=$subscriber['subscriber_phone'] . ",";
            $csv_output.=$subscriber['subscriber_state'] . ",";
            $csv_output.=$subscriber['subscriber_zip_code'] . "";

            $extra_field = ($subscriber['subscriber_extra_fields'] != "") ? unserialize($subscriber['subscriber_extra_fields']) : array();
            foreach ($header as $value) {
                $csv_output.=(array_key_exists($value, $extra_field) ) ? "," . $extra_field[$value] : ",";
            }
            $csv_output.="\n";
        }
        $csv_output = $csv_output_header . $csv_output;
        //Create filename and send output headers
        $filename = "Contacts_" . date("Y-m-d_H-i", time());
        header("Content-type: application/vnd.ms-excel");
        header("Content-disposition: csv" . date("Y-m-d") . ".csv");
        header("Content-disposition: filename=" . $filename . ".csv");
        //print csv output
        print $csv_output;
        exit;
    }

    /**
     * 	Function Copy_to_list
     *
     * 	'Copy_to_list' Conttroller for copy subscribers from one list to another
     *
     * 	@param (int) (subscriber_id)  contains subscriber  which subscriber  will be copy
     */
    function copy_to_list($subscriber_id = 0) {
        //Check if user is not login then redirect to index page
        if ($this->session->userdata('member_id') == '')
            redirect('user/index');

        $id = (is_numeric($subscriber_id)) ? $subscriber_id : 0;
        $subscriptions_to_copy_in = $this->input->get_post('select_subscription', true);
        $this->Subscriber_Model->replace_subscription_subscriber(array('subscriber_id' => $id, 'subscription_id' => $subscriptions_to_copy_in));

        return 'Contacts have been copied';
    }

    /**
     * 	Function Move_to_list
     *
     * 	'Mopy_to_list' Conttroller for move subscribers from one list to another
     *
     * 	@param (int) (subscription_id)  contains subscription id in which subscriber list will be move
     *
     * 	@return (string) return success or error message
     */
    function move_to_list($subscription_id = 0) {
        //Check if user is not login then redirect to index page
        if ($this->session->userdata('member_id') == '')
            redirect('user/index');

        $create_subscriber = false;
        $id = (is_numeric($subscriber_id)) ? $subscriber_id : 0;

        $subscriptions_to_copy_in = $this->input->get_post('select_subscription', true);
        if ($this->input->get_post('action_from', true) < 0) {
            foreach ($_POST['subscriber_id'] as $subscriber_id) {
                $input_array = array('subscriber_id' => $subscriber_id, 'subscription_id' => $subscriptions_to_copy_in);
                $this->Subscriber_Model->replace_subscription_subscriber($input_array);
            }
        } else {
            foreach ($_POST['subscriber_id'] as $subscriber_id) {
                $input_array = array('subscriber_id' => $subscriber_id, 'subscription_id' => $subscriptions_to_copy_in);
                $this->Subscriber_Model->replace_subscription_subscriber($input_array);
                $condition_array = array('subscriber_id' => $subscriber_id, 'subscription_id' => $this->input->get_post('action_from', true));
                $this->Subscriber_Model->delete_subscription_subscriber($condition_array);
            }
        }
        return 'Contacts have been moved'; // Success message
    }

    /**
     * 	Function Subscriber_action
     *
     * 	'Subscriber_action' Conttroller function for calling function for following
     * 	action:"Delete","Unsubscribe","Copy_to_list","Move_to_list"
     */
    function subscriber_action() {

        //Check if user is not login then redirect to index page
        if ($this->session->userdata('member_id') == '')
            redirect('user/index');

        if ($_POST['contact_list_action'] == "delete") {
            foreach ($_POST['subscriber_id'] AS $id)
                $this->delete($id);

            $this->subscriber_list($_POST['subscription_selected_id']);
            if (!count($_POST['subscriber_id'])) {
                $msg = "No Record Selected";
            } else {
                $msg = "Contacts deleted successfully";
            }
            echo "|" . $msg;
        } else if ($_POST['contact_list_action'] == "unsubscribe_list") {
            $subscriptions_to_unsubscribe = $this->input->get_post('action_from', true);
            $action = $this->input->get_post('action', true);
            $mid = $this->session->userdata('member_id');
            $whereClause = '';
            $todayDt = date('Y-m-d');
            if ($action == "page") {
                if (isset($_POST['email_search']) and trim($_POST['email_search']) != '') {
                    $srch = mysql_real_escape_string($this->input->post('email_search'));
                    $whereClause = " and ( `subscriber_email_address` LIKE '%$srch%' OR `subscriber_first_name` LIKE '%$srch%' OR `subscriber_last_name` LIKE '%$srch%'  OR `subscriber_extra_fields` LIKE '%$srch%')";
                }
                if ($subscriptions_to_unsubscribe == "-{$mid}") {
                    $qryAddtoDNM = "update `red_email_subscribers` set `subscriber_status`=5,`status_change_date`='$todayDt' where `subscriber_created_by` = '$mid' and `is_deleted` = 0 and `subscriber_status`=1 " . $whereClause;
                    $i = $this->db->query($qryAddtoDNM);
                } else {
                    $qryAddtoDNM = "UPDATE `red_email_subscribers` s INNER JOIN `red_email_subscription_subscriber` ss ON s.subscriber_id=ss.subscriber_id and ss.`subscription_id`='$subscriptions_to_unsubscribe' SET `subscriber_status` = 5, `status_change_date`='$todayDt'  where 1 {$whereClause}  ";
                    $i = $this->db->query($qryAddtoDNM);

                    /* $qryGetContactsFromList = "Select `subscriber_id` from `red_email_subscription_subscriber` where `subscription_id`='$subscriptions_to_unsubscribe' ";
                      $rsGetContacts = $this->db->query($qryGetContactsFromList);
                      if($rsGetContacts->num_rows()>0){
                      foreach($rsGetContacts->result_array() as $rowGetContacts){
                      $intSubscriberId = $rowGetContacts['subscriber_id'];
                      // If updated then, remove from Email-Queue table
                      $this->Emailreport_Model->delete_emailqueue(array('subscriber_id'=>$intSubscriberId));
                      }
                      } */
                }
            } else {
                $i = 0;
                if (isset($_POST['subscriber_id']) and count($_POST['subscriber_id']) > 0) {
                    foreach ($_POST['subscriber_id'] AS $id) {
                        $this->suppress($id);
                        $i++;
                    }
                }
            }
            #############################
            # create activity log		#
            #############################
            # create array for insert values in activty table
            $this->Activity_Model->create_activity(array('user_id' => $this->session->userdata('member_id'), 'activity' => 'contact_added_to_do_not_mail', 'number_of_contacts' => $i));
            $msg = "Unsubscribed Successfully";
            $_POST['uncheck_list'] = "";
            $this->subscriber_list($_POST['subscription_selected_id']);
            echo "|" . $msg;
        } else if ($_POST['contact_list_action'] == "copy_to_list") {
            $subscriptions_to_copy_from = $this->input->get_post('action_from', true);
            $subscriptions_to_copy_in = $this->input->get_post('select_subscription', true);
            $action = $this->input->get_post('action', true);
            $mid = $this->session->userdata('member_id');
            $whereClause = '';
            if ($action == "page") {
                if (isset($_POST['email_search']) and trim($_POST['email_search']) != '') {
                    $srch = mysql_real_escape_string($this->input->post('email_search'));
                    $whereClause = " and ( `subscriber_email_address` LIKE '%$srch%' OR `subscriber_first_name` LIKE '%$srch%' OR `subscriber_last_name` LIKE '%$srch%'  OR `subscriber_extra_fields` LIKE '%$srch%')";
                }
                if ($subscriptions_to_copy_from == "-{$mid}") {
                    $qryCopyAllContactsFromList = "select `subscriber_id` from `red_email_subscribers` where `subscriber_created_by` = '$mid' and `is_deleted` = 0 and `subscriber_status`=1 " . $whereClause;
                } else {
                    $qryCopyAllContactsFromList = "select s.`subscriber_id` from `red_email_subscribers` s INNER JOIN `red_email_subscription_subscriber` ss ON s.subscriber_id=ss.subscriber_id and ss.subscription_id='{$subscriptions_to_copy_from}' where `subscriber_created_by` = '$mid' and `is_deleted` = 0 and `subscriber_status`=1 " . $whereClause;
                }
                $rsAllContactsFromList = $this->db->query($qryCopyAllContactsFromList);
                if ($rsAllContactsFromList->num_rows() > 0) {
                    $strInsertString = '';
                    foreach ($rsAllContactsFromList->result_array() as $rowAllContacts) {
                        $intSubscriberId = $rowAllContacts['subscriber_id'];
                        #$input_array=array('subscriber_id'=>$intSubscriberId, 'subscription_id'=>$subscriptions_to_copy_in);
                        $arrInsertString[] = "('$subscriptions_to_copy_in', '$intSubscriberId')";
                        #$this->Subscriber_Model->replace_subscription_subscriber($input_array);
                    }
                    if (count($arrInsertString) > 0) {
                        $strInsertString = @implode(', ', $arrInsertString);
                        $this->db->query("insert ignore into `red_email_subscription_subscriber`(`subscription_id`, `subscriber_id`) values {$strInsertString}");
                    }
                }

                $msg = 'Contacts have been copied';
            } else {
                if (count($_POST['subscriber_id']) > 0) {
                    foreach ($_POST['subscriber_id'] AS $id) {
                        $msg = $this->copy_to_list($id);
                    }
                }
//				$this->subscriber_list($_POST['subscription_selected_id']);
                if (!count($_POST['subscriber_id'])) {
                    $msg = "No Record Selected";
                }
            }
            $this->subscriber_list($_POST['subscription_selected_id']);
            echo "|" . $msg;
        } else if ($_POST['contact_list_action'] == "move_to_list") {
            $subscriptions_to_copy_from = $this->input->get_post('action_from', true);
            $subscriptions_to_copy_in = $this->input->get_post('select_subscription', true);
            $action = $this->input->get_post('action', true);
            $mid = $this->session->userdata('member_id');
            $whereClause = '';
            if ($action == "page") {
                if (isset($_POST['email_search']) and trim($_POST['email_search']) != '') {
                    $srch = mysql_real_escape_string($this->input->post('email_search'));
                    $whereClause = " and ( `subscriber_email_address` LIKE '%$srch%' OR `subscriber_first_name` LIKE '%$srch%' OR `subscriber_last_name` LIKE '%$srch%'  OR `subscriber_extra_fields` LIKE '%$srch%')";
                }
                if ($subscriptions_to_copy_from == "-" . $this->session->userdata('member_id')) {
                    $qryMoveAllContactsFromList = "select `subscriber_id` from `red_email_subscribers` where `subscriber_created_by` = '$mid' and `is_deleted` = 0 and `subscriber_status`=1 " . $whereClause;
                    $rsAllContactsFromList = $this->db->query($qryMoveAllContactsFromList);
                    if ($rsAllContactsFromList->num_rows() > 0) {
                        foreach ($rsAllContactsFromList->result_array() as $rowAllContacts) {
                            $intSubscriberId = $rowAllContacts['subscriber_id'];
                            $input_array = array('subscriber_id' => $intSubscriberId, 'subscription_id' => $subscriptions_to_copy_in);
                            $this->Subscriber_Model->replace_subscription_subscriber($input_array);
                        }
                    }
                } else {
                    if ($whereClause != '') {
                        $qryUpdateMoveAllContactsFromList = "UPDATE Ignore  `red_email_subscription_subscriber` ss INNER JOIN `red_email_subscribers` s ON ss.subscriber_id = s.subscriber_id  and ss.`subscription_id`='$subscriptions_to_copy_from' {$whereClause} SET ss.subscription_id = '$subscriptions_to_copy_in'";
                    } else {
                        $qryUpdateMoveAllContactsFromList = "Update Ignore `red_email_subscription_subscriber` set `subscription_id`='$subscriptions_to_copy_in' where `subscription_id`='$subscriptions_to_copy_from'";
                    }
                    $this->db->query($qryUpdateMoveAllContactsFromList);
                }
                $msg = 'Contacts have been moved';
            } else {
                if (count($_POST['subscriber_id']) > 0) {
                    $msg = $this->move_to_list();
                }

                if (!count($_POST['subscriber_id'])) {
                    $msg = "No Record Selected";
                }
            }
            $this->subscriber_list($_POST['subscription_selected_id']);
            echo "|" . $msg;
        } else if ($_POST['contact_list_action'] == "copy_all") {
            $msg = $this->copy_list($id);
            $this->subscriber_list($_POST['subscription_selected_id']);
            echo "|" . $msg;
        }
    }

    function c2sdecrypt($s, $k) {
        $s = urldecode($s);
        $k = str_split(str_pad('', strlen($s), $k));
        $sa = str_split($s);
        foreach ($sa as $i => $v) {
            $t = ord($v) - ord($k[$i]);
            $sa[$i] = chr($t < 0 ? ($t + 256) : $t);
        }
        return join('', $sa);
    }

    function contact_notification($add_contacts = 0, $max_contacts = 0, $action = "") {
        // Load the user model which interact with database
        $this->load->model('UserModel');
        // Fetch user data from database
        $user_data_array = $this->UserModel->get_user_data(array('member_id' => $this->session->userdata('member_id')));
        $user_info = array($user_data_array[0]['member_username'], $add_contacts, $max_contacts, $action);

        if ($action == "add") {
            @create_notification("add_contact_limit", $user_info);
        } else {
            @create_notification("delete_contact_limit", $user_info, $add_contacts);
        }
    }

    /**
      Function upload_zip_file to upload a zip type file

      @param $upload_path String :contain upload path
      @param $new_file_name String :contain new file name
      @param $file_ext String :contain uploaded file extension
     */
    function upload_zip_file($upload_path = "", $new_file_name = "", $file_ext = "") {
        $zip = new ZipArchive;
        if ($zip->open($upload_path . $new_file_name . $file_ext) === TRUE) {
            $zip->extractTo($upload_path . $new_file_name . '/');
            $zip->close();
            $this->load->helper('file_name');  #load file helper
            $extensions = array('csv', 'xls', 'xlsx');  #Allowed file extensions in folder
            #########################################
            # Check files extesion in zip folder	#
            #########################################
            $filenames = get_filenames_by_extension($upload_path . $new_file_name . '/', $extensions);
            if (count($filenames) > 0) {
                foreach ($filenames as $v) {
                    $file_extension = pathinfo($v, PATHINFO_EXTENSION);
                    #Remove zip folder from system
                    chmod($upload_path . $new_file_name, 0777);
                    chmod($upload_path . $new_file_name . '/' . $v, 0777);
                    // check for php escript in files. if found remove/delete it and throw error.
                    $f_content = @file_get_contents($upload_path . $new_file_name . '/' . $v);
                    if (stripos($f_content, "<?php") !== false) {
                        @unlink($upload_path . $new_file_name . '/' . $v);
                        send_mail(SYSTEM_NOTICE_EMAIL_TO, SYSTEM_EMAIL_FROM, 'system', SYSTEM_DOMAIN_NAME . ': Hacking Attempt', "Contact file(" . $upload_path . $new_file_name . '/' . $v . ") having PHP into it tried to upload", "Contact file(" . $upload_path . $new_file_name . '/' . $v . ") having PHP into it tried to upload");
                        $data1 = array('error' => 'Your file could not be imported');
                        echo json_encode($data1);
                        exit;
                    }
                    rename($upload_path . $new_file_name . '/' . $v, $upload_path . $new_file_name . ".$file_extension");
                    rmdir($upload_path . $new_file_name);
                    unlink($upload_path . $new_file_name . ".zip");
                    return $file_extension;
                }
            } else {
                #Remove zip folder from system
                chmod($upload_path . $new_file_name, 0777);
                chmod($upload_path . $new_file_name . '/' . $v, 0777);
                recursive_remove_directory($upload_path . $new_file_name);
                unlink($upload_path . $new_file_name . ".zip");
                #displays error message if uploading fails
                $this->form_validation->set_message('validate_csv_upload', $this->upload->display_errors());
                $data1 = array('error' => 'You can upload file with extension csv or xls ');
                echo json_encode($data1);
                exit;
            }
        }
    }

    function checkImportStatus() {
        $mid = $this->session->userdata('member_id');
        echo $this->db->query("select `contact_import_progress` from `red_members` where `member_id`='$mid' limit 1")->row(0)->contact_import_progress;
    }

}

?>
