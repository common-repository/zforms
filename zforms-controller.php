<?php

    //load API and route traffic similar to a controller
    require_once('ZRequest.inc.php');
    require_once('ZMail.inc.php'); 
    require_once('ZFactory.inc.php');

    //similar to a controller
    if (empty($module)) {
        $module = ZRequest::getParam('zform_module');
    }
    $modules = array('zform','zformstep','zforminput',
                        'zforminputoption','zformemail','zformdecorator',
                        'zformhandler','zformvalidator','zformrecaptcha','zforminputvalidator');
    if (!in_array($module, $modules)) {
        $module = 'zform';
    }

    //similar to a controller action
    if (empty($action)) {
        $action = ZRequest::getParam('zform_action');
    }
    $actions = array('list','add','edit','trash');
    if (!in_array($action, $actions)) {
        $action = 'list';
    }

    //load action helpers - using my own pattern
    require_once("modules/{$module}/actions/helpers/form.inc.php");
    include(ZRequest::getDir() . 'inc/sub-nav.inc.php');
    include("modules/{$module}/actions/{$action}.inc.php");