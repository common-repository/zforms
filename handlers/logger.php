<?php

    function zfHandle($formData = array(), $key='') {
        $success = 0;
        //example of allowing a form to be submitted multiple times
        $_SESSION['zforms']['handled'][$key] = 0;
        $file = dirname(__FILE__) . '/log/log.txt';
        $fh = fopen($file, 'a');
        if ($fh) {
            fwrite($fh, print_r($formData,1));
            $success = 1;
            fclose($fh);
        }
        return $success;
    }

    function zfSuccess($formData = array(), $key='') {
        $sendMail = 0;

        //can disable sending email by returning false
        return $sendMail;
    }

    function zfError($formData = array(), $key='') {
        $sendMail = 0;

        return $sendMail;
    }
