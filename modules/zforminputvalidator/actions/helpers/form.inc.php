<?php

class ZFormInputValidator {

    public static function getEmptyRowData() {
        $emptyRowData = array(
            //'id'='' ,
            'input_id'=>'',
            'type'=>'',
            'message'=>'',
            'extra'=>'',
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

    public static function getTypes() {
        $types = array('NotEmpty','EmailAddress','Alnum','Alpha','Date','LessThan','GreaterThan','Postcode');
        return $types;
    }
    
    public static function getForm($action = '',$foreignId = 0, $rowId = 0) {
        //get the form from a simple factory
        $form = ZFactory::createForm('zform_input_validator');
//VALIDATORS
        //validate input is not empty
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('Field cannot be empty.');

        //validate input is only letters and numbers. strictly.
        $alnum = new Zend_Validate_Alnum();
        $alnum->setMessage('Only letters and numbers are valid. No spaces or underscores.');

//HIDDEN INPUTS
        $foreignIdInput = ZFactory::createHiddenElement('input_id');
        $foreignIdInput->setValue($foreignId);
        $form->addElement($foreignIdInput);

        $extraInput = ZFactory::createHiddenElement('extra');
        $extraInput->setValue('');
        $form->addElement($extraInput);

//FORM 

        $selectInput = ZFactory::createElement('select','type');
        $selectInput->setLabel('Type');
        $types = self::getTypes();
        foreach($types as $type) {
            $selectInput->addMultiOption($type, $type);
        }
        $form->addElement($selectInput);

        $messageInput = ZFactory::createElement('text','message');
        $messageInput->setLabel('Message');
        $messageInput->addValidator($notEmpty);
        $form->addElement($messageInput);

        switch($action) {
            case 'add':
                $submitInput = ZFactory::createSubmitElement('zforminputvalidator_add_submit');
                $submitInput->setLabel('Save');
                $form->addElement($submitInput);
                break;
            case 'edit':
                //add submit button
                $submitInput = ZFactory::createSubmitElement('zforminputvalidator_edit_submit');
                $submitInput->setLabel('Update');
                $form->addElement($submitInput);
                //set the rowId to the form
                $idInput = ZFactory::createHiddenElement('input_validator_id');
                $idInput->setValue($rowId);
                $idInput->setOrder(0);
                $form->addElement($idInput);
                break;
            case 'trash':
                //add submit button
                $submitInput = ZFactory::createSubmitElement('zforminputvalidator_trash_submit');
                $submitInput->setLabel('Trash');
                $form->addElement($submitInput);
                //set the rowId to the form
                $idInput = ZFactory::createHiddenElement('input_validator_id');
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
