<?php

class ZFormEmail {

    public static function getEmptyRowData() {
        $emptyRowData=array(
            //'id'='' ,
            'title'=>'',
            'direction'=>'',
            'to'=>'',
            'to_name'=>'',
            'from'=>'',
            'from_name'=>'',
            'force_from'=>'',
            'reply_to'=>'',
            'subject'=>'',
            'body'=>'',
            'is_html'=>'',
        );
        return $emptyRowData;
    }

    public static function hydrateRowData($formData=array()) {
        $emptyRowData = self::getEmptyRowData();
        $hydratedData = array();
        foreach($emptyRowData as $k=>$v){
            if (isset($formData[$k])) {
                $hydratedData[$k] = $formData[$k];
            }
        }
        return $hydratedData;
    }
    
    public static function getForm($action='', $rowId=0) {
        //get the form from a simple factory
        $form = ZFactory::createForm('zformemail');
//VALIDATORS
        //validate input is not empty
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('Field cannot be empty.');

        //validate input is only letters and numbers. strictly.
        $alnum = new Zend_Validate_Alnum();
        $alnum->setMessage('Only letters and numbers are valid. No spaces or underscores.');

//HIDDEN INPUTS
       
//FORM 
        //email title
        $titleInput = ZFactory::createElement('text','title');
        $titleInput->setLabel('Email Title (for reference)');
        $form->addElement($titleInput);

        //to email
        $toInput = ZFactory::createElement('text','to');
        $toInput->setLabel('To Email (or leave blank and use \'to_email\' in form data)');
        $form->addElement($toInput);

        //to name
        $toNameInput = ZFactory::createElement('text','to_name');
        $toNameInput->setLabel('To Name (\'to_name\')');
        $form->addElement($toNameInput);
        
        //from email
        $fromInput = ZFactory::createElement('text','from');
        $fromInput->setLabel('From Email (\'from_email\')');
        $form->addElement($fromInput);

        //from name
        $fromNameInput = ZFactory::createElement('text','from_name');
        $fromNameInput->setLabel('From Name (\'from_name\')');
        $form->addElement($fromNameInput);
        
        //from email
        $replyToInput = ZFactory::createElement('text','reply_to');
        $replyToInput->setLabel('Reply-to Email (\'reply_email\')');
        $form->addElement($replyToInput);

        //force from - Sendmail option
        $forceFromInput = ZFactory::createElement('checkbox','force_from');
        $forceFromInput->setLabel('Force From (Sendmail -f flag)');
        $form->addElement($forceFromInput);
        
        //subject
        $subjectInput = ZFactory::createElement('text','subject');
        $subjectInput->setLabel('Subject (\'email_subject\')');
        $form->addElement($subjectInput);

        //is Html or text
        $isHtmlInput = ZFactory::createElement('checkbox','is_html');
        $isHtmlInput->setLabel('Is HTML');
        $form->addElement($isHtmlInput);        

        //body
        $bodyInput = ZFactory::createElement('textarea','body');
        $bodyInput->setLabel('Body - use [form_data] or brackets around form input names eg [first_name]');
        $bodyInput->setAttrib('cols',50);
        $bodyInput->setAttrib('rows',8);
        $bodyInput->setValue("Form Submission:\n[form_data]");
        $form->addElement($bodyInput);

        switch($action) {
            case 'add':
                $submitInput = ZFactory::createSubmitElement('zformemail_add_submit');
                $submitInput->setLabel('Save');
                $form->addElement($submitInput);
                break;
            case 'edit':
                //add submit button
                $submitInput = ZFactory::createSubmitElement('zformemail_edit_submit');
                $submitInput->setLabel('Update');
                $form->addElement($submitInput);
                //set the rowId to the form
                $idInput = ZFactory::createHiddenElement('form_email_id');
                $idInput->setValue($rowId);
                $idInput->setOrder(0);
                $form->addElement($idInput);
                break;
            case 'trash':
                //add submit button
                $submitInput = ZFactory::createSubmitElement('zformemail_trash_submit');
                $submitInput->setLabel('Trash');
                $form->addElement($submitInput);
                //set the rowId to the form
                $idInput = ZFactory::createHiddenElement('form_email_id');
                $idInput->setValue($rowId);
                $idInput->setOrder(0);
                $form->addElement($idInput);
                break;
            default:
                //good luck submitting. you really shouldn't be here.
                break;
        }
        return $form;
    }

}
