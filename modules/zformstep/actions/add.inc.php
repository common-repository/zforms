<?php

    
    $formData = ZRequest::getPost();
    $formId = ZRequest::getParam('form_id');
    $form = ZFormStep::getForm('add',$formId);
    
    $formModel = ZFactory::createModel('zform');
    $formRow = $formModel->fetchRow($formModel->select()->where('id = ?',$formId));
    $formTitle = '';
    if (!empty($formRow->title)) {
        $formTitle = $formRow->title;
    }
    
    $headerHtml = "<h2>Add Form Step</h2>\n<div><p>Step in &quot;{$formTitle}&quot;</p></div>";

    if (isset($formData['zformstep_add_submit'])) {
        if ($form->isValid($formData)) {
            //get model and hydrate a row to be inserted
            $formModel = ZFactory::createModel('zform_step');
            $insertData = ZFormStep::hydrateRowData($formData);
            //save row
            $newId = $formModel->insert($insertData);
            //cannot redirect, show listing page
            $formId = $insertData['form_id'];
            include('list.inc.php');
        } else {
            //populate form and show again with error messages
            echo $headerHtml;
            $form->populate($formData);
            echo $form;
        }
    } else {
        //initial view of form
        echo $headerHtml;
        echo $form;
    }
