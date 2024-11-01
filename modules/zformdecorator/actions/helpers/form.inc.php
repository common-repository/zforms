<?php

class ZFormDecorator {

    public static function getEmptyRowData() {
        $emptyRowData=array(
            //'id'=>'' ,
            'title'=>'',
            'container_tag'=>'',
            'row_tag'=>'',
            'label_tag'=>'',
            'input_tag'=>''
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
        $form = ZFactory::createForm('zform_decorator');
       
//FORM 

        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('Field cannot be empty.');

        $titleInput = ZFactory::createElement('text','title');
        $titleInput->setLabel('Title');
        $titleInput->setRequired(1);
        $titleInput->addValidator($notEmpty);
        $form->addElement($titleInput);

        $containerOptions = array('table','div','ul','dl');
        $rowOptions = array('tr','div','li','p','none');
        $labelOptions = array('td','div','li','p','dt','dd','span');
            
        $containerInput = ZFactory::createElement('select','container_tag');
        $containerInput->setLabel('Container Tag');
        foreach($containerOptions as $option) {
            $containerInput->addMultiOption($option,$option);
        }
        $form->addElement($containerInput);

        $rowInput = ZFactory::createElement('select','row_tag');
        $rowInput->setLabel('Row Tag');
        foreach($rowOptions as $option) {
            $rowInput->addMultiOption($option,$option);
        }
        $form->addElement($rowInput);

        $labelInput = ZFactory::createElement('select','label_tag');
        $labelInput->setLabel('Label Tag');
        foreach($labelOptions as $option) {
            $labelInput->addMultiOption($option,$option);
        }
        $form->addElement($labelInput);

        $input = ZFactory::createElement('select','input_tag');
        $input->setLabel('Input Tag');
        foreach($labelOptions as $option) {
            $input->addMultiOption($option,$option);
        }
        $form->addElement($input);

        switch($action) {
            case 'add':
                $submitInput = ZFactory::createSubmitElement('zformdecorator_add_submit');
                $submitInput->setLabel('Save');
                $form->addElement($submitInput);
                break;
            case 'edit':
                //add submit button
                $submitInput = ZFactory::createSubmitElement('zformdecorator_edit_submit');
                $submitInput->setLabel('Update');
                $form->addElement($submitInput);
                //set the rowId to the form
                $idInput = ZFactory::createHiddenElement('decorator_id');
                $idInput->setValue($rowId);
                $idInput->removeDecorator('label');
                $idInput->removeDecorator('htmltag');
                $idInput->setOrder(0);
                $form->addElement($idInput);
                break;
            case 'trash':
                //add submit button
                $submitInput = ZFactory::createSubmitElement('zformdecorator_trash_submit');
                $submitInput->setLabel('Trash');
                $form->addElement($submitInput);
                //set the rowId to the form
                $idInput = ZFactory::createHiddenElement('decorator_id');
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
