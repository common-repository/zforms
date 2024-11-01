<?php

    $formData = ZRequest::getPost();
    $form = ZFormHandler::getForm('add');

    if (isset($formData['zformhandler_add_submit'])) {
        if ($form->isValid($formData)) {
            //get model and hydrate a row to be inserted
            $rowModel = ZFactory::createModel('zform_handler');
            $insertData = ZFormHandler::hydrateRowData($formData);
            //save row
            $newId = $rowModel->insert($insertData);
            //cannot redirect, show listing page
            include('list.inc.php');
        } else {
            //populate form and show again with error messages
            echo "<h2>Add Form Handler</h2>";
            $form->populate($formData);
            echo $form;
        }
    } else {
        //initial view of form
        echo "<h2>Add Form Handler</h2>";
        echo $form;
    }