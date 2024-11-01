<?php
    //get rowId
    $formInputId = ZRequest::getParam('form_input_id'); //could use either post or get
    //get parentId and set to form
    $formStepId = ZRequest::getParam('form_step_id'); 
    $form = ZFormInput::getForm('edit', $formStepId, $formInputId);
    $formData = ZRequest::getPost();
    $formInputModel = ZFactory::createModel('zform_input');
    
    $formId = ZRequest::getParam('form_id');
    $formModel = ZFactory::createModel('zform');
    $formRow = $formModel->fetchRow($formModel->select()->where('id = ?', $formId));
    $formTitle = '';
    if (!empty($formRow->title)) {
        $formTitle = $formRow->title;
    }
    
    $formStepId = ZRequest::getParam('form_step_id');
    $stepModel = ZFactory::createModel('zform_step');
    $stepRow = $stepModel->fetchRow($stepModel->select()->where('id = ?', $formStepId));
    $step = '';
    if (!empty($stepRow->step)) {
        $step = $stepRow->step;
    }
    
    $headerHtml = "<h2>Edit Form Input</h2>\n<div><p>Step {$step} of &quot;{$formTitle}&quot;</p></div>";

    if (isset($formData['zforminput_edit_submit'])) {
        if ($form->isValid($formData)) {
            //fill an empty row with form data
            $updateData = ZFormInput::hydrateRowData($formData);
            //update row
            $res = 0;
            if ($formInputId >0) {
                $res = $formInputModel->update($updateData, array('id = ?' => $formInputId));
            }
            include('list.inc.php');
        } else {
            //populate form and show again with error messages
            $form->populate($formData);
            echo $headerHtml;
            echo $form;
        }
    } else {
        //initial view of form
        if ($formInputId >0) {
            $row = $formInputModel->find($formInputId);
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