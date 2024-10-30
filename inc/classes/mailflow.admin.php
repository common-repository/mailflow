<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/*
 * Mailflow Admin Class Declration. This class will handle all the Front end functions like shortcode, 
 * redering form, recording emails and matching tags.
 */

class mailflow_admin {

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

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'mailflow_add_admin_menu')); //Admin menus
        add_action('admin_enqueue_scripts', array($this, 'mailflow_admin_scripts')); // Add javascripts
        add_action('init', array($this, 'mailflow_process_post')); //processing post

        /**
         * Getting API details
         */
        $api_details = get_option('mailflow-api-details');
        if ($api_details && $api_details != '') {
            $api_details = unserialize($api_details);

            if (isset($api_details['api_key']) && isset($api_details['api_secret'])) {
                $this->api_key = $api_details['api_key'];
                $this->api_secret = $api_details['api_secret'];
            }
            
            //Only activate Mailflow API credential verification on mailflow pages
            if(strpos($_GET['page'], 'mailflow') !== false){
                $api_status = $this->mailflow_api_verify();
                if($api_status !== 200){
                    if($api_status == 'curl_failed'){
                        $this->error = __("We have detected that 'CURL' is not enabled on your Server. We require 'CURL' to connect to mailflow. Please contact your hosting provider and try again.", 'mailflow');
                    }else{
                        $this->error = __("Mailflow API credential are invalid. Please login to your mailflow account and provide your API credentials in the <a href='admin.php?page=mailflow_settings'>settings.</a>", 'mailflow');                        
                    }
                }                
            }

        }
    }

    /**
     * Adding Admin menus
     */
    public function mailflow_add_admin_menu() {
        add_menu_page(__('Mailflow', 'mailflow'), __('Mailflow', 'mailflow'), 'publish_posts', 'mailflow', array($this, 'mailflow_admin_main'), MAILFLOW_ASSETS_URL . '/images/icon.png');
        //dont display menu of Form and Tags if the api details are not set
        if ($this->api_key != '' && $this->api_secret != '') {
            add_submenu_page('mailflow', __('Forms', 'mailflow'), __('Forms', 'mailflow'), 'publish_posts', 'mailflow_forms', array($this, 'mailflow_admin_forms'));
            add_submenu_page('mailflow', __('Roles', 'mailflow'), __('Roles', 'mailflow'), 'publish_posts', 'mailflow_roles', array($this, 'mailflow_admin_roles'));
            add_submenu_page('mailflow', __('Tracking', 'mailflow'), __('Tracking', 'mailflow'), 'publish_posts', 'mailflow_tags', array($this, 'mailflow_admin_tags'));
        }
        add_submenu_page('mailflow', __('Settings', 'mailflow'), __('Settings', 'mailflow'), 'publish_posts', 'mailflow_settings', array($this, 'mailflow_admin_settings'));
    }

    /**
     * This function will render form for updating front form fields.
     */
    public function mailflow_admin_forms() {
        $fields = get_option('mailflow-registration-form-fields'); //getting stored fields if any
        $double_opt = get_option("mailflow-doubleopt-form"); //doubple opt value
        $form_messages = get_option("mailflow-form-message"); //getting for messages if any
        $form_tags = get_option("mailflow-form-tags"); //getting for messages if any
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
        
        if ($form_tags && $form_tags != '') {
            $form_tags = unserialize($form_tags);
        } else {
            $form_tags = array();
        }
        
        require_once MAILFLOW_TPL . '/admin/forms.tpl.php';
    }

    
    /**
     * This function will render form for updating tracking
     */
    public function mailflow_admin_tags() {
        $pages = get_pages();
        $page_tags = get_option('mailflow-tags-page-mapping'); //getting stored page-tags connections
        if ($page_tags && $page_tags != '') {
            $page_tags = unserialize($page_tags);
            foreach ($page_tags as $pageID => $page_tag) {
                foreach ($page_tag as $tag) {
                    $tagsArray[$tag][] = $pageID;
                }
            }
        } else {
            $tagsArray = array();
        }

        require_once MAILFLOW_TPL . '/admin/tags.tpl.php';
    }

    /**
     * This function will render form for updating Roles - user connections
     */
    public function mailflow_admin_roles() {
        $role_tags = get_option('mailflow-role-tags-mapping'); /// getting roles-user connection
        $role_status = get_option('mailflow-role-tags-status'); // getting role status. If a role is set to disable it wont update then
        $double_opt = get_option("mailflow-doubleopt-roles"); //double opt for storing role-tag connection

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

        $available_roles = $this->mailflow_available_roles(); //getting all roles created by wordpress or anyother plugin

        require_once MAILFLOW_TPL . '/admin/roles.tpl.php';
    }

    /**
     * Setting Page rendering
     */
    public function mailflow_admin_settings() {
        require_once MAILFLOW_TPL . '/admin/settings.tpl.php';
    }

    /**
     * Cover Page
     */
    public function mailflow_admin_main() {
        if ($this->api_key == '' || $this->api_secret == '') {
            $this->error = __("Mailflow API credential are not set. Please login to your mailflow account and add your <a href='https://mailflow.com/account/api'>API credentials</a> in the <a href='admin.php?page=mailflow_settings'>settings.</a>", 'mailflow');
        }
        require_once MAILFLOW_TPL . '/admin/main.tpl.php';
    }

    /**
     * Enqueueing public scripts
     */
    public function mailflow_admin_scripts() {
        wp_enqueue_style('mailform-admin', MAILFLOW_ASSETS_URL . 'css/style.admin.css');
	$version = get_bloginfo("version");
        if($version <= "3.7"){
            wp_enqueue_style('mailform-admin-legacy', MAILFLOW_ASSETS_URL . 'css/style.admin.legacy.css');
        }
        wp_enqueue_style('select2-css', MAILFLOW_ASSETS_URL . 'thirdparty/select2/select2.min.css');
        wp_enqueue_script('select2-js', MAILFLOW_ASSETS_URL . 'thirdparty/select2/select2.min.js', array('jquery'));
        wp_enqueue_script('ajax-script', MAILFLOW_ASSETS_URL . 'js/scripts.js', array('jquery'));
    }
    
    /**
     * This function contact mailflow and verify if the stored api credentials are valid.
     */
    function mailflow_api_verify() {
        if (function_exists('curl_init')) {
            $url = "https://mailflow.com/api/test/";

            $ch = curl_init(); //initializing curl
            /**
             * default CURL options for all requests
             */
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
            curl_setopt($ch, CURLOPT_USERPWD, $this->api_key . ":" . $this->api_secret);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);


            /**
             * Executing CURL
             */
            $resp = curl_exec($ch);

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            //close connection
            curl_close($ch);
            return $httpCode;
        }else{
            return "curl_failed";
        }
    }

    /**
     * All post request by this plugin will be controlled by this plugin
     * It will return incase of invalid form action. Function will called based on the action provided in the POST. Only allowed actions will be processed
     */
    public function mailflow_process_post() {
        $allowed_action = array("update_field_form", "update_tag_form", "update_mailflow_settings", "update_role_tags");
        if (current_user_can("publish_posts")) {
            if (isset($_POST) && isset($_POST['mailflow_form_action']) && in_array($_POST['mailflow_form_action'], $allowed_action)) {
                $action_name = "mailflow_" . $_POST['mailflow_form_action'];
                $this->$action_name($_POST);
            }
        } else {
            return FALSE;
        }
    }

    /**
     * Process from submit from Forms menu. Save it in the database as Wordpress options
     * @param array $post POST ARRAY
     */
    public function mailflow_update_field_form($post) {
        if (isset($post['field']['name']) && !empty($post['field']['name']) && isset($post['field']['key']) && !empty($post['field']['key']) && isset($post['field']['type']) && !empty($post['field']['type'])) {
            foreach ($post['field']['name'] as $nkey => $name) {
                $fields[$nkey]['name'] = $name;
            }

            foreach ($post['field']['key'] as $kkey => $key) {
                $fields[$kkey]['key'] = $key;
            }

            foreach ($post['field']['type'] as $tkey => $type) {
                $fields[$tkey]['type'] = $type;
            }
            
            if(isset($post['mailflow-form-message']) && !empty($post['mailflow-form-message'])){
                update_option("mailflow-form-message", serialize($post['mailflow-form-message']));
            }
            
            if(isset($post['mailflow-form-tags']) && !empty($post['mailflow-form-tags'])){
                update_option("mailflow-form-tags", serialize($post['mailflow-form-tags']));
            }

            if (isset($fields)) {
                update_option('mailflow-registration-form-fields', serialize($fields));
                if(isset($post['mailflow-doubleopt-form']) && $post['mailflow-doubleopt-form'] == 1){
                    update_option('mailflow-doubleopt-form', 1);                
                }else{
                    update_option('mailflow-doubleopt-form', 0);
                }
                $this->success = __("Registration form fields updated successfully", 'mailflow');
            }
        }
    }

    /**
     * Process form submitted from Tracking menu. Save it in database as wordpress option
     * @param array $post POST ARRAY
     */
    public function mailflow_update_tag_form($post) {
        if ($post['mailflow-tags'] && !empty($post['mailflow-tags'])) {
            foreach ($post['mailflow-tags'] as $mt) {
                foreach ($mt['pages'] as $mtpages) {
                    $tags_by_page[$mtpages][] = $mt['tag'];
                }
            }
            if (isset($tags_by_page) && !empty($tags_by_page)) {
                update_option('mailflow-tags-page-mapping', serialize($tags_by_page));
                $this->success = __("Tags connected to pages successfully", 'mailflow');
            }
        } else {
            update_option('mailflow-tags-page-mapping', serialize(array()));
            $this->success = __("All tag mapping to pages removed  successfully", 'mailflow');
        }
    }

    /**
     * Process Settings form. Save it in database as wordpress option
     * @param array $post POST ARRAY
     */
    public function mailflow_update_mailflow_settings($post) {
        if (isset($post['mailflow-api-key']) && $post['mailflow-api-key'] != '' && isset($post['mailflow-api-secret']) && $post['mailflow-api-secret'] != '') {
            update_option('mailflow-api-details', serialize(array("api_key" => $post['mailflow-api-key'], "api_secret" => $post['mailflow-api-secret'])));
            $this->api_key = $post['mailflow-api-key'];
            $this->api_secret = $post['mailflow-api-secret'];
            $this->success = __("API credentials saved successfully", 'mailflow');
        }
    }

    /**
     * Process Roles form. Save it in database as wordpress option
     * @param array $post POST ARRAY
     */
    public function mailflow_update_role_tags($post) {
        if ($post['mailflow-role-tags'] && !empty($post['mailflow-role-tags'])) {
            update_option('mailflow-role-tags-mapping', serialize($post['mailflow-role-tags']));
            update_option('mailflow-role-tags-status', serialize($post['mailflow-role-status']));
            $this->success = __("Tags connected to roles successfully", 'mailflow');
        } else {
            update_option('mailflow-role-tags-mapping', serialize(array()));
            update_option('mailflow-role-tags-status', serialize(array()));

            $this->success = __("All tag mapping to roles removed  successfully", 'mailflow');
        }
        
        if(isset($post['mailflow-doubleopt-roles']) && $post['mailflow-doubleopt-roles'] == 1){
            update_option('mailflow-doubleopt-roles', 1);                
        }else{
            update_option('mailflow-doubleopt-roles', 0);
        }
    }
    
    /**
     * Get available roles created by any plugin or wordpress
     * @param array $post POST ARRAY
     */
    public function mailflow_available_roles() {
        global $wp_roles;

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        return $wp_roles->get_names(); //Available roles
    }

}

$mailflow_admin = new mailflow_admin();
