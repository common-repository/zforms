<?php

    $formInputId = ZRequest::getParam('form_input_id'); 
    $formStepId = ZRequest::getParam('form_step_id');
    $form = ZFormInput::getForm('trash',$formStepId,$formInputId);
    $formData = ZRequest::getPost();
    $formInputModel=ZFactory::createModel('zform_input');
    
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
    
    $headerHtml = "<h2>Trash Form Input</h2>\n<div><p>Step {$step} of &quot;{$formTitle}&quot;</p></div>";

    //common action logic
    if (isset($formData['zforminput_trash_submit'])) {
        //delete row
        $res = 0;
        if ($formInputId >0) {
            $res = $formInputModel->delete(array('id = ?' => $formInputId));
        }
        include('list.inc.php');
    } else {
        //initial view of form
        if ($formInputId >0) {
            $row = $formInputModel->find($formInputId);
            if (!empty($row)) {
                //get the row we're deleting
                $dataRow = $row[0]; //first row in a collection/array
                $data = $dataRow->toArray(); //change the array elements to arrays
                //fill the form with rowData
                $form->populate($data);
                echo $headerHtml;
                echo $form;
            } else {
                include('list.inc.php');
            }
        } else {
            include('list.inc.php');
        }
    }