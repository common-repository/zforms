<?php

    //get parentId
    $inputId = ZRequest::getParam('form_input_id');
    $form = ZFormInputOption::getForm('add',$inputId);
    $formData = ZRequest::getPost();

    if (isset($formData['zforminputoption_add_submit'])) {
        if ($form->isValid($formData)) {
            //get model and hydrate a row to be inserted
            $inputOptionModel = ZFactory::createModel('zform_input_option');
            $insertData = ZFormInputOption::hydrateRowData($formData);
            //save row
            $newId = $inputOptionModel->insert($insertData);
            //cannot redirect, show listing page
            include('list.inc.php');
        } else {
            //populate form and show again with error messages
            $form->populate($formData);
            echo "<h2>Add Form Input Option</h2>";
            echo $form;
        }
    } else {
        //initial view of form
        echo "<h2>Add Form Input Option</h2>";
        echo $form;
    }