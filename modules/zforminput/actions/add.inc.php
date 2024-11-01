<?php

    //get parentId
    $formStepId = ZRequest::getParam('form_step_id');
    $form = ZFormInput::getForm('add',$formStepId);
    $formData = ZRequest::getPost();
    
    $formId = ZRequest::getParam('form_id');
    $formModel = ZFactory::createModel('zform');
    $formRow = $formModel->fetchRow($formModel->select()->where('id = ?',$formId));
    $formTitle = '';
    if (!empty($formRow->title)) {
        $formTitle = $formRow->title;
    }
    
    $formStepId = ZRequest::getParam('form_step_id');
    $stepModel = ZFactory::createModel('zform_step');
    $stepRow = $stepModel->fetchRow($stepModel->select()->where('id = ?',$formStepId));
    $step = '';
    if (!empty($stepRow->step)) {
        $step = $stepRow->step;
    }
    
    $headerHtml = "<h2>Add Form Input</h2>\n<div><p>Step {$step} of &quot;{$formTitle}&quot;</p></div>";

    if (isset($formData['zforminput_add_submit'])) {
        if ($form->isValid($formData)) {
            //get model and hydrate a row to be inserted
            $formInputModel = ZFactory::createModel('zform_input');
            $insertData = ZFormInput::hydrateRowData($formData);
            //save row
            $newId = $formInputModel->insert($insertData);
            //cannot redirect, show listing page
            include('list.inc.php');
        } else {
            //populate form and show again with error messages
            $form->populate($formData);
            echo $headerHtml;
            echo $form;
        }
    } else {
        //initial view of form
        echo $headerHtml;
        echo $form;
    }