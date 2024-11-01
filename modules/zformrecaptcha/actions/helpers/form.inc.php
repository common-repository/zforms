<?php

class ZFormRecaptcha {

    public static function getEmptyRowData() {
        $emptyRowData = array(
            //'id'='' ,
            'title'=>'',
            'pub_key'=>'',
            'priv_key'=>'',
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
    
    public static function getForm($action = '', $rowId = 0) {
        //get the form from a simple factory
        $form = ZFactory::createForm('zform_recaptcha');
//VALIDATORS
        //validate input is not empty
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('Field cannot be empty.');

        //validate input is only letters and numbers. strictly.
        $alnum = new Zend_Validate_Alnum();
        $alnum->setMessage('Only letters and numbers are valid. No spaces or underscores.');

//FORM 
        $titleInput = ZFactory::createElement('text','title');
        $titleInput->setLabel('Title');
        $titleInput->addValidator($notEmpty);
        $form->addElement($titleInput);
        
        $pubKeyInput = ZFactory::createElement('text','pub_key');
        $pubKeyInput->setLabel('Public Key');
        $pubKeyInput->addValidator($notEmpty);
        $form->addElement($pubKeyInput);

        $privKeyInput = ZFactory::createElement('text','priv_key');
        $privKeyInput->setLabel('Private Key');
        $privKeyInput->addValidator($notEmpty);
        $form->addElement($privKeyInput);


        switch($action) {
            case 'add':
                $submitInput = ZFactory::createSubmitElement('zformrecaptcha_add_submit');
                $submitInput->setLabel('Save');
                $form->addElement($submitInput);
                break;
            case 'edit':
                //add submit button
                $submitInput = ZFactory::createSubmitElement('zformrecaptcha_edit_submit');
                $submitInput->setLabel('Update');
                $form->addElement($submitInput);
                //set the rowId to the form
                $idInput = ZFactory::createHiddenElement('recaptcha_id');
                $idInput->setValue($rowId);
                $idInput->setOrder(0);
                $form->addElement($idInput);
                break;
            case 'trash':
                //add submit button
                $submitInput = ZFactory::createSubmitElement('zformrecaptcha_trash_submit');
                $submitInput->setLabel('Trash');
                $form->addElement($submitInput);
                //set the rowId to the form
                $idInput = ZFactory::createHiddenElement('recaptcha_id');
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
