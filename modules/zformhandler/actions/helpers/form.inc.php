<?php

class ZFormHandler {

    public static function getEmptyRowData() {
        $emptyRowData = array(
            //'id'='' ,
            'title'=>'',
            'filename'=>'',
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
    
    public static function getForm($action = '',$rowId = 0) {
        //get the form from a simple factory
        $form = ZFactory::createForm('zform_handler');
//VALIDATORS
        //validate input is not empty
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('Field cannot be empty.');

        //validate input is only letters and numbers. strictly.
        $alnum = new Zend_Validate_Alnum();
        $alnum->setMessage('Only letters and numbers are valid. No spaces or underscores.');

//HIDDEN INPUTS

//FORM 

        $titleInput = ZFactory::createElement('text','title');
        $titleInput->setLabel('Title');
        $titleInput->addValidator($notEmpty);
        $form->addElement($titleInput);
        
        $filenameInput = ZFactory::createElement('text','filename');
        $filenameInput->setLabel('Filename');
        $filenameInput->addValidator($notEmpty);
        $form->addElement($filenameInput);

        switch($action) {
            case 'add':
                $submitInput = ZFactory::createSubmitElement('zformhandler_add_submit');
                $submitInput->setLabel('Save');
                $form->addElement($submitInput);
                break;
            case 'edit':
                //add submit button
                $submitInput = ZFactory::createSubmitElement('zformhandler_edit_submit');
                $submitInput->setLabel('Update');
                $form->addElement($submitInput);
                //set the rowId to the form
                $idInput = ZFactory::createHiddenElement('handler_id');
                $idInput->setValue($rowId);
                $idInput->setOrder(0);
                $form->addElement($idInput);
                break;
            case 'trash':
                //add submit button
                $submitInput = ZFactory::createSubmitElement('zformhandler_trash_submit');
                $submitInput->setLabel('Trash');
                $form->addElement($submitInput);
                //set the rowId to the form
                $idInput = ZFactory::createHiddenElement('handler_id');
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
