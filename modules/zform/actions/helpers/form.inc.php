<?php

class ZForm {

    public static function getEmptyRowData() {
        $emptyRowData=array(
            //'id'='' ,
            'code'=>'',
            'title'=>'',
            'handler_id'=>'',
            'success_email_id'=>'',
            'error_email_id'=>'',
            //'channel_id'=>'',
        );
        return $emptyRowData;
    }

    public static function hydrateRowData($formData=array()) {
        $emptyRowData = self::getEmptyRowData();
        $hydratedData = array();
        foreach($emptyRowData as $k=>$v){
            if (isset($formData[$k])) {
                $hydratedData[$k]=$formData[$k];
            }
        }
        return $hydratedData;
    }
       
    public static function getForm($action='', $rowId=0) {
        //get the form from a simple factory
        $form = ZFactory::createForm('zform');
//VALIDATORS
        //validate input is not empty
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('Field cannot be empty.');

        //validate input is only letters and numbers. strictly.
        $alnum = new Zend_Validate_Alnum();
        $alnum->setMessage('Only letters and numbers are valid. No spaces or underscores.');
       
//FORM 
        //maybe heading is a better word
        $titleInput = ZFactory::createElement('text','title');
        $titleInput->setLabel('Title');
        $titleInput->setRequired(true);
        $titleInput->addValidator($notEmpty);
        $form->addElement($titleInput);
        
        //for the shortcode
        $keyInput = ZFactory::createElement('text','code');
        $keyInput->setLabel('FormKey eg thing_add');
        $keyInput->setRequired(true);
        $keyInput->addValidator($notEmpty);
        //$keyInput->addValidator($alnum); //alnum except underscores
        $form->addElement($keyInput);

        $handlerModel = ZFactory::createModel('zform_handler');
        $handlerRows = $handlerModel->fetchAll();

        $handlerInput = ZFactory::createElement('select','handler_id');
        $handlerInput->setLabel('Form Handler');
        $handlerInput->addMultiOption('0','None');
        if (!empty($handlerRows)) {
            foreach($handlerRows as $handlerRow) {
                $handlerInput->addMultiOption($handlerRow->id,$handlerRow->title);
            }
        }
        $form->addElement($handlerInput);

        $emailModel = ZFactory::createModel('zform_email');
        $emailRows = $emailModel->fetchAll();

        //disable emails
        $disableEmailInput = ZFactory::createElement('checkbox','is_smtp');
        $disableEmailInput->setLabel('Disable Email');
        $form->addElement($disableEmailInput);        

        //success email
        $successEmailInput = ZFactory::createElement('select','success_email_id');
        $successEmailInput->setLabel('Success Email');
        $successEmailInput->addMultiOption('0','None');
        if (!empty($emailRows)) {
            foreach($emailRows as $emailRow) {
                $successEmailInput->addMultiOption($emailRow->id,$emailRow->subject);
            }
        }
        $form->addElement($successEmailInput);

        //error email
        $errorEmailInput = ZFactory::createElement('select','error_email_id');
        $errorEmailInput->setLabel('Error Email');
        $errorEmailInput->addMultiOption('0','None');
        if (!empty($emailRows)) {
            foreach($emailRows as $emailRow) {
                $errorEmailInput->addMultiOption($emailRow->id,$emailRow->subject);
            }
        }
        $form->addElement($errorEmailInput);

        switch($action) {
            case 'add':
                $submitInput = ZFactory::createSubmitElement('zform_add_submit');
                $submitInput->setLabel('Save');
                $form->addElement($submitInput);
                break;
            case 'edit':
                //add submit button
                $submitInput = ZFactory::createSubmitElement('zform_edit_submit');
                $submitInput->setLabel('Update');
                $form->addElement($submitInput);
                //set the rowId to the form
                $idInput = ZFactory::createHiddenElement('form_id');
                $idInput->setValue($rowId);
                $idInput->removeDecorator('label');
                $idInput->removeDecorator('htmltag');
                $idInput->setOrder(0);
                $form->addElement($idInput);
                break;
            case 'trash':
                //add submit button
                $submitInput = ZFactory::createSubmitElement('zform_trash_submit');
                $submitInput->setLabel('Trash');
                $form->addElement($submitInput);
                //set the rowId to the form
                $idInput = ZFactory::createHiddenElement('form_id');
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
