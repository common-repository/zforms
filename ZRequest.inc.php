<?php

class ZRequest {

    public static function getParam($key) {
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        return false;
    }

    public static function getParams() {
        if ($_GET) {
            return $_GET;
        }
        return array();
    }

    public static function getPost() {
        if ($_POST) {
            return $_POST;
        }
        return array();
    }
    
    public static function getRequestParams() {
        return array_merge(self::getParams(),self::getPost());
    }

    public static function getPostParam($key) {
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }
        return false;
    }
    
    //mostly called by catchZform to see if we have a form to handle
    public static function isZFormsSubmit() {
        $params = self::getRequestParams();
        return (isset($params['zforms_step']) && isset($params['zforms_key']));
    }

    //mostly called by zform to know whether we submitted the same form we're building
    public static function isSubmitted($key,$step) {
        if (self::isZFormsSubmit()) {  
                $formData = self::getRequestParams();
                $formStep = $formData['zforms_step'];
                $formKey = $formData['zforms_key'];
                if ($formKey == $key && $formStep == $step) {
                     return true;
                }
                return false;
        }
        return false;
    }//*/

    public static function hydrateRowData($emptyRowData=array(),$formData=array()) {
        $hydratedData = array();
        $ignore = array('zforms_key','zforms_step');
        if (!empty($emptyRowData)) {
            foreach($emptyRowData as $k=>$v){
                if (isset($formData[$k]) && !isset($ignore[$k])) {
                    $hydratedData[$k] = $formData[$k];
                }
            }
        }
        return $hydratedData;
    }
	
	//session isn't totally request related but *shrug*
	public static function getSessionParam($key) {
		if (isset($_SESSION[$key])) {
			return $_SESSION[$key];
		}
		return false;
	}

	public static function setSessionParam($key, $value) {
		if ($_SESSION) {
			$_SESSION[$key] = $value;
			return true;
		}
		return false;
	}

    //this class is becoming a general helper
    function getDir() {
        return substr(__FILE__, 0, strrpos(__FILE__, '/')+1);
    }
    
    function getBaseUrl() {
        return get_bloginfo('siteurl');
    }
}
