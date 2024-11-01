<?php
    
    $formData = ZRequest::getPost();
    $formStepId = ZRequest::getParam('form_step_id');
    $formId = ZRequest::getParam('form_id');
    $form = ZFormStep::getForm('edit',$formId,$formStepId);
    $formStepModel = ZFactory::createModel('zform_step');
    
    $formModel = ZFactory::createModel('zform');
    $formRow = $formModel->fetchRow($formModel->select()->where('id = ?',$formId));
    $formTitle = '';
    if (!empty($formRow->title)) {
        $formTitle = $formRow->title;
    }
    
    $headerHtml = "<h2>Edit Form Step</h2>\n<div><p>Step in &quot;{$formTitle}&quot;</p></div>";

    if (isset($formData['zformstep_edit_submit'])) {
        if ($form->isValid($formData)) {
            //fill an empty row with form data
            $updateData = ZFormStep::hydrateRowData($formData);
            //update row
            $res = 0;
            if ($formStepId >0) {
                $res = $formStepModel->update($updateData,array('id = ?'=>$formStepId));
            }
            include('list.inc.php');
        } else {
            //populate form and show again with error messages
            echo $headerHtml;
            $form->populate($formData);
            echo $form;
        }
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