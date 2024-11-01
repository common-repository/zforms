<?php

    
    $formData = ZRequest::getPost();
    $formStepId = ZRequest::getParam('form_step_id'); 
    $formId = ZRequest::getParam('form_id');
    $form = ZFormStep::getForm('trash',$formId,$formStepId);
    $formStepModel = ZFactory::createModel('zform_step');
    
    $formModel = ZFactory::createModel('zform');
    $formRow = $formModel->fetchRow($formModel->select()->where('id = ?',$formId));
    $formTitle = '';
    if (!empty($formRow->title)) {
        $formTitle = $formRow->title;
    }
    
    $headerHtml = "<h2>Trash Form Step</h2>\n<div><p>Step in &quot;{$formTitle}&quot;</p></div>";

    if (isset($formData['zformstep_trash_submit'])) {
        //delete row
        $res = 0;
        if ($formStepId >0) {
            //remove children
            $inputModel = ZFactory::createModel('zform_input');
            $inputModel->delete(array('form_step_id = ?' => $formStepId));
            //remove parent
            $formStepModel->delete(array('id = ?' => $formStepId));
        }
        include('list.inc.php');
    } else {
        //initial view of form
        if ($formStepId >0) {
            $row = $formStepModel->find($formStepId);
            if (!empty($row)) {
                //fill the form and show it
                $dataRow = $row[0]; //collection/array of objects
                $data = $dataRow->toArray(); //convert objects to arrays. using naming scheme.
                $form->populate($data);
                echo $headerHtml;
                echo $form;
            } else {
                include('list.inc.php');
            }
        } else {
            //not much to edit if we don't have the rowId
            include('list.inc.php');
        }
    }