<?php

class ZFormInputOption {

    public static function getEmptyRowData() {
        $emptyRowData=array(
            //'id'='' , //let the update functions insert the id
            'input_id'=>'',
            'label'=>'',
            'value'=>'',
            'order'=>'',
        );
        return $emptyRowData;
    }

    public static function hydrateRowData($formData=array()) {
        $emptyRowData=self::getEmptyRowData();
        $hydratedData=array();
        foreach($emptyRowData as $k=>$v){
            if (isset($formData[$k])) {
                $hydratedData[$k] = $formData[$k];
            }
        }
        return $hydratedData;
    }

    public static function getForm($action,$inputId=0,$rowId=0) {
        //get the form from a simple factory
        $form = ZFactory::createForm('zform_input_option');
//VALIDATORS
        //validate input is not empty
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('Field cannot be empty.');
        //validate input is only letters and numbers. strictly.
        $alnum = new Zend_Validate_Alnum();
        $alnum->setMessage('Only letters and numbers are valid. No spaces.');

//HIDDEN INPUTS

        //set the rowId to the form
        $inputIdInput = ZFactory::createHiddenElement('input_id');
        $inputIdInput->setValue($inputId);
        $form->addElement($inputIdInput);

//FORM

        //get a count, cater to order value selection
        $inputOptionModel = ZFactory::createModel('zform_input_option');
        $select = $inputOptionModel->select();
        $info = $inputOptionModel->info();
        $tableName = $info['name'];
        $select->from($tableName, 'count(*) as count');
        $select->where('input_id = ?',$inputId);
        $countRow = $inputOptionModel->fetchRow($select);
        $count = 0;
        if (!empty($countRow)) {
            $count = $countRow->count;
        }
        $count += 2;

        //order
        $orderInput = ZFactory::createElement('select','order');
        $orderInput->setLabel('Order');
        for ($x=1;$x<=$count;$x++) {
            $orderInput->addMultiOption($x,$x);
        }
        $orderInput->setValue($count - 1);
        $form->addElement($orderInput);

        //maybe heading is a better term
        $labelInput = ZFactory::createElement('text','label');
        $labelInput->setLabel('Label');
        $labelInput->setRequired(true);
        $labelInput->addValidator($notEmpty);
        $form->addElement($labelInput);

        //default_value - in future phase
        $valueInput = ZFactory::createElement('text','value');
        $valueInput->setLabel('Value');
        $valueInput->setRequired(true);
        $valueInput->addValidator($notEmpty);
        $form->addElement($valueInput);
        
        switch($action) {
            case 'add':
                $submitInput = ZFactory::createSubmitElement('zforminputoption_add_submit');
                $submitInput->setLabel('Save');
                $form->addElement($submitInput);
                break;
            case 'edit':
                //add submit button
                $submitInput = ZFactory::createSubmitElement('zforminputoption_edit_submit');
                $submitInput->setLabel('Update');
                $form->addElement($submitInput);
                //set the rowId to the form input
                $idInput = ZFactory::createHiddenElement('form_input_option_id');
                $idInput->setValue($rowId);
                $idInput->setOrder(0);
                $form->addElement($idInput);
                break;
            case 'trash':
                //add submit button
                $submitInput = ZFactory::createSubmitElement('zforminputoption_trash_submit');
                $submitInput->setLabel('Trash');
                $form->addElement($submitInput);
                //set the rowId to the form
                $idInput = ZFactory::createHiddenElement('form_input_option_id');
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