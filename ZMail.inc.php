<?php

class ZMail {

    //standard stuff, plus form data
    public $subject = '';
    public $body = '';
    public $toEmail = '';
    public $toName = '';
    public $fromEmail = '';
    public $fromName = '';
    public $replyTo = '';
    public $formData = array();

    //flags
//     public $isZend = 0;
//     public $isSmtp = 0;
    public $isHtml = 0;
    public $forceFrom = 0;

    //smtp 
//     public $smtpUsername = '';
//     public $smtpPassword = '';
//     public $smtpServer = '';
//     public $smtpPort = 25;
//     public $smtpAuth = '';
//     public $smtpSsl = '';

    /**
     * Construct email with standard email stuff, plus form data.
     */
    public function __construct($fromEmail,$fromName,$replyTo,$toEmail,$toName,$subject,$body,$formData) {
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
        $this->toEmail = $toEmail;
        $this->toName = $toName;
        $this->subject = $subject;
        $this->body = stripslashes($body);
        $this->formData = $formData;
        $this->replyTo = $replyTo;
    }
    
    public function setIsHtml($isHtml = 1) {
        $this->isHtml = $isHtml;
    }
    
    public function setForceFrom($forceFrom = 1) {
        $this->forceFrom = $forceFrom;
    }

    /**
     * Set all flags at once since all are optional.
     */
//     public function setFlags($isZend=0,$isHtml=0,$forceFrom=0,$isSmtp=0) {
//         $this->isZend = $isZend;
//         $this->isHtml = $isHtml;
//         $this->forceFrom = $forceFrom;
//         $this->isSmtp = $isSmtp;
//     }

    /**
     * Set SMTP settings.
     */
//     public function setSmtp($username,$password,$server,$auth='login',$ssl='',$port=25) {
//         $this->isSmtp = 1;
//         $this->isZend = 1;
//         $authValues = array('login','plain','crammd5');
//         if (!in_array($auth,$authValues)) {
//             $auth = 'login';
//         }
//         if (!in_array($ssl,$sslValues)) {
//             $ssl = '';
//         }
//         $sslValues = array('','ssl','tls');
// 
//         $this->smtpUsername = $username;
//         $this->smtpPassword = $password;
//         $this->smtpServer = $server;
//         $port = (int) $port;
//         if ($port > 0 && $port != 25) {
//             $this->smtpPort = $port;
//         } else {
//             $this->smtpPort = 25;
//         }
//     }

    /**
     * For outputting form data as a table row.
     */
    public function getTableRow($key,$value) {
        return "<tr><td>{$key}:</td><td>{$value}</td></tr>\n";
    }

    /**
     * Substitute expected brackets with formData.
     */
    public function subBody() {
        if (!empty($this->body) && !empty($this->formData)) {
            foreach($this->formData as $k=>$v) {
                $this->body = str_replace("[$k]",$v,$this->body);
            }
        }
        if (strpos($this->body,'[form_data]') !== false && !empty($this->formData)) {
            if ($this->isHtml) {
                $table = "<table>\n";
                foreach($this->formData as $k => $v) {
                    $table .= $this->getTableRow($k,$v);
                    $this->body = str_replace("[$k]", $v, $this->body);
                }
                $table .= "</table>\n";
                $this->body = str_replace('[form_data]', $table, $this->body);
            } else {
                $str = '';
                foreach($this->formData as $k => $v) {
                    $str .= "\n$k : $v";
                    $this->body = str_replace("[$k]", $v, $this->body);
                }
                $this->body = str_replace('[form_data]', $str, $this->body);
            }
        }
    }

    /**
     * Build and send the email via mail() , zend_mail, zend_smtp .
     */
    public function send() {

        //replace bracket keys
        $this->subBody();
        
        //either it's zend mail or it's mail()
//         if ($this->isZend) {
//             $mail = new Zend_Mail();
//             if ($this->isHtml) {
//                 $mail->setBodyHtml($this->body);
//             } else {
//                 $mail->setBodyText($this->body);
//             }
//             $mail->addTo($this->toEmail, $this->toName);
//             $mail->setFrom($this->fromEmail, $this->fromName);
//             $mail->setSubject($this->subject);
//             if ($this->isSmtp) {
//                 $defaultPorts = array('','25');
//                 $sslOptions = array('ssl','tls');
//                 $config = array(
//                     'auth' => $this->smtp_auth,
//                     'username' => $this->smtp_username,
//                     'password' => $this->smtp_password,
//                 );
//                 if (!in_array($this->smtp_port,$defaultPorts)) {
//                     $config['port'] = $this->smtp_port;
//                 }
//                 if (in_array($this->smtp_ssl,$sslOptions)) {
//                     $config['ssl'] = $this->smtp_ssl;
//                 }
//                 $transport = new Zend_Mail_Transport_Smtp($this->smtp_server, $config);
//                 return $mail->send($transport);
//             } else {
//                 return $mail->send();
//             }
//         } else {
            $headers = ''; //string
            $additional =''; //string
            if ($this->isHtml) {
                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            }
            if ($this->forceFrom) {
                $additional = "-f{$this->fromEmail}";
            }
            $headers .= "To: {$this->toName} <{$this->toEmail}>" . "\r\n";
            $headers .= "From: {$this->fromName} <{$this->fromEmail}>" . "\r\n";
            if (!empty($this->replyTo)) {
                $headers .= "Reply-To: {$this->replyTo}" . "\r\n";
            }
            return mail($this->toEmail,$this->subject,$this->body,$headers,$additional);
//         }
    }

}