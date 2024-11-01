<?php

class example_action_helper {

    public static function getEmptyRowData() {
        $emptyRowData = array(
            //'id'='' ,
            'foreign_id'=>'',
            'first_name'=>'',
            'last_name'=>'',
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
    
    public static function getForm($action = '',$foreignId = 0, $rowId = 0) {
        //get the form from a simple factory
        $form = ZFactory::createForm('example');
//VALIDATORS
        //validate input is not empty
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('Field cannot be empty.');

        //validate input is only letters and numbers. strictly.
        $alnum = new Zend_Validate_Alnum();
        $alnum->setMessage('Only letters and numbers are valid. No spaces or underscores.');

//HIDDEN INPUTS
        $foreignIdInput = ZFactory::createHiddenElement('foreign_id');
        $foreignIdInput->setValue($foreignId);
        $form->addElement($foreignIdInput);

//FORM 

        $firstNameInput = ZFactory::createElement('text','first_name');
        $firstNameInput->setLabel('First Name');
        $firstNameInput->addValidator($notEmpty);
        $form->addElement($firstNameInput);
        
        $lastNameInput = ZFactory::createElement('text','last_name');
        $lastNameInput->setLabel('Last Name');
        $lastNameInput->addValidator($notEmpty);
        $form->addElement($lastNameInput);


        switch($action) {
            case 'add':
                $submitInput = ZFactory::createSubmitElement('example_add_submit');
                $submitInput->setLabel('Save');
                $form->addElement($submitInput);
                break;
            case 'edit':
                //add submit button
                $submitInput = ZFactory::createSubmitElement('example_edit_submit');
                $submitInput->setLabel('Update');
                $form->addElement($submitInput);
                //set the rowId to the form
                $idInput = ZFactory::createHiddenElement('example_id');
                $idInput->setValue($rowId);
                $idInput->setOrder(0);
                $form->addElement($idInput);
                break;
            case 'trash':
                //add submit button
                $submitInput = ZFactory::createSubmitElement('example_trash_submit');
                $submitInput->setLabel('Trash');
                $form->addElement($submitInput);
                //set the rowId to the form
                $idInput = ZFactory::createHiddenElement('example_id');
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
