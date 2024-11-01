<?php

class ZFormStep {

    public static function getEmptyRowData() {
        $emptyRowData = array(
            //'id'='' ,
            'form_id'=>'',
            'decorator_id'=>'',
            'step'=>'',
            'next_post_id'=>'',
            'custom_action'=>'',
            'method'=>'',
            'requires_key'=>'',
        );
        return $emptyRowData;
    }

    public static function hydrateRowData($formData=array()) {
        $emptyRowData = self::getEmptyRowData();
        $hydratedData = array();
        foreach($emptyRowData as $k => $v){
            if (isset($formData[$k])) {
                $hydratedData[$k] = $formData[$k];
            }
        }
        return $hydratedData;
    }
    
    public static function getForm($action = '', $formId = 0, $rowId = 0) {
        //get the form from a simple factory
        $form = ZFactory::createForm('zformstep');
//VALIDATORS
        //validate input is not empty
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('Field cannot be empty.');

        //validate input is only letters and numbers. strictly.
        $alnum = new Zend_Validate_Alnum();
        $alnum->setMessage('Only letters and numbers are valid. No spaces or underscores.');

//HIDDEN INPUTS
        //form_id - in widget phase. will be post_id or channel_id.
        $formIdInput = ZFactory::createHiddenElement('form_id');
        $formIdInput->setValue($formId);
        //$formIdInput->setOrder(0);
        $form->addElement($formIdInput);

//FORM 

        $stepModel = ZFactory::createModel('zform_step');
        $select = $stepModel->select();
        $info = $stepModel->info();
        $tableName = $info['name'];
        $select->from($tableName, 'count(*) as count');
        $select->where('form_id = ?',$formId);
        $countRow = $stepModel->fetchRow($select);
        $count = 0;
        if (! empty($countRow)) {
            reset($countRow);
            $count = $countRow->count;
        }
        $count += 2;

        //current step
        $stepInput = ZFactory::createElement('select','step');
        $stepInput->setLabel('Current Step');
        $stepInput->setRequired(true);
        for ($x=1; $x<=$count; $x++) {
            $stepInput->addMultiOption($x, $x);
        }
        $stepInput->setValue($count - 1);
        $form->addElement($stepInput);        

        //get wp posts
        $postModel = ZFactory::createModel('posts');
        $select = $postModel->select();
        $select->where('post_status = ?','publish');
        $posts = $postModel->fetchAll($select);
        //next post
        $nextPostIdInput = ZFactory::createElement('select','next_post_id');
        $nextPostIdInput->setLabel('Next Step/Post (where it goes after user submits form)');
        if (empty($posts)) {
            $nextPostIdInput->addMultiOption('','None Found');
        } else {
            foreach($posts as $post) {
                $nextPostIdInput->addMultiOption($post->ID,$post->post_title);
            }
        }
        $form->addElement($nextPostIdInput);

        //get decorators
        $decoratorModel = ZFactory::createModel('zform_decorator');
        $decoratorRows = $decoratorModel->fetchAll();

        //decorator input
        $decoratorIdInput = ZFactory::createElement('select','decorator_id');
        $decoratorIdInput->setLabel('Decorator');
        if (!empty($decoratorRows)) {
            foreach($decoratorRows as $decorator) {
                $decoratorIdInput->addMultiOption($decorator->id, $decorator->title);
            }
        }
        $form->addElement($decoratorIdInput);
        
        //method - post or get
        $methodInput = ZFactory::createElement('select','method');
        $methodInput->setLabel('Form Method');
        $methodInput->addMultiOption("post","POST");
        $methodInput->addMultiOption("get","GET");
        $form->addElement($methodInput);

        //step - default to 1 for now
        $customActionInput = ZFactory::createElement('text','custom_action');
        $customActionInput->setLabel('Custom Action (optional feature)');
        $form->addElement($customActionInput);

        //step - default to 1 for now
        $reqKeyInput = ZFactory::createElement('text','requires_key');
        $reqKeyInput->setLabel('Required Session Key (optional feature)');
        $form->addElement($reqKeyInput);

        switch($action) {
            case 'add':
                $submitInput = ZFactory::createSubmitElement('zformstep_add_submit');
                $submitInput->setLabel('Save');
                $form->addElement($submitInput);
                break;
            case 'edit':
                //add submit button
                $submitInput = ZFactory::createSubmitElement('zformstep_edit_submit');
                $submitInput->setLabel('Update');
                $form->addElement($submitInput);
                //set the rowId to the form
                $idInput = ZFactory::createHiddenElement('step_id');
                $idInput->setValue($rowId);
                $idInput->setOrder(0);
                $form->addElement($idInput);
                break;
            case 'trash':
                //add submit button
                $submitInput = ZFactory::createSubmitElement('zformstep_trash_submit');
                $submitInput->setLabel('Trash');
                $form->addElement($submitInput);
                //set the rowId to the form
                $idInput = ZFactory::createHiddenElement('step_id');
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
