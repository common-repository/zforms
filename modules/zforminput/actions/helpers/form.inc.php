<?php

class ZFormInput {

    public static function getEmptyRowData() {
        $emptyRowData=array(
            //'id'='' , //let the update functions insert the id
            'form_step_id'=>'',
            'name'=>'',
            //'field_id'=>'', //will be in future phase involving channels
            'label'=>'',
            'type'=>'',
            'required'=>'',
            'css_classes'=>'',
            'css_id'=>'',
            'validators'=>'',
            'order'=>'',
            'display_group'=>'',
            'default_value'=>'',
        );
        return $emptyRowData;
    }

    public static function hydrateRowData($formData=array()) {
        $emptyRowData=self::getEmptyRowData();
        $hydratedData=array();
        foreach($emptyRowData as $k=>$v){
            if (isset($formData[$k])) {
                $hydratedData[$k]=$formData[$k];
            }
        }
        return $hydratedData;
    }

    public static function getForm($action, $formStepId=0, $rowId=0) {
        //get the form from a simple factory
        $form = ZFactory::createForm('form_input');
//VALIDATORS
        //validate input is not empty
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('Field cannot be empty.');
        //validate input is only letters and numbers. strictly.
        $alnum = new Zend_Validate_Alnum();
        $alnum->setMessage('Only letters and numbers are valid. No spaces.');

//HIDDEN INPUTS

        //set the rowId to the form
        $formStepIdInput = ZFactory::createHiddenElement('form_step_id');
        $formStepIdInput->setValue($formStepId);
        //$formStepIdInput->setOrder(0);
        $form->addElement($formStepIdInput);

        //field_id - in future phase. no channels yet.
        $fieldIdInput = ZFactory::createHiddenElement('field_id');
        $fieldIdInput->setValue(0);
        //$fieldIdInput->setOrder(0);
        $form->addElement($fieldIdInput);

        $displayGroupInput = ZFactory::createHiddenElement('display_group');
        $displayGroupInput->setValue(1);
        $form->addElement($displayGroupInput);

//FORM
        //maybe heading is a better term
        $labelInput = ZFactory::createElement('text','label');
        $labelInput->setLabel('Label');
        $labelInput->setRequired(true);
        $labelInput->addValidator($notEmpty);
        $form->addElement($labelInput);
        //for use in the code
        $nameInput = ZFactory::createElement('text','name');
        $nameInput->setLabel('Input Name/Key eg first_name');
        $nameInput->setRequired(true);
        $nameInput->addValidator($notEmpty);
        //$nameInput->addValidator($alnum);
        $form->addElement($nameInput);

        //get a count, cater to order value selection
        $inputModel = ZFactory::createModel('zform_input');
        $select = $inputModel->select();
        $info = $inputModel->info();
        $tableName = $info['name'];
        $select->from($tableName, 'count(*) as count');
        $select->where('form_step_id = ?', $formStepId);
        $countRow = $inputModel->fetchRow($select);
        $count = 0;
        if (!empty($countRow)) {
            $count = $countRow->count;
        }
        $count += 2;

        //order
        $orderInput=ZFactory::createElement('select', 'order');
        $orderInput->setLabel('Order');
        for ($x = 1; $x <= $count; $x++) {
            $orderInput->addMultiOption($x,$x);
        }
        //set default value for add, since it won't be populated initially
        $orderInput->setValue($count - 1);
        $form->addElement($orderInput);

        //input type
        $typeInput = ZFactory::createElement('select', 'type');
        $typeInput->setLabel('Input Type');
        $typeInput->addMultiOption("text", "Text");
        $typeInput->addMultiOption("textarea", "Textarea");
        $typeInput->addMultiOption("checkbox", "Checkbox");
        $typeInput->addMultiOption("radio", "Radio");
        $typeInput->addMultiOption("select", "Select");
        $typeInput->addMultiOption("hidden", "Hidden");
        $typeInput->addMultiOption("submit", "Submit");

        //could probably add some more interesting options here
        //$typeInput->addMultiOption("session","Session");
        //$typeInput->addMultiOption("server","Server");
        //$typeInput->addMultiOption("cookie","Cookie");

        //add recaptchas
        $recaptchaModel = ZFactory::createModel('zform_recaptcha');
        $recaptchaRows = $recaptchaModel->fetchAll();
        //probably only one, but *shrug*
        if (!empty($recaptchaRows)) {
            foreach($recaptchaRows as $recaptchaRow) {
                $recaptchaId = $recaptchaRow->id;
                $recaptchaTitle = "Recaptcha: " . $recaptchaRow->title;
                $elementId = "recaptcha:$recaptchaId";
                $typeInput->addMultiOption($elementId, $recaptchaTitle);
            }
        }

        $form->addElement($typeInput);

        //default value
        $defaultValueInput = ZFactory::createElement('text','default_value');
        $defaultValueInput->setLabel('Default Value (optional)');
        $form->addElement($defaultValueInput);

        //required
        $reqdInput = ZFactory::createElement('checkbox','required');
        $reqdInput->setLabel('Is Required?');
        $form->addElement($reqdInput);

        //css_id
        $cssIdInput = ZFactory::createElement('text','css_id');
        $cssIdInput->setLabel('CSS/Element Id (optional)');
        $form->addElement($cssIdInput);

        //css_classes
        $cssClassesInput = ZFactory::createElement('text','css_classes');
        $cssClassesInput->setLabel('CSS Classes (optional, space separated)');
        $form->addElement($cssClassesInput);
  
        switch($action) {
            case 'add':
                $submitInput = ZFactory::createSubmitElement('zforminput_add_submit');
                $submitInput->setLabel('Save');
                $form->addElement($submitInput);
                break;
            case 'edit':
                //add submit button
                $submitInput = ZFactory::createSubmitElement('zforminput_edit_submit');
                $submitInput->setLabel('Update');
                $form->addElement($submitInput);
                //set the rowId to the form input
                $idInput = ZFactory::createHiddenElement('form_input_id');
                $idInput->setValue($rowId);
                $idInput->setOrder(0);
                $form->addElement($idInput);
                break;
            case 'trash':
                //add submit button
                $submitInput = ZFactory::createSubmitElement('zforminput_trash_submit');
                $submitInput->setLabel('Trash');
                $form->addElement($submitInput);
                //set the rowId to the form
                $idInput = ZFactory::createHiddenElement('form_input_id');
                $idInput->setValue($rowId);
                $idInput->setOrder(0);
                $form->addElement($idInput);
                break;
            default:
                //what do
                break;
        }
        return $form;
    }
    
}