<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

require_once(ABSPATH . 'wp-includes/pluggable.php');


/*
 * Mailflow Class Declration. This class will handle all the Front end functions like shortcode, 
 * redering form, recording emails and matching tags.
 */

class mailflow {

    /**
     * Decleration for Mailflow api credentials
     */
    public $api_key = '';
    public $api_secret = '';

    /**
     *
     * @var null|string hold the success message
     */
    public $success;

    /**
     *
     * @var null|string This variable hold the error message
     */
    public $error;

    public function __construct() {
        /* Public script enqueue */
        add_action('wp_enqueue_scripts', array($this, 'mailflow_publicscripts'));

        /* Hook to save tags at the time of registration */
        add_action('user_register', array($this, 'mailflow_process_registration'));
        
       // Shortcode
        add_shortcode('mailflow-form', array($this, 'mailflow_form_shortcode'));
        
       //Process Post
        add_action('init', array($this, 'mailflow_process_post')); //processing post

        /* wp-ajax implementation */
        add_action('wp_ajax_mailflow_tag_page_visit', array($this, 'mailflow_tag_page_visit_callback'));

        /* setting api-details to class objects */
        $api_details = get_option('mailflow-api-details');
        if ($api_details && $api_details != '') {
            $api_details = unserialize($api_details);

            if (isset($api_details['api_key']) && isset($api_details['api_secret'])) {
                $this->api_key = $api_details['api_key'];
                $this->api_secret = $api_details['api_secret'];
            }
        }
    }

    /**
     * Public Scripts
     */
    public function mailflow_publicscripts() {
        wp_enqueue_script('mailflow-front-js', MAILFLOW_ASSETS_URL . 'js/front.js', array('jquery')); //javascript for frontend
        wp_localize_script('mailflow-front-js', 'mailflowAjax', array('ajaxurl' => admin_url('admin-ajax.php'), 'security' => wp_create_nonce('save-tag-based-on-url')));
    }

    /**
     * Call to wp-ajax action mailflow_tag_page_visit. Function will save tags based on the page the logged in user will visit
     */
    public function mailflow_tag_page_visit_callback() {
        $current_user = wp_get_current_user(); //current user
        if (isset($current_user->data) && isset($current_user->data->user_email) && $current_user->data->user_email != '') {
            check_ajax_referer('save-tag-based-on-url', 'security'); //checking valid wp nonce
            $pageid = url_to_postid(sanitize_text_field($_POST['path'])); //guessing the ID of the url the user is visiting
            if ($pageid > 0) {
                $page_tags = get_option('mailflow-tags-page-mapping');
                if ($page_tags && $page_tags != '') {
                    $page_tags = unserialize($page_tags);
                    if (isset($page_tags) && isset($page_tags[$pageid])) {
                        foreach ($page_tags[$pageid] as $pt) {
                            $page_tag_array[] = array("name" => $pt);
                            if ($page_tag_array && !empty($page_tag_array) && is_array($page_tag_array)) {
                                $tagInfo = json_decode($this->mailflow_api("tags", "POST", array("email" => $current_user->data->user_email, "tags" => $page_tag_array)));
                            }
                        }
                    }
                }
            }
            die; //returning the empty ajax response
        }
    }
    
    public function mailflow_form_shortcode(){
         if ($this->api_key != '' && $this->api_secret != '') {
            $fields = get_option('mailflow-registration-form-fields');
            $form_messages = get_option("mailflow-form-message");
            
            if ($fields && $fields != '') {
                $fields = unserialize($fields);
            } else {
                $fields = array();
            }
            if ($form_messages && $form_messages != '') {
                $form_messages = unserialize($form_messages);
            } else {
                $form_messages = array();
            }
            
            if ($fields && !empty($fields)) {
                ob_start();
                require_once MAILFLOW_TPL . 'front/form.php';
                $mailflow_form = ob_get_contents();
                ob_end_clean();
                
                return $mailflow_form;
            }
        }       
    }

    /**
     * This function will add attributes created inside wordpress admin to registration form
     * @depricated
     */
    public function mailflow_registration_form_fields() {
        //get the fields here
        //provide it to the template
        if ($this->api_key != '' && $this->api_secret != '') {
            $fields = get_option('mailflow-registration-form-fields');
            if ($fields && $fields != '') {
                $fields = unserialize($fields);
            } else {
                $fields = array();
            }

            if ($fields && !empty($fields)) {
                require_once MAILFLOW_TPL . 'front/form.php';
            }
        }
    }

    /**
     * Post-registration hook, called to save attributes and tags to mailflow. This hook will keep on looking for all the registration form for new registration event.
     * @param int $user_id
     */
    function mailflow_process_registration($user_id) {
        if (is_numeric($user_id) && $user_id > 0) {
            $userInfo = get_user_by('id', $user_id);
            $role_tags = get_option('mailflow-role-tags-mapping');
            $role_status = get_option('mailflow-role-tags-status');
            if ($role_tags && $role_tags != '') {
                $role_tags = unserialize($role_tags);
            } else {
                $role_tags = array();
            }

            if ($role_status && $role_status != '') {
                $role_status = unserialize($role_status);
            } else {
                $role_status = array();
            }
            
            $roles_tags_array = array(); //initializing an array
            if (!empty($role_tags) && !empty($userInfo) && isset($userInfo->roles) && is_array($userInfo->roles) && !empty($userInfo->roles)) {
                foreach ($userInfo->roles as $ur) {
                    if (isset($role_status[$ur]) && $role_status[$ur] == 1) { //check if current if user's role status is active
                        foreach ($role_tags[$ur] as $rt) {
                            $roles_tags_array[] = array("name" => $rt); //storing tags in main tag array to update in batch on mailflow
                        }
                    }
                }
            }
            
            foreach($userInfo->roles as $user_roles){
                if (isset($role_status[$user_roles]) && $role_status[$user_roles] == 1) {
                    $add_contact = 1;
                }
            }


            //if any of user role's update to mailflow status is active
            if(isset($add_contact) && $add_contact == 1){
                $double_opt = get_option("mailflow-doubleopt-roles");
                if($double_opt && $double_opt == 1){
                    $confirmed = true;
                }else{
                    $confirmed = false;
                }
                //Create Contact
                $contactInfo = json_decode($this->mailflow_api("contacts", "POST", array("email" => str_replace("+", "%2B", $userInfo->data->user_email), "confirmed" => $confirmed)));                
            }

            //If contact created successfully then save the tags
            if (isset($contactInfo) && isset($contactInfo->id) && isset($roles_tags_array) && !empty($roles_tags_array)) {
                $tagInfo = json_decode($this->mailflow_api("tags", "POST", array("email" =>  str_replace("+", "%2B", $userInfo->data->user_email), "tags" => $roles_tags_array)));
            }
        }
    }

    /**
     * This function contact mailflow through mailflow API. It will use CURL to communicate.
     * @param string $path Contain path to make api request for example /contacts
     * @param string $requestType Contain request type like GET, POST, DELETE
     * @param array  $data Contain data to be posted to mailflow
     */
    function mailflow_api($path = '', $requestType = 'POST', $data = array()) {
        if (function_exists('curl_init') && $path != '' && $requestType != '') {
            $url = "https://mailflow.com/api/$path/";

            $ch = curl_init(); //initializing curl
            /**
             * default CURL options for all requests
             */
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
            curl_setopt($ch, CURLOPT_USERPWD, $this->api_key . ":" . $this->api_secret);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));


            /**
             * Sending POST
             */
            $data = urldecode($this->mailflow_http_build_query_curl($data));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

            /**
             * Executing CURL
             */
            $resp = curl_exec($ch);

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            //close connection
            curl_close($ch);
            return $resp;
        }
    }

    /**
     * Function will convert array to curl suitable POST string
     * @param array $a Array
     * @param string $b Holder
     * @param string $c Holder
     * @return string
     */
    public function mailflow_http_build_query_curl($a, $b = 0, $c = 0) {
        if (!is_array($a))
            return false;
        foreach ((array) $a as $k => $v) {
            if ($c && is_int($k))
                $k = $b . "[]";
            elseif (is_int($k))
                $k = $b . $k;
            elseif ($c && !is_int($k))
                $k = $b . "[$k]";

            if (is_array($v) || is_object($v)) {
                $r[] = $this->mailflow_http_build_query_curl($v, $k, 1);
                continue;
            }
            $r[] = urlencode($k) . "=" . urlencode($v);
        }
        return implode("&", $r);
    }
    
    /**
     * Function will detect post action and will perform post action accordingly
     */
    
    public function mailflow_process_post(){
        $allowed_action = array("front_form_post");
            if (isset($_POST) && isset($_POST['mailflow_form_action']) && in_array($_POST['mailflow_form_action'], $allowed_action)) {
                $action_name = "mailflow_" . $_POST['mailflow_form_action'];
                $this->$action_name($_POST);
            }
       
    }
    
    /**
     * Function will save attributes to mailflow through mailflow rendered form
     * @param array $post POST Data
     */
    public function mailflow_front_form_post($post) {
        $post['mailflow-fields']['contact'] = str_replace("+", "%2B", $post['mailflow-fields']['contact']);
        $form_messages = get_option("mailflow-form-message");
        if ($form_messages && $form_messages != '') {
            $form_messages = unserialize($form_messages);
        } else {
            $form_messages = array();
        }
        
        $form_tags = get_option("mailflow-form-tags");
        if ($form_tags && $form_tags != '') {
            $form_tags = unserialize($form_tags);
        } else {
            $form_tags = array();
        }
        
        if(!empty($form_tags)){
            foreach ($form_tags as $t) {
               $roles_tags_array[] = array("name" => $t);
            }
        }

        
        if (filter_var($post['mailflow-fields']['contact'], FILTER_VALIDATE_EMAIL)) {
            $double_opt = get_option("mailflow-doubleopt-form");
            if($double_opt && $double_opt == 1){
                $confirmed = true;
            }else{
                $confirmed = false;
            }
            $contactInfo = json_decode($this->mailflow_api("contacts", "POST", array("email" => str_replace("+", "%2B", $post['mailflow-fields']['contact']), 'confirmed' => $confirmed)));

            //If contact created successfully then save the tags
            if (isset($contactInfo) && isset($contactInfo->id) && isset($roles_tags_array) && !empty($roles_tags_array)) {
                $tagInfo = json_decode($this->mailflow_api("tags", "POST", array("email" =>  str_replace("+", "%2B", $post['mailflow-fields']['contact']), "tags" => $roles_tags_array)));
            }
            
            if (isset($contactInfo) && isset($contactInfo->id)) {
                if (isset($post['mailflow-fields']) && !empty($post['mailflow-fields'])) {
                    foreach ($post['mailflow-fields'] as $mkey => $mfields) {
                        if($mkey != 'contact'){
                            $attrInfo = json_decode(
                                $this->mailflow_api("attributes", "POST", array(
                                    "email" => $post['mailflow-fields']['contact'],
                                    "attributes" => array("key" => $mkey, "value" => trim($mfields), "label" => $mkey)
                                )
                            ));
                        }
                    }

                    $this->success = (isset($form_messages['success-message'])) ? $form_messages['success-message'] : "Contact saved successfully";
                }
            }
        } else {
            $this->error = (isset($form_messages['incorrect-email-message'])) ? $form_messages['incorrect-email-message'] : "you have entered an invalid email";
        }
    }

}

$mailflow = new mailflow(); //mailflow start
