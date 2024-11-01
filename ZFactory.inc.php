<?php

class ZFactory {

    public static function createFormDecorator($tag='table') {
        $tags = array('table','div','dl','ul','ol');
        if (!in_array($tag,$tags)) { $tag='dl'; }
        $formDecorator = array(
            'FormElements',
            array(array('data'=>'HtmlTag'), array('tag'=>$tag)),
            'Form'
        );
        return $formDecorator;
    }
    
    public static function createSubmitTableDecorator() {
        
        $dataData = array('tag' => 'td', 'colspan' => '2');
        $labelData = array('tag' => 'td');//, 'placement' => 'prepend');
        $rowData = array('tag' => 'tr','closeOnly'=>'true');        
        return self::createDecorator($rowData, $labelData, $dataData, false);
    }
    
    public static function createSubmitDecorator($labelTag='dt', $dataTag='dd', $rowTag='none', $showLabel = false) {
        return self::createInputDecorator($labelTag, $dataTag, $rowTag, $showLabel);
    }
    
    public static function createInputDecorator($labelTag='dt', $dataTag='dd', $rowTag='none', $showLabel = true) {
        
        $dataData = array('tag' => $dataTag);
        $labelData = array('tag' => $labelTag);
        
        $rowData = array();
        if (!empty($rowTag) && $rowTag != 'none') {
            $rowData = array('tag' => $rowTag);
        } else {
            $rowData = array('tag' => '');
        }
        return self::createDecorator($rowData, $labelData, $dataData, $showLabel);
    }
    
    public static function createDecorator($rowData = array(), $labelData = array(), $dataData = array(), $showLabel = true) {

        $labelTree = array(array('label' => 'HtmlTag'), $labelData);
        if ($showLabel) {
            $labelTree = array('Label', $labelData);
        }

        $decorator = array('ViewHelper', 'Description', 'Errors',
            array(array('data' => 'HtmlTag'), $dataData),
            $labelTree,
            array(array('row' => 'HtmlTag'), $rowData),
        );
        
        return $decorator;
    }
    
    public static function getDbPrefix() {
        global $wpdb;
        return $wpdb->prefix;
    }
    
    public static function createModel($tableName) {
        if (empty($tableName)) { return false; }
        global $wpdb;
        $prefix = $wpdb->prefix; //eg 'wp_'
        $db = Zend_Db::factory('Pdo_Mysql',array(
            'host'     => DB_HOST,
            'username' => DB_USER,
            'password' => DB_PASSWORD,
            'dbname'   => DB_NAME,
        ));
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
        $table = $prefix . $tableName;
        return new Zend_Db_Table($table,$db);
    }

    public static function createForm($name,$isFrontend = 0) {
        $form = new Zend_Form($name);
        $form->setView(new Zend_View());
        if (!$isFrontend) {
            $form->setDecorators(self::createFormDecorator('div'));
        }
        return $form;
    }

    public static function createEmail($emailId,$formData) {
        $emailModel = ZFactory::createModel('zform_email');
        $emailRow = $emailModel->fetchRow($emailModel->select()->where('id = ?',$emailId));
        if (empty($emailRow)) { 
            return false; 
        }

        $from = $emailRow->from;
        $fromName = $emailRow->from_name;
        if (empty($from) && !empty($formData['from_email'])) {
            $from = $formData['from_email'];
        }
        if (empty($fromName) && !empty($formData['from_name'])) {
            $fromName = $formData['from_name'];
        }

        $replyTo = $emailRow->reply_to;
        if (empty($replyTo)) {
            $replyTo = $from;
            if (empty($replyTo) && !empty($formData['reply_email'])) {
                $replyTo = $formData['reply_email'];
            }
        }

        $to = $emailRow->to;
        $toName = $emailRow->to_name;
        if (empty($to) && !empty($formData['to_email'])) {
            $to = $formData['to_email'];
        }
        if (empty($toName) && !empty($formData['to_name'])) {
            $toName = $formData['to_name'];
        }

        $subject = $emailRow->subject;
        if (empty($subject) && !empty($formData['email_subject'])) {
            $subject = $formData['email_subject'];
        }

        $body = $emailRow->body;

        //flags
//         $isZend = $emailRow->is_zend;
        $isHtml = $emailRow->is_html;
//         $isSmtp = $emailRow->is_smtp;
        $forceFrom = $emailRow->force_from;

//         //smtp stuff
//         $smtpUsername = $emailRow->smtp_username;
//         $smtpPassword = $emailRow->smtp_password;
//         $smtpServer = $emailRow->smtp_server;
//         $smtpAuth = $emailRow->smtp_auth;
//         $smtpSsl = $emailRow->smtp_ssl;
        
        $mail = new ZMail($from,$fromName,$replyTo,$to,$toName,$subject,$body,$formData);
        if ($isHtml) {
            $mail->setIsHtml(1);
        }
        if ($forceFrom) {
            $mail->setForceFrom(1);
        }
        
//         $mail->setFlags($isZend,$isHtml,$forceFrom,0);
//         if ($isSmtp && !empty($smtpUsername) && !empty($smtpPassword) && !empty($smtpServer)) {
//             
//         }

        return $mail;
    }

    public static function createHiddenElement($name, $isFrontend = 0) {
        $input = new Zend_Form_Element_Hidden($name);
        if (!$isFrontend) {
            $input->removeDecorator('label');
            $input->removeDecorator('htmltag');
        }
        return $input;
    }

    public static function createSubmitElement($name, $isFrontend=0) {
        $obj = new Zend_Form_Element_Submit($name);
        if (!$isFrontend) {
            $obj->setDecorators(self::createSubmitTableDecorator());
            $obj->removeDecorator('label');
        }
        return $obj;
    }

    public static function createElement($type,$name,$isFrontend=0) {
        $ztype = 'Zend_Form_Element_' . strtoupper(substr($type,0,1)) . strtolower(substr($type,1,strlen($type)-1));
        //could use hidden inputs to store exception messages
        try {
            $obj = new $ztype($name);
        } catch(Exception $e) {
            return false;
        }
        if (!$isFrontend) {
            $obj->setDecorators(self::createInputDecorator('div','div','div'));
        }
        return $obj;
    }

    public static function createValidator($type,$message='') {
        $ztype = 'Zend_Validate_' . $type;
        try {
            $obj = new $ztype();
        } catch(Exception $e) {
            return false;
        }
        if (!empty($message)) {
            $obj->setMessage($message);
        }
        return $obj;
    }

    public static function createRecaptchaElement($pubKey,$privKey,$name='captcha') {
        
        $recaptchaService = new Zend_Service_ReCaptcha($pubKey,$privKey);        
        $adapter = new Zend_Captcha_ReCaptcha();
        $adapter->setService($recaptchaService);        
        $captcha = array(
            'captcha' => $adapter,
            'privKey' => $privKey,
            'pubKey' => $pubKey,
        );
        
        return new Zend_Form_Element_Captcha($name,$captcha);
    }

}
