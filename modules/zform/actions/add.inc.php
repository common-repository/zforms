<?php

    $form = ZForm::getForm('add');
    $formData = ZRequest::getPost();

    if (isset($formData['zform_add_submit'])) {
        if ($form->isValid($formData)) {
            //get model and hydrate a row to be inserted
            $formModel = ZFactory::createModel('zform');
            $insertData = ZForm::hydrateRowData($formData);
            //save row
            $newId = $formModel->insert($insertData);
            //cannot redirect, show listing page
            include('list.inc.php');
        } else {
            //populate form and show again with error messages
            echo "<h2>Add Form</h2>";
            $form->populate($formData);
            echo $form;
        }
    } else {
        //initial view of form
        echo "<h2>Add Form</h2>";
        echo $form;
    }
