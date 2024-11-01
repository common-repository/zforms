<?php

    $formData = ZRequest::getPost();
    $foreignId = ZRequest::getParam('input_id');
    $form = ZFormInputValidator::getForm('add',$foreignId);

    if (isset($formData['zforminputvalidator_add_submit'])) {
        if ($form->isValid($formData)) {
            //get model and hydrate a row to be inserted
            $rowModel = ZFactory::createModel('zform_input_validator');
            $insertData = ZFormInputValidator::hydrateRowData($formData);
            //save row
            $newId = $rowModel->insert($insertData);
            //cannot redirect, show listing page
            $foreignId = $insertData['input_id'];
            include('list.inc.php');
        } else {
            //populate form and show again with error messages
            echo "<h2>Add Input Validator</h2>";
            $form->populate($formData);
            echo $form;
        }
    } else {
        //initial view of form
        echo "<h2>Add Input Validator</h2>";
        echo $form;
    }