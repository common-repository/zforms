<?php
/**
 * @package ZForms
 * @version 1.2
 */
/*
Plugin Name: ZForms
Plugin URI: http://www.jessehanson.com/
Description: Easily create forms with an admin interface and customize destinations using Zend Framework. requires WP-ZFF Zend Framework Full
Author: Jesse Hanson
Version: 1.2
Author URI: http://www.jessehanson.com/
*/

//INIT VARIABLES - limited use of dollar-sign variables because WP is (very) procedural.
    if (!function_exists('zform_init_vars')) {
    function zform_init_vars() {
        if (!defined('zform_controller_file')) define('zform_controller_file', 'zforms-controller.php');
        if (!defined('zform_email_link')) define('zform_email_link', 'zforms-controller.php&zform_module=zformemail');
        if (!defined('zform_admin_icon')) define('zform_admin_icon', WP_PLUGIN_URL . '/zforms/images/zforms-icon-20x20.png');
        if (!defined('zform_plugin_file')) define('zform_plugin_file', plugin_basename(__FILE__));
        if (!defined('zform_base_file')) define('zform_base_file', __FILE__);
        if (!defined('zform_capability')) define('zform_capability', 'zform_capability');
        //ini_set('display_errors',1);
    }
    }
    add_action('plugins_loaded', 'zform_init_vars');

//FRONTEND CALL VIA SHORT-CODE    
// [zform key="formKey" step="X"]
    if (!function_exists('zform_tag')) {
    function zform_tag($atts) {
        extract(shortcode_atts(array(
            'key' => '',
            'step' => '',
        ), $atts));
        $step = (int) $step;
        if (!$step) { $step = 1; }
        return zform($key,$step);
    }
    }
    add_shortcode('zform', 'zform_tag');

//main api call
    if (!function_exists('zform')) {
    function zform($key,$step=1) {
        $step = (int) $step;
        require_once('ZRequest.inc.php');
        require_once('ZMail.inc.php');
        require_once('ZFactory.inc.php');
        require_once('zform.php');
        echo zform::build($key,$step);
    }
    }
    
//FRONTEND FORM HANDLER
    if (!function_exists('catchZForm')) {
    function catchZForm() {
        if (!session_id()) { session_start(); }
        require_once('ZRequest.inc.php');
        
        if (ZRequest::isZFormsSubmit()) {            
            //get form data
            $formData = ZRequest::getRequestParams();
            
            //figure out what's being submitted
            $key = $formData['zforms_key'];
            $step = $formData['zforms_step'];
            //don't want this to get in the way. stripping asap.
            unset($formData['zforms_step']);
            unset($formData['zforms_key']);
            
            //load some api and figure out where we are 
            require_once('ZMail.inc.php');
            require_once('ZFactory.inc.php');
            require_once('zform.php');
            
            $uri = $_SERVER['REQUEST_URI'];
            //die("uri: $uri");
            
            if ($step >1 && !isset($_SESSION['zforms']['valid'][$key][$step-1])) {
                header("Location: $uri");
                exit();
            }
            //get form row
            $formRow = zform::getFormRow($key);
            if (empty($formRow->id)) {
                header("Location: $uri");
                exit();
            }
            $formId = $formRow->id;
            //get step row
            $stepRow = zform::getFormStepRow($formId,$step);
            if (empty($stepRow->id)) {
                header("Location: $uri");
                exit();
            }
            
            $nextPostId = $stepRow->next_post_id;
            $post = &get_post($nextPostId);
            $url = $post->guid;
            $isLastStep = zform::isLastStep($formId, $stepRow->step);
            
            //hydrate empty row with posted data, but dont populate form with it
            $form = zform::build($key, $step, 0);
            //make sure we have our recaptcha fields to be filled
            $emptyRowData = zform::getEmptyFormKeyStepData($key, $step);
            if (zform::hasRecaptcha($key, $step)) {
                $emptyRowData['recaptcha_challenge_field'] = '';
                $emptyRowData['recaptcha_response_field'] = '';
            }
            //fill the empty array. strip everything else.
            $formData = ZRequest::hydrateRowData($emptyRowData, $formData);
            //last chance to massage data
            $isValid = $form->isValid($formData);
            
            //good place to debug
            //$debug = "<pre>isValid: $isValid</pre><pre>emptyRowData:\n".print_r($emptyRowData,1)."</pre><pre>formData:\n".print_r($formData,1)."</pre>";
            //die($debug);
            
            //don't need these after validation. stripping asap so it doesn't go into session
            unset($formData['recaptcha_response_field']);
            unset($formData['recaptcha_challenge_field']);
            
            if ($isValid) {
                //set to session
                $_SESSION['zforms']['valid'][$key][$step] = $formData;
                //see if we're ready to handle the formData
                if ($isLastStep) {
                    $finalData = array();
                    //merge all steps/submissions into a single array
                    foreach($_SESSION['zforms']['valid'][$key] as $step => $stepData) {
                        $finalData = array_merge($stepData, $finalData);
                    }
                    //get empty row for all steps, exclude submits and captchas
                    $emptyRowData = zform::getEmptyFormData($key);
                    $finalData = ZRequest::hydrateRowData($emptyRowData, $finalData);

                    //handled is only set on success, not failure
                    if (isset($_SESSION['zforms']['handled'][$key]) && 
                                $_SESSION['zforms']['handled'][$key] == 1) {
                        header("Location: $url");
                        exit();
                    } else {
                        //where it all ends up if everything is valid
                        zform::handle($key, $finalData);
                    }
                }
                header("Location: $url");
                exit();
            } else {
                //set to session
                $_SESSION['zforms']['invalid'][$key][$step] = $formData;

                //get uri and redirect
                if (!empty($uri)) {
                    header("Location: $uri");
                    exit();
                }
            }
        }
    }
    }
    add_action('load_wp_zff', 'catchZForm');

//ACTIVATE
    if (!function_exists('zform_install')) {
    function zform_install() {
        global $wpdb, $user_level, $wp_rewrite, $wp_version;
        //load some core functions
        if (file_exists(ABSPATH . 'wp-admin/includes/upgrade.php')) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        } else {
            require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
        }
        //need ZendFramework plugin installed
        if (!defined('WP-ZFF')) {
            deactivate_plugins('zforms/zforms-plugin.php'); //Deactivate ourself
            wp_die('ZForms requires this plugin to be installed: <br>http://wordpress.org/extend/plugins/wp-zff-zend-framework-full/');
        }
        //save permissions
        $role = get_role('administrator');
        if (!is_null($role)) { 
            $role->add_cap(zform_capability); 
        }

        include('sql/install.inc.php');
        mysql_query($createZForm,$wpdb->dbh);
        mysql_query($createZFormStep,$wpdb->dbh);
        mysql_query($createZFormInput,$wpdb->dbh);
        mysql_query($createZFormInputOption,$wpdb->dbh);
        mysql_query($createZFormInputValidator,$wpdb->dbh);
        mysql_query($createZFormDecorator,$wpdb->dbh);
        mysql_query($insertExampleDecorators,$wpdb->dbh);
        mysql_query($createZFormEmail,$wpdb->dbh);
        mysql_query($createZFormValidator,$wpdb->dbh);
        mysql_query($createZFormRecaptcha,$wpdb->dbh);
        mysql_query($createZFormHandler,$wpdb->dbh);
        mysql_query($insertExampleHandler,$wpdb->dbh);
    }
    }
    register_activation_hook(__FILE__, 'zform_install');
    
//DEACTIVATE
    if (!function_exists('zform_deactivate')) {
    function zform_deactivate() {
        global $wpdb, $user_level, $wp_rewrite, $wp_version;
        //load some core functions

        if (file_exists(ABSPATH . 'wp-admin/includes/upgrade.php')) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        } else {
            require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
        }

        //remove permissions
        $role = get_role('administrator');
        if (!is_null($role)) { 
            $role->remove_cap(zform_capability); 
        }

        include('sql/install.inc.php');
        mysql_query($dropZForm,$wpdb->dbh);
        mysql_query($dropZFormStep,$wpdb->dbh);
        mysql_query($dropZFormInput,$wpdb->dbh);
        mysql_query($dropZFormInputValidator,$wpdb->dbh);
        mysql_query($dropZFormInputOption,$wpdb->dbh);
        mysql_query($dropZFormDecorator,$wpdb->dbh);
        mysql_query($dropZFormEmail,$wpdb->dbh);
        mysql_query($dropZFormHandler,$wpdb->dbh);
        mysql_query($dropZFormRecaptcha,$wpdb->dbh);

    }
    }
    register_deactivation_hook( __FILE__, 'zform_deactivate' );
    

//ADMIN MENU - calls a page output function and references filename.
    if (!function_exists('zform_admin_menu')) {
    function zform_admin_menu() {
        //add_menu_page(pageTitle,menuTitle,permissionKey,destFile,destFileOutput(),iconFile,position)
        add_menu_page(__('ZForms','zforms-menu'), __('ZForms','zforms-menu'), zform_capability, zform_controller_file, 'zform_controller_page', zform_admin_icon);
        //add_submenu_page(zform_controller_file, __('Emails','zforms-menu'), __('Emails','zforms-menu'), zform_capability, zform_email_link, 'zform_email_page');
    }
    }
    //Add link to the admin menu
    add_action('admin_menu', 'zform_admin_menu');


//LOADERS - utilizing the procedural nature of wp
    if (!function_exists('zform_controller_page')) {
    function zform_controller_page() {
        include(zform_controller_file);
    }
    }


    
